<?php

namespace App\Domains\Report\Service\ControllerApi;

use App\Domains\Vehicle\Model\VehicleImageReport;
use getID3;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;
use League\Flysystem\UnableToCheckExistence;

class SendImageReport
{
    public function handle(UploadedFile $image, array $data)
    {
        // Validate image using getID3
        $startTime = microtime(true);
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($image->getPathname());
        $validationTime = microtime(true) - $startTime;

        if (!isset($fileInfo['fileformat']) || !in_array($fileInfo['fileformat'], ['jpg', 'jpeg', 'png'])) {
            Log::error('SendImageReport: Invalid image format', [
                'file_format' => $fileInfo['fileformat'] ?? 'unknown',
            ]);
            throw new \Exception('Invalid image format');
        }

        // Prepare MinIO storage
        $minioBucket = $data['minio_bucket'];
        $minioUrl = ltrim($data['minio_url'], '/');

        // Check and create directory if needed
        $startTime = microtime(true);
        try {
            $disk = Storage::disk('minio');

            // Parse directory and file name
            $pathParts = explode('/', $minioUrl);
            $fileName = array_pop($pathParts); // Get file name (e.g., 4.png)
            $directory = implode('/', $pathParts); // Get directory (e.g., 18)

            // Create directory if it doesn't exist
            if ($directory && !$disk->exists($directory)) {
                $disk->makeDirectory($directory);
            }
        } catch (S3Exception $e) {
            Log::error('SendImageReport: Failed to check or create directory', [
                'message' => $e->getMessage(),
                'aws_error' => $e->getAwsErrorCode(),
                'status_code' => $e->getStatusCode(),
                'bucket' => $minioBucket,
                'url' => $minioUrl,
            ]);
            throw new \Exception('Failed to access MinIO directory: ' . $e->getMessage());
        } catch (UnableToCheckExistence $e) {
            Log::error('SendImageReport: Flysystem unable to check existence', [
                'message' => $e->getMessage(),
                'bucket' => $minioBucket,
                'url' => $minioUrl,
            ]);
            throw new \Exception('Unable to check MinIO directory: ' . $e->getMessage());
        }
        $checkTime = microtime(true) - $startTime;

        // Upload to MinIO
        $startTime = microtime(true);
        try {
            $disk = Storage::disk('minio');
            $uploaded = $disk->putFileAs($directory, $image, $fileName);
            $uploadTime = microtime(true) - $startTime;
            $fullPath = $directory ? $directory . '/' . $fileName : $fileName;

            if (!$uploaded) {
                Log::error('SendImageReport: Failed to upload image to MinIO', [
                    'bucket' => $minioBucket,
                    'url' => $minioUrl,
                ]);
                throw new \Exception('Failed to upload image to MinIO');
            }

            // Verify file existence
            $fileExists = $disk->exists($fullPath);
            if (!$fileExists) {
                Log::error('SendImageReport: Uploaded file not found on MinIO', [
                    'bucket' => $minioBucket,
                    'url' => $minioUrl,
                ]);
                throw new \Exception('Uploaded file not found on MinIO');
            }
        } catch (S3Exception $e) {
            $uploadTime = microtime(true) - $startTime;
            Log::error('SendImageReport: MinIO S3 exception', [
                'message' => $e->getMessage(),
                'aws_error' => $e->getAwsErrorCode(),
                'status_code' => $e->getStatusCode(),
                'bucket' => $minioBucket,
                'url' => $minioUrl,
                'upload_time_seconds' => $uploadTime,
            ]);
            throw new \Exception('Failed to upload image to MinIO: ' . $e->getMessage());
        } catch (\Exception $e) {
            $uploadTime = microtime(true) - $startTime;
            Log::error('SendImageReport: General exception during MinIO upload', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'upload_time_seconds' => $uploadTime,
            ]);
            throw new \Exception('Failed to upload image to MinIO: ' . $e->getMessage());
        }

        // Prepare data for database
        $reportData = [
            'media_id' => $data['media_id'] ?? null,
            'device_id' => $data['device_id'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'minio_url' => $minioUrl,
            'minio_bucket' => $minioBucket,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'enterprise_id' => $data['enterprise_id'],
            'target' => $data['target'] ?? 0,
            'source_type' => $data['source_type'] ?? null,
            'label' => $data['label'] ?? null,
        ];

        // Handle database operation
        $startTime = microtime(true);
        try {
            // Check if label is 'odo'
            $isOdoLabel = $data['label'] === 'odo';

            // If target is provided, check for existing record
            if (isset($data['target']) && !is_null($data['target'])) {
                $query = VehicleImageReport::query();

                // For 'odo' label, we don't require media_id, so adjust the query
                if (!$isOdoLabel) {
                    $query->where('media_id', $data['media_id']);
                }

                $query->whereDate('created_at', Carbon::today());

                if (isset($data['device_id'])) {
                    $query->where('device_id', $data['device_id']);
                }
                if (isset($data['vehicle_id'])) {
                    $query->where('vehicle_id', $data['vehicle_id']);
                }

                // If 'odo', additionally filter by label to ensure we're updating the correct type
                if ($isOdoLabel) {
                    $query->where('label', 'odo');
                }

                $existingReport = $query->first();

                if ($existingReport) {
                    $existingReport->update([
                        'minio_url' => $minioUrl,
                        'minio_bucket' => $minioBucket,
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'enterprise_id' => $data['enterprise_id'],
                        'target' => $data['target'],
                        'media_id' => $data['media_id'] ?? $existingReport->media_id,
                        'device_id' => $data['device_id'] ?? $existingReport->device_id,
                        'vehicle_id' => $data['vehicle_id'] ?? $existingReport->vehicle_id,
                        'source_type' => $data['source_type'] ?? $existingReport->source_type,
                        'label' => $data['label'],
                    ]);

                    $report = $existingReport;
                } else {
                    $report = VehicleImageReport::create($reportData);
                }
            } else {
                $report = VehicleImageReport::create($reportData);
            }
        } catch (\Exception $e) {
            Log::error('SendImageReport: Database operation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to process database operation: ' . $e->getMessage());
        }

        return $report;
    }
}