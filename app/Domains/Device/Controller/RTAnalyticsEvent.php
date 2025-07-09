<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RTAnalyticsEvent as ControllerService;
use App\Exceptions\NotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RTAnalyticsEvent extends ControllerAbstract
{
    public function __invoke(int $id): Response|RedirectResponse|JsonResponse
    {
        try {
            $this->row($id);
            $instance_id = (int)$this->request->input('instance_id');
            if ($instance_id) {
                $this->instance($instance_id);
            }
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('device.index');
        }

        $this->meta('titile', __('rt-analytics-event.meta-title'));

        return $this->page('device.rt-analytics-event', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
