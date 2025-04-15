<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Controller;

use App\Domains\Campaign\Media\Action\Delete;
use App\Domains\Campaign\Media\Action\Rename;
use App\Domains\Campaign\Media\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class Index extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('media-index.meta-title'));

        return $this->page('media.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
    public function destroy(): RedirectResponse
    {
        $mediaUrl = $this->request->input('media_url');
        $action = new Delete();
        $result = $action->handle($mediaUrl, $this->auth);

        return $this->redirectResult($result, 'fpp.media.index');
    }

    public function restore($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->restore($id, $this->auth);

        return $this->redirectResult($result, 'fpp.media.index');
    }

    public function forceDelete($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->forceDelete($id, $this->auth);

        return $this->redirectResult($result, 'fpp.media.index');
    }
    public function rename(): RedirectResponse
    {
        $mediaId = $this->request->input('media_id');
        $newName = $this->request->input('name');
        $action = new Rename();
        $result = $action->handle($mediaId, $newName, $this->auth);
        return $this->redirectResult($result, 'fpp.media.index');
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
