<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Site\Controller;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Class FlexformsController
 *
 * @since  1.6
 */
class DisplayController extends BaseController
{
    /**
     * The default view.
     *
     * @var    string
     * @since  1.6
     */
    protected $default_view = 'form';
}
