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

// Load flexform plugins
JPluginHelper::importPlugin('flexforms');

// Register helper class
JLoader::registerPrefix('Flexforms', JPATH_COMPONENT);
JLoader::register('FlexformsController', JPATH_COMPONENT . '/controller.php');

// Compatibility Layer for Flexforms 1.0
if (JFactory::getApplication()->input->get('task') === 'submit')
{
    JFactory::getApplication()->input->set('task', 'form.submit');
}

// Execute the task.
$controller = JControllerLegacy::getInstance('Flexforms');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
