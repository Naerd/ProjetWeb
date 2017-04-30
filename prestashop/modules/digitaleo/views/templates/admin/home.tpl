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
<div class="dgo_content home_content">

    <div class="home_desc">
        <strong>{l s='Communicate easily' mod='digitaleo'}</strong> {l s='with SMS and Email with your customers and' mod='digitaleo'} <strong>{l s='boost your business' mod='digitaleo'}</strong> {l s='with' mod='digitaleo'} <strong>{l s='Digitaleo' mod='digitaleo'}</strong> {l s='and PrestaShop!' mod='digitaleo'}
    </div>
    <div class="group_home">
        <div class="customers">
            <a href="#video1" class="video_link"><img src="../modules/digitaleo/views/img/synchroniser-clients.png" /><span></span><i class="material-icons player-icon">&#xE038</i></a>
            <div class="content">
                <p class="text1">{l s='Synchronize your PrestaShop customers with your Digitaleo account.' mod='digitaleo'}</p>
                <p class="text2">{l s='Segment and finely target your customers.' mod='digitaleo'}</p>
                <p class="dgo_button"><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=segments" class="btn btn_orange">{l s='Synchronize my customers' mod='digitaleo'}</a></p>
            </div>
        </div>
        <div class="notifications">
            <a href="#video2" class="video_link"><img src="../modules/digitaleo/views/img/gerer-notifications.png" /><span></span><i class="material-icons player-icon">&#xE038</i></a>
            <div class="content">
                <p class="text1">{l s='Automatically trigger the sending of emails and transactional SMS based on events: newsletter subscription, order confirmations, etc.' mod='digitaleo'}</p>
                <p class="dgo_button"><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=notifications" class="btn btn_orange">{l s='Manage my notifications' mod='digitaleo'}</a></p>
            </div>
        </div>
        <div class="campaigns">
            <a href="#video3" class="video_link"><img src="../modules/digitaleo/views/img/lancer-operation.png" /><span></span><i class="material-icons player-icon">&#xE038</i></a>
            <div class="content">
                <p class="text1">{l s='Send quickly business operations ready to use and customizable.' mod='digitaleo'}</p>
                <p class="text2">{l s='Access content specific to your industry.' mod='digitaleo'}</p>
                <p class="dgo_button"><a href="{$currentIndex|escape:'htmlall':'UTF-8'}&action=campaigns" class="btn btn_orange">{l s='Launch of commercial operations' mod='digitaleo'}</a></p>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="fonctionnalites">
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
        <div class="clear"></div>
        <div class="lien_site">
            <a href="https://app.digitaleo.com/" title="Accéder à Digitaleo" target="_blank">{l s='Go to Digitaleo' mod='digitaleo'}</a>
        </div>
    </div>
    <div class="dgo_footer">
        <img src="{$module_dir|escape:'htmlall':'UTF-8'}/views/img/logo-100x105.png" alt="Logo Digitaleo" width="75">
        <div class="dgo_footer_left">
            <p class="dgo_footer_title">{l s='About Digitaleo' mod='digitaleo' mod='digitaleo'}</p>
            <p class="dgo_footer_desc">{l s='Digitaleo is the solution that animates all your business operations based on your highlights.' mod='digitaleo'}<br>
            {l s='Traffic generation, point of sale animation and customer loyalty.' mod='digitaleo'}</p>
        </div>
        <div class="dgo_footer_left dgo_footer_left_number">
            <p class="dgo_footer_title">{l s='Contact us at' mod='digitaleo'}</p>
            <p class="dgo_footer_number">{l s='02 56 03 67 00' mod='digitaleo'}</p>
        </div>
        <div class="clearfix"></div>
    </div>
    <div id="videos">
        <div id="video1">
              <iframe width="854" height="480" src="{if $iso_lang == 'es'}http://eo4.me/W55xX{else}http://eo4.me/N7W7Q{/if}" frameborder="0" allowfullscreen></iframe>
        </div>
        <div id="video2">
              <iframe width="854" height="480" src="{if $iso_lang == 'es'}http://eo4.me/XPKPF{else}http://eo4.me/SYzAz{/if}" frameborder="0" allowfullscreen></iframe>
        </div>
        <div id="video3">
              <iframe width="854" height="480" src="{if $iso_lang == 'es'}http://eo4.me/vjxmc{else}http://eo4.me/1jWSK{/if}" frameborder="0" allowfullscreen></iframe>
        </div>
    </div>
</div>