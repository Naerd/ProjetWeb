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
{foreach from=$templates item=tpl}
<a href="javascript:;" onclick="selectTemplate({$tpl.id|intval})" class="dgo_tpl {if isset($notification_id_template) && $notification_id_template == $tpl.id}active{/if}" id="dgo_link_tpl{$tpl.id|intval}">
	<span class="tpl_name">{$tpl.name|escape:'htmlall':'UTF-8'}</span>
	<img src="{$tpl.url_thumbnails.340x440|escape:'htmlall':'UTF-8'}" />
</a>
{/foreach}