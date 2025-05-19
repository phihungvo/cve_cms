<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Service\Controller;

use Illuminate\Validation\ValidationException;
use App\Domains\User\Enterprise\License\Action\ActionFactory;
use App\Domains\User\Enterprise\License\Model\License;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Enterprise\EService\Model\EService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            'name' => 'required|string',
            'alias' => 'required|string',
            'service_id' => 'required|integer|exists:service,id',
            'enterprise_id' => 'required|integer|exists:enterprise,id',
            'license_type' => 'required|string|in:trial,standard,premium,enterprise',
            'max_users' => 'required|integer|min:0',
            'max_devices' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string|in:active,expired,suspended',
            'license_key' => 'required|string|unique:license,license_key|max:255',
        ]);

        return $this->factory->create($data);
    }

    public function data(): array
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found in Create service');
            return redirect()->guest('user/auth')->toArray();
        }

        $userId = $user->id;
        $userPermission = session('userPermission_' . $userId, []);
        $allPermissions = $userPermission['all'] ?? [];

        $enterprises = Enterprise::pluck('name', 'id')->toArray();
        $services = EService::pluck('name', 'id')->toArray();

        $data = [
            'enterpriseOptions' => $enterprises,
            'serviceOptions' => $services,
            'licenses' => License::all(),
        ];

        if (isset($allPermissions['root'])) {
            return $data;
        }

        return [
            'licenses' => License::all(),
        ];
    }
}