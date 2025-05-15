<?php declare(strict_types=1);

namespace App\Domains\PlaylistGroup\Controller;

use Exception;
use App\Domains\PlaylistGroup\Service\Controller\UpdateService as ControllerService;
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

            return redirect()->route('playlist_group.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('playlist-group-update.meta-title'));

        return $this->page('playlist-group.update', $this->data());
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
        $strategies = [
            'update' => fn () => $this->update(),
            'delete' => fn () => $this->delete(),
            'forceDelete' => fn () => $this->forceDelete(),
            'restore' => fn () => $this->restore(),

        ];

        foreach ($strategies as $action => $callback) {
            if ($this->actionPost($action)) {
                return $callback();
            }
        }

        return false;
    }

    /**
     * @return RedirectResponse
     */
    protected function update(): RedirectResponse
    {
        try {
            $this->action()->update();

            $this->sessionMessage('success', __('playlist-group-update.update.success'));

            return redirect()->route('playlist_group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('playlist_group.index');
        }
    }

    /**
     * @return RedirectResponse
     */
    protected function delete(): RedirectResponse
    {
        try {
            $this->action()->delete();

            $this->sessionMessage('success', __('playlist-group-update.delete.success'));

            return redirect()->route('playlist_group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('playlist_group.index');
        }
    }

    /**
     * @return RedirectResponse
     */
    protected function forceDelete(): RedirectResponse
    {
        try {
            $this->action()->forceDelete();

            $this->sessionMessage('success', __('playlist-group-update.force-delete.success'));

            return redirect()->route('playlist_group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('playlist_group.index');
        }
    }

    /**
     * @return RedirectResponse
     */
    protected function restore(): RedirectResponse
    {
        try {
            $this->action()->restore();

            $this->sessionMessage('success', __('playlist-group-update.restore.success'));

            return redirect()->route('playlist_group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('playlist_group.index');
        }
    }
}
