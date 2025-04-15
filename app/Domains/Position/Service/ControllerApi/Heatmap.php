<?php declare(strict_types=1);

namespace App\Domains\Position\Service\ControllerApi;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Position\Model\Position as Model;
use App\Domains\Playlist\Model\PlaylistMediaModel as PlaylistMediaModel;
use App\Domains\Display\Model\Display;
use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\Campaign\Model\Campaign as CampaignModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Heatmap
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
        $campaignId = $this->request->input('campaign_id');
        $startDate = $this->request->input('start_date');
        $endDate = $this->request->input('end_date');
        $deviceListInput = $this->request->input('device_list', []);
        $mediaListInput = $this->request->input('media_list', []);
        $playlistListInput = $this->request->input('playlist_list', []);

        // Lấy start_time và end_time từ Campaign
        $campaign = CampaignModel::query()->findOrFail($campaignId);
        $campaignStart = Carbon::parse($campaign->start_time);
        $campaignEnd = Carbon::parse($campaign->end_time);

        // Chuyển đổi input từ request
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Kiểm tra trường hợp bình thường: start_date và end_date nằm hoàn toàn trong campaign
        $isWithinCampaign = $start->gte($campaignStart) && $end->lte($campaignEnd);
        $daysDiff = $start->diffInDays($end);

        if ($isWithinCampaign && $daysDiff <= 7) {
            // Giữ nguyên start và end nếu nằm trong campaign và <= 7 ngày
        } elseif ($end->lt($campaignEnd)) {
            // Trường hợp end_date < end_time: Lấy từ end_date ngược về 7 ngày
            $start = $end->copy()->subDays(6);
            if ($start->lt($campaignStart)) {
                $start = $campaignStart; // Không vượt quá start_time
            }
        } else {
            // Trường hợp đặc biệt khác: Lấy từ end_time ngược về 7 ngày
            $end = $campaignEnd;
            $start = $campaignEnd->copy()->subDays(6);
            if ($start->lt($campaignStart)) {
                $start = $campaignStart; // Không vượt quá start_time
            }
        }

        // Nếu không giao nhau với campaign, trả về rỗng
        if ($start->gt($campaignEnd) || $end->lt($campaignStart)) {
            return [];
        }

        $deviceList = $this->normalizeInput($deviceListInput);
        $mediaList = $this->normalizeInput($mediaListInput);
        $playlistList = $this->normalizeInput($playlistListInput);

        if (empty($deviceList)) {
            if (!empty($playlistList)) {
                $deviceList = Display::query()
                    ->whereIn('playlist_id', $playlistList)
                    ->pluck('device_id')
                    ->unique()
                    ->toArray();
            } elseif (!empty($mediaList)) {
                $playlistIds = PlaylistMediaModel::query()
                    ->whereIn('media_id', $mediaList)
                    ->whereNull('deleted_at')
                    ->pluck('playlist_id')
                    ->unique()
                    ->toArray();

                $deviceList = Display::query()
                    ->whereIn('playlist_id', $playlistIds)
                    ->pluck('device_id')
                    ->unique()
                    ->toArray();
            } else {
                $mediaIds = MediaModel::query()
                    ->where('campaign_id', $campaignId)
                    ->whereNull('deleted_at')
                    ->pluck('id')
                    ->unique()
                    ->toArray();

                $playlistIds = PlaylistMediaModel::query()
                    ->whereIn('media_id', $mediaIds)
                    ->whereNull('deleted_at')
                    ->pluck('playlist_id')
                    ->unique()
                    ->toArray();

                $deviceList = Display::query()
                    ->whereIn('playlist_id', $playlistIds)
                    ->pluck('device_id')
                    ->unique()
                    ->toArray();
            }
        }

        $chunkedDeviceLists = array_chunk($deviceList, 50);
        $unionQueries = [];

        foreach ($chunkedDeviceLists as $chunk) {
            $inClause = implode(',', array_fill(0, count($chunk), '?'));
            $unionQueries[] = "(
                SELECT 
                    p.device_id, 
                    p.latitude, 
                    p.longitude, 
                    p.date_at, 
                    d.id as device_id, 
                    d.name as device_name,
                    d.serial as device_serial
                FROM position p
                LEFT JOIN device d ON d.id = p.device_id
                WHERE p.device_id IN ($inClause)
                AND p.date_at BETWEEN ? AND ?
                ORDER BY p.date_at DESC
            )";
        }

        $sql = implode(' UNION ALL ', $unionQueries);
        $bindings = [];

        foreach ($chunkedDeviceLists as $chunk) {
            $bindings = array_merge($bindings, $chunk);
            $bindings[] = $start;
            $bindings[] = $end;
        }

        $results = DB::select($sql, $bindings);

        return array_map(function ($item) {
            return [
                'device_id' => $item->device_id,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
                'date_at' => $item->date_at,
                'device' => [
                    'id' => $item->device_id,
                    'name' => $item->device_name,
                    'serial' => $item->device_serial
                ]
            ];
        }, $results);
    }

    protected function normalizeInput($input): array
    {
        if (is_string($input)) {
            $decoded = json_decode($input, true);
            return (json_last_error() === JSON_ERROR_NONE) ? $decoded : [$input];
        }
        return (array) $input;
    }
}