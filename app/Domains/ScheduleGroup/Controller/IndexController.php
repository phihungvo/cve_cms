<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Controller;

use App\Domains\ScheduleGroup\Service\Controller\IndexService as ControllerService;
use Illuminate\Http\Response;

class IndexController extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('schedule-group-index.meta-title'));

        return $this->page('schedule-group.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
