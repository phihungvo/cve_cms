<?php declare(strict_types=1);

namespace App\Domains\Vehicle\Action;

use Exception;

class Update extends CreateUpdateAbstract
{
    /**
     * @return void
     * @throws Exception
     */
    protected function save(): void
    {
        try {
            $this->transaction(function () {
                $this->row->name = $this->data['name'];
                $this->row->plate = $this->data['plate'];
                $this->row->timezone_auto = $this->data['timezone_auto'];
                $this->row->enabled = $this->data['enabled'];
                $this->row->timezone_id = $this->data['timezone_id'];

                $this->row->save();

                // Đồng bộ các nhóm xe (vehicle_group)
                $this->row->vehicleGroups()->sync($this->data['vehicle_groups']);
            });
        } catch (\Throwable $e) {
            throw new Exception('Error updating vehicle: '.$e->getMessage(), 0, $e);
        }
    }
}
