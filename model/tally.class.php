<?php 
class tallyClass extends userClass{
	function topostmasterdetails($get, $post){
	 	global $callConfig;
	 	//echo "<pre>";  print_r($post); print_r($get); echo "</pre>";  exit;
	 	$api_hits = array('company_id'=>$get['company_id'],'api_url'=>$get['mode'],'api_request_parameters'=>json_encode($get),'api_response_details'=>json_encode($post), 'application_table'=> $get['table'], 'status_json'=>$get['json_data'], 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
		$res=$callConfig->insertRecord('api_hits',$api_hits);	
	 	return $res;
	}
	function tosenditem_categoriestotally($get){
	 	global $callConfig;
	 	$query = "SELECT id,category_description FROM item_categories WHERE company_id =".$get['company_id']." AND is_active=0";
 		$data = $callConfig->getAllRows($query);
 		$return['result'] = $data;
	 	return $return;
	}
	function togetapiidbasemapdetail($get){
		global $callConfig;

		$query = "SELECT * FROM api_masters WHERE company_id =".$get['company_id']." AND is_active=0";
 		$data = $callConfig->getAllRows($query);
 		$return['all_api_masters'] = $data;


		$query = "SELECT * FROM api_masters WHERE company_id =".$get['company_id']." AND  id = ".$get['api_master_id']." AND is_active=0";
 		$data = $callConfig->getRow($query);
 		$return['api_masters'] = $data;
 		

 		//print_r($return);
 		// if($data->application_table!=""){
 		// 	$query="show COLUMNS from ".$data->application_table;
			// //echo $query;
			// $dataa = $callConfig->getAllRows($query);
			// $return['table_filed'] = $dataa;
 		// }
 		//print_r($dataa); exit;
 		return $return;
	}
	function getApiMastersDetails($get){
	    global $callConfig;
	    $return = array();
	    $return['get'] = $get;
	    if($get['id'] != ""){
	      	$account_details_query = "SELECT * FROM api_masters WHERE company_id='".$get['company_id']."' AND id = '".$get['id']."' AND is_active = '0'";
	      	$account_details = $callConfig->getRow($account_details_query);
	      	$return['api_masters'] = $account_details;
	      	if($account_details->application_table!=""){
	 			$query="SELECT id, application_table_name, list_attributes FROM display_attributes WHERE company_id='".$get['company_id']."'  AND application_table_name = '".$account_details->application_table."' AND is_active = '0'";
				$dataa = $callConfig->getRow($query);
				$return['display_attributes_list_attributes'] = $dataa;
	 		}
	    }
	   
     	$display_attributessql = "SELECT id, application_table_name FROM display_attributes WHERE company_id='" . $get['company_id'] . "'  AND is_active = '0'";
      	$display_attributesres = $callConfig->getAllRows($display_attributessql);
      	$return['display_attributes'] = $display_attributesres;

	    return $return;
	  }
	function getAPIHits($get){
		global $callConfig;
	    $return = array();
	    $return['get'] = $get;
	    if($get['api_master_id'] !=""){
			$api_hitssql = "SELECT * FROM api_hits WHERE company_id='".$get['company_id']."' AND api_master_id = '".$get['api_master_id']."' AND is_active = '0' ORDER BY request_date_time DESC";
	      	$api_hitsres = $callConfig->getAllRows($api_hitssql);
	      	$return['api_hits'] = $api_hitsres;

	      	$apimasterssql = "SELECT id, api_service_provider, api_name, end_point, api_method FROM api_masters WHERE company_id='".$get['company_id']."' AND id = '".$get['api_master_id']."' AND is_active = '0'";
	      	$apimastersres = $callConfig->getRow($apimasterssql);
	      	$return['api_masters'] = $apimastersres;
      	}elseif($get['api_hit_id'] !=""){

      		$api_hitssql = "SELECT * FROM api_hits WHERE company_id='".$get['company_id']."' AND id = '".$get['api_hit_id']."' AND is_active = '0'";
	      	$api_hitsres = $callConfig->getRow($api_hitssql);
	      	$return['api_hits'] = $api_hitsres;

	      	if($api_hitsres->api_master_id !=""){
		      	$apimasterssql = "SELECT * FROM api_masters WHERE company_id='".$get['company_id']."' AND id = '".$api_hitsres->api_master_id."' AND is_active = '0'";
		      	$apimastersres = $callConfig->getRow($apimasterssql);
	      		$return['api_masters'] = $apimastersres;
	      	}

      	}else{
		    $apimasterssql = "SELECT id, api_service_provider, api_name, api_type FROM api_masters WHERE company_id='".$get['company_id']."' AND is_active = '0'";
	      	$apimastersres = $callConfig->getAllRows($apimasterssql);
	      	$return['api_masters'] = $apimastersres;
      	}
	    return $return;
	}

	function insertAPIHits($get){
	 	global $callConfig;
	 	$res=$callConfig->insertRecord('api_hits',$get);	
	 	return $res;
	}

	function getLedgerMaster($get,$post){
		global $callConfig;
		$return = array();
		$uniquecode = $get['uniquecode'];
	 	$company_id = $get['company_id'];
	 	$companysql = "SELECT id, name FROM companies WHERE company_id='".$company_id."' AND is_active = '0'";
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
		
		// $vendorsql = "SELECT company_id, company, display_name, billing_address, billing_country, billing_state, billing_city, billing_post_code, email, website_url, pan_no, gstin, account_code FROM `vendors` WHERE company_id='".$company_id."' AND is_active = '0'";
		// $vendorres = $callConfig->getAllRows($vendorsql);
		// foreach($vendorres as $ven){
		// 	$ven->company_id = $companies[$ven->company_id];
		// 	$chartofaccount = 	$chart_of_accounts[$cust->account_code]->accgro_group_id;

		// 	$cust->ledger_group = $account_groups[$chartofaccount];

		// 	$ven->ledger_group = $chart_of_accounts[$ven->account_code]->account_name;
		// 	$ledger_master[] = (array)$ven;
		// }

		$customersql = "SELECT company_id, company, display_name, billing_address, billing_country, billing_state, billing_city, billing_post_code, email, website_url, pan_no, gstin, account_code FROM `customers` WHERE company_id='".$company_id."' AND is_active = '0'";
		$customerres = $callConfig->getAllRows($customersql);
		foreach($customerres as $cust){
			$cust->company_id = $companies[$cust->company_id];
			$chartofaccount = 	$chart_of_accounts[$cust->account_code]->accgro_group_id;
			$cust->account_name = $chart_of_accounts[$cust->account_code]->account_name;
			$cust->account_code = $chart_of_accounts[$cust->account_code]->account_code;
			$ledger_group_name = $account_groups[$chartofaccount];
			$ledgeary = array('company_id'=>$cust->company_id, 'account_name'=>$cust->company, 'account_code'=>$cust->account_code, 'ledger_group_name'=>$ledger_group_name,'billing_address'=>$cust->billing_address, 'billing_country'=>'', 'billing_state'=>'', 'billing_city'=>'', 'billing_post_code'=>$cust->billing_post_code, 'email'=>$cust->email, 'website_url'=>'' , 'pan_no'=>$cust->pan_no, 'gstin'=>$cust->gstin);
			$ledger_master[] = (array)$ledgeary;
		}


		$vendorsql = "SELECT company_id, company, display_name, billing_address, billing_country, billing_state, billing_city, billing_post_code, email, website_url, pan_no, gstin, account_code FROM `vendors` WHERE company_id='".$company_id."' AND is_active = '0'";
		$vendorres = $callConfig->getAllRows($vendorsql);
		foreach($vendorres as $ven){
			$ven->company_id = $companies[$ven->company_id];
			$chartofaccount = 	$chart_of_accounts[$ven->account_code]->accgro_group_id;
			$ven->account_name = $chart_of_accounts[$ven->account_code]->account_name;
			$ven->account_code = $chart_of_accounts[$ven->account_code]->account_code;
			$ledger_group_name = $account_groups[$chartofaccount];
			$ledgeary = array('company_id'=>$ven->company_id, 'account_name'=>$ven->company, 'account_code'=>$ven->account_code, 'ledger_group_name'=>$ledger_group_name,'billing_address'=>$ven->billing_address, 'billing_country'=>'', 'billing_state'=>'', 'billing_city'=>'', 'billing_post_code'=>$ven->billing_post_code, 'email'=>$ven->email, 'website_url'=>'' , 'pan_no'=>$ven->pan_no, 'gstin'=>$ven->gstin);
			$ledger_master[] = (array)$ledgeary;
		}

		


		foreach($chartofaccountsres as $cca){
			$ledgeary = array();
		//	$ledgeary = array('company_id'=>$cca->company_id, 'company'=>$cca->account_name, 'ledger_group'=>$cca->account_name);
			if($cca->account_name !=""){
				$cca->company_id = $companies[$cca->company_id];
				$ledger_group_name = $account_groups[$cca->accgro_group_id];
				$ledgeary = array('company_id'=>$cca->company_id, 'account_name'=>$cca->account_name, 'account_code'=>$cca->account_code, 'ledger_group_name'=>$ledger_group_name,'billing_address'=>'', 'billing_country'=>'', 'billing_state'=>'', 'billing_city'=>'', 'billing_post_code'=>'', 'email'=>'', 'website_url'=>'' , 'pan_no'=>'', 'gstin'=>'');
			}
			$ledger_master[] = (array)$ledgeary;
    	}
    	$return['status'] = 200;
		$return['result'] = $ledger_master;
		//echo "<pre>"; print_r($return);
		return $return;

	}

	function getTransactionInvoices($get,$post,$transaction_table=""){
	 	global $callConfig;
	 	$return = $transaction_entry =$item_trans =  array();
	 	$uniquecode = $get['uniquecode'];
	 	$company_id = $get['company_id'];
	 	$vendcustarray = array('20003501','1004001');
	 	$giveledger_entries = array('goods_receipt_notes','delivery_orders','credit_notes','debit_notes','supplier_bills','purchase_invoices','sales_invoices');
	 	$companysql = "SELECT id, name FROM companies WHERE company_id='".$company_id."' AND is_active = '0'";
		$companysres = $callConfig->getAllRows($companysql);
		$companies = array();
	    foreach($companysres as $comp){
	        $companies[$comp->id] = $comp->name;
	    }

	    $citeissql = "SELECT id, city_name FROM cities WHERE company_id='".$company_id."' AND is_active = '0'";
		$citeisres = $callConfig->getAllRows($citeissql);
		$cities = array();
	    foreach($citeisres as $comp){
	        $cities[$comp->id] = $comp->city_name;
	    }
	    
	    $statessql = "SELECT id, state_name FROM states WHERE company_id='".$company_id."' AND is_active = '0'";
		$statesres = $callConfig->getAllRows($statessql);
		$states = array();
	    foreach($statesres as $stat){
	        $states[$stat->id] = $stat->state_name;
	    }

		$chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type, accgro_group_id FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND is_active =0";
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

	    if($transaction_table == "sales_invoices" || $transaction_table == "sales_orders" || $transaction_table == "delivery_orders"){
		    $customerssql = "SELECT id, company FROM customers WHERE company_id='".$company_id."' AND is_active = '0'";
			$customersres = $callConfig->getAllRows($customerssql);
		}else{
		    $customerssql = "SELECT id, company FROM vendors WHERE company_id='".$company_id."' AND is_active = '0'";
			$customersres = $callConfig->getAllRows($customerssql);	
		}
		$customers = array();
	    foreach($customersres as $custre){
	        $customers[$custre->id] = $custre->company;
	    }



	 	if($transaction_table !=""){
			//echo "<pre>";
			$dmtsql = "SELECT id, page_title, application_table_name, display_table, item_table, tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$transaction_table."' AND is_active = 0";
			$dmtres=$callConfig->getRow($dmtsql);

			if($transaction_table == "credit_notes" OR $transaction_table == "debit_notes"){
				$item_transaction_sql = "SELECT titm.id, titm.company_id, titm.transaction_id, titm.item_id, titm.account_code, titm.item_name, titm.item_name, titm.item_quantity, titm.item_price, titm.item_amount FROM ".$dmtres->item_table." titm WHERE titm.company_id='".$company_id."' AND titm.is_active = '0'";
				
			}else{
				$item_transaction_sql = "SELECT titm.id, titm.company_id, titm.transaction_id, titm.item_id, titm.item_name, titm.item_quantity, titm.item_price, titm.item_amount, itm.sku as part_no FROM ".$dmtres->item_table." titm, items itm WHERE titm.company_id='".$company_id."' AND titm.company_id=itm.company_id AND titm.item_id = itm.id AND titm.is_active = itm.is_active AND titm.is_active = '0'";
			}
			$item_transaction_res = $callConfig->getAllRows($item_transaction_sql);
			foreach($item_transaction_res as $itemtrans){
				$itemtrans->company_id = $companies[$itemtrans->company_id];
				$item_trans[$itemtrans->transaction_id][] = $itemtrans;
			}

			$item_tax_transaction_sql = "SELECT id, company_id, transaction_id, transaction_item_id, tax_name, tax_value, tax_id, tax_amount, tax_split_details FROM ".$dmtres->tax_table." WHERE company_id='".$company_id."'  AND is_active = '0' ";
			$item_tax_transaction_res = $callConfig->getAllRows($item_tax_transaction_sql);
			foreach($item_tax_transaction_res as $itemtaxtrans){
				$tax_split_details = json_decode($itemtaxtrans->tax_split_details,1);
				foreach($tax_split_details as $ttt){
					$taxitementry = array();
					$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']]['hsn_code'] = $itemtaxtrans->tax_name !=""?$itemtaxtrans->tax_name:"";
					$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']]['tax_name'] = $ttt['tax_name'] !=""?$ttt['tax_name']:"";
					$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']]['amount'] += $ttt['tax_amount'] !=""?$ttt['tax_amount']:0;
					$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']]['account_code'] = $ttt['account_code'] !=""?$ttt['account_code']:"";
					$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']]['tax_percentage'] = $ttt['tax_percentage'] !=""?$ttt['tax_percentage']:"";
					$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']]['account_name'] = $chart_of_accounts[$ttt['account_code']]->account_name !=""?$chart_of_accounts[$ttt['account_code']]->account_name:"";

					$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']]['ledger_group_name'] = $account_groups[$chart_of_accounts[$ttt['account_code']]->accgro_group_id] !=""?$account_groups[$chart_of_accounts[$ttt['account_code']]->accgro_group_id]:"";

					

					// $taxitementry['hsn_code'] = $itemtaxtrans->tax_name !=""?$itemtaxtrans->tax_name:"";
					// $taxitementry['tax_name'] = $ttt['tax_name'] !=""?$ttt['tax_name']:"";
					// $taxitementry['amount'] = $ttt['tax_amount'] !=""?$ttt['tax_amount']:0;
					// $taxitementry['account_code'] = $ttt['account_code'] !=""?$ttt['account_code']:"";
					// $taxitementry['tax_percentage'] = $ttt['tax_percentage'] !=""?$ttt['tax_percentage']:"";
					// $taxitementry['ledger_name'] = $chart_of_accounts[$ttt['account_code']]->account_name !=""?$chart_of_accounts[$ttt['account_code']]->account_name:"";
		
					//$item_tax_trans[$itemtaxtrans->transaction_id][$ttt['account_code']] = $taxitementry; 
				}
				// $item_tax_trans[$itemtaxtrans->transaction_id][$itemtaxtrans->tax_id]['hsn_code'] = $itemtaxtrans->tax_name !=""?$itemtaxtrans->tax_name:"";
				// $item_tax_trans[$itemtaxtrans->transaction_id][$itemtaxtrans->tax_id]['tax_value'] = $itemtaxtrans->tax_value !=""?$itemtaxtrans->tax_value:"";
				// $item_tax_trans[$itemtaxtrans->transaction_id][$itemtaxtrans->tax_id]['amount'] += $itemtaxtrans->tax_amount !=""?$itemtaxtrans->tax_amount:0;
			}


			$jvsql = "SELECT jv.transaction_number bill_number, DATE_FORMAT(jv.transaction_date, '%d-%m-%Y') AS bill_date , jv.ref_type, jv.ref_id, ijv.account_code, ijv.account_name as ledger_name, ijv.debit_amount, ijv.credit_amount FROM journal_vouchers jv, jv_items ijv WHERE jv.company_id='".$company_id."' AND jv.ref_type = '".$dmtres->application_table_name."' AND jv.company_id = ijv.company_id AND jv.id = ijv.transaction_id AND jv.is_active = ijv.is_active AND jv.is_active = 0";
			$jvres = $callConfig->getAllRows($jvsql);
			$journal_vouchers = array();
			foreach($jvres as $jv){
				$jv->amount = ($jv->debit_amount !="0" AND $jv->debit_amount !="")?$jv->debit_amount:$jv->credit_amount;
				$jv->ledger_group_name = $account_groups[$chart_of_accounts[$jv->account_code]->accgro_group_id] !=""?$account_groups[$chart_of_accounts[$jv->account_code]->accgro_group_id]:"";
				$journal_vouchers[$jv->ref_id][] = $jv;

			}

			//print_r($item_trans);
			$main_transaction_sql = "SELECT id, company_id, transaction_number, DATE_FORMAT(transaction_date, '%d-%m-%Y') AS transaction_date, party_id, party_name, party_email, party_phone, party_address, supplier_state_id, place_of_supply, ref_type, ref_no FROM ".$dmtres->application_table_name." WHERE company_id='".$company_id."' AND is_active = '0'";
			$main_transaction_res = $callConfig->getAllRows($main_transaction_sql);
			foreach($main_transaction_res as $trans){
				$trans->company_id = $companies[$trans->company_id];
				$trans->place_of_supply = $cities[$trans->place_of_supply];
				$trans->supplier_state_id = $states[$trans->supplier_state_id];
				if($transaction_table != "credit_notes" AND $transaction_table != "debit_notes"){
					$trans->party_name = $customers[$trans->party_id];
				}

				if(isset($item_trans[$trans->id]) AND ($transaction_table != "credit_notes" AND $transaction_table != "debit_notes")){
					$trans->item_entries = $item_trans[$trans->id];
				}else{
					$trans->item_entries = array();
				}

				if(isset($item_tax_trans[$trans->id])){
					$itemtaxentry = array();
					foreach($item_tax_trans[$trans->id] as $ackey=>$acval){
						$itemtaxentry[] = $acval;
					}
					$trans->item_tax = $itemtaxentry;

					//$trans->item_tax = $item_tax_trans[$trans->id];
				}else{
					$trans->item_tax = array();
				}

				if(in_array($transaction_table, $giveledger_entries)){
					if(isset($journal_vouchers[$trans->id])){
						$journal_vouchersjvv = array();
						foreach($journal_vouchers[$trans->id] as $lfgdetail){
							// if(in_array($lfgdetail->account_code, (array)$vendcustarray)){
							// 	$lfgdetail->ledger_name = $trans->party_name;
							// 	$journal_vouchersjvv[] = $lfgdetail;
							// }else{
							// 	$journal_vouchersjvv[] = $lfgdetail;
							// } 
							if(!in_array($lfgdetail->account_code, (array)$vendcustarray)){
								//$lfgdetail->ledger_name = $trans->party_name;
								$journal_vouchersjvv[] = $lfgdetail;
							}


						}
							
						$trans->ledger_entries = $journal_vouchersjvv;	
						//$trans->ledger_entries = $journal_vouchers[$trans->id];
					}else{
						$trans->ledger_entries = array();
					}
				}
				// else{
				// 	if(isset($item_tax_trans[$trans->id])){
				// 		$trans->ledger_entries = $item_tax_trans[$trans->id];
				// 	}else{
				// 		$trans->ledger_entries = array();
				// 	}
				// }
				$transaction_entry[] = $trans;
			}
			$return['status'] = 200;
			$return['transaction'] = $transaction_table;
    		$return['result'] = $transaction_entry;
		}else{
    		$return['status'] = 404;
			$return['result'] = $ledger_master;
    	}
    	//echo "<pre>"; print_r($return);
	 	//$return['transaction_table'] = $transaction_table;
	 	return $return;

	}
	function getAPIPayments($get,$post){
		global $callConfig;
	 	$return = array();
	 	$uniquecode = $get['uniquecode'];
	 	$company_id = $get['company_id'];
		$companysql = "SELECT id, name FROM companies WHERE company_id='".$company_id."' AND is_active = '0'";
		$companysres = $callConfig->getAllRows($companysql);
		$companies = array();
	    foreach($companysres as $comp){
	        $companies[$comp->id] = $comp->name;
	    }

	    $customerssql = "SELECT id, company FROM vendors WHERE company_id='".$company_id."' AND is_active = '0'";
		$customersres = $callConfig->getAllRows($customerssql);
		$customers = array();
	    foreach($customersres as $custre){
	        $customers[$custre->id] = $custre->company;
	    }
	    


		$chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND is_active =0";
	    $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
	    $chart_of_accounts = array();
	    foreach($chartofaccountsres as $cca){
	        $chart_of_accounts[$cca->account_code] = $cca;
	    }

		$payment_allocations_sql = "SELECT company_id, payment_id, transaction_table, transaction_id, transaction_no as bill_transaction_no, party_id, total_paid_amonut as amount  FROM payment_allocations WHERE company_id='".$company_id."' AND is_active = '0'";
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
		$journal_vouchers = array();
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

		$banckaccountsql = "SELECT account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '".$company_id."' AND is_active=0";
    	$banckaccountres = $callConfig->getAllRows($banckaccountsql);
    
    	foreach($banckaccountres as $payres){
			$bank_accounts[$payres->account_code] = (array)$payres;
		}	

    	$whrs = "company_id= ".$company_id." AND is_active = 0";
		$query = $callConfig->selectQuery('config','default_cash_acct',$whrs,'','','');
		$config = $callConfig->getRow($query);
		$cashaccount =$config->default_cash_acct;
		



		//print_r($item_trans);
		$payments_sql = "SELECT id, company_id, transaction_no, DATE_FORMAT(payment_date, '%d-%m-%Y') AS payment_date, payment_type, account_code FROM payments WHERE company_id='".$company_id."' AND is_active = '0'";
		$payments_res = $callConfig->getAllRows($payments_sql);
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
			// if(isset($journal_vouchers[$paytrans->id])){
			// 	$paytrans->ledger_entries = $journal_vouchers[$paytrans->id];
			// }else{
			// 	$paytrans->ledger_entries = array();
			// }
		
			$transaction_entry[] = $paytrans;
		}
		$return['status'] = 200;
		$return['result'] = $transaction_entry;
		//echo "<pre>"; print_r($return);
		return $return;
	}
	function getAPIReceipts($get,$post){
		global $callConfig;
	 	$return = array();
	 	$uniquecode = $get['uniquecode'];
	 	$company_id = $get['company_id'];
		$companysql = "SELECT id, name FROM companies WHERE company_id='".$company_id."' AND is_active = '0'";
		$companysres = $callConfig->getAllRows($companysql);
		$companies = array();
	    foreach($companysres as $comp){
	        $companies[$comp->id] = $comp->name;
	    }

		$chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND is_active =0";
	    $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
	    $chart_of_accounts = array();
	    foreach($chartofaccountsres as $cca){
	        $chart_of_accounts[$cca->account_code] = $cca;
	    }

	    $customerssql = "SELECT id, company FROM customers WHERE company_id='".$company_id."' AND is_active = '0'";
		$customersres = $callConfig->getAllRows($customerssql);
		$customers = array();
	    foreach($customersres as $custre){
	        $customers[$custre->id] = $custre->company;
	    }
	    
	    $payment_allocations_sql = "SELECT company_id, receipt_id, transaction_table, transaction_id, transaction_no as bill_transaction_no, party_id, total_paid_amonut as amount FROM receipt_allocations WHERE company_id='".$company_id."' AND is_active = '0'";
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
		$journal_vouchers = array();
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

		$banckaccountsql = "SELECT account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '".$company_id."' AND is_active=0";
    	$banckaccountres = $callConfig->getAllRows($banckaccountsql);
    
    	foreach($banckaccountres as $payres){
			$bank_accounts[$payres->account_code] = (array)$payres;
		}	

    	$whrs = "company_id= ".$company_id." AND is_active = 0";
		$query = $callConfig->selectQuery('config','default_cash_acct',$whrs,'','','');
		$config = $callConfig->getRow($query);
		$cashaccount =$config->default_cash_acct;
		



		//print_r($item_trans);
		$payments_sql = "SELECT id, company_id, transaction_no, DATE_FORMAT(payment_date, '%d-%m-%Y') AS payment_date, payment_type, account_code FROM receipts WHERE company_id='".$company_id."' AND is_active = '0'";
		$payments_res = $callConfig->getAllRows($payments_sql);
		foreach($payments_res as $paytrans){
			$paytrans->company_id = $companies[$paytrans->company_id];	
			if($paytrans->payment_type == 'bank'){
				$paytrans->bankcash_name = $bank_accounts[$paytrans->account_code]['bank_account_name']; 
				$paytrans->ladger_name = $chart_of_accounts[$paytrans->account_code]->account_name;
			}else{
				$paytrans->bankcash_name = $cashaccount; 
				$paytrans->ladger_name = $chart_of_accounts[$cashaccount]->account_name;
			}

					
			//$paytrans->bank_account_id = $bank_accounts[$paytrans->bank_account_id];
			if(isset($item_trans[$paytrans->id])){
				$paytrans->item_entries = $item_trans[$paytrans->id];
			}else{
				$paytrans->item_entries = array();
			}
			// if(isset($journal_vouchers[$paytrans->id])){
			// 	$paytrans->ledger_entries = $journal_vouchers[$paytrans->id];
			// }else{
			// 	$paytrans->ledger_entries = array();
			// }
			$transaction_entry[] = $paytrans;
		}
		$return['status'] = 200;
		$return['result'] = $transaction_entry;
		//echo "<pre>"; print_r($return);
		return $return;
	}
	function getAPIJournalVouchers($get,$post){
		global $callConfig;
	 	$return = array();
	 	$uniquecode = $get['uniquecode'];
	 	$company_id = $get['company_id'];
		$companysql = "SELECT id, name FROM companies WHERE company_id='".$company_id."' AND is_active = '0'";
		$companysres = $callConfig->getAllRows($companysql);
		$companies = array();
	    foreach($companysres as $comp){
	        $companies[$comp->id] = $comp->name;
	    }


	    $jvisql = "SELECT transaction_id, account_name as ledger_name, debit_amount, credit_amount FROM jv_items WHERE company_id='".$company_id."' AND is_active = 0";
		$jvires = $callConfig->getAllRows($jvisql);
		$journal_vouchers = array();
		foreach($jvires as $jvi){
			$jvi->amount = ($jvi->debit_amount !="0" AND $jvi->debit_amount !="")?$jvi->debit_amount:$jvi->credit_amount;
			$jv_items[$jvi->transaction_id][] = $jvi;
		}

	   	$jvsql = "SELECT id, transaction_number, DATE_FORMAT(transaction_date, '%d-%m-%Y') AS transaction_date, ref_type, ref_id FROM journal_vouchers WHERE company_id='".$company_id."' AND is_active = 0";
		$jvres = $callConfig->getAllRows($jvsql);
		$journal_vouchers = array();
		foreach($jvres as $jv){
			//$journal_vouchers[$jv->ref_id][] = $jv;
			if(isset($jv_items[$jv->id])){
				$jv->ledger_entries = $jv_items[$jv->id];
			}else{
				$jv->ledger_entries = array();
			}
			$transaction_entry[] = $jv;
		}
	    $return['status'] = 200;
		$return['result'] = $transaction_entry;
		//echo "<pre>"; print_r($return);
		return $return;
	}
	function getAPIItems($get,$post){
		global $callConfig;
	 	$return = array();
	 	$uniquecode = $get['uniquecode'];
	 	$company_id = $get['company_id'];
		$companysql = "SELECT id, name FROM companies WHERE company_id='".$company_id."' AND is_active = '0'";
		$companysres = $callConfig->getAllRows($companysql);
		$companies = array();
	    foreach($companysres as $comp){
	        $companies[$comp->id] = $comp->name;
	    }

	    $unit_of_measurementssql = "SELECT id, uom_short_code FROM unit_of_measurements WHERE company_id='".$company_id."' AND is_active = '0'";
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
		//echo "<pre>"; print_r($return);
		return $return;
	}
	function getAPImastersAPICode($get,$post){
	 	global $callConfig;
	 	$return = array();
	 	$uniquecode = $get['uniquecode'];
	 	$company_id = $get['company_id'];
	 	$apimasterssql = "SELECT * FROM api_masters WHERE company_id='".$company_id."' AND api_code = '".$uniquecode."' AND is_active = '0'";
      	$apimastersres = $callConfig->getRow($apimasterssql);
      	$api_url = "";
  		if($apimastersres->id !=""){  // api code match
  			$api_master_id = $apimastersres->id;
  			$api_url = $apimastersres->end_point;
  			if($apimastersres->api_type == "S"){
  				$api_code = $apimastersres->api_code;
  				if($api_code == "SENDLEDGERMASTER"){   //Start ladger Masters
  					$return = $this->getLedgerMaster($get,$post);
					//return $return; 
				}elseif($api_code == "SENDSALESINVOICES" OR $api_code =="SENDSALESORDERS" OR $api_code == "SENDPURCHASEORDERS" OR $api_code == "SENDPURCHASEINVOICES" OR $api_code == "SENDGOODSRECEIPTNOTES" OR $api_code == "SENDDEBITNOTES" OR $api_code == "SENDCREDITNOTES" OR $api_code == "SENDDELIVERYORDERS"){   //Start Sales Invoice
					$return = $this->getTransactionInvoices($get,$post,$apimastersres->application_table);
					//echo "<pre>"; print_r($return);
				}elseif($api_code == "SENDPAYMENTS"){   //Start Sales Invoice
					$return = $this->getAPIPayments($get,$post);
				}elseif($api_code == "SENDRECEIPTS"){   //Start Sales Invoice
					$return = $this->getAPIReceipts($get,$post);
				}elseif($api_code == "SENDJOURNALVOUCHERS"){   //Start Sales Invoice
					$return = $this->getAPIJournalVouchers($get,$post);
				}elseif($api_code == "SENDITEMS"){   //Start Sales Invoice
					$return = $this->getAPIItems($get,$post);	
				}else{  //Start dynamic api

	  				//display attributes list
	  				$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$apimastersres->application_table."' AND is_active = 0";
					$displayattributesres =  $callConfig->getRow($displayattributessql);
					$fieldkeywithvalue = array();
					if($displayattributesres->list_attributes !=""){
						$lists = json_decode($displayattributesres->list_attributes,1);
						$type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
						foreach ($lists as $key => $value){
							if(in_array($value['type'], (array)$type_array)){
								$tablefield = $this->getDyanamicSelectData($apimastersres->application_table, $key); 
								if($tablefield['type'] != 'function'){
									$fieldkeywithvalue[$key] = $tablefield['res'];
								}else{
									$fieldkeywithvalue[$key] =  $tablefield;
								}
					 		}
						}
					}
					//echo "<pre>"; print_r($fieldkeywithvalue); echo "</pre>"; 
					//display attributes list

	  				$response_field_ary = json_decode($apimastersres->api_response_details, 1);
	  				$getstringlist = "";
	  				foreach($response_field_ary as $responsekey=>$fieldname){
	  					if($fieldname['application_table_field'] !=""){
	  						$getstringlist .= $fieldname['application_table_field']." as ".$responsekey.","; 
	  					}
	  				}
	  				if($getstringlist !=""){
	  					$getstringlists = substr($getstringlist, 0, -1);
	  				}else{
	  					$getstringlists = "*";
	  				}
	  				$query = "SELECT ".$getstringlists." FROM ".$apimastersres->application_table." WHERE company_id =".$get['company_id']." AND is_active=0";
	  				$data = $callConfig->getAllRows($query);
	  				if(count((array)$data) != '0'){
	  					$returnres = array();
	  					foreach($data as $resdata){
	  						$finalresdata = array();
	  						foreach($resdata as $keyfld=>$rsdata){
	  							if($fieldkeywithvalue[$keyfld][$rsdata] !=""){
	  								$finalresdata[$keyfld] = $fieldkeywithvalue[$keyfld][$rsdata];
	  							}else{
	  								$finalresdata[$keyfld] = $rsdata;
	  							}
	  						}
	  						$returnres[] = $finalresdata;
	  					}
	  					//echo "<pre>"; print_r($returnres); echo "</pre>"; 
	  					$return['status'] = 200;
			 			$return['result'] = $returnres;
			 		}else{
			 			$return['status'] = 404;
			 		}
			 	} //close dynamic api
  			}else{
  				//recievd /...................
  				$return = $post;

  			}
  		}else{
  			$api_master_id = 0;
  			$return['status'] = 403; // api code not match
  		}
  		$final_status = $return['status']!=""?$return['status']:"200";
  		$api_hits = array('company_id'=>$company_id,'api_url'=>$api_url,'api_master_id'=>$api_master_id,'api_request_parameters'=>urlencode(json_encode((array)$get)),'api_response_details'=>urlencode(json_encode((array)$return)), 'request_date_time'=> date('Y-m-d H:i:s'), 'response_status'=>$final_status, 'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id']);
    	$this->insertAPIHits($api_hits);
    	//echo "<pre>"; print_r($return);
	 	return $return;
	}
	function tallyAPIReceiveReceipts($get,$post,$api_hitsres,$apimastersres){
		global $callConfig;
		$api_hit_id = $post['api_hit_id'] !=""?$post['api_hit_id']:$get['api_hit_id'];
	 	$checkstatus = $post['checkstatus'] !=""?$post['checkstatus']:$get['checkstatus'];
	 	$company_id = $get['company_id'];
		//start get masters
	 	$application_userssql = "SELECT id,display_name, cost_center, location_id, default_warehouse_id, work_station_id, country_id, default_state_id, default_currency_id FROM application_users WHERE company_id = '".$company_id."' AND id = '".$get['user_id']."' AND is_active = 0";
		$application_usersres =  $callConfig->getRow($application_userssql);

		$chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND is_active =0";
	    $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
	    $chart_of_accounts = array();
	    foreach($chartofaccountsres as $cca){
	    	$chart_of_accounts_name[$cca->account_name] = $cca;
	        $chart_of_accounts[$cca->account_code] = $cca;
	    }
	    $customerssql = "SELECT ctm.customer_id, ctm.party_name, cust.id, cust.company, cust.account_code FROM customer_tally_mappings ctm, customers cust WHERE ctm.company_id = ".$company_id." AND ctm.company_id = cust.company_id AND ctm.customer_id = cust.id AND ctm.is_active = cust.is_active AND ctm.is_active = 0";
		$customersres = $callConfig->getAllRows($customerssql);
		$customers = array();
	    foreach($customersres as $custre){
	        $customers[$custre->party_name] = $custre;
	    }
		$banckaccountsql = "SELECT id, account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '".$company_id."' AND is_active=0";
		$banckaccountres = $callConfig->getAllRows($banckaccountsql);
    	foreach($banckaccountres as $payres){
			$bank_accounts[$payres->account_code] = (array)$payres;
		}	
		$sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id FROM sales_invoices WHERE company_id = '".$company_id."' AND is_active=0";
		$sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);    
    	foreach($sales_invoicesres as $si){
			$sales_invoices[$si->transaction_number] = (array)$si;
		}	
		
		//end get master
		$api_master_id = $apimastersres->id;
		$api_url = $apimastersres->end_point;
		$api_code = $apimastersres->api_code;
 		$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
		$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
		$responsary = $receipt_allocations= array();
		
		//recept list
		$receiptssql = "SELECT id, transaction_no, amount, unallocated_amount, allocated_amount, paid_amonut, tds_amount FROM receipts WHERE company_id='".$company_id."' AND is_active = '0'";
      	$receiptsres = $callConfig->getAllRows($receiptssql);
  		//$return['receipts'] = $receiptsres;

  		$receipt_allocationssql = "SELECT id, receipt_id, transaction_no AS bill_transaction_no , total_paid_amonut FROM receipt_allocations WHERE company_id='".$company_id."' AND is_active = '0'";
      	$receipt_allocationsres = $callConfig->getAllRows($receipt_allocationssql);
  		//$return['receipt_allocations'] = $receipt_allocationsres;

  		foreach($receipt_allocationsres as $recalcpt){  
			$receipt_allocations[$recalcpt->receipt_id] =  $recalcpt;
		}

		foreach($receiptsres as $recpt){  
			$recpt->bll_details = $receipt_allocations[$recpt->id];
			$receipts[$recpt->transaction_no] =  $recpt; //marge two table data
 		}
 		unset($receipts['REC00016']);
 		foreach($api_hit_api_response_details[$api_code] as $apihitre){  
			//$return['apihitre'] = $apihitre;
			$paymentarray = $paymentallocationarrayre = array();
			if(isset($receipts[$apihitre['VOUCHERNUMBER']])){
				$stringary['status'] = 1062; 
		        $stringary['query'] = $getstringlist;
		        $stringary['error'] = 'Duplicate Entry';
			}else{		
				
					//total amount recepts
					$recepttotalamt = 0;
					$party_id = array();
					$payment_date = $this->dateconvertdbformat($apihitre['InvDate']);
					$arychunks = array_chunk($apihitre['Ledger_Entries'], 2);
					$payment_type = "cash";
					$bank_account_id = $account_code="";
					foreach($arychunks as $partydetailandtransdetail){
						$recepttotalamt += $partydetailandtransdetail[0]['AMOUNT'];
						$receptaccountname = $partydetailandtransdetail[1]['Ledger_Name'];

						if(isset($customers[$partydetailandtransdetail[0]['Party_Name']])){
							$custid = $customers[$partydetailandtransdetail[0]['Party_Name']]->id;
							$party_id[$custid] = $custid;
						}
					}	
					//total amount recepts
					if(isset($chart_of_accounts_name[$receptaccountname])){
						$payment_account_details = $chart_of_accounts_name[$receptaccountname];
						if(isset($bank_accounts[$payment_account_details->account_code])){
							$bankaccountdetails = $bank_accounts[$payment_account_details->account_code];
							$payment_type = "bank";
							$bank_account_id = $bankaccountdetails['id'];
							$account_code = $bankaccountdetails['account_code'];
						}
					}
					//payment_type
					
					//strat insert transaction 
					
					$paymentarray = array('company_id'=>$company_id,'transaction_type'=>'customers','transaction_no'=>$transaction_no,'payment_date'=>$payment_date,'party_id'=>json_encode($party_id),'payment_type'=>$payment_type,'bank_account_id'=>$bank_account_id,'account_code'=>$account_code,'amount'=>$recepttotalamt,'ref_no'=>'','due_date'=>$payment_date,'unallocated_amount'=>0,'allocated_amount'=>$recepttotalamt,'remarks'=>'','originator'=>1,'is_active'=>'0','current_status'=>'199','paid_amonut'=>$recepttotalamt,'tds_amount'=>0,'payment_process'=>'NP','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"));
					
					
						foreach($arychunks as $partydetailandtransdetail){
							$transaction_details = $sales_invoices[$partydetailandtransdetail[0]['Bill_Details']['Bill_NO']];
							$paymentallocationarrayre[] = array('company_id'=>$company_id,'receipt_id'=>$receipt_id,'transaction_table'=>'sales_invoices','transaction_id'=>$transaction_details['id'],'transaction_no'=>$transaction_details['transaction_number'],'total_amount'=>$partydetailandtransdetail[0]['AMOUNT'],'pending_amount'=>0,'paid_amonut'=>$partydetailandtransdetail[0]['AMOUNT'],'is_active'=>'0','current_status'=>'0','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"),'tds_amount'=>0,'total_paid_amonut'=>$partydetailandtransdetail[0]['AMOUNT'],'total_tax_split_details'=>'','party_id'=>$transaction_details['party_id']);	
						}
					$popisosichildtablerequirdary = array('transaction_table','transaction_id','transaction_no');
					$popisosimaintablerequirdary = array('party_id','payment_type','amount','cost_center','location_id');
					foreach($application_usersres as $fieldkey=>$userdetails){
						if(in_array($fieldkey, (array)$popisosimaintablerequirdary) AND $fieldval ==""){
							$valuenotavailablefieldary[$fieldkey] =$fieldval;
						}
					}
					//Start VALIDATION PARTS
					foreach($paymentarray as $fieldkey=>$fieldval){
						if(in_array($fieldkey, (array)$popisosimaintablerequirdary) AND $fieldval ==""){
							$valuenotavailablefieldary[$fieldkey] =$fieldval;
						}
					}
					foreach($paymentallocationarrayre as $itemtble){
						foreach ($itemtble as  $fieldkey=>$fieldval) {	
							if(in_array($fieldkey, (array)$popisosichildtablerequirdary) AND $fieldval ==""){
								$valuenotavailablefieldary[$fieldkey] =$fieldval;
							}
						}
					}
					//End VALIDATION PART
			    if($checkstatus == "S" AND count((array)$valuenotavailablefieldary) ==0){ // Save
			    	$transaction_no	= $this->toGetApplicationSerialNumber('receipts');
			    	$paymentarray['$transaction_no'] =$transaction_no;  
			    	$res = $callConfig->insertRecord('receipts',$paymentarray);
					$receipt_id = $res['LASTID'];
					if($res['status'] == 'sucess'){
						foreach($paymentallocationarrayre as $paymentallocationarray){
							$paymentallocationarray['receipt_id'] = $receipt_id;
							$payallocationres[] = $callConfig->insertRecord('receipt_allocations',$paymentallocationarray);
						}
						$stringary['jvquery'] = $this->insertpaymentstojv($receipt_id,$get,'receipts');
					}
			    }	
			    
			    $stringary['transaction_item_res'] = $payallocationres;
		        $stringary['transaction_item'] = $paymentallocationarrayre;
				$stringary['status'] = 200; 
	        	$stringary['query'] = '';
	        	$stringary['transaction'] = $paymentarray;
	        	$stringary['transaction_res'] = $res;
	        	$stringary['error'] = 'success';

			}	  				
			$apihitre['response'] =$stringary; 
			$apihitre['valuenotavailablefieldary'] =$valuenotavailablefieldary;  
			$responsary[] = $apihitre;
		}
		//$return['responsary'] = $responsary;

		//echo "<pre>"; print_r($return);
 		return $responsary;
	}

	function tallyAPIReceivePayments($get,$post,$api_hitsres,$apimastersres){
		global $callConfig;
		$api_hit_id = $post['api_hit_id'] !=""?$post['api_hit_id']:$get['api_hit_id'];
	 	$checkstatus = $post['checkstatus'] !=""?$post['checkstatus']:$get['checkstatus'];
	 	$company_id = $get['company_id'];
		
		$application_userssql = "SELECT id,display_name, cost_center, location_id, default_warehouse_id, work_station_id, country_id, default_state_id, default_currency_id FROM application_users WHERE company_id = '".$company_id."' AND id = '".$get['user_id']."' AND is_active = 0";
		$application_usersres =  $callConfig->getRow($application_userssql);

		//start get masters
		$chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND is_active =0";
	    $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
	    $chart_of_accounts = array();
	    foreach($chartofaccountsres as $cca){
	    	$chart_of_accounts_name[$cca->account_name] = $cca;
	        $chart_of_accounts[$cca->account_code] = $cca;
	    }
	    $customerssql = "SELECT vtm.vendor_id, vtm.party_name, vend.id, vend.company, vend.account_code FROM vendor_tally_mappings vtm, vendors vend WHERE vtm.company_id = ".$company_id." AND vtm.company_id = vend.company_id AND vtm.vendor_id = vend.id AND vtm.is_active = vend.is_active AND vtm.is_active = 0";
		$customersres = $callConfig->getAllRows($customerssql);
		$customers = array();
	    foreach($customersres as $custre){
	        $customers[$custre->party_name] = $custre;
	    }
		$banckaccountsql = "SELECT id, account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '".$company_id."' AND is_active=0";
		$banckaccountres = $callConfig->getAllRows($banckaccountsql);
    	foreach($banckaccountres as $payres){
			$bank_accounts[$payres->account_code] = (array)$payres;
		}	
		$sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id FROM purchase_invoices WHERE company_id = '".$company_id."' AND is_active=0";
		$sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);    
    	foreach($sales_invoicesres as $si){
			$purchase_invoices[$si->transaction_number] = (array)$si;
		}	
		
		//end get master
		$api_master_id = $apimastersres->id;
		$api_url = $apimastersres->end_point;
		$api_code = $apimastersres->api_code;
 		$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
		$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
		$responsary = $receipt_allocations= array();
		
		//recept list
		$receiptssql = "SELECT id, transaction_no, amount, unallocated_amount, allocated_amount, paid_amonut, tds_amount FROM payments WHERE company_id='".$company_id."' AND is_active = '0'";
      	$receiptsres = $callConfig->getAllRows($receiptssql);
  		//$return['receipts'] = $receiptsres;

  		$receipt_allocationssql = "SELECT id, payment_id, transaction_no AS bill_transaction_no , total_paid_amonut FROM payment_allocations WHERE company_id='".$company_id."' AND is_active = '0'";
      	$receipt_allocationsres = $callConfig->getAllRows($receipt_allocationssql);
  		//$return['receipt_allocations'] = $receipt_allocationsres;

  		foreach($receipt_allocationsres as $recalcpt){  
			$receipt_allocations[$recalcpt->payment_id] =  $recalcpt;
		}

		foreach($receiptsres as $recpt){  
			$recpt->bll_details = $receipt_allocations[$recpt->id];
			$receipts[$recpt->transaction_no] =  $recpt; //marge two table data
 		}
 		foreach($api_hit_api_response_details[$api_code] as $apihitre){  
			//$return['apihitre'] = $apihitre;
			if(isset($receipts[$apihitre['VOUCHERNUMBER']])){
				$stringary['status'] = 1062; 
		        $stringary['query'] = $getstringlist;
		        $stringary['error'] = 'Duplicate Entry';
			}else{		
	
					$recepttotalamt = 0;
					$party_id = array();
					$payment_date = $this->dateconvertdbformat($apihitre['InvDate']);
					$arychunks = array_chunk($apihitre['Ledger_Entries'], 2);
					$payment_type = "cash";
					$bank_account_id = $account_code="";
					foreach($arychunks as $partydetailandtransdetail){
						$recepttotalamt += $partydetailandtransdetail[0]['AMOUNT'];
						$receptaccountname = $partydetailandtransdetail[1]['Ledger_Name'];

						if(isset($customers[$partydetailandtransdetail[0]['Party_Name']])){
							$custid = $customers[$partydetailandtransdetail[0]['Party_Name']]->id;
							$party_id[$custid] = $custid;
						}
					}	
					//total amount recepts
					if(isset($chart_of_accounts_name[$receptaccountname])){
						$payment_account_details = $chart_of_accounts_name[$receptaccountname];
						if(isset($bank_accounts[$payment_account_details->account_code])){
							$bankaccountdetails = $bank_accounts[$payment_account_details->account_code];
							$payment_type = "bank";
							$bank_account_id = $bankaccountdetails['id'];
							$account_code = $bankaccountdetails['account_code'];
						}
					}
					//payment_type
					
					//strat insert transaction 
					$paymentarray = array('company_id'=>$company_id,'transaction_type'=>'customers','transaction_no'=>$transaction_no,'payment_date'=>$payment_date,'party_id'=>json_encode($party_id),'payment_type'=>$payment_type,'bank_account_id'=>$bank_account_id,'account_code'=>$account_code,'amount'=>$recepttotalamt,'ref_no'=>'','due_date'=>$payment_date,'unallocated_amount'=>0,'allocated_amount'=>$recepttotalamt,'remarks'=>'','originator'=>1,'is_active'=>'0','current_status'=>'199','paid_amonut'=>$recepttotalamt,'tds_amount'=>0,'payment_process'=>'NP','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"));
					foreach($arychunks as $partydetailandtransdetail){
						$transaction_details = $purchase_invoices[$partydetailandtransdetail[0]['Bill_Details']['Bill_NO']];
						$paymentallocationarrayre[] = array('company_id'=>$company_id,'payment_id'=>$payment_id,'transaction_table'=>'purchase_invoices','transaction_id'=>$transaction_details['id'],'transaction_no'=>$transaction_details['transaction_number'],'total_amount'=>$partydetailandtransdetail[0]['AMOUNT'],'pending_amount'=>0,'paid_amonut'=>$partydetailandtransdetail[0]['AMOUNT'],'is_active'=>'0','current_status'=>'0','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"),'tds_amount'=>0,'total_paid_amonut'=>$partydetailandtransdetail[0]['AMOUNT'],'total_tax_split_details'=>'','party_id'=>$transaction_details['party_id']);
					}
					

				$popisosichildtablerequirdary = array('transaction_table','transaction_id','transaction_no');
				$popisosimaintablerequirdary = array('party_id','payment_type','amount','cost_center','location_id');
				foreach($application_usersres as $fieldkey=>$userdetails){
					if(in_array($fieldkey, (array)$popisosimaintablerequirdary) AND $fieldval ==""){
						$valuenotavailablefieldary[$fieldkey] =$fieldval;
					}
				}
				//Start VALIDATION PARTS
				foreach($paymentarray as $fieldkey=>$fieldval){
					if(in_array($fieldkey, (array)$popisosimaintablerequirdary) AND $fieldval ==""){
						$valuenotavailablefieldary[$fieldkey] =$fieldval;
					}
				}
				foreach($paymentallocationarrayre as $itemtble){
					foreach ($itemtble as  $fieldkey=>$fieldval) {	
						if(in_array($fieldkey, (array)$popisosichildtablerequirdary) AND $fieldval ==""){
							$valuenotavailablefieldary[$fieldkey] =$fieldval;
						}
					}
				}
				
				//End VALIDATION PART
			    if($checkstatus == "S" AND count((array)$valuenotavailablefieldary) ==0){ // Save
			    	$transaction_no	= $this->toGetApplicationSerialNumber('receipts');
			    	$paymentarray['$transaction_no'] =$transaction_no;  
			    	$res = $callConfig->insertRecord('receipts',$paymentarray);
					$receipt_id = $res['LASTID'];
					if($res['status'] == 'sucess'){
						foreach($paymentallocationarrayre as $paymentallocationarray){
							$paymentallocationarray['receipt_id'] = $receipt_id;
							$payallocationres[] = $callConfig->insertRecord('receipt_allocations',$paymentallocationarray);
						}
						$stringary['jvquery'] = $this->insertpaymentstojv($receipt_id,$get,'receipts');
					}
			    }	
			    
			    $stringary['transaction_item_res'] = $payallocationres;
		        $stringary['transaction_item'] = $paymentallocationarrayre;
				$stringary['status'] = 200; 
	        	$stringary['query'] = '';
	        	$stringary['transaction'] = $paymentarray;
	        	$stringary['transaction_res'] = $res;
	        	$stringary['error'] = 'success';

			}	  				
			$apihitre['response'] =$stringary; 
			$responsary[] = $apihitre;
		}
		$return['responsary'] = $responsary;
 		return $return;
	}
	function tallyAPIDebitANDCreditNoteTransactions($get,$post,$api_hitsres,$apimastersres){
		global $callConfig;
		$api_hit_id = $post['api_hit_id'] !=""?$post['api_hit_id']:$get['api_hit_id'];
	 	$checkstatus = $post['checkstatus'] !=""?$post['checkstatus']:$get['checkstatus'];
	 	$company_id = $get['company_id'];
	 	$api_code = $apimastersres->api_code;
	 	$application_table = $apimastersres->application_table;
	 	$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
	 	$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
		$responsary = $parties = array();

		$popisosichildtablerequirdary = array('account_code','item_name');
		$popisosimaintablerequirdary = array('cost_center_id','party_id','transaction_location_id','originator_currency_id','originator_state_id');
		
	 	$application_userssql = "SELECT id,display_name, cost_center, location_id, default_warehouse_id, work_station_id, country_id, default_state_id, default_currency_id FROM application_users WHERE company_id = '".$company_id."' AND id = '".$get['user_id']."' AND is_active = 0";
		$application_usersres =  $callConfig->getRow($application_userssql);
		$originator_currency_id = $application_usersres->default_currency_id;
	 	$originator_state_id = $application_usersres->default_state_id;

		$dmtsql = "SELECT id, page_title, application_table_name, display_table, item_table, tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$application_table."' AND item_table !='' AND tax_table !='' AND is_active = 0";
		$dmtres=$callConfig->getRow($dmtsql);

		$chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND is_active =0";
	    $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
	    $chart_of_accounts = array();
	    foreach($chartofaccountsres as $cca){
	    	$chart_of_accounts_name[$cca->account_name] = $cca;
	        $chart_of_accounts[$cca->account_code] = $cca;
	    }

		$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$application_table."' AND is_active = 0";
		$displayattributesres =  $callConfig->getRow($displayattributessql);


		 $customerssql = "SELECT ctm.customer_id, ctm.party_name, cust.id, cust.company, cust.account_code FROM customer_tally_mappings ctm, customers cust WHERE ctm.company_id = ".$company_id." AND ctm.company_id = cust.company_id AND ctm.customer_id = cust.id AND ctm.is_active = cust.is_active AND ctm.is_active = 0";
		$customersres = $callConfig->getAllRows($customerssql);
		$customers = array();
	    foreach($customersres as $custre){
	        $parties[$custre->party_name] = $custre;
	    }
		$vendorsql = "SELECT vtm.vendor_id, vtm.party_name, vend.id, vend.company, vend.account_code FROM vendor_tally_mappings vtm, vendors vend WHERE vtm.company_id = ".$company_id." AND vtm.company_id = vend.company_id AND vtm.vendor_id = vend.id AND vtm.is_active = vend.is_active AND vtm.is_active = 0";
		$vendorres = $callConfig->getAllRows($vendorsql);
		$customers = array();
	    foreach($vendorres as $vend){
	        $parties[$vend->party_name] = $vend;
	    }



		$transactionssql = "SELECT id, transaction_date, transaction_number, party_name, party_address, place_of_supply, party_phone, party_email FROM ".$application_table." WHERE company_id='".$company_id."' AND is_active = '0'";
      	$transactionres = $callConfig->getAllRows($transactionssql);
		foreach($transactionres as $recpt){  
			$transaction[$recpt->transaction_number] =  $recpt; //marge two table data
 		}

 		foreach($api_hit_api_response_details[$api_code] as $apihitre){  //api Hites response fileds
 			$valuenotavailablefieldary = array();
  			$stringary = array();
  			$maintransactionary = $transactionitemtaxary = $transactionitemsql = array();
  			if(isset($transaction[$apihitre['VOUCHERNUMBER']])){
				$stringary['status'] = 1062; 
		        $stringary['query'] = $getstringlist;
		        $stringary['error'] = 'Duplicate Entry';
			}else{	
				$currency_id = "";
				$taxarray = array();
				$party_id = $parties[$apihitre['PARTYNAME']]->id;
				$party_name = $parties[$apihitre['PARTYNAME']]->company;
				$mobile_number = $apihitre['mobile_number'] !="N/A"?$apihitre['mobile_number']:"";
				$email_address = $apihitre['email_address'] !="N/A"?$apihitre['email_address']:"";
				$tax_amount= 0;
				$maintotal_amount = $mainamount = $tax_amount = $tax_percentage   =$maintax_amount_total = 0;
				$tax_state = "I";
				foreach($apihitre['Item_Entries'] as $itmss){
					$maintotal_amount +=$itmss['AMOUNT'];  
				}	

				$maintransactionary = array('company_id'=>$company_id,'transaction_number'=>$apihitre['VOUCHERNUMBER'],'transaction_date'=>$this->dateconvertdbformat($apihitre['InvDate']),'cost_center_id'=>$application_usersres->cost_center,'party_name'=>$party_name,'party_id'=>$party_id,'party_address'=>$apihitre['STATE'],'supplier_state_id'=>'','place_of_supply'=>'','party_phone'=>$mobile_number,'party_email'=>$email_address,'amount'=>$maintotal_amount,'tax_amount'=>0,'total_amount'=>$maintotal_amount,'pending_amount'=>$maintotal_amount,'transaction_location_id'=>$application_usersres->location_id, 'transaction_warehouse_id'=>$application_usersres->default_warehouse_id, 'originator_currency_id'=>$originator_currency_id, 'currency_id'=>$currency_id, 'currency_rate'=>'1' ,'originator'=>$get['user_id'], 'originator_state_id'=>$originator_state_id,'payment_terms'=>'','payment_term_history'=>'', 'due_date'=>$this->dateconvertdbformat($apihitre['InvDate']),'tax_state'=>$tax_state,'is_active'=>'0','current_status'=>'199','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"));
				
				foreach($apihitre['Item_Entries'] as $itementry){
					$tax_amount = $tax_percentage  = $amount =$tax_total_amount = 0;	
					$child_total_amount = $itementry['AMOUNT'];
					$tax_id = $tax_name = "";
					if(isset($chart_of_accounts_name[$itementry['Stock_Item']])){
						$account_code = $chart_of_accounts_name[$itementry['Stock_Item']]->account_code;
						$account_name = $chart_of_accounts_name[$itementry['Stock_Item']]->account_name;
					}
					$transactionitemsql[] = array('company_id'=>$company_id, 'transaction_id'=>$transaction_id, 'item_id'=>0, 'account_code'=>$account_code, 'item_name'=>$account_name, 'description'=>$account_name, 'purchase_uom'=>'UN', 'item_quantity'=>1, 'item_price'=>$child_total_amount, 'item_amount'=>$child_total_amount, 'tax_amount'=>0, 'discount_amount'=>'0', 'total_amount'=>$child_total_amount, 'reason_id'=>'', 'item_st_cost'=>'0','item_fifo_cost'=>'0', 'item_lifo_cost'=>'0', 'item_wa_cost'=>'0', 'approved_quantity'=>1, 'transaction_type'=>'', 'transaction_qty'=>1, 'pending_qty'=>1, 'item_details'=>'', 'tax_deducted_source_id'=>'', 'tds_amount'=>'0', 'is_active'=>'0', 'created_date'=>date("Y-m-d H:i:s"), 'created_by'=>'1');
				}
				//Start VALIDATION PARTS
				
				// if(round($tallywiithgstamount,2) != round($maintotal_amount,2)){
				// 	$valuenotavailablefieldary['total_amount'] =$maintotal_amount;
				// }
				foreach($maintransactionary as $fieldkey=>$fieldval){
					if(in_array($fieldkey, (array)$popisosimaintablerequirdary) AND $fieldval ==""){
						$valuenotavailablefieldary[$fieldkey] =$fieldval;
					}
				}
				foreach($transactionitemsql as $itemtble){
					foreach ($itemtble as  $fieldkey=>$fieldval) {	
						if(in_array($fieldkey, (array)$popisosichildtablerequirdary) AND $fieldval ==""){
							$valuenotavailablefieldary[$fieldkey] =$fieldval;
						}
					}
				}
				//End VALIDATION PART

				if($checkstatus == "S" AND count((array)$valuenotavailablefieldary) ==0){ 
					$maintransactionaryres = $callConfig->insertRecord($dmtres->application_table_name,$maintransactionary);
					$transaction_id = $maintransactionaryres['LASTID'];
				}
				if($checkstatus == "S" && $maintransactionaryres['status'] == 'sucess' AND count((array)$valuenotavailablefieldary) ==0){ 
					foreach($transactionitemsql as $transitemarry){
						$transitemarry['transaction_id'] = $transaction_id;
			        	$transactionitemres[] = $callConfig->insertRecord($dmtres->item_table,$transitemarry);
			    	}
	        	}
	        	if($checkstatus == "S" && $maintransactionaryres['status'] == 'sucess' AND count((array)$valuenotavailablefieldary) ==0 AND $transaction_id !=""){

	        		$get['transaction_table'] = $dmtres->application_table_name;
					$this->updateTransactionItemsQty($get,$apihitre,"-");
					
					$stringary['payments_result'] = $this->tocreatejounalvoucherbaseallcases($dmtres->application_table_name,$transaction_id);
		        }

	        	$stringary['transaction_item_res'] = $transactionitemres;
		        $stringary['transaction_item'] = $transactionitemsql;
				$stringary['status'] = 200; 
	        	$stringary['query'] = $paymentarray;
	        	$stringary['transaction'] = $maintransactionary;
	        	$stringary['transaction_res'] = $maintransactionaryres;
	        	$stringary['error'] = 'success';
						
			}	  				
			//stringary
			$apihitre['response'] =$stringary; 
			$apihitre['valuenotavailablefieldary'] =$valuenotavailablefieldary; 
			$responsary[] = $apihitre;
		}

















//$return['responsary'] = $responsary;
		//echo "<pre>"; print_r($responsary); exit;
 		return $responsary;









	}
	function tallyAPIReceiveTransactions($get,$post,$api_hitsres,$apimastersres){
		global $callConfig;
		
		$api_hit_id = $post['api_hit_id'] !=""?$post['api_hit_id']:$get['api_hit_id'];
	 	$checkstatus = $post['checkstatus'] !=""?$post['checkstatus']:$get['checkstatus'];
	 	$company_id = $get['company_id'];
	 	$api_code = $apimastersres->api_code;
	 	$application_table = $apimastersres->application_table;
	 	$withoutwarehouse = array('RECEIVEPURCHASEORDERS','RECEIVEPURCHASEINVOICES','RECEIVESALESINVOICES','RECEIVESALESORDERS');
		$withwarehouse = array('RECEIVEGOODSRECEIPTNOTES','RECEIVEDELIVERYORDERS');
		$popisosichildtablerequirdary = array('item_id','item_name','purchase_uom');
		if(in_array($api_code, $withoutwarehouse)){
			$popisosimaintablerequirdary = array('cost_center_id','party_id','transaction_location_id','originator_currency_id','originator_state_id');
		}elseif(in_array($api_code, $withwarehouse)){
			$popisosimaintablerequirdary = array('cost_center_id','party_id','transaction_location_id','transaction_warehouse_id','originator_currency_id','originator_state_id');
		}
	 	$application_userssql = "SELECT id,display_name, cost_center, location_id, default_warehouse_id, work_station_id, country_id, default_state_id, default_currency_id FROM application_users WHERE company_id = '".$company_id."' AND id = '".$get['user_id']."' AND is_active = 0";
		$application_usersres =  $callConfig->getRow($application_userssql);

		$dmtsql = "SELECT id, page_title, application_table_name, display_table, item_table, tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$application_table."' AND item_table !='' AND tax_table !='' AND is_active = 0";
		$dmtres=$callConfig->getRow($dmtsql);

		$itemssql = "SELECT itm.id, itm.sku, itm.item_name, itm.item_description, itm.purchase_uom, itm.sale_uom, itm.hsn_and_sac_code_id, itm.inventory_asset_account, hsnsac.gst, hsnsac.code, hsnsac.tax_authority_id FROM items itm LEFT JOIN hsn_and_sac_codes hsnsac ON itm.company_id = hsnsac.company_id AND itm.hsn_and_sac_code_id = hsnsac.id AND itm.is_active = hsnsac.is_active WHERE itm.company_id='".$company_id."' AND itm.is_active = '0'"; 
      	$itemsres = $callConfig->getAllRows($itemssql);
		foreach($itemsres as $itmre){  
			$items[$itmre->sku] =  $itmre; //marge two table data
 		}
	 	$transaction_location_id = "";
	 	$transaction_warehouse_id = "";
	 	$originator_currency_id = $application_usersres->default_currency_id;
	 	$originator_state_id = $application_usersres->default_state_id;
	 	//echo $application_table; 
	 	$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
	 	//echo "<pre>"; print_r($api_hit_api_response_details);
		$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
		$responsary = array();

		$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$application_table."' AND is_active = 0";
		$displayattributesres =  $callConfig->getRow($displayattributessql);
				//echo "<pre>"; print_r($displayattributesres); exit;
		$fieldkeywithvalue = array();
		$checklists = array('InvDate'=>'transaction_date', 'VOUCHERNUMBER'=>'transaction_number', 'PARTYNAME'=>'party_name', 'STATE'=>'party_address', 'PLACEOFSUPPLY'=>'place_of_supply', 'mobile_number'=>'party_phone', 'email_address'=>'party_email');
		foreach($checklists as $checklis){
			$checkfiled[] = $checklis;
		}
		if($displayattributesres->list_attributes !=""){
			$lists = json_decode($displayattributesres->list_attributes,1);
			$type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
			//print_r($lists);
			foreach ($lists as $key => $value){
				//if(in_array($value['type'], (array)$checkfiled)){
					if(in_array($value['type'], (array)$type_array)){
						$listsechck[] = $key;
						$tablefield = $this->getDyanamicSelectData($application_table, $key); 
						if($tablefield['type'] != 'function'){
							$reverkeyvaluearay = array();
							foreach($tablefield['res'] as $kkey=>$valuu){
								$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
								$reverkeyvaluearay[$removewhinewline] = $kkey;
							}
							$fieldkeywithvalue[$key] = $reverkeyvaluearay;
						}else{
							$reverkeyvaluearay = array();
							foreach($tablefield as $kkey=>$valuu){
								$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
								$reverkeyvaluearay[$removewhinewline] = $kkey;
							}
							$fieldkeywithvalue[$key] =  $reverkeyvaluearay;
						}
			 		}
			 	//}
			}
		}	
		$transactionssql = "SELECT id, transaction_date, transaction_number, party_name, party_address, place_of_supply, party_phone, party_email FROM ".$application_table." WHERE company_id='".$company_id."' AND is_active = '0'";
      	$transactionres = $callConfig->getAllRows($transactionssql);
		foreach($transactionres as $recpt){  
			$transaction[$recpt->transaction_number] =  $recpt; //marge two table data
 		}
 		foreach($api_hit_api_response_details[$api_code] as $apihitre){  //api Hites response fileds
 			$valuenotavailablefieldary = array();
  			$stringary = array();
  			$maintransactionary = $transactionitemtaxary = $transactionitemsql = array();
  			if(isset($transaction[$apihitre['VOUCHERNUMBER']])){
				$stringary['status'] = 1062; 
		        $stringary['query'] = $getstringlist;
		        $stringary['error'] = 'Duplicate Entry';
			}else{	
				$currency_id = "";
				$taxarray = array();
				$party_id = $fieldkeywithvalue['party_id'][$apihitre['PARTYNAME']];
				if(isset($fieldkeywithvalue['place_of_supply'][$apihitre['PLACEOFSUPPLY']])){
					$place_of_supply = $fieldkeywithvalue['place_of_supply'][$apihitre['PLACEOFSUPPLY']];
				}
				if(isset($fieldkeywithvalue['supplier_state_id'][$apihitre['STATE']])){
					$supplier_state_id = $fieldkeywithvalue['supplier_state_id'][$apihitre['STATE']];
				}
				$mobile_number = $apihitre['mobile_number'] !="N/A"?$apihitre['mobile_number']:"";
				$email_address = $apihitre['email_address'] !="N/A"?$apihitre['email_address']:"";
				$tallywiithgstamount = str_replace(",","",$apihitre['Voucher_Amount']);
				$tax_amount= 0;
				$maintotal_amount = $mainamount = $tax_amount = $tax_percentage   =$maintax_amount_total = 0;
				foreach($apihitre['Item_Entries'] as $itmss){
					//$amount += $itmss['AMOUNT'];
					//$total_amount +=  $itmss['AMOUNT'];
					if(isset($items[$itmss['part_no']])){
						$tax_percentage = $items[$itmss['part_no']]->gst;
						if($tax_percentage !=""){
							$tax_amount = $itmss['AMOUNT']*($tax_percentage/100);
							$maintax_amount_total +=$tax_amount;  
							$maintotal_amount += $itmss['AMOUNT']+$tax_amount;
							$mainamount +=$itmss['AMOUNT'];  
						}else{
							$maintotal_amount += $itmss['AMOUNT'];
							$mainamount +=$itmss['AMOUNT']; 
						}
					}else{
						$maintotal_amount += $itmss['AMOUNT'];
						$mainamount +=$itmss['AMOUNT'];  
					}
				}	
				if($supplier_state_id == $originator_state_id){
					$tax_state = "S";
				}else{
					$tax_state = "I";
				}
				$maintransactionary = array('company_id'=>$company_id,'transaction_number'=>$apihitre['VOUCHERNUMBER'],'transaction_date'=>$this->dateconvertdbformat($apihitre['InvDate']),'cost_center_id'=>$application_usersres->cost_center,'party_name'=>$apihitre['PARTYNAME'],'party_id'=>$party_id,'party_address'=>$apihitre['STATE'],'supplier_state_id'=>$supplier_state_id,'place_of_supply'=>$place_of_supply,'party_phone'=>$mobile_number,'party_email'=>$email_address,'amount'=>$mainamount,'tax_amount'=>$maintax_amount_total,'total_amount'=>$maintotal_amount,'pending_amount'=>$maintotal_amount,'transaction_location_id'=>$application_usersres->location_id, 'transaction_warehouse_id'=>$application_usersres->default_warehouse_id, 'originator_currency_id'=>$originator_currency_id, 'currency_id'=>$currency_id, 'currency_rate'=>'1' ,'originator'=>$get['user_id'], 'originator_state_id'=>$originator_state_id,'payment_terms'=>'','payment_term_history'=>'', 'due_date'=>$this->dateconvertdbformat($apihitre['InvDate']),'tax_state'=>$tax_state,'is_active'=>'0','current_status'=>'199','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"));
				
				foreach($apihitre['Item_Entries'] as $itementry){
					$tax_amount = $tax_percentage  = $amount =$tax_total_amount = 0;	
					$total_amount = $itementry['AMOUNT'];
					$tax_id = $tax_name = "";
					if(isset($items[$itementry['part_no']])){
						$item_id = $items[$itementry['part_no']]->id;
						$account_code = $items[$itementry['part_no']]->inventory_asset_account;
						$item_name = $items[$itementry['part_no']]->item_name;
						$description = $items[$itementry['part_no']]->item_description;
						$purchase_uom = $items[$itementry['part_no']]->purchase_uom;
						$tax_percentage = $items[$itementry['part_no']]->gst;
						if($tax_percentage !=""){

							$tax_amount = $itementry['AMOUNT']*($tax_percentage/100);
							$total_amount = $total_amount+$tax_amount;
							$tax_id =$items[$itementry['part_no']]->hsn_and_sac_code_id;
							$tax_percentage =$tax_percentage;
							$tax_name =$items[$itementry['part_no']]->code;
							$amount =$total_amount;  
							$tax_total_amount =$total_amount; 
							$details =$items[$itementry['part_no']];

							$transactionitemtaxary[] = array('company_id'=>$company_id, 'transaction_id'=>$transaction_id, 'transaction_item_id'=>$item_id, 'tax_name'=>$tax_name, 'tax_value'=>$tax_percentage, 'tax_id'=>$tax_id, 'amount'=>$itementry['AMOUNT'], 'tax_amount'=>$tax_amount, 'total_amount'=>$total_amount, 'tax_type'=>$tax_state, 'tax_split_details'=>'', 'is_active'=>0, 'current_status'=>0, 'created_date'=>date("Y-m-d H:i:s"), 'created_by'=>0);

						}
					}
					$transactionitemsql[] = array('company_id'=>$company_id, 'transaction_id'=>$transaction_id, 'item_id'=>$item_id, 'account_code'=>$account_code, 'item_name'=>$item_name, 'description'=>$description, 'purchase_uom'=>$purchase_uom, 'item_quantity'=>$itementry['QUANTITY'], 'item_price'=>$itementry['RATE'], 'item_amount'=>$itementry['AMOUNT'], 'tax_amount'=>$tax_amount, 'discount_amount'=>'0', 'total_amount'=>$total_amount, 'reason_id'=>'', 'item_st_cost'=>'0','item_fifo_cost'=>'0', 'item_lifo_cost'=>'0', 'item_wa_cost'=>'0', 'approved_quantity'=>$itementry['QUANTITY'], 'transaction_type'=>'', 'transaction_qty'=>$itementry['QUANTITY'], 'pending_qty'=>$itementry['QUANTITY'], 'item_details'=>'', 'tax_deducted_source_id'=>'', 'tds_amount'=>'0', 'is_active'=>'0', 'created_date'=>date("Y-m-d H:i:s"), 'created_by'=>'1');
					
		       
				}
				//VALIDATION PARTS
				
				if(round($tallywiithgstamount,2) != round($maintotal_amount,2)){
					$valuenotavailablefieldary['total_amount'] =$maintotal_amount;
				}
				foreach($maintransactionary as $fieldkey=>$fieldval){
					if(in_array($fieldkey, (array)$popisosimaintablerequirdary) AND $fieldval ==""){
						$valuenotavailablefieldary[$fieldkey] =$fieldval;
					}
				}
				foreach($transactionitemsql as $itemtble){
					foreach ($itemtble as  $fieldkey=>$fieldval) {	
						//echo $fieldkey."====".$fieldval."</br>";
						if(in_array($fieldkey, (array)$popisosichildtablerequirdary) AND $fieldval ==""){
							$valuenotavailablefieldary[$fieldkey] =$fieldval;
						}
					}
				}
				//VALIDATION PART

				if($checkstatus == "S" AND count((array)$valuenotavailablefieldary) ==0){ 
					$maintransactionaryres = $callConfig->insertRecord($dmtres->application_table_name,$maintransactionary);
					$transaction_id = $maintransactionaryres['LASTID'];
				}
				if($checkstatus == "S" && $maintransactionaryres['status'] == 'sucess' AND count((array)$valuenotavailablefieldary) ==0){ 
					foreach($transactionitemsql as $transitemarry){
						$transitemarry['transaction_id'] = $transaction_id;
			        	$transactionitemres[] = $callConfig->insertRecord($dmtres->item_table,$transitemarry);
			    	}
			        foreach($transactionitemtaxary as $transitemtaxarry){
			        	$transitemtaxarry['transaction_id'] = $transaction_id;
		        		$transactionitemtaxres[] = $callConfig->insertRecord($dmtres->tax_table,$transitemtaxarry);
		        	}
	        	}

				if($checkstatus == "S" && $maintransactionaryres['status'] == 'sucess' AND count((array)$valuenotavailablefieldary) ==0){
					$get['transaction_table'] = $dmtres->application_table_name;
					$this->updateTransactionItemsQty($get,$apihitre,"-");
					
					$stringary['payments_result'] = $this->tocreatejounalvoucherbaseallcases($dmtres->application_table_name,$transaction_id);
		        }
	        	$stringary['transaction_item_res'] = $transactionitemres;
		        $stringary['transaction_item'] = $transactionitemsql;
		        $stringary['transaction_item_tax_ary'] = $transactionitemtaxary;
		        $stringary['transaction_item_tax'] = $transactionitemtaxres;
				$stringary['status'] = 200; 
	        	$stringary['query'] = $paymentarray;
	        	$stringary['transaction'] = $maintransactionary;
	        	$stringary['transaction_res'] = $maintransactionaryres;
	        	$stringary['error'] = 'success';
	        	$stringary['error'] = 'success';
						
			}	  				
			//stringary
			$apihitre['response'] =$stringary; 
			$apihitre['valuenotavailablefieldary'] =$valuenotavailablefieldary; 
			$responsary[] = $apihitre;
		}
		//$return['responsary'] = $responsary;
		//echo "<pre>"; print_r($responsary); exit;
 		return $responsary;
	}

	function checkAPIDataValidateOrNot($get,$post){
		global $callConfig;
 		$return['post'] = $post;
 		$api_hit_id = $post['api_hit_id'] !=""?$post['api_hit_id']:$get['api_hit_id'];
	 	$checkstatus = $post['checkstatus'] !=""?$post['checkstatus']:$get['checkstatus'];
 		//$api_hit_id = $post['api_hit_id'];
	 	//$checkstatus = $post['checkstatus'];
	 	$api_hitssql = "SELECT * FROM api_hits WHERE company_id='".$get['company_id']."' AND id = '".$api_hit_id."' AND is_active = '0'";
      	$api_hitsres = $callConfig->getRow($api_hitssql);
      	$return['api_hits'] = $api_hitsres;
  		if($api_hitsres->api_master_id !=""){  // api code match
  			$apimasterssql = "SELECT * FROM api_masters WHERE company_id='".$get['company_id']."' AND id = '".$api_hitsres->api_master_id."' AND is_active = '0'";
	      	$apimastersres = $callConfig->getRow($apimasterssql);
      		$return['api_masters'] = $apimastersres;

  			$api_master_id = $apimastersres->id;
  			$api_url = $apimastersres->end_point;
  			$api_code = $apimastersres->api_code;

  			////echo $api_code;
  			//$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
  			//echo "<pre>"; print_r($api_hit_api_response_details);
  			if($apimastersres->api_type == "S"){
				//Send /...................
  				$return['status'] = 4040404;
			}else{
				if($api_code == "RECEIVERECEIPTS"){
					$responsary = $this->tallyAPIReceiveReceipts($get,$post,$api_hitsres,$apimastersres);
				}elseif($api_code == "SENDPAYMENTS"){
					$responsary = $this->tallyAPIReceivePayments($get,$post,$api_hitsres,$apimastersres);
				
				}elseif($api_code == "RECEIVEPURCHASEORDERS" || $api_code == "RECEIVEPURCHASEINVOICES" || $api_code == "RECEIVEGOODSRECEIPTNOTES" || $api_code == "RECEIVESALESINVOICES" || $api_code == "RECEIVESALESORDERS" || $api_code == "RECEIVEDELIVERYORDERS"){
					$responsary = $this->tallyAPIReceiveTransactions($get,$post,$api_hitsres,$apimastersres);
				
				}elseif($api_code == "RECEIVEDEBITNOTES" || $api_code == "RECEIVECREDITNOTES"){
					$responsary = $this->tallyAPIDebitANDCreditNoteTransactions($get,$post,$api_hitsres,$apimastersres);
				
				}else{	
					//display attributes list
	  				$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$apimastersres->application_table."' AND is_active = 0";
					$displayattributesres =  $callConfig->getRow($displayattributessql);
					$fieldkeywithvalue = array();
					if($displayattributesres->list_attributes !=""){
						$lists = json_decode($displayattributesres->list_attributes,1);
						$type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
						foreach ($lists as $key => $value){
							if(in_array($value['type'], (array)$type_array)){
								$tablefield = $this->getDyanamicSelectData($apimastersres->application_table, $key); 
								if($tablefield['type'] != 'function'){
									$reverkeyvaluearay = array();
									foreach($tablefield['res'] as $kkey=>$valuu){
										$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
										$reverkeyvaluearay[$removewhinewline] = $kkey;
									}
									$fieldkeywithvalue[$key] = $reverkeyvaluearay;
								}else{
									$reverkeyvaluearay = array();
									foreach($tablefield as $kkey=>$valuu){
										$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
										$reverkeyvaluearay[$removewhinewline] = $kkey;
									}
									$fieldkeywithvalue[$key] =  $reverkeyvaluearay;
								}
					 		}
						}
					}
					//Display Attributes
					//print_r($fieldkeywithvalue);
					$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
					$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
					$responsary = array();
	  				foreach($api_hit_api_response_details[$api_code] as $apihitre){  //api Hites response fileds
	  					$sqlfieldstring =$sqlvaluestring  = $getstringlist = $alreadyavilablecheck = "";
	  					$alreadyavilablesql = "SELECT id FROM ".$apimastersres->application_table." WHERE "; 
	  					$getstringlist = "INSERT INTO ".$apimastersres->application_table." (";
	  					foreach($api_mastersr_api_response_details as $valuefield=>$tablefield){ //api master response fileds
	  						if($tablefield['application_table_field'] !=""){
	  							$sqlfieldstring .= "`".$tablefield['application_table_field']."`,"; 
	  						}						
	  					}
		  				$sqlfieldstring .="`is_active`,`current_status`"; 
	  					$getstringlist .= $sqlfieldstring.") VALUES (";
	  					foreach($api_mastersr_api_response_details as $valuefield=>$tablefield){ //api master response fileds
	  						if($tablefield['application_table_field'] !=""){
	  							if($fieldkeywithvalue[$tablefield['application_table_field']][$apihitre[$valuefield]] !=""){
	  								$sqlvaluestring .= "'".$fieldkeywithvalue[$tablefield['application_table_field']][$apihitre[$valuefield]]."',";

	  								if($tablefield['uniqueness'] == 'Y'){
	  									$alreadyavilablecheck = "Y";
	  									$alreadyavilablesql .= " ".$tablefield['application_table_field']."='".$fieldkeywithvalue[$tablefield['application_table_field']][$apihitre[$valuefield]]."' AND";
	  								} 

	  							}else{
	  								$sqlvaluestring .= "'".$apihitre[$valuefield]."',"; 
	  								if($tablefield['uniqueness'] == 'Y'){
	  									$alreadyavilablecheck = "Y";
	  									$alreadyavilablesql .= " ".$tablefield['application_table_field']."='".$apihitre[$valuefield]."' AND"; 
	  								}
	  							}	
	  						}
	  					}
	  					if($alreadyavilablecheck =="Y"){
	  						$alreadyavilablesql .= " is_active = 0";
	  						//echo $alreadyavilablesql; echo "</br>";
	  						$alreadyavilableresult = $callConfig->getRow($alreadyavilablesql);
	  					}else{
	  						$alreadyavilableresult->id = "";
	  					}
	  					$sqlvaluestring .= "0,0";
		  				$getstringlist .= $sqlvaluestring.")";
	  					if($alreadyavilableresult->id !=""){
	  						$stringary['status'] = 1062; 
					        $stringary['query'] = $getstringlist;
					        $stringary['error'] = 'Duplicate Entry';
	  					}else{		
	  						if($checkstatus == "S"){
	  							$savgdsc = $callConfig->executeQuery($getstringlist);
	  							if($savgdsc['status'] == 'sucess'){
		  							$stringary['status'] = 200; 
							        $stringary['query'] = $getstringlist;
							        $stringary['error'] = 'success';
						    	}else{
						    		$stringary['status'] = $savgdsc['error_no']; 
							        $stringary['query'] = $getstringlist;
							        $stringary['error'] = $savgdsc['message']; 
						    	}
	  						}else{
	  							$stringary = $callConfig->checkExecuteQueryString($getstringlist);
	  						}		
		  				}	  				
	  					$apihitre['response'] =$stringary; 
	  					$responsary[] = $apihitre;
	  				}	

	  				if($checkstatus == "S"){
	  					$api_hits_sync = array('company_id'=>$get['company_id'],'api_hit_id'=>$api_hit_id,'originator'=>$get['user_id'],'application_table'=>$apimastersres->application_table,'sync_datetime'=>date('Y-m-d H:i:s'), 'sync_details'=>urlencode(json_encode((array)$responsary)),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
	  					$syncres = $callConfig->insertRecord('api_hit_sync_with_db',$api_hits_sync);	
					}

				}	





  			}
  		}else{
  			$return['status'] = 403; // api code not match
  		}
  		$return['result'] = $responsary;
	 	return $return;
	}


	function toSendItemsToTally($get){
	 	global $callConfig;
	 	$query = "SELECT company_id, sku, item_name FROM items WHERE company_id =".$get['company_id']." AND is_active=0";
 		$data = $callConfig->getAllRows($query);
 		$return['result'] = $data;
	 	return $return;
	}
	function tosendCostCentersToTally($get){
	 	global $callConfig;
	 	$query = "SELECT company_id, cost_center_name FROM cost_centers WHERE company_id =".$get['company_id']." AND is_active=0";
 		$data = $callConfig->getAllRows($query);
 		$return['result'] = $data;
	 	return $return;
	}
	function toSendUnitOfMeasurementsToTally($get){
	 	global $callConfig;
	 	$query = "SELECT company_id, uom_short_code, uom_name FROM unit_of_measurements WHERE company_id =".$get['company_id']." AND is_active=0";
 		$data = $callConfig->getAllRows($query);
 		$return['result'] = $data;
	 	return $return;
	}
	function toSendWarehousesToTally($get){
	 	global $callConfig;
	 	$query = "SELECT company_id, warehouse_short_name, warehouse_name FROM warehouses WHERE company_id =".$get['company_id']." AND is_active=0";
 		$data = $callConfig->getAllRows($query);
 		$return['result'] = $data;
	 	return $return;
	}

	
	/**
	function tocheckdynamictransactionall($get){
		global $callConfig;
		echo "<pre>";
		$api_hit_id = $get['api_hit_id'];
	 	$checkstatus = $get['checkstatus'];
	 	$api_hitssql = "SELECT * FROM api_hits WHERE company_id='".$get['company_id']."' AND id = '".$api_hit_id."' AND is_active = '0'";
      	$api_hitsres = $callConfig->getRow($api_hitssql);
      	$return['api_hits'] = $api_hitsres;
		
      	if($api_hitsres->api_master_id !=""){  // api code match
  			

  			$apimasterssql = "SELECT * FROM api_masters WHERE company_id='".$get['company_id']."' AND id = '".$api_hitsres->api_master_id."' AND is_active = '0'";
	      	$apimastersres = $callConfig->getRow($apimasterssql);
      		$return['api_masters'] = $apimastersres;

  			$api_master_id = $apimastersres->id;
  			$api_url = $apimastersres->end_point;
  			$api_code = $apimastersres->api_code;
  			
  			if($apimastersres->api_type == "S"){
				//Send /...................
  				$return['status'] = 4040404;
			


			}else{



				$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$apimastersres->application_table."' AND is_active = 0";
				$displayattributesres =  $callConfig->getRow($displayattributessql);
				$fieldkeywithvalue = array();
				$checklists = array('InvDate'=>'transaction_date', 'VOUCHERNUMBER'=>'transaction_number', 'PARTYNAME'=>'party_name', 'STATE'=>'party_address', 'PLACEOFSUPPLY'=>'place_of_supply', 'mobile_number'=>'party_phone', 'email_address'=>'party_email');
				/*if($checkstatus == "S"){
					$checklists['transaction_number']=>'transaction_number';
				}*/ /** ROHAN
				

				foreach($checklists as $checklis){
					$checkfiled[] = $checklis;
				}

				

				if($displayattributesres->list_attributes !=""){
					$lists = json_decode($displayattributesres->list_attributes,1);
					$type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
					foreach ($lists as $key => $value){
						if(in_array($key, (array)$checkfiled)){
							if(in_array($value['type'], (array)$type_array)){
								$listsechck[] = $key;
								$tablefield = $this->getDyanamicSelectData($apimastersres->application_table, $key); 
								if($tablefield['type'] != 'function'){
									$reverkeyvaluearay = array();
									foreach($tablefield['res'] as $kkey=>$valuu){
										$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
										$reverkeyvaluearay[$removewhinewline] = $kkey;
									}
									$fieldkeywithvalue[$key] = $reverkeyvaluearay;
								}else{
									$reverkeyvaluearay = array();
									foreach($tablefield as $kkey=>$valuu){
										$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
										$reverkeyvaluearay[$removewhinewline] = $kkey;
									}
									$fieldkeywithvalue[$key] =  $reverkeyvaluearay;
								}
					 		}
					 	}
					}
				}


				$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
				$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
				$responsary = array();
				//echo $api_code;
				//print_r($api_hit_api_response_details); exit;
				foreach($api_hit_api_response_details[$api_code] as $apihitre){  
					//api Hites response fileds
					//echo "<pre>"; print_r($apihitre); exit;
  					$sqlfieldstring =$sqlvaluestring  = $getstringlist = $alreadyavilablecheck = "";
  					$alreadyavilablesql = "SELECT id FROM ".$apimastersres->application_table." WHERE "; 
  					$getstringlist = "INSERT INTO ".$apimastersres->application_table." (";
  					//echo $getstringlist; exit;
  					foreach($checklists as $valuefield=>$tablefield){ //api master response fileds
  						
  						$sqlfieldstring .= "`".$tablefield."`,"; 				
  					}
	  				$sqlfieldstring .="`is_active`,`current_status`"; 
  					$getstringlist .= $sqlfieldstring.") VALUES (";
  					foreach($checklists as $valuefield=>$tablefield){ //api master response fileds
  						
  						if(in_array($tablefield,(array)$listsechck)){
  							$sqlvaluestring .= "'".$fieldkeywithvalue[$tablefield][$apihitre[$valuefield]]."',";
  							$alreadyavilablesql .= " ".$tablefield."='".$fieldkeywithvalue[$tablefield][$apihitre[$valuefield]]."' AND";
  						}elseif($tablefield=='date_pick'){
  							$invoiced_at = str_replace('/"', '-', $apihitre[$valuefield]);
							$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
  							$sqlvaluestring .= "'".$invoiced_at."',"; 
  							$alreadyavilablesql .= " ".$tablefield."='".$invoiced_at."' AND";
  						}else{
  							$sqlvaluestring .= "'".$apihitre[$valuefield]."',"; 
  							$alreadyavilablesql .= " ".$tablefield."='".$apihitre[$valuefield]."' AND";
  						}  						
  					}
  					//echo  $sqlvaluestring; exit;
  					$alreadyavilablesql .= " is_active = 0";
  					$alreadyavilableresult = $callConfig->getRow($alreadyavilablesql);
  					
  					$sqlvaluestring .= "0,0";
	  				$getstringlist .= $sqlvaluestring.")";
	  				//echo  $getstringlist; exit;
  					if($alreadyavilableresult->id !=""){
  						$stringary['status'] = 1062; 
				        $stringary['query'] = $getstringlist;
				        $stringary['error'] = 'Duplicate Entry';
  					}else{		
  						if($checkstatus == "S"){
  							$savgdsc = $callConfig->executeQuery($getstringlist);
  							if($savgdsc['status'] == 'sucess'){
	  							$queryresult = $callConfig->getRow($alreadyavilablesql);
	  							if($queryresult->id!=""){
	  								$itemres = $this->toadditemleveltallydata($apihitre, $apimastersres->application_table, $queryresult->id);
	  							}
	  							$stringary['status'] = 200; 
						        $stringary['query'] = $getstringlist;
						        $stringary['item_res'] = $itemres;
						        $stringary['error'] = 'success';
					    	}else{
					    		$stringary['status'] = $savgdsc['error_no']; 
						        $stringary['query'] = $getstringlist;
						        $stringary['error'] = $savgdsc['message']; 
					    	}
  						}else{
  							$stringary = $callConfig->checkExecuteQueryString($getstringlist);
  						}		
	  				}	  				
  					$apihitre['response'] =$stringary; 
  					$responsary[] = $apihitre;
  				}



  				if($checkstatus == "S"){
  					$api_hits_sync = array('company_id'=>$get['company_id'],'api_hit_id'=>$api_hit_id,'originator'=>$get['user_id'],'application_table'=>$apimastersres->application_table,'sync_datetime'=>date('Y-m-d H:i:s'), 'sync_details'=>urlencode(json_encode((array)$responsary)),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
  					$syncres = $callConfig->insertRecord('api_hit_sync_with_db',$api_hits_sync);	
				}




				//echo "<pre>"; print_r($fieldkeywithvalue); exit;
				$return['status'] = "Good";
			}
  		}else{
  			$return['status'] = 403; // api code not match
  		}
  		$return['result'] = $responsary;
  		return $return;

	}
	**/
	/**
	function tocheckdynamictransactionall($get){
		global $callConfig;
		$api_hit_id = $get['api_hit_id'];
	 	$checkstatus = $get['checkstatus'];
	 	$api_hitssql = "SELECT * FROM api_hits WHERE company_id='".$get['company_id']."' AND id = '".$api_hit_id."' AND is_active = '0'";
      	$api_hitsres = $callConfig->getRow($api_hitssql);
      	$return['api_hits'] = $api_hitsres;
		//print_r($api_hitsres); exit;
      	if($api_hitsres->api_master_id !=""){  // api code match
  			$apimasterssql = "SELECT * FROM api_masters WHERE company_id='".$get['company_id']."' AND id = '".$api_hitsres->api_master_id."' AND is_active = '0'";
	      	$apimastersres = $callConfig->getRow($apimasterssql);
      		$return['api_masters'] = $apimastersres;

  			$api_master_id = $apimastersres->id;
  			$api_url = $apimastersres->end_point;
  			$api_code = $apimastersres->api_code;
  			if($apimastersres->api_type == "S"){
				//Send /...................
  				$return['status'] = 4040404;
			}else{
				$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$apimastersres->application_table."' AND is_active = 0";
				$displayattributesres =  $callConfig->getRow($displayattributessql);
				//echo "<pre>"; print_r($displayattributesres); exit;
				$fieldkeywithvalue = array();
				$checklists = array('InvDate'=>'transaction_date', 'VOUCHERNUMBER'=>'transaction_number', 'PARTYNAME'=>'party_name', 'STATE'=>'party_address', 'PLACEOFSUPPLY'=>'place_of_supply', 'mobile_number'=>'party_phone', 'email_address'=>'party_email');
				/*if($checkstatus == "S"){
					$checklists['transaction_number']=>'transaction_number';
				}*/ /** ROHAN **
				foreach($checklists as $checklis){
					$checkfiled[] = $checklis;
				}
				if($displayattributesres->list_attributes !=""){
					$lists = json_decode($displayattributesres->list_attributes,1);
					$type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
					foreach ($lists as $key => $value){
						if(in_array($value['type'], (array)$checkfiled)){
							if(in_array($value['type'], (array)$type_array)){
								$listsechck[] = $key;
								$tablefield = $this->getDyanamicSelectData($apimastersres->application_table, $key); 
								if($tablefield['type'] != 'function'){
									$reverkeyvaluearay = array();
									foreach($tablefield['res'] as $kkey=>$valuu){
										$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
										$reverkeyvaluearay[$removewhinewline] = $kkey;
									}
									$fieldkeywithvalue[$key] = $reverkeyvaluearay;
								}else{
									$reverkeyvaluearay = array();
									foreach($tablefield as $kkey=>$valuu){
										$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
										$reverkeyvaluearay[$removewhinewline] = $kkey;
									}
									$fieldkeywithvalue[$key] =  $reverkeyvaluearay;
								}
					 		}
					 	}
					}
				}
				$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
				$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
				//echo "<pre>".$api_code; print_r($api_hit_api_response_details); exit;
				$responsary = array();
				foreach($api_hit_api_response_details[$api_code] as $apihitre){  
					//api Hites response fileds
					//echo "<pre>"; print_r($apihitre); exit;
  					$sqlfieldstring =$sqlvaluestring  = $getstringlist = $alreadyavilablecheck = "";
  					$alreadyavilablesql = "SELECT id FROM ".$apimastersres->application_table." WHERE "; 
  					$getstringlist = "INSERT INTO ".$apimastersres->application_table." (";
  					//echo $getstringlist; exit;
  					foreach($checklists as $valuefield=>$tablefield){ //api master response fileds
  						
  						$sqlfieldstring .= "`".$tablefield."`,"; 				
  					}
	  				$sqlfieldstring .="`is_active`,`current_status`"; 
  					$getstringlist .= $sqlfieldstring.") VALUES (";
  					foreach($checklists as $valuefield=>$tablefield){ //api master response fileds
  						
  						if(in_array($tablefield,(array)$listsechck)){
  							$sqlvaluestring .= "'".$fieldkeywithvalue[$tablefield][$apihitre[$valuefield]]."',";
  							$alreadyavilablesql .= " ".$tablefield."='".$fieldkeywithvalue[$tablefield][$apihitre[$valuefield]]."' AND";
  						}elseif($tablefield=='date_pick'){
  							$invoiced_at = str_replace('/"', '-', $apihitre[$valuefield]);
							$invoiced_at =  date('Y-m-d', strtotime($invoiced_at));
  							$sqlvaluestring .= "'".$invoiced_at."',"; 
  							$alreadyavilablesql .= " ".$tablefield."='".$invoiced_at."' AND";
  						}else{
  							$sqlvaluestring .= "'".$apihitre[$valuefield]."',"; 
  							$alreadyavilablesql .= " ".$tablefield."='".$apihitre[$valuefield]."' AND";
  						}  						
  					}
  					//echo  $sqlvaluestring; exit;
  					$alreadyavilablesql .= " is_active = 0";
  					$alreadyavilableresult = $callConfig->getRow($alreadyavilablesql);
  					
  					$sqlvaluestring .= "0,0";
	  				$getstringlist .= $sqlvaluestring.")";
	  				//echo  $getstringlist; exit;
  					if($alreadyavilableresult->id !=""){
  						$stringary['status'] = 1062; 
				        $stringary['query'] = $getstringlist;
				        $stringary['error'] = 'Duplicate Entry';
  					}else{		
  						if($checkstatus == "S"){
  							$savgdsc = $callConfig->executeQuery($getstringlist);
  							if($savgdsc['status'] == 'sucess'){
	  							$queryresult = $callConfig->getRow($alreadyavilablesql);
	  							if($queryresult->id!=""){
	  								$itemres = $this->toadditemleveltallydata($apihitre, $apimastersres->application_table, $queryresult->id);
	  								$pass_get = array('company_id'=>$get['company_id'], 'transaction_table'=>$apimastersres->application_table);
	  								
	  								$dquery = "SELECT * FROM ".$apimastersres->application_table." WHERE id =".$queryresult->id;
									$table_transaction = $callConfig->getRow($dquery);
									$res = $this->updateTransactionItemsQty($pass_get,$table_transaction);

	  							}
	  							$stringary['status'] = 200; 
						        $stringary['query'] = $getstringlist;
						        $stringary['item_res'] = $itemres;
						        $stringary['error'] = 'success';
					    	}else{
					    		$stringary['status'] = $savgdsc['error_no']; 
						        $stringary['query'] = $getstringlist;
						        $stringary['error'] = $savgdsc['message']; 
					    	}
  						}else{
  							$stringary = $callConfig->checkExecuteQueryString($getstringlist);
  						}		
	  				}	  				
  					$apihitre['response'] =$stringary; 
  					$responsary[] = $apihitre;
  				}
  				if($checkstatus == "S"){
  					$api_hits_sync = array('company_id'=>$get['company_id'],'api_hit_id'=>$api_hit_id,'originator'=>$get['user_id'],'application_table'=>$apimastersres->application_table,'sync_datetime'=>date('Y-m-d H:i:s'), 'sync_details'=>urlencode(json_encode((array)$responsary)),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
  					$syncres = $callConfig->insertRecord('api_hit_sync_with_db',$api_hits_sync);	
				}
				//echo "<pre>"; print_r($fieldkeywithvalue); exit;
				$return['status'] = "Good";
			}
  		}else{
  			$return['status'] = 403; // api code not match
  		}
  		$return['result'] = $responsary;
  		return $return;

	}  
	function toadditemleveltallydata($apihitre, $table, $id){
		global $callConfig;
		$item_array = array('Stock_Item'=>'item_name', 'RATE'=>'item_price', 'QUANTITY'=>'item_quantity', 'AMOUNT'=>'item_amount');
		
		$dquery = "SELECT id, item_table, tax_table FROM dynamic_mapping_tables WHERE application_table_name ='".."'";
		$dtable = $callConfig->getRow($dquery);

		$arry = array( 'item_st_cost', 'item_fifo_cost',  'item_lifo_cost',  'item_wa_cost');
		$query = "SELECT id, item_name, purchase_uom, item_description  FROM items";
		$itemres = $callConfig->getAllRows($query);
		foreach( $itemres as $itemr){
			$resit[$itemr->item_name] = (array)$itemr;
		}
		foreach($apihitre['Item_Entries'] as $item_data){
			$item_insert = array();
			foreach($item_array as $ikey=>$iarr){
				$item_insert[$iarr] = $item_data[$ikey];
			}
			foreach($arry as $iarr){
				$item_insert[$iarr] = $item_insert['item_price'];
			}
			$item_insert['approved_quantity'] = $item_data['item_quantity'];
			$item_insert['item_id'] = $resit[$item_insert['item_name']]['id'];
			$item_insert['description'] = $resit[$item_insert['item_name']]['item_description'];
			$item_insert['transaction_id'] = $id;
			$item_insert['is_active'] = '0';
			$item_insert['current_status'] = '0';
			$initemres[] = $callConfig->insertRecord($dtable->item_table,$item_insert);

		}
		return $initemres;
	} **/


	function aPIDetailStoretoDatabase($get,$post){
		global $callConfig;
 		$return['post'] = $post;
 		$api_hit_id = $post['api_hit_id'] !=""?$post['api_hit_id']:$get['api_hit_id'];
	 	$checkstatus = $post['checkstatus'] !=""?$post['checkstatus']:$get['checkstatus'];
	 	$api_hitssql = "SELECT * FROM api_hits WHERE company_id='".$get['company_id']."' AND id = '".$api_hit_id."' AND is_active = '0'";
      	$api_hitsres = $callConfig->getRow($api_hitssql);

      	$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
echo "<pre>cvfcgbfgjh"; print_r($api_hit_api_response_details); exit;


      	$return['api_hits'] = $api_hitsres;
  		if($api_hitsres->api_master_id !=""){  // api code match
  			$apimasterssql = "SELECT * FROM api_masters WHERE company_id='".$get['company_id']."' AND id = '".$api_hitsres->api_master_id."' AND is_active = '0'";
	      	$apimastersres = $callConfig->getRow($apimasterssql);
      		$return['api_masters'] = $apimastersres;

  			$api_master_id = $apimastersres->id;
  			$api_url = $apimastersres->end_point;
  			$api_code = $apimastersres->api_code;
  			if($apimastersres->api_type == "S"){
				//Send /...................
  				$return['status'] = 4040404;
			}else{
				//display attributes list
  				$displayattributessql = "SELECT id,application_table_name,help_text,list_attributes,empty_table_message,insert_page_type FROM display_attributes WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$apimastersres->application_table."' AND is_active = 0";
				$displayattributesres =  $callConfig->getRow($displayattributessql);
				$fieldkeywithvalue = array();
				if($displayattributesres->list_attributes !=""){
					$lists = json_decode($displayattributesres->list_attributes,1);
					$type_array = array('select','auto_select','multi_select','disable_select','radio','checkbox', 'color_pick','image');
					foreach ($lists as $key => $value){
						if(in_array($value['type'], (array)$type_array)){
							$tablefield = $this->getDyanamicSelectData($apimastersres->application_table, $key); 
							if($tablefield['type'] != 'function'){
								$reverkeyvaluearay = array();
								foreach($tablefield['res'] as $kkey=>$valuu){
									$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
									$reverkeyvaluearay[$removewhinewline] = $kkey;
								}
								$fieldkeywithvalue[$key] = $reverkeyvaluearay;
							}else{
								$reverkeyvaluearay = array();
								foreach($tablefield as $kkey=>$valuu){
									$removewhinewline = preg_replace("/\r|\n/", '', $valuu);
									$reverkeyvaluearay[$removewhinewline] = $kkey;
								}
								$fieldkeywithvalue[$key] =  $reverkeyvaluearay;
							}
				 		}
					}
				}
				//Display Attributes
				//print_r($fieldkeywithvalue);
				$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
				$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
				      	echo "<pre>cvfcgbfgjh"; print_r($api_hit_api_response_details); exit;
				$responsary = array();
  				foreach($api_hit_api_response_details[$api_code] as $apihitre){  //api Hites response fileds
  					$sqlfieldstring =$sqlvaluestring  = $getstringlist = $alreadyavilablecheck = "";
  					$alreadyavilablesql = "SELECT id FROM ".$apimastersres->application_table." WHERE "; 
  					$getstringlist = "INSERT INTO ".$apimastersres->application_table." (";
  					foreach($api_mastersr_api_response_details as $valuefield=>$tablefield){ //api master response fileds
  						if($tablefield['application_table_field'] !=""){
  							$sqlfieldstring .= "`".$tablefield['application_table_field']."`,"; 
  						}						
  					}
	  				$sqlfieldstring .="`is_active`,`current_status`"; 
  					$getstringlist .= $sqlfieldstring.") VALUES (";
  					foreach($api_mastersr_api_response_details as $valuefield=>$tablefield){ //api master response fileds
  						if($tablefield['application_table_field'] !=""){
  							if($fieldkeywithvalue[$tablefield['application_table_field']][$apihitre[$valuefield]] !=""){
  								$sqlvaluestring .= "'".$fieldkeywithvalue[$tablefield['application_table_field']][$apihitre[$valuefield]]."',";

  								if($tablefield['uniqueness'] == 'Y'){
  									$alreadyavilablecheck = "Y";
  									$alreadyavilablesql .= " ".$tablefield['application_table_field']."='".$fieldkeywithvalue[$tablefield['application_table_field']][$apihitre[$valuefield]]."' AND";
  								} 

  							}else{
  								$sqlvaluestring .= "'".$apihitre[$valuefield]."',"; 
  								if($tablefield['uniqueness'] == 'Y'){
  									$alreadyavilablecheck = "Y";
  									$alreadyavilablesql .= " ".$tablefield['application_table_field']."='".$apihitre[$valuefield]."' AND"; 
  								}
  							}	
  						}
  					}
  					if($alreadyavilablecheck =="Y"){
  						$alreadyavilablesql .= " is_active = 0";
  						//echo $alreadyavilablesql; echo "</br>";
  						$alreadyavilableresult = $callConfig->getRow($alreadyavilablesql);
  					}else{
  						$alreadyavilableresult->id = "";
  					}
  					$sqlvaluestring .= "0,0";
	  				$getstringlist .= $sqlvaluestring.")";
  					if($alreadyavilableresult->id !=""){
  						$stringary['status'] = 1062; 
				        $stringary['query'] = $getstringlist;
				        $stringary['error'] = 'Duplicate Entry';
  					}else{		
  						if($checkstatus == "S"){
  							$savgdsc = $callConfig->executeQuery($getstringlist);
  							if($savgdsc['status'] == 'sucess'){
	  							$stringary['status'] = 200; 
						        $stringary['query'] = $getstringlist;
						        $stringary['error'] = 'success';
					    	}else{
					    		$stringary['status'] = $savgdsc['error_no']; 
						        $stringary['query'] = $getstringlist;
						        $stringary['error'] = $savgdsc['message']; 
					    	}
  						}else{
  							$stringary = $callConfig->checkExecuteQueryString($getstringlist);
  						}		
	  				}	  				
  					$apihitre['response'] =$stringary; 
  					$responsary[] = $apihitre;
  				}	

  				if($checkstatus == "S"){
  					$api_hits_sync = array('company_id'=>$get['company_id'],'api_hit_id'=>$api_hit_id,'originator'=>$get['user_id'],'application_table'=>$apimastersres->application_table,'sync_datetime'=>date('Y-m-d H:i:s'), 'sync_details'=>urlencode(json_encode((array)$responsary)),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
  					$syncres = $callConfig->insertRecord('api_hit_sync_with_db',$api_hits_sync);	
				}

  			}
  		}else{
  			$return['status'] = 403; // api code not match
  		}
  		$return['result'] = $responsary;
	 	return $return;
	 	}


	//function tallyAPIReceiveReceipts($get,$post,$api_hitsres,$apimastersres){
	// 	global $callConfig;
	// 	$api_hit_id = $post['api_hit_id'] !=""?$post['api_hit_id']:$get['api_hit_id'];
	//  	$checkstatus = $post['checkstatus'] !=""?$post['checkstatus']:$get['checkstatus'];
	//  	$company_id = $get['company_id'];
	// 	//start get masters
	// 	$chartofaccountssql = "SELECT id,company_id, account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$company_id." AND allow_posting = 0 AND is_active =0";
	//     $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
	//     $chart_of_accounts = array();
	//     foreach($chartofaccountsres as $cca){
	//     	$chart_of_accounts_name[$cca->account_name] = $cca;
	//         $chart_of_accounts[$cca->account_code] = $cca;
	//     }
	//     $customerssql = "SELECT ctm.customer_id, ctm.party_name, cust.id, cust.company, cust.account_code FROM customer_tally_mappings ctm, customers cust WHERE ctm.company_id = ".$company_id." AND ctm.company_id = cust.company_id AND ctm.customer_id = cust.id AND ctm.is_active = cust.is_active AND ctm.is_active = 0";
	// 	$customersres = $callConfig->getAllRows($customerssql);
	// 	$customers = array();
	//     foreach($customersres as $custre){
	//         $customers[$custre->party_name] = $custre;
	//     }
	// 	$banckaccountsql = "SELECT id, account_code, bank_account_name, bank_account_number FROM bank_accounts WHERE company_id = '".$company_id."' AND is_active=0";
	// 	$banckaccountres = $callConfig->getAllRows($banckaccountsql);
 //    	foreach($banckaccountres as $payres){
	// 		$bank_accounts[$payres->account_code] = (array)$payres;
	// 	}	
	// 	$sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id FROM sales_invoices WHERE company_id = '".$company_id."' AND is_active=0";
	// 	$sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);    
 //    	foreach($sales_invoicesres as $si){
	// 		$sales_invoices[$si->transaction_number] = (array)$si;
	// 	}	
		
	// 	//end get master
	// 	$api_master_id = $apimastersres->id;
	// 	$api_url = $apimastersres->end_point;
	// 	$api_code = $apimastersres->api_code;
 // 		$api_hit_api_response_details = json_decode(urldecode($api_hitsres->api_response_details),1);
	// 	$api_mastersr_api_response_details = json_decode($apimastersres->api_response_details, 1);
	// 	$responsary = $receipt_allocations= array();
		
	// 	//recept list
	// 	$receiptssql = "SELECT id, transaction_no, amount, unallocated_amount, allocated_amount, paid_amonut, tds_amount FROM receipts WHERE company_id='".$company_id."' AND is_active = '0'";
 //      	$receiptsres = $callConfig->getAllRows($receiptssql);
 //  		//$return['receipts'] = $receiptsres;

 //  		$receipt_allocationssql = "SELECT id, receipt_id, transaction_no AS bill_transaction_no , total_paid_amonut FROM receipt_allocations WHERE company_id='".$company_id."' AND is_active = '0'";
 //      	$receipt_allocationsres = $callConfig->getAllRows($receipt_allocationssql);
 //  		//$return['receipt_allocations'] = $receipt_allocationsres;

 //  		foreach($receipt_allocationsres as $recalcpt){  
	// 		$receipt_allocations[$recalcpt->receipt_id] =  $recalcpt;
	// 	}

	// 	foreach($receiptsres as $recpt){  
	// 		$recpt->bll_details = $receipt_allocations[$recpt->id];
	// 		$receipts[$recpt->transaction_no] =  $recpt; //marge two table data
 // 		}
 // 		foreach($api_hit_api_response_details[$api_code] as $apihitre){  
	// 		//$return['apihitre'] = $apihitre;
	// 		if(isset($receipts[$apihitre['VOUCHERNUMBER']])){
	// 			$stringary['status'] = 1062; 
	// 	        $stringary['query'] = $getstringlist;
	// 	        $stringary['error'] = 'Duplicate Entry';
	// 		}else{		
	// 			if($checkstatus == "S"){ // Save
	// 				//total amount recepts
	// 				$recepttotalamt = 0;
	// 				$party_id = array();
	// 				$payment_date = $this->dateconvertdbformat($apihitre['InvDate']);
	// 				$arychunks = array_chunk($apihitre['Ledger_Entries'], 2);
	// 				$payment_type = "cash";
	// 				$bank_account_id = $account_code="";
	// 				foreach($arychunks as $partydetailandtransdetail){
	// 					$recepttotalamt += $partydetailandtransdetail[0]['AMOUNT'];
	// 					$receptaccountname = $partydetailandtransdetail[1]['Ledger_Name'];

	// 					if(isset($customers[$partydetailandtransdetail[0]['Party_Name']])){
	// 						$custid = $customers[$partydetailandtransdetail[0]['Party_Name']]->id;
	// 						$party_id[$custid] = $custid;
	// 					}
	// 				}	
	// 				//total amount recepts
	// 				if(isset($chart_of_accounts_name[$receptaccountname])){
	// 					$payment_account_details = $chart_of_accounts_name[$receptaccountname];
	// 					if(isset($bank_accounts[$payment_account_details->account_code])){
	// 						$bankaccountdetails = $bank_accounts[$payment_account_details->account_code];
	// 						$payment_type = "bank";
	// 						$bank_account_id = $bankaccountdetails['id'];
	// 						$account_code = $bankaccountdetails['account_code'];
	// 					}
	// 				}
	// 				//payment_type
					
	// 				//strat insert transaction 
	// 				$transaction_no	= $this->toGetApplicationSerialNumber('receipts');
	// 				$paymentarray = array('company_id'=>$company_id,'transaction_type'=>'customers','transaction_no'=>$transaction_no,'payment_date'=>$payment_date,'party_id'=>json_encode($party_id),'payment_type'=>$payment_type,'bank_account_id'=>$bank_account_id,'account_code'=>$account_code,'amount'=>$recepttotalamt,'ref_no'=>'','due_date'=>$payment_date,'unallocated_amount'=>0,'allocated_amount'=>$recepttotalamt,'remarks'=>'','originator'=>1,'is_active'=>'0','current_status'=>'199','paid_amonut'=>$recepttotalamt,'tds_amount'=>0,'payment_process'=>'NP','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"));
	// 				$res = $callConfig->insertRecord('receipts',$paymentarray);
	// 				$receipt_id = $res['LASTID'];
	// 				if($res['status'] == 'sucess'){
	// 					foreach($arychunks as $partydetailandtransdetail){
	// 						$transaction_details = $sales_invoices[$partydetailandtransdetail[0]['Bill_Details']['Bill_NO']];
	// 						$paymentallocationarray = array('company_id'=>$company_id,'receipt_id'=>$receipt_id,'transaction_table'=>'sales_invoices','transaction_id'=>$transaction_details['id'],'transaction_no'=>$transaction_details['transaction_number'],'total_amount'=>$partydetailandtransdetail[0]['AMOUNT'],'pending_amount'=>0,'paid_amonut'=>$partydetailandtransdetail[0]['AMOUNT'],'is_active'=>'0','current_status'=>'0','created_by'=>1,'created_date'=>date("Y-m-d H:i:s"),'tds_amount'=>0,'total_paid_amonut'=>$partydetailandtransdetail[0]['AMOUNT'],'total_tax_split_details'=>'','party_id'=>$transaction_details['party_id']);
	// 						$payallocationres = $callConfig->insertRecord('receipt_allocations',$paymentallocationarray);
	// 					}
						
	// 					$stringary['status'] = 200; 
	// 		        	$stringary['query'] = $paymentarray;
	// 		        	$stringary['error'] = 'success';
	// 		        	//$stringary['jvquery'] = $this->insertpaymentstojv($receipt_id,$get,'receipts');
	// 		    	}else{
	// 		    		$stringary['status'] = $res['error_no']; 
	// 			        $stringary['query'] = $paymentarray;
	// 			        $stringary['error'] = $res['message']; 
	// 		    	}
	// 			}else{ //insert check
	// 					//$stringary = $callConfig->checkExecuteQueryString($getstringlist);
	// 			}		
	// 		}	  				
	// 		$apihitre['response'] =$stringary; 
	// 		$responsary[] = $apihitre;
	// 	}
	// 	$return['responsary'] = $responsary;

	// 	echo "<pre>"; print_r($return);
 // 		return $return;
	// }

}
?>