<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Job;

use App\Domains\Core\Job\JobAbstract as JobAbstractCore;
use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;

abstract class JobAbstract extends JobAbstractCore
{
    /**
     * @var Model
     */
    protected Model $row;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->row = $row;

    }

    /**
     * @return array
     */
    public function middleware(): array
    {
        return [$this->middlewareWithoutOverlapping()->expireAfter(30)];
    }

    /**
     * @return Model
     */
    protected function row(): Model
    {
        return $this->rowOrDeleteAndException(Model::class);
    }
}
