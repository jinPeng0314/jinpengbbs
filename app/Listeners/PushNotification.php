<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\DatabaseNotification;
use JPush\Client;

class PushNotification
{
    protected $client;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DatabaseNotification $notification)
    {
        // 本地环境默认不推送
        if (app()->environment('local')){
            return ;
        }
        $user = $notification->notifiable;

        // 没有 registration_id 的不推送
        if (!$user->reregistration_id){
            return ;
        }

        // 推送消息
        $this->client->push()
            ->setPlatform('all')
            ->addRegistrationId($user->reregistration_id)
            ->setNotificationAlert(strip_tags($notification->data['reply_content']))
            ->send();
    }
}
