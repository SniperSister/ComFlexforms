<?php
/**
 * @version    %%PLUGINVERSION%%
 * @package    Flexforms
 * @copyright  2017 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

defined('_JEXEC') or die;

/**
 * Class PlgContentFlexforms
 *
 * @since  1.0.0
 */
class PlgContentFlexforms extends JPlugin
{
    /**
     * Plugin that loads a form within content
     *
     * @param   string   $context   The context of the content being passed to the plugin.
     * @param   object   &$article  The article object.  Note $article->text is also available
     * @param   mixed    &$params   The article params
     * @param   integer  $page      The 'page' number
     *
     * @return  mixed   true if there is an error. Void otherwise.
     *
     * @throws  Exception
     *
     * @since   1.6
     */
    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer')
        {
            return true;
        }

        // Simple performance check to determine whether bot should process further
        if (strpos($article->text, 'flexform') === false)
        {
            return true;
        }

        // Expression to search for
        $regex = '/{flexform (\\d*)}/';

        // Find all instances of plugin and put in $matches for kolumbusslideshow
        // $matches[0] is full pattern match, $matches[1] is the form id
        preg_match_all($regex, $article->text, $matches, PREG_SET_ORDER, 0);

        // No matches found, skip
        if (!$matches)
        {
            return false;
        }

        // Load flexform plugins
        JPluginHelper::importPlugin('flexforms');

        // Load language helper
        JLoader::registerPrefix('Flexforms', JPATH_SITE . "/components/com_flexforms");

        jimport("joomla.filesystem.file");

        foreach ($matches as $match)
        {
            // Load model
            JModelLegacy::addIncludePath(JPATH_SITE . "/components/com_flexforms/models");
            $model = JModelLegacy::getInstance('Form', 'FlexformsModel');

            // Load data
            $this->item = $model->getItem($match[1]);
            $this->form = $model->getFormDefinition($this->item->id);

            // Generate submit route - use a plain index.php if the component is called from the plugin
            $route = JRoute::_('index.php');

            if (!JFactory::getApplication()->input->get('option', 'com_flexforms') !== "com_flexforms")
            {
                $route = JURI::base() . 'index.php';
            }

            $this->route = $route;

            // Load form specific language files
            FlexformsHelpersLanguage::loadFormLanguageFiles($this->item->form);

            JHtml::_('behavior.formvalidation');

            $tempFilePath = $this->getLayoutFile($this->item->layout, $this->item->form);

            unset($model);
            unset($tpl);

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

    /**
     * check different options for layout overwrites
     *
     * @param   string  $layoutName  name of layout file that should be loaded
     * @param   string  $formName    name of form
     *
     * @return  string
     *
     * @throws Exception
     */
    protected function getLayoutFile($layoutName, $formName)
    {
        $layoutParts = explode(".", $layoutName);

        if ($layoutParts[0] == "com")
        {
            $path = JPATH_SITE . "/components/com_flexforms/views/form/tmpl/" . $layoutParts[1] . ".php";

            if (!JFile::exists($path))
            {
                throw new Exception("Invalid layout");
            }

            return $path;
        }

        if ($layoutParts[0] == "tpl")
        {
            $path = JPATH_SITE . "/templates/" . $layoutParts[1] . "/html/com_flexforms/form/" . $layoutParts[2] . ".php";

            if (!JFile::exists($path))
            {
                throw new Exception("Invalid layout");
            }

            return $path;
        }

        if ($layoutParts[0] == "media")
        {
            $path = JPATH_SITE . "/media/com_flexforms/forms/" . $formName . ".php";

            if (!JFile::exists($path))
            {
                throw new Exception("Invalid layout");
            }

            return $path;
        }

        throw new Exception("Invalid layout");
    }
}
