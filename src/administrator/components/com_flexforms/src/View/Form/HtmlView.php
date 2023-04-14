<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Administrator\View\Form;

// No direct access
defined('_JEXEC') or die;

use Djumla\Component\Flexforms\Administrator\Helper\FlexformsHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View to edit
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
    protected $state;

    protected $item;

    protected $form;

    /**
     * Display the view
     *
     * @param   string  $tpl  Template name
     *
     * @return void
     *
     * @throws \Exception
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new \Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function addToolbar()
    {
        Factory::getApplication()->input->set('hidemainmenu', true);

        $user  = Factory::getUser();
        $isNew = ($this->item->id == 0);

        if (isset($this->item->checked_out)) {
            $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }

        $canDo = FlexformsHelper::getActions();

        ToolbarHelper::title(Text::_('COM_FLEXFORMS_TITLE_FORMS'));

        $toolbarButtons = [];

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create')))) {
            ToolbarHelper::apply('form.apply');
            $toolbarButtons[] = ['save', 'form.save'];

            if ($canDo->get('core.create')) {
                $toolbarButtons[] = ['save2new', 'form.save2new'];
            }
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create')) {
            $toolbarButtons[] = ['save2copy', 'form.save2copy'];
        }

        ToolbarHelper::saveGroup(
            $toolbarButtons,
            'btn-success'
        );

        if (empty($this->item->id)) {
            ToolbarHelper::cancel('form.cancel');
        } else {
            ToolbarHelper::cancel('form.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
