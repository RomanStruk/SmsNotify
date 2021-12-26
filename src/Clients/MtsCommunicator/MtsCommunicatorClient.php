<?php

namespace RomanStruk\SmsNotify\Clients\MtsCommunicator;

use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\ClientException;
use RomanStruk\SmsNotify\Exceptions\UnauthorizedException;
use RomanStruk\SmsNotify\Response;

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
        $this->alfa_name = $alfa_name;
        $this->api_url = str_replace('{client_id}', $client_id, $this->api_url);
    }

    /**
     * @param string $phone
     * @param string $message
     * @param string $channel
     * @return Response
     * @throws UnauthorizedException
     */
    public function singleMessages(string $phone, string $message, string $channel): Response
    {
        $json = [
            'phone_number' => $phone,   // Phone number of User. â–ª It is given in the international format without the Â«+Â» sign
            'channels' => [$channel],
            'channel_options' => [  // Have to be specified for each communication channel
                'sms' => [
                    'text' => $message,
                    'alpha_name' => $this->alfa_name,
                    'ttl' => 300     // Message lifetime in seconds
                ]
            ],
        ];
        try {
            $json = $this->request($json)->getBody()->getContents();
        }catch (ClientException $e){
            if ($e->getResponse()->getStatusCode() === 401){
                throw new UnauthorizedException('Unauthorized');
            }
            $json = $e->getResponse()->getBody()->getContents();
        }

        return new Response($json, ResponseMessage::class, null);
    }

    /**
     * @param $json
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request($json)
    {
        $guzzleClient = new GuzzleHttpClient([
            'auth' => [$this->login, $this->password],
        ]);

        return $guzzleClient->post($this->api_url, [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'json' => $json
        ]);
    }
}
