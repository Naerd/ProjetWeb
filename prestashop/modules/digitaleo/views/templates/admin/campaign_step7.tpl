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
<div class="step_label">7. {l s='Confirm your campaign' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=7 type_steps="campaign"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step7" method="post">
			<p class="dgo_title_blue">{l s='Review the parameters before creating your campaign' mod='digitaleo'}</p>
			<p class="marb20">
				<p class="dgo_review">
					<label>{l s='Campaign name' mod='digitaleo'} :</label>
					<span>{$campaign_name|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Target' mod='digitaleo'} :</label>
					<span>{$campaign_target|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Channel' mod='digitaleo'} :</label>
					<span>{$campaign_channel_text|escape:'htmlall':'UTF-8'}</span>
				</p>
				{if $campaign_channel == "mail"}
				<p class="dgo_review">
					<label>{l s='Sender' mod='digitaleo'} :</label>
					<span>{$campaign_sender|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Reply To' mod='digitaleo'} :</label>
					<span>{$campaign_replyto|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='E-mail Subject' mod='digitaleo'} :</label>
					<span>{$campaign_subject|escape:'htmlall':'UTF-8'}</span>
				</p>
				{/if}
				<p class="dgo_review">
					<label>{l s='Sending date' mod='digitaleo'} :</label>
					<span>{$campaign_date|escape:'htmlall':'UTF-8'}</span>
				</p>
			</p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step6" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Create campaign' mod='digitaleo'}" name="submitCampaignStep7" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>