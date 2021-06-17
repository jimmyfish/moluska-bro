<?php

namespace App\Http\Services\Notification;

use GuzzleHttp\Client;

class FcmDispatcherService
{
    // private $url = "https://fcm.googleapis.com/fcm/send";
    private $url = "https://minority.requestcatcher.com";

    public function send($args = [])
    {
        if (!isset($args['title']) || !$args['title']) return false;

        $headers = [
            'Authorization' => "key=" . config('app.fcmToken'),
            'Content-Type' => 'application/json'
        ];

        $fields = [
            'notification' => [
                'title' => isset($args['title']) ? $args['title'] : '',
                'body' => isset($args['body']) ? $args['body'] : '',
            ],
        ];

        if (isset($args['device_token']) && $args['device_token']) {
            $fields['registration_ids'] = is_array($args['device_token']) ? $args['device_token'] : [$args['device_token']];
        }

        if (isset($args['to']) && $args['to']) $fields['to'] = $args['to'];

        if (isset($args['data']) && $args['data']) $fields['data'] = $args['data'];

        $fields = json_encode($fields);

        $client = new Client();

        $request = $client->post($this->url, [
            'headers' => $headers,
            'json' => $fields
        ]);

        return $request->getBody()->getContents();
    }
}
