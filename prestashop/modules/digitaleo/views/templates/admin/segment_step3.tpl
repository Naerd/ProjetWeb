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
{if isset($type_segment_action) && $type_segment_action == "modif"}
3. {l s='Confirm Segmentation' mod='digitaleo'}
{else}
3. {l s='Confirm new Segmentation' mod='digitaleo'}
{/if}
</div>
{include file='./steps_dgo.tpl' step_active=3 type_steps="segment"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action={if isset($type_segment_action) && $type_segment_action == "modif"}segment_edit{else}segment_create{/if}" method="post">
			{if isset($type_segment_action) && $type_segment_action == "modif"}
			<p class="dgo_title_blue">{l s='Please review your contact segmentation' mod='digitaleo'}</p>
			{else}
			<p class="dgo_title_blue">{l s='Please review your new contact segmentation' mod='digitaleo'}</p>
			{/if}
			<p class="marb20">
				<p class="dgo_review">
					<label>{l s='Segment name' mod='digitaleo'} :</label>
					<span>{$segment_name|escape:'htmlall':'UTF-8'}</span>
				</p>
				{if !empty($segment_countries)}
				<p class="dgo_review">
					<label>{l s='Countries' mod='digitaleo'} :</label>
					<span>{$segment_countries|escape:'htmlall':'UTF-8'}</span>
				</p>
				{/if}
				{if !empty($segment_age_from) || !empty($segment_age_to)}
				<p class="dgo_review">
					<label>{l s='Age' mod='digitaleo'} :</label>
					<span>
					{if !empty($segment_age_from)}{l s='From2' mod='digitaleo'} {$segment_age_from|intval} {l s='years old' mod='digitaleo'}{/if}
					{if !empty($segment_age_to)}{l s='To2' mod='digitaleo'} {$segment_age_to|intval} {l s='years old' mod='digitaleo'}{/if}
					</span>
				</p>
				{/if}
				{if (!empty($segment_registration_from) && $segment_registration_from != '0000-00-00') || (!empty($segment_registration_to) && $segment_registration_to != '0000-00-00')}
				<p class="dgo_review">
					<label>{l s='Registration date' mod='digitaleo'} :</label>
					<span>
					{if !empty($segment_registration_from)}{l s='From' mod='digitaleo'} {$segment_registration_from|escape:'htmlall':'UTF-8'}{/if}
					{if !empty($segment_registration_to)}{l s='To' mod='digitaleo'} {$segment_registration_to|escape:'htmlall':'UTF-8'}{/if}
					</span>
				</p>
				{/if}
				{if isset($segment_newsletter)}
				<p class="dgo_review">
					<label>{l s='Newsletter registered' mod='digitaleo'} :</label>
					<span>{$segment_newsletter|escape:'htmlall':'UTF-8'}</span>
				</p>
				{/if}
				{if !empty($segment_groups)}
				<p class="dgo_review">
					<label>{l s='Customer groups' mod='digitaleo'} :</label>
					<span>{$segment_groups|escape:'htmlall':'UTF-8'}</span>
				</p>
				{/if}
				{if $segment_orders_from !== false || $segment_orders_to !== false }
				<p class="dgo_review">
					<label>{l s='Number of Orders' mod='digitaleo'} :</label>
					<span>
					{if $segment_orders_from !== false}{l s='From2' mod='digitaleo'} {$segment_orders_from|intval} {l s='orders' mod='digitaleo'}{/if}
					{if $segment_orders_to !== false}{l s='To2' mod='digitaleo'} {$segment_orders_to|intval} {l s='orders' mod='digitaleo'}{/if}</span>
				</p>
				{/if}
				{if !empty($segment_genre)}
				<p class="dgo_review">
					<label>{l s='Genre' mod='digitaleo'} :</label>
					<span>{$segment_genre|escape:'htmlall':'UTF-8'}</span>
				</p>
				{/if}
				{if isset($segment_optin)}
				<p class="dgo_review">
					<label>{l s='Optin registered' mod='digitaleo'} :</label>
					<span>{$segment_optin|escape:'htmlall':'UTF-8'}</span>
				</p>
				{/if}
			</p>
			<p class="marb20 dgo_big_text">
			{l s='Number of Contacts' mod='digitaleo'} : <strong>{$segment_contacts_number|intval}</strong>
			</p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segment_step2" class="btn">{l s='Back' mod='digitaleo'}</a>
				{if isset($type_segment_action) && $type_segment_action == "modif"}
				<input type="submit" name="edit_segment" class="btn btn_orange marl20" value="{l s='Edit this segment' mod='digitaleo'}" />
				{else}
				<input type="submit" name="create_segment" class="btn btn_orange marl20" value="{l s='Create this segment' mod='digitaleo'}" />
				{/if}
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>