<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Service\Controller\Create as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class Create extends ControllerAbstract
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $this->meta('title', __('schedule-create.meta-title'));

        if ($request->isMethod('post')) {
            return $this->create($request);
        }

        $service = ControllerService::new($request, $this->auth);

        return $this->page('schedule.create', $service->data());
    }

    protected function create(Request $request): RedirectResponse
    {
        $service = ControllerService::new($request, $this->auth);
        try {
            $schedule = $service->create();
            $this->sessionMessage('success', __('schedule-create.success'));

            return redirect()->route('schedule.update', $schedule->id);
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->errors());
        }
    }
}
