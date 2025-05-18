<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Service\Controller;

use App\Domains\User\Enterprise\Billing\Model\Billing;
use App\Domains\User\Enterprise\Billing\Action\ActionFactory;
use App\Domains\User\Enterprise\License\Model\License;
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

    public function update(Billing $billing): Billing
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

        return $this->factory->update($billing, $data);
    }

    public function data(?Billing $row = null): array
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found in billing', [
                'request_path' => $this->request->path(),
                'request_method' => $this->request->method()
            ]);
            return redirect()->guest('user/auth')->toArray();
        }

        $userId = $user->id;
        $userPermission = session('userPermission_' . $userId, []);
        $allPermissions = $userPermission['all'] ?? [];

        // Lấy danh sách licenses từ License
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

        $data = [
            'licenses' => $licenses,
            'row' => $row, // Trả về đối tượng Billing thay vì mảng
        ];

        // Log::info('Returning data', [
        //     'user_id' => $userId,
        //     'license_count' => count($licenses),
        //     'has_row' => !is_null($row)
        // ]);

        return $data;
    }
}