CREATE TABLE IF NOT EXISTS `#__flexforms_forms` (
  `flexforms_form_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) unsigned NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `slug` varchar(255) NOT NULL,
  `layout` varchar(255) NOT NULL DEFAULT '',
  `form` varchar(255) NOT NULL DEFAULT '',
  `owners` varchar(255) NOT NULL DEFAULT '',
  `send_owner_mail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `owner_subject` varchar(255) DEFAULT NULL,
  `owner_mail` mediumtext DEFAULT NULL,
  `owner_attachments` tinyint(1) unsigned NOT NULL,
  `send_sender_mail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sender_subject` varchar(255) DEFAULT NULL,
  `sender_mail` mediumtext DEFAULT NULL,
  `sender_field` varchar(255) DEFAULT NULL,
  `sender_attachments` tinyint(1) unsigned NOT NULL,
  `created_on` datetime NOT NULL,
  `created_by` int(11) unsigned NOT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` int(11) unsigned DEFAULT NULL,
  `locked_on` datetime DEFAULT NULL,
  `locked_by` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`flexforms_form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;