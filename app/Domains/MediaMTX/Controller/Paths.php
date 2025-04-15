<?php

declare(strict_types=1);

namespace App\Domains\MediaMTX\Controller;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use App\Domains\MediaMTX\Service\Controller\Paths as ControllerService; // Import service class

class Paths extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function __invoke(): Response
    {
        $this->meta('title', __('mediamtx-paths.meta-title'));

        return $this->page('monitor.mediamtx.paths', $this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}