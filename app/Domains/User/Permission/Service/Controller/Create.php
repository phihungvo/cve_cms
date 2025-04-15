<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Service\Controller;

use Illuminate\Validation\ValidationException;

use App\Domains\User\Permission\Action\ActionFactory;

use App\Domains\User\Permission\Model\Permission as Permission;


class Create
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

    public function create(): Permission
    {
        $data = $this->request->validate([
            'alias' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'menu_route_name' => 'nullable|string',
            'menu_route_uri' => 'nullable|string',
            'is_menu' => 'nullable|string',
            'menu_name' => 'nullable|string',
            'menu_icon' => 'nullable|string',
            'parent_id' => 'nullable|string',
        ]);

        return $this->factory->create($data);
    }

    public function data(): array
    {
        return [
            'permissions' => Permission::all(),
        ];
    }
}