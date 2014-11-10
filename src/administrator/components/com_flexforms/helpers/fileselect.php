<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/**
 * Class FlexformsHelperFileselect
 *
 * @since  1.0.0
 */
class FlexformsHelperFileselect
{
    /**
     * list of available layouts
     *
     * @return array
     */
    public static function getLayoutOptions()
    {
        // Get the database object and a new query object.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // Build the query.
        $query->select('e.element, e.name')
            ->from('#__extensions as e')
            ->where('e.client_id = 0')
            ->where('e.type = ' . $db->quote('template'))
            ->where('e.enabled = 1');

        // Set the query and load the templates.
        $db->setQuery($query);
        $templates = $db->loadObjectList('element');

        $options["com.default"] = JText::_('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_COMPONENT');

        // Loop on all templates
        if ($templates)
        {
            foreach ($templates as $template)
            {
                $template_path = JPath::clean(JPATH_SITE . '/templates/' . $template->element . '/html/com_flexforms/form');

                // Add the layout options from the template path.
                if (is_dir($template_path) && ($files = JFolder::files($template_path, '^[^_]*\.php$', false, true)))
                {
                    if (count($files))
                    {
                        foreach ($files as $file)
                        {
                            // Add an option to the template group
                            $value = "tpl." . $template->element . "." . basename($file, '.php');
                            $text = JText::sprintf('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_TEMPLATE', $template->name) . " - " . basename($file, '.php');
                            $options[$value] = $text;
                        }
                    }
                }
            }
        }

        return $options;
    }

    /**
     * returns a list of available forms
     *
     * @return  array list of forms
     */
    public static function getFormOptions()
    {
        jimport('joomla.filesystem.folder');

        $files = JFolder::files(JPATH_SITE . '/media/com_flexforms/forms', '.xml');

        $options = array();

        $options[] = JText::_('COM_FLEXFORMS_COMMON_SELECT');

        foreach ($files as $file)
        {
            $filename = basename($file, ".xml");
            $options[$filename] = $filename;
        }

        return $options;
    }
}