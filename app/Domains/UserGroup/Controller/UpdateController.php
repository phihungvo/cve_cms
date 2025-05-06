<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;

use App\Domains\UserGroup\Service\Controller\UpdateService;
use App\Exceptions\NotFoundException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;

class UpdateController extends ControllerAbstract
{
    public function __invoke(int $id)
    {
        try {
            $this->row($id);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('group.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('user-group-update.meta-title'));

        return $this->page('user-group.update', $this->data());
    }

    protected function actions()
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

    protected function data()
    {
        return UpdateService::new($this->request, $this->auth, $this->row)->data();
    }

    public function update(): RedirectResponse
    {
        try {
            $this->row = $this->action()->update();

            $this->sessionMessage('success', __('user-group-update.update.success'));

            return redirect()->route('user-group.index');
        } catch (ModelNotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->back()->withInput();
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            // Log the unexpected exception
            logger()->error($e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput();
        }
    }

    public function delete(): RedirectResponse
    {
        try {
            $this->action()->delete();
            $this->sessionMessage('success', __('user-group-update.delete.success'));

            return redirect()->route('user-group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e.getMessage());

            // Log the unexpected exception
            logger()->error('Unexpected error in UserGroup delete: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect(route('user-group.index'));
        }
    }

    public function forceDelete(): RedirectResponse
    {
        try {
            $this->action()->forceDelete();
            $this->sessionMessage('success', __('user-group-update.force-delete.success'));

            return redirect()->route('user-group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            // Log the unexpected exception
            logger()->error('Unexpected error in UserGroup force delete: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect(route('group.index'));
        }
    }

    public function restore(): RedirectResponse
    {
        try {
            $this->action()->restore();
            $this->sessionMessage('success', __('user-group-update.restore.success'));

            return redirect()->route('group.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            // Log the unexpected exception
            logger()->error('Unexpected error in UserGroup restore: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect(route('user-group.index'));
        }
    }
}
