<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RuntimeAnalytics as ControllerService;
use Illuminate\Http\Response;

class RTAnalyticsIndex extends ControllerAbstract
{
    public function __invoke(int $id): Response
    {
        $this->row($id);

        $this->meta('title', __('device-create.meta-title-analytics'));

        return $this->page('device.rt-analytics-index', $this->data());
    }

    protected function data():array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
