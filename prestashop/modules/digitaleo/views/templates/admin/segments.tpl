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
<div class="dgo_image_title"><span>{l s='Customers' mod='digitaleo'}</span></div>
<div class="dgo_buttons_top">
	<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segment_step1" class="btn btn_orange">{l s='New Segment' mod='digitaleo'}</a>
	<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync_step1" class="btn btn_left_10">{l s='New Synchronization' mod='digitaleo'}</a>
</div>
<div class="dgo_tab">
	<div class="dgo_tab_inner">
    	<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segments" class="active">{l s='Segments' mod='digitaleo'}</a>
		<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=sync">{l s='Clients Synchronized' mod='digitaleo'}</a>
	</div>
</div>
<div class="dgo_tab_content dgo_tab_content_right">
	<table class="dgo_table">
		<tr>
			<th>{l s='Segment name' mod='digitaleo'}</th>
			<th>{l s='Number of contacts' mod='digitaleo'}</th>
			<th>{l s='Actions' mod='digitaleo'}</th>
		</tr>
		{if !empty($segments)}
		{foreach from=$segments item=segment}
		<tr>
			<td>{$segment.name|escape:'htmlall':'UTF-8'}</td>
			<td>{$segment.nb_contacts|intval}</td>
			<td class="nowrap">
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segment_step1&modif_id_segment={$segment.id_segment|intval}" class="btn link-dropdown"><i class="material-icons">create</i> {l s='Edit' mod='digitaleo'}</a><button class="btn btn-dropdown" data-toggle="dropdown" data-target="dropdown{$segment.id_segment|intval}">down</button>
				<ul class="dropdown-menu" id="dropdown{$segment.id_segment|intval}">
					<li><a href="javascript:;" onclick="confirm_delete('{$currentIndex|escape:'htmlall':'UTF-8'}&action=delete_segment&id_segment={$segment.id_segment|intval}')"><i class="material-icons">delete</i> {l s='Delete' mod='digitaleo'}</a></li>
				</ul>
			</td>
		</tr>
		{/foreach}
		{else}
		<tr>
			<td colspan="3" class="center info">{l s='There is no segments created yet' mod='digitaleo'}</td>
		</tr>
		{/if}
	</table>
	{include file='./pagination.tpl'}
</div>
<div id="left_column">
	 <div class="main">
	 	<p class="header">{l s='You need help' mod='digitaleo'} ?</p>
	 	<p>{l s='Discover the creation of a segments and synchronizations in this video' mod='digitaleo'}</p>
	 	<a href="#video4" class="video_link" ><img src="../modules/digitaleo/views/img/synchroniser-clients.png" /><span></span><i class="material-icons player-icon">&#xE038</i></a>
	 </div>
	 <div class="footer">
	 	<p class="header">{l s='What is a segment' mod='digitaleo'} ?</p>
	 	<p>{l s='A segment will allow you to filter your clients according to criteria you have defined (sex, age, residence ...)' mod='digitaleo'}</p>
	 	<p class="header">{l s='What is a synchronization' mod='digitaleo'} ?</p>
	 	<p>{l s='Synchronization will allow you to automatically import your segments and customers onto Digitaleo' mod='digitaleo'}</p>
	 </div>
	 <div class="clear"></div>
</div>
<div id="videos">
    <div id="video4">
          <iframe width="854" height="480" src="{if $iso_lang == 'es'}http://eo4.me/W55xX{else}http://eo4.me/N7W7Q{/if}" frameborder="0" allowfullscreen></iframe>
    </div>
</div>
{include file='./footer_assistance.tpl'}