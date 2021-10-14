<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

// No direct access
use Joomla\CMS\Application\ApplicationHelper;

defined('_JEXEC') or die;

/**
 * Box Table class
 *
 * @since  1.6
 */
class FlexformsTableForm extends JTable
{
    /**
     * Constructor
     *
     * @param   JDatabase  &$db  A database connector object
     */
    public function __construct(&$db)
    {
        parent::__construct('#__flexforms_forms', 'id', $db);

        $this->setColumnAlias('published', 'enabled');
        $this->setColumnAlias('checked_out', 'locked_by');
        $this->setColumnAlias('checked_out_time', 'locked_on');
    }

    /**
     * Overloaded bind function to pre-process the params.
     *
     * @param   array  $array   Named array
     * @param   mixed  $ignore  Optional array or list of parameters to ignore
     *
     * @return  null|string  null is operation was satisfactory, otherwise returns an error
     *
     * @see     JTable:bind
     * @since   1.5
     */
    public function bind($array, $ignore = '')
    {
        $input = JFactory::getApplication()->input;
        $task = $input->getString('task', '');

        if ($array['id'] == 0 && empty($array['created_by']))
        {
            $array['created_by'] = JFactory::getUser()->id;
        }

        if ($array['id'] == 0 && empty($array['created_on']))
        {
            $array['created_on'] = JFactory::getDate()->toSql();
        }

        if ($array['id'] == 0 && empty($array['modified_by']))
        {
            $array['modified_by'] = JFactory::getUser()->id;
        }

        if ($task == 'apply' || $task == 'save')
        {
            $array['modified_on'] = JFactory::getDate()->toSql();
            $array['modified_by'] = JFactory::getUser()->id;
        }

        return parent::bind($array, $ignore);
    }

    /**
     * Overloaded check method to ensure data integrity.
     *
     * @return  boolean  True on success.
     */
    public function check()
    {
        try
        {
            parent::check();
        }
        catch (\Exception $e)
        {
            $this->setError($e->getMessage());

            return false;
        }


        if (empty($this->slug))
        {
            $this->slug = $this->title;
        }

        $this->slug = ApplicationHelper::stringURLSafe($this->slug);

        return true;
    }
}
