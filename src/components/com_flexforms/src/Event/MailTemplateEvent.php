<?php

namespace Djumla\Component\Flexforms\Site\Event;

use Joomla\CMS\Mail\MailTemplate;

class MailTemplateEvent extends DataAwareEvent
{
    protected $legacyArgumentsOrder = ['form', 'jform', 'data', 'mail'];

    public function __construct($name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        if (!\array_key_exists('mail', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'mail' of event {$name} is required but has not been provided");
        }
    }

    protected function onSetMail(MailTemplate $value): MailTemplate
    {
        return $value;
    }

    public function getMail(): MailTemplate
    {
        return $this->arguments['data'];
    }
}