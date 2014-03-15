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

// Load FOF
include_once JPATH_LIBRARIES . '/fof/include.php';

if (!defined('FOF_INCLUDED') || !class_exists('FOFForm', true))
{
    throw new Exception("FOF not found");
}

FOFDispatcher::getTmpInstance('com_flexforms')->dispatch();