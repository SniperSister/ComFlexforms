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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');

$user      = JFactory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
$canOrder  = $user->authorise('core.edit.state', 'com_flexforms');

$sortFields = $this->getSortFields();
?>

<form action="<?php echo JRoute::_('index.php?option=com_flexforms&view=forms'); ?>" method="post"
      name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)): ?>
        <div id="j-sidebar-container" class="span2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif; ?>
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
        <div class="clearfix"></div>
        <table class="table table-striped" id="formsList">
            <thead>
                <tr>
                    <th width="1%" class="hidden-phone">
                        <input type="checkbox" name="checkall-toggle" value=""
                               title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <?php if (isset($this->items[0]->enabled)): ?>
                        <th width="1%" class="nowrap center">
                                <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.`enabled`', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>

                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort',  'COM_FLEXFORMS_FORMS_FIELD_ID', 'a.`id`', $listDirn, $listOrder); ?>
                    </th>
                    <th class='left'>
                        <?php echo JHtml::_('searchtools.sort',  'COM_FLEXFORMS_FORMS_FIELD_TITLE', 'a.`title`', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($this->items as $i => $item) :
                    $canCreate  = $user->authorise('core.create', 'com_flexforms');
                    $canEdit    = $user->authorise('core.edit', 'com_flexforms');
                    $canCheckin = $user->authorise('core.manage', 'com_flexforms');
                    $canChange  = $user->authorise('core.edit.state', 'com_flexforms');
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="hidden-phone">
                            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                        </td>
                        <?php if (isset($this->items[0]->enabled)): ?>
                            <td class="center">
                                <?php echo JHtml::_('jgrid.published', $item->enabled, $i, 'forms.', $canChange, 'cb'); ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?php echo $item->id; ?>
                        </td>
                        <td>
                            <?php if (isset($item->checked_out) && $item->checked_out && ($canEdit || $canChange)) : ?>
                                <?php echo JHtml::_('jgrid.checkedout', $i, $item->uEditor, $item->checked_out_time, 'flexforms.', $canCheckin); ?>
                            <?php endif; ?>

                            <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_flexforms&task=form.edit&id='.(int) $item->id); ?>">
                                <?php echo $this->escape($item->title); ?></a>
                            <?php else : ?>
                                <?php echo $this->escape($item->title); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="list[fullorder]" value="<?php echo $listOrder; ?> <?php echo $listDirn; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<script>
    window.toggleField = function (id, task, field) {

        var f = document.adminForm, i = 0, cbx, cb = f[ id ];

        if (!cb) return false;

        while (true) {
            cbx = f[ 'cb' + i ];

            if (!cbx) break;

            cbx.checked = false;
            i++;
        }

        var inputField   = document.createElement('input');

        inputField.type  = 'hidden';
        inputField.name  = 'field';
        inputField.value = field;
        f.appendChild(inputField);

        cb.checked = true;
        f.boxchecked.value = 1;
        window.submitform(task);

        return false;
    };
</script>