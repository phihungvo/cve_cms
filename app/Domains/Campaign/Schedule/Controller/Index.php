<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Action\Delete;
use App\Domains\Campaign\Schedule\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class Index extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('schedule-index.meta-title'));

        return $this->page('schedule.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    public function destroy(): Response|RedirectResponse
    {
        $id = (int) $this->request->query('schedule_id');

        $action = new Delete();
        $result = $action->handle($id, $this->auth);

        if ($result['success']) {
            $this->sessionMessage('success', __($result['message']));

            return redirect()->route('schedule.index');
        } else {
            $this->sessionMessage('error', __($result['message']));

            return redirect()->route('schedule.index');
        }
    }
}
