<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class RTAnalyticsCreate extends RTAnalyticsCreateUpdateAbstract
{

    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
        $this->request();
    }

    public function data(): array
    {
        // lấy data chung + data riêng
        return $this->dataCreateUpdate() + [
                'uuid' => ($this->input['uuid'] ?? helper()->uuid()),
        ];
    }


}
