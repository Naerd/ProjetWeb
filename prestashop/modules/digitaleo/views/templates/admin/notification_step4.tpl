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
<div class="step_label">
{if $notification_channel == "sms"}
4. {l s='Write your message' mod='digitaleo'}
{else}
4. {l s='Sender of the E-mail and Template' mod='digitaleo'}
{/if}
</div>
{include file='./steps_dgo.tpl' step_active=4 type_steps="notification"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step4" method="post">
			{if $notification_channel == "sms"}
				<p class="dgo_title_blue">{l s='Use the assistant to write your SMS message' mod='digitaleo'}</p>
				<div class="marb20">
					<div class="dgo_sms_write">
						<div class="dgo_select_fields">
							<select class="dgo_sms_select_field">
								<option value="">-- {l s='Add a customized field' mod='digitaleo'} --</option>
								<option value="#civility#">{l s='Contact Civility' mod='digitaleo'}</option>
								<option value="#firstName#">{l s='Contact Firstname' mod='digitaleo'}</option>
								<option value="#lastName#">{l s='Contact Lastname' mod='digitaleo'}</option>
							</select>
						</div>
						<textarea name="notification_sms_content" class="sms_content" rows="4" cols="60">{$notification_sms_content|escape:'htmlall':'UTF-8'}</textarea><br/>
						{l s='Number of characters used' mod='digitaleo'} : <span class="sms_nb_chars">0</span> - <span class="sms_nb_sms">0</span> SMS
					</div>
					<div class="dgo_sms_preview">
						<div class="sms-preview-content">
							<div class="sms has-text">
								<div class="sms_content"></div>
								<div class="sms-arrow-wrapper">
									<div class="sms-arrow"></div>
								</div>
							</div>
						</div>
					</div>
					<div class="clear"></div>
				</div>
			{else}
				<p class="dgo_title_blue">{l s='Fill the information about Sender and Subject of the E-mail' mod='digitaleo'}</p>
				<p>{l s='Sender' mod='digitaleo'} : <input type="text" size="40" name="notification_sender" value="{$notification_sender|escape:'htmlall':'UTF-8'}" />&nbsp;&nbsp;&nbsp;&nbsp;{l s='Reply To' mod='digitaleo'} : <input type="text" size="40" name="notification_replyto" value="{$notification_replyto|escape:'htmlall':'UTF-8'}" /></p>
				<p class="marb20">{l s='E-mail Subject' mod='digitaleo'} : <input type="text" size="90" name="notification_subject" value="{$notification_subject|escape:'htmlall':'UTF-8'}" /></p>
				{include file='./template_chooser.tpl'}

			{/if}
			<p>
				<input type="hidden" name="notification_id_template" id="notification_id_template" value="{$notification_id_template|intval}" />
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step3" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" name="submitNotificationStep4" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>