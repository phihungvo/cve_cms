<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Service\Controller;

use App\Domains\User\Enterprise\EService\Model\EService;
use App\Domains\User\Enterprise\EService\Action\ActionFactory;
use App\Domains\User\Enterprise\Model\Enterprise;
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

    public function update(EService $eservice): EService
    {
        $data = $this->request->validate([
            'enterprise_id' => 'required|integer|exists:enterprise,id',
            'name' => 'required|string|max:255',
            'alias' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pricing_model' => 'required|string|in:fixed,per_unit',
            'price' => 'required|numeric|min:0',
            'billing_cycle' => 'required|string|in:monthly,yearly',
            'max_unit' => 'required|integer|min:0',
            'note' => 'nullable|string',
        ]);
        return $this->factory->update($eservice, $data);
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
        ];

        if (isset($allPermissions['root'])) {
            return $data;
        }

        return [
            'enterpriseOptions' => [],
        ];
    }
}