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
<p class="dgo_title_blue">{l s='Choose a template below or create a new one in your Digitaleo App' mod='digitaleo'}</p>
<div class="marb20 templates_digitaleo">
{include file='./template_list.tpl'}
<div class="dgo_tpl dgo_tpl_more">
	<span class="tpl_name">{l s='See all our models on Digitaleo' mod='digitaleo'}</span>
	<a href="https://app.digitaleo.com/" class="btn btn_orange" target="_blank">{l s='Create my email' mod='digitaleo'}</a><br/><br/>
	<a href="http://eo4.me/QLr9f" target="_blank" class="btn">{l s='Need Help' mod='digitaleo'} ?</a>
</div>
</div>
<div class="marb20">
	<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/ajax-loader.gif" class="loader_templates" style="display:none">
	<a href="javascript:;" onclick="more_templates()" class="btn btn_blue btn_big btn_more_templates">{l s='Display more E-mail Templates' mod='digitaleo'}</a>
</div>