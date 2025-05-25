<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\RTAnalyticsUpdate as ServiceController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RTAnalyticsRules extends ControllerAbstract
{
    public function __invoke(int $id): Response|RedirectResponse|JsonResponse
    {
        try {
            $this->row($id);

            $this->deviceCvedixrtInstance((int)$this->request->query('instanceId'));
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('device.index');
        }

        if ($this->request->wantsJson()) {
            if ($response = $this->actionPost('updateLines')) {
                return $response;
            }
        }

        $this->meta('title', __('rt-analytics-rules.meta-title'));

        return $this->page('device.rt-analytics-rules', $this->data());
    }

    protected function data(): array
    {
        return ServiceController::new($this->request, $this->auth, $this->row, $this->deviceCvedixrtInstance)->dataAnalyticsRule();
    }

    protected function updateLines(): JsonResponse
    {
        try {
            $this->deviceCvedixrtInstance = $this->action()->updateLines($this->deviceCvedixrtInstance);
            $this->sessionMessage('success', __('rt-analytics-update.update-success'));

            return response()->json([
                'success' => true,
                'lines' => $this->deviceCvedixrtInstance->lines,
                'message' => __('rt-analytics-update.update-success')]);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
