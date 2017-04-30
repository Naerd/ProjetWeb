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
<div class="step_label">1. {l s='Campaign Name' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=1 type_steps="campaign"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step1" method="post">
			<p class="dgo_title_blue">{l s='It will be easier to identify your campaign later' mod='digitaleo'}</p>
			<p class="marb20"><input type="text" name="campaign_name" size="40" value="{$campaign_name|escape:'htmlall':'UTF-8'}" /></p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" name="submitCampaignStep1" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>