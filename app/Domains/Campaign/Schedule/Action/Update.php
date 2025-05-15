<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Action;

use App\Domains\Campaign\Schedule\Model\Schedule;
use Illuminate\Support\Facades\Log;

class Update
{
    protected array $data;

    protected Schedule $row;

    public function handle(Schedule $schedule, array $data): Schedule
    {
        $this->row = $schedule;
        $this->data = $data;

        return $this->updateSchedule();
    }

    protected function updateSchedule(): Schedule
    {
        // Đảm bảo dữ liệu có các giá trị hợp lý
        $dataToUpdate = [
            'name' => $this->data['name'] ?? null,
            'description' => $this->data['description'] ?? null,
            'playlist_id' => $this->data['playlist_id'] ?? null,
            'start_time' => $this->data['start_time'],
            'end_time' => $this->data['end_time'],
            'repeat' => isset($this->data['repeat']) && (bool)$this->data['repeat'],
            'active' => !isset($this->data['active']) || (bool)$this->data['active'],
        ];

        Log::info('Updating schedule with data: ', $dataToUpdate);

        $updated = $this->row->update($dataToUpdate);

        // Đồng bộ schedule_groups
        $this->row->scheduleGroups()->sync($this->data['schedule_groups'] ?? []);

        if ($updated) {
            Log::info('Schedule updated successfully: ', $this->row->fresh()->toArray());
        } else {
            Log::error('Failed to update schedule: ', $dataToUpdate);
        }

        return $this->row->fresh();
    }
}
