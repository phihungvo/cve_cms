<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Action;

use App\Domains\Display\Model\Display;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Domains\Campaign\Schedule\Model\Schedule;
use Illuminate\Support\Facades\Log;

class Create extends ActionAbstract
{
    protected array $data;

    protected ?Schedule $row; // Sửa khai báo để khớp với ActionAbstract

    public function handle(array $data): Schedule
    {
        $this->data = $data;

        return $this->createSchedule();
    }

    protected function createSchedule(): ?Schedule
    {
        try {
            return $this->transaction(closure: function () {
                // Lấy user hiện tại
                $user = Auth::user();
                $enterpriseId = null;

                if ($user) {
                    $userId = $user->id;
                    $sessionKeyEnterprise = 'userEnterprise_'.$userId;
                    $enterprise = session::get($sessionKeyEnterprise);
                    if ($enterprise) {
                        $enterpriseId = $enterprise->id;
                    }
                }

                // Đảm bảo dữ liệu có các giá trị mặc định hợp lý
                $dataToCreate = [
                    'name' => $this->data['name'] ?? null,
                    'description' => $this->data['description'] ?? null,
                    'playlist_id' => $this->data['playlist_id'] ?? null,
                    'start_time' => $this->data['start_time'],
                    'end_time' => $this->data['end_time'] ?? now()->addHours(1),
                    'repeat' => isset($this->data['repeat']) && (bool)$this->data['repeat'],
                    'active' => !isset($this->data['active']) || (bool)$this->data['active'],
                    'enterprise_id' => $this->data['enterprise_id'] ?? $enterpriseId,
                ];

                $this->row = Schedule::query()->create($dataToCreate);

                $display = Display::query()
                    ->where('playlist_id', $this->data['playlist_id'])
                    ->first();
                if ($display) {
                    $display->schedule_id = $this->row->id;
                    $display->save();
                }

                return $this->row;
            });
        } catch (\Throwable $e) {
            Log::error('Create Schedule faild'.$e->getMessage());

            return null;
        }
    }
}
