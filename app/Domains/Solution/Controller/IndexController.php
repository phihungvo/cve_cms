<?php

namespace App\Domains\Solution\Controller;
use App\Domains\Solution\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{

    public function __invoke(): Response
    {
        $this->device((int)$this->request->query('deviceId'));
        $this->meta('title', __('solution-index.meta-title'));

        return $this->page('solution.index', $this->data());
    }

    protected function data():array
    {
       return ControllerService::new($this->request, $this->auth, $this->device)->data();
    }
}
