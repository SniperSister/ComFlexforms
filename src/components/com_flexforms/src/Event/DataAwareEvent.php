<?php

namespace Djumla\Component\Flexforms\Site\Event;

class DataAwareEvent extends FlexformEvent
{
    protected $legacyArgumentsOrder = ['form', 'jform', 'data'];

    public function __construct($name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        if (!\array_key_exists('data', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'data' of event {$name} is required but has not been provided");
        }
    }

    protected function onSetData(array $value): array
    {
        return $value;
    }

    public function getData(): array
    {
        return $this->arguments['data'];
    }
}