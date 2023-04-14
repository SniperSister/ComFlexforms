<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Site\Service;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Menu\AbstractMenu;

/**
 * Class FlexformsRouter
 *
 * @since  3.3
 */
class Router extends RouterView
{
    public function __construct(CMSApplication $app = null, AbstractMenu $menu = null)
    {
        $form = new RouterViewConfiguration('form');
        $form->setKey('id');
        $this->registerView($form);

        parent::__construct($app, $menu);

        $this->attachRule(new MenuRules($this));
        $this->attachRule(new StandardRules($this));
        $this->attachRule(new NomenuRules($this));
    }

    /**
     * Method to get the segment(s) for a form
     *
     * @param   string  $id       Segment of the form to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getFormSegment($id, $query)
    {
        return [(int) $id => $id];
    }

    /**
     * Method to get the segment(s) for a form
     *
     * @param   string  $segment  ID of the form to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getFormId($segment, $query)
    {
        return (int) $segment;
    }
}
