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
        // Log::info('Starting billing creation process');
        try {
            $data = $this->request->validate([
                'name' => 'required|string',
                'license_id' => 'required|integer|exists:license,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'usage_unit' => 'required|integer|min:0',
                'payment_status' => 'required|string|in:pending,paid,failed',
                'price' => 'required|integer',
            ]);

            // Log::debug('Validated request data', [
            //     'validated_data' => $data
            // ]);

            $billing = $this->factory->create($data);

            return $billing;
        } catch (ValidationException $e) {
            Log::error('Validation failed during billing creation', [
                'errors' => $e->errors(),
                'request_data' => $this->request->all()
            ]);
            throw $e;
        } catch (\Exception $e) {
            Log::error('Unexpected error during billing creation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function data(): array
    {

        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found in Create billing', [
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

            return $licenseData;
        })->toArray();

        Log::debug('Collected licenses', [
            'license_count' => count($licenses)
        ]);

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