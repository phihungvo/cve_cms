<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Service\Controller;

use Illuminate\Support\Facades\Log;
use App\Domains\User\Enterprise\EService\Model\EService;
use App\Domains\User\Enterprise\EService\Action\ActionFactory;

class Delete
{
    protected $request;
    protected $auth;
    protected $factory;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->factory = new ActionFactory($request, $auth);
        Log::info('DeleteService: Initialized', ['user_id' => $auth->id ?? null]);
    }

    public static function new($request, $auth): self
    {
        Log::info('DeleteService: Creating new instance');
        return new self($request, $auth);
    }

    public function delete(EService $service): void
    {
        Log::info('DeleteService: Starting delete', [
            'id' => $service->id,
            'deleted_at' => $service->deleted_at
        ]);

        try {
            if ($service->deleted_at === null) {
                Log::info('DeleteService: Performing soft delete', ['id' => $service->id]);
                $service->delete();
            } else {
                Log::info('DeleteService: Performing hard delete', ['id' => $service->id]);
                $this->factory->delete($service);
            }
            Log::info('DeleteService: Delete completed', ['id' => $service->id]);
        } catch (\Exception $e) {
            Log::error('DeleteService: Delete failed', [
                'id' => $service->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}