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
<div class="step_label">3. {l s='Choose the communication channel' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=3 type_steps="notification"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step3" method="post">
			<p class="dgo_title_blue">{l s='This is the communication channel used for sending the messages' mod='digitaleo'}
			<p class="marb20">
				<div class="dgo_btn_mode dgo_btn_mode_mail {if isset($notification_channel) && $notification_channel == 'mail'}active{/if}" data-auto="mail" data-type="notification_channel">
					<div class="dgo_btn_mode_desc">{l s='Send a personalized and interactive message: photos, social networks, access map, links to articles...' mod='digitaleo'}</div>
					<span>{l s='E-mail' mod='digitaleo'}</span>
				</div>
				<div class="dgo_btn_mode dgo_btn_mode_sms {if isset($notification_channel) && $notification_channel == 'sms'}active{/if}" data-auto="sms" data-type="notification_channel">
					<div class="dgo_btn_mode_desc">{l s='Personalize a short message that will be read on average by 98% of your list a few minutes after reception' mod='digitaleo'}</div>
					<span>{l s='SMS' mod='digitaleo'}</span>
				</div>
				<input type="hidden" name="notification_channel" id="notification_channel" value="{$notification_channel|escape:'htmlall':'UTF-8'}" />
			</p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step2" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" name="submitNotificationStep3" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>