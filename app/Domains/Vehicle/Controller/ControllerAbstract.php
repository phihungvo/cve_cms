<?php

declare(strict_types=1);

namespace App\Domains\Vehicle\Controller;

use App\Domains\Alarm\Model\Alarm as AlarmModel;
use App\Domains\AlarmNotification\Model\AlarmNotification as AlarmNotificationModel;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Domains\Vehicle\Model\Builder\Vehicle;
use App\Domains\Vehicle\Model\Vehicle as Model;
use App\Domains\Vehicle\Model\VehicleImageReport as VehicleImageReportModel;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\Vehicle\Model\Vehicle
     */
    protected ?Model $row;
    /**
     * @var ?\App\Domains\Vehicle\Model\VehicleImageReport
     */
    protected ?VehicleImageReportModel $rowReport;

    /**
     * @var ?\App\Domains\Alarm\Model\Alarm
     */
    protected ?AlarmModel $alarm;

    /**
     * @var ?\App\Domains\AlarmNotification\Model\AlarmNotification
     */
    protected ?AlarmNotificationModel $alarmNotification;

    /**
     * @param int $id
     *
     * @return \App\Domains\Vehicle\Model\Vehicle
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->byUserOrManager($this->auth)
            ->firstOr(fn() => $this->exceptionNotFound(__('vehicle.error.not-found')));
    }
    /**
     * @param int $id
     *
     * @return \App\Domains\Vehicle\Model\VehicleImageReport
     */
    protected function rowReport(int $id): VehicleImageReportModel
    {
        return $this->rowReport = VehicleImageReportModel::query()
            ->whereKey($id)
            ->firstOr(fn() => $this->exceptionNotFound(__('vehicle.error.not-found')));
    }

    /**
     * @param int $alarm_id
     *
     * @return \App\Domains\Alarm\Model\Alarm
     */
    protected function alarm(int $alarm_id): AlarmModel
    {
        return $this->alarm = AlarmModel::query()
            ->byId($alarm_id)
            ->byVehicleId($this->row->id)
            ->firstOr(fn() => $this->exceptionNotFound(__('vehicle.error.not-found')));
    }

    /**
     * @param int $alarm_notification_id
     *
     * @return \App\Domains\AlarmNotification\Model\AlarmNotification
     */
    protected function alarmNotification(int $alarm_notification_id): AlarmNotificationModel
    {
        return $this->alarmNotification = AlarmNotificationModel::query()
            ->byId($alarm_notification_id)
            ->byVehicleId($this->row->id)
            ->firstOr(fn() => $this->exceptionNotFound(__('vehicle.error.not-found')));
    }
}
