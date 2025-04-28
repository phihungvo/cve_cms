<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Exceptions\NotFoundException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class ControllerAbstract
{
    protected Request $request;
    protected Authenticatable $auth;
    protected Schedule $row;

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

    /**
     * @param int $id
     * @return Schedule
     *
     */
   protected function row(int $id): Schedule
    {
        return $this->row = Schedule::query()
            ->byId($id)
            ->firstOr(fn() => throw new NotFoundException(__('schedule-index.error.not-found')));
    }
}
