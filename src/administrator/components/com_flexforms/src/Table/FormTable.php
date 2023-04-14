<?php
/**
 * @version    %%COMPONENTVERSION%%
 * @package    Flexforms
 * @copyright  2014 David Jardin
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link       http://www.djumla.de
 */

namespace Djumla\Component\Flexforms\Administrator\Table;

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

/**
 * Box Table class
 *
 * @since  1.6
 */
class FormTable extends Table
{
    /**
     * Constructor
     *
     * @param   DatabaseDriver  &$db  A database connector object
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
        $input = Factory::getApplication()->input;
        $task = $input->getString('task', '');

        if ($array['id'] == 0 && empty($array['created_by'])) {
            $array['created_by'] = Factory::getUser()->id;
        }

        if ($array['id'] == 0 && empty($array['created_on'])) {
            $array['created_on'] = Factory::getDate()->toSql();
        }

        if ($array['id'] == 0 && empty($array['modified_by'])) {
            $array['modified_by'] = Factory::getUser()->id;
        }

        if ($task === 'apply' || $task === 'save') {
            $array['modified_on'] = Factory::getDate()->toSql();
            $array['modified_by'] = Factory::getUser()->id;
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
        try {
            parent::check();
        } catch (\Exception $e) {
            $this->setError($e->getMessage());

            return false;
        }


        if (empty($this->slug)) {
            $this->slug = $this->title;
        }

        $this->slug = ApplicationHelper::stringURLSafe($this->slug);

        return true;
    }
}
