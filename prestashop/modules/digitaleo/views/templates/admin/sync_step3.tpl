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
<div class="step_label">3. {l s='Sync Mode' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=3 type_steps="sync"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step3" method="post">
			<p class="dgo_title_blue">{l s='Choose the sync mode between Prestashop Customers and your Digitaleo Target' mod='digitaleo'}</p>
			<p class="marb20">
				<div class="sync_mode_container">
					<div class="sync_mode_choice">
						<div><input type="radio" name="sync_auto" value="1" id="sync_auto_1" {if isset($sync_auto) && $sync_auto == 1}checked{/if} /> <label for="sync_auto_1">{l s='Automatic Synchronization' mod='digitaleo'}</label></div>
						<div class="sync_mode_desc">{l s='Every new Prestashop Customer will be automatically added in your Digitaleo target.' mod='digitaleo'}</div>
					</div>
					<div class="sync_mode_choice">
						<div><input type="radio" name="sync_auto" value="0" id="sync_auto_0" {if isset($sync_auto) && $sync_auto == 0}checked{/if} /> <label for="sync_auto_0">{l s='Manual Synchronization' mod='digitaleo'}</label></div>
						<div class="sync_mode_desc">{l s='New Customers will only be added to your Digitaleo Target after launching a synchronization manually' mod='digitaleo'}</div>
					</div>
					<div class="clear"></div>
				</div>

				<input type="hidden" name="sync_event" value="{$sync_event|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sync_id_list" value="{$sync_id_list|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sync_new_list" value="{$sync_new_list|escape:'htmlall':'UTF-8'}" />
			</p>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step2" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" name="submitSyncStep3" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>