<?php

namespace RomanStruk\SmsNotify;

use Closure;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;

class SmsNotify implements SmsNotifyInterface
{
    /**
     * @var PhoneNumberInterface $phoneNumber
    */
    private $phoneNumber;

    /**
     * @var MessageInterface $message
     */
    private $message;

    /**
     * @var ClientInterface $client
     */
    private $client;

    private $config;

    /**
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->client($config['default']);
    }

    /**
     * @param string $alias
     * @param array $configuration
     * @return SmsNotifyInterface
     * @throws \Exception
     */
    public function client(string $alias, array $configuration = []): SmsNotifyInterface
    {
        if (! array_key_exists($alias, $this->config['drivers']) || !$client = $this->config['drivers'][$alias]['client']){
            throw new \Exception('Invalid client driver');
        }

        $this->client = new $client(!empty($configuration) ? $configuration: $this->config['drivers'][$alias]);

        return $this;
    }

    public function to(PhoneNumberInterface $phoneNumber): SmsNotifyInterface
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function send(MessageInterface $message): ResponseInterface
    {
        $this->message = $message;

        return $this->client
            ->to($this->phoneNumber)
            ->send($this->message);
    }

    public function debug(bool $mode = false): SmsNotifyInterface
    {
        $this->config['debug'] = $mode;
        $this->client->debug($mode);

        return $this;
    }

    /**
     * @param Closure $closure
     * @return SmsNotify
     * @throws \Exception
     */
    public function clientMap(Closure $closure): SmsNotifyInterface
    {
        $key = $closure($this);

        $this->client($this->config['map'][$key]);
        return $this;
    }

    /**
     * @return SmsNotifyInterface
     */
    public function enableDebug(): SmsNotifyInterface
    {
        $this->debug(true);
        return $this;
    }
}