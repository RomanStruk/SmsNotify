<?php

namespace RomanStruk\SmsNotify\Response;

use RomanStruk\SmsNotify\Contracts\ClientInterface;
use RomanStruk\SmsNotify\Contracts\Response\DeliveryReportInterface;
use RomanStruk\SmsNotify\Contracts\Response\ResponseInterface;

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
     * @var array
     */
    private $debugInformation = [];

    private $deliveryReport = [];

    public function __construct(DeliveryReportInterface $report = null)
    {
        if (! is_null($report)){
            $this->setDeliveryReport($report);
        }
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
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
     * @param mixed $value
     */
    public function setDebugInformation(string $key, $value): void
    {
        $this->debugInformation[$key] = $value;
    }

    /**
     * @param DeliveryReportInterface $report
     */
    public function setDeliveryReport(DeliveryReportInterface $report): void
    {
        $this->deliveryReport[$report->getPhoneNumber()] = $report;
    }

    /**
     * @param string|null $phoneNumber
     * @return string
     */
    public function getStatus(string $phoneNumber = null): string
    {
        if (is_null($phoneNumber)) {
            $array = array_slice($this->deliveryReport, -1);
            return (array_pop($array))->getStatus();
        }
        return $this->deliveryReport[$phoneNumber]->getStatus();
    }

    public function getMessageId($phoneNumber = null)
    {
        if (is_null($phoneNumber)) {
            $array = array_slice($this->deliveryReport, -1);
            return (array_pop($array))->getMessageId();
        }
        return $this->deliveryReport[$phoneNumber]->getMessageId();
    }
}