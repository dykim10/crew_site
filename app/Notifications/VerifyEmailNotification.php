<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage)
            ->subject('[PAC RUN CREW] 이메일 인증을 완료해주세요')
            ->view('emails.verify-email', [
                'url'  => $url,
                'user' => $this->notifiable,
            ]);
    }
}
