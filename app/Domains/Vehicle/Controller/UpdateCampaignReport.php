<?php

declare(strict_types=1);

namespace App\Domains\Vehicle\Controller;

use Illuminate\Http\Response;
use App\Domains\Vehicle\Service\Controller\UpdateImageReport as ControllerService;

class UpdateCampaignReport extends ControllerAbstract
{
    public function __invoke(int $id): Response
    {
        $this->rowReport($id);

        $this->meta('title', __('vehicle-update-campaign-report.meta-title', ['title' => $this->rowReport->name]));

        return $this->page('vehicle.update-campaign-report', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth, $this->rowReport)->data();
    }
}
