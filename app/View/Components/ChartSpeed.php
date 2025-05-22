<?php declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Domains\Position\Model\Collection\Position as PositionCollection;
use App\Domains\Position\Model\Position as PositionModel;
use Illuminate\Support\Facades\Log;

class ChartSpeed extends Component
{
    /**
     * @param \App\Domains\Position\Model\Collection\Position $positions
     *
     * @return self
     */
    public function __construct(readonly public PositionCollection $positions)
    {
    }

    /**
     * @return bool
     */
    public function shouldRender(): bool
    {
        return $this->positions->isNotEmpty();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function render(): View
    {
        return view('components.chart-speed', [
            'id' => 'chart-speed-' . uniqid(),
            'positionsJson' => $this->positionsJson(),
        ]);
    }

    /**
     * @return string
     */
    protected function positionsJson(): string
    {
        $sortedPositions = $this->positions->toBase()->sortBy('date_at');
        Log::debug('Sorted Positions:', $sortedPositions->toArray());
        return $sortedPositions
            ->map($this->positionsJsonMap(...))
            ->values()
            ->toJson();
    }

    /**
     * @param \App\Domains\Position\Model\Position $position
     *
     * @return array
     */
    protected function positionsJsonMap(PositionModel $position): array
    {
        return [
            'id' => $position->id,
            'date_at' => $position->date_at->format('H:i:s'), // Extract time part directly
            'speed' => helper()->unit('speed', $position->speed),
        ];
    }
}
