<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

/**
 * Class FlexformsHelpersLayout
 *
 * @since  1.0.0
 */
class FlexformsHelpersLayout
{
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
    public static function getLayoutFile($layoutName, $formName)
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
