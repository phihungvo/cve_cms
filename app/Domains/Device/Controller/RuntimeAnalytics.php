<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RuntimeAnalytics as ControllerService;

class RuntimeAnalytics extends ControllerAbstract
{
    public function __invoke(int $id)
    {
        $this->row($id);


        if($response = $this->actionPost('create')){
            return $response;
        }

        $this->meta('title', __('device-create.meta-title-analytics'));

        return $this->page('device.update-runtime-analytics', $this->data());
    }

    protected function data():array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
