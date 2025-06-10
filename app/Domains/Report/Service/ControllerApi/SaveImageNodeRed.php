<?php

namespace App\Domains\Report\Service\ControllerApi;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class SaveImageNodeRed
{
    public function handle(array $imageData)
    {
        // Extract base64 and file_name
        $base64Image = $imageData['base64'];
        $fileName = $imageData['file_name'];

        // Ensure file_name has .png extension if none provided
        if (!preg_match('/\.[a-zA-Z0-9]+$/', $fileName)) {
            $fileName .= '.png';
        }

        // Convert base64 to binary and PNG
        try {
            $base64String = preg_replace('/^data:image\/[a-z]+;base64,/', '', $base64Image);
            $imageBinary = base64_decode($base64String);
            if ($imageBinary === false) {
                Log::error('SendImageReport: Failed to decode base64 image', [
                    'file_name' => $fileName,
                ]);
                throw new \Exception('Failed to decode base64 image');
            }

            $image = imagecreatefromstring($imageBinary);
            if ($image === false) {
                Log::error('SendImageReport: Failed to create image from binary', [
                    'file_name' => $fileName,
                ]);
                throw new \Exception('Failed to create image from binary');
            }

            ob_start();
            imagepng($image, null, 6); // Compress PNG with quality level 6
            $pngBinary = ob_get_clean();
            imagedestroy($image);
        } catch (\Exception $e) {
            Log::error('SendImageReport: Image conversion failed', [
                'message' => $e->getMessage(),
                'file_name' => $fileName,
            ]);
            throw new \Exception('Failed to convert image to PNG: ' . $e->getMessage());
        }

        // Prepare MinIO storage
        $minioBucket = config('filesystems.disks.minio.bucket', 'default');
        $directory = 'cameraDevice';
        $fullPath = $directory . '/' . $fileName;

        // Check and create bucket if not exists
        try {
            $disk = Storage::disk('minio');

            // Initialize S3 client for bucket creation
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => config('filesystems.disks.minio.region', 'us-east-1'),
                'endpoint' => config('filesystems.disks.minio.endpoint'),
                'use_path_style_endpoint' => config('filesystems.disks.minio.use_path_style_endpoint', true),
                'credentials' => [
                    'key' => config('filesystems.disks.minio.key'),
                    'secret' => config('filesystems.disks.minio.secret'),
                ],
            ]);

            // Check if bucket exists
            try {
                $s3Client->headBucket(['Bucket' => $minioBucket]);
            } catch (S3Exception $e) {
                if ($e->getAwsErrorCode() === 'NotFound' || $e->getStatusCode() === 404) {
                    Log::info('SendImageReport: Bucket does not exist, creating bucket', [
                        'bucket' => $minioBucket,
                    ]);
                    $s3Client->createBucket(['Bucket' => $minioBucket]);
                    $s3Client->waitUntil('BucketExists', ['Bucket' => $minioBucket]);
                } else {
                    throw $e;
                }
            }

            // Check and create directory if not exists
            try {
                if (!$disk->exists($directory)) {
                    Log::info('SendImageReport: Directory does not exist, creating directory', [
                        'bucket' => $minioBucket,
                        'directory' => $directory,
                    ]);
                    $disk->makeDirectory($directory);
                    $disk->put($directory . '/', ''); // Ensure directory creation
                }
            } catch (\Exception $e) {
                Log::warning('SendImageReport: Unable to check or create directory, proceeding anyway', [
                    'message' => $e->getMessage(),
                    'bucket' => $minioBucket,
                    'directory' => $directory,
                ]);
            }
        } catch (S3Exception $e) {
            Log::error('SendImageReport: Failed to check or create bucket/directory', [
                'message' => $e->getMessage(),
                'aws_error' => $e->getAwsErrorCode(),
                'status_code' => $e->getStatusCode(),
                'bucket' => $minioBucket,
                'directory' => $directory,
            ]);
            throw new \Exception('Failed to access or create MinIO bucket/directory: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('SendImageReport: General exception during bucket/directory check', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'bucket' => $minioBucket,
                'directory' => $directory,
            ]);
            throw new \Exception('Failed to access or create MinIO bucket/directory: ' . $e->getMessage());
        }

        // Upload to MinIO
        try {
            $disk = Storage::disk('minio');
            $uploaded = $disk->put($fullPath, $pngBinary);

            if (!$uploaded) {
                Log::error('SendImageReport: Failed to upload image to MinIO', [
                    'bucket' => $minioBucket,
                    'path' => $fullPath,
                ]);
                throw new \Exception('Failed to upload image to MinIO');
            }

            // Verify file existence
            try {
                $fileExists = $disk->exists($fullPath);
                if (!$fileExists) {
                    Log::error('SendImageReport: Uploaded file not found on MinIO', [
                        'bucket' => $minioBucket,
                        'path' => $fullPath,
                    ]);
                    throw new \Exception('Uploaded file not found on MinIO');
                }
            } catch (\Exception $e) {
                Log::warning('SendImageReport: Unable to verify file existence, proceeding anyway', [
                    'message' => $e->getMessage(),
                    'bucket' => $minioBucket,
                    'path' => $fullPath,
                ]);
            }
        } catch (S3Exception $e) {
            Log::error('SendImageReport: MinIO S3 exception', [
                'message' => $e->getMessage(),
                'aws_error' => $e->getAwsErrorCode(),
                'status_code' => $e->getStatusCode(),
                'bucket' => $minioBucket,
                'path' => $fullPath,
            ]);
            throw new \Exception('Failed to upload image to MinIO: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('SendImageReport: General exception during MinIO upload', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'bucket' => $minioBucket,
                'path' => $fullPath,
            ]);
            throw new \Exception('Failed to upload image to MinIO: ' . $e->getMessage());
        }

        return [
            'path' => $fullPath,
            'bucket' => $minioBucket,
        ];
    }
}