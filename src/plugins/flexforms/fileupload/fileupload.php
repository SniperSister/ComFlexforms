<?php
/**
 * Flexforms
 *
 * @package    Flexforms
 * @author     Robert Deutz <rdeutz@googlemail.com>
 *
 * @copyright  2015 Robert Deutz
 * @license    GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * Flexforms Fileupload Plugin
 *
 * @package     com_flexforms
 * @since       1.0
 */
class PlgFlexformsFileupload extends JPlugin
{
	public function onBeforeFlexformsValidate(&$item, &$form, &$data) {}

	public function onAfterFlexformsValidate(&$item, &$form, &$data, &$result) {}

	public function onBeforeFlexformsSubmit(&$item, &$form, &$data)
	{
		$fileFields = $this->getFileFields($form);

		$time = time();

		$uploaddir = JPATH_SITE . '/' . ltrim($this->params->get('uploaddir', 'media/com_flexforms/upload', '/\\'));

		$input = JFactory::getApplication()->input;

		foreach ($fileFields as $field)
		{
			$file = $input->files->get($field);

			if ( ! is_null($file) && $file['error'] == 0)
			{
				$data[$field]  =  $uploaddir . '/' . $time . '-' . $file['name'];

				JFile::move($file['tmp_name'], $data[$field]);
			}
		}
	}

	private function getFileFields($form)
	{
		$result = [];
		$keys = array_keys($form->getFieldset());

		foreach($keys as $key)
		{
			if ($form->getFieldAttribute($key, 'type') == 'file')
			{
				$result[] = $key;
			}
		}

		return $result;
	}

	public function onBeforeFlexformsSendOwnerMail(&$item, &$form, &$data, &$ownerMail)
	{
		$fileFields = $this->getFileFields($form);

		foreach ($fileFields as $field)
		{
			if (file_exists($data[$field]))
			{
				$filename = basename($data[$field]);
				$realname = substr($filename, strpos($filename, '-') + 1);
				$ownerMail->addAttachment($data[$field], $realname);
			}
		}
	}

	public function onBeforeFlexformsSendSenderMail(&$item, &$form, &$data, &$senderMail) {}

	public function onAfterFlexformsSubmit(&$item, &$form, &$data) {}

	public function onBeforeParseOwnerEmailtext(&$item, &$form, &$data){}

	public function onAfterParseOwnerEmailtext(&$item, &$form, &$data, &$text){}

	public function onBeforeParseSenderEmailtext(&$item, &$form, &$data){}

	public function onAfterParseSenderEmailtext(&$item, &$form, &$data, &$text){}
}