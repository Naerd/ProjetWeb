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

require(dirname(__FILE__).'/../../config/config.inc.php');

$token_dgo = Tools::getToken(
    '/modules/digitaleo/views/js/dgo_front.js'
);

if (!Tools::getIsset('token_dgo') || Tools::getValue('token_dgo') != $token_dgo) {
    die("Forbidden");
}

require(dirname(__FILE__).'/digitaleo.php');

$digitaleo = new digitaleo();
$digitaleo->execNotificationCartAbandonment();
