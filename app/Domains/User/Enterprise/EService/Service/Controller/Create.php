<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Service\Controller;

use Illuminate\Validation\ValidationException;

use App\Domains\User\Enterprise\EService\Action\ActionFactory;

use App\Domains\User\Enterprise\EService\Model\EService as EService;


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

    public function create(): EService
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
            'services' => EService::all(),
        ];
    }
}