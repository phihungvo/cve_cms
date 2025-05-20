<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Solution\Controller;

use App\Domains\Cvedixrt\Solution\Controller\ControllerAbstract;
use App\Domains\Cvedixrt\Solution\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('cvedixrt-solution-index.meta-title'));

        return $this->page('cvedixrt.solution.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
