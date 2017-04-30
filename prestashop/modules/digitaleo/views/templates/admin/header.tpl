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
<div class="dgo_header">
    {if !empty($smarty.get.action) && $smarty.get.action == 'createaccount'}
        {if $ps14}<div class="conf">
            <img src="../img/admin/ok2.png" alt=""> {l s='Account create.' mod='digitaleo'}
        </div>
        {else}
            <div class="bootstrap"><div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>{l s='Account create.' mod='digitaleo'}</div></div>
        {/if}
    {/if}
    <div class="dgo_menu">
        <a href="{$currentIndex|escape:'htmlall':'UTF-8'}"><img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/logo-bleu.png" alt="Logo Digitaleo"></a>
        <ul>
            <li><a href="{$currentIndex|escape:'htmlall':'UTF-8'}" class="icon_home {if !isset($smarty.get.action) || empty($smarty.get.action) || $smarty.get.action == 'createaccount'}active{/if}">{l s='Home' mod='digitaleo'}</a></li>
            <li><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segments" class="icon_target {if isset($smarty.get.action) && in_array($smarty.get.action, array('sync', 'segments', 'sync_step1', 'sync_step2', 'sync_step3', 'sync_step4', 'segment_step2', 'segment_step3', 'segment_step1'))}active{/if}">{l s='Customers' mod='digitaleo'}</a></li>
            <li><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notifications" class="icon_notif {if isset($smarty.get.action) && stripos($smarty.get.action, 'notification') !== false}active{/if}">{l s='Notifications' mod='digitaleo'}</a></li>
            <li><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaigns" class="icon_campaign {if isset($smarty.get.action) && stripos($smarty.get.action, 'campaign') !== false}active{/if}">{l s='Campaigns' mod='digitaleo'}</a></li>
        </ul>
        <div id="digitaleo_link">
            <a href="https://app.digitaleo.com/" title="Accéder à Digitaleo" target="_blank">{l s='Go to Digitaleo' mod='digitaleo'}</a>
        </div>
        <div id="dropdown">
            <i class="material-icons">&#xE853</i>
            <div class="content">
                <a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=account" class="dgo_my_account {if isset($smarty.get.action) && $smarty.get.action == 'account'}active{/if}"><i class="material-icons">&#xE853</i>{l s='My Account' mod='digitaleo'}</a>
                <a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=logout" class="btn_logout"><i class="material-icons">input</i>{l s='Logout' mod='digitaleo'}</a>
            </div>
        </div>
    </div>
</div>