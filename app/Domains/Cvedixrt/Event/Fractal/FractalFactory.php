<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Fractal;

use App\Domains\Core\Fractal\FractalAbstract;
use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use App\Domains\Device\Model\DeviceCvedixrtEvent as Event;

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
            'uuid' => $row->uuid,
            'image_url' => $row->image_url,
            'video_url' => $row->video_url,
            'detected_object' => $row->detected_object,
            'event_name' => $row->event_name,
            'event_type' => $row->event_type,
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
            'name' => $row->event_name,
        ];
    }
}
