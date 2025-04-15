<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;
use App\Domains\Device\Model\DeviceType;
use App\Domains\User\Enterprise\Model\Enterprise;

abstract class CreateUpdateAbstract extends ControllerAbstract
{
    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow([
            'code' => ($this->row->code ?? helper()->uuid()),
            'user_id' => $this->user()->id,
        ]);
    }

    /**
     * @return array
     */
    protected function dataCreateUpdate(): array
    {
        return $this->dataCore() + [
            'vehicles' => $this->vehicles(),
            'device_types' => $this->deviceTypes(),
        ];
        // $data = $this->dataCore() + [
        //     'vehicles' => $this->vehicles(),
        //     'device_types' => $this->deviceTypes(),
        // ];

        // if ($this->auth->isRoot()) {
        //     $data['enterprises'] = Enterprise::all()->map(function ($enterprise) {
        //         return ['id' => $enterprise->id, 'name' => $enterprise->name];
        //     })->toArray();
        // } else {
        //     $data['enterprise_name'] = $this->auth->enterprise->name ?? '';
        // }

        // return $data;
    }
    protected function deviceTypes(): array
    {
        return DeviceType::all()->map(function ($deviceType) {
            return [
                'id' => $deviceType->id,
                'name' => $deviceType->name,
            ];
        })->toArray();
    }


}
