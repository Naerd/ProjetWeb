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
<div class="step_label">4. {l s='Confirm Synchronization' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=4 type_steps="sync"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step4&gosync" method="post">
			<p class="dgo_title_blue">{l s='Review the parameters of your new Synchronization' mod='digitaleo'}</p>
			<p class="marb20">
				<p class="dgo_review">
					<label>{l s='Original Prestashop Event' mod='digitaleo'} :</label>
					<span>{$sync_event_text|escape:'htmlall':'UTF-8'}</span><br>
					<label>{l s='Number of Contacts' mod='digitaleo'} :</label>
					<span>{$nb_contacts|intval}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Digitaleo Target' mod='digitaleo'} :</label>
					<span>{$sync_list_text|escape:'htmlall':'UTF-8'}</span>
				</p>
				<p class="dgo_review">
					<label>{l s='Sync Mode' mod='digitaleo'} :</label>
					<span>{$sync_auto_text|escape:'htmlall':'UTF-8'}</span>
				</p>
				<input type="hidden" name="sync_event" value="{$sync_event|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sync_id_list" value="{$sync_id_list|intval}" />
				<input type="hidden" name="sync_new_list" value="{$sync_new_list|escape:'htmlall':'UTF-8'}" />
				<input type="hidden" name="sync_auto" id="sync_auto" value="{$sync_auto|intval}" />
			</p>
			{if isset($gosync) && gosync == true}
			<p class="marb20">
			<span class="error" style="display: none;"></span><br><br>
			<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/ajax-loader.gif" class="dgo_img_loader" /> {l s='First Synchronization... please wait...' mod='digitaleo'}<br/>
			<div class="dgo_progressbar">
				<div class="dgo_progress"></div>
			</div>
			<br/>
			<span class="nb_current_sync_{$id_sync|intval}">0</span> / <span class="total_sync">{$total_sync|intval}</span> {l s='Contacts' mod='digitaleo'}
			</p>
			<script>
			launch_sync({$id_sync|intval});
			</script>
			<p class="btn_end_sync" style="display:none">
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync" class="btn btn_orange">{l s='Go Back to the list' mod='digitaleo'}</a>
			</p>
			{else}
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step3" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Let\'s start !' mod='digitaleo'}" />
			</p>
			{/if}
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>