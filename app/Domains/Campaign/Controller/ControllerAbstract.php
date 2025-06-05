<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected $meta = [];

    protected function meta(string $key, $value): void
    {
        $this->meta[$key] = $value;
    }

    // protected function json($data = [], int $status = 200, array $headers = []): JsonResponse
    // {
    //     return response()->json(
    //         array_replace_recursive(['status' => true], $data),
    //         $status,
    //         $headers
    //     );
    // }

    // protected function page(string $view, array $data = []): View
    // {
    //     $meta = is_array($this->meta) ? $this->meta : [];
    //     $mergedData = array_replace_recursive($data, ['meta' => $meta]);
    //     return view($view, $mergedData);
    // }
}
