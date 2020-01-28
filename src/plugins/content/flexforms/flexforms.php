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

        // Load F0F and component
        include_once JPATH_LIBRARIES . '/f0f/include.php';

        if (!defined('F0F_INCLUDED') || !class_exists('F0FForm', true))
        {
            throw new Exception("F0F not found");
        }

        // Load flexform plugins
        JPluginHelper::importPlugin('flexforms');

        // Register helper class
        JLoader::register('FlexformsHelpersLanguage', JPATH_ROOT . "/components/com_flexforms/helpers/language.php");

        foreach ($matches as $match)
        {
            // Config array
            $config = array(
                "input" => array("view" => "form", "option" => "com_flexforms", "task" => "read", "id" => $match[1])
            );

            ob_start();

            F0FDispatcher::getTmpInstance('com_flexforms', "form", $config)->dispatch();
            $formOutput = ob_get_contents();

            ob_end_clean();

            $article->text = str_replace($match[0], $formOutput, $article->text);
        }
    }
}
