<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Device\Model\Device;

class UpdateCamera extends CreateUpdateAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     * @param \App\Domains\Device\Model\Device $row
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Device $row)
    {
        $this->request();
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            'cameras' => $this->row->cameras, // Explicitly include cameras for clarity
        ];
    }
}