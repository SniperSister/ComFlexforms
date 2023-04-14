<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Administrator\Model;

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * Flexforms model.
 *
 * @since  1.6
 */
class FormModel extends AdminModel
{
    /**
     * @var      string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_FLEXFORMS';

    /**
     * @var     string  Alias to manage history control
     * @since   3.2
     */
    public $typeAlias = 'com_flexforms.form';

    /**
     * @var null  Item data
     * @since  1.6
     */
    protected $item = null;

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return    Table    A database object
     *
     * @since    1.6
     */
    public function getTable($type = 'Form', $prefix = 'Administrator', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      An optional array of data for the form to interogate.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return  Form  A JForm object on success, false on failure
     *
     * @since    1.6
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        return $this->loadForm(
            'com_flexforms.form',
            'form',
            [
                'control' => 'jform',
                'load_data' => $loadData
            ]
        );
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return   mixed  The data for the form.
     *
     * @since    1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_flexforms.edit.form.data', []);

        if (empty($data)) {
            if ($this->item === null) {
                $this->item = $this->getItem();
            }

            $data = $this->item;
        }

        return $data;
    }
}
