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

class DigitaleoModel
{
    protected $context;

    public function getSync($id_sync)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_sync_customers WHERE id_sync='.(int) $id_sync;

        return Db::getInstance()->getRow($sql);
    }

    public function getSyncCustomers($limit = 0, $num_page = 0)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_sync_customers 
                ORDER BY id_sync DESC';

        if (!empty($limit)) {
            $offset = ($num_page - 1) * $limit;
            $sql .= ' LIMIT '.(int)$offset.','.(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public function getTotalSyncCustomers()
    {
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'dgo_sync_customers ORDER BY auto DESC, active DESC';

        return Db::getInstance()->GetValue($sql);
    }

    public function getActiveAutoSync()
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_sync_customers WHERE auto=1 AND active=1';

        return Db::getInstance()->executeS($sql);
    }

    public function addSync($hook_prestashop, $id_segment, $id_target_digitaleo, $auto)
    {
        $sql = 'INSERT INTO '._DB_PREFIX_."dgo_sync_customers (hook_prestashop, id_segment, id_target_digitaleo,
                auto, active)
				VALUES ('" .pSQL($hook_prestashop)."', ".(int) $id_segment.', '.(int) $id_target_digitaleo.', '
                .(int) $auto.', 1)';
        Db::getInstance()->execute($sql);

        return Db::getInstance()->Insert_ID();
    }

    public function updateSync($id_sync, $hook_prestashop, $id_segment, $id_target_digitaleo, $auto)
    {
        $sql = 'UPDATE '._DB_PREFIX_."dgo_sync_customers SET
						hook_prestashop = '" .pSQL($hook_prestashop)."',
						id_segment = " .(int) $id_segment.',
						id_target_digitaleo = ' .(int) $id_target_digitaleo.',
						auto = ' .(int) $auto.'
				WHERE id_sync = ' .(int) $id_sync;
        Db::getInstance()->execute($sql);
    }

    public function deleteSync($id_sync)
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'dgo_sync_customers WHERE id_sync='.(int) $id_sync;
        Db::getInstance()->execute($sql);
    }

    public function setSyncActive($id_sync, $active)
    {
        $sql = 'UPDATE '._DB_PREFIX_.'dgo_sync_customers SET active='.(int) $active.' WHERE id_sync='.
            (int) $id_sync;
        Db::getInstance()->execute($sql);
    }

    // Retourn le nombre total de contacts à synchroniser
    public function getTotalSync($id_sync)
    {
        $nb_customers = $this->getCustomersToSync($id_sync, 0, 0, true);

        return $nb_customers;
    }

    public function getTotalCustomer()
    {
        return Db::getInstance()->getValue('SELECT COUNT(id_customer) FROM '._DB_PREFIX_.'customer
                WHERE active = 1 AND deleted = 0');
    }

    public function getTotalNewsletterRegistered()
    {
        return Db::getInstance()->getValue('SELECT COUNT(id_customer) FROM '._DB_PREFIX_.'customer
                WHERE active = 1 AND deleted = 0 AND newsletter = 1');
    }

    public function getCustomersToSync($id_sync, $num_start = 0, $limit = 0, $count = false, $id_customer = 0)
    {
        $sync = $this->getSync($id_sync);

        if (!$count) {
            $select = " id_customer, firstname, lastname, email, date_upd,
						(SELECT IF(phone_mobile IS NULL or phone_mobile = '', phone, phone_mobile)
						    FROM " ._DB_PREFIX_.'address
							WHERE id_customer = ' ._DB_PREFIX_.'customer.id_customer AND active = 1 AND deleted = 0
							ORDER BY id_address LIMIT 1) AS phone_mobile ';
        } else {
            $select = ' COUNT(DISTINCT id_customer) ';
        }

        $customers = false;
        if (empty($sync['id_segment'])) {
            if ($sync['hook_prestashop'] == 'hook_customers') {
                $sql = 'SELECT '.$select.'
						FROM ' ._DB_PREFIX_.'customer
						WHERE active = 1 AND deleted = 0 ' .(!empty($id_customer) ? ' AND id_customer='.
                        (int) $id_customer.' ' : '').'
						ORDER BY id_customer ';

                if (!$count) {
                    if (!empty($limit)) {
                        $sql .= 'LIMIT '.(int) $num_start.','.(int) $limit;
                    }

                    $customers = Db::getInstance()->executeS($sql);
                } else {
                    $customers = Db::getInstance()->getValue($sql);
                }
            } elseif ($sync['hook_prestashop'] == 'hook_newsletter') {
                $sql = 'SELECT '.$select.'
                    FROM ' ._DB_PREFIX_.'customer
                    WHERE active = 1 AND deleted = 0 AND newsletter=1 ' .(!empty($id_customer) ? ' AND id_customer='.
                        (int) $id_customer.' ' : '').'
                    ORDER BY id_customer ';

                if (!$count) {
                    if (!empty($limit)) {
                        $sql .= 'LIMIT '.(int) $num_start.','.(int) $limit;
                    }

                    $customers = Db::getInstance()->executeS($sql);
                } else {
                    $customers = Db::getInstance()->getValue($sql);
                }
            }
        } else {
            // Segmentation
            $customers = $this->getSegmentContacts($sync['id_segment'], $num_start, $limit, $count, $id_customer);
        }

        return $customers;
    }

    public function getIdContactByIdCustomer($id_customer)
    {
        $sql = 'SELECT id_contact_digitaleo FROM '._DB_PREFIX_.'dgo_customers_contacts
				WHERE id_customer = ' .(int) $id_customer;

        return Db::getInstance()->getValue($sql);
    }

    public function addCustomerContact($id_customer, $id_contact)
    {
        $sql = 'INSERT INTO '._DB_PREFIX_.'dgo_customers_contacts (id_customer, id_contact_digitaleo, date_sync)
				VALUES (' .(int) $id_customer.', '.(int) $id_contact.', \''.pSQL(date('Y-m-d H:i:s')).'\')';
        Db::getInstance()->execute($sql);
    }

    public function updateCustomersContacts($values_array)
    {
        $sql_values = array();
        foreach ($values_array as $va) {
            $sql_values[] = (int)$va['id_customer'].",".(int)$va['id'].",".pSQL($va['date']);
        }

        $sql = 'REPLACE INTO '._DB_PREFIX_.'dgo_customers_contacts (id_customer, id_contact_digitaleo, date_sync)
				VALUES (\'' .implode('\'),(\'', $sql_values).'\')';
        Db::getInstance()->execute($sql);
    }

    public function deleteContact($id_customer)
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'dgo_customers_contacts
				WHERE id_customer = ' .(int) $id_customer;

        return Db::getInstance()->getValue($sql);
    }

    public function isSynchronized($id_customer, $date_upd)
    {
        $sql = 'SELECT id_contact_digitaleo FROM '._DB_PREFIX_.'dgo_customers_contacts
				WHERE id_customer = ' .(int) $id_customer." AND date_sync >= '".pSQL($date_upd)."'";
        $id_contact_digitaleo = Db::getInstance()->getValue($sql);

        if (empty($id_contact_digitaleo)) {
            return false;
        }

        return $id_contact_digitaleo;
    }

    public function getCountryList($id_lang, $list_ids)
    {
        if (empty($list_ids)) {
            return '';
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT cl.`name`
            FROM `' ._DB_PREFIX_.'country` c '.(version_compare(_PS_VERSION_, '1.5', '>=') ?
                    Shop::addSqlAssociation('country', 'c') : '').'
            LEFT JOIN `' ._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.
            (int) $id_lang.')
            WHERE c.id_country IN(' .implode(",", array_map('intval', explode(",", $list_ids))). ')
            GROUP BY c.id_country'
        );

        $countries = array();
        foreach ($result as $r) {
            $countries[] = $r['name'];
        }

        return implode(', ', $countries);
    }

    public function getGroupList($id_lang, $list_ids)
    {
        if (empty($list_ids)) {
            return '';
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT gl.`name`
            FROM `' ._DB_PREFIX_.'group` g
            LEFT JOIN `' ._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.
            (int) $id_lang.')
            WHERE g.id_group IN(' .implode(",", array_map('intval', explode(",", $list_ids))).')
            GROUP BY g.id_group'
        );

        $groups = array();
        foreach ($result as $r) {
            $groups[] = $r['name'];
        }

        return implode(', ', $groups);
    }

    public function yesOrNo($bool)
    {
        return ($bool == 1) ? 'Yes' : 'No';
    }

    public function getSegments($limit = 0, $num_page = 0)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_segments ORDER BY id_segment DESC';

        if (!empty($limit)) {
            $offset = ($num_page - 1) * $limit;
            $sql .= ' LIMIT '.(int)$offset.','.(int)$limit;
        }

        return Db::getInstance()->executeS($sql);
    }

    public function getTotalSegments()
    {
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'dgo_segments';

        return Db::getInstance()->GetValue($sql);
    }

    public function getSegment($id_segment)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_segments WHERE id_segment='.(int) $id_segment;

        return Db::getInstance()->getRow($sql);
    }

    public function deleteSegment($id_segment)
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'dgo_segments WHERE id_segment='.(int) $id_segment;
        Db::getInstance()->execute($sql);
    }

    public function getSegmentContactsNumber($id_segment = 0)
    {
        $nb_contacts = $this->getSegmentContacts($id_segment, 0, 0, true);

        return $nb_contacts;
    }

    public function getSegmentContacts($id_segment = 0, $num_start = 0, $limit = 0, $count = false, $id_customer = 0)
    {
        if (empty($id_segment)) {
            $this->context = Context::getContext();

            // On se base sur les cookies
            $segment = array();
            $segment['name'] = $this->context->cookie->segment_name;
            $segment['countries'] = $this->context->cookie->segment_countries;
            $segment['age_from'] = $this->context->cookie->segment_age_from;
            $segment['age_to'] = $this->context->cookie->segment_age_to;
            $segment['registration_from'] = $this->context->cookie->segment_registration_from;
            $segment['registration_to'] = $this->context->cookie->segment_registration_to;
            $segment['genre'] = $this->context->cookie->segment_genre;
            $segment['orders_from'] = $this->context->cookie->segment_orders_from;
            $segment['orders_to'] = $this->context->cookie->segment_orders_to;
            $segment['groups'] = $this->context->cookie->segment_groups;

            $segment['newsletter'] = 2;
            $segment['optin'] = 2;

            if (isset($this->context->cookie->segment_newsletter)) {
                $segment['newsletter'] = $this->context->cookie->segment_newsletter;
            }

            if (isset($this->context->cookie->segment_optin)) {
                $segment['optin'] = $this->context->cookie->segment_optin;
            }
        } else {
            // Chargement depuis la base de données
            $segment = $this->getSegment($id_segment);
        }

        if (!$count) {
            $select = " c.id_customer, c.firstname, c.lastname, c.email, c.date_upd,
				(SELECT IF(phone_mobile IS NULL or phone_mobile = '', phone, phone_mobile)
				    FROM " ._DB_PREFIX_.'address
					WHERE id_customer = c.id_customer AND active = 1 AND deleted = 0
					ORDER BY id_address LIMIT 1) AS phone_mobile ';
        } else {
            $select = ' COUNT(DISTINCT c.id_customer) ';
        }

        $sql = 'SELECT '.$select.'
				FROM ' ._DB_PREFIX_.'customer c
				LEFT JOIN ' ._DB_PREFIX_.'address a ON c.id_customer = a.id_customer
				' .(version_compare(_PS_VERSION_, '1.5', '>=') ? 'LEFT JOIN '._DB_PREFIX_.'gender g
				ON c.id_gender = g.id_gender' : '').'
				LEFT JOIN ' ._DB_PREFIX_.'customer_group cg ON c.id_customer = cg.id_customer
				WHERE c.active = 1 AND c.deleted = 0 
                AND ((a.active=1 AND a.deleted=0) OR a.active IS NULL) ' .(!empty($id_customer) ? '
				AND c.id_customer=' .(int) $id_customer.' ' : '').' ';

        // Filtres
        if (!empty($segment['countries'])) {
            $sql .= ' AND a.id_country IN('.implode(",", array_map('intval', explode(",", $segment['countries']))).') ';
        }

        if (!empty($segment['age_from'])) {
            $sql .= ' AND FLOOR(DATEDIFF(NOW(), c.birthday)/365) >= '.(int) $segment['age_from'];
        }

        if (!empty($segment['age_to'])) {
            $sql .= ' AND FLOOR(DATEDIFF(NOW(), c.birthday)/365) <= '.(int) $segment['age_to'];
        }

        if (!empty($segment['registration_from']) && $segment['registration_from'] != '0000-00-00') {
            $sql .= " AND c.date_add >= '".pSQL($segment['registration_from'])."' ";
        }

        if (!empty($segment['registration_to']) && $segment['registration_to'] != '0000-00-00') {
            $sql .= " AND c.date_add <= '".pSQL($segment['registration_to'])."' ";
        }

        if (!empty($segment['genre'])) {
            $type_gender = false;
            if (version_compare(_PS_VERSION_, '1.5', '>=')) {
                if ($segment['genre'] == 'H') {
                    $type_gender = 0;
                }
                if ($segment['genre'] == 'F') {
                    $type_gender = 1;
                }
            } else {
                if ($segment['genre'] == 'H') {
                    $type_gender = 1;
                }
                if ($segment['genre'] == 'F') {
                    $type_gender = 2;
                }
            }

            if ($type_gender !== false) {
                if (version_compare(_PS_VERSION_, '1.5', '>=')) {
                    $sql .= ' AND g.type = '.(int) $type_gender;
                } else {
                    $sql .= ' AND c.id_gender = '.(int) $type_gender;
                }
            }
        }

        if (!empty($segment['groups'])) {
            $sql .= ' AND (cg.id_group IN('.implode(",", array_map('intval', explode(",", $segment['groups']))).') 
                OR c.id_default_group IN('.implode(",", array_map('intval', explode(",", $segment['groups']))).')) ';
        }

        if ($segment['orders_from'] != "") {
            $sql .= ' AND (SELECT COUNT(id_order) FROM '._DB_PREFIX_.'orders o WHERE o.id_customer = c.id_customer
                AND o.valid=1) >= ' .(int) $segment['orders_from'];
        }

        if ($segment['orders_to'] != "") {
            $sql .= ' AND (SELECT COUNT(id_order) FROM '._DB_PREFIX_.'orders o WHERE o.id_customer = c.id_customer
                AND o.valid=1) <= ' .(int) $segment['orders_to'];
        }

        if ($segment['newsletter'] < 2) {
            $sql .= ' AND c.newsletter = '.(int) $segment['newsletter'];
        }

        if ($segment['optin'] < 2) {
            $sql .= ' AND c.optin = '.(int) $segment['optin'];
        }

        if (!$count) {
            $sql .= ' GROUP BY c.id_customer ORDER BY c.id_customer';

            if (!empty($limit)) {
                $sql .= ' LIMIT '.(int) $num_start.','.(int) $limit;
            }

            $contacts = Db::getInstance()->executeS($sql);
        } else {
            $contacts = Db::getInstance()->getValue($sql);
        }

        return $contacts;
    }

    public function createSegment()
    {
        $this->context = Context::getContext();

        // On se base sur les cookies
        $segment = array();
        $segment['name'] = $this->context->cookie->segment_name;
        $segment['countries'] = $this->context->cookie->segment_countries;
        $segment['age_from'] = $this->context->cookie->segment_age_from;
        $segment['age_to'] = $this->context->cookie->segment_age_to;
        $segment['registration_from'] = $this->context->cookie->segment_registration_from;
        $segment['registration_to'] = $this->context->cookie->segment_registration_to;
        $segment['genre'] = $this->context->cookie->segment_genre;
        $segment['orders_from'] = $this->context->cookie->segment_orders_from;
        $segment['orders_to'] = $this->context->cookie->segment_orders_to;
        $segment['groups'] = $this->context->cookie->segment_groups;

        $segment['newsletter'] = 2;
        $segment['optin'] = 2;

        if (isset($this->context->cookie->segment_newsletter)) {
            $segment['newsletter'] = $this->context->cookie->segment_newsletter;
        }

        if (isset($this->context->cookie->segment_optin)) {
            $segment['optin'] = $this->context->cookie->segment_optin;
        }

        if (!isset($segment['orders_from']) || $segment['orders_from'] == ""
            || $segment['orders_from'] === false) {
            $segment['orders_from'] = 'NULL';
        }
        
        if (!isset($segment['orders_to']) || $segment['orders_to'] == ""
            || $segment['orders_to'] === false) {
            $segment['orders_to'] = 'NULL';
        }

        $sql = 'INSERT INTO '._DB_PREFIX_."dgo_segments (name, countries, age_from, age_to, registration_from,
                registration_to, newsletter, groups, orders_from, orders_to, genre, optin)
        		VALUES ('" .pSQL($segment['name'])."',
        				'" .pSQL($segment['countries'])."',
        				" .(int) $segment['age_from'].',
        				' .(int) $segment['age_to'].",
        				'" .pSQL($segment['registration_from'])."',
        				'" .pSQL($segment['registration_to'])."',
        				" .(int) $segment['newsletter'].",
        				'" .pSQL($segment['groups'])."',
        				" .($segment['orders_from'] == "NULL" ? "NULL" : "'".pSQL($segment['orders_from'])."'"). ",
        				" .($segment['orders_to'] == "NULL" ? "NULL" : "'".pSQL($segment['orders_to'])."'"). ",
        				'" .pSQL($segment['genre'])."',
        				" .(int) $segment['optin'].')';

        return Db::getInstance()->execute($sql);
    }

    public function editSegment()
    {
        $this->context = Context::getContext();

        if (empty($this->context->cookie->id_segment)) {
            return false;
        }

        // On se base sur les cookies
        $segment = array();
        $segment['name'] = $this->context->cookie->segment_name;
        $segment['countries'] = $this->context->cookie->segment_countries;
        $segment['age_from'] = $this->context->cookie->segment_age_from;
        $segment['age_to'] = $this->context->cookie->segment_age_to;
        $segment['registration_from'] = $this->context->cookie->segment_registration_from;
        $segment['registration_to'] = $this->context->cookie->segment_registration_to;
        $segment['genre'] = $this->context->cookie->segment_genre;
        $segment['orders_from'] = $this->context->cookie->segment_orders_from;
        $segment['orders_to'] = $this->context->cookie->segment_orders_to;
        $segment['groups'] = $this->context->cookie->segment_groups;

        $segment['newsletter'] = 2;
        $segment['optin'] = 2;

        if (isset($this->context->cookie->segment_newsletter)) {
            $segment['newsletter'] = $this->context->cookie->segment_newsletter;
        }

        if (isset($this->context->cookie->segment_optin)) {
            $segment['optin'] = $this->context->cookie->segment_optin;
        }

        if (!isset($segment['orders_from']) || $segment['orders_from'] == ""
            || $segment['orders_from'] === false) {
            $segment['orders_from'] = 'NULL';
        }
        
        if (!isset($segment['orders_to']) || $segment['orders_to'] == ""
            || $segment['orders_to'] === false) {
            $segment['orders_to'] = 'NULL';
        }

        $sql = 'UPDATE '._DB_PREFIX_."dgo_segments
        SET name = '" .pSQL($segment['name'])."',
            countries = '" .pSQL($segment['countries'])."',
            age_from = " .(int) $segment['age_from'].',
            age_to = ' .(int) $segment['age_to'].",
            registration_from = '" .pSQL($segment['registration_from'])."',
            registration_to = '" .pSQL($segment['registration_to'])."',
            newsletter = " .(int) $segment['newsletter'].",
            groups = '" .pSQL($segment['groups'])."',
            orders_from = " .($segment['orders_from'] == "NULL" ? "NULL" : "'".pSQL($segment['orders_from'])."'").',
            orders_to = ' .($segment['orders_to'] == "NULL" ? "NULL" : "'".pSQL($segment['orders_to'])."'").",
            genre = '" .pSQL($segment['genre'])."',
            optin = " .(int) $segment['optin'].'
        WHERE id_segment = ' .(int) $this->context->cookie->id_segment;

        return Db::getInstance()->execute($sql);
    }

    public function getCampaigns($limit = 0, $num_page = 0)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_campaigns ORDER BY date_send DESC';

        if (!empty($limit)) {
            $offset = ($num_page - 1) * $limit;
            $sql .= ' LIMIT '.(int)$offset.','.(int)$limit;
        }

        return Db::getInstance()->ExecuteS($sql);
    }

    public function getTotalCampaigns()
    {
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'dgo_campaigns';

        return Db::getInstance()->GetValue($sql);
    }

    public function getCampaign($id_campaign)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_campaigns WHERE id_campaign = '.(int) $id_campaign;

        return Db::getInstance()->GetRow($sql);
    }

    public function deleteCampaign($id_campaign)
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'dgo_campaigns WHERE id_campaign = '.(int) $id_campaign;

        return Db::getInstance()->Execute($sql);
    }

    public function createCampaign($id_campaign_digitaleo, $fields)
    {
        if ($fields['channel'] == 'sms') {
            $content = $fields['sms_content'];
        } else {
            $content = $fields['html'];
        }

        $sql = 'INSERT INTO '._DB_PREFIX_.'dgo_campaigns (id_campaign_digitaleo, name, id_list_digitaleo, content,
                sender, replyto, subject, id_template, date_send, channel)
                VALUES (' .(int) $id_campaign_digitaleo.",
                    '" .pSQL($fields['name'])."',
                    " .(int) $fields['id_list'].",
                    '" .pSQL($content, true)."',
                    '" .pSQL($fields['sender'])."',
                    '" .pSQL($fields['replyto'])."',
                    '" .pSQL($fields['subject'])."',
                    " .(int) $fields['id_template'].",
                    '" .pSQL($fields['date_iso'])."',
                    '" .pSQL($fields['channel'])."')";

        return Db::getInstance()->Execute($sql);
    }

    public function createNotification($id_campaign_digitaleo, $fields)
    {
        if ($fields['channel'] == 'sms') {
            $content = $fields['sms_content'];
        } else {
            $content = $fields['html'];
        }

        $sql = 'INSERT INTO '._DB_PREFIX_.'dgo_notifications (id_campaign_digitaleo, recipient_type,
                prestashop_event, delay_cart_abandonment, content, sender, replyto, subject, id_template, channel,
                administrator_email, administrator_sms)
                VALUES (' .(int) $id_campaign_digitaleo.",
                    '" .pSQL($fields['type'])."',
                    '" .pSQL($fields['event'])."',
                    '" .(int) $fields['delay_cart_abandonment']."',
                    '" .pSQL($content, true)."',
                    '" .pSQL($fields['sender'])."',
                    '" .pSQL($fields['replyto'])."',
                    '" .pSQL($fields['subject'])."',
                    " .(int) $fields['id_template'].",
                    '" .pSQL($fields['channel'])."',
                    '" .pSQL($fields['administrator_email'])."',
                    '" .pSQL($fields['administrator_sms'])."')";

        return Db::getInstance()->Execute($sql);
    }

    public function getNotifications($limit = 0, $num_page = 0)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_notifications ORDER BY id_notification DESC';

        if (!empty($limit)) {
            $offset = ($num_page - 1) * $limit;
            $sql .= ' LIMIT '.(int)$offset.','.(int)$limit;
        }

        return Db::getInstance()->ExecuteS($sql);
    }

    public function getTotalNotifications()
    {
        $sql = 'SELECT COUNT(*) FROM '._DB_PREFIX_.'dgo_notifications';

        return Db::getInstance()->GetValue($sql);
    }

    public function getNotification($id_notification)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_.'dgo_notifications WHERE id_notification = '.(int) $id_notification;

        return Db::getInstance()->GetRow($sql);
    }

    public function getNotificationByEvent($event)
    {
        $sql = 'SELECT * FROM '._DB_PREFIX_."dgo_notifications WHERE prestashop_event = '".pSQL($event)."'";

        return Db::getInstance()->executeS($sql);
    }

    public function deleteNotification($id_notification)
    {
        $sql = 'DELETE FROM '._DB_PREFIX_.'dgo_notifications WHERE id_notification = '.(int) $id_notification;

        return Db::getInstance()->Execute($sql);
    }

    public function getCartAbandonment($hour)
    {
        if (!(int) $hour) {
            return false;
        }

        return Db::getInstance()->executeS(
            'SELECT c.id_customer, c.id_cart FROM '._DB_PREFIX_.'cart c
            JOIN (SELECT cc.id_customer, cc.id_cart FROM ' ._DB_PREFIX_.'cart cc
            WHERE cc.id_customer AND DATE_ADD(NOW(), INTERVAL -' .(int) $hour.' HOUR) > cc.date_upd
            AND NOT EXISTS (SELECT 1 FROM ' ._DB_PREFIX_.'orders o WHERE o.id_cart = cc.id_cart)
            AND DATEDIFF(NOW(), cc.date_upd) < ' .
            (int) Configuration::get('DIGITALEO_ABANDONMENT_MAXDAY', null, 0, 0).'
            ORDER BY cc.date_upd DESC) j
            ON j.id_customer = c.id_customer AND j.id_cart = c.id_cart
            WHERE c.id_customer AND DATE_ADD(NOW(), INTERVAL -' .(int) $hour.' HOUR) > c.date_upd
            AND NOT EXISTS (SELECT o.id_cart FROM ' ._DB_PREFIX_.'orders o WHERE o.id_cart = c.id_cart)
            AND DATEDIFF(NOW(), c.date_upd) < ' .
            (int) Configuration::get('DIGITALEO_ABANDONMENT_MAXDAY', null, 0, 0).'
            AND NOT EXISTS (SELECT 1 FROM ' ._DB_PREFIX_.'dgo_cart_abandonment a WHERE a.id_cart = c.id_cart)
            GROUP BY c.id_customer'
        );
    }

    public function getCustomerMobile($id_customer)
    {
        return Db::getInstance()->getValue('SELECT phone_mobile FROM '._DB_PREFIX_.'address
            WHERE id_customer = ' .(int) $id_customer.' AND active = 1 AND deleted = 0 AND phone_mobile
            ORDER BY id_address DESC');
    }

    public function notificationCartAbandonmentSent($id_cart)
    {
        return Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'dgo_cart_abandonment
            (id_cart, date_add) VALUES (' .(int) $id_cart.', NOW())');
    }

    public function getCustomerLocale($id_customer)
    {
        $sql = 'SELECT l.iso_code FROM '._DB_PREFIX_.'customer c
                INNER JOIN ' ._DB_PREFIX_.'lang l ON c.id_lang = l.id_lang
                WHERE c.id_customer = '.(int) $id_customer;

        return Db::getInstance()->getValue($sql);
    }
}
