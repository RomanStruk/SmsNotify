<?php

namespace RomanStruk\SmsNotify\Response;

use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\ResponseInterface;

class Response implements ResponseInterface
{

    /**
     * @var ClientInterface
     */
    public $senderClient;

    /**
     * @var int
     */
    public $statusCode;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @var bool
     */
    public $success;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string
     */
    public $messageId;

    /**
     * @var array
     */
    private $debugInformation = [];

    public function __construct(int $code = 200, bool $success = true, string $message = '')
    {
        $this->statusCode = $code;
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @param mixed $messageId
     */
    public function setMessageId(string $messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->errors[] = $error;
    }
    /**
     * @param array $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * @return string
     */
    public function getMessageId(): string
    {
        return $this->messageId;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param ClientInterface $senderClient
     */
    public function setSenderClient(ClientInterface $senderClient): void
    {
        $this->senderClient = $senderClient;
    }

    /**
     * @return ClientInterface
     */
    public function getSenderClient(): ClientInterface
    {
        return $this->senderClient;
    }

    public function getDebugInformation(): array
    {
        return $this->debugInformation;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setDebugInformation(string $key, string $value): void
    {
        $this->debugInformation[$key] = $value;
    }
}