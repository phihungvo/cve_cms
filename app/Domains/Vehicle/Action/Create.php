<?php declare(strict_types=1);

namespace App\Domains\Vehicle\Action;

use App\Domains\Vehicle\Model\Vehicle as Model;
use Exception;
use Throwable;

class Create extends CreateUpdateAbstract
{
    /**
     * @throws Exception
     *
     * @return void
     */
    protected function save(): void
    {
        try {
            $this->transaction(function () {
                $this->row = Model::query()->create([
                    'name' => $this->data['name'],
                    'plate' => $this->data['plate'],
                    'timezone_auto' => $this->data['timezone_auto'],
                    'enabled' => $this->data['enabled'],
                    'timezone_id' => $this->data['timezone_id'],
                    'user_id' => $this->data['user_id'],
                    'enterprise_id' => $this->data['enterprise_id'],
                ]);

                // Đồng bộ các nhóm xe (vehicle_group)
                $this->row->vehicleGroups()->sync($this->data['vehicle_groups']);
            });
        } catch (Throwable $e) {
            throw new Exception('Error creating vehicle: '.$e->getMessage(), 0, $e);
        }
    }
}
