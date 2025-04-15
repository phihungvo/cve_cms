<?php declare(strict_types=1);

namespace App\Domains\Position\Service\ControllerApi;

use App\Domains\CoreApp\Service\ControllerApi\ControllerApiAbstract as CoreAppControllerApiAbstract;
use League\Fractal\Manager; // Import Fractal Manager
use League\Fractal\Resource\Collection; // Import Collection Resource

abstract class ControllerApiAbstract extends CoreAppControllerApiAbstract
{
    protected $fractal;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    protected function transformData($data)
    {
        return $this->fractal->createData(
            new Collection($data, new \App\Domains\Position\Fractal\PositionTransformer())
        )->toArray();
    }
}