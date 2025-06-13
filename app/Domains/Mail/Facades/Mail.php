<?php

// namespace App\Facades;
namespace App\Domains\Mail\Facades;

use Illuminate\Support\Facades\Facade;

class Mail extends Facade
{
    protected static function getFacadeAccessor()
    {
        // return \App\Services\Mail\MailService::class;
        return \App\Domains\Mail\Services\MailService::class;
    }
}