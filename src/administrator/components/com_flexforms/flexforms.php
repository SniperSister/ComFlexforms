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

// Old PHP version detected. EJECT! EJECT! EJECT!
if (!version_compare(phpversion(), '5.3.0', '>='))
{
    throw new Exception("Your PHP Version is outdated");
}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_flexforms'))
{
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix(
    'Flexforms',
    JPATH_COMPONENT_ADMINISTRATOR
);

JLoader::register(
    'FlexformsHelper',
    JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'flexforms.php'
);

$controller = JControllerLegacy::getInstance('Flexforms');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
