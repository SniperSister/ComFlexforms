ALTER TABLE `#__flexforms_forms` ADD `owner_attachments` TINYINT(1)  UNSIGNED  NOT NULL  AFTER `owner_mail`;
ALTER TABLE `#__flexforms_forms` ADD `sender_attachments` TINYINT(1)  UNSIGNED  NOT NULL  AFTER `sender_field`;
