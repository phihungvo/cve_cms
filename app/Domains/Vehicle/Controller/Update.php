<?php declare(strict_types=1);

namespace App\Domains\Vehicle\Controller;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\Vehicle\Service\Controller\Update as ControllerService;

class Update extends ControllerAbstract
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
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('vehicle.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('vehicle-update.meta-title', ['title' => $this->row->name]));

        return $this->page('vehicle.update', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }

    /**
     * @return RedirectResponse|false|null
     */
    protected function actions(): RedirectResponse|false|null
    {
        return $this->actionPost('update')
            ?: $this->actionPost('delete');
    }

    /**
     * @return RedirectResponse
     */
    protected function update(): RedirectResponse
    {
        try {
            $this->action()->update();

            $this->sessionMessage('success', __('vehicle-update.success'));

            return redirect()->route('vehicle.update', $this->row->id);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('vehicle.index');
        }
    }

    /**
     * @return RedirectResponse
     */
    protected function delete(): RedirectResponse
    {
        $this->action()->delete();

        $this->sessionMessage('success', __('vehicle-update.delete-success'));

        return redirect()->route('vehicle.index');
    }
}
