<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;

use App\Domains\UserGroup\Service\Controller\UpdateService;
use App\Exceptions\NotFoundException;
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

        $this->meta('title', __('user-group.update.title'));

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

            $this->sessionMessage('success', __('user-group.update.success'));

            return redirect()->route('group.index');
        } catch (ModelNotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->back()->withInput();
        } catch (\Exception $e) {
            $this->sessionMessage('error', __('user-group.update.error'));

            // Log the unexpected exception
            logger()->error('Unexpected error in UserGroup update: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput();
        }
    }

    public function delete(): RedirectResponse
    {
        $this->action()->delete();
        $this->sessionMessage('success', __('user-group.update.delete.success'));

        return redirect()->route('group.index');
    }

    public function forceDelete(): RedirectResponse
    {
        $this->action()->forceDelete();
        $this->sessionMessage('success', __('user-group.update.force-delete.success'));

        return redirect()->route('group.index');
    }

    public function restore(): RedirectResponse
    {
        $this->action()->restore();
        $this->sessionMessage('success', __('user-group.update.restore.success'));

        return redirect()->route('group.index');
    }
}
