<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Controller;

use App\Domains\VehicleGroup\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('vehicle-group-index.meta-title'));

        return $this->page('vehicle-group.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
