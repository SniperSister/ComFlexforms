<?php
/**
 * @version    %%PLUGINVERSION%%
 * @package    Flexforms
 * @copyright  2017 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Djumla\Component\Flexforms\Site\Helper\LanguageHelper;
use Djumla\Component\Flexforms\Site\Helper\LayoutHelper;
use Joomla\CMS\Application\SiteApplication;

/**
 * Class PlgSystemFlexforms
 *
 * @since  1.0.0
 */
class PlgSystemFlexforms extends CMSPlugin
{
    /**
     * @var    \Joomla\CMS\Application\CMSApplication
     *
     * @since  3.2
     */
    protected $app;

    /**
     * Remember me method to run onAfterInitialise
     * Only purpose is to initialise the login authentication process if a cookie is present
     *
     * @return  void
     *
     * @since   1.5
     *
     * @throws  InvalidArgumentException
     */
    public function onAfterRoute()
    {
        // No remember me for admin.
        if (!$this->app->isClient('administrator')) {
            return;
        }

        if ($this->app->getInput()->get('option') !== 'com_mails') {
            return;
        }

        // Load language files
        $db = Factory::getDbo();
        $forms = $db->setQuery("SELECT DISTINCT(form) FROM #__flexforms_forms WHERE enabled = 1")->loadColumn();

        foreach ($forms as $form) {
            LanguageHelper::loadFormLanguageFiles($form);
        }
    }
}
