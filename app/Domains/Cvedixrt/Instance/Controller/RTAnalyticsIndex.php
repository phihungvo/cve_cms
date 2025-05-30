<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Controller;

use App\Domains\Cvedixrt\Instance\Service\Controller\RTAnalyticsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RTAnalyticsIndex extends ControllerAbstract
{
    public function __invoke(int $id): Response|RedirectResponse|JsonResponse
    {
        try {
            $this->row($id);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('cvedixrt_instance.analytics', ['id' => $id]);
        }

        if ($this->request->wantsJson()) {
            $ruleId = $this->request->input('rule_id');
            if ($ruleId) {
                $this->instanceRule($ruleId);
            }

            return $this->actions();
        }

        $this->meta('title', __('Cvedixrt Instance Analytics Index'));

        return $this->page('cvedixrt.instance.cvedix-analytics.analyticsr-rules', $this->data());
    }

    protected function data(): array
    {
        return RTAnalyticsService::new($this->request, $this->auth, $this->row)->data();
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
            $data = $this->request->all();

            $data['cvedixrt_instance_id'] = $data['instance_id'] ?? $this->row->id;

            $rule = $this->action($this->row, 'Cvedixrt\Instance')->createInstanceRule($data);

            return $this->json([
                'status' => true,
                'data' => $rule,
                'message' => __('cvedixrt-instance-analytics.create-success'),
            ]);
        } catch (Exception $e) {
            return $this->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    protected function updateInstanceRule(): JsonResponse
    {
        try {
            $data = $this->request->all();
            $data['cvedixrt_instance_id'] = $data['instance_id'] ?? $this->row->id;

            $rule = $this->action($this->row, 'Cvedixrt\Instance', $data)
                ->updateInstanceRule();

            return $this->json([
                'status' => true,
                'data' => $rule,
                'message' => __('cvedixrt-instance-analytics.update-success'),
            ]);
        } catch (Exception $e) {
            return $this->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    protected function deleteInstanceRule(): JsonResponse
    {
        try {
            $this->action(
                $this->row,
                'Cvedixrt\Instance',
                ['id' => $this->instanceRule->id]
            )->deleteInstanceRule();

            return $this->json([
                'status' => true,
                'message' => __('cvedixrt-instance-analytics.delete-success'),
            ]);
        } catch (Exception $e) {
            return $this->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
