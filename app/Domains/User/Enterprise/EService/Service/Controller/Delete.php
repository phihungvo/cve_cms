<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Service\Controller;

use App\Domains\User\Enterprise\EService\Model\EService;
use App\Domains\User\Enterprise\EService\Action\ActionFactory;

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

    public function delete(EService $permission): void
    {
        $this->factory->delete($permission);
    }
}