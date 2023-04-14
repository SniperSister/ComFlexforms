<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

defined('_JEXEC') or die();

?>

<h1><?php echo $this->item->title ?></h1>

<form enctype="multipart/form-data" action="<?php echo $this->route ?>" method="post" id="flexforms-form-<?php echo $this->item->flexforms_form_id ?>" class="flexforms-form form-validate formid-<?php echo $this->item->flexforms_form_id ?> formtype-<?php echo $this->item->form ?>">
    <?php foreach($this->form->getFieldset() as $field): ?>
        <?php echo $field->label; ?>
        <?php echo $field->input; ?>
    <?php endforeach; ?>

    <div class="clearfix"></div>

    <input type="submit" />

    <input type="hidden" name="task" value="form.submit" />
    <input type="hidden" name="component" value="com_flexforms" />
    <input type="hidden" name="option" value="com_flexforms" />
    <input type="hidden" name="view" value="form" />
    <input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</form>
