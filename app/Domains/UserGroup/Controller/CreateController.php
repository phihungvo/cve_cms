<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;

use App\Domains\UserGroup\Service\Controller\CreateService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;
use Throwable;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        if ($response = $this->actionPost('create')) {
            return $response;
        }

        $this->meta('title', __('user-group-create.meta-title'));

        return $this->page('user-group.create', $this->data());
    }

    protected function data()
    {
        return CreateService::new($this->request, $this->auth)->data();
    }

    protected function create(): RedirectResponse
    {
        try {
            $this->row = $this->action()->create();

            $this->sessionMessage('success', __('user-group-create.success'));

            return redirect()->route('user_group.index');
        } catch (RuntimeException $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->back()->withInput();
        } catch (Throwable $e) {
            $this->sessionMessage('error', $e->getMessage());

            // Log the unexpected exception
            logger()->error('Unexpected error in UserGroup creation: '.$e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withInput();
        }
    }
}
