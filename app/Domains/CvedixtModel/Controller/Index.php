<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Controller;

use App\Domains\CvedixtModel\Action\Delete;
use App\Domains\CvedixtModel\Action\Rename;
use App\Domains\CvedixtModel\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class Index extends ControllerAbstract
{
    public function __invoke(): Response
    {
//        $this->meta('title', __('media-index.meta-title'));
        $this->meta('title', __('Cvedixt Model'));


        return $this->page('model.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    public function destroy(): RedirectResponse
    {
        $mediaUrl = $this->request->input('model_url');
        $action = new Delete();
        $result = $action->handle($mediaUrl, $this->auth);

        return $this->redirectResult($result, 'cvedixt-model.index');
    }

    public function restore($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->restore($id, $this->auth);

        return $this->redirectResult($result, 'cvedixt-model.index');
    }

    public function forceDelete($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->forceDelete($id, $this->auth);

        return $this->redirectResult($result, 'cvedixt-model.index');
    }

    public function rename(): RedirectResponse
    {
        $modelId = $this->request->input('model_id');
        $newName = $this->request->input('name');
        $action = new Rename();
        $result = $action->handle($modelId, $newName, $this->auth);

        return $this->redirectResult($result, 'cvedixt-model.index');
    }

    protected function redirectResult(array $result, string $route): RedirectResponse
    {
        if ($result['success']) {
            $this->sessionMessage('success', $result['message']);
        } else {
            $this->sessionMessage('error', $result['message']);
        }

        return redirect()->route($route);
    }
}
