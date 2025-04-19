<?php declare(strict_types=1);

namespace App\Domains\Device\Service\ControllerApi;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Device\Model\DeviceCaptureMedia as DeviceCaptureMediaModel;
use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\Campaign\Model\Campaign as CampaignModel;

class GetListMediaCapture extends ControllerApiAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     * @param string|null $id
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected ?string $id)
    {
    }

    /**
     * @return array
     */
    public function data(): array
    {
        if (!$this->id) {
            return [];
        }

        // Get media_ids from DeviceCaptureMedia where device_id matches and enable = 1
        $mediaIds = DeviceCaptureMediaModel::query()
            ->where('device_id', $this->id)
            ->where('enabled', 1)
            ->pluck('media_id')
            ->toArray();

        if (empty($mediaIds)) {
            return [];
        }

        // Get media details from MediaModel with campaign name from CampaignModel
        $media = MediaModel::query()
            ->with([
                'campaign' => function ($query) {
                    $query->select('id', 'name'); // Adjust 'name' to the actual column if different
                }
            ])
            ->whereIn('id', $mediaIds)
            ->get()
            ->map(function ($item) {
                return array_merge($item->toArray(), [
                    'campaign_name' => $item->campaign->name ?? null, // Adjust 'name' to the actual column if different
                ]);
            })
            ->toArray();

        return $media;
    }
}