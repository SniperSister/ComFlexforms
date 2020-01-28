<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

use Joomla\Utilities\ArrayHelper;

/**
 * Forms list controller class.
 *
 * @since  1.6
 */
class FlexformsControllerForms extends JControllerAdmin
{
    /**
     * Proxy for getModel.
     *
     * @param   string  $name    Optional. Model name
     * @param   string  $prefix  Optional. Class prefix
     * @param   array   $config  Optional. Configuration array for model
     *
     * @return  object The Model
     *
     * @since    1.6
     */
    public function getModel($name = 'form', $prefix = 'FlexformsModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }
}
