<?php

namespace Djumla\Component\Flexforms\Site\Event;

class MailTextEvent extends DataAwareEvent
{
    protected $legacyArgumentsOrder = ['form', 'jform', 'data', 'text'];

    public function __construct($name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        if (!\array_key_exists('text', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'text' of event {$name} is required but has not been provided");
        }
    }

    protected function onSetText(string $value): string
    {
        return $value;
    }

    public function getText(): string
    {
        return $this->arguments['text'];
    }
}