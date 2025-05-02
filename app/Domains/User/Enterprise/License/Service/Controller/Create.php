<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Service\Controller;

use Illuminate\Validation\ValidationException;

use App\Domains\User\Enterprise\License\Action\ActionFactory;

use App\Domains\User\Enterprise\License\Model\License as License;


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

    public function create(): License
    {
        $data = $this->request->validate([
            'alias' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        return $this->factory->create($data);
    }

    public function data(): array
    {
        return [
            'licenses' => License::all(),
        ];
    }
}