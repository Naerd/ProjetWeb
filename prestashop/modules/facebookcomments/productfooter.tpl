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
<div id="fcbcfooter" class="page-content card card-block">
    <div id="fcbc">
        <div data-href="http://{$var['product_page_url']}" class="fb-comments" data-width="{$fcbc_width}" data-num-posts="{$fcbc_nbp}" data-colorscheme="{$fcbc_scheme}"></div>
    </div>
</div>