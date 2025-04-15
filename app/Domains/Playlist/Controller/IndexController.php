<?php

namespace App\Domains\Playlist\Controller;

use App\Domains\Playlist\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('playlist-index.meta-title'));

        return $this->page('campaign.playlist.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
