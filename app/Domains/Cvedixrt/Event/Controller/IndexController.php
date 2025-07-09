<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Controller;

use App\Domains\Cvedixrt\Event\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('cvedixrt-event-index.meta-title'));

        return $this->page('cvedixrt.event.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
