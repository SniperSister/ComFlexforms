<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Site\Helper;

use Joomla\CMS\Factory;

/**
 * Class FlexformsHelperLanguage
 *
 * @since  1.0.0
 */
class LanguageHelper
{
    /**
     * Load form specific language files
     * filename must be com_flexforms.{formname}.ini and be save in system language folder
     * or in media/com_flexforms/language/{LANG}/
     *
     * @param   string  $formName  The name of the form
     *
     * @return  void
     */
    public static function loadFormLanguageFiles ($formName)
    {
        $jlang = Factory::getLanguage();
        $jlang->load('com_flexforms.' . $formName, JPATH_SITE, 'en-GB', true);
        $jlang->load('com_flexforms.' . $formName, JPATH_SITE, $jlang->getDefault(), true);
        $jlang->load('com_flexforms.' . $formName, JPATH_SITE, null, true);

        $jlang->load('com_flexforms.' . $formName, JPATH_SITE . '/media/com_flexforms', 'en-GB', true);
        $jlang->load('com_flexforms.' . $formName, JPATH_SITE . '/media/com_flexforms', $jlang->getDefault(), true);
        $jlang->load('com_flexforms.' . $formName, JPATH_SITE . '/media/com_flexforms', null, true);
    }
}
