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
<div class="step_label">1. {l s='Original Prestashop Event' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=1 type_steps="sync"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step2" method="post">
			<p class="dgo_title_blue">{l s='Choose a Prestashop event or a Segment' mod='digitaleo'}</p>
			<p class="marb20">
				<select name="sync_event">
					<optgroup label="{l s='Prestashop Hooks' mod='digitaleo'}">
						{foreach from=$sync_events key=value item=event}
							<option value="{$value|escape:'htmlall':'UTF-8'}" {if $sync_event == $value}selected{/if}>{$event|escape:'htmlall':'UTF-8'}</option>
							{if $value=="hook_newsletter"}
							</optgroup>
							<optgroup label="{l s='Segments' mod='digitaleo'}">
							{/if}
						{/foreach}
					</optgroup>
				</select>
			</p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>