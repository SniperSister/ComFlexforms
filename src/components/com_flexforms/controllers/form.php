<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

defined('_JEXEC') or die();

/**
 * Class FlexformsControllerForm
 *
 * @since  1.0.0
 */
class FlexformsControllerForm extends JControllerLegacy
{
    protected $cacheableTasks = array();

    /**
     * submit form
     *
     * @return void
     *
     * @throws Exception
     */
    public function submit()
    {
        $this->checkToken();

        $app = JFactory::getApplication();
        $input = $app->input;

        /** @var FlexformsModelForm $model */
        $model = $this->getModel('Form');
        $item = $model->getItem();

        // Load form specific language files
        FlexformsHelpersLanguage::loadFormLanguageFiles($item->form);

        $inputData = $input->post->getArray();
        $uploadedFiles = $input->files->getArray();

        // Merge uploaded files and post data into one array for validation
        foreach ($uploadedFiles as $field => $file)
        {
            $inputData[$field] = $file['name'];
        }

        // Store user input into session so we are able to restore data after redirect
        $app->setUserState('com_flexforms.form.' . $item->form . '.data', $inputData);

        // Validate user input before starting the send process
        if (!$model->validateUserForm($inputData))
        {
            $this->setRedirect(
                JRoute::_('index.php?option=com_flexforms&view=form&id=' . (int) $input->post->get('id'), false),
                JText::_('COM_FLEXFORMS_FORM_SUBMIT_MSG_INVALID'),
                'error'
            );

            // Append more specific error messages created by JForm
            foreach ($model->getErrors() as $error)
            {
                JFactory::getApplication()->enqueueMessage($error, 'error');
            }

            return;
        }

        // Try to submit the form
        try
        {
            $model->submit($inputData, $uploadedFiles);
        }
        // An error occurred
        catch (Exception $e)
        {
            $this->setRedirect(
                JRoute::_('index.php?option=com_flexforms&view=form&id=' . (int) $input->post->get('id'), false),
                JText::_('COM_FLEXFORMS_FORM_SUBMIT_MSG_SEND_ERROR'),
                'error'
            );

            return;
        }

        // Successful submission, reset saved data
        $app->setUserState('com_flexforms.form.' . $item->form . '.data', null);

        // Use provided URL for redirect to success page
        $successUrl = JRoute::_('index.php?option=com_flexforms&view=form&id=' . (int) $input->post->get('id'), false);

        if (!empty($inputData['successUrl']) && Juri::isInternal(base64_decode($inputData['successUrl'])))
        {
            $successUrl = base64_decode($inputData['successUrl']);
        }

        $item = $model->getItem();

        // Use hardcoded URL for redirect, overwriting everything else
        if ($item->redirecturl)
        {
            $successUrl = JText::_($item->redirecturl);
        }

        $successMessage = JText::_('COM_FLEXFORMS_FORM_SUBMIT_MSG_SENT');

        // Use hardcoded success message
        if ($item->custommessage)
        {
            $successMessage = JText::_($item->custommessage);
        }

        // Everything went fine, return
        $this->setRedirect(
            $successUrl,
            $successMessage,
            'message'
        );
    }
}
