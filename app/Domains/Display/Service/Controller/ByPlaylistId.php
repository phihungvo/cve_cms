<?php

namespace App\Domains\Display\Service\Controller;

use App\Domains\Display\Model\Display;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class ByPlaylistId
{
    protected Request $request;

    protected ?Authenticatable $auth;

    public function __construct(Request $request, Authenticatable $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new(Request $request, $auth)
    {
        return new self($request, $auth);
    }

    public function getDisplayByPlaylistId(): array
    {
        $playlistId = $this->request->get('playlist_id');

        // Logic to fetch displays by playlist ID
        // This is just a placeholder, replace with actual logic
        $devices = Display::where('playlist_id', $playlistId)
            ->with('device') // Giả sử có quan hệ 'device' được định nghĩa trong model Display
            ->get()
            ->map(function ($display) {
                return [
                    'display' => $display, // Bao gồm cả đối tượng display
                    'device' => $display->device, // Lấy thông tin device từ mỗi display
                ];
            })
            ->toArray();

        return $devices;
    }
}
