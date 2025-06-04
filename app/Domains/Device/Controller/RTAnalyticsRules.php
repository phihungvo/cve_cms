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

            $this->instance((int)$this->request->query('instanceId'));

            if ($this->request->wantsJson()) {
                $ruleId = $this->request->input('rule_id');
                if ($ruleId) {
                    $this->instanceRule($ruleId);
                }

                return $this->actions();
            }
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('device.index');
        }

        $this->meta('title', __('rt-analytics-rules.meta-title'));

        return $this->page('device.rt-analytics-rules', $this->data());
    }

    protected function data(): array
    {
        return ServiceController::new($this->request, $this->auth, $this->row, $this->instance)->dataAnalyticsRule();
    }

    protected function actions(): JsonResponse|false|null
    {
        return $this->actionPost('createInstanceRule')
            ?: $this->actionPost('updateInstanceRule')
                ?: $this->actionPost('deleteInstanceRule');
    }

    protected function createInstanceRule(): JsonResponse
    {
        try {
            $rule = $this->action()->createInstanceRule();

            return $this->json([
                'status' => 'success',
                'data' => $rule,
                'message' => __('rt-analytics-rules.create-success'),
            ]);
        } catch (Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    protected function updateInstanceRule(): JsonResponse
    {
        try {
            $rule = $this->action()->updateInstanceRule();

            return $this->json([
                'status' => 'success',
                'data' => $rule,
                'message' => __('rt-analytics-rules.update-success'),
            ]);
        } catch (Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    protected function deleteInstanceRule(): JsonResponse
    {
        try {
            $this->action()->deleteInstanceRule($this->instanceRule);

            return $this->json([
                'status' => 'true',
                'message' => __('rt-analytics-rules.delete-success'),
            ]);
        } catch (Exception  $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
