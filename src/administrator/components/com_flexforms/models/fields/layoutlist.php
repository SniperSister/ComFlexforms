<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

// No direct access to this file
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Layoutlist Form Field class for the Flexforms component
 *
 * @since  0.0.1
 */
class JFormFieldLayoutlist extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var  string
     */
    protected $type = 'Layoutlist';

    /**
     * Method to get a list of options for a list input.
     *
     * @return array  An array of JHtml options.
     */
    protected function getOptions()
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

        $options = array();

        $options[] = JHtml::_('select.option', 'com.default', JText::_('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_COMPONENT'));
        $options[] = JHtml::_('select.option', 'media.default', JText::_('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_MEDIA'));

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
                            $text = JText::sprintf('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_TEMPLATE', $template->name)
                                . " - "
                                . basename($file, '.php');

                            $options[] = JHtml::_('select.option', $value, $text);
                        }
                    }
                }
            }
        }

        return $options;
    }
}
