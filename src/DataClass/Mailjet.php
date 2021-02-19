<?php

namespace App\DataClass;

use Mailjet\Client;
use Mailjet\Resources;

class Mailjet
{
    private $api_key ="52de446b9c56a58b82f0f974851694fd";
    private $api_key_secret = "7cc01f9ef4a1f12323f53a8fb7414ec5";

    public  function send($to_name, $to_email, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "usopola37@gmail.com",
                        'Name' => "Ma Boutique"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2442811,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());
    }
}