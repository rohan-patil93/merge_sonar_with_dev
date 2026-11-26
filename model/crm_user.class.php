<?php

final class CrmConstants {
    public const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    public const AND_IS_ACTIVE_0_APOS = "' AND is_active = 0";
    public const AND_IS_ACTIVE_0 = " AND is_active = 0";
    public const AND_ID_APOS = "' AND id = '";
    public const AND_COMPANY_ID_EQ = " AND company_id=";
    public const AND_IS_ACTIVE_0_NO_SPACE = " AND is_active=0";
    public const COMPANY_ID_EQ = " company_id=";
    public const AND_ID_EQ = "' AND id = ";
    public const AND_POSITION = " AND position_";
    public const AND_APP_SERIAL_MASTER_ID = "' AND application_serial_number_master_id = '";
    public const SEL_APP_SERIAL_MASTER_DETAILS = "SELECT * FROM application_serial_number_master_details WHERE company_id = '";
}

trait CrmSalesAndImportsTrait {
    public function importCSV($get, $post){
        global $callConfig;
        $return = array();
        $parent_id =  '';

        $data = array(
            'file_name' => $post['file_name'],
            'import_name' => $post['import_name'],
            'company_id' => $get['company_id'],
            'current_status' => '0',
            'is_active' => '0',
            'created_date' => date(CrmConstants::DATE_TIME_FORMAT),
            'created_by' => $get['user_id'],
            'import_date' =>$this->dateconvertdbformat($post['import_date']),
            'invoice_from_date' =>$this->dateconvertdbformat($post['invoice_from_date']),
            'invoice_to_date' =>$this->dateconvertdbformat($post['invoice_to_date'])
        );

        $res = $callConfig->insertRecord('sales_invoice_data_parent', $data);
        $parent_id = $res['LASTID'];
        $return['parent'] = $res;

        $csvData = base64_decode($post['filedata']);

        $rows = explode("\n", $csvData);
        $headerSkipped = false;
        if($parent_id != '' && $parent_id > 0){
            foreach ($rows as $line) {
                if (!$headerSkipped) {
                    $headerSkipped = true;
                    continue;
                }

                $columns = str_getcsv($line, ",", '"');
                if (count($columns) < 32 && count(array_filter($columns)) === 0) {
                    continue; // skip only completely empty lines
                }

                $insert_array = array();

                $insert_array['user_name'] = trim($columns[0]);
                $insert_array['party_name'] = trim($columns[1]);
                $insert_array['user_code'] = trim($columns[2]);
                $insert_array['pin_code'] = trim($columns[3]);
                $insert_array['sales_person'] = trim($columns[4]);
                $insert_array['address'] = trim($columns[5]);
                $insert_array['mobile_no'] = trim($columns[6]);
                $insert_array['expiry_date'] = $this->toMySQLDate($columns[7]);
                $insert_array['mode_of_renew'] = trim($columns[8]);
                $insert_array['bill_no'] = trim($columns[9]);
                $insert_array['activation_type'] = trim($columns[10]);
                $insert_array['plan_activation_date'] = $this->toMySQLDate($columns[11]);
                $insert_array['bill_date'] = $this->toMySQLDateTime($columns[12]);
                $insert_array['plan_amount'] = trim($columns[13]);
                $insert_array['client_gst'] = trim($columns[14]);
                $insert_array['sgst'] = trim($columns[15]);
                $insert_array['cgst'] = trim($columns[16]);
                $insert_array['bill_amount'] = trim($columns[17]);
                $insert_array['operator_amount'] = trim($columns[18]);
                $insert_array['partner_share'] = trim($columns[19]);
                $insert_array['partner_commission'] = trim($columns[20]);
                $insert_array['adjusted_amount'] = trim($columns[21]);
                $insert_array['actual_bill_amount'] = trim($columns[22]);
                $insert_array['discount_amount'] = trim($columns[23]);
                $insert_array['user_pending_amount'] = trim($columns[24]);
                $insert_array['plan_name'] = trim($columns[25]);
                $insert_array['site'] = trim($columns[26]);
                $insert_array['building_name'] = trim($columns[27]);
                $insert_array['zone_name'] = trim($columns[28]);
                $insert_array['operator_name'] = trim($columns[29]);
                $insert_array['franchisee_name'] = trim($columns[30]);
                $insert_array['user_current_status'] = trim($columns[31]);
                $insert_array['parent_id'] = $parent_id;
                $insert_array['company_id'] = $get['company_id'];
                $insert_array['current_status'] = '0';
                $insert_array['is_active'] = '0';
                $insert_array['created_date'] = date(CrmConstants::DATE_TIME_FORMAT);
                $insert_array['created_by'] = $get['user_id'];

                $res1 = $callConfig->insertRecord('sales_invoice_data_excel', $insert_array);
                $return['res'][] = $res1;
                   if (!$res1) {
                    error_log("Insert failed: " . json_encode($insert_array));
                }

            }
        }

           return $return;

    }

    public function toMySQLDate($dateString) {
        $date = DateTime::createFromFormat('d-m-Y', trim($dateString));
        return $date ? $date->format('Y-m-d') : null;
    }

    public function toMySQLDateTime($dateTimeString) {
        $date = DateTime::createFromFormat('d-m-Y H:i:s', trim($dateTimeString));
        return $date ? $date->format(CrmConstants::DATE_TIME_FORMAT) : null;
    }

    public function getSalesInvoiceDynamicData($get){
        global $callConfig;
        $return['get'] = $get;
        $sql = "SELECT s.id,s.user_name,s.party_name,s.mobile_no,s.expiry_date,s.bill_no, s.plan_name,
                CASE
                    WHEN (c.company IS NOT NULL  OR c.display_name IS NOT NULL) THEN 'Yes'
                    ELSE 'No'
                END AS party_exists,
                CASE
                    WHEN (i.item_name IS NOT NULL OR i.sku IS NOT NULL)  THEN 'Yes'
                    ELSE 'No'
                 END AS plan_exists,
                CASE
                    WHEN si.transaction_number IS NOT NULL  THEN 'Yes'
                    ELSE 'No'
                END AS transaction_exists,
                CASE
                    WHEN (sm.salesman_name IS NOT NULL) THEN 'Yes'
                    ELSE 'No'
                 END AS salesman_exists,
                si.id as sales_invoice_id,
                si.transaction_number as sales_invoice_transaction_number
            FROM
                sales_invoice_data_excel s
            LEFT JOIN
                customers c ON (c.company = s.party_name OR c.display_name = s.party_name) AND c.company_id= s.company_id AND c.is_active=0
            LEFT JOIN
                items i ON (i.item_name = s.plan_name OR i.sku = s.plan_name) AND i.company_id= s.company_id AND i.is_active=0
            LEFT JOIN
                salesmen sm ON (sm.salesman_name = s.user_name) AND sm.company_id= s.company_id AND sm.is_active=0
            LEFT JOIN
                sales_invoices si ON si.doc_ref_no = s.bill_no AND si.company_id= s.company_id AND si.is_active=0
            WHERE s.parent_id = ".$get['rid']." AND
                s.company_id = ".$get['company_id']." AND s.is_active = 0";
        $res = $callConfig->getAllRows($sql);
        $sales = array();
        foreach($res as $row) {
            $sales[] = $row;
        }
        $return['sales_sql'] = $sql;
        $return['sales'] = $sales;

        return $return;
    }

    public function getSalesInvoiceDynamicExcelList($get){
        global $callConfig;
        $condlimi = '';
        if($get['LIMIT']!=""){
            $condlimi = " LIMIT ".$get['LIMIT'];
        }
        $sql = "SELECT * FROM sales_invoice_data_parent WHERE company_id=".$get['company_id']." AND is_active=0 ORDER BY `import_date` ASC".$condlimi;
        $res = $callConfig->getAllRows($sql);
        $excel_data = array();
        foreach($res as $row) {
            $excel_data[] = $row;
        }
        $return['excel_data'] = $excel_data;

        return $return;

    }

    private function resolveOrCreateCustomer($post, $first, array &$customerscompany, array &$customersdisplay_name)
    {
        global $callConfig;
        if (isset($customerscompany[$first->party_name])) {
            return $customerscompany[$first->party_name];
        }
        if (isset($customersdisplay_name[$first->party_name])) {
            return $customersdisplay_name[$first->party_name];
        }
        $insertcustomersary = array(
            'company_id' => $post['company_id'],
            'company' => $first->party_name,
            'display_name' => $first->party_name,
            'email' => '',
            'billing_address' => $first->address,
            'shipping_address' => $first->address,
            'billing_post_code' => $first->pin_code,
            'shipping_post_code' => $first->pin_code,
            'phone' => $first->mobile_no,
            'contact_person' => $first->user_name,
            'is_active' => 0,
            'current_status' => 0,
            'created_date' => date(CrmConstants::DATE_TIME_FORMAT),
            'created_by' => $post['user_id']
        );
        $customerres = $callConfig->insertRecord('customers', $insertcustomersary);
        $custdeatilary = array(
            'id' => $customerres['LASTID'],
            'company' => $first->party_name,
            'display_name' => $first->party_name,
            'email' => '',
            'billing_address' => $first->address,
            'shipping_address' => $first->address,
            'billing_post_code' => $first->pin_code,
            'shipping_post_code' => $first->pin_code,
            'phone' => $first->mobile_no
        );
        $custdeatil = (object) $custdeatilary;
        $customerscompany[$first->party_name] = $custdeatil;
        return $custdeatil;
    }

    private function calculateBillTaxAndTotals(array $billitems, $post, $tax_authorities_rates_splitres1, array &$return): array
    {
        $saleinvitem = array('amount' => 0, 'total_amount' => 0, 'total_tax_amount' => 0, 'discount_amount' => 0);
        $txvalc1 = $txvalc2 = $txperc1 = $txperc2 = $split_1 = $split_2 = 0;
        $tax_flag = "S";
        $split_1_name = $split_2_name = $split_1_gl_account_code = $split_2_gl_account_code = '';

        foreach ($billitems as $key => $itmlist) {
            $saleinvitem['amount'] += $itmlist->plan_amount;
            $saleinvitem['total_amount'] += $itmlist->bill_amount;
            $cgstamt = ($itmlist->cgst != "") ? $itmlist->cgst : 0;
            $sgstamt = ($itmlist->sgst != "") ? $itmlist->sgst : 0;
            $totaltax = $cgstamt + $sgstamt;
            $saleinvitem['total_tax_amount'] += $totaltax;
            $saleinvitem['discount_amount'] += $itmlist->discount_amount;

            if (count((array) $tax_authorities_rates_splitres1) != 0) {
                $tax_authorities_rates_splitres = json_decode(json_encode($tax_authorities_rates_splitres1), true);
                $return['tax_authorities_rates_splitres'] = $tax_authorities_rates_splitres;
                $split_1_name = $tax_authorities_rates_splitres[0]['split_name'];
                $split_2_name = $tax_authorities_rates_splitres[1]['split_name'];
                $split_1 = $tax_authorities_rates_splitres[0]['tax_rate'];
                $split_2 = $tax_authorities_rates_splitres[1]['tax_rate'];
                $split_1_gl_account_code = $tax_authorities_rates_splitres[0]['account_code'];
                $split_2_gl_account_code = $tax_authorities_rates_splitres[1]['account_code'];
                $return['split_1_name'] = $split_1_name;
                $return['split_1'] = $split_1;
            }

            if ($itmlist->sgst != '0' && $itmlist->sgst != '') {
                if ($tax_flag == "S") {
                    $perc = ($totaltax / $itmlist->plan_amount) * 100;
                    $txperc1 = $perc * $split_1;
                    $txvalc1 += $totaltax * $split_1;
                    $txperc2 = $perc * $split_2;
                    $txvalc2 += $totaltax * $split_2;
                } else {
                    $txperc1 = ($post['tax'][$key] ?? 0) * $split_1;
                    $txvalc1 += ($post['ptax_amount'][$key] ?? 0) * $split_1;
                }
            }
        }

        $total_tax_split_details = ($tax_flag == "S")
            ? array(array('tax_name' => $split_1_name, 'tax_percentage' => $txperc1, 'tax_amount' => $txvalc1, 'account_code' => $split_1_gl_account_code), array('tax_name' => $split_2_name, 'tax_percentage' => $txperc2, 'tax_amount' => $txvalc2, 'account_code' => $split_2_gl_account_code))
            : array(array('tax_name' => $split_1_name, 'tax_percentage' => $txperc1, 'tax_amount' => $txvalc1, 'account_code' => $split_1_gl_account_code));

        $return['total_tax_split_details'] = $total_tax_split_details;
        $taxConfig = compact('tax_flag', 'split_1', 'split_2', 'split_1_name', 'split_2_name', 'split_1_gl_account_code', 'split_2_gl_account_code');
        return [$saleinvitem, $total_tax_split_details, $taxConfig];
    }

    private function insertSalesInvoiceItemsAndTaxes($post, array $billitems, $sales_invoice_id, array $hsnary, array &$itemssku, array &$itemsitemname, array $taxConfig, array &$return): void
    {
        global $callConfig;
        $split_1 = $taxConfig['split_1'];
        $split_2 = $taxConfig['split_2'];
        $split_1_name = $taxConfig['split_1_name'];
        $split_2_name = $taxConfig['split_2_name'];
        $split_1_gl_account_code = $taxConfig['split_1_gl_account_code'];
        $split_2_gl_account_code = $taxConfig['split_2_gl_account_code'];
        $tax_flag = $taxConfig['tax_flag'];

        foreach ($billitems as $key => $itmlist) {
            if (!empty($itemssku[$itmlist->plan_name])) {
                $itemdetail = $itemssku[$itmlist->plan_name];
            } elseif (!empty($itemsitemname[$itmlist->plan_name])) {
                $itemdetail = $itemsitemname[$itmlist->plan_name];
            } else {
                $insertitemary = array('company_id' => $post['company_id'], 'sku' => $itmlist->plan_name, 'item_name' => $itmlist->plan_name, 'is_active' => 0, 'current_status' => 0, 'created_date' => date(CrmConstants::DATE_TIME_FORMAT), 'created_by' => $post['user_id']);
                $itemsres = $callConfig->insertRecord('items', $insertitemary);
                $itmary = array('id' => $itemsres['LASTID'], 'item_name' => $itmlist->plan_name);
                $itemdetail = (object) $itmary;
                $itemssku[$itmlist->plan_name] = $itemdetail;
            }

            $descriptions = $itmlist->site . " " . $itmlist->building_name . " " . $itmlist->zone_name . " " . $itmlist->operator_name . " " . $itmlist->franchisee_name . " Plan Activation Date - " . $itmlist->plan_activation_date . " To " . $itmlist->expiry_date;
            $itemfieldnames = array('company_id' => $post['company_id'], 'transaction_id' => $sales_invoice_id, 'item_id' => $itemdetail->id, 'item_name' => $itemdetail->item_name, 'description' => $descriptions, 'purchase_uom' => 31, 'item_quantity' => 1, 'item_price' => $itmlist->plan_amount, 'item_amount' => $itmlist->plan_amount, 'tax_amount' => ($itmlist->sgst + $itmlist->cgst), 'total_amount' => $itmlist->bill_amount, 'discount_amount' => $itmlist->discount_amount, 'req_date' => '', 'account_code' => '', 'item_wa_cost' => 0, 'item_fifo_cost' => 0, 'item_lifo_cost' => 0, 'pending_qty' => 1, 'is_active' => '0', 'current_status' => '0', 'ref_transaction_item_table_id' => '');
            $res2 = $callConfig->insertRecord('si_items', $itemfieldnames);
            $return['res_item'][] = $res2;

            if ($itmlist->sgst != '0' && $itmlist->sgst != '') {
                $perc = (($itmlist->sgst + $itmlist->cgst) / $itmlist->plan_amount) * 100;
                $minDiff = PHP_INT_MAX;
                $nearest = 18;
                foreach ([5, 8, 12, 18, 28] as $value) {
                    $diff = abs($value - $perc);
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $nearest = $value;
                    }
                }
                $hsnnearest = $hsnary[$nearest] ?? $hsnary['18'];
                $taxfield = array('company_id' => $post['company_id'], 'transaction_id' => $sales_invoice_id, 'transaction_item_id' => $itemdetail->id, 'tax_id' => $hsnnearest['hsn_id'], 'tax_name' => $hsnnearest['hsn_name'], 'amount' => $itmlist->plan_amount, 'tax_amount' => ($itmlist->sgst + $itmlist->cgst), 'total_amount' => $itmlist->bill_amount, 'tax_type' => $tax_flag, 'tax_value' => $hsnnearest['percentage'], 'is_active' => '0', 'current_status' => '0', 'transaction_item_row_id' => $res2['LASTID']);

                if ($tax_flag == "S") {
                    $txper1 = $perc * $split_1;
                    $txval1 = ($itmlist->sgst + $itmlist->cgst) * $split_1;
                    $txper2 = $perc * $split_2;
                    $txval2 = ($itmlist->sgst + $itmlist->cgst) * $split_2;
                    $tax_split_details = array(array('tax_name' => $split_1_name, 'tax_percentage' => $txper1, 'tax_amount' => $txval1, 'account_code' => $split_1_gl_account_code), array('tax_name' => $split_2_name, 'tax_percentage' => $txper2, 'tax_amount' => $txval2, 'account_code' => $split_2_gl_account_code));
                } else {
                    $txper1 = ($post['tax'][$key] ?? 0) * $split_1;
                    $txval1 = ($itmlist->sgst + $itmlist->cgst) * $split_1;
                    $tax_split_details = array(array('tax_name' => $split_1_name, 'tax_percentage' => $txper1, 'tax_amount' => $txval1, 'account_code' => $split_1_gl_account_code));
                }
                $taxfield['tax_split_details'] = json_encode($tax_split_details);
                $callConfig->insertRecord('si_item_taxes', $taxfield);
                $return['res_itm_tax'][] = $taxfield;
            }
        }
    }

    private function processSingleSalesInvoiceBill($get, $post, array $billitems, array &$customerscompany, array &$customersdisplay_name, array &$itemssku, array &$itemsitemname, $tax_authorities_rates_splitres1, array $hsnary, array &$return): void
    {
        global $callConfig;
        $first = $billitems[0];
        $custdeatil = $this->resolveOrCreateCustomer($post, $first, $customerscompany, $customersdisplay_name);
        list($saleinvitem, $total_tax_split_details, $taxConfig) = $this->calculateBillTaxAndTotals($billitems, $post, $tax_authorities_rates_splitres1, $return);

        $totaltax = $saleinvitem['total_tax_amount'];
        $payment_term_history = json_encode(array(
            array('amount' => $saleinvitem['total_amount'], 'due_date' => $this->dateconvertdbformat($first->bill_date), 'extra_amt' => '0', 'extra_date' => '', 'percentage' => '100', 'tax_amount' => $totaltax, 'without_tax_amount' => $saleinvitem['amount'])
        ));
        $party_additional_details = json_encode(array('gstin' => $first->client_gst, 'pan_no' => $custdeatil->pan_no));

        $sales_invoice_data = array(
            'transaction_number' => $this->toGetApplicationSerialNumber('sales_invoices'),
            'transaction_date' => $this->dateconvertdbformat($first->bill_date),
            'currency_id' => 93,
            'currency_rate' => 1,
            'originator' => $post['user_id'],
            'party_id' => $custdeatil->id,
            'salesmen_id' => null,
            'party_name' => $custdeatil->company,
            'party_email' => $custdeatil->email,
            'party_phone' => $first->mobile_no,
            'party_address' => $custdeatil->shipping_address,
            'party_ship_adderss' => $custdeatil->shipping_address,
            'originator_state_id' => 4008,
            'supplier_state_id' => 4008,
            'amount' => $saleinvitem['amount'],
            'tax_amount' => $saleinvitem['total_tax_amount'],
            'discount_amount' => $saleinvitem['discount_amount'],
            'total_amount' => $saleinvitem['total_amount'],
            'due_date' => $this->dateconvertdbformat($first->bill_date),
            'pending_amount' => $saleinvitem['total_amount'],
            'company_id' => $get['company_id'],
            'current_status' => ST_APPROVED,
            'is_active' => '0',
            'created_date' => date(CrmConstants::DATE_TIME_FORMAT),
            'created_by' => $get['user_id'],
            'total_tax_split_details' => json_encode($total_tax_split_details),
            'payment_term_history' => $payment_term_history,
            'party_additional_details' => $party_additional_details,
            'doc_ref_no' => $first->bill_no,
            'doc_ref_date' => $this->dateconvertdbformat($first->bill_date),
            'ref_type' => 'sales_invoice_data_parent',
            'ref_id' => $first->id,
            'ref_no' => $first->bill_no,
            'ref_date' => $this->dateconvertdbformat($first->bill_date),
            'payment_terms' => 1
        );

        $return['sales_invoice_data'][] = $sales_invoice_data;
        $res1 = $callConfig->insertRecord('sales_invoices', $sales_invoice_data);
        $return['res'] = $res1;

        if ($res1['status'] == 'sucess') {
            $this->insertSalesInvoiceItemsAndTaxes($post, $billitems, $res1['LASTID'], $hsnary, $itemssku, $itemsitemname, $taxConfig, $return);
        }

        $query4 = "UPDATE sales_invoice_data_excel SET table_name ='sales_invoices', table_id = '" . $res1['LASTID'] . "',comments = '',current_status=" . ST_APPROVED . " WHERE bill_no = '" . $first->bill_no . "'";
        $callConfig->executeQuery($query4);
    }

    public function createSalesInvoice($get, $post) {
        global $callConfig;
        $return = array();
        $return['post'] = $post;
        $selected_ids = $post['selected_ids'];
        $hsnary = array('5' => array('hsn_id' => 141, 'hsn_name' => '0303.00.00', 'percentage' => 5), '12' => array('hsn_id' => 5, 'hsn_name' => '0101.21.00', 'percentage' => 12), '18' => array('hsn_id' => 245, 'hsn_name' => '0402.91.10', 'percentage' => 18), '28' => array('hsn_id' => 1086, 'hsn_name' => '1703.00.00', 'percentage' => 28));

        if (!empty($selected_ids) && is_array($selected_ids)) {
            $bill_no_array = $customerscompany = $customersdisplay_name = $itemssku = $itemsitemname = array();
            $implodeid = implode("','", $selected_ids);
            $query = "SELECT * FROM sales_invoice_data_excel WHERE bill_no IN ('" . $implodeid . "') AND company_id = " . $get['company_id'] . " AND parent_id = '" . $post['sales_invoice_data_parent_id'] . CrmConstants::AND_IS_ACTIVE_0_APOS;
            foreach ($callConfig->getAllRows($query) as $side) {
                $bill_no_array[$side->bill_no][] = $side;
            }
            $return['bill_no_array'] = $bill_no_array;

            $custquery = "SELECT id, company, display_name,email, phone, mobile, billing_address, shipping_address, payment_terms, gstin, pan_no FROM customers WHERE company_id = " . $get['company_id'] . CrmConstants::AND_IS_ACTIVE_0;
            foreach ($callConfig->getAllRows($custquery) as $custrs) {
                $customerscompany[$custrs->company] = $custrs;
                $customersdisplay_name[$custrs->display_name] = $custrs;
            }

            $itemquery = "SELECT id, sku, item_name, sale_uom FROM items WHERE company_id = " . $get['company_id'] . CrmConstants::AND_IS_ACTIVE_0;
            foreach ($callConfig->getAllRows($itemquery) as $itemre) {
                $itemssku[$itemre->sku] = $itemre;
                $itemsitemname[$itemre->item_name] = $itemre;
            }

            $tax_authorities_rates_splitsql = "SELECT id, split_name, (tax_rate/100) as tax_rate, account_code, split_type, sales_account_code FROM `tax_authorities_rates_split` WHERE company_id = '" . $post['company_id'] . "' AND is_active =0";
            $tax_authorities_rates_splitres1 = $callConfig->getAllRows($tax_authorities_rates_splitsql);

            foreach ($bill_no_array as $billitems) {
                $this->processSingleSalesInvoiceBill($get, $post, $billitems, $customerscompany, $customersdisplay_name, $itemssku, $itemsitemname, $tax_authorities_rates_splitres1, $hsnary, $return);
            }
        }
        return $return;
    }
    //Koteswar End
    //OOmkar Data uploaded - 23-06-2025
    private function saveLeadContactImportRow(array $columns, $parent_id, $get, array &$seenPhones, &$inserted, &$duplicates, &$failed): void
    {
        global $callConfig;
        $phone = trim($columns[3]);
        $officePhone = trim($columns[4]);
        $is_duplicate = (in_array($phone, $seenPhones) || in_array($officePhone, $seenPhones)) ? 1 : 0;

        if (!empty($phone)) {
            $seenPhones[] = $phone;
        }
        if (!empty($officePhone)) {
            $seenPhones[] = $officePhone;
        }
        if ($is_duplicate) {
            $duplicates++;
        }

        $insert_array = array(
            'first_name' => trim($columns[0]),
            'last_name' => trim($columns[1]),
            'display_name' => trim($columns[0] . ' ' . $columns[1]),
            'email' => trim($columns[2]),
            'phone' => $phone,
            'office_phone' => $officePhone,
            'address' => trim($columns[5]),
            'company_name' => trim($columns[6]),
            'campaign_name' => trim($columns[7]),
            'crm_import_file_id' => $parent_id,
            'company_id' => $get['company_id'],
            'current_status' => '0',
            'is_active' => '0',
            'is_duplicate' => $is_duplicate,
            'created_date' => date(CrmConstants::DATE_TIME_FORMAT),
            'created_by' => $get['user_id']
        );

        $res1 = $callConfig->insertRecord('crm_lead_and_contact_import_items', $insert_array);
        if (isset($res1['LASTID']) && $res1['LASTID'] > 0) {
            $inserted++;
        } else {
            $failed++;
        }
    }

    public function importleadcontactCSV($get, $post)
    {
        global $callConfig;
        $return = array('get' => $get, 'post' => $post);
        $data = array('file_name' => $post['file_name'], 'import_type' => $post['import_type'], 'original_file_name' => $post['original_file_name'], 'company_id' => $get['company_id'], 'source_type' => $post['source_type'], 'current_status' => '0', 'is_active' => '0', 'created_date' => date(CrmConstants::DATE_TIME_FORMAT), 'created_by' => $get['user_id']);
        $res = $callConfig->insertRecord('crm_lead_and_contact_imports', $data);
        $parent_id = $res['LASTID'];
        if (!$parent_id) {
            return ['status' => 'error', 'message' => 'Failed to create import file record.'];
        }

        $csvData = base64_decode($post['filedata']);
        $rows = explode("\n", $csvData);
        $headerSkipped = false;
        $seenPhones = [];
        $inserted = $duplicates = $failed = 0;

        foreach ($rows as $line) {
            if (!$headerSkipped) {
                $headerSkipped = true;
                continue;
            }
            $columns = str_getcsv($line, ",", '"');
            if (count($columns) < 8 || count(array_filter($columns)) === 0) {
                continue;
            }
            $this->saveLeadContactImportRow($columns, $parent_id, $get, $seenPhones, $inserted, $duplicates, $failed);
        }

        $return['status'] = 'success';
        $return['message'] = "Data import complete.";
        $return['rid'] = $parent_id;
        $return['summary'] = ['total_rows' => count($rows) - 2, 'inserted' => $inserted, 'duplicates' => $duplicates, 'failed' => $failed];
        return $return;
    }
    public function getlistcontactleaddata($get)
    {
        global $callConfig;
        $return['get'] = $get;

        $query = "SELECT id, file_name, import_type, original_file_name, source_type FROM crm_lead_and_contact_imports WHERE company_id = '" . $get['company_id'] . CrmConstants::AND_ID_APOS . $get['rid'] . CrmConstants::AND_IS_ACTIVE_0_APOS;
        $data = $callConfig->getAllRows($query);
        $return['crm_import_files'] = $data;
        $query = "SELECT id, crm_import_file_id, display_name, first_name, last_name, email, phone, office_phone, address, company_name, campaign_name, is_duplicate FROM crm_lead_and_contact_import_items WHERE company_id = '" . $get['company_id'] . "' AND crm_import_file_id = '" . $get['rid'] . CrmConstants::AND_IS_ACTIVE_0_APOS;
        $data = $callConfig->getAllRows($query);
        $return['crm_import_file_rows'] = $data;
        return $return;
    }

    public function insertcontactdata($get, $post){
        global $callConfig;
        $rows = $post['rows'] ?? [];
        $files = $post['files'][0] ?? [];
        $inserted = 0;
        $duplicates = 0;
        $failed = 0;
        $seenPhones = [];
        foreach ($rows as $row) {
            $phone = trim($row['phone'] ?? '');
            $officePhone = trim($row['office_phone'] ?? '');
            $is_duplicate = (in_array($phone, $seenPhones) || in_array($officePhone, $seenPhones)) ? 1 : 0;
            if (!empty($phone)) {
                $seenPhones[] = $phone;
            }
            if (!empty($officePhone)) {
                $seenPhones[] = $officePhone;
            }
            if ($is_duplicate) {
                $duplicates++;
            }
            $contact = ['company_id' => $get['company_id'], 'first_name' => $row['first_name'] ?? '', 'middle_name' => '', 'last_name' => $row['last_name'] ?? '','display_name' => $row['display_name'] ?? '', 'email_id' => $row['email'] ?? '', 'phone' => $phone, 'office_phone' => $officePhone, 'address' => $row['address'] ?? '', 'company_name' => $row['company_name'] ?? '', 'originator' => $get['user_id'], 'contact_source' => $files['source_type'] ?? '', 'image' => null, 'is_active' => '0', 'current_status' => '0', 'created_date' => date(CrmConstants::DATE_TIME_FORMAT), 'created_by' => $get['user_id']];
            $result = $callConfig->insertRecord('crm_contacts', $contact);
            if (isset($result['LASTID']) && $result['LASTID'] > 0) {
                $inserted++;
            } else {
                $failed++;
            }
        }
        return ['status' => 'success','message' => 'Contacts imported successfully.','summary' => ['total_rows' => count($rows), 'inserted' => $inserted, 'duplicates' => $duplicates, 'failed' => $failed]];
    }


    public function insertleaddata($get, $post){
        global $callConfig;
        $rows = $post['rows'] ?? [];
        $files = $post['files'][0] ?? [];
        $inserted = 0;
        $duplicates = 0;
        $failed = 0;
        $seenPhones = [];
        foreach ($rows as $row) {
            $phone = trim($row['phone']);
            $officePhone = trim($row['office_phone']);
            $is_duplicate = (in_array($phone, $seenPhones) || in_array($officePhone, $seenPhones)) ? 1 : 0;
            if (!empty($phone)) {
                $seenPhones[] = $phone;
            }
            if (!empty($officePhone)) {
                $seenPhones[] = $officePhone;
            }
            if ($is_duplicate) {
                $duplicates++;
            }
            $lead = ['company_id' => $get['company_id'], 'first_name' => $row['first_name'] ?? '', 'middle_name' => '', 'last_name' => $row['last_name'] ?? '', 'display_name' => $row['display_name'] ?? '', 'email_id' => $row['email'] ?? '', 'phone' => $phone, 'office_phone' => $officePhone, 'address' => $row['address'] ?? '', 'company_name' => $row['company_name'] ?? '', 'originator' => $get['user_id'], 'contact_source' => $files['source_type'] ?? '', 'image' => null, 'is_active' => '0', 'current_status' => '0', 'created_date' => date(CrmConstants::DATE_TIME_FORMAT), 'created_by' => $get['user_id']];
            $result = $callConfig->insertRecord('crm_leads', $lead);
            if (isset($result['LASTID']) && $result['LASTID'] > 0) {
                $inserted++;
            } else {
                $failed++;
            }
        }
        return ['status' => 'success','summary' => ['total_rows' => count($rows), 'inserted' => $inserted, 'duplicates' => $duplicates, 'failed' => $failed]];
    }
    public function getleadcontactsourcetype($get){
        global $callConfig;
        $return['get'] = $get;
        $query = "SELECT id, code, value, label FROM crm_general_codes WHERE company_id = '" . $get['company_id'] . "' AND code = 'SOURCETYPE' AND is_active = 0";
        $data = $callConfig->getAllRows($query);
        $return['crm_general_codes'] = $data;
        return $return;
    }

    // End feasibilty part By Kote - 25-06-2025

}

trait CrmFeasibilityAndCustomerTrait {
    private function loadFeasibilityEvaluationDetails($company_id, $feasibility_id, array &$return): void
    {
        global $callConfig;
        $whr = " feasibility_id = " . intval($feasibility_id) . " AND ";
        $query = "SELECT * FROM lead_feasibility_evaluations WHERE " . $whr . CrmConstants::COMPANY_ID_EQ . $company_id . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $return['feasibility_evaluation_res'] = $callConfig->getRow($query);

        $query = "SELECT * FROM lead_feasibility_boms WHERE " . $whr . CrmConstants::COMPANY_ID_EQ . $company_id . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $return['feasibility_bom_res'] = $callConfig->getAllRows($query);

        $query = "SELECT * FROM lead_feasibility_checklists WHERE " . $whr . CrmConstants::COMPANY_ID_EQ . $company_id . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $return['feasibility_checklist_res'] = $callConfig->getAllRows($query);
    }

    private function loadFeasibilityMasterLookups($company_id, array &$return): void
    {
        global $callConfig;
        $users_res = array();
        foreach ($callConfig->getAllRows("SELECT id,display_name FROM application_users WHERE " . CrmConstants::COMPANY_ID_EQ . $company_id . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE) as $row) {
            $users_res[$row->id] = $row->display_name;
        }
        $return['users_res'] = $users_res;

        $feasibility_types_res = array();
        foreach ($callConfig->getAllRows("SELECT id,type_name FROM feasibility_types WHERE company_id=" . $company_id . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE) as $row) {
            $feasibility_types_res[$row->id] = $row->type_name;
        }
        $return['feasibility_types_res'] = $feasibility_types_res;

        $uom_res = array();
        foreach ($callConfig->getAllRows("SELECT id,uom_name, uom_short_code FROM unit_of_measurements WHERE " . CrmConstants::COMPANY_ID_EQ . $company_id . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE) as $row) {
            $uom_res[$row->id] = $row;
        }
        $return['uom_res'] = $uom_res;

        $items_res = array();
        foreach ($callConfig->getAllRows("SELECT id,item_name, purchase_uom, purchase_price FROM items WHERE " . CrmConstants::COMPANY_ID_EQ . $company_id . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE) as $row) {
            $items_res[$row->id] = $row;
        }
        $return['items_res'] = $items_res;
    }

    public function getFeasiblityData($get){
        global $callConfig;
        $return = array();
        $id = $get['rid'] ?? '';
        $lead_id = $get['lead_id'] ?? '';
        $feasibility_id = $get['feasibility_id'] ?? '';

        if (empty($id) && !empty($lead_id)) {
            $query = "SELECT id FROM feasibility_requirements WHERE lead_id = {$lead_id} AND company_id = {$get['company_id']} AND is_active = 0 ORDER BY id DESC LIMIT 1";
            $return['feasibility_types_query'] = $query;
            $row = $callConfig->getRow($query);
            if (!empty($row)) {
                $id = $row->id;
            }
        }
        if (!empty($id)) {
            $query = "SELECT * FROM feasibility_requirements WHERE id = " . intval($id) . CrmConstants::AND_COMPANY_ID_EQ . $get['company_id'] . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $return['feasibility_requirement_res'] = $callConfig->getRow($query);
        }
        if (!empty($feasibility_id)) {
            $query = "SELECT * FROM feasibility_requirements WHERE id = " . intval($feasibility_id) . CrmConstants::AND_COMPANY_ID_EQ . $get['company_id'] . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
            $return['feasibility_requirement_res'] = $callConfig->getRow($query);
            $this->loadFeasibilityEvaluationDetails($get['company_id'], $feasibility_id, $return);
        }

        $this->loadFeasibilityMasterLookups($get['company_id'], $return);
        return $return;
    }
    private function renderFeasibilityFieldHtml(array $row): string
    {
        $label = $row['field_label'] ?? '';
        $code = $row['code'] ?? '';
        $type = $row['field_type'] ?? '';
        $length = $row['field_length'] ?? 'col-md-6';
        $values = json_decode($row['field_value'] ?? '[]', true) ?: array();
        $class = ($type === 'number') ? 'text-right' : '';
        $mandatory = isset($row['mandatory']) ? (int) $row['mandatory'] : 0;
        $star = ($mandatory === 0) ? " <span class='text-danger'>*</span>" : '';
        $requiredAttr = ($mandatory === 0) ? 'required' : '';
        $html = "<div class='$length mb-3'><label class='form-label'>$label $star </label>";

        if ($type === 'text' || $type === 'number' || $type === 'date') {
            $html .= "<input type='$type' name='$code' class='form-control $class' $requiredAttr>";
        } elseif ($type === 'select') {
            $html .= "<select name='$code' class='form-select' $requiredAttr>";
            foreach ($values as $v) {
                $html .= "<option value='$v'>$v</option>";
            }
            $html .= "</select>";
        } elseif ($type === 'radio') {
            $isFirst = true;
            foreach ($values as $v) {
                $required = ($mandatory === 0 && $isFirst) ? 'checked' : '';
                $html .= "<div class='form-check'><input class='form-check-input' type='radio' name='$code' value='$v' id='{$code}_$v' $required><label class='form-check-label' for='{$code}_$v'>$v</label></div>";
                $isFirst = false;
            }
        }
        $html .= "</div>";
        return $html;
    }

    public function getFeasibilityFields($get){
        global $callConfig;
        $html = "";
        if (!empty($get['rid'])) {
            $query = "SELECT * FROM feasibility_fields WHERE feasibility_type_id = " . intval($get['rid']) . CrmConstants::AND_COMPANY_ID_EQ . $get['company_id'] . CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
            foreach ($callConfig->getAllRows($query) as $row) {
                $html .= $this->renderFeasibilityFieldHtml((array) $row);
            }
        }
        return $html;
    }
    private function saveLeadFeasibilityBoms($get, $post, array &$return): void
    {
        global $callConfig;
        $feasibility_id = $post['feasibility_id'];
        $updated_date = date(CrmConstants::DATE_TIME_FORMAT);
        $updated_by = $get['user_id'];
        $item_name_arr = $post['item_name'] ?? array();
        $bom_row_id_arr = $post['bom_row_id'] ?? array();

        foreach ($item_name_arr as $i => $item_name) {
            if ($item_name == 'others') {
                $newItemName = $post['new_item_name'][$i] ?? '';
                if ($newItemName) {
                    $insert_item_array = array(
                        'company_id' => $get['company_id'],
                        'item_name' => $newItemName,
                        'purchase_uom' => $post['unit'][$i],
                        'sale_uom' => $post['unit'][$i],
                        'purchase_base_uom' => $post['unit'][$i],
                        'sale_base_uom' => $post['unit'][$i],
                        'purchase_price' => $post['cost'][$i],
                        'sale_price' => $post['cost'][$i],
                        'item_type' => 1,
                        'is_active' => 0,
                        'current_status' => 0,
                        'status_date' => date(CrmConstants::DATE_TIME_FORMAT),
                        'created_by' => $get['user_id'],
                        'created_date' => date(CrmConstants::DATE_TIME_FORMAT)
                    );
                    $insertItemResult = $callConfig->insertRecord('items', $insert_item_array);
                    $newItemId = $insertItemResult['LASTID'];
                } else {
                    $newItemId = 0;
                }
            } else {
                $newItemId = $item_name;
            }

            $bom_row_id = $bom_row_id_arr[$i] ?? null;
            if ($bom_row_id) {
                $update_array = array(
                    'item_id' => $newItemId,
                    'item_quantity' => $post['quantity'][$i],
                    'unit' => $post['unit'][$i],
                    'item_price' => $post['cost'][$i],
                    'total_amount' => $post['total_cost'][$i],
                    'planned_request_by' => $this->dateconvertdbformat($post['planned_request_by'][$i]),
                    'planned_return_by' => $this->dateconvertdbformat($post['planned_return_by'][$i]),
                    'actual_request_by' => $this->dateconvertdbformat($post['actual_request_by'][$i]),
                    'actual_return_by' => $this->dateconvertdbformat($post['actual_return_by'][$i]),
                    'updated_date' => $updated_date,
                    'updated_by' => $updated_by
                );
                $return['update_res'] = $callConfig->updateRecord('lead_feasibility_boms', $update_array, "id", $bom_row_id);
            } else {
                $insert_array = array(
                    'company_id' => $get['company_id'],
                    'feasibility_id' => $feasibility_id,
                    'item_id' => $newItemId,
                    'item_quantity' => $post['quantity'][$i],
                    'unit' => $post['unit'][$i],
                    'item_price' => $post['cost'][$i],
                    'total_amount' => $post['total_cost'][$i],
                    'planned_request_by' => $this->dateconvertdbformat($post['planned_request_by'][$i]),
                    'planned_return_by' => $this->dateconvertdbformat($post['planned_return_by'][$i]),
                    'actual_request_by' => $this->dateconvertdbformat($post['actual_request_by'][$i]),
                    'actual_return_by' => $this->dateconvertdbformat($post['actual_return_by'][$i]),
                    'is_active' => 0,
                    'current_status' => 0,
                    'status_date' => date(CrmConstants::DATE_TIME_FORMAT),
                    'created_date' => date(CrmConstants::DATE_TIME_FORMAT),
                    'created_by' => $get['user_id']
                );
                $return['insert_res'] = $callConfig->insertRecord('lead_feasibility_boms', $insert_array);
            }
        }
    }

    private function saveLeadFeasibilityChecklists($get, $post, array &$return): void
    {
        global $callConfig;
        $updated_date = date(CrmConstants::DATE_TIME_FORMAT);
        $updated_by = $get['user_id'];
        $checklist_item_name_arr = $post['checklist_item_name'] ?? array();
        $check_row_id_arr = $post['check_row_id'] ?? array();

        foreach ($checklist_item_name_arr as $i => $item_name) {
            $check_row_id = $check_row_id_arr[$i] ?? null;
            $update_array = array(
                'item_name' => $item_name,
                'item_type' => $post['checklist_type'][$i],
                'is_done' => $post['checklist_is_done'][$i],
                'comment' => $post['checklist_comment'][$i],
                'is_active' => 0,
                'current_status' => 0,
                'status_date' => date(CrmConstants::DATE_TIME_FORMAT),
                'updated_date' => $updated_date,
                'updated_by' => $updated_by
            );
            if ($check_row_id) {
                $return['update_res'] = $callConfig->updateRecord('lead_feasibility_checklists', $update_array, "id", $check_row_id);
            } else {
                $insert_array = array(
                    'company_id' => $get['company_id'],
                    'feasibility_id' => $post['feasibility_id'],
                    'item_name' => $item_name,
                    'item_type' => $post['checklist_type'][$i],
                    'is_done' => $post['checklist_is_done'][$i],
                    'comment' => $post['checklist_comment'][$i],
                    'is_active' => 0,
                    'current_status' => 0,
                    'status_date' => date(CrmConstants::DATE_TIME_FORMAT),
                    'created_date' => date(CrmConstants::DATE_TIME_FORMAT),
                    'created_by' => $get['user_id']
                );
                $return['insert_res'] = $callConfig->insertRecord('lead_feasibility_checklists', $insert_array);
            }
        }
    }

    public function insertFeasibilityBomAndChecklist($get, $post){
        $return = array();
        if (($post['table_name'] ?? '') == 'lead_feasibility_boms') {
            $this->saveLeadFeasibilityBoms($get, $post, $return);
        } elseif (($post['table_name'] ?? '') == 'lead_feasibility_checklists') {
            $this->saveLeadFeasibilityChecklists($get, $post, $return);
        }
        return $return;
    }
    public function getFeasibilityReport($get) {
        global $callConfig;
        $feasibility_id = $get['feasibility_id'];
        $company_id = $get['company_id'];
        $report = [];
        // === 1. Feasibility Requirement Section ===
        $reqQuery = "SELECT * FROM feasibility_requirements  WHERE id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $requirement = $callConfig->getRow($reqQuery);
        $report['feasibility_requirements'] = $requirement;

        $typeId = $requirement->feasibility_type_id ?? 0;
        $typeName = '';
        if ($typeId) {
            $typeQuery = "SELECT type_name FROM feasibility_types  WHERE id = $typeId AND company_id = $company_id AND is_active = 0";
            $res = $callConfig->getRow($typeQuery);
            $typeName = $res->type_name ??'';
        }
        $reqJson = json_decode($requirement->requirement_json ?? '{}', true);
        $fields = [];
        if (!empty($reqJson)) {
            $fieldQuery = "SELECT code, field_label FROM feasibility_fields WHERE feasibility_type_id = $typeId AND company_id = $company_id AND is_active = 0";
            $fieldMap = [];
            foreach ($callConfig->getAllRows($fieldQuery) as $row) {
                $fieldMap[$row->code] = $row->field_label;
            }
            foreach ($reqJson as $code => $value) {
                $label = $fieldMap[$code] ?? $code;
                $fields[$label] = $value;
            }
        }
        $report['requirement'] = ['feasibility_type' => $typeName,'fields' => $fields];
        // === 2. Evaluation Section ===
        $evalQuery = "SELECT * FROM lead_feasibility_evaluations WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['evaluation'] = $callConfig->getRow($evalQuery);

        // === 3. BOM Section ===
        $bomQuery = "SELECT * FROM lead_feasibility_boms  WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['bom'] = $callConfig->getAllRows($bomQuery);

        // === 4. Checklist Section ===
        $chkQuery = "SELECT * FROM lead_feasibility_checklists WHERE feasibility_id = $feasibility_id AND company_id = $company_id AND is_active = 0";
        $report['checklist'] = $callConfig->getAllRows($chkQuery);

        // === 5. Files Section ===
        $fileQuery = "SELECT actual_file_name, file_name FROM application_attachments  WHERE transaction_id = $feasibility_id AND company_id = $company_id  AND application_table_name = 'feasibility_requirements' AND is_active = 0";
        $report['files'] = $callConfig->getAllRows($fileQuery);

        // === 6. Unit Of measurement Section ===
        $chkQuery = "SELECT id, uom_name, uom_short_code FROM unit_of_measurements WHERE  company_id = $company_id AND is_active = 0";
        $query_res = $callConfig->getAllRows($chkQuery);
        $uom_res = array();
        foreach ($query_res as $row) {
            $uom_res[$row->id] = $row;
        }
        $report['uom_res'] = $uom_res;

        $chkQuery = "SELECT id, item_name FROM items WHERE  company_id = $company_id AND is_active = 0";
        $query_res = $callConfig->getAllRows($chkQuery);
        $items_res = array();
        foreach ($query_res as $row) {
            $items_res[$row->id] = $row->item_name;
        }
        $report['items_res'] = $items_res;
        return $report;
    }
    public function checkBOMReturns($get){
        global $callConfig;
        $return = array();
        $fid = $get['feasibility_id'];
        $query = "SELECT i.item_name FROM lead_feasibility_boms bom, items i WHERE bom.item_id = i.id AND feasibility_id = $fid AND (actual_return_by IS NULL OR actual_return_by = '')  AND bom.company_id=".$get['company_id']." AND  bom.is_active = 0 AND i.is_active =0";
        $rows = $callConfig->getAllRows($query);
        $items = array_map(fn($r) => $r->item_name, $rows);
        $return['unreturned_items'] = $items;
        return $return;
    }
    public function closeFeasibility($get){
        global $callConfig;
        $updated_date = date(CrmConstants::DATE_TIME_FORMAT);
        $updated_by = $get['user_id'];
        $fid = $get['feasibility_id'];
        $query = "UPDATE feasibility_requirements SET current_status = ".ST_APPROVED.", updated_date = '{$updated_date}', updated_by={$updated_by}  WHERE id = $fid";
        return $callConfig->executeQuery($query);

    }
    public function getFeasibilityDashboard($get){
        global $callConfig;
        $query = "SELECT
            fr.id AS feasibility_id,
            fr.lead_id AS lead_id,
            ft.type_name AS feasibility_type,
            b.id AS bom_id,
            b.item_name,b.planned_return_by,
            b.actual_return_by,
            crmcon.display_name,
            itm.item_name
        FROM feasibility_requirements fr
        LEFT JOIN feasibility_types ft  ON fr.feasibility_type_id = ft.id AND ft.company_id = " .$get['company_id']." AND  ft.is_active = 0
        LEFT JOIN lead_feasibility_boms b ON fr.id = b.feasibility_id AND b.company_id = " .$get['company_id']." AND  b.is_active = 0
        LEFT JOIN crm_contacts crmcon  ON fr.company_id  = crmcon.company_id AND fr.lead_id = crmcon.id AND  b.is_active = 0
        LEFT JOIN items itm  ON b.company_id  = itm.company_id AND b.item_id = itm.id AND  b.is_active = 0
        WHERE fr.current_status = ".ST_APPROVED."
        AND (b.actual_return_by IS NULL OR b.actual_return_by = '')
        AND fr.company_id = " .$get['company_id']." AND  fr.is_active = 0";
        return $callConfig->getAllRows($query);
    }
    public function updateReturnDate($get){
        global $callConfig;
        $updated_date = date(CrmConstants::DATE_TIME_FORMAT);
        $updated_by = $get['user_id'];
        $bom_id = $get['bom_id'] ?? '';
        $actual_return_by = $get['actual_return_by'] ?? '';
        $bom_id = intval($bom_id);
        $actual_return_by = $this->dateconvertdbformat($actual_return_by); // sanitize
        $update_sql = "UPDATE lead_feasibility_boms SET actual_return_by = '{$actual_return_by}', updated_date = '{$updated_date}', updated_by={$updated_by} WHERE id = $bom_id";
        return $callConfig->executeQuery($update_sql);
    }
    private function renderFeasibilityTableRow(array $row): string
    {
        $isSelectable = in_array($row['field_type'], ['select', 'radio']);
        $mandatory = isset($row['mandatory']) ? (int) $row['mandatory'] : 0;
        $mandatoryBtnClass = $mandatory ? 'btn-success' : 'btn-warning';
        $mandatoryBtnText = $mandatory ? 'Unmandatory' : 'Mandatory';
        $mandatoryBtnData = $mandatory ? 1 : 0;

        $lengthOptions = '';
        for ($i = 1; $i <= 12; $i++) {
            $val = "col-md-$i";
            $sel = ($row['field_length'] == $val) ? 'selected' : '';
            $lengthOptions .= "<option value='$val' $sel>$val</option>";
        }

        $typeOptions = '';
        foreach (['text' => 'Text', 'number' => 'Number', 'date' => 'Date', 'select' => 'Select', 'radio' => 'Radio'] as $optVal => $optLabel) {
            $sel = ($row['field_type'] == $optVal) ? 'selected' : '';
            $typeOptions .= "<option value='$optVal' $sel>$optLabel</option>";
        }

        return "<tr>
            <td width='20%'>
                <input type='text' class='form-control' name='field_label[]' value='" . htmlspecialchars($row['field_label']) . "'>
                <input type='hidden' name='code[]' value='" . htmlspecialchars($row['code']) . "'>
                <input type='hidden' name='is_active[]' value='" . htmlspecialchars($row['is_active']) . "'>
            </td>
            <td width='20%'><select class='form-select' name='field_type[]' onchange='handleFieldTypeChange(this);'>$typeOptions</select></td>
            <td width='20%'>
                <input type='text' class='form-control field-value-input' name='field_value[]' value='" . htmlspecialchars($row['field_value']) . "' " . ($isSelectable ? 'readonly' : '') . ">
                <button type='button' class='btn btn-sm btn-success mt-3 add-options-btn' style='" . ($isSelectable ? '' : 'display:none;') . " z-index:2;' onclick='openOptionsModal(this)'>Edit Options</button>
            </td>
            <td width='20%'><select class='form-select js-example-basic-single' name='field_length[]'>$lengthOptions</select></td>
            <td width='10%'>
                <button type='button' class='btn $mandatoryBtnClass btn-sm toggle-mandatory-btn' onclick='toggleMandatory(this)' data-mandatory='$mandatoryBtnData'>$mandatoryBtnText</button>
                <input type='hidden' name='mandatory[]' value='$mandatory'>
            </td>
            <td width='10%'><button type='button' class='btn btn-danger btn-sm' onclick='removeFieldRow(this)'><i class='fa fa-trash'></i></button></td>
        </tr>";
    }

    private function renderFeasibilityFieldPreview(array $row): string
    {
        $label = $row['field_label'] ?? '';
        $code = $row['code'] ?? '';
        $type = $row['field_type'] ?? '';
        $length = $row['field_length'] ?? 'col-md-6';
        $values = json_decode($row['field_value'] ?? '[]', true) ?: array();
        $class = ($type === 'number') ? 'text-right' : '';
        $mandatory = isset($row['mandatory']) ? (int) $row['mandatory'] : 0;
        $isRequired = ($mandatory === 0);
        $star = $isRequired ? " <span class='text-danger'>*</span>" : '';
        $requiredAttr = $isRequired ? 'required' : '';

        $html = "<div class='$length mb-3'><label class='form-label'>$label$star</label>";
        if (in_array($type, ['text', 'number', 'date'])) {
            $html .= "<input type='$type' name='$code' class='form-control $class' $requiredAttr>";
        } elseif ($type === 'select') {
            $html .= "<select name='$code' class='form-select' $requiredAttr>";
            foreach ($values as $v) {
                $html .= "<option value='$v'>$v</option>";
            }
            $html .= "</select>";
        } elseif ($type === 'radio') {
            $isFirst = true;
            foreach ($values as $v) {
                $required = ($mandatory === 0 && $isFirst) ? 'checked' : '';
                $html .= "<div class='form-check'><input class='form-check-input' type='radio' name='$code' value='$v' id='{$code}_$v' $required><label class='form-check-label' for='{$code}_$v'>$v</label></div>";
                $isFirst = false;
            }
        }
        $html .= "</div>";
        return $html;
    }

    public function getFeasibilityTableAndFields($get) {
        global $callConfig;
        $html = "";
        if (!empty($get['rid'])) {
            $query = "SELECT * FROM feasibility_fields WHERE feasibility_type_id = " . intval($get['rid']) . " AND company_id = " . intval($get['company_id']) . CrmConstants::AND_IS_ACTIVE_0;
            $data = $callConfig->getAllRows($query);

            if (!empty($data)) {
                $html .= "<div class='table-responsive mb-3'><table class='table table-striped' id='feasibilityFieldsTable'><thead><tr><th scope=\"col\" width='20%'>Label</th><th scope=\"col\" width='20%'>Type</th><th scope=\"col\" width='20%'>Values (JSON)</th><th scope=\"col\" width='20%'>Length (col)</th><th scope=\"col\" width='10%'>Mandatory</th><th width='0%'>Action</th></tr></thead><tbody>";
                foreach ($data as $row) {
                    $html .= $this->renderFeasibilityTableRow((array) $row);
                }
                $html .= "</tbody></table><div class='d-flex justify-content-between mt-3'><button type='button' class='btn btn-success mt-2' onclick='addNewFieldRow()'>+ Add New Field</button><button type='button' class='btn btn-success mt-2' onclick='saveCustomFields()'>Save Fields</button></div></div><div class='row' id='dynamic_preview'>";
                foreach ($data as $row) {
                    $html .= $this->renderFeasibilityFieldPreview((array) $row);
                }
                $html .= "</div>";
            } else {
                $html .= "<div class='alert alert-warning'>No fields found for this feasibility type.</div>";
            }
        }
        return $html;
    }
    public function insertFeasibilityfeildslist($get, $post) {
        global $callConfig;
        $return = array();

        $return['post'] = $post;

        $feasibility_id = $post['feasibility_id'];
        $company_id = $get['company_id'];
        $user_id = $get['user_id'];
        $updated_date = date(CrmConstants::DATE_TIME_FORMAT);

        $fields = json_decode($post['fields_data'], true);

        $lastCodeRow = $callConfig->getRow("SELECT code FROM feasibility_fields ORDER BY id DESC LIMIT 1");
        $lastCodeNumber = 0;
        if ($lastCodeRow && preg_match('/RQ(\d+)/', $lastCodeRow->code, $matches)) {
            $lastCodeNumber = (int) $matches[1];
        }

        $query = "UPDATE feasibility_fields SET is_active = null WHERE feasibility_type_id = {$feasibility_id}";
        $res = $callConfig->executeQuery($query);
        $return['update_res'] = $res;

        if (!empty($fields)) {
            foreach ($fields as $field) {
                $code = $field['code'];

                if (!empty($code)) {
                    $existingQuery = "SELECT id FROM feasibility_fields WHERE code = '{$code}' AND feasibility_type_id = {$feasibility_id}";
                    $existing = $callConfig->getRow($existingQuery);
                } else {
                    $existing = false;
                }

                $data = array(
                    'company_id' => $company_id,
                    'feasibility_type_id' => $feasibility_id,
                    'field_label' => $field['field_label'],
                    'field_type' => $field['field_type'],
                    'field_value' => $field['field_value'],
                    'mandatory' => $field['mandatory'],
                    'field_length' => $field['field_length'],
                    'is_active' => 0,
                );

                if ($existing) {
                    $data['code'] = $code;
                    $data['updated_by'] = $user_id;
                    $data['updated_date'] = $updated_date;
                    $response = $callConfig->updateRecord('feasibility_fields', $data, 'id', $existing->id);
                    $return['updaterecord'][] = $response;
                } else {
                    $lastCodeNumber++;
                    $data['code'] = 'RQ' . $lastCodeNumber;
                    $data['created_by'] = $user_id;
                    $data['created_date'] = $updated_date;
                    $callConfig->insertRecord('feasibility_fields', $data);
                    $return['insertrecord'][] = $data;
                }
            }
            $return['status'] = 'success';
        } else {
            $return['status'] = 'error';
            $return['message'] = 'No fields data found.';
        }

        return $return;
    }

    public function reOpenFeasibility($get){
        global $callConfig;
        $updated_date = date(CrmConstants::DATE_TIME_FORMAT);
        $updated_by = $get['user_id'];
        $feasibility_id = $get['feasibility_id'] ?? '';
        $update_sql = "UPDATE feasibility_requirements SET current_status = " . ST_ACTIVE .", updated_date = '{$updated_date}', updated_by={$updated_by} WHERE id = $feasibility_id";
        return $callConfig->executeQuery($update_sql);
    }

    // End feasibilty part By Kote - 25-06-2025



    public function createSalesOrdersWithFeasibilty($get){
        global $callConfig;
        $return = array();
        $feasibility_id = $get['feasibility_id'];
        $crm_contact_id = $get['crm_contact_id'];
        $frsql = "SELECT * FROM feasibility_requirements WHERE company_id = '".$get['company_id'].CrmConstants::AND_ID_EQ.$feasibility_id." AND sales_order_id IS NULL AND is_active = 0";
        $frres = $callConfig->getRow($frsql);
        $return['feasibility_requirements'] = $frres;

        $crm_contactssql = "SELECT * FROM crm_contacts WHERE company_id = '".$get['company_id'].CrmConstants::AND_ID_EQ.$crm_contact_id.CrmConstants::AND_IS_ACTIVE_0;
        $crm_contactsres = $callConfig->getRow($crm_contactssql);
        $return['crm_contacts'] = $crm_contactsres;

        if($crm_contactsres->crm_company_id !=""){
            $crm_companiessql = "SELECT * FROM crm_companies WHERE company_id = '".$get['company_id'].CrmConstants::AND_ID_EQ.$crm_contactsres->crm_company_id.CrmConstants::AND_IS_ACTIVE_0;
            $crm_companiesres = $callConfig->getRow($crm_companiessql);
            $return['crm_companies'] = $crm_companiesres;
            if($crm_companiesres->customer_id !=""){
                $customer_id = $crm_companiesres->customer_id;
            }else{
                //to create customer
                $insertcust = $this->createCustomerWithContactLeadDetails($crm_contactsres, $crm_contact_id, $crm_contactsres->crm_company_id, $get);
                $return['insert_customers'] = $insertcust;
                $customer_id = $insertcust['customers']['LASTID'];
                //to create customer
            }
        }else{
            $insertcust = $this->createCustomerWithContactLeadDetails($crm_contactsres, $crm_contact_id,'',$get);
            $return['insert_customers'] = $insertcust;
            $customer_id = $insertcust['customers']['LASTID'];
        }
        $customersql = "SELECT * FROM customers WHERE company_id = '".$get['company_id'].CrmConstants::AND_ID_APOS.$customer_id.CrmConstants::AND_IS_ACTIVE_0_APOS;
        $customerres = $callConfig->getRow($customersql);
        $return['customers'] = $customerres;


        $lfbsql = "SELECT * FROM lead_feasibility_boms WHERE company_id = '".$get['company_id']."' AND feasibility_id = '".$feasibility_id.CrmConstants::AND_IS_ACTIVE_0_APOS;
        $lfbres = $callConfig->getAllRows($lfbsql);
        $return['lead_feasibility_boms'] = $lfbres;
        $total_amount = 0;
        $itemsid = $itemsdetail = array();
        foreach($lfbres as $item_data){
            $itemsid[$item_data->item_id] = $item_data->item_id;
            $total_amount += $item_data->total_amount;
        }

        if(count((array)$itemsid) !="0"){
            $itemssql = "SELECT id, item_name FROM items WHERE company_id = '".$get['company_id']."' AND id IN(".implode(',',$itemsid).") AND is_active = 0";
            $itemsres = $callConfig->getAllRows($itemssql);
            foreach($itemsres as $ires){
                $itemsdetail[$ires->id] = $ires->item_name;
            }
        }

        $transaction_number = $this->toGetApplicationSerialNumber('sales_orders');
        $currency_id = 93;
        $cost_center_id = 1;
        $transaction_date = $due_date = date("Y-m-d");
        $fieldnames=array('company_id'=>$get['company_id'],'transaction_number'=>$transaction_number,'transaction_date'=>$transaction_date,'currency_id'=>$currency_id,'currency_rate'=>1,'originator'=>$get['user_id'],'party_id'=>$customerres->id,'party_name'=>$customerres->company,'party_email'=>$customerres->email,'party_phone'=>$customerres->phone,'party_address'=>$customerres->billing_address,'party_ship_adderss'=>$customerres->shipping_address,'place_of_supply'=>'','payment_terms'=>$customerres->payment_terms,'amount'=>$total_amount,'tax_amount'=>0,'total_amount'=>$total_amount,'ref_type'=>'crm_contacts','ref_id'=>$crm_contact_id,'cost_center_id'=>$cost_center_id,'current_status'=>0,'created_by'=>$get['user_id'],'created_date'=>date(CrmConstants::DATE_TIME_FORMAT),'originator_currency_id'=>$currency_id,'originator_state_id'=>$customerres->shipping_state,'supplier_state_id'=>$customerres->shipping_state,'due_date'=>$due_date, 'pending_amount'=>$total_amount,'is_active'=>'0');
        $insert_data=$callConfig->insertRecord('sales_orders',$fieldnames);
        $return['main'] = $insert_data;
        if($insert_data['status'] == 'sucess'){
            $insert_id = $insert_data['LASTID'];
            foreach($lfbres as $item_data){
                $itemfieldnames=array('company_id'=>$item_data->company_id,'transaction_id'=>$insert_id,'item_id'=>$item_data->item_id,'item_name'=>$itemsdetail[$item_data->item_id],'description'=>'','purchase_uom'=>$item_data->unit,'item_quantity'=>$item_data->item_quantity,'item_price'=>$item_data->item_price,'item_amount'=>$item_data->total_amount,'tax_amount'=>0,'discount_amount'=>0,'total_amount'=>$item_data->total_amount,'pending_qty'=>$item_data->item_quantity,'transaction_qty'=>$item_data->item_quantity,'created_by'=>$get['user_id'],'created_date'=>date(CrmConstants::DATE_TIME_FORMAT),'is_active'=>'0');
                $item_id=$callConfig->insertRecord('so_items',$itemfieldnames);
                $return['child'][] = $item_id;
            }

            $insertary1['updated_date']=date(CrmConstants::DATE_TIME_FORMAT);
            $insertary1['updated_by']=$get['user_id'];
            $insertary1['sales_order_id']=$insert_id;
            $callConfig->updateRecord("feasibility_requirements",$insertary1,'id',$feasibility_id);
        }
        return $return;
    }
    public function createCustomerWithContactLeadDetails($crm_contactsres, $crm_lead_contact_id, $crm_company_id, $get){
        global $callConfig;
        $return = array();
        $post = array();
        $crecustom = "INSERT INTO customers(company_id, first_name, middle_name, last_name, display_name, email, phone, mobile, fax, website_url, billing_address, shipping_address,billing_post_code, shipping_post_code, billing_country, shipping_country, billing_state, shipping_state, billing_city, shipping_city,  company, is_active, current_Status, Created_By, Created_Date, image, pan_no)";
        if($crm_company_id !=""){
            $crecustom .="SELECT ".$get['company_id'].",cc.first_name, cc.middle_name, cc.last_name, cc.display_name, cc.email_id, cc.phone, cc.office_phone, cc.fax, ccomp.website_url, ccomp.address, ccomp.address, ccomp.post_code, ccomp.post_code, ccomp.country_id, ccomp.country_id, ccomp.state_id, ccomp.state_id, ccomp.city_id, ccomp.city_id, ccomp.company_name ,0,0,'".$get['user_id']."','".date(CrmConstants::DATE_TIME_FORMAT)."', ccomp.image, ccomp.pan_no FROM crm_contacts cc, crm_companies ccomp WHERE cc.id = '".$crm_lead_contact_id."' AND ccomp.id = '".$crm_company_id."' AND cc.is_active = ccomp.is_active";
        }else{ // crm contact
            $crecustom .="SELECT ".$get['company_id'].", first_name, middle_name, last_name, display_name, email_id, phone, office_phone, fax, website, address, address, post_code, post_code, country_id, country_id, state_id, state_id, city_id, city_id, company_name , 0, 0, ".$get['user_id'].", '".date(CrmConstants::DATE_TIME_FORMAT)."','','' FROM crm_contacts WHERE id = ".$crm_lead_contact_id;

        }
        $res = $callConfig->executeQuery($crecustom);
        if($res['status'] == 'sucess'){
            if($crm_company_id !=""){
                $insertary['updated_date']=date(CrmConstants::DATE_TIME_FORMAT);
                $insertary['updated_by']=$get['user_id'];
                $insertary['customer_id']=$res['LASTID'];
                $callConfig->updateRecord("crm_companies",$insertary,'id',$crm_company_id);
            }else{
                $insertary=array('company_id'=>$get['company_id'], 'default_crm_contact_id'=>$crm_lead_contact_id, 'company_name'=>$crm_contactsres->company_name, 'customer_id'=> $res['LASTID'], 'website_url'=>$crm_contactsres->website, 'address'=>$crm_contactsres->address, 'post_code'=>$crm_contactsres->post_code, 'country_id'=>$crm_contactsres->country_id, 'state_id'=>$crm_contactsres->state_id, 'city_id'=>$crm_contactsres->city_id, 'is_active'=>'0', 'current_status'=>'0','created_by'=>$post['user_id'], 'created_date'=>date(CrmConstants::DATE_TIME_FORMAT));
                $crmleaddetails = $callConfig->insertRecord('crm_companies',$insertary);

                $insertary1 = array();
                $insertary1['updated_date']=date(CrmConstants::DATE_TIME_FORMAT);
                $insertary1['updated_by']=$get['user_id'];
                $insertary1['crm_company_id']=$crmleaddetails['LASTID'];
                $callConfig->updateRecord("crm_contacts",$insertary1,'id',$crm_lead_contact_id);
            }
        }
        $return['crecustom'] = $crecustom;
        $return['customers'] = $res;
        return $return;
     }

    public function checkCompanyNameAvilableOrNotInCustomer($get, $post){
        global $callConfig;
        $company_name = $post['company_name'];
        $customerssql = "SELECT id FROM customers WHERE company_id = ".$get['company_id']." AND company LIKE '%".$company_name."%' AND is_active = 0";
        $customersres=$callConfig->getRow($customerssql);
        $return['customers'] = $customersres;
        return $return;
    }

    // start Data comparision by koti
    private function getDatabaseTables($db): array
    {
        $tables = [];
        $result = $db->query("SHOW TABLES");
        if ($result) {
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
        }
        return $tables;
    }

    private function getDatabaseTableFields($db, $table): array
    {
        $fields = [];
        $result = $db->query("SHOW COLUMNS FROM `$table`");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $fields[$row['Field']] = [
                    'Type' => $row['Type'],
                    'Null' => $row['Null'],
                    'Default' => $row['Default'],
                    'Extra' => $row['Extra']
                ];
            }
        }
        return $fields;
    }

    private function compareTableColumns($db2, $table, $dbName2, array $fields1, array $fields2, array &$report): void
    {
        $allFields = array_unique(array_merge(array_keys($fields1), array_keys($fields2)));
        foreach ($allFields as $field) {
            $in1 = array_key_exists($field, $fields1);
            $in2 = array_key_exists($field, $fields2);

            if (!$in2 && $in1) {
                $f = $fields1[$field];
                $null = ($f['Null'] === 'NO') ? "NOT NULL" : "NULL";
                $default = ($f['Default'] !== null) ? "DEFAULT '" . $f['Default'] . "'" : "";
                $extra = $f['Extra'];
                $columnBefore = null;
                $columnOrder = array_keys($fields2);
                $index = array_search('is_active', $columnOrder);
                if ($index !== false && $index > 0) {
                    $columnBefore = $columnOrder[$index - 1];
                }
                $sql = $columnBefore
                    ? "ALTER TABLE `$table` ADD COLUMN `$field` {$f['Type']} $null $default $extra AFTER `$columnBefore`;"
                    : "ALTER TABLE `$table` ADD COLUMN `$field` {$f['Type']} $null $default $extra;";
                $result = $db2->query($sql);
                $report['actions'][] = ['direction' => 'to_db2:' . $dbName2, 'type' => 'add_column', 'query' => $sql, 'result' => $result];
            } elseif ($in1 && $in2 && $fields1[$field]['Type'] !== $fields2[$field]['Type']) {
                $f1 = $fields1[$field];
                $null1 = ($f1['Null'] === 'NO') ? "NOT NULL" : "NULL";
                $default1 = ($f1['Default'] !== null) ? "DEFAULT '" . $f1['Default'] . "'" : "";
                $extra1 = $f1['Extra'];
                $sql1 = "ALTER TABLE `$table` MODIFY COLUMN `$field` {$f1['Type']} $null1 $default1 $extra1;";
                $result = $db2->query($sql1);
                $report['actions'][] = ['direction' => 'to_db2:' . $dbName2, 'type' => 'modify_column', 'query' => $sql1, 'result' => $result];
            }
        }
    }

    public function compareDatabaseSchemas($get) {
        $dbName1 = $get['db1'];
        $dbName2 = $get['db2'];
        $db1 = $this->connectToDatabaseByName($dbName1);
        $db2 = $this->connectToDatabaseByName($dbName2);

        $tables1 = $this->getDatabaseTables($db1);
        $tables2 = $this->getDatabaseTables($db2);
        $allTables = array_unique(array_merge($tables1, $tables2));
        $output = [];

        foreach ($allTables as $table) {
            $report = ['table' => $table, 'status' => [], 'actions' => []];
            $existsInDb1 = in_array($table, $tables1);
            $existsInDb2 = in_array($table, $tables2);

            if (!$existsInDb2 && $existsInDb1) {
                $report['status'][] = 'missing_in_db2:' . $dbName2;
                $row = $db1->query("SHOW CREATE TABLE `$table`")->fetch_assoc();
                $sql = ($row['Create Table'] ?? '') . ";";
                $result = $db2->query($sql);
                $report['actions'][] = ['direction' => 'to_db2', 'type' => 'create_table', 'query' => $sql, 'result' => $result];
            } elseif ($existsInDb1 && $existsInDb2) {
                $report['status'][] = 'exists_in_both';
                $fields1 = $this->getDatabaseTableFields($db1, $table);
                $fields2 = $this->getDatabaseTableFields($db2, $table);
                $this->compareTableColumns($db2, $table, $dbName2, $fields1, $fields2, $report);
            }

            if (!empty($report['status']) || !empty($report['actions'])) {
                $output[] = $report;
            }
        }
        echo "<pre>";
        print_r($output);
        echo "</pre>";
    }

    public function connectToDatabaseByName($dbName) {
        $host = HOSTNAME;
        $user = USERNAME;
        $pass = PASSWORD;
        $mysqli = new mysqli($host, $user, $pass, $dbName);
        if ($mysqli->connect_error) {
            die("Connection failed to DB [$dbName]: " . $mysqli->connect_error);
        }
        return $mysqli;
    }
    // end Data comparision by koti



    private function buildSerialDynamicParts($asnres, $get, $partytable, $party_id, $transaction_type, array $checksequincerow, array &$parts, array &$insertwithconditionary): string
    {
        global $callConfig;
        $additional_conditions = "";
        foreach ($checksequincerow as $seq_key => $seq_val) {
            if ($seq_val == "PARTY_NAME") {
                if ($partytable != "" && $party_id != "") {
                    $partysql = "SELECT id, short_name FROM " . $partytable . " WHERE company_id = " . $get['company_id'] . " AND id = " . $party_id . CrmConstants::AND_IS_ACTIVE_0;
                    $partyres = $callConfig->getRow($partysql);
                    $val = !empty($partyres->short_name) ? $partyres->short_name : $seq_val;
                } else {
                    $val = $seq_val;
                }
            } elseif ($seq_val == "TRANSACTION_TYPE") {
                $val = ($transaction_type != "") ? $transaction_type : $seq_val;
            } elseif ($seq_val == "TRANSACTION_LOCATION") {
                $locationsql = "SELECT prty.id, loc.short_name FROM " . $partytable . " as prty, locations as loc WHERE prty.company_id = " . $get['company_id'] . " AND prty.company_id = loc.company_id AND prty.location_id = loc.id AND prty.is_active = loc.is_active AND prty.id = " . $party_id . CrmConstants::AND_IS_ACTIVE_0;
                $locationres = $callConfig->getRow($locationsql);
                $val = !empty($locationres->short_name) ? $locationres->short_name : $seq_val;
            } else {
                $val = $seq_val;
            }
            $additional_conditions .= CrmConstants::AND_POSITION . $seq_key . " = '" . $val . "'";
            $insertwithconditionary["position_" . $seq_key] = $val;
            $parts[$seq_key] = $val;
        }
        return $additional_conditions;
    }

    private function resolveResetSerialNumber($get, $asnres, $reset_type, $from_date, $to_date, $additional_conditions, array $insertwithconditionary, $numLength, array &$return): string
    {
        global $callConfig;
        $sql = CrmConstants::SEL_APP_SERIAL_MASTER_DETAILS . $get['company_id'] . CrmConstants::AND_APP_SERIAL_MASTER_ID . $asnres->id . "' AND serial_number_reset_type = '{$reset_type}' AND from_date <= '{$from_date}' AND to_date >= '{$to_date}'" . CrmConstants::AND_IS_ACTIVE_0 . $additional_conditions;
        $row = $callConfig->getRow($sql);
        if (!empty($row->id)) {
            $serial_number = $row->serial_number + 1;
            $callConfig->updateRecord('application_serial_number_master_details', array('serial_number' => $serial_number), 'id', $row->id);
        } else {
            $serial_number = 1;
            $insertary = array('company_id' => $get['company_id'], 'application_serial_number_master_id' => $asnres->id, 'serial_number_reset_type' => $reset_type, 'from_date' => $from_date, 'to_date' => $to_date, 'serial_number' => 1, 'created_by' => $get['user_id'], 'created_date' => date(CrmConstants::DATE_TIME_FORMAT), 'current_status' => 0, 'is_active' => 0);
            foreach ($insertwithconditionary as $k => $v) {
                $insertary[$k] = $v;
            }
            $return['res'] = $callConfig->insertRecord('application_serial_number_master_details', $insertary);
        }
        return str_pad((string) $serial_number, $numLength, '0', STR_PAD_LEFT);
    }

    public function generatingApplicationSerialNumberMastersTst($transaction_table, $get){
        global $callConfig;
        $return = array();
        $transaction_date = $get['transaction_date'] ?? date('Y-m-d');
        $transdate = !empty($transaction_date) ? $this->dateconvertdbformat($transaction_date) : date('Y-m-d');
        $party_id = $get['party_id'] ?? '';
        $transaction_type = $get['transaction_type'] ?? '';

        $asnsql = "SELECT * FROM application_serial_number_masters WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $transaction_table . CrmConstants::AND_IS_ACTIVE_0_APOS;
        $asnres = $callConfig->getRow($asnsql);
        $main_transaction_no_type = '';
        $monthly_transaction_number = '';
        $yearly_transaction_number = '';
        $lifetime_transaction_number = '';

        if (!empty($asnres->id)) {
            $main_transaction_no_type = $asnres->reset_type;
            $serial_number_position = $asnres->serial_number_position;
            $financial_year_position = $asnres->financial_year_position;
            $numLength = !empty($asnres->doc_no_length) ? (int) $asnres->doc_no_length : 3;

            $display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $transaction_table . "' AND field_name = 'party_id'";
            $parttable_name = $callConfig->getRow($display_attribute_typessql);
            $explosql = explode("FROM", urldecode($parttable_name->content ?? ''));
            $partytable = isset($explosql[1]) ? trim(explode(" ", trim($explosql[1]))[0]) : '';

            $finyaer = date("m", strtotime($transdate)) >= 4 ? date("Y", strtotime($transdate)) . '-' . (date("y", strtotime($transdate)) + 1) : (date("Y", strtotime($transdate)) - 1) . '-' . date("y", strtotime($transdate));
            $additional_settings = json_decode($asnres->additional_settings, 1) ?: array();
            $position_with_val = array();
            foreach ($additional_settings as $adsetting) {
                $position_with_val[$adsetting['positions']] = $adsetting;
            }

            $parts = [];
            $checksequincerow = array();
            for ($m = 1; $m <= 10; $m++) {
                if (!empty($asnres->serial_number) && $serial_number_position == $m) {
                    $parts[$m] = '';
                }
                if (!empty($finyaer) && $asnres->financial_year_check == "Y" && $financial_year_position == $m) {
                    $parts[$m] = $finyaer;
                }
                if (isset($position_with_val[$m])) {
                    $p = $position_with_val[$m];
                    if ($p['display_type'] == "STATIC" && $p['static_value'] != "") {
                        $checksequincerow[$m] = $p['static_value'];
                    } elseif ($p['display_type'] == "DYNAMIC" && $p['dynamic_value'] != "") {
                        $checksequincerow[$m] = $p['dynamic_value'];
                    }
                }
            }

            $insertwithconditionary = array();
            $additional_conditions = $this->buildSerialDynamicParts($asnres, $get, $partytable, $party_id, $transaction_type, $checksequincerow, $parts, $insertwithconditionary);
            ksort($parts);

            // MT
            $m_from = date("Y-m-01", strtotime($transdate));
            $m_to = date("Y-m-t", strtotime($transdate));
            $parts[$serial_number_position] = $this->resolveResetSerialNumber($get, $asnres, 'MT', $m_from, $m_to, $additional_conditions, $insertwithconditionary, $numLength, $return);
            $parts[$financial_year_position] = date("M-Y", strtotime($transdate));
            $monthly_transaction_number = trim(implode($asnres->separator_symbols, (array) $parts));

            // YR
            $mNum = (int) date("m", strtotime($transdate));
            $yrStart = ($mNum >= 4) ? date("Y", strtotime($transdate)) : (date("Y", strtotime($transdate)) - 1);
            $yrEnd = ($mNum >= 4) ? (date("Y", strtotime($transdate)) + 1) : date("Y", strtotime($transdate));
            $y_from = $yrStart . "-04-01";
            $y_to = $yrEnd . "-03-31";
            $parts[$serial_number_position] = $this->resolveResetSerialNumber($get, $asnres, 'YR', $y_from, $y_to, $additional_conditions, $insertwithconditionary, $numLength, $return);
            $parts[$financial_year_position] = date("Y", strtotime($y_from)) . "-" . date("y", strtotime($y_to));
            $yearly_transaction_number = trim(implode($asnres->separator_symbols, (array) $parts));

            // LT
            $parts[$serial_number_position] = $this->resolveResetSerialNumber($get, $asnres, 'LT', '2020-04-01', '3020-03-31', $additional_conditions, $insertwithconditionary, $numLength, $return);
            $lifetime_transaction_number = trim(implode($asnres->separator_symbols, (array) $parts));
        }

        $main_transaction_number = random_int(1, PHP_INT_MAX);
        $monthly_transaction_number = !empty($monthly_transaction_number) ? $monthly_transaction_number : random_int(1, PHP_INT_MAX);
        $yearly_transaction_number = !empty($yearly_transaction_number) ? $yearly_transaction_number : random_int(1, PHP_INT_MAX);
        $lifetime_transaction_number = !empty($lifetime_transaction_number) ? $lifetime_transaction_number : random_int(1, PHP_INT_MAX);
        if ($main_transaction_no_type == "MT") {
            $main_transaction_number = $monthly_transaction_number;
        } elseif ($main_transaction_no_type == "YR") {
            $main_transaction_number = $yearly_transaction_number;
        } elseif ($main_transaction_no_type == "LT") {
            $main_transaction_number = $lifetime_transaction_number;
        }
        return array('main_transaction_number' => $main_transaction_number, 'monthly_transaction_number' => $monthly_transaction_number, 'yearly_transaction_number' => $yearly_transaction_number, 'lifetime_transaction_number' => $lifetime_transaction_number);
    }
    public function getCRMAIDashboards($get){
        global $callConfig;
        $originatercondition = '';
        $crm_leadssql = "SELECT id, type_name, type, created_date, created_by FROM crm_activity_types WHERE company_id = '".$get['company_id']."' ".$originatercondition.CrmConstants::AND_IS_ACTIVE_0;
        $crm_leadsres=$callConfig->getAllRows($crm_leadssql);
        $return['crm_activity_types'] = $crm_leadsres;

        $crm_leadssql = "SELECT id, originator, campaign_name, crm_campaign_type, campaign_start_date, campaign_end_date, total_budget, created_date FROM crm_campaigns WHERE company_id = '".$get['company_id']."' ".$originatercondition.CrmConstants::AND_IS_ACTIVE_0;
        $crm_leadsres=$callConfig->getAllRows($crm_leadssql);
        $return['crm_campaigns'] = $crm_leadsres;

        $crm_leadssql = "SELECT id, originator, company_name, default_crm_contact_id, created_date FROM crm_companies WHERE company_id = '".$get['company_id']."' ".$originatercondition.CrmConstants::AND_IS_ACTIVE_0;
        $crm_leadsres=$callConfig->getAllRows($crm_leadssql);
        $return['crm_companies'] = $crm_leadsres;

        $crm_leadssql = "SELECT id, originator, display_name, post_code, country_id, state_id,city_id, created_date FROM crm_contacts WHERE company_id = '".$get['company_id']."' ".$originatercondition.CrmConstants::AND_IS_ACTIVE_0;
        $crm_leadsres=$callConfig->getAllRows($crm_leadssql);
        $return['crm_contacts'] = $crm_leadsres;

        $crm_leadssql = "SELECT id, originator, deal_name, crm_company_id, crm_contact_id, due_date, expected_close_date, originator, crm_status, created_date FROM crm_deals WHERE company_id = '".$get['company_id']."' ".$originatercondition.CrmConstants::AND_IS_ACTIVE_0;
        $crm_leadsres=$callConfig->getAllRows($crm_leadssql);
        $return['crm_deals'] = $crm_leadsres;

        $crm_leadssql = "SELECT id, transaction_table, `transaction_id`, `lead_type`, `date`, `time`, `address`, `country_id`, `state_id`, `city_id`, `post_code`, `phone`, `sms_text`, `email_content_id`, `recipient_email_id`, `member`, `comment`, `originator`, `section_id`, `division_id`, `job_grade_id`, `completion_datetime`, `completion_note`, created_date, created_by FROM crm_histories WHERE company_id = '".$get['company_id']."' ".$originatercondition.CrmConstants::AND_IS_ACTIVE_0;
        $crm_leadsres=$callConfig->getAllRows($crm_leadssql);
        $return['crm_histories'] = $crm_leadsres;

        $crm_leadssql = "SELECT id, display_name, post_code, country_id, state_id,city_id, created_date, created_by FROM crm_leads WHERE company_id = '".$get['company_id']."' ".$originatercondition.CrmConstants::AND_IS_ACTIVE_0;
        $crm_leadsres=$callConfig->getAllRows($crm_leadssql);
        $return['crm_leads'] = $crm_leadsres;

        $project_task_statussql = "SELECT id, table_name, name, status, action_label, created_date, created_by FROM project_task_status WHERE company_id = '".$get['company_id'].CrmConstants::AND_IS_ACTIVE_0_APOS;
        $project_task_statusres = $callConfig->getAllRows($project_task_statussql);
        $return['project_task_status'] = $project_task_statusres;

        $crm_general_codessql = "SELECT id, code, value, label, created_date, created_by FROM crm_general_codes WHERE company_id = '".$get['company_id'].CrmConstants::AND_IS_ACTIVE_0_APOS;
        $crm_general_codesres=$callConfig->getAllRows($crm_general_codessql);
        foreach($crm_general_codesres as $crmcode){
            $crm_general_codes[$crmcode->code][] =$crmcode;
        }
        $return['crm_general_codes'] = $crm_general_codes;

        $query = "SELECT id, display_name, created_date, created_by FROM application_users WHERE  company_id=".$get['company_id'].CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $data = $callConfig->getAllRows($query);
         $return['users_res'] = $data;

        return $return;

    }


}

class CrmUserClass extends userClass {
    use CrmSalesAndImportsTrait;
    use CrmFeasibilityAndCustomerTrait;

    public const DATE_FORMAT_HIS = CrmConstants::DATE_TIME_FORMAT;
    public const DATE_FORMAT_LOWER_HIS = "Y-m-d h:i:s";
    public const SQL_AND_IS_ACTIVE_0 = CrmConstants::AND_IS_ACTIVE_0;
    public const SQL_AND_IS_ACTIVE_0_NOSPACE = CrmConstants::AND_IS_ACTIVE_0_NO_SPACE;
    public const SQL_QUOTE_AND_IS_ACTIVE_0 = CrmConstants::AND_IS_ACTIVE_0_APOS;
    public const SQL_AND_IS_ACTIVE_0_GROUP_STATUS = " AND is_active = 0 GROUP BY crm_status";
    public const SQL_WHERE_COMPANY_ID_EQ_QUOTE = " WHERE company_id = '";
    public const SQL_SEL_CRM_CONTACTS = "SELECT * FROM crm_contacts WHERE company_id = '";
    public const SQL_SEL_CRM_DEALS = "SELECT * FROM crm_deals WHERE company_id = '";
    public const SQL_SEL_CRM_COMPANIES = "SELECT id, company_name FROM crm_companies WHERE company_id = '";
    public const SQL_AND_ORIGINATOR_EQ = " AND originator = '";
    public const SQL_ZERO_TIME_AND = " 00:00:00' AND '";
    public const SQL_QUOTE_AND_ID_IN = "' AND id IN(";
    public const SQL_QUOTE_AND_ID_EQ = CrmConstants::AND_ID_APOS;
    public const SQL_QUOTE_AND_ID_EQ_NUM = CrmConstants::AND_ID_EQ;
    public const SQL_QUOTE_AND_CRM_COMPANY_ID = "' AND crm_company_id = '";
    public const SQL_AND_POSITION_PREFIX = CrmConstants::AND_POSITION;
    public const SQL_AND_COMPANY_ID_EQ = CrmConstants::AND_COMPANY_ID_EQ;
    public const SQL_SPACE_COMPANY_ID_EQ = CrmConstants::COMPANY_ID_EQ;
    public const SQL_QUOTE_AND_APP_SERIAL_ID = CrmConstants::AND_APP_SERIAL_MASTER_ID;
    public const SQL_SEL_APP_SERIAL_MASTER_DETAILS = CrmConstants::SEL_APP_SERIAL_MASTER_DETAILS;


    private function loadCRMDashboardData($get, $originatercondition, $prooriginatercondition, $feasibiltycondition, array &$return): void
    {
        global $callConfig;
        $todaydate = date("Y-m-d");
        $datecondition = " AND created_date BETWEEN '" . $todaydate . self::SQL_ZERO_TIME_AND . $todaydate . " 23:59:59'";
        $othercondition = self::SQL_WHERE_COMPANY_ID_EQ_QUOTE . $get['company_id'] . self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $crm_leadssql = "SELECT (SELECT COUNT(id) as cnt FROM crm_leads " . $othercondition . $datecondition . $originatercondition . ") as today_crm_lead_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts " . $othercondition . $datecondition . $originatercondition . ") as today_crm_contacts_cnt, (SELECT COUNT(id) as cnt FROM crm_deals " . $othercondition . $datecondition . $originatercondition . ") as today_crm_deals_cnt, (SELECT COUNT(id) as cnt FROM crm_leads " . $othercondition . $originatercondition . ") as crm_lead_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts " . $othercondition . $originatercondition . ") as crm_contacts_cnt, (SELECT COUNT(id) as cnt FROM crm_companies " . $othercondition . $originatercondition . ") as crm_companies_cnt, (SELECT COUNT(id) as cnt FROM crm_campaigns " . $othercondition . $originatercondition . ") as crm_campaigns_cnt, (SELECT COUNT(id) as cnt FROM crm_deals " . $othercondition . $originatercondition . ") as crm_deals_cnt, (SELECT COUNT(id) as cnt FROM projects " . $othercondition . $prooriginatercondition . ") as projects_cnt";
        $return['dashboard_sql'] = $crm_leadssql;
        $return['dashboard_cnt'] = $callConfig->getRow($crm_leadssql);

        $return['crm_leads'] = $callConfig->getAllRows("SELECT * FROM crm_leads WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . " ORDER BY created_date DESC limit 5");
        $return['crm_leads_graph'] = $callConfig->getAllRows("SELECT count(id) as cnt, crm_status FROM crm_leads WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . self::SQL_AND_IS_ACTIVE_0_GROUP_STATUS);

        $return['crm_contacts'] = $callConfig->getAllRows(self::SQL_SEL_CRM_CONTACTS . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . " ORDER BY created_date DESC limit 5");
        $return['crm_contacts_graph'] = $callConfig->getAllRows("SELECT count(id) as cnt, crm_status FROM crm_contacts WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . self::SQL_AND_IS_ACTIVE_0_GROUP_STATUS);

        $return['crm_deals'] = $callConfig->getAllRows(self::SQL_SEL_CRM_DEALS . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . " ORDER BY created_date DESC limit 5");
        $return['crm_deals_graph'] = $callConfig->getAllRows("SELECT count(id) as cnt, crm_status FROM crm_deals WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . self::SQL_AND_IS_ACTIVE_0_GROUP_STATUS);

        $crm_companies = array();
        foreach ($callConfig->getAllRows(self::SQL_SEL_CRM_COMPANIES . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0) as $crmlead) {
            $crm_companies[$crmlead->id] = $crmlead->company_name;
        }
        $return['crm_companies_id_name'] = $crm_companies;
        $return['crm_activity_types'] = $callConfig->getAllRows("SELECT id,type_name,type, icon FROM crm_activity_types WHERE company_id = '" . $get['company_id'] . self::SQL_QUOTE_AND_IS_ACTIVE_0);

        $crm_lead_detailsres = $callConfig->getAllRows("SELECT id, transaction_table, transaction_id, lead_type, date, time, address, country_id, state_id, city_id, post_code, phone, sms_text, email_content_id, recipient_email_id, member, comment, created_date FROM crm_histories WHERE company_id = '" . $get['company_id'] . "' AND lead_type IN(2,3,6,7,8) AND(originator = '" . $get['user_id'] . "' OR JSON_SEARCH(member, 'one', '" . $get['user_id'] . "') IS NOT NULL) AND current_status = 0 AND is_active = 0");
        $return['crm_pendding_tasks'] = $crm_lead_detailsres;

        $tasktabd = array();
        foreach ($crm_lead_detailsres as $hist) {
            $tasktabd[$hist->transaction_table][$hist->transaction_id] = $hist->transaction_id;
        }
        foreach ($tasktabd as $transtable => $ids) {
            $crm_leadssql = "SELECT id,display_name,phone FROM " . $transtable . self::SQL_WHERE_COMPANY_ID_EQ_QUOTE . $get['company_id'] . self::SQL_QUOTE_AND_ID_IN . implode(',', (array) $ids) . ") " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0;
            foreach ($callConfig->getAllRows($crm_leadssql) as $crmdet) {
                $return['crm_pendding_task_details'][$transtable][$crmdet->id] = $crmdet;
            }
        }

        $return['feasibility_requirements'] = $callConfig->getAllRows("SELECT fr.id as feasibility_id, cc.id as crm_contact_id, cc.display_name, cc.email_id, cc.phone, cc.company_name, cc.crm_company_id FROM feasibility_requirements fr, crm_contacts cc WHERE fr.company_id = '" . $get['company_id'] . "' AND fr.company_id = cc.company_id AND fr.lead_id = cc.id AND fr.is_active = cc.is_active AND fr.is_active = 0 AND fr.sales_order_id IS NULL AND fr.current_status = '" . ST_APPROVED . "'" . $feasibiltycondition);
        $return['feasibility_requirements_expiry_days'] = $callConfig->getAllRows("SELECT fr.id AS feasibility_id, fr.current_status, fr.lead_id AS lead_id, ft.type_name AS feasibility_type, fe.end_date AS evaluation_end_date, crmcon.display_name FROM feasibility_requirements fr LEFT JOIN feasibility_types ft ON fr.feasibility_type_id = ft.id AND ft.company_id = " . $get['company_id'] . " AND ft.is_active = 0 LEFT JOIN lead_feasibility_evaluations fe ON fr.id = fe.feasibility_id AND fe.company_id = " . $get['company_id'] . " AND fe.is_active = 0 LEFT JOIN crm_contacts crmcon ON fr.company_id = crmcon.company_id AND fr.lead_id = crmcon.id WHERE fr.company_id = " . $get['company_id'] . " AND fr.current_status != " . ST_APPROVED . " AND fr.is_active = 0 ");
    }

    private function loadCRMListData($get, $originatercondition, array &$return): void
    {
        global $callConfig;
        $from_date = $this->dateconvertdbformat($get['from_date']);
        $to_date = $this->dateconvertdbformat($get['to_date']);
        $datecondition = " AND created_date BETWEEN '" . $from_date . self::SQL_ZERO_TIME_AND . $to_date . " 23:59:59'";
        $tableList = $get['transaction_table_list'] ?? '';

        if ($tableList == "crm_leads") {
            $return['crm_leads'] = $callConfig->getAllRows("SELECT * FROM crm_leads WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . $datecondition);
            $return['crm_leads_graph'] = $callConfig->getAllRows("SELECT count(id) as cnt, crm_status, GROUP_CONCAT(CONCAT_WS('|', id,display_name, phone,image) SEPARATOR ',') AS group_values FROM crm_leads WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . " GROUP BY crm_status");
        } elseif ($tableList == "crm_contacts") {
            $return['crm_contacts'] = $callConfig->getAllRows("SELECT * FROM crm_contacts WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . $datecondition);
            $return['crm_contacts_graph'] = $callConfig->getAllRows("SELECT count(id) as cnt, crm_status, GROUP_CONCAT(CONCAT_WS('|', id,display_name, phone,image) SEPARATOR ',') AS group_values FROM crm_contacts WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . " GROUP BY crm_status");
        } elseif ($tableList == "crm_deals") {
            $return['crm_deals'] = $callConfig->getAllRows("SELECT * FROM crm_deals WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . $datecondition);
            $return['crm_deals_graph'] = $callConfig->getAllRows("SELECT count(id) as cnt, crm_status, GROUP_CONCAT(CONCAT_WS('|', id,deal_name, total_budget,image) SEPARATOR ',') AS group_values FROM crm_deals WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . " GROUP BY crm_status");
        } elseif ($tableList == "crm_companies") {
            $return['crm_companies'] = $callConfig->getAllRows("SELECT * FROM crm_companies WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . $datecondition);
        } elseif ($tableList == "crm_campaigns") {
            $return['crm_campaigns'] = $callConfig->getAllRows("SELECT * FROM crm_campaigns WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0 . $datecondition);
        }
    }

    private function loadCRMSingleLeadData($get, $originatercondition, array &$return): void
    {
        global $callConfig;
        $return['crm_leads'] = $callConfig->getAllRows(self::SQL_SEL_CRM_LEADS . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0);
        $return['crm_contacts'] = $callConfig->getAllRows(self::SQL_SEL_CRM_CONTACTS . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0);
        $return['crm_companies'] = $callConfig->getAllRows(self::SQL_SEL_CRM_COMPANIES . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0);
        $return['crm_campaigns'] = $callConfig->getAllRows(self::SQL_SEL_CRM_CAMPAIGNS . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0);
        $return['crm_deals'] = $callConfig->getAllRows(self::SQL_SEL_CRM_DEALS . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0);
        $return['projects'] = $callConfig->getAllRows("SELECT id, project_name, current_status FROM projects WHERE company_id = '" . $get['company_id'] . "' " . $originatercondition . CrmConstants::AND_IS_ACTIVE_0);

        if (!empty($get['rid'])) {
            $return['crm_leads_details'] = $callConfig->getRow("SELECT * FROM " . $get['transaction_table'] . " WHERE company_id = '" . $get['company_id'] . CrmConstants::AND_ID_APOS . $get['rid'] . CrmConstants::AND_IS_ACTIVE_0_APOS);
            if ($get['transaction_table'] == "crm_companies") {
                $customersql = "SELECT * FROM customers WHERE company_id = '" . $get['company_id'] . CrmConstants::AND_ID_APOS . ($return['crm_leads_details']->customer_id ?? 0) . CrmConstants::AND_IS_ACTIVE_0_APOS;
                $return['customers'] = $callConfig->getRow($customersql);
            }
            if ($get['transaction_table'] == "projects") {
                $return['project_teams'] = $callConfig->getAllRows("SELECT id, user_id FROM `project_teams` WHERE company_id = '" . $get['company_id'] . "' AND project_id = '" . $get['rid'] . self::SQL_QUOTE_AND_IS_ACTIVE_0);
            }
        }
    }

    public function getCRMLeads($get){
        $return = array('get' => $get);
        $originatercondition = $prooriginatercondition = $feasibiltycondition = "";
        if (($get['admin_type'] ?? 0) != 1) {
            $originatercondition = self::SQL_AND_ORIGINATOR_EQ . $get['user_id'] . "'";
            $prooriginatercondition = " AND assigner_id = '" . $get['user_id'] . "'";
            $feasibiltycondition = " AND fr.created_by = '" . $get['user_id'] . "'";
        }

        if (($get['dashboard'] ?? '') == "Y") {
            $this->loadCRMDashboardData($get, $originatercondition, $prooriginatercondition, $feasibiltycondition, $return);
        } elseif (($get['list'] ?? '') == "Y") {
            $this->loadCRMListData($get, $originatercondition, $return);
        } else {
            $this->loadCRMSingleLeadData($get, $originatercondition, $return);
        }

        return $return;
    }
    private function buildCrmEntityInsertArray($transaction_table, $post, array &$return): array
    {
        global $callConfig;
        $display_name = $post['first_name'] . " " . $post['last_name'];
        $insertary = array('company_id' => $post['company_id'], 'first_name' => $post['first_name'], 'middle_name' => $post['middle_name'], 'last_name' => $post['last_name'], 'display_name' => $display_name, 'phone' => $post['phone'], 'office_phone' => $post['office_phone'], 'fax' => $post['fax'], 'website' => $post['website'], 'address' => $post['address'], 'post_code' => $post['post_code'], 'country_id' => $post['country_id'], 'state_id' => $post['state_id'], 'city_id' => $post['city_id'], 'originator' => $post['originator'], 'contact_source' => $post['contact_source'], 'remark' => $post['remark'], 'email_id' => $post['email_id'], 'image' => $post['image']);

        if ($transaction_table == "crm_leads") {
            $insertary['company_name'] = $post['company_name'];
            $insertary['crm_status'] = $post['crm_status'];
            $insertary['crm_campaign_id'] = $post['crm_campaign_id'];
        } elseif ($transaction_table == "crm_contacts") {
            $insertary['crm_company_id'] = $post['crm_company_id'];
            $insertary['crm_campaign_id'] = $post['crm_campaign_id'];
            $insertary['feasibility_check'] = ($post['feasibility_check'] == "Y") ? "Y" : "N";
        } elseif ($transaction_table == "crm_companies") {
            $insertary = array('company_id' => $post['company_id'], 'company_name' => $post['company_name'], 'website_url' => $post['website_url'], 'pan_no' => $post['pan_no'], 'gstin' => $post['gstin'], 'fax' => $post['fax'], 'address' => $post['address'], 'post_code' => $post['post_code'], 'country_id' => $post['country_id'], 'state_id' => $post['state_id'], 'city_id' => $post['city_id'], 'originator' => $post['originator'], 'remark' => $post['remark'], 'image' => $post['image']);
            $insertary1 = array('company_id' => $post['company_id'], 'display_name' => $post['company_name'], 'company' => $post['company_name'], 'website_url' => $post['website_url'], 'pan_no' => $post['pan_no'], 'billing_address' => $post['address'], 'shipping_address' => $post['address'], 'billing_post_code' => $post['post_code'], 'shipping_post_code' => $post['post_code'], 'billing_country' => $post['country_id'], 'shipping_country' => $post['country_id'], 'billing_state' => $post['state_id'], 'shipping_state' => $post['state_id'], 'billing_city' => $post['city_id'], 'shipping_city' => $post['city_id'], 'image' => $post['image'], 'updated_by' => $post['user_id'], 'updated_date' => date(self::DATE_FORMAT_HIS), 'gstin' => $post['gstin'], 'fax' => $post['fax']);
            if ($post['crm_customer_id'] != "") {
                $return['customer'] = $callConfig->updateRecord('customers', $insertary1, 'id', $post['crm_customer_id']);
            } else {
                $customerinsert = $callConfig->insertRecord('customers', $insertary1);
                $return['customer'] = $customerinsert;
                $insertary['customer_id'] = $customerinsert['LASTID'];
            }
        } elseif ($transaction_table == "crm_campaigns") {
            $insertary = array('company_id' => $post['company_id'], 'campaign_name' => $post['campaign_name'], 'crm_campaign_type' => $post['crm_campaign_type'], 'campaign_objective' => $post['campaign_objective'], 'campaign_start_date' => $this->dateconvertdbformat($post['campaign_start_date']), 'campaign_end_date' => $this->dateconvertdbformat($post['campaign_end_date']), 'total_budget' => $post['total_budget'], 'redirect_url' => $post['redirect_url'], 'contact_email_id' => $post['contact_email_id'], 'contact_no' => $post['contact_no'], 'originator' => $post['originator'], 'remark' => $post['remark'], 'image' => $post['image']);
        } elseif ($transaction_table == "crm_deals") {
            $insertary = array('company_id' => $post['company_id'], 'deal_name' => $post['deal_name'], 'source_type' => $post['source_type'], 'crm_company_id' => $post['crm_company_id'], 'crm_contact_id' => $post['crm_contact_id'], 'total_budget' => $post['total_budget'], 'due_date' => $this->dateconvertdbformat($post['due_date']), 'expected_close_date' => $this->dateconvertdbformat($post['expected_close_date']), 'originator' => $post['originator'], 'remark' => $post['remark'], 'image' => $post['image']);
        } elseif ($transaction_table == "projects") {
            $insertary = array('company_id' => $post['company_id'], 'project_name' => $post['project_name'], 'customer_id' => $post['customer_id'], 'project_cost' => $post['project_cost'], 'planned_start' => $this->dateconvertdbformat($post['planned_start']), 'planned_finish' => $this->dateconvertdbformat($post['planned_finish']), 'assigner_id' => $post['originator'], 'description' => $post['description'], 'project_logo' => $post['image']);
        }
        return $insertary;
    }

    private function syncProjectTeams($get, $post, $project_id, $isUpdate, array &$return): void
    {
        global $callConfig;
        if ($isUpdate) {
            $projctteamres = $callConfig->getAllRows("SELECT id, user_id FROM `project_teams` WHERE company_id = '" . $get['company_id'] . "' AND project_id = '" . $project_id . self::SQL_QUOTE_AND_IS_ACTIVE_0);
            $project_teamsuserid = array();
            foreach ($projctteamres as $prteam) {
                $project_teamsuserid[$prteam->user_id] = $prteam->id;
            }
            $callConfig->updateRecord('project_teams', array('is_active' => null), 'id', $project_id);
            if (!empty($post['project_team_user_id']) && is_array($post['project_team_user_id'])) {
                foreach ($post['project_team_user_id'] as $userid) {
                    $insertary1 = array('company_id' => $post['company_id'], 'project_id' => $project_id, 'user_id' => $userid, 'is_active' => '0', 'created_by' => $post['user_id'], 'created_date' => date(self::DATE_FORMAT_HIS));
                    if (!empty($project_teamsuserid[$userid])) {
                        $return['project_teams_up'][] = $callConfig->updateRecord('project_teams', $insertary1, 'id', $project_teamsuserid[$userid]);
                    } else {
                        $return['project_teams'][] = $callConfig->insertRecord('project_teams', $insertary1);
                    }
                }
            }
        } elseif (!empty($post['project_team_user_id']) && is_array($post['project_team_user_id'])) {
            foreach ($post['project_team_user_id'] as $userid) {
                $insertary1 = array('company_id' => $post['company_id'], 'project_id' => $project_id, 'user_id' => $userid, 'is_active' => '0', 'created_by' => $post['user_id'], 'created_date' => date(self::DATE_FORMAT_HIS));
                $return['project_teams'][] = $callConfig->insertRecord('project_teams', $insertary1);
            }
        }
    }

    private function handleContactFeasibilitySync($post, $contact_id, array &$return): void
    {
        global $callConfig;
        if (($post['feasibility_check'] ?? '') != "Y") {
            return;
        }
        $lead_id = $contact_id;
        $feasibility_id = $post['feasibility_id'] ?? '';
        $feasibility_type_id = $post['feasibility_type_id'] ?? '';
        $company_id = $post['company_id'];
        $user_id = $post['user_id'];
        $feasibility_types_query = "SELECT * FROM feasibility_types WHERE company_id = {$company_id} AND id = {$feasibility_type_id} AND is_active = 0";
        $feasibility_types_res = $callConfig->getRow($feasibility_types_query);

        if (!empty($feasibility_types_res)) {
            $checklist = $feasibility_types_res->checklist_id;
            $checklist_query = "SELECT * FROM feasibility_checklists WHERE company_id = {$company_id} AND id IN ($checklist) AND is_active = 0";
            $checklist_res = $callConfig->getAllRows($checklist_query);
            foreach ($checklist_res as $item) {
                $insert_array = array('company_id' => $company_id, 'feasibility_id' => $feasibility_id, 'item_name' => $item->checklist_name, 'item_type' => $item->type, 'is_done' => 0, 'comment' => '', 'is_active' => 0, 'current_status' => 0, 'status_date' => date(CrmConstants::DATE_TIME_FORMAT), 'created_date' => date(CrmConstants::DATE_TIME_FORMAT), 'created_by' => $user_id);
                $return['insert_res'][] = $callConfig->insertRecord('lead_feasibility_checklists', $insert_array);
            }
        }
    }

    public function insertCRMLeads($get,$post){
        global $callConfig;
        $return = array('post' => $post);
        $transaction_table = $post['transaction_table'];
        $insertary = $this->buildCrmEntityInsertArray($transaction_table, $post, $return);

        if (!empty($post['rid'])) {
            $insertary['updated_date'] = date(self::DATE_FORMAT_LOWER_HIS);
            $insertary['updated_by'] = $post['user_id'];
            $return['res'] = $callConfig->updateRecord($transaction_table, $insertary, 'id', $post['rid']);
            $lastid = $post['rid'];
            if ($transaction_table == "projects") {
                $this->syncProjectTeams($get, $post, $lastid, true, $return);
            }
        } else {
            $return['insertary---'] = $insertary;
            $insertary['created_by'] = $post['user_id'];
            $insertary['created_date'] = date(self::DATE_FORMAT_HIS);
            $insertary['is_active'] = '0';
            $insertary['current_status'] = '0';
            $tapres = $callConfig->insertRecord($transaction_table, $insertary);
            $return['res'] = $tapres;
            $lastid = $tapres['LASTID'] ?? 0;
            if (($tapres['status'] ?? '') == 'sucess' && $transaction_table == "projects") {
                $this->syncProjectTeams($get, $post, $lastid, false, $return);
            }
        }

        if ($transaction_table == "crm_contacts") {
            $this->handleContactFeasibilitySync($post, $lastid, $return);
        }

        $crmleaddetails = $callConfig->getRow("SELECT * FROM " . $transaction_table . " WHERE company_id = '" . $post['company_id'] . CrmConstants::AND_ID_APOS . $lastid . CrmConstants::AND_IS_ACTIVE_0_APOS);
        $return['res'] = $crmleaddetails;
        return $return;
    }
    public function dynamicUpdateStatus($get,$post){
        global $callConfig;
        $return = array('get' => $get, 'post' => $post);
        $updateary['updated_date']=date(self::DATE_FORMAT_LOWER_HIS);
        $updateary['updated_by']=$post['user_id'];
        $updateary['current_status'] = $post['current_status'];
        $return['res'] = $callConfig->updateRecord($post['transaction_table'],$updateary,'id',$post['rid']);
        return $return;
    }



//Start Email New Code

    public function emailsendmediaternew($senddata){
        global $callConfig;
        $senddata['super_admin_email_id'] = SUPERADMIN_EMAIL_ID;
        $senddata['super_admin_company_name'] = SUPERADMIN_COMPNAY_NAME;
        $senddata['super_admin_display_name'] = SUPERADMIN_DISLAY_NAME;
        $adminsersql = "SELECT au.id, au.display_name, au.user_email, au.user_mobile, comp.name FROM application_users au, companies comp WHERE au.company_id = '".$senddata['session_company_id']."' AND au.company_id = comp.id AND au.role_id LIKE '".'%"'.ADMIN_ROLES.'"%'."'";
        $adminserres = $callConfig->getRow($adminsersql);
        $senddata['admin_email_sql'] = $adminsersql;
        $senddata['admin_email_id'] = $adminserres->user_email;
        $senddata['admin_company_name'] = $adminserres->name;
        $senddata['company_name'] = $adminserres->name;
        $senddata['admin_display_name'] = $adminserres->display_name;
        $senddata['admin_phone_number'] = $adminserres->user_mobile;

        $email_send_types = array('TO'=>'to_users','CC'=>'cc_users');
        $query = "SELECT id,user_email FROM application_users";
        $emaildata = $callConfig->getAllRows($query);
        foreach($emaildata as $emaild){
            $useremail[$emaild->id] = $emaild->user_email;
        }
        $ress = array();
        foreach($email_send_types as $key=>$email_send_type){
            $revicer = array();
            $senddata['email_send_type'] =$key;
            $email_array = $this->emailtemplatenew($senddata);
            $file_name = 'email'.'_'.time().uniqid().'.'.'html';
            $email_array ['content'] = $file_name;
            if(count((array)$senddata[$email_send_type]) !='0'){
                foreach($senddata[$email_send_type] as $email_send){
                    $revicer[] = $useremail[$email_send];
                }
                $email_array['revicer'] = (count((array)$senddata['recipient_email_id'])!='0')?$senddata['recipient_email_id']:$revicer;
                $email_array['email_type'] = 1;
                $email = $email_array['mesg'];
                $file_path='uploads/email/'.$file_name;
                file_put_contents($file_path, $email);
                $this->sendEmailslistnew($email_array);
                $ress[] = $email_array;
            }
        }
        return $ress;
    }
    public function sendEmailslistnew($data){
        global $callConfig;
        $email_data = array('email_source'=>$data['sender'], 'content'=>$data['content'], 'email_type'=>$data['email_type'], 'is_active'=>0 , 'current_status'=>0,'email_content_ref_id'=>$data['email_content_ref_id'], 'created_by'=>$data['created_by'] ,'created_date'=> date(self::DATE_FORMAT_HIS));
        foreach ($data['revicer'] as $email) {
            $email_data['email'] = $email;
            $callConfig->insertRecord(TBL_EMAIL_LOGS,$email_data);
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // More headers
            $sender = $data['sender']!="" ? $data['sender']:SUPERADMIN_EMAIL_ID;
            $headers .= 'From:'.$sender . "\r\n";
            $subject = ucwords(str_replace('_', ' ', $data['subject']));
            mail($email,$subject,$data['mesg'],$headers);
        }
        return $email_data;
    }
    public function emailtemplatenew($send_data){
        global $callConfig;
        $temp = "";
        $configsql = "SELECT name,contact_address,application_module,admin_email,display_logo FROM config WHERE id = 1";
        $return['config'] = $configsql;
        $configres=$callConfig->getRow($configsql);
        $send_data['company_name'] = $configres->name!=""?$configres->name:SUPERADMIN_COMPNAY_NAME;
        $send_data['contact_address'] = $configres->contact_address ;
        if($send_data['email_send_type'] == "TO"){
            $emailcontentsql = "SELECT email_subject_to as email_subject,email_header_to as email_header,email_content_to as email_content_1,button_label, email_footer  as email_fotter FROM email_content WHERE company_id = '".$send_data['session_company_id']."' AND  template_reference_id = '".$send_data['template_reference_id'] ."' AND is_active = 0 ";
        }else{
            $emailcontentsql = "SELECT email_subject_cc as email_subject,email_header_cc as email_header,email_content_cc as email_content_1,button_label,email_footer  as email_fotter FROM email_content WHERE company_id = '".$send_data['session_company_id']."' AND  template_reference_id = '".$send_data['template_reference_id'] .self::SQL_QUOTE_AND_IS_ACTIVE_0;
        }
        $return['emailcontentsql'] = $emailcontentsql;
        $emailcontentres=$callConfig->getRow($emailcontentsql);
        $subject =  $this->emaildatareplace($send_data, $emailcontentres->email_subject);

        $temp = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
        </head>
        <body yahoo bgcolor="#f6f8f1">
        <table width="70%" style="padding:20px;background-color:#f5f5f5;" align="center" cellpadding="0" cellspacing="0" border="0">
        <tr>
        <td style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424;padding:15px 0px;">
        <img class="fix" src="'.SITEURL.'/uploads/all/'.$configres->display_logo.'" border="0" alt="" style="width:150px"/>
        </td>
        </tr>
        <tr>
        <td>
        <table width="100%" style="padding:10px;background-color:#FFFFFF;"  >
        <tr>
        <td style="padding-left:32px; padding-top:20px; font-size:14px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424" >
        '.$this->emaildatareplace($send_data, $emailcontentres->email_header).'
        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:13px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
        '.$this->emaildatareplace($send_data, $emailcontentres->email_sub_header).'
        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:13px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
        <div>'.$this->emaildatareplace($send_data, $emailcontentres->email_content_1).' </div>

        </td>
        </tr>
        <tr>
        <td>
        &nbsp;
        </td>
        </tr>
        <tr>
        <td>
        &nbsp;
        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:13px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
        '.$this->emaildatareplace($send_data, $emailcontentres->email_content_2).'
        </td>
        </tr>
        <td style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
        '.$this->emaildatareplace($send_data, $emailcontentres->email_fotter).'

        </td>
        </tr>
        </table>
        </body>
        </html>
        ';
        $return['subject'] = $subject;
        $return['mesg'] = $temp;
        $return['sender'] =  $configres->admin_email;
        return $return;
    }

//End Email New Code

    public function setStateCity($get){
        global $callConfig;
        $return = array('get' => $get);
        $citiessql = "SELECT id,city_name FROM cities";
        $citiesres=$callConfig->getAllRows($citiessql);
        foreach($citiesres as $citt){
            $cityNames[$citt->id] = $citt->city_name;
        }

        $state_namesql = "SELECT id,state_name FROM states";
        $state_nameres=$callConfig->getAllRows($state_namesql);
        foreach($state_nameres as $citt){
            $stateNames[$citt->id] = $citt->state_name;
        }

        $customersssql = "SELECT id,billing_address FROM customers WHERE billing_address !=''";
        $customerssres=$callConfig->getAllRows($customersssql);
        foreach($customerssres as $cust){
            $customers[$cust->id]['billing_address'] = $cust->billing_address;

            //Statrt Get Post Code
            preg_match('/\b\d{6}\b/', $cust->billing_address, $postalCodeMatch);
            $postalCode = $postalCodeMatch[0] ?? null;
            $customers[$cust->id]['post_code'] = $postalCode;
            //End Get Post Code




            $updateary = array('billing_city'=>'','billing_state'=>'','billing_post_code'=>$postalCode,'billing_country'=>'101','shipping_city'=>'','shipping_state'=>'','shipping_post_code'=>$postalCode,'shipping_country'=>'101');
            $return['res'] = $callConfig->updateRecord('customers',$updateary,'id',$cust->id);
        }
        echo "<pre>";
        print_r($return);

    }
    public function leadConvertToContacts($get,$post){
        global $callConfig;
        $transaction_id = $post['transaction_id'];
        $exicutesql = "INSERT INTO `crm_contacts`(`crm_lead_id`,`company_name`,`company_id`, `first_name`, `middle_name`, `last_name`, `display_name`, `email_id`, `phone`, `office_phone`, `fax`, `website`, `address`, `post_code`, `country_id`, `state_id`, `city_id`, `originator`, `contact_source`, `is_active`, `current_status`, `created_date`, `created_by`,`image`) SELECT `id`,`company_name`,'".$post['company_id']."', `first_name`, `middle_name`, `last_name`, `display_name`, `email_id`, `phone`, `office_phone`, `fax`, `website`, `address`, `post_code`, `country_id`, `state_id`, `city_id`, `originator`, `contact_source`, 0, 0,'".date(self::DATE_FORMAT_HIS)."', '".$post['user_id']."',`image` FROM `crm_leads` WHERE id='".$transaction_id."'";
        $res = $callConfig->executeQuery($exicutesql);
        if($res['status'] == 'sucess'){
            $insertary['updated_date']=date(self::DATE_FORMAT_LOWER_HIS);
            $insertary['updated_by']=$post['user_id'];
            $insertary['lead_convert_to_contact']="Y";
            $insertary['crm_contact_id']=$transaction_id;
            $callConfig->updateRecord("crm_leads",$insertary,'id',$transaction_id);
        }
        $res['get'] = $get;
        return $res;

    }
    public function getLeadAndContactAssigned($get){
        global $callConfig;
        if($get['admin_type'] != 1){
            $originatercondition = " AND crml.originator = '".$get['user_id']."'";
            $originatercondition2 = self::SQL_AND_ORIGINATOR_EQ.$get['user_id']."'";
        }

        $application_userssql = "SELECT id,display_name,user_photo FROM application_users WHERE company_id = '".$get['company_id']."' AND is_active = 0  AND JSON_SEARCH(role_id, 'one', 'CRM_USER') IS NOT NULL";
        $application_usersres=$callConfig->getAllRows($application_userssql);


         $crm_historiessql = "SELECT crml.access_user_id, COUNT(DISTINCT crml.id) AS lead_total_cnt, COUNT(DISTINCT crmh.transaction_id) AS lead_used_cnt FROM crm_leads crml LEFT JOIN crm_histories crmh ON crmh.company_id = crml.company_id AND crmh.transaction_table = 'crm_leads' AND crmh.transaction_id = crml.id AND crmh.is_active = crml.is_active WHERE crml.company_id = '".$get['company_id']."' ".$originatercondition." AND crml.is_active = 0 GROUP BY crml.access_user_id";
         $return['crm_historiessql'] = $crm_historiessql;
         $crm_historiesres=$callConfig->getAllRows($crm_historiessql);
         foreach($crm_historiesres as $aus){
             $crm_histories_lead['crm_leads'][$aus->access_user_id] =$aus;
         }

         $crm_contactssql = "SELECT crml.access_user_id, COUNT(DISTINCT crml.id) AS lead_total_cnt, COUNT(DISTINCT crmh.transaction_id) AS lead_used_cnt FROM crm_contacts crml LEFT JOIN crm_histories crmh ON crmh.company_id = crml.company_id AND crmh.transaction_table = 'crm_contacts' AND crmh.transaction_id = crml.id AND crmh.is_active = crml.is_active WHERE crml.company_id = '".$get['company_id']."' ".$originatercondition." AND crml.is_active = 0 GROUP BY crml.access_user_id";
         $return['crm_contactssql'] = $crm_contactssql;
         $crm_contactsres=$callConfig->getAllRows($crm_contactssql);
         foreach($crm_contactsres as $aus){
             $crm_histories_lead['crm_contacts'][$aus->access_user_id] =$aus;
         }
         $return['crm_histories_lead'] = $crm_histories_lead;

         $application_users = array();
         foreach($application_usersres as $aus){
             $aus->leads = $crm_histories_lead['crm_leads'][$aus->id];
             $aus->contacts = $crm_histories_lead['crm_contacts'][$aus->id];
             $application_users[$aus->id] = $aus;
         }
        $return['application_users'] = $application_users;

        $othercondition  =self::SQL_WHERE_COMPANY_ID_EQ_QUOTE.$get['company_id']."' ".$originatercondition2.self::SQL_AND_IS_ACTIVE_0;
        $crm_leadssql = "SELECT (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition.") as crm_lead_total_cnt, (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition." AND access_user_id IS NOT NULL) as crm_lead_assigne_cnt, (SELECT COUNT(id) as cnt FROM crm_leads ".$othercondition." AND access_user_id IS NULL) as crm_lead_un_assigne_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition.") as crm_contacts_total_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition." AND access_user_id IS NOT NULL) as crm_contacts_assigne_cnt, (SELECT COUNT(id) as cnt FROM crm_contacts ".$othercondition." AND access_user_id IS NULL) as crm_contacts_un_assigne_cnt";
        $crm_leadsres=$callConfig->getRow($crm_leadssql);
        $return['dashboard_sql'] = $crm_leadssql;
        $return['dashboard_cnt'] = $crm_leadsres;

        $crm_dealssql = "SELECT id, project_name, (SELECT COUNT(pt.id) as cnt FROM project_teams pt WHERE pt.company_id = proj.company_id AND pt.project_id = proj.id AND pt.is_active = proj.is_active) as team_user_cnt FROM projects proj WHERE company_id = '".$get['company_id'].self::SQL_QUOTE_AND_IS_ACTIVE_0;
        $crm_dealsres=$callConfig->getAllRows($crm_dealssql);
        $return['crm_projects'] = $crm_dealsres;

        return $return;
    }
    public function leadAndContactAssigning($get,$post){
        global $callConfig;
        $return['post'] = $post;
        $assign_lead_contact = $post['assign_lead_contact'];
        if($get['admin_type'] != 1){
            $originatercondition = self::SQL_AND_ORIGINATOR_EQ.$get['user_id']."'";
        }
        $user_id = array();
        if($post['assign_type'] =="2"){ //project
            $sql = "SELECT user_id FROM project_teams WHERE company_id ='".$post['company_id']."' AND project_id ='".$post['project_id']."'  AND is_active = 0";
            $resul=$callConfig->getAllRows($sql);
            $projuid = array();
            foreach($resul as $resultt){
                $projuid[] = $resultt->user_id;
            }
            $return['projuid'] = $projuid;
            $contactCount = $post['number_of_lead_contact'];
            $employeeCount = count((array)$projuid);
            $contactsPerEmployee = intdiv($contactCount, $employeeCount);
            $extraContacts = $contactCount % $employeeCount;
            $seq = 0;
            for ($i = 0; $i < $employeeCount; $i++){
                $distribution = $contactsPerEmployee + ($i == $employeeCount - 1 ? $extraContacts : 0);
                $user_id[] = array("user_id"=>$projuid[$seq],"contact"=>$distribution);
                $seq++;
            }
        }else{ //user
            $user_id[] = array("user_id"=>$post['application_user_id'],"contact"=>$post['number_of_lead_contact']);
        }
        $insertary=array('company_id'=>$post['company_id'],'assign_type'=>$post['assign_type'],'assign_lead_contact'=>$post['assign_lead_contact'],'order_by'=>$post['order_by'],'user_id'=>json_encode((array)$user_id),'project_id'=>$post['project_id'],'number_of_lead_contact'=>$post['number_of_lead_contact'],'assigning_datetime'=>date(self::DATE_FORMAT_HIS),'is_active'=>'0','created_by'=>$post['user_id'],'created_date'=>date(self::DATE_FORMAT_HIS));
        $crm_lead_and_contact_assignings = $callConfig->insertRecord('crm_lead_and_contact_assignings',$insertary);
        $return['res'] = $crm_lead_and_contact_assignings;
        if($crm_lead_and_contact_assignings['status'] == 'sucess'){
            foreach($user_id as $usepor){
                $uids = $usepor['user_id'];
                $contact = $usepor['contact'];
                $query = "UPDATE ".$assign_lead_contact." SET access_user_id =".$uids.", access_datetime = '".date(self::DATE_FORMAT_HIS)."', crm_lead_and_contact_assigning_id =".$crm_lead_and_contact_assignings['LASTID'].self::SQL_WHERE_COMPANY_ID_EQ_QUOTE.$post['company_id']."' ".$originatercondition." AND `access_user_id` IS NULL AND is_active = 0 ORDER BY created_date asc limit ".$contact;
                $return['udate_crm_leads']  = $callConfig->executeQuery($query);

                $msg = $contact. " : New Lead/Cntacts Assign For You";
                $notdataa = array('company_id'=>$post['company_id'],'notification_message'=>$msg, 'id'=>"",'application_table_name'=>'crm_lead_and_contact_assignings', 'type'=>'INFORMATION', 'notification_users'=>array($uids),'option'=>'','assigner_id'=>$post['user_id']);
                $this->tosendnotifiations($notdataa);

            }
        }
        return $return;
    }
    public function testAPI($get){
        global $callConfig;
        $return = array('get' => $get);
        echo "Working<pre>"; print_r($return);

    }

    //Koteswar start

}
