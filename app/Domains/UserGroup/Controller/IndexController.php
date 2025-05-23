<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;


use App\Domains\UserGroup\Service\Controller\IndexService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('user-group-index.meta-title'));

        return $this->page('user-group.index', $this->data());
    }

    protected function data(): array
    {
        return IndexService::new($this->request, $this->auth)->data();
    }
}
