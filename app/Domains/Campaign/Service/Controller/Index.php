<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Service\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Domains\Campaign\Model\Campaign as Model;
use App\Domains\User\Enterprise\Model\Enterprise;

class Index
{
    protected Request $request;
    protected mixed $auth;

    public function __construct(Request $request, mixed $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new(Request $request, mixed $auth): static
    {
        return new static($request, $auth);
    }

    public function data(): array
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $campaigns = $this->list();

        $filteredCampaigns = $campaigns->filter(function ($campaign) use ($user) {
            if ($user->isRoleRoot()) {
                return true;
            }
            if ($user->isOwner()) {
                return true;
            }
            return false;
        });

        $enterprises = $user->isRoleRoot() ? Enterprise::all() : collect();

        return [
            'campaign' => $filteredCampaigns,
            'search' => $this->request->get('search'),
            'enterprises' => $enterprises,
        ];
    }

    protected function list(): Collection
    {
        $query = Model::query();
        $user = \Illuminate\Support\Facades\Auth::user();

        if (!$user->isRoleRoot()) {
            $query->byEnterprise();
        }

        if ($user->isRoleRoot() && $this->request->filled('enterprise_id')) {
            $query->where('enterprise_id', $this->request->get('enterprise_id'));
        }

        $query->select([
            'campaign.id',
            'campaign.name',
            'campaign.start_time',
            'campaign.end_time',
            'campaign.enterprise_id',
            'campaign.budget',
            'campaign.status',
            'campaign.deleted_at',
            'campaign.performance_id',
            'campaign.location_id',
            'enterprise.name as enterprise_name',
        ])
            ->leftJoin('enterprise', 'campaign.enterprise_id', '=', 'enterprise.id')
            ->with(['media', 'performance', 'location', 'users'])
            ->withTrashed();

        if ($this->request->filled('search')) {
            $search = $this->request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('campaign.name', 'LIKE', "%{$search}%")
                    ->orWhere('campaign.status', 'LIKE', "%{$search}%");
            });
        }

        return $query->get()->map(function ($campaign) {
            return $this->formatCampaign($campaign);
        });
    }

    public function responseJsonList(): Collection
    {
        return $this->list();
    }

    public function formatCampaign(Model $campaign): array
    {
        $actualReach = $campaign->performance ? $campaign->performance->actual_reach : 0;
        $cost = $campaign->budget ?? 0;
        $actualCpm = $actualReach > 0 ? round(($cost / $actualReach) * 1000) : 0;

        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'enterprise_name' => $campaign->enterprise_name ?? 'N/A',
            'media_names' => $campaign->media->pluck('name')->implode(', ') ?: 'N/A',
            'user_names' => $campaign->users->pluck('name')->implode(', ') ?: 'N/A',
            'start_time' => $campaign->start_time->toDateTimeString(),
            'end_time' => $campaign->end_time->toDateTimeString(),
            'budget' => $campaign->budget,
            'status' => $campaign->status,
            'deleted_at' => $campaign->deleted_at ? $campaign->deleted_at->toDateTimeString() : null,
            'reach' => [
                'actual' => $actualReach > 0 ? $actualReach : 0,
                'target' => $campaign->performance ? $campaign->performance->reach : 0,
            ],
            'impression' => [
                'actual' => $campaign->performance ? $campaign->performance->actual_impression : 0,
                'target' => $campaign->performance ? $campaign->performance->impression : 0,
            ],
            'distance' => [
                'actual' => $campaign->performance ? $campaign->performance->actual_distance : 0,
                'target' => $campaign->performance ? $campaign->performance->distance : 0,
            ],
            'no_device' => $campaign->performance ? $campaign->performance->no_device : 0, // Added no_device
            'cpm' => [
                'actual' => $actualCpm,
                'target' => $campaign->performance ? $campaign->performance->cpm : 0,
            ],
            'city' => $campaign->location ? $campaign->location->city : 'N/A',
        ];
    }
}
