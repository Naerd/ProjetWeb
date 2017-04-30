<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from DIGITALEO SAS
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the DIGITALEO SAS is strictly forbidden.
 *
 *  @author	    Digitaleo
 *  @copyright 2016 Digitaleo
 *  @license   All Rights Reserved
 */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dgo_sync_customers` (
	`id_sync` INT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
	`hook_prestashop` VARCHAR(64) NOT NULL,
	`id_segment` INT(4) UNSIGNED NOT NULL,
	`id_target_digitaleo` INT(10) UNSIGNED NOT NULL,
	`auto` TINYINT(1) UNSIGNED NOT NULL,
	`active` TINYINT(1) UNSIGNED NOT NULL,
	PRIMARY KEY (`id_sync`),
	INDEX `id_segment` (`id_segment`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dgo_segments` (
	`id_segment` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NULL DEFAULT NULL,
	`countries` TEXT NULL,
	`age_from` INT(3) NULL DEFAULT NULL,
	`age_to` INT(3) NULL DEFAULT NULL,
	`registration_from` DATE NULL DEFAULT NULL,
	`registration_to` DATE NULL DEFAULT NULL,
	`newsletter` INT(1) NULL DEFAULT NULL,
	`groups` TEXT NULL,
	`orders_from` INT(8) NULL DEFAULT NULL,
	`orders_to` INT(8) NULL DEFAULT NULL,
	`genre` ENUM(\'H\',\'F\') NULL DEFAULT NULL,
	`optin` INT(1) NULL DEFAULT NULL,
	PRIMARY KEY (`id_segment`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dgo_customers_contacts` (
	`id_customer` INT(10) UNSIGNED NOT NULL,
	`id_contact_digitaleo` INT(10) UNSIGNED NOT NULL,
	`date_sync` DATETIME NOT NULL,
	PRIMARY KEY (`id_customer`, `id_contact_digitaleo`)
    ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dgo_campaigns` (
	`id_campaign` INT(8) NOT NULL AUTO_INCREMENT,
	`id_campaign_digitaleo` INT(10) NULL DEFAULT NULL,
	`name` VARCHAR(255) NULL DEFAULT NULL,
	`id_list_digitaleo` INT(10) NULL DEFAULT NULL,
	`content` MEDIUMTEXT NULL,
	`sender` VARCHAR(255) NULL DEFAULT NULL,
	`replyto` VARCHAR(255) NULL DEFAULT NULL,
	`subject` VARCHAR(255) NULL DEFAULT NULL,
	`id_template` INT(10) NULL DEFAULT NULL,
	`channel` VARCHAR(8) NULL DEFAULT NULL,
	`date_send` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id_campaign`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dgo_error_log` (
	`id_error` INT(10) NOT NULL AUTO_INCREMENT,
	`date_error` DATETIME NULL DEFAULT NULL,
	`api_call` VARCHAR(255) NULL DEFAULT NULL,
	`parameters` MEDIUMTEXT NULL,
	`return` MEDIUMTEXT NULL,
	`http_code` INT(3) NULL DEFAULT NULL,
	PRIMARY KEY (`id_error`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dgo_notifications` (
	`id_notification` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_campaign_digitaleo` INT(10) UNSIGNED NULL DEFAULT NULL,
	`recipient_type` VARCHAR(16) NULL DEFAULT NULL,
	`prestashop_event` VARCHAR(32) NULL DEFAULT NULL,
	`delay_cart_abandonment` INT(10) UNSIGNED NULL DEFAULT NULL,
	`content` MEDIUMTEXT NULL,
	`sender` VARCHAR(255) NULL DEFAULT NULL,
	`replyto` VARCHAR(255) NULL DEFAULT NULL,
	`subject` VARCHAR(255) NULL DEFAULT NULL,
	`id_template` INT(10) UNSIGNED NULL DEFAULT NULL,
	`channel` VARCHAR(8) NULL DEFAULT NULL,
	`administrator_email` VARCHAR(255) NULL DEFAULT NULL,
	`administrator_sms` VARCHAR(16) NULL DEFAULT NULL,
	PRIMARY KEY (`id_notification`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'dgo_cart_abandonment` (
	`id_cart` INT(10) UNSIGNED NOT NULL,
	`date_add` DATETIME NULL DEFAULT NULL,
	PRIMARY KEY (`id_cart`)
	) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
