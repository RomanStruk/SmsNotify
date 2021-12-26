<?php

namespace RomanStruk\SmsNotify\Clients\ViberUa;

use RomanStruk\SmsNotify\Contracts\ClientChannelInterface;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Exceptions\InvalidClientConfigurationException;

class Viber implements ClientInterface, ClientChannelInterface
{
    const CHANNEL_VIBER_SMS = 'viber_sms';
    const CHANNEL_VIBER = 'viber';
    const CHANNEL_SMS = 'sms';

    /**
     * @var ViberClient
     */
    private $client;

    /**
     * Канали відправки
     * @var string
     */
    private $channel = 'viber';

    /**
     * @var string
     */
    private $numbers;

    /**
     * @throws InvalidClientConfigurationException
     */
    public function __construct($config)
    {
        if (!$config['token']) {
            throw new InvalidClientConfigurationException('Need token');
        }
        if (!$config['sender_vb'] || !$config['sender_sms']) {
            throw new InvalidClientConfigurationException('Need alfa name for viber or sms');
        }

        $this->client = new ViberClient(
            $config['token'],
            $config['sender_vb'],
            $config['sender_sms']
        );
        $this->setChannel($config['default_channel']);
        $this->setOption('name', $config['sender_vb']);
    }

    /**
     * @param MessageInterface $message
     * @return ResponseInterface
     */
    public function send(MessageInterface $message): ResponseInterface
    {
        return $this->request($this->numbers, $message->getMessage());
    }

    protected function request(string $phone, string $message)
    {
        return call_user_func([$this->client, "{$this->channel}Request"], $phone, $message);
    }

    /**
     * @param string $channel
     * @return Viber
     */
    public function setChannel(string $channel): ClientChannelInterface
    {
        $this->channel = $channel;
        return $this;
    }

    public function setOption($option, $value): void
    {
        if ($option === 'name') {
            $this->client->setMailingName($value);
        }
    }

    public function to(PhoneNumberInterface $phoneNumber): ClientInterface
    {
        $this->numbers = $phoneNumber->implode();
        return $this;
    }
}