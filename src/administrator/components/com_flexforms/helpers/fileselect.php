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

class FlexformsHelperFileselect
{
    /**
     * list of available layouts
     *
     * @return array
     */
    public static function getLayoutOptions()
    {
        $client = JApplicationHelper::getClientInfo(0);

        // Get the extension.
        $extension = "com_flexforms";
        $extension = preg_replace('#\W#', '', $extension);

        // Get the view.
        $view = "form";

        // If a template, extension and view are present build the options.
        if ($extension && $view && $client)
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

            // Build the search paths for component layouts.
            $component_path = JPath::clean($client->path . '/components/' . $extension . '/views/' . $view . '/tmpl');

            // Prepare array of component layouts
            $component_layouts = array();

            // Prepare the grouped list
            $options = array();

            // Add the layout options from the component path.
            if (is_dir($component_path) && ($component_layouts = JFolder::files($component_path, '^[^_]*\.xml$', false, true)))
            {
                foreach ($component_layouts as $i => $file)
                {
                    // Attempt to load the XML file.
                    if (!$xml = simplexml_load_file($file))
                    {
                        unset($component_layouts[$i]);

                        continue;
                    }

                    // Get the help data from the XML file if present.
                    if (!$menu = $xml->xpath('layout[1]'))
                    {
                        unset($component_layouts[$i]);

                        continue;
                    }

                    // Add an option to the component group
                    $value = basename($file, '.xml');
                    $text = JText::_('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_COMPONENT') . " - " . $value;
                    $options["com." . $value] = $text;
                }
            }

            // Loop on all templates
            if ($templates)
            {
                foreach ($templates as $template)
                {
                    $template_path = JPath::clean($client->path . '/templates/' . $template->element . '/html/' . $extension . '/' . $view);

                    // Add the layout options from the template path.
                    if (is_dir($template_path) && ($files = JFolder::files($template_path, '^[^_]*\.php$', false, true)))
                    {
                        // Files with corresponding XML files are alternate menu items, not alternate layout files
                        // so we need to exclude these files from the list.
                        $xml_files = JFolder::files($template_path, '^[^_]*\.xml$', false, true);

                        for ($j = 0, $count = count($xml_files); $j < $count; $j++)
                        {
                            $xml_files[$j] = basename($xml_files[$j], '.xml');
                        }

                        foreach ($files as $i => $file)
                        {
                            // Remove layout files that exist in the component folder or that have XML files
                            if ((in_array(basename($file, '.php'), $component_layouts))
                             || (in_array(basename($file, '.php'), $xml_files)))
                            {
                                unset($files[$i]);
                            }
                        }

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