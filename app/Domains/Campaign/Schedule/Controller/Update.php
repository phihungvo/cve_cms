<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Campaign\Schedule\Service\Controller\Update as UpdateService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Update extends ControllerAbstract
{
    public function __invoke(Request $request, int $id): Response|RedirectResponse
    {
        $schedule = Schedule::findOrFail($id);

        $this->meta('title', __('schedule-update.meta-title'));

        // Xử lý cả POST và PUT
        if ($request->isMethod('post') || $request->isMethod('patch')) {
            return $this->update($request, $schedule);
        }

        $service = UpdateService::new($request, $this->auth, $schedule);

        return $this->page('schedule.update', $service->data());
    }

    protected function update(Request $request, Schedule $schedule): RedirectResponse
    {
        $service = UpdateService::new($request, $this->auth, $schedule);

        try {
            $service->update();
            $this->sessionMessage('success', __('schedule-update.success'));

            return redirect()->route('schedule.update', $schedule->id);
        } catch (ValidationException $e) {
            // Ghi log lỗi validation để debug
            Log::error('Validation failed: ', $e->errors());

            return redirect()->back()->withInput()->withErrors($e->errors());
        }
    }
}
