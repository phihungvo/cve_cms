<?php declare(strict_types=1);

namespace App\Domains\Position\Job;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateCity extends JobAbstract implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 120;

    public $uniqueFor = 600; // giữ job duy nhất trong 10 phút

    public function uniqueId()
    {
        return $this->row()->id; // hoặc $this->id nếu truyền id
    }


    public function handle(): void
    {
        try {
            $this->factory(row: $this->row())->action()->updateCity();
        } catch (\Exception $e) {
            \Log::error('UpdateCityJob failed: ' . $e->getMessage());
            throw $e;
        }
    }
}