<?php

declare(strict_types=1);

namespace App\Domains\Video\Service\Controller;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Validator;
use App\Domains\Video\Model\Video as Model;

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

    public function create(): Model
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'required|string|max:255',
            'video_url' => 'required|url',
            'reach_target' => 'required|integer|min:0',
            'distance_target' => 'required|integer|min:0',
            'impression_target' => 'required|integer|min:0',
            'device_target' => 'required|integer|min:0',
            'cpm_target' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'video_type' => 'required|in:0,1,2',
            'enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        return Model::create($this->request->only([
            'name',
            'video_url',
            'reach_target',
            'distance_target',
            'impression_target',
            'device_target',
            'cpm_target',
            'cost',
            'video_type',
            'enabled'
        ]));
    }

    public function update(Model $video): Model
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'required|string|max:255',
            'video_url' => 'required|url',
            'reach_target' => 'required|integer|min:0',
            'distance_target' => 'required|integer|min:0',
            'impression_target' => 'required|integer|min:0',
            'device_target' => 'required|integer|min:0',
            'cpm_target' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'video_type' => 'required|in:0,1,2',
            'enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            throw new \Exception($validator->errors()->first());
        }

        $video->update($this->request->only([
            'name',
            'video_url',
            'reach_target',
            'distance_target',
            'impression_target',
            'device_target',
            'cpm_target',
            'cost',
            'video_type',
            'enabled'
        ]));

        return $video;
    }
}
