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
{if $campaign_channel == "sms"}
5. {l s='Test the SMS Sending' mod='digitaleo'}
{else}
5. {l s='Test the E-mail sending' mod='digitaleo'}
{/if}
</div>
{include file='./steps_dgo.tpl' step_active=5 type_steps="campaign"}
</div>
<div class="dgo_tab_content">
	<div class="center">
		<form action="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step6" method="post">
			{if $campaign_channel == "sms"}
				<p class="dgo_title_blue">{l s='To review the message in best condition, we recommand you send it to yourself' mod='digitaleo'}</p>
				<p class="marb20">
				<input type="text" size="40" id="sms_test_number" placeholder="{l s='+33...' mod='digitaleo'}"> <a href="javascript:;" class="btn btn_sms" onclick="sms_test()">{l s='Send test' mod='digitaleo'}</a>&nbsp;<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/ajax-loader.gif" class="loader_sms" style="display:none" /></p>
			{else}
				<p class="dgo_title_blue">{l s='To check the display of your email, we recommend you send it to your email' mod='digitaleo'}</p>
				<p class="marb20">
				<input type="text" size="40" id="email_test" value="{$dgo_email|escape:'htmlall':'UTF-8'}"> <a href="javascript:;" class="btn btn_email" onclick="email_test()">{l s='Send test' mod='digitaleo'}</a>&nbsp;<img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/ajax-loader.gif" class="loader_email" style="display:none" />
				</p>
			{/if}
			<p>
				<a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaign_step4" class="btn">{l s='Back' mod='digitaleo'}</a>
				<input type="submit" class="btn btn_orange marl20" value="{l s='Continue' mod='digitaleo'}" />
			</p>
		</form>
	</div>
</div>
<div id="footer_assistance">
	<p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>