<?php

namespace App\Domains\Playlist\Action;

use App\Domains\Playlist\Model\PlaylistModel as Model;

abstract class CreateUpdateAbstract extends ActionAbstract
{
    abstract protected function save();

    public function handle(): Model
    {
        $this->data();
        $this->check();
        $this->save();

        return $this->row;
    }

    private function data(): void
    {
        $this->dataName();
        $this->dataDescription();
    }

    private function check(): void
    {
        // TODO: implement logic checkName and checkDescription
        //        $this->checkName();
        //        $this->checkDescription();
    }

    private function dataName(): void
    {
        $this->data['name'] = trim($this->data['name']);
    }

    private function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }
}
