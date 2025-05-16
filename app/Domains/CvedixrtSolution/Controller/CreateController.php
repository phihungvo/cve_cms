<?php declare(strict_types=1);

namespace App\Domains\CvedixrtSolution\Controller;

use Exception;
use App\Domains\CvedixrtSolution\Service\Controller\CreateService as ControllerService;
use Illuminate\Http\RedirectResponse;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->redirectUrl();

        $this->meta('title', __('cvedixrt-solution-create.meta-title'));

        return $this->page('cvedixrt-solution.create', $this->data());
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

            $this->sessionMessage('success', __('cvedixrt-solution-create.success'));

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
        $route = route('cvedixrt_solution.index');

        // Lưu vào session nếu cần
        if ($uriPrevious !== $route) {
            if ($uriPrevious === route('cvedixrt_solution.create')) {
                return;
            }
            session(['redirect_url' => $uriPrevious]);
        } else {
            session(['redirect_url' => $route]);
        }
    }
}
