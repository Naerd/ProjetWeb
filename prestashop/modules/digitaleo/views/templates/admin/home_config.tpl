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
<div class="presentation_digitaleo">
    <div class="logo_digitaleo">
        <img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/logo-Digitaleo-white-200x44.png" alt="Logo Digitaleo">
    </div>
    <div class="home_desc">
        <strong>{l s='Communicate easily' mod='digitaleo'}</strong> {l s='with SMS and Email with your customers and' mod='digitaleo'} <strong>{l s='boost your business' mod='digitaleo'}</strong> {l s='with' mod='digitaleo'} <strong>{l s='Digitaleo' mod='digitaleo'}</strong> {l s='and PrestaShop!' mod='digitaleo'}
    </div>
    <div id="player">
        <div id="player_vid" class="lang_{$iso_lang|escape:'htmlall':'UTF-8'}"></div>
        <iframe id="video" width="489" height="275" src="{if $iso_lang == 'es'}https://www.youtube.com/embed/dNMRUSH5YmQ{else}https://www.youtube.com/embed/7FUrtdehqqk{/if}?rel=0&amp;controls=1&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
    </div>
    <div class="digitaleo_advantages">
        <div class="dgo_adv">
            <strong>{l s='Synchronize your PrestaShop customers' mod='digitaleo'}</strong> {l s='with your Digitaleo account.' mod='digitaleo'}
        </div>
        <div class="dgo_adv">
            <strong>{l s='Automatically' mod='digitaleo'}</strong> {l s='send' mod='digitaleo'} <strong>{l s='emails' mod='digitaleo'}</strong> {l s='and' mod='digitaleo'} <strong>{l s='SMS' mod='digitaleo'}</strong> {l s='based on customer events' mod='digitaleo'}<br/><br/>{l s='(order confirmation, validation of payment, etc.).' mod='digitaleo'}
        </div>
        <div class="dgo_adv">
            {l s='Quickly launch' mod='digitaleo'} <strong>{l s='commercial operations' mod='digitaleo'}</strong> {l s='SMS and Emails ' mod='digitaleo'} <strong>{l s='already ready for use.' mod='digitaleo'}</strong>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="fonctionnalites fonctionnalites_auth">
        <h2>{l s='Discover also all the features that Digitaleo offers' mod='digitaleo'} :</h2>
        <div class="col-3">
            <p>{l s='Time saving' mod='digitaleo'}</p>
            <div class="icons">
                <div class="pacc"><i class="material-icons">&#xE8E5;</i><span>{l s='PACC' mod='digitaleo'}</span></div>
                <div class="leo"><i class="material-icons">&#xE916;</i><span>{l s='Léo' mod='digitaleo'}</span></div>
            </div>
        </div>
        <div class="col-6">
            <p>{l s='Easy and inexpensive visibility' mod='digitaleo'}</p>
            <div class="icons">
                <div class="sms"><i class="material-icons">&#xE324;</i><span>{l s='SMS' mod='digitaleo'}</span></div>
                <div class="email"><i class="material-icons">&#xE158;</i><span>{l s='Email' mod='digitaleo'}</span></div>
                <div class="mini_site"><i class="material-icons">&#xE321;</i><span>{l s='Mini mobile site' mod='digitaleo'}</span></div>
                <div class="msg_repondeur"><i class="material-icons">&#xE0D9;</i><span>{l s='Message answering machine' mod='digitaleo'}</span></div>
            </div>
        </div>
        <div class="col-3">
            <p>{l s='Customer Management' mod='digitaleo'}</p>
            <div class="icons">
                <div class="import_locations"><i class="material-icons">&#xE7F0;</i><span>{l s='Import and rent' mod='digitaleo'}</span></div>
                <div class="collecte_donnees"><i class="material-icons">&#xE331;</i><span>{l s='Data gathering' mod='digitaleo'}</span></div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="lien_site">
            <a href="https://app.digitaleo.com/" title="Accéder à Digitaleo" target="_blank">{l s='Go to Digitaleo' mod='digitaleo'}</a>
        </div>
    </div>
</div>
{if $action == 'createTrialAccount'}
    {include file="./trialAccount.tpl"}
{elseif $action == 'checkcode'}
    {include file="./checkcode.tpl"}
{else}
    {include file="./authentification.tpl"}
{/if}