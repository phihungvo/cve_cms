<?php

namespace App\Domains\Mail\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Mail\Facades\Mail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MailController extends Controller
{
    /**
     * Gửi email thông báo
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sendNotification(Request $request): JsonResponse
    {
        // Validate dữ liệu đầu vào
        $validated = $request->validate([
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'email',
            'template' => 'required|string',
            'data' => 'required|array',
        ]);

        // Gọi MailService để gửi email
        $result = Mail::sendEmail(
            recipients: $validated['recipients'],
            template: $validated['template'],
            data: $validated['data'],
            mailer: env('MAIL_MAILER', 'smtp')
        );

        if ($result) {
            return response()->json([
                'message' => 'Email sent successfully',
                'recipients' => $validated['recipients'],
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to send email',
        ], 500);
    }
}