<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Site\Controller;

// No direct access
defined('_JEXEC') or die;

use Djumla\Component\Flexforms\Administrator\Model\FormModel;
use Djumla\Component\Flexforms\Site\Helper\LanguageHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Class FlexformsControllerForm
 *
 * @since  1.0.0
 */
class FormController extends BaseController
{
    protected $cacheableTasks = [];

    /**
     * submit form
     *
     * @return void
     */
    public function submit()
    {
        $this->checkToken();

        $app = Factory::getApplication();
        $input = $app->input;

        /** @var FormModel $model */
        $model = $this->getModel('Form');
        $item = $model->getItem();

        // Load form specific language files
        LanguageHelper::loadFormLanguageFiles($item->form);

        $inputData = $input->post->getArray();
        $uploadedFiles = $input->files->getArray();

        // Merge uploaded files and post data into one array for validation
        foreach ($uploadedFiles as $field => $file) {
            $inputData[$field] = $file['name'];
        }

        // Store user input into session so we are able to restore data after redirect
        $app->setUserState('com_flexforms.form.' . $item->form . '.data', $inputData);

        // Validate user input before starting the send process
        if (!$model->validateUserForm($inputData)) {
            $this->setRedirect(
                Route::_('index.php?option=com_flexforms&view=form&id=' . (int) $input->post->get('id'), false),
                Text::_('COM_FLEXFORMS_FORM_SUBMIT_MSG_INVALID'),
                'error'
            );

            // Append more specific error messages created by JForm
            foreach ($model->getErrors() as $error) {
                Factory::getApplication()->enqueueMessage($error, 'error');
            }

            return;
        }

        // Try to submit the form
        try {
            $model->submit($inputData, $uploadedFiles);
        } catch (\Exception $e) {
            // An error occurred
            $this->setRedirect(
                Route::_('index.php?option=com_flexforms&view=form&id=' . (int) $input->post->get('id'), false),
                Text::_('COM_FLEXFORMS_FORM_SUBMIT_MSG_SEND_ERROR'),
                'error'
            );

            return;
        }

        // Successful submission, reset saved data
        $app->setUserState('com_flexforms.form.' . $item->form . '.data', null);

        // Use provided URL for redirect to success page
        $successUrl = Route::_('index.php?option=com_flexforms&view=form&id=' . (int) $input->post->get('id'), false);

        if (!empty($inputData['successUrl']) && Uri::isInternal(base64_decode($inputData['successUrl']))) {
            $successUrl = base64_decode($inputData['successUrl']);
        }

        $item = $model->getItem();

        // Use hardcoded URL for redirect, overwriting everything else
        if ($item->redirecturl) {
            $successUrl = Text::_($item->redirecturl);
        }

        $successMessage = Text::_('COM_FLEXFORMS_FORM_SUBMIT_MSG_SENT');

        // Use hardcoded success message
        if ($item->custommessage) {
            $successMessage = Text::_($item->custommessage);
        }

        // Everything went fine, return
        $this->setRedirect(
            $successUrl,
            $successMessage,
            'message'
        );
    }
}
