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

class ReachAndDistance
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
        // Log bước 1: Nhận tham số đầu vào
        $campaignId = $this->request->input('campaign_id');
        $startDate = $this->request->input('start_date');
        $endDate = $this->request->input('end_date');
        $enterpriseId = $this->auth->enterprise_id ?? null;

        Log::info('Step 1 - Input Parameters:', [
            'campaign_id' => $campaignId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'enterprise_id' => $enterpriseId,
        ]);

        if (!$enterpriseId) {
            Log::error('Step 1 - Error: No enterprise_id found');
            throw new \Exception('Enterprise ID is required from authenticated user.');
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Log bước 2: Lấy danh sách media
        $mediaQuery = MediaModel::query()
            ->where('enterprise_id', $enterpriseId)
            ->when($campaignId, fn($q) => $q->where('campaign_id', $campaignId));

        $mediaList = $mediaQuery->pluck('file_name', 'id')->all();
        Log::info('Step 2 - Media List:', $mediaList);

        if (empty($mediaList)) {
            Log::info('Step 2 - No media found');
            return [];
        }

        $mediaIds = array_keys($mediaList);
        $fileNames = array_values($mediaList);
        Log::info('Step 2 - Extracted Media IDs and File Names:', [
            'media_ids' => $mediaIds,
            'file_names' => $fileNames,
        ]);

        // Log bước 3: Lấy danh sách device_id từ view_logs
        $deviceIds = DB::table('view_logs')
            ->whereIn('media_filename', $fileNames)
            ->whereBetween('created_at', [$start, $end])
            ->distinct()
            ->pluck('device_id')
            ->all();
        Log::info('Step 3 - Device IDs from view_logs:', $deviceIds);

        if (empty($deviceIds)) {
            Log::info('Step 3 - No devices found');
            return [];
        }

        // Log bước 4: Tính view stats từ view_logs
        $viewStats = DB::table('view_logs')
            ->select(
                'device_id',
                'media_filename',
                DB::raw('COUNT(*) as impression'),
                DB::raw('SUM(view_count) as total_views')
            )
            ->whereIn('device_id', $deviceIds)
            ->whereIn('media_filename', $fileNames)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('device_id', 'media_filename')
            ->get()
            ->mapWithKeys(function ($item) use ($mediaList) {
                $mediaId = array_search($item->media_filename, $mediaList);
                return [
                    "{$item->device_id}_{$mediaId}" => [
                        'device_id' => $item->device_id,
                        'media_id' => $mediaId,
                        'impression' => (int) $item->impression,
                        'total_views' => (int) $item->total_views,
                    ]
                ];
            })->all();
        Log::info('Step 4 - View Stats:', $viewStats);

        // Log bước 5: Tính tổng khoảng cách từ trip
        $distanceStats = TripModel::query()
            ->select('device_id', DB::raw('SUM(distance) as total_distance_km'))
            ->whereIn('device_id', $deviceIds)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('device_id')
            ->pluck('total_distance_km', 'device_id')
            ->all();
        Log::info('Step 5 - Distance Stats:', $distanceStats);

        // Log bước 6: Lấy thông tin phương tiện từ vehicle và user_id
        $vehicles = VehicleModel::query()
            ->select('id', 'name', 'plate', 'user_id')
            ->whereIn('id', $deviceIds)
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => [
                    'vehicle_id' => $item->id,
                    'name' => $item->name,
                    'plate' => $item->plate,
                    'user_id' => $item->user_id,
                ]
            ])
            ->all();
        Log::info('Step 6 - Vehicles:', $vehicles);

        // Log bước 6.1: Lấy thông tin user từ user_id
        $userIds = array_filter(array_column($vehicles, 'user_id'));
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
        Log::info('Step 6.1 - Users:', $users);

        // Log bước 6.2: Lấy thông tin address từ device
        $devices = DeviceModel::query()
            ->select('id', 'address')
            ->whereIn('id', $deviceIds)
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->id => [
                    'address' => $item->address,
                ]
            ])
            ->all();
        Log::info('Step 6.2 - Devices:', $devices);

        // Log bước 7: Lấy vị trí cuối cùng từ trip (trích xuất từ stats JSON)
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
        Log::info('Step 7 - Last Positions:', $lastPositions);

        // Log bước 8: Kết hợp dữ liệu và trả về kết quả
        $results = [];
        foreach ($viewStats as $key => $stat) {
            $deviceId = $stat['device_id'];
            $userId = $vehicles[$deviceId]['user_id'] ?? null;
            $results[] = [
                'device_id' => $deviceId,
                'media_id' => $stat['media_id'],
                'impression' => $stat['impression'],
                'total_views' => $stat['total_views'],
                'total_distance_km' => number_format((float) ($distanceStats[$deviceId] ?? 0.00), 0, '', ''),
                'bookmark' => [
                    'vehicle_id' => $vehicles[$deviceId]['vehicle_id'] ?? null,
                    'name' => $vehicles[$deviceId]['name'] ?? null,
                    'plate' => $vehicles[$deviceId]['plate'] ?? null,
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
                    'name' => null,
                    'phone' => null,
                    'address' => $devices[$deviceId]['address'] ?? null,
                ],
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
            ];
        }

        Log::info('Step 8 - Final Results:', $results);
        return $results;
    }
}