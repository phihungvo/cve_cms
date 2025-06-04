<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Alarm\Model\Alarm as AlarmModel;
use App\Domains\AlarmNotification\Model\AlarmNotification as AlarmNotificationModel;
use App\Domains\Device\Model\Device as Model;
use App\Domains\Device\Model\DeviceCvedixrtInstance;
use App\Domains\Device\Model\DeviceCvedixrtInstanceRule;
use App\Domains\DeviceMessage\Model\DeviceMessage as DeviceMessageModel;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Exceptions\NotFoundException;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?Model
     */
    protected ?Model $row;

    /**
     * @var ?\App\Domains\Alarm\Model\Alarm
     */
    protected ?AlarmModel $alarm;

    /**
     * @var ?\App\Domains\AlarmNotification\Model\AlarmNotification
     */
    protected ?AlarmNotificationModel $alarmNotification;

    /**
     * @var ?DeviceMessageModel
     */
    protected ?DeviceMessageModel $message;

    /**
     * @var ?DeviceCvedixrtInstance
     */
    protected ?DeviceCvedixrtInstance $instance;

    /**
     * @var ?DeviceCvedixrtInstanceRule
     */
    protected ?DeviceCvedixrtInstanceRule $instanceRule;

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
            ->byUserOrManager($this->auth)
            ->firstOr(fn () => $this->exceptionNotFound(__('device.error.not-found')));
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
            ->byDeviceId($this->row->id)
            ->firstOr(fn () => $this->exceptionNotFound(__('device.error.not-found')));
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
            ->byDeviceId($this->row->id)
            ->firstOr(fn () => $this->exceptionNotFound(__('device.error.not-found')));
    }

    /**
     * @param int $device_message_id
     *
     * @return \App\Domains\DeviceMessage\Model\DeviceMessage
     */
    protected function message(int $device_message_id): DeviceMessageModel
    {
        return $this->message = DeviceMessageModel::query()
            ->byId($device_message_id)
            ->byDeviceId($this->row->id)
            ->firstOr(fn () => $this->exceptionNotFound(__('device.error.not-found')));
    }

    /**
     * @param int $instanceId
     *
     * @throws NotFoundException
     *
     * @return DeviceCvedixrtInstance
     */
    protected function instance(int $instanceId): DeviceCvedixrtInstance
    {
        return $this->instance = DeviceCvedixrtInstance::query()
            ->byId($instanceId)
            ->byDeviceId($this->row->id)
            ->firstOr(fn () => $this->exceptionNotFound(__('device.error.not-found')));
    }

    /**
     * @param int $instanceRuleId
     *
     * @throws NotFoundException
     *
     * @return DeviceCvedixrtInstanceRule
     *
     */
    protected function instanceRule(int $instanceRuleId): DeviceCvedixrtInstanceRule
    {
        return $this->instanceRule = DeviceCvedixrtInstanceRule::query()
            ->byId($instanceRuleId)
            ->byInstanceId($this->instance->id)
            ->firstOr(fn () => $this->exceptionNotFound(__('device.error.not-found')));
    }
}
