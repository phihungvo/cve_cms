<?php declare(strict_types=1);

namespace App\Domains\Device\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Domains\Device\Service\ControllerApi\GetListMediaCapture as ControllerService;

class GetListMediaCapture extends ControllerApiAbstract
{
    /**
     * @param string|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(?string $id = null): JsonResponse
    {
        return response()->json($this->data($id));
    }

    /**
     * @param string|null $id
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function data(?string $id): array
    {
        // Validate id parameter
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return ControllerService::new($this->request, $this->auth, $id)->data();
    }
}