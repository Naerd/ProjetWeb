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

class DigitaleoError
{
    public function add($api_call, $parameters, $return, $http_code)
    {
        $sql = "INSERT INTO " . _DB_PREFIX_ . "dgo_error_log (date_error, api_call, parameters, `return`, http_code)
				VALUES (NOW(), '" . pSQL($api_call) . "', '" . pSQL(serialize($parameters)) . "',
				'" . pSQL(serialize($return)) . "', '" . (int)$http_code . "')";
        Db::getInstance()->Execute($sql);
    }
}
