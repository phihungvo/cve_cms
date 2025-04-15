<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Model\Traits;

use App\Domains\User\Role\Service\Type\Format\FormatAbstract as TypeFormatAbstract;
use App\Domains\User\Role\Service\Type\Manager as TypeManager;

trait TypeFormat
{
    /**
     * @var \App\Domains\User\Role\Service\Type\Format\FormatAbstract
     */
    protected TypeFormatAbstract $typeFormat;

    /**
     * @param ?array $config = null
     *
     * @return \App\Domains\User\Role\Service\Type\Format\FormatAbstract
     */
    public function typeFormat(?array $config = null): TypeFormatAbstract
    {
        return $this->typeFormat ??= TypeManager::new()->factory($this->type, $config ?? $this->config ?? []);
    }
}
