<?php

namespace App\Domains\Playlist\Validate;

use App\Domains\Core\Validate\ValidateAbstract;

class PushMessage extends ValidateAbstract
{
    public function rules(): array
    {
        return [
            'playlist_id' => ['required', 'integer', 'exists:playlist,id'],
        ];
    }
}
