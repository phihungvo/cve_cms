<?php declare(strict_types=1);

namespace App\Domains\Device\Service\ControllerApi;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class DeviceStatus extends ControllerApiAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->dataCustom() + $this->dataDefault();
    }

    /**
     * @return array
     */
    protected function dataCustom(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function dataDefault(): array
    {
        return $this->request->only([
            'serial',
            'data',
            // 'error',
        ]);
    }
}
