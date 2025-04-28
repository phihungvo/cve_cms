<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class PreviewMessage extends ControllerAbstract
{
    public function __construct(
        Request $request,
        protected Authenticatable $auth,
        protected Model $row
    ) {
    }

    public function data(): array
    {
        return $data = [
            'playlist' => $this->playlistName(),
            'display_id' => $this->displayId(),
            'day' => '7',
            'startTime' => $this->startTime(),
            'endTime' => $this->endTime(),
            'startDate' => $this->startDate(),
            'endDate' => $this->endDate(),
        ];

    }

    protected function playlistName(): string
    {
        return $this->row->playlist->name;
    }

    protected function displayId(): array
    {
        return $this->row->devices()
            ->where('playlist_published', 1)
            ->pluck('serial')->toArray();
    }

    protected function startTime()
    {
        return $this->row->start_time->format('H:i:s');
    }

    protected function endTime()
    {
        return $this->row->end_time->format('H:i:s');
    }

    protected function startDate()
    {
        return $this->row->start_time->format('Y-m-d');
    }

    protected function endDate()
    {
        return $this->row->start_time->format('Y-m-d');
    }
}
