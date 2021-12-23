<?php

namespace RomanStruk\SmsNotify;

use Countable;
use Iterator;

class Response implements Countable, Iterator
{
    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $messages = [];

    protected $messagesKey;
    /**
     * @var null
     */
    private $messageClass;

    public function __construct($data, $messageClass = null, $messagesKey= null)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('expected response data to be a string');
        }

        $this->data = json_decode($data, true);
        if (is_null($messagesKey)) {
            $this->messagesKey = 'messages';
            $this->data[$this->messagesKey] = $this->data;
        }else{
            $this->messagesKey = $messagesKey;
        }
        $this->messageClass = $messageClass;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function getMessages(): array
    {
        return $this->data[$this->messagesKey] ?? [];
    }


    public function current()
    {
        if (!isset($this->messages[$this->position])) {
            if (! $this->valid()){
                return new $this->messageClass($this->data);
            }
            $this->messages[$this->position] = new $this->messageClass($this->data[$this->messagesKey][$this->position]);
        }

        return $this->messages[$this->position];
    }

    public function count(): int
    {
        return count($this->data[$this->messagesKey]);
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): int
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return isset($this->data[$this->messagesKey][$this->position]);
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}