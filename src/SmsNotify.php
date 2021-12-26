<?php

namespace RomanStruk\SmsNotify;

use Closure;
use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\MessageInterface;
use RomanStruk\SmsNotify\Contracts\PhoneNumberInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;
use RomanStruk\SmsNotify\Contracts\SmsNotifyInterface;
use RomanStruk\SmsNotify\Exceptions\InvalidConfigurationException;

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

    /**
     * @var string|array[]
     */
    private $config;

    /**
     * @param string|array[] $config
     * @throws InvalidConfigurationException
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client($config['default']);
    }

    /**
     * @param string $alias
     * @param array $configuration
     * @return SmsNotifyInterface
     * @throws InvalidConfigurationException
     */
    public function client(string $alias, array $configuration = []): SmsNotifyInterface
    {
        if (! array_key_exists($alias, $this->config['drivers']) || !$client = $this->config['drivers'][$alias]['client']){
            throw new InvalidConfigurationException('Invalid client driver');
        }

        $this->client = new $client(!empty($configuration) ? $configuration: $this->config['drivers'][$alias]);

        return $this;
    }

    /**
     * @param PhoneNumberInterface $phoneNumber
     * @return SmsNotifyInterface
     */
    public function to(PhoneNumberInterface $phoneNumber): SmsNotifyInterface
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    /**
     * @param MessageInterface $message
     * @return ResponseInterface
     */
    public function send(MessageInterface $message): ResponseInterface
    {
        $this->message = $message;

        return $this->client
            ->to($this->phoneNumber)
            ->send($this->message);
    }

    /**
     * @param Closure $closure
     * @return SmsNotify
     * @throws InvalidConfigurationException
     */
    public function clientMap(Closure $closure): SmsNotifyInterface
    {
        $key = $closure($this);

        $this->client($this->config['map'][$key]);
        return $this;
    }
}