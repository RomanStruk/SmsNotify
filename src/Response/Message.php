<?php

namespace RomanStruk\SmsNotify\Response;

abstract class Message
{
    /**
     * @var array
     */
    protected $data;

    protected $errorKey = 'error-text';
    protected $messageIdKey = 'message-id';
    protected $statusKey = 'status';

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param $param
     * @return mixed
     */
    protected function checkData($param)
    {
        if (!array_key_exists($param, $this->data)) {
            throw new \RuntimeException('tried to access ' . $param . ' but data is missing');
        }

        return $this->data[$param];
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function getErrorMessage(): string
    {
        if (!isset($this->data[$this->errorKey])) {
            return '';
        }

        return $this->checkData($this->errorKey);
    }

    public function getId()
    {
        return $this->checkData($this->messageIdKey);
    }

    public function getStatus()
    {
        return $this->checkData($this->statusKey);
    }
}