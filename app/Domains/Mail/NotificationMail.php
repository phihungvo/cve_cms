<?php

namespace App\Domains\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;

class NotificationMail extends Mailable
{
    public $data;
    public $template;

    public function __construct($data, $template)
    {
        $this->data = $data;
        $this->template = $template;
    }

    public function build()
    {
        Log::info('Mail data in build: ', $this->data);
        Log::info('Using template: ' . $this->template);
        return $this->view($this->template)->with('data', $this->data);
    }
}