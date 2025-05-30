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
     * Chuẩn bị dữ liệu để tạo rule mới
     *
     * @return array
     */
    public function dataCreateRule(): array
    {
        return [
            'uuid' => $this->request->input('uuid', Str::uuid()->toString()),
            'name' => trim($this->request->input('name', '')),
            'detected_object' => $this->request->input('detected_object'),
            'rule_type' => $this->request->input('rule_type', ''),
            'drawing_object' => $this->request->input('drawing_object'),
            'direction' => $this->request->input('direction', 'both'),
            'cvedixrt_instance_id' => $this->row?->id,
        ];
    }
}
