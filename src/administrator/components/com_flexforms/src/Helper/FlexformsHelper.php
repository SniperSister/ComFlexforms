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
use Joomla\CMS\Object\CMSObject;

/**
 * Flexforms helper.
 *
 * @since  1.6
 */
class FlexformsHelper
{
    /**
     * Gets a list of the actions that can be performed.
     *
     * @return    CMSObject
     *
     * @since    1.6
     */
    public static function getActions()
    {
        $user   = Factory::getUser();
        $result = new CMSObject();

        $assetName = 'com_flexforms';

        $actions = [
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        ];

        foreach ($actions as $action)
        {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }
}
