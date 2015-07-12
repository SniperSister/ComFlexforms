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

// Load F0F
include_once JPATH_LIBRARIES . '/f0f/include.php';

if (!defined('F0F_INCLUDED') || !class_exists('F0FForm', true))
{
    throw new Exception("F0F not found");
}

F0FDispatcher::getTmpInstance('com_flexforms')->dispatch();
