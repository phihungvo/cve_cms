<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class ControllerAbstract
{
    protected Request $request;
    protected Authenticatable $auth;

    public function __construct(Request $request, Authenticatable $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    protected function meta(string $key, string $value): void
    {
        view()->share('meta_' . $key, $value);
    }

    protected function page(string $view, array $data = []): Response
    {
        return new Response(view($view, $data));
    }
}
