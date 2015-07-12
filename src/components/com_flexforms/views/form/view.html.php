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
class FlexformsViewForm extends F0FViewHtml
{
    /**
     * display a form
     *
     * @param   null  $tpl  template to use
     *
     * @return  bool
     *
     * @throws  Exception
     */
    protected function onRead($tpl = null)
    {
        jimport("joomla.filesystem.file");

        $model = $this->getModel();
        $this->assign('item', $model->getItem());
        $this->assign('form', $model->getFormDefinition($this->item->flexforms_form_id));

        // Load form specific language files
        FlexformsHelperLanguage::loadFormLanguageFiles($this->item->form);

        JHtml::_('behavior.formvalidation');

        $this->_tempFilePath = $this->getLayoutFile($this->item->layout, $this->item->form);

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

    /**
     * check different options for layout overwrites
     *
     * @param   string  $layoutName  name of layout file that should be loaded
     * @param   string  $formName    name of form
     *
     * @return  string
     *
     * @throws Exception
     */
    protected function getLayoutFile($layoutName, $formName)
    {
        $layoutParts = explode(".", $layoutName);

        if ($layoutParts[0] == "com")
        {
            $path = JPATH_SITE . "/components/com_flexforms/views/form/tmpl/" . $layoutParts[1] . ".php";

            if (!JFile::exists($path))
            {
                throw new Exception("Invalid layout");
            }

            return $path;
        }

        if ($layoutParts[0] == "tpl")
        {
            $path = JPATH_SITE . "/templates/" . $layoutParts[1] . "/html/com_flexforms/form/" . $layoutParts[2] . ".php";

            if (!JFile::exists($path))
            {
                throw new Exception("Invalid layout");
            }

            return $path;
        }

        if ($layoutParts[0] == "media")
        {
            $path = JPATH_SITE . "/media/com_flexforms/forms/" . $formName . ".php";

            if (!JFile::exists($path))
            {
                throw new Exception("Invalid layout");
            }

            return $path;
        }

        throw new Exception("Invalid layout");
    }

    /**
     * Displays the view
     *
     * @param   string  $tpl  The template to use
     *
     * @return  boolean|null False if we can't render anything
     */
    public function display($tpl = null)
    {
        // Get the task set in the model
        $model = $this->getModel();
        $task = $model->getState('task', 'browse');

        // Show the view
        if ($this->doPreRender)
        {
            $this->preRender();
        }

        // Call the relevant method
        $method_name = 'on' . ucfirst($task);

        if (method_exists($this, $method_name))
        {
            $result = $this->$method_name($tpl);
        }
        else
        {
            $result = $this->onDisplay();
        }

        if ($result === false)
        {
            return;
        }

        if ($this->doPostRender)
        {
            $this->postRender();
        }
    }
}
