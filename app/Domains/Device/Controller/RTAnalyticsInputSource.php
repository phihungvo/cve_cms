<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RTAnalyticsInputSource as ControllerService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RTAnalyticsInputSource extends ControllerAbstract
{
    public function __invoke(int $id): Response|RedirectResponse
    {
        try {
            $this->row($id);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('device.index');
        }

        $this->meta('title', __('rt-analytics-input-source.meta-title'));

        return $this->page('device.rt-analytics-input-source', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
