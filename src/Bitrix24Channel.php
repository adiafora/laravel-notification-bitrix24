<?php

namespace Adiafora\Bitrix24;

use Adiafora\Api\Bitrix24\Bitrix24;
use Adiafora\Bitrix24\Exceptions\NoticeBitrix24Exception;
use Illuminate\Notifications\Notification;

class Bitrix24Channel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     * @throws
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toBitrix24($notifiable);

        if (!is_numeric($notifiable)) {
            $notifiable = $notifiable->routeNotificationFor('bitrix24');
        }

        if (empty($notifiable)) {
            throw new NoticeBitrix24Exception('Сhat id was not transferred');
        }

        if ($message->toUser === true) {
            $typeOfChat = 'USER_ID';
        } else {
            $typeOfChat = 'CHAT_ID';
        }

        $params = [
            $typeOfChat => $notifiable,
            'MESSAGE' => $message->message
        ];

        $bitrix = new Bitrix24();
        $bitrix->send($params);
    }
}