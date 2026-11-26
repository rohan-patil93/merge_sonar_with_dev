<?php

class RentalConstants
{
    public const SEL_BANKS = "SELECT id, bank_name FROM banks WHERE company_id = '";
    public const AND_IS_ACTIVE_0_NO_SPACE = "' AND is_active =0";
    public const AND_IS_ACTIVE_0_DOUBLE_SPACE = "'  AND is_active =0";
    public const AND_ID_APOS = "' AND id = '";
    public const AND_PROPERTY_OWNER_ID = "' AND property_owner_id = '";
    public const CLOSE_PAREN_AND_IS_ACTIVE_0_NO_SPACE = ") AND is_active =0";
    public const AND_IS_ACTIVE_0 = "' AND is_active = 0";
    public const DATE_FORMAT_YMD_HIS_LOWER = "Y-m-d h:i:s";
    public const DATE_FORMAT_YMD_HIS = "Y-m-d H:i:s";
    public const SEL_PTB = "SELECT id, month_start_date, rental_invoice_id, utility_rental_invoice_id FROM `property_tenant_billings` WHERE company_id = '";
    public const AND_PTD_ID = "' AND property_tenant_detail_id = '";
    public const CLOSE_PAREN_AND_IS_ACTIVE_0 = ") AND is_active = 0";
    public const AND_ID_IN = "' AND id IN(";
    public const SEL_PROPERTY_OWNERS = "SELECT id, owner_name FROM property_owners WHERE company_id = '";
    public const SEL_GENC = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '";
    public const SEL_GENC_UTILITY = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'UTILITYUOM' AND company_id = '";
    public const SEL_PTD = "SELECT * FROM property_tenant_details WHERE company_id = '";
    public const AND_TRANS_ID_IN = "' AND transaction_id IN(";
    public const PLUS_ONE_MONTH_SPACE = " +1 month";
    public const PLUS_ONE_MONTH = "+1 month";
    public const AND_CREATED_BY = " AND created_by = '";
    public const AND_IS_ACTIVE_0_SPACE = " AND is_active = 0";
    public const SEL_CUSTOMERS = "SELECT id, display_name FROM customers WHERE company_id = '";
    public const AND_PARTY_ID_IN = "' AND party_id IN(";
    public const LIMIT_5 = " limit 5";
    public const AND_IS_ACTIVE_0_TRAILING_SPACE = " AND is_active = 0 ";
    public const SEL_POD = "SELECT id, property_name FROM property_owner_details WHERE company_id = '";
    public const UPLOADS_RENTAL_INVOICES = "/uploads/rental_invoices/";
    public const WHERE_COMPANY_ID = " WHERE company_id = '";
    public const SEL_FROM = "SELECT * FROM ";
    public const SEL_ALL_CUSTOMERS = "SELECT * FROM customers WHERE company_id = '";
    public const COMPANY_ID_EQ = "company_id = '";
    public const AND_IS_ACTIVE_0_COMPACT = "' AND is_active=0";
    public const SEL_PO_FULL = "SELECT id, owner_name, address, gst_no, bank_id, bank_account_no FROM property_owners WHERE company_id = '";
    public const SEL_CUSTOMERS_COMPANY = "SELECT id, display_name, company FROM customers WHERE company_id = '";
    public const AND_IS_ACTIVE_0_MID_SPACE = " AND is_active =0";
    public const AND_IS_ACTIVE_0_STATUS = "' AND is_active =0 AND current_status = '";
    public const AND_TRANS_DATE_LTE_DOUBLE_SPACE = "' AND transaction_date <=  '";
    public const CN_GROUP_1 = "' AND cn.is_active =0 GROUP BY ri.parent_ref_id, cn.party_id";
    public const AND_CN_TRANS_DATE_LTE = "' AND cn.transaction_date <= '";
    public const CN_GROUP_2 = "' AND cn.is_active =0 GROUP BY  ri.parent_ref_id, cn.party_id";
    public const AND_TRANS_ID = "' AND transaction_id = '";
    public const AND_RI_TRANS_DATE_LTE = "' AND ri.transaction_date <=  '";
    public const RI_PTD_JOIN = "' AND ri.company_id = ptd.company_id AND ri.parent_ref_id = ptd.id AND ri.is_active = ptd.is_active AND ri.is_active = 0 AND ri.current_status = '";
    public const SEL_HSN_SAC = "SELECT * FROM `hsn_and_sac_codes` WHERE company_id = '";
}

trait RentalPropertyTrait
{
public function getPropertyOwners($get)
    {
        global $callConfig;
        $property_typessql = "SELECT id, property_type_name, parent_id, icon FROM property_types WHERE is_active =0 AND company_id = '" . $get['company_id'] . "'";
        $property_typesres = $callConfig->getAllRows($property_typessql);
        $return['property_types'] = $property_typesres;

        $bankssql = RentalConstants::SEL_BANKS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $banksres = $callConfig->getAllRows($bankssql);
        $return['banks'] = $banksres;

        $property_owners_list_sql = "SELECT po.id, po.owner_name, (SELECT count(*) FROM property_owner_details pod WHERE pod.company_id = po.company_id AND pod.property_owner_id = po.id AND pod.is_active = po.is_active) as total_property_cnt FROM property_owners po WHERE po.company_id = '" . $get['company_id'] . "' AND po.is_active =0";
        $property_owners_list_res = $callConfig->getAllRows($property_owners_list_sql);
        $return['property_owners_list'] = $property_owners_list_res;

        if ($get['rid'] != "") {
            $property_ownerssql = "SELECT * FROM property_owners WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
            $property_ownersres = $callConfig->getRow($property_ownerssql);
            $return['property_owners'] = $property_ownersres;

            $property_owner_detailssql = "SELECT * FROM property_owner_details WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_PROPERTY_OWNER_ID . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
            $property_owner_detailsres = $callConfig->getAllRows($property_owner_detailssql);
            $return['property_owner_details'] = $property_owner_detailsres;
            $podid = array();
            foreach ($property_owner_detailsres as $pwd) {
                $podid[$pwd->id] = $pwd->id;
            }
            $podids = implode(',', $podid);

            $property_owner_detail_areassql = "SELECT id, property_owner_detail_id, property_area_name, property_area, per_sq_ft_rate, uom, property_tenant_detail_id FROM property_owner_detail_areas WHERE company_id = '" . $get['company_id'] . "' AND property_owner_detail_id IN(" . $podids . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0_NO_SPACE;
            $property_owner_detail_areasres = $callConfig->getAllRows($property_owner_detail_areassql);
            $return['property_owner_detail_areas'] = $property_owner_detail_areasres;

            $rentaluomsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'RENTALUOM' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
            $rentaluomres = $callConfig->getAllRows($rentaluomsql);
            foreach ($rentaluomres as $rentuom) {
                $rentaluom[$rentuom->genc_detail_code] = $rentuom->genc_detail_name;
            }
            $return['rentaluom'] = $rentaluom;

            $account_type_sql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'BANKACCTTYPE' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
            $account_type_res = $callConfig->getAllRows($account_type_sql);
            foreach ($account_type_res as $btr) {
                $account_types[$btr->genc_detail_code] = $btr->genc_detail_name;
            }
            $return['account_types'] = $account_types;

        }

        return $return;
    }
    private function savePropertyOwnerAreas($post, $property_owner_detail_id, $keyid): array
    {
        global $callConfig;
        $items = array();
        if (empty($post['property_area_name'][$keyid]) || !is_array($post['property_area_name'][$keyid])) {
            return $items;
        }
        foreach ($post['property_area_name'][$keyid] as $keyyid => $keyvalue) {
            if ($keyvalue != "" && !empty($post['property_area'][$keyid][$keyyid])) {
                $insertary = array(
                    'company_id' => $post['company_id'],
                    'property_owner_detail_id' => $property_owner_detail_id,
                    'property_area_name' => $post['property_area_name'][$keyid][$keyyid],
                    'property_area' => $post['property_area'][$keyid][$keyyid],
                    'per_sq_ft_rate' => $post['per_sq_ft_rate'][$keyid][$keyyid] ?? null,
                    'uom' => $post['uom'][$keyid][$keyyid] ?? null
                );
                if (!empty($post['property_owner_detail_area_id'][$keyid][$keyyid])) {
                    $insertary['updated_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER);
                    $insertary['updated_by'] = $post['user_id'];
                    $insertary['is_active'] = 0;
                    $items[] = $callConfig->updateRecord('property_owner_detail_areas', $insertary, 'id', $post['property_owner_detail_area_id'][$keyid][$keyyid]);
                } else {
                    $insertary['is_active'] = 0;
                    $insertary['current_status'] = 0;
                    $insertary['created_by'] = $post['user_id'];
                    $insertary['created_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS);
                    $items[] = $callConfig->insertRecord('property_owner_detail_areas', $insertary);
                }
            }
        }
        return $items;
    }

    private function saveSinglePropertyOwnerDetail($post, $property_owner_id, $keyid, array &$return): void
    {
        global $callConfig;
        $updateary = array('is_active' => null);
        if (!empty($post['property_owner_detail_id'][$keyid])) {
            $callConfig->updateRecord('property_owner_detail_areas', $updateary, 'property_owner_detail_id', $post['property_owner_detail_id'][$keyid]);
        }
        $property_joint_owner = array();
        if (!empty($post['joint_owner_names'][$keyid]) && is_array($post['joint_owner_names'][$keyid])) {
            foreach ($post['joint_owner_names'][$keyid] as $keyyid => $keyvalue) {
                if ($keyvalue != "" && !empty($post['owner_percentage'][$keyid][$keyyid])) {
                    $property_joint_owner[] = array('joint_owner_names' => $keyvalue, 'owner_percentage' => $post['owner_percentage'][$keyid][$keyyid]);
                }
            }
        }

        $property_joint_owner_encode = json_encode($property_joint_owner);
        $attachment = json_encode($_POST['all_attachment'][$keyid] ?? array());
        $insertary = array(
            'company_id' => $post['company_id'],
            'property_owner_id' => $property_owner_id,
            'property_name' => $post['property_name'][$keyid],
            'property_type' => $post['property_type'][$keyid] ?? null,
            'property_sub_type' => $post['property_sub_type'][$keyid] ?? null,
            'property_address' => $post['property_address'][$keyid] ?? null,
            'property_joint_owner' => $property_joint_owner_encode,
            'longitude' => $post['longitude'][$keyid] ?? null,
            'latitude' => $post['latitude'][$keyid] ?? null,
            'attachment' => $attachment
        );

        if (!empty($post['property_owner_detail_id'][$keyid])) {
            $insertary['updated_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER);
            $insertary['updated_by'] = $post['user_id'];
            $insertary['is_active'] = 0;
            $res1 = $callConfig->updateRecord('property_owner_details', $insertary, 'id', $post['property_owner_detail_id'][$keyid]);
            $return['item'][] = $res1;
            $property_owner_detail_id = $post['property_owner_detail_id'][$keyid];
        } else {
            $insertary['is_active'] = 0;
            $insertary['current_status'] = 0;
            $insertary['created_by'] = $post['user_id'];
            $insertary['created_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS);
            $res1 = $callConfig->insertRecord('property_owner_details', $insertary);
            $return['item'][] = $res1;
            $property_owner_detail_id = $res1['LASTID'] ?? null;
        }

        $childItems = $this->savePropertyOwnerAreas($post, $property_owner_detail_id, $keyid);
        if (!empty($childItems)) {
            $return['item_child'] = array_merge($return['item_child'] ?? array(), $childItems);
        }
    }

    public function insertPropertyOwners($get, $post)
    {
        global $callConfig;
        $return = array('get' => $get, 'post' => $post);
        $insertary = array('company_id' => $post['company_id'], 'owner_name' => $post['owner_name'], 'address' => $post['address'], 'gst_no' => $post['gst_no'], 'bank_id' => $post['bank_id'], 'bank_account_no' => $post['bank_account_no'], 'owner_company_name' => $post['owner_company_name'], 'pan_no' => $post['pan_no'], 'ifsc_code' => $post['ifsc_code'], 'account_type' => $post['account_type'], 'contact_no' => $post['contact_no'], 'email_id' => $post['email_id']);
        if (!empty($post['rid'])) {
            $insertary['updated_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER);
            $insertary['updated_by'] = $post['user_id'];
            $res = $callConfig->updateRecord('property_owners', $insertary, 'id', $post['rid']);
            $return['res'] = $res;
            $property_owner_id = $post['rid'];
        } else {
            $insertary['is_active'] = 0;
            $insertary['current_status'] = 0;
            $insertary['created_by'] = $post['user_id'];
            $insertary['created_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS);
            $res = $callConfig->insertRecord('property_owners', $insertary);
            $return['res'] = $res;
            $property_owner_id = $res['LASTID'] ?? null;
        }

        if ($property_owner_id != "") {
            $callConfig->updateRecord('property_owner_details', array('is_active' => null), 'property_owner_id', $property_owner_id);
        }
        if (!empty($post['property_name']) && is_array($post['property_name'])) {
            foreach ($post['property_name'] as $keyid => $keyvalue) {
                if ($keyvalue != "") {
                    $this->saveSinglePropertyOwnerDetail($post, $property_owner_id, $keyid, $return);
                }
            }
        }
        return $return;
    }
    private function getPropertyTenantUnlockDetails($get): array
    {
        global $callConfig;
        $un_lock_date = $this->dateconvertdbformat($get['un_lock_date']);
        $rental_invoice_id = $utility_rental_invoice_id = array();
        $ptbsql = RentalConstants::SEL_PTB . $get['company_id'] . RentalConstants::AND_PTD_ID . $get['rid'] . "' AND  month_start_date >= '" . $un_lock_date . RentalConstants::AND_IS_ACTIVE_0;
        $ptbres = $callConfig->getAllRows($ptbsql);
        foreach ($ptbres as $ptbress) {
            if ($ptbress->rental_invoice_id != "") {
                $rental_invoice_id[$ptbress->rental_invoice_id] = $ptbress->rental_invoice_id;
            }
            if ($ptbress->utility_rental_invoice_id != "") {
                $utility_rental_invoice_id[$ptbress->utility_rental_invoice_id] = $ptbress->utility_rental_invoice_id;
            }
        }
        $rentalinvid = implode(',', $rental_invoice_id);
        $ptbsql = "SELECT id, transaction_number, transaction_date, total_amount FROM `rental_invoices` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_IN . $rentalinvid . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0;
        $rental_invoices = $callConfig->getAllRows($ptbsql);

        $rentalinvid = implode(',', $utility_rental_invoice_id);
        $ptbsql = "SELECT id, transaction_number, transaction_date, total_amount FROM `utility_rental_invoices` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_IN . $rentalinvid . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0;
        $utility_rental_invoices = $callConfig->getAllRows($ptbsql);

        return array('rental_invoices' => array_merge((array) $rental_invoices, (array) $utility_rental_invoices));
    }

    private function getPropertyTenantRidDetails($get, $property_tenant_detailsres): array
    {
        global $callConfig;
        $res = array();
        $property_owner_detailssql = "SELECT id, property_name, property_type, property_sub_type FROM property_owner_details WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_PROPERTY_OWNER_ID . $property_tenant_detailsres->property_owner_id . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $res['property_owner_details'] = $callConfig->getAllRows($property_owner_detailssql);

        $property_owner_detail_areassql = "SELECT id, property_owner_detail_id, property_area_name, property_area, per_sq_ft_rate ,uom FROM property_owner_detail_areas WHERE company_id = '" . $get['company_id'] . "' AND property_owner_detail_id = '" . $property_tenant_detailsres->property_owner_detail_id . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $res['property_owner_detail_areas'] = $callConfig->getAllRows($property_owner_detail_areassql);

        $property_tenant_billingssql = "SELECT id, month_start_date, total_amount, bill_detail,utility_details FROM property_tenant_billings WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_PTD_ID . $property_tenant_detailsres->id . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $res['property_tenant_billings'] = $callConfig->getAllRows($property_tenant_billingssql);

        $hsn_and_sac_codessql = "SELECT id, code, code_description, gst FROM hsn_and_sac_codes WHERE company_id = '" . $get['company_id'] . "' AND id='" . $property_tenant_detailsres->hsn_and_sac_code_id . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $res['hsn_and_sac_codes'] = $callConfig->getAllRows($hsn_and_sac_codessql);

        if (($get['openingbalance'] ?? '') == 'Y') {
            $rental_invoicessql = "SELECT id, transaction_number, transaction_date, party_name, amount, tax_amount, discount_amount, total_amount, pending_amount, party_email FROM rental_invoices WHERE company_id = '" . $get['company_id'] . "' AND parent_ref_id = '" . $get['rid'] . "' AND ref_bill_type = 'OB' AND is_active =0";
            $res['rental_invoices'] = $callConfig->getAllRows($rental_invoicessql);
        }
        return $res;
    }

    public function getPropertyTenantDetails($get)
    {
        global $callConfig;
        if (!empty($get['un_lock_date'])) {
            return $this->getPropertyTenantUnlockDetails($get);
        }

        $return = array();
        $property_ownerssql = RentalConstants::SEL_PROPERTY_OWNERS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $return['property_owners'] = $callConfig->getAllRows($property_ownerssql);

        $rent_bill_to_be_raise_typesql = RentalConstants::SEL_GENC . $get['company_id'] . "' AND genc_code = 'RENTBILLTOBERAISETYP' AND is_active =0";
        $return['rent_bill_to_be_raise_type'] = $callConfig->getAllRows($rent_bill_to_be_raise_typesql);

        $bill_to_be_raise_typesql = RentalConstants::SEL_GENC . $get['company_id'] . "' AND genc_code = 'BILLTOBERAISETYPE' AND is_active =0";
        $return['bill_to_be_raise_type'] = $callConfig->getAllRows($bill_to_be_raise_typesql);

        $customerssql = "SELECT id, display_name,created_by FROM customers WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $return['customers'] = $callConfig->getAllRows($customerssql);

        $rental_utilitiessql = "SELECT id, type_name, parent_id FROM rental_utilities WHERE company_id = '" . $get['company_id'] . "' AND is_active =0 ORDER BY `id` ASC";
        foreach ($callConfig->getAllRows($rental_utilitiessql) as $ru) {
            if ($ru->parent_id == '0') {
                $return['rental_bill_types'][$ru->id] = $ru;
            } else {
                $return['rental_bill_sub_types'][$ru->parent_id][] = $ru;
            }
        }
        $utilityuomsql = RentalConstants::SEL_GENC_UTILITY . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $utilityuom = array();
        foreach ($callConfig->getAllRows($utilityuomsql) as $rentuom) {
            $utilityuom[$rentuom->genc_detail_code] = $rentuom->genc_detail_name;
        }
        $return['utilityuom'] = $utilityuom;

        if (!empty($get['rid'])) {
            $property_tenant_detailssql = RentalConstants::SEL_PTD . $get['company_id'] . RentalConstants::AND_ID_APOS . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
            $property_tenant_detailsres = $callConfig->getRow($property_tenant_detailssql);
            $return['property_tenant_details'] = $property_tenant_detailsres;
            $ridDetails = $this->getPropertyTenantRidDetails($get, $property_tenant_detailsres);
            $return = array_merge($return, $ridDetails);
        }

        return $return;
    }
    public function insertPropertyTenantDetails($get, $post)
    {
        global $callConfig;
        $return['post'] = $post;
        if ($post['customer_id'] == "" || $post['customer_id'] == "0") {
            $display_nameary = array();
            if ($post['tenant_name'] != "") {
                $display_nameary = explode(" ", $post['tenant_name']);
            }
            $insertcust = array('company_id' => $post['company_id'], 'company' => $post['company_name'], 'title' => $post['tenant_name'], 'first_name' => $display_nameary[0], 'last_name' => $display_nameary[1], 'display_name' => $post['tenant_name'], 'email' => $post['tenant_email'], 'phone' => $post['tenant_mobile'], 'billing_address' => $post['tenant_address'], 'billing_post_code' => $post['tenant_pin_code'], 'is_active' => 0, 'current_status' => 0, 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
            $res = $callConfig->insertRecord('customers', $insertcust);
            $return['customers'] = $res;
            $customer_id = $res['LASTID'];
        } else {
            $customer_id = $post['customer_id'];
        }

        if ($post['hsn_and_sac_code_id'] != "") { // get percentage tax
            $hsngstsql = "SELECT gst FROM hsn_and_sac_codes WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $post['hsn_and_sac_code_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $hsngstres = $callConfig->getRow($hsngstsql);
            $tax_percentage = $hsngstres->gst;
        }


        $bill_to_be_send_for_the_following = array();
        foreach ($post['following_name'] as $keyid => $keyval) {
            $bill_to_be_send_for_the_following[] = array('following_name' => $post['following_name'][$keyid], 'following_email' => $post['following_email'][$keyid], 'following_mobile' => $post['following_mobile'][$keyid], 'following_address' => $post['following_address'][$keyid]);
        }
        $btbsff = json_encode((array) $bill_to_be_send_for_the_following);
        $poda = array();
        foreach ($post['property_area_id'] as $keyid => $keyval) {
            $per_sq_ft_with_discount = $post['per_sq_ft_rate'][$keyid] - $post['per_sq_ft_discount'][$keyid];
            $manually_rent = $post['manually_rent'][$keyval] == "0" ? "0" : "1";
            $poda[] = array('property_area_id' => $post['property_area_id'][$keyid], 'property_area_name' => $post['property_area_name'][$keyid], 'per_sq_ft_rate' => $post['per_sq_ft_rate'][$keyid], 'per_sq_ft_discount' => $post['per_sq_ft_discount'][$keyid], 'rent' => $post['rent'][$keyid], 'property_area' => $post['property_area'][$keyid], 'per_sq_ft_with_discount' => $per_sq_ft_with_discount, 'uom' => $post['uom'][$keyid], 'manually_rent' => $manually_rent);
        }
        $property_owner_detail_areas = json_encode((array) $poda);
        $rental_bill_types = array();
        foreach ($post['rental_bill_types'] as $keyid => $keyval) {
            $rental_bill_types[$keyid] = array('rental_bill_type_id' => $keyid, 'rental_bill_type_name' => $post['rental_bill_type_name'][$keyid], 'status' => $keyval, 'utility_bill_auto_and_fix' => $post['utility_bill_auto_and_fix'][$keyid], 'utility_bill_amount' => $post['utility_bill_amount'][$keyid], 'child_utility' => $post['utility_bill_manual_amount'][$keyid]);
        }
        $rental_bill_type = json_encode((array) $rental_bill_types);
        if ($tax_percentage != "") {
            $monthly_tax = $post['monthly_rent'] * ($tax_percentage / 100);
            $monthly_total = $post['monthly_rent'] + $monthly_tax;
        }
        $property_owner_area_ids = json_encode((array) $post['property_owner_area_ids']);
        $insertary = array('company_id' => $post['company_id'], 'property_owner_id' => $post['property_owner_id'], 'property_owner_detail_id' => $post['property_owner_detail_id'], 'property_owner_area_ids' => $property_owner_area_ids, 'property_owner_detail_areas' => $property_owner_detail_areas, 'lease_period_start_date' => $this->dateconvertdbformat($post['lease_period_start_date']), 'lease_period_end_date' => $this->dateconvertdbformat($post['lease_period_end_date']), 'rent_free_period_day' => $post['rent_free_period_day'], 'rent_commencement_date' => $this->dateconvertdbformat($post['rent_commencement_date']), 'rent_escalation_after_month' => $post['rent_escalation_after_month'], 'notice_period' => $post['notice_period'], 'rent_bill_to_be_raise' => $post['rent_bill_to_be_raise'], 'rent_bill_to_be_raise_type' => $post['rent_bill_to_be_raise_type'], 'security_deposit' => $post['security_deposit'], 'gst_added' => $post['gst_added'], 'locking_period_tenant' => $post['locking_period_tenant'], 'locking_period_landlord' => $post['locking_period_landlord'], 'bill_to_be_raised' => $post['bill_to_be_raised'], 'tenant_name' => $post['tenant_name'], 'tenant_email' => $post['tenant_email'], 'tenant_mobile' => $post['tenant_mobile'], 'tenant_address' => $post['tenant_address'], 'tenant_pin_code' => $post['tenant_pin_code'], 'bill_to_be_send_for_the_following' => $btbsff, 'note' => $post['note'], 'customer_id' => $customer_id, 'rent_escalation_percentage' => $post['rent_escalation_percentage'], 'rental_bill_types' => $rental_bill_type, 'last_date_of_billing' => $this->dateconvertdbformat($post['last_date_of_billing']), 'monthly_rent' => $post['monthly_rent'], 'hsn_and_sac_code_id' => $post['hsn_and_sac_code_id'], 'tax_percentage' => $tax_percentage, 'monthly_tax' => $monthly_tax, 'monthly_total' => $monthly_total, 'originator' => $post['user_id'], 'company_name' => $post['company_name'], 'rental_invoice_to_be_raised' => $post['rental_invoice_to_be_raised'], 'rental_invoice_stoped_from' => $post['rental_invoice_stoped_from'], 'due_bill_to_be_raise_type' => $post['due_bill_to_be_raise_type'], 'due_bill_to_be_raise' => $post['due_bill_to_be_raise']);
        if ($post['rid'] != "") {
            $insertary['updated_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER);
            $insertary['updated_by'] = $post['user_id'];
            $res = $callConfig->updateRecord('property_tenant_details', $insertary, 'id', $post['rid']);
            $return['res'] = $res;
            $property_owner_id = $post['rid'];
        } else {
            $insertary['is_active'] = 0;
            $insertary['current_status'] = 0;
            $insertary['created_by'] = $post['user_id'];
            $insertary['created_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS);
            $res = $callConfig->insertRecord('property_tenant_details', $insertary);
            $return['res'] = $res;
            $property_owner_id = $res['LASTID'];
        }

        $update_query = "UPDATE property_owner_detail_areas SET property_tenant_detail_id = null WHERE property_tenant_detail_id = '" . $property_owner_id . "'";
        $callConfig->executeQuery($update_query);


        $implodeids = implode(",", (array) $post['property_owner_area_ids']);
        $update_query = "UPDATE property_owner_detail_areas SET property_tenant_detail_id = '" . $property_owner_id . "' WHERE id IN(" . $implodeids . ")";
        $callConfig->executeQuery($update_query);
        $return['update_query'] = $update_query;

        return $return;
    }
    private function calculateRentalBillingAreaDetails($podaajax, $property_tenant_detailsres, $monthincreas, $totaldays, &$monthlyrant, &$per_sq_ft_with_discount): array
    {
        $amount = $totalmonthly_tax = $rent_amount = $rent_total_amount = 0;
        $bill_detail = array();
        $rent_bill_to_be_raise_type = $property_tenant_detailsres->rent_bill_to_be_raise_type;
        $rent_escalation_after_month = $property_tenant_detailsres->rent_escalation_after_month;
        $rent_escalation_percentage = $property_tenant_detailsres->rent_escalation_percentage;
        $rent_escalation_after_every_month = ($rent_escalation_after_month != "") ? range($rent_escalation_after_month, 5000, $rent_escalation_after_month) : array();

        foreach ($podaajax as $keyyy => $poda) {
            $effectiveDays = ($totaldays != '0' && $totaldays != '') ? $totaldays : 1;
            if ($monthincreas == 1 && ($rent_bill_to_be_raise_type == "S" || $rent_bill_to_be_raise_type == "N")) {
                $rentcommencementstartdate = $property_tenant_detailsres->rent_commencement_date;
                $rentcommencementenddate = date('Y-m-t', strtotime($rentcommencementstartdate));
                $d1 = new DateTime($rentcommencementstartdate);
                $d2 = new DateTime($rentcommencementenddate);
                $rentcommencementdate_totalday = $d1->diff($d2)->days + 1;
                $mStart = new DateTime(date('Y-m-1', strtotime($rentcommencementstartdate)));
                $mothtotaldays = $mStart->diff($d2)->days + 1;

                if (empty($monthlyrant[$keyyy])) {
                    $hlfmonthperday = $poda['rent'] / $mothtotaldays;
                    $extra = ($rent_bill_to_be_raise_type == "N") ? $poda['rent'] : 0;
                    $monthlyrant[$keyyy] = ($hlfmonthperday * $rentcommencementdate_totalday) + $extra;
                    $per_sq_ft_with_discount[$keyyy] = $poda['per_sq_ft_with_discount'];
                }
            } elseif ($monthincreas == 2 && ($rent_bill_to_be_raise_type == "S" || $rent_bill_to_be_raise_type == "N")) {
                $monthlyrant[$keyyy] = $poda['rent'];
                $per_sq_ft_with_discount[$keyyy] = $poda['per_sq_ft_with_discount'];
            }
            if (empty($monthlyrant[$keyyy])) {
                $monthlyrant[$keyyy] = $poda['rent'];
                $per_sq_ft_with_discount[$keyyy] = $poda['per_sq_ft_with_discount'];
            }

            if ($rent_escalation_after_month != "" && $rent_escalation_after_month <= $monthincreas && $rent_escalation_percentage != "" && $rent_escalation_percentage != "0" && in_array($monthincreas, (array) $rent_escalation_after_every_month)) {
                $upd_pct = $rent_escalation_percentage ?: 0;
                $increase_rent = $monthlyrant[$keyyy] * ($upd_pct / 100);
                $monthlyrant[$keyyy] += $increase_rent;
                $per_sq_ft_with_discount[$keyyy] += $per_sq_ft_with_discount[$keyyy] * ($upd_pct / 100);
            }

            $perdayrante = $monthlyrant[$keyyy] / $effectiveDays;
            $rent_tax = 0;
            if ($property_tenant_detailsres->tax_percentage != "") {
                $rent_tax = $monthlyrant[$keyyy] * ($property_tenant_detailsres->tax_percentage / 100);
            }
            $totalmonthly_tax += $rent_tax;
            $rent_amount += $monthlyrant[$keyyy];
            $monthly_total = $monthlyrant[$keyyy] + $rent_tax;
            $rent_total_amount += $monthly_total;
            $amount += $monthly_total;
            $bill_detail[] = array('poda_and_rbt_id' => $poda['property_area_id'], 'name' => $poda['property_area_name'], 'monthly_rent' => round($monthlyrant[$keyyy], 2), 'rent_tax' => round($rent_tax, 2), 'rent' => round($monthly_total, 2), 'transaction_table' => 'property_owner_detail_areas', 'days' => null, 'per_day_rent' => round($perdayrante, 2), 'tax_percentage' => $property_tenant_detailsres->tax_percentage, 'property_area' => $poda['property_area'], 'per_sq_ft_with_discount' => $per_sq_ft_with_discount[$keyyy], 'uom' => $poda['uom'], 'parent_id' => 0, 'manually_rent' => $poda['manually_rent']);
        }
        return [$amount, $rent_amount, $totalmonthly_tax, $rent_total_amount, $bill_detail];
    }

    private function calculateRentalBillingUtilityDetails($rbt, &$amount, &$bill_detail): array
    {
        $total_utility_amount = 0;
        $utility_details = array();
        foreach ($rbt as $poda) {
            if ($poda['status'] == '0') {
                $utility_bill_amount = $poda['utility_bill_amount'] != "" ? $poda['utility_bill_amount'] : 0;
                $amount += $utility_bill_amount;
                $total_utility_amount += $utility_bill_amount;
                $utilityinvary = array('poda_and_rbt_id' => $poda['rental_bill_type_id'], 'name' => $poda['rental_bill_type_name'], 'monthly_rent' => $utility_bill_amount, 'rent_tax' => 0, 'rent' => $utility_bill_amount, 'transaction_table' => 'rental_utilities', 'property_area' => '0', 'per_sq_ft_with_discount' => '0', 'uom' => $poda['uom'], 'utility_bill_auto_and_fix' => $poda['utility_bill_auto_and_fix'], 'parent_id' => 0);

                if (RENTAL_INVOICE_SPLIT == "Y") {
                    $utility_details[] = $utilityinvary;
                } else {
                    $bill_detail[] = $utilityinvary;
                }

                if ($poda['utility_bill_auto_and_fix'] == "M" && !empty($poda['child_utility'])) {
                    foreach ($poda['child_utility'] as $cuvalue) {
                        if (($cuvalue['check'] ?? '') == "Y") {
                            $childInv = array('poda_and_rbt_id' => $cuvalue['rental_bill_type_id'], 'name' => $cuvalue['rental_bill_type_name'], 'monthly_rent' => 0, 'rent_tax' => 0, 'rent' => 0, 'transaction_table' => 'rental_utilities', 'property_area' => '0', 'per_sq_ft_with_discount' => '0', 'uom' => $cuvalue['uom'], 'parent_id' => $poda['rental_bill_type_id'], 'qty' => $cuvalue['qty'], 'condition' => $cuvalue['condition'], 'rate' => $cuvalue['rate']);
                            if (RENTAL_INVOICE_SPLIT == "Y") {
                                $utility_details[] = $childInv;
                            } else {
                                $bill_detail[] = $childInv;
                            }
                        }
                    }
                }
            }
        }
        return [$total_utility_amount, $utility_details];
    }

    private function calculateRentalDueDate($property_tenant_detailsres, $month_start_date): string
    {
        $due_bill_to_be_raise = str_pad($property_tenant_detailsres->due_bill_to_be_raise, 2, '0', STR_PAD_LEFT);
        if ($property_tenant_detailsres->due_bill_to_be_raise_type == 'DY') {
            return date('Y-m-d', strtotime($month_start_date . ' +' . $due_bill_to_be_raise . ' days'));
        }
        if ($property_tenant_detailsres->due_bill_to_be_raise_type == 'DT') {
            $due_date = date('Y-m-' . $due_bill_to_be_raise, strtotime($month_start_date . RentalConstants::PLUS_ONE_MONTH_SPACE));
            $due_dateyearmonth = date('Y-m-01', strtotime($month_start_date . RentalConstants::PLUS_ONE_MONTH_SPACE));
            $checkvaliddate = strtotime($due_date);
            return (date("Y-m-d", $checkvaliddate) != $due_date) ? date("Y-m-t", strtotime($due_dateyearmonth)) : date("Y-m-d", $checkvaliddate);
        }
        return $month_start_date;
    }

    public function insertRentalInvoices($get)
    {
        global $callConfig;
        $return = array('get' => $get);
        $property_tenant_detailssql = RentalConstants::SEL_PTD . $get['company_id'] . RentalConstants::AND_ID_APOS . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $property_tenant_detailsres = $callConfig->getRow($property_tenant_detailssql);
        if ($property_tenant_detailsres->lock_tenant == '0' && empty($get['extend_agreement_month'])) {
            return $return;
        }

        $sqlq = "SELECT month_start_date FROM property_tenant_billings WHERE  company_id = '" . $get['company_id'] . RentalConstants::AND_PTD_ID . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $property_tenant_billingsYD = array();
        foreach ($callConfig->getAllRows($sqlq) as $ptb) {
            $monthstartdate = date('Ym', strtotime($ptb->month_start_date));
            $property_tenant_billingsYD[$monthstartdate] = $monthstartdate;
        }

        $return['property_tenant_details'] = $property_tenant_detailsres;
        $rentastartdate = $property_tenant_detailsres->rent_commencement_date;
        $rentenddate = $property_tenant_detailsres->lease_period_end_date;
        if ($property_tenant_detailsres->rent_bill_to_be_raise_type == "N") {
            $rentastartdate = date('Y-m-d', strtotime($rentastartdate . RentalConstants::PLUS_ONE_MONTH_SPACE));
        }
        $startday = $property_tenant_detailsres->rent_bill_to_be_raise;
        $billinggeneratedate = date('Y-m-' . $startday, strtotime($rentastartdate));
        if ($property_tenant_detailsres->un_lock_tenant_date != "" && $property_tenant_detailsres->lock_tenant == "" && $property_tenant_detailsres->un_lock_tenant_date >= $billinggeneratedate) {
            $billinggeneratedate = $property_tenant_detailsres->un_lock_tenant_date;
        }

        $startDate = new DateTime($billinggeneratedate);
        if (!empty($get['extend_agreement_month'])) {
            $imploderenew = explode('-', $get['extend_agreement_month']);
            $renewdate = new DateTime($rentenddate);
            $endDate = new DateTime($renewdate->format($imploderenew[0] . '-' . $imploderenew[1] . '-' . $startday));
        } else {
            $endDate = new DateTime($rentenddate);
        }

        $monthincreas = 1;
        $monthlyrant = $per_sq_ft_with_discount = array();
        while ($startDate <= $endDate) {
            $month_start_date = $startDate->format('Y-m-' . $startday);
            $startDate->modify(RentalConstants::PLUS_ONE_MONTH);
            $month_end_date = date('Y-m-d', strtotime($startDate->format('Y-m-' . $startday) . ' -1 day'));
            $sdate = new DateTime($month_start_date);
            $edate2 = new DateTime($month_end_date);
            $totaldays = $sdate->diff($edate2)->days;

            $podaajax = json_decode($property_tenant_detailsres->property_owner_detail_areas, 1) ?: array();
            list($amount, $rent_amount, $totalmonthly_tax, $rent_total_amount, $bill_detail) = $this->calculateRentalBillingAreaDetails($podaajax, $property_tenant_detailsres, $monthincreas, $totaldays, $monthlyrant, $per_sq_ft_with_discount);

            $rbt = json_decode($property_tenant_detailsres->rental_bill_types, 1) ?: array();
            list($total_utility_amount, $utility_details) = $this->calculateRentalBillingUtilityDetails($rbt, $amount, $bill_detail);

            $due_date = $this->calculateRentalDueDate($property_tenant_detailsres, $month_start_date);
            $insertcust = array('company_id' => $get['company_id'], 'property_tenant_detail_id' => $property_tenant_detailsres->id, 'month_start_date' => $month_start_date, 'month_end_date' => $month_end_date, 'rent_amount' => $rent_amount, 'rent_tax' => $totalmonthly_tax, 'rent_total_amount' => $rent_total_amount, 'utility_amount' => $total_utility_amount, 'total_amount' => $amount, 'bill_detail' => json_encode($bill_detail), 'utility_details' => json_encode($utility_details), 'is_active' => 0, 'current_status' => 0, 'created_by' => $get['user_id'] ?? null, 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'rent_due_date' => $due_date, 'rent_transaction_date' => $month_start_date, 'utility_transaction_date' => $month_start_date, 'utility_due_date' => $due_date);

            $submonthstartdate = date("Ym", strtotime($month_start_date));
            if (empty($property_tenant_billingsYD[$submonthstartdate])) {
                $callConfig->insertRecord('property_tenant_billings', $insertcust);
            }
            $monthincreas++;
        }

        if (!empty($get['extend_agreement_month'])) {
            $imploderenew = explode('-', $get['extend_agreement_month']);
            $rentenddate = date($imploderenew[0] . "-" . $imploderenew[1] . "-d", strtotime($property_tenant_detailsres->lease_period_end_date));
            $updateary = array('lease_period_end_date' => $rentenddate, 'updated_by' => $get['user_id'], 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
            $extendary = array('company_id' => $get['company_id'], 'originator' => $get['user_id'], 'property_tenant_detail_id' => $get['rid'], 'property_tenant_details' => base64_encode(json_encode($property_tenant_detailsres)), 'extend_agreement_month_year' => $get['extend_agreement_month'], 'extend_agreement_datetime' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'is_active' => 0, 'current_status' => 0, 'created_by' => $get['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
            $callConfig->insertRecord('property_tenant_details_extends', $extendary);
        } else {
            $updateary = array('lock_tenant' => '0', 'lock_tenant_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'updated_by' => $get['user_id'] ?? null, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        }
        $callConfig->updateRecord('property_tenant_details', $updateary, 'id', $get['rid']);

        return $return;
    }
    public function getRentalAdminDashboard($get)
    {
        global $callConfig;
        $returnarray = array();
        $vendor_id = array();
        $todaydate = date("Y-m-d");
        $after45date = date('Y-m-d', strtotime($todaydate . ' -45 day'));
        if ($get['admin_type'] != 1 || $get['admin_access_employee'] == 'Y') {
            $condition = RentalConstants::AND_CREATED_BY . $get['user_id'] . "'";
            $originatorcondition = " AND originator = '" . $get['user_id'] . "'";
            $originatorcondition_join = " AND ptd.originator = '" . $get['user_id'] . "'";
        }
        $vendorssql = RentalConstants::SEL_CUSTOMERS . $get['company_id'] . "' " . $condition . RentalConstants::AND_IS_ACTIVE_0_SPACE;
        $vendorsres = $callConfig->getAllRows($vendorssql);
        foreach ($vendorsres as $vendors1) {
            $vendorsary[$vendors1->id] = $vendors1;
            $vendor_id[$vendors1->id] = $vendors1->id;
        }
        $returnarray['customers'] = $vendorsary;
        $vendorid = implode(',', $vendor_id);

        $supplierbillssql = "SELECT COUNT(*) as cnt FROM rental_invoices WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_PARTY_ID_IN . $vendorid . ") AND is_active = 0 " . $originatorcondition;
        $supplierbillsres = $callConfig->getRow($supplierbillssql);
        $returnarray['supplier_bill_cnt'] = $supplierbillsres->cnt;



        $billapproved = "SELECT count(*) as cnt FROM rental_invoices WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_PARTY_ID_IN . $vendorid . ") AND is_active = 0 AND current_status = " . ST_APPROVED . $originatorcondition;
        $billapprores = $callConfig->getRow($billapproved);
        $returnarray['users_cnt'] = $billapprores->cnt;

        $vendorslistsql = "SELECT vend.id, sb.party_id,sum(sb.total_amount) as total_amount, sum(sb.pending_amount) as pending_amount, vend.display_name,vend.image, vend.company, sb.due_date FROM rental_invoices sb, customers vend WHERE sb.company_id = '" . $get['company_id'] . "'  AND vend.company_id = sb.company_id AND sb.party_id = vend.id AND sb.is_active = vend.is_active AND sb.party_id IN(" . $vendorid . ") AND sb.current_status='" . ST_APPROVED . "' AND sb.is_active = 0 GROUP BY sb.party_id order by vend.created_date desc limit " . $get['vend_limit'];
        $vendorslistres = $callConfig->getAllRows($vendorslistsql);
        $returnarray['vendors_recent_join'] = $vendorslistres;


        $vendorslistsql = "SELECT vend.id, sb.party_id,sum(sb.total_amount) as total_amount, sum(sb.pending_amount) as pending_amount, vend.display_name,vend.image, vend.company, sb.due_date FROM rental_invoices sb, customers vend WHERE sb.company_id = '" . $get['company_id'] . "'  AND vend.company_id = sb.company_id AND sb.party_id = vend.id AND sb.is_active = vend.is_active AND sb.party_id IN(" . $vendorid . ") AND sb.current_status='" . ST_APPROVED . "' AND sb.is_active = 0 GROUP BY sb.party_id limit " . $get['vend_limit'];
        $vendorslistres = $callConfig->getAllRows($vendorslistsql);
        $returnarray['vendors_list'] = $vendorslistres;
        $returnarray['vendors_list_sql'] = $vendorslistsql;

        $vendorinvoicecntsql = "SELECT sum(if(current_status='" . ST_APPROVED . "',total_amount,0)) as total_amount, sum(if(current_status='" . ST_APPROVED . "' AND due_date <= '" . $todaydate . "',pending_amount,0)) as payment_due, sum(if(current_status='" . ST_APPROVED . "' AND due_date > '" . $todaydate . "',pending_amount,0)) as yet_to_due, sum(if(current_status='" . ST_APPROVED . "',pending_amount,0)) as total_pending FROM rental_invoices WHERE company_id = '1' AND party_id IN(" . $vendorid . ") AND is_active = 0 AND current_status NOT IN(" . ST_DRAFT . "," . ST_ACTIVE . ")";
        $vendorinvoicecntres = $callConfig->getrow($vendorinvoicecntsql);
        $returnarray['vendor_invoice_cnt'] = $vendorinvoicecntres;
        $returnarray['vendor_invoice_cnt_sql'] = $vendorinvoicecntsql;
        $transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, pay.transaction_no, pay.payment_date, pay.amount, pay.originator,pay.id FROM transaction_approval_logs tal, payments pay WHERE tal.company_id = '" . $get['company_id'] . "'  AND tal.company_id = pay.company_id AND  tal.transaction_table = 'payments' AND pay.cancel_payment = 'N' AND tal.user_id = '" . $get['user_id'] . "' AND tal.transaction_id = pay.id AND pay.is_active = 0";
        $transapprlogres = $callConfig->getAllRows($transapprlogsql);
        $returnarray['transaction_approval_logs'] = $transapprlogres;






        $query = "SELECT id, payment_terms, total_amount, party_id, due_date FROM rental_invoices WHERE company_id = " . $get['company_id'] . " AND (pending_amount IS NULL OR  pending_amount !=0) AND is_active = 0 " . $originatorcondition;
        $returnarray['bills_sql'] = $query;
        $returnarray['bills'] = $callConfig->getAllRows($query);



        $query = "SELECT ptb.id, ptb.total_amount, ptb.month_start_date, ptd.customer_id FROM property_tenant_billings ptb, property_tenant_details ptd WHERE ptb.company_id = " . $get['company_id'] . " AND ptb.company_id = ptd.company_id AND ptb.property_tenant_detail_id = ptd.id and ptb.month_start_date > '" . $todaydate . "' AND ptb.is_active = ptd.is_active AND ptb.is_active = 0 " . $originatorcondition_join;
        $returnarray['projection_report_sql'] = $query;
        $returnarray['projection_report'] = $callConfig->getAllRows($query);


        $rentalinvoiceslistsql = "SELECT ri.id, ri.transaction_number, ri.transaction_date, ri.party_name, ri.party_id, ri.current_status, ri.party_name, ri.amount, ri.pending_amount, ptb.generated_invoice, ptb.shared, ptb.shared, ptb.shared_datetime FROM rental_invoices ri, property_tenant_billings ptb WHERE ri.company_id = '" . $get['company_id'] . "' AND ri.company_id = ptb.company_id AND ptb.rental_invoice_id = ri.id AND ri.party_id IN(" . $vendorid . ") AND ri.current_status NOT IN(" . ST_DRAFT . ") AND ri.is_active = ptb.is_active AND ri.is_active = 0";
        $rentalinvoiceslistres = $callConfig->getAllRows($rentalinvoiceslistsql);
        $returnarray['rental_invoices_list_sql'] = $rentalinvoiceslistsql;
        $returnarray['rental_invoices_list'] = $rentalinvoiceslistres;

        //alltenat invoice
        $rentalinvoiceslistsql = "SELECT ri.id, ri.transaction_number, ri.transaction_date FROM rental_invoices ri WHERE ri.company_id = '" . $get['company_id'] . "' AND ri.current_status NOT IN(" . ST_DRAFT . ") AND ri.is_active = 0";
        $rentalinvoiceslistres = $callConfig->getAllRows($rentalinvoiceslistsql);
        $returnarray['all_rental_invoices_list'] = $rentalinvoiceslistres;
        // ------------------------

        $returnarray['tenant_billings'] = $this->getPropertyTenantBillings($get);


        $property_tenant_detailssql = "SELECT id, tenant_name,tenant_email,created_date FROM property_tenant_details WHERE company_id = " . $get['company_id'] . " AND created_date >='" . $after45date . "' AND created_date <= '" . $todaydate . " 23:59:59' AND parent_id = 0  AND is_active = 0 " . $originatorcondition . RentalConstants::LIMIT_5;
        $property_tenant_detailsres = $callConfig->getAllRows($property_tenant_detailssql);
        $returnarray['recent_create_contract'] = $property_tenant_detailsres;
        $returnarray['property_tenant_detailssql'] = $property_tenant_detailssql;

        $recent_expire_contractsql = "SELECT id, tenant_name,tenant_email,lease_period_end_date FROM property_tenant_details WHERE company_id = " . $get['company_id'] . " AND lease_period_end_date >='" . $after45date . "' AND lease_period_end_date <= '" . $todaydate . "' AND is_active = 0 " . $originatorcondition . RentalConstants::LIMIT_5;
        $recent_expire_contractres = $callConfig->getAllRows($recent_expire_contractsql);
        $returnarray['recent_expire_contract'] = $recent_expire_contractres;
        $returnarray['recent_expire_contractsql'] = $recent_expire_contractsql;

        $recent_expire_contractsql = "SELECT id, tenant_name,tenant_email,renewal_date FROM property_tenant_details WHERE company_id = " . $get['company_id'] . " AND parent_id != 0 AND renewal_date >='" . $after45date . "' AND renewal_date <= '" . $todaydate . "'  AND is_active = 0 " . $originatorcondition . RentalConstants::LIMIT_5;
        $recent_expire_contractres = $callConfig->getAllRows($recent_expire_contractsql);
        $returnarray['recent_renewal_contract'] = $recent_expire_contractres;
        $returnarray['recent_renewal_contractsql'] = $recent_expire_contractsql;

        $top_rent_sql = "SELECT id, tenant_name,tenant_email,monthly_rent FROM property_tenant_details WHERE company_id = " . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_TRAILING_SPACE . $originatorcondition . " ORDER BY monthly_rent ASC limit 5";
        $top_rent_res = $callConfig->getAllRows($top_rent_sql);
        $returnarray['top_rent_res'] = $top_rent_res;


        $top_oldest_sql = "SELECT id, tenant_name,tenant_email,DATEDIFF(lease_period_end_date, rent_commencement_date) as days FROM property_tenant_details WHERE company_id = " . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_TRAILING_SPACE . $originatorcondition . " ORDER BY DATEDIFF(lease_period_end_date, rent_commencement_date) DESC limit 5";
        $top_oldest_res = $callConfig->getAllRows($top_oldest_sql);
        $returnarray['top_oldest'] = $top_oldest_res;

        return $returnarray;
    }
    public function getRentalDashboardChart($get)
    {
        global $callConfig;
        $condition = '';
        $customer_id = array();
        if ($get['admin_type'] != 1 || $get['admin_access_employee'] == 'Y') {
            $custcondition = RentalConstants::AND_CREATED_BY . $get['user_id'] . "'";
        }
        $vendorssql = RentalConstants::SEL_CUSTOMERS . $get['company_id'] . "' " . $custcondition . RentalConstants::AND_IS_ACTIVE_0_SPACE;
        $vendorsres = $callConfig->getAllRows($vendorssql);
        foreach ($vendorsres as $vendors1) {
            $customer_id[$vendors1->id] = $vendors1->id;
        }
        $customersid = implode(',', (array) $customer_id);
        $year = $get['reimbursementsyear'];
        $startdate = date($year . '-01-01');
        $lastdate = date($year . '-31-12');
        $montharay = array('1' => '01', '2' => '02', '3' => '03', '4' => '04', '5' => '05', '6' => '06', '7' => '07', '8' => '08', '9' => '09', '10' => '10', '11' => '11', '12' => '12');
        $expensessql = "SELECT sum(total_amount) as total_amount, sum(pending_amount) as pending_amount, MONTH(due_date) as month,YEAR(due_date) as year FROM `rental_invoices` WHERE company_id = '" . $get['company_id'] . "' " . $condition . " AND due_date >= '" . $startdate . "' AND due_date <= '" . $lastdate . RentalConstants::AND_PARTY_ID_IN . $customersid . ") AND is_active = 0 GROUP BY YEAR(due_date), MONTH(due_date)";
        $expensesres = $callConfig->getAllRows($expensessql);
        foreach ($expensesres as $empexp) {
            $expensetotal[$empexp->month] = $empexp->total_amount;
            $expensepending[$empexp->month] = $empexp->pending_amount;
        }
        foreach ($montharay as $monthary => $moth) {
            $expensetotalamt = isset($expensetotal[$monthary]) ? $expensetotal[$monthary] : "0";
            $expensependingamt = isset($expensepending[$monthary]) ? $expensepending[$monthary] : "0";
            $totalpendin = $expensetotalamt - $expensependingamt;
            $expensemontharray[] = array("date" => date($year . '-' . $moth . '-01'), "market1" => round($expensetotalamt), "market2" => round($totalpendin));
        }
        return $expensemontharray;
    }
    public function getPropertyTenantBillings($get)
    {
        global $callConfig;
        $return = array();
        $month = $get['month'];
        $year = $get['year'];
        if ($get['admin_type'] != 1 || $get['admin_access_employee'] == 'Y') {
            $condition = " AND ptd.originator = '" . $get['user_id'] . "'";
        }
        $property_tenant_billingssql = "SELECT ptb.id, ptb.month_start_date, ptb.rent_amount,ptb.rent_tax,ptb.utility_amount, ptb.total_amount, ptb.bill_detail, ptb.utility_details , ptb.ready_to_create_invoice, ptb.rental_invoice_id, ptb.generated_invoice, ptb.generated_invoice_datetime, ptb.shared, ptb.shared_datetime, ptb.utility_rental_invoice_id, ptb.utility_generated_invoice, ptb.utility_generated_invoice_datetime, ptb.utility_pdf_name, ptb.utility_shared, ptb.utility_shared_datetime, ptd.property_owner_id, ptd.property_owner_detail_id, ptd.customer_id, ptd.tenant_name FROM property_tenant_billings ptb, property_tenant_details ptd WHERE ptb.company_id = '" . $get['company_id'] . "' AND ptb.company_id = ptd.company_id AND MONTH(ptb.month_start_date) = '" . $month . "' AND YEAR(ptb.month_start_date) = '" . $year . "' AND ptb.is_active = ptd.is_active AND ptb.property_tenant_detail_id = ptd.id AND ptb.is_active = 0" . $condition;
        $property_tenant_billingsres = $callConfig->getAllRows($property_tenant_billingssql);
        $return['property_tenant_billings_sql'] = $property_tenant_billingssql;
        $return['property_tenant_billings'] = $property_tenant_billingsres;

        $property_ownerssql = RentalConstants::SEL_PROPERTY_OWNERS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $property_ownersres = $callConfig->getAllRows($property_ownerssql);
        $property_owners = array();
        foreach ($property_ownersres as $po) {
            $property_owners[$po->id] = $po;
        }
        $return['property_owners'] = $property_owners;

        $property_owner_detailssql = RentalConstants::SEL_POD . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $property_owner_detailsres = $callConfig->getAllRows($property_owner_detailssql);
        $property_owner_details = array();
        foreach ($property_owner_detailsres as $po) {
            $property_owner_details[$po->id] = $po;
        }
        $return['property_owner_details'] = $property_owner_details;

        $rental_utilitiessql = "SELECT id, type_name FROM rental_utilities WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $rental_utilitiesres = $callConfig->getAllRows($rental_utilitiessql);
        $return['rental_bill_types'] = $rental_utilitiesres;
        return $return;
    }
    private function updateUtilityBillingDetails($ptbres, $post, $get): array
    {
        $utility_amount = $utility_tax = 0;
        $utility_details = json_decode($ptbres->utility_details, 1) ?: array();
        foreach ($post['rent'] as $seq => $rent) {
            $utility_details[$seq]['rent'] = $rent;
            $utility_details[$seq]['remarks'] = $post['remarks'][$seq] ?? '';
            $utility_details[$seq]['uom'] = !empty($post['uom'][$seq]) ? $post['uom'][$seq] : '';
            $utility_details[$seq]['qty'] = !empty($post['qty'][$seq]) ? $post['qty'][$seq] : 1;
            $utility_details[$seq]['rate'] = !empty($post['rate'][$seq]) ? $post['rate'][$seq] : $rent;
            $utility_details[$seq]['tax_percentage'] = !empty($post['utility_gst_percentage'][$seq]) ? $post['utility_gst_percentage'][$seq] : 0;
            $utility_details[$seq]['rent_tax'] = !empty($post['utility_gst_total_tax'][$seq]) ? $post['utility_gst_total_tax'][$seq] : 0;
            $utility_details[$seq]['monthly_rent'] = !empty($post['utility_gst_without_tax'][$seq]) ? $post['utility_gst_without_tax'][$seq] : 0;
            $sw = $post['utility_gst_switch'][$seq] ?? '';
            $utility_details[$seq]['utility_gst_switch'] = (($post['utility_gst'][$sw] ?? '') == "Y") ? "Y" : "N";
        }
        foreach ($utility_details as $biilrent) {
            if (($biilrent['utility_bill_auto_and_fix'] ?? '') != "M") {
                $utility_amount += $biilrent['rent'] ?? 0;
            } else {
                $rent_tax = (!empty($biilrent['rent_tax']) && $biilrent['rent_tax'] != '0') ? $biilrent['rent_tax'] : 0;
                $utility_tax += $rent_tax;
                $utility_amount += $rent_tax;
            }
        }
        return array('utility_amount' => $utility_amount, 'utility_tax' => $utility_tax, 'total_amount' => $utility_amount + $ptbres->rent_total_amount, 'utility_details' => json_encode($utility_details), 'updated_by' => $get['user_id'], 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'utility_transaction_date' => $this->dateconvertdbformat($post['transaction_date']), 'utility_due_date' => $this->dateconvertdbformat($post['due_date']), 'utility_additional_notes' => $post['additional_notes'] ?? null);
    }

    private function updateAmcBillingDetails($ptbres, $post, $get): array
    {
        $utility_amount = $utility_tax = 0;
        $utility_amc_details = json_decode($ptbres->utility_amc_details, 1) ?: array();
        foreach ($post['rent'] as $seq => $rent) {
            $utility_amc_details[$seq]['rent'] = $rent;
            $utility_amc_details[$seq]['remarks'] = $post['remarks'][$seq] ?? '';
            $utility_amc_details[$seq]['uom'] = !empty($post['uom'][$seq]) ? $post['uom'][$seq] : '';
            $utility_amc_details[$seq]['qty'] = !empty($post['qty'][$seq]) ? $post['qty'][$seq] : 1;
            $utility_amc_details[$seq]['rate'] = !empty($post['rate'][$seq]) ? $post['rate'][$seq] : $rent;
            $utility_amc_details[$seq]['tax_percentage'] = !empty($post['utility_gst_percentage'][$seq]) ? $post['utility_gst_percentage'][$seq] : 0;
            $utility_amc_details[$seq]['rent_tax'] = !empty($post['utility_gst_total_tax'][$seq]) ? $post['utility_gst_total_tax'][$seq] : 0;
            $utility_amc_details[$seq]['monthly_rent'] = !empty($post['utility_gst_without_tax'][$seq]) ? $post['utility_gst_without_tax'][$seq] : 0;
            $sw = $post['utility_gst_switch'][$seq] ?? '';
            $utility_amc_details[$seq]['utility_gst_switch'] = (($post['utility_gst'][$sw] ?? '') == "Y") ? "Y" : "N";
        }
        foreach ($utility_amc_details as $biilrent) {
            if (($biilrent['utility_bill_auto_and_fix'] ?? '') != "M") {
                $utility_amount += $biilrent['rent'] ?? 0;
            } else {
                $rent_tax = (!empty($biilrent['rent_tax']) && $biilrent['rent_tax'] != '0') ? $biilrent['rent_tax'] : 0;
                $utility_tax += $rent_tax;
                $utility_amount += $rent_tax;
            }
        }
        return array('utility_amc_amount' => $utility_amount, 'utility_amc_tax' => $utility_tax, 'total_amount' => $utility_amount + $ptbres->rent_total_amount, 'utility_amc_details' => json_encode($utility_amc_details), 'updated_by' => $get['user_id'], 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'utility_amc_transaction_date' => $this->dateconvertdbformat($post['transaction_date']), 'utility_amc_due_date' => $this->dateconvertdbformat($post['due_date']), 'utility_amc_additional_notes' => $post['additional_notes'] ?? null);
    }

    public function updatePropertyTenantBillings($get, $post)
    {
        global $callConfig;
        $return = array('post' => $post);
        $property_tenant_billings_id = $post['property_tenant_billing_id'];
        $ptbsql = "SELECT bill_detail, utility_details, utility_amc_details, rent_tax, total_amount , rent_total_amount FROM property_tenant_billings WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $property_tenant_billings_id . RentalConstants::AND_IS_ACTIVE_0;
        $ptbres = $callConfig->getRow($ptbsql);
        $get['bill_type'] = $post['bill_type'];

        if ($post['bill_type'] == 'U') {
            $updateary = $this->updateUtilityBillingDetails($ptbres, $post, $get);
            $return['utility_detail_updateary'] = $updateary;
        } elseif ($post['bill_type'] == 'AMC') {
            $updateary = $this->updateAmcBillingDetails($ptbres, $post, $get);
            $return['utility_detail_updateary'] = $updateary;
        } else {
            $bill_detail = json_decode($ptbres->bill_detail, 1) ?: array();
            $totalrent = $totaltax = $totaltaxwithrent = 0;
            foreach ($post['rent'] as $seq => $rent) {
                $bill_detail[$seq]['rent'] = $rent;
                $bill_detail[$seq]['remarks'] = $post['remarks'][$seq] ?? '';
                $tax_percentage = $bill_detail[$seq]['tax_percentage'] ?? 0;
                $tax = ($tax_percentage != "" && $tax_percentage != '0') ? ($rent * ($tax_percentage / 100)) : 0;
                $totalrent += $rent;
                $totaltax += $tax;
                $totaltaxwithrent += ($rent + $tax);
            }
            $updateary = array('rent_amount' => $totalrent, 'rent_tax' => $totaltax, 'rent_total_amount' => $totaltaxwithrent, 'total_amount' => $totaltaxwithrent + $ptbres->utility_amount, 'bill_detail' => json_encode($bill_detail), 'updated_by' => $get['user_id'], 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'rent_transaction_date' => $this->dateconvertdbformat($post['transaction_date']), 'rent_due_date' => $this->dateconvertdbformat($post['due_date']), 'rent_additional_notes' => $post['additional_notes'] ?? null);
            $return['updateary'] = $updateary;
        }

        $callConfig->updateRecord('property_tenant_billings', $updateary, 'id', $property_tenant_billings_id);
        if (($post['shared'] ?? '') == '0') {
            $this->insertRentalInvoicesNew($get, $property_tenant_billings_id, '0');
        }
        return $return;
    }
    public function renatalInvoiceSend($get, $property_tenant_billings_id = "")
    {
        global $callConfig;
        $return = array();
        if ($property_tenant_billings_id == "") {
            $property_tenant_billings_id = $get['property_tenant_billings_id'];
        }
        $getfield = "rental_invoice_id, pdf_name";
        if ($get['bill_type'] == 'U') {
            $getfield = "utility_rental_invoice_id as rental_invoice_id, utility_pdf_name as pdf_name";
            $invoicetable = "utility_rental_invoices";
        } elseif ($get['bill_type'] == 'AMC') {
            $getfield = "utility_amc_rental_invoice_id as rental_invoice_id, utility_amc_pdf_name as pdf_name";
            $invoicetable = "utility_amc_rental_invoices";
        } else {
            $invoicetable = "rental_invoices";
        }
        $ptbsql1 = "SELECT " . $getfield . " FROM property_tenant_billings WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $property_tenant_billings_id . RentalConstants::AND_IS_ACTIVE_0;
        $return['ptbsql1'] = $ptbsql1;
        $ptbres1 = $callConfig->getRow($ptbsql1);
        if ($ptbres1->pdf_name != "") {
            $attachment_file_path = SITEROOT . RentalConstants::UPLOADS_RENTAL_INVOICES . $ptbres1->pdf_name;
        } else {
            $get['transaction_table'] = $invoicetable;
            $generatepdf = $this->generateTenantInvoicePDF($get, $ptbres1->rental_invoice_id);
            $attachment_file_path = SITEROOT . RentalConstants::UPLOADS_RENTAL_INVOICES . $generatepdf;
        }

        $rental_invoicessql = RentalConstants::SEL_FROM . $invoicetable . RentalConstants::WHERE_COMPANY_ID . $get['company_id'] . RentalConstants::AND_ID_APOS . $ptbres1->rental_invoice_id . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $rental_invoicesres = $callConfig->getRow($rental_invoicessql);

        $originaterssql = "SELECT display_name, user_email FROM application_users WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $rental_invoicesres->originator . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $originatersres = $callConfig->getRow($originaterssql);

        $emailarray['to_company_id'] = $get['company_id'];
        $emailarray['session_company_id'] = $get['company_id'];
        $emailarray['user_company_id'] = $get['company_id'];
        $emailarray['user_display_name'] = $rental_invoicesres->party_name;
        $emailarray['user_email_id'] = $rental_invoicesres->party_email;
        $emailarray['transaction_number'] = $rental_invoicesres->transaction_number;
        $emailarray['transaction_date'] = $rental_invoicesres->transaction_date;
        $emailarray['transaction_due_date'] = $rental_invoicesres->due_date;
        $emailarray['total_amount'] = $rental_invoicesres->total_amount;
        $emailarray['created_by'] = $get['user_id'];
        $emailarray['transaction_originater_name'] = $originatersres->display_name;
        $emailarray['transaction_originater_email_id'] = $originatersres->user_email;
        $emailarray['attachment'] = "Y";
        $emailarray['attachment_file_path'] = $attachment_file_path;
        $emailarray['attachment_file_name'] = $ptbres1->pdf_name;
        $notarry = array();
        $email = $this->sendNotificationAndEmails($notarry, $emailarray, 33);
        $retrun['email'] = $email;
        if ($get['bill_type'] == 'U') {
            $updateary = array('utility_shared' => '0', 'utility_shared_datetime' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        } elseif ($get['bill_type'] == 'AMC') {
            $updateary = array('utility_amc_shared' => '0', 'utility_amc_shared_datetime' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        } else {
            $updateary = array('shared' => '0', 'shared_datetime' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        }
        $res = $callConfig->updateRecord('property_tenant_billings', $updateary, 'id', $property_tenant_billings_id);
        $return['res'] = $res;
        return $return;
    }
    private function resolveRentalInvoiceConfig($ptbres, $bill_type): array
    {
        $ref_bill_type = "";
        $rent_tax = 0;
        if (RENTAL_INVOICE_SPLIT == 'Y') {
            if ($bill_type == 'U') {
                $podaajax = json_decode($ptbres->utility_details, 1) ?: array();
                $rent_tax = (!empty($ptbres->utility_tax) && $ptbres->utility_tax != '0') ? $ptbres->utility_tax : 0;
                $total_amount = $ptbres->utility_amount;
                $rent_amount = (!empty($ptbres->utility_amount) && $ptbres->utility_amount != '0') ? ($ptbres->utility_amount - $rent_tax) : 0;
                $invoicetable = "utility_rental_invoices";
                $invoiceitemtable = "utility_rental_invoice_items";
                $invoiceitemtaxtable = "utility_rental_invoice_item_taxes";
                $transaction_date = $this->dateconvertdbformat($ptbres->utility_transaction_date);
                $due_date = $ptbres->utility_due_date;
                $additional_notes = $ptbres->utility_additional_notes;
                $ref_bill_type = "U";
            } elseif ($bill_type == 'AMC') {
                $podaajax = json_decode($ptbres->utility_amc_details, 1) ?: array();
                $rent_tax = (!empty($ptbres->utility_amc_tax) && $ptbres->utility_amc_tax != '0') ? $ptbres->utility_amc_tax : 0;
                $total_amount = $ptbres->utility_amc_amount;
                $rent_amount = (!empty($ptbres->utility_amc_amount) && $ptbres->utility_amc_amount != '0') ? ($ptbres->utility_amc_amount - $rent_tax) : 0;
                $invoicetable = "utility_amc_rental_invoices";
                $invoiceitemtable = "utility_amc_rental_invoice_items";
                $invoiceitemtaxtable = "utility_amc_rental_invoice_item_taxes";
                $transaction_date = $this->dateconvertdbformat($ptbres->utility_amc_transaction_date);
                $due_date = $ptbres->utility_amc_due_date;
                $additional_notes = $ptbres->utility_amc_additional_notes;
                $ref_bill_type = "U";
            } else {
                $podaajax = json_decode($ptbres->bill_detail, 1) ?: array();
                $rent_tax = (!empty($ptbres->rent_tax) && $ptbres->rent_tax != '0') ? $ptbres->rent_tax : 0;
                $total_amount = $ptbres->rent_total_amount;
                $rent_amount = (!empty($ptbres->rent_amount) && $ptbres->rent_amount != '0') ? $ptbres->rent_amount : ($total_amount - $rent_tax);
                $invoicetable = "rental_invoices";
                $invoiceitemtable = "rental_invoice_items";
                $invoiceitemtaxtable = "rental_invoice_item_taxes";
                $transaction_date = $this->dateconvertdbformat($ptbres->rent_transaction_date);
                $due_date = $ptbres->rent_due_date;
                $additional_notes = $ptbres->rent_additional_notes;
            }
        } else {
            $podaajax = json_decode($ptbres->bill_detail, 1) ?: array();
            $rent_tax = (!empty($ptbres->rent_tax) && $ptbres->rent_tax != '0') ? $ptbres->rent_tax : 0;
            $total_amount = $ptbres->total_amount;
            $rent_amount = (!empty($ptbres->rent_amount) && $ptbres->rent_amount != '0') ? $ptbres->rent_amount : ($total_amount - $rent_tax);
            $invoicetable = "rental_invoices";
            $invoiceitemtable = "rental_invoice_items";
            $invoiceitemtaxtable = "rental_invoice_item_taxes";
            $transaction_date = $this->dateconvertdbformat($ptbres->rent_transaction_date);
            $due_date = $ptbres->rent_due_date;
            $additional_notes = $ptbres->rent_additional_notes;
        }
        return [$invoicetable, $invoiceitemtable, $invoiceitemtaxtable, $podaajax, $rent_tax, $total_amount, $rent_amount, $ref_bill_type, $transaction_date, $due_date, $additional_notes];
    }

    private function resolveCustomerAddressDetails($company_id, $customer): array
    {
        global $callConfig;
        $countries = $states = $cities = array();
        foreach ($callConfig->getAllRows("SELECT id,country_name FROM countries WHERE company_id = '" . $company_id . RentalConstants::AND_IS_ACTIVE_0) as $cutr) {
            $countries[$cutr->id] = $cutr->country_name;
        }
        $statessql = "SELECT id,state_name FROM states WHERE company_id = '" . $company_id . "' AND (id = '" . $customer->billing_state . "' OR id = '" . $customer->shipping_state . "') AND is_active = 0";
        foreach ($callConfig->getAllRows($statessql) as $cutr) {
            $states[$cutr->id] = $cutr->state_name;
        }
        $citiessql = "SELECT id,city_name FROM cities WHERE company_id = '" . $company_id . "' AND (id = '" . $customer->billing_city . "' OR id = '" . $customer->shipping_city . "') AND is_active = 0";
        foreach ($callConfig->getAllRows($citiessql) as $cutr) {
            $cities[$cutr->id] = $cutr->city_name;
        }
        $party_address = ($customer->billing_address ?? '') . " " . ($cities[$customer->billing_city] ?? '') . " " . ($states[$customer->billing_state] ?? '') . " " . ($customer->billing_post_code ?? '');
        return [$party_address, $states[$customer->billing_state] ?? ''];
    }

    public function insertRentalInvoicesNew($get, $property_tenant_billings_id, $status = '')
    {
        global $callConfig;
        $return = array();
        $ptbsql = "SELECT id,property_tenant_detail_id,bill_detail,utility_details,month_start_date, rent_amount, rent_tax, rent_total_amount, utility_amount, total_amount, rent_due_date, rent_additional_notes, utility_transaction_date,utility_due_date,utility_additional_notes,rent_transaction_date, utility_tax, utility_amc_details, utility_amc_amount, utility_amc_tax, utility_amc_transaction_date, utility_amc_due_date, utility_amc_additional_notes FROM property_tenant_billings WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $property_tenant_billings_id . RentalConstants::AND_IS_ACTIVE_0;
        $ptbres = $callConfig->getRow($ptbsql);

        list($invoicetable, $invoiceitemtable, $invoiceitemtaxtable, $podaajax, $rent_tax, $total_amount, $rent_amount, $ref_bill_type, $transaction_date, $due_date, $additional_notes) = $this->resolveRentalInvoiceConfig($ptbres, $get['bill_type'] ?? '');

        $property_tenant_detailssql = RentalConstants::SEL_PTD . $get['company_id'] . RentalConstants::AND_ID_APOS . $ptbres->property_tenant_detail_id . RentalConstants::AND_IS_ACTIVE_0;
        $return['property_tenant_detailssql'] = $property_tenant_detailssql;
        $property_tenant_detailsres = $callConfig->getRow($property_tenant_detailssql);
        $serial_transaction_number = $this->toGetApplicationSerialNumber($invoicetable);
        $transaction_number = $this->toGetApplicationRentalOwnerSerialNumber($invoicetable, $property_tenant_detailsres->property_owner_id, $transaction_date);

        $ausql = "SELECT id,default_state_id FROM application_users WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $property_tenant_detailsres->originator . RentalConstants::AND_IS_ACTIVE_0;
        $aures = $callConfig->getRow($ausql);

        $property_ownerssql = "SELECT * FROM property_owners WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $property_tenant_detailsres->property_owner_id . RentalConstants::AND_IS_ACTIVE_0;
        $property_ownersres = $callConfig->getRow($property_ownerssql);

        $owner_additional_detail = array('owner_name' => $property_ownersres->owner_name, 'owner_company_name' => $property_ownersres->owner_company_name, 'address' => str_replace(["'", "\n", "\r"], "", $property_ownersres->address), 'gst_no' => $property_ownersres->gst_no, 'pan_no' => $property_ownersres->pan_no, 'bank_id' => $property_ownersres->bank_id, 'ifsc_code' => $property_ownersres->ifsc_code, 'bank_account_no' => $property_ownersres->bank_account_no, 'account_type' => $property_ownersres->account_type, 'contact_no' => $property_ownersres->contact_no, 'email_id' => $property_ownersres->email_id);
        $owner_additional_details = json_encode((array) $owner_additional_detail);

        $customersql = RentalConstants::SEL_ALL_CUSTOMERS . $get['company_id'] . RentalConstants::AND_ID_APOS . $property_tenant_detailsres->customer_id . RentalConstants::AND_IS_ACTIVE_0;
        $customer = $callConfig->getRow($customersql);

        list($party_address, $place_of_supply) = $this->resolveCustomerAddressDetails($get['company_id'], $customer);

        $current_status = ($status != "") ? $status : ST_APPROVED;
        $insertary = array('company_id' => $get['company_id'], 'property_tenant_billing_id' => $property_tenant_billings_id, 'property_owner_id' => $property_tenant_detailsres->property_owner_id, 'property_owner_detail_id' => $property_tenant_detailsres->property_owner_detail_id, 'parent_ref_id' => $property_tenant_detailsres->id, 'ref_type' => 'property_tenant_details', 'transaction_number' => $transaction_number, 'transaction_date' => $transaction_date, 'originator' => $get['user_id'], 'party_id' => $property_tenant_detailsres->customer_id, 'party_name' => $customer->company, 'party_email' => $customer->email, 'party_phone' => $customer->phone, 'party_address' => $party_address, 'amount' => $rent_amount, 'tax_amount' => $rent_tax, 'total_amount' => $total_amount, 'pending_amount' => $total_amount, 'originator_currency_id' => 1, 'currency_id' => 1, 'originator_state_id' => $aures->default_state_id ?? 0, 'supplier_state_id' => $customer->billing_state, 'place_of_supply' => $place_of_supply, 'due_date' => $due_date, 'owner_additional_details' => $owner_additional_details, 'notes' => $additional_notes, 'is_active' => 0, 'current_status' => $current_status, 'ref_bill_type' => $ref_bill_type, 'created_by' => $get['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));

        $res = $callConfig->insertRecord($invoicetable, $insertary);
        $transaction_id = $res['LASTID'] ?? 0;
        $return['res'] = $res;

        $hsn_and_sac_codes = array();
        foreach ($callConfig->getAllRows(RentalConstants::SEL_HSN_SAC . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0) as $hsn) {
            $hsn_and_sac_codes[$hsn->id] = $hsn;
        }

        $tax_state = (($aures->default_state_id ?? 0) == $customer->billing_state) ? "S" : "I";
        foreach ($podaajax as $poda) {
            $hsnid = $poda['hsn_and_sac_code_id'] ?? $property_tenant_detailsres->hsn_and_sac_code_id;
            $hsnDetails = $hsn_and_sac_codes[$hsnid] ?? null;
            $tax_amount = (!empty($poda['rent_tax']) && $poda['rent_tax'] != '0') ? $poda['rent_tax'] : 0;
            $per_sq_ft_with_discount = $poda['per_sq_ft_with_discount'] ?? 0;
            $item_quantity = $poda['property_area'] ?? 1;
            $item_price = (!empty($poda['monthly_rent']) && $poda['monthly_rent'] != '0') ? $poda['monthly_rent'] : $poda['rent'];
            $insert_item = array('company_id' => $get['company_id'], 'transaction_id' => $transaction_id, 'item_id' => $poda['poda_and_rbt_id'], 'item_name' => $poda['name'], 'description' => $poda['remarks'] ?? '', 'purchase_uom' => $poda['uom'] ?? '', 'item_quantity' => $item_quantity, 'item_price' => $item_price, 'item_amount' => $item_price, 'tax_amount' => $tax_amount, 'total_amount' => $item_price + $tax_amount, 'transaction_table' => $poda['transaction_table'] ?? '', 'per_sq_ft_with_discount' => $per_sq_ft_with_discount, 'utility_bill_auto_and_fix' => $poda['utility_bill_auto_and_fix'] ?? null, 'parent_id' => $poda['parent_id'] ?? 0, 'qty' => $poda['qty'] ?? 1, 'rate' => $poda['rate'] ?? 0, 'condition' => $poda['condition'] ?? '', 'operation_symbol' => $poda['operation_symbol'] ?? '', 'is_active' => 0, 'created_by' => $get['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));

            $res_item = $callConfig->insertRecord($invoiceitemtable, $insert_item);
            $transaction_item_id = $res_item['LASTID'] ?? 0;

            if ($tax_amount > 0 && $hsnDetails) {
                $insert_tax = array('company_id' => $get['company_id'], 'transaction_id' => $transaction_id, 'transaction_item_id' => $transaction_item_id, 'tax_id' => $hsnid, 'tax_name' => $hsnDetails->code, 'tax_value' => $hsnDetails->gst, 'amount' => $item_price, 'tax_amount' => $tax_amount, 'total_amount' => $item_price + $tax_amount, 'tax_type' => $tax_state, 'is_active' => 0, 'current_status' => 0, 'created_by' => $get['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
                $callConfig->insertRecord($invoiceitemtaxtable, $insert_tax);
            }
        }

        $invField = (($get['bill_type'] ?? '') == 'U') ? 'utility_rental_invoice_id' : ((($get['bill_type'] ?? '') == 'AMC') ? 'utility_amc_rental_invoice_id' : 'rental_invoice_id');
        $callConfig->updateRecord('property_tenant_billings', array($invField => $transaction_id), 'id', $property_tenant_billings_id);
        $this->tocreatejounalvoucherbaseallcases($invoicetable, $transaction_id);

        return $return;
    }
    public function generateTenantInvoicePDF($get, $rid = "")
    {
        global $callConfig;
        if ($rid == "") {
            $rid = $get['rid'];
        } else {
            $get['rid'] = $rid;
        }
        $postarray = $this->getRentalInvoicePrint($get);
        $postarray['callapi'] = 'Y';
        $api_url = SITEURL . 'views/custom_view_rental_invoices.php';
        $postdata = http_build_query($postarray);
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => $postdata
            )
        );
        $context = stream_context_create($opts);
        $content = file_get_contents($api_url, false, $context);
        $options = new Options();
        $options->set('chroot', realpath(''));
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->set_option('margin-top', '10mm');
        $dompdf->set_option('margin-right', '10mm');
        $dompdf->set_option('margin-bottom', '10mm');
        $dompdf->set_option('margin-left', '10mm');
        $dompdf->loadHtml($content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = "rental_invoice_" . time() . '.pdf';
        $sFilePath = SITEROOT . RentalConstants::UPLOADS_RENTAL_INVOICES . $filename;
        $output = $dompdf->output();
        file_put_contents($sFilePath, $output);
        $updateary = array('pdf_name' => $filename, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        $callConfig->updateRecord('property_tenant_billings', $updateary, 'rental_invoice_id', $rid);
        return $filename;
    }
    public function getTenantCustomers($get)
    {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $condition = "";
        $sortby = " ORDER BY display_name ASC";
        $limit = "LIMIT 10";
        if ($get['filter'] == "Y") {
            if ($get['alphabet'] != "") {
                $condition .= " AND display_name like '" . $get['alphabet'] . "%'";
            }
            if ($get['searchname'] != "") {
                $condition = "AND (display_name LIKE '%" . $get['searchname'] . "%' OR email LIKE '%" . $get['searchname'] . "%' OR phone LIKE '%" . $get['searchname'] . "%' OR company LIKE '%" . $get['searchname'] . "%')";
            }
            if ($get['sort_by'] != "") {
                $sortby = " ORDER BY " . $get['sort_by'] . " ASC ";
            }
            if ($get['limit'] != "") {
                $limit = " limit " . $get['limit'];
            }
        }
        if ($get['admin_type'] != 1) {
            $condition .= RentalConstants::AND_CREATED_BY . $get['user_id'] . "'";
        }
        $custsql = "SELECT id, email, phone, company, display_name, image, customer_since FROM customers WHERE company_id = '" . $get['company_id'] . "' " . $condition . RentalConstants::AND_IS_ACTIVE_0_TRAILING_SPACE . $sortby . " " . $limit;
        $custres = $callConfig->getAllRows($custsql);
        $return['customers_sql'] = $custsql;
        $return['customers'] = $custres;

        $rilistsql = "SELECT ri1.id, ri1.party_id, ri1.due_date, ri1.pending_amount FROM rental_invoices ri1 WHERE ri1.company_id = '" . $get['company_id'] . "' AND ri1.due_date = (SELECT ri2.due_date FROM rental_invoices ri2 WHERE ri2.company_id = ri1.company_id AND ri2.due_date >= CURDATE() AND ri2.party_id = ri1.party_id AND ri2.pending_amount != 0 AND ri2.is_active = ri1.is_active ORDER BY due_date ASC limit 1) AND ri1.is_active = 0 GROUP BY ri1.party_id";
        $rilistres = $callConfig->getAllRows($rilistsql);
        $next_payment_invoices = array();
        foreach ($rilistres as $rilist) {
            $next_payment_invoices[$rilist->party_id] = $rilist;
        }
        $return['next_payment_invoices'] = $next_payment_invoices;
        $riduelistsql = "SELECT party_id, due_date, SUM(pending_amount) as pending_amount FROM rental_invoices WHERE company_id = '" . $get['company_id'] . "' AND due_date <= CURDATE() AND is_active = 0 GROUP BY party_id";
        $riduelistres = $callConfig->getAllRows($riduelistsql);
        $payment_due = array();
        foreach ($riduelistres as $rilist) {
            $payment_due[$rilist->party_id] = $rilist;
        }
        $return['payment_due'] = $payment_due;
        $lastpaysql = "SELECT pay.party_id, pay.payment_date, pa.transaction_table, pa.transaction_id, pa.paid_amonut FROM payments pay,  payment_allocations pa WHERE pay.company_id = '" . $get['company_id'] . "' AND pay.company_id = pa.company_id AND pa.transaction_table = 'rental_invoices' AND pay.id = pa.payment_id AND pay.is_active = pa.is_active AND pay.is_active = 0 AND pay.payment_date = (SELECT pay1.payment_date FROM payments pay1 WHERE pay1.company_id = pay.company_id AND pay1.due_date <= CURDATE() AND pay1.party_id = pay.party_id AND pay.cancel_payment = 'N' AND pay1.cancel_payment = 'N' AND pay1.is_active = pay.is_active ORDER BY payment_date DESC limit 1) GROUP BY pay.party_id";
        $lastpayres = $callConfig->getAllRows($lastpaysql);
        $last_payment = array();
        foreach ($lastpayres as $rilist) {
            $last_payment[$rilist->party_id] = $rilist;
        }
        $return['last_payment'] = $last_payment;
        $totalpropertysql = "SELECT customer_id, count(*) as cnt FROM property_tenant_details WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0 GROUP BY customer_id";
        $totalpropertyres = $callConfig->getAllRows($totalpropertysql);
        $total_property = array();
        foreach ($totalpropertyres as $rilist) {
            $total_property[$rilist->customer_id] = $rilist->cnt;
        }
        $return['total_property_cnt'] = $total_property;


        return $return;
    }
    public function getCustomersDetails($get)
    {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $customerslistsql = "SELECT id,display_name,company FROM customers WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $customerslistres = $callConfig->getAllRows($customerslistsql);
        $return['customers_list'] = $customerslistres;

        $customerssql = RentalConstants::SEL_ALL_CUSTOMERS . $get['company_id'] . RentalConstants::AND_ID_APOS . $get['id'] . RentalConstants::AND_IS_ACTIVE_0;
        $customersres = $callConfig->getRow($customerssql);
        $return['customers'] = $customersres;


        $property_tenant_detailssql = RentalConstants::SEL_PTD . $get['company_id'] . "' AND customer_id = '" . $customersres->id . RentalConstants::AND_IS_ACTIVE_0;
        $property_tenant_detailsres = $callConfig->getAllRows($property_tenant_detailssql);
        $return['property_tenant_details'] = $property_tenant_detailsres;

        $property_owner_detailssql = "SELECT id, property_name, property_address FROM property_owner_details WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $property_owner_detailsres = $callConfig->getAllRows($property_owner_detailssql);
        $propertylist = array();
        foreach ($property_owner_detailsres as $rilist) {
            $propertylist[$rilist->id] = $rilist;
        }
        $return['property_owner_details'] = $propertylist;

        $property_ownerssql = RentalConstants::SEL_PROPERTY_OWNERS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $property_ownersres = $callConfig->getAllRows($property_ownerssql);
        $property_owners = array();
        foreach ($property_ownersres as $po) {
            $property_owners[$po->id] = $po;
        }
        $return['property_owners'] = $property_owners;





        $paymentmethodsql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'PAYMETHOD' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $paymentmethodres = $callConfig->getAllRows($paymentmethodsql);
        foreach ($paymentmethodres as $pm) {
            $paymentmethod[$pm->genc_detail_code] = $pm->genc_detail_name;
        }
        $return['payment_method'] = $paymentmethod;

        $salestypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'SALESTYPE' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $salestyperes = $callConfig->getAllRows($salestypesql);
        foreach ($salestyperes as $st) {
            $salestype[$st->genc_detail_code] = $st->genc_detail_name;
        }
        $return['salestype'] = $salestype;

        $gstregistrationtypesql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'GSTREGISTRATIONTYPE' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $gstregistrationtyperes = $callConfig->getAllRows($gstregistrationtypesql);
        foreach ($gstregistrationtyperes as $st) {
            $gstregistrationtype[$st->genc_detail_code] = $st->genc_detail_name;
        }
        $return['gstregistrationtype'] = $gstregistrationtype;

        $paymenttermsql = "SELECT payment_term_name as genc_detail_name, id as genc_detail_code FROM payment_terms WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $paymenttermres = $callConfig->getAllRows($paymenttermsql);
        foreach ($paymenttermres as $pt) {
            $pmtype[$pt->genc_detail_code] = $pt->genc_detail_name;
        }
        $return['paymenttype'] = $pmtype;


        $whrcat = RentalConstants::COMPANY_ID_EQ . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $query = $callConfig->selectQuery('countries', 'id,country_name', $whrcat, '', '', '');
        $components6 = (array) $callConfig->getAllRows($query);
        $return['countries'] = (array) $components6;

        $statessql = "SELECT id, state_name FROM `states` WHERE company_id = '" . $get['company_id'] . "' AND (country_id = '" . $customersres->billing_country . "' || country_id = '" . $customersres->shipping_country . "') AND is_active=0";
        $statesres = $callConfig->getAllRows($statessql);
        $return['states'] = $statesres;

        $citiessql = "SELECT id, city_name FROM `cities` WHERE company_id = '" . $get['company_id'] . "' AND (state_id = '" . $customersres->billing_state . "' || state_id = '" . $customersres->shipping_state . "') AND is_active=0";
        $citiesres = $callConfig->getAllRows($citiessql);
        $return['cities'] = $citiesres;
        return $return;
    }
    public function insertCustomers($get, $post)
    {
        global $callConfig;
        $return['get'] = $get;
        $return['post'] = $post;
        $insertary = array('company_id' => $post['company_id'], 'title' => $post['title'], 'first_name' => $post['first_name'], 'middle_name' => $post['middle_name'], 'last_name' => $post['last_name'], 'suffix' => $post['suffix'], 'email' => $post['email'], 'phone' => $post['phone'], 'mobile' => $post['mobile'], 'fax' => $post['fax'], 'company' => $post['company'], 'display_name' => $post['display_name'], 'image' => $post['image'], 'billing_address' => $post['billing_address'], 'billing_city' => $post['billing_city'], 'billing_state' => $post['billing_state'], 'billing_post_code' => $post['billing_post_code'], 'billing_country' => $post['billing_country'], 'shipping_address' => $post['shipping_address'], 'shipping_city' => $post['shipping_city'], 'shipping_state' => $post['shipping_state'], 'shipping_post_code' => $post['shipping_post_code'], 'shipping_country' => $post['shipping_country'], 'curreny_id' => $post['curreny_id'], 'website_url' => $post['website_url'], 'parent_customer' => $post['parent_customer'], 'bill_with_parent' => $post['bill_with_parent'], 'customer_inv_notes' => $post['customer_inv_notes'], 'customer_stmnt_notes' => $post['customer_stmnt_notes'], 'customer_tax_reg_no' => $post['customer_tax_reg_no'], 'cst_reg_no' => $post['cst_reg_no'], 'pan_no' => $post['pan_no'], 'tds_apply' => $post['tds_apply'], 'sales_type' => $post['sales_type'], 'hold_reason' => $post['hold_reason'], 'payment_terms' => $post['payment_terms'], 'default_discount_perc' => $post['default_discount_perc'], 'payment_discount_perc' => $post['payment_discount_perc'], 'pref_payment_method' => $post['pref_payment_method'], 'opening_balance' => $post['opening_balance'], 'last_paid' => $post['last_paid'], 'last_paid_date' => $post['last_paid_date'], 'credit_limit' => $post['credit_limit'], 'edi_invoices' => $post['edi_invoices'], 'edi_orders' => $post['edi_orders'], 'edi_reference' => $post['edi_reference'], 'edi_transport' => $post['edi_transport'], 'edi_address' => $post['edi_address'], 'edi_server_user' => $post['edi_server_user'], 'edi_server_pwd' => $post['edi_server_pwd'], 'gstin' => $post['gstin'], 'gsttype' => $post['gsttype'], 'language_id' => $post['language_id'], 'other' => $post['other'], 'asof' => $this->dateconvertdbformat($post['asof']), 'preferred_deliver_method' => $post['preferred_deliver_method'], 'pay_with_me' => $post['pay_with_me'], 'contact_person' => $post['contact_person'], 'account_code' => $post['account_code']);
        if ($post['rid'] != "") {
            $insertary['updated_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER);
            $insertary['updated_by'] = $post['user_id'];
            $res = $callConfig->updateRecord('customers', $insertary, 'id', $post['rid']);
            $return['res'] = $res;
        } else {
            $insertary['customer_since'] = date('Y-m-d');
            $insertary['is_active'] = 0;
            $insertary['current_status'] = 0;
            $insertary['created_by'] = $post['user_id'];
            $insertary['created_date'] = date(RentalConstants::DATE_FORMAT_YMD_HIS);
            $res = $callConfig->insertRecord('customers', $insertary);
            $return['res'] = $res;
        }
        // $get['id'] = $customer_id;
        // $get['table_name'] = 'customers'
        // $this->updatefillAttachments();
        // function updatefillAttachments($data){
        //                   global $callConfig;
        //                   $attachmentarray = array('transaction_id'=>$data['id']);
        //                   $where = "application_table_name = '".$data['table_name']."' AND temp_id = '".$data['transaction_number']."'";
        //                   $res=$callConfig->updateRecord("application_attachments",$attachmentarray,"","",$where);
        //                   return $res;
        //               }


        return $return;
    }
}

trait RentalBillingTrait
{
public function getOwnerReports($get)
    {
        global $callConfig;
        $post = null;
        $return['post'] = $post;
        $condition = "";
        if ($get['property_owner_id'] != "") {
            $condition .= " AND id = " . $get['property_owner_id'];
        }
        $property_ownersallsql = "SELECT id, owner_name WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $property_ownersallres = $callConfig->getAllRows($property_ownersallsql);
        $return['property_owners_all'] = $property_ownersallres;

        $property_ownerssql = RentalConstants::SEL_PO_FULL . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE . $condition;
        $property_ownersres = $callConfig->getAllRows($property_ownerssql);
        $return['property_owners'] = $property_ownersres;

        $property_owner_detailssql = "SELECT id, property_owner_id, property_name, property_type, property_sub_type, property_address FROM property_owner_details WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $property_owner_detailsres = $callConfig->getAllRows($property_owner_detailssql);
        $property_owner_details = array();
        foreach ($property_owner_detailsres as $pwd) {
            $property_owner_details[$pwd->property_owner_id][] = $pwd;
        }
        $return['property_owner_details'] = $property_owner_details;

        $bankssql = RentalConstants::SEL_BANKS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $banksres = $callConfig->getAllRows($bankssql);
        $banks = array();
        foreach ($banksres as $br) {
            $banks[$br->id] = $br->bank_name;
        }
        $return['banks'] = $banks;

        $property_typessql = "SELECT id, property_type_name, parent_id, icon FROM property_types WHERE is_active =0 AND company_id = '" . $get['company_id'] . "'";
        $property_typesres = $callConfig->getAllRows($property_typessql);
        $property_types = array();
        foreach ($property_typesres as $br) {
            $property_types[$br->id] = $br->property_type_name;
        }
        $return['property_types'] = $property_types;

        $property_owner_detail_areassql = "SELECT id, property_owner_detail_id, property_area_name, property_area, per_sq_ft_rate FROM property_owner_detail_areas WHERE is_active =0 AND company_id = '" . $get['company_id'] . "'";
        $property_owner_detail_areasres = $callConfig->getAllRows($property_owner_detail_areassql);
        $property_owner_detail_areas = array();
        foreach ($property_owner_detail_areasres as $br) {
            $property_owner_detail_areas[$br->property_owner_detail_id][] = $br;
        }
        $return['property_owner_detail_areas'] = $property_owner_detail_areas;
        return $return;
    }
    public function getTenantReports($get)
    {
        global $callConfig;
        $return['get'] = $get;
        $condition = "";
        if ($get['customer_id'] != "") {
            $condition .= " AND id = " . $get['customer_id'];
        }
        $property_ownerssql = RentalConstants::SEL_PROPERTY_OWNERS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $property_ownersres = $callConfig->getAllRows($property_ownerssql);
        $property_owners = array();

        foreach ($property_ownersres as $pwd) {
            $property_owners[$pwd->id] = $pwd->owner_name;
        }
        $return['property_owners'] = $property_owners;

        $property_owner_detailssql = RentalConstants::SEL_POD . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $property_owner_detailsres = $callConfig->getAllRows($property_owner_detailssql);
        $property_owner_details = array();
        foreach ($property_owner_detailsres as $pwd) {
            $property_owner_details[$pwd->id] = $pwd->property_name;
        }
        $return['property_owner_details'] = $property_owner_details;

        $property_ownersallsql = RentalConstants::SEL_CUSTOMERS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE . $condition;
        $property_ownersallres = $callConfig->getAllRows($property_ownersallsql);
        $return['customers_all'] = $property_ownersallres;


        $property_ownerssql = "SELECT id, display_name, email, phone, image, billing_address FROM customers WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE . $condition;
        $property_ownersres = $callConfig->getAllRows($property_ownerssql);
        $return['customers'] = $property_ownersres;
        $return['property_ownerssql'] = $property_ownerssql;
        $property_tenant_detailssqql = "SELECT id, property_owner_id, property_owner_detail_id, property_owner_detail_areas, rent_commencement_date, lease_period_end_date, security_deposit, customer_id FROM property_tenant_details WHERE company_id = '" . $get['company_id'] . "' AND is_active =0 AND agreement_expiry_date IS NULL";
        $property_tenant_detailsres = $callConfig->getAllRows($property_tenant_detailssqql);
        $property_tenant_details = array();
        foreach ($property_tenant_detailsres as $pwd) {
            $property_tenant_details[$pwd->customer_id][] = $pwd;
        }
        $return['property_tenant_details'] = $property_tenant_details;
        return $return;
    }
    private function fetchTenantBillingOpeningBalance($company_id, $custids, $previouslastdate): array
    {
        global $callConfig;
        $opening_balance_inv = array();
        $ob_rental_invoicessql = "SELECT parent_ref_id, SUM(amount) as amount, SUM(tax_amount) as tax_amount, SUM(total_amount) as total_amount, SUM(pending_amount) as pending_amount FROM rental_invoices WHERE company_id = '" . $company_id . RentalConstants::AND_IS_ACTIVE_0_STATUS . ST_APPROVED . "' AND cancel_invoice = 'N' AND transaction_date <= '" . $previouslastdate . RentalConstants::AND_PARTY_ID_IN . $custids . ") GROUP BY parent_ref_id";
        foreach ($callConfig->getAllRows($ob_rental_invoicessql) as $ri) {
            $opening_balance_inv[$ri->parent_ref_id]['ob_rent'] = $ri;
        }
        $ob_utility_sql = "SELECT parent_ref_id, SUM(amount) as amount, SUM(tax_amount) as tax_amount, SUM(total_amount) as total_amount, SUM(pending_amount) as pending_amount FROM utility_rental_invoices WHERE company_id = '" . $company_id . RentalConstants::AND_IS_ACTIVE_0_STATUS . ST_APPROVED . RentalConstants::AND_PARTY_ID_IN . $custids . ")  AND cancel_invoice = 'N' AND transaction_date <= '" . $previouslastdate . "' GROUP BY parent_ref_id";
        foreach ($callConfig->getAllRows($ob_utility_sql) as $ri) {
            $opening_balance_inv[$ri->parent_ref_id]['ob_utility'] = $ri;
        }
        return $opening_balance_inv;
    }

    public function gettenantBillingReports($get)
    {
        global $callConfig;
        $return['get'] = $get;
        $sdate = date("Y-m-01", strtotime($get['start_month']));
        $previouslastdate = date('Y-m-d', strtotime($sdate . ' - 1 days'));
        $edate = date("Y-m-t", strtotime($get['end_month']));
        $startDate = new DateTime($sdate);
        $endDate = new DateTime($edate);
        $monthyearary = array();
        while ($startDate <= $endDate) {
            $monthandyear = $startDate->format('M Y');
            $monthyearkey = $startDate->format('mY');
            $monthyearary[$monthyearkey] = $monthandyear;
            $startDate->modify(RentalConstants::PLUS_ONE_MONTH);
        }
        $return['monthyearary'] = $monthyearary;
        $customer_condition = ($get['customer_id'] != "") ? " AND id = '" . $get['customer_id'] . "'" : "";
        $customerssql = RentalConstants::SEL_CUSTOMERS_COMPANY . $get['company_id'] . "' " . $customer_condition . RentalConstants::AND_IS_ACTIVE_0_MID_SPACE;
        $customersres = $callConfig->getAllRows($customerssql);
        $partyid = array();
        foreach ($customersres as $cut) {
            $partyid[$cut->id] = $cut->id;
        }
        $return['customers'] = $customersres;
        $custids = implode(',', $partyid);
        $property_tenant_detailssqql = "SELECT id, customer_id, property_owner_id, property_owner_detail_id, property_owner_area_ids FROM property_tenant_details  WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0 AND customer_id IN(" . $custids . ") AND agreement_expiry_date IS NULL";
        $property_tenant_details = array();
        foreach ($callConfig->getAllRows($property_tenant_detailssqql) as $poda) {
            $property_tenant_details[$poda->customer_id][] = $poda;
        }
        $return['property_tenant_details'] = $property_tenant_details;

        $property_owners = array();
        foreach ($callConfig->getAllRows(RentalConstants::SEL_PROPERTY_OWNERS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE) as $pwd) {
            $property_owners[$pwd->id] = $pwd;
        }
        $return['property_owners'] = $property_owners;

        $property_owner_details = array();
        foreach ($callConfig->getAllRows(RentalConstants::SEL_POD . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE) as $pwd) {
            $property_owner_details[$pwd->id] = $pwd;
        }
        $return['property_owner_details'] = $property_owner_details;
        $return['opening_balance'] = $this->fetchTenantBillingOpeningBalance($get['company_id'], $custids, $previouslastdate);
        //End opening balance
        $rental_invoicessql = "SELECT transaction_date, parent_ref_id, SUM(amount) as amount, SUM(tax_amount) as tax_amount, SUM(total_amount) as total_amount, SUM(pending_amount) as pending_amount FROM rental_invoices WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_STATUS . ST_APPROVED . "'  AND cancel_invoice = 'N' AND transaction_date >= '" . $sdate . RentalConstants::AND_TRANS_DATE_LTE_DOUBLE_SPACE . $edate . RentalConstants::AND_PARTY_ID_IN . $custids . ") GROUP BY MONTH(transaction_date), YEAR(transaction_date),parent_ref_id";
        $return['rent_sql'] = $rental_invoicessql;
        $rental_invoicesres = $callConfig->getAllRows($rental_invoicessql);

        foreach ($rental_invoicesres as $ri) {
            $monthyear = date('mY', strtotime($ri->transaction_date));
            $invoices[$ri->parent_ref_id]['rent'][$monthyear] = $ri;
        }
        $utility_rental_invoicessql = "SELECT transaction_date, parent_ref_id, ref_bill_type, SUM(amount) as amount, SUM(tax_amount) as tax_amount, SUM(total_amount) as total_amount, SUM(pending_amount) as pending_amount FROM utility_rental_invoices WHERE company_id = '" . $get['company_id'] . "' AND current_status = '" . ST_APPROVED . "'  AND cancel_invoice = 'N' AND transaction_date >= '" . $sdate . RentalConstants::AND_TRANS_DATE_LTE_DOUBLE_SPACE . $edate . RentalConstants::AND_PARTY_ID_IN . $custids . ") AND is_active =0 GROUP BY MONTH(transaction_date), YEAR(transaction_date),parent_ref_id,ref_bill_type";
        $utility_rental_invoicesres = $callConfig->getAllRows($utility_rental_invoicessql);
        $return['utility_sql'] = $utility_rental_invoicessql;
        foreach ($utility_rental_invoicesres as $ri) {
            $monthyear = date('mY', strtotime($ri->transaction_date));
            $invoices[$ri->parent_ref_id]['utility'][$monthyear] = $ri;
        }
        $return['invoices'] = $invoices;

        $customerssql = RentalConstants::SEL_CUSTOMERS_COMPANY . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $customersres = $callConfig->getAllRows($customerssql);
        $return['all_customers'] = $customersres;


        $paymentssql = "SELECT SUM(unallocated_amount) as unallocated_amount, party_id FROM payments WHERE company_id = '" . $get['company_id'] . "' AND cancel_payment = 'N' AND payment_date <= '" . $edate . "' AND is_active =0 GROUP BY transaction_type,party_id";
        $paymentsres = $callConfig->getAllRows($paymentssql);
        $payments = array();
        foreach ($paymentsres as $ri) {
            $payments[$ri->party_id] = $ri;
        }
        $return['payments'] = $payments;


        $paymentssql = "SELECT DATE_FORMAT(pay.payment_date, '%m%Y') as payment_month_year, pay.party_id, SUM(pa.total_paid_amonut) as total_paid FROM payments pay, payment_allocations pa WHERE pay.company_id = '" . $get['company_id'] . "' AND pay.company_id = pa.company_id  AND pay.cancel_payment = 'N' AND pay.payment_date <= '" . $edate . "' AND pay.id = pa.payment_id AND pay.is_active = pa.is_active GROUP BY YEAR(pay.payment_date), MONTH(pay.payment_date)";
        $paymentsres = $callConfig->getAllRows($paymentssql);
        $payments = array();
        foreach ($paymentsres as $ri) {
            $payments[$ri->party_id][$ri->payment_month_year] = $ri;
        }
        $return['payments_month_wyse_sql'] = $paymentssql;
        $return['payments_month_wyse'] = $payments;


        $credit_notessql = "SELECT SUM(cn.total_amount) as total_amount, cn.party_id, ri.parent_ref_id FROM credit_notes cn LEFT JOIN rental_invoices ri ON cn.ref_id = ri.id AND ri.cancel_invoice = 'N' WHERE cn.company_id = '" . $get['company_id'] . RentalConstants::AND_CN_TRANS_DATE_LTE . $edate . RentalConstants::CN_GROUP_1;
        $credit_notesres = $callConfig->getAllRows($credit_notessql);
        $credit_notes = array();
        foreach ($credit_notesres as $ri) {
            $credit_notes[$ri->party_id][$ri->parent_ref_id] = $ri;
        }


        $credit_notessql = "SELECT SUM(cn.total_amount) as total_amount, cn.party_id, ri.parent_ref_id FROM credit_notes cn LEFT JOIN utility_rental_invoices ri ON cn.ref_id = ri.id AND ri.cancel_invoice = 'N' WHERE cn.company_id = '" . $get['company_id'] . RentalConstants::AND_CN_TRANS_DATE_LTE . $edate . RentalConstants::CN_GROUP_1;
        $credit_notesres = $callConfig->getAllRows($credit_notessql);
        foreach ($credit_notesres as $ri) {
            $ri->total_amount = $credit_notes[$ri->party_id][$ri->parent_ref_id]->total_amount + $ri->total_amount;
            $credit_notes[$ri->party_id][$ri->parent_ref_id] = $ri;
        }
        $return['credit_notes'] = $credit_notes;
        $return['credit_notes_sql'] = $credit_notessql;


        $debit_notessql = "SELECT SUM(cn.total_amount) as total_amount, cn.party_id, ri.parent_ref_id FROM debit_notes cn LEFT JOIN rental_invoices ri ON cn.ref_id = ri.id AND ri.cancel_invoice = 'N' WHERE cn.company_id = '" . $get['company_id'] . RentalConstants::AND_CN_TRANS_DATE_LTE . $edate . RentalConstants::CN_GROUP_2;
        $debit_notesres = $callConfig->getAllRows($debit_notessql);
        $debit_notes = array();
        foreach ($debit_notesres as $ri) {
            $debit_notes[$ri->party_id][$ri->parent_ref_id] = $ri;
        }

        $debit_notessql = "SELECT SUM(cn.total_amount) as total_amount, cn.party_id, ri.parent_ref_id FROM debit_notes cn LEFT JOIN utility_rental_invoices ri ON cn.ref_id = ri.id AND ri.cancel_invoice = 'N' WHERE cn.company_id = '" . $get['company_id'] . RentalConstants::AND_CN_TRANS_DATE_LTE . $edate . RentalConstants::CN_GROUP_2;
        $debit_notesres = $callConfig->getAllRows($debit_notessql);
        foreach ($debit_notesres as $ri) {
            $ri->total_amount = $debit_notes[$ri->party_id][$ri->parent_ref_id]->total_amount + $ri->total_amount;
            $debit_notes[$ri->party_id][$ri->parent_ref_id] = $ri;
        }

        $return['debit_notes'] = $debit_notes;
        $return['debit_notes_sql'] = $debit_notessql;




        $paymentssql = "SELECT pay.payment_date, pay.party_id, IF(pay.amount != 0,pay.amount,pay.tds_amount) as total_paid_amonut FROM payments pay WHERE pay.company_id = '" . $get['company_id'] . "' AND pay.cancel_payment = 'N' AND pay.payment_date <= '" . $edate . "' AND pay.is_active = 0";
        $paymentsres = $callConfig->getAllRows($paymentssql);
        $payments = array();
        foreach ($paymentsres as $ri) {
            $payments[$ri->party_id][] = $ri;
        }
        $return['all_this_date_range_payments_sql'] = $paymentssql;
        $return['all_this_date_range_payments_res'] = $payments;


        return $return;
    }
    public function getTenantProperty($get)
    {
        global $callConfig;
        if ($get['type'] == 'getcustid' || $get['type'] == 'parent_ref_id') {
            $sql = "SELECT  ptd.id, ptd.monthly_total, pod.property_name as value, ptd.rent_commencement_date, ptd.company_name, poown.owner_name FROM property_tenant_details ptd, property_owner_details pod, property_owners poown WHERE  ptd.company_id = '" . $get['company_id'] . "' AND pod.company_id = ptd.company_id AND pod.company_id = poown.company_id AND pod.id = ptd.property_owner_detail_id AND poown.id = ptd.property_owner_id AND ptd.customer_id = '" . $get['valu'] . "' AND ptd.current_status = '" . ST_APPROVED . "' AND pod.is_active = ptd.is_active AND ptd.is_active ='0' ORDER BY pod.property_name ASC";
            $property_tenant_details = $callConfig->getAllRows($sql);
            $return['result'] = $property_tenant_details;
            $return['result_sql'] = $sql;
        } elseif ($get['type'] == 'getproperty_tenant_detail_id') {

            $property_tenant_detailssql = "SELECT rent_commencement_date FROM property_tenant_details WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $get['valu'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $property_tenant_detailsres = $callConfig->getRow($property_tenant_detailssql);
            $rent_commencement_date = $property_tenant_detailsres->rent_commencement_date;

            $configsql = "SELECT system_start_date FROM config WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $configres = $callConfig->getRow($configsql);
            $system_start_date = $configres->system_start_date;
            if ($system_start_date != "") {
                $dateTime1 = new DateTime($rent_commencement_date);
                $dateTime2 = new DateTime($system_start_date);
                $maxDate = max($dateTime1, $dateTime2);
                $maxrecorddate = $maxDate->format('Y-m-01');
            } else {
                $maxrecorddate = $rent_commencement_date;
            }
            $billtypecondition = ' AND generated_invoice IS NULL';
            if ($get['bill_type'] == "U") {
                $billtypecondition = ' AND utility_generated_invoice IS NULL';
            } elseif ($get['bill_type'] == "AMC") {
                $billtypecondition = ' AND utility_amc_generated_invoice IS NULL';
            }
            $sql = "SELECT id, month_start_date,total_amount FROM property_tenant_billings WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_PTD_ID . $get['valu'] . "' " . $billtypecondition . " AND month_start_date >= '" . $maxrecorddate . "' AND is_active = 0 ORDER BY month_start_date ASC";
            $property_tenant_billings = $callConfig->getAllRows($sql);
            $return['result'] = $property_tenant_billings;
            $return['result_sql'] = $sql;
        } elseif ($get['type'] == 'getproperty_tenant_billings_id') {
            $sql = "SELECT id, month_start_date,bill_detail,utility_details, rent_additional_notes, rent_due_date, rent_transaction_date, utility_transaction_date, utility_due_date, utility_additional_notes, utility_amc_details, utility_amc_transaction_date, utility_amc_due_date, utility_amc_additional_notes FROM property_tenant_billings WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $get['valu'] . RentalConstants::AND_IS_ACTIVE_0;
            $property_tenant_billings = $callConfig->getRow($sql);
            $return['result'] = $property_tenant_billings;
            $return['result_sql'] = $sql;

            $hsnandsactable = "SELECT ru.id, ru.type_name, hsn.code, hsn.gst FROM rental_utilities ru, hsn_and_sac_codes hsn WHERE ru.company_id = 1 AND  ru.company_id =  hsn.company_id AND ru.hsn_and_sac_code_id = hsn.id AND ru.is_active = hsn.is_active AND ru.is_active = 0";
            $hsnandsacdata = $callConfig->getAllRows($hsnandsactable);
            $hsn_and_sac_codes = array();
            foreach ($hsnandsacdata as $pwd) {
                $hsn_and_sac_codes[$pwd->id] = $pwd;
            }
            $return['hsn_and_sac_codes'] = $hsn_and_sac_codes;


            $sql = RentalConstants::SEL_GENC . $get['company_id'] . "' AND genc_code = 'UTILITYUOM' AND is_active = 0";
            $uomres = $callConfig->getAllRows($sql);
            $return['utility_uom'] = $uomres;


        } elseif ($get['type'] == 'ref_id') {
            $sql = "SELECT id, month_start_date as value FROM property_tenant_billings WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_PTD_ID . $get['valu'] . "' AND is_active = 0 ORDER BY month_start_date ASC";
            $property_tenant_billings = $callConfig->getAllRows($sql);
            $return['result'] = $property_tenant_billings;
            $return['result_sql'] = $sql;
        } else {
            $customerssql = "SELECT id, display_name,created_by FROM customers WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $customersres = $callConfig->getAllRows($customerssql);
            $return['customers'] = $customersres;
        }
        return $return;
    }
    private function getRentalInvoicesListDetails($company_id, $invoicetable, $property_tenant_billingscondition): array
    {
        global $callConfig;
        $res = array();
        $customerssql = "SELECT id, transaction_number, transaction_date, party_name, amount, tax_amount, discount_amount, total_amount, pending_amount, party_email, ref_bill_type, current_status, cancel_invoice FROM " . $invoicetable . RentalConstants::WHERE_COMPANY_ID . $company_id . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $res['rental_invoices'] = $callConfig->getAllRows($customerssql);

        $property_tenant_billingssql = "SELECT id, rental_invoice_id, utility_rental_invoice_id, shared_datetime, pdf_name, shared, utility_shared FROM `property_tenant_billings` WHERE company_id = '" . $company_id . "' " . $property_tenant_billingscondition . RentalConstants::AND_IS_ACTIVE_0_SPACE;
        $shared = array();
        foreach ($callConfig->getAllRows($property_tenant_billingssql) as $pwd) {
            if ($pwd->shared == '0') {
                $shared[$pwd->rental_invoice_id] = $pwd;
            }
            if ($pwd->utility_shared == '0') {
                $shared[$pwd->utility_rental_invoice_id] = $pwd;
            }
        }
        $res['property_tenant_billings_shared_datetime'] = $shared;

        $credit_notes_sum = array();
        foreach ($callConfig->getAllRows("SELECT ref_id, SUM(total_amount) total_amount FROM `credit_notes` WHERE company_id = 1 AND ref_type = '" . $invoicetable . "' AND is_active = 0 GROUP BY ref_id") as $pwd) {
            $credit_notes_sum[$pwd->ref_id] = $pwd->total_amount;
        }
        $res['credit_notes_sum'] = $credit_notes_sum;

        $debit_notes_sum = array();
        foreach ($callConfig->getAllRows("SELECT ref_id, SUM(total_amount) total_amount FROM `debit_notes` WHERE company_id = 1 AND ref_type = '" . $invoicetable . "' AND is_active = 0 GROUP BY ref_id") as $pwd) {
            $debit_notes_sum[$pwd->ref_id] = $pwd->total_amount;
        }
        $res['debit_notes_sum'] = $debit_notes_sum;
        return $res;
    }

    public function getRentalInvoices($get)
    {
        global $callConfig;
        $return['get'] = $get;
        if (($get['bill_type'] ?? '') == 'U') {
            $invoicetable = "utility_rental_invoices";
            $property_tenant_billingscondition = " AND utility_shared = '0'";
        } elseif (($get['bill_type'] ?? '') == 'AMC') {
            $invoicetable = "utility_amc_rental_invoices";
            $property_tenant_billingscondition = " AND utility_amc_shared = '0'";
        } else {
            $invoicetable = "rental_invoices";
            $property_tenant_billingscondition = " AND shared = '0'";
        }

        if (!empty($get['rid'])) {
            $customerssql = "SELECT id, transaction_number, transaction_date, originator, party_name, amount, tax_amount, discount_amount, total_amount, ref_bill_type, current_status, cancel_invoice FROM " . $invoicetable . RentalConstants::WHERE_COMPANY_ID . $get['company_id'] . "' AND id ='" . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $return['rental_invoices'] = $callConfig->getRow($customerssql);

            $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = " . $get['company_id'] . " AND is_active =0 ORDER BY display_priority ASC";
            $return['chart_of_accounts'] = $callConfig->getAllRows($chart_of_accountssql);

            $debit_notessql = "SELECT id, transaction_number, transaction_date, total_amount FROM `debit_notes` WHERE company_id = " . $get['company_id'] . " AND ref_type = '" . $invoicetable . "' AND ref_id = '" . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $return['debit_notes'] = $callConfig->getAllRows($debit_notessql);

            $credit_notessql = "SELECT id, transaction_number, transaction_date, total_amount FROM `credit_notes` WHERE company_id = " . $get['company_id'] . " AND ref_type = '" . $invoicetable . "' AND ref_id = '" . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $return['credit_notes'] = $callConfig->getAllRows($credit_notessql);
        } else {
            $listData = $this->getRentalInvoicesListDetails($get['company_id'], $invoicetable, $property_tenant_billingscondition);
            $return = array_merge($return, $listData);
        }
        return $return;
    }
    public function insertDebitNote($get, $post)
    {
        global $callConfig;
        $return = array();
        $transaction_table = $post['popup_transaction_table'];
        $transaction_id = $post['transaction_id'];
        $amount = $post['popup_amount'];
        $comment = $post['popup_comment'];
        $account_code = $post['popup_account_code'];
        $transactionsql = RentalConstants::SEL_FROM . $transaction_table . RentalConstants::WHERE_COMPANY_ID . $get['company_id'] . RentalConstants::AND_ID_APOS . $transaction_id . RentalConstants::AND_IS_ACTIVE_0;

        $transactionres = $callConfig->getRow($transactionsql);

        $transaction_date = $due_date = date("Y-m-d");


        $property_tenant_detailssql = RentalConstants::SEL_PTD . $get['company_id'] . RentalConstants::AND_ID_APOS . $transactionres->parent_ref_id . RentalConstants::AND_IS_ACTIVE_0;
        $property_tenant_detailsres = $callConfig->getRow($property_tenant_detailssql);
        $transaction_number = $this->toGetApplicationRentalOwnerSerialNumber('debit_notes', $property_tenant_detailsres->property_owner_id, $transaction_date);



        //ST_APPROVED
        $insertcust = array('company_id' => $get['company_id'], 'transaction_number' => $transaction_number, 'transaction_date' => $transaction_date, 'originator' => $get['user_id'], 'party_id' => $transactionres->party_id, 'party_name' => $transactionres->party_name, 'party_email' => $transactionres->party_email, 'party_phone' => $transactionres->party_phone, 'party_address' => $transactionres->party_address, 'amount' => $amount, 'tax_amount' => '0', 'total_amount' => $amount, 'due_date' => $due_date, 'ref_type' => $transaction_table, 'ref_id' => $transaction_id, 'ref_date' => $transactionres->transaction_date, 'ref_no' => $transactionres->transaction_number, 'pending_amount' => $amount, 'is_active' => 0, 'current_status' => ST_APPROVED, 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        $res = $callConfig->insertRecord('debit_notes', $insertcust);
        $rid = $res['LASTID'];
        $return['debit_notes'] = $res;
        $insertcust = array('company_id' => $get['company_id'], 'transaction_id' => $rid, 'item_id' => '0', 'account_code' => $account_code, 'item_name' => $comment, 'description' => $comment, 'purchase_uom' => 'UN', 'item_quantity' => '1', 'item_price' => $amount, 'item_amount' => $amount, 'tax_amount' => '0', 'total_amount' => $amount, 'approved_quantity' => '1', 'transaction_qty' => '1', 'pending_qty' => '1', 'is_active' => 0, 'current_status' => 0, 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        $rental_invoicesins = $callConfig->insertRecord('debit_note_items', $insertcust);
        $return['debit_note_items'] = $rental_invoicesins;
        return $return;
    }
    public function insertCreditNote($get, $post)
    {
        global $callConfig;
        $return = array();
        $transaction_table = $post['popup_transaction_table'];
        $transaction_id = $post['transaction_id'];
        $amount = $post['popup_amount'];
        $comment = $post['popup_comment'];
        $account_code = $post['popup_account_code'];
        $transactionsql = RentalConstants::SEL_FROM . $transaction_table . RentalConstants::WHERE_COMPANY_ID . $get['company_id'] . RentalConstants::AND_ID_APOS . $transaction_id . RentalConstants::AND_IS_ACTIVE_0;

        $transactionres = $callConfig->getRow($transactionsql);

        $transaction_date = $due_date = date("Y-m-d");

        $property_tenant_detailssql = RentalConstants::SEL_PTD . $get['company_id'] . RentalConstants::AND_ID_APOS . $transactionres->parent_ref_id . RentalConstants::AND_IS_ACTIVE_0;
        $property_tenant_detailsres = $callConfig->getRow($property_tenant_detailssql);
        $transaction_number = $this->toGetApplicationRentalOwnerSerialNumber('credit_notes', $property_tenant_detailsres->property_owner_id, $transaction_date);

        //ST_APPROVED
        $insertcust = array('company_id' => $get['company_id'], 'transaction_number' => $transaction_number, 'transaction_date' => $transaction_date, 'originator' => $get['user_id'], 'party_id' => $transactionres->party_id, 'party_name' => $transactionres->party_name, 'party_email' => $transactionres->party_email, 'party_phone' => $transactionres->party_phone, 'party_address' => $transactionres->party_address, 'amount' => $amount, 'tax_amount' => '0', 'total_amount' => $amount, 'due_date' => $due_date, 'ref_type' => $transaction_table, 'ref_id' => $transaction_id, 'ref_date' => $transactionres->transaction_date, 'ref_no' => $transactionres->transaction_number, 'pending_amount' => $amount, 'is_active' => 0, 'current_status' => ST_APPROVED, 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        $res = $callConfig->insertRecord('credit_notes', $insertcust);
        $rid = $res['LASTID'];
        $return['credit_notes'] = $res;
        $insertcust = array('company_id' => $get['company_id'], 'transaction_id' => $rid, 'item_id' => '0', 'account_code' => $account_code, 'item_name' => $comment, 'description' => $comment, 'purchase_uom' => 'UN', 'item_quantity' => '1', 'item_price' => $amount, 'item_amount' => $amount, 'tax_amount' => '0', 'total_amount' => $amount, 'approved_quantity' => '1', 'transaction_qty' => '1', 'pending_qty' => '1', 'is_active' => 0, 'current_status' => 0, 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
        $rental_invoicesins = $callConfig->insertRecord('credit_note_items', $insertcust);
        $return['credit_note_items'] = $rental_invoicesins;
        return $return;
    }
    public function getRentalInvoicePrint($get)
    {
        global $callConfig;
        $return = array();
        $rid = $get['rid'];
        $transaction_table = $get['transaction_table'];
        $fieldarray = $this->toGetDynamicInvoicetables();
        $configsql = "SELECT name, display_logo, default_bank_account_id,number_format, date_separator, date_format,pan_number,phone_number, gst_number, contact_address  FROM config WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $configres = $callConfig->getRow($configsql);
        $return['config'] = $configres;

        $rental_invoicessql = RentalConstants::SEL_FROM . $transaction_table . RentalConstants::WHERE_COMPANY_ID . $get['company_id'] . RentalConstants::AND_ID_APOS . $rid . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $rental_invoicesres = $callConfig->getRow($rental_invoicessql);
        $return['rental_invoices'] = $rental_invoicesres;

        $rental_invoice_itemsaql = "SELECT id, item_name, description, item_price, item_amount, tax_amount, total_amount, purchase_uom, item_quantity, item_id, parent_id FROM " . $fieldarray[$transaction_table]['item'] . RentalConstants::WHERE_COMPANY_ID . $get['company_id'] . RentalConstants::AND_TRANS_ID . $rid . RentalConstants::AND_IS_ACTIVE_0_DOUBLE_SPACE;
        $rental_invoice_items = $callConfig->getAllRows($rental_invoice_itemsaql);
        $return['rental_invoice_items'] = $rental_invoice_items;
        $return['rental_invoice_items_sql'] = $rental_invoice_itemsaql;
        $rental_invoice_item_taxessql = "SELECT riit.id, riit.transaction_item_id, riit.tax_id, riit.tax_value,  riit.amount, riit.tax_amount,hsn.code, hsn.code_description FROM " . $fieldarray[$transaction_table]['tax'] . " riit, hsn_and_sac_codes hsn WHERE riit.company_id = '" . $get['company_id'] . "' AND riit.company_id = hsn.company_id  AND riit.transaction_id = '" . $rid . "' AND riit.tax_id = hsn.id AND riit.is_active = hsn.is_active AND riit.is_active =0";
        $rental_invoice_item_taxesres = $callConfig->getAllRows($rental_invoice_item_taxessql);
        $return['rental_invoice_item_taxes'] = $rental_invoice_item_taxesres;
        $return['rental_invoice_item_taxes_sql'] = $rental_invoice_item_taxessql;
        $billbysql = "SELECT id, display_name, user_email, user_mobile FROM application_users WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $rental_invoicesres->originator . "'AND is_active =0";
        $billbyres = $callConfig->getRow($billbysql);
        $return['bill_by'] = $billbyres;

        $bank_accountssql = "SELECT ba.id, ba.account_code, ba.bank_account_name, ba.bank_account_number, ba.bank_contact_person, ba.bank_contact_no, ba.bank_address, ba.upi_id, ba.ifsc_code, ba.account_type, (SELECT bank.bank_name FROM banks bank WHERE ba.company_id = bank.company_id AND bank.id =ba.bank_id AND ba.is_active = bank.is_active) as bank_name FROM bank_accounts ba WHERE ba.company_id = '" . $get['company_id'] . "' AND ba.id = '" . $configres->default_bank_account_id . "' AND ba.is_active =0";
        $bank_accountsres = $callConfig->getRow($bank_accountssql);
        $return['bank_accounts'] = $bank_accountsres;
        $return['transaction_table'] = $transaction_table;

        $rental_utilitiessql = "SELECT id, type_name, parent_id FROM rental_utilities WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $rental_utilitiesres = $callConfig->getAllRows($rental_utilitiessql);
        foreach ($rental_utilitiesres as $ru) {
            $return['rental_utilities'][$ru->id] = $ru;
        }


        $bankssql = RentalConstants::SEL_BANKS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $banksres = $callConfig->getAllRows($bankssql);
        foreach ($banksres as $bnk) {
            $banks[$bnk->id] = $bnk->bank_name;
        }
        $return['banks'] = $banks;

        $account_type_sql = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'BANKACCTTYPE' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $account_type_res = $callConfig->getAllRows($account_type_sql);
        foreach ($account_type_res as $btr) {
            $account_types[$btr->genc_detail_code] = $btr->genc_detail_name;
        }
        $return['account_types'] = $account_types;



        return $return;
    }
    public function insertTenantOpeningBalance($get, $post)
    {
        global $callConfig;
        $return = array();
        $return['post'] = $post;
        $sql = RentalConstants::SEL_PTD . $post['company_id'] . RentalConstants::AND_ID_APOS . $post['rid'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $ptdres = $callConfig->getRow($sql);

        $customersql = RentalConstants::SEL_ALL_CUSTOMERS . $get['company_id'] . RentalConstants::AND_ID_APOS . $ptdres->customer_id . RentalConstants::AND_IS_ACTIVE_0;
        $customer = $callConfig->getRow($customersql);
        $party_additional_details = array('gstin' => $customer->gstin, 'pan_no' => $customer->pan_no);
        $party_additional_detail = json_encode((array) $party_additional_details);

        foreach ($post['transaction_date'] as $key => $valu) {
            $transaction_date = $this->dateconvertdbformat($post['transaction_date'][$key]);
            $total_rent = $post['total'][$key];
            $serial_transaction_number = $this->toGetApplicationSerialNumber('rental_invoices');
            $transaction_number = $this->toGetApplicationRentalOwnerSerialNumber('rental_invoices', $ptdres->property_owner_id, $transaction_date);
            $insertcust = array('company_id' => $post['company_id'], 'transaction_number' => $transaction_number, 'transaction_date' => $transaction_date, 'originator' => $post['user_id'], 'party_id' => $ptdres->customer_id, 'party_name' => $ptdres->tenant_name, 'party_email' => $ptdres->tenant_email, 'party_phone' => $ptdres->tenant_mobile, 'party_address' => $ptdres->tenant_address, 'party_ship_adderss' => $ptdres->tenant_address, 'amount' => $total_rent, 'tax_amount' => '0', 'total_amount' => $total_rent, 'due_date' => $transaction_date, 'ref_type' => 'property_tenant_billings', 'parent_ref_id' => $ptdres->id, 'pending_amount' => $total_rent, 'is_active' => '0', 'current_status' => '199', 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'party_additional_details' => $party_additional_detail, 'ref_bill_type' => 'OB', 'serial_transaction_number' => $serial_transaction_number);
            $rental_invoicesins = $callConfig->insertRecord('rental_invoices', $insertcust);
            $rental_invoice_id = $rental_invoicesins['LASTID'];
            $return['rental_invoicesins'][$rental_invoice_id] = $rental_invoice_id;
            foreach ($post['property_area_name'][$key] as $key2 => $item_name) {
                $rent_amount = $post['rent'][$key][$key2];
                $property_area_id = $post['property_area_id'][$key][$key2];
                $insertcust = array('company_id' => $post['company_id'], 'transaction_id' => $rental_invoice_id, 'item_table_name' => 'property_owner_detail_areas', 'item_id' => $property_area_id, 'item_name' => $item_name, 'description' => $item_name, 'purchase_uom' => 'UN', 'item_quantity' => '1', 'item_price' => $rent_amount, 'item_amount' => $rent_amount, 'tax_amount' => '0', 'total_amount' => $rent_amount, 'approved_quantity' => '1', 'transaction_qty' => '1', 'pending_qty' => '0', 'is_active' => 0, 'current_status' => 0, 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
                $rental_invoicesins = $callConfig->insertRecord('rental_invoice_items', $insertcust);
                $return['rental_invoice_items'][$rental_invoice_id] = $rental_invoicesins;
            }
            $this->tocreatejounalvoucherbaseallcases('rental_invoices', $rental_invoice_id);
        }
        return $return;
    }
    private function deactivateFutureRentalBillingsAndInvoices($company_id, $user_id, $target_date, array $ptbid, array $rental_invoice_id, array $utility_rental_invoice_id): array
    {
        global $callConfig;
        $return = array();
        $upd = array('is_active' => null, 'to_date' => $target_date, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER), 'updated_by' => $user_id);

        if (!empty($ptbid)) {
            $where = RentalConstants::COMPANY_ID_EQ . $company_id . RentalConstants::AND_ID_IN . implode(',', $ptbid) . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0;
            $return['property_tenant_billings_update'] = $callConfig->updateRecord('property_tenant_billings', $upd, '', '', $where);
        }
        if (!empty($rental_invoice_id)) {
            $csv = implode(',', $rental_invoice_id);
            $where = RentalConstants::COMPANY_ID_EQ . $company_id . RentalConstants::AND_ID_IN . $csv . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0;
            $return['rental_invoices'] = $callConfig->updateRecord('rental_invoices', $upd, '', '', $where);
            $whereItems = RentalConstants::COMPANY_ID_EQ . $company_id . RentalConstants::AND_TRANS_ID_IN . $csv . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0;
            $return['rental_invoice_items'] = $callConfig->updateRecord('rental_invoice_items', $upd, '', '', $whereItems);
        }
        if (!empty($utility_rental_invoice_id)) {
            $csv = implode(',', $utility_rental_invoice_id);
            $where = RentalConstants::COMPANY_ID_EQ . $company_id . RentalConstants::AND_ID_IN . $csv . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0;
            $return['utility_rental_invoices'] = $callConfig->updateRecord('utility_rental_invoices', $upd, '', '', $where);
            $whereItems = RentalConstants::COMPANY_ID_EQ . $company_id . RentalConstants::AND_TRANS_ID_IN . $csv . RentalConstants::CLOSE_PAREN_AND_IS_ACTIVE_0;
            $return['utility_rental_invoice_items'] = $callConfig->updateRecord('utility_rental_invoice_items', $upd, '', '', $whereItems);
        }
        return $return;
    }

    public function updatePropertyTenantDetails($get, $post)
    {
        global $callConfig;
        $return = array('get' => $get, 'post' => $post);
        $isExpired = (($post['formtype'] ?? '') == "EXPIRED");
        $target_date = $this->dateconvertdbformat($post['un_lock_date']);

        if ($isExpired) {
            $updateary = array('agreement_expiry_date' => $target_date, 'agreement_expiry_remark' => $post['remarks'], 'to_date' => $target_date, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER), 'updated_by' => $post['user_id']);
            $return['unlock'] = $callConfig->updateRecord('property_tenant_details', $updateary, 'id', $post['rid']);
            $updArea = array('property_tenant_detail_id' => null, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER), 'updated_by' => $post['user_id']);
            $return['ree'] = $callConfig->updateRecord('property_owner_detail_areas', $updArea, 'property_tenant_detail_id', $post['rid']);
        } else {
            $updateary = array('un_lock_tenant_date' => $target_date, 'un_lock_tenant_remarks' => $post['remarks'], 'to_date' => $target_date, 'last_date_of_billing' => $target_date, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS_LOWER), 'updated_by' => $post['user_id']);
            $return['property_tenant_details_update'] = $callConfig->updateRecord('property_tenant_details', $updateary, 'id', $post['rid']);
        }

        $rental_invoice_id = $utility_rental_invoice_id = $ptbid = array();
        $ptbsql = RentalConstants::SEL_PTB . $post['company_id'] . RentalConstants::AND_PTD_ID . $post['rid'] . "' AND  month_start_date >= '" . $target_date . RentalConstants::AND_IS_ACTIVE_0;
        foreach ($callConfig->getAllRows($ptbsql) as $ptbress) {
            $ptbid[$ptbress->id] = $ptbress->id;
            if ($ptbress->rental_invoice_id != "") {
                $rental_invoice_id[$ptbress->rental_invoice_id] = $ptbress->rental_invoice_id;
            }
            if ($ptbress->utility_rental_invoice_id != "") {
                $utility_rental_invoice_id[$ptbress->utility_rental_invoice_id] = $ptbress->utility_rental_invoice_id;
            }
        }

        $deactResults = $this->deactivateFutureRentalBillingsAndInvoices($post['company_id'], $post['user_id'], $target_date, $ptbid, $rental_invoice_id, $utility_rental_invoice_id);
        $return = array_merge($return, $deactResults);
        return $return;
    }
    public function getOwnerInvoiceReports($get)
    {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $startmothyear = date("Y-m-01", strtotime($get['start_month']));
        $endmothyear = date("Y-m-t", strtotime($get['end_month']));
        $return['startmothyear'] = $startmothyear . " -- " . $endmothyear;
        if ($get['property_owner_id'] != "") {
            $property_owners_condition = " AND id ='" . $get['property_owner_id'] . "'";

            $podsql = RentalConstants::SEL_POD . $get['company_id'] . RentalConstants::AND_PROPERTY_OWNER_ID . $get['property_owner_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $podres = $callConfig->getAllRows($podsql);
            $return['all_property_owner_details'] = $podres;
        }
        $property_ownerssql = RentalConstants::SEL_PO_FULL . $get['company_id'] . "' " . $property_owners_condition . RentalConstants::AND_IS_ACTIVE_0_MID_SPACE;
        $property_ownersres = $callConfig->getAllRows($property_ownerssql);
        $return['property_owners'] = $property_ownersres;
        if ($get['property_owner_detail_id'] != "") {
            $property_owner_details_condition = " AND id ='" . $get['property_owner_detail_id'] . "'";
        }
        $podsql = "SELECT id, property_owner_id, property_name, property_address FROM property_owner_details WHERE company_id = '" . $get['company_id'] . "' " . $property_owner_details_condition . RentalConstants::AND_IS_ACTIVE_0_MID_SPACE;
        $podres = $callConfig->getAllRows($podsql);
        $return['property_owner_details_sql'] = $podres;
        $property_owner_details = array();
        foreach ($podres as $pod) {
            $property_owner_details[$pod->property_owner_id][] = $pod;
        }
        $return['property_owner_details'] = $property_owner_details;

        $customerssql = RentalConstants::SEL_CUSTOMERS_COMPANY . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $customersres = $callConfig->getAllRows($customerssql);
        $customers = array();
        foreach ($customersres as $poda) {
            $customers[$poda->id] = $poda;
        }
        $return['customers'] = $customers;
        if ($get['type'] == "INVOICES") {

            //sum(if(current_status='".ST_APPROVED."',pending_amount,0)) as total_pending

            $risql = "SELECT ptd.customer_id,  ptd.property_owner_id, ptd.property_owner_detail_id,ri.id, ri.transaction_number, ri.transaction_date, ri.ref_bill_type, ri.amount as amount, ri.tax_amount as tax_amount, ri.total_amount as total_amount, ri.pending_amount as pending_amount, (SELECT SUM(dn.pending_amount) FROM debit_notes dn WHERE dn.company_id = ri.company_id  AND dn.ref_type = 'rental_invoices' AND dn.ref_id =ri.id AND dn.is_active = ri.is_active AND dn.current_status = '" . ST_APPROVED . "') as debit_total_amount, (SELECT SUM(cn.pending_amount) FROM credit_notes cn WHERE cn.company_id = ri.company_id  AND cn.ref_type = 'rental_invoices' AND cn.ref_id = ri.id AND cn.is_active = ri.is_active AND cn.current_status = '" . ST_APPROVED . "') as credit_total_amount FROM property_tenant_details ptd, rental_invoices ri WHERE ri.company_id = '" . $get['company_id'] . RentalConstants::RI_PTD_JOIN . ST_APPROVED . "' AND ri.cancel_invoice = 'N' AND ri.transaction_date >= '" . $startmothyear . RentalConstants::AND_RI_TRANS_DATE_LTE . $endmothyear . "'";
            $rires = $callConfig->getAllRows($risql);
            $invoices = array();
            foreach ($rires as $ri) {
                $invoices[$ri->property_owner_id][$ri->property_owner_detail_id][$ri->customer_id]['rent'][] = $ri;
            }
            $return['invoices_Sql'] = $risql;

            $urisql = "SELECT ptd.customer_id,  ptd.property_owner_id, ptd.property_owner_detail_id,ri.id, ri.transaction_number, ri.transaction_date, ri.amount as amount, ri.tax_amount as tax_amount, ri.total_amount as total_amount, ri.pending_amount as pending_amount, (SELECT SUM(dn.total_amount) FROM debit_notes dn WHERE dn.company_id = ri.company_id  AND dn.ref_type = 'utility_rental_invoices' AND dn.ref_id = ri.id AND dn.is_active = ri.is_active AND dn.current_status = '" . ST_APPROVED . "') as debit_total_amount, (SELECT SUM(cn.total_amount) FROM credit_notes cn WHERE cn.company_id = ri.company_id  AND cn.ref_type = 'utility_rental_invoices' AND cn.ref_id = ri.id AND cn.is_active = ri.is_active AND cn.current_status = '" . ST_APPROVED . "') as credit_total_amount  FROM property_tenant_details ptd, utility_rental_invoices ri WHERE ri.company_id = '" . $get['company_id'] . RentalConstants::RI_PTD_JOIN . ST_APPROVED . "'  AND ri.transaction_date >= '" . $startmothyear . "' AND ri.cancel_invoice = 'N' AND ri.transaction_date <=  '" . $endmothyear . "'";
            $urires = $callConfig->getAllRows($urisql);
            foreach ($urires as $uri) {
                $invoices[$uri->property_owner_id][$uri->property_owner_detail_id][$uri->customer_id]['utility'][] = $uri;
            }
            $return['invoices'] = $invoices;
        } else {


            $risql = "SELECT ptd.customer_id,  ptd.property_owner_id, ptd.property_owner_detail_id, SUM(ri.amount) as amount, SUM(ri.tax_amount) as tax_amount, SUM(ri.total_amount) as total_amount, SUM(ri.pending_amount) as pending_amount, (SELECT SUM(dn.pending_amount) FROM debit_notes dn WHERE dn.company_id = ri.company_id  AND dn.ref_type = 'rental_invoices' AND FIND_IN_SET(dn.ref_id, GROUP_CONCAT(ri.id)) AND dn.is_active = ri.is_active AND dn.current_status = '" . ST_APPROVED . "') as debit_total_amount, (SELECT SUM(cn.pending_amount) FROM credit_notes cn WHERE cn.company_id = ri.company_id  AND cn.ref_type = 'rental_invoices' AND FIND_IN_SET(cn.ref_id, GROUP_CONCAT(ri.id)) AND cn.is_active = ri.is_active AND cn.current_status = '" . ST_APPROVED . "') as credit_total_amount FROM property_tenant_details ptd, rental_invoices ri WHERE ri.company_id = '" . $get['company_id'] . RentalConstants::RI_PTD_JOIN . ST_APPROVED . "' AND ri.transaction_date >= '" . $startmothyear . RentalConstants::AND_RI_TRANS_DATE_LTE . $endmothyear . "'  AND ri.cancel_invoice = 'N' GROUP BY ptd.customer_id, ptd.property_owner_id, ptd.property_owner_detail_id";
            $rires = $callConfig->getAllRows($risql);
            $invoices = array();
            foreach ($rires as $ri) {
                $invoices[$ri->property_owner_id][$ri->property_owner_detail_id][$ri->customer_id]['rent'] = $ri;
            }
            $return['invoices_Sql'] = $risql;
            $urisql = "SELECT ptd.customer_id, ptd.property_owner_id, ptd.property_owner_detail_id, SUM(ri.amount) as amount, SUM(ri.tax_amount) as tax_amount, SUM(ri.total_amount) as total_amount, SUM(ri.pending_amount) as pending_amount, (SELECT SUM(dn.pending_amount) FROM debit_notes dn WHERE dn.company_id = ri.company_id  AND dn.ref_type = 'utility_rental_invoices' AND FIND_IN_SET(dn.ref_id, GROUP_CONCAT(ri.id)) AND dn.is_active = ri.is_active AND dn.current_status = '" . ST_APPROVED . "') as debit_total_amount, (SELECT SUM(cn.pending_amount) FROM credit_notes cn WHERE cn.company_id = ri.company_id  AND cn.ref_type = 'utility_rental_invoices' AND FIND_IN_SET(cn.ref_id, GROUP_CONCAT(ri.id)) AND cn.is_active = ri.is_active AND cn.current_status = '" . ST_APPROVED . "') as credit_total_amount  FROM property_tenant_details ptd, utility_rental_invoices ri WHERE ri.company_id = '" . $get['company_id'] . RentalConstants::RI_PTD_JOIN . ST_APPROVED . "' AND ri.transaction_date >= '" . $startmothyear . RentalConstants::AND_RI_TRANS_DATE_LTE . $endmothyear . "' AND ri.cancel_invoice = 'N' GROUP BY ptd.customer_id, ptd.property_owner_id, ptd.property_owner_detail_id";
            $urires = $callConfig->getAllRows($urisql);
            foreach ($urires as $uri) {
                $invoices[$uri->property_owner_id][$uri->property_owner_detail_id][$uri->customer_id]['utility'] = $uri;
            }
            $return['invoices'] = $invoices;
        }

        //Start filter
        $property_ownerssql = RentalConstants::SEL_PO_FULL . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $property_ownersres = $callConfig->getAllRows($property_ownerssql);
        $return['all_property_owners'] = $property_ownersres;
        //End filter

        return $return;
    }




    public function toGetApplicationRentalOwnerSerialNumber($table_name, $owner_id, $trandate = "")
    {
        global $callConfig;
        $transdate = ($trandate != "") ? $trandate : date('Y-m-d');
        $month = (int) date("m", strtotime($transdate));
        $year = (int) date("Y", strtotime($transdate));
        $finyaer = ($month >= 4) ? $year . '-' . ((int) date("y", strtotime($transdate)) + 1) : ($year - 1) . '-' . date("y", strtotime($transdate));

        $query = "SELECT * FROM `application_rental_owner_serial_numbers` WHERE application_table_name = '" . $table_name . "' AND owner_id = '" . $owner_id . "' AND start_date <= '" . $this->dateconvertdbformat($transdate) . "' AND end_date >= '" . $this->dateconvertdbformat($transdate) . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $res = $callConfig->getRow($query);
        if (!empty($res->id)) {
            $le = $res->doc_no_length ?: 4;
            $data = $res->pre_fix . '/' . $res->post_fix . '/' . sprintf("%0" . $le . "d", $res->serial_number + 1) . '/' . $finyaer;
            $callConfig->updateRecord('application_rental_owner_serial_numbers', array('serial_number' => $res->serial_number + 1), 'id', $res->id);
            return trim($data);
        }

        $query2 = "SELECT * FROM `application_rental_owner_serial_numbers` WHERE application_table_name = '" . $table_name . "' AND owner_id = '" . $owner_id . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $res2 = $callConfig->getRow($query2);
        if (!empty($res2->doc_no_length)) {
            $le = $res2->doc_no_length;
            $data = $res2->pre_fix . '/' . $res2->post_fix . '/' . sprintf("%0" . $le . "d", 1) . '/' . $finyaer;
            $startyear = ($month >= 4) ? $year : ($year - 1);
            $endyear = ($month >= 4) ? ($year + 1) : $year;
            $startdate = $startyear . "-04-01";
            $enddate = $endyear . "-03-31";

            if ($res2->serial_number_type != "" && $table_name != "") {
                $insertary = array('company_id' => $res2->company_id, 'application_table_name' => $table_name, 'owner_id' => $res2->owner_id, 'serial_number' => '1', 'pre_fix' => $res2->pre_fix, 'post_fix' => $res2->post_fix, 'doc_no_length' => $res2->doc_no_length, 'serial_number_type' => $res2->serial_number_type, 'start_date' => $startdate, 'end_date' => $enddate, 'is_active' => 0, 'current_status' => 0, 'created_by' => 1, 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
                $callConfig->insertRecord('application_rental_owner_serial_numbers', $insertary);
            }
            return trim($data);
        }
        return '1';
    }
    public function updateRentalInvoiceData($get, $post)
    {
        global $callConfig;
        $return = array('get' => $get, 'Post' => $post);
        $fieldsToUpdate = [
            'transaction_date' => $this->dateconvertdbformat($post['transaction_date']) ?? null,
            'due_date' => $this->dateconvertdbformat($post['due_date']) ?? null,
            'ref_no' => $post['ref_no'] ?? null,
            'ref_date' => $this->dateconvertdbformat($post['ref_date']) ?? null,
            'notes' => $post['notes'] ?? null,
            'printing_notes' => $post['printing_notes'] ?? null,
            'amount' => $post['sub_amount'] ?? null,
            'tax_amount' => $post['total_tax_amount'] ?? null,
            'total_amount' => $post['grand_total_amount'] ?? null,
            'pending_amount' => $post['grand_total_amount'] ?? null,
            'rounding_off' => $post['rounding_off'] ?? 0,
            'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS),
            'updated_by' => $post['user_id'],
        ];

        $res = $callConfig->updateRecord('rental_invoices', $fieldsToUpdate, 'id', $post['rid']);
        $return['res'] = $res;

        if ($res && !empty($post['id']) && is_array($post['id'])) {
            foreach ($post['id'] as $key => $id) {
                $itemData = [
                    'item_name' => $post['item_name'][$key] ?? null,
                    'item_quantity' => $post['item_quantity'][$key] ?? null,
                    'purchase_uom' => $post['purchase_uom'][$key] ?? null,
                    'item_price' => $post['item_price'][$key] ?? null,
                    'item_amount' => $post['item_amount'][$key] ?? null,
                    'tax_amount' => $post['tax_amount'][$key] ?? null,
                    'total_amount' => $post['total_amount'][$key] ?? null,
                    'description' => $post['description'][$key] ?? null,
                    'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS),
                    'updated_by' => $post['user_id'],
                ];
                $return['itemsdata'][] = $itemData;
                if (!empty($id)) {
                    $return['keydata'][] = $id;
                    $return['items'][] = $callConfig->updateRecord('rental_invoice_items', $itemData, 'id', $id);
                }
            }
        }

        if ($res && !empty($post['tax_ids']) && is_array($post['tax_ids'])) {
            foreach ($post['tax_ids'] as $key => $id) {
                $taxData = [
                    'amount' => $post['item_amount'][$key] ?? null,
                    'tax_amount' => $post['tax_amount'][$key] ?? null,
                    'total_amount' => $post['total_amount'][$key] ?? null,
                    'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS),
                    'updated_by' => $post['user_id'],
                ];
                $return['taxdata'][] = $taxData;
                if (!empty($post['id'][$key])) {
                    $return['keydata'][] = $post['id'][$key];
                    $return['tax items'][] = $callConfig->updateRecord('rental_invoice_item_taxes', $taxData, 'id', $id);
                }
            }
        }
        return $return;
    }

    public function getrentalinvoice($get)
    {
        global $callConfig;
        $return = array();

        $rentalinvoicetable = "SELECT * FROM `rental_invoices` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $rentalinvoicetabledata = $callConfig->getRow($rentalinvoicetable);
        $return['rental_invoices'] = $rentalinvoicetabledata;

        $originatorId = $rentalinvoicetabledata->originator;
        $originatorQuery = "SELECT * FROM `application_users` WHERE company_id = '" . $get['company_id'] . "' AND id = '$originatorId' AND is_active = 0";
        $originatorData = $callConfig->getRow($originatorQuery);
        $return['application_users'] = $originatorData;

        $rentalinvoiceitemtable = "SELECT * FROM `rental_invoice_items` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_TRANS_ID . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $rentalinvoicetableitemdata = $callConfig->getAllRows($rentalinvoiceitemtable);
        $return['rental_invoice_items'] = $rentalinvoicetableitemdata;

        $rentalinvoiceitemtaxtable = "SELECT * FROM `rental_invoice_item_taxes` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_TRANS_ID . $get['rid'] . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $rentalinvoicetableallitemtaxdata = $callConfig->getAllRows($rentalinvoiceitemtaxtable);
        $rentalinvoicetableitemtaxdata = $callConfig->getRow($rentalinvoiceitemtaxtable);
        $return['rental_invoice_item_taxes'] = $rentalinvoicetableitemtaxdata;
        $return['rental_invoice_all_item_taxes'] = $rentalinvoicetableallitemtaxdata;

        $taxId = $rentalinvoicetableitemtaxdata->tax_id;
        $hsnandsactable = RentalConstants::SEL_HSN_SAC . $get['company_id'] . RentalConstants::AND_ID_APOS . $taxId . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $hsnandsacdata = $callConfig->getRow($hsnandsactable);
        $return['hsn_and_sac_codes'] = $hsnandsacdata;

        $paymenttable = "SELECT * FROM `payment_terms` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $paymenttabledata = $callConfig->getAllRows($paymenttable);
        $return['payment_terms'] = $paymenttabledata;

        $gcdtable = "SELECT genc_detail_name, genc_detail_code FROM general_code_details WHERE genc_code = 'RENTALUOM' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $gcdtabledata = $callConfig->getAllRows($gcdtable);
        $return['general_code_details'] = $gcdtabledata;

        $dmtsql = "SELECT id, application_table_name, cancellation_application_table_name FROM dynamic_mapping_tables WHERE application_table_name = 'rental_invoices' AND company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $dmtres = $callConfig->getRow($dmtsql);
        $return['dynamic_mapping_tables'] = $dmtres;

        $return['get'] = $get;

        return $return;
    }
    public function backSideUpdatedata($get)
    {
        global $callConfig;
        $return = array('get' => $get);
        $paymenttable = "SELECT transaction_table, transaction_id, SUM(total_paid_amonut) as total_paid_amonut FROM `payment_allocations` WHERE is_active = 0 AND payment_id IN(SELECT id FROM `payments` WHERE current_status = 199 AND is_active = 0 AND cancel_payment = 'N') GROUP BY transaction_table, transaction_id";
        $paymenttabledata = $callConfig->getAllRows($paymenttable);

        $return['payment_allocations'] = $paymenttabledata;
        return $return;
    }
    private function fetchTenantBillingPayments($custids, $sdate, $edate, $previouslastdate): array
    {
        global $callConfig;
        $res = array();
        $ob_paymentssql = "SELECT DATE_FORMAT(pay.payment_date, '%m%Y') as payment_month_year, pay.party_id, SUM(pa.total_paid_amonut) as total_paid,ri.parent_ref_id as rental_inv_ref_id,uri.parent_ref_id as rental_utility_inv_ref_id FROM    payments pay, payment_allocations pa LEFT JOIN rental_invoices ri ON ri.id = pa.transaction_id AND pa.transaction_table = 'rental_invoices' AND    pa.company_id = ri.company_id AND pa.is_active = ri.is_active AND ri.cancel_invoice  ='N' LEFT JOIN utility_rental_invoices uri ON uri.id = pa.transaction_id AND pa.transaction_table = 'utility_rental_invoices' AND pa.company_id = uri.company_id AND pa.is_active = uri.is_active AND uri.cancel_invoice  ='N' WHERE    pay.company_id = '1' AND pay.company_id = pa.company_id AND    pay.cancel_payment = 'N' AND pay.payment_date <= '" . $previouslastdate . "' AND pay.party_id IN(" . $custids . ") AND pay.id = pa.payment_id AND pay.is_active = 0 AND pay.cancel_payment = 'N' AND pay.is_active = pa.is_active GROUP BY ri.parent_ref_id, uri.parent_ref_id";
        $ob_payments = array();
        foreach ($callConfig->getAllRows($ob_paymentssql) as $ob_ri) {
            if ($ob_ri->rental_inv_ref_id != "") {
                $ob_payments[$ob_ri->rental_inv_ref_id]['ob_rent'] = $ob_ri;
            } elseif ($ob_ri->rental_utility_inv_ref_id != "") {
                $ob_payments[$ob_ri->rental_utility_inv_ref_id]['ob_utility'] = $ob_ri;
            }
        }
        $res['opening_balance_payments_month_wyse'] = $ob_payments;

        $paymentssql = "SELECT DATE_FORMAT(pay.payment_date, '%m%Y') as payment_month_year, pay.party_id, SUM(pa.total_paid_amonut) as total_paid,ri.parent_ref_id as rental_inv_ref_id,uri.parent_ref_id as rental_utility_inv_ref_id FROM    payments pay, payment_allocations pa LEFT JOIN rental_invoices ri ON ri.id = pa.transaction_id AND pa.transaction_table = 'rental_invoices' AND    pa.company_id = ri.company_id AND pa.is_active = ri.is_active AND ri.cancel_invoice  ='N' LEFT JOIN utility_rental_invoices uri ON uri.id = pa.transaction_id AND pa.transaction_table = 'utility_rental_invoices' AND pa.company_id = uri.company_id AND pa.is_active = uri.is_active AND uri.cancel_invoice  ='N' WHERE    pay.company_id = '1' AND pay.company_id = pa.company_id AND    pay.cancel_payment = 'N' AND pay.payment_date >= '" . $sdate . "' AND pay.payment_date <=  '" . $edate . "' AND pay.id = pa.payment_id AND pay.is_active = 0 AND pay.cancel_payment = 'N' AND pay.is_active = pa.is_active GROUP BY YEAR(pay.payment_date), MONTH(pay.payment_date),ri.parent_ref_id, uri.parent_ref_id";
        $payments_month_wyse = array();
        foreach ($callConfig->getAllRows($paymentssql) as $ri1) {
            if ($ri1->rental_inv_ref_id != "") {
                $payments_month_wyse[$ri1->rental_inv_ref_id]['rent'][$ri1->payment_month_year] = $ri1;
            } elseif ($ri1->rental_utility_inv_ref_id != "") {
                $payments_month_wyse[$ri1->rental_utility_inv_ref_id]['utility'][$ri1->payment_month_year] = $ri1;
            }
        }
        $res['payments_month_wyse_sql'] = $paymentssql;
        $res['payments_month_wyse'] = $payments_month_wyse;
        return $res;
    }

    public function gettenantBillingReportsNewReport($get)
    {
        global $callConfig;
        $return['get'] = $get;
        $sdate = date("Y-m-01", strtotime($get['start_month']));
        $previouslastdate = date('Y-m-d', strtotime($sdate . ' - 1 days'));
        $edate = date("Y-m-t", strtotime($get['end_month']));
        $startDate = new DateTime($sdate);
        $endDate = new DateTime($edate);
        $monthyearary = array();
        while ($startDate <= $endDate) {
            $monthyearary[$startDate->format('mY')] = $startDate->format('M Y');
            $startDate->modify(RentalConstants::PLUS_ONE_MONTH);
        }
        $return['monthyearary'] = $monthyearary;
        $customer_condition = ($get['customer_id'] != "") ? " AND id = '" . $get['customer_id'] . "'" : "";
        $customerssql = RentalConstants::SEL_CUSTOMERS_COMPANY . $get['company_id'] . "' " . $customer_condition . RentalConstants::AND_IS_ACTIVE_0_MID_SPACE;
        $customersres = $callConfig->getAllRows($customerssql);
        $partyid = array();
        foreach ($customersres as $cut) {
            $partyid[$cut->id] = $cut->id;
        }
        $return['customers'] = $customersres;
        $custids = implode(',', $partyid);

        $property_tenant_detailssqql = "SELECT id, customer_id, property_owner_id, property_owner_detail_id, property_owner_area_ids FROM property_tenant_details  WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0 AND customer_id IN(" . $custids . ") AND agreement_expiry_date IS NULL";
        $property_tenant_details = array();
        foreach ($callConfig->getAllRows($property_tenant_detailssqql) as $poda) {
            $property_tenant_details[$poda->customer_id][] = $poda;
        }
        $return['property_tenant_details'] = $property_tenant_details;

        $property_owners = array();
        foreach ($callConfig->getAllRows(RentalConstants::SEL_PROPERTY_OWNERS . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE) as $pwd) {
            $property_owners[$pwd->id] = $pwd;
        }
        $return['property_owners'] = $property_owners;

        $property_owner_details = array();
        foreach ($callConfig->getAllRows(RentalConstants::SEL_POD . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE) as $pwd) {
            $property_owner_details[$pwd->id] = $pwd;
        }
        $return['property_owner_details'] = $property_owner_details;
        $return['opening_balance'] = $this->fetchTenantBillingOpeningBalance($get['company_id'], $custids, $previouslastdate);

        $invoices = array();
        $rental_invoicessql = "SELECT transaction_date, parent_ref_id, SUM(amount) as amount, SUM(tax_amount) as tax_amount, SUM(total_amount) as total_amount, SUM(pending_amount) as pending_amount FROM rental_invoices WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_STATUS . ST_APPROVED . "'  AND cancel_invoice = 'N' AND transaction_date >= '" . $sdate . RentalConstants::AND_TRANS_DATE_LTE_DOUBLE_SPACE . $edate . RentalConstants::AND_PARTY_ID_IN . $custids . ") GROUP BY MONTH(transaction_date), YEAR(transaction_date),parent_ref_id";
        $return['rent_sql'] = $rental_invoicessql;
        foreach ($callConfig->getAllRows($rental_invoicessql) as $ri) {
            $invoices[$ri->parent_ref_id]['rent'][date('mY', strtotime($ri->transaction_date))] = $ri;
        }

        $utility_rental_invoicessql = "SELECT transaction_date, parent_ref_id, ref_bill_type, SUM(amount) as amount, SUM(tax_amount) as tax_amount, SUM(total_amount) as total_amount, SUM(pending_amount) as pending_amount FROM utility_rental_invoices WHERE company_id = '" . $get['company_id'] . "' AND current_status = '" . ST_APPROVED . "'  AND cancel_invoice = 'N' AND transaction_date >= '" . $sdate . RentalConstants::AND_TRANS_DATE_LTE_DOUBLE_SPACE . $edate . RentalConstants::AND_PARTY_ID_IN . $custids . ") AND is_active =0 GROUP BY MONTH(transaction_date), YEAR(transaction_date),parent_ref_id,ref_bill_type";
        $return['utility_sql'] = $utility_rental_invoicessql;
        foreach ($callConfig->getAllRows($utility_rental_invoicessql) as $ri) {
            $invoices[$ri->parent_ref_id]['utility'][date('mY', strtotime($ri->transaction_date))] = $ri;
        }
        $return['invoices'] = $invoices;

        $return['all_customers'] = $callConfig->getAllRows(RentalConstants::SEL_CUSTOMERS_COMPANY . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0_NO_SPACE);

        $payData = $this->fetchTenantBillingPayments($custids, $sdate, $edate, $previouslastdate);
        $return = array_merge($return, $payData);

        $credit_notes = array();
        $credit_notesres = $callConfig->getAllRows("SELECT SUM(cn.total_amount) as total_amount, cn.party_id, ri.parent_ref_id FROM credit_notes cn LEFT JOIN rental_invoices ri ON cn.ref_id = ri.id AND ri.cancel_invoice = 'N' WHERE cn.company_id = '" . $get['company_id'] . RentalConstants::AND_CN_TRANS_DATE_LTE . $edate . RentalConstants::CN_GROUP_1);
        foreach ($credit_notesres as $ri) {
            $credit_notes[$ri->party_id][$ri->parent_ref_id] = $ri;
        }
        $return['credit_notes'] = $credit_notes;

        $debit_notes = array();
        $debit_notesres = $callConfig->getAllRows("SELECT SUM(cn.total_amount) as total_amount, cn.party_id, ri.parent_ref_id FROM debit_notes cn LEFT JOIN rental_invoices ri ON cn.ref_id = ri.id AND ri.cancel_invoice = 'N' WHERE cn.company_id = '" . $get['company_id'] . RentalConstants::AND_CN_TRANS_DATE_LTE . $edate . RentalConstants::CN_GROUP_2);
        foreach ($debit_notesres as $ri) {
            $debit_notes[$ri->party_id][$ri->parent_ref_id] = $ri;
        }
        $return['debit_notes'] = $debit_notes;
        return $return;
    }
    public function getUtilityRentalInvoices($get)
    {
        global $callConfig;
        $return['get'] = $get;
        $transaction_id = $get['transaction_id'];
        $utilityrentalinvoicessql = "SELECT * FROM `utility_rental_invoices` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $transaction_id . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $utilityrentalinvoicesres = $callConfig->getRow($utilityrentalinvoicessql);
        $return['utility_rental_invoices'] = $utilityrentalinvoicesres;

        $utilityrentalinvoiceitemssql = "SELECT * FROM `utility_rental_invoice_items` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_TRANS_ID . $transaction_id . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $utilityrentalinvoiceitemsres = $callConfig->getAllRows($utilityrentalinvoiceitemssql);
        $return['utility_rental_invoice_items'] = $utilityrentalinvoiceitemsres;

        $utilityrentalinvoiceitemtaxessql = "SELECT * FROM `utility_rental_invoice_item_taxes` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_TRANS_ID . $transaction_id . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $utilityrentalinvoiceitemtaxesres = $callConfig->getAllRows($utilityrentalinvoiceitemtaxessql);
        $hsnids = array();
        foreach ($utilityrentalinvoiceitemtaxesres as $hsncd) {
            if ($hsncd->tax_id != "" && $hsncd->tax_id != "0") {
                $hsnids[$hsncd->tax_id] = $hsncd->tax_id;
            }
        }
        $return['utility_rental_invoice_item_taxes'] = $utilityrentalinvoiceitemtaxesres;
        $implodehsnid = implode(',', (array) $hsnids);
        $hsnandsactable = RentalConstants::SEL_HSN_SAC . $get['company_id'] . RentalConstants::AND_ID_IN . $implodehsnid . ") AND is_active=0";
        $hsnandsacdata = $callConfig->getAllRows($hsnandsactable);
        $return['hsn_and_sac_codes'] = $hsnandsacdata;

        $gcdtable = RentalConstants::SEL_GENC_UTILITY . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $gcdtabledata = $callConfig->getAllRows($gcdtable);
        $return['utility_uom'] = $gcdtabledata;

        $statessql = "SELECT id, state_name FROM states WHERE company_id = '" . $get['company_id'] . "' AND  country_id = 101 AND is_active = 0";
        $statesres = $callConfig->getAllRows($statessql);
        $return['states'] = $statesres;

        return $return;
    }


    private function updateUtilityInvoiceItemsAndTaxes($post, $get, $transaction_id, $itemsTable, $taxesTable, array &$return): void
    {
        global $callConfig;
        if (empty($post['utility_rental_invoice_items_id']) || !is_array($post['utility_rental_invoice_items_id'])) {
            return;
        }
        foreach ($post['utility_rental_invoice_items_id'] as $key => $tritmid) {
            $itemData = [
                'item_quantity' => $post['item_quantity'][$key] ?? 1,
                'purchase_uom' => $post['purchase_uom'][$key] ?? '',
                'item_price' => $post['item_price'][$key] ?? 0,
                'item_amount' => $post['total_amount'][$key] ?? 0,
                'total_amount' => $post['total_amount'][$key] ?? 0,
                'description' => $post['description'][$key] ?? '',
                'operation_symbol' => $post['operation_symbol'][$key] ?? '',
                'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS),
                'updated_by' => $post['user_id']
            ];

            $sw = $post['utility_gst_switch'][$key] ?? '';
            $itemData['tax_amount'] = (($post['utility_gst'][$sw] ?? '') == "Y") ? ($post['utility_gst_total_tax'][$key] ?? 0) : 0;
            $return['itemsdata'][] = $itemData;
            $return['items'][] = $callConfig->updateRecord($itemsTable, $itemData, 'id', $tritmid);

            if (!empty($itemData['tax_amount'])) {
                if (!empty($post['utility_rental_invoice_item_taxes_id'][$key])) {
                    $taxData = ['amount' => $post['total_amount'][$key] ?? null, 'tax_amount' => $itemData['tax_amount'], 'total_amount' => $post['total_amount'][$key] ?? null, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'updated_by' => $post['user_id']];
                    $return['taxdata'][] = $taxData;
                    $callConfig->updateRecord($taxesTable, $taxData, 'id', $post['tax_ids'][$key] ?? $post['utility_rental_invoice_item_taxes_id'][$key]);
                } else {
                    $taxData = array('company_id' => $get['company_id'], 'transaction_id' => $transaction_id, 'transaction_item_id' => $post['item_id'][$key] ?? 0, 'tax_id' => $post['tax_id'][$key] ?? 0, 'tax_name' => $post['tax_name'][$key] ?? '', 'tax_value' => $post['utility_gst_percentage'][$key] ?? 0, 'amount' => $post['total_amount'][$key] ?? 0, 'tax_amount' => $itemData['tax_amount'], 'total_amount' => $post['total_amount'][$key] ?? 0, 'is_active' => 0, 'current_status' => 0, 'created_by' => $post['user_id'], 'created_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS));
                    $return['taxdata'][] = $taxData;
                    $return['tax_items'][] = $callConfig->insertRecord($taxesTable, $taxData);
                }
            }
        }
    }

    public function updateUtilityRentalInvoices($get, $post)
    {
        global $callConfig;
        $return = array('get' => $get, 'Post' => $post);
        $transaction_id = $post['rid'];
        $fieldsToUpdate = ['transaction_date' => $this->dateconvertdbformat($post['transaction_date']), 'due_date' => $this->dateconvertdbformat($post['due_date']), 'ref_no' => $post['ref_no'] ?? null, 'ref_date' => $this->dateconvertdbformat($post['ref_date']) ?? null, 'notes' => $post['notes'] ?? null, 'printing_notes' => $post['printing_notes'] ?? null, 'amount' => $post['sub_amount'] ?? null, 'tax_amount' => $post['total_tax_amount'] ?? null, 'total_amount' => $post['grand_total_amount'] ?? null, 'pending_amount' => $post['grand_total_amount'] ?? null, 'rounding_off' => $post['rounding_off'] ?? 0, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'updated_by' => $post['user_id'], 'originator_state_id' => $post['originator_state_id'] ?? null, 'supplier_state_id' => $post['supplier_state_id'] ?? null];
        $callConfig->updateRecord('utility_rental_invoices', $fieldsToUpdate, 'id', $transaction_id);
        $callConfig->updateRecord('utility_rental_invoice_item_taxes', array('is_active' => 1), 'transaction_id', $transaction_id);

        $return['res'] = $fieldsToUpdate;
        $this->updateUtilityInvoiceItemsAndTaxes($post, $get, $transaction_id, 'utility_rental_invoice_items', 'utility_rental_invoice_item_taxes', $return);
        return $return;
    }

    public function updateUtilityAMCRentalInvoices($get, $post)
    {
        global $callConfig;
        $return = array('get' => $get, 'Post' => $post);
        $transaction_id = $post['rid'];
        $fieldsToUpdate = ['transaction_date' => $this->dateconvertdbformat($post['transaction_date']), 'due_date' => $this->dateconvertdbformat($post['due_date']), 'ref_no' => $post['ref_no'] ?? null, 'ref_date' => $this->dateconvertdbformat($post['ref_date']) ?? null, 'notes' => $post['notes'] ?? null, 'printing_notes' => $post['printing_notes'] ?? null, 'amount' => $post['sub_amount'] ?? null, 'tax_amount' => $post['total_tax_amount'] ?? null, 'total_amount' => $post['grand_total_amount'] ?? null, 'pending_amount' => $post['grand_total_amount'] ?? null, 'rounding_off' => $post['rounding_off'] ?? 0, 'updated_date' => date(RentalConstants::DATE_FORMAT_YMD_HIS), 'updated_by' => $post['user_id'], 'originator_state_id' => $post['originator_state_id'] ?? null, 'supplier_state_id' => $post['supplier_state_id'] ?? null];
        $callConfig->updateRecord('utility_amc_rental_invoices', $fieldsToUpdate, 'id', $transaction_id);
        $callConfig->updateRecord('utility_amc_rental_invoice_item_taxes', array('is_active' => 1), 'transaction_id', $transaction_id);

        $return['res'] = $fieldsToUpdate;
        $this->updateUtilityInvoiceItemsAndTaxes($post, $get, $transaction_id, 'utility_amc_rental_invoice_items', 'utility_amc_rental_invoice_item_taxes', $return);
        return $return;
    }
    public function getUtilityAMCRentalInvoices($get)
    {
        global $callConfig;
        $return['get'] = $get;
        $transaction_id = $get['transaction_id'];
        $utilityrentalinvoicessql = "SELECT * FROM `utility_amc_rental_invoices` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_ID_APOS . $transaction_id . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $utilityrentalinvoicesres = $callConfig->getRow($utilityrentalinvoicessql);
        $return['utility_rental_invoices'] = $utilityrentalinvoicesres;

        $utilityrentalinvoiceitemssql = "SELECT * FROM `utility_amc_rental_invoice_items` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_TRANS_ID . $transaction_id . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $utilityrentalinvoiceitemsres = $callConfig->getAllRows($utilityrentalinvoiceitemssql);
        $return['utility_rental_invoice_items'] = $utilityrentalinvoiceitemsres;

        $utilityrentalinvoiceitemtaxessql = "SELECT * FROM `utility_amc_rental_invoice_item_taxes` WHERE company_id = '" . $get['company_id'] . RentalConstants::AND_TRANS_ID . $transaction_id . RentalConstants::AND_IS_ACTIVE_0_COMPACT;
        $utilityrentalinvoiceitemtaxesres = $callConfig->getAllRows($utilityrentalinvoiceitemtaxessql);
        $hsnids = array();
        foreach ($utilityrentalinvoiceitemtaxesres as $hsncd) {
            if ($hsncd->tax_id != "" && $hsncd->tax_id != "0") {
                $hsnids[$hsncd->tax_id] = $hsncd->tax_id;
            }
        }
        $return['utility_rental_invoice_item_taxes'] = $utilityrentalinvoiceitemtaxesres;
        $implodehsnid = implode(',', (array) $hsnids);
        $hsnandsactable = RentalConstants::SEL_HSN_SAC . $get['company_id'] . RentalConstants::AND_ID_IN . $implodehsnid . ") AND is_active=0";
        $hsnandsacdata = $callConfig->getAllRows($hsnandsactable);
        $return['hsn_and_sac_codes'] = $hsnandsacdata;

        $gcdtable = RentalConstants::SEL_GENC_UTILITY . $get['company_id'] . RentalConstants::AND_IS_ACTIVE_0;
        $gcdtabledata = $callConfig->getAllRows($gcdtable);
        $return['utility_uom'] = $gcdtabledata;

        $statessql = "SELECT id, state_name FROM states WHERE company_id = '" . $get['company_id'] . "' AND  country_id = 101 AND is_active = 0";
        $statesres = $callConfig->getAllRows($statessql);
        $return['states'] = $statesres;

        return $return;
    }
}

class RentalUserClass
{
    use RentalPropertyTrait;
    use RentalBillingTrait;
}
if (!class_exists('rentalUserClass', false)) {
    class_alias('RentalUserClass', 'rentalUserClass');
}
