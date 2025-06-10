<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Feature\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Domains\User\Permission\Feature\Model\Feature as Model;
use App\Domains\User\Permission\Feature\Model\Collection\Feature as Collection;
use App\Domains\User\Permission\Feature\Service\Controller\Index as ControllerService;

class Index extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function __invoke(): Response|JsonResponse
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        $this->meta('title', __('permission-feature-index.meta-title'));

        return $this->page('user.permission.feature.index', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson(): JsonResponse
    {
        return $this->json($this->factory()->fractal('simple', $this->responseJsonList()));
    }

    /**
     * @return \App\Domains\User\Permission\Feature\Model\Collection\Feature
     */

    protected function responseJsonList(): Collection
    {
        return new Collection(
            Model::query()
                ->byUserId($this->auth->id)
                ->enabled()
                ->get()
            // ->all()
        );
    }

}
