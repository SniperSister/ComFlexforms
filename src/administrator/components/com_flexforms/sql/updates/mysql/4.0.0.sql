ALTER TABLE `#__flexforms_forms` ADD `owner_mail_type` TINYINT(1)  UNSIGNED  NOT NULL DEFAULT '0' AFTER `send_owner_mail` /** CAN FAIL **/;
ALTER TABLE `#__flexforms_forms` ADD `owner_mail_template` varchar(127)  UNSIGNED  NULL AFTER `owner_mail_type` /** CAN FAIL **/;
ALTER TABLE `#__flexforms_forms` ADD `sender_mail_type` TINYINT(1)  UNSIGNED  NOT NULL DEFAULT '0' AFTER `send_sender_mail` /** CAN FAIL **/;
ALTER TABLE `#__flexforms_forms` ADD `sender_mail_template` varchar(127)  UNSIGNED  NULL AFTER `sender_mail_type` /** CAN FAIL **/;