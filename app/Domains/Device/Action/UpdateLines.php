<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;

class UpdateLines extends ActionAbstract
{
    protected ?DeviceCvedixrtInstance $instance;

    public function handle(DeviceCvedixrtInstance $instance): DeviceCvedixrtInstance
    {
        $this->instance = $instance;
        $this->data();

        return $this->save();
    }

    protected function data(): void
    {
        $this->dataLines();
    }

    protected function dataLines(): void
    {
        $this->data['lines'] = $this->request->input('lines', []);
    }

    protected function save(): ?DeviceCvedixrtInstance
    {
        $this->instance->update([
            'lines' => $this->data['lines'],
        ]);

        return $this->instance;
    }
}
