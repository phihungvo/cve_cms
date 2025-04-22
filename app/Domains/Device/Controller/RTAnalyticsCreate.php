<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;


use App\Domains\Device\Service\Controller\RTAnalyticsCreate as ServiceController;
use App\Exceptions\NotFoundException;

class RTAnalyticsCreate extends ControllerAbstract
{
    public function __invoke(int $id)
    {
        try {
            $this->row($id);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('device.runtime-analytics');
        }
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('device-create.meta-title-analytics'));

        return $this->page('device.rt-analytics-create', $this->data());
    }

    protected function data(): array
    {
        return ServiceController::new($this->request, $this->auth, $this->row)->data();
    }

    public function create()
    {
        try {
            $this->action()->createInstance();
            $this->sessionMessage('success', __('create instance success'));
        } catch (\Exception $exception) {
            $this->sessionMessage('error', $exception->getMessage());

            return redirect()->back()->withInput();
        }
    }
}
