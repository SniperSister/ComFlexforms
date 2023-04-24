<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Site\Service;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Mail\MailTemplate;

/**
 * Class MailingHelper
 *
 * @since  1.0.0
 */
class Mailing
{
    protected $item;

    protected $data;

    protected $form;

    protected $files;

    protected $language;

    public function __construct(object $item, Form $form, array $data, array $files)
    {
        $this->item = $item;
        $this->data = $data;
        $this->files = $files;
        $this->form = $form;
        $this->language = Factory::getLanguage();
    }

    public function sendSenderMail()
    {

        // Check mail subject
        if ($this->item->sender_subject == "") {
            throw new \Exception("Missing sender mail subject");
        }

        // Check if the sender field name is correct
        if (!$this->form->getField($this->item->sender_field)) {
            throw new \Exception("Invalid sender field name");
        }

        // Check and append recipients
        if (!MailHelper::isEmailAddress($this->data[$this->item->sender_field])) {
            throw new \Exception("Invalid sender addresses");
        }

        if ($this->item->owner_mail_type == "1") {
            $this->sendOwnerMailTemplate();

            return;
        }

        $this->sendOwnerMailManual();
    }

    protected function sendSenderMailManual()
    {
        $senderMail = Factory::getMailer();

        // Check mail text
        if ($this->item->sender_mail == "") {
            throw new \Exception("Missing sender mail text");
        }

        // Get mail body and subject and check if they are i18n strings
        $senderText = ($this->language->hasKey($this->item->sender_mail)) ? Text::_($this->item->sender_mail) : $this->item->sender_mail;
        $senderSubject = ($this->language->hasKey($this->item->sender_subject)) ? Text::_($this->item->sender_subject) : $this->item->sender_subject;

        // Parse text
        Factory::getApplication()->triggerEvent('onBeforeFlexformsParseSenderEmailtext', [&$this->item, &$this->form, &$this->data, &$senderText]);

        $senderText = $this->parseMailText($senderText);
        $senderSubject = $this->parseMailText($senderSubject);

        Factory::getApplication()->triggerEvent('onAfterFlexformsParseSenderEmailtext', [&$this->item, &$this->form, &$this->data, &$senderText]);

        // Attach uploaded files
        if ($this->item->sender_attachments) {
            $this->attachFilesToMail($senderMail);
        }

        // Apply mail attributes
        $senderMail->addRecipient($this->data[$this->item->sender_field]);
        $senderMail->setSubject($senderSubject);
        $senderMail->setBody($senderText);
        $senderMail->isHtml(false);

        if (!empty($senderMail)) {
            Factory::getApplication()->triggerEvent('onBeforeFlexformsSendSenderMail', [&$this->item, &$this->form, &$this->data, &$senderMail]);
            $senderMail->Send();
            Factory::getApplication()->triggerEvent('onAfterFlexformsSendSenderMail', [&$this->item, &$this->form, &$this->data, &$senderMail]);
        }
    }

    protected function sendSenderMailTemplate()
    {
        // Check mail text
        if ($this->item->sender_mail_template == "") {
            throw new \Exception("Missing sender mail template");
        }

        // Prepare email and try to send it
        $senderMail = new MailTemplate($this->item->sender_mail_template, $this->language->getTag());
        $senderMail->addTemplateData($this->data);
        $senderMail->addRecipient($this->data[$this->item->sender_field]);

        // Attach uploaded files
        if ($this->item->owner_attachments) {
            $this->attachFilesToMail($senderMail);
        }

        Factory::getApplication()->triggerEvent('onBeforeFlexformsSendSenderMailTemplate', [&$this->item, &$this->form, &$this->data, &$senderMail]);
        $senderMail->send();
        Factory::getApplication()->triggerEvent('onAfterFlexformsSendSenderMailTemplate', [&$this->item, &$this->form, &$this->data, &$senderMail]);
    }

    public function sendOwnerMail()
    {
        // Check that the subject exists
        if ($this->item->owner_subject == "") {
            throw new \Exception("Missing owner mail subject");
        }

        // Split owner array by comma
        $owners = explode(",", $this->item->owners);

        // Check and append recipients
        foreach ($owners as $owner) {
            if (!MailHelper::isEmailAddress($owner)) {
                throw new \Exception("Invalid owner addresses");
            }
        }


        if ($this->item->sender_mail_type == "1") {
            $this->sendSenderMailTemplate();

            return;
        }

        $this->sendSenderMailManual();
    }

    protected function sendOwnerMailManual()
    {
        $ownerMail = Factory::getMailer();

        // Check that the owner exists
        if ($this->item->owner_mail == "") {
            throw new \Exception("Missing owner mail text");
        }

        // Get mail body and subject and check if they are i18n strings
        $ownerText = ($this->language->hasKey($this->item->owner_mail)) ? Text::_($this->item->owner_mail) : $this->item->owner_mail;
        $ownerSubject = ($this->language->hasKey($this->item->owner_subject)) ? Text::_($this->item->owner_subject) : $this->item->owner_subject;

        // Parse text
        Factory::getApplication()->triggerEvent('onBeforeFlexformsParseOwnerEmailtext', [&$this->item, &$this->form, &$this->data, &$ownerText]);

        $ownerText = $this->parseMailText($ownerText);
        $ownerSubject = $this->parseMailText($ownerSubject);

        Factory::getApplication()->triggerEvent('onAfterFlexformsParseOwnerEmailtext',[&$this->item, &$this->form, &$this->data, &$ownerText]);

        // Attach uploaded files
        if ($this->item->owner_attachments) {
            $this->attachFilesToMail($ownerMail);
        }

        // Apply mail attributes
        $ownerMail->addRecipient($owners);
        $ownerMail->setSubject($ownerSubject);
        $ownerMail->setBody($ownerText);
        $ownerMail->isHtml(false);

        // Everything seems to be fine, send mails
        if (!empty($ownerMail)) {
            Factory::getApplication()->triggerEvent('onBeforeFlexformsSendOwnerMail', [&$this->item, &$this->form, &$this->data, &$ownerMail]);
            $ownerMail->Send();
            Factory::getApplication()->triggerEvent('onAfterFlexformsSendOwnerMail', [&$this->item, &$this->form, &$this->data, &$ownerMail]);
        }
    }

    protected function sendOwnerMailTemplate()
    {
        // Check mail template
        if ($this->item->owner_mail_template == "") {
            throw new \Exception("Missing owner mail template");
        }

        // Prepare email and try to send it
        $ownerMail = new MailTemplate($this->item->owner_mail_template, $this->language->getTag());
        $ownerMail->addTemplateData($this->data);

        // Add owner
        foreach (explode(",", $this->item->owners) as $owner) {
            $ownerMail->addRecipient($owner);
        }

        // Attach uploaded files
        if ($this->item->owner_attachments) {
            $this->attachFilesToMail($ownerMail);
        }

        Factory::getApplication()->triggerEvent('onBeforeFlexformsSendSenderMailTemplate', [&$this->item, &$this->form, &$this->data, &$ownerMail]);
        $ownerMail->send();
        Factory::getApplication()->triggerEvent('onAfterFlexformsSendSenderMailTemplate', [&$this->item, &$this->form, &$this->data, &$ownerMail]);
    }

    protected function attachFilesToMail($mail)
    {
        Factory::getApplication()->triggerEvent('onBeforeFlexformsAddAttachments', [&$this->files]);

        if (count($this->files)) {
            foreach ($this->files as $file)
            {
                if (!$file['tmp_name'])
                {
                    continue;
                }

                if ($mail instanceof MailTemplate)
                {
                    $mail->addAttachment($file['name'], $file['tmp_name']);

                    continue;
                }

                $mail->addAttachment($file['tmp_name'], $file['name']);
            }
        }
    }

    /**
     * replaced placeholders in mail templates
     *
     * @param   string  $text  mail text
     * @param   array   $this->data  user data
     * @param   Form   $this->form  current form object
     *
     * @return  string  parsed text
     */
    protected function parseMailText($text)
    {
        foreach ($this->data as $fieldName => $fieldValue) {
            $field = $this->form->getField($fieldName);

            // Placeholder present and field valid?
            if (!strpos($text, "{" . $fieldName . "}") || !$field) {
                continue;
            }

            // Replace placeholder
            if (is_array($fieldValue)) {
                $text = str_ireplace("{" . $fieldName . "}", implode(", ", $fieldValue), $text);
            } else {
                $text = str_ireplace("{" . $fieldName . "}", $fieldValue, $text);
            }
        }

        return $text;
    }
}
