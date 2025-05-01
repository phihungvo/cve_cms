<?php declare(strict_types=1);

namespace App\Domains\Group\Controller;

use App\Domains\Group\Service\Controller\CreateService as ControllerService;
use Exception;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->redirectUrl();

        $this->device((int)$this->request->query('deviceId'));

        $this->meta('title', __('group-create.meta-title'));

        return $this->page('group.create', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->device)->data();
    }

    protected function create(): RedirectResponse
    {
        $redirectUrl = session('redirect_url', route('group.index'));
        try {
            $this->row = $this->action()->create();
            $this->sessionMessage('success', __('group-create.success'));
            session()->forget('redirect_url');

            return redirect()->to($redirectUrl)->with('success', __('group-create.success'));
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    protected function redirectUrl(): void
    {
        $uriPrevious = url()->previous();
        $route = route('group.index');

        if ($uriPrevious !== $route) {
            if ($uriPrevious === route('group.create')) {
                return;
            }
            session(['redirect_url' => $uriPrevious]);
        } else {
            session(['redirect_url' => $route]);
        }
    }
}
