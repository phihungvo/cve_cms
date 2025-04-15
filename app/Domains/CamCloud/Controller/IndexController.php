<?php

namespace App\Domains\CamCloud\Controller;

use App\Domains\CamCloud\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('camcloud-index.meta-title'));

        return $this->page(
            'cam-cloud.index',
            $this->data()
        );
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
