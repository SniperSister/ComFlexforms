<?php

namespace Djumla\Component\Flexforms\Site\Event;

use Joomla\CMS\Event\AbstractImmutableEvent;
use Joomla\CMS\Event\ReshapeArgumentsAware;
use Joomla\CMS\Form\Form;

class FlexformEvent extends AbstractImmutableEvent
{
    use ReshapeArgumentsAware;

    protected $legacyArgumentsOrder = ['form', 'jform'];

    public function __construct($name, array $arguments = [])
    {
        // Reshape the arguments array to preserve b/c with legacy listeners
        if ($this->legacyArgumentsOrder)
        {
            // Reshape
            $arguments = $this->reshapeArguments($arguments, $this->legacyArgumentsOrder);
        }

        parent::__construct($name, $arguments);

        if (!\array_key_exists('form', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'form' of event {$name} is required but has not been provided");
        }

        if (!\array_key_exists('jform', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'jform' of event {$name} is required but has not been provided");
        }
    }

    protected function onSetForm(\stdClass $value): \stdClass
    {
        return $value;
    }

    protected function onSetJform(Form $value): Form
    {
        return $value;
    }

    public function getForm(): \stdClass
    {
        return $this->arguments['form'];
    }

    public function getJform(): Form
    {
        return $this->arguments['jform'];
    }
}