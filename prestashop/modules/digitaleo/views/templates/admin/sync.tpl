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
<div class="dgo_image_title"><span>{l s='Customers' mod='digitaleo'}</span></div>
<div class="dgo_buttons_top">
	<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step1" class="btn btn_orange">{l s='New Synchronization' mod='digitaleo'}</a>
	<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segment_step1" class="btn btn_left_10">{l s='New Segment' mod='digitaleo'}</a>
</div>
<div class="dgo_tab">
	<div class="dgo_tab_inner">
    	<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segments">{l s='Segments' mod='digitaleo'}</a>
		<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync" class="active">{l s='Clients Synchronized' mod='digitaleo'}</a>
	</div>
</div>

<div class="dgo_tab_content dgo_tab_content_right">
	<table class="dgo_table">
		<tr>
			<th>{l s='Original Event' mod='digitaleo'}</th>
			<th>{l s='Digitaleo Target' mod='digitaleo'}</th>
			<th>{l s='Nb Contacts' mod='digitaleo'}</th>
			<th>{l s='Sync Mode' mod='digitaleo'}</th>
			<th>{l s='Actions' mod='digitaleo'}</th>
		</tr>
		{if !empty($sync_customers)}
		{foreach from=$sync_customers item=sync}
		<tr>
			<td>{$sync.text_event|escape:'htmlall':'UTF-8'}</td>
			<td>{$sync.target_name|escape:'htmlall':'UTF-8'}</td>
			<td>{$sync.total_contacts|intval}</td>
			<td class="nowrap">
			{if $sync.auto == 1}
				{l s='Auto' mod='digitaleo'}
				{if $sync.active == 1}
					<span class="badge_enabled">{l s='Enabled' mod='digitaleo'}</span>
				{else}
					<span class="badge_disabled">{l s='Disabled' mod='digitaleo'}</span>
				{/if}
			{else}
				{l s='Manual' mod='digitaleo'}
			{/if}
			</td>
			<td class="nowrap">
			<div class="sync_refresh_count" id="sync_refresh_count{$sync.id_sync|intval}"><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/ajax-loader.gif" />&nbsp;<span class="nb_current_sync_{$sync.id_sync|intval}">0</span> / {$sync.total_contacts|intval}</div>

			<div id="sync_refresh_btn{$sync.id_sync|intval}">
			{if $sync.auto == 1}{if $sync.active == 1}<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=active_sync&id={$sync.id_sync|intval}&active=0" class="btn link-dropdown"><i class="material-icons">power_settings_new</i> {l s='Disable' mod='digitaleo'}</a>{else}<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=active_sync&id={$sync.id_sync|intval}&active=1" class="btn link-dropdown"><i class="material-icons">power_settings_new</i> {l s='Enable' mod='digitaleo'}</a>{/if}{else}
				<a href="javascript:;" onclick="launch_sync_list({$sync.id_sync|intval}, {$sync.id_target_digitaleo|intval})" class="btn link-dropdown"><i class="material-icons">refresh</i> {l s='Synchronize' mod='digitaleo'}</a>{/if}<button class="btn btn-dropdown" data-toggle="dropdown" data-target="dropdown{$sync.id_sync|intval}">down</button>
			<ul class="dropdown-menu" id="dropdown{$sync.id_sync|intval}">
				<li><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step1&modif_id_sync={$sync.id_sync|intval}"><i class="material-icons">create</i> {l s='Edit' mod='digitaleo'}</a></li>
				<li><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step1&duplique_id_sync={$sync.id_sync|intval}"><i class="material-icons">content_copy</i> {l s='Duplicate' mod='digitaleo'}</a></li>
				<li><a href="javascript:;" onclick="confirm_delete('{$currentIndex|escape:'htmlall':'UTF-8'}&action=delete_sync&id_sync={$sync.id_sync|intval}')"><i class="material-icons">delete</i> {l s='Delete' mod='digitaleo'}</a></li>
				<li><a href="https://app.digitaleo.com/frontoffice/contact/list-edit/id/{$sync.id_target_digitaleo|intval}" target="_blank"><i class="material-icons">supervisor_account</i> {l s='See target' mod='digitaleo'}</a></li>
			</ul>
			</div>
			</td>
		</tr>
		{/foreach}
		{else}
		<tr>
			<td colspan="6" class="center info">{l s='There is no synchronized customers yet' mod='digitaleo'}</td>
		</tr>
		{/if}
	</table>
	{include file='./pagination.tpl'}
</div>
<div id="left_column">
	 <div class="main">
	 	<p class="header">{l s='You need help' mod='digitaleo'} ?</p>
	 	<p>{l s='Discover the creation of a segments and synchronizations in this video' mod='digitaleo'}</p>
	 	<a href="#video4" class="video_link" ><img src="../modules/digitaleo/views/img/synchroniser-clients.png" /><span></span><i class="material-icons player-icon">&#xE038</i></a>
	 </div>
	 <div class="footer">
	 	<p class="header">{l s='What is a segment' mod='digitaleo'} ?</p>
	 	<p>{l s='A segment will allow you to filter your clients according to criteria you have defined (sex, age, residence ...)' mod='digitaleo'}</p>
	 	<p class="header">{l s='What is a synchronization' mod='digitaleo'} ?</p>
	 	<p>{l s='Synchronization will allow you to automatically import your segments and customers onto Digitaleo' mod='digitaleo'}</p>
	 </div>
	 <div class="clear"></div>
</div>
<div id="videos">
    <div id="video4">
          <iframe width="854" height="480" src="{if $iso_lang == 'es'}http://eo4.me/W55xX{else}http://eo4.me/N7W7Q{/if}" frameborder="0" allowfullscreen></iframe>
    </div>
</div>
{include file='./footer_assistance.tpl'}