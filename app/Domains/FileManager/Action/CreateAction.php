<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Action;

use App\Domains\FileManager\Model\FileManager as Model;

class CreateAction
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
