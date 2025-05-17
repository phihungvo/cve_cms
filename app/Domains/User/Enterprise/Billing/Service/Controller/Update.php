<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Service\Controller;

use App\Domains\User\Enterprise\Billing\Model\Billing;
use App\Domains\User\Enterprise\Billing\Action\ActionFactory;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Enterprise\EService\Model\EService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

    public function update(Billing $license): Billing
    {
        $data = $this->request->validate([
            'name' => 'required|string',
            'license_id' => 'required|integer|exists:license,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'usage_unit' => 'required|integer|min:0',
            'payment_status' => 'required|string|in:pending,paid,failed',
            'price' => 'required|integer',
        ]);

        return $this->factory->update($license, $data);
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