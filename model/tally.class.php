<?php

class TallyConstants
{
    public const AND_IS_ACTIVE_0 = " AND is_active=0";
    public const AND_IS_ACTIVE_0_APOS = "' AND is_active = '0'";
    public const AND_IS_ACTIVE_0_UNQUOTED = "' AND is_active = 0";
    public const AND_IS_ACTIVE_0_NO_SPACE = "' AND is_active=0";
    public const AND_ID_APOS = "' AND id = '";
    public const SEL_API_MASTERS = "SELECT * FROM api_masters WHERE company_id='";
    public const SEL_API_HITS = "SELECT * FROM api_hits WHERE company_id='";
    public const SEL_COMPANIES = "SELECT id, name FROM companies WHERE company_id='";
    public const ALLOW_POSTING_0 = " AND allow_posting = 0 AND is_active =0";
    public const SEL_DMT = "SELECT id, page_title, application_table_name, display_table, item_table, tax_table FROM `dynamic_mapping_tables` WHERE company_id = '";
    public const AND_APP_TABLE = "' AND application_table_name = '";
    public const WHERE_COMPANY_ID = " WHERE company_id='";
    public const SEL_COA = "SELECT id,company_id, account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ";
    public const SEL_DISPLAY_ATTR = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '";
    public const DATE_FORMAT_YMD_HIS = "Y-m-d H:i:s";
    public const SEL_APP_USERS = "SELECT id,display_name, cost_center, location_id, default_warehouse_id, work_station_id, country_id, default_state_id, default_currency_id FROM application_users WHERE company_id = '";
    public const DUPLICATE_ENTRY = "Duplicate Entry";
    public const REGEX_NEWLINE = '/[\r\n]/';
    public const APOS_AND = "' AND";
}

trait TallyApiOutboundTrait
{
public function topostmasterdetails($get, $post){
         global $callConfig;
         $api_hits = array('company_id'=>$get['company_id'],'api_url'=>$get['mode'],'api_request_parameters'=>json_encode($get),'api_response_details'=>json_encode($post), 'application_table'=> $get['table'], 'status_json'=>$get['json_data'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
        return $callConfig->insertRecord('api_hits',$api_hits);
    }
    public function toSendItemCategoriesToTally($get){
         global $callConfig;
         $query = "SELECT id,category_description FROM item_categories WHERE company_id =".$get['company_id'].TallyConstants::AND_IS_ACTIVE_0;
         $data = $callConfig->getAllRows($query);
         $return['result'] = $data;
         return $return;
    }
    public function togetapiidbasemapdetail($get){
        global $callConfig;

        $query = "SELECT * FROM api_masters WHERE company_id =".$get['company_id'].TallyConstants::AND_IS_ACTIVE_0;
         $data = $callConfig->getAllRows($query);
         $return['all_api_masters'] = $data;


        $query = "SELECT * FROM api_masters WHERE company_id =".$get['company_id']." AND  id = ".$get['api_master_id'].TallyConstants::AND_IS_ACTIVE_0;
         $data = $callConfig->getRow($query);
         $return['api_masters'] = $data;


         return $return;
    }
    public function getApiMastersDetails($get){
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        if($get['id'] != ""){
              $account_details_query = TallyConstants::SEL_API_MASTERS.$get['company_id'].TallyConstants::AND_ID_APOS.$get['id'].TallyConstants::AND_IS_ACTIVE_0_APOS;
              $account_details = $callConfig->getRow($account_details_query);
              $return['api_masters'] = $account_details;
              if($account_details->application_table!=""){
                 $query="SELECT id, application_table_name, list_attributes FROM display_attributes WHERE company_id='".$get['company_id']."'  AND application_table_name = '".$account_details->application_table.TallyConstants::AND_IS_ACTIVE_0_APOS;
                $dataa = $callConfig->getRow($query);
                $return['display_attributes_list_attributes'] = $dataa;
             }
        }

         $display_attributessql = "SELECT id, application_table_name FROM display_attributes WHERE company_id='" . $get['company_id'] . "'  AND is_active = '0'";
          $display_attributesres = $callConfig->getAllRows($display_attributessql);
          $return['display_attributes'] = $display_attributesres;

        return $return;
      }
    public function getAPIHits($get){
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        if($get['api_master_id'] !=""){
            $api_hitssql = TallyConstants::SEL_API_HITS.$get['company_id']."' AND api_master_id = '".$get['api_master_id']."' AND is_active = '0' ORDER BY request_date_time DESC";
              $api_hitsres = $callConfig->getAllRows($api_hitssql);
              $return['api_hits'] = $api_hitsres;

              $apimasterssql = "SELECT id, api_service_provider, api_name, end_point, api_method FROM api_masters WHERE company_id='".$get['company_id'].TallyConstants::AND_ID_APOS.$get['api_master_id'].TallyConstants::AND_IS_ACTIVE_0_APOS;
              $apimastersres = $callConfig->getRow($apimasterssql);
              $return['api_masters'] = $apimastersres;
          }elseif($get['api_hit_id'] !=""){

              $api_hitssql = TallyConstants::SEL_API_HITS.$get['company_id'].TallyConstants::AND_ID_APOS.$get['api_hit_id'].TallyConstants::AND_IS_ACTIVE_0_APOS;
              $api_hitsres = $callConfig->getRow($api_hitssql);
              $return['api_hits'] = $api_hitsres;

              if($api_hitsres->api_master_id !=""){
                  $apimasterssql = TallyConstants::SEL_API_MASTERS.$get['company_id'].TallyConstants::AND_ID_APOS.$api_hitsres->api_master_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
                  $apimastersres = $callConfig->getRow($apimasterssql);
                  $return['api_masters'] = $apimastersres;
              }

          }else{
            $apimasterssql = "SELECT id, api_service_provider, api_name, api_type FROM api_masters WHERE company_id='".$get['company_id'].TallyConstants::AND_IS_ACTIVE_0_APOS;
              $apimastersres = $callConfig->getAllRows($apimasterssql);
              $return['api_masters'] = $apimastersres;
          }
        return $return;
    }

    public function insertAPIHits($get){
         global $callConfig;
         return $callConfig->insertRecord('api_hits',$get);
    }

    public function getLedgerMaster($get){
        global $callConfig;
        $return = array();
        $company_id = $get['company_id'];
         $companysql = TallyConstants::SEL_COMPANIES.$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $companysres = $callConfig->getAllRows($companysql);
        $companies = array();
        foreach($companysres as $comp){
            $companies[$comp->id] = $comp->name;
        }

        $chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type, accgro_group_id FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND account_code IN('1003809','50000106','50000151','20003507','40001101','10003700','20003501','1004001','20003506','10005101','40001109','200037018','20003701','20003702','20003701','20003716','50002714','5002557','10005508','10005501','10005502','10005503','10005508','10006301','10006302','10006303','10001602','10006104') AND is_active =0 ";
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
        $chart_of_accounts = array();
        foreach($chartofaccountsres as $cca){
            $chart_of_accounts[$cca->account_code] = $cca;
        }

        $account_groupssql = "SELECT id,company_id, group_name FROM `account_groups` WHERE company_id = ".$company_id." AND is_active =0";
        $account_groupsres = $callConfig->getAllRows($account_groupssql);
        $account_groups = array();
        foreach($account_groupsres as $cca){
            $account_groups[$cca->id] = $cca->group_name;
        }


        $ledger_master = array();




        $customersql = "SELECT company_id, company, display_name, billing_address, billing_country, billing_state, billing_city, billing_post_code, email, website_url, pan_no, gstin, account_code FROM `customers` WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $customerres = $callConfig->getAllRows($customersql);
        foreach($customerres as $cust){
            $cust->company_id = $companies[$cust->company_id];
            $chartofaccount =     $chart_of_accounts[$cust->account_code]->accgro_group_id;
            $cust->account_name = $chart_of_accounts[$cust->account_code]->account_name;
            $cust->account_code = $chart_of_accounts[$cust->account_code]->account_code;
            $ledger_group_name = $account_groups[$chartofaccount];
            $ledgeary = array('company_id'=>$cust->company_id, 'account_name'=>$cust->company, 'account_code'=>$cust->account_code, 'ledger_group_name'=>$ledger_group_name,'billing_address'=>$cust->billing_address, 'billing_country'=>'', 'billing_state'=>'', 'billing_city'=>'', 'billing_post_code'=>$cust->billing_post_code, 'email'=>$cust->email, 'website_url'=>'' , 'pan_no'=>$cust->pan_no, 'gstin'=>$cust->gstin);
            $ledger_master[] = (array)$ledgeary;
        }


        $vendorsql = "SELECT company_id, company, display_name, billing_address, billing_country, billing_state, billing_city, billing_post_code, email, website_url, pan_no, gstin, account_code FROM `vendors` WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $vendorres = $callConfig->getAllRows($vendorsql);
        foreach($vendorres as $ven){
            $ven->company_id = $companies[$ven->company_id];
            $chartofaccount =     $chart_of_accounts[$ven->account_code]->accgro_group_id;
            $ven->account_name = $chart_of_accounts[$ven->account_code]->account_name;
            $ven->account_code = $chart_of_accounts[$ven->account_code]->account_code;
            $ledger_group_name = $account_groups[$chartofaccount];
            $ledgeary = array('company_id'=>$ven->company_id, 'account_name'=>$ven->company, 'account_code'=>$ven->account_code, 'ledger_group_name'=>$ledger_group_name,'billing_address'=>$ven->billing_address, 'billing_country'=>'', 'billing_state'=>'', 'billing_city'=>'', 'billing_post_code'=>$ven->billing_post_code, 'email'=>$ven->email, 'website_url'=>'' , 'pan_no'=>$ven->pan_no, 'gstin'=>$ven->gstin);
            $ledger_master[] = (array)$ledgeary;
        }




        foreach($chartofaccountsres as $cca){
            $ledgeary = array();
            if($cca->account_name !=""){
                $cca->company_id = $companies[$cca->company_id];
                $ledger_group_name = $account_groups[$cca->accgro_group_id];
                $ledgeary = array('company_id'=>$cca->company_id, 'account_name'=>$cca->account_name, 'account_code'=>$cca->account_code, 'ledger_group_name'=>$ledger_group_name,'billing_address'=>'', 'billing_country'=>'', 'billing_state'=>'', 'billing_city'=>'', 'billing_post_code'=>'', 'email'=>'', 'website_url'=>'' , 'pan_no'=>'', 'gstin'=>'');
            }
            $ledger_master[] = (array)$ledgeary;
        }
        $return['status'] = 200;
        $return['result'] = $ledger_master;
        return $return;

    }

    private function fetchTransactionLookupMaps($company_id, $transaction_table): array
    {
        global $callConfig;
        $companies = array();
        foreach ($callConfig->getAllRows(TallyConstants::SEL_COMPANIES . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS) as $comp) {
            $companies[$comp->id] = $comp->name;
        }

        $cities = array();
        foreach ($callConfig->getAllRows("SELECT id, city_name FROM cities WHERE company_id='" . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS) as $comp) {
            $cities[$comp->id] = $comp->city_name;
        }

        $states = array();
        foreach ($callConfig->getAllRows("SELECT id, state_name FROM states WHERE company_id='" . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS) as $stat) {
            $states[$stat->id] = $stat->state_name;
        }

        $chart_of_accounts = array();
        foreach ($callConfig->getAllRows("SELECT id,company_id, account_name,account_code,account_type, accgro_group_id FROM `chart_of_accounts` WHERE company_id = " . $company_id . TallyConstants::ALLOW_POSTING_0) as $cca) {
            $chart_of_accounts[$cca->account_code] = $cca;
        }

        $account_groups = array();
        foreach ($callConfig->getAllRows("SELECT id,company_id, group_name FROM `account_groups` WHERE company_id = " . $company_id . " AND is_active =0") as $cca) {
            $account_groups[$cca->id] = $cca->group_name;
        }

        $customers = array();
        $isSales = ($transaction_table == "sales_invoices" || $transaction_table == "sales_orders" || $transaction_table == "delivery_orders");
        $custTable = $isSales ? "customers" : "vendors";
        foreach ($callConfig->getAllRows("SELECT id, company FROM {$custTable} WHERE company_id='" . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS) as $custre) {
            $customers[$custre->id] = $custre->company;
        }

        return [$companies, $cities, $states, $chart_of_accounts, $account_groups, $customers];
    }

    private function fetchTransactionItemsAndTaxes($company_id, $transaction_table, $dmtres, array $companies, array $chart_of_accounts, array $account_groups): array
    {
        global $callConfig;
        $item_trans = array();
        if ($transaction_table == "credit_notes" || $transaction_table == "debit_notes") {
            $itemSql = "SELECT titm.id, titm.company_id, titm.transaction_id, titm.item_id, titm.account_code, titm.item_name, titm.item_name, titm.item_quantity, titm.item_price, titm.item_amount FROM " . $dmtres->item_table . " titm WHERE titm.company_id='" . $company_id . "' AND titm.is_active = '0'";
        } else {
            $itemSql = "SELECT titm.id, titm.company_id, titm.transaction_id, titm.item_id, titm.item_name, titm.item_quantity, titm.item_price, titm.item_amount, itm.sku as part_no FROM " . $dmtres->item_table . " titm, items itm WHERE titm.company_id='" . $company_id . "' AND titm.company_id=itm.company_id AND titm.item_id = itm.id AND titm.is_active = itm.is_active AND titm.is_active = '0'";
        }
        foreach ($callConfig->getAllRows($itemSql) as $itemtrans) {
            $itemtrans->company_id = $companies[$itemtrans->company_id] ?? $itemtrans->company_id;
            $item_trans[$itemtrans->transaction_id][] = $itemtrans;
        }

        $item_tax_trans = array();
        $taxSql = "SELECT id, company_id, transaction_id, transaction_item_id, tax_name, tax_value, tax_id, tax_amount, tax_split_details FROM " . $dmtres->tax_table . TallyConstants::WHERE_COMPANY_ID . $company_id . "'  AND is_active = '0' ";
        foreach ($callConfig->getAllRows($taxSql) as $itemtaxtrans) {
            $tax_split_details = json_decode($itemtaxtrans->tax_split_details, 1) ?: array();
            foreach ($tax_split_details as $ttt) {
                $code = $ttt['account_code'] ?? '';
                $item_tax_trans[$itemtaxtrans->transaction_id][$code]['hsn_code'] = $itemtaxtrans->tax_name ?: "";
                $item_tax_trans[$itemtaxtrans->transaction_id][$code]['tax_name'] = $ttt['tax_name'] ?: "";
                $item_tax_trans[$itemtaxtrans->transaction_id][$code]['amount'] = ($item_tax_trans[$itemtaxtrans->transaction_id][$code]['amount'] ?? 0) + ($ttt['tax_amount'] ?: 0);
                $item_tax_trans[$itemtaxtrans->transaction_id][$code]['account_code'] = $code;
                $item_tax_trans[$itemtaxtrans->transaction_id][$code]['tax_percentage'] = $ttt['tax_percentage'] ?: "";
                $item_tax_trans[$itemtaxtrans->transaction_id][$code]['account_name'] = isset($chart_of_accounts[$code]) ? $chart_of_accounts[$code]->account_name : "";
                $groupId = isset($chart_of_accounts[$code]) ? $chart_of_accounts[$code]->accgro_group_id : 0;
                $item_tax_trans[$itemtaxtrans->transaction_id][$code]['ledger_group_name'] = $account_groups[$groupId] ?? "";
            }
        }

        return [$item_trans, $item_tax_trans];
    }

    private function fetchTransactionJournalVouchers($company_id, $application_table_name, array $chart_of_accounts, array $account_groups): array
    {
        global $callConfig;
        $journal_vouchers = array();
        $jvsql = "SELECT jv.transaction_number bill_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS bill_date , jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM journal_vouchers jv, jv_items ijv WHERE jv.company_id='" . $company_id . "' AND jv.ref_type = '" . $application_table_name . "' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
        foreach ($callConfig->getAllRows($jvsql) as $jv) {
            $jv->amount = ($jv->debit_amount != "0" && $jv->debit_amount != "") ? $jv->debit_amount : $jv->credit_amount;
            $groupId = isset($chart_of_accounts[$jv->account_code]) ? $chart_of_accounts[$jv->account_code]->accgro_group_id : 0;
            $jv->ledger_group_name = $account_groups[$groupId] ?? "";
            $journal_vouchers[$jv->ref_id][] = $jv;
        }
        return $journal_vouchers;
    }

    public function getTransactionInvoices($get, $transaction_table = "")
    {
        global $callConfig;
        $company_id = $get['company_id'];
        if ($transaction_table === "") {
            return array('status' => 404, 'result' => array());
        }

        list($companies, $cities, $states, $chart_of_accounts, $account_groups, $customers) = $this->fetchTransactionLookupMaps($company_id, $transaction_table);

        $dmtsql = TallyConstants::SEL_DMT . $company_id . TallyConstants::AND_APP_TABLE . $transaction_table . TallyConstants::AND_IS_ACTIVE_0_UNQUOTED;
        $dmtres = $callConfig->getRow($dmtsql);

        list($item_trans, $item_tax_trans) = $this->fetchTransactionItemsAndTaxes($company_id, $transaction_table, $dmtres, $companies, $chart_of_accounts, $account_groups);
        $journal_vouchers = $this->fetchTransactionJournalVouchers($company_id, $dmtres->application_table_name, $chart_of_accounts, $account_groups);

        $main_transaction_sql = "SELECT id, company_id, transaction_number, DATE_FORMAT(transaction_date, '%d-%m-%Y') AS transaction_date, party_id, party_name, party_email, party_phone, party_address, supplier_state_id, place_of_supply, ref_type, ref_no FROM " . $dmtres->application_table_name . TallyConstants::WHERE_COMPANY_ID . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        $main_transaction_res = $callConfig->getAllRows($main_transaction_sql);
        $transaction_entry = array();
        $vendcustarray = array('20003501', '1004001');
        $giveledger_entries = array('goods_receipt_notes', 'delivery_orders', 'credit_notes', 'debit_notes', 'supplier_bills', 'purchase_invoices', 'sales_invoices');

        foreach ($main_transaction_res as $trans) {
            $trans->company_id = $companies[$trans->company_id] ?? $trans->company_id;
            $trans->place_of_supply = $cities[$trans->place_of_supply] ?? $trans->place_of_supply;
            $trans->supplier_state_id = $states[$trans->supplier_state_id] ?? $trans->supplier_state_id;
            if ($transaction_table != "credit_notes" && $transaction_table != "debit_notes") {
                $trans->party_name = $customers[$trans->party_id] ?? $trans->party_name;
            }

            $trans->item_entries = (isset($item_trans[$trans->id]) && $transaction_table != "credit_notes" && $transaction_table != "debit_notes") ? $item_trans[$trans->id] : array();
            $trans->item_tax = isset($item_tax_trans[$trans->id]) ? array_values($item_tax_trans[$trans->id]) : array();

            $trans->ledger_entries = array();
            if (in_array($transaction_table, $giveledger_entries) && isset($journal_vouchers[$trans->id])) {
                foreach ($journal_vouchers[$trans->id] as $lfgdetail) {
                    if (!in_array($lfgdetail->account_code, (array)$vendcustarray)) {
                        $trans->ledger_entries[] = $lfgdetail;
                    }
                }
            }
            $transaction_entry[] = $trans;
        }

        return array(
            'status' => 200,
            'transaction' => $transaction_table,
            'result' => $transaction_entry
        );
    }
    public function getAPIPayments($get){
        global $callConfig;
         $return = array();
         $company_id = $get['company_id'];
        $companysql = TallyConstants::SEL_COMPANIES.$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $companysres = $callConfig->getAllRows($companysql);
        $companies = array();
        foreach($companysres as $comp){
            $companies[$comp->id] = $comp->name;
        }

        $customerssql = "SELECT id, company FROM vendors WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $customersres = $callConfig->getAllRows($customerssql);
        foreach($customersres as $custre){
            $customers[$custre->id] = $custre->company;
        }



        $chartofaccountssql = TallyConstants::SEL_COA.$company_id.TallyConstants::ALLOW_POSTING_0;
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
        $chart_of_accounts = array();
        foreach($chartofaccountsres as $cca){
            $chart_of_accounts[$cca->account_code] = $cca;
        }

        $payment_allocations_sql = "SELECT company_id, payment_id, transaction_table, transaction_id, transaction_no as bill_transaction_no, party_id, total_paid_amonut as amount  FROM payment_allocations WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $payment_allocations_res = $callConfig->getAllRows($payment_allocations_sql);
        foreach($payment_allocations_res as $payres){
            $payres->company_id = $companies[$payres->company_id];
            $payres->party_name = $customers[$payres->party_id];
            $item_trans[$payres->payment_id][] = $payres;
        }

         //Start Ledger Entries
    /**
        $jvsql = "SELECT jv.transaction_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS transaction_date, jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM journal_vouchers jv, jv_items ijv WHERE jv.company_id='".$company_id."' AND jv.ref_type = 'receipts' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
        $jvres = $callConfig->getAllRows($jvsql);
        $transaction_entry = array();
        foreach($jvres as $jv){
            $jv->amount = ($jv->debit_amount !="0" AND $jv->debit_amount !="")?$jv->debit_amount:$jv->credit_amount;
            $journal_vouchers[$jv->ref_id][] = $jv;
        }

        $jvsql = "SELECT jv.transaction_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS transaction_date, jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM cash_vouchers jv, cv_items ijv WHERE jv.company_id='".$company_id."' AND jv.ref_type = 'receipts' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
        $jvres = $callConfig->getAllRows($jvsql);
        foreach($jvres as $jv){
            $jv->amount = ($jv->debit_amount !="0" AND $jv->debit_amount !="")?$jv->debit_amount:$jv->credit_amount;
            $journal_vouchers[$jv->ref_id][] = $jv;
        }

        $jvsql = "SELECT jv.transaction_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS transaction_date, jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM bank_vouchers jv, bv_items ijv WHERE jv.company_id='".$company_id."' AND jv.ref_type = 'receipts' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
        $jvres = $callConfig->getAllRows($jvsql);
        foreach($jvres as $jv){
            $jv->amount = ($jv->debit_amount !="0" AND $jv->debit_amount !="")?$jv->debit_amount:$jv->credit_amount;
            $journal_vouchers[$jv->ref_id][] = $jv;
        } **/
 //End Ledger Entries

        $banckaccountsql = "SELECT account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '".$company_id.TallyConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $banckaccountres = $callConfig->getAllRows($banckaccountsql);

        foreach($banckaccountres as $payres){
            $bank_accounts[$payres->account_code] = (array)$payres;
        }

        $whrs = "company_id= ".$company_id." AND is_active = 0";
        $query = $callConfig->selectQuery('config','default_cash_acct',$whrs,'','','');
        $config = $callConfig->getRow($query);
        $cashaccount =$config->default_cash_acct;




        $payments_sql = "SELECT id, company_id, transaction_no, DATE_FORMAT(payment_date, '%d-%m-%Y') AS payment_date, payment_type, account_code FROM payments WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $payments_res = $callConfig->getAllRows($payments_sql);
        $transaction_entry = array();
        foreach($payments_res as $paytrans){
            $paytrans->company_id = $companies[$paytrans->company_id];
            if($paytrans->payment_type == 'bank'){
                $paytrans->bankcash_name = $bank_accounts[$paytrans->account_code]['bank_account_name'];
                $paytrans->ladger_name = $chart_of_accounts[$paytrans->account_code]->account_name;
            }else{
                $paytrans->bankcash_name = $cashaccount;
                $paytrans->ladger_name = $chart_of_accounts[$cashaccount]->account_name;
            }

            if(isset($item_trans[$paytrans->id])){
                $paytrans->item_entries = $item_trans[$paytrans->id];
            }else{
                $paytrans->item_entries = array();
            }

            $transaction_entry[] = $paytrans;
        }
        $return['status'] = 200;
        $return['result'] = $transaction_entry;
        return $return;
    }
    public function getAPIReceipts($get){
        global $callConfig;
         $return = array();
         $company_id = $get['company_id'];
        $companysql = TallyConstants::SEL_COMPANIES.$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $companysres = $callConfig->getAllRows($companysql);
        $companies = array();
        foreach($companysres as $comp){
            $companies[$comp->id] = $comp->name;
        }

        $chartofaccountssql = TallyConstants::SEL_COA.$company_id.TallyConstants::ALLOW_POSTING_0;
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
        $chart_of_accounts = array();
        foreach($chartofaccountsres as $cca){
            $chart_of_accounts[$cca->account_code] = $cca;
        }

        $customerssql = "SELECT id, company FROM customers WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $customersres = $callConfig->getAllRows($customerssql);
        foreach($customersres as $custre){
            $customers[$custre->id] = $custre->company;
        }

        $payment_allocations_sql = "SELECT company_id, receipt_id, transaction_table, transaction_id, transaction_no as bill_transaction_no, party_id, total_paid_amonut as amount FROM receipt_allocations WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $payment_allocations_res = $callConfig->getAllRows($payment_allocations_sql);
        foreach($payment_allocations_res as $payres){
            $payres->company_id = $companies[$payres->company_id];
            $payres->party_id = $customers[$payres->party_id];
            $item_trans[$payres->receipt_id][] = $payres;
        }

 //Start Ledger Entries
        /**
        $jvsql = "SELECT jv.transaction_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS transaction_date, jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM journal_vouchers jv, jv_items ijv WHERE jv.company_id='".$company_id."' AND jv.ref_type = 'receipts' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
        $jvres = $callConfig->getAllRows($jvsql);
        $transaction_entry = array();
        foreach($jvres as $jv){
            $jv->amount = ($jv->debit_amount !="0" AND $jv->debit_amount !="")?$jv->debit_amount:$jv->credit_amount;
            $journal_vouchers[$jv->ref_id][] = $jv;
        }

        $jvsql = "SELECT jv.transaction_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS transaction_date, jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM cash_vouchers jv, cv_items ijv WHERE jv.company_id='".$company_id."' AND jv.ref_type = 'receipts' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
        $jvres = $callConfig->getAllRows($jvsql);
        foreach($jvres as $jv){
            $jv->amount = ($jv->debit_amount !="0" AND $jv->debit_amount !="")?$jv->debit_amount:$jv->credit_amount;
            $journal_vouchers[$jv->ref_id][] = $jv;
        }

        $jvsql = "SELECT jv.transaction_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS transaction_date, jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM bank_vouchers jv, bv_items ijv WHERE jv.company_id='".$company_id."' AND jv.ref_type = 'receipts' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
        $jvres = $callConfig->getAllRows($jvsql);
        foreach($jvres as $jv){
            $jv->amount = ($jv->debit_amount !="0" AND $jv->debit_amount !="")?$jv->debit_amount:$jv->credit_amount;
            $journal_vouchers[$jv->ref_id][] = $jv;
        } **/
 //End Ledger Entries

        $banckaccountsql = "SELECT account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '".$company_id.TallyConstants::AND_IS_ACTIVE_0_NO_SPACE;
        $banckaccountres = $callConfig->getAllRows($banckaccountsql);

        foreach($banckaccountres as $payres){
            $bank_accounts[$payres->account_code] = (array)$payres;
        }

        $whrs = "company_id= ".$company_id." AND is_active = 0";
        $query = $callConfig->selectQuery('config','default_cash_acct',$whrs,'','','');
        $config = $callConfig->getRow($query);
        $cashaccount =$config->default_cash_acct;




        $payments_sql = "SELECT id, company_id, transaction_no, DATE_FORMAT(payment_date, '%d-%m-%Y') AS payment_date, payment_type, account_code FROM receipts WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $payments_res = $callConfig->getAllRows($payments_sql);
        $transaction_entry = array();
        foreach($payments_res as $paytrans){
            $paytrans->company_id = $companies[$paytrans->company_id];
            if($paytrans->payment_type == 'bank'){
                $paytrans->bankcash_name = $bank_accounts[$paytrans->account_code]['bank_account_name'];
                $paytrans->ladger_name = $chart_of_accounts[$paytrans->account_code]->account_name;
            }else{
                $paytrans->bankcash_name = $cashaccount;
                $paytrans->ladger_name = $chart_of_accounts[$cashaccount]->account_name;
            }


            if(isset($item_trans[$paytrans->id])){
                $paytrans->item_entries = $item_trans[$paytrans->id];
            }else{
                $paytrans->item_entries = array();
            }
            $transaction_entry[] = $paytrans;
        }
        $return['status'] = 200;
        $return['result'] = $transaction_entry;
        return $return;
    }
    public function getAPIJournalVouchers($get){
        global $callConfig;
         $return = array();
         $company_id = $get['company_id'];
        $companysql = TallyConstants::SEL_COMPANIES.$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $companysres = $callConfig->getAllRows($companysql);
        $companies = array();
        foreach($companysres as $comp){
            $companies[$comp->id] = $comp->name;
        }


        $jvisql = "SELECT transaction_id, account_name as ledger_name, debit_amount, credit_amount FROM jv_items WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_UNQUOTED;
        $jvires = $callConfig->getAllRows($jvisql);
        foreach($jvires as $jvi){
            $jvi->amount = ($jvi->debit_amount !="0" && $jvi->debit_amount !="")?$jvi->debit_amount:$jvi->credit_amount;
            $jv_items[$jvi->transaction_id][] = $jvi;
        }

           $jvsql = "SELECT id, transaction_number, DATE_FORMAT(transaction_date, '%d-%m-%Y') AS transaction_date, ref_type, ref_id FROM journal_vouchers WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_UNQUOTED;
        $jvres = $callConfig->getAllRows($jvsql);
        $transaction_entry = array();
        foreach($jvres as $jv){
            if(isset($jv_items[$jv->id])){
                $jv->ledger_entries = $jv_items[$jv->id];
            }else{
                $jv->ledger_entries = array();
            }
            $transaction_entry[] = $jv;
        }
        $return['status'] = 200;
        $return['result'] = $transaction_entry;
        return $return;
    }
    public function getAPIItems($get){
        global $callConfig;
         $return = array();
         $company_id = $get['company_id'];
        $companysql = TallyConstants::SEL_COMPANIES.$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $companysres = $callConfig->getAllRows($companysql);
        $companies = array();
        foreach($companysres as $comp){
            $companies[$comp->id] = $comp->name;
        }

        $unit_of_measurementssql = "SELECT id, uom_short_code FROM unit_of_measurements WHERE company_id='".$company_id.TallyConstants::AND_IS_ACTIVE_0_APOS;
        $unit_of_measurementssres = $callConfig->getAllRows($unit_of_measurementssql);
        $unit_of_measurements = array();
        foreach($unit_of_measurementssres as $uom){
            $unit_of_measurements[$uom->id] = $uom->uom_short_code;
        }

           $jvsql = "SELECT itm.id, itm.company_id, itm.sku, itm.item_name, itm.purchase_uom, itm.sale_uom, itm.hsn_and_sac_code_id, hasc.gst, hasc.code, hasc.code_description FROM items itm left join hsn_and_sac_codes hasc ON itm.hsn_and_sac_code_id = hasc.id AND itm.company_id = hasc.company_id AND itm.is_active = hasc.is_active WHERE itm.company_id ='".$company_id."' AND itm.is_active = 0";
        $jvres = $callConfig->getAllRows($jvsql);
        $items = array();
        foreach($jvres as $jv){
            $jv->company_id = $companies[$jv->company_id];
            $jv->purchase_uom = $unit_of_measurements[$jv->purchase_uom];
            $jv->sale_uom = $unit_of_measurements[$jv->sale_uom];
            $items[] = $jv;
        }
        $return['status'] = 200;
        $return['result'] = $items;
        return $return;
    }
    private function executeDynamicSendApi($get, $apimastersres): array
    {
        global $callConfig;
        $fieldkeywithvalue = array();
        $response_field_ary = json_decode($apimastersres->api_response_details, 1) ?: array();
        $getstringlist = "";
        foreach ($response_field_ary as $responsekey => $fieldname) {
            if (!empty($fieldname['application_table_field'])) {
                $getstringlist .= $fieldname['application_table_field'] . " as " . $responsekey . ",";
            }
        }
        $getstringlists = ($getstringlist !== "") ? substr($getstringlist, 0, -1) : "*";
        $query = "SELECT " . $getstringlists . " FROM " . $apimastersres->application_table . " WHERE company_id =" . $get['company_id'] . TallyConstants::AND_IS_ACTIVE_0;
        $data = $callConfig->getAllRows($query);
        if (!empty($data)) {
            $returnres = array();
            foreach ($data as $resdata) {
                $finalresdata = array();
                foreach ($resdata as $keyfld => $rsdata) {
                    $finalresdata[$keyfld] = !empty($fieldkeywithvalue[$keyfld][$rsdata]) ? $fieldkeywithvalue[$keyfld][$rsdata] : $rsdata;
                }
                $returnres[] = $finalresdata;
            }
            return array('status' => 200, 'result' => $returnres);
        }
        return array('status' => 404);
    }

    private function dispatchSendApiCode($get, $post, $apimastersres): array
    {
        $api_code = $apimastersres->api_code;
        $transactionCodes = array('SENDSALESINVOICES', 'SENDSALESORDERS', 'SENDPURCHASEORDERS', 'SENDPURCHASEINVOICES', 'SENDGOODSRECEIPTNOTES', 'SENDDEBITNOTES', 'SENDCREDITNOTES', 'SENDDELIVERYORDERS');

        if ($api_code == "SENDLEDGERMASTER") {
            return $this->getLedgerMaster($get, $post);
        }
        if (in_array($api_code, $transactionCodes)) {
            return $this->getTransactionInvoices($get, $apimastersres->application_table);
        }
        if ($api_code == "SENDPAYMENTS") {
            return $this->getAPIPayments($get, $post);
        }
        if ($api_code == "SENDRECEIPTS") {
            return $this->getAPIReceipts($get, $post);
        }
        if ($api_code == "SENDJOURNALVOUCHERS") {
            return $this->getAPIJournalVouchers($get, $post);
        }
        if ($api_code == "SENDITEMS") {
            return $this->getAPIItems($get, $post);
        }
        return $this->executeDynamicSendApi($get, $apimastersres);
    }

    public function getAPImastersAPICode($get, $post)
    {
        global $callConfig;
        $company_id = $get['company_id'];
        $uniquecode = $get['uniquecode'] ?? '';
        $apimasterssql = TallyConstants::SEL_API_MASTERS . $company_id . "' AND api_code = '" . $uniquecode . TallyConstants::AND_IS_ACTIVE_0_APOS;
        $apimastersres = $callConfig->getRow($apimasterssql);
        $api_url = "";
        $api_master_id = 0;

        if (!empty($apimastersres->id)) {
            $api_master_id = $apimastersres->id;
            $api_url = $apimastersres->end_point;
            if ($apimastersres->api_type == "S") {
                $return = $this->dispatchSendApiCode($get, $post, $apimastersres);
            } else {
                $return = $post;
            }
        } else {
            $return = array('status' => 403);
        }

        $final_status = !empty($return['status']) ? $return['status'] : "200";
        $api_hits = array(
            'company_id' => $company_id,
            'api_url' => $api_url,
            'api_master_id' => $api_master_id,
            'api_request_parameters' => urlencode(json_encode((array)$get)),
            'api_response_details' => urlencode(json_encode((array)$return)),
            'request_date_time' => date(TallyConstants::DATE_FORMAT_YMD_HIS),
            'response_status' => $final_status,
            'is_active' => 0,
            'current_status' => 0,
            'created_by' => $get['user_id']
        );
        $this->insertAPIHits($api_hits);
        return $return;
    }
}

trait TallyApiInboundTrait
{
    private function loadReceiptInboundMasters($company_id, $user_id): array
    {
        global $callConfig;
        $application_userssql = TallyConstants::SEL_APP_USERS . $company_id . TallyConstants::AND_ID_APOS . $user_id . TallyConstants::AND_IS_ACTIVE_0_UNQUOTED;
        $application_usersres = $callConfig->getRow($application_userssql);

        $chart_of_accounts = array();
        $chart_of_accounts_name = array();
        foreach ($callConfig->getAllRows(TallyConstants::SEL_COA . $company_id . TallyConstants::ALLOW_POSTING_0) as $cca) {
            $chart_of_accounts_name[$cca->account_name] = $cca;
            $chart_of_accounts[$cca->account_code] = $cca;
        }

        $customers = array();
        $customerssql = "SELECT ctm.customer_id, ctm.party_name, cust.id, cust.company, cust.account_code FROM customer_tally_mappings ctm, customers cust WHERE ctm.company_id = " . $company_id . " AND ctm.company_id = cust.company_id AND ctm.customer_id = cust.id AND ctm.is_active = cust.is_active AND ctm.is_active = 0";
        foreach ($callConfig->getAllRows($customerssql) as $custre) {
            $customers[$custre->party_name] = $custre;
        }

        $bank_accounts = array();
        $banckaccountsql = "SELECT id, account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '" . $company_id . TallyConstants::AND_IS_ACTIVE_0_NO_SPACE;
        foreach ($callConfig->getAllRows($banckaccountsql) as $payres) {
            $bank_accounts[$payres->account_code] = (array)$payres;
        }

        $sales_invoices = array();
        $sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id FROM sales_invoices WHERE company_id = '" . $company_id . TallyConstants::AND_IS_ACTIVE_0_NO_SPACE;
        foreach ($callConfig->getAllRows($sales_invoicessql) as $si) {
            $sales_invoices[$si->transaction_number] = (array)$si;
        }

        $receipt_allocations = array();
        $receipt_allocationssql = "SELECT id, receipt_id, transaction_no AS bill_transaction_no , total_paid_amonut FROM receipt_allocations WHERE company_id='" . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        foreach ($callConfig->getAllRows($receipt_allocationssql) as $recalcpt) {
            $receipt_allocations[$recalcpt->receipt_id] = $recalcpt;
        }

        $receipts = array();
        $receiptssql = "SELECT id, transaction_no, amount, unallocated_amount, allocated_amount, paid_amonut, tds_amount FROM receipts WHERE company_id='" . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        foreach ($callConfig->getAllRows($receiptssql) as $recpt) {
            $recpt->bll_details = $receipt_allocations[$recpt->id] ?? null;
            $receipts[$recpt->transaction_no] = $recpt;
        }
        unset($receipts['REC00016']);

        return [$application_usersres, $chart_of_accounts, $chart_of_accounts_name, $customers, $bank_accounts, $sales_invoices, $receipts];
    }

    private function processSingleReceiptHit($apihitre, $company_id, $checkstatus, $get, array $masters): array
    {
        global $callConfig;
        list($application_usersres, $chart_of_accounts, $chart_of_accounts_name, $customers, $bank_accounts, $sales_invoices, $receipts) = $masters;
        $valuenotavailablefieldary = array();
        $stringary = array();

        if (isset($receipts[$apihitre['VOUCHERNUMBER']])) {
            $stringary['status'] = 1062;
            $stringary['query'] = '';
            $stringary['error'] = TallyConstants::DUPLICATE_ENTRY;
            $apihitre['response'] = $stringary;
            $apihitre['valuenotavailablefieldary'] = $valuenotavailablefieldary;
            return $apihitre;
        }

        $recepttotalamt = 0;
        $party_id = array();
        $payment_date = $this->dateconvertdbformat($apihitre['InvDate']);
        $arychunks = array_chunk($apihitre['Ledger_Entries'], 2);
        $payment_type = "cash";
        $bank_account_id = $account_code = "";
        $receptaccountname = "";

        foreach ($arychunks as $partydetailandtransdetail) {
            $recepttotalamt += $partydetailandtransdetail[0]['AMOUNT'] ?? 0;
            $receptaccountname = $partydetailandtransdetail[1]['Ledger_Name'] ?? '';
            $pName = $partydetailandtransdetail[0]['Party_Name'] ?? '';
            if (isset($customers[$pName])) {
                $custid = $customers[$pName]->id;
                $party_id[$custid] = $custid;
            }
        }

        if (isset($chart_of_accounts_name[$receptaccountname])) {
            $payment_account_details = $chart_of_accounts_name[$receptaccountname];
            if (isset($bank_accounts[$payment_account_details->account_code])) {
                $bankaccountdetails = $bank_accounts[$payment_account_details->account_code];
                $payment_type = "bank";
                $bank_account_id = $bankaccountdetails['id'];
                $account_code = $bankaccountdetails['account_code'];
            }
        }

        $paymentarray = array(
            'company_id' => $company_id,
            'transaction_type' => 'customers',
            'transaction_no' => '',
            'payment_date' => $payment_date,
            'party_id' => json_encode($party_id),
            'payment_type' => $payment_type,
            'bank_account_id' => $bank_account_id,
            'account_code' => $account_code,
            'amount' => $recepttotalamt,
            'ref_no' => '',
            'due_date' => $payment_date,
            'unallocated_amount' => 0,
            'allocated_amount' => $recepttotalamt,
            'remarks' => '',
            'originator' => 1,
            'is_active' => '0',
            'current_status' => '199',
            'paid_amonut' => $recepttotalamt,
            'tds_amount' => 0,
            'payment_process' => 'NP',
            'created_by' => 1,
            'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS)
        );

        $paymentallocationarrayre = array();
        foreach ($arychunks as $partydetailandtransdetail) {
            $billNo = $partydetailandtransdetail[0]['Bill_Details']['Bill_NO'] ?? '';
            $transaction_details = $sales_invoices[$billNo] ?? array('id' => 0, 'transaction_number' => '', 'party_id' => 0);
            $paymentallocationarrayre[] = array(
                'company_id' => $company_id,
                'receipt_id' => 0,
                'transaction_table' => 'sales_invoices',
                'transaction_id' => $transaction_details['id'],
                'transaction_no' => $transaction_details['transaction_number'],
                'total_amount' => $partydetailandtransdetail[0]['AMOUNT'] ?? 0,
                'pending_amount' => 0,
                'paid_amonut' => $partydetailandtransdetail[0]['AMOUNT'] ?? 0,
                'is_active' => '0',
                'current_status' => '0',
                'created_by' => 1,
                'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS),
                'tds_amount' => 0,
                'total_paid_amonut' => $partydetailandtransdetail[0]['AMOUNT'] ?? 0,
                'total_tax_split_details' => '',
                'party_id' => $transaction_details['party_id']
            );
        }

        $popisosichildtablerequirdary = array('transaction_table', 'transaction_id', 'transaction_no');
        $popisosimaintablerequirdary = array('party_id', 'payment_type', 'amount', 'cost_center', 'location_id');

        foreach ((array)$application_usersres as $fieldkey => $fieldval) {
            if (in_array($fieldkey, $popisosimaintablerequirdary) && $fieldval == "") {
                $valuenotavailablefieldary[$fieldkey] = $fieldval;
            }
        }
        foreach ($paymentarray as $fieldkey => $fieldval) {
            if (in_array($fieldkey, $popisosimaintablerequirdary) && $fieldval == "") {
                $valuenotavailablefieldary[$fieldkey] = $fieldval;
            }
        }
        foreach ($paymentallocationarrayre as $itemtble) {
            foreach ($itemtble as $fieldkey => $fieldval) {
                if (in_array($fieldkey, $popisosichildtablerequirdary) && $fieldval == "") {
                    $valuenotavailablefieldary[$fieldkey] = $fieldval;
                }
            }
        }

        $res = array();
        $payallocationres = array();
        if ($checkstatus == "S" && count($valuenotavailablefieldary) == 0) {
            $transaction_no = $this->toGetApplicationSerialNumber('receipts');
            $paymentarray['transaction_no'] = $transaction_no;
            $res = $callConfig->insertRecord('receipts', $paymentarray);
            $receipt_id = $res['LASTID'] ?? 0;
            if (($res['status'] ?? '') == 'sucess') {
                foreach ($paymentallocationarrayre as $paymentallocationarray) {
                    $paymentallocationarray['receipt_id'] = $receipt_id;
                    $payallocationres[] = $callConfig->insertRecord('receipt_allocations', $paymentallocationarray);
                }
                $stringary['jvquery'] = $this->insertpaymentstojv($receipt_id, $get, 'receipts');
            }
        }

        $stringary['transaction_item_res'] = $payallocationres;
        $stringary['transaction_item'] = $paymentallocationarrayre;
        $stringary['status'] = 200;
        $stringary['query'] = '';
        $stringary['transaction'] = $paymentarray;
        $stringary['transaction_res'] = $res;
        $stringary['error'] = 'success';

        $apihitre['response'] = $stringary;
        $apihitre['valuenotavailablefieldary'] = $valuenotavailablefieldary;
        return $apihitre;
    }

    public function tallyAPIReceiveReceipts($get, $post, $api_hitsres, $apimastersres)
    {
        $checkstatus = !empty($post['checkstatus']) ? $post['checkstatus'] : ($get['checkstatus'] ?? '');
        $company_id = $get['company_id'];
        $masters = $this->loadReceiptInboundMasters($company_id, $get['user_id']);
        $api_code = $apimastersres->api_code;
        $api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details), 1) ?: array();
        $responsary = array();

        if (!empty($api_hit_api_response_details[$api_code])) {
            foreach ($api_hit_api_response_details[$api_code] as $apihitre) {
                $responsary[] = $this->processSingleReceiptHit($apihitre, $company_id, $checkstatus, $get, $masters);
            }
        }
        return $responsary;
    }

    private function loadPaymentInboundMasters($company_id, $user_id): array
    {
        global $callConfig;
        $application_userssql = TallyConstants::SEL_APP_USERS . $company_id . TallyConstants::AND_ID_APOS . $user_id . TallyConstants::AND_IS_ACTIVE_0_UNQUOTED;
        $application_usersres = $callConfig->getRow($application_userssql);

        $chart_of_accounts = array();
        $chart_of_accounts_name = array();
        foreach ($callConfig->getAllRows(TallyConstants::SEL_COA . $company_id . TallyConstants::ALLOW_POSTING_0) as $cca) {
            $chart_of_accounts_name[$cca->account_name] = $cca;
            $chart_of_accounts[$cca->account_code] = $cca;
        }

        $customers = array();
        $customerssql = "SELECT vtm.vendor_id, vtm.party_name, vend.id, vend.company, vend.account_code FROM vendor_tally_mappings vtm, vendors vend WHERE vtm.company_id = " . $company_id . " AND vtm.company_id = vend.company_id AND vtm.vendor_id = vend.id AND vtm.is_active = vend.is_active AND vtm.is_active = 0";
        foreach ($callConfig->getAllRows($customerssql) as $custre) {
            $customers[$custre->party_name] = $custre;
        }

        $bank_accounts = array();
        $banckaccountsql = "SELECT id, account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '" . $company_id . TallyConstants::AND_IS_ACTIVE_0_NO_SPACE;
        foreach ($callConfig->getAllRows($banckaccountsql) as $payres) {
            $bank_accounts[$payres->account_code] = (array)$payres;
        }

        $purchase_invoices = array();
        $sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id FROM purchase_invoices WHERE company_id = '" . $company_id . TallyConstants::AND_IS_ACTIVE_0_NO_SPACE;
        foreach ($callConfig->getAllRows($sales_invoicessql) as $si) {
            $purchase_invoices[$si->transaction_number] = (array)$si;
        }

        $receipt_allocations = array();
        $receipt_allocationssql = "SELECT id, payment_id, transaction_no AS bill_transaction_no , total_paid_amonut FROM payment_allocations WHERE company_id='" . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        foreach ($callConfig->getAllRows($receipt_allocationssql) as $recalcpt) {
            $receipt_allocations[$recalcpt->payment_id] = $recalcpt;
        }

        $receipts = array();
        $receiptssql = "SELECT id, transaction_no, amount, unallocated_amount, allocated_amount, paid_amonut, tds_amount FROM payments WHERE company_id='" . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        foreach ($callConfig->getAllRows($receiptssql) as $recpt) {
            $recpt->bll_details = $receipt_allocations[$recpt->id] ?? null;
            $receipts[$recpt->transaction_no] = $recpt;
        }

        return [$application_usersres, $chart_of_accounts, $chart_of_accounts_name, $customers, $bank_accounts, $purchase_invoices, $receipts];
    }

    private function processSinglePaymentHit($apihitre, $company_id, $checkstatus, $get, array $masters): array
    {
        global $callConfig;
        list($application_usersres, $chart_of_accounts, $chart_of_accounts_name, $customers, $bank_accounts, $purchase_invoices, $receipts) = $masters;
        $valuenotavailablefieldary = array();
        $stringary = array();

        if (isset($receipts[$apihitre['VOUCHERNUMBER']])) {
            $stringary['status'] = 1062;
            $stringary['query'] = '';
            $stringary['error'] = TallyConstants::DUPLICATE_ENTRY;
            $apihitre['response'] = $stringary;
            return $apihitre;
        }

        $recepttotalamt = 0;
        $party_id = array();
        $payment_date = $this->dateconvertdbformat($apihitre['InvDate']);
        $arychunks = array_chunk($apihitre['Ledger_Entries'], 2);
        $payment_type = "cash";
        $bank_account_id = $account_code = "";
        $receptaccountname = "";

        foreach ($arychunks as $partydetailandtransdetail) {
            $recepttotalamt += $partydetailandtransdetail[0]['AMOUNT'] ?? 0;
            $receptaccountname = $partydetailandtransdetail[1]['Ledger_Name'] ?? '';
            $pName = $partydetailandtransdetail[0]['Party_Name'] ?? '';
            if (isset($customers[$pName])) {
                $custid = $customers[$pName]->id;
                $party_id[$custid] = $custid;
            }
        }

        if (isset($chart_of_accounts_name[$receptaccountname])) {
            $payment_account_details = $chart_of_accounts_name[$receptaccountname];
            if (isset($bank_accounts[$payment_account_details->account_code])) {
                $bankaccountdetails = $bank_accounts[$payment_account_details->account_code];
                $payment_type = "bank";
                $bank_account_id = $bankaccountdetails['id'];
                $account_code = $bankaccountdetails['account_code'];
            }
        }

        $paymentarray = array(
            'company_id' => $company_id,
            'transaction_type' => 'customers',
            'transaction_no' => '',
            'payment_date' => $payment_date,
            'party_id' => json_encode($party_id),
            'payment_type' => $payment_type,
            'bank_account_id' => $bank_account_id,
            'account_code' => $account_code,
            'amount' => $recepttotalamt,
            'ref_no' => '',
            'due_date' => $payment_date,
            'unallocated_amount' => 0,
            'allocated_amount' => $recepttotalamt,
            'remarks' => '',
            'originator' => 1,
            'is_active' => '0',
            'current_status' => '199',
            'paid_amonut' => $recepttotalamt,
            'tds_amount' => 0,
            'payment_process' => 'NP',
            'created_by' => 1,
            'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS)
        );

        $paymentallocationarrayre = array();
        foreach ($arychunks as $partydetailandtransdetail) {
            $billNo = $partydetailandtransdetail[0]['Bill_Details']['Bill_NO'] ?? '';
            $transaction_details = $purchase_invoices[$billNo] ?? array('id' => 0, 'transaction_number' => '', 'party_id' => 0);
            $paymentallocationarrayre[] = array(
                'company_id' => $company_id,
                'payment_id' => 0,
                'transaction_table' => 'purchase_invoices',
                'transaction_id' => $transaction_details['id'],
                'transaction_no' => $transaction_details['transaction_number'],
                'total_amount' => $partydetailandtransdetail[0]['AMOUNT'] ?? 0,
                'pending_amount' => 0,
                'paid_amonut' => $partydetailandtransdetail[0]['AMOUNT'] ?? 0,
                'is_active' => '0',
                'current_status' => '0',
                'created_by' => 1,
                'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS),
                'tds_amount' => 0,
                'total_paid_amonut' => $partydetailandtransdetail[0]['AMOUNT'] ?? 0,
                'total_tax_split_details' => '',
                'party_id' => $transaction_details['party_id']
            );
        }

        $popisosichildtablerequirdary = array('transaction_table', 'transaction_id', 'transaction_no');
        $popisosimaintablerequirdary = array('party_id', 'payment_type', 'amount', 'cost_center', 'location_id');

        foreach ((array)$application_usersres as $fieldkey => $fieldval) {
            if (in_array($fieldkey, $popisosimaintablerequirdary) && $fieldval == "") {
                $valuenotavailablefieldary[$fieldkey] = $fieldval;
            }
        }
        foreach ($paymentarray as $fieldkey => $fieldval) {
            if (in_array($fieldkey, $popisosimaintablerequirdary) && $fieldval == "") {
                $valuenotavailablefieldary[$fieldkey] = $fieldval;
            }
        }
        foreach ($paymentallocationarrayre as $itemtble) {
            foreach ($itemtble as $fieldkey => $fieldval) {
                if (in_array($fieldkey, $popisosichildtablerequirdary) && $fieldval == "") {
                    $valuenotavailablefieldary[$fieldkey] = $fieldval;
                }
            }
        }

        $res = array();
        $payallocationres = array();
        if ($checkstatus == "S" && count($valuenotavailablefieldary) == 0) {
            $transaction_no = $this->toGetApplicationSerialNumber('receipts');
            $paymentarray['transaction_no'] = $transaction_no;
            $res = $callConfig->insertRecord('receipts', $paymentarray);
            $receipt_id = $res['LASTID'] ?? 0;
            if (($res['status'] ?? '') == 'sucess') {
                foreach ($paymentallocationarrayre as $paymentallocationarray) {
                    $paymentallocationarray['receipt_id'] = $receipt_id;
                    $payallocationres[] = $callConfig->insertRecord('receipt_allocations', $paymentallocationarray);
                }
                $stringary['jvquery'] = $this->insertpaymentstojv($receipt_id, $get, 'receipts');
            }
        }

        $stringary['transaction_item_res'] = $payallocationres;
        $stringary['transaction_item'] = $paymentallocationarrayre;
        $stringary['status'] = 200;
        $stringary['query'] = '';
        $stringary['transaction'] = $paymentarray;
        $stringary['transaction_res'] = $res;
        $stringary['error'] = 'success';

        $apihitre['response'] = $stringary;
        return $apihitre;
    }

    public function tallyAPIReceivePayments($get, $post, $api_hitsres, $apimastersres)
    {
        $checkstatus = !empty($post['checkstatus']) ? $post['checkstatus'] : ($get['checkstatus'] ?? '');
        $company_id = $get['company_id'];
        $masters = $this->loadPaymentInboundMasters($company_id, $get['user_id']);
        $api_code = $apimastersres->api_code;
        $api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details), 1) ?: array();
        $responsary = array();

        if (!empty($api_hit_api_response_details[$api_code])) {
            foreach ($api_hit_api_response_details[$api_code] as $apihitre) {
                $responsary[] = $this->processSinglePaymentHit($apihitre, $company_id, $checkstatus, $get, $masters);
            }
        }
        return array('responsary' => $responsary);
    }
    private function loadDebitCreditMasters($company_id, $application_table, $user_id): array
    {
        global $callConfig;
        $application_userssql = TallyConstants::SEL_APP_USERS . $company_id . TallyConstants::AND_ID_APOS . $user_id . TallyConstants::AND_IS_ACTIVE_0_UNQUOTED;
        $application_usersres = $callConfig->getRow($application_userssql);

        $dmtsql = TallyConstants::SEL_DMT . $company_id . TallyConstants::AND_APP_TABLE . $application_table . "' AND item_table !='' AND tax_table !='' AND is_active = 0";
        $dmtres = $callConfig->getRow($dmtsql);

        $chart_of_accounts = array();
        $chart_of_accounts_name = array();
        foreach ($callConfig->getAllRows(TallyConstants::SEL_COA . $company_id . TallyConstants::ALLOW_POSTING_0) as $cca) {
            $chart_of_accounts_name[$cca->account_name] = $cca;
            $chart_of_accounts[$cca->account_code] = $cca;
        }

        $parties = array();
        $customerssql = "SELECT ctm.customer_id, ctm.party_name, cust.id, cust.company, cust.account_code FROM customer_tally_mappings ctm, customers cust WHERE ctm.company_id = " . $company_id . " AND ctm.company_id = cust.company_id AND ctm.customer_id = cust.id AND ctm.is_active = cust.is_active AND ctm.is_active = 0";
        foreach ($callConfig->getAllRows($customerssql) as $custre) {
            $parties[$custre->party_name] = $custre;
        }
        $vendorsql = "SELECT vtm.vendor_id, vtm.party_name, vend.id, vend.company, vend.account_code FROM vendor_tally_mappings vtm, vendors vend WHERE vtm.company_id = " . $company_id . " AND vtm.company_id = vend.company_id AND vtm.vendor_id = vend.id AND vtm.is_active = vend.is_active AND vtm.is_active = 0";
        foreach ($callConfig->getAllRows($vendorsql) as $vend) {
            $parties[$vend->party_name] = $vend;
        }

        $transaction = array();
        $transactionssql = "SELECT id, transaction_date, transaction_number, party_name, party_address, place_of_supply, party_phone, party_email FROM " . $application_table . TallyConstants::WHERE_COMPANY_ID . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        foreach ($callConfig->getAllRows($transactionssql) as $recpt) {
            $transaction[$recpt->transaction_number] = $recpt;
        }

        return [$application_usersres, $dmtres, $chart_of_accounts, $chart_of_accounts_name, $parties, $transaction];
    }

    private function processSingleDebitCreditHit($apihitre, $company_id, $checkstatus, $get, array $masters): array
    {
        global $callConfig;
        list($application_usersres, $dmtres, $chart_of_accounts, $chart_of_accounts_name, $parties, $transaction) = $masters;
        $valuenotavailablefieldary = array();
        $stringary = array();

        if (isset($transaction[$apihitre['VOUCHERNUMBER']])) {
            $stringary['status'] = 1062;
            $stringary['query'] = '';
            $stringary['error'] = TallyConstants::DUPLICATE_ENTRY;
            $apihitre['response'] = $stringary;
            $apihitre['valuenotavailablefieldary'] = $valuenotavailablefieldary;
            return $apihitre;
        }

        $party_id = $parties[$apihitre['PARTYNAME']]->id ?? 0;
        $party_name = $parties[$apihitre['PARTYNAME']]->company ?? '';
        $mobile_number = ($apihitre['mobile_number'] ?? '') != "N/A" ? ($apihitre['mobile_number'] ?? '') : "";
        $email_address = ($apihitre['email_address'] ?? '') != "N/A" ? ($apihitre['email_address'] ?? '') : "";
        $maintotal_amount = 0;
        foreach (($apihitre['Item_Entries'] ?? array()) as $itmss) {
            $maintotal_amount += $itmss['AMOUNT'] ?? 0;
        }

        $maintransactionary = array(
            'company_id' => $company_id,
            'transaction_number' => $apihitre['VOUCHERNUMBER'],
            'transaction_date' => $this->dateconvertdbformat($apihitre['InvDate']),
            'cost_center_id' => $application_usersres->cost_center ?? null,
            'party_name' => $party_name,
            'party_id' => $party_id,
            'party_address' => $apihitre['STATE'] ?? '',
            'supplier_state_id' => '',
            'place_of_supply' => '',
            'party_phone' => $mobile_number,
            'party_email' => $email_address,
            'amount' => $maintotal_amount,
            'tax_amount' => 0,
            'total_amount' => $maintotal_amount,
            'pending_amount' => $maintotal_amount,
            'transaction_location_id' => $application_usersres->location_id ?? null,
            'transaction_warehouse_id' => $application_usersres->default_warehouse_id ?? null,
            'originator_currency_id' => $application_usersres->default_currency_id ?? null,
            'currency_id' => '',
            'currency_rate' => '1',
            'originator' => $get['user_id'],
            'originator_state_id' => $application_usersres->default_state_id ?? null,
            'payment_terms' => '',
            'payment_term_history' => '',
            'due_date' => $this->dateconvertdbformat($apihitre['InvDate']),
            'tax_state' => 'I',
            'is_active' => '0',
            'current_status' => '199',
            'created_by' => 1,
            'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS)
        );

        $transactionitemsql = array();
        foreach (($apihitre['Item_Entries'] ?? array()) as $itementry) {
            $child_total_amount = $itementry['AMOUNT'] ?? 0;
            $stockItem = $itementry['Stock_Item'] ?? '';
            $account_code = isset($chart_of_accounts_name[$stockItem]) ? $chart_of_accounts_name[$stockItem]->account_code : '';
            $account_name = isset($chart_of_accounts_name[$stockItem]) ? $chart_of_accounts_name[$stockItem]->account_name : '';

            $transactionitemsql[] = array(
                'company_id' => $company_id,
                'transaction_id' => 0,
                'item_id' => 0,
                'account_code' => $account_code,
                'item_name' => $account_name,
                'description' => $account_name,
                'purchase_uom' => 'UN',
                'item_quantity' => 1,
                'item_price' => $child_total_amount,
                'item_amount' => $child_total_amount,
                'tax_amount' => 0,
                'discount_amount' => '0',
                'total_amount' => $child_total_amount,
                'reason_id' => '',
                'item_st_cost' => '0',
                'item_fifo_cost' => '0',
                'item_lifo_cost' => '0',
                'item_wa_cost' => '0',
                'approved_quantity' => 1,
                'transaction_type' => '',
                'transaction_qty' => 1,
                'pending_qty' => 1,
                'item_details' => '',
                'tax_deducted_source_id' => '',
                'tds_amount' => '0',
                'is_active' => '0',
                'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS),
                'created_by' => '1'
            );
        }

        $popisosichildtablerequirdary = array('account_code', 'item_name');
        $popisosimaintablerequirdary = array('cost_center_id', 'party_id', 'transaction_location_id', 'originator_currency_id', 'originator_state_id');

        foreach ($maintransactionary as $fieldkey => $fieldval) {
            if (in_array($fieldkey, $popisosimaintablerequirdary) && $fieldval == "") {
                $valuenotavailablefieldary[$fieldkey] = $fieldval;
            }
        }
        foreach ($transactionitemsql as $itemtble) {
            foreach ($itemtble as $fieldkey => $fieldval) {
                if (in_array($fieldkey, $popisosichildtablerequirdary) && $fieldval == "") {
                    $valuenotavailablefieldary[$fieldkey] = $fieldval;
                }
            }
        }

        $maintransactionaryres = array();
        $transactionitemres = array();
        $transaction_id = "";

        if ($checkstatus == "S" && count($valuenotavailablefieldary) == 0 && !empty($dmtres->application_table_name)) {
            $maintransactionaryres = $callConfig->insertRecord($dmtres->application_table_name, $maintransactionary);
            $transaction_id = $maintransactionaryres['LASTID'] ?? "";
            if (($maintransactionaryres['status'] ?? '') == 'sucess') {
                foreach ($transactionitemsql as $transitemarry) {
                    $transitemarry['transaction_id'] = $transaction_id;
                    $transactionitemres[] = $callConfig->insertRecord($dmtres->item_table, $transitemarry);
                }
                if ($transaction_id != "") {
                    $get['transaction_table'] = $dmtres->application_table_name;
                    $this->updateTransactionItemsQty($get, $apihitre, "-");
                    $stringary['payments_result'] = $this->tocreatejounalvoucherbaseallcases($dmtres->application_table_name, $transaction_id);
                }
            }
        }

        $stringary['transaction_item_res'] = $transactionitemres;
        $stringary['transaction_item'] = $transactionitemsql;
        $stringary['status'] = 200;
        $stringary['query'] = array();
        $stringary['transaction'] = $maintransactionary;
        $stringary['transaction_res'] = $maintransactionaryres;
        $stringary['error'] = 'success';

        $apihitre['response'] = $stringary;
        $apihitre['valuenotavailablefieldary'] = $valuenotavailablefieldary;
        return $apihitre;
    }

    public function tallyAPIDebitANDCreditNoteTransactions($get, $post, $api_hitsres, $apimastersres)
    {
        $checkstatus = !empty($post['checkstatus']) ? $post['checkstatus'] : ($get['checkstatus'] ?? '');
        $company_id = $get['company_id'];
        $api_code = $apimastersres->api_code;
        $application_table = $apimastersres->application_table;
        $api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details), 1) ?: array();
        $responsary = array();

        $masters = $this->loadDebitCreditMasters($company_id, $application_table, $get['user_id']);

        if (!empty($api_hit_api_response_details[$api_code])) {
            foreach ($api_hit_api_response_details[$api_code] as $apihitre) {
                $responsary[] = $this->processSingleDebitCreditHit($apihitre, $company_id, $checkstatus, $get, $masters);
            }
        }
        return $responsary;
    }
    private function loadReceiveTransactionMasters($company_id, $application_table, $user_id): array
    {
        global $callConfig;
        $application_userssql = TallyConstants::SEL_APP_USERS . $company_id . TallyConstants::AND_ID_APOS . $user_id . TallyConstants::AND_IS_ACTIVE_0_UNQUOTED;
        $application_usersres = $callConfig->getRow($application_userssql);

        $dmtsql = TallyConstants::SEL_DMT . $company_id . TallyConstants::AND_APP_TABLE . $application_table . "' AND item_table !='' AND tax_table !='' AND is_active = 0";
        $dmtres = $callConfig->getRow($dmtsql);

        $items = array();
        $itemssql = "SELECT itm.id, itm.sku, itm.item_name, itm.item_description, itm.purchase_uom, itm.sale_uom, itm.hsn_and_sac_code_id, itm.inventory_asset_account, hsnsac.gst, hsnsac.code, hsnsac.tax_authority_id FROM items itm LEFT JOIN hsn_and_sac_codes hsnsac ON itm.company_id = hsnsac.company_id AND itm.hsn_and_sac_code_id = hsnsac.id AND itm.is_active = hsnsac.is_active WHERE itm.company_id='" . $company_id . "' AND itm.is_active = '0'";
        foreach ($callConfig->getAllRows($itemssql) as $itmre) {
            $items[$itmre->sku] = $itmre;
        }

        $transaction = array();
        $transactionssql = "SELECT id, transaction_date, transaction_number, party_name, party_address, place_of_supply, party_phone, party_email FROM " . $application_table . TallyConstants::WHERE_COMPANY_ID . $company_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        foreach ($callConfig->getAllRows($transactionssql) as $recpt) {
            $transaction[$recpt->transaction_number] = $recpt;
        }

        return [$application_usersres, $dmtres, $items, $transaction];
    }

    private function processSingleReceiveTransactionHit($apihitre, $company_id, $checkstatus, $get, $popisosimaintablerequirdary, array $masters): array
    {
        global $callConfig;
        list($application_usersres, $dmtres, $items, $transaction) = $masters;
        $valuenotavailablefieldary = array();
        $stringary = array();
        $popisosichildtablerequirdary = array('item_id', 'item_name', 'purchase_uom');

        if (isset($transaction[$apihitre['VOUCHERNUMBER']])) {
            $stringary['status'] = 1062;
            $stringary['query'] = '';
            $stringary['error'] = TallyConstants::DUPLICATE_ENTRY;
            $apihitre['response'] = $stringary;
            $apihitre['valuenotavailablefieldary'] = $valuenotavailablefieldary;
            return $apihitre;
        }

        $party_id = $apihitre['PARTYNAME'] ?? '';
        $place_of_supply = $apihitre['PLACEOFSUPPLY'] ?? '';
        $supplier_state_id = $apihitre['STATE'] ?? '';
        $mobile_number = ($apihitre['mobile_number'] ?? '') != "N/A" ? ($apihitre['mobile_number'] ?? '') : "";
        $email_address = ($apihitre['email_address'] ?? '') != "N/A" ? ($apihitre['email_address'] ?? '') : "";
        $tallywiithgstamount = str_replace(",", "", $apihitre['Voucher_Amount'] ?? '0');
        $maintotal_amount = $mainamount = $maintax_amount_total = 0;

        foreach (($apihitre['Item_Entries'] ?? array()) as $itmss) {
            $partNo = $itmss['part_no'] ?? '';
            $amt = $itmss['AMOUNT'] ?? 0;
            if (isset($items[$partNo]) && $items[$partNo]->gst != "") {
                $tax_amount = $amt * ($items[$partNo]->gst / 100);
                $maintax_amount_total += $tax_amount;
                $maintotal_amount += $amt + $tax_amount;
            } else {
                $maintotal_amount += $amt;
            }
            $mainamount += $amt;
        }

        $originator_state_id = $application_usersres->default_state_id ?? '';
        $tax_state = ($supplier_state_id == $originator_state_id) ? "S" : "I";

        $maintransactionary = array(
            'company_id' => $company_id,
            'transaction_number' => $apihitre['VOUCHERNUMBER'],
            'transaction_date' => $this->dateconvertdbformat($apihitre['InvDate']),
            'cost_center_id' => $application_usersres->cost_center ?? null,
            'party_name' => $apihitre['PARTYNAME'] ?? '',
            'party_id' => $party_id,
            'party_address' => $apihitre['STATE'] ?? '',
            'supplier_state_id' => $supplier_state_id,
            'place_of_supply' => $place_of_supply,
            'party_phone' => $mobile_number,
            'party_email' => $email_address,
            'amount' => $mainamount,
            'tax_amount' => $maintax_amount_total,
            'total_amount' => $maintotal_amount,
            'pending_amount' => $maintotal_amount,
            'transaction_location_id' => $application_usersres->location_id ?? null,
            'transaction_warehouse_id' => $application_usersres->default_warehouse_id ?? null,
            'originator_currency_id' => $application_usersres->default_currency_id ?? null,
            'currency_id' => '',
            'currency_rate' => '1',
            'originator' => $get['user_id'],
            'originator_state_id' => $originator_state_id,
            'payment_terms' => '',
            'payment_term_history' => '',
            'due_date' => $this->dateconvertdbformat($apihitre['InvDate']),
            'tax_state' => $tax_state,
            'is_active' => '0',
            'current_status' => '199',
            'created_by' => 1,
            'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS)
        );

        $transactionitemsql = array();
        $transactionitemtaxary = array();

        foreach (($apihitre['Item_Entries'] ?? array()) as $itementry) {
            $partNo = $itementry['part_no'] ?? '';
            $amt = $itementry['AMOUNT'] ?? 0;
            $tax_amount = 0;
            $total_amount = $amt;
            $item_id = $account_code = $item_name = $description = $purchase_uom = "";

            if (isset($items[$partNo])) {
                $item_id = $items[$partNo]->id;
                $account_code = $items[$partNo]->inventory_asset_account;
                $item_name = $items[$partNo]->item_name;
                $description = $items[$partNo]->item_description;
                $purchase_uom = $items[$partNo]->purchase_uom;
                $tax_percentage = $items[$partNo]->gst;
                if ($tax_percentage != "") {
                    $tax_amount = $amt * ($tax_percentage / 100);
                    $total_amount += $tax_amount;
                    $transactionitemtaxary[] = array(
                        'company_id' => $company_id,
                        'transaction_id' => 0,
                        'transaction_item_id' => $item_id,
                        'tax_name' => $items[$partNo]->code,
                        'tax_value' => $tax_percentage,
                        'tax_id' => $items[$partNo]->hsn_and_sac_code_id,
                        'amount' => $amt,
                        'tax_amount' => $tax_amount,
                        'total_amount' => $total_amount,
                        'tax_type' => $tax_state,
                        'tax_split_details' => '',
                        'is_active' => 0,
                        'current_status' => 0,
                        'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS),
                        'created_by' => 0
                    );
                }
            }

            $transactionitemsql[] = array(
                'company_id' => $company_id,
                'transaction_id' => 0,
                'item_id' => $item_id,
                'account_code' => $account_code,
                'item_name' => $item_name,
                'description' => $description,
                'purchase_uom' => $purchase_uom,
                'item_quantity' => $itementry['QUANTITY'] ?? 1,
                'item_price' => $itementry['RATE'] ?? 0,
                'item_amount' => $amt,
                'tax_amount' => $tax_amount,
                'discount_amount' => '0',
                'total_amount' => $total_amount,
                'reason_id' => '',
                'item_st_cost' => '0',
                'item_fifo_cost' => '0',
                'item_lifo_cost' => '0',
                'item_wa_cost' => '0',
                'approved_quantity' => $itementry['QUANTITY'] ?? 1,
                'transaction_type' => '',
                'transaction_qty' => $itementry['QUANTITY'] ?? 1,
                'pending_qty' => $itementry['QUANTITY'] ?? 1,
                'item_details' => '',
                'tax_deducted_source_id' => '',
                'tds_amount' => '0',
                'is_active' => '0',
                'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS),
                'created_by' => '1'
            );
        }

        if (round((float)$tallywiithgstamount, 2) != round($maintotal_amount, 2)) {
            $valuenotavailablefieldary['total_amount'] = $maintotal_amount;
        }
        foreach ($maintransactionary as $fieldkey => $fieldval) {
            if (in_array($fieldkey, $popisosimaintablerequirdary) && $fieldval == "") {
                $valuenotavailablefieldary[$fieldkey] = $fieldval;
            }
        }
        foreach ($transactionitemsql as $itemtble) {
            foreach ($itemtble as $fieldkey => $fieldval) {
                if (in_array($fieldkey, $popisosichildtablerequirdary) && $fieldval == "") {
                    $valuenotavailablefieldary[$fieldkey] = $fieldval;
                }
            }
        }

        $maintransactionaryres = array();
        $transactionitemres = array();
        $transactionitemtaxres = array();
        $transaction_id = "";

        if ($checkstatus == "S" && count($valuenotavailablefieldary) == 0 && !empty($dmtres->application_table_name)) {
            $maintransactionaryres = $callConfig->insertRecord($dmtres->application_table_name, $maintransactionary);
            $transaction_id = $maintransactionaryres['LASTID'] ?? "";
            if (($maintransactionaryres['status'] ?? '') == 'sucess') {
                foreach ($transactionitemsql as $transitemarry) {
                    $transitemarry['transaction_id'] = $transaction_id;
                    $transactionitemres[] = $callConfig->insertRecord($dmtres->item_table, $transitemarry);
                }
                foreach ($transactionitemtaxary as $transitemtaxarry) {
                    $transitemtaxarry['transaction_id'] = $transaction_id;
                    $transactionitemtaxres[] = $callConfig->insertRecord($dmtres->tax_table, $transitemtaxarry);
                }
                $get['transaction_table'] = $dmtres->application_table_name;
                $this->updateTransactionItemsQty($get, $apihitre, "-");
                $stringary['payments_result'] = $this->tocreatejounalvoucherbaseallcases($dmtres->application_table_name, $transaction_id);
            }
        }

        $stringary['transaction_item_res'] = $transactionitemres;
        $stringary['transaction_item'] = $transactionitemsql;
        $stringary['transaction_item_tax_ary'] = $transactionitemtaxary;
        $stringary['transaction_item_tax'] = $transactionitemtaxres;
        $stringary['status'] = 200;
        $stringary['query'] = '';
        $stringary['transaction'] = $maintransactionary;
        $stringary['transaction_res'] = $maintransactionaryres;
        $stringary['error'] = 'success';

        $apihitre['response'] = $stringary;
        $apihitre['valuenotavailablefieldary'] = $valuenotavailablefieldary;
        return $apihitre;
    }

    public function tallyAPIReceiveTransactions($get, $post, $api_hitsres, $apimastersres)
    {
        $checkstatus = !empty($post['checkstatus']) ? $post['checkstatus'] : ($get['checkstatus'] ?? '');
        $company_id = $get['company_id'];
        $api_code = $apimastersres->api_code;
        $application_table = $apimastersres->application_table;

        $withoutwarehouse = array('RECEIVEPURCHASEORDERS', 'RECEIVEPURCHASEINVOICES', 'RECEIVESALESINVOICES', 'RECEIVESALESORDERS');
        $withwarehouse = array('RECEIVEGOODSRECEIPTNOTES', 'RECEIVEDELIVERYORDERS');

        $popisosimaintablerequirdary = in_array($api_code, $withoutwarehouse)
            ? array('cost_center_id', 'party_id', 'transaction_location_id', 'originator_currency_id', 'originator_state_id')
            : (in_array($api_code, $withwarehouse)
                ? array('cost_center_id', 'party_id', 'transaction_location_id', 'transaction_warehouse_id', 'originator_currency_id', 'originator_state_id')
                : array());

        $masters = $this->loadReceiveTransactionMasters($company_id, $application_table, $get['user_id']);
        $api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details), 1) ?: array();
        $responsary = array();

        if (!empty($api_hit_api_response_details[$api_code])) {
            foreach ($api_hit_api_response_details[$api_code] as $apihitre) {
                $responsary[] = $this->processSingleReceiveTransactionHit($apihitre, $company_id, $checkstatus, $get, $popisosimaintablerequirdary, $masters);
            }
        }
        return $responsary;
    }

    private function syncDynamicInboundApiData($get, $apimastersres, $api_hitsres, $api_hit_id, $checkstatus): array
    {
        global $callConfig;
        $fieldkeywithvalue = array();
        $api_code = $apimastersres->api_code;
        $api_mastersr_api_response_details = json_decode($apimastersres->api_response_details ?? '', 1) ?: array();
        $api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details ?? ''), 1) ?: array();
        $responsary = array();

        foreach (($api_hit_api_response_details[$api_code] ?? array()) as $apihitre) {
            $sqlfieldstring = $sqlvaluestring = $getstringlist = $alreadyavilablecheck = "";
            $alreadyavilablesql = "SELECT id FROM " . $apimastersres->application_table . " WHERE ";
            $getstringlist = "INSERT INTO " . $apimastersres->application_table . " (";

            foreach ($api_mastersr_api_response_details as $valuefield => $tablefield) {
                if (!empty($tablefield['application_table_field'])) {
                    $sqlfieldstring .= "`" . $tablefield['application_table_field'] . "`,";
                }
            }
            $sqlfieldstring .= "`is_active`,`current_status`";
            $getstringlist .= $sqlfieldstring . ") VALUES (";

            foreach ($api_mastersr_api_response_details as $valuefield => $tablefield) {
                if (!empty($tablefield['application_table_field'])) {
                    $tfField = $tablefield['application_table_field'];
                    $hitVal = $apihitre[$valuefield] ?? '';
                    $valToUse = (!empty($fieldkeywithvalue[$tfField][$hitVal])) ? $fieldkeywithvalue[$tfField][$hitVal] : $hitVal;
                    $sqlvaluestring .= "'" . $valToUse . "',";
                    if (($tablefield['uniqueness'] ?? '') == 'Y') {
                        $alreadyavilablecheck = "Y";
                        $alreadyavilablesql .= " " . $tfField . "='" . $valToUse . TallyConstants::APOS_AND;
                    }
                }
            }

            if ($alreadyavilablecheck == "Y") {
                $alreadyavilablesql .= " is_active = 0";
                $alreadyavilableresult = $callConfig->getRow($alreadyavilablesql);
            } else {
                $alreadyavilableresult = (object) array('id' => '');
            }

            $sqlvaluestring .= "0,0";
            $getstringlist .= $sqlvaluestring . ")";

            $stringary = array();
            if (!empty($alreadyavilableresult->id)) {
                $stringary['status'] = 1062;
                $stringary['query'] = $getstringlist;
                $stringary['error'] = TallyConstants::DUPLICATE_ENTRY;
            } else {
                if ($checkstatus == "S") {
                    $savgdsc = $callConfig->executeQuery($getstringlist);
                    if (($savgdsc['status'] ?? '') == 'sucess') {
                        $stringary['status'] = 200;
                        $stringary['query'] = $getstringlist;
                        $stringary['error'] = 'success';
                    } else {
                        $stringary['status'] = $savgdsc['error_no'] ?? 500;
                        $stringary['query'] = $getstringlist;
                        $stringary['error'] = $savgdsc['message'] ?? 'Query failed';
                    }
                } else {
                    $stringary = $callConfig->checkExecuteQueryString($getstringlist);
                }
            }
            $apihitre['response'] = $stringary;
            $responsary[] = $apihitre;
        }

        if ($checkstatus == "S") {
            $api_hits_sync = array(
                'company_id' => $get['company_id'],
                'api_hit_id' => $api_hit_id,
                'originator' => $get['user_id'],
                'application_table' => $apimastersres->application_table,
                'sync_datetime' => date(TallyConstants::DATE_FORMAT_YMD_HIS),
                'sync_details' => urlencode(json_encode((array)$responsary)),
                'is_active' => '0',
                'current_status' => '0',
                'created_by' => $get['user_id'],
                'created_date' => date(TallyConstants::DATE_FORMAT_YMD_HIS)
            );
            $callConfig->insertRecord('api_hit_sync_with_db', $api_hits_sync);
        }
        return $responsary;
    }

    public function checkAPIDataValidateOrNot($get, $post)
    {
        global $callConfig;
        $return = array('post' => $post);
        $checkstatus = !empty($post['checkstatus']) ? $post['checkstatus'] : ($get['checkstatus'] ?? '');
        $api_hit_id = $get['api_hit_id'] ?? 0;
        $api_hitssql = TallyConstants::SEL_API_HITS . $get['company_id'] . TallyConstants::AND_ID_APOS . $api_hit_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        $api_hitsres = $callConfig->getRow($api_hitssql);
        $return['api_hits'] = $api_hitsres;

        if (empty($api_hitsres->api_master_id)) {
            $return['status'] = 403;
            $return['result'] = array();
            return $return;
        }

        $apimasterssql = TallyConstants::SEL_API_MASTERS . $get['company_id'] . TallyConstants::AND_ID_APOS . $api_hitsres->api_master_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        $apimastersres = $callConfig->getRow($apimasterssql);
        $return['api_masters'] = $apimastersres;

        if ($apimastersres->api_type == "S") {
            $return['status'] = 4040404;
            $return['result'] = array();
            return $return;
        }

        $api_code = $apimastersres->api_code;
        $transactionCodes = array('RECEIVEPURCHASEORDERS', 'RECEIVEPURCHASEINVOICES', 'RECEIVEGOODSRECEIPTNOTES', 'RECEIVESALESINVOICES', 'RECEIVESALESORDERS', 'RECEIVEDELIVERYORDERS');

        if ($api_code == "RECEIVERECEIPTS") {
            $responsary = $this->tallyAPIReceiveReceipts($get, $post, $api_hitsres, $apimastersres);
        } elseif ($api_code == "SENDPAYMENTS") {
            $responsary = $this->tallyAPIReceivePayments($get, $post, $api_hitsres, $apimastersres);
        } elseif (in_array($api_code, $transactionCodes)) {
            $responsary = $this->tallyAPIReceiveTransactions($get, $post, $api_hitsres, $apimastersres);
        } elseif ($api_code == "RECEIVEDEBITNOTES" || $api_code == "RECEIVECREDITNOTES") {
            $responsary = $this->tallyAPIDebitANDCreditNoteTransactions($get, $post, $api_hitsres, $apimastersres);
        } else {
            $responsary = $this->syncDynamicInboundApiData($get, $apimastersres, $api_hitsres, $api_hit_id, $checkstatus);
        }

        $return['result'] = $responsary;
        return $return;
    }

    public function toSendItemsToTally($get)
    {
        global $callConfig;
        $query = "SELECT company_id, sku, item_name FROM items WHERE company_id =" . $get['company_id'] . TallyConstants::AND_IS_ACTIVE_0;
        $return['result'] = $callConfig->getAllRows($query);
        return $return;
    }

    public function tosendCostCentersToTally($get)
    {
        global $callConfig;
        $query = "SELECT company_id, cost_center_name FROM cost_centers WHERE company_id =" . $get['company_id'] . TallyConstants::AND_IS_ACTIVE_0;
        $return['result'] = $callConfig->getAllRows($query);
        return $return;
    }

    public function toSendUnitOfMeasurementsToTally($get)
    {
        global $callConfig;
        $query = "SELECT company_id, uom_short_code, uom_name FROM unit_of_measurements WHERE company_id =" . $get['company_id'] . TallyConstants::AND_IS_ACTIVE_0;
        $return['result'] = $callConfig->getAllRows($query);
        return $return;
    }

    public function toSendWarehousesToTally($get)
    {
        global $callConfig;
        $query = "SELECT company_id, warehouse_short_name, warehouse_name FROM warehouses WHERE company_id =" . $get['company_id'] . TallyConstants::AND_IS_ACTIVE_0;
        $return['result'] = $callConfig->getAllRows($query);
        return $return;
    }

    public function aPIDetailStoretoDatabase($get, $post)
    {
        global $callConfig;
        $return = array('post' => $post);
        $checkstatus = !empty($post['checkstatus']) ? $post['checkstatus'] : ($get['checkstatus'] ?? '');
        $api_hit_id = $get['api_hit_id'] ?? 0;
        $api_hitssql = TallyConstants::SEL_API_HITS . $get['company_id'] . TallyConstants::AND_ID_APOS . $api_hit_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        $api_hitsres = $callConfig->getRow($api_hitssql);
        $return['api_hits'] = $api_hitsres;

        if (empty($api_hitsres->api_master_id)) {
            $return['status'] = 403;
            $return['result'] = array();
            return $return;
        }

        $apimasterssql = TallyConstants::SEL_API_MASTERS . $get['company_id'] . TallyConstants::AND_ID_APOS . $api_hitsres->api_master_id . TallyConstants::AND_IS_ACTIVE_0_APOS;
        $apimastersres = $callConfig->getRow($apimasterssql);
        $return['api_masters'] = $apimastersres;

        if ($apimastersres->api_type == "S") {
            $return['status'] = 4040404;
            $return['result'] = array();
            return $return;
        }

        $responsary = $this->syncDynamicInboundApiData($get, $apimastersres, $api_hitsres, $api_hit_id, $checkstatus);
        $return['result'] = $responsary;
        return $return;
    }







    

    private function getFieldKeyWithValueMapping($displayattributesres, $application_table)
    {
        $fieldkeywithvalue = array();
        if ($displayattributesres && $displayattributesres->list_attributes != "") {
            $lists = json_decode($displayattributesres->list_attributes, 1);
            $type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
            foreach ($lists as $key => $value) {
                if (in_array($value['type'], (array)$type_array)) {
                    $tablefield = $this->getDyanamicSelectData($application_table, $key);
                    $source = ($tablefield['type'] != 'function') ? $tablefield['res'] : $tablefield;
                    $reverkeyvaluearay = array();
                    foreach ($source as $kkey => $valuu) {
                        $removewhinewline = preg_replace(TallyConstants::REGEX_NEWLINE, '', $valuu);
                        $reverkeyvaluearay[$removewhinewline] = $kkey;
                    }
                    $fieldkeywithvalue[$key] = $reverkeyvaluearay;
                }
            }
        }
        return $fieldkeywithvalue;
    }
}

class TallyClass extends userClass
{
    use TallyApiOutboundTrait;
    use TallyApiInboundTrait;

    public function __call($name, $arguments)
    {
        if (strcasecmp($name, 'tosenditem_categoriestotally') === 0) {
            return $this->toSendItemCategoriesToTally(...$arguments);
        }
        throw new \BadMethodCallException("Method {$name} does not exist");
    }
}
if (!class_exists('tallyClass', false)) {
    class_alias('TallyClass', 'tallyClass');
}
