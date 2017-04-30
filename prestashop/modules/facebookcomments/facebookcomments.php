<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class facebookcomments extends Module
{

    function __construct()
    {
        $this->name = 'facebookcomments';
        $this->tab = 'social_networks';
        $this->version = '1.7.1';
        $this->author = 'mypresta.eu';
        $this->bootstrap = true;
        $this->mypresta_link = 'https://mypresta.eu/modules/social-networks/facebook-comments.html';
        $this->dir = '/modules/facebookcomments/';
        parent::__construct();
        $this->displayName = $this->l('Facebook Comments');
        $this->description = $this->l('An easiest way to add facebook comments plugin for your prestashop store');
        $this->mkey = "freelicense";
        $this->checkforupdates();
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 12 //
        // ---------- //
        // ---------- //
        if (@file_exists('../modules/' . $this->name . '/key.php'))
        {
            @require_once('../modules/' . $this->name . '/key.php');
        }
        else
        {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php'))
            {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            }
            else
            {
                if (@file_exists('modules/' . $this->name . '/key.php'))
                {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1)
        {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        }
        else
        {
            if (defined('_PS_ADMIN_DIR_'))
            {
                if (Tools::isSubmit('submit_settings_updates'))
                {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') == false)
                {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200))
                    {
                        $actual_version = facebookcommentsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (facebookcommentsUpdate::version($this->version) < facebookcommentsUpdate::version(Configuration::get('updatev_' . $this->name)))
                    {
                        $this->warning = $this->l('New version available, check http://MyPresta.eu for more informations');
                    }
                }
                if ($display_msg == 1)
                {
                    if (facebookcommentsUpdate::version($this->version) < facebookcommentsUpdate::version(facebookcommentsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version)))
                    {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    }
                    else
                    {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    function install()
    {
        if (parent::install() == false or !$this->registerHook('displayProductExtraContent') OR !Configuration::updateValue('update_' . $this->name, '0') OR !$this->registerHook('header') OR !$this->registerHook('displayProductFooter') OR !$this->registerHook('displayRightColumn') OR !$this->registerHook('displayLeftColumn') OR !Configuration::updateValue('fcbc_where', '1')
            //OR !Configuration::updateValue('fcbc_url','http://mypresta.eu/') OR !Configuration::updateValue('fcbc_width','535') OR !Configuration::updateValue('fcbc_nbp','5') OR !Configuration::updateValue('fcbc_scheme','light') OR !Configuration::updateValue('fcbc_langarray',$this->installconfiguration()) OR !Configuration::updateValue('fcbc_admins','100001227592471') OR !Configuration::updateValue('fcbc_appid','')
        )
        {
            return false;
        }
        return true;
    }

    public function installconfiguration()
    {

        $fcbc_langarray = "";
        foreach (Language::getLanguages(false) AS $key => $value)
        {
            $fcbc_langarray[$key] = 'en_GB';
        }
        return $fcbc_langarray;
    }

    public function getconf()
    {
        $array['fcbc_where'] = Configuration::get('fcbc_where');
        $array['fcbc_url'] = Configuration::get('fcbc_url');
        $array['fcbc_width'] = Configuration::get('fcbc_width');
        $array['fcbc_nbp'] = Configuration::get('fcbc_nbp');
        $array['fcbc_scheme'] = Configuration::get('fcbc_scheme');
        if ($this->psversion() == 5 || $this->psversion() == 6)
        {
            $array['fcbc_lang'] = Configuration::get('fcbc_langarray', $this->context->language->id);
        }
        else
        {
            global $cookie;
            $array['fcbc_lang'] = Configuration::get('fcbc_langarray', $cookie->id_lang);
        }
        $array['fcbc_admins'] = Configuration::get('fcbc_admins');
        $array['fcbc_appid'] = Configuration::get('fcbc_appid');
        $array['product_page_url'] = trim(strtok($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], '?'));
        return $array;
    }

    public function psversion()
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        return $exp[1];
    }

    public function advert()
    {
        return '
        <div class="panel">
            <h3> <i class="icon-usd"></i> ' . $this->l('MyPresta Modules') . '</h3>
            <div class="alert alert-success">
                <strong>' . $this->l('We develop this module for free.') . '</strong>
                ' . $this->l('If you want, you can ') . '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7WE8PTH4ZPYZA">' . $this->l('send PayPal donation') . '</a>
            </div>
            <div style="diplay:block; clear:both; margin-bottom:20px;">
              <iframe src="//apps.facepages.eu/somestuff/onlyexample.html" width="100%" height="150" border="0" style="border:none;"></iframe>
            </div>
		</div>';
    }

    public function getContent()
    {
        $output = "";
        if (Tools::isSubmit('submit_settings'))
        {
            Configuration::updatevalue('fcbc_where', $_POST['fcbc_where']);
            //Configuration::updatevalue('fcbc_url',$_POST['fcbc_url']);
            Configuration::updatevalue('fcbc_width', $_POST['fcbc_width']);
            Configuration::updatevalue('fcbc_nbp', $_POST['fcbc_nbp']);
            Configuration::updatevalue('fcbc_scheme', $_POST['fcbc_scheme']);
            Configuration::updatevalue('fcbc_langarray', $_POST['fcbc_langarray']);
            Configuration::updatevalue('fcbc_admins', $_POST['fcbc_admins']);
            Configuration::updatevalue('fcbc_appid', $_POST['fcbc_appid']);
            Configuration::updateValue('fcbc_addappid', $_POST['fcbc_addappid']);
            $output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="' . $this->l('Settings Saved') . '" />' . $this->l('Settings Saved') . '</div>';
        }
        return $output . $this->displayForm();
    }

    public function displayForm()
    {

        $var = $this->getconf();
        $fcbcwhere1 = "";
        $fcbcwhere2 = "";
        $fcbcwhere3 = "";
        $fcbcwhere4 = "";
        $fcbcscheme1 = "";
        $fcbcscheme2 = "";
        if ($var['fcbc_where'] == "1")
        {
            $fcbcwhere1 = "checked=\"yes\"";
        }
        if ($var['fcbc_where'] == "2")
        {
            $fcbcwhere2 = "checked=\"yes\"";
        }
        if ($var['fcbc_where'] == "3")
        {
            $fcbcwhere3 = "checked=\"yes\"";
        }
        if ($var['fcbc_where'] == "4")
        {
            $fcbcwhere4 = "checked=\"yes\"";
        }
        if ($var['fcbc_scheme'] == "dark")
        {
            $fcbcscheme2 = "selected=\"yes\"";
        }
        if ($var['fcbc_scheme'] == "light")
        {
            $fcbcscheme1 = "selected=\"yes\"";
        }
        $languages = Language::getLanguages(false);
        $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
        $langiso = "";
        foreach ($languages as $language)
        {
            $langiso .= '<div id="header_fcbc_langarray_' . $language['id_lang'] . '" style="display: ' . ($language['id_lang'] == $id_lang_default ? 'block' : 'none') . ';float: left;">
        <input type="text" id="fcbc_langarray_' . $language['id_lang'] . '" name="fcbc_langarray[' . $language['id_lang'] . ']" value="' . Configuration::get('fcbc_langarray', $language['id_lang']) . '">
        </div>';
        }
        $langiso .= '<div class="flags_block">' . $this->displayFlags($languages, $id_lang_default, 'header_fcbc_langarray', 'header_fcbc_langarray', true) . "</div>";
        $form2a = '<div class="bootstrap" style="margin-top:20px; margin:auto; max-width:354px; margin-top:10px;"><div class="alert alert-info">' . $this->l('If you use default-bootstrap template to display tabs in that way you have to modify your theme product.tpl file') . ' <u><a href="http://mypresta.eu/en/art/prestashop-16/product-tabs.html" target="_blank">' . $this->l('read how to do that') . '</a></u></div></div>';
        $form2b = '<div class="bootstrap" style="margin-top:20px; margin:auto; max-width:354px; margin-top:10px;"><div class="alert alert-info">' . $this->l('If you use default-bootstrap template in PrestaShop in version 1.6 this is default way of how tabs appear ') . '</div></div>';
        $form = '<div id="module_block_settings">
                <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                <fieldset id="fieldset_module_block_settings">
                    <legend>' . $this->l('Configuration of comments') . '</legend>
                    <label>' . $this->l('Product Tabs') . '</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="1" ' . $fcbcwhere1 . '/>
                    </div>
                    
                    <label>' . $this->l('Product Footer') . '</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="2" ' . $fcbcwhere2 . '/>
                    </div>
                    
                    <label>' . $this->l('Right Column') . '</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="3" ' . $fcbcwhere3 . '/>
                    </div>

                    <label>' . $this->l('Left Column') . '</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="4" ' . $fcbcwhere4 . '/>
                    </div>                    
                  
                    <label>' . $this->l('Comments feed width') . '</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_width" value="' . $var['fcbc_width'] . '"/>
                    </div> 
                    <label>' . $this->l('Number of comments') . '</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_nbp" value="' . $var['fcbc_nbp'] . '"/>
                    </div>
                    
                    <div style="clear:both; display:block;">
                    <label>' . $this->l('Color scheme') . '</label>
                    <div class="margin-form">
                        <select name="fcbc_scheme"/>
                            <option value="light" ' . $fcbcscheme1 . '>' . $this->l('light') . '</option>
                            <option value="dark" ' . $fcbcscheme2 . '>' . $this->l('dark') . '</option>
                        </select>
                    </div>
                    </div>
                    
                    <div style="clear:both; display:block;">
                    <label>' . $this->l('Language') . '</label><p class="small" ><a href="http://mypresta.eu/en/art/know-how/facebook-list-of-local-language-codes.html" target="_blank">' . $this->l('read more about language codes') . '</a></p>
                    <div class="margin-form" >
                        ' . $langiso . '
                    </div>   
                    </div>    
                    
                    <div style="clear:both; display:block; margin-top:25px;">                               
                    <label>' . $this->l('Admins') . '</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_admins" value="' . $var['fcbc_admins'] . '"/>
                        <p class="clear">' . $this->l('Grant moderation privileges for selected facebook accounts.') . ' ' . $this->l('Separate all admin IDs by commas') . ' ' . $this->l('(ID of facebook private profile)') . '</p>
                    </div>
                    </div>

                    <div style="clear:both; display:block;">
                    <label>' . $this->l('Include Facebook APP') . '</label>
                    <div class="margin-form">
                        <select name="fcbc_addappid"/>
                            <option value="1" ' . (configuration::get('fcbc_addappid') == 1 ? 'selected="selected"' : '') . '>' . $this->l('Yes') . '</option>
                            <option value="0" ' . (configuration::get('fcbc_addappid') != 1 ? 'selected="selected"' : '') . '>' . $this->l('No') . '</option>
                        </select>
                    </div>
                    </div>

                    <label>' . $this->l('APP id') . '</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_appid" value="' . $var['fcbc_appid'] . '"/>
                        <p class="clear">' . $this->l('You can use own facebook app') . '</p>
                    </div>                                                                                                                                
                    <center>
                    <input type="submit" name="submit_settings" value="' . $this->l('Save Settings') . '" class="button" />
                    <br/><br/><br/><br/>
                    ' . $this->l('') . '
                    </center>
                </form>
            </fieldset><div style="diplay:block; clear:both; margin-bottom:5px;">
		</div>
		<div style="display:block; clear:both; overflow:hidden; margin-bottom:20px;">
            <div style="float:left; text-align:left; display:inline-block; margin-top:5px;">' . $this->l('like us on Facebook') . '</br><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fmypresta&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=true&amp;font=verdana&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=276212249177933" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px; margin-top:10px;" allowtransparency="true"></iframe>
            </div><link href="../modules/' . $this->name . '/css.css" rel="stylesheet" type="text/css" />
            ' . '<div style="float:right; text-align:right; display:inline-block; margin-top:5px; font-size:10px;">
            ' . $this->l('Proudly developed by') . ' <a href="http://mypresta.eu" style="font-weight:bold; color:#B73737">MyPresta<font style="color:black;">.eu</font></a>
            </div>
            </div>
        </div>
        ';
        return $this->advert() . '<div class="panel"><div class="nobootstrap">' . $form . '</div></div>' . $this->checkforupdates(0, 1);
    }

    public function hookheader($params)
    {

        $var = $this->getconf();
        $this->context->controller->addCSS(($this->_path) . 'facebookcomments.css', 'all');
        $this->context->smarty->assign('var', $var);
        return $this->display(__FILE__, 'header.tpl');
    }

    public function hookdisplayProductFooter($params)
    {
        if (isset($_GET['id_product']) && isset($_GET['controller']))
        {
            if ($_GET['controller'] == 'product')
            {
                $var = $this->getconf();
                $this->context->smarty->assign('var', $var);
                if ($var['fcbc_where'] == 2)
                {
                    return $this->display(__FILE__, 'productfooter.tpl');
                }
            }
        }
    }

    public function hookdisplayProductExtraContent($params)
    {
        $var = $this->getconf();
        if ($var['fcbc_where'] == 1)
        {
            $ps17tabz[] = (new PrestaShop\PrestaShop\Core\Product\ProductExtraContent())->setTitle($this->l('Comments'))->setContent($this->context->smarty->fetch('module:facebookcomments/tabcontents.tpl'));
            return $ps17tabz;
        }
    }

    public function hookdisplayRightColumn($params)
    {
        $var = $this->getconf();
        if ($var['fcbc_where'] == 3)
        {
            if (isset($_GET['id_product']) && isset($_GET['controller']))
            {
                if ($_GET['controller'] == 'product')
                {
                    $this->context->assign('var', $var);
                    return $this->display(__FILE__, 'productfooter.tpl');
                }
            }
        }
    }

    public function hookdisplayLeftColumn($params)
    {
        $var = $this->getconf();
        if ($var['fcbc_where'] == 4)
        {
            if (isset($_GET['id_product']) && isset($_GET['controller']))
            {
                if ($_GET['controller'] == 'product')
                {
                    $this->context->smarty->assign('var', $var);
                    return $this->display(__FILE__, 'productfooter.tpl');
                }
            }
        }
    }

    public function inconsistency($ret)
    {
        return;
    }

}

class facebookcommentsUpdate extends facebookcomments
{

    public static function version($version)
    {

        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3)
        {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2)
        {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1)
        {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0)
        {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {

        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {

        if (ini_get("allow_url_fopen"))
        {
            if (function_exists("file_get_contents"))
            {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

?>