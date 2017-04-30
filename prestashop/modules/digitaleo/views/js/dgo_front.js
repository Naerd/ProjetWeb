/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to a commercial license from DIGITALEO SAS
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the DIGITALEO SAS is strictly forbidden.
 *
 *  @author		Digitaleo
 *  @copyright 	2016 Digitaleo
 *  @license 	All Rights Reserved
 */
$(document).ready(function() {
    $.ajax({
        type: "GET",
        url: baseDir + "modules/digitaleo/ajax.php?token_dgo="+token_dgo,
        async: true,
        cache: false
    });
});