<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Service\Controller;

use App\Domains\User\Permission\Model\Permission;
use App\Domains\User\Permission\Action\ActionFactory;

class Delete
{
    protected $request;
    protected $auth;
    protected $factory;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->factory = new ActionFactory($request, $auth);
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function delete(Permission $permission): void
    {
        $this->factory->delete($permission);
    }
}