<?php declare(strict_types=1);

namespace App\Domains\Group\Controller;

use App\Domains\Group\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->device((int)$this->request->query('deviceId'));
        $this->meta('title', __('group-index.meta-title'));

        return $this->page('group.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->device)->data();
    }
}
