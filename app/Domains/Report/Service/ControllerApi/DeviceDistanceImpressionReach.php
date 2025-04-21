<?php declare(strict_types=1);

namespace App\Domains\Report\Service\ControllerApi;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\Trip\Model\Trip as TripModel;
use App\Domains\Vehicle\Model\Vehicle as VehicleModel;
use App\Domains\User\Model\User as UserModel;
use App\Domains\Device\Model\Device as DeviceModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DeviceDistanceImpressionReach
{
    protected $request;
    protected $auth;

    public function __construct(Request $request, ?Authenticatable $auth = null)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new(Request $request, ?Authenticatable $auth = null): self
    {
        return new static($request, $auth);
    }

    public function data(): array
    {
        try {
            // Bước 1: Nhận tham số đầu vào
            $campaignId = $this->request->input('campaign_id');
            $startDate = $this->request->input('start_date');
            $endDate = $this->request->input('end_date');
            $enterpriseId = $this->auth->enterprise_id ?? null;

            // Log::info('Step 1 - Input Parameters:', [
            //     'campaign_id' => $campaignId,
            //     'start_date' => $startDate,
            //     'end_date' => $endDate,
            //     'enterprise_id' => $enterpriseId,
            // ]);

            if (!$enterpriseId) {
                Log::error('Step 1 - Error: No enterprise_id found');
                throw new \Exception('Enterprise ID is required from authenticated user.');
            }

            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            // Bước 2: Lấy danh sách media
            $mediaQuery = MediaModel::query()
                ->where('enterprise_id', $enterpriseId)
                ->when($campaignId, fn($q) => $q->where('campaign_id', $campaignId));

            $mediaList = $mediaQuery->pluck('file_name', 'id')->all();

            // Log::info('Step 2 - Media List:', ['count' => count($mediaList)]);

            if (empty($mediaList)) {
                Log::info('Step 2 - No media found');
                return [];
            }

            $mediaIds = array_keys($mediaList);
            $fileNames = array_values($mediaList);
            // Log::info('Step 2 - Extracted Media IDs and File Names:', [
            //     'media_ids' => $mediaIds,
            //     'file_names' => $fileNames,
            // ]);

            // Bước 3: Lấy danh sách serial từ view_logs
            $serials = DB::table('view_logs')
                ->whereIn('media_filename', $fileNames)
                ->whereBetween('created_at', [$start, $end])
                ->distinct()
                ->pluck('serial')
                ->all();

            // Log::info('Step 3 - Serials from view_logs:', ['count' => count($serials)]);

            if (empty($serials)) {
                Log::info('Step 3 - No serials found');
                return [];
            }

            // Bước 3.1: Lấy device_id từ DeviceModel dựa trên serial
            $deviceMap = DeviceModel::query()
                ->whereIn('serial', $serials)
                ->pluck('id', 'serial')
                ->all();

            // Log::info('Step 3.1 - Device ID Mapping from Serials:', ['count' => count($deviceMap)]);

            if (empty($deviceMap)) {
                Log::info('Step 3.1 - No devices found for serials');
                return [];
            }

            $deviceIds = array_values($deviceMap);
            // Log::info('Step 3.1 - Device IDs:', ['device_ids' => $deviceIds]);

            // Bước 4: Tính view stats từ view_logs
            $viewStats = DB::table('view_logs')
                ->select(
                    'serial',
                    'media_filename',
                    DB::raw('COUNT(*) as impression'),
                    DB::raw('SUM(view_count) as total_views')
                )
                ->whereIn('serial', $serials)
                ->whereIn('media_filename', $fileNames)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('serial', 'media_filename')
                ->get()
                ->mapWithKeys(function ($item) use ($mediaList, $deviceMap) {
                    $mediaId = array_search($item->media_filename, $mediaList);
                    $deviceId = $deviceMap[$item->serial] ?? null;
                    if (!$deviceId) {
                        return [];
                    }
                    return [
                        "{$deviceId}_{$mediaId}" => [
                            'device_id' => $deviceId,
                            'serial' => $item->serial,
                            'media_id' => $mediaId,
                            'impression' => (int) $item->impression,
                            'total_views' => (int) $item->total_views,
                        ]
                    ];
                })->all();

            // Log::info('Step 4 - View Stats:', ['count' => count($viewStats)]);

            // Bước 5: Tính tổng khoảng cách từ trip
            $distanceStats = TripModel::query()
                ->select('device_id', DB::raw('SUM(distance) as total_distance_km'))
                ->whereIn('device_id', $deviceIds)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('device_id')
                ->pluck('total_distance_km', 'device_id')
                ->all();
            // Log::info('Step 5 - Distance Stats:', ['device_ids' => $deviceIds, 'count' => count($distanceStats), 'data' => $distanceStats]);

            // Bước 6: Lấy vehicle_id và user_id từ device
            $devices = DeviceModel::query()
                ->select('id', 'vehicle_id', 'user_id', 'address')
                ->whereIn('id', $deviceIds)
                ->get()
                ->mapWithKeys(fn($item) => [
                    $item->id => [
                        'vehicle_id' => $item->vehicle_id,
                        'user_id' => $item->user_id,
                        'address' => $item->address,
                    ]
                ])
                ->all();
            // Log::info('Step 6 - Devices Query:', ['device_ids' => $deviceIds]);
            // Log::info('Step 6 - Devices:', ['count' => count($devices), 'data' => $devices]);

            // Lấy thông tin phương tiện từ vehicle dựa trên vehicle_id
            $vehicleIds = array_filter(array_column($devices, 'vehicle_id'));
            $vehicles = VehicleModel::query()
                ->select('id', 'name', 'plate')
                ->whereIn('id', $vehicleIds)
                ->get()
                ->mapWithKeys(fn($item) => [
                    $item->id => [
                        'vehicle_id' => $item->id,
                        'name' => $item->name,
                        'plate' => $item->plate,
                    ]
                ])
                ->all();
            // Log::info('Step 6 - Vehicles Query:', ['vehicle_ids' => $vehicleIds]);
            // Log::info('Step 6 - Vehicles:', ['count' => count($vehicles), 'data' => $vehicles]);

            // Bước 6.1: Lấy thông tin user từ user_id
            $userIds = array_filter(array_column($devices, 'user_id'));
            // Log::info('Step 6.1 - User IDs:', ['count' => count($userIds), 'data' => $userIds]);

            $users = UserModel::query()
                ->select('id', 'name', 'phone')
                ->whereIn('id', $userIds)
                ->get()
                ->mapWithKeys(fn($item) => [
                    $item->id => [
                        'id' => $item->id,
                        'name' => $item->name,
                        'phone' => $item->phone,
                    ]
                ])
                ->all();
            // Log::info('Step 6.1 - Users:', ['count' => count($users), 'data' => $users]);

            // Bước 7: Lấy vị trí cuối cùng từ trip
            $lastPositions = TripModel::query()
                ->select('device_id')
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(stats, '$.lat')) as latitude")
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(stats, '$.lng')) as longitude")
                ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(stats, '$.speed')) as speed")
                ->whereIn('device_id', $deviceIds)
                ->whereBetween('created_at', [$start, $end])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('device_id')
                ->map(fn($group) => $group->first())
                ->mapWithKeys(fn($item) => [
                    $item->device_id => [
                        'latitude' => (float) $item->latitude,
                        'longitude' => (float) $item->longitude,
                        'speed' => (int) $item->speed,
                    ]
                ])
                ->all();
            // Log::info('Step 7 - Last Positions:', ['device_ids' => $deviceIds, 'count' => count($lastPositions), 'data' => $lastPositions]);

            // Bước 8: Kết hợp dữ liệu và trả về kết quả
            $results = [];
            foreach ($viewStats as $key => $stat) {
                $deviceId = $stat['device_id'];
                $vehicleId = $devices[$deviceId]['vehicle_id'] ?? null;
                $userId = $devices[$deviceId]['user_id'] ?? null;

                if (!$vehicleId || !isset($vehicles[$vehicleId])) {
                    Log::warning('Step 8 - No vehicle data for device_id', ['device_id' => $deviceId, 'vehicle_id' => $vehicleId]);
                }

                if (!$userId || !isset($users[$userId])) {
                    Log::warning('Step 8 - No user data for device_id', ['device_id' => $deviceId, 'user_id' => $userId]);
                }

                $results[] = [
                    'device_id' => $deviceId,
                    'serial' => $stat['serial'],
                    'media_id' => $stat['media_id'],
                    'impression' => $stat['impression'],
                    'total_views' => $stat['total_views'],
                    'total_distance_km' => (int) ($distanceStats[$deviceId] ?? 0),
                    'bookmark' => [
                        'vehicle_id' => $vehicles[$vehicleId]['vehicle_id'] ?? null,
                        'name' => $vehicles[$vehicleId]['name'] ?? 'Unknown Vehicle',
                        'plate' => $vehicles[$vehicleId]['plate'] ?? 'N/A',
                    ],
                    'position' => $lastPositions[$deviceId] ?? [
                        'latitude' => 0,
                        'longitude' => 0,
                        'speed' => 0,
                    ],
                    'user' => $userId && isset($users[$userId]) ? [
                        'id' => $users[$userId]['id'],
                        'name' => $users[$userId]['name'],
                        'phone' => $users[$userId]['phone'],
                        'address' => $devices[$deviceId]['address'] ?? null,
                    ] : [
                        'id' => null,
                        'name' => 'Unknown User',
                        'phone' => null,
                        'address' => $devices[$deviceId]['address'] ?? null,
                    ],
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                ];
            }

            // Log::info('Step 8 - Final Results:', ['count' => count($results), 'data' => $results]);
            return $results;
        } catch (\Exception $e) {
            Log::error('Error in data method:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw new \Exception('An error occurred while processing your request.', 0, $e);
        }
    }
}