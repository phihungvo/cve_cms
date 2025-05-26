<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Controller;

use Exception;
use App\Domains\Cvedixrt\Instance\Service\Controller\RTAnalyticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class RTAnalyticsIndex extends ControllerAbstract
{
    public function __invoke(int $id): Response|RedirectResponse
    {
        try {
            $this->row($id);
        } catch (Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('cvedixrt.instance.analyticsr-rules');
        }

        $this->meta('title', __('Cvedixrt Instance Analytics Index'));

        return $this->page('cvedixrt.instance.cvedix-analytics.analyticsr-rules', $this->data());
    }

    protected function data(): array
    {
        return RTAnalyticsService::new($this->request, $this->auth, $this->row)
            ->data();
    }
}
