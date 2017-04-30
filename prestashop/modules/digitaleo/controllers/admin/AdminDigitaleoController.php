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

class AdminDigitaleoController extends ModuleAdminController
{
    public $context;
    protected $config;
    protected $api;
    protected $isAuthenticate;
    public static $currentIndex;
    public $url_config;
    public $dgo_model;
    public $smartyvar = array();
    public $pagination = 30;

    public function __construct()
    {
        $this->lang = true;
        $this->context = Context::getContext();

        parent::__construct();

        if (!isset(self::$currentIndex)) {
            self::$currentIndex = 'index.php?tab=AdminDigitaleo&token='.Tools::getValue('token');
        }

        include_once dirname(__FILE__).'/../../classes/DigitaleoAPI.php';
        include_once dirname(__FILE__).'/../../classes/DigitaleoModel.php';

        $this->dgo_model = new DigitaleoModel();

        $token = Tools::getAdminToken(
            'AdminModules'.(int) Tab::getIdFromClassName('AdminModules').(int) $this->context->cookie->id_employee
        );
        $this->url_config = 'index.php?tab=AdminModules&configure=digitaleo&token='.$token;

        $this->config = Configuration::getMultiple(array('DIGITALEO_LOGIN', 'DIGITALEO_PASSWORD'), null, 0, 0);

        $this->isAuthenticate = false;
        if (!empty($this->config['DIGITALEO_LOGIN']) && !empty($this->config['DIGITALEO_PASSWORD'])) {
            // Test des accès
            $this->api = new DigitaleoAPI($this->config['DIGITALEO_LOGIN'], $this->config['DIGITALEO_PASSWORD']);
            $this->isAuthenticate = $this->api->isUserAuthenticate();

            if (!Tools::getIsset('ajax') && isset($this->api->error['invalid_grant'])) {
                self::logout();
            }
        }

        if (Tools::getIsset('ajax')) {
            @ob_clean();

            $token_ajax = Tools::getAdminToken(
                'AdminModules'.(int) Tab::getIdFromClassName('AdminModules').(int) $this->context->cookie->id_employee
            );

            if (!Tools::getIsset('token') || Tools::getValue('token') != $token_ajax) {
                die("Forbidden");
            }

            $ajax = Tools::toCamelCase(Tools::getValue('ajax'));
            $this->$ajax();
            die();
        }

        if (!$this->isAuthenticate) {
            Tools::redirectAdmin($this->url_config);
        }

        if ($this->isAuthenticate) {
            if (!isset($this->context->cookie->dgo_id_user)) {
                $digitaleo_user = $this->api->getUser($this->config['DIGITALEO_LOGIN']);
                $this->context->cookie->dgo_id_user = $digitaleo_user['id'];
                $this->context->cookie->dgo_user = $digitaleo_user['firstName'].' '.$digitaleo_user['name'];
                $this->context->cookie->dgo_email = $digitaleo_user['email'];
            }
        }
    }

    public function renderList()
    {
        $action = Tools::getValue('action');

        $function = 'dgo'.Tools::toCamelCase($action, true);
        if (method_exists($this, $function)) {
            $this->$function();
        }

        $tpl_file = '';
        if (file_exists(_PS_MODULE_DIR_.'digitaleo/views/templates/admin/'.$action.'.tpl')) {
            $tpl_file = './'.$action.'.tpl';
        }


        $this->context->smarty->assign(array(
            'ps14' => version_compare(_PS_VERSION_, '1.5', '<'),
            'config' => $this->config,
            'module_dir' => _MODULE_DIR_.'digitaleo',
            'currentIndex' => self::$currentIndex,
            'isAuthenticate' => $this->isAuthenticate,
            'action' => Tools::getValue('action'),
            'dgo_user' => $this->context->cookie->dgo_user,
            'dgo_email' => $this->context->cookie->dgo_email,
            'tpl_file' => $tpl_file,
            'iso_lang' => $this->context->language->iso_code,
            'debug' => (_PS_MODE_DEV_ && !empty($this->api->debug)) ? $this->api->debug : array(),
        ));

        if (isset($this->context->cookie->type_sync_action)) {
            $this->smartyvar['type_sync_action'] = $this->context->cookie->type_sync_action;
        }

        if (isset($this->context->cookie->type_segment_action)) {
            $this->smartyvar['type_segment_action'] = $this->context->cookie->type_segment_action;
        }

        if (!empty($this->smartyvar)) {
            $this->context->smarty->assign($this->smartyvar);
        }

        return $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/admindigitaleo.tpl');
    }

    public function dgoAccount()
    {
        $dgo_user_total = $this->api->getUser($this->config['DIGITALEO_LOGIN']);
        $dgo_contract = $this->api->getContracts();
        $dgo_restrictions_sms = $this->api->getRestrictions('SMSLIMIT');
        $dgo_restrictions_email = $this->api->getRestrictions('EMAILLIMIT');

        $this->context->smarty->assign(array(
            'dgo_user_total' => $dgo_user_total,
            'dgo_contract' => $dgo_contract,
            'dgo_restrictions_sms' => $dgo_restrictions_sms,
            'dgo_restrictions_email' => $dgo_restrictions_email,
        ));
    }

    public function pagination($action_lien)
    {
        $num_page = 1;
        if (Tools::getIsset('num_page')) {
            $num_page = Tools::getValue('num_page');
        }

        $this->smartyvar['pages_nb'] = ceil($this->smartyvar['total_results'] / $this->pagination);
        $this->smartyvar['p'] = $num_page;

        $range = 2; /* how many pages around page selected */
        $this->smartyvar['start'] = (int) ($this->smartyvar['p'] - $range);
        if ($this->smartyvar['start'] < 1) {
            $this->smartyvar['start'] = 1;
        }

        $this->smartyvar['stop'] = (int) ($this->smartyvar['p'] + $range);
        if ($this->smartyvar['stop'] > $this->smartyvar['pages_nb']) {
            $this->smartyvar['stop'] = (int) $this->smartyvar['pages_nb'];
        }

        $token = Tools::getAdminToken(
            'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').(int) $this->context->cookie->id_employee
        );
        $this->smartyvar['lien_pagination'] = 'index.php?tab=AdminDigitaleo&token='.$token.'&action='.$action_lien;
    }

    public function dgoSync()
    {
        $this->context->cookie->id_list = 0;
        $this->context->cookie->id_sync = 0;
        unset($this->context->cookie->type_sync_action);
        unset($this->context->cookie->sync_event);
        unset($this->context->cookie->sync_id_list);
        unset($this->context->cookie->sync_new_list);
        unset($this->context->cookie->sync_auto);
        $this->context->cookie->num_customer_sync = 0;

        $this->smartyvar['total_results'] = $this->dgo_model->getTotalSyncCustomers();
        $this->pagination('sync');

        $this->smartyvar['sync_customers'] =
        $this->dgo_model->getSyncCustomers($this->pagination, $this->smartyvar['p']);

        if (!empty($this->smartyvar['sync_customers'])) {
            $array_target = array();
            foreach ($this->smartyvar['sync_customers'] as &$sync) {
                $array_target[] = $sync['id_target_digitaleo'];
                if (!empty($sync['id_segment'])) {
                    $segment = $this->dgo_model->getSegment($sync['id_segment']);
                    $sync['text_event'] = $segment['name'];
                } else {
                    if ($sync['hook_prestashop'] == 'hook_newsletter') {
                        $sync['text_event'] = $this->l('Newsletter registered');
                    } elseif ($sync['hook_prestashop'] == 'hook_customers') {
                        $sync['text_event'] = $this->l('All customers registered in Prestashop');
                    }
                }
                $sync['total_contacts'] = $this->dgo_model->getTotalSync($sync['id_sync']);
            }

            if (!empty($array_target)) {
                $list = $this->api->getContactList($array_target);

                foreach ($list as $l) {
                    foreach ($this->smartyvar['sync_customers'] as &$sync) {
                        if ((int) $sync['id_target_digitaleo'] == (int) $l['id']) {
                            $sync['target_name'] = $l['name'];
                            continue;
                        }
                    }
                }
            }
        }
    }

    public function dgoDeleteSync()
    {
        $id_sync = (int) Tools::getValue('id_sync');
        if (!empty($id_sync)) {
            $this->dgo_model->deleteSync($id_sync);
        }

        $token = Tools::getAdminToken(
            'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').(int) $this->context->cookie->id_employee
        );
        Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=sync');
    }

    public function dgoSyncStep1()
    {
        if (Tools::getIsset('duplique_id_sync')) {
            $this->context->cookie->id_sync = (int) Tools::getValue('duplique_id_sync');
            $this->context->cookie->type_sync_action = 'duplique';
        }

        if (Tools::getIsset('modif_id_sync')) {
            $this->context->cookie->id_sync = (int) Tools::getValue('modif_id_sync');
            $this->context->cookie->type_sync_action = 'modif';
        }

        if (!empty($this->context->cookie->id_sync)) {
            $sync = $this->dgo_model->getSync($this->context->cookie->id_sync);
            $this->context->cookie->sync_event =
                $sync['hook_prestashop'] ? $sync['hook_prestashop'] : 'segment_'.$sync['id_segment'];
            $this->context->cookie->sync_id_list = $sync['id_target_digitaleo'];
            $this->context->cookie->sync_auto = $sync['auto'];
        }

        $this->smartyvar['sync_event'] = $this->context->cookie->sync_event;
        $this->smartyvar['sync_events'] = array(
            'hook_customers' => $this->l('All customers registered in Prestashop'),
            'hook_newsletter' => $this->l('Newsletter registered'),
        );

        $segments = $this->dgo_model->getSegments();

        foreach ($segments as $segment) {
            $this->smartyvar['sync_events']['segment_'.$segment['id_segment']] = $segment['name'];
        }
    }

    public function dgoSyncStep2()
    {
        if (Tools::getIsset('sync_event')) {
            $this->context->cookie->sync_event = Tools::getValue('sync_event');
        }

        $this->smartyvar['sync_event'] = $this->context->cookie->sync_event;
        $this->smartyvar['contact_list'] = $this->api->getContactList();
        $this->smartyvar['sync_id_list'] = $this->context->cookie->sync_id_list;
        $this->smartyvar['sync_new_list'] = $this->context->cookie->sync_new_list;
    }

    public function dgoSyncStep3()
    {
        $this->smartyvar['sync_event'] = $this->context->cookie->sync_event;
        $this->smartyvar['sync_id_list'] = $this->context->cookie->sync_id_list;
        $this->smartyvar['sync_new_list'] = $this->context->cookie->sync_new_list;

        if (isset($this->context->cookie->sync_auto)) {
            $this->smartyvar['sync_auto'] = $this->context->cookie->sync_auto;
        }
    }

    public function dgoSyncStep4()
    {
        $this->smartyvar['sync_event'] = $this->context->cookie->sync_event;
        $this->smartyvar['sync_id_list'] = $this->context->cookie->sync_id_list;
        $this->smartyvar['sync_new_list'] = $this->context->cookie->sync_new_list;
        $this->smartyvar['sync_auto'] = $this->context->cookie->sync_auto;

        $hook_prestashop = '';
        $id_segment = 0;
        if ($this->smartyvar['sync_event'] == 'hook_customers') {
            $this->smartyvar['sync_event_text'] = $this->l('All customers registered in Prestashop');
            $hook_prestashop = 'hook_customers';
            $this->smartyvar['nb_contacts'] = $this->dgo_model->getTotalCustomer();
        } elseif ($this->smartyvar['sync_event'] == 'hook_newsletter') {
            $this->smartyvar['sync_event_text'] = $this->l('Newsletter registered');
            $hook_prestashop = 'hook_newsletter';
            $this->smartyvar['nb_contacts'] = $this->dgo_model->getTotalNewsletterRegistered();
        } else {
            // TODO Nom du segment + ID
            preg_match('`^segment_([0-9]+)$`iUs', $this->smartyvar['sync_event'], $regs);

            $id_segment = (int) $regs[1];
            $segment = $this->dgo_model->getSegment($id_segment);
            $this->smartyvar['sync_event_text'] = $this->l('Segment').' : '.$segment['name'];
            $this->smartyvar['nb_contacts'] = $this->dgo_model->getSegmentContactsNumber($id_segment);
        }

        if (empty($this->smartyvar['sync_id_list'])) {
            $this->smartyvar['sync_list_text'] = $this->smartyvar['sync_new_list'];
        } else {
            $list = $this->api->getContactList(array($this->smartyvar['sync_id_list']));
            $this->smartyvar['sync_list_text'] = $list[0]['name'];
        }

        if ((int) $this->smartyvar['sync_auto'] == 1) {
            $this->smartyvar['sync_auto_text'] = $this->l('Auto');
        } else {
            $this->smartyvar['sync_auto_text'] = $this->l('Manual');
        }

        if (Tools::getIsset('gosync')) {
            $this->smartyvar['gosync'] = true;

            if (!isset($this->context->cookie->id_sync) || empty($this->context->cookie->id_sync)) {
                if (!isset($this->context->cookie->id_list) || empty($this->context->cookie->id_list)) {
                    // Si nouvelle liste, on va la créer
                    if (empty($this->smartyvar['sync_id_list'])) {
                        $this->context->cookie->id_list =
                            $this->api->createContactList($this->smartyvar['sync_new_list']);
                        if ($this->api->error) {
                            $this->smartyvar['errors'] =
                                $this->l('Error when creating new contact list:').' "'.$this->api->error[0].'"';

                            return;
                        }
                    } else {
                        $this->context->cookie->id_list = $this->smartyvar['sync_id_list'];
                    }
                }

                // TODO : Insertion dans la BDD
                $this->context->cookie->id_sync = $this->dgo_model->addSync(
                    $hook_prestashop,
                    $id_segment,
                    $this->context->cookie->id_list,
                    $this->smartyvar['sync_auto']
                );
            } else {
                if ($this->context->cookie->type_sync_action == 'modif') {
                    // Si nouvelle liste, on va la créer
                    if (!empty($this->smartyvar['sync_new_list'])) {
                        $this->context->cookie->id_list =
                            $this->api->createContactList($this->smartyvar['sync_new_list']);
                        if ($this->api->error) {
                            $this->smartyvar['errors'] =
                                $this->l('Error when creating new contact list:').' "'.$this->api->error[0].'"';

                            return;
                        }
                    } else {
                        $this->context->cookie->id_list = $this->smartyvar['sync_id_list'];
                    }

                    $this->dgo_model->updateSync(
                        $this->context->cookie->id_sync,
                        $hook_prestashop,
                        $id_segment,
                        $this->context->cookie->id_list,
                        $this->smartyvar['sync_auto']
                    );
                } elseif ($this->context->cookie->type_sync_action == 'duplique') {
                    // Si nouvelle liste, on va la créer
                    if (!empty($this->smartyvar['sync_new_list'])) {
                        $this->context->cookie->id_list =
                            $this->api->createContactList($this->smartyvar['sync_new_list']);
                        if ($this->api->error) {
                            $this->smartyvar['errors'] =
                                $this->l('Error when creating new contact list:').' "'.$this->api->error[0].'"';

                            return;
                        }
                    } else {
                        $this->context->cookie->id_list = $this->smartyvar['sync_id_list'];
                    }

                    // TODO : Insertion dans la BDD
                    $this->context->cookie->id_sync = $this->dgo_model->addSync(
                        $hook_prestashop,
                        $id_segment,
                        $this->context->cookie->id_list,
                        $this->smartyvar['sync_auto']
                    );
                }
            }

            $this->smartyvar['id_sync'] = $this->context->cookie->id_sync;
            $this->smartyvar['total_sync'] = $this->dgo_model->getTotalSync($this->context->cookie->id_sync);
            $this->context->cookie->num_customer_sync = 0;
        }
    }

    public function ajaxDoSync()
    {
        $id_sync = (int) Tools::getValue('id_sync');
        $nb_sync = 200;
        $return = array(
            'error' => false,
            'nb_sync' => 0,
            'end' => false,
        );

        $id_list = $this->context->cookie->id_list;
        if (Tools::getIsset('id_list')) {
            $id_list = (int) Tools::getValue('id_list');
        }

        $customers = $this->dgo_model->getCustomersToSync(
            $id_sync,
            $this->context->cookie->num_customer_sync,
            $nb_sync
        );

        if (count($customers) == 0) {
            $return['end'] = true;
            die(Tools::jsonEncode($return));
        }

        // Synchronisation
        $contacts = array();
        $contacts_ids = array();

        foreach ($customers as $customer) {

            $objCustomer = new Customer($customer['id_customer']);

            $gender = new Gender($objCustomer->id_gender, $this->context->language->id);
            $civility = $gender->name;

            // On cherche si on a un numéro de portable (valide) à envoyer
            $customer['phone_mobile'] = preg_replace('`[^0-9]`iUs', '', $customer['phone_mobile']);
            $customer['phone_mobile'] = trim($customer['phone_mobile']);

            if (!preg_match('`^(06|07|336|337)([0-9]{8})$`iUs', $customer['phone_mobile'])) {
                $customer['phone_mobile'] = '';
            }

            // On envoie pas un contact déjà synchronizé
            if (!$id_contact_digitaleo = $this->dgo_model->isSynchronized(
                $customer['id_customer'],
                $customer['date_upd']
            )) {
                $contact = array(
                    'email' => $customer['email'],
                    'firstName' => $customer['firstname'],
                    'lastName' => $customer['lastname'],
                    'civility' => $civility,
                    'reference' => $objCustomer->id,
                    'birthDate' => $objCustomer->birthday,
                    'mobile' => $customer['phone_mobile'],
                    'locale' => $this->dgo_model->getCustomerLocale($customer['id_customer']),
                );

                $addresses = $objCustomer->getAddresses($this->context->language->id);
                $address = (object) array_shift($addresses);

                if ($address) {
                    // Mise à jour de l'adresse
                    $contact['address1'] = @$address->address1;
                    $contact['address2'] = @$address->address2;
                    $contact['zipcode'] = @$address->postcode;
                    $contact['city'] = @$address->city;
                    $contact['state'] = State::getNameById(@$address->id_state);
                    $contact['country'] = Country::getNameById($this->context->language->id, @$address->id_country);
                    $contact['company'] = @$address->company;
                }

                $contacts[] = $contact;
            } else {
                $contacts_ids[] = $id_contact_digitaleo;
            }
        }

        if (!empty($contacts)) {
            $return_contacts = $this->api->createContacts($contacts);
        }

        // Association des contacts à la liste
        $customers_ids = array();
  
        if (!empty($return_contacts)) {
            foreach ($return_contacts as $rc) {
                $contacts_ids[] = $rc['id'];

                // Pour Ajout à la bdd
                foreach ($customers as $customer) {
                    if ($customer['email'] == $rc['email']) {
                        $customers_ids[] = array(   "id_customer" => $customer['id_customer'],
                                                    "id" => $rc['id'],
                                                    "date" => date('Y-m-d H:i:s'));
                    }
                }
            }
        }

        if (!empty($contacts_ids)) {
            $this->api->addContactsToList($id_list, $contacts_ids);
        }

        if (!empty($customers_ids)) {
            $this->dgo_model->updateCustomersContacts($customers_ids);
        }

        $this->context->cookie->num_customer_sync += count($customers);
        $return['nb_sync'] = $this->context->cookie->num_customer_sync;

        echo Tools::jsonEncode($return);
    }

    public function dgoActiveSync()
    {
        $id = (int) Tools::getValue('id');
        $active = (int) Tools::getValue('active');

        $this->dgo_model->setSyncActive($id, $active);

        $token = Tools::getAdminToken(
            'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
            (int) $this->context->cookie->id_employee
        );

        Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=sync');
    }

    public function dgoSegments()
    {
        $this->context->cookie->id_segment = 0;
        unset($this->context->cookie->type_segment_action);
        unset($this->context->cookie->segment_name);
        unset($this->context->cookie->segment_countries);
        unset($this->context->cookie->segment_age_from);
        unset($this->context->cookie->segment_age_to);
        unset($this->context->cookie->segment_registration_from);
        unset($this->context->cookie->segment_registration_to);
        unset($this->context->cookie->segment_genre);
        unset($this->context->cookie->segment_orders_from);
        unset($this->context->cookie->segment_orders_to);
        unset($this->context->cookie->segment_groups);
        unset($this->context->cookie->segment_newsletter);
        unset($this->context->cookie->segment_optin);

        $this->smartyvar['total_results'] = $this->dgo_model->getTotalSegments();
        $this->pagination('segments');

        $segments = $this->dgo_model->getSegments($this->pagination, $this->smartyvar['p']);

        foreach ($segments as &$segment) {
            $segment['nb_contacts'] = $this->dgo_model->getSegmentContactsNumber($segment['id_segment']);
        }

        $this->context->smarty->assign(array('segments' => $segments));
    }

    public function dgoDeleteSegment()
    {
        $id_segment = (int) Tools::getValue('id_segment');

        // TODO : Tester si une synchro est associée. La supprimer ?

        if (!empty($id_segment)) {
            $this->dgo_model->deleteSegment($id_segment);
        }

        $token = Tools::getAdminToken(
            'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
            (int) $this->context->cookie->id_employee
        );
        Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=segments');
    }

    public function dgoSegmentStep1()
    {
        if (Tools::getIsset('modif_id_segment')) {
            $this->context->cookie->id_segment = (int) Tools::getValue('modif_id_segment');
            $this->context->cookie->type_segment_action = 'modif';
        }

        if (!empty($this->context->cookie->id_segment)) {
            $segment = $this->dgo_model->getSegment($this->context->cookie->id_segment);
            $this->context->cookie->segment_name = $segment['name'];
            $this->context->cookie->segment_countries = $segment['countries'];
            $this->context->cookie->segment_age_from = $segment['age_from'];
            $this->context->cookie->segment_age_to = $segment['age_to'];
            $this->context->cookie->segment_registration_from = $segment['registration_from'];
            $this->context->cookie->segment_registration_to = $segment['registration_to'];
            $this->context->cookie->segment_newsletter = $segment['newsletter'];
            $this->context->cookie->segment_optin = $segment['optin'];
            $this->context->cookie->segment_genre = $segment['genre'];
            $this->context->cookie->segment_orders_from = $segment['orders_from'];
            $this->context->cookie->segment_orders_to = $segment['orders_to'];
            $this->context->cookie->segment_groups = $segment['groups'];
        }

        $this->smartyvar['segment_name'] = $this->context->cookie->segment_name;
    }

    public function dgoSegmentStep2()
    {
        $countries = Country::getCountries($this->context->cookie->id_lang, true);
        $groups = Group::getGroups($this->context->cookie->id_lang);
        $this->context->smarty->assign(array(
            'countries' => $countries,
            'groups' => $groups,
        ));

        $this->smartyvar['segment_countries'] = $this->context->cookie->segment_countries ?
            explode(',', $this->context->cookie->segment_countries) : '';
        $this->smartyvar['segment_age_from'] = $this->context->cookie->segment_age_from;
        $this->smartyvar['segment_age_to'] = $this->context->cookie->segment_age_to;
        $this->smartyvar['segment_registration_from'] = $this->context->cookie->segment_registration_from;
        $this->smartyvar['segment_registration_to'] = $this->context->cookie->segment_registration_to;
        $this->smartyvar['segment_newsletter'] = $this->context->cookie->segment_newsletter;
        $this->smartyvar['segment_optin'] = $this->context->cookie->segment_optin;
        $this->smartyvar['segment_genre'] = $this->context->cookie->segment_genre;
        $this->smartyvar['segment_orders_from'] = $this->context->cookie->segment_orders_from;
        $this->smartyvar['segment_orders_to'] = $this->context->cookie->segment_orders_to;
        $this->smartyvar['segment_groups'] = $this->context->cookie->segment_groups ?
            explode(',', $this->context->cookie->segment_groups) : '';
    }

    public function dgoSegmentStep3()
    {
        // On assign
        $this->smartyvar['segment_name'] = $this->context->cookie->segment_name;
        $this->smartyvar['segment_countries'] = $this->dgo_model->getCountryList(
            $this->context->cookie->id_lang,
            $this->context->cookie->segment_countries
        );
        $this->smartyvar['segment_age_from'] = $this->context->cookie->segment_age_from;
        $this->smartyvar['segment_age_to'] = $this->context->cookie->segment_age_to;
        $this->smartyvar['segment_registration_from'] = $this->context->cookie->segment_registration_from;
        $this->smartyvar['segment_registration_to'] = $this->context->cookie->segment_registration_to;

        $this->smartyvar['segment_genre'] = '';
        if ($this->context->cookie->segment_genre == 'H') {
            $this->smartyvar['segment_genre'] = $this->l('Man');
        }

        if ($this->context->cookie->segment_genre == 'F') {
            $this->smartyvar['segment_genre'] = $this->l('Women');
        }

        $this->smartyvar['segment_orders_from'] = $this->context->cookie->segment_orders_from;
        $this->smartyvar['segment_orders_to'] = $this->context->cookie->segment_orders_to;
        $this->smartyvar['segment_groups'] = $this->dgo_model->getGroupList(
            $this->context->cookie->id_lang,
            $this->context->cookie->segment_groups
        );

        if (isset($this->context->cookie->segment_newsletter) && $this->context->cookie->segment_newsletter != 2) {
            $this->smartyvar['segment_newsletter'] =
                $this->l($this->dgo_model->yesOrNo($this->context->cookie->segment_newsletter));
        }

        if (isset($this->context->cookie->segment_optin) && $this->context->cookie->segment_optin != 2) {
            $this->smartyvar['segment_optin'] =
                $this->l($this->dgo_model->yesOrNo($this->context->cookie->segment_optin));
        }

        $this->smartyvar['segment_contacts_number'] = $this->dgo_model->getSegmentContactsNumber();
    }

    public function dgoSegmentCreate()
    {
        if ($this->dgo_model->createSegment()) {
            $token = Tools::getAdminToken(
                'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
                (int) $this->context->cookie->id_employee
            );
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=segments');
        }
    }

    public function dgoCampaigns()
    {
        unset($this->context->cookie->campaign_name);
        unset($this->context->cookie->campaign_id_list);
        unset($this->context->cookie->campaign_sms_content);
        unset($this->context->cookie->campaign_date);
        unset($this->context->cookie->campaign_channel);
        unset($this->context->cookie->campaign_sender);
        unset($this->context->cookie->campaign_replyto);
        unset($this->context->cookie->campaign_subject);
        unset($this->context->cookie->campaign_id_template);
        unset($this->context->cookie->id_campaign);
        unset($this->context->cookie->id_campaign_digitaleo);
        unset($this->context->cookie->type_campaign_action);

        $this->smartyvar['total_results'] = $this->dgo_model->getTotalCampaigns();
        $this->pagination('campaigns');

        $this->smartyvar['campaign_list'] = $this->dgo_model->getCampaigns($this->pagination, $this->smartyvar['p']);

        $campaign_ids = array();
        $array_target = array();
        foreach ($this->smartyvar['campaign_list'] as $campaign) {
            $campaign_ids[] = $campaign['id_campaign_digitaleo'];
            $array_target[] = $campaign['id_list_digitaleo'];
        }

        $campaigns_digitaleo = $this->api->getCampaigns($campaign_ids);

        foreach ($this->smartyvar['campaign_list'] as &$campaign) {
            foreach ($campaigns_digitaleo['list'] as $cd) {
                if ((int) $cd['id'] == (int) $campaign['id_campaign_digitaleo']) {
                    $campaign['target'] = $cd['listName'];

                    if ((int) $cd['cancelled'] == 1) {
                        $campaign['status'] = 'cancelled';
                        $campaign['status_texte'] = $this->l('cancelled');
                    } elseif ($cd['status'] == 'created') {
                        if (strtotime($campaign['date_send']) > time()) {
                            $campaign['status'] = 'planned';
                            $campaign['status_texte'] = $this->l('planned');
                        } else {
                            $campaign['status'] = 'finished';
                            $campaign['status_texte'] = $this->l('finished');
                        }
                    } else {
                        $campaign['status'] = $cd['status'];
                        $campaign['status_texte'] = $cd['status'];
                    }

                    if ($campaign['channel'] == 'mail') {
                        $campaign['channel_text'] = 'E-mail';
                    } else {
                        $campaign['channel_text'] = 'SMS';
                    }

                    break;
                }
            }

            $campaign['date'] = date('d/m/Y H:i', strtotime($campaign['date_send']));
        }
    }

    public function dgoCampaignStep1()
    {
        if (Tools::getIsset('modif_id_campaign')) {
            $this->context->cookie->id_campaign = (int) Tools::getValue('modif_id_campaign');
            $this->context->cookie->type_campaign_action = 'modif';

            $campaign = $this->dgo_model->getCampaign($this->context->cookie->id_campaign);

            $this->context->cookie->campaign_name = $campaign['name'];
            $this->context->cookie->campaign_id_list = $campaign['id_list_digitaleo'];

            // Conversion de la date
            $this->context->cookie->campaign_date = date('d/m/Y H:i', strtotime($campaign['date_send']));

            $this->context->cookie->campaign_channel = $campaign['channel'];
            $this->context->cookie->campaign_sender = $campaign['sender'];
            $this->context->cookie->campaign_replyto = $campaign['replyto'];
            $this->context->cookie->campaign_subject = $campaign['subject'];
            $this->context->cookie->id_campaign_digitaleo = $campaign['id_campaign_digitaleo'];

            if ($campaign['channel'] == 'sms') {
                $this->context->cookie->campaign_sms_content = $campaign['content'];
            } else {
                $this->context->cookie->campaign_id_template = $campaign['id_template'];
            }
        }

        $this->smartyvar['campaign_name'] = $this->context->cookie->campaign_name;
    }

    public function dgoCampaignStep2()
    {
        $this->smartyvar['campaign_id_list'] = $this->context->cookie->campaign_id_list;
        $this->smartyvar['contact_list'] = $this->api->getContactList();
    }

    public function dgoCampaignStep3()
    {
        $this->smartyvar['campaign_channel'] = $this->context->cookie->campaign_channel;
    }

    public function dgoCampaignStep4()
    {
        $this->smartyvar['campaign_channel'] = $this->context->cookie->campaign_channel;
        $this->smartyvar['campaign_sms_content'] = $this->context->cookie->campaign_sms_content;

        $this->smartyvar['campaign_id_template'] = $this->context->cookie->campaign_id_template;
        $this->smartyvar['campaign_sender'] = $this->context->cookie->campaign_sender;
        $this->smartyvar['campaign_replyto'] = $this->context->cookie->campaign_replyto;
        $this->smartyvar['campaign_subject'] = $this->context->cookie->campaign_subject;

        $this->smartyvar['templates'] = $this->api->getTemplates();
    }

    public function dgoCampaignStep5()
    {
        $this->smartyvar['campaign_channel'] = $this->context->cookie->campaign_channel;
        $this->smartyvar['dgo_email'] = $this->context->cookie->dgo_email;
    }

    public function dgoCampaignStep6()
    {
        $this->smartyvar['campaign_date'] = $this->context->cookie->campaign_date;
        $this->smartyvar['campaign_channel'] = $this->context->cookie->campaign_channel;
    }

    public function dgoCampaignStep7()
    {
        if (Tools::getIsset('campaign_date')) {
            $this->context->cookie->campaign_date = Tools::getValue('campaign_date');
        }

        $this->smartyvar['campaign_name'] = $this->context->cookie->campaign_name;
        $this->smartyvar['campaign_id_list'] = $this->context->cookie->campaign_id_list;

        $list = $this->api->getContactList(array($this->smartyvar['campaign_id_list']));

        $this->smartyvar['campaign_target'] = $list[0]['name'];

        $this->smartyvar['campaign_sms_content'] = $this->context->cookie->campaign_sms_content;

        $this->smartyvar['campaign_date'] = $this->context->cookie->campaign_date;

        if (!preg_match("`^[0-9]{2}/[0-9]{2}/[0-9]{4}`iUs", $this->smartyvar['campaign_date'])) {
            $this->smartyvar['campaign_date'] = $this->l('Now');
        }

        $this->smartyvar['campaign_channel'] = $this->context->cookie->campaign_channel;
        if ($this->smartyvar['campaign_channel'] == 'mail') {
            $this->smartyvar['campaign_channel_text'] = 'E-mail';
        } else {
            $this->smartyvar['campaign_channel_text'] = 'SMS';
        }

        if (isset($this->context->cookie->campaign_sender)) {
            $this->smartyvar['campaign_sender'] = $this->context->cookie->campaign_sender;
        }

        if (isset($this->context->cookie->campaign_replyto)) {
            $this->smartyvar['campaign_replyto'] = $this->context->cookie->campaign_replyto;
        }

        if (isset($this->context->cookie->campaign_subject)) {
            $this->smartyvar['campaign_subject'] = $this->context->cookie->campaign_subject;
        }
    }

    public function dgoCancelCampaign()
    {
        $id_campaign = (int) Tools::getValue('id_campaign');

        if (!empty($id_campaign)) {
            $this->api->cancelCampaign($id_campaign);
            if ($this->api->error) {
                $this->smartyvar['errors'] = $this->l('Error when deleting:').' "'.$this->api->error[0].'"';

                return;
            }
        }

        $token = Tools::getAdminToken(
            'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
            (int) $this->context->cookie->id_employee
        );
        Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=campaigns');
    }

    public function dgoNotifications()
    {
        unset($this->context->cookie->notification_type);
        unset($this->context->cookie->notification_event);
        unset($this->context->cookie->notification_delay_cart_abandonment);
        unset($this->context->cookie->notification_sms_content);
        unset($this->context->cookie->notification_channel);
        unset($this->context->cookie->notification_sender);
        unset($this->context->cookie->notification_replyto);
        unset($this->context->cookie->notification_subject);
        unset($this->context->cookie->notification_id_template);
        unset($this->context->cookie->type_notification_action);
        unset($this->context->cookie->administrator_email);
        unset($this->context->cookie->administrator_sms);

        $this->smartyvar['total_results'] = $this->dgo_model->getTotalNotifications();
        $this->pagination('notifications');

        $this->smartyvar['notification_list'] =
        $this->dgo_model->getNotifications($this->pagination, $this->smartyvar['p']);

        foreach ($this->smartyvar['notification_list'] as &$notif) {
            if ($notif['channel'] == 'mail') {
                $notif['channel_text'] = 'E-mail';
            } else {
                $notif['channel_text'] = 'SMS';
            }

            switch ($notif['prestashop_event']) {
                case 'hook_customer_add':
                    $notif['prestashop_event'] = $this->l('Creation of a customer account');
                    break;

                case 'hook_order_add':
                    $notif['prestashop_event'] = $this->l('New order on the shop');
                    break;

                case 'hook_return_ask':
                    $notif['prestashop_event'] = $this->l('Ask for product return');
                    break;

                case 'hook_cart_abandonment':
                    $notif['prestashop_event'] = $this->l('Cart abandonment');
                    break;
            }

            if ($notif['recipient_type'] == 'admin') {
                $notif['recipient_type'] = $this->l('Shop Administrator');

                if ($notif['channel'] == 'mail') {
                    $notif['contact'] = $notif['administrator_email'];
                } else {
                    $notif['contact'] = $notif['administrator_sms'];
                }
            } else {
                $notif['recipient_type'] = $this->l('Shop Customer');
                $notif['contact'] = $this->l('Customer contacts');
            }
        }
    }

    public function dgoNotificationStep1()
    {
        if (Tools::getIsset('modif_id_notification')) {
            $this->context->cookie->id_notification = (int) Tools::getValue('modif_id_notification');
            $this->context->cookie->type_notification_action = 'modif';

            $notification = $this->dgo_model->getNotification($this->context->cookie->id_notification);

            $this->context->cookie->notification_channel = $notification['channel'];
            $this->context->cookie->notification_type = $notification['recipient_type'];
            $this->context->cookie->notification_event = $notification['prestashop_event'];
            $this->context->cookie->notification_delay_cart_abandonment = $notification['delay_cart_abandonment'];
            $this->context->cookie->notification_sender = $notification['sender'];
            $this->context->cookie->notification_replyto = $notification['replyto'];
            $this->context->cookie->notification_subject = $notification['subject'];
            $this->context->cookie->id_campaign_digitaleo = $notification['id_campaign_digitaleo'];
            $this->context->cookie->administrator_email = $notification['administrator_email'];
            $this->context->cookie->administrator_sms = $notification['administrator_sms'];

            if ($notification['channel'] == 'sms') {
                $this->context->cookie->notification_sms_content = $notification['content'];
            } else {
                $this->context->cookie->notification_id_template = $notification['id_template'];
            }
        }

        $this->smartyvar['notification_type'] = $this->context->cookie->notification_type;

        if (empty($this->context->cookie->administrator_email)) {
            $this->context->cookie->administrator_email = Configuration::get('PS_SHOP_EMAIL');
        }

        $this->smartyvar['administrator_email'] = $this->context->cookie->administrator_email;
        $this->smartyvar['administrator_sms'] = $this->context->cookie->administrator_sms;
    }

    public function dgoNotificationStep2()
    {
        $this->smartyvar['delay_cart_abandonment'] = $this->context->cookie->notification_delay_cart_abandonment;
        $this->smartyvar['notification_event'] = $this->context->cookie->notification_event;

        $this->smartyvar['notification_events'] = array(
            'hook_customer_add' => $this->l('Creation of a customer account'),
            'hook_order_add' => $this->l('New order on the shop'),
            'hook_return_ask' => $this->l('Ask for product return'),
            'hook_cart_abandonment' => $this->l('Cart abandonment'),
        );
    }

    public function dgoNotificationStep3()
    {
        if (Tools::getIsset('notification_event')) {
            $this->context->cookie->notification_event = Tools::getValue('notification_event');
        }
        if (Tools::getIsset('delay_cart_abandonment')) {
            $this->context->cookie->notification_delay_cart_abandonment =
                Tools::getValue('delay_cart_abandonment');
        }

        $this->smartyvar['notification_channel'] = $this->context->cookie->notification_channel;
    }

    public function dgoNotificationStep4()
    {
        $this->smartyvar['notification_channel'] = $this->context->cookie->notification_channel;

        $this->smartyvar['notification_sms_content'] = $this->context->cookie->notification_sms_content;

        $this->smartyvar['notification_id_template'] = $this->context->cookie->notification_id_template;
        $this->smartyvar['notification_sender'] = $this->context->cookie->notification_sender;
        $this->smartyvar['notification_replyto'] = $this->context->cookie->notification_replyto;
        $this->smartyvar['notification_subject'] = $this->context->cookie->notification_subject;

        $this->smartyvar['templates'] = $this->api->getTemplates();
    }

    public function dgoNotificationStep5()
    {
        $this->smartyvar['notification_channel'] = $this->context->cookie->notification_channel;
        $this->smartyvar['dgo_email'] = $this->context->cookie->dgo_email;
    }

    public function dgoNotificationStep6()
    {
        $this->smartyvar['notification_type'] = $this->context->cookie->notification_type;
        $this->smartyvar['notification_event'] = $this->context->cookie->notification_event;

        $this->smartyvar['notification_sms_content'] = $this->context->cookie->notification_sms_content;
        $this->smartyvar['notification_channel'] = $this->context->cookie->notification_channel;
        if ($this->smartyvar['notification_channel'] == 'mail') {
            $this->smartyvar['notification_channel_text'] = 'E-mail';
        } else {
            $this->smartyvar['notification_channel_text'] = 'SMS';
        }

        if (isset($this->context->cookie->notification_sender)) {
            $this->smartyvar['notification_sender'] = $this->context->cookie->notification_sender;
        }

        if (isset($this->context->cookie->notification_replyto)) {
            $this->smartyvar['notification_replyto'] = $this->context->cookie->notification_replyto;
        }

        if (isset($this->context->cookie->notification_subject)) {
            $this->smartyvar['notification_subject'] = $this->context->cookie->notification_subject;
        }

        switch ($this->smartyvar['notification_event']) {
            case 'hook_customer_add':
                $this->smartyvar['notification_event'] = $this->l('Creation of a customer account');
                break;

            case 'hook_order_add':
                $this->smartyvar['notification_event'] = $this->l('New order on the shop');
                break;

            case 'hook_return_ask':
                $this->smartyvar['notification_event'] = $this->l('Ask for product return');
                break;

            case 'hook_cart_abandonment':
                $this->smartyvar['notification_event'] = $this->l('Cart abandonment');
                break;
        }

        if ($this->smartyvar['notification_type'] == 'admin') {
            $this->smartyvar['notification_type'] = $this->l('Shop Administrator');
        } else {
            $this->smartyvar['notification_type'] = $this->l('Shop Customer');
        }
    }

    public function dgoSegmentEdit()
    {
        if ($this->dgo_model->editSegment()) {
            $token = Tools::getAdminToken(
                'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
                (int) $this->context->cookie->id_employee
            );
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=segments');
        }
    }

    public function ajaxGetSegmentFilter()
    {
        if (Tools::getValue('choice') == 'countries') {
            $countries = Country::getCountries($this->context->cookie->id_lang, true);
            $this->context->smarty->assign(array('countries' => $countries));
        }

        if (Tools::getValue('choice') == 'groups') {
            $groups = Group::getGroups($this->context->cookie->id_lang);
            $this->context->smarty->assign(array('groups' => $groups));
        }
        $this->context->smarty->assign(array(
            'segment_countries' => '',
            'segment_age_from' => '',
            'segment_age_to' => '',
            'segment_registration_from' => '',
            'segment_registration_to' => '',
            'segment_newsletter' => 2,
            'segment_optin' => 2,
            'segment_genre' => '',
            'segment_orders_from' => false,
            'segment_orders_to' => false,
            'segment_groups' => '',
        ));

        echo $this->context->smarty->fetch(dirname(__FILE__).'/../../views/templates/admin/segments_filters.tpl');
    }

    public function ajaxSmsTest()
    {
        $mobile_phone = Tools::getValue('sms_test_number');
        $return = array('error' => false);

        if (isset($this->context->cookie->campaign_sms_content)) {
            $sms_content = $this->context->cookie->campaign_sms_content;
        } elseif (isset($this->context->cookie->notification_sms_content)) {
            $sms_content = $this->context->cookie->notification_sms_content;
        }

        $this->api->sendSMS($mobile_phone, $sms_content, 'Firstname', 'Lastname');
        if ($this->api->error) {
            $return['error'] = $this->l('Error sending SMS:').' "'.$this->api->error[0].'"';
        }

        echo Tools::jsonEncode($return);
    }

    public function ajaxEmailTest()
    {
        $email_test = Tools::getValue('email_test');
        $return = array('error' => false);

        $fields = array();

        if (isset($this->context->cookie->campaign_id_template)) {
            $fields['sender'] = $this->context->cookie->campaign_sender;
            $fields['replyto'] = $this->context->cookie->campaign_replyto;
            $fields['subject'] = $this->context->cookie->campaign_subject;
            $fields['id_template'] = $this->context->cookie->campaign_id_template;

            $template = $this->api->getTemplates($this->context->cookie->campaign_id_template);
            $fields['content'] = preg_replace("`(<\?php.*\?>)`iUs", '', $template[0]['templateJson']['email']['html']);
        } elseif (isset($this->context->cookie->notification_id_template)) {
            $fields['sender'] = $this->context->cookie->notification_sender;
            $fields['replyto'] = $this->context->cookie->notification_replyto;
            $fields['subject'] = $this->context->cookie->notification_subject;
            $fields['id_template'] = $this->context->cookie->notification_id_template;

            $template = $this->api->getTemplates($this->context->cookie->notification_id_template);
            $fields['content'] = preg_replace("`(<\?php.*\?>)`iUs", '', $template[0]['templateJson']['email']['html']);
        }

        $this->api->sendEmail($email_test, $fields, 'Firstname', 'Lastname');
        if ($this->api->error) {
            $return['error'] = $this->l('Error sending e-mail:').' "'.$this->api->error[0].'"';
        }

        echo Tools::jsonEncode($return);
    }

    public function ajaxGetTemplates()
    {
        $templates = $this->api->getTemplates(0, 20, Tools::getValue('offset'));

        $this->context->smarty->assign(array('templates' => $templates));

        $this->context->smarty->display(dirname(__FILE__).'/views/templates/admin/template_list.tpl');
    }

    public function postProcessCampaignStep1()
    {
        if (Tools::getValue('campaign_name')) {
            $this->context->cookie->campaign_name = Tools::getValue('campaign_name');
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=campaign_step2');
        } else {
            $this->smartyvar['errors'] = $this->l('You must enter a name');
        }
    }

    public function postProcessCampaignStep2()
    {
        if (Tools::getValue('campaign_id_list')) {
            $this->context->cookie->campaign_id_list = Tools::getValue('campaign_id_list');
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=campaign_step3');
        } else {
            $this->smartyvar['errors'] = $this->l('You must choose a recipient list');
        }
    }

    public function postProcessCampaignStep3()
    {
        if (Tools::getValue('campaign_channel')) {
            $this->context->cookie->campaign_channel = Tools::getValue('campaign_channel');
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=campaign_step4');
        } else {
            $this->smartyvar['errors'] = $this->l('You must choose the means of communication');
        }
    }

    public function postProcessCampaignStep4()
    {
        if ($this->context->cookie->campaign_channel == 'sms') {
            if (Tools::getValue('campaign_sms_content')) {
                $this->context->cookie->campaign_sms_content = Tools::getValue('campaign_sms_content');
            } else {
                $this->smartyvar['errors'] = $this->l('You must enter the SMS content');

                return;
            }
        } else {
            if (Tools::getValue('campaign_sender')
                && Tools::getValue('campaign_replyto')
                && Tools::getValue('campaign_subject')) {
                $this->context->cookie->campaign_sender = Tools::getValue('campaign_sender');
                $this->context->cookie->campaign_replyto = Tools::getValue('campaign_replyto');
                $this->context->cookie->campaign_subject = Tools::getValue('campaign_subject');
            } else {
                $this->smartyvar['errors'] = $this->l('You must complete all fields');

                return;
            }
            if (!Validate::isEmail($this->context->cookie->campaign_replyto)) {
                $this->smartyvar['errors'] = $this->l('The e-mail is not valid');

                return;
            }
            if (Tools::getValue('campaign_id_template')) {
                $this->context->cookie->campaign_id_template = Tools::getValue('campaign_id_template');
            } else {
                $this->smartyvar['errors'] = $this->l('You must choose a template');

                return;
            }
        }
        Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=campaign_step5');
    }

    public function postProcessCampaignStep7()
    {
        $fields = array();
        $fields['type_digitaleo'] = 'STANDARD';
        $fields['name'] = $this->context->cookie->campaign_name;
        $fields['id_list'] = $this->context->cookie->campaign_id_list;
        $fields['sms_content'] = $this->context->cookie->campaign_sms_content;
        $fields['date'] = $this->context->cookie->campaign_date;
        $fields['channel'] = $this->context->cookie->campaign_channel;
        $fields['sender'] = $this->context->cookie->campaign_sender;
        $fields['replyto'] = $this->context->cookie->campaign_replyto;
        $fields['subject'] = $this->context->cookie->campaign_subject;
        $fields['id_template'] = $this->context->cookie->campaign_id_template;

        if (isset($this->context->cookie->campaign_id_template)) {
            $template = $this->api->getTemplates($this->context->cookie->campaign_id_template);
            $fields['html'] = preg_replace("`(<\?php.*\?>)`iUs", '', $template[0]['templateJson']['email']['html']);
        }

        if (preg_match('`^[0-9]{2}/[0-9]{2}/[0-9]{4}`iUs', $fields['date'])) {
            list($date, $hour) = explode(' ', $fields['date']);
            list($d, $m, $y) = explode('/', $date);
            $fields['date_iso'] = $y.'-'.$m.'-'.$d.' '.$hour;
        } else {
            $fields['date_iso'] = date('Y-m-d H:i:s');
        }

        if (isset($this->context->cookie->type_campaign_action)
            && $this->context->cookie->type_campaign_action == 'modif') {
            // Modification : On annule la précédente
            $this->api->cancelCampaign($this->context->cookie->id_campaign_digitaleo);
            if ($this->api->error) {
                $this->smartyvar['errors'] = $this->l('Error during the campaign modification:')
                    .' "'.$this->api->error[0].'"';

                return;
            }
            $this->dgo_model->deleteCampaign($this->context->cookie->id_campaign);
        }

        // Création
        $retour = $this->api->createCampaign($fields);

        if ($this->api->error) {
            $this->smartyvar['errors'] = $this->l('Error during creating the campaign:')
                .' "'.$this->api->error[0].'"';

            return;
        }

        if (isset($retour['list'][0]['id']) && !empty($retour['list'][0]['id'])) {
            $id_campaign_digitaleo = $retour['list'][0]['id'];

            $this->dgo_model->createCampaign($id_campaign_digitaleo, $fields);

            $token = Tools::getAdminToken(
                'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
                (int) $this->context->cookie->id_employee
            );
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=campaigns');
        }
    }

    public function postProcessNotificationStep1()
    {
        if (Tools::getValue('notification_type')) {
            $this->context->cookie->notification_type = Tools::getValue('notification_type');
        } else {
            $this->smartyvar['errors'] = $this->l('You must choose the recipient');

            return;
        }

        if ($this->context->cookie->notification_type == 'admin') {
            if (Tools::getValue('administrator_email') && Tools::getValue('administrator_sms')) {
                $this->context->cookie->administrator_email = Tools::getValue('administrator_email');
                $this->context->cookie->administrator_sms = Tools::getValue('administrator_sms');
            } else {
                $this->smartyvar['errors'] = $this->l('You must enter your email and phone number');

                return;
            }
            if (!Validate::isEmail($this->context->cookie->administrator_email)) {
                $this->smartyvar['errors'] = $this->l('The e-mail is not valid');

                return;
            }
        }
        Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=notification_step2');
    }

    public function postProcessNotificationStep3()
    {
        if (Tools::getValue('notification_channel')) {
            $this->context->cookie->notification_channel = Tools::getValue('notification_channel');
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=notification_step4');
        } else {
            $this->smartyvar['errors'] = $this->l('You must choose the means of communication');
        }
    }

    public function postProcessNotificationStep4()
    {
        if ($this->context->cookie->notification_channel == 'sms') {
            if (Tools::getValue('notification_sms_content')) {
                $this->context->cookie->notification_sms_content = Tools::getValue('notification_sms_content');
            } else {
                $this->smartyvar['errors'] = $this->l('You must enter the SMS content');

                return;
            }
        } else {
            if (Tools::getValue('notification_sender')
                && Tools::getValue('notification_replyto')
                && Tools::getValue('notification_subject')) {
                $this->context->cookie->notification_sender = Tools::getValue('notification_sender');
                $this->context->cookie->notification_replyto = Tools::getValue('notification_replyto');
                $this->context->cookie->notification_subject = Tools::getValue('notification_subject');
            } else {
                $this->smartyvar['errors'] = $this->l('You must complete all fields');

                return;
            }
            if (!Validate::isEmail($this->context->cookie->notification_replyto)) {
                $this->smartyvar['errors'] = $this->l('The e-mail is not valid');

                return;
            }
            if (Tools::getValue('notification_id_template')) {
                $this->context->cookie->notification_id_template = Tools::getValue('notification_id_template');
            } else {
                $this->smartyvar['errors'] = $this->l('You must choose a template');

                return;
            }
        }
        Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=notification_step5');
    }

    public function postProcessNotificationStep6()
    {
        $fields = array();
        $fields['type_digitaleo'] = 'TRANSACTIONAL';
        $fields['type'] = $this->context->cookie->notification_type;
        $fields['event'] = $this->context->cookie->notification_event;
        $fields['delay_cart_abandonment'] = $this->context->cookie->notification_delay_cart_abandonment;
        $fields['sms_content'] = $this->context->cookie->notification_sms_content;
        $fields['channel'] = $this->context->cookie->notification_channel;
        $fields['sender'] = $this->context->cookie->notification_sender;
        $fields['replyto'] = $this->context->cookie->notification_replyto;
        $fields['subject'] = $this->context->cookie->notification_subject;
        $fields['id_template'] = $this->context->cookie->notification_id_template;
        $fields['administrator_email'] = $this->context->cookie->administrator_email;
        $fields['administrator_sms'] = $this->context->cookie->administrator_sms;
        $fields['date_iso'] = date('Y-m-d H:i:s');
        $fields['name'] = 'Notification '.$fields['date_iso'];

        if (isset($this->context->cookie->notification_id_template)) {
            $template = $this->api->getTemplates($this->context->cookie->notification_id_template);
            $fields['html'] = preg_replace("`(<\?php.*\?>)`iUs", '', $template[0]['templateJson']['email']['html']);
        }

        if (isset($this->context->cookie->type_notification_action)
            && $this->context->cookie->type_notification_action == 'modif') {
            // Modification : On annule la précédente
            $this->api->cancelCampaign($this->context->cookie->id_campaign_digitaleo);
            if ($this->api->error) {
                $this->smartyvar['errors'] =
                    $this->l('Error during the notification modification:').' "'.$this->api->error[0].'"';

                return;
            }
            $this->dgo_model->deleteNotification($this->context->cookie->id_notification);
        }

        // Création de la campagne
        $retour = $this->api->createCampaign($fields);
        if ($this->api->error) {
            $this->smartyvar['errors'] =
                $this->l('Error during creating the notification:').' "'.$this->api->error[0].'"';

            return;
        }

        if (isset($retour['list'][0]['id']) && !empty($retour['list'][0]['id'])) {
            $id_campaign_digitaleo = $retour['list'][0]['id'];

            $this->dgo_model->createNotification($id_campaign_digitaleo, $fields);

            $token = Tools::getAdminToken(
                'AdminDigitaleo'.(int) Tab::getIdFromClassName('AdminDigitaleo').
                (int) $this->context->cookie->id_employee
            );
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=notifications');
        }
    }

    public function postProcessSyncStep2()
    {
        if (Tools::getValue('sync_id_list') || Tools::getValue('sync_new_list')) {
            $this->context->cookie->sync_id_list = Tools::getValue('sync_id_list');
            $this->context->cookie->sync_new_list = Tools::getValue('sync_new_list');
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=sync_step3');
        } else {
            $this->smartyvar['errors'] = $this->l('Choose a contact list or create a new one');
        }
    }

    public function postProcessSyncStep3()
    {
        if (Tools::getValue('sync_auto') !== '') {
            $this->context->cookie->sync_auto = Tools::getValue('sync_auto');
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=sync_step4');
        } else {
            $this->smartyvar['errors'] = $this->l('You must choose a synchronization mode');
        }
    }

    public function postProcessSegmentStep1()
    {
        if (Tools::getValue('segment_name')) {
            $this->context->cookie->segment_name = Tools::getValue('segment_name');
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=segment_step2');
        } else {
            $this->smartyvar['errors'] = $this->l('You must enter a name');
        }
    }

    public function postProcessSegmentStep2()
    {
        // On commence par vide les anciennes valeurs
        unset($this->context->cookie->segment_countries);
        unset($this->context->cookie->segment_age_from);
        unset($this->context->cookie->segment_age_to);
        unset($this->context->cookie->segment_registration_from);
        unset($this->context->cookie->segment_registration_to);
        unset($this->context->cookie->segment_newsletter);
        unset($this->context->cookie->segment_optin);
        unset($this->context->cookie->segment_genre);
        unset($this->context->cookie->segment_orders_from);
        unset($this->context->cookie->segment_orders_to);
        unset($this->context->cookie->segment_groups);

        if (Tools::getIsset('segment_countries')
            || Tools::getIsset('segment_age_from')
            || Tools::getIsset('segment_age_to')
            || Tools::getIsset('segment_registration_from')
            || Tools::getIsset('segment_registration_to')
            || Tools::getIsset('segment_newsletter')
            || Tools::getIsset('segment_optin')
            || Tools::getIsset('segment_genre')
            || Tools::getIsset('segment_orders_from')
            || Tools::getIsset('segment_orders_to')
            || Tools::getIsset('segment_groups')) {
            if (Tools::getIsset('segment_countries')) {
                $this->context->cookie->segment_countries = implode(',', Tools::getValue('segment_countries'));
            }
            if (Tools::getIsset('segment_age_from')) {
                $this->context->cookie->segment_age_from = Tools::getValue('segment_age_from');
            }
            if (Tools::getIsset('segment_age_to')) {
                $this->context->cookie->segment_age_to = Tools::getValue('segment_age_to');
            }
            if (Tools::getIsset('segment_registration_from')) {
                $this->context->cookie->segment_registration_from = Tools::getValue('segment_registration_from');
            }
            if (Tools::getIsset('segment_registration_to')) {
                $this->context->cookie->segment_registration_to = Tools::getValue('segment_registration_to');
            }
            if (Tools::getIsset('segment_newsletter')) {
                $this->context->cookie->segment_newsletter = Tools::getValue('segment_newsletter');
            }
            if (Tools::getIsset('segment_optin')) {
                $this->context->cookie->segment_optin = Tools::getValue('segment_optin');
            }
            if (Tools::getIsset('segment_genre')) {
                $this->context->cookie->segment_genre = Tools::getValue('segment_genre');
            }
            if (Tools::getIsset('segment_orders_from')) {
                $this->context->cookie->segment_orders_from = Tools::getValue('segment_orders_from');
            }
            if (Tools::getIsset('segment_orders_to')) {
                $this->context->cookie->segment_orders_to = Tools::getValue('segment_orders_to');
            }
            if (Tools::getIsset('segment_groups')) {
                $this->context->cookie->segment_groups = implode(',', Tools::getValue('segment_groups'));
            }
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$this->token.'&action=segment_step3');
        } else {
            $this->smartyvar['errors'] = $this->l('You must add at least one filter');
        }
    }

    public function postProcess()
    {
        $action = Tools::toCamelCase(Tools::getValue('action'), true);
        if (Tools::getIsset('submit'.$action)) {
            $functionPostProcess = 'postProcess'.$action;
            if (method_exists($this, $functionPostProcess)) {
                $this->$functionPostProcess();
            }
        }

        if (Tools::getValue('action') == 'logout') {
            // Déconnexion
            self::logout();
            Tools::redirectAdmin($this->url_config);
        }

        // Suppression notification
        if (Tools::isSubmit('delete_notification')) {
            $id_notification = (int) Tools::getValue('id_notification');

            if (!empty($id_notification)) {
                $notif = $this->dgo_model->getNotification($id_notification);
                $this->api->cancelCampaign($notif['id_campaign_digitaleo']);
                if ($this->api->error) {
                    $this->smartyvar['errors'] = $this->l('Error when deleting:').' "'.$this->api->error[0].'"';

                    return;
                }

                $this->dgo_model->deleteNotification($id_notification);
            }

            $token = Tools::getAdminToken(
                'AdminDigitaleo'.(int)Tab::getIdFromClassName('AdminDigitaleo').
                (int)$this->context->cookie->id_employee
            );
            Tools::redirectAdmin('index.php?tab=AdminDigitaleo&token='.$token.'&action=notifications');
        }
    }

    public static function logout()
    {
        Configuration::updateValue('DIGITALEO_LOGIN', '', false, 0, 0);
        Configuration::updateValue('DIGITALEO_PASSWORD', '', false, 0, 0);
        Configuration::updateValue('DIGITALEO_USER_ACCESS_TOKEN', '', false, 0, 0);
    }
}
