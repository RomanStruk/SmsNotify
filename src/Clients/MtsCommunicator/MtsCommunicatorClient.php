<?php

namespace RomanStruk\SmsNotify\Clients\MtsCommunicator;

use GuzzleHttp\Client as GuzzleHttpClient;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use RomanStruk\SmsNotify\Response\Response;

class MtsCommunicatorClient
{
    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $client_id;

    /**
     * @var string
     */
    private $alfa_name;

    /**
     * @var string
     */
    private $api_url = 'https://api.communicator.mts.by/{client_id}/json2/simple';

    /**
     * @param string $login
     * @param string $password
     * @param string $client_id
     * @param string $alfa_name
     */
    public function __construct(string $login, string $password, string $client_id, string $alfa_name)
    {
        $this->login = $login;
        $this->password = $password;
        $this->client_id = $client_id;
        $this->alfa_name = $alfa_name;
        $this->api_url = str_replace('{client_id}', $this->client_id, $this->api_url);
    }

    /**
     * @param string $phone
     * @param string $message
     * @param string $channel
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $phone, string $message, string $channel): Response
    {
        $guzzleClient = new GuzzleHttpClient([
            'auth' => [$this->login, $this->password],
        ]);

        $response =  $guzzleClient->post($this->api_url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => [
                'phone_number' => $phone,   // Phone number of User. â–ª It is given in the international format without the Â«+Â» sign
                'channels' => [$channel],
                'channel_options' => [  // Have to be specified for each communication channel
                    'sms' => [
                        'text' => $message,
                        'alpha_name' => $this->alfa_name,
                        'ttl' => 300     // Message lifetime in seconds
                    ]
                ],
            ]
        ]);
        if ($response->getStatusCode() === 200) {

            $content = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            $response = new Response();
            if (array_key_exists('message_id', $content)) {
                $response->setMessageId($content['message_id']);
            }
            $response->setErrors($content);
            return $response;
        }
        return new Response();
    }
}
