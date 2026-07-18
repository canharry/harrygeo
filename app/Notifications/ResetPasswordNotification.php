<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * 中文密码重置邮件通知
 *
 * 重写默认通知，使用中文主题与正文，符合本项目语言环境。
 */
class ResetPasswordNotification extends ResetPasswordBase
{
    /**
     * 构建邮件内容
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('重置密码通知')
            ->line('您收到这封邮件是因为我们收到了您账户的密码重置请求。')
            ->action('点击重置密码', $resetUrl)
            ->line('此重置链接将在 60 分钟后失效。')
            ->line('如果您没有请求重置密码，请忽略此邮件。');
    }
}
