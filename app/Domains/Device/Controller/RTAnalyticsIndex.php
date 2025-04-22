<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RTAnalyticsIndex as ControllerService;
use Illuminate\Http\Response;

class RTAnalyticsIndex extends ControllerAbstract
{
    public function __invoke(int $id): Response
    {
        try {
            $this->row($id);
        } catch (\NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('device.runtime-analytics');
        }

        $this->meta('title', __('rt-analytics-index.meta-title'));

        return $this->page('device.rt-analytics-index', $this->data());
    }

    protected function data():array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
