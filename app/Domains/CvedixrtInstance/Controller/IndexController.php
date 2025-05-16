<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Controller;

use App\Domains\CvedixrtInstance\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response|RedirectResponse
    {
        $this->meta('title', __('cvedixrt-instance-index.meta-title'));

        return $this->page('cvedixrt-instance.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
