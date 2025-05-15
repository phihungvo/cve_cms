<?php declare(strict_types=1);

namespace App\Domains\CvedixrtSolution\Controller;

use Exception;
use App\Domains\CvedixrtSolution\Service\Controller\UpdateService as ControllerService;
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

            return redirect()->route('cvedixrt_solution.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('vehicle-group-update.meta-title'));

        return $this->page('cvedixrt-solution.update', $this->data());
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

            $this->sessionMessage('success', __('cvedixrt-solution-update.update.success'));

            return redirect()->route('cvedixrt_solution.index');
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('cvedixrt_solution.index');
        }
    }

    
}
