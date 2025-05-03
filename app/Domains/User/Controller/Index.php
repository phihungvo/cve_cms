<?php declare(strict_types=1);

namespace App\Domains\User\Controller;

use App\Domains\User\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class Index extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function __invoke(): Response
    {
        $this->meta('title', __('user-index.meta-title'));

        return $this->page('user.index', $this->data());
    }

    protected function data()
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
