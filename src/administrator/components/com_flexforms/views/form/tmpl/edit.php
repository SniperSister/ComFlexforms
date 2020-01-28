<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Learnmoreboxes
 * @author     djumla GmbH <info@djumla.de>
 * @copyright  2019 djumla GmbH
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {

    });

    Joomla.submitbutton = function (task) {
        if (task == 'form.cancel') {
            Joomla.submitform(task, document.getElementById('form-form'));
        }
        else {

            if (task != 'form.cancel' && document.formvalidator.isValid(document.id('form-form'))) {

                Joomla.submitform(task, document.getElementById('form-form'));
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
    action="<?php echo JRoute::_('index.php?option=com_flexforms&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="form-form" class="form-validate">

    <div class="form-horizontal">
        <div class="row-fluid">
            <div class="span10 form-horizontal">
                <fieldset class="adminform">
                    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
                    <?php echo $this->form->renderField('title'); ?>
                    <?php echo $this->form->renderField('enabled'); ?>
                    <?php echo $this->form->renderField('layout'); ?>
                    <?php echo $this->form->renderField('form'); ?>
                    <?php echo $this->form->renderField('redirecturl'); ?>
                    <?php echo $this->form->renderField('spacer'); ?>
                    <?php echo $this->form->renderField('send_owner_mail'); ?>
                    <?php echo $this->form->renderField('owners'); ?>
                    <?php echo $this->form->renderField('owner_subject'); ?>
                    <?php echo $this->form->renderField('owner_mail'); ?>
                    <?php echo $this->form->renderField('owner_attachments'); ?>
                    <?php echo $this->form->renderField('spacer1'); ?>
                    <?php echo $this->form->renderField('send_sender_mail'); ?>
                    <?php echo $this->form->renderField('sender_field'); ?>
                    <?php echo $this->form->renderField('sender_subject'); ?>
                    <?php echo $this->form->renderField('sender_mail'); ?>
                    <?php echo $this->form->renderField('sender_attachments'); ?>
                </fieldset>
            </div>
        </div>

        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
