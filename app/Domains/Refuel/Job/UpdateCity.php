<?php declare(strict_types=1);

namespace App\Domains\Refuel\Job;

class UpdateCity extends JobAbstract
{
    public $uniqueFor = 600; // giữ job duy nhất trong 10 phút

    public function uniqueId()
    {
        return $this->row()->id; // hoặc $this->id nếu truyền id
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->factory(row: $this->row())->action()->updateCity();
    }
}
