<?php declare(strict_types=1);

namespace App\Domains\Device\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Domains\Device\Service\ControllerApi\GetDeviceStatusBySerial as ControllerService;

class GetDeviceStatusBySerial extends ControllerApiAbstract
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return response()->json($this->data());
    }

    /**
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function data(): array
    {
        $serial = $this->request->query('serial');

        // Validate serial parameter
        $validator = Validator::make(['serial' => $serial], [
            'serial' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return ControllerService::new($this->request, $this->auth, $serial)->data();
    }
}