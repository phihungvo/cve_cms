<?php declare(strict_types=1);

namespace App\Domains\Playlist\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class Create extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'name' => ['string', 'required'],
            'description' => ['string', 'required'],
            'medias' => ['array', 'nullable'], // Danh sách media là mảng
            // Mỗi phần tử phải là ID hợp lệ trong bảng media hoặc null
            'medias.*.id' => ['nullable', 'integer', 'exists:media,id'],
            'medias.*.position' => ['required', 'integer', 'min:1'],
            'enterprise_id' => ['nullable', 'integer', 'exists:enterprise,id'],
            'deviceIds' => ['array', 'nullable'], // Danh sách thiết bị là mảng
            'playlist_groups' => ['bail', 'nullable', 'array'],
            'playlist_group.*' => ['bail', 'integer'],
        ];
    }
}
