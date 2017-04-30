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
<div class="step_label">6. {l s='Confirm your notification' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=6 type_steps="notification"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step6" method="post">
			<p class="dgo_title_blue">{l s='Review the parameters before creating your notification' mod='digitaleo'}</p>
			<p class="marb20">
				<p class="dgo_review">
					<label>{l s='Notification type' mod='digitaleo'} :</label>
					<span>{$notification_type|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Prestashop Event' mod='digitaleo'} :</label>
					<span>{$notification_event|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Channel' mod='digitaleo'} :</label>
					<span>{$notification_channel_text|escape:'htmlall':'UTF-8'}</span>
				</p>
				{if $notification_channel == "mail"}
				<p class="dgo_review">
					<label>{l s='Sender' mod='digitaleo'} :</label>
					<span>{$notification_sender|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Reply To' mod='digitaleo'} :</label>
					<span>{$notification_replyto|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='E-mail Subject' mod='digitaleo'} :</label>
					<span>{$notification_subject|escape:'htmlall':'UTF-8'}</span>
				</p>
				{/if}
			</p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step5" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Create notification' mod='digitaleo'}" name="submitNotificationStep6" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>