<?php
/**
 * @version    %%PLUGINVERSION%%
 * @package    Flexforms
 * @copyright  2017 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Plugin\Content\Flexforms\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Event\Content\ContentPrepareEvent;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Djumla\Component\Flexforms\Site\Helper\LanguageHelper;
use Djumla\Component\Flexforms\Site\Helper\LayoutHelper;
use Joomla\CMS\Application\SiteApplication;
use Joomla\Event\SubscriberInterface;

/**
 * Class PlgContentFlexforms
 *
 * @since  1.0.0
 */
class Flexforms extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['onContentPrepare' => 'onContentPrepare'];
    }

    public function onContentPrepare(ContentPrepareEvent $event)
    {
        $context = $event->getContext();
        $article = $event->getItem();

        // Don't run this plugin when the content is being indexed
        if ($context === 'com_finder.indexer') {
            $article->text = preg_replace('/{flexform (\\d*)}/', '', $article->text);
        }

        // Simple performance check to determine whether bot should process further
        if (strpos($article->text, 'flexform') === false) {
            return true;
        }

        // Expression to search for
        $regex = '/{flexform (\\d*)}/';

        // Find all instances of plugin and put in $matches for kolumbusslideshow
        // $matches[0] is full pattern match, $matches[1] is the form id
        preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER, 0);

        // No matches found, skip
        if (!$matches) {
            return false;
        }

        if (!Factory::getApplication() instanceof SiteApplication) {
            return true;
        }

        // Load flexform plugins
        PluginHelper::importPlugin('flexforms');

        foreach ($matches as $match) {
            /** @var \Djumla\Component\Flexforms\Site\Model\FormModel $model */
            $model = Factory::getApplication()->bootComponent('com_flexforms')
                ->getMVCFactory()
                ->createModel('Form', 'Site');

            // Load data
            $this->item = $model->getItem($match[1]);
            $this->form = $model->getFormDefinition($this->item->id);

            // Generate submit route - use a plain index.php if the component is called from the plugin
            $route = Route::_('index.php');

            if (!Factory::getApplication()->input->get('option', 'com_flexforms') !== "com_flexforms") {
                $route = \Joomla\CMS\Uri\Uri::base() . 'index.php';
            }

            $this->route = $route;

            // Load form specific language files
            LanguageHelper::loadFormLanguageFiles($this->item->form);

            // Enable js-based frontend validation
            if ($this->item->jsvalidation) {
                /** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
                $wa = Factory::getDocument()->getWebAssetManager();
                $wa->useScript('keepalive')
                    ->useScript('form.validate');
            }

            $tempFilePath = LayoutHelper::getLayoutFile($this->item->layout, $this->item->form);

            unset($model, $tpl);

            // Start capturing output into a buffer
            ob_start();

            // Include the requested template filename in the local scope (this will execute the view logic).
            include $tempFilePath;

            // Done with the requested template; get the buffer and clear it.
            $output = ob_get_contents();
            ob_end_clean();

            $article->text = str_replace($match[0], $output, $article->text);
        }
    }
}
