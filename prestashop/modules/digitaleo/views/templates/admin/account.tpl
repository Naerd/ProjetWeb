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
<div class="dgo_content">
    <div class="dgo_account">
        <div class="dgo_mon_offre">
            <div class="dgo_border">
                {if stripos($dgo_contract.list.0.industryName, "Restauration") !== false || stripos($dgo_contract.list.0.industryName, "alimentaire") !== false || stripos($dgo_contract.list.0.industryName, "alimentación") !== false}
                    {assign var="type_logo" value="restauration"}
                {else if stripos($dgo_contract.list.0.industryName, "commerçants") !== false || stripos($dgo_contract.list.0.industryName, "Commerce") !== false || stripos($dgo_contract.list.0.industryName, "comerciantes") !== false}
                    {assign var="type_logo" value="commerce-proximite"}
                {else if stripos($dgo_contract.list.0.industryName, "personne") !== false || stripos($dgo_contract.list.0.industryName, "personal") !== false}
                    {assign var="type_logo" value="service-personne"}
                {else if stripos($dgo_contract.list.0.industryName, "culture") !== false || stripos($dgo_contract.list.0.industryName, "cultura") !== false}
                    {assign var="type_logo" value="loisirs-culture"}
                {else if stripos($dgo_contract.list.0.industryName, "Sport") !== false || stripos($dgo_contract.list.0.industryName, "Deporte") !== false}
                    {assign var="type_logo" value="sport-detente"}
                {else if stripos($dgo_contract.list.0.industryName, "maison") !== false || stripos($dgo_contract.list.0.industryName, "hogar") !== false}
                    {assign var="type_logo" value="equipement-maison"}
                {else if stripos($dgo_contract.list.0.industryName, "Auto") !== false || stripos($dgo_contract.list.0.industryName, "Automóvil") !== false}
                    {assign var="type_logo" value="auto"}
                {else}
                    {assign var="type_logo" value="generique"}
                {/if}
                <img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/logos/logo-{$type_logo|escape:'htmlall':'UTF-8'}.png" />
                <p class="dgo_offre_title">{l s='My actual Offer' mod='digitaleo'}</p>
                <p class="dgo_offre_type">{l s=$dgo_contract.list.0.type mod='digitaleo'}</p>
                <p><a href="https://app.digitaleo.com/marketing#/settings/contact" target="_blank" class="btn btn_orange">{l s='Upgrade my Offer' mod='digitaleo'}</a></p>
            </div>
            <div class="dgo_credits">
                <p class="dgo_title_blue">{l s='Credits left' mod='digitaleo'}</p>
                <div class="dgo_credits_nb"><big>{$dgo_restrictions_sms.credit|intval}</big><small>SMS</small></div>
                <div class="dgo_credits_nb"><big>{$dgo_restrictions_email.credit|intval}</big><small>E-mails</small></div>
            </div>
        </div>
        <div class="dgo_infos">
            <h4 class="dgo_title_blue">{l s='My Personal informations' mod='digitaleo'}</h4>
            <p><i class="material-icons">business</i> {$dgo_contract.list.0.name|escape:'htmlall':'UTF-8'}</p>
            <p><i class="material-icons">account_box</i> {$dgo_user_total.firstName|escape:'htmlall':'UTF-8'} {$dgo_user_total.name|escape:'htmlall':'UTF-8'}</p>
            <p><i class="material-icons">email</i> {$dgo_user_total.email|escape:'htmlall':'UTF-8'}</p>
            {if $dgo_user_total.mobile}<p><i class="material-icons">call</i> {$dgo_user_total.mobile|escape:'htmlall':'UTF-8'}</p>{/if}
        </div>
        <div class="clear"></div>
    </div>
</div>
<div id="footer_assistance">
    <p><span class="accroche">Besoin d'assistance ?</span>  Contactez-nous par email à : <span>serviceclient@digitaleo.com</span></p>
</div>