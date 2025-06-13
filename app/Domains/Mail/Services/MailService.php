<?php

// namespace App\Services\Mail;
namespace App\Domains\Mail\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendEmail($recipients, $template, $data, $mailer = 'mailersend')
    {
        try {
            // Chuyển recipients thành mảng nếu là chuỗi
            $recipients = is_array($recipients) ? $recipients : [$recipients];

            // Gửi email
            Mail::mailer($mailer)->to($recipients)->send(new class ($template, $data) extends Mailable {
                private $template;
                private $data;

                public function __construct($template, $data)
                {
                    $this->template = $template;
                    $this->data = $data;
                }

                public function build()
                {
                    return $this->view($this->template)->with($this->data);
                }
            });

            Log::info('Email sent successfully to: ' . implode(', ', $recipients));
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send email: ' . $e->getMessage());
            // Fallback sang Gmail nếu mailer chính (Mailersend) thất bại
            if ($mailer !== 'smtp') {
                return $this->sendEmail($recipients, $template, $data, 'smtp');
            }
            return false;
        }
    }
}