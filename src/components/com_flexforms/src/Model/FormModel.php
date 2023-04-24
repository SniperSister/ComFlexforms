<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Site\Model;

use Djumla\Component\Flexforms\Site\Helper\LanguageHelper;
use Djumla\Component\Flexforms\Site\Service\Mailing;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Mail\Mail;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Utilities\ArrayHelper;

/**
 * Class FlexformsModelForms
 *
 * @since  1.0.0
 */
class FormModel extends ItemModel
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
        if ($this->_item === null) {
            $this->_item = false;

            if (empty($id)) {
                $id = $this->getState('form.id');
            }

            // Get a level row instance.
            $table = $this->getTable();

            // Attempt to load the row.
            if ($table->load($id)) {
                // Check published state.
                if (!$table->enabled) {
                    throw new \Exception(Text::_('NOT_FOUND'), 404);
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
    public function getTable($type = 'Form', $prefix = 'Administrator', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * fetch a form
     *
     * @param   int  $id  form id
     *
     * @return   Form
     *
     * @throws   \Exception
     */
    public function getFormDefinition($id = null)
    {
        $item = $this->getItem($id);

        Form::addFormPath(JPATH_SITE . "/media/com_flexforms/forms");

        $form = Form::getInstance("com.flexforms." . $item->form, $item->form);

        if ($form == false) {
            throw new \Exception("Invalid form returned");
        }

        Factory::getApplication()->triggerEvent('onBeforeFlexformsReturnForm', [&$form, &$item]);

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

        if (!$item->id) {
            throw new \Exception("Invalid form");
        }

        $form = $this->getFormDefinition();

        Factory::getApplication()->triggerEvent('onBeforeFlexformsValidate', [&$item, &$form, &$data]);

        $result = $form->validate($data);

        Factory::getApplication()->triggerEvent('onAfterFlexformsValidate', [&$item, &$form, &$data, &$result]);

        // Append error messages
        if (! $result) {
            foreach ($form->getErrors() as $error) {
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
     */
    public function submit($data, $files)
    {
        $item = $this->getItem();

        if (!$item->id) {
            throw new \Exception("Invalid form");
        }

        // Load form specific language files
        LanguageHelper::loadFormLanguageFiles($item->form);

        $form = $this->getFormDefinition();

        Factory::getApplication()->triggerEvent('onBeforeFlexformsSubmit', [&$item, &$form, &$data]);

        $mailService = new Mailing($item, $form, $data, $files);

        // Prepare owner mail
        if ($item->send_owner_mail == 1) {
            $mailService->sendOwnerMail();;
        }

        // Prepare sender email
        if ($item->send_sender_mail == 1)
        {
            $mailService->sendSenderMail();
        }

        // Trigger "after submit" event
        Factory::getApplication()->triggerEvent('onAfterFlexformsSubmit', [&$item, &$form, &$data]);

        return true;
    }
}
