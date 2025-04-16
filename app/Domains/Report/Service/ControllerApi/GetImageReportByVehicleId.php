<?php

namespace App\Domains\Report\Service\ControllerApi;

use App\Domains\Vehicle\Model\VehicleImageReport;
use App\Domains\Vehicle\Model\Vehicle;
use Illuminate\Support\Facades\Log;

/**
 * Class GetImageReportByVehicleId
 * 
 * Service to handle the logic for extracting image reports based on a date range and vehicle IDs.
 * Returns reports as an array for a single vehicle_id or grouped by vehicle_id for multiple.
 */
class GetImageReportByVehicleId
{
    /**
     * Handle the extraction of image reports.
     *
     * @param string $startDate Start date in YYYY-MM-DD format
     * @param string $endDate End date in YYYY-MM-DD format
     * @param array $vehicleIds Array of vehicle IDs
     * @return array Reports as array (single vehicle_id) or grouped by vehicle_id (multiple)
     * @throws \Exception If vehicle IDs are invalid
     */
    public function handle(string $startDate, string $endDate, array $vehicleIds): array
    {
        Log::info('GetImageReportByVehicleIdService: Starting handle method', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'vehicle_ids' => $vehicleIds,
        ]);

        // Check if all vehicle_ids exist
        Log::info('GetImageReportByVehicleIdService: Checking vehicle IDs existence');
        $startTime = microtime(true);
        $existingVehicles = Vehicle::whereIn('id', $vehicleIds)->pluck('id')->toArray();
        $missingIds = array_diff($vehicleIds, $existingVehicles);
        if (!empty($missingIds)) {
            Log::error('GetImageReportByVehicleIdService: Invalid vehicle IDs', [
                'missing_vehicle_ids' => $missingIds,
            ]);
            throw new \Exception('One or more vehicle IDs not found');
        }
        $fetchTime = microtime(true) - $startTime;
        Log::info('GetImageReportByVehicleIdService: Vehicle IDs checked', [
            'vehicle_ids' => $vehicleIds,
            'fetch_time_seconds' => $fetchTime,
        ]);

        // Initialize result
        $isSingleVehicle = count($vehicleIds) === 1;
        $result = $isSingleVehicle ? [] : array_fill_keys($vehicleIds, []);

        // Fetch reports within date range and vehicle_ids
        Log::info('GetImageReportByVehicleIdService: Fetching reports');
        $startTime = microtime(true);
        $reports = VehicleImageReport::whereIn('vehicle_id', $vehicleIds)
            ->whereBetween('created_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59',
            ])
            ->get([
                'id',
                'media_id',
                'device_id',
                'vehicle_id',
                'minio_url',
                'minio_bucket',
                'latitude',
                'longitude',
                'enterprise_id',
                'target',
                'created_at',
            ]);

        // Process reports
        if ($reports->isNotEmpty()) {
            foreach ($reports as $report) {
                $vehicleId = (string) $report->vehicle_id;
                $reportData = [
                    'report_id' => $report->id,
                    'media_id' => $report->media_id,
                    'device_id' => $report->device_id,
                    'minio_url' => $report->minio_url,
                    'minio_bucket' => $report->minio_bucket,
                    'latitude' => $report->latitude,
                    'longitude' => $report->longitude,
                    'enterprise_id' => $report->enterprise_id,
                    'target' => $report->target,
                    'created_at' => $report->created_at->toIso8601String(),
                ];

                if ($isSingleVehicle) {
                    $result[] = $reportData;
                } else {
                    $result[$vehicleId][] = $reportData;
                }
            }
        }

        $fetchTime = microtime(true) - $startTime;
        Log::info('GetImageReportByVehicleIdService: Reports fetched', [
            'vehicle_ids' => $vehicleIds,
            'report_count' => $isSingleVehicle ? count($result) : array_sum(array_map('count', $result)),
            'fetch_time_seconds' => $fetchTime,
        ]);

        Log::info('GetImageReportByVehicleIdService: Handle method completed successfully');
        return $result;
    }
}