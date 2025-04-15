<?php

declare(strict_types=1);

namespace App\Domains\MediaMTX\Service\Controller;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class Paths
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth) {}

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     * @return static
     */
    public static function new(Request $request, Authenticatable $auth): static
    {
        return new static($request, $auth);
    }

    /**
     * @return array
     */
    public function data(): array
    {
        // Lấy thông tin từ .env với giá trị mặc định
        $baseServer = config('filesystems.mediamtx.url');
        $baseEndpoint = '/v3/paths/list';
        $baseUrl = "{$baseServer}{$baseEndpoint}";

        // Gọi API bằng Guzzle qua facade Http
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->get($baseUrl);

        // Kiểm tra nếu request thành công
        if ($response->successful()) {
            $data = $response->json();
            return ['paths' => $data['items'] ?? []]; // Trả về mảng paths, mặc định rỗng nếu không có items
        }

        // Nếu thất bại, trả về mảng rỗng hoặc xử lý lỗi tùy ý
        return ['paths' => [], 'error' => 'Không thể lấy dữ liệu từ MediaMTX'];
    }
}
