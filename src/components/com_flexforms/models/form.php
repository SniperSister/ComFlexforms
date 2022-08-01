<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

/**
 * Class FlexformsModelForms
 *
 * @since  1.0.0
 */
class FlexformsModelForm extends JModelItem
{
    // @codingStandardsIgnoreLine
    protected $_item;

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @return void
     *
     * @since    1.6
     */
    protected function populateState()
    {
        $id = Factory::getApplication()->input->get('id');
        $this->setState('form.id', $id);
    }

    public function getItem($id = null)
    {
        if ($this->_item === null)
        {
            $this->_item = false;

            if (empty($id))
            {
                $id = $this->getState('form.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id))
            {
                // Check published state.
                if (!$table->enabled)
                {
                    throw new Exception(JText::_('NOT_FOUND'), 404);
                }

                // Convert the JTable to a clean JObject.
                $properties  = $table->getProperties(1);
                $this->_item = ArrayHelper::toObject($properties, 'JObject');
                $this->_item->flexforms_form_id = $this->_item->id;
            }
        }

        return $this->_item;
    }

    /**
     * Get an instance of JTable class
     *
     * @param   string  $type    Name of the JTable class to get an instance of.
     * @param   string  $prefix  Prefix for the table class name. Optional.
     * @param   array   $config  Array of configuration values for the JTable object. Optional.
     *
     * @return  JTable|bool JTable if success, false on failure.
     */
    public function getTable($type = 'Form', $prefix = 'FlexformsTable', $config = array())
    {
        $this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_flexforms/tables');

        return JTable::getInstance($type, $prefix, $config);
    }

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

        Factory::getApplication()->triggerEvent('onBeforeFlexformsReturnForm', array(&$form, &$item));

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

        if (!$item->id)
        {
            throw new Exception("Invalid form");
        }

        $form = $this->getFormDefinition();

        Factory::getApplication()->triggerEvent('onBeforeFlexformsValidate', array(&$item, &$form, &$data));

        $result = $form->validate($data);

        Factory::getApplication()->triggerEvent('onAfterFlexformsValidate', array(&$item, &$form, &$data, &$result));

        // Append error messages
        if ( ! $result)
        {
            foreach ($form->getErrors() as $error)
            {
                $this->setError($error->getMessage());
            }
        }

        return $result;
    }

    /**
     * submits a form
     *
     * @param   array  $data   user data
     * @param   array  $files  uploaded files
     *
     * @return bool
     *
     * @throws Exception
     */
    public function submit($data, $files)
    {
        $item = $this->getItem();
        $language = JFactory::getLanguage();

        if (!$item->id)
        {
            throw new Exception("Invalid form");
        }

        // Load form specific language files
        FlexformsHelpersLanguage::loadFormLanguageFiles($item->form);

        $form = $this->getFormDefinition();

        Factory::getApplication()->triggerEvent('onBeforeFlexformsSubmit', array(&$item, &$form, &$data));

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

            // Get mail body and subject and check if they are i18n strings
            $ownerText = ($language->hasKey($item->owner_mail)) ? JText::_($item->owner_mail) : $item->owner_mail;
            $ownerSubject = ($language->hasKey($item->owner_subject)) ? JText::_($item->owner_subject) : $item->owner_subject;

            // Parse text
            Factory::getApplication()->triggerEvent('onBeforeFlexformsParseOwnerEmailtext', array(&$item, &$form, &$data, &$ownerText));

            $ownerText = $this->parseMailText($ownerText, $data, $form);
            $ownerSubject = $this->parseMailText($ownerSubject, $data, $form);

            Factory::getApplication()->triggerEvent('onAfterFlexformsParseOwnerEmailtext', array(&$item, &$form, &$data, &$ownerText));

            // Attach uploaded files
            if ($item->owner_attachments)
            {
                $this->attachFiles($files, $ownerMail);
            }

            // Apply mail attributes
            $ownerMail->addRecipient($owners);
            $ownerMail->setSubject($ownerSubject);
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

            // Get mail body and subject and check if they are i18n strings
            $senderText = ($language->hasKey($item->sender_mail)) ? JText::_($item->sender_mail) : $item->sender_mail;
            $senderSubject = ($language->hasKey($item->sender_subject)) ? JText::_($item->sender_subject) : $item->sender_subject;

            // Parse text
            Factory::getApplication()->triggerEvent('onBeforeFlexformsParseSenderEmailtext', array(&$item, &$form, &$data, &$senderText));

            $senderText = $this->parseMailText($senderText, $data, $form);
            $senderSubject = $this->parseMailText($senderSubject, $data, $form);

            Factory::getApplication()->triggerEvent('onAfterFlexformsParseSenderEmailtext', array(&$item, &$form, &$data, &$senderText));

            // Attach uploaded files
            if ($item->sender_attachments)
            {
                $this->attachFiles($files, $senderMail);
            }

            // Apply mail attributes
            $senderMail->addRecipient($data[$item->sender_field]);
            $senderMail->setSubject($senderSubject);
            $senderMail->setBody($senderText);
            $senderMail->isHtml(false);
        }

        // Everything seems to be fine, send mails
        if (!empty($ownerMail))
        {
            Factory::getApplication()->triggerEvent('onBeforeFlexformsSendOwnerMail', array(&$item, &$form, &$data, &$ownerMail));
            $ownerMail->Send();
            Factory::getApplication()->triggerEvent('onAfterFlexformsSendOwnerMail', array(&$item, &$form, &$data, &$ownerMail));
        }

        if (!empty($senderMail))
        {
            Factory::getApplication()->triggerEvent('onBeforeFlexformsSendSenderMail', array(&$item, &$form, &$data, &$senderMail));
            $senderMail->Send();
            Factory::getApplication()->triggerEvent('onAfterFlexformsSendSenderMail', array(&$item, &$form, &$data, &$senderMail));
        }

        // Trigger "after submit" event
        Factory::getApplication()->triggerEvent('onAfterFlexformsSubmit', array(&$item, &$form, &$data));

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

    /**
     * Append uploaded files to sender or admin email
     *
     * @param   array  $files  array with uploaded files
     * @param   JMail  &$mail  mail to send
     *
     * @return  void
     */
    protected function attachFiles(array $files, JMail &$mail)
    {
        Factory::getApplication()->triggerEvent('onBeforeFlexformsAddAttachments', array(&$files));

        if (count($files))
        {
            foreach ($files as $file)
            {
                if (!$file['tmp_name'])
                {
                    continue;
                }

                $mail->addAttachment($file['tmp_name'], $file['name']);
            }
        }
    }
}
