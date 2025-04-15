<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class Toggle extends ControllerAbstract
{
    public function __invoke(Request $request, int $id): RedirectResponse
    {
        $scheduleDetail = Schedule::findOrFail($id);

        $field = $request->input('field'); // repeat hoặc active
        $value = $request->has($field) ? (bool) $request->input($field) : false;

        if (in_array($field, ['repeat', 'active'])) {
            $scheduleDetail->update([$field => $value]);
            $this->sessionMessage('success', __("schedule-{$field}-updated"));
        } else {
            $this->sessionMessage('error', __('Invalid field'));
        }

        return redirect()->route('schedule.index');
    }
}
