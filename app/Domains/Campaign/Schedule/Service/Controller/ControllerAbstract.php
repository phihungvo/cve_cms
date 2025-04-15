<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

abstract class ControllerAbstract
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected Request $request;

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    protected Authenticatable $auth;

    /**
     * Constructor
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     */
    public function __construct(Request $request, Authenticatable $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }
}
