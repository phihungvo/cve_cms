<?php
declare(strict_types=1);

namespace App\Domains\Display\ControllerApi;

use Illuminate\Http\JsonResponse;
use App\Domains\Display\Model\Display as Model;
use App\Domains\Display\Service\ControllerApi\Update as ControllerService;

class Update extends ControllerApiAbstract
{
    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(int $id): JsonResponse
    {
        // Load model instance based on ID
        $this->row($id);

        // Create service instance and call handle method
        $service = ControllerService::new($this->request, $this->auth, $this->row);

        return $service->handle();
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        // Gọi phương thức data() từ service
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
