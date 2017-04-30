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
<div class="dgo_steps">
<div class="step_label">1. {l s='Choose the recipient' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=1 type_steps="notification"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step1" method="post">
			<p class="dgo_title_blue">{l s='It can be either the customer or the shop administrator' mod='digitaleo'}</p>
			<p class="marb20">
				<div class="dgo_btn_mode dgo_btn_mode_admin {if isset($notification_type) && $notification_type == 'admin'}active{/if}" data-auto="admin" data-type="notification_type">
					{l s='Shop Administrator' mod='digitaleo'}
				</div>
				<div class="dgo_btn_mode dgo_btn_mode_customer {if isset($notification_type) && $notification_type == 'customer'}active{/if}" data-auto="customer" data-type="notification_type">
					{l s='Shop Customer' mod='digitaleo'}
				</div>
				<input type="hidden" name="notification_type" id="notification_type" value="{if isset($notification_type)}{$notification_type|escape:'htmlall':'UTF-8'}{/if}" />
			</p>
			<div {if !isset($notification_type) || empty($notification_type) || $notification_type == "customer"}style="display:none"{/if} class="dgo_display_admin_contacts">
				<p>{l s='Administrator E-mail' mod='digitaleo'} : <input type="text" size="40" name="administrator_email" value="{$administrator_email|escape:'htmlall':'UTF-8'}" /></p>
				<p class="marb20">{l s='Administrator Mobile Phone Number' mod='digitaleo'} : <input type="text" size="20" name="administrator_sms" value="{$administrator_sms|escape:'htmlall':'UTF-8'}" placeholder="{l s='+33...' mod='digitaleo'}" /></p>
			</div>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notifications" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" name="submitNotificationStep1" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>