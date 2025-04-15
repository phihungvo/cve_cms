<?php declare(strict_types=1);

namespace App\Domains\Position\ControllerApi;

use Illuminate\Http\JsonResponse;
use App\Domains\Position\Service\ControllerApi\Heatmap as ControllerService;
use App\Domains\Position\Fractal\PositionTransformer;

class Heatmap extends ControllerApiAbstract
{
    public function __invoke(): JsonResponse
    {
        $data = $this->data();
        $transformer = new PositionTransformer();
        $transformedData = array_map([$transformer, 'transform'], $data);
        return response()->json($transformedData);
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}