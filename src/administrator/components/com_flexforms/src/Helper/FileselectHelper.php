<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Administrator\Helper;

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Language\Text;

/**
 * Class FileselectHelper
 *
 * @since  1.0.0
 */
class FileselectHelper
{
    /**
     * list of available layouts
     *
     * @return array
     */
    public static function getLayoutOptions()
    {
        // Get the database object and a new query object.
        $db = Factory::getContainer()->get('DatabaseDriver');
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

        $options["com.default"]   = Text::_('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_COMPONENT');
        $options["media.default"] = Text::_('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_MEDIA');

        // Loop on all templates
        if ($templates) {
            foreach ($templates as $template) {
                $template_path = Path::clean(JPATH_SITE . '/templates/' . $template->element . '/html/com_flexforms/form');

                // Add the layout options from the template path.
                if (is_dir($template_path) && ($files = Folder::files($template_path, '^[^_]*\.php$', false, true))) {
                    if (count($files)) {
                        foreach ($files as $file) {
                            // Add an option to the template group
                            $value = "tpl." . $template->element . "." . basename($file, '.php');
                            $text = Text::sprintf('COM_FLEXFORMS_FORMS_FIELD_LAYOUT_OPTION_TEMPLATE', $template->name)
                                . " - " . basename($file, '.php');
                            $options[$value] = $text;
                        }
                    }
                }
            }
        }

        return $options;
    }
}
