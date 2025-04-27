<?php

namespace App\Domains\Solution\Controller;


use App\Domains\Solution\Service\Controller\CreateService as ControllerService;
use Exception;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {

        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->redirectUrl();

        $this->device((int)$this->request->query('deviceId'));

        $this->meta('title', __('solution-create.meta-title'));

        return $this->page('solution.create', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->device)->data();
    }

    protected function create()
    {
        $redirectUrl = session('redirect_url', route('solution.index'));
        try {
            $this->row = $this->action()->create();
            $this->sessionMessage('success', __('solution-create.success'));
            session()->forget('redirect_url');
            return redirect()->to($redirectUrl)->with('success', __('solution-create.success'));
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    protected function redirectUrl(): void
    {
        $uriPrevious = url()->previous();
        $route = route('solution.index');

        // Lưu vào session nếu cần
        if ($uriPrevious !== $route) {
            if ($uriPrevious === route('solution.create')) {
                return;
            }
            session(['redirect_url' => $uriPrevious]);
        } else {
            session(['redirect_url' => $route]);
        }
    }
}
