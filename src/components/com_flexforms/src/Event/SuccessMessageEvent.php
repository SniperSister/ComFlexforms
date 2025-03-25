<?php

namespace Djumla\Component\Flexforms\Site\Event;

class SuccessMessageEvent extends FlexformEvent
{
    protected $legacyArgumentsOrder = ['form', 'jform', 'data', 'successMessage'];

    public function __construct($name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        if (!\array_key_exists('successMessage', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'successMessage' of event {$name} is required but has not been provided");
        }
    }

    protected function onSetSuccessMessage(string $value): string
    {
        return $value;
    }

    public function getSuccessMessage(): string
    {
        return $this->arguments['successMessage'];
    }
}