<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Service\Controller;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceModel as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RTAnalyticsService extends ControllerAbstract
{
    public function __construct(
        protected Request $request,
        protected Authenticatable $auth,
        protected ?Model $row
    ) {
    }

    public function data(): array
    {
        return [
            'row' => $this->row,
        ];
    }

    /**
     * Xử lý dữ liệu shapes từ form gửi lên
     *
     * @param array $shapes
     *
     * @return array
     */
    public function processShapesData(array $shapes): array
    {
        $result = [
            'lines' => [],
            'zones' => [],
        ];

        foreach ($shapes as $shape) {

            switch ($shape['type']) {
                case 'line':
                    $result['lines'][] = $this->processLineShape($shape);
                    break;

                case 'rect':
                    $result['zones'][] = $this->processRectShape($shape);
                    break;

                case 'poly':
                    $result['zones'][] = $this->processPolyShape($shape);
                    break;
            }
        }

        return $result;
    }

    /**
     * Xử lý dữ liệu đường thẳng
     *
     * @param array $shape
     *
     * @return array
     */
    protected function processLineShape(array $shape): array
    {
        $color = $this->convertColorToRGB($shape['color'] ?? 'ff0000');
        return [
            'id' => $this->row->uuid ?? Str::uuid()->toString(),
            'label' => $shape['label'] ?? 'Đường thẳng',
            'color' => $color,
            'rule_name' => $this->row->name ?? 'Quy tắc mặc định',
            'detect_objects' => $shape['detect_objects'],
            'direction' => $shape['direction'] ?? 'both',
            'coordinates' => [
                'startX' => (float)($shape['startX'] ?? $shape['coordinates']['startX'] ?? 0),
                'startY' => (float)($shape['startY'] ?? $shape['coordinates']['startY'] ?? 0),
                'endX' => (float)($shape['endX'] ?? $shape['coordinates']['endX'] ?? 0),
                'endY' => (float)($shape['endY'] ?? $shape['coordinates']['endY'] ?? 0),
            ],
        ];
    }

    /**
     * Xử lý dữ liệu hình chữ nhật
     *
     * @param array $shape
     *
     * @return array
     */
    protected function processRectShape(array $shape): array
    {
        $color = $this->convertColorToRGB($shape['color'] ?? 'ff0000');
        return [
            'id' => $this->row->uuid ?? Str::uuid()->toString(),
            'type' => 'rect',
            'label' => $shape['label'] ?? 'Hình chữ nhật',
            'color' => $color,
            'rule_name' => $this->row->name ?? 'Quy tắc mặc định',
            'detect_objects' => $shape['detect_objects'] ?? ['Person'],
            'direction' => $shape['direction'] ?? 'both',
            'coordinates' => [
                'startX' => (float)($shape['startX'] ?? $shape['coordinates']['startX'] ?? 0),
                'startY' => (float)($shape['startY'] ?? $shape['coordinates']['startY'] ?? 0),
                'width' => (float)($shape['width'] ?? $shape['coordinates']['width'] ?? 0),
                'height' => (float)($shape['height'] ?? $shape['coordinates']['height'] ?? 0),
            ],
        ];
    }

    /**
     * Xử lý dữ liệu đa giác
     *
     * @param array $shape
     *
     * @return array
     */
    protected function processPolyShape(array $shape): array
    {
        $coordinates = $shape['points'] ?? $shape['coordinates'] ?? [];

        $color = $this->convertColorToRGB($shape['color'] ?? '#ff0000');

        return [
            'id' => $shape['id'] ?? Str::uuid()->toString(),
            'type' => 'poly',
            'label' => $shape['label'] ?? 'Đa giác',
            'color' => $color,
            'rule_name' => $this->row->name ?? 'Quy tắc mặc định',
            'detect_objects' => $shape['detect_objects'] ?? ['Person'],
            'direction' => $shape['direction'] ?? 'both',
            'coordinates' => array_map(function ($point) {
                return [
                    'x' => (float)($point['x'] ?? 0),
                    'y' => (float)($point['y'] ?? 0),
                ];
            }, $coordinates),
        ];
    }

    /**
     * Chuyển đổi màu hex sang RGB
     *
     * @param string|array $color
     * @return array
     */
    protected function convertColorToRGB($color): array
    {
        if (is_array($color)) {
            return [
                (int)($color[0] ?? 255),
                (int)($color[1] ?? 0),
                (int)($color[2] ?? 0),
            ];
        }

        // Nếu là hex, chuyển sang RGB
        $hex = ltrim($color, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }
}
