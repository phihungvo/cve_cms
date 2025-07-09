<?php declare(strict_types=1);

namespace App\Domains\Device\Fractal;

use App\Domains\Core\Fractal\FractalAbstract;
use App\Domains\Device\Model\Device as Model;
use App\Domains\Device\Model\DeviceCvedixrtEvent as Event;
use App\Domains\Position\Model\Position as PositionModel;
use App\Domains\Vehicle\Model\Vehicle as VehicleModel;

class FractalFactory extends FractalAbstract
{
    /**
     * @param Model $row
     *
     * @return array
     */
    protected function json(Model $row): array
    {
        return [
            'id' => $row->id,
            'code' => $row->code,
            'name' => $row->name,
            'model' => $row->model,
            'serial' => $row->serial,
            'type' => $row->type,
            'phone_number' => $row->phone_number,
            'information' => $row->information,
            'password' => $row->password,
            'enabled' => $row->enabled,
            'shared' => $row->shared,
            'shared_public' => $row->shared_public,
            'user' => $this->from('User', 'related', $row->user),
            'vehicle' => $this->from('Vehicle', 'related', $row->vehicle),
        ];
    }

    /**
     * @param Model $row
     *
     * @return array
     */
    protected function map(Model $row): array
    {
        return [
            'id' => $row->id,
            'name' => $row->name,
            'position' => $this->mapPosition($row->positionLast),
            'vehicle' => $this->mapVehicle($row->vehicle),
        ];
    }

    /**
     * @param ?PositionModel $position
     *
     * @return ?array
     */
    protected function mapPosition(?PositionModel $position): ?array
    {
        if ($position === null) {
            return null;
        }

        return [
            'id' => $position->id,
            'date_at' => $position->date_at,
            'date_utc_at' => $position->date_utc_at,
            'latitude' => $position->latitude,
            'longitude' => $position->longitude,
            'direction' => $position->direction,
            'speed' => helper()->unit('speed', $position->speed),
            'speed_human' => helper()->unitHuman('speed', $position->speed),
            'city' => $position->city?->name,
            'state' => $position->city?->state->name,
        ];
    }

    /**
     * @param ?VehicleModel $vehicle
     *
     * @return ?array
     */
    protected function mapVehicle(?VehicleModel $vehicle): ?array
    {
        if ($vehicle === null) {
            return null;
        }

        return [
            'id' => $vehicle->id,
            'name' => $vehicle->name,
        ];
    }

    /**
     * @param Model $row
     *
     * @return array
     */
    protected function related(Model $row): array
    {
        return [
            'id' => $row->id,
            'name' => $row->name,
        ];
    }

    /**
     * @param Model $row
     *
     * @return array
     */
    protected function simple(Model $row): array
    {
        return $row->only('id', 'name', 'shared', 'shared_public');
    }

    /**
     * @param Event|null $event
     *
     * @return array|null
     */
    protected function mapEvent(?Event $event): ?array
    {
        if ($event === null) {
            return null;
        }

        return [
            'id' => $event->id,
            'uuid' => $event->uuid,
            'image_url' => $event->image_url,
            'video_url' => $event->video_url,
            'detected_object' => $event->detected_object,
            'event_name' => $event->event_name,
            'event_type' => $event->event_type,
            'priority' => $event->instanceRule->priority,
        ];
    }
}
