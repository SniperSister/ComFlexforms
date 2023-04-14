<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Site\View\Form;

// No direct access
defined('_JEXEC') or die;

use Djumla\Component\Flexforms\Site\Helper\LanguageHelper;
use Djumla\Component\Flexforms\Site\Helper\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

/**
 * Class FlexformsViewForm
 *
 * @since  1.0.0
 */
class HtmlView extends BaseHtmlView
{
    /**
     * display a form
     *
     * @param   null  $tpl  template to use
     *
     * @return  boolean
     */
    public function display($tpl = null)
    {
        $app    = Factory::getApplication();
        $this->params = $app->getParams();

        $model = $this->getModel();
        $this->item = $model->getItem();
        $this->form = $model->getFormDefinition($this->item->id);

        // Restore saved form data
        $data = (array) Factory::getApplication()->getUserState('com_flexforms.form.' . $this->item->form . '.data', []);
        $this->form->bind($data);

        // Generate submit route - use a plain index.php if the component is called from the plugin
        $route = Route::_('index.php');

        if (!Factory::getApplication()->input->get('option', 'com_flexforms') !== "com_flexforms")
        {
            $route = Uri::base() . 'index.php';
        }

        $this->route = $route;

        // Load form specific language files
        LanguageHelper::loadFormLanguageFiles($this->item->form);

        /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = $this->document->getWebAssetManager();

        // Enable js-based frontend validation
        if ($this->item->jsvalidation)
        {
            $wa->useScript('form.validate');
        }

        $wa->useScript('keepalive');
        $wa->useScript('showon');

        $this->_tempFilePath = LayoutHelper::getLayoutFile($this->item->layout, $this->item->form);

        unset($model);
        unset($tpl);

        $this->prepareDocument();

        // Start capturing output into a buffer
        ob_start();

        // Include the requested template filename in the local scope (this will execute the view logic).
        include $this->_tempFilePath;

        // Done with the requested template; get the buffer and clear it.
        $this->_output = ob_get_contents();
        ob_end_clean();

        echo $this->_output;

        return true;
    }

    /**
     * Prepares the document.
     *
     * @return  void
     */
    protected function prepareDocument()
    {
        $app   = Factory::getApplication();
        $title = $this->params->get('page_title', '');

        // Check for empty title and add site name if param is set
        if (empty($title))
        {
            $title = $app->get('sitename');
        }
        elseif ($app->get('sitename_pagetitles', 0) == 1)
        {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        }
        elseif ($app->get('sitename_pagetitles', 0) == 2)
        {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        if (empty($title))
        {
            $title = $this->item->title;
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }
}
