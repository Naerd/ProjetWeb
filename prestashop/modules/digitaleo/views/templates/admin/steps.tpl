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
<div class="dgo_steps {$type_steps|escape:'htmlall':'UTF-8'}">
	<div class="step_label">
	{if $type_steps == "sync"}
		{if isset($type_sync_action) && $type_sync_action == "modif"}
			{l s='Sync Modification' mod='digitaleo'}
		{elseif isset($type_sync_action) && $type_sync_action == "duplique"}
			{l s='Sync Duplication' mod='digitaleo'}
		{else}
			{l s='New Synchronization' mod='digitaleo'}
		{/if}
	{elseif $type_steps == "segment"}
		{if isset($type_segment_action) && $type_segment_action == "modif"}
			{l s='Segment Modification' mod='digitaleo'}
		{elseif isset($type_segment_action) && $type_segment_action == "duplique"}
			{l s='Segment Duplication' mod='digitaleo'}
		{else}
			{l s='New Segment' mod='digitaleo'}
		{/if}
	{elseif $type_steps == "campaign"}
		{if isset($type_campaign_action) && $type_campaign_action == "modif"}
			{l s='Campaign Modification' mod='digitaleo'}
		{elseif isset($type_campaign_action) && $type_campaign_action == "duplique"}
			{l s='Campaign Duplication' mod='digitaleo'}
		{else}
			{l s='New Campaign' mod='digitaleo'}
		{/if}
	{elseif $type_steps == "notification"}
		{if isset($type_notification_action) && $type_notification_action == "modif"}
			{l s='Notification Modification' mod='digitaleo'}
		{elseif isset($type_notification_action) && $type_notification_action == "duplique"}
			{l s='Notification Duplication' mod='digitaleo'}
		{else}
			{l s='New Notification' mod='digitaleo'}
		{/if}
	{/if}
	</div>
	<div class="step step_first step_active">
		<span class="step_title">{l s='Step' mod='digitaleo'} 1</span>
		<span class="step_desc">
		{if $type_steps == "sync"}{l s='Original Prestashop Event' mod='digitaleo'}{/if}
		{if $type_steps == "segment"}{l s='Segment name' mod='digitaleo'}{/if}
		{if $type_steps == "campaign"}{l s='Campaign name' mod='digitaleo'}{/if}
		{if $type_steps == "notification"}{l s='Recipient' mod='digitaleo'}{/if}
		</span>
	</div>
	<div class="step_arrow1 arrow1_active"></div>
	<div class="step_arrow2 {if $step_active >= 2}arrow2_active{/if}"></div>
	<div class="step {if $step_active >= 2}step_active{/if}">
		<span class="step_title">{l s='Step' mod='digitaleo'} 2</span>
		<span class="step_desc">
		{if $type_steps == "sync"}{l s='Digitaleo Target' mod='digitaleo'}{/if}
		{if $type_steps == "segment"}{l s='Filters Criteria' mod='digitaleo'}{/if}
		{if $type_steps == "campaign"}{l s='Target' mod='digitaleo'}{/if}
		{if $type_steps == "notification"}{l s='Prestashop event' mod='digitaleo'}{/if}
		</span>
	</div>
	<div class="step_arrow1 {if $step_active >= 2}arrow1_active{/if}"></div>
	<div class="step_arrow2 {if $step_active >= 3}arrow2_active{/if}"></div>
	<div class="step {if $step_active >= 3}step_active{/if}">
		<span class="step_title">{l s='Step' mod='digitaleo'} 3</span>
		<span class="step_desc">
		{if $type_steps == "sync"}{l s='Sync Mode' mod='digitaleo'}{/if}
		{if $type_steps == "segment"}
			{if isset($type_segment_action) && $type_segment_action == "modif"}
			{l s='Confirm Segmentation' mod='digitaleo'}
			{else}
			{l s='Confirm new Segmentation' mod='digitaleo'}
			{/if}
		{/if}
		{if $type_steps == "campaign"}{l s='Channel' mod='digitaleo'}{/if}
		{if $type_steps == "notification"}{l s='Channel' mod='digitaleo'}{/if}
		</span>
	</div>
	<div class="step_arrow1 {if $step_active >= 3}arrow1_active{/if}"></div>

	{if $type_steps != "segment"}
	<div class="step_arrow2 {if $step_active >= 4}arrow2_active{/if}"></div>
	<div class="step step_last {if $step_active >= 4}step_active{/if}">
		<span class="step_title">{l s='Step' mod='digitaleo'} 4</span>
		<span class="step_desc">
		{if $type_steps == "sync"}{l s='Confirm Synchronization' mod='digitaleo'}{/if}
		{if $type_steps == "campaign"}{l s='Content' mod='digitaleo'}{/if}
		{if $type_steps == "notification"}{l s='Content' mod='digitaleo'}{/if}
		</span>
	</div>
	<div class="step_arrow1 {if $step_active >= 4}arrow1_active{/if}"></div>
	{/if}

	{if $type_steps != "segment" && $type_steps != "sync"}
	<div class="step_arrow2 {if $step_active >= 5}arrow2_active{/if}"></div>
	<div class="step step_last {if $step_active >= 5}step_active{/if}">
		<span class="step_title">{l s='Step' mod='digitaleo'} 5</span>
		<span class="step_desc">
		{if $type_steps == "campaign"}{l s='Test' mod='digitaleo'}{/if}
		{if $type_steps == "notification"}{l s='Test' mod='digitaleo'}{/if}
		</span>
	</div>
	<div class="step_arrow1 {if $step_active >= 5}arrow1_active{/if}"></div>

	<div class="step_arrow2 {if $step_active >= 6}arrow2_active{/if}"></div>
	<div class="step step_last {if $step_active >= 6}step_active{/if}">
		<span class="step_title">{l s='Step' mod='digitaleo'} 6</span>
		<span class="step_desc">
		{if $type_steps == "campaign"}{l s='Parameters' mod='digitaleo'}{/if}
		{if $type_steps == "notification"}{l s='Confirm' mod='digitaleo'}{/if}
		</span>
	</div>
	<div class="step_arrow1 {if $step_active >= 6}arrow1_active{/if}"></div>

	{if $type_steps == "campaign"}
	<div class="step_arrow2 {if $step_active >= 7}arrow2_active{/if}"></div>
	<div class="step step_last {if $step_active >= 7}step_active{/if}">
		<span class="step_title">{l s='Step' mod='digitaleo'} 7</span>
		<span class="step_desc">
		{if $type_steps == "campaign"}{l s='Confirm' mod='digitaleo'}{/if}
		</span>
	</div>
	<div class="step_arrow1 {if $step_active >= 7}arrow1_active{/if}"></div>
	{/if}
	{/if}
</div>