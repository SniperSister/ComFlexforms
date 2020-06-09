<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

// No direct access
defined('_JEXEC') or die;

JLoader::registerPrefix('Flexforms', JPATH_SITE . '/components/com_flexforms/');

use \Joomla\CMS\Component\Router\RouterViewConfiguration;
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
class FlexformsRouter extends Joomla\CMS\Component\Router\RouterView
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
}
