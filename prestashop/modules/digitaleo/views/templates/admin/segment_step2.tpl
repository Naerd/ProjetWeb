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
<script>
var texte_delete_segment = "{l s='Are you sure you want to delete this filter ?' mod='digitaleo'}";
</script>
<div class="dgo_steps">
<div class="step_label">2. {l s='Filters Criteria' mod='digitaleo'}</div>
{include file='./steps_dgo.tpl' step_active=2 type_steps="segment"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		{if !empty($errors)}
			<span class="error">{$errors|escape:'htmlall':'UTF-8'}</span>
		{/if}
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segment_step2" method="post">
			<p class="dgo_title_blue">{l s='Please choose the filters for your segment' mod='digitaleo'}</p>
			<p class="marb20">
				{l s='Add a filter' mod='digitaleo'} :
				<select id="segment_select_filter">
					<option value="">-- {l s='Choose' mod='digitaleo'} --</option>
					<option value="countries" {if $segment_countries}disabled="disabled"{/if}>{l s='Countries' mod='digitaleo'}</option>
					<option value="age" {if $segment_age_from || $segment_age_to}disabled="disabled"{/if}>{l s='Age' mod='digitaleo'}</option>
					<option value="registration" {if $segment_registration_from || $segment_registration_to}disabled="disabled"{/if}>{l s='Registration date' mod='digitaleo'}</option>
					<option value="newsletter" {if $segment_newsletter === false || $segment_newsletter == 2}{else}disabled="disabled"{/if}>{l s='Newsletter registered' mod='digitaleo'}</option>
					<option value="groups" {if $segment_groups}disabled="disabled"{/if}>{l s='Customer groups' mod='digitaleo'}</option>
					<option value="orders" {if !$segment_orders_from && !$segment_orders_to}{else}disabled="disabled"{/if}>{l s='Number of Orders' mod='digitaleo'}</option>
					<option value="genre" {if $segment_genre}disabled="disabled"{/if}>{l s='Genre' mod='digitaleo'}</option>
					<option value="optin" {if $segment_optin === false || $segment_optin == 2}{else}disabled="disabled"{/if}>{l s='Optin registererd' mod='digitaleo'}</option>
				</select>
				<a href="javascript:;" class="btn" onclick="choose_filter()">{l s='Add this filter' mod='digitaleo'}</a>
			</p>
			<div class="marb20 segment_filter_choose">{include file='./segments_filters.tpl'}</div>
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segment_step1" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Validate this segmentation' mod='digitaleo'}" name="submitSegmentStep2" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>
<script type="application/javascript">
	$(document).ready(function(){
		if ($(".datepicker").length > 0) {
			$(".datepicker").datepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd'
			});
		}
	});
</script>