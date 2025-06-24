<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel as Model;

class Create
{
    protected array $data;

    public function handle(array $data)
    {
        try {
            return Model::create($data);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
