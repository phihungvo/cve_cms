<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RTAnalyticsIndex as ControllerService;
use App\Exceptions\NotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RTAnalyticsIndex extends ControllerAbstract
{
    public function __invoke(int $id): Response|RedirectResponse
    {
        try {
            $this->row($id);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('device.index');
        }

        $this->meta('title', __('rt-analytics-index.meta-title'));

        return $this->page('device.rt-analytics-index', $this->data());
    }

    protected function data():array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
