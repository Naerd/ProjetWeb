{*
* NOTICE OF LICENSE
*
* This source file is subject to a commercial license from DIGITALEO SAS
* Use, copy, modification or distribution of this source file without written
* license agreement from the DIGITALEO SAS is strictly forbidden.
*
*  @author		Digitaleo
*  @copyright 	2016 Digitaleo
*  @license 	All Rights Reserved
*}
<script>
var l_confirm_delete = "{l s='Are you sure you want to delete this element ?' mod='digitaleo'}";
</script>
<div class="digitaleo">
    {include file="./header.tpl"}
    {if !$action || empty($tpl_file)}
        {include file="./home.tpl"}
    {else}
        <div class="dgo_content">
        {include file=$tpl_file}
        </div>
    {/if}
</div>