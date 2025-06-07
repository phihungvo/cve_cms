<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Controller;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceRuleModel as InstanceRule;
use App\Exceptions\NotFoundException;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected ?InstanceRule $instanceRule;
    /**
     * @param int $id
     *
     * @throws NotFoundException
     *
     * @return Model
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOr(fn () => $this->exceptionNotFound(__('cvedixrt-instance-update.error.not-found')));
    }

    /**
     * @param int $ruleId
     *
     * @throws NotFoundException
     *
     * @return InstanceRule
     */
    protected function instanceRule(int $ruleId): InstanceRule
    {
        return $this->instanceRule = InstanceRule::query()
            ->byId($ruleId)
            ->firstOr(fn () => $this->exceptionNotFound(__('cvedixrt-instance-analytics.error.rule-not-found')));
    }
}
