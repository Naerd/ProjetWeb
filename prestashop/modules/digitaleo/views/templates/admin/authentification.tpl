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
<div class="auth_digitaleo">
    <form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
            <div class="dgo_title_blue">
            <strong>{l s='Sign in' mod='digitaleo'}</strong><br/>
            {l s='with your account Digitaleo' mod='digitaleo'}
            </div>
            <p>
                <input name="email" id="email" type="text" placeholder="{l s='E-mail' mod='digitaleo'}">
            </p>
            <p>
                <input name="password" id="password" type="password" placeholder="{l s='Password' mod='digitaleo'}">
            </p>
            <p>
                <input id="module_form_submit_btn" value="{l s='Sign in' mod='digitaleo'}" name="submitAuth" class="btn btn_orange" type="submit">
            </p>
            <br><br>
            <p style="text-align:center;">
                <div class="dgo_title_blue">{l s='No Digitaleo account?' mod='digitaleo'}</div>
                <a href="{$url_config|escape:'htmlall':'UTF-8'}&action=createTrialAccount" class="btn btn_orange">{l s='Try for free' mod='digitaleo'}</a><br><br>
                {l s='Up to 100 SMS and 10 000 Emails available for 15-day free trial with no obligation!' mod='digitaleo'}<br>
            </p>
    </form>
</div>
