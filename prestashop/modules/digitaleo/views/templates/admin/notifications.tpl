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
<div class="dgo_image_title"><span>{l s='Notifications' mod='digitaleo'}</span></div>
<div class="dgo_buttons_top"><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step1" class="btn btn_orange">{l s='New Notification' mod='digitaleo'}</a></div>
<div class="dgo_tab_content dgo_tab_content_right">
	<table class="dgo_table">
		<tr>
			<th>{l s='Channel' mod='digitaleo'}</th>
			<th>{l s='Prestashop Event' mod='digitaleo'}</th>
			<th>{l s='Recipient' mod='digitaleo'}</th>
			<th>{l s='Contact' mod='digitaleo'}</th>
			<th>{l s='Sender' mod='digitaleo'}</th>
			<th>{l s='Actions' mod='digitaleo'}</th>
		</tr>
		{if !empty($notification_list)}
			{foreach from=$notification_list item=notification}
			<tr>
				<td><div class="dgo_channel_{$notification.channel|escape:'htmlall':'UTF-8'}"></div></td>
				<td>{$notification.prestashop_event|escape:'htmlall':'UTF-8'}</td>
				<td>{$notification.recipient_type|escape:'htmlall':'UTF-8'}</td>
				<td>{$notification.contact|escape:'htmlall':'UTF-8'}</td>
				<td>{if $notification.sender}{$notification.sender|escape:'htmlall':'UTF-8'}<br>{$notification.replyto|escape:'htmlall':'UTF-8'}{/if}</td>
				<td class="nowrap">
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step1&modif_id_notification={$notification.id_notification|intval}" class="btn link-dropdown"><i class="material-icons">create</i> {l s='Edit' mod='digitaleo'}</a><button class="btn btn-dropdown" data-toggle="dropdown" data-target="dropdown{$notification.id_notification|intval}">down</button>
				<ul class="dropdown-menu" id="dropdown{$notification.id_notification|intval}">
					<li><a href="javascript:;" onclick="confirm_delete('{$currentIndex|escape:'htmlall':'UTF-8'}&action=notifications&delete_notification&id_notification={$notification.id_notification|intval}')"><i class="material-icons">delete</i> {l s='Delete' mod='digitaleo'}</a></li>
					
				</ul>
				</td>
			</tr>
			{/foreach}
		{else}
		<tr>
			<td colspan="6" class="center info">{l s='There is no notifications created yet' mod='digitaleo'}</td>
		</tr>
		{/if}
	</table>
	{include file='./pagination.tpl'}
</div>
<div id="left_column">
	 <div class="main">
	 	<p class="header">{l s='You need help' mod='digitaleo'}  ?</p>
	 	<p>{l s='Learn how to create a new notification with this video' mod='digitaleo'}</p>
	 	<a href="#video4" class="video_link" ><img src="../modules/digitaleo/views/img/synchroniser-clients.png" /><span></span><i class="material-icons player-icon">&#xE038</i></a>
	 </div>
	 <div class="footer">
	 	<p class="header">{l s='What is a notification' mod='digitaleo'} ?</p>
	 	<p>{l s='A notification allows you to send or receive an email or SMS during an event (Registration, purchase, cart abandonment ...)' mod='digitaleo'}</p>
	 </div>
</div>
<div id="videos">
    <div id="video4">
          <iframe width="854" height="480" src="{if $iso_lang == 'es'}http://eo4.me/XPKPF{else}http://eo4.me/SYzAz{/if}" frameborder="0" allowfullscreen></iframe>
    </div>
</div>
{include file='./footer_assistance.tpl'}