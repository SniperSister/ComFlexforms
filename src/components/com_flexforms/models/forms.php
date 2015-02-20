<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

/**
 * Class FlexformsModelForms
 *
 * @since  1.0.0
 */
class FlexformsModelForms extends F0FModel
{
    /**
     * fetch a form
     *
     * @param   int  $id  form id
     *
     * @return   mixed|object
     *
     * @throws   Exception
     */
    public function getFormDefinition($id = null)
    {
        $item = $this->getItem($id);

        JForm::addFormPath(JPATH_SITE . "/media/com_flexforms/forms");

        $form = JForm::getInstance("com.flexforms." . $item->form, $item->form);

        if ($form == false)
        {
            throw new Exception("Invalid form returned");
        }

        JEventDispatcher::getInstance()->trigger('onBeforeFlexformsReturnForm', array($form));

        return $form;
    }

    /**
     * validate entered data
     *
     * @param   array  $data  post form data
     *
     * @return  mixed
     *
     * @throws Exception
     */
    public function validateUserForm($data)
    {
        $item = $this->getItem();
        $dispatcher = JEventDispatcher::getInstance();

        if (!$item->flexforms_form_id)
        {
            throw new Exception("Invalid form");
        }

        $form = $this->getFormDefinition();

        $dispatcher->trigger('onBeforeFlexformsValidate', array(&$item, &$form, &$data));

        $result = $form->validate($data);

        $dispatcher->trigger('onAfterFlexformsValidate', array(&$item, &$form, &$data, &$result));

        return $result;
    }

    /**
     * submits a form
     *
     * @param   array  $data  user data
     *
     * @return bool
     *
     * @throws Exception
     */
    public function submit($data)
    {
        $item = $this->getItem();
        $dispatcher = JEventDispatcher::getInstance();

        if (!$item->flexforms_form_id)
        {
            throw new Exception("Invalid form");
        }

        $form = $this->getFormDefinition();

        $dispatcher->trigger('onBeforeFlexformsSubmit', array(&$item, &$form, &$data));

        // Prepare owner mail
        if ($item->send_owner_mail == 1)
        {
            $ownerMail = JFactory::getMailer();

            // Check that the subject exists
            if ($item->owner_subject == "")
            {
                throw new Exception("Missing owner mail subject");
            }

            // Check that the owner exists
            if ($item->owner_mail == "")
            {
                throw new Exception("Missing owner mail text");
            }

            // Split owner array by comma
            $owners = explode(",", $item->owners);

            // Check and append recipients
            foreach ($owners as $owner)
            {
                if (!JMailHelper::isEmailAddress($owner))
                {
                    throw new Exception("Invalid owner addresses");
                }
            }

            // Parse text
            $ownerText = $this->parseMailText($item->owner_mail, $data, $form);

            // Apply mail attributes
            $ownerMail->addRecipient($owners);
            $ownerMail->setSubject($item->owner_subject);
            $ownerMail->setBody($ownerText);
            $ownerMail->isHtml(false);
        }

        // Prepare sender email
        if ($item->send_sender_mail == 1)
        {
            $senderMail = JFactory::getMailer();

            // Check mail subject
            if ($item->sender_subject == "")
            {
                throw new Exception("Missing owner mail subject");
            }

            // Check mail text
            if ($item->sender_mail == "")
            {
                throw new Exception("Missing owner mail text");
            }

            // Check if the sender field name is correct
            if (!$form->getField($item->sender_field))
            {
                throw new Exception("Invalid sender field name");
            }

            // Check and append recipients
            if (!JMailHelper::isEmailAddress($data[$item->sender_field]))
            {
                throw new Exception("Invalid sender addresses");
            }

            // Parse text
            $senderText = $this->parseMailText($item->sender_mail, $data, $form);

            // Apply mail attributes
            $senderMail->addRecipient($data[$item->sender_field]);
            $senderMail->setSubject($item->sender_subject);
            $senderMail->setBody($senderText);
            $senderMail->isHtml(false);
        }

        // Everything seems to be fine, send mails
        if (!empty($ownerMail))
        {
            $dispatcher->trigger('onBeforeFlexformsSendOwnerMail', array(&$item, &$form, &$data, &$ownerMail));
            $ownerMail->Send();
        }

        if (!empty($senderMail))
        {
            $dispatcher->trigger('onBeforeFlexformsSendSenderMail', array(&$item, &$form, &$data, &$senderMail));
            $senderMail->Send();
        }

        // Trigger "after submit" event
        $dispatcher->trigger('onAfterFlexformsSubmit', array(&$item, &$form, &$data));

        return true;
    }

    /**
     * replaced placeholders in mail templates
     *
     * @param   string  $text  mail text
     * @param   array   $data  user data
     * @param   JForm   $form  current form object
     *
     * @return  string  parsed text
     */
    protected function parseMailText($text, $data, $form)
    {
        foreach ($data as $fieldName => $fieldValue)
        {
            $field = $form->getField($fieldName);

            // Placeholder present and field valid?
            if (!strpos($text, "{" . $fieldName . "}") || !$field)
            {
                continue;
            }

            // Replace placeholder
            if (is_array($fieldValue))
            {
                $text = str_ireplace("{" . $fieldName . "}", implode(", ", $fieldValue), $text);
            }
            else
            {
                $text = str_ireplace("{" . $fieldName . "}", $fieldValue, $text);
            }
        }

        return $text;
    }
}