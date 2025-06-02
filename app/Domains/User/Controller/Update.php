<?php declare(strict_types=1);

namespace App\Domains\User\Controller;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\User\Service\Controller\Update as ControllerService;

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

            return redirect()->route('user.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('user-update.meta-title', ['title' => $this->row->name]));

        return $this->page('user.update', $this->data());
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

            $this->sessionMessage('success', __('user-update.success'));

            return redirect()->route('user.update', $this->row->id);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('user.update', $this->row->id);
        }
    }

    /**
     * @return RedirectResponse
     */
    protected function delete(): RedirectResponse
    {
        try {
            $this->action()->delete();

            $this->sessionMessage('success', __('user-update.delete-success'));
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
        } finally {
            return redirect()->route('user.index');
        }

    }
}
