<?php
/**
 * NOTICE OF LICENSE.
 *
 * This source file is subject to a commercial license from DIGITALEO SAS
 * Use, copy, modification or distribution of this source file without written
 * license agreement from the DIGITALEO SAS is strictly forbidden.
 *
 *  @author	    Digitaleo
 *  @copyright 2016 Digitaleo
 *  @license   All Rights Reserved
 */

class Digitaleo extends Module
{
    protected $api;
    protected $dgo_model;

    public function __construct()
    {
        $this->name = 'digitaleo';
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $this->tab = 'emailing';
        } else {
            $this->tab = 'advertising_marketing';
        }
        $this->version = '1.3.3';
        $this->author = 'Digitaleo';
        $this->module_key = 'df24da720d492f921104ce64032ecf48';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7.1.0');
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->limited_countries = array('fr', 'es');

        parent::__construct();

        $this->displayName = $this->l('Digitaleo');
        $this->description = $this->l('module description');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $this->menu_parent = 'AdminParentCustomer';
        } else {
            $this->menu_parent = 'AdminCustomers';
        }
        $this->menu_controller = 'AdminDigitaleo';
        $this->menu_name = 'Digitaleo';

        include_once dirname(__FILE__).'/classes/DigitaleoAPI.php';
        include_once dirname(__FILE__).'/classes/DigitaleoModel.php';

        $config = Configuration::getMultiple(array('DIGITALEO_LOGIN', 'DIGITALEO_PASSWORD'), null, 0, 0);
        if (!empty($config['DIGITALEO_LOGIN']) && !empty($config['DIGITALEO_PASSWORD'])) {
            $this->api = new DigitaleoAPI($config['DIGITALEO_LOGIN'], $config['DIGITALEO_PASSWORD']);
            $this->dgo_model = new DigitaleoModel();
        }

        require dirname(__FILE__).'/backward_compatibility/backward.php';
    }

    public function install()
    {
        $sql = array();
        include dirname(__FILE__).'/install/install.php';

        $return_sql = true;
        foreach ($sql as $query) {
            if (!Db::getInstance()->execute($query)) {
                $this->_errors[] = Db::getInstance()->getMsgError();

                $return_sql = false;
                break;
            }
        }

        $id_lang = Language::getIdByIso('en');
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }
        $id_lang_fr = Language::getIdByIso('fr');
        $id_tab = self::getIdTab($this->menu_parent);

        if (!$this->installModuleTab(
            $this->menu_controller,
            array($id_lang => $this->menu_name, $id_lang_fr => $this->menu_name),
            $id_tab
        )) {
            return false;
        }

        // Paniers abandonnés qui ont moins de 7 jours
        Configuration::updateValue('DIGITALEO_ABANDONMENT_MAXDAY', 7, false, 0, 0);

        if (empty($return_sql)
            || !parent::install()
            || !$this->registerHook('backOfficeHeader')
            || !$this->registerHook('postUpdateOrderStatus')
            || !$this->registerHook('createAccount')
            || !$this->registerHook('newOrder')
            || !$this->registerHook('orderReturn')
            || !$this->registerHook('header')) {
            return false;
        }

        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $this->registerHook('actionObjectCustomerUpdateAfter');
            $this->registerHook('actionObjectCustomerDeleteBefore');
            $this->registerHook('actionObjectAddressAddAfter');
            $this->registerHook('actionObjectAddressUpdateAfter');
            $this->registerHook('actionObjectAddressDeleteAfter');
        }

        return true;
    }

    protected static function getIdTab($tabClass)
    {
        return (int) Db::getInstance()->getValue(
            'SELECT id_tab FROM '._DB_PREFIX_.'tab WHERE class_name = \''.pSQL($tabClass).'\''
        );
    }

    protected function installModuleTab($tabClass, $tabName, $idTabParent)
    {
        $tab = new Tab();

        $id_lang = Language::getIdByIso('en');
        if (!$id_lang) {
            $id_lang = $this->context->language->id;
        }
        $langues = Language::getLanguages(false);
        foreach ($langues as $langue) {
            if (!isset($tabName[$langue['id_lang']])) {
                $tabName[$langue['id_lang']] = $tabName[$id_lang];
            }
        }

        $tab->name = $tabName;
        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTabParent;
        $id_tab = $tab->save();
        if (!$id_tab) {
            return false;
        }

        $this->installcleanPositions($tab->id, $idTabParent);

        return true;
    }

    public function installcleanPositions($id, $id_parent)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT `id_tab`,`position`
            FROM `' ._DB_PREFIX_.'tab`
            WHERE `id_parent` = ' .(int) $id_parent.'
            AND `id_tab` != ' .(int) $id.'
            ORDER BY `position`');
        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i) {
            Db::getInstance()->execute('
			UPDATE `' ._DB_PREFIX_.'tab`
			SET `position` = ' .(int) ($result[$i]['position'] + 1).'
			WHERE `id_tab` = ' .(int) $result[$i]['id_tab']);
        }

        Db::getInstance()->execute('
			UPDATE `' ._DB_PREFIX_.'tab`
			SET `position` = 2
			WHERE `id_tab` = ' .(int) $id);

        return true;
    }

    public function uninstall()
    {
        Configuration::deleteByName('DIGITALEO_LOGIN');
        Configuration::deleteByName('DIGITALEO_PASSWORD');
        Configuration::deleteByName('DIGITALEO_API_ACCESS_TOKEN');
        Configuration::deleteByName('DIGITALEO_USER_ACCESS_TOKEN');
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            Configuration::deleteByName('DIGITALEO_ABANDONMENT_MAXDAY');
        }
        Configuration::deleteByName('DIGITALEO_LASTCHECKCARTABANDON');

        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'dgo_sync_customers');
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'dgo_segments');
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'dgo_customers_contacts');
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'dgo_error_log');
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'dgo_campaigns');
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'dgo_notifications');
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'dgo_cart_abandonment');

        $this->uninstallModuleTab($this->menu_controller);

        return parent::uninstall();
    }

    protected function uninstallModuleTab($tabClass)
    {
        $idTab = self::getIdTab($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();

            return true;
        }

        return false;
    }

    public function getContent()
    {
        $return = $this->postProcess();

        if (!extension_loaded('curl')) {
            $return .= $this->displayError($this->l('error cURL extension'));
        }

        $config = Configuration::getMultiple(array('DIGITALEO_LOGIN', 'DIGITALEO_PASSWORD'), null, 0, 0);

        if (!empty($config['DIGITALEO_LOGIN']) && !empty($config['DIGITALEO_PASSWORD'])) {
            // Test des accès
            $api = new DigitaleoAPI($config['DIGITALEO_LOGIN'], $config['DIGITALEO_PASSWORD']);

            if ($api->isUserAuthenticate()) {
                $token = Tools::getAdminToken(
                    'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
                    (int) $this->context->cookie->id_employee
                );
                Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token);
            }
        } elseif (Tools::getValue('action') == 'createTrialAccount') {
            $api = new DigitaleoAPI();
            $this->context->smarty->assign(array(
                'listIndustries' => $api->getIndustries(),
                'country' => Tools::strtolower($this->context->language->iso_code).
                            '_'.Tools::strtoupper($this->context->language->iso_code),
            ));
        }

        $this->context->smarty->assign(array(
            'iso_lang' => $this->context->language->iso_code,
            'config' => $config,
            'module_dir' => _MODULE_DIR_.$this->name,
            'url_config' => 'index.php?tab=AdminModules&configure=digitaleo&token='.Tools::getValue('token'),
            'action' => Tools::getValue('action'),
            'debug' => (_PS_MODE_DEV_ && !empty($api->debug)) ? $api->debug : array(),
        ));

        return $return.$this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/configure.tpl');
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitAuth')) {
            // Authentification
            $DIGITALEO_LOGIN = Tools::getValue('email');
            $DIGITALEO_PASSWORD = Tools::getValue('password');

            // Test des accès
            $api = new DigitaleoAPI($DIGITALEO_LOGIN, $DIGITALEO_PASSWORD);
            if ($api->isPartnerAuthenticate()) {
                Configuration::updateValue('DIGITALEO_LOGIN', $DIGITALEO_LOGIN, false, 0, 0);
                Configuration::updateValue('DIGITALEO_PASSWORD', $DIGITALEO_PASSWORD, false, 0, 0);

                if ($api->isUserAuthenticate()) {
                    $token = Tools::getAdminToken(
                        'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
                        (int) $this->context->cookie->id_employee
                    );
                    Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token);
                } else {
                    return $this->displayError($this->l('Connection failed'));
                }
            } else {
                return $this->displayError($this->l('Connection failed'));
            }
        } elseif (Tools::isSubmit('submitCreateTrialAccount') && empty($this->context->cookie->digitaleo_api_userid)) {
            // Création du compte gratuit
            $email = Tools::getValue('email');
            $mobile = Tools::getValue('mobile');
            $password = Tools::getValue('password');
            $company = Tools::getValue('company');
            $name = Tools::getValue('name');
            $industryName = Tools::getValue('industryName');
            $cgs = Tools::getValue('cgs');

            if (!$email || !$mobile || !$password || !$name || !$company || !$industryName) {
                return $this->displayError($this->l('You must enter required fields'));
            }
            if (!$cgs) {
                return $this->displayError($this->l('You must agree to the terms of service'));
            }
            if (!Validate::isEmail($email)) {
                return $this->displayError($this->l('Invalid email'));
            }
            if (!Validate::isPhoneNumber($mobile)) {
                return $this->displayError($this->l('Invalid phone number'));
            }

            $iso = $this->context->language->iso_code;
            $api = new DigitaleoAPI();
            $this->context->cookie->digitaleo_api_userid =
            $api->createFreeTrial($email, $mobile, $company, $name, $password, $industryName, $iso);

            // Envoi d'un code par SMS
            if (!$api->error) {
                Configuration::updateValue('DIGITALEO_LOGIN_TMP', Tools::getValue('email'), false, 0, 0);
                Configuration::updateValue('DIGITALEO_PASSWORD_TMP', Tools::getValue('password'), false, 0, 0);
                $api->sendCode();
            }

            if ($api->error) {
                foreach ($api->error as &$error) {
                    if (stripos($error, "is already used") !== false) {
                        if (stripos($error, "mobile") !== false) {
                            $error = $this->l('Parameter mobile is already used');
                        } else {
                            $error = $this->l('Parameter login is already used email is already used');
                        }
                    } elseif (stripos($error, "password is not strong enough") !== false) {
                        $error = $this->l('password is not strong enough');
                    }
                }

                return $this->displayError($api->error);
            }

            Tools::redirectAdmin(
                'index.php?tab=AdminModules&configure=digitaleo&action=checkcode&token='.Tools::getValue('token')
            );
        } elseif (Tools::isSubmit('submitNewCode') && !empty($this->context->cookie->digitaleo_api_userid)) {
            // Envoi d'un nouveau code par SMS
            $api = new DigitaleoAPI();
            $api->sendCode($this->context->cookie->digitaleo_api_userid);

            if ($api->error) {
                return $this->displayError($api->error);
            }

            Tools::redirectAdmin(
                'index.php?tab=AdminModules&configure=digitaleo&action=checkcode&token='.Tools::getValue('token')
            );
        } elseif (Tools::isSubmit('submitCheckCode') && !empty($this->context->cookie->digitaleo_api_userid)) {
            // Vérification du code reçu par SMS
            $code = Tools::getValue('code');
            if (!$code) {
                return $this->displayError($this->l('You must enter the code'));
            }

            $config = Configuration::getMultiple(array('DIGITALEO_LOGIN_TMP', 'DIGITALEO_PASSWORD_TMP'), null, 0, 0);
            $api = new DigitaleoAPI($config['DIGITALEO_LOGIN_TMP'], $config['DIGITALEO_PASSWORD_TMP']);
            $api->checkcode($this->context->cookie->digitaleo_api_userid, $code);

            if ($api->error) {
                ddd($api->error);
                return $this->displayError($api->error);
            }

            if (!$api->isUserAuthenticate()) {
                return $this->displayError($this->l('The code you entered is incorrect'));
            }
            unset($this->context->cookie->digitaleo_api_userid);
            Configuration::updateValue('DIGITALEO_LOGIN', $config['DIGITALEO_LOGIN_TMP'], false, 0, 0);
            Configuration::updateValue('DIGITALEO_PASSWORD', $config['DIGITALEO_PASSWORD_TMP'], false, 0, 0);
            Configuration::deleteByName('DIGITALEO_LOGIN_TMP');
            Configuration::deleteByName('DIGITALEO_PASSWORD_TMP');

            $token = Tools::getAdminToken(
                'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
                (int) $this->context->cookie->id_employee
            );
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&action=createaccount&token='.$token);
        }

        return '';
    }

    protected function addUpdateContact(Customer $customer, $address = false)
    {
        $civility = '';
        if (version_compare(_PS_VERSION_, '1.5', '>=')) {
            $gender = new Gender($customer->id_gender, $this->context->language->id);
            $civility = $gender->name;
        } else {
            if ($customer->id_gender == 1) {
                $civility = $this->l('Mr.');
            } elseif ($customer->id_gender == 2) {
                $civility = $this->l('Ms.');
            }
        }

        $data = array(
            'email' => $customer->email,
            'civility' => $civility,
            'firstName' => $customer->firstname,
            'lastName' => $customer->lastname,
            'reference' => $customer->id,
            'birthDate' => $customer->birthday,
            'locale' => $this->dgo_model->getCustomerLocale($customer->id),
        );

        if ($address && !($address instanceof Address)) {
            $address = new Address($address);
        }

        if (!$address) {
            $addresses = $customer->getAddresses($this->context->language->id);
            $address = (object) array_shift($addresses);
        }

        if ($address) {
            // Mise à jour de l'adresse
            $data['mobile'] = @$address->phone_mobile;

            $data['mobile'] = preg_replace('`[^0-9]`iUs', '', $data['mobile']);
            $data['mobile'] = trim($data['mobile']);

            if (!preg_match('`^(06|07|336|337)([0-9]{8})$`iUs', $data['mobile'])) {
                $data['mobile'] = '';
            }

            $data['address1'] = @$address->address1;
            $data['address2'] = @$address->address2;
            $data['zipcode'] = @$address->postcode;
            $data['city'] = @$address->city;
            $data['state'] = State::getNameById(@$address->id_state);
            $data['country'] = Country::getNameById($this->context->language->id, @$address->id_country);
            $data['company'] = @$address->company;
        }

        $id_contact = $this->dgo_model->getIdContactByIdCustomer($customer->id);

        // On recherche le contact sur le serveur de Digitaleo s'il n'est pas sur PS
        //TODO gérer les doublons
        if (!$id_contact) {
            $id_contact = $this->api->getIdContactByEmail($customer->email);
            if ($id_contact) {
                $this->dgo_model->addCustomerContact($customer->id, $id_contact);
            }
        }

        // On va prendre les synchronisations actives et automatiques, puis voir si l'on doit le synchroniser
        $sync_actives = $this->dgo_model->getActiveAutoSync();

        $array_id_list_add = array();
        $array_id_list_remove = array();
        if (!empty($sync_actives)) {
            // On va voir si on est censé faire partie d'une synchro
            foreach ($sync_actives as $sync) {
                $num = $this->dgo_model->getCustomersToSync($sync['id_sync'], 0, 0, true, $customer->id);

                if ($num >= 1) {
                    $array_id_list_add[] = (int) $sync['id_target_digitaleo'];
                } else {
                    $array_id_list_remove[] = (int) $sync['id_target_digitaleo'];
                }
            }
        }

        if ($id_contact) {
            // Mise à jour du contact
            $this->api->updateContact($id_contact, $data);
        } elseif ($array_id_list_add) {
            // Création du contact s'il est dans une liste
            $return_contacts = $this->api->createContacts(array($data));

            if (!empty($return_contacts[0]['id'])) {
                $id_contact = $return_contacts[0]['id'];
                $this->dgo_model->addCustomerContact($customer->id, $id_contact);
            }
        }

        // Mise à jour des listes
        if ($id_contact) {
            // Association du contact aux listes
            foreach ($array_id_list_add as $id_list) {
                $this->api->addContactsToList($id_list, $id_contact);
            }

            // Suppression du contact des listes
            foreach ($array_id_list_remove as $id_list) {
                $this->api->removeContactsFromList($id_list, $id_contact);
            }
        }
    }

    public function hookCreateAccount($params)
    {
        if (!$this->api) {
            return;
        }

        $customer = $params['newCustomer'];

        $id_address = false;
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $id_address = Address::getFirstCustomerAddressId($customer->id);
        }
        // Mise à jour du contact
        $this->addUpdateContact($customer, $id_address);

        // Envoi de notification
        $this->execNotification('hook_customer_add', $params);
    }

    public function hookActionObjectCustomerUpdateAfter($params)
    {
        if (!$this->api) {
            return;
        }

        $this->addUpdateContact($params['object']);
    }

    public function hookActionObjectCustomerDeleteBefore($params)
    {
        if (!$this->api) {
            return;
        }

        $customer = $params['object'];
        $id_contact = $this->dgo_model->getIdContactByIdCustomer($customer->id);

        // On recherche le contact sur le serveur de Digitaleo s'il n'est pas sur PS
        //TODO gérer les doublons
        if (!$id_contact) {
            $id_contact = $this->api->getIdContactByEmail($customer->email);
        }

        $sync_actives = $this->dgo_model->getActiveAutoSync();

        $array_id_list_add = array();
        $array_id_list_remove = array();
        if (!empty($sync_actives)) {
            // On va voir si on est censé faire partie d'une synchro
            foreach ($sync_actives as $sync) {
                $num = $this->dgo_model->getCustomersToSync($sync['id_sync'], 0, 0, true, $customer->id);

                if ($num >= 1) {
                    $array_id_list_add[] = (int) $sync['id_target_digitaleo'];
                } else {
                    $array_id_list_remove[] = (int) $sync['id_target_digitaleo'];
                }
            }
        }

        // Mise à jour des listes
        if ($id_contact) {
            // Suppression du contact des listes
            foreach ($array_id_list_remove as $id_list) {
                $this->api->removeContactsFromList($id_list, $id_contact);
            }
        }

        // Suppression du contact
        if ($id_contact) {
            $this->api->deleteContacts($id_contact);
            $this->dgo_model->deleteContact($params['object']->id);
        }
    }

    public function hookActionObjectAddressAddAfter($params)
    {
        if (!$this->api) {
            return;
        }
        $customer = new Customer($params['object']->id_customer);

        $this->addUpdateContact($customer, $params['object']);
    }

    public function hookActionObjectAddressUpdateAfter($params)
    {
        $this->hookActionObjectAddressAddAfter($params);
    }

    public function hookActionObjectAddressDeleteAfter($params)
    {
        if (!$this->api) {
            return;
        }

        $id_address = Address::getFirstCustomerAddressId($params['object']->id_customer);
        $address = new Address($id_address);
        $address->id_customer = $params['object']->id_customer;
        $this->hookActionObjectAddressAddAfter(array('object' => $address));
    }

    public function hookPostUpdateOrderStatus($params)
    {
        if (!$this->api) {
            return;
        }

        $order = new Order($params['id_order']);
        if ($order->valid) {
            $customer = new Customer($order->id_customer);
            $this->addUpdateContact($customer, $order->id_address_invoice);
        }
    }

    public function hookNewOrder($params)
    {
        $this->execNotification('hook_order_add', $params);
    }

    public function hookOrderReturn($params)
    {
        $this->execNotification('hook_return_ask', $params);
    }

    public function hookHeader($params)
    {
        if (!$this->api) {
            return '';
        }

        $last_check = (int) Configuration::get('DIGITALEO_LASTCHECKCARTABANDON', null, 0, 0);
        if (!$last_check || time() - $last_check > 3600) {
            Configuration::updateValue('DIGITALEO_LASTCHECKCARTABANDON', time(), false, 0, 0);

            $token_dgo = Tools::getToken(
                $this->_path.'views/js/dgo_front.js'
            );

            Media::addJsDef(array(
                    'token_dgo' => $token_dgo
            ));
            $this->context->controller->addJS($this->_path.'views/js/dgo_front.js');
        }

        return '';
    }

    public function execNotification($event, $params = array())
    {
        if (!$this->api) {
            return;
        }

        $notifications = $this->dgo_model->getNotificationByEvent($event);
        if (!$notifications) {
            return;
        }

        if (isset($params['customer'])) {
            $customer = $params['customer'];
        } elseif (isset($params['orderReturn'])) {
            $customer = new Customer($params['orderReturn']->id_customer);
        } elseif (isset($params['newCustomer'])) {
            $customer = $params['newCustomer'];
        }

        if (isset($params['order'])) {
            $order = $params['order'];
        } elseif (isset($params['orderReturn'])) {
            $order = new Order($params['orderReturn']->id_order);
        }
        $address = new Address($order->id_address_delivery);
        $mobile_address = $address->phone_mobile;
        if (!$mobile_address) {
            $address = new Address($order->id_address_invoice);
            $mobile_address = $address->phone_mobile;
        }

        $contacts = array();
        foreach ($notifications as $notification) {
            if ($notification['recipient_type'] == 'admin') {
                $contacts[] = array(
                    'email' => $notification['administrator_email'],
                    'mobile' => $notification['administrator_sms'],
                    'firstName' => '',
                    'lastName' => '',
                );
            } else {
                $contacts[] = array(
                    'email' => $customer->email,
                    'mobile' => $mobile_address,
                    'firstName' => $customer->firstname,
                    'lastName' => $customer->lastname,
                );
            }
            $this->api->addCampaignContact($notification['id_campaign_digitaleo'], $contacts);
        }
    }

    public function execNotificationCartAbandonment()
    {
        if (!$this->api) {
            return;
        }

        $notifications = $this->dgo_model->getNotificationByEvent('hook_cart_abandonment');
        if (!$notifications) {
            return;
        }

        $contacts = array();
        foreach ($notifications as $notification) {
            $carts = $this->dgo_model->getCartAbandonment($notification['delay_cart_abandonment']);
            if (!$carts) {
                continue;
            }
            foreach ($carts as $cart) {
                $customer = new Customer($cart['id_customer']);

                $contacts[] = array(
                    'email' => $customer->email,
                    'mobile' => '',
                    'firstName' => $customer->firstname,
                    'lastName' => $customer->lastname,
                );

                $return = $this->api->addCampaignContact($notification['id_campaign_digitaleo'], $contacts);
                if ($return['count'] > 0) {
                    $this->dgo_model->notificationCartAbandonmentSent($cart['id_cart']);
                }
            }
        }
    }

    public function displayError($errors)
    {
        if (is_array($errors)) {
            $return = '';
            foreach ($errors as $err) {
                $return .= parent::displayError($err);
            }
        } else {
            $return = parent::displayError($errors);
        }

        return $return;
    }

    public function hookBackOfficeHeader($params)
    {
        if (Tools::getValue('tab') != $this->menu_controller
            && Tools::getValue('controller') != $this->menu_controller
            && Tools::getValue('tab') != 'AdminModules'
            && Tools::getValue('controller') != 'AdminModules'
            && Tools::getValue('configure') != 'digitaleo') {
            return '';
        }

        $token = Tools::getAdminToken(
            'AdminModules'.(int) Tab::getIdFromClassName('AdminModules').(int) $this->context->cookie->id_employee
        );

        $header = '<link type="text/css" rel="stylesheet" href="'.$this->_path.'views/css/digitaleo.css" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet" />
        <script>
        var token_tab = "'.$token.'";
        </script>
        <script type="text/javascript" src="'.$this->_path.'views/js/digitaleo.js"></script>';
       
        if (version_compare(_PS_VERSION_, '1.5', '<')) {
            $header .= '<link type="text/css" rel="stylesheet" href="'.$this->_path.'views/css/patch_p14.css" />';
        }
        if (version_compare(_PS_VERSION_, '1.6', '<') && version_compare(_PS_VERSION_, '1.5', '>=')) {
            $header .= '<link type="text/css" rel="stylesheet" href="'.$this->_path.'views/css/patch_p15.css" />';
        }

        return $header;
    }
}
