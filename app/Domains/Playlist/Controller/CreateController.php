<?php

namespace App\Domains\Playlist\Controller;

use App\Domains\Playlist\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }
        $this->meta('title', __('playlist-create.meta-title'));

        return $this->page('campaign.playlist.create', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    /**
     * @return RedirectResponse
     */
    protected function create(): RedirectResponse
    {
        $this->row = $this->action()->create();

        $this->sessionMessage('success', __('playlist-create.success'));

        return redirect()->route('fpp.playlist.update', $this->row->id);
    }
}
