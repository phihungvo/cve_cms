<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Group\Controller;

use App\Domains\Cvedixrt\Group\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('cvedixrt-group-index.meta-title'));

        return $this->page('cvedixrt.group.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
