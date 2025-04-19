<?php declare(strict_types=1);

namespace App\Domains\Report\Service\ControllerApi;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\Trip\Model\Trip as TripModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DailyDistanceImpressionReach
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

        // Log bước 2: Lấy danh sách media
        $mediaQuery = MediaModel::query()
            ->where('enterprise_id', $enterpriseId)
            ->when($campaignId, fn($q) => $q->where('campaign_id', $campaignId));

        $mediaList = $mediaQuery->pluck('file_name', 'id')->all();
        // Log::info('Step 2 - Media List:', $mediaList);

        if (empty($mediaList)) {
            // Log::info('Step 2 - No media found');
            return [];
        }

        $mediaIds = array_keys($mediaList);
        $fileNames = array_values($mediaList);
        // Log::info('Step 2 - Extracted Media IDs and File Names:', [
        //     'media_ids' => $mediaIds,
        //     'file_names' => $fileNames,
        // ]);

        // Log bước 3: Lấy danh sách device_id từ view_logs
        $deviceIds = DB::table('view_logs')
            ->whereIn('media_filename', $fileNames)
            ->whereBetween('created_at', [$start, $end])
            ->distinct()
            ->pluck('device_id')
            ->all();
        // Log::info('Step 3 - Device IDs from view_logs:', $deviceIds);

        if (empty($deviceIds)) {
            // Log::info('Step 3 - No devices found');
            return [];
        }

        // Log bước 4: Tính view stats từ view_logs, tổng hợp theo ngày
        $viewStats = DB::table('view_logs')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as view_date'),
                DB::raw('COUNT(*) as impression'),
                DB::raw('SUM(view_count) as total_views')
            )
            ->whereIn('device_id', $deviceIds)
            ->whereIn('media_filename', $fileNames)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('view_date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [
                    $item->view_date => [
                        'view_date' => $item->view_date,
                        'impression' => (int) $item->impression,
                        'total_views' => (int) $item->total_views,
                    ]
                ];
            })->all();

        // Log::info('Step 4 - View Stats:', $viewStats);

        // Log bước 5: Tính tổng khoảng cách từ trip, tổng hợp theo ngày
        $distanceStats = TripModel::query()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as trip_date'),
                DB::raw('SUM(distance) as total_distance_km')
            )
            ->whereIn('device_id', $deviceIds)
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('trip_date')
            ->get()
            ->mapWithKeys(fn($item) => [
                $item->trip_date => (int) $item->total_distance_km
            ])
            ->all();

        // Log::info('Step 5 - Distance Stats:', $distanceStats);

        // Log bước 6: Kết hợp dữ liệu và trả về kết quả
        $results = [];
        foreach ($viewStats as $viewDate => $stat) {
            $results[] = [
                'date' => Carbon::parse($viewDate)->format('d/m/Y'),
                'impression' => $stat['impression'],
                'total_views' => $stat['total_views'],
                'total_distance_km' => $distanceStats[$viewDate] ?? 0,
            ];
        }

        // Sắp xếp theo ngày tăng dần
        usort($results, fn($a, $b) => Carbon::createFromFormat('d/m/Y', $a['date'])->timestamp <=> Carbon::createFromFormat('d/m/Y', $b['date'])->timestamp);

        // Log::info('Step 6 - Final Results:', $results);
        return $results;
    }
}