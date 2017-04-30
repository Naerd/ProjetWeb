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
<div class="dgo_image_title"><span>{l s='Campaigns' mod='digitaleo'}</span></div>
<div class="dgo_buttons_top"><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step1" class="btn btn_orange">{l s='New Campaign' mod='digitaleo'}</a></div>
<div class="dgo_tab_content dgo_tab_content_right">
	<table class="dgo_table">
		<tr>
			<th>{l s='Channel' mod='digitaleo'}</th>
			<th>{l s='Name' mod='digitaleo'}</th>
			<th>{l s='Target' mod='digitaleo'}</th>
			<th>{l s='Sending date' mod='digitaleo'}</th>
			<th>{l s='Status' mod='digitaleo'}</th>
			<th>{l s='Actions' mod='digitaleo'}</th>
		</tr>
		{if !empty($campaign_list)}
		{foreach from=$campaign_list item=campaign}
		<tr>
			<td><div class="dgo_channel_{$campaign.channel|escape:'htmlall':'UTF-8'}"></div></td>
			<td>{$campaign.name|escape:'htmlall':'UTF-8'}</td>
			<td>{$campaign.target|escape:'htmlall':'UTF-8'}</td>
			<td>{$campaign.date|escape:'htmlall':'UTF-8'}</td>
			<td><div class="dgo_badge_status dgo_status_{$campaign.status|escape:'htmlall':'UTF-8'}">{$campaign.status_texte|ucfirst|escape:'htmlall':'UTF-8'}</div></td>
			<td class="nowrap">
			<a href="https://app.digitaleo.com/marketing#/statistics/{$campaign.id_campaign_digitaleo|intval}" class="btn link-dropdown" target="_blank"><i class="material-icons">show_chart</i> {l s='View Results' mod='digitaleo'}</a><button class="btn btn-dropdown" data-toggle="dropdown" data-target="dropdown{$campaign.id_campaign|intval}">down</button>
			<ul class="dropdown-menu" id="dropdown{$campaign.id_campaign|intval}">
				{if $campaign.status == "planned"}<li><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step1&modif_id_campaign={$campaign.id_campaign|intval}"><i class="material-icons">create</i> {l s='Edit' mod='digitaleo'}</a></li>{/if}
				<li><a href="javascript:;" onclick="confirm_delete('{$currentIndex|escape:'htmlall':'UTF-8'}&action=cancel_campaign&id_campaign={$campaign.id_campaign_digitaleo|intval}')"><i class="material-icons">delete</i> {l s='Delete' mod='digitaleo'}</a></li>
				
			</ul>
			</td>
		</tr>
		{/foreach}
		{else}
		<tr>
			<td colspan="6" class="center info">{l s='There is no campaigns created yet' mod='digitaleo'}</td>
		</tr>
		{/if}
	</table>
	{include file='./pagination.tpl'}
</div>
<div id="left_column">
	 <div class="main">
	 	<p class="header">{l s='You need help' mod='digitaleo'} ?</p>
	 	<p>{l s='Discover the creation of a new campaign in this video' mod='digitaleo'}</p>
	 	<a href="#video4" class="video_link" ><img src="../modules/digitaleo/views/img/synchroniser-clients.png" /><span></span><i class="material-icons player-icon">&#xE038</i></a>
	 </div>
	 <div class="footer">
	 	<p class="header">{l s='What is a campaign' mod='digitaleo'} ?</p>
	 	<p>{l s='Campaigns, SMS or email, allow you to send your newsletters and manage your commercial operations (destocking, promotions ...)' mod='digitaleo'}</p>
	 </div>
</div>
<div id="videos">
    <div id="video4">
          <iframe width="854" height="480" src="{if $iso_lang == 'es'}http://eo4.me/vjxmc{else}http://eo4.me/1jWSK{/if}" frameborder="0" allowfullscreen></iframe>
    </div>
</div>
{include file='./footer_assistance.tpl'}