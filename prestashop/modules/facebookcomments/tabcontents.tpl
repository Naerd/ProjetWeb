{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
* @copyright 2010-9999 VEKIA
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{assign var=fcbc_width value=$var['fcbc_width']}
{assign var=fcbc_nbp value=$var['fcbc_nbp']}
{assign var=fcbc_scheme value=$var['fcbc_scheme']}
{literal}
    <style>
        .fb_ltr, .fb_iframe_widget, .fb_iframe_widget span {
            width: 100% !important
        }
    </style>
{/literal}
<div id="fcbc" class="">
    <fb:comments href="http://{$var['product_page_url']}" colorscheme="{$fcbc_scheme}" width="{$fcbc_width}"></fb:comments>
</div>
