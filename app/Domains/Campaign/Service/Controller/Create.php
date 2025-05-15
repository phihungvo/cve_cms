<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Service\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Validator;
use App\Domains\Campaign\Model\Campaign as Model;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\Campaign\Performance\Model\Performance;
use App\Domains\Campaign\Location\Model\Location;
use App\Domains\Campaign\Media\Model\Media;

class Create
{
    protected Request $request;

    protected ?Authenticatable $auth;

    public function __construct(Request $request, ?Authenticatable $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new(Request $request, ?Authenticatable $auth): self
    {
        return new self($request, $auth);
    }

    public function data(): array
    {
        $enterpriseId = $this->auth && !$this->auth->isRoleRoot() ? $this->auth->enterprise_id : null;
        $campaignId = $this->request->route('id');
        if ($campaignId) {
            $campaign = Model::find($campaignId);
            $enterpriseId = $campaign ? $campaign->enterprise_id : $enterpriseId;
        }

        $mediaQuery = Media::query();
        if ($enterpriseId) {
            $mediaQuery->where('enterprise_id', $enterpriseId);
        }

        $usersQuery = \App\Domains\User\Model\User::query();
        if ($this->auth->isRoleRoot()) {
            if ($enterpriseId) {
                $usersQuery->where('enterprise_id', $enterpriseId);
            }
        } else {
            $usersQuery->where('enterprise_id', $this->auth->enterprise_id)
                ->where('id', '!=', $this->auth->id);
        }

        return [
            'enterprises' => Enterprise::all(),
            'performances' => Performance::all(),
            'locations' => Location::all(),
            'media' => $mediaQuery->get(),
            'users' => $usersQuery->get(),
        ];
    }

    public function store(): array
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'enterprise_id' => 'required|exists:enterprise,id',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:media,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:user,id',
            'reach' => 'required|integer|min:0',
            'impression' => 'required|integer|min:0',
            'distance' => 'required|integer|min:0',
            'no_device' => 'required|integer|min:0', // Added validation for no_device
            'cpm' => 'nullable|integer|min:0',
            'location_id' => 'required|exists:location,id',
            'budget' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $reach = $this->request->input('reach');
        $budget = $this->request->input('budget');
        $cpm = $this->request->input('cpm');

        if (is_null($cpm) && $reach > 0) {
            $cpm = round(($budget / $reach) * 1000);
        } elseif (is_null($cpm)) {
            $cpm = 0;
        }

        $performance = Performance::create([
            'reach' => $reach,
            'impression' => $this->request->input('impression'),
            'distance' => $this->request->input('distance'),
            'no_device' => $this->request->input('no_device'), // Added no_device
            'cpm' => $cpm,
        ]);

        $data = $this->request->only([
            'name',
            'start_time',
            'end_time',
            'enterprise_id',
            'location_id',
            'budget',
            'status',
        ]);
        $data['performance_id'] = $performance->id;

        if ($this->auth) {
            $data['user_id'] = $this->auth->id;
        }

        $campaign = Model::create($data);

        if ($this->request->has('media_ids')) {
            Media::whereIn('id', $this->request->input('media_ids'))
                ->update(['campaign_id' => $campaign->id]);
        }

        if ($this->request->has('user_ids')) {
            $campaign->users()->sync($this->request->input('user_ids'));
        }

        return [
            'status' => true,
            'message' => __('campaign-create.success'),
            'data' => $this->formatCampaign($campaign),
        ];
    }

    public function update(int $id): array
    {
        $campaign = Model::findOrFail($id);

        $validator = Validator::make($this->request->all(), [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'enterprise_id' => 'required|exists:enterprise,id',
            'media_ids' => 'nullable|array',
            'media_ids.*' => 'exists:media,id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:user,id',
            'reach' => 'required|integer|min:0',
            'impression' => 'required|integer|min:0',
            'distance' => 'required|integer|min:0',
            'no_device' => 'required|integer|min:0', // Added validation for no_device
            'cpm' => 'nullable|integer|min:0',
            'location_id' => 'required|exists:location,id',
            'budget' => 'required|numeric|min:0',
            'status' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $reach = $this->request->input('reach');
        $budget = $this->request->input('budget');
        $cpm = $this->request->input('cpm');

        if (is_null($cpm) && $reach > 0) {
            $cpm = round(($budget / $reach) * 1000);
        } elseif (is_null($cpm)) {
            $cpm = 0;
        }

        if ($campaign->performance) {
            $campaign->performance->update([
                'reach' => $reach,
                'impression' => $this->request->input('impression'),
                'distance' => $this->request->input('distance'),
                'no_device' => $this->request->input('no_device'), // Added no_device
                'cpm' => $cpm,
            ]);
        } else {
            $performance = Performance::create([
                'reach' => $reach,
                'impression' => $this->request->input('impression'),
                'distance' => $this->request->input('distance'),
                'no_device' => $this->request->input('no_device'), // Added no_device
                'cpm' => $cpm,
            ]);
            $campaign->performance_id = $performance->id;
        }

        $data = $this->request->only([
            'name',
            'start_time',
            'end_time',
            'enterprise_id',
            'location_id',
            'budget',
            'status',
        ]);

        if ($this->auth) {
            $data['user_id'] = $this->auth->id;
        }

        $campaign->update($data);

        Media::where('campaign_id', $campaign->id)
            ->whereNotIn('id', $this->request->input('media_ids', []))
            ->update(['campaign_id' => null]);

        if ($this->request->has('media_ids')) {
            Media::whereIn('id', $this->request->input('media_ids'))
                ->update(['campaign_id' => $campaign->id]);
        }

        if ($this->request->has('user_ids')) {
            $campaign->users()->sync($this->request->input('user_ids'));
        } else {
            $campaign->users()->detach();
        }

        return [
            'status' => true,
            'message' => __('campaign-edit.success'),
            'data' => $this->formatCampaign($campaign),
        ];
    }

    public function destroy(int $id): array
    {
        $campaign = Model::withTrashed()->findOrFail($id);
        Media::where('campaign_id', $campaign->id)->update(['campaign_id' => null]);
        $campaign->delete();

        return [
            'status' => true,
            'message' => __('campaign-delete.success'),
            'data' => $this->formatCampaign($campaign),
        ];
    }

    public function restore(int $id): array
    {
        $campaign = Model::withTrashed()->findOrFail($id);
        $campaign->restore();

        return [
            'status' => true,
            'message' => __('campaign-restore.success'),
            'data' => $this->formatCampaign($campaign),
        ];
    }

    public function forceDelete(int $id): array
    {
        $campaign = Model::withTrashed()->findOrFail($id);
        Media::where('campaign_id', $campaign->id)->update(['campaign_id' => null]);
        $campaign->users()->detach();
        $campaign->forceDelete();

        return [
            'status' => true,
            'message' => __('campaign-delete.success'),
            'data' => null,
        ];
    }

    public function formatCampaign(Model $campaign): array
    {
        $actualReach = $campaign->performance ? $campaign->performance->actual_reach : 0;
        $cost = $campaign->budget ?? 0;
        $actualCpm = $actualReach > 0 ? round(($cost / $actualReach) * 1000) : 0;

        return [
            'id' => $campaign->id,
            'name' => $campaign->name,
            'media_names' => $campaign->media->pluck('name')->implode(', ') ?: 'N/A',
            'media_ids' => $campaign->media->pluck('id')->toArray(),
            'user_ids' => $campaign->users->pluck('id')->toArray(),
            'user_names' => $campaign->users->pluck('name')->implode(', ') ?: 'N/A',
            'start_time' => $campaign->start_time->toDateTimeString(),
            'end_time' => $campaign->end_time->toDateTimeString(),
            'enterprise_id' => $campaign->enterprise_id,
            'enterprise_name' => $campaign->enterprise ? $campaign->enterprise->name : 'N/A',
            'performance_id' => $campaign->performance_id,
            'reach' => $campaign->performance ? $campaign->performance->reach : 0,
            'impression' => $campaign->performance ? $campaign->performance->impression : 0,
            'distance' => $campaign->performance ? $campaign->performance->distance : 0,
            'no_device' => $campaign->performance ? $campaign->performance->no_device : 0, // Added no_device
            'cpm' => $campaign->performance ? $campaign->performance->cpm : 0,
            'location_id' => $campaign->location_id,
            'budget' => $campaign->budget,
            'status' => $campaign->status,
            'created_at' => $campaign->created_at->toDateTimeString(),
            'updated_at' => $campaign->updated_at->toDateTimeString(),
            'deleted_at' => $campaign->deleted_at ? $campaign->deleted_at->toDateTimeString() : null,
        ];
    }
}
