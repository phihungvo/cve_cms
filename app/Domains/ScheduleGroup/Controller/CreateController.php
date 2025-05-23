<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Controller;

use Exception;
use App\Domains\ScheduleGroup\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('schedule-group-create.meta-title'));

        return $this->page('schedule-group.create', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    /**
     * @return RedirectResponse
     */
    protected function create(): RedirectResponse
    {
        try {
            $this->row = $this->action()->create();

            $this->sessionMessage('success', __('schedule-group-create.success'));

            return redirect()->route('schedule_group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('schedule_group.create')->withInput();
        }
    }
}
