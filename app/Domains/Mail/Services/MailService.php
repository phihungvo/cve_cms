<?php

namespace App\Domains\Mail\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Domains\Mail\NotificationMail;

class MailService
{
    public function sendEmail($recipients, $template, $data, $mailer = 'mailersend')
    {
        try {
            $recipients = is_array($recipients) ? $recipients : [$recipients];

            Mail::mailer($mailer)->to($recipients)->send(new NotificationMail($data, $template));

            Log::info('Email sent successfully to: ' . implode(', ', $recipients));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            if ($mailer !== 'smtp') {
                return $this->sendEmail($recipients, $template, $data, 'smtp');
            }
            return false;
        }
    }
}