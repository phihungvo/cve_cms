<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Service\Controller;


use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
        $this->request();
    }

    /**
     * Data update Event
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            // and more ...
        ];
    }
}
