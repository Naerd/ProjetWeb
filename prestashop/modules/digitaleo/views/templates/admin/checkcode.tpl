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
    <form action="{$url_config|escape:'htmlall':'UTF-8'}&action=checkcode" method="post" class="width3">
        <fieldset>
            <div class="dgo_title_blue">{l s='Try for free' mod='digitaleo'}</div>
            <p class="dgo_desc_blue">{l s='Up to 100 SMS and 10 000 Emails available for 15-day free trial with no obligation!' mod='digitaleo'}</p>
            <p class="dgo_desc_blue">{l s='An activation code was sent to you by SMS' mod='digitaleo'}</p>
            <p><input name="code" id="code" value="" class="" size="50" type="text" placeholder="{l s='Code' mod='digitaleo'}"></p>
            <p><input id="module_form_submit_btn" value="{l s='Confirm' mod='digitaleo'}" name="submitCheckCode" class="btn btn_orange" type="submit"></p>
            <br><br>
            <p>
                <p class="dgo_desc_blue">{l s='You have not received a code?' mod='digitaleo'}</p>
                <input id="module_form_submit_btn" value="{l s='Send another code' mod='digitaleo'}" name="submitNewCode" class="btn btn_orange" type="submit">
            </p>
        </fieldset>
    </form>
</div>