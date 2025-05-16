<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Controller;

use Exception;
use App\Domains\CvedixrtGroup\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->redirectUrl();

        $this->meta('title', __('cvedixrt-group-create.meta-title'));

        return $this->page('cvedixrt-group.create', $this->data());
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
        $redirectUrl = session('redirect_url', route('solution.index'));

        try {
            $this->row = $this->action()->create();

            $this->sessionMessage('success', __('cvedixrt-group-create.success'));

            session()->forget('redirect_url');

            return redirect()->to($redirectUrl);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    protected function redirectUrl(): void
    {
        $uriPrevious = url()->previous();
        $route = route('cvedixrt_group.index');

        // Lưu vào session nếu cần
        if ($uriPrevious !== $route) {
            if ($uriPrevious === route('cvedixrt_group.create')) {
                return;
            }
            session(['redirect_url' => $uriPrevious]);
        } else {
            session(['redirect_url' => $route]);
        }
    }
}
