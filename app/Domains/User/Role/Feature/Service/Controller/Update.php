<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Service\Controller;

use App\Domains\User\Role\Feature\Model\Feature;
use App\Domains\User\Role\Model\Role;
use App\Domains\User\Role\Feature\Action\ActionFactory;

class Update
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

    public function update(Feature $feature): Feature
    {
        $data = $this->request->validate([
            'alias' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return $this->factory->update($feature, $data);
    }

    public function data(): array
    {
        return [
            'features' => Feature::all(),
        ];
    }
}