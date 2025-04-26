<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Service\Controller;

use Illuminate\Http\Request;
use App\Domains\User\Enterprise\EService\Model\EService as Model;
use App\Domains\User\Enterprise\EService\Action\Delete as DeleteAction;

class Delete
{
    protected Request $request;
    protected $auth;

    public function __construct(Request $request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new(Request $request, $auth): static
    {
        return new static($request, $auth);
    }

    public function delete(Model $row): void
    {
        $action = new DeleteAction();
        $action->setRow($row)->handle();
    }
}