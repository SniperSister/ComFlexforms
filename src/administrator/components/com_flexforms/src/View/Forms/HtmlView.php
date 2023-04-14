<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Administrator\View\Forms;

// No direct access
defined('_JEXEC') or die;

use Djumla\Component\Flexforms\Administrator\Helper\FlexformsHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a list of forms.
 *
 * @since  1.6
 */
class HtmlView extends BaseHtmlView
{
    protected $items;

    protected $pagination;

    protected $state;

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
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

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
     * @since    1.6
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $canDo = FlexformsHelper::getActions();

        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('COM_FLEXFORMS_TITLE_FORMS'));

        if ($canDo->get('core.create')) {
            $toolbar->addNew('form.add');
        }

        if ($canDo->get('core.edit.state')) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);


            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.edit.state')) {
                $childBar->publish('forms.publish')->listCheck(true);
                $childBar->unpublish('forms.unpublish')->listCheck(true);
                $childBar->checkin('forms.checkin')->listCheck(true);
                $childBar->trash('forms.delete', 'JACTION_DELETE')->listCheck(true);
            }
        }

        if ($canDo->get('core.admin')) {
            $toolbar->preferences('com_flexforms');
        }
    }

    protected function getSortFields()
    {
        return [
            'a.`id`' => Text::_('JGRID_HEADING_ID'),
            'a.`title`' => Text::_('COM_FLEXFORMS_FORMS_FIELD_TITLE'),
            'a.`enabled`' => Text::_('JSTATUS'),
        ];
    }

    /**
     * Check if state is set
     *
     * @param   mixed  $state  State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
