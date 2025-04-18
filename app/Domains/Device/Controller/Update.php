<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Exceptions\NotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use App\Domains\Device\Service\Controller\Update as ControllerService;

class Update extends ControllerAbstract
{
    /**
     * @param int $id
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(int $id): Response|RedirectResponse
    {
        try {
            $this->row($id);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('device.index');
        }

        if ($response = $this->actions()) {
            return $response;
        }

        $this->meta('title', __('device-update.meta-title', ['title' => $this->row->name]));

        return $this->page('device.update', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|false|null
     */
    protected function actions(): RedirectResponse|false|null
    {
        return $this->actionPost('update')
            ?: $this->actionPost('delete');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function update(): RedirectResponse
    {
        try {
            $this->action()->update();
            $this->sessionMessage('success', __('device-update.success'));
            return redirect()->route('device.update', $this->row->id);
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function delete(): RedirectResponse
    {
        $this->action()->delete();

        $this->sessionMessage('success', __('device-update.delete-success'));

        return redirect()->route('device.index');
    }
}
