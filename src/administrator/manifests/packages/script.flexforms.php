<?php
/**
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

defined('_JEXEC') or die;

/**
 * Flexforms installer class
 *
 * @package  Flexforms
 */
class Pkg_FlexformsInstallerScript
{
    /**
     * The minimum PHP version required to install this extension
     *
     * @var   string
     */
    protected $minimumPHPVersion = '7.4.0';

    /**
     * The minimum Joomla! version required to install this extension
     *
     * @var   string
     */
    protected $minimumJoomlaVersion = '4.0.0';

    /**
     * The maximum Joomla! version this extension can be installed on
     *
     * @var   string
     */
    protected $maximumJoomlaVersion = '4.9.999';


    /**
     * Joomla! pre-flight event. This runs before Joomla! installs or updates the package. This is our last chance to
     * tell Joomla! if it should abort the installation.
     *
     * @param   string                     $type    Installation type (install, update, discover_install)
     * @param   \JInstallerAdapterPackage  $parent  Parent object
     *
     * @return  boolean  True to let the installation proceed, false to halt the installation
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function preflight($type, $parent)
    {
        // Check the minimum PHP version
        if (!version_compare(PHP_VERSION, $this->minimumPHPVersion, 'ge')) {
            $msg = "<p>You need PHP $this->minimumPHPVersion or later to install this package</p>";
            JLog::add($msg, JLog::WARNING, 'jerror');

            return false;
        }

        // Check the minimum Joomla! version
        if (!version_compare(JVERSION, $this->minimumJoomlaVersion, 'ge')) {
            $msg = "<p>You need Joomla! $this->minimumJoomlaVersion or later to install this component</p>";
            JLog::add($msg, JLog::WARNING, 'jerror');

            return false;
        }

        // Check the maximum Joomla! version
        if (!version_compare(JVERSION, $this->maximumJoomlaVersion, 'le')) {
            $msg = "<p>You need Joomla! $this->maximumJoomlaVersion or earlier to install this component</p>";
            JLog::add($msg, JLog::WARNING, 'jerror');

            return false;
        }
    }
}
