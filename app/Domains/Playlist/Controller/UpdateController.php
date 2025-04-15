<?php

namespace App\Domains\Playlist\Controller;

use App\Domains\Playlist\Service\Controller\UpdateService as ControllerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class UpdateController extends ControllerAbstract
{
    /**
     * @param int $id
     *
     * @return Response|RedirectResponse
     */
    public function __invoke(int $id): Response|RedirectResponse
    {
        try {
            $this->row($id);
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('fpp.playlist.index');
        }
        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('playlist-update.meta-title', ['title' => $this->row->name]));

        return $this->page('campaign.playlist.update', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)
            ->data();
    }

    /**
     * @return RedirectResponse|false|null
     */
    protected function actions(): RedirectResponse|false|null
    {
        if ($this->actionPost('update')) {
            return $this->actionPost('update');
        } elseif ($this->actionPost('delete')) {
            return $this->actionPost('delete');
        } elseif ($this->actionPost('forceDelete')) {
            return $this->actionPost('forceDelete');
        } elseif ($this->actionPost('restore')) {
            return $this->actionPost('restore');
        }

        return false;
    }

    /**
     * @return RedirectResponse
     */
    protected function update(): RedirectResponse
    {
        $this->action()->update();

        $this->sessionMessage('success', __('playlist-update.success'));

        return redirect()->route('fpp.playlist.update', $this->row->id);
    }

    /**
     * @return RedirectResponse
     */
    protected function delete(): RedirectResponse
    {
        $this->action()->delete();

        $this->sessionMessage('success', __('playlist-update.delete-success'));

        return redirect()->route('fpp.playlist.index');
    }

    protected function forceDelete(): RedirectResponse
    {
        $this->action()->forceDelete();
        $this->sessionMessage('success', __('playlist-update.delete-success'));

        return redirect()->route('fpp.playlist.index');
    }

    protected function restore(): RedirectResponse
    {
        $this->action()->restore();
        $this->sessionMessage('success', __('playlist-update.restore-success'));

        return redirect()->route('fpp.playlist.index');
    }
}
