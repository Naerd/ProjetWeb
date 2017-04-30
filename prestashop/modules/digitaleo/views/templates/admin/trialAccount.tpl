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
<div class="trial_account">
    <form action="{$url_config|escape:'htmlall':'UTF-8'}&action=createTrialAccount" method="post" class="width3">
        <fieldset>
            <div class="dgo_title_blue">{l s='Try for free' mod='digitaleo'}</div>
            <p class="dgo_desc_blue">{l s='Up to 100 SMS and 10 000 Emails available for 15-day free trial with no obligation!' mod='digitaleo'}</p>
            <p><input name="name" id="name" value="{if isset($smarty.post.name)}{$smarty.post.name|escape:'htmlall':'UTF-8'}{/if}" type="text" placeholder="{l s='Name' mod='digitaleo'}"></p>
            <p><input name="company" id="company" value="{if isset($smarty.post.company)}{$smarty.post.company|escape:'htmlall':'UTF-8'}{/if}" type="text" placeholder="{l s='Company' mod='digitaleo'}"></p>
            <p><select name="industryName" id="industryName">
                <option value="">{l s='Activity sector' mod='digitaleo'}</option>
                {foreach $listIndustries as $industry}
                    {if $industry['locale'] == $country || ($country != "fr_FR" && $country != "es_ES" && $industry['locale'] == "fr_FR")}
                    <option value="{$industry['name']|escape:'htmlall':'UTF-8'}"{if isset($smarty.post.industryName) && $industry['name'] == $smarty.post.industryName} selected="selected"{/if}>{$industry['name']|escape:'htmlall':'UTF-8'}</option>
                    {/if}
                {/foreach}
            </select></p>
            <p><input name="email" id="email" value="{if isset($smarty.post.email)}{$smarty.post.email|escape:'htmlall':'UTF-8'}{/if}" type="text" placeholder="{l s='Email' mod='digitaleo'}"></p>
            <p><input name="password" id="password" value="" type="password" placeholder="{l s='Password' mod='digitaleo'}"></p>
            <p>({l s='8 characters min, 1 lowercase, 1 uppercase and 1 digit' mod='digitaleo'})</p>
            <p><input name="mobile" id="mobile" value="{if isset($smarty.post.mobile)}{$smarty.post.mobile|escape:'htmlall':'UTF-8'}{/if}" type="text" placeholder="{l s='Mobile phone' mod='digitaleo'}"></p>
            <p>
                <input name="cgs" id="cgs" value="1" type="checkbox"{if isset($smarty.post.cgs)} checked="checked"{/if}>
                <label for="cgs">{l s='I accept the' mod='digitaleo'}</label> <a href="https://www.digitaleo.fr/cgs" target="_blank">{l s='CGS' mod='digitaleo'}</a> {l s='and the' mod='digitaleo'} <a href="https://www.digitaleo.fr/cgu" target="_blank">{l s='CGU' mod='digitaleo'}</a>
            </p>
            <p><input id="module_form_submit_btn" value="{l s='Boost my business' mod='digitaleo'}" name="submitCreateTrialAccount" class="btn btn_orange" type="submit"></p>
            <div class="clear"></div>
            <a href="{$url_config|escape:'htmlall':'UTF-8'}" class="button btn btn-default">{l s='Cancel' mod='digitaleo'}</a>
        </fieldset>
    </form>
</div>