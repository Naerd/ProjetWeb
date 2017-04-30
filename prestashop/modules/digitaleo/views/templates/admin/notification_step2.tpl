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
<div class="step_label">2. {l s='Choose the Prestashop Event' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=2 type_steps="notification"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step3" method="post">
			<p class="dgo_title_blue">{l s='Choose the event you want to trigger the sending of E-mail or SMS' mod='digitaleo'}</p>
			<p class="marb20">
				<select name="notification_event">
					{foreach from=$notification_events key=value item=event}
						<option value="{$value|escape:'htmlall':'UTF-8'}" {if $notification_event == $value}selected{/if}>{$event|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			</p>
			<p{if $notification_event != 'hook_cart_abandonment'} style="display: none;"{/if} class="delay_cart_abandonment">{l s='Cart abandoned after' mod='digitaleo'} : <input type="text" name="delay_cart_abandonment" value="{$delay_cart_abandonment|intval}"> {l s='Hours' mod='digitaleo'}</p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notification_step1" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>