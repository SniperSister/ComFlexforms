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
class FlexformsControllerForm extends F0FController
{
    /**
     * submit form
     *
     * @return void
     *
     * @throws Exception
     */
    public function submit()
    {
        $this->_csrfProtection();

        $input = JFactory::getApplication()->input;
        $model = $this->getThisModel();

        $inputData = $input->post->getArray();
        $uploadedFiles = $input->files->getArray();

        // Merge uploaded files and post data into one array for validation
        foreach ($uploadedFiles as $field => $file)
        {
            $inputData[$field] = $file['name'];
        }

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

        // Everything went fine, return
        $this->setRedirect(
            JRoute::_('index.php?option=com_flexforms&view=form&id=' . (int) $input->post->get('id'), false),
            JText::_('COM_FLEXFORMS_FORM_SUBMIT_MSG_SENT'),
            'message'
        );
    }
}