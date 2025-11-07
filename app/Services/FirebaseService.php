<?php

namespace App\Services;

use Google_Client;

class FirebaseService
{
    protected $client;
    protected $accessToken;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('app\reusemart-firebase-bc368-firebase-adminsdk-fbsvc-91959b5f1a.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        // $this->client->setSubject('your-service-account-email@your-project.iam.gserviceaccount.com'); // Optional: kalau mau impersonate user
        $this->client->refreshTokenWithAssertion();
        $this->accessToken = $this->client->getAccessToken()['access_token'];
    }

    public function sendMessage($fcmToken, $title, $body, $data = [])
    {
        $url = "https://fcm.googleapis.com/v1/projects/reusemart-firebase-bc368/messages:send";

        $message = [
    "message" => [
        "token" => $fcmToken,
        "notification" => [
            "title" => $title,
            "body" => $body,
        ],
        "android" => [
            "priority" => "HIGH",
            "notification" => [
                "sound" => "default",
                "channel_id" => "default_channel",
            ],
        ],
        "apns" => [
            "headers" => [
                "apns-priority" => "10"
            ],
            "payload" => [
                "aps" => [
                    "sound" => "default"
                ]
            ]
        ],
        "data" => (object) $data,  // cast supaya jadi associative map, bukan list
    ],
];


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->accessToken,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $errorMsg = curl_error($ch);
            \Log::error('Curl error: ' . $errorMsg);
            curl_close($ch);
            throw new \Exception('Curl error: ' . $errorMsg);
        }

        curl_close($ch);

        \Log::info('Firebase send response: ' . $response);
        \Log::info('Access token length: ' . strlen($this->accessToken));

        return json_decode($response, true);
    }
}
