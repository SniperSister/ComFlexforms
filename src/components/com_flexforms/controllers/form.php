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
    public function onBeforeRead()
    {
        JFactory::getApplication()->setUserState('com_flexforms.starttime', time());

        return true;
    }

    /**
     * submit form
     *
     * @return void
     *
     * @throws Exception
     */
    public function submit()
    {
        $starttime = JFactory::getApplication()->getUserState('com_flexforms.starttime');
        $now = time();
        $delay = 5;

        if ($starttime + $delay > $now)
        {
            // some submitted the form to fast, seems a to be a bot
            $this->setRedirect('index.php');
            return;
        }

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
                '<li>' . implode('</li><li>', $model->getErrors()) . '</li>',
                'error'
            );

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