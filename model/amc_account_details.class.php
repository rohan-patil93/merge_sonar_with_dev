<?php

class amcAccountDetailsClass extends userClass{
  function insertAmcAccountDetails($get, $post){
    global $callConfig;
    $return['post'] = $post;
    $dob = str_replace('/"', '-', $post['dob']);
    $dob =  date('Y-m-d', strtotime($dob));
    $aniversary = str_replace('/"', '-', $post['aniversary']);
    $aniversary =  date('Y-m-d', strtotime($aniversary));
    $insertary = array(
      'company_id' => $post['company_id'],
      'account_name' => $post['account_name'],
      'account_group_id' => $post['account_group_id'],
      'payble_or_reciveable' => $post['payble_or_reciveable'],
      'email_id' => $post['email_id'],
      'mobile_no' => $post['mobile_no'],
      'alternative_no' => $post['alternative_no'],
      'address_line1' => $post['address_line1'],
      'address_line2' => $post['address_line2'],
      'country_id' => $post['country_id'],
      'state_id' => $post['state_id'],
      'city_id' => $post['city_id'],
      'pin' => $post['pin'],
      'gst_no' => $post['gst_no'],
      'cst_no' => $post['cst_no'],
      'vat_no' => $post['vat_no'],
      'membership_card_no' => $post['membership_card_no'],
      'pan_no' => $post['pan_no'],
      'aadhaar_no' => $post['aadhaar_no'],
      'dob' => $dob,
      'aniversary' => $aniversary,
      'credit_limit' => $post['credit_limit'],
      'bill_limit' => $post['bill_limit'],
      'contact_person' => $post['contact_person'],
      'opening_balance' => $post['opening_balance'],
      'credit_days_for_sale' => $post['credit_days_for_sale'],
      'credit_days_for_purchase' => $post['credit_days_for_purchase'],
      'audit_upto' => $post['audit_upto'],
      'any_liense_no1' => $post['any_liense_no1'],
      'any_license_no2' => $post['any_license_no2'],
      'bank_name' => $post['bank_name'],
      'branch_name' => $post['branch_name'],
      'account_no' => $post['account_no'],
      'ifsc_code' => $post['ifsc_code']
    );
    if ($post['rid'] != "") {
      // echo $post['rid']; exit;
      $insertary['updated_date'] = date('Y-m-d h:i:s');
      $insertary['updated_by'] = $post['user_id'];
      // print_r($callConfig); exit;
      $res = $callConfig->updateRecord('amc_account_details', $insertary, 'id', $post['rid']);
      // echo "res: ".$res; exit;
      $return['res'] = $res;
      $customer_id = $post['rid'];
    } else {
      // $insertary['customer_since']=date('Y-m-d');
      $insertary['is_active'] = 0;
      $insertary['current_status'] = 0;
      ;
      $insertary['created_by'] = $post['user_id'];
      $insertary['created_date'] = date("Y-m-d H:i:s");
      $res = $callConfig->insertRecord('amc_account_details', $insertary);
      $return['res'] = $res;

      $customer_id = $res['LASTID'];
    }
    return $return;
  }
  function toafterapprovaltoupdatemanufirmorder($transaction_no, $get){
    global $callConfig;
    print_r($get); echo "transaction_no".$transaction_no; exit;
    /*$query = "SELECT * FROM manufacturing_forecast WHERE company_id=".$get['company_id']." AND id=".$transaction_no;
    $data = $callConfig->getRow($query);
    $upquery = "UPDATE manufacturing_forecast SET current_status =".ST_APPROVED." WHERE company_id=".$get['company_id']." AND year='".$data->year."' AND is_active=0";
    $exquery = $callConfig->executeQuery($upquery);*/
    return $exquery;
  }
  function getAmcAccountDetails($get){
    global $callConfig;
     //$res = $this->toafterapprovaltoupdatemanufirmorder($get['transaction_no'],$get);
    //print_r($get); print_r($res); exit;
    $return = array();
    $return['get'] = $get;
    if ($get['id'] != "") {
      $account_details_query = "SELECT * FROM amc_account_details WHERE id = '".$get['id']."' AND is_active = 0";
      $account_details = $callConfig->getRow($account_details_query);
      $return['amc_details'] = $account_details;
      // print_r($account_details); exit;
    } else {
      $amc_account_details = "SELECT * FROM  amc_account_details WHERE is_active =0";
      $amc_account_details = $callConfig->getAllRows($amc_account_details);
      $amc_details = array();
      foreach ($amc_account_details as $ascat) {
        $amc_details[$ascat->id] = $ascat;
      }
      $return['amc_details'] = $amc_details;
    }
    return $return;
  }
  function getAllDetails($get){
    global $callConfig;
    $return = array();
    $return['get'] = $get;
    $payble = 'PAYBLE';
    $general_code_details = "SELECT id, company_id, genc_code,genc_detail_code,genc_detail_name FROM  general_code_details WHERE is_active =0  AND 	genc_code='".$payble."'";
    $general_code_details = $callConfig->getAllRows($general_code_details);
    $gc_details = array();
    foreach ($general_code_details as $gcd) {
      $gc_details[$gcd->id] = $gcd;
    }
    $return['gc_details'] = $gc_details;

    $states_details = "SELECT id, company_id, country_name,other_code FROM  countries WHERE is_active =0 ";
    $states_details = $callConfig->getAllRows($states_details);
    $st_details = array();
    foreach ($states_details as $st) {
      $st_details[$st->id] = $st;
    }
    $return['st_details'] = $st_details;

    $account_group_details = "SELECT id, company_id, group_name, parent_group_id FROM  account_groups WHERE is_active =0";
    $account_group_details = $callConfig->getAllRows($account_group_details);
    $account_groups = array();
    foreach ($account_group_details as $agd) {
      $account_groups[$agd->id] = $agd;
    }
    $return['account_groups'] = $account_groups;
    return $return;
  }
  function getProductDetails($get){
    global $callConfig;
    $return = array();
    $return['get'] = $get;
    $general_code_details = "SELECT id, company_id, genc_code,genc_detail_code,genc_detail_name FROM  general_code_details WHERE is_active =0  AND  genc_code in ('AMCUNIT','YESNO','ITEMTYPE')";
    $general_code_details = $callConfig->getAllRows($general_code_details);
    $gc_details = array();
    foreach ($general_code_details as $gcd) {
      $gc_details[$gcd->id] = $gcd;
    }
    $return['gc_details'] = $gc_details;

    //$tax_details = "SELECT id, rate,slad_from,slad_to FROM tax_slads WHERE assessment_year='2024-2025' and is_active =0 group by rate ";
    $tax_details = "SELECT id, code,code_description FROM hsn_and_sac_codes WHERE   is_active =0 AND company_id='" . $get['company_id'] . "'";
    $tax_details = $callConfig->getAllRows($tax_details);
    $ts_details = array();
    foreach ($tax_details as $tsd) {
      $ts_details[$tsd->id] = $tsd;
    }
    $return['ts_details'] = $ts_details;

    $building_details = "SELECT id, building_name, building_address FROM buildings WHERE is_active =0";
    $building_details = $callConfig->getAllRows($building_details);
    $buildings = array();
    foreach ($building_details as $bd) {
      $buildings[$bd->id] = $bd;
    }
    $return['buildings'] = $buildings;

    if ($get['id'] != "") {
      $product_details_query = "SELECT * FROM amc_product_management WHERE id = '" . $get['id'] . "' AND is_active = 0";
      $product_details = $callConfig->getRow($product_details_query);
      $return['pm_details'] = $product_details;
    }
    return $return;
  }
  function getAllProductDetails($get){
    
    global $callConfig;
    $return = array();
    $return['get'] = $get;
    $product_details_query = "SELECT id, item_name FROM items WHERE company_id='" . $get['company_id'] . "' AND  is_active = 0";
    $product_details = $callConfig->getAllRows($product_details_query);
    $pm_details = array();
    foreach ($product_details as $row) {
      $pm_details[$row->id] = $row;
    }
    $return['pm_details'] = $pm_details;
    

    $asset_details_query = "SELECT a.id, a.asset_code, a.asset_description, a.category_id, a.asset_location,
      ac.category_description AS category_name
      FROM assets a
      LEFT JOIN asset_categories ac ON ac.id = a.category_id AND ac.company_id = a.company_id AND ac.is_active = 0
      WHERE a.company_id='" . $get['company_id'] . "' AND a.is_active = 0";
    $asset_details = $callConfig->getAllRows($asset_details_query);
    $assets = array();
    foreach ($asset_details as $row) {
      $assets[$row->id] = $row;
    }
    $return['asset_details'] = $assets;

    $asset_categories_query = "SELECT id, category_description FROM asset_categories WHERE company_id='" . $get['company_id'] . "' AND is_active = 0 ORDER BY category_description ASC";
    $asset_categories_result = $callConfig->getAllRows($asset_categories_query);
    $return['asset_categories'] = $asset_categories_result;
    
 
    $customer_details_query = "SELECT id,display_name FROM customers WHERE company_id='" . $get['company_id'] . "' AND  is_active = 0";
    $customer_details = $callConfig->getAllRows($customer_details_query);
    $cust_details = array();
    foreach ($customer_details as $row) {
      $cust_details[$row->id] = $row;
    }
    $return['cust_details'] = $cust_details;
    
    
    $query = "SELECT id,display_name FROM vendors WHERE company_id='" . $get['company_id'] . "' AND  is_active = 0";
    $return['query'] = $query;
    $details = $callConfig->getAllRows($query);

    $ven_details = array();
    foreach ($details as $row) {
      $ven_details[$row->id] = $row;
    }
    $return['ven_details'] = $ven_details; 
 
    $technician_query = "SELECT account_name, id from amc_account_details where company_id='" . $get['company_id'] . "' AND is_active='0'";
    $technician_details = $callConfig->getAllRows($technician_query);
    $tech_details = array();
    foreach ($technician_details as $row) {
       $tech_details[$row->id] = $row->account_name;
    }
    $return['tech_details'] = $tech_details;
     $account_group_query = "SELECT id, group_name from amc_account_groups where company_id='" . $get['company_id'] . "' AND is_active = '0'";
    $account_group_details = $callConfig->getAllRows($account_group_query);
    $ag_details = array();
    foreach ($account_group_details as $row) {
      $ag_details[$row->id] = $row->group_name;
    }
    $return['ag_details'] = $ag_details;
    $general_code_details = "SELECT id, company_id, genc_code,genc_detail_code,genc_detail_name FROM  general_code_details WHERE is_active =0  AND company_id='" . $get['company_id'] . "' AND  genc_code in ('AMCPAYTYPE','AMCSERVICE')";
    $general_code_details = $callConfig->getAllRows($general_code_details);
    $gc_details = array();
    foreach ($general_code_details as $gcd) {
      $gc_details[$gcd->id] = $gcd;
    }
    $return['gc_details'] = $gc_details;
    $amc_service_schedules = "SELECT distinct registration_id  FROM  amc_service_schedules WHERE company_id='" . $get['company_id'] . "'  AND is_active = '0' ";
    $amc_services = $callConfig->getAllRows($amc_service_schedules);
    $return['services'] = $amc_services;

    $query = "SELECT id, building_name from buildings where company_id='" . $get['company_id'] . "' AND is_active='0'";
    $result = $callConfig->getAllRows($query);
    $return['buildings'] = $result;

    $departments_query = "SELECT id, department_name FROM departments WHERE company_id='" . $get['company_id'] . "' AND is_active='0'";
    $departments_result = $callConfig->getAllRows($departments_query);
    $return['departments'] = $departments_result;
    


    $employees_query = "SELECT id, emp_code, display_name FROM employees WHERE company_id='" . $get['company_id'] . "' AND is_active='0'";
    $employees_result = $callConfig->getAllRows($employees_query);

    $emp_details = array();
    foreach ($employees_result as $row) {
      $emp_details[$row->id] = $row;
    }
    $return['emp_details'] = $emp_details;

    return $return;
  }
  function insertAmcRegistrationDetails($get, $post){
    global $callConfig;
    // $return['post'] = $post;
    // $return['get'] = $get;
    $insertary = array(
      'company_id' => $get['company_id'],
      'amc_name' => $post['amc_name'],
      'product_id' => $post['product_id'],
      'customer_id' => $post['customer_id'],
      'activation_date' => $post['activation_date'],
      'deactivation_date' => $post['deactivation_date'],
      'payment_type' => $post['payment_type'],
      'service_charge' => $post['service_charge'] ?? 0,
      'service_on' => $post['service_on'],
      'no_of_services' => $post['no_of_services'],
      'technician_id' => $post['technician_id'],
      'account_group_id' => $post['account_group_id'],
      'building_id' => $post['building_id'],
      'building_floor_id' => $post['building_floor_id'],
      'building_floor_space_id' => $post['building_floor_space_id'],
      'check_list' => $post['check_list'] ?? 'off',
      'party_type' => $post['party_type'],
      'vendor_id' => $post['vendor_id']

    );

    if (isset($post['record_id']) && $post['record_id'] != '') {
      $insertary['updated_date'] = date('Y-m-d h:i:s');
      $insertary['updated_by'] = $get['user_id'];
      $res = $callConfig->updateRecord('amc_registration_details', $insertary, 'id', $post['record_id']);
      $return['res'] = $res;

      $delte_array = array(
        'is_active' => '1',
        'service_on' => 'NULL',
        'updated_by' => $get['user_id']
      );
      $delete_res = $callConfig->updateRecord('amc_registration_services', $delte_array, 'registration_id', $post['record_id']);
      $return['delete_res'] = $delete_res;
      if ($delete_res) {
        $dynamicFields = $post['dynamic_field'];
        foreach ($dynamicFields as $fieldKey => $fieldValue) {
          $serviceInsertary = array(
            'company_id' => $get['company_id'],
            'registration_id' =>  $post['record_id'],
            'service_key' => $fieldKey,
            'service_value' => $fieldValue,
            'created_by' => $post['user_id'],
            'created_date' => date("Y-m-d H:i:s")
          );
          $dynamicRes = $callConfig->insertRecord('amc_registration_services', $serviceInsertary);

          if (!$dynamicRes) {
            $return['error'] = 'Error inserting dynamic service fields';
            break;
          }
        }
      }

      $update_date = date('Y-m-d H:i:s');
      $query = "UPDATE  application_check_lists SET is_active = NULL, updated_by={$get['user_id']}, updated_date = '{$update_date}'  WHERE transaction_id = '" . $post['record_id'] . "' AND application_tablename='amc_registration_details' AND company_id={$get['company_id']} AND is_active = 0";
      $callConfig->executeQuery($query);

      if(isset($post['check_list']) && $post['check_list'] == 'on'){ 
          $checklist_items = $post['checklist_items'];
          foreach ($checklist_items as $i => $fieldValue) {
            if(isset($post['existing_checklist_ids'][$i]) && $post['existing_checklist_ids'][$i] !=''){
              $check_id = $post['existing_checklist_ids'][$i];
              $update_check_array = array(
                'check_name' => $fieldValue,
                'is_active' => '0',
                'updated_by' => $get['user_id'],
                'updated_date' => date("Y-m-d H:i:s")
              );
              $callConfig->updateRecord('application_check_lists', $update_check_array, 'id', $check_id);
              continue;
            }else{
              // Insert new checklist item
              $check_array = array(
                'company_id' => $get['company_id'],
                'transaction_id' => $post['record_id'],
                'application_tablename' => 'amc_registration_details',
                'check_name' => $fieldValue,
                'current_status' => '0',
                'is_active' => '0',
                'created_by' => $get['user_id'],
                'created_date' => date("Y-m-d H:i:s")
              );
              $callConfig->insertRecord('application_check_lists', $check_array);
            }
          }
        }

    } else {
      $insertary['is_active'] = 0;
      $insertary['current_status'] = 0;
      $insertary['created_by'] = $get['user_id'];
      $insertary['created_date'] = date("Y-m-d H:i:s");
      $res = $callConfig->insertRecord('amc_registration_details', $insertary);
      $return['res'] = $res;
      $last_rec_id = $res['LASTID'];

      if ($last_rec_id) {
        $dynamicFields = $post['dynamic_field'];
        foreach ($dynamicFields as $fieldKey => $fieldValue) {
          $serviceInsertary = array(
            'company_id' => $get['company_id'],
            'registration_id' => $last_rec_id,
            'service_key' => $fieldKey,
            'service_value' => $fieldValue,
            'service_on' => $post['service_on'],
            'created_by' => $get['user_id'],
            'created_date' => date("Y-m-d H:i:s")
          );

          $dynamicRes = $callConfig->insertRecord('amc_registration_services', $serviceInsertary);

          if (!$dynamicRes) {
            $return['error'] = 'Error inserting dynamic service fields';
            break;
          }
        }
        if(isset($post['check_list']) && $post['check_list'] == 'on'){ 
          $checklist_items = $post['checklist_items'];
          foreach ($checklist_items as  $fieldValue) {
            $check_array = array(
              'company_id' => $get['company_id'],
              'transaction_id' => $last_rec_id,
              'application_tablename' => 'amc_registration_details',
              'check_name' => $fieldValue,
              'current_status' => '0',
              'is_active' => '0',
              'created_by' => $get['user_id'],
              'created_date' => date("Y-m-d H:i:s")
            );

            $checkRes = $callConfig->insertRecord('application_check_lists', $check_array);

            if (!$checkRes) {
              $return['check_error'] = 'Error inserting dynamic service fields';
              break;
            }
          }
        }
      }
      $return['last_rec_id'] = $last_rec_id;
    }
    return $return;
  }
  function getAmcRegistrationDetails($get){
    // print_r($get);    exit;
    global $callConfig;
    $return = array();
    $return['get'] = $get;
    if ($get['id'] != "") {
      $registration_details_query = "SELECT * FROM amc_registration_details WHERE id = '" . $get['id'] . "' AND company_id={$get['company_id']} AND is_active = 0";
      $registration_details = $callConfig->getRow($registration_details_query);
      $return['amc_registration_details'] = $registration_details;

      $dynamic_fields_query = "SELECT * FROM amc_registration_services WHERE registration_id = '" . $get['id'] . "' AND company_id={$get['company_id']} AND is_active = 0";
      $dynamic_fields_details_list = $callConfig->getAllRows($dynamic_fields_query);
      // $dynamic_fields_details = array();
      // foreach ($dynamic_fields_details_list as $row) {
      //   $dynamic_fields_details[$row->id] = $row;
      // }
      $return['dynamic_fields_details'] = $dynamic_fields_details_list;

      $checklist_query = "SELECT id,  check_name FROM application_check_lists WHERE transaction_id = '" . $get['id'] . "' AND application_tablename='amc_registration_details' AND company_id={$get['company_id']} AND is_active = 0";
      $result = $callConfig->getAllRows($checklist_query);
      $checklist_items = array();
      foreach ($result as $row) {
        $checklist_items[$row->id] = $row->check_name;
      }
      $return['checklist_items'] = $checklist_items;

    } else {
      $amc_registration_details = "SELECT * FROM  amc_registration_details WHERE  company_id={$get['company_id']} AND is_active =0";
      $amc_registration_details = $callConfig->getAllRows($amc_registration_details);
      $registration_details = array();
      foreach ($amc_registration_details as $row) {
        $registration_details[$row->id] = $row;
      }
      $return['amc_registration_details'] = $amc_registration_details;
    }
    return $return;
  }
  function getCustomerDetails($get){
    global $callConfig;
    $return = array();
    $return['get'] = $get;
    if ($get['id'] != "") {
      $amc_customer_details_query = "SELECT * FROM amc_customer_details WHERE id = '" . $get['id'] . "' AND company_id='" . $get['company_id'] . "' AND is_active = 0";
      $amc_customer_details = $callConfig->getRow($amc_customer_details_query);
      $return['amc_customer_details'] = $amc_customer_details;
    }
    return $return;
  } 

  function createSchedule($get){
    // print_r($get);    exit;
    global $callConfig;
    $return = array();
    $return['get'] = $get;
    if ($get['id'] != "") {
      $registrationId = $get['id'];
      $registration_details_query = "SELECT * FROM amc_registration_details WHERE company_id='" . $get['company_id'] . "' AND id = '" . $get['id'] . "' AND is_active = '0'";
      $registrationDetails = $callConfig->getRow($registration_details_query);
      $return['amc_registration_details'] = $registrationDetails;
      // Convert the object to an associative array
      $registrationDetails = json_decode(json_encode($registrationDetails), 1);

      $dynamic_fields_query = "SELECT * FROM amc_registration_services WHERE company_id='" . $get['company_id'] . "' AND registration_id = '" . $registrationId . "' AND is_active = '0'";
      $dynamic_fields_details_list = $callConfig->getAllRows($dynamic_fields_query);
      $serviceDetails = array();
      $i = 1;
      foreach ($dynamic_fields_details_list as $row) {
        $serviceDetails[$i] = $row->service_value;
        $i++;
      }
      $return['dynamic_fields_details'] = $serviceDetails;
      $serviceDetails = json_decode(json_encode($serviceDetails), 1);
      $product_id = $registrationDetails['product_id'];
      $activationDate = $registrationDetails['activation_date'];
      $deactivationDate = $registrationDetails['deactivation_date'];
      $noOfServices = $registrationDetails['no_of_services'];
      $serviceOn = $registrationDetails['service_on'];
      $customerId = $registrationDetails['customer_id'];
      $companyId = $registrationDetails['company_id'];
      $paymentType = $registrationDetails['payment_type'];
      $serviceCharge = $registrationDetails['service_charge'];
      $technicianId = $registrationDetails['technician_id'];
      $account_group_id = $registrationDetails['account_group_id'];
      $building_id = $registrationDetails['building_id'];
      $building_floor_id = $registrationDetails['building_floor_id'];
      $building_floor_space_id = $registrationDetails['building_floor_space_id'];

      $createdBy = 1; // Example value, replace with actual user ID
      $createdDate = date('Y-m-d H:i:s'); // Current timestamp

      // Prepare schedule dates based on service_on type
      $currentDate = $activationDate;
      $endDate = $deactivationDate;


      while (strtotime($currentDate) < strtotime($endDate)) {
        if ($serviceOn == '1') { // Daily
          for ($i = 1; $i <= $noOfServices; $i++) {
            $serviceTime = $serviceDetails[$i];
            $serviceDate = date('Y-m-d', strtotime($currentDate)) . " $serviceTime";
            $values = [
              'company_id' => $companyId,
              'registration_id' => $registrationId,
              'product_id' => $product_id,
              'customer_id' => $customerId,
              'service_date' => $serviceDate,
              'payment_type' => $paymentType,
              'service_charge' => $serviceCharge,
              'technician_id' => $technicianId,
              'account_group_id'=> $account_group_id,
              'building_id' => $building_id,
              'building_floor_id' => $building_floor_id,
              'building_floor_space_id' => $building_floor_space_id,
              'is_active' => '0',
              'created_by' => $createdBy,
              'created_date' => $createdDate
            ];
            $dynamicRes = $callConfig->insertRecord('amc_service_schedules', $values);
            $return['dynamicRes'][$i] = $dynamicRes;
          }
          $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        } elseif ($serviceOn == '2') { // Weekly
          for ($i = 1; $i <= $noOfServices; $i++) {
            $serviceDay = $serviceDetails[$i];
            $serviceDate = date('Y-m-d', strtotime("next $serviceDay", strtotime($currentDate)));
            if (strtotime($serviceDate) > strtotime($endDate)) {
              break;
            }
            $values = [
              'company_id' => $companyId,
              'registration_id' => $registrationId,
              'product_id' => $product_id,
              'customer_id' => $customerId,
              'service_date' => $serviceDate,
              'payment_type' => $paymentType,
              'service_charge' => $serviceCharge,
              'technician_id' => $technicianId,
              'account_group_id'=> $account_group_id,
              'building_id' => $building_id,
              'building_floor_id' => $building_floor_id,
              'building_floor_space_id' => $building_floor_space_id,
              'is_active' => '0',
              'created_by' => $createdBy,
              'created_date' => $createdDate
            ];
            $dynamicRes = $callConfig->insertRecord('amc_service_schedules', $values);
            $return['dynamicRes'][$serviceDay][$i] = $serviceDate;
          }
          $currentDate = date('Y-m-d', strtotime($currentDate . ' +7 days'));
        } elseif ($serviceOn == '3') { // Monthly
          for ($i = 1; $i <= $noOfServices; $i++) {
            $serviceDay = $serviceDetails[$i];
            $serviceDay =  $serviceDay < '10' ? '0' . $serviceDay : $serviceDay;
            $serviceDate = date('Y-m-' . $serviceDay, strtotime($currentDate));
            $values = [
              'company_id' => $companyId,
              'registration_id' => $registrationId,
              'product_id' => $product_id,
              'customer_id' => $customerId,
              'service_date' => $serviceDate,
              'payment_type' => $paymentType,
              'service_charge' => $serviceCharge,
              'account_group_id'=> $account_group_id,
              'technician_id' => $technicianId,
              'building_id' => $building_id,
              'building_floor_id' => $building_floor_id,
              'building_floor_space_id' => $building_floor_space_id,
              'is_active' => '0',
              'created_by' => $createdBy,
              'created_date' => $createdDate
            ];
            $dynamicRes = $callConfig->insertRecord('amc_service_schedules', $values);
            $return['dynamicRes'][$i] = $dynamicRes;
          }
          $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 month'));
        } elseif ($serviceOn == '4') { // Yearly
          for ($i = 1; $i <= $noOfServices; $i++) {
            $serviceDate = $serviceDetails[$i];
            // $serviceDate = date('Y-m-d', strtotime("+$i year", strtotime($activationDate)));
            $values = [
              'company_id' => $companyId,
              'registration_id' => $registrationId,
              'product_id' => $product_id,
              'customer_id' => $customerId,
              'service_date' => $serviceDate,
              'payment_type' => $paymentType,
              'service_charge' => $serviceCharge,
              'account_group_id'=> $account_group_id,
              'technician_id' => $technicianId,
              'building_id' => $building_id,
              'building_floor_id' => $building_floor_id,
              'building_floor_space_id' => $building_floor_space_id,
              'is_active' => '0',
              'created_by' => $createdBy,
              'created_date' => $createdDate
            ];
            $dynamicRes = $callConfig->insertRecord('amc_service_schedules', $values);
            $return['dynamicRes'][$i] = $dynamicRes;
          }
          break; // Yearly only adds records once
        }
      }

      // Return response
      if ($dynamicRes) {
        echo json_encode(['message' => 'Service schedule created successfully']);
      } else {
        echo json_encode(['message' => 'Error creating schedule']);
      }
    }

    return $return;
  }

  function getServiceScheduleDetails($get){
    global $callConfig;
    $return = array();
    if ($get['id'] != "") {
      $service_schedule_details_query = "SELECT * FROM amc_service_schedules WHERE registration_id = '" . $get['id'] . "' AND company_id='" . $get['company_id'] . "' AND is_active = '0' order by service_date asc";
      $service_schedule_details = $callConfig->getAllRows($service_schedule_details_query);
      $return['serviceDetails'] = $service_schedule_details;

      $customer_details_query = "SELECT ard.amc_name, acd.customer_name, acd.mobile_no, acd.address_line1,acd.address_line2,ard.activation_date, ard.deactivation_date, account_group_id, serv.genc_detail_name AS service_frequency FROM `amc_registration_details` ard
        LEFT join amc_customer_details acd on acd.id = ard.customer_id
        LEFT JOIN (SELECT genc_detail_code,genc_detail_name  FROM  general_code_details WHERE  genc_code ='AMCSERVICE' AND company_id='{$get['company_id']}' AND is_active =0 ) serv ON serv.genc_detail_code = ard.service_on
        where ard.id='" . $get['id'] . "' AND ard.company_id='" . $get['company_id'] . "' AND ard.is_active = '0' ";
      $customer_details = $callConfig->getRow($customer_details_query);
      $return['customer_details'] = $customer_details;
    }
    $general_code_details = "SELECT id, company_id, genc_code,genc_detail_code,genc_detail_name FROM  general_code_details WHERE  genc_code ='AMCPAYTYPE' AND company_id='" . $get['company_id'] . "' AND is_active ='0' ";
    $general_code_details = $callConfig->getAllRows($general_code_details);
    $gc_details = array();

    foreach ($general_code_details as $row) {
      $gc_details[$row->genc_detail_code] = $row->genc_detail_name;
    }
    $return['gc_details'] = $gc_details;

    $account_group_query = "SELECT id, group_name from amc_account_groups where company_id='" . $get['company_id'] . "' AND is_active = '0'";
    $account_group_details = $callConfig->getAllRows($account_group_query);
    $ag_details = array();
    foreach ($account_group_details as $row) {
      $ag_details[$row->id] = $row->group_name;
    }
    $return['ag_details'] = $ag_details;

    $technician_query = "SELECT account_name, id from amc_account_details where company_id='" . $get['company_id'] . "' AND is_active='0'";
    $technician_details = $callConfig->getAllRows($technician_query);
    $tech_details = array();
    foreach ($technician_details as $row) {
      $tech_details[$row->id] = $row->account_name;
    }
    $return['tech_details'] = $tech_details;
    return $return;
  }
  function getAmcServiceDetails($get){
    // print_r($get);    exit;
    global $callConfig;
    $return = array();
    $return['get'] = $get;
    if ($get['id'] != "") {
      $amc_service_schedules_query = "SELECT * FROM amc_service_schedules WHERE company_id='" . $get['company_id'] . "' AND id = '" . $get['id'] . "' AND is_active = 0";
      $amc_service_schedules = $callConfig->getRow($amc_service_schedules_query);
      $return['amc_service_schedules'] = $amc_service_schedules;
      $registrationId = $amc_service_schedules->registration_id ?? '';
      if($registrationId != ''){        
        $checklist_query = "SELECT id,  check_name FROM application_check_lists WHERE transaction_id = '{$registrationId}' AND application_tablename='amc_registration_details' AND company_id={$get['company_id']} AND is_active = 0";
        $result = $callConfig->getAllRows($checklist_query);
        $checklist_items = array();
        foreach ($result as $row) {
          $checklist_items[$row->id] = $row->check_name;
        }
        $return['checklist_items'] = $checklist_items;
      }

      $query = "SELECT id,application_check_list_id, check_status, check_remarks FROM amc_check_list WHERE amc_service_schedule_id = '{$get['id']}' AND amc_registration_id = '{$registrationId}' AND company_id='{$get['company_id']}' AND is_active = 0";
      $check_list_details = $callConfig->getAllRows($query);
      $check_list_status = array();
      foreach ($check_list_details as $row) {
        $check_list_status[$row->application_check_list_id] = $row;
      }
      $return['check_list_status'] = $check_list_status;
      
    }
    $general_code_details = "SELECT id, company_id, genc_code,genc_detail_code,genc_detail_name FROM  general_code_details WHERE  genc_code ='AMCPAYTYPE' AND company_id='" . $get['company_id'] . "' AND is_active ='0' ";
    $general_code_details = $callConfig->getAllRows($general_code_details);
    $gc_details = array();

    foreach ($general_code_details as $row) {
      $gc_details[$row->genc_detail_code] = $row->genc_detail_name;
    }
    $return['gc_details'] = $gc_details;

    $account_group_query = "SELECT id, group_name from amc_account_groups where company_id='" . $get['company_id'] . "' AND is_active = '0'";
    $account_group_details = $callConfig->getAllRows($account_group_query);
    $ag_details = array();
    foreach ($account_group_details as $row) {
      $ag_details[$row->id] = $row->group_name;
    }
    $return['ag_details'] = $ag_details;
    return $return;
  }

  function togetuserfielddashboard($get){
    global $callConfig;
   // echo "<pre>"; print_r($get); exit;
    $whr = "";
   
    if($get['moduletype']=='FIELD_EMPLOYEE'){
       $whr = "AND user_id = ".$get['user_id'];
    } 
    if($get['moduletype']=='FIELD_MANAGER'){
       $whr = "AND user_id IN ( SELECT id FROM application_users WHERE reports_to =".$get['user_id']." AND is_active =0)";
    }   
    $query = "SELECT COUNT(id) as cc_plan FROM field_work_daily_plans WHERE company_id =".$get['company_id']." ".$whr."  AND current_status NOT IN (25,28) AND is_active = 0";
    $pdata = $callConfig->getRow($query);
    $return['user_daily_plan'] = $pdata->cc_plan;
    $query = "SELECT COUNT(id) as cc_plan, sum(if(field_work_daily_plan_id!='',1,0)) as cc_daily_plan FROM field_work_user_map_points WHERE company_id =".$get['company_id']."  ".$whr." AND is_active = 0";
    $udpdata = $callConfig->getRow($query);
    $return['all_user_map'] = $udpdata->cc_plan;
    $return['all_user_daily_map'] = $udpdata->cc_daily_plan;
    $return['all_user_extra_map'] = $return['all_user_map']- $return['all_user_daily_map'];
    $return['alldatamonth'] = $this->togetoprationlistbasedonmonthly($get);
    
    //print_r($return); exit;
    return $return;
  }
  function togetoprationlistbasedonmonthly($get){
    global $callConfig;
    $year_month = $get['year_month']!="" ? $get['year_month'] : date('Y-m');
    $from_date =  date('Y-m-d', strtotime($year_month."-01"));
    $to_date =  date('Y-m-t', strtotime($from_date));
    $whr = "";
   
    if($get['moduletype']=='FIELD_EMPLOYEE'){
       $whr = "AND user_id = ".$get['user_id'];
    } 
    if($get['moduletype']=='FIELD_MANAGER'){
       $whr = "AND user_id IN ( SELECT id FROM application_users WHERE reports_to =".$get['user_id']." AND is_active =0)";
    }   
    $query = "SELECT id, contact_person as map_title, plan_date as map_date,plan_time as map_time, current_status, user_id  FROM field_work_daily_plans WHERE company_id =".$get['company_id']."  ".$whr." AND  plan_date >= '".$from_date."' AND plan_date <= '".$to_date."' AND is_active = 0";
    $palldata = $callConfig->getAllRows($query);
    $return['user_all_daily_plan'] = $palldata;
    $return['user_all_daily_plan_query'] = $query;
    $query = "SELECT id, map_title, map_date, map_time, field_work_daily_plan_id, current_status, user_id FROM field_work_user_map_points WHERE company_id =".$get['company_id']." ".$whr." AND map_date >= '".$from_date."' AND map_date <= '".$to_date."'  AND is_active = 0";
    $aumapudpdata = $callConfig->getAllRows($query);
    $return['all_user_map_detail'] = $aumapudpdata;
    return $return;
  }
  function updateAmcServiceDetailsAndCheckList($get, $post){
    global $callConfig;
    // $return['post'] = $post;
    // $return['get'] = $get;
    $update_arr = array(
      'service_date' => $post['service_date'],
      'payment_type' => $post['payment_type'],
      'service_charge' => $post['service_charge'] ?? 0,
      'technician_id' => $post['technician_id'] ?? '',
      'account_group_id' => $post['account_group_id'] ?? '',
      'service_expenditure' => $post['service_expenditure'] ?? 0,
      'ta_or_da' => $post['ta_or_da'] ?? 0,
      'service_reports' => $post['service_reports'] ?? '',
      'updated_by' => $get['user_id'],
      'updated_date' => date("Y-m-d H:i:s")
    );

    $res = $callConfig->updateRecord('amc_service_schedules', $update_arr, 'id', $post['record_id']);
    $return['res'] = $res;

    if(count((array)$post['checklist_items'])>0){
      foreach ($post['checklist_items'] as $i => $fieldValue) {
        $check_id = $post['existing_checklist_ids'][$i];
        if(isset($post['new_checklist_ids'][$i]) && $post['new_checklist_ids'][$i] != ''){
          // Insert new checklist item
          $check_update_arr = array(
            'check_status' => $post['checklist_status'][$check_id],
            'check_remarks' => $post['checklist_remarks'][$i],
            'updated_by' => $get['user_id'],
            'updated_date' => date("Y-m-d H:i:s")
          );
          // $result = $callConfig->insertRecord('amc_check_list', $check_array);
          $res = $callConfig->updateRecord('amc_check_list', $check_update_arr, 'id', $post['new_checklist_ids'][$i]);

          $return['checklist_result'][$check_id] = $res;
        }else{
          $check_array = array(
            'company_id' => $get['company_id'],
            'amc_registration_id' => $post['registration_id'],
            'amc_service_schedule_id' => $post['record_id'],
            'application_check_list_id' => $check_id,
            'check_status' => $post['checklist_status'][$check_id],
            'check_remarks' => $post['checklist_remarks'][$i],
            'check_name' => $fieldValue,
            'current_status' => '0',
            'is_active' => '0',
            'created_by' => $get['user_id'],
            'created_date' => date("Y-m-d H:i:s")
          );
          $result = $callConfig->insertRecord('amc_check_list', $check_array);
          $return['checklist_result'][$check_id] = $result;

        }

      }
    }
    return $return;
  }
  function getAmcCheckListDetails($get){
    global $callConfig;
    $data = json_decode(urldecode($get['data']),1);
    $whr = "";
    if($data['product_id'] != ''){
      $whr .= " AND s.product_id={$data['product_id']}";
    }
    
    if($data['account_group_id'] != ''){
      $whr .= " AND s.account_group_id={$data['account_group_id']}";
    }
    if($data['customer_id'] != ''){
      $whr .= " AND s.customer_id={$data['customer_id']}";
    }
    if($data['building_id'] != ''){
      $whr .= " AND r.building_id={$data['building_id']}";
    }
    if($data['building_floor_id'] != ''){
      $whr .= " AND r.building_floor_id={$data['building_floor_id']}";
    }
    if($data['building_floor_space_id'] != ''){
      $whr .= " AND r.building_floor_space_id={$data['building_floor_space_id']}";
    }
    if($data['activation_date'] != ''){
      $whr .= " AND s.service_date LIKE'%{$data['activation_date']}%'";
    }

    $query = "SELECT s.* FROM  amc_service_schedules  s 
    LEFT JOIN amc_registration_details r ON r.id=s.registration_id AND r.company_id=s.company_id AND r.is_active=0
    WHERE s.company_id= {$get['company_id']} AND s.is_active=0 $whr";
    $res = $callConfig->getAllRows($query);
    $return['query'] = $query;
    $return['service'] = $res;

    $technician_query = "SELECT account_name, id from amc_account_details where company_id='" . $get['company_id'] . "' AND is_active='0'";
    $technician_details = $callConfig->getAllRows($technician_query);
    $tech_details = array();
    foreach ($technician_details as $row) {
       $tech_details[$row->id] = $row->account_name;
    }
    $return['tech_details'] = $tech_details;
     $account_group_query = "SELECT id, group_name from amc_account_groups where company_id='" . $get['company_id'] . "' AND is_active = '0'";
    $account_group_details = $callConfig->getAllRows($account_group_query);
    $ag_details = array();
    foreach ($account_group_details as $row) {
      $ag_details[$row->id] = $row->group_name;
    }
    $return['ag_details'] = $ag_details;

    $general_code_details = "SELECT genc_code,genc_detail_code,genc_detail_name FROM  general_code_details WHERE is_active =0  AND  genc_code in ('AMCPAYTYPE','AMCSERVICE')";
    $gc_details = $callConfig->getAllRows($general_code_details);
    $return['gc_details'] = $gc_details;


    return $return;
    
  } 
  function getqrgenerationdetails($get) {
    global $callConfig;

    if($get['id'] != ''){
      $query = "SELECT * FROM complaint_qr_codes WHERE id={$get['id']} AND company_id={$get['company_id']} AND is_active = 0";
      $result = $callConfig->getRow($query);
      $return['amc_qr_data'] = $result;
    }

    $buildingquery = "SELECT id, building_name, location_id FROM buildings WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
    $buildingdata = $callConfig->getAllRows($buildingquery);
    $return['buildings'] = $buildingdata;

    $floorsquery = "SELECT id, building_id, floor_name FROM building_floors WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
    $floorsdata = $callConfig->getAllRows($floorsquery);
    $return['building_floors'] = $floorsdata;

    $spacesquery = "SELECT id, building_floor_id, cabin_name FROM building_floor_spaces WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
    $spacesdata = $callConfig->getAllRows($spacesquery);
    $return['building_floor_spaces'] = $spacesdata;

    $locationsquery = "SELECT id, short_name FROM locations WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
    $locationsdata = $callConfig->getAllRows($locationsquery);
    $return['locations'] = $locationsdata;

    $assetsquery = "SELECT id, asset_code, asset_description, asset_location FROM assets WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
    $assetsdata = $callConfig->getAllRows($assetsquery);
    $return['assets'] = $assetsdata;

    $itemsquery = "SELECT id, sku, item_name FROM items WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
    $itemsdata = $callConfig->getAllRows($itemsquery);
    $return['items'] = $itemsdata;

    return $return;
  } 
  function getamccomplaintsdetails($get){
    global $callConfig;
    $return = array();

    $buildings_details = "SELECT id, building_name FROM  buildings WHERE  company_id='" . $get['company_id'] . "' AND  is_active =0 ";
    $buildings_details = $callConfig->getAllRows($buildings_details);
    $buildings = array();
    $i = 1;
    foreach ($buildings_details as $row) {
      $buildings[$i] = $row;
      $i++;
    }
    $return['buildings'] = $buildings;

    $items_details = "SELECT id, item_name, item_type FROM  items WHERE company_id='" . $get['company_id'] . "' AND is_active =0";
    $items_details = $callConfig->getAllRows($items_details);
    $items = array();
    $i = 1;
    foreach ($items_details as $row){
      $items[$i] = $row;
      $i++;
    }
    $return['items'] = $items;

    $assets_details = "SELECT id, asset_code, asset_description  FROM assets WHERE  company_id='" . $get['company_id'] . "' AND is_active = 0 ";
    $assets_details = $callConfig->getAllRows($assets_details);
    $assets = array();
    $i = 1;
    foreach ($assets_details as $row) {
      $assets[$i] = $row;
      $i++;
    }
    $return['assets'] = $assets;

    $locationsquery = "SELECT id, concat(short_name,'-',location_name) AS short_name FROM locations WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
    $locationsdata = $callConfig->getAllRows($locationsquery);
    $return['locations'] = $locationsdata;

    return $return;
  }
  function getamccomplaintslistdetails($get){
    global $callConfig;
    $return = array();
    if($get['rid'] && $get['rid'] != ''){
      $table_details = "SELECT c.id as id, c.user_id as user_id, c.complaint_name, c.complaint_type, c.complaint_priority, c.transaction_date, i.item_name, a.asset_code, a.asset_description, s.name, s.background_color, s.color, b.building_name,bf.floor_name, bfs.cabin_name , au.display_name , c.created_date
      FROM complaints c
      LEFT JOIN items i ON i.id = c.item_id
      LEFT JOIN assets a ON a.id = c.asset_id
      LEFT JOIN status s ON s.name_value = c.current_status
      LEFT JOIN buildings b ON b.id = c.building_id
            LEFT JOIN building_floors bf ON bf.id = c.building_floor_id
            LEFT JOIN building_floor_spaces bfs ON bfs.id = c.building_floor_space_id
            LEFT JOIN application_users au ON au.id = c.created_by
      WHERE c.id='{$get['rid']}' AND c.company_id='{$get['company_id']}' AND c.is_active='0'";
      $table_details = $callConfig->getRow($table_details);
      $return['table'] = $table_details;
      
      $query = "SELECT * FROM  application_attachments  
        WHERE transaction_id = '{$get['rid']}' AND application_table_name = 'complaints'   AND  company_id='{$get['company_id']}' AND  is_active='0' ";
        $res = $callConfig->getAllRows($query);
      $return['complaints_images_data'] = $res;

      $query = "SELECT t1.id, t1.user_id,t1.transaction_date,t1.remarks, t2.display_name 
        FROM  complaint_responses t1
        LEFT JOIN application_users t2 ON t2.id = t1.user_id
        WHERE t1.complaint_id = '{$get['rid']}' AND t1.company_id='{$get['company_id']}' AND  t1.is_active=0";
      $res = $callConfig->getAllRows($query);
      $return['complaint_responses_data'] = $res;
      $return['complaint_responses_images_data'] = array();
      if(count((array)$res)>0){
        foreach ($res as $row) {
          $query = "SELECT * FROM  application_attachments  
            WHERE transaction_id = $row->id AND application_table_name = 'complaint_responses'   AND  company_id='{$get['company_id']}' AND  is_active='0' ";
            $res1 = $callConfig->getAllRows($query);
          $return['complaint_responses_images_data'][$row->id] = $res1;
        }
      }

    return $return;
    }

    $query = "SELECT c.id as id, c.user_id as user_id, c.complaint_name, c.complaint_type, c.complaint_priority, c.transaction_date, i.item_name, a.asset_code, a.asset_description, s.name, s.background_color, s.color, c.current_status FROM complaints c
    LEFT JOIN items i ON i.id = c.item_id
    LEFT JOIN assets a ON a.id = c.asset_id
    LEFT JOIN status s ON s.name_value = c.current_status
    WHERE c.company_id={$get['company_id']} AND  c.is_active='0' ORDER BY id DESC";
    $table_details = $callConfig->getAllRows($query);
    $return['table'] = $table_details;
    return $return;
  }  

  function getstationeryrelateddata($get){
    global $callConfig;
    $sql = "SELECT id,item_name FROM stationery_items WHERE company_id = '{$get['company_id']}' AND is_active = 0";
    $res = $callConfig->getAllRows($sql);
    $return['items_res'] = $res;

      // If GRN ID is passed → fetch parent & child
      if (!empty($get['grn_id'])) {
          $grn_id = $get['grn_id'];
          $company_id = $get['company_id'];

          // ==== Fetch GRN Master ====
          $sql_master = "SELECT id, transaction_no, transaction_date, supplier, supplier_invoice_no, total_amount
                         FROM stationery_grn WHERE id = '{$grn_id}' AND company_id = '{$company_id}' AND is_active = 0";
          $master_res = $callConfig->getRow($sql_master);
          $return['grn_master'] = $master_res;

          // ==== Fetch GRN Items (Child) ====
          $sql_items = "SELECT sgi.id, sgi.item_id, i.item_name, sgi.qty, sgi.amount
                        FROM stationery_grn_items sgi
                        LEFT JOIN stationery_items i  ON i.id = sgi.item_id  AND i.company_id = sgi.company_id 
                        WHERE sgi.grn_id = '{$grn_id}' AND sgi.company_id = '{$company_id}' AND sgi.is_active = 0";
          $child_res = $callConfig->getAllRows($sql_items);
          $return['grn_items'] = $child_res;
      }
    return $return;
  }

  function insertstationerydata($get, $post){
    global $callConfig;
    $return = array();
    $company_id = $get['company_id'];
    $user_id    = $get['user_id'];
    $grn_id     = intval($post['grn_id'] ?? 0);
    if($grn_id != '' && $grn_id > 0){
        // --- Update GRN Master ---
          $record = array(
              'supplier'       => $post['supplier'] ?? '',
              'supplier_invoice_no'       => $post['supplier_invoice_no'] ?? '',
              'total_amount'   => floatval($post['total_amount']) ?? '',
              'updated_by'     => $user_id,
              'updated_date'   => date('Y-m-d H:i:s')
          );
          $return['update1'] = $callConfig->updateRecord('stationery_grn', $record, 'id', $grn_id);

          $sql = "UPDATE stationery_grn_items SET is_active = NULL, updated_by = '{$user_id}', updated_date = '" . date('Y-m-d H:i:s') . "'
                WHERE company_id = '{$company_id}'
                  AND grn_id = '{$grn_id}'
                  AND is_active = 0";
        $return['update2'] = $callConfig->executeQuery($sql);
          // --- Loop through child items ---
          if (count((array)$post['item_id']) > 0) {
              foreach ($post['item_id'] as $i => $item_id) {
                  $child_id = intval($post['grn_items_id'][$i] ?? 0);

                  $child_data = array(
                      'item_id'      => $item_id,
                      'qty'          => floatval($post['qty'][$i]),
                      'amount'       => floatval($post['amount'][$i]),
                  );

                  if ($child_id > 0) {
                    $child_data['updated_by']     = $user_id;
                    $child_data['is_active']      = 0;
                      $child_data['updated_date']   = date('Y-m-d H:i:s');
                      // --- Update existing GRN item ---
                    $return['update3'][] =  $callConfig->updateRecord('stationery_grn_items', $child_data, 'id', $child_id);
                  } else {
                      // --- Insert new GRN item ---
                      $child_data['company_id']     = $company_id;
                      $child_data['grn_id']         = $grn_id;
                      $child_data['is_active']      = 0;
                      $child_data['current_status'] = 0;
                      $child_data['created_by']     = $user_id;
                      $child_data['created_date']   = date('Y-m-d H:i:s');

                     $return['insert'][] =  $callConfig->insertRecord('stationery_grn_items', $child_data);
                  }
              }
          }

          $return[] = array('status' => 'success', 'message' => 'GRN updated successfully', 'grn_id' => $grn_id);
    }else{
      $whr = "application_table_name = 'stationery_grn' ";
          $query = $callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS, '*', $whr, '', '', '');
          // $return['query'] = $query;
          $query_res = $callConfig->getRow($query);
          $res = $query_res;
          $le = $res->doc_no_length;
          $var = "%0" . $le . "d";
          $data = $res->pre_fix . sprintf($var, $res->serial_number+1) . $res->post_fix;
          $return['serialCode'] = trim($data);

          $transaction_no = trim($data);
          // ===== Insert into manufacturing_product_notes (Master) =====
          $record = array(
              'company_id'       => $company_id,
              'transaction_no'   => $transaction_no,
              'transaction_date' => $post['transaction_date'] ?? '',
              'supplier_invoice_no' => $post['supplier_invoice_no'] ?? '',
              'supplier'         => $post['supplier'] ?? '',
              'total_amount'     => $post['total_amount'] ?? '',
              'is_active'        => 0,
              'current_status'   => 0,
              'created_by'       => $user_id,
              'created_date'     => date('Y-m-d H:i:s')
          );

          $result = $callConfig->insertRecord('stationery_grn', $record);
          $return[] = $result;
          $note_id = $result['LASTID'];

          if (isset($res->id)) {
              $update = $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS, array('serial_number' => $res->serial_number + 1), 'id', $res->id);
          }
          // ===== Insert into manufacturing_product_note_items (Child Loop) =====
          if (count((array)$post['item_id'])>0) {
              foreach ($post['item_id'] as $i => $row) {
                  $child = array(
                      'company_id'     => $company_id,
                      'grn_id' => $note_id,
                      'item_id'  => $row,
                      'qty'        => $post['qty'][$i],
                      'amount'   => $post['amount'][$i],
                      'is_active'      => 0,
                      'current_status' => 0,
                      'created_by'     => $user_id,
                      'created_date'   => date('Y-m-d H:i:s')
                  );
                 $res =  $callConfig->insertRecord('stationery_grn_items', $child);
                 $return[] = $res;
              }
          }
      }
    return $return;
  }

  function getstationeryitemsreportdata($get) {
    global $callConfig;
    $return = array();

    $company_id = $get['company_id'];

    $sql = "SELECT id, item_name 
            FROM stationery_items 
            WHERE company_id = '{$company_id}' 
              AND is_active = 0
            ORDER BY item_name ASC";
    $res = $callConfig->getAllRows($sql);
    $return['items_res'] = $res;
    // ===================================================
      $whr = " WHERE sg.company_id = '{$get['company_id']}' AND sg.is_active = 0 AND sgi.is_active = 0 ";
      // --- Filter by selected items ---
      if (!empty($get['item_id']) && is_array($get['item_id'])) {
          $item_ids = array_map('intval', $get['item_id']);
          $ids_str = implode(',', $item_ids);
          $whr .= " AND sgi.item_id IN ({$ids_str}) ";
      }

      // --- Filter by date range ---
      if (!empty($get['from_date']) && !empty($get['to_date'])) {
          $from_date = $get['from_date'];
          $to_date   = $get['to_date'];
          $whr .= " AND sg.transaction_date >= '{$from_date}' AND sg.transaction_date <= '{$to_date}' ";
      }
      // --- Main query: fetch GRN + Item details ---
      $sql = "SELECT sg.transaction_no,
                  sg.transaction_date,
                  sg.supplier,
                  sgi.qty,
                  sgi.amount,
                  i.item_name
              FROM  stationery_grn_items sgi
              LEFT JOIN stationery_grn sg
                  ON sgi.grn_id = sg.id 
                 AND sgi.company_id = sg.company_id
              LEFT JOIN stationery_items i 
                  ON i.id = sgi.item_id 
                 AND i.company_id = sg.company_id
              {$whr}
              ORDER BY sg.transaction_date DESC, i.item_name ASC";

      $report_res = $callConfig->getAllRows($sql);
      $return['report_res'] = $report_res;
      // 3) OPENING BALANCE (before from_date)
      $opening_res = array();

      if (!empty($get['from_date'])) {
          $whr1='';
          if (!empty($get['item_id']) && is_array($get['item_id'])) {
              $item_ids = array_map('intval', $get['item_id']);
              $ids_str = implode(',', $item_ids);
              $whr1 = " AND i.id IN ({$ids_str})";
          }
          $from_date = $get['from_date'];

          $sql_opening = "SELECT
                  i.id,
                  i.item_name,
                  COALESCE(SUM(
                      CASE 
                          WHEN sg.transaction_date < '{$from_date}' THEN sgi.qty 
                          ELSE 0 
                      END
                  ), 0) AS total_qty,
                  COALESCE(SUM(
                      CASE 
                          WHEN sg.transaction_date < '{$from_date}' THEN sgi.amount 
                          ELSE 0 
                      END
                  ), 0) AS total_amount
              FROM stationery_items AS i
              LEFT JOIN  stationery_grn_items AS sgi
                     ON i.id = sgi.item_id
                    AND i.company_id = sgi.company_id AND sgi.is_active=0
              LEFT JOIN stationery_grn AS sg
                     ON sg.id = sgi.grn_id
                    AND sg.company_id = i.company_id
                    AND sg.is_active = 0
              WHERE i.company_id = $company_id
                AND i.is_active = 0
                {$whr1}
              GROUP BY
                  i.item_name
              ORDER BY 
                  i.item_name ASC";

          $opening_res = $callConfig->getAllRows($sql_opening);
      }

      $return['opening_res'] = $opening_res;

      return $return;
  }
  function getamccomplaintsreportdata($get){
      global $callConfig;
      $return = array();
      $company_id = $get['company_id'];
      // BASE WHERE CONDITION
      $whr = " WHERE c.company_id = '{$company_id}' AND c.is_active = 0 ";

      // DATE FILTER
      if (!empty($get['from_date']) && !empty($get['to_date'])) {
          $from_date = date('Y-m-d', strtotime($get['from_date']));
          $to_date   = date('Y-m-d', strtotime($get['to_date']));

          $whr .= " AND DATE(c.transaction_date) BETWEEN '{$from_date}' AND '{$to_date}' ";
      }

      // STATUS FILTER
      if (isset($get['status']) && $get['status'] !== '') {
          if ($get['status'] == '0') {
              $whr .= " AND c.current_status = 0 ";
          } elseif ($get['status'] == '1') {
              $whr .= " AND c.current_status > 0 AND c.current_status < 199 ";
          } elseif ($get['status'] == '199') {
              $whr .= " AND c.current_status = 199 ";
          }
      }

      // MAIN QUERY WITH NECESSARY JOINS
      $sql = "
          SELECT 
              c.*,
              i.item_name,
              a.asset_code,
              a.asset_description,
              concat(l.short_name, '-', l.location_name) AS location_name

          FROM complaints c

          LEFT JOIN items i 
              ON i.id = c.item_id 
              AND i.company_id = c.company_id 
              AND i.is_active = 0

          LEFT JOIN assets a 
              ON a.id = c.asset_id 
              AND a.company_id = c.company_id 
              AND a.is_active = 0

          LEFT JOIN locations l 
              ON l.id = c.location_id 
              AND l.company_id = c.company_id 
              AND l.is_active = 0
          {$whr}
          ORDER BY c.transaction_date DESC, c.id DESC";

      $return['report_res'] = $callConfig->getAllRows($sql);

      return $return;
  }
  function getcomplaintqrcodesreportdata($get){
      global $callConfig;
      $company_id = $get['company_id'];
      $return = [];
      // 1. Load Master Items & Master Assets
      $return['items_master'] = $callConfig->getAllRows("
          SELECT id, item_name 
          FROM items
          WHERE company_id='{$company_id}' AND is_active=0
          ORDER BY item_name ASC
      ");

      $return['assets_master'] = $callConfig->getAllRows("
          SELECT id, asset_code, asset_description 
          FROM assets
          WHERE company_id='{$company_id}' AND is_active=0
          ORDER BY asset_code ASC
      ");

    // 2. BUILD WHERE CONDITIONS FOR complaint_qr_codes
    // --------------------------------------------------------
    $where = " WHERE c.company_id = '{$company_id}' AND c.is_active = 0 ";

    $conditions = [];

    // ITEM FILTER
    if (!empty($get['item_id']) && is_array($get['item_id'])) {
        $item_ids = implode(",", array_map('intval', $get['item_id']));
        $conditions[] = " c.item_id IN ($item_ids) ";
    }

    // ASSET FILTER
    if (!empty($get['asset_id']) && is_array($get['asset_id'])) {
        $asset_ids = implode(",", array_map('intval', $get['asset_id']));
        $conditions[] = " c.asset_id IN ($asset_ids) ";
    }

    // APPLY OR CONDITIONS IF ANY FILTER EXISTS
    if (!empty($conditions)) {
        $where .= " AND (" . implode(" OR ", $conditions) . ") ";
    }

    // --------------------------------------------------------
    // 3. FINAL SINGLE QUERY WITH ALL REQUIRED JOINs
    // --------------------------------------------------------
    $sql = "
        SELECT 
            c.*,
            l.short_name AS location_name,
            i.item_name,
            a.asset_code,
            a.asset_description

        FROM complaint_qr_codes c

        LEFT JOIN locations l 
            ON l.id = c.location_id
            AND l.company_id = c.company_id
            AND l.is_active = 0

        LEFT JOIN items i 
            ON i.id = c.item_id
            AND i.company_id = c.company_id
            AND i.is_active = 0

        LEFT JOIN assets a 
            ON a.id = c.asset_id
            AND a.company_id = c.company_id
            AND a.is_active = 0

        {$where}ORDER BY c.id DESC";
      $return['qr_res'] = $callConfig->getAllRows($sql);
      return $return;
  } 

  // here function for dahsboard data.
  function getamcdashboarddata($get) {
      global $callConfig;

      $company_id = (int)($get['company_id'] ?? 0);
      $return = [];

      if ($company_id <= 0) {
          return $return;
      }

      // 1) Stationery items
      $sql = "
          SELECT
              COUNT(*) AS total,
              SUM(CASE WHEN available_qty < minmum_qty THEN 1 ELSE 0 END) AS low_stock
          FROM stationery_items
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['stationery_items_count'] = (int)($r->total ?? 0);
      $return['stationery_low_stock_count'] = (int)($r->low_stock ?? 0);

      // 2) Stationery GRN
      $sql = "
          SELECT COUNT(*) AS c
          FROM stationery_grn
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['stationery_grn_count'] = (int)($r->c ?? 0);

      // 3) Stationery requests
      $sql = "
          SELECT
              COUNT(*) AS total,
              SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS active,
              SUM(CASE WHEN current_status > 0 AND current_status < 199 THEN 1 ELSE 0 END) AS process,
              SUM(CASE WHEN current_status = 199 THEN 1 ELSE 0 END) AS approved
          FROM stationery_requests
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['stationery_request_total'] = (int)($r->total ?? 0);
      $return['stationery_request_active'] = (int)($r->active ?? 0);
      $return['stationery_request_process'] = (int)($r->process ?? 0);
      $return['stationery_request_approved'] = (int)($r->approved ?? 0);

      // 4) Complaints
      $sql = "
          SELECT
              COUNT(*) AS total,
              SUM(CASE WHEN current_status = 0 THEN 1 ELSE 0 END) AS active,
              SUM(CASE WHEN current_status > 0 AND current_status < 199 THEN 1 ELSE 0 END) AS process,
              SUM(CASE WHEN current_status = 199 THEN 1 ELSE 0 END) AS closed
          FROM complaints
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['complaints_total'] = (int)($r->total ?? 0);
      $return['complaints_active'] = (int)($r->active ?? 0);
      $return['complaints_process'] = (int)($r->process ?? 0);
      $return['complaints_closed'] = (int)($r->closed ?? 0);

      // 5) AMC master counts
      $sql = "
          SELECT COUNT(*) AS c
          FROM complaint_qr_codes
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['qr_code_count'] = (int)($r->c ?? 0);

      $sql = "
          SELECT COUNT(*) AS c
          FROM amc_product_management
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['products_count'] = (int)($r->c ?? 0);

      $sql = "
          SELECT COUNT(*) AS c
          FROM amc_account_groups
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['services_count'] = (int)($r->c ?? 0);

      $sql = "
          SELECT COUNT(*) AS c
          FROM amc_account_details
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['service_companies_count'] = (int)($r->c ?? 0);

      $sql = "
          SELECT COUNT(*) AS c
          FROM amc_customer_details
          WHERE company_id = '{$company_id}' AND is_active = 0
      ";
      $r = $callConfig->getRow($sql);
      $return['customers_count'] = (int)($r->c ?? 0);

      // 6) AMC registrations
      $sql = "
        SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN ars.registration_id IS NOT NULL THEN 1 ELSE 0 END) AS schedule,
            SUM(CASE WHEN ars.registration_id IS NULL THEN 1 ELSE 0 END) AS pending,
            SUM(CASE WHEN ars.registration_id IS NOT NULL AND ard.deactivation_date < CURDATE() THEN 1 ELSE 0 END) AS closed,
            SUM(CASE WHEN ars.registration_id IS NOT NULL AND ard.deactivation_date >= CURDATE() THEN 1 ELSE 0 END) AS running
        FROM amc_registration_details ard
        LEFT JOIN (
            SELECT DISTINCT registration_id, company_id
            FROM amc_service_schedules
            WHERE is_active = 0
        ) ars 
            ON ars.registration_id = ard.id 
          AND ars.company_id = ard.company_id
        WHERE ard.company_id = '{$company_id}'
          AND ard.is_active = 0
    ";
    $r = $callConfig->getRow($sql);

    $return['amc_registration_total'] = (int)($r->total ?? 0);
    $return['amc_registration_schedule'] = (int)($r->schedule ?? 0);
    $return['amc_registration_pending'] = (int)($r->pending ?? 0);
    $return['amc_registration_closed'] = (int)($r->closed ?? 0);
    $return['amc_registration_running'] = (int)($r->running ?? 0);

    
    $get['date'] = date('Y-m-d');
    $return['today_services'] = $this->getamcschedulesbydate($get);
    $get['date'] = date('Y-m-d', strtotime('-1 day'));
    $return['yesterday_services'] = $this->getamcschedulesbydate($get);
    return $return;
  }

  function getamcschedulesbydate($get){
    global $callConfig;
    $company_id=$get['company_id'];
    $date=$get['date'];
    $return=array();
    $return['date']=$date;

    $sql="SELECT 
        ars.service_date AS date,
        ard.amc_name,
        pm.product_name,
        acd.customer_name,
        gcd.genc_detail_name AS service_type,
        aag.group_name AS service_name,
        aad.account_name AS service_company,
        ars.current_status,
        CASE
          WHEN ars.current_status IS NULL OR ars.current_status=0 THEN 'Pending'
          WHEN ars.current_status>0 AND ars.current_status<199 THEN 'In Process'
          WHEN ars.current_status=199 THEN 'Completed'
          ELSE ''
        END AS status_name
      FROM amc_service_schedules ars
      LEFT JOIN amc_registration_details ard ON ard.id=ars.registration_id AND ard.company_id=ars.company_id AND ard.is_active=0
      LEFT JOIN amc_product_management pm ON pm.id=ard.product_id AND pm.company_id=ars.company_id AND pm.is_active=0
      LEFT JOIN amc_customer_details acd ON acd.id=ars.customer_id AND acd.company_id=ars.company_id AND acd.is_active=0
      LEFT JOIN amc_account_details aad ON aad.id=ars.technician_id AND aad.company_id=ars.company_id AND aad.is_active=0
      LEFT JOIN general_code_details gcd ON gcd.genc_detail_code=ard.service_on AND gcd.company_id=ars.company_id AND gcd.genc_code='AMCSERVICE' AND gcd.is_active=0
      LEFT JOIN amc_account_groups aag ON aag.id=ars.account_group_id AND aag.company_id=ars.company_id AND aag.is_active=0
      WHERE ars.company_id='{$company_id}'
      AND ars.is_active=0
      AND DATE(ars.service_date)='{$date}'
      ORDER BY ars.service_date ASC, ars.id ASC";

    $return['schedule_data']=$callConfig->getAllRows($sql);
    // $return['sql']=($sql);
    return $return;
  }


}

?>