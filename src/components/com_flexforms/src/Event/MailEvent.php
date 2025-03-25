<?php

namespace Djumla\Component\Flexforms\Site\Event;

use Joomla\CMS\Mail\Mail;

class MailEvent extends DataAwareEvent
{
    protected $legacyArgumentsOrder = ['form', 'jform', 'data', 'mail'];

    public function __construct($name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        if (!\array_key_exists('mail', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'mail' of event {$name} is required but has not been provided");
        }
    }

    protected function onSetMail(Mail $value): Mail
    {
        return $value;
    }

    public function getMail(): Mail
    {
        return $this->arguments['mail'];
    }
}