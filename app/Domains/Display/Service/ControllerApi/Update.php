<?php
declare(strict_types=1);

namespace App\Domains\Display\Service\ControllerApi;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Display\Model\Display as Model;
use App\Domains\Display\Action\Update as UpdateAction;

class Update extends ControllerApiAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     * @param \App\Domains\Display\Model\Display $row
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(): \Illuminate\Http\JsonResponse
    {
        return $this->responseJson(
            $this->updateDisplay()
        );
    }

    /**
     * @return array
     */
    protected function updateDisplay(): array
    {
        $action = new UpdateAction();

        return $action->handle($this->row, $this->request->input());
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson(array $data): \Illuminate\Http\JsonResponse
    {
        return response()->json($data);
    }

    /**
     * @return mixed
     */
    public function data(): mixed
    {
        // Trả về dữ liệu từ request hoặc dữ liệu của row tùy theo yêu cầu cụ thể
        return [
            'input' => $this->request->input(),
            'display' => $this->row->toArray(),
        ];
    }
}
