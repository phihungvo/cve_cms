<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Controller;

use App\Exceptions\NotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ExportSettingsController extends ControllerAbstract
{
    public function __invoke(int $id): Response|RedirectResponse
    {
        try {
            $this->row($id);
        } catch (NotFoundException $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('cvedixrt_instance.index');
        }

        $this->meta('title', __('cvedixrt-instance-export-settings.meta-title'));

        return $this->page('cvedixrt.instance.export-settings');
    }
}
