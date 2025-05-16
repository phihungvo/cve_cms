<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Service\Controller;

use Illuminate\Validation\ValidationException;
use App\Domains\User\Enterprise\Billing\Action\ActionFactory;
use App\Domains\User\Enterprise\Billing\Model\Billing;
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

    public function create(): Billing
    {
        $data = $this->request->validate([
            'service_id' => 'required|integer|exists:service,id',
            'enterprise_id' => 'required|integer|exists:enterprise,id',
            'license_type' => 'required|string|in:trial,standard,premium,enterprise',

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
            Log::warning('No authenticated user found in Create service', [
                'request_path' => $this->request->path(),
                'request_method' => $this->request->method()
            ]);
            return redirect()->guest('user/auth')->toArray();
        }

        $userId = $user->id;
        $userPermission = session('userPermission_' . $userId, []);
        $allPermissions = $userPermission['all'] ?? [];

        $licenses = License::with(['service', 'enterprise'])->get()->map(function ($license) {
            $licenseData = array_merge(
                $license->toArray(),
                [
                    'service' => $license->service ? $license->service->toArray() : null,
                    'enterprise' => $license->enterprise ? $license->enterprise->toArray() : null,
                ]
            );
            Log::debug('Processed license data', [
                'license_id' => $license->id,
                'license_key' => $license->license_key,
                'service_id' => $license->service_id,
                'enterprise_id' => $license->enterprise_id
            ]);
            return $licenseData;
        })->toArray();

        $data = [
            'licenses' => $licenses,
        ];

        if (isset($allPermissions['root'])) {
            Log::info('Returning data for root user', [
                'user_id' => $userId,
                'license_count' => count($licenses)
            ]);
            return $data;
        }

        $billingData = Billing::all()->toArray();
        Log::info('Returning billing data for non-root user', [
            'user_id' => $userId,
            'billing_count' => count($billingData)
        ]);

        return [
            'licenses' => $billingData,
        ];
    }
}