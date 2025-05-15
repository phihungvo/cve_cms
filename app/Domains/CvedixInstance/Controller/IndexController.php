<?php declare(strict_types=1);

namespace App\Domains\CvedixInstance\Controller;

use App\Domains\CvedixInstance\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('cvedix-instance-index.meta-title'));

        return $this->page('cvedix-instance.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
