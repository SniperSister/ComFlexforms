<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Administrator\Extension;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Psr\Container\ContainerInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('JPATH_PLATFORM') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Component class for com_banners
 *
 * @since  4.0.0
 */
class FlexformsComponent extends MVCComponent implements
    BootableExtensionInterface,
    RouterServiceInterface
{
    use RouterServiceTrait;

    public function boot(ContainerInterface $container)
    {
    }
}
