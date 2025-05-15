<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Controller;

use App\Domains\CvedixSolution\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('cvedix-solution-index.meta-title'));

        return $this->page('cvedix-solution.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
