<?php declare(strict_types=1);

namespace App\Domains\PlaylistGroup\Controller;

use Exception;
use App\Domains\PlaylistGroup\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('playlist-group-create.meta-title'));

        return $this->page('playlist-group.create', $this->data());
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
        try {
            $this->row = $this->action()->create();

            $this->sessionMessage('success', __('playlist-group-create.success'));

            return redirect()->route('playlist_group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('playlist-group.create')->withInput();
        }
    }
}
