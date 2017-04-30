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
<div class="step_label">6. {l s='Parameters of sending' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=6 type_steps="campaign"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step7" method="post">
				<p class="dgo_title_blue">{l s='You can choose a date for sending or left empty and send now' mod='digitaleo'}</p>
				<p class="marb20">{l s='Sending date :' mod='digitaleo'} <input type="text" size="40" name="campaign_date" value="{if isset($campaign_date) && !empty($campaign_date)}{$campaign_date|escape:'htmlall':'UTF-8'}{else}{l s='Send now / click to choose another date' mod='digitaleo'}{/if}" class="datetimepicker" /></p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step5" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>