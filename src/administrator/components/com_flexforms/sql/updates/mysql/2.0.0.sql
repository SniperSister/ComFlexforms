ALTER TABLE `#__flexforms_forms` CHANGE `flexforms_form_id` `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__flexforms_forms` ADD `redirecturl` VARCHAR(255)  NULL  AFTER `form`;
