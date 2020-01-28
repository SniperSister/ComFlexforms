<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

/**
 * Class FlexformsViewForm
 *
 * @since  1.0.0
 */
class FlexformsViewForm extends JViewLegacy
{
    /**
     * display a form
     *
     * @param   null  $tpl  template to use
     *
     * @return  boolean
     *
     * @throws  Exception
     */
    public function display($tpl = null)
    {
        jimport("joomla.filesystem.file");

        $model = $this->getModel();
        $this->assign('item', $model->getItem());
        $this->assign('form', $model->getFormDefinition($this->item->id));

        // Generate submit route - use a plain index.php if the component is called from the plugin
        $route = JRoute::_('index.php');

        if (!JFactory::getApplication()->input->get('option', 'com_flexforms') !== "com_flexforms")
        {
            $route = JURI::base() . 'index.php';
        }

        $this->assign('route', $route);

        // Load form specific language files
        FlexformsHelpersLanguage::loadFormLanguageFiles($this->item->form);

        JHtml::_('behavior.formvalidation');

        $this->_tempFilePath = FlexformsHelpersLayout::getLayoutFile($this->item->layout, $this->item->form);

        unset($model);
        unset($tpl);

        // Start capturing output into a buffer
        ob_start();

        // Include the requested template filename in the local scope (this will execute the view logic).
        include $this->_tempFilePath;

        // Done with the requested template; get the buffer and clear it.
        $this->_output = ob_get_contents();
        ob_end_clean();

        echo $this->_output;

        return true;
    }
}
