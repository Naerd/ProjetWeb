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

class DigitaleoAPI
{
    protected $partner = false;
    protected $username = '';
    protected $password = '';
    protected $userId = '';
    protected $token_partner = '';
    protected $token_user = '';
    public $error = array();
    public $debug = array();
    protected $httpClient;

    protected static $client_id = '7af5e49f08e3dcdf8a878ed0eadfe9f3';
    protected static $client_secret = '56370f79fa02ba6aa9c58d2bfade972d';

    public function __construct($username = false, $password = false)
    {
        include_once 'DigitaleoApiPhp.php';
        include_once 'DigitaleoError.php';

        $this->username = $username;
        $this->password = $password;
        $this->token_partner = Configuration::get('DIGITALEO_API_ACCESS_TOKEN', null, 0, 0);
        $this->token_user = Configuration::get('DIGITALEO_USER_ACCESS_TOKEN', null, 0, 0);

        $this->httpClient = new \DigitaleoApiPhp();
        $this->httpClient->setBaseUrl('https://oauth.messengeo.net/');

        $this->dgoError = new DigitaleoError();

        // Obtention du token
        if (!$this->token_partner) {
            $this->getPartnerToken();
        }
    }

    public function isPartnerAuthenticate()
    {
        return $this->token_partner;
    }

    public function isUserAuthenticate()
    {
        if (!$this->token_user) {
            $this->getUserToken();
        }

        return $this->token_user;
    }

    protected function setClientCredentials()
    {
        $this->httpClient->setOauthClientCredentials(
            'https://oauth.messengeo.net/token',
            self::$client_id,
            self::$client_secret
        );
    }

    protected function getPartnerToken()
    {
        $this->setClientCredentials();

        $credential = $this->httpClient->callGetToken();

        $this->token_partner = $credential->token;
        Configuration::updateValue('DIGITALEO_API_ACCESS_TOKEN', $this->token_partner, false, 0, 0);

        return $this->token_partner;
    }

    protected function setPasswordCredentials()
    {
        $this->httpClient->setOauthPasswordCredentials(
            'https://oauth.messengeo.net/token',
            self::$client_id,
            self::$client_secret,
            $this->username,
            $this->password
        );
    }

    protected function getUserToken()
    {
        $this->setPasswordCredentials();

        $credential = $this->httpClient->callGetToken();
        $this->token_user = $credential->token;
        Configuration::updateValue('DIGITALEO_USER_ACCESS_TOKEN', $this->token_user, false, 0, 0);

        return $this->token_user;
    }

    protected function call($resource, $params = array(), $type = 'get')
    {
        if ($this->token_user && $resource != "industries") {
            $this->setPasswordCredentials();
        } else {
            $this->setClientCredentials();
        }

        if ($type == 'get') {
            $return = $this->httpClient->callGet($resource, $params);
        } elseif ($type == 'post') {
            $return = $this->httpClient->callPost($resource, $params);
        } elseif ($type == 'put') {
            $return = $this->httpClient->callPut($resource, $params);
        } elseif ($type == 'delete') {
            $return = $this->httpClient->callDelete($resource, $params);
        }

        $return = Tools::jsonDecode($return, true);

        if (isset($return['status']) && (int) $return['status'] >= 400) {
            $this->dgoError->add($this->httpClient->getUri(), $params, $return, $this->httpClient->getResponseCode());

            // récupération des messages d'erreurs
            if (!empty($return['messages'])) {
                foreach ($return['messages'] as $msg) {
                    $this->error[] = $msg['details'];
                }
            } elseif (!empty($return['message'])) {
                $this->error[] = $return['message'];
            } elseif (!empty($return['error_description'])) {
                $this->error[$return['error']] = $return['error_description'];
            }
        }

        return $return;
    }

    public function getRestrictions($type)
    {
        $this->debug[] = 'getRestrictions';
        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/rest/');

        $return = $this->call('restrictions?type='.$type);

        return $return['list'][0];
    }

    public function getContracts()
    {
        $this->debug[] = 'getContracts';
        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/rest/');

        $return = $this->call('contracts?properties=DEFAULT,name,type,industryName');

        return $return;
    }

    public function getUser($login)
    {
        $users = $this->getUsers();

        foreach ($users['list'] as $user) {
            if ($user['login'] == $login) {
                return $user;
            }
        }

        return false;
    }

    public function getUsers()
    {
        $this->debug[] = 'getUsers';

        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/rest/');

        $return = $this->call('users');

        return $return;
    }

    public function getIndustries()
    {
        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/rest/');

        $result = $this->call('industries');

        return $result['list'];
    }

    public function getContactList($ids = 0)
    {
        $this->debug[] = 'getContactList';

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        $params = array();
        $sort = '';
        if (!empty($ids)) {
            $params['id'] = implode(',', $ids);
        } else {
            $sort = '?sort=id%20DESC';
        }

        $result = $this->call('lists'.$sort, $params);

        return $result['list'];
    }

    public function createContactList($name)
    {
        $this->debug[] = 'createContactList';

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        $params = array('name' => $name);
        $result = $this->call('lists', $params, 'post');

        return $result['list'][0]['id'];
    }

    public function getIdContactByEmail($email)
    {
        $this->debug[] = 'getContact';

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        $params = array('email' => $email);
        $result = $this->call('contacts', $params);

        return isset($result['list'][0]['id']) ? $result['list'][0]['id'] : null;
    }

    public function createContacts($contacts)
    {
        $this->debug[] = 'createContacts';

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        $params = array();
        $key = 0;
        foreach ($contacts as &$contact) {
            if (isset($contact['mobile']) && preg_match('`^(336|337)$`iUs', $contact['mobile'])) {
                $contact['mobile'] = '+'.$contact['mobile'];
            } elseif (isset($contact['mobile'])) {
                if ($contact['locale'] == 'es') {
                    $contact['mobile'] = preg_replace('`^(0)`iUs', '+34', $contact['mobile']);
                }
            }
            foreach ($contact as $field => $value) {
                $params['contacts['.$key.']['.$field.']'] = $value;
            }

            ++$key;
        }

        $result = $this->call('contacts', $params, 'post');

        // TODO : Gestion des erreurs
        if (isset($result['status']) && (int) $result['status'] == 417) {
            // E-mail incorrect
            foreach ($contacts as $k => $c) {
                if (stripos($result['message'], $c['email']) !== false) {
                    unset($contacts[$k]);

                    return $this->createContacts($contacts);
                }
            }
        }

        if (isset($result['list'])) {
            return $result['list'];
        }

        return false;
    }

    public function updateContact($id_contact, $data)
    {
        $this->debug[] = 'updateContact';

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        if (isset($data['mobile']) && preg_match('`^(336|337)$`iUs', $data['mobile'])) {
            $data['mobile'] = '+'.$data['mobile'];
        } elseif (isset($data['mobile'])) {
            if ($data['locale'] == 'es') {
                $data['mobile'] = preg_replace('`^(0)`iUs', '+34', $data['mobile']);
            }
        }

        $metaData = array();
        foreach ($data as $k => $d) {
            $metaData[] = '"'.$k.'":"'.$d.'"';
        }

        $params = array(
            'id' => $id_contact,
            'metaData' => '{'.implode(',', $metaData).'}',
        );

        $result = $this->call('contacts', $params, 'put');

        return (int) $result['count'];
    }

    public function deleteContacts($contacts_ids)
    {
        $this->debug[] = 'deleteContacts';

        if (!is_array($contacts_ids)) {
            $contacts_ids = array($contacts_ids);
        }

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        $params = array('id' => implode(',', $contacts_ids));

        $result = $this->call('contacts', $params, 'delete');

        return (int) $result['count'];
    }

    public function addContactsToList($id_list, $contacts_ids)
    {
        $this->debug[] = 'addContactsToList';

        if (!is_array($contacts_ids)) {
            $contacts_ids = array($contacts_ids);
        }

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        $params = array(
            'id' => $id_list,
            'metaData' => '{"contactIds":"'.implode(',', $contacts_ids).'"}',
        );

        $result = $this->call('lists?action=contactadd', $params, 'post');

        return (int) $result['count'];
    }

    public function removeContactsFromList($id_list, $contacts_ids)
    {
        $this->debug[] = 'removeContactsFromList';

        if (!is_array($contacts_ids)) {
            $contacts_ids = array($contacts_ids);
        }

        $this->httpClient->setBaseUrl('https://contacts.messengeo.net/rest/');

        $params = array(
            'id' => $id_list,
            'metaData' => '{"contactIds":"'.implode(',', $contacts_ids).'"}',
        );

        $result = $this->call('lists?action=contactremove', $params, 'post');

        return (int) $result['count'];
    }

    public function createFreeTrial($email, $mobile, $company, $name, $password, $industryName, $iso_code)
    {
        $this->debug[] = 'createFreeTrial';
        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/rest/');
        $params = array(
            'email' => $email,
            'locale' => ($iso_code == 'es') ? 'es_ES' : 'fr_FR',
            'mobile' => $mobile,
            'company' => $company,
            'name' => $name,
            'password' => $password,
            'industryName' => $industryName,
            'source' => 'Module Prestashop',
        );

        $return = $this->call('freetrialaccount', $params, 'post');

        if (!$this->error) {
            $params_lead = $params;

            unset($params_lead['password']);

            $this->call('lead', $params_lead, 'post');

            $this->userId = $return['userId'];
        }

        return $this->userId;
    }

    public function sendCode($userId = false)
    {
        if ($userId) {
            $this->userId = $userId;
        }

        $this->debug[] = 'sendCode';
        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/rest/');
        $params = array(
            'userId' => $this->userId,
        );

        $this->call('usermobilecheck?action=sendCode', $params, 'post');
    }

    public function checkcode($userId, $code)
    {
        $this->debug[] = 'checkcode';
        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/rest/');
        $params = array(
            'userId' => $userId,
            'code' => $code,
        );

        $this->call('usermobilecheck?action=checkcode', $params, 'post');
    }

    public function sendSMS($mobile_phone, $content, $firstname = '', $lastname = '')
    {
        $this->httpClient->setBaseUrl('https://api.messengeo.net/rest/');

        $params = array(
            'media' => 'SMS',
            'text' => $this->escapeSms($content),
            'contacts[0][recipient]' => $mobile_phone,
            'contacts[0][firstName]' => $firstname,
            'contacts[0][lastName]' => $lastname,
        );

        return $this->call('mailings', $params, 'post');
    }

    public function sendEmail($email_test, $fields, $firstname = '', $lastname = '')
    {
        $this->httpClient->setBaseUrl('https://api.messengeo.net/rest/');

        $params = array(
            'media' => 'EMAIL',
            'html' => $fields['content'],
            'sender' => $fields['sender'],
            'replyContact' => $fields['replyto'],
            'subject' => $fields['subject'],
            'contacts[0][recipient]' => $email_test,
            'contacts[0][firstName]' => $firstname,
            'contacts[0][lastName]' => $lastname,
        );

        return $this->call('mailings', $params, 'post');
    }

    public function getCampaigns($array_ids = array())
    {
        $this->httpClient->setBaseUrl('https://api.messengeo.net/rest/');

        $params = array();
        if (!empty($array_ids)) {
            $params['id'] = implode(',', $array_ids);
        }

        return $this->call('campaigns', $params);
    }

    public function cancelCampaign($id_campaign)
    {
        $this->httpClient->setBaseUrl('https://api.messengeo.net/rest/');

        $params = array('id' => $id_campaign);

        return $this->call('campaigns?action=cancel', $params, 'post');
    }

    public function getTemplates($id = 0, $limit = 20, $offset = 0)
    {
        $this->httpClient->setBaseUrl('https://baseo.messengeo.net/content/rest/');

        $params = array();

        $query = '&format=340x440&sort=id%20DESC&sources=MINE&limit='.$limit.'&offset='.$offset;

        if (!empty($id)) {
            $query = '&id='.$id;
        }

        $retour = $this->call('contents?mimeType=template/email'.$query, $params, 'get');

        return $retour['list'];
    }

    public function createCampaign($fields)
    {
        $this->httpClient->setBaseUrl('https://api.messengeo.net/rest/');

        if ($fields['channel'] == 'sms') {
            $priorities = 'SMS';
            $array_mailing = array(
                'text' => $this->escapeSms($fields['sms_content']),
                'media' => 'SMS',
            );
        } else {
            if ($fields['channel'] == 'mail') {
                $priorities = 'EMAIL';
                $array_mailing = array(
                    'html' => $fields['html'],
                    'subject' => $fields['subject'],
                    'sender' => $fields['sender'],
                    'replyContact' => $fields['replyto'],
                    'media' => 'EMAIL',
                );
            }
        }

        $steps = array(
            'mode' => 'prioritized',
            'priorities' => $priorities,
            'mailings' => array($array_mailing),
        );

        if ($fields['type_digitaleo'] == 'STANDARD') {
            $steps['date'] = $fields['date_iso'];
        }

        $params = array(
            'type' => $fields['type_digitaleo'],
            'name' => $fields['name'],
            'steps' => array($steps),
        );

        if ($fields['type_digitaleo'] == 'STANDARD') {
            $params['listId'] = $fields['id_list'];
        }

        return $this->call('campaigns?action=create', $params, 'post');
    }

    public function addCampaignContact($id_campaign, $contacts)
    {
        $this->httpClient->setBaseUrl('https://api.messengeo.net/rest/');

        $params = array(
            'campaignId' => $id_campaign,
            'contacts' => $contacts,
        );

        return $this->call('campaigncontacts?action=create', $params, 'post');
    }

    public function escapeSms($sms_content)
    {
        $table = array(
            'Š'=>'S', 'š'=>'s', 'Đ'=>'Dj', 'đ'=>'dj', 'Ž'=>'Z', 'ž'=>'z', 'Č'=>'C',
            'č'=>'c', 'Ć'=>'C', 'ć'=>'c', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A',
            'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E',
            'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O',
            'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a',
            'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e',
            'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
            'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o',
            'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
            'ÿ'=>'y', 'Ŕ'=>'R', 'ŕ'=>'r'
        );

        $sms_content = strtr($sms_content, $table);

        return $sms_content;
    }
}
