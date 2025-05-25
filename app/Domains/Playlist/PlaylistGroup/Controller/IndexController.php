<?php declare(strict_types=1);

namespace App\Domains\Playlist\PlaylistGroup\Controller;

use App\Domains\Playlist\PlaylistGroup\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('playlist-group-index.meta-title'));

        return $this->page('campaign.playlist.playlist-group.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
