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
        // Log::info('SendImageReport: Starting handle method', [
        //     'data' => $data,
        //     'image_path' => $image->getPathname(),
        //     'image_size_bytes' => $image->getSize(),
        //     'minio_config' => [
        //         'endpoint' => config('filesystems.disks.minio.endpoint'),
        //         'bucket' => config('filesystems.disks.minio.bucket'),
        //         'region' => config('filesystems.disks.minio.region'),
        //     ],
        // ]);

        // Validate image using getID3
        // Log::info('SendImageReport: Starting image validation with getID3');
        $startTime = microtime(true);
        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($image->getPathname());
        $validationTime = microtime(true) - $startTime;
        // Log::info('SendImageReport: Image validation completed', [
        //     'file_format' => $fileInfo['fileformat'] ?? 'unknown',
        //     'validation_time_seconds' => $validationTime,
        // ]);

        if (!isset($fileInfo['fileformat']) || !in_array($fileInfo['fileformat'], ['jpg', 'jpeg', 'png'])) {
            Log::error('SendImageReport: Invalid image format', [
                'file_format' => $fileInfo['fileformat'] ?? 'unknown',
            ]);
            throw new \Exception('Invalid image format');
        }

        // Prepare MinIO storage
        $minioBucket = $data['minio_bucket'];
        $minioUrl = ltrim($data['minio_url'], '/');
        // Log::info('SendImageReport: Prepared MinIO storage', [
        //     'bucket' => $minioBucket,
        //     'url' => $minioUrl,
        // ]);

        // Check and create directory if needed
        // Log::info('SendImageReport: Checking MinIO directory');
        $startTime = microtime(true);
        try {
            $disk = Storage::disk('minio');

            // Parse directory and file name
            $pathParts = explode('/', $minioUrl);
            $fileName = array_pop($pathParts); // Get file name (e.g., 4.png)
            $directory = implode('/', $pathParts); // Get directory (e.g., 18)

            // Create directory if it doesn't exist
            if ($directory && !$disk->exists($directory)) {
                // Log::info('SendImageReport: Directory does not exist, creating', [
                //     'bucket' => $minioBucket,
                //     'directory' => $directory,
                // ]);
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
        // Log::info('SendImageReport: Directory check completed', [
        //     'check_time_seconds' => $checkTime,
        // ]);

        // Upload to MinIO
        // Log::info('SendImageReport: Starting upload to MinIO');
        $startTime = microtime(true);
        try {
            $disk = Storage::disk('minio');
            $uploaded = $disk->putFileAs($directory, $image, $fileName);
            $uploadTime = microtime(true) - $startTime;
            $fullPath = $directory ? $directory . '/' . $fileName : $fileName;
            // Log::info('SendImageReport: MinIO upload completed', [
            // FORMATTING: Indent code consistently with 4 spaces
            //     'success' => $uploaded,
            //     'upload_time_seconds' => $uploadTime,
            //     'file_path' => $fullPath,
            // ]);

            if (!$uploaded) {
                Log::error('SendImageReport: Failed to upload image to MinIO', [
                    'bucket' => $minioBucket,
                    'url' => $minioUrl,
                ]);
                throw new \Exception('Failed to upload image to MinIO');
            }

            // Verify file existence
            // Log::info('SendImageReport: Verifying uploaded file existence');
            $fileExists = $disk->exists($fullPath);
            // Log::info('SendImageReport: File existence check', [
            //     'file_exists' => $fileExists,
            //     'file_path' => $fullPath,
            // ]);
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
            'media_id' => $data['media_id'],
            'device_id' => $data['device_id'] ?? null,
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'minio_url' => $minioUrl,
            'minio_bucket' => $minioBucket,
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'enterprise_id' => $data['enterprise_id'],
            'target' => $data['target'] ?? 0,
            'source_type' => $data['source_type'] ?? null,
        ];

        // Handle database operation
        // Log::info('SendImageReport: Preparing database operation', [
        //     'report_data' => $reportData,
        //     'has_target' => isset($data['target']) && !is_null($data['target']),
        // ]);

        $startTime = microtime(true);
        try {
            // If target is provided, check for existing record
            if (isset($data['target']) && !is_null($data['target'])) {
                // Log::info('SendImageReport: Checking for existing record', [
                //     'media_id' => $data['media_id'],
                //     'device_id' => $data['device_id'] ?? null,
                //     'vehicle_id' => $data['vehicle_id'] ?? null,
                //     'date' => Carbon::today()->toDateString(),
                // ]);

                $query = VehicleImageReport::where('media_id', $data['media_id'])
                    ->whereDate('created_at', Carbon::today());

                if (isset($data['device_id'])) {
                    $query->where('device_id', $data['device_id']);
                }
                if (isset($data['vehicle_id'])) {
                    $query->where('vehicle_id', $data['vehicle_id']);
                }

                $existingReport = $query->first();

                if ($existingReport) {
                    // Log::info('SendImageReport: Existing record found, updating', [
                    //     'report_id' => $existingReport->id,
                    // ]);

                    $existingReport->update([
                        'minio_url' => $minioUrl,
                        'minio_bucket' => $minioBucket,
                        'latitude' => $data['latitude'],
                        'longitude' => $data['longitude'],
                        'enterprise_id' => $data['enterprise_id'],
                        'target' => $data['target'],
                        'device_id' => $data['device_id'] ?? $existingReport->device_id,
                        'vehicle_id' => $data['vehicle_id'] ?? $existingReport->vehicle_id,
                        'source_type' => $data['source_type'] ?? $existingReport->source_type,
                    ]);

                    $report = $existingReport;
                } else {
                    // Log::info('SendImageReport: No existing record found, inserting new');
                    $report = VehicleImageReport::create($reportData);
                }
            } else {
                // Log::info('SendImageReport: No target provided, inserting new record');
                $report = VehicleImageReport::create($reportData);
            }

            // $insertTime = microtime(true) - $startTime;
            // Log::info('SendImageReport: Database operation completed', [
            //     'report_id' => $report->id,
            //     'operation' => isset($data['target']) && $existingReport ? 'update' : 'insert',
            //     'insert_time_seconds' => $insertTime,
            //     'inserted_data' => $report->toArray(),
            // ]);
        } catch (\Exception $e) {
            Log::error('SendImageReport: Database operation failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \Exception('Failed to process database operation: ' . $e->getMessage());
        }

        // Log::info('SendImageReport: Handle method completed successfully');
        return $report;
    }
}