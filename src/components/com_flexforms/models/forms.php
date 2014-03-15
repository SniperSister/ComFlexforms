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
class FlexformsModelForms extends FOFModel
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

        if (!$item->flexforms_form_id)
        {
            throw new Exception("Invalid form");
        }

        $form = $this->getFormDefinition();

        return $form->validate($data);
    }

    public function submit($data)
    {
        $item = $this->getItem();

        if (!$item->flexforms_form_id)
        {
            throw new Exception("Invalid form");
        }

        $form = $this->getFormDefinition();

        JPluginHelper::importPlugin('flexforms');

        JEventDispatcher::getInstance()->trigger('onBeforeFlexformsSubmit', array($item, $form, $data));

        if ($item->send_owner_mail == 1)
        {
            $ownerMail = JFactory::getMailer();

            if ($item->owner_subject == "")
            {
                throw new Exception("Missing owner mail subject");
            }

            if ($item->owner_mail == "")
            {
                throw new Exception("Missing owner mail text");
            }

            $owners = explode(",", $item->owners);

            foreach ($owners as $owner)
            {
                if ($owner == "")
                {
                    throw new Exception("Invalid owner addresses");
                }
            }

            $ownerText = $this->parseMailText($item->owner_mail, $data, $form);

            $ownerMail->addRecipient($owners);
            $ownerMail->setSubject($item->owner_subject);
            $ownerMail->setBody($ownerText);

            $ownerMail->Send();
        }

        if ($item->send_sender_mail == 1)
        {
            $senderMail = JFactory::getMailer();
        }

        JEventDispatcher::getInstance()->trigger('onBeforeFlexformsSubmit', array($item, $form, $data));

        return true;
    }

    protected function parseMailText($text, $data, $form)
    {
        foreach ($data as $fieldName => $fieldValue)
        {
            if (strpos($text, "{" . $fieldName . "}"))
            {
                
            }

            $field = $form->getField($fieldName);
        }
    }
}