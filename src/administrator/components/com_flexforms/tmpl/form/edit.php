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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

?>
<form
    action="<?php echo Route::_('index.php?option=com_flexforms&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" enctype="multipart/form-data" name="adminForm" id="form-form" class="form-validate"
>
    <div class="main-card">
        <div class="row">
            <div class="col-12">
                <fieldset class="options-form">
                    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
                    <?php echo $this->form->renderField('title'); ?>
                    <?php echo $this->form->renderField('enabled'); ?>
                    <?php echo $this->form->renderField('layout'); ?>
                    <?php echo $this->form->renderField('form'); ?>
                    <?php echo $this->form->renderField('redirecturl'); ?>
                    <?php echo $this->form->renderField('custommessage'); ?>
                    <?php echo $this->form->renderField('jsvalidation'); ?>
                    <?php echo $this->form->renderField('spacer'); ?>
                    <?php echo $this->form->renderField('send_owner_mail'); ?>
                    <?php echo $this->form->renderField('owners'); ?>
                    <?php echo $this->form->renderField('owner_mail_type'); ?>
                    <?php echo $this->form->renderField('owner_mail_template'); ?>
                    <?php echo $this->form->renderField('owner_subject'); ?>
                    <?php echo $this->form->renderField('owner_mail'); ?>
                    <?php echo $this->form->renderField('owner_attachments'); ?>
                    <?php echo $this->form->renderField('spacer1'); ?>
                    <?php echo $this->form->renderField('send_sender_mail'); ?>
                    <?php echo $this->form->renderField('sender_mail_type'); ?>
                    <?php echo $this->form->renderField('sender_mail_template'); ?>
                    <?php echo $this->form->renderField('sender_field'); ?>
                    <?php echo $this->form->renderField('sender_subject'); ?>
                    <?php echo $this->form->renderField('sender_mail'); ?>
                    <?php echo $this->form->renderField('sender_attachments'); ?>
                </fieldset>
            </div>
        </div>

        <input type="hidden" name="task" value=""/>
        <?php echo HTMLHelper::_('form.token'); ?>

    </div>
</form>
