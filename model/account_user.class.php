<?php
require_once "dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options; 
class accountUserClass extends userClass{
	function getFinanaceReportGroups($get){
    	global $callConfig;
    	$return = array();
    	$chartofaccountssql = "SELECT id,account_name,account_code FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND parent_account_code = 0 AND is_active =0";
    	$chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
    	$return['chart_of_accounts']= $chartofaccountsres;
    	$financereportgroupssql = "SELECT * FROM `finance_report_groups` WHERE company_id = ".$get['company_id']." AND finance_report_id = '".$get['fr_id']."' AND is_active =0 ORDER BY display_priority ASC";
    	$financereportgroupsres = $callConfig->getAllRows($financereportgroupssql);
    	$return['finance_report_groups']= $financereportgroupsres;

		$financereportssql = "SELECT id,report_name, description FROM `finance_reports` WHERE company_id = ".$get['company_id']." AND id = '".$get['fr_id']."' AND is_active =0";
    	$financereportgroupsres = $callConfig->getRow($financereportssql);
    	$return['finance_reports']= $financereportgroupsres;
    	if($get['Rid'] !=""){
    		$financereportgroupssqll = "SELECT * FROM `finance_report_groups` WHERE company_id = ".$get['company_id']." AND id = '".$get['Rid']."' AND is_active =0 ";
    		$financereportgroupsress = $callConfig->getRow($financereportgroupssqll);	
    		$return['finance_report_groups_edit']= $financereportgroupsress;
    		//print_r($financereportgroupsress);
    		if($financereportgroupsress->chart_of_account_codes !="" AND $financereportgroupsress->chart_of_account_codes!='null'){
    			$imlpod = implode("','",json_decode($financereportgroupsress->chart_of_account_codes,1));
    			$get['chart_of_account_codes'] = "'".$imlpod."'";
    		}
    		
    		$return['selectedaccountlist'] = $this->selectChartOfAccont($get);
    	}
		return $return; 
    }
    function selectChartOfAccont($get){
    	global $callConfig;
    	$chartofaccountssql = "SELECT id,account_name,parent_account_code,account_code FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND (parent_account_code IN('".$get['chart_of_account_codes']."') OR account_code IN('".$get['chart_of_account_codes']."')) AND is_active =0";
    	$chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
    	$return['chart_of_accounts_sql']= $chartofaccountssql;
    	$chartofaccounts = array();
    	$accode = array();
    	foreach($chartofaccountsres as $chartofaccountsre){
    		$accode[$chartofaccountsre->account_code] = $chartofaccountsre->account_code;
    		if($chartofaccountsre->parent_account_code =='0'){
    			$chartofaccountsparent[] = $chartofaccountsre;
			}else{
    			$chartofaccounts[$chartofaccountsre->parent_account_code][] = $chartofaccountsre;
    		}
    	}
    	$implode = implode("','",$accode); 
    	$usedaccontcdesql = "SELECT frcoa.id,frcoa.finance_report_group_id,frcoa.chart_of_account_code, coa.group_name,coa.short_code FROM finance_report_chart_of_account frcoa, finance_report_groups coa WHERE frcoa.company_id = ".$get['company_id']." AND frcoa.company_id = coa.company_id AND frcoa.finance_report_group_id = coa.id AND chart_of_account_code IN('".$implode."') AND frcoa.finance_report_group_id != '".$get['Rid']."'  AND frcoa.is_active = coa.is_active AND frcoa.is_active =0 ";
    	$usedaccontcderes = $callConfig->getAllRows($usedaccontcdesql);
    	foreach($usedaccontcderes as $chartofaccountsre){
    		$usedaccontcdelist[$chartofaccountsre->chart_of_account_code] = $chartofaccountsre;
		}

		$groupaccountsql = "SELECT id,chart_of_account_code,finance_report_group_id FROM finance_report_chart_of_account WHERE company_id = ".$get['company_id']." AND finance_report_group_id = '".$get['Rid']."' AND is_active =0";
    	$groupaccountres = $callConfig->getAllRows($groupaccountsql);
    	foreach($groupaccountres as $groupaccount){
    		$groupaccountlist[$groupaccount->chart_of_account_code] = $groupaccount;
		}
		$return['finance_report_chart_of_account']= $groupaccountlist;
    	$return['chart_of_accounts_already_used']= $usedaccontcdelist;
    	$return['chart_of_accounts_parent']= $chartofaccountsparent;
    	$return['chart_of_accounts_child']= $chartofaccounts;
    	return $return;
    }
    function insertFinanaceReportGroups($get,$post){
    	global $callConfig;
    	$return =array();
    	$return['post'] = $post;
    	$encodedata = "";
    	if($post['chart_of_account_code'] !=""){
    		$encodedata = json_encode($post['chart_of_account_codes']);
    	}
    	$implode = "";
    	if($post['chart_of_account_code'] !=""){
    		$implode = implode("','",$post['chart_of_account_code']);  
    	} 
    	$return['implode'] = $implode;
    	$fr_id = $post['fr_id'];
    	$footer_total = $post['footer_total']=='0'?"0":"1";
    	if($post['Rid']!=""){ 
    	     
    		$insert_array = array('group_name'=>$post['group_name'],'footer_total'=>$footer_total,'finance_report_id'=>$fr_id,'short_code'=>$post['short_code'],'display_priority'=> $post['display_priority'],'parent_group_id'=>$post['parent_group_id'],'chart_of_account_codes'=>$encodedata,'calculation_formula'=>$post['calculation_formula'],'updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d H:i:s'));
    		$res=$callConfig->updateRecord('finance_report_groups',$insert_array,'id',$post['Rid']);
    		$return['res'] = $res;
    		$updatearray = array('is_active'=>null,'to_date'=>date('Y-m-d'),'updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d H:i:s'));
    		$whrcon = "(finance_report_group_id ='".$post['Rid']."' OR chart_of_account_code IN('".$implode."')) AND is_active='0'";
			$updateres = $callConfig->updateRecord('finance_report_chart_of_account',$updatearray,'','',$whrcon);
	 		$return['whrcon'] = $whrcon;
    		//$updateres=$callConfig->updateRecord('finance_report_chart_of_account',$updatearray,'finance_report_group_id',$post['Rid']);
    		foreach($post['chart_of_account_code'] as $acountid){
    			$insertarray = array('company_id'=>$get['company_id'],'finance_report_group_id'=>$post['Rid'],'chart_of_account_code'=>$acountid,'assigned_date'=>date('Y-m-d'),'from_date'=>date('Y-m-d'),'to_date'=>date('Y-'.'12-31'),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
    			$insertdata=$callConfig->insertRecord('finance_report_chart_of_account',$insertarray);
    			$return['frcoa'][] = $insertdata;	
    		}
    	}else{
    		$insert_array = array('company_id'=>$get['company_id'],'footer_total'=>$footer_total,'finance_report_id'=>$fr_id,'group_name'=>$post['group_name'],'short_code'=>$post['short_code'],'display_priority'=> $post['display_priority'],'parent_group_id'=>$post['parent_group_id'],'chart_of_account_codes'=>$encodedata,'calculation_formula'=>$post['calculation_formula'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
    		$insert_data=$callConfig->insertRecord('finance_report_groups',$insert_array);
    		$return['res'] = $insert_data;

    		$updatearray = array('is_active'=>null,'to_date'=>date('Y-m-d'),'updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d H:i:s'));
    		$whrcon = "(finance_report_group_id ='".$post['Rid']."' OR chart_of_account_code IN('".$implode."')) AND is_active='0'";
			$updateres = $callConfig->updateRecord('finance_report_chart_of_account',$updatearray,'','',$whrcon);

			foreach($post['chart_of_account_code'] as $acountid){
    			$insertarray = array('company_id'=>$get['company_id'],'finance_report_group_id'=>$insert_data['LASTID'],'chart_of_account_code'=>$acountid,'assigned_date'=>date('Y-m-d'),'from_date'=>date('Y-m-d'),'to_date'=>date('Y-'.'12-31'),'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
    			$insertdata=$callConfig->insertRecord('finance_report_chart_of_account',$insertarray);
    			//$return['frcoa'][] = $insertdata;	
    		}
    	} 
		return $return;  
    }
    function getChartOfAccounts($get){
    	global $callConfig;
    	$return = array();
    	$chartofaccountssql = "SELECT * FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY parent_account_code ASC";
    	$chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
    	$return['chart_of_accounts']= $chartofaccountsres;

    	$financereportgroupssql = "SELECT id,account_type_name FROM `account_type` WHERE company_id = ".$get['company_id']." AND is_active =0";
    	$financereportgroupsres = $callConfig->getAllRows($financereportgroupssql);
    	$return['account_type']= $financereportgroupsres;

        $taxessql = "SELECT id,tax_name FROM `taxes` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $taxesres = $callConfig->getAllRows($taxessql);
        $return['taxes']= $taxesres;

        $journalssql = "SELECT id,journal_name FROM `journals` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $journalsres = $callConfig->getAllRows($journalssql);
        $return['journals']= $journalsres;

        $currenciessql = "SELECT id,currency_name, currency_symbol FROM `currencies` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $currenciesres = $callConfig->getAllRows($currenciessql);
        $return['currencies']= $currenciesres;

        $locationssql = "SELECT id,location_name FROM `locations` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $locationsres = $callConfig->getAllRows($locationssql);
        $return['locations']= $locationsres;

        $departmentssql = "SELECT id,department_name FROM `departments` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $departmentsres = $callConfig->getAllRows($departmentssql);
        $return['departments']= $departmentsres;

        $cost_centerssql = "SELECT id,cost_center_name FROM `cost_centers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $cost_centersres = $callConfig->getAllRows($cost_centerssql);
        $return['cost_centers']= $cost_centersres;


    	if($get['Rid'] !=""){
    		$financereportgroupssqll = "SELECT * FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND id = '".$get['Rid']."' AND is_active =0 ";
    		$financereportgroupsress = $callConfig->getRow($financereportgroupssqll);	
    		$return['chart_of_accounts_single']= $financereportgroupsress;
    		//$return['selectedaccountlist'] = $this->selectChartOfAccont($get);
    	}

        $accountgroupsssql = "SELECT id,group_name,parent_group_id,pl_yn,tb_seq,bs_seq,accsec_section_id FROM `account_groups` WHERE company_id = " . $get['company_id'] . " AND is_active =0";
        $accountgroupsres = $callConfig->getAllRows($accountgroupsssql);
        $return['account_groups'] = $accountgroupsres;

		return $return;
    }
    function insertChartOfAccounts($get,$post){
    	global $callConfig;
    	$return =array();
    	$return['post'] = $post;

        $allow_posting = ($post['allow_posting'] =="0")?0:1;
         $allow_manual_entries = ($post['allow_manual_entries'] =="0")?0:1;
    	if($post['Rid']!=""){ 
    		$insert_array = array('account_code'=>$post['account_code'],'account_name'=>$post['account_name'],'account_short_name'=> $post['account_short_name'],'account_type'=>$post['account_type'],'parent_account_code'=>$post['parent_account_code'],'display_priority'=>$post['display_priority'],'tax_id'=>$post['tax_id'],'currency_id'=>$post['currency_id'],'journal_id'=>$post['journal_id'],'reconciliation'=>$post['reconciliation'],'allow_posting'=>$allow_posting,'allow_manual_entries'=>$allow_manual_entries,'updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d H:i:s'),'accgro_group_id' => $post['account_groups']);
    		$res=$callConfig->updateRecord('chart_of_accounts',$insert_array,'id',$post['Rid']);
    		$return['res'] = $res;
    	}else{
            $accountcode = $this->setAccountCode($get, $post);
            $post['account_code'] = $accountcode['account_code']; 
    		$insert_array = array('company_id'=>$get['company_id'],'account_code'=>$post['account_code'],'account_name'=>$post['account_name'],'account_short_name'=> $post['account_short_name'],'account_type'=>$post['account_type'],'parent_account_code'=>$post['parent_account_code'],'display_priority'=>$post['display_priority'],'tax_id'=>$post['tax_id'],'currency_id'=>$post['currency_id'],'journal_id'=>$post['journal_id'],'reconciliation'=>$post['reconciliation'],'allow_posting'=>$allow_posting,'allow_manual_entries'=>$allow_manual_entries,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'),'accgro_group_id' => $post['account_groups']);
    		$insert_data=$callConfig->insertRecord('chart_of_accounts',$insert_array);
    		$return['res'] = $insert_data;
             //Open  account balance 
            
            if($insert_data['status'] =='sucess'){
                $totalopening_balance = 0;
                $configssql = "SELECT system_start_date FROM `config` WHERE company_id = ".$get['company_id']." AND is_active =0 ";
                $configres = $callConfig->getRow($configssql);
                $system_start_date = $configres->system_start_date !=""?$configres->system_start_date:date("Y-m-d");
                $getFinanceialYear = $this->getFinanceialYear($system_start_date);
                $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];

                $query = "INSERT INTO account_balances_details (`company_id`, `finance_year`, `account_code`, `location_id`, `department_id`, `cost_center_id`, `balance_date`, `debit_amount`, `credit_amount`, `opening_balance`, `closing_balance`, `is_active`, `current_status`,`created_by`,`created_date`) VALUES";
                foreach($post['opening_balance'] as $lockey=> $locid){
                     foreach($locid as $depkey=>$depid){
                        foreach($depid as $cckey=>$amount){
                            $totalopening_balance += $amount !=""?$amount:0;
                    $query.="('".$get['company_id']."','".$financeyear."' ,'".$post['account_code']."' ,'".$lockey."' ,'".$depkey."' ,'".$cckey."' ,'".$this->dateconvertdbformat($system_start_date)."','0','0','0','".$amount."','0','0','".$get['user_id']."','".date("Y-m-d H:i:s")."'),";
                        }
                    }
                }
                $prscquery= trim($query,",");

                $return['prscquery'] = $prscquery;
                $res4 = $callConfig->executeQuery($prscquery);
               
               $insertaryitmledger=array('company_id'=>$get['company_id'],'finance_year'=>$financeyear,'account_code'=>$post['account_code'],'balance_date'=>$this->dateconvertdbformat($system_start_date),'debit_amount'=>0,'credit_amount'=>0,'opening_balance'=>0,'closing_balance'=>$totalopening_balance,'is_active'=>0,'current_status'=>0,'created_by'=>'','created_date'=>date("Y-m-d H:i:s"));  
                $res2 = $callConfig->insertRecord('account_balances',$insertaryitmledger);
            }  

             //Close account balance 
       	}

		return $return;  
    }
    function insertTOCHeaders($get,$post){
    	global $callConfig;
    	$return =array();
    	$return['post'] = $post;
    	$encodedata = $post['header_contain'];
    	if($post['Rid']!=""){ 
    	     $insert_array = array('parent_toc_header_id'=>$post['parent_toc_header_id'],'text_of_content_id'=>$post['toc_id'],'header_name'=>$post['header_name'],'display_priority'=> $post['display_priority'],'header_contain'=>$encodedata,'updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d H:i:s'));
    		$res=$callConfig->updateRecord('toc_headers',$insert_array,'id',$post['Rid']);
    		$return['res'] = $res;
    	}else{
    		$insert_array = array('company_id'=>$get['company_id'],'parent_toc_header_id'=>$post['parent_toc_header_id'],'text_of_content_id'=>$post['toc_id'],'header_name'=>$post['header_name'],'display_priority'=> $post['display_priority'],'header_contain'=>$encodedata,'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s')); 
    		$insert_data=$callConfig->insertRecord('toc_headers',$insert_array);
    		$return['res'] = $insert_data;
    	}
		return $return;  
    }
 	function getTOCHeader($get){
    	global $callConfig;
    	$returnarray = array();
    	if($get['id'] != ""){
    		$codition .= " AND id = '".$get['id']."'";
    		$tocheaderssql = "SELECT id, parent_toc_header_id, header_name, display_priority, header_contain FROM toc_headers WHERE company_id = '".$get['company_id']."' ".$codition." AND is_active = 0";
			$tocheadersres = $callConfig->getRow($tocheaderssql);
			$returnarray['toc_headers_single'] = $tocheadersres;
    	}elseif($get['popup'] =="Y"){

    	}
    	$toc_headerssql = "SELECT id, parent_toc_header_id, header_name, display_priority, header_contain,header_type FROM toc_headers WHERE company_id = '".$get['company_id']."' AND text_of_content_id= '".$get['EID']."'  AND is_active = 0";
		$toc_headersres = $callConfig->getAllRows($toc_headerssql);
		$returnarray['toc_headers'] = $toc_headersres;

		$textofcontentssql = "SELECT id, toc_name,description FROM text_of_contents WHERE company_id = '".$get['company_id']."' AND id= '".$get['EID']."' AND is_active = 0";
		$textofcontentsres = $callConfig->getRow($textofcontentssql);
		$returnarray['text_of_contents'] = $textofcontentsres;

		$create_documents_fieldssql = "SELECT id, apply_table_name, application_table_name, display_field, value_field, label,preview_value,field_type FROM create_documents_fields WHERE company_id = '".$get['company_id']."' AND apply_table_name = 'toc_headers'  AND is_active = 0";
		$createdocumentsfieldsres = $callConfig->getAllRows($create_documents_fieldssql);
		$returnarray['create_documents_fields'] = $createdocumentsfieldsres;

		return $returnarray;

    }
    function getTextOfContentReports($get){
    	global $callConfig;
    	//echo "<pre>";
    	if($get['inputformreport'] =="Y"){
	    	$text_of_contentssql = "SELECT id, toc_name FROM text_of_contents WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$text_of_contentsres = $callConfig->getAllRows($text_of_contentssql);
			$returnarray['text_of_contents'] = $text_of_contentsres;

			$auditsql = "SELECT id, audit_name FROM audit WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$auditres = $callConfig->getAllRows($auditsql);
			$returnarray['audit'] = $auditres;


		}else{
			$auditsql = "SELECT * FROM audit WHERE company_id = '".$get['company_id']."' AND id = '".$get['audit_id']."' AND is_active = 0";
			$auditres = $callConfig->getRow($auditsql);
			$returnarray['audit'] = $auditres;

			$customerssql = "SELECT * FROM customers WHERE company_id = '".$get['company_id']."' AND id = '".$auditres->customer_id."' AND is_active = 0";
			$customersres = $callConfig->getRow($customerssql);
			$returnarray['customers'] = $customersres;

			$create_documents_fieldssql = "SELECT id, apply_table_name, application_table_name,  display_field, value_field, label,preview_value,field_type FROM create_documents_fields WHERE company_id = '".$get['company_id']."' AND apply_table_name = 'toc_headers'  AND is_active = 0";
			$createdocumentsfieldsres = $callConfig->getAllRows($create_documents_fieldssql);
			$returnarray['create_documents_fields'] = $createdocumentsfieldsres;
		}
    	$toc_headerssql = "SELECT id, parent_toc_header_id, header_name, display_priority, header_contain,header_type FROM toc_headers WHERE company_id = '".$get['company_id']."' AND text_of_content_id= '".$get['toc_id']."'  AND is_active = 0";
		$toc_headersres = $callConfig->getAllRows($toc_headerssql);
		$returnarray['toc_headers'] = $toc_headersres;

		$create_documents_fieldssql = "SELECT id, apply_table_name, application_table_name,  display_field, value_field, label,preview_value,field_type FROM create_documents_fields WHERE company_id = '".$get['company_id']."' AND apply_table_name = 'toc_headers'  AND is_active = 0";
		$createdocumentsfieldsres = $callConfig->getAllRows($create_documents_fieldssql);
		//print_r($createdocumentsfieldsres);
		$stringfields = array();
		$tables = array();
		$tablesresult = array();
	 	foreach($toc_headersres as $tocheadersres){
	 		$contents = urldecode(json_decode($tocheadersres->header_contain,1));
			foreach($createdocumentsfieldsres as $cdf){
				if($cdf->field_type == '2' || $cdf->field_type == '3'){
					$replacekey = strtolower(str_replace(' ', '_', $cdf->label));
   					$matchstring = "{[".$replacekey."]}";
					if(substr_count($contents,$matchstring) !=0) 
					{
						$stringfields[$tocheadersres->id][] = $cdf;
						$tables[$cdf->application_table_name] = $cdf->application_table_name;
					}
				}
			}
	 	}
	 	foreach($tables as $table){
	 		$valuefieldsql = "SELECT * FROM ".$table." WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$valuefieldres = $callConfig->getAllRows($valuefieldsql);
			$tablesresult[$table]= $valuefieldres;	
	 	}
		$returnarray['tablesresult']  = $tablesresult;
		$returnarray['stringfields']  = $stringfields;
		return $returnarray;
    }
    function getFinanaceReports($get){
        global $callConfig;
        $financereportgroupssql = "SELECT * FROM `finance_report_groups` WHERE company_id = ".$get['company_id']." AND finance_report_id = '".$get['fr_id']."' AND is_active =0 ORDER BY display_priority ASC";
        $financereportgroupsres = $callConfig->getAllRows($financereportgroupssql);
        $return['finance_report_groups']= $financereportgroupsres;

        // $financereportssql = "SELECT id,report_name FROM `finance_reports` WHERE company_id = ".$get['company_id']." AND is_active =0";
  //    $financereportgroupsres = $callConfig->getAllRows($financereportssql);
  //    $return['finance_reports']= $financereportgroupsres;
// $chartofaccounts = "SELECT coa.id,coa.account_code, coa.account_name ,coa.account_type,atype.account_type_name,atype.transaction_type FROM chart_of_accounts coa, account_type atype WHERE coa.company_id = ".$get['company_id']." AND coa.company_id = atype.company_id AND coa.account_type = atype.id AND coa.is_active = atype.is_active AND coa.is_active  = 0";
//      $chartofaccountsres = $callConfig->getAllRows($chartofaccounts);
//      $return['chart_of_accounts']= $chartofaccountsres;


        // $financereportchartofaccountsql = "SELECT id,finance_report_group_id,chart_of_account_code FROM `finance_report_chart_of_account` WHERE company_id = ".$get['company_id']." AND is_active =0";
  //    $financereportchartofaccountres = $callConfig->getAllRows($financereportchartofaccountsql);
  //    $return['finance_report_chart_of_account']= $financereportchartofaccountres;


// $trialbalancesql = "SELECT id,customer_account_code,account_name,debit_amount,credit_amount,mapping_account_code FROM `trial_balance` WHERE company_id = ".$get['company_id']." AND is_active =0";
//      $trialbalanceres = $callConfig->getAllRows($trialbalancesql);
//      $return['trial_balance']= $trialbalanceres;


    $sql = "SELECT tb.opening_balance,tb.debit_amount,tb.credit_amount,tb.closing_balance,tb.mapping_account_code,coa.account_type,frcoa.finance_report_group_id,atype.transaction_type, atype.account_type_name FROM trial_balance tb, chart_of_accounts coa, finance_report_chart_of_account frcoa, account_type atype WHERE tb.company_id = ".$get['company_id']." AND tb.company_id = coa.company_id AND tb.mapping_account_code = coa.account_code AND tb.is_active = coa.is_active AND tb.company_id = frcoa.company_id AND tb.mapping_account_code = frcoa.chart_of_account_code AND tb.is_active = frcoa.is_active AND tb.company_id = atype.company_id AND coa.account_type = atype.id AND tb.is_active = atype.is_active AND tb.is_active = 0";   
        $trialbalanceres = $callConfig->getAllRows($sql);
        $return['trial_balance']= $trialbalanceres;
        return $return;
    }
    function insertNotes($get,$post){
        global $callConfig;
        $return =array();
        $return['post'] = $post;
        if($post['note_id']!=""){ 
            $insert_array = array('parent_id'=>$post['parent_id'],'note_number'=>$post['note_number'],'note_name'=>$post['note_name'],'description'=> $post['description'],'updated_by'=>$get['user_id'],'updated_date'=>date('Y-m-d H:i:s'));
            $res=$callConfig->updateRecord('notes',$insert_array,'id',$post['note_id']);
            $return['res'] = $res;
        }else{
            $insert_array = array('company_id'=>$get['company_id'],'parent_id'=>$post['parent_id'],'note_number'=>$post['note_number'],'note_name'=>$post['note_name'],'description'=> $post['description'],'is_active'=>'0','current_status'=>'0','created_by'=>$get['user_id'],'created_date'=>date('Y-m-d H:i:s'));
            $insert_data=$callConfig->insertRecord('notes',$insert_array);
            $return['res'] = $insert_data;
        }
        return $return;  
    }
    function getNotes($get){
        global $callConfig;
        $notessql = "SELECT id, note_number, parent_id, note_name, description FROM notes WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY created_date ASC";
        $notesres = $callConfig->getAllRows($notessql);
        $return['notes'] = $notesres;
        return $return; 
    }
    function getJournals($get){
        global $callConfig;
        $currenciessql = "SELECT * FROM `journal_settings` WHERE company_id = ".$get['company_id']." AND (id = '".$get['rid']."' OR transaction_table = '".$get['tt']."') AND is_active =0";
        $currenciesres = $callConfig->getRow($currenciessql);
        $return['journal_settings']= $currenciesres;

        //  $journal_typesql = "SELECT id,journal_type_name FROM `journal_type` WHERE company_id = ".$get['company_id']." AND is_active =0";
        // $journal_typeres = $callConfig->getAllRows($journal_typesql);
        // $return['journal_type']= $journal_typeres;

        $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY display_priority ASC";
        $chart_of_accountsres = $callConfig->getAllRows($chart_of_accountssql);
        $return['chart_of_accounts']= $chart_of_accountsres;

        $currenciessql = "SELECT id,currency_name, currency_short_code, currency_symbol FROM `currencies` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $currenciesres = $callConfig->getAllRows($currenciessql);
        $return['currencies']= $currenciesres;

        $dynamic_mapping_tablessql = "SELECT id, page_title, application_table_name FROM `dynamic_mapping_tables` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $dynamic_mapping_tablesres = $callConfig->getAllRows($dynamic_mapping_tablessql);
        $return['dynamic_mapping_tables']= $dynamic_mapping_tablesres;
        

        return $return; 
    }
    function insertJournals($get,$post){
        global $callConfig;
        $return =array();
        $return['post'] = $post;
        foreach($post['currency_id'] as $keyid=>$valu){
            if($valu !=""){
                $journal_details[] =array('currency_id'=>$valu,'item_account'=>$post['item_account'][$keyid],'provision_account_check'=>$post['provision_account_check'][$keyid],'provision_account'=>$post['provision_account'][$keyid]); 
            }
        }
        $journal_detail = json_encode((array)$journal_details);
        $insertary=array('company_id'=>$post['company_id'],'journal_name'=>$post['journal_name'],'transaction_table'=>$post['transaction_table'],'discount_account'=>$post['discount_account'],'suspense_account'=>$post['suspense_account'],'forex_difference_account'=>$post['forex_difference_account'],'journal_details'=>$journal_detail,'journal_entry'=>$post['journal_entry']);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('journal_settings',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
        }else{
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('journal_settings',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        }
        return $return;  
    }
    function getTaxAuthorities($get){
        global $callConfig;
        $all_tax_authoritiessql = "SELECT id, tax_name, tax_type, tax_scope, tax_rate, tax_computation_id FROM `tax_authorities` WHERE company_id = ".$get['company_id']." AND tax_computation_id != '1' AND id != '".$get['rid']."'  AND is_active =0";
        $all_tax_authoritiesres = $callConfig->getAllRows($all_tax_authoritiessql);
        $return['all_tax_authorities']= $all_tax_authoritiesres;

        $taxtypesql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'TAXTYPE' AND is_active =0";
        $taxtyperes = $callConfig->getAllRows($taxtypesql);
        $taxtype = array();
        foreach($taxtyperes as $tt){
            $taxtype[$tt->genc_detail_code] = $tt;
        }
        $return['tax_type']= $taxtype;

        $taxscopesql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'TAXSCOPE' AND is_active =0";
        $taxscoperes = $callConfig->getAllRows($taxscopesql);
        $return['tax_scope']= $taxscoperes;

        $tax_computationssql = "SELECT id, tax_computations_name FROM `tax_computations` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $tax_computationsres = $callConfig->getAllRows($tax_computationssql);
        //$return['tax_computations']= $tax_computationsres;
        $tax_computations = array();
        foreach($tax_computationsres as $tc){
            $tax_computations[$tc->id] = $tc;
        }
        $return['tax_computations']= $tax_computations;

        $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY display_priority ASC";
        $chart_of_accountsres = $callConfig->getAllRows($chart_of_accountssql);
        $return['chart_of_accounts']= $chart_of_accountsres;
        
        if(isset($get['rid']) AND $get['rid'] !=""){
            $tax_authoritiessql = "SELECT * FROM `tax_authorities` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $tax_authoritiesres = $callConfig->getRow($tax_authoritiessql);
            $return['tax_authorities']= $tax_authoritiesres;

            $tax_authorities_ratessql = "SELECT id, tax_authority, dispatch_tax_province, tax_description FROM `tax_authorities_rates` WHERE company_id = ".$get['company_id']." AND tax_authority = '".$tax_authoritiesres->id."' AND is_active =0";
            $tax_authorities_ratesres = $callConfig->getAllRows($tax_authorities_ratessql);
            $return['tax_authorities_rates']= $tax_authorities_ratesres;


            $taiid = array();
            foreach($tax_authorities_ratesres as $tar){
                $taiid[$tar->id] = $tar->id;
            }

            if(count((array)$taiid) != 0){
                $tax_authoritiessql = "SELECT id, tax_rate, account_code, split_type, tax_authority_id, authority_rate_id FROM `tax_authorities_rates_split` WHERE company_id = ".$get['company_id']." AND tax_authority_id IN(".implode(',',$taiid).") AND is_active =0";
                $tax_authoritiessql = $callConfig->getAllRows($tax_authoritiessql);
                $return['tax_authorities_rates_split']= $tax_authoritiessql;
            }      
        }

        $hsn_and_sac_codessql = "SELECT id, code, code_description, gst, tax_authority_id FROM `hsn_and_sac_codes` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $hsn_and_sac_codesres = $callConfig->getAllRows($hsn_and_sac_codessql);
        $return['hsn_and_sac_codes']= $hsn_and_sac_codesres;

        return $return;  

    }
    function insertTaxAuthorities($get,$post){
        global $callConfig;
        $return =array();
        $return['post'] = $post;
        $group_tax = json_encode($post['group_tax'],1);
        $insertary=array('company_id'=>$post['company_id'],'tax_name'=>$post['tax_name'],'tax_type'=>$post['tax_type'],'tax_scope'=>$post['tax_scope'],'tax_computation_id'=>$post['tax_computation_id'],'description'=>$post['description'],'group_tax'=>$group_tax);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('tax_authorities',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
        }else{
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('tax_authorities',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        }
        $callConfig->updateRecord('hsn_and_sac_codes',array('tax_authority_id'=>''),'tax_authority_id',$rid);
        foreach($post['hsn_and_sac_code_id'] as $hsn_id){
            $callConfig->updateRecord('hsn_and_sac_codes',array('tax_authority_id'=>$rid),'id',$hsn_id);
        }

        $callConfig->updateRecord('tax_authorities_rates',array('is_active'=>null),'tax_authority',$rid);
        $callConfig->updateRecord('tax_authorities_rates_split',array('is_active'=>null),'tax_authority_id',$rid);

        foreach($post['tax_authorities_rates_seq'] as $parseq){    
            
            $insertary=array('company_id'=>$post['company_id'],'tax_authority'=>$rid,'tax_description'=>$post['tax_description'][$parseq],'is_active'=>'0');
            if($post['tax_authorities_rates_id'][$parseq] !=""){
                $insertary['updated_date']=date('Y-m-d h:i:s');
                $insertary['updated_by']=$post['user_id'];
                $res11= $callConfig->updateRecord('tax_authorities_rates',$insertary,'id',$post['tax_authorities_rates_id'][$parseq]);
                $return['tax_authorities_rates'][] = $res11;
                $tax_authorities_rates_id = $post['tax_authorities_rates_id'][$parseq];
            }else{
                $insertary['current_status']=0;;
                $insertary['created_by']=$post['user_id'];
                $insertary['created_date']=date("Y-m-d H:i:s");
                $res11 = $callConfig->insertRecord('tax_authorities_rates',$insertary);
                $tax_authorities_rates_id = $res11['LASTID'];
            }
            if($res11['status'] == "sucess"){

                foreach($post['split_type'][$parseq] as $keyid=>$v){
                    if($post['tax_rate_split'][$parseq][$keyid] !="" AND $post['accountcode_split'][$parseq][$keyid] !=""){
                        $insertary=array('company_id'=>$post['company_id'],'split_type'=>$v,'tax_authority_id'=>$rid,'tax_rate'=>$post['tax_rate_split'][$parseq][$keyid],'account_code'=>$post['accountcode_split'][$parseq][$keyid],'authority_rate_id'=>$tax_authorities_rates_id,'is_active'=>'0');
                        if($post['srid'][$parseq][$keyid] !=""){
                            $insertary['updated_date']=date('Y-m-d h:i:s');
                            $insertary['updated_by']=$post['user_id'];
                            $res= $callConfig->updateRecord('tax_authorities_rates_split',$insertary,'id',$post['srid'][$parseq][$keyid]);
                            $return['splite_res'][] = $res;
                        }else{
                            $insertary['current_status']=0;;
                            $insertary['created_by']=$post['user_id'];
                            $insertary['created_date']=date("Y-m-d H:i:s");
                            $res = $callConfig->insertRecord('tax_authorities_rates_split',$insertary);
                            $return['splite_res'][] = $res;
                        }
                    }
                }
            }    
        }

        return $return;  
    }
    function getFiscalPositions($get){
        global $callConfig;
        $return = array();
        $countriessql = "SELECT id, country_name FROM `countries` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY country_name ASC";
        $countriesres = $callConfig->getAllRows($countriessql);
        $return['countries']= $countriesres;

        $tax_authoritiessql = "SELECT id, tax_name FROM `tax_authorities` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $tax_authoritiesres = $callConfig->getAllRows($tax_authoritiessql);
        $return['tax_authorities']= $tax_authoritiesres;

        $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY display_priority ASC";
        $chart_of_accountsres = $callConfig->getAllRows($chart_of_accountssql);
        $return['chart_of_accounts']= $chart_of_accountsres;

        if(isset($get['rid']) AND $get['rid'] !=""){
            $fiscal_positionssql = "SELECT * FROM `fiscal_positions` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $fiscal_positionsres = $callConfig->getRow($fiscal_positionssql);
            $return['fiscal_positions']= $fiscal_positionsres;

            $fiscal_positions_mappingssql = "SELECT * FROM `fiscal_positions_mappings` WHERE company_id = ".$get['company_id']." AND fiscal_positions_id = '".$get['rid']."' AND is_active =0";
            $fiscal_positions_mappingsres = $callConfig->getAllRows($fiscal_positions_mappingssql);
            $return['fiscal_positions_mappings']= $fiscal_positions_mappingsres;
        }

        return $return;  

    }
    function insertFiscalPositions($get,$post){
        global $callConfig;
        $return =array();
        $return['post'] = $post;
        $insertary=array('company_id'=>$post['company_id'],'fiscal_positions_name'=>$post['fiscal_positions_name'],'country_id'=>$post['country_id']);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('fiscal_positions',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
        }else{
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('fiscal_positions',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        }

        $callConfig->updateRecord('fiscal_positions_mappings',array('is_active'=>null),'fiscal_positions_id',$rid);
        foreach($post['fiscal_type'] as $keyid=>$v){
            if($post['get_type'][$keyid] !="" AND $post['apply_type'][$keyid] !=""){
                $insertary=array('company_id'=>$post['company_id'],'fiscal_type'=>$v,'fiscal_positions_id'=>$rid,'get_type'=>$post['get_type'][$keyid],'apply_type'=>$post['apply_type'][$keyid],'is_active'=>'0');
                if($post['srid'][$keyid] !=""){
                    $insertary['updated_date']=date('Y-m-d h:i:s');
                    $insertary['updated_by']=$post['user_id'];
                    $res= $callConfig->updateRecord('fiscal_positions_mappings',$insertary,'id',$post['srid'][$keyid]);
                    $return['ampping_res'][] = $res;
                }else{
                    $insertary['current_status']=0;;
                    $insertary['created_by']=$post['user_id'];
                    $insertary['created_date']=date("Y-m-d H:i:s");
                    $res = $callConfig->insertRecord('fiscal_positions_mappings',$insertary);
                    $return['ampping_res'][] = $res;
                }
            }
        }
        return $return;
    }
    function getDeferredRevenues($get){
        global $callConfig;
        $return =array();
        $return['get'] = $get;
        $depreciationmethodsql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'DEPRECIATIONMETHOD' AND is_active =0";
        $depreciationmethodres = $callConfig->getAllRows($depreciationmethodsql);
        $return['depreciation_method']= $depreciationmethodres;

         $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0 ORDER BY display_priority ASC";
        $chart_of_accountsres = $callConfig->getAllRows($chart_of_accountssql);
        $return['chart_of_accounts']= $chart_of_accountsres;

        if(isset($get['rid']) AND $get['rid'] !=""){
            $fiscal_positionssql = "SELECT * FROM `deferred_revenues` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $fiscal_positionsres = $callConfig->getRow($fiscal_positionssql);
            $return['deferred_revenues']= $fiscal_positionsres;
        }


        return $return;
    }
    function insertDeferredRevenues($get,$post){
        global $callConfig;
        $return =array();
        $return['post'] = $post;
        foreach($post['depreciation_date'] as $keyid=>$valu){
            $deferred_revenue_table[] =array('depreciation_date'=>$valu,'per_year_deprciation'=>$post['per_year_deprciation'][$keyid],'accumulated_depreciation'=>$post['accumulated_depreciation'][$keyid],'depreciation_price'=>$post['depreciation_price'][$keyid]); 
        }
        $deferredrevenuetable = json_encode((array)$deferred_revenue_table);
        $insertary=array('company_id'=>$post['company_id'],'deferred_revenue_name'=>$post['deferred_revenue_name'],'original_amount'=>$post['original_amount'],'acquisition_date'=>$this->dateconvertdbformat($post['acquisition_date']),'scrap_amount'=>$post['scrap_amount'],'deferred_revenue_amount'=>$post['deferred_revenue_amount'],'duration'=>$post['duration'],'end_deferred_revenue_date'=>$this->dateconvertdbformat($post['end_deferred_revenue_date']),'deferred_revenue_account'=>$post['deferred_revenue_account'],'revenue_account'=>$post['revenue_account'],'Journal'=>$post['Journal'],'deferred_revenue_table'=>$deferredrevenuetable);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('deferred_revenues',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
        }else{
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('deferred_revenues',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        }
        return $return;
    }
    function getFollowUpLevels($get){
         global $callConfig;
        $return =array();
        $return['get'] = $get;
        // $followupactivityssql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'FOLLOUPACTIVITY' AND is_active =0";
        // $followupactivitysres = $callConfig->getAllRows($followupactivityssql);
        // $crm_lead_typessql = "SELECT id,type_name FROM crm_lead_types WHERE company_id = '".$get['company_id']."' AND is_active = 0";
        // $crm_lead_typesres=$callConfig->getAllRows($crm_lead_typessql);

        // $follow_up_activity = array();
        // foreach($crm_lead_typesres as $fua){
        //     $follow_up_activity[$fua->id] = $fua->type_name;
        // }
        // $return['follow_up_activity']= $follow_up_activity;

        $email_contentsql = "SELECT id, template_name FROM `email_content` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $email_contentres = $callConfig->getAllRows($email_contentsql);
        $email_content = array();
        foreach($email_contentres as $ec){
            $email_content[$ec->id] = $ec->template_name;
        }
        $return['email_content']= $email_content;

        $follow_up_levelssql = "SELECT * FROM `follow_up_levels` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $follow_up_levelsres = $callConfig->getAllRows($follow_up_levelssql);
        $return['follow_up_levels_all']= $follow_up_levelsres;

        if(isset($get['rid']) AND $get['rid'] !=""){
            $fiscal_positionssql = "SELECT * FROM `follow_up_levels` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $fiscal_positionsres = $callConfig->getRow($fiscal_positionssql);
            $return['follow_up_levels']= $fiscal_positionsres;
        }
        return $return;
    }
    function insertFollowUpLevels($get,$post){
         global $callConfig;
        $return =array();
        $return['post'] = $post;
        $insertary=array('company_id'=>$post['company_id'],'follow_up_level_name'=>$post['follow_up_level_name'],'days'=>$post['days'],'send_sms'=>$post['send_sms'],'follow_up_call'=>$post['follow_up_call'],'send_email'=>$post['send_email'],'attach_invoice'=>$post['attach_invoice'],'email_content_id'=>$post['email_content_id']);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('follow_up_levels',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
        }else{
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('follow_up_levels',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        }
        return $return;
    }
    function getReconciliations($get){
        global $callConfig;
        $return =array();
        $reconciliationtypesql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'RECONCILIATIONTYPE' AND is_active =0";
        $reconciliationtyperes = $callConfig->getAllRows($reconciliationtypesql);
        $reconciliationtype = array();
        foreach($reconciliationtyperes as $rct){
            $reconciliationtype[$rct->genc_detail_code] = $rct->genc_detail_name;
        }
        $return['reconciliationtype']= $reconciliationtype;

        $transactiontypesql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'TRANSACTIONTYPE' AND is_active =0";
        $transactiontyperes = $callConfig->getAllRows($transactiontypesql);
        $transactiontype = array();
        foreach($transactiontyperes as $tt){
            $transactiontype[$tt->genc_detail_code] = $tt->genc_detail_name;
        }
        $return['transactiontype']= $transactiontype;

        $journalssql = "SELECT id,journal_name FROM `journals` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $journalsres = $callConfig->getAllRows($journalssql);
        $return['journals']= $journalsres;

        $chartofaccountssql = "SELECT id,account_name,account_code FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
        $return['chart_of_accounts']= $chartofaccountsres;

        $vendorssql = "SELECT id,display_name FROM `vendors` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $vendorsres = $callConfig->getAllRows($vendorssql);
        $return['vendors']= $vendorsres;

        $customerssql = "SELECT id,display_name FROM `customers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        $return['customers']= $customersres;

        if(isset($get['rid']) AND $get['rid'] !=""){
            $reconciliationssql = "SELECT * FROM `reconciliations` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $reconciliationsres = $callConfig->getRow($reconciliationssql);
            $return['reconciliations']= $reconciliationsres;
        }
        return $return;
    }
    function insertReconciliations($get,$post){
         global $callConfig;
        $return =array();
        $return['post'] = $post;
        $journal_availability = json_encode((array)$post['journal_availability']); 
        $customer_ids = json_encode((array)$post['customer_ids']);
        $vendor_ids = json_encode((array)$post['vendor_ids']); 
        $counterpart_entry = array(); 
        foreach($post['chart_of_account_id'] as $key=>$val){
            $counterpart_entry[] = array('chart_of_account_id'=>$post['chart_of_account_id'][$key],'counter_part_amount_type'=>$post['counter_part_amount_type'][$key],'counter_part_amount'=>$post['counter_part_amount'][$key]);
        }
        $counterpart_entry = json_encode($counterpart_entry);
        $insertary=array('company_id'=>$post['company_id'],'reconciliation_name'=>$post['reconciliation_name'],'reconciliation_type'=>$post['reconciliation_type'],'auto_validate'=>$post['auto_validate'],'search_month_limit'=>$post['search_month_limit'],'journal_availability'=>$journal_availability,'amount_type'=>$post['amount_type'],'transaction_type'=>$post['transaction_type'],'amount_condition'=>$post['amount_condition'],'amount'=>$post['amount'],'customer_ids'=>$customer_ids,'vendor_ids'=>$vendor_ids,'same_currency'=>$post['same_currency'],'counterpart_entry'=>$counterpart_entry);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('reconciliations',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
        }else{
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('reconciliations',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        }
        return $return;
    }
    function togetbudgetuserdeailsaccount($post){
		global $callConfig;
		$query = "SELECT * FROM chart_of_accounts WHERE account_code IN (SELECT account_code FROM gl_account_users WHERE user_id = ".$post['user_id'].")";
		$data = $callConfig->getAllRows($query);
		foreach ($data as $value) {
			$return [$value->accgro_group_id][] = (array)$value;
			$group_ids[] = $value->accgro_group_id;
		}
		$group_ids = implode(",", (array)$group_ids);
		$query2 = "SELECT id,group_name,parent_group_id,accsec_section_id FROM account_groups WHERE id in(".$group_ids.")";
		$data2 = $callConfig->getAllRows($query2);
		foreach ($data2 as  $value2) {
			$returns1  = (array)$value2;
			$returns1['details'] = $return[$value2->id];
			$returns[$value2->accsec_section_id][] = $returns1;
			$account_section[] = $value2->accsec_section_id;
		}

		$account_section = implode(",", (array)$account_section);
		$query3 = "SELECT id,section_name,section_description FROM account_sections WHERE id in(".$account_section.")";
		$data3 = $callConfig->getAllRows($query3);
		foreach ($data3 as  $value3) {
			$main[$value3->id] = (array)$value3;
			$main[$value3->id]['data'] = $returns [$value3->id];
		}
		//echo "<pre>".$account_section; print_r($main);
		
		$query3 = "SELECT cc.id, cc.cost_center_name, cc.cost_center_type FROM cost_centers cc, user_cost_centers ucc WHERE cc.company_id = ucc.company_id AND cc.company_id = ".$post['company_id']." AND cc.id = ucc.cost_center_id AND ucc.user_id = ".$post['user_id']." AND ucc.is_active =0 AND cc.is_active = 0";
		$cost_centers = $callConfig->getAllRows($query3);
		if($post['fiscal_year']!="" && $post['cost_center']!="" ){
			$query = "SELECT * FROM account_budgets WHERE company_id = ".$post['company_id']." AND fiscal_year = '".$post['fiscal_year']."' AND cost_center_id =".$post['cost_center']." AND  is_active = 0";
			$account_budgets = $callConfig->getRow($query);
			$querys = "SELECT * FROM budget WHERE company_id = ".$post['company_id']." AND fiscal_year = '".$post['fiscal_year']."' AND cost_center_id =".$post['cost_center']."  AND  is_active = 0";
			$budget = $callConfig->getAllRows($querys);
		}
		return array('main'=>$main, 'cost_centers'=>$cost_centers, 'account_budgets'=>$account_budgets, 'budget'=>$budget, 'account_budget_query'=>$query, 'budget_query'=>$querys);
	}
	function budgetinsertaccount($get,$post){
		global $callConfig;		
		//print_r($post); exit;
		$whrqu= "UPDATE account_budgets SET is_active = NULL , updated_by = ".$get['user_id']." WHERE company_id= '".$get['company_id']."' AND  cost_center_id='".$post['cost_center_id']."' AND fiscal_year='".$post['fiscal_year']."' AND is_active = 0";
		$rres = $callConfig->executeQuery($whrqu);
		$whrqu= "UPDATE budget SET is_active = NULL , updated_by = ".$get['user_id']." WHERE company_id= '".$get['company_id']."' AND  cost_center_id='".$post['cost_center_id']."' AND fiscal_year='".$post['fiscal_year']."' AND is_active = 0";
		$rres = $callConfig->executeQuery($whrqu);
		
		$insert_array = array('company_id'=>$get['company_id'],'budget_name'=>$post['budget_name'],'user_id'=>$post['user_id'],'budget_created'=>$post['inveral'],'budget_version'=>$post['budget_version'],'cost_center_id'=>$post['cost_center_id'],'fiscal_year'=>$post['fiscal_year'],'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id']);
		$res[]=$callConfig->insertRecord('account_budgets',$insert_array);
		foreach ($post['data'] as $value) {
			$value['company_id'] = $get['company_id'];
			//$value['account_code'] = $post['account_code'];
			$value['budget_version'] = $post['budget_version'];
			$value['budget_date'] = $post['budget_date'];
			$value['budget_created'] = $post['inveral'];
			$value['cost_center_id'] = $post['cost_center_id'];
			$value['fiscal_year'] = $post['fiscal_year'];
			$value['user_id'] = $post['user_id'];
			$value['is_active'] = 0;
			$value['created_by'] = $post['user_id'];
			$value['current_status'] = 0;
			$value['action_if_exceed_yearly'] = $post['action_if_exceed_yearly'];
			$value['action_if_exceed_quaterly'] = $post['action_if_exceed_quaterly'];
			$value['action_if_exceed_halfyearly'] = $post['action_if_exceed_halfyearly'];
			$value['action_if_exceed_month'] = $post['action_if_exceed_month'];
			//$data = $this->insertorupdatebudgetaccount($value);
			$res[]=$callConfig->insertRecord('budget',$value);
		}
		return $res;
	}
	function insertorupdatebudgetaccount($post){
		global $callConfig;
		$whr1="company_id= '".$post['company_id']."' and budget_version='".$post['budget_version']."' and cost_center_id='".$post['cost_center_id']."' and account_code='".$post['account_code']."' and fiscal_year='".$post['fiscal_year']."'";
		$query2 = $callConfig->selectQuery('budget','*',$whr1,'','','');
		//echo $query2; exit;
		$data = $callConfig->getRow($query2);
		if($data->id !=''){
			$suc_data=$callConfig->updateRecord('budget',$post,'id',$data->id);
		}else{
			$res=$callConfig->insertRecord('budget',$post);
		}
		return $res;
	}
    function getJournalVouchers($get){
        global $callConfig;
        $return= $partylist = array();
        if(isset($get['rid']) AND $get['rid'] !=""){
            $journal_voucherssql = "SELECT * FROM `journal_vouchers` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $journal_vouchersres = $callConfig->getRow($journal_voucherssql);
            $return['journal_vouchers']= $journal_vouchersres;

            if($journal_vouchersres->ref_type !=""){

                $application_pagessaql = "SELECT application_table_name, view_page_name, list_page_name, insert_page_name FROM application_pages WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$journal_vouchersres->ref_type."' AND is_active=0";
                $application_pagessres = $callConfig->getRow($application_pagessaql);
                $return['ref_details_table']= $application_pagessres;

                $parentdmtsql = "SELECT id,display_table,item_table,tax_table,application_table_name FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$journal_vouchersres->ref_type."' AND item_table !='' AND is_active = 0";
                $parentdmtres=$callConfig->getRow($parentdmtsql);
                $return['parentdmtsql']= $parentdmtsql;

                $ref_deatils_sql = "SELECT * FROM ".$parentdmtres->application_table_name." WHERE company_id = ".$get['company_id']." AND id = '".$journal_vouchersres->ref_id."' AND is_active =0";
                $ref_deatils_res = $callConfig->getRow($ref_deatils_sql);
                $return['ref_details_sql']= $ref_deatils_sql;
                $return['ref_details']= $ref_deatils_res;

                $ref_item_deatils_sql = "SELECT * FROM ".$parentdmtres->item_table." WHERE company_id = ".$get['company_id']." AND transaction_id = '".$journal_vouchersres->ref_id."' AND is_active =0";
                $ref_item_deatils_res = $callConfig->getAllRows($ref_item_deatils_sql);
                $return['ref_item_details_sql']= $ref_item_deatils_sql;
                $return['ref_item_details']= $ref_item_deatils_res;

                //$get['transaction_table'] = $parentdmtres->application_table_name;
               // $return['display_attributes'] = $this->getTopUpdetails($get);                    
            }
            


            $jv_itemssql = "SELECT * FROM `jv_items` WHERE company_id = ".$get['company_id']." AND transaction_id = '".$get['rid']."' AND is_active =0";
            $jv_itemsres = $callConfig->getAllRows($jv_itemssql);
            $return['jv_items']= $jv_itemsres;
       
       

        }
        $locationsql = "SELECT id,location_name FROM `locations` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $locationres = $callConfig->getAllRows($locationsql);
        $return['locations']= $locationres;   

        $cost_centerssql = "SELECT id,cost_center_name FROM `cost_centers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $cost_centersres = $callConfig->getAllRows($cost_centerssql);
        $return['cost_centers']= $cost_centersres;

        $departmentssql = "SELECT id,department_name FROM `departments` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $departmentsres = $callConfig->getAllRows($departmentssql);
        $return['departments']= $departmentsres;


        $currenciessql = "SELECT id,currency_name, currency_short_code, currency_symbol FROM `currencies` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $currenciesres = $callConfig->getAllRows($currenciessql);
        $return['currencies']= $currenciesres;

        $journalssql = "SELECT id,journal_name FROM `journal_settings` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $journalsres = $callConfig->getAllRows($journalssql);
        $return['journal_settings']= $journalsres;

        $chartofaccountssql = "SELECT id,account_name,account_code FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND allow_posting = 0 AND is_active =0 ORDER BY account_name ASC";
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);

        $return['chart_of_accounts']= $chartofaccountsres;

        $vendorssql = "SELECT id,display_name,company FROM `vendors` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $vendorsres = $callConfig->getAllRows($vendorssql);
        foreach($vendorsres as $prty){
            $partylist['vendors'][$prty->id]= $prty;
        }

        $customerssql = "SELECT id,display_name ,company FROM `customers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        foreach($customersres as $prty){
            $partylist['customers'][$prty->id]= $prty;
        }

        $employeessql = "SELECT id,display_name FROM `employees` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $employeesres = $callConfig->getAllRows($employeessql);
        foreach($employeesres as $prty){
            $partylist['employees'][$prty->id]= $prty;
        }
        $return['partylist'] = $partylist;
        return $return;

    }
    function insertJournalVouchers($get,$post){
         global $callConfig;
        $return =array();
        $return['post'] = $post; 
        $insertary=array('company_id'=>$post['company_id'],'transaction_date'=>$this->dateconvertdbformat($post['transaction_date']),'currency_id'=>$post['currency_id'],'printing_notes'=>$post['printing_notes'],'notes'=>$post['notes'],'amount'=>$post['total_amount'],'total_amount'=>$post['total_amount'],'journal_id'=>$post['journal_id'],'doc_ref_no'=>$post['doc_ref_no'],'doc_ref_date'=>$this->dateconvertdbformat($post['doc_ref_date']));
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('journal_vouchers',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
            //all transaction update
            $callConfig->executeQuery("DELETE FROM jv_items WHERE transaction_id = '".$rid."'");
            $callConfig->executeQuery("DELETE FROM jv_items_references WHERE transaction_id = '".$rid."'");
            $callConfig->executeQuery("DELETE FROM general_ledger WHERE voucher_id = '".$rid."' AND ledger_type = 'JV'");
            //$callConfig->updateRecord('jv_items',array('is_active'=>'1'),'transaction_id',$rid);
            //all transaction update
        }else{
            $transation = $this->toGetApplicationSerialNumber('journal_vouchers');
            $transationyear = $this->toGetApplicationVoucherSerialNumbers($post,'journal_vouchers',$post['transaction_date'],'Y');
            $return['transationyear'] = $transationyear;
            $transationmonth = $this->toGetApplicationVoucherSerialNumbers($post,'journal_vouchers',$post['transaction_date'],'M');
             $return['transationmonth'] = $transationmonth;
            $insertary['transaction_number']=$transation;
            $insertary['transaction_number_yearly']=$transationyear;
            $insertary['transaction_number_monthly']=$transationmonth;

            $post['transaction_number']=$transation;
            $insertary['originator']=$post['user_id'];
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('journal_vouchers',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        } 
        foreach($post['old_jv_item_id'] as $key=>$val){
            $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['old_account_code'][$key],'date'=>$this->dateconvertdbformat($post['old_transaction_date']),'debit_amount'=>$post['old_debit_amount'][$key],'credit_amount'=>$post['old_credit_amount'][$key],'account_type'=>$post['old_account_type'][$key],'location_id'=>$post['old_transaction_location_id'][$key],'department_id'=>$post['old_department_id'][$key],'cost_center_id'=>$post['old_cost_center_id'][$key]);
            $updatebalances = $this->insertReverseAccountBalances($balanceary);
            $return['reversaerenty'][] = $updatebalances;
        }
        foreach($post['chart_of_account_id'] as $key=>$val){
            if($post['party_id'][$key] !=""){
                $explod = explode('||', $post['party_id'][$key]);
                $party_transaction_table = $explod[1];
                $party_id = $explod[0];
            }
            $insertaryitm=array('company_id'=>$post['company_id'],'transaction_id'=>$rid,'account_code'=>$post['account_code'][$key],'particulars'=>$post['particulars'][$key],'debit_amount'=>$post['debit_amount'][$key],'credit_amount'=>$post['credit_amount'][$key],'party_transaction_table'=>$party_transaction_table,'party_id'=>$party_id,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'account_name'=>$post['account_name'][$key],'transaction_location_id'=>$post['transaction_location_id'][$key],'department_id'=>$post['department_id'][$key],'cost_center_id'=>$post['cost_center_id'][$key],'account_type'=>$post['account_type'][$key]);
            
            $res1 = $callConfig->insertRecord('jv_items',$insertaryitm);
            $transaction_item_id = $res1['LASTID'];
            $return['res_item'][] = $res1;

 /// balance update
            $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['account_code'][$key],'date'=>$this->dateconvertdbformat($post['transaction_date']),'debit_amount'=>$post['debit_amount'][$key],'credit_amount'=>$post['credit_amount'][$key],'account_type'=>$post['account_type'][$key],'location_id'=>$post['transaction_location_id'][$key],'department_id'=>$post['department_id'][$key],'cost_center_id'=>$post['cost_center_id'][$key]);

            $return['balanceenty_ary'][] = $balanceary;
            $updatebalances = $this->insertAccountBalances($balanceary);
            $return['balanceenty'][] = $updatebalances;
 /// balance update
            $insertaryitmref=array('company_id'=>$post['company_id'],'transaction_id'=>$rid,'transaction_item_id'=>$transaction_item_id,'item_id'=>$post['chart_of_account_id'][$key],'account_code'=>$post['account_code'][$key],'particulars'=>$post['particulars'][$key],'debit_amount'=>$post['debit_amount'][$key],'credit_amount'=>$post['credit_amount'][$key],'item_name'=>$post['account_name'][$key],'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));  
            $res1 = $callConfig->insertRecord('jv_items_references',$insertaryitmref);
            $return['res_item_ref'][] = $res1;

            
            $insertaryitmledger=array('company_id'=>$post['company_id'],'voucher_id'=>$rid,'voucher_ref_no'=>$post['transaction_number'],'voucher_date'=>$this->dateconvertdbformat($post['transaction_date']),'account_code'=>$post['account_code'][$key],'debit_amount'=>$post['debit_amount'][$key],'credit_amount'=>$post['credit_amount'][$key],'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'ledger_type'=>'JV','account_name'=>$post['account_name'][$key],'remarks'=>$post['particulars'][$key],'account_type'=>$post['account_type'][$key]);  
            

            $closingbalamt = $this->getAccountClosingBalance($post,$post['account_code'][$key]);
            $return['closingbalamt'][] = $closingbalamt;
            if($post['debit_amount'][$key] !="" AND $post['debit_amount'][$key] !="0"){
                $insertaryitmledger['balance_amount'] = $closingbalamt-$post['debit_amount'][$key];
            }elseif($post['credit_amount'][$key] !="" AND $post['credit_amount'][$key] !="0"){
                $insertaryitmledger['balance_amount'] = $post['credit_amount'][$key]+$closingbalamt;
            }
            $res2 = $callConfig->insertRecord('general_ledger',$insertaryitmledger);
            $return['res_item_ledger'][] = $res2;
        }
        /**
        foreach($post['chart_of_account_id'] as $key=>$val){
            $insertaryitm=array('company_id'=>$post['company_id'],'transaction_id'=>$rid,'account_code'=>$post['account_code'][$key],'particulars'=>$post['particulars'][$key],'debit_amount'=>$post['debit_amount'][$key],'credit_amount'=>$post['credit_amount'][$key],'is_active'=>0,'current_status'=>0);  
            if($post['jv_item_id'][$key] !=""){ 
                $insertaryitm['updated_date']=date('Y-m-d h:i:s');
                $insertaryitm['updated_by']=$post['user_id'];
                $res1= $callConfig->updateRecord('jv_items',$insertaryitm,'id',$post['jv_item_id'][$key]);
                $return['res_item'] = $res1;
            }else{
                $insertaryitm['created_by']=$post['user_id'];
                $insertaryitm['created_date']=date("Y-m-d H:i:s");
                $res1 = $callConfig->insertRecord('jv_items',$insertaryitm);
                $return['res_item'] = $res1;
            } 
        } **/
        
        return $return;
    }
     function insertReverseAccountBalances($journalEntry) {
        global $callConfig;
        $return['journalEntry'] = $journalEntry;
        $company_id = $journalEntry['company_id'];
        $accountId = $journalEntry['account_code'];
        $entryDate = $this->dateconvertdbformat($journalEntry['date']);
        $debitAmount = $journalEntry['debit_amount'] !=""?$journalEntry['debit_amount']:0;
        $creditAmount = $journalEntry['credit_amount'] !=""?$journalEntry['credit_amount']:0;
        $accountType = $journalEntry['account_type']; 
        $location_id = $journalEntry['location_id'];  
        $department_id = $journalEntry['department_id']; 
        $cost_center_id = $journalEntry['cost_center_id']; 
        $getFinanceialYear = $this->getFinanceialYear($entryDate);
        $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];
        // Determine if the amount should be treated as debit or credit
         $isDebit = in_array($accountType, array('1', '4'));
        $amountChange = $isDebit ? $debitAmount - $creditAmount : $creditAmount - $debitAmount;
       
//account_balances start

         // Check if an entry exists for the entry date
        $sql = "SELECT * FROM account_balances WHERE company_id ='".$company_id."' AND account_code = '".$accountId."' AND finance_year = '".$financeyear."' AND balance_date = '".$entryDate."' AND is_active = 0";
        $existingEntry = $callConfig->getRow($sql);
        $return['existingEntrysql'] = $sql;
        $return['existingEntry'] = $existingEntry;

        if($existingEntry->id !=""){
            $updatedBalance = $existingEntry->closing_balance - $amountChange;
            $sql = "UPDATE account_balances SET closing_balance = '".$updatedBalance."', debit_amount = debit_amount-".$debitAmount.",credit_amount = credit_amount-".$creditAmount." WHERE company_id ='".$company_id."' AND id = '".$existingEntry->id."'";
            $updateaccres = $callConfig->executeQuery($sql);
            $return['update_account_balances'] = $sql;
        }

        $sql = "UPDATE account_balances SET closing_balance = closing_balance + ".$amountChange." WHERE company_id ='".$company_id."' AND finance_year = '".$financeyear."' AND account_code = '".$accountId."' AND balance_date > '".$entryDate."' AND is_active = 0";
        $updateaccres = $callConfig->executeQuery($sql);
        $return['update_all_record'] = $sql;
//account_balances End    


//account_balances start
         // Check if an entry exists for the entry date
         $sql = "SELECT * FROM account_balances_details WHERE company_id ='".$company_id."' AND account_code = '".$accountId."' AND finance_year = '".$financeyear."' AND balance_date = '".$entryDate."' AND location_id = '".$location_id."'  AND department_id = '".$department_id."' AND cost_center_id = '".$cost_center_id."' AND is_active = 0 ORDER BY balance_date DESC LIMIT 1";

        $existingEntry = $callConfig->getRow($sql);
        $return['existingEntrysql_details'] = $sql;
        $return['existingEntry_details'] = $existingEntry;

        if($existingEntry->id !=""){
            $updatedBalance = $existingEntry->closing_balance - $amountChange;
            $sql = "UPDATE account_balances_details SET closing_balance = '".$updatedBalance."', debit_amount = debit_amount-".$debitAmount.",credit_amount = credit_amount-".$creditAmount." WHERE company_id ='".$company_id."' AND id = '".$existingEntry->id."'";
            $updateaccres = $callConfig->executeQuery($sql);
            $return['update_account_balances_details'] = $sql;
        }

        $sql = "UPDATE account_balances_details SET closing_balance = closing_balance + ".$amountChange." WHERE company_id ='".$company_id."' AND finance_year = '".$financeyear."' AND account_code = '".$accountId."' AND balance_date > '".$entryDate."' AND location_id = '".$location_id."'  AND department_id = '".$department_id."' AND cost_center_id = '".$cost_center_id."' AND is_active = 0";
        $updateaccres = $callConfig->executeQuery($sql);
        $return['update_all_record_details'] = $sql;
//account_balances End    



        return $return;    
    }
    function insertAccountBalances($journalEntry){
        global $callConfig;
        $return['journalEntry'] = $journalEntry;
        $company_id = $journalEntry['company_id'];
        $accountId = $journalEntry['account_code'];
        $entryDate = $this->dateconvertdbformat($journalEntry['date']);
        $debitAmount = $journalEntry['debit_amount'] !=""?$journalEntry['debit_amount']:0;
        $creditAmount = $journalEntry['credit_amount'] !=""?$journalEntry['credit_amount']:0;
        $accountType = $journalEntry['account_type']; 
        $location_id = $journalEntry['location_id'];  
        $department_id = $journalEntry['department_id']; 
        $cost_center_id = $journalEntry['cost_center_id']; 

        $getFinanceialYear = $this->getFinanceialYear($entryDate);
        $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];
        // Determine if the amount should be treated as debit or credit
        $isDebit = in_array($accountType, array('1', '4'));
        $amountChange = $isDebit ? $debitAmount - $creditAmount : $creditAmount - $debitAmount;

//account_balances Start   
         // Check if an entry exists for the entry date
        $sql = "SELECT * FROM account_balances WHERE company_id ='".$company_id."' AND account_code = '".$accountId."' AND finance_year = '".$financeyear."' AND balance_date = '".$entryDate."' AND is_active = 0";
        $existingEntry = $callConfig->getRow($sql);
        $return['existingEntrysql'] = $sql;
        $return['existingEntry'] = $existingEntry;

        if($existingEntry->id !=""){
            // Calculate the difference between the new amount and the old amount
            $oldDebitAmount = $existingEntry->debit_amount;
            $oldCreditAmount = $existingEntry->credit_amount;
            
            $updateddebitamt = $oldDebitAmount +$debitAmount;
            $updatedcreditamt = $oldCreditAmount +$creditAmount; 
            
            $oldAmountChange = $isDebit ? $oldDebitAmount - $oldCreditAmount : $oldCreditAmount - $oldDebitAmount;
     
           // $amountDifference = $amountChange - $oldAmountChange;
     
            // Update the balance for the existing entry
            $updatedBalance = $existingEntry->closing_balance + $amountChange;

            $sql = "UPDATE account_balances SET closing_balance = '".$updatedBalance."', debit_amount = '".$updateddebitamt."',credit_amount = '".$updatedcreditamt."' 
                    WHERE company_id ='".$company_id."' AND id = '".$existingEntry->id."'";

            $updateaccres = $callConfig->executeQuery($sql);
            $return['update_account_balances'] = $sql;

            // Update amountChange to reflect the difference for future updates
           // $amountChange = $amountDifference;
        }else{

            // Fetch the latest balance before the entry date
            $sql = "SELECT closing_balance, balance_date FROM account_balances WHERE company_id ='".$company_id."' AND finance_year = '".$financeyear."' AND account_code = '".$accountId."' AND balance_date < '".$entryDate."' AND is_active = 0 ORDER BY balance_date DESC LIMIT 1";
            $latestBalance = $callConfig->getRow($sql);
            $return['latestBalancesql'] = $sql;
            $return['latestBalance'] = $latestBalance;
            $return['select_lastbal_account_balances_sql'] = $sql; 
            $return['select_lastbal_account_balances_arey'] = $latestBalance; 
            
            $newBalance = ($latestBalance->closing_balance!=""?$latestBalance->closing_balance:0) + $amountChange;

            $insertaryitmledger=array('company_id'=>$company_id,'finance_year'=>$financeyear,'account_code'=>$accountId,'balance_date'=>$this->dateconvertdbformat($entryDate),'debit_amount'=>$debitAmount,'credit_amount'=>$creditAmount,'opening_balance'=>$latestBalance->closing_balance,'closing_balance'=>$newBalance,'is_active'=>0,'current_status'=>0,'created_by'=>'','created_date'=>date("Y-m-d H:i:s"));  
            $res2 = $callConfig->insertRecord('account_balances',$insertaryitmledger);
            $return['insert_account_balances_arey'] = $insertaryitmledger; 
            $return['insert_account_balances'] = $res2;  
        } 
        
        $sql = "UPDATE account_balances SET closing_balance = closing_balance + ".$amountChange.",opening_balance = opening_balance + ".$amountChange." WHERE company_id ='".$company_id."' AND finance_year = '".$financeyear."' AND account_code = '".$accountId."' AND balance_date > '".$entryDate."' AND is_active = 0";
        $updateaccres = $callConfig->executeQuery($sql);
        $return['update_all_record'] = $sql;
//account_balances End

//account_balances_details start
        $isDebit = in_array($accountType, array('1', '4'));
        $amountChange = $isDebit ? $debitAmount - $creditAmount : $creditAmount - $debitAmount;
        if($location_id !="" AND $department_id !="" AND $cost_center_id !=""){
            $sql = "SELECT * FROM account_balances_details WHERE company_id ='".$company_id."' AND account_code = '".$accountId."' AND finance_year = '".$financeyear."' AND balance_date = '".$entryDate."' AND location_id = '".$location_id."'  AND department_id = '".$department_id."' AND cost_center_id = '".$cost_center_id."' AND is_active = 0 ORDER BY balance_date DESC LIMIT 1";
            $existingEntry1 = $callConfig->getRow($sql);
            $return['existingEntrysql_details'] = $sql;
            $return['existingEntry_details'] = $existingEntry1;
        }
        if($existingEntry1->id !=""){
            // Calculate the difference between the new amount and the old amount
            $oldDebitAmount = $existingEntry1->debit_amount;
            $oldCreditAmount = $existingEntry1->credit_amount;
            
            $updateddebitamt = $oldDebitAmount +$debitAmount;
            $updatedcreditamt = $oldCreditAmount +$creditAmount; 
            
            $oldAmountChange = $isDebit ? $oldDebitAmount - $oldCreditAmount : $oldCreditAmount - $oldDebitAmount;
     
            //$amountDifference = $amountChange - $oldAmountChange;
     
            // Update the balance for the existing entry
            $updatedBalance = $existingEntry1->closing_balance + $amountChange;

            $sql = "UPDATE account_balances_details SET closing_balance = '".$updatedBalance."', debit_amount = '".$updateddebitamt."',credit_amount = '".$updatedcreditamt."' 
                    WHERE company_id ='".$company_id."' AND id = '".$existingEntry1->id."'";

            $updateaccres = $callConfig->executeQuery($sql);
            $return['account_balances_details_balances_details'] = $sql;

            // Update amountChange to reflect the difference for future updates
            $amountChange = $updatedBalance;
        }else{

            $sql = "SELECT closing_balance, balance_date FROM account_balances_details WHERE company_id ='".$company_id."' AND finance_year = '".$financeyear."' AND account_code = '".$accountId."' AND balance_date < '".$entryDate."' AND location_id = '".$location_id."'  AND department_id = '".$department_id."' AND cost_center_id = '".$cost_center_id."' AND is_active = 0 ORDER BY balance_date DESC LIMIT 1";
            $latestBalance = $callConfig->getRow($sql);
            $return['latestBalancesql_details'] = $sql;
            $return['latestBalance_details'] = $latestBalance;
            $return['select_lastbal_account_balances_sql_details'] = $sql; 
            $return['select_lastbal_account_balances_arey_details'] = $latestBalance; 
            $newBalancedetail = 0;
            $newBalancedetail = ($latestBalance->closing_balance!=""?$latestBalance->closing_balance:0) + $amountChange;

            $insertaryitmledger=array('company_id'=>$company_id,'finance_year'=>$financeyear,'account_code'=>$accountId,'balance_date'=>$this->dateconvertdbformat($entryDate),'debit_amount'=>$debitAmount,'credit_amount'=>$creditAmount,'opening_balance'=>$latestBalance->closing_balance,'closing_balance'=>$newBalancedetail,'location_id'=>$location_id,'department_id'=>$department_id,'cost_center_id'=>$cost_center_id,'is_active'=>0,'current_status'=>0,'created_by'=>'','created_date'=>date("Y-m-d H:i:s"));  
            $res2 = $callConfig->insertRecord('account_balances_details',$insertaryitmledger);
            $return['insert_account_balances_details_arey_details'] = $insertaryitmledger; 
            $return['insert_account_balances_details_details'] = $res2;  
        } 

        $sql = "UPDATE account_balances_details SET closing_balance = closing_balance + ".$amountChange." ,opening_balance = opening_balance + ".$amountChange." WHERE company_id ='".$company_id."' AND finance_year = '".$financeyear."' AND account_code = '".$accountId."' AND balance_date > '".$entryDate."' AND location_id = '".$location_id."'  AND department_id = '".$department_id."' AND cost_center_id = '".$cost_center_id."' AND is_active = 0";
        $updateaccres = $callConfig->executeQuery($sql);
        $return['update_all_record_details'] = $sql;
//account_balances_details End

        return $return;    
    }
    function getBankVouchers($get){
        global $callConfig;
        $return= $partylist = array();
        if(isset($get['rid']) AND $get['rid'] !=""){
            $journal_voucherssql = "SELECT * FROM `bank_vouchers` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $journal_vouchersres = $callConfig->getRow($journal_voucherssql);
            $return['journal_vouchers']= $journal_vouchersres;

            $jv_itemssql = "SELECT * FROM `bv_items` WHERE company_id = ".$get['company_id']." AND transaction_id = '".$get['rid']."' AND is_active =0";
            $jv_itemsres = $callConfig->getAllRows($jv_itemssql);
            $return['jv_items']= $jv_itemsres;
        }
        $locationsql = "SELECT id,location_name FROM `locations` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $locationres = $callConfig->getAllRows($locationsql);
        $return['locations']= $locationres;   

        $cost_centerssql = "SELECT id,cost_center_name FROM `cost_centers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $cost_centersres = $callConfig->getAllRows($cost_centerssql);
        $return['cost_centers']= $cost_centersres;

        $departmentssql = "SELECT id,department_name FROM `departments` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $departmentsres = $callConfig->getAllRows($departmentssql);
        $return['departments']= $departmentsres;

        $currenciessql = "SELECT id,currency_name, currency_short_code, currency_symbol FROM `currencies` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $currenciesres = $callConfig->getAllRows($currenciessql);
        $return['currencies']= $currenciesres;

        $journalssql = "SELECT id,account_code,bank_account_name FROM `bank_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $journalsres = $callConfig->getAllRows($journalssql);
        $return['bank_accounts']= $journalsres;

        $chartofaccountssql = "SELECT id,account_name,account_code FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND allow_posting = 0 AND is_active =0 ORDER BY account_name ASC";
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);

        $return['chart_of_accounts']= $chartofaccountsres;

        $vendorssql = "SELECT id,display_name FROM `vendors` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $vendorsres = $callConfig->getAllRows($vendorssql);
        foreach($vendorsres as $prty){
            $partylist['vendors'][$prty->id]= $prty;
        }

        $customerssql = "SELECT id,display_name FROM `customers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        foreach($customersres as $prty){
            $partylist['customers'][$prty->id]= $prty;
        }

        $employeessql = "SELECT id,display_name FROM `employees` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $employeesres = $callConfig->getAllRows($employeessql);
        foreach($employeesres as $prty){
            $partylist['employees'][$prty->id]= $prty;
        }
        $return['partylist'] = $partylist;

        $vouchartypesql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'VOUCHERTYPE' AND is_active =0";
        $vouchartyperes = $callConfig->getAllRows($vouchartypesql);
        $vouchar_type = array();
        foreach($vouchartyperes as $tt){
            $vouchar_type[$tt->genc_detail_code] = $tt->genc_detail_name;
        }
        $return['vouchar_type']= $vouchar_type;
        return $return;
    }
    function insertBankVouchers($get,$post){
         global $callConfig;
        $return =array();
        $return['post'] = $post; 

        $partysql = "SELECT au.*, (SELECT department_id FROM cost_centers cc WHERE au.company_id = cc.company_id AND au.cost_center = cc.id AND au.is_active = cc.is_active) as department_id FROM application_users au WHERE au.company_id = '".$post['company_id']."' AND au.id = '".$post['user_id']."' AND au.is_active = 0";  
        $user = $callConfig->getRow($partysql);

        $insertary=array('company_id'=>$post['company_id'],'transaction_date'=>$this->dateconvertdbformat($post['transaction_date']),'currency_id'=>$post['currency_id'],'printing_notes'=>$post['printing_notes'],'notes'=>$post['notes'],'amount'=>$post['total_amount'],'total_amount'=>$post['total_amount'],'bank_account_code'=>$post['bank_account_code'],'doc_ref_no'=>$post['doc_ref_no'],'doc_ref_date'=>$this->dateconvertdbformat($post['doc_ref_date']),'voucher_type'=>$post['voucher_type']);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('bank_vouchers',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
            //all transaction update
            $callConfig->executeQuery("DELETE FROM general_ledger WHERE transaction_id = '".$rid."'");
            $callConfig->updateRecord('bv_items',array('is_active'=>'1'),'transaction_id',$rid);
            $callConfig->executeQuery("DELETE FROM general_ledger WHERE voucher_id = '".$rid."' AND ledger_type = 'BV'");
            //all transaction update
        }else{
            $transation = $this->toGetApplicationSerialNumber('bank_vouchers');
            $transationyear = $this->toGetApplicationVoucherSerialNumbers($post,'bank_vouchers',$post['transaction_date'],'Y',$post['voucher_type']);
            $transationmonth = $this->toGetApplicationVoucherSerialNumbers($post,'bank_vouchers',$post['transaction_date'],'M',$post['voucher_type']);
            $insertary['transaction_number_yearly']=$transationyear;
            $insertary['transaction_number_monthly']=$transationmonth;
            $insertary['transaction_number']=$transation;
            $post['transaction_number']=$transation;
            $insertary['originator']=$post['user_id'];
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('bank_vouchers',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        } 
//reverse balance update code      
 
        foreach($post['old_jv_item_id'] as $key=>$val){
            $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['old_account_code'][$key],'date'=>$this->dateconvertdbformat($post['old_transaction_date']),'debit_amount'=>$post['old_debit_amount'][$key],'credit_amount'=>$post['old_credit_amount'][$key],'account_type'=>$post['old_account_type'][$key],'location_id'=>$post['old_transaction_location_id'][$key],'department_id'=>$post['old_department_id'][$key],'cost_center_id'=>$post['old_cost_center_id'][$key]);
            $updatebalances = $this->insertReverseAccountBalances($balanceary);
            $return['reversaerenty'][] = $updatebalances;
        }
        if($post['old_bank_account_code'] !=""){ 
            $oldchartofaccountssql = "SELECT id,account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$post['company_id']." AND account_code = '".$post['old_bank_account_code']."' AND is_active =0";
            $oldchartofaccountsres = $callConfig->getRow($oldchartofaccountssql);

            $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['old_bank_account_code'],'date'=>$this->dateconvertdbformat($post['old_transaction_date']),'account_type'=>$oldchartofaccountsres->account_type,'location_id'=>$user->location_id,'department_id'=>$user->department_id,'cost_center_id'=>$user->cost_center);
            if($post['old_voucher_type'] == "RV"){
                $balanceary['debit_amount'] = $post['old_total_amount'];
                $balanceary['credit_amount'] = 0;
            }else{
                $balanceary['debit_amount'] = 0;
                $balanceary['credit_amount'] = $post['old_total_amount'];
            } 
            $updatebalances = $this->insertReverseAccountBalances($balanceary);
            $return['reversaerenty'][] = $updatebalances;   
        }    
//reverse balance update code


//New parent level entry
        $parentacccountsql = "SELECT id,account_code,bank_account_name FROM `bank_accounts` WHERE company_id = ".$post['company_id']." AND account_code = '".$post['bank_account_code']."' AND is_active =0";
        $parentacccountres = $callConfig->getRow($parentacccountsql);

        $newchartofaccountssql = "SELECT id,account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$post['company_id']." AND account_code = '".$post['bank_account_code']."' AND is_active =0";
        $newchartofaccountsres = $callConfig->getRow($newchartofaccountssql);
        $return['newchartofaccountssql'][] = $newchartofaccountssql;
        $return['newchartofaccountsres'][] = $newchartofaccountsres;
        $insertaryitmledger=array('company_id'=>$post['company_id'],'voucher_id'=>$rid,'voucher_ref_no'=>$post['transaction_number'],'voucher_date'=>$this->dateconvertdbformat($post['transaction_date']),'account_code'=>$post['bank_account_code'],'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'ledger_type'=>'BV','account_name'=>$parentacccountres->bank_account_name,'remarks'=>$newchartofaccountsres->account_name,'account_type'=>$newchartofaccountsres->account_type); 
        if($post['voucher_type'] == "RV"){
            $insertaryitmledger['debit_amount'] = $post['total_amount'];
            $insertaryitmledger['credit_amount'] = 0;
        }else{
            $insertaryitmledger['debit_amount'] = 0;
            $insertaryitmledger['credit_amount'] = $post['total_amount'];
        } 
        $res2 = $callConfig->insertRecord('general_ledger',$insertaryitmledger);
        $return['res_item_ledger'][] = $res2;

///New balance update
    $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['bank_account_code'],'date'=>$this->dateconvertdbformat($post['transaction_date']),'account_type'=>$newchartofaccountsres->account_type,'location_id'=>$user->location_id,'department_id'=>$user->department_id,'cost_center_id'=>$user->cost_center);
    if($post['voucher_type'] == "RV"){
        $balanceary['debit_amount'] = $post['total_amount'];
        $balanceary['credit_amount'] = 0;
    }else{
        $balanceary['debit_amount'] = 0;
        $balanceary['credit_amount'] = $post['total_amount'];
    } 
    $return['balanceenty_ary'][] = $balanceary;
    $updatebalances = $this->insertAccountBalances($balanceary);
    $return['balanceenty'][] = $updatebalances; 
///New balance update



        //child level entry
        foreach($post['chart_of_account_id'] as $key=>$val){
            if($post['party_id'][$key] !=""){
                $explod = explode('||', $post['party_id'][$key]);
                $party_transaction_table = $explod[1];
                $party_id = $explod[0];
            }
            $insertaryitm=array('company_id'=>$post['company_id'],'transaction_id'=>$rid,'account_code'=>$post['account_code'][$key],'particulars'=>$post['particulars'][$key],'party_transaction_table'=>$party_transaction_table,'party_id'=>$party_id,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'account_name'=>$post['account_name'][$key],'account_type'=>$post['account_type'][$key],'transaction_location_id'=>$post['transaction_location_id'][$key],'department_id'=>$post['department_id'][$key],'cost_center_id'=>$post['cost_center_id'][$key]); 

            $insertaryitmledger=array('company_id'=>$post['company_id'],'voucher_id'=>$rid,'voucher_ref_no'=>$post['transaction_number'],'voucher_date'=>$this->dateconvertdbformat($post['transaction_date']),'account_code'=>$post['account_code'][$key],'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'ledger_type'=>'BV','account_name'=>$post['account_name'][$key],'remarks'=>$post['particulars'][$key],'account_type'=>$post['account_type'][$key]); 

            if($post['voucher_type'] == "RV"){
                $insertaryitm['credit_amount'] = $post['amount'][$key];
                $insertaryitm['debit_amount'] = 0;
                $insertaryitmledger['credit_amount'] = $post['amount'][$key];
            }else{
                $insertaryitm['debit_amount'] = $post['amount'][$key];
                $insertaryitm['credit_amount'] = 0;
                $insertaryitmledger['debit_amount'] = $post['amount'][$key];
            } 
            if($post['jv_item_id'][$key] !=""){ 
                $insertaryitm['updated_date']=date('Y-m-d h:i:s');
                $insertaryitm['updated_by']=$post['user_id'];
                $res1= $callConfig->updateRecord('bv_items',$insertaryitm,'id',$post['jv_item_id'][$key]);
                $return['res_item'] = $res1;
            }else{
                $insertaryitm['created_by']=$post['user_id'];
                $insertaryitm['created_date']=date("Y-m-d H:i:s");
                $res1 = $callConfig->insertRecord('bv_items',$insertaryitm);
                $return['res_item'] = $res1;
            }           
            $res2 = $callConfig->insertRecord('general_ledger',$insertaryitmledger);
            $return['res_item_ledger'][] = $res2;


///New balance update
        $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['account_code'][$key],'date'=>$this->dateconvertdbformat($post['transaction_date']),'account_type'=>$post['account_type'][$key],'location_id'=>$post['transaction_location_id'][$key],'department_id'=>$post['department_id'][$key],'cost_center_id'=>$post['cost_center_id'][$key]);
        if($post['voucher_type'] == "RV"){
            $balanceary['credit_amount'] = $post['amount'][$key];
            $balanceary['debit_amount'] = 0;
        }else{
            $balanceary['credit_amount'] = 0;
            $balanceary['debit_amount'] = $post['amount'][$key];
        } 
        $return['balanceenty_ary'][] = $balanceary;
        $updatebalances = $this->insertAccountBalances($balanceary);
        $return['balanceenty'][] = $updatebalances;
                
///New balance update

        }  
        //child level entry
        return $return;
    }
    function getCashVouchers($get){
        global $callConfig;
        $return= $partylist = array();
        if(isset($get['rid']) AND $get['rid'] !=""){
            $journal_voucherssql = "SELECT * FROM `cash_vouchers` WHERE company_id = ".$get['company_id']." AND id = '".$get['rid']."' AND is_active =0";
            $journal_vouchersres = $callConfig->getRow($journal_voucherssql);
            $return['journal_vouchers']= $journal_vouchersres;

            $jv_itemssql = "SELECT * FROM `cv_items` WHERE company_id = ".$get['company_id']." AND transaction_id = '".$get['rid']."' AND is_active =0";
            $jv_itemsres = $callConfig->getAllRows($jv_itemssql);
            $return['jv_items']= $jv_itemsres;
        }
         $locationsql = "SELECT id,location_name FROM `locations` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $locationres = $callConfig->getAllRows($locationsql);
        $return['locations']= $locationres;   

        $cost_centerssql = "SELECT id,cost_center_name FROM `cost_centers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $cost_centersres = $callConfig->getAllRows($cost_centerssql);
        $return['cost_centers']= $cost_centersres;

        $departmentssql = "SELECT id,department_name FROM `departments` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $departmentsres = $callConfig->getAllRows($departmentssql);
        $return['departments']= $departmentsres;

        $currenciessql = "SELECT id,currency_name, currency_short_code, currency_symbol FROM `currencies` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $currenciesres = $callConfig->getAllRows($currenciessql);
        $return['currencies']= $currenciesres;

        $journalssql = "SELECT id,gl_account_pc_cash,pc_box_description FROM `petty_cash_boxes` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $journalsres = $callConfig->getAllRows($journalssql);
        $return['petty_cash_boxes']= $journalsres;

        $chartofaccountssql = "SELECT id,account_name,account_code FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND allow_posting = 0 AND is_active =0 ORDER BY account_name ASC";
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);

        $return['chart_of_accounts']= $chartofaccountsres;

        $vendorssql = "SELECT id,display_name FROM `vendors` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $vendorsres = $callConfig->getAllRows($vendorssql);
        foreach($vendorsres as $prty){
            $partylist['vendors'][$prty->id]= $prty;
        }

        $customerssql = "SELECT id,display_name FROM `customers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        foreach($customersres as $prty){
            $partylist['customers'][$prty->id]= $prty;
        }

        $employeessql = "SELECT id,display_name FROM `employees` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $employeesres = $callConfig->getAllRows($employeessql);
        foreach($employeesres as $prty){
            $partylist['employees'][$prty->id]= $prty;
        }
        $return['partylist'] = $partylist;

        $vouchartypesql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'VOUCHERTYPE' AND is_active =0";
        $vouchartyperes = $callConfig->getAllRows($vouchartypesql);
        $vouchar_type = array();
        foreach($vouchartyperes as $tt){
            $vouchar_type[$tt->genc_detail_code] = $tt->genc_detail_name;
        }
        $return['vouchar_type']= $vouchar_type;
        return $return;
    }
     function insertCashVouchers($get,$post){
        global $callConfig;
        $return =array();
        $return['post'] = $post; 
        $partysql = "SELECT au.*, (SELECT department_id FROM cost_centers cc WHERE au.company_id = cc.company_id AND au.cost_center = cc.id AND au.is_active = cc.is_active) as department_id FROM application_users au WHERE au.company_id = '".$post['company_id']."' AND au.id = '".$post['user_id']."' AND au.is_active = 0";  
        $user = $callConfig->getRow($partysql);


        $insertary=array('company_id'=>$post['company_id'],'transaction_date'=>$this->dateconvertdbformat($post['transaction_date']),'currency_id'=>$post['currency_id'],'printing_notes'=>$post['printing_notes'],'notes'=>$post['notes'],'amount'=>$post['total_amount'],'total_amount'=>$post['total_amount'],'cash_account_code'=>$post['cash_account_code'],'doc_ref_no'=>$post['doc_ref_no'],'doc_ref_date'=>$this->dateconvertdbformat($post['doc_ref_date']),'voucher_type'=>$post['voucher_type']);
        if($post['rid'] !=""){
            $insertary['updated_date']=date('Y-m-d h:i:s');
            $insertary['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord('cash_vouchers',$insertary,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
            //all transaction update
            $callConfig->executeQuery("DELETE FROM general_ledger WHERE transaction_id = '".$rid."'");
            $callConfig->updateRecord('cv_items',array('is_active'=>'1'),'transaction_id',$rid);
            $callConfig->executeQuery("DELETE FROM general_ledger WHERE voucher_id = '".$rid."' AND ledger_type = 'CV'");
            //all transaction update
        }else{
            $transation = $this->toGetApplicationSerialNumber('cash_vouchers');
            $transationyear = $this->toGetApplicationVoucherSerialNumbers($post,'cash_vouchers',$post['transaction_date'],'Y',$post['voucher_type']);
            $transationmonth = $this->toGetApplicationVoucherSerialNumbers($post,'cash_vouchers',$post['transaction_date'],'M',$post['voucher_type']);
            $insertary['transaction_number_yearly']=$transationyear;
            $insertary['transaction_number_monthly']=$transationmonth;
            $insertary['transaction_number']=$transation;
            $post['transaction_number']=$transation;
            $insertary['originator']=$post['user_id'];
            $insertary['is_active']=0;
            $insertary['current_status']=0;;
            $insertary['created_by']=$post['user_id'];
            $insertary['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord('cash_vouchers',$insertary);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        } 

//reverse balance update code      
 
        foreach($post['old_jv_item_id'] as $key=>$val){
            $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['old_account_code'][$key],'date'=>$this->dateconvertdbformat($post['old_transaction_date']),'debit_amount'=>$post['old_debit_amount'][$key],'credit_amount'=>$post['old_credit_amount'][$key],'account_type'=>$post['old_account_type'][$key],'location_id'=>$post['old_transaction_location_id'][$key],'department_id'=>$post['old_department_id'][$key],'cost_center_id'=>$post['old_cost_center_id'][$key]);
            $updatebalances = $this->insertReverseAccountBalances($balanceary);
            $return['reversaerenty'][] = $updatebalances;
        }
        if($post['old_cash_account_code'] !=""){
            $oldchartofaccountssql = "SELECT id,account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND account_code = '".$post['old_cash_account_code']."' AND is_active =0";
            $oldchartofaccountsres = $callConfig->getRow($oldchartofaccountssql);

            $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['old_cash_account_code'],'date'=>$this->dateconvertdbformat($post['old_transaction_date']),'account_type'=>$oldchartofaccountsres->account_type,'location_id'=>$user->location_id,'department_id'=>$user->department_id,'cost_center_id'=>$user->cost_center);
            if($post['old_voucher_type'] == "RV"){
                $balanceary['debit_amount'] = $post['old_total_amount'];
                $balanceary['credit_amount'] = 0;
            }else{
                $balanceary['debit_amount'] = 0;
                $balanceary['credit_amount'] = $post['old_total_amount'];
            } 
            $updatebalances = $this->insertReverseAccountBalances($balanceary);
            $return['reversaerenty'][] = $updatebalances;   
        }    
//reverse balance update code


        //parent level entry
        $parentacccountsql = "SELECT id,gl_account_pc_cash,pc_box_description FROM `petty_cash_boxes` WHERE company_id = ".$post['company_id']." AND gl_account_pc_cash = '".$post['cash_account_code']."' AND is_active =0";
        $parentacccountres = $callConfig->getRow($parentacccountsql);

        $newchartofaccountssql = "SELECT id,account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$post['company_id']." AND account_code = '".$post['cash_account_code']."' AND is_active =0";
        $newchartofaccountsres = $callConfig->getRow($newchartofaccountssql);

        $insertaryitmledger=array('company_id'=>$post['company_id'],'voucher_id'=>$rid,'voucher_ref_no'=>$post['transaction_number'],'voucher_date'=>$this->dateconvertdbformat($post['transaction_date']),'account_code'=>$post['cash_account_code'],'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'ledger_type'=>'CV','account_name'=>$parentacccountres->pc_box_description,'remarks'=>$newchartofaccountsres->account_name,'account_type'=>$newchartofaccountsres->account_type); 
         if($post['voucher_type'] == "RV"){
            $insertaryitmledger['debit_amount'] = $post['total_amount'];
             $insertaryitmledger['credit_amount'] = 0;
        }else{
            $insertaryitmledger['debit_amount'] = 0;
            $insertaryitmledger['credit_amount'] = $post['total_amount'];
        } 
        $res2 = $callConfig->insertRecord('general_ledger',$insertaryitmledger);
        $return['res_item_ledger'][] = $res2;

///New balance update
    $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['cash_account_code'],'date'=>$this->dateconvertdbformat($post['transaction_date']),'account_type'=>$newchartofaccountsres->account_type,'location_id'=>$user->location_id,'department_id'=>$user->department_id,'cost_center_id'=>$user->cost_center);
    if($post['voucher_type'] == "RV"){
        $balanceary['debit_amount'] = $post['total_amount'];
        $balanceary['credit_amount'] = 0;
    }else{
        $balanceary['debit_amount'] = 0;
        $balanceary['credit_amount'] = $post['total_amount'];
    } 
    $return['balanceenty_ary'][] = $balanceary;
    $updatebalances = $this->insertAccountBalances($balanceary);
    $return['balanceenty'][] = $updatebalances; 
///New balance update



        //parent level entry
        //child level entry
        foreach($post['chart_of_account_id'] as $key=>$val){
            if($post['party_id'][$key] !=""){
                $explod = explode('||', $post['party_id'][$key]);
                $party_transaction_table = $explod[1];
                $party_id = $explod[0];
            }
            $insertaryitm=array('company_id'=>$post['company_id'],'transaction_id'=>$rid,'account_code'=>$post['account_code'][$key],'particulars'=>$post['particulars'][$key],'party_transaction_table'=>$party_transaction_table,'party_id'=>$party_id,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'account_name'=>$post['account_name'][$key],'account_type'=>$post['account_type'][$key],'transaction_location_id'=>$post['transaction_location_id'][$key],'department_id'=>$post['department_id'][$key],'cost_center_id'=>$post['cost_center_id'][$key]); 

            $insertaryitmledger=array('company_id'=>$post['company_id'],'voucher_id'=>$rid,'voucher_ref_no'=>$post['transaction_number'],'voucher_date'=>$this->dateconvertdbformat($post['transaction_date']),'account_code'=>$post['account_code'][$key],'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"),'ledger_type'=>'CV','account_name'=>$post['account_name'][$key],'remarks'=>$post['particulars'][$key],'account_type'=>$post['account_type'][$key]); 

            if($post['voucher_type'] == "RV"){
                $insertaryitm['credit_amount'] = $post['amount'][$key];
                $insertaryitm['debit_amount'] = 0;
                $insertaryitmledger['credit_amount'] = $post['amount'][$key];
            }else{
                $insertaryitm['debit_amount'] = $post['amount'][$key];
                $insertaryitm['credit_amount'] = 0;
                $insertaryitmledger['debit_amount'] = $post['amount'][$key];
            } 
            if($post['jv_item_id'][$key] !=""){ 
                $insertaryitm['updated_date']=date('Y-m-d h:i:s');
                $insertaryitm['updated_by']=$post['user_id'];
                $res1= $callConfig->updateRecord('cv_items',$insertaryitm,'id',$post['jv_item_id'][$key]);
                $return['res_item'] = $res1;
            }else{
                $insertaryitm['created_by']=$post['user_id'];
                $insertaryitm['created_date']=date("Y-m-d H:i:s");
                $res1 = $callConfig->insertRecord('cv_items',$insertaryitm);
                $return['res_item'] = $res1;
            }           
            $res2 = $callConfig->insertRecord('general_ledger',$insertaryitmledger);
            $return['res_item_ledger'][] = $res2;

        ///New balance update
            $balanceary = array('company_id'=>$post['company_id'],'account_code'=>$post['account_code'][$key],'date'=>$this->dateconvertdbformat($post['transaction_date']),'account_type'=>$post['account_type'][$key],'location_id'=>$post['transaction_location_id'][$key],'department_id'=>$post['department_id'][$key],'cost_center_id'=>$post['cost_center_id'][$key]);
            if($post['voucher_type'] == "RV"){
                $balanceary['credit_amount'] = $post['amount'][$key];
                $balanceary['debit_amount'] = 0;
            }else{
                $balanceary['credit_amount'] = 0;
                $balanceary['debit_amount'] = $post['amount'][$key];
            } 
            $return['balanceenty_ary'][] = $balanceary;
            $updatebalances = $this->insertAccountBalances($balanceary);
            $return['balanceenty'][] = $updatebalances;            
        ///New balance update

        }  
        //child level entry

        return $return;
    }
    function getGeneralLedger($get){
        global $callConfig;
        $getFinanceialYear = $this->getFinanceialYear($get['from_date']);
        $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];
        $journalssql = "SELECT id,gl_account_pc_cash,pc_box_description FROM `petty_cash_boxes` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $journalsres = $callConfig->getAllRows($journalssql);
        $return['petty_cash_boxes']= $journalsres;

        $chartofaccountssql = "SELECT id,account_name,account_code,parent_account_code FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND allow_posting = 0 AND is_active =0 ORDER BY account_name ASC";
        $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);

        $return['chart_of_accounts']= $chartofaccountsres;
        $gl_condition = "";
       // if($get['account_code'] !=""){
            //$gl_condition = " AND voucher_id IN(SELECT voucher_id FROM `general_ledger` WHERE company_id = ".$get['company_id']." AND is_active =0 AND account_code = '".$get['account_code']."')";
       // }
        // if($get['first_account_code'] !=""){
        //      $gl_condition = " AND voucher_id IN(SELECT voucher_id FROM `general_ledger` WHERE company_id = ".$get['company_id']." AND is_active =0 AND account_code = '".$get['first_account_code']."')";
        // }
        // if($post['second_account_code'] !=""){
        //      $gl_condition = " AND voucher_id IN(SELECT voucher_id FROM `general_ledger` WHERE company_id = ".$get['company_id']." AND is_active =0 AND account_code = '".$get['second_account_code']."')";
        // }
        // if($get['third_account_code'] !=""){
        //      $gl_condition = " AND voucher_id IN(SELECT voucher_id FROM `general_ledger` WHERE company_id = ".$get['company_id']." AND is_active =0 AND account_code = '".$get['third_account_code']."')";
        // }
        // if($get['fourth_account_code'] !=""){
        //     $gl_condition = " AND voucher_id IN(SELECT voucher_id FROM `general_ledger` WHERE company_id = ".$get['company_id']." AND is_active =0 AND account_code = '".$get['fourth_account_code']."')";
        // }
        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $gl_condition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= voucher_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= voucher_date";
        }
        if($get['GLT'] !=""){
            $gl_condition .= " AND ledger_type = '".$get['GLT']."'";
        }
        $general_ledgersql = "SELECT * FROM `general_ledger` WHERE company_id = ".$get['company_id']." AND account_code = '".$get['account_code']."' AND is_active =0 ".$gl_condition." ORDER BY voucher_date ASC";
        $general_ledgerres = $callConfig->getAllRows($general_ledgersql);
        $return['general_ledgersql']= $general_ledgersql;
        $return['general_ledger']= $general_ledgerres;

        $lastbalancesql ="SELECT * FROM `account_balances` WHERE company_id = ".$get['company_id']." AND finance_year = '".$financeyear."' AND  account_code = '".$get['account_code']."' AND balance_date < '".$this->dateconvertdbformat($get['from_date'])."' AND is_active = 0 ORDER BY balance_date desc limit 1";
        $lastbalanceres = $callConfig->getRow($lastbalancesql);
        $return['last_account_balances_sql']= $lastbalancesql;
        $return['last_account_balances']= $lastbalanceres;



        $vouchartypesql = "SELECT genc_detail_code, genc_detail_name FROM `general_code_details` WHERE company_id = ".$get['company_id']." AND genc_code = 'VOUCHERTYPE' AND is_active =0";
        $vouchartyperes = $callConfig->getAllRows($vouchartypesql);
        $vouchar_type = array();
        foreach($vouchartyperes as $tt){
            $vouchar_type[$tt->genc_detail_code] = $tt->genc_detail_name;
        }
        $return['vouchar_type']= $vouchar_type;

        $bank_accountssql = "SELECT id,account_code,bank_account_name FROM `bank_accounts` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $bank_accountsres = $callConfig->getAllRows($bank_accountssql);
        $return['bank_accounts']= $bank_accountsres;
        
        $petty_cash_boxessql = "SELECT id,gl_account_pc_cash,pc_box_description FROM `petty_cash_boxes` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $petty_cash_boxesres = $callConfig->getAllRows($petty_cash_boxessql);
        $return['petty_cash_boxes']= $petty_cash_boxesres;

        return $return;
    }
    function toGetApplicationVoucherSerialNumbers($get,$table_name,$trans_date,$serial_number_type,$voucher_type=""){
        global $callConfig;
        $return = array();
        if($voucher_type !=""){
            $quercond = " AND voucher_type = '".$voucher_type."'";
        }
        $query = "SELECT * FROM `application_voucher_serial_numbers` WHERE company_id = ".$get['company_id']." AND application_table_name = '".$table_name."' AND serial_number_type = '".$serial_number_type."' AND start_date <= '".$this->dateconvertdbformat($trans_date)."' AND end_date >= '".$this->dateconvertdbformat($trans_date)."' AND is_active =0";  
        $return['query_1'] = $query;   
        $res = $callConfig->getRow($query);
        $return['query_1_res'] = $res;   
        if($res->id !=""){
            $le = $res->doc_no_length;
            $var = "%0".$le."d";
            $data = $res->pre_fix.sprintf($var,$res->serial_number+1).$res->post_fix;
            $update = $callConfig->updateRecord('application_voucher_serial_numbers',array('serial_number' =>$res->serial_number+1),'id',$res->id); 
            $return['query_1_update'] = $update; 
       }else{
            $query = "SELECT * FROM `application_voucher_serial_numbers` WHERE company_id = ".$get['company_id']." AND application_table_name = '".$table_name."' AND serial_number_type = '".$serial_number_type."' AND is_active =0";  
            $return['query_2_res'] = $query;   
            $res = $callConfig->getRow($query);
            $return['query_2_res'] = $res;  
            if(isset($res->doc_no_length)){
                $le = $res->doc_no_length;
                $var = "%0".$le."d";
                $data = $res->pre_fix.sprintf($var,1).$res->post_fix;

                if($serial_number_type == "M"){
                    $startdate = date("Y-m-01",strtotime($trans_date));
                    $enddate = date("Y-m-t",strtotime($trans_date));
                }else{
                    if(date("m",strtotime($trans_date)) >= 4){
                        $startyear = date("Y",strtotime($trans_date));
                        $endyear = (date("Y",strtotime($trans_date))+1);
                        $startdate = $startyear."-04-01";
                        $enddate = $endyear."-03-31";
                    }else{
                        $startyear = (date("Y",strtotime($trans_date)) - 1);
                        $endyear = date("Y",strtotime($trans_date));
                        $startdate = $startyear."-04-01";
                        $enddate = $endyear."-03-31";
                    }
                }
                if($res->post_fix !="" AND $res->pre_fix !="" AND $res->doc_no_length !="" AND $res->serial_number_type !="" AND  $res->voucher_type !=""){
                    $insertary=array('company_id'=>$get['company_id'],'application_table_name'=>$table_name,'serial_number'=>'1','pre_fix'=>$res->pre_fix,'post_fix'=>$res->post_fix,'doc_no_length'=>$res->doc_no_length,'serial_number_type'=>$res->serial_number_type,'voucher_type'=>$res->voucher_type,'start_date'=>$startdate,'end_date'=>$enddate,'is_active'=>0,'current_status'=>0,'created_by'=>$get['user_id'],'created_date'=>date("Y-m-d H:i:s")); 
                    $return['res_item_insertary'] = $insertary;
                    $res1 = $callConfig->insertRecord('application_voucher_serial_numbers',$insertary);
                    $return['res_item'] = $res1;  
                } 
            }else{
                $data = 1;
            }  
        }  
        //return $return;
        return trim($data);
    }

    function getAccountClosingBalance($get,$acount_code){
        global $callConfig;
        $closingbalance = 0;
        $query = "SELECT id, balance_amount FROM `general_ledger` WHERE company_id = ".$get['company_id']." AND account_code = '".$acount_code."' AND is_active =0 ORDER BY voucher_date DESC limit 1";    
        $res = $callConfig->getRow($query);
        if($res->id !=""){
            $closingbalance = $res->balance_amount;
        }else{
            $closingbalance = 0;
        }
        return trim($closingbalance);
    } 
    function getTrialBalancesReport($get){
        global $callConfig;
        $company_id = $get['company_id'];
        $balance_date = $get['balance_date'];
        $fy = $this->getFinanceialYear($get['balance_date']);
        $financial_year = $fy['fullstartyear'].$fy['fullendyear'];
        $query = "SELECT
  coa.account_code,
  coa.account_name,
  coa.parent_account_code,
  coa.company_id,
  coa.account_type,
  coa.allow_posting,
  coa.allow_manual_entries,
  SUM(CASE WHEN coa.account_type IN (1, 4) THEN COALESCE(closing_balance, 0) ELSE NULL END) AS debit_amount,
  SUM(CASE WHEN coa.account_type IN (2, 3) THEN COALESCE(closing_balance, 0) ELSE NULL END) AS credit_amount
FROM
  chart_of_accounts coa
LEFT JOIN (
  SELECT
            account_code,
            company_id,
            ab1.closing_balance,
            balance_date
  FROM account_balances ab1
  WHERE balance_date = (
                        SELECT  MAX(balance_date)
                        FROM    account_balances ab2
                        WHERE   ab1.company_id = ab2.company_id
                        AND     ab2.finance_year = '".$financial_year."'
                        AND     ab1.account_code = ab2.account_code
                        AND     ab2.balance_date <= '".$this->dateconvertdbformat($balance_date)."'
                      )
) b ON coa.account_code = b.account_code
  AND coa.company_id = b.company_id AND coa.company_id = '".$company_id."'
GROUP BY  coa.account_code, coa.account_name, coa.parent_account_code, coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries";    
        $res = $callConfig->getAllRows($query);
        $return['query'] =$query; 
        $return['balances'] =$res; 
        return $return;
    } 
    function getFinanceialYear($trans_date){
        $returnary = array();
        if(date("m",strtotime($trans_date)) >= 4){
            $shortstartyear = date("y",strtotime($trans_date));
            $shortendyear = (date("y",strtotime($trans_date))+1);
            $startyear = date("Y",strtotime($trans_date));
            $endyear = (date("Y",strtotime($trans_date))+1);
            $startdate = $startyear."-04-01";
            $enddate = $endyear."-03-31";
        }else{
            $shortstartyear = (date("Y",strtotime($trans_date)) - 1);
            $shortendyear = date("Y",strtotime($trans_date));
            $startyear = (date("Y",strtotime($trans_date)) - 1);
            $endyear = date("Y",strtotime($trans_date));
            $startdate = $startyear."-04-01";
            $enddate = $endyear."-03-31";
        }
        $returnary = array('startdate'=>$startdate,'enddate'=>$startdate,'fullstartyear'=>$startyear,'fullendyear'=>$endyear,'shortstartyear'=>$shortstartyear,'shortendyear'=>$shortendyear);
        return $returnary;
    } 
    function getPurchaseLedger($get){
        global $callConfig;
        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= transaction_date";
        }
        if($get['vendor_id'] !=""){
            $condition .= " AND party_id = '".$get['vendor_id']."'";
        }
        
        $vendorsquery = "SELECT id,display_name,company FROM `vendors` WHERE company_id = ".$get['company_id']." AND is_active =0";    
        $vendorsres = $callConfig->getAllRows($vendorsquery);
        $return['vendors'] =$vendorsres; 

        $purchase_invoicessql = "SELECT * FROM `purchase_invoices` WHERE company_id = ".$get['company_id']." AND is_active =0 ".$condition;    
        $purchase_invoicesres = $callConfig->getAllRows($purchase_invoicessql);
        $return['purchase_invoices_Sql'] =$purchase_invoicessql; 
        $return['purchase_invoices'] =$purchase_invoicesres; 
        return $return;
    }
     function getSalesLedger($get){
        global $callConfig;
        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= transaction_date";
        }
        if($get['customer_id'] !=""){
            $condition .= " AND party_id = '".$get['customer_id']."'";
        }
        
        $customerssql = "SELECT id,display_name,company FROM `customers` WHERE company_id = ".$get['company_id']." AND is_active =0";    
        $customersres = $callConfig->getAllRows($customerssql);
        $return['customers'] =$customersres; 

        $sales_invoicessql = "SELECT * FROM `sales_invoices` WHERE company_id = ".$get['company_id']." AND is_active =0 ".$condition;    
        $sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);
        $return['sales_invoices_Sql'] =$sales_invoicessql; 
        $return['sales_invoices'] =$sales_invoicesres; 
        return $return;
    }
    function getProfitAndLossReports($get){
        global $callConfig;
        $company_id = $get['company_id'];
        $start_date = $get['start_date'];
        $end_date = $get['end_date'];
        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT  MAX(balance_date) FROM account_balances ab2 WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($start_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance";    
        $res = $callConfig->getAllRows($query);
        $return['start_query'] =$query; 
        $return['start_balances'] =$res; 

        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT MAX(balance_date) FROM account_balances ab2  WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($end_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance";
        $res = $callConfig->getAllRows($query);
        $return['end_query'] =$query; 
        $return['end_balances'] =$res; 
        //previous year data
        $prev_start_date = $get['prev_start_date'];
        $prev_end_date = $get['prev_end_date'];
        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT  MAX(balance_date) FROM account_balances ab2 WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($prev_start_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance";    
        $res = $callConfig->getAllRows($query);
        $return['prev_start_query'] =$query; 
        $return['prev_start_balances'] =$res; 

        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT MAX(balance_date) FROM account_balances ab2  WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($prev_end_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance";
        $res = $callConfig->getAllRows($query);
        $return['prev_end_query'] =$query; 
        $return['prev_end_balances'] =$res; 
        //previous year data
        return $return;


    }
    function getBalanceSheetReport($get){
        global $callConfig;
        $company_id = $get['company_id'];
        $balance_date = $get['balance_date'];
        $query = "SELECT coa.account_code, coa.account_name,coa.parent_account_code,coa.company_id,coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT MAX(balance_date) FROM account_balances ab2 WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($balance_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code, coa.account_name,coa.parent_account_code,coa.company_id,coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance";    
        $res = $callConfig->getAllRows($query);
        $return['query'] =$query; 
        $return['balances'] =$res; 
        
        $prev_balance_date = $get['prev_balance_date'];
        $query = "SELECT coa.account_code, coa.account_name,coa.parent_account_code,coa.company_id,coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT MAX(balance_date) FROM account_balances ab2 WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($prev_balance_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code, coa.account_name,coa.parent_account_code,coa.company_id,coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance";    
        $res = $callConfig->getAllRows($query);
        $return['prev_query'] =$query; 
        $return['prev_balances'] =$res; 
        
        return $return;
    }
    function getcashflowreports($get){
        global $callConfig;
        $company_id = $get['company_id'];
        $start_date = $get['start_date'];
        $end_date = $get['end_date'];
        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT  MAX(balance_date) FROM account_balances ab2 WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($start_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance";    
        $res = $callConfig->getAllRows($query);
        $return['start_query'] =$query; 
        $return['start_balances'] =$res; 

        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT MAX(balance_date) FROM account_balances ab2  WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($end_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance";
        $res = $callConfig->getAllRows($query);
        $return['end_query'] =$query; 
        $return['end_balances'] =$res; 
        //previous year data
        $prev_start_date = $get['prev_start_date'];
        $prev_end_date = $get['prev_end_date'];
        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT  MAX(balance_date) FROM account_balances ab2 WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($prev_start_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY coa.account_code,coa.account_name,coa.parent_account_code,coa.company_id, coa.account_type, coa.allow_posting, coa.allow_manual_entries, ab1.balance_date, ab1.closing_balance";    
        $res = $callConfig->getAllRows($query);
        $return['prev_start_query'] =$query; 
        $return['prev_start_balances'] =$res; 

        $query = "SELECT coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance FROM chart_of_accounts coa LEFT JOIN account_balances ab1 ON coa.account_code = ab1.account_code AND coa.company_id = ab1.company_id AND coa.company_id = ".$get['company_id']." AND ab1.balance_date = (SELECT MAX(balance_date) FROM account_balances ab2  WHERE ab1.company_id = ab2.company_id AND ab1.account_code = ab2.account_code AND ab2.balance_date <= '".$this->dateconvertdbformat($prev_end_date)."') WHERE coa.company_id = ".$get['company_id']." AND coa.is_active = 0 GROUP BY  coa.account_code,coa.account_name,coa.parent_account_code, coa.company_id, coa.account_type,coa.allow_posting,coa.allow_manual_entries,ab1.balance_date,ab1.closing_balance";
        $res = $callConfig->getAllRows($query);
        $return['prev_end_query'] =$query; 
        $return['prev_end_balances'] =$res; 
        //previous year data
        return $return;
    }
    function getdebitandcreditnotes($get){
        global $callConfig;
        $return = array();
        $return['get']= $get;
        $note_type = $get['note_type'];
        $rid = $get['rid'];
        if($rid !=""){
            $notetypesql = "SELECT * FROM ".$note_type." WHERE company_id = '".$get['company_id']."' AND id = '".$rid."' AND is_active = 0";
            $notetyperes = $callConfig->getRow($notetypesql);
            $return['note_type_res']= $notetyperes;
        }
        if(isset($notetyperes->id) AND $notetyperes->id !=""){
            $transaction_table = $notetyperes->ref_type;
            $transaction_id = $notetyperes->ref_id;
        }else{
            $transaction_table = $get['transaction_table'];
            $transaction_id = $get['transaction_id'];
        }
    
        $parentdmtsql = "SELECT id,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$note_type."' AND item_table !='' AND is_active = 0";
        $parentdmtres=$callConfig->getRow($parentdmtsql);
        if($parentdmtres->item_table !=""){
            $notetypeitemsql = "SELECT * FROM ".$parentdmtres->item_table." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$rid."' AND is_active = 0";
            $notetypeitemres = $callConfig->getallRows($notetypeitemsql);
            $return['note_type_items_res']= $notetypeitemres;
        }
        $chart_of_accountssql = "SELECT id, account_code, account_name FROM `chart_of_accounts` WHERE company_id = ".$get['company_id']." AND allow_posting = 0 AND is_active =0 ORDER BY account_name ASC";
        $chart_of_accountsres = $callConfig->getAllRows($chart_of_accountssql);
        $return['chart_of_accounts']= $chart_of_accountsres;
 
       if($transaction_table !="" AND $transaction_id !=""){ 

            $transactionssql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND id ='".$transaction_id."' AND is_active =0";
            $transactionsres=$callConfig->getRow($transactionssql);
            $return['transactions_sql'] = $transactionssql;
            $return['transactions'] = $transactionsres;
        
            $alltransaction = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND current_status = '".ST_APPROVED."' AND is_active =0 ";
            $alltransactionres=$callConfig->getAllRows($alltransaction);
            $return['all_transactions'] = $alltransactionres;


            $credit_notessql = "SELECT * FROM credit_notes WHERE company_id = '".$get['company_id']."' AND ref_type = '".$transaction_table."' AND ref_id = '".$transaction_id."' AND is_active = 0";
            $credit_notesres = $callConfig->getAllRows($credit_notessql);
            $return['credit_notessql']= $credit_notessql;
            $return['credit_notes']= $credit_notesres;
            
            $debit_notessql = "SELECT * FROM debit_notes WHERE company_id = '".$get['company_id']."' AND ref_type = '".$transaction_table."' AND ref_id = '".$transaction_id."' AND is_active = 0";
            $debit_notesres = $callConfig->getAllRows($debit_notessql);
            $return['debit_notes']= $debit_notesres;
            $return['debit_notessql']= $debit_notessql;
        }elseif($get['list'] =="Y"){
            $credit_notessql = "SELECT * FROM credit_notes WHERE company_id = '".$get['company_id']."' AND is_active = 0";
            $credit_notesres = $callConfig->getAllRows($credit_notessql);
            $return['credit_notessql']= $credit_notessql;
            $return['credit_notes']= $credit_notesres;
            
            $debit_notessql = "SELECT * FROM debit_notes WHERE company_id = '".$get['company_id']."' AND is_active = 0";
            $debit_notesres = $callConfig->getAllRows($debit_notessql);
            $return['debit_notes']= $debit_notesres;
            $return['debit_notessql']= $debit_notessql;
         }
        return $return;
    }
    function insertdebitandcreditnotes($get,$post){
        global $callConfig;
        $return = array();
        $return['post'] = $post;
        $transaction_table = $post['ref_type'];
        $transaction_id = $post['ref_id'];
        $note_type = $post['note_type'];
        
        $transactionsql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND id = '".$transaction_id."' AND is_active = 0";
        $transactionres = $callConfig->getRow($transactionsql);

        $transaction_number = $this->toGetApplicationSerialNumber($note_type,$post);
        $transaction_date = $due_date = $this->dateconvertdbformat($post['transaction_date']);
        
        $amount = 0;
        foreach($post['account_code'] as $key=>$value){
            if($post['account_code'][$key] !="" AND $post['item_price'][$key] !=""){
                $amount += $post['item_price'][$key];
            }
        }
        //ST_APPROVED
        $insertcust = array('company_id'=>$get['company_id'],'transaction_location_id'=>$transactionres->transaction_location_id,'transaction_number'=>$transaction_number,'transaction_date'=>$transaction_date,'originator'=>$get['user_id'],'party_id'=>$transactionres->party_id,'party_name'=>$transactionres->party_name,'party_email'=>$transactionres->party_email,'party_phone'=>$transactionres->party_phone,'party_address'=>$transactionres->party_address,'amount'=>$amount,'tax_amount'=>'0','total_amount'=>$amount,'due_date'=>$due_date,'ref_type'=>$transaction_table,'ref_id'=>$transaction_id,'ref_date'=>$transactionres->transaction_date,'ref_no'=>$transactionres->transaction_number,'pending_amount'=>$amount);
         if($post['rid'] !=""){
            $insertcust['updated_date']=date('Y-m-d h:i:s');
            $insertcust['updated_by']=$post['user_id'];
            $res= $callConfig->updateRecord($note_type,$insertcust,'id',$post['rid']);
            $return['res'] = $res;
            $rid = $post['rid'];
        }else{
            $insertcust['is_active']=0;
            $insertcust['current_status']=0;
            $insertcust['created_by']=$post['user_id'];
            $insertcust['created_date']=date("Y-m-d H:i:s");
            $res = $callConfig->insertRecord($note_type,$insertcust);
            $return['res'] = $res;
            $rid = $res['LASTID'];
        }
        if($res['status'] =='sucess'){
            $chartofaccountssql = "SELECT id,account_name,account_code,account_type FROM `chart_of_accounts` WHERE company_id = ".$post['company_id']." AND is_active =0";
            $chartofaccountsres = $callConfig->getAllRows($chartofaccountssql);
            $chart_of_accounts = array();
            foreach($chartofaccountsres as $cca){
                $chart_of_accounts[$cca->account_code] = $cca;
            }

            $parentdmtsql = "SELECT id,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$note_type."' AND item_table !='' AND is_active = 0";
            $parentdmtres=$callConfig->getRow($parentdmtsql);

            $callConfig->updateRecord($parentdmtres->item_table,array('is_active'=>null),'transaction_id',$rid);
            foreach($post['account_code'] as $key=>$value){
                if($post['account_code'][$key] !="" AND $post['item_price'][$key] !=""){
                    $insertcust = array('company_id'=>$get['company_id'],'transaction_id'=>$rid,'item_id'=>'0','account_code'=>$post['account_code'][$key],'item_name'=>$chart_of_accounts[$post['account_code'][$key]]->account_name,'description'=>$post['description'][$key],'purchase_uom'=>'UN','item_quantity'=>'1','item_price'=>$post['item_price'][$key],'item_amount'=>$post['item_price'][$key],'tax_amount'=>'0','total_amount'=>$post['item_price'][$key],'approved_quantity'=>'1','transaction_qty'=>'1','pending_qty'=>'1','is_active'=>0,'current_status'=>0);
                    
                    if($post['old_id'][$key] !=""){
                        $insertcust['updated_date']=date('Y-m-d h:i:s');
                        $insertcust['updated_by']=$post['user_id'];
                        $res11= $callConfig->updateRecord($parentdmtres->item_table,$insertcust,'id',$post['old_id'][$key]);
                        $return['debit_note_items'][] = $insertcust;
                    }else{
                        $insertcust['created_by']=$post['user_id'];
                        $insertcust['created_date']=date("Y-m-d H:i:s");
                        $res11 = $callConfig->insertRecord($parentdmtres->item_table,$insertcust);
                        $return['debit_note_items'][] = $insertcust;
                    }
               }
            }
          // $return['jv'] = $this->tocreatejounalvoucherbaseallcases($note_type, $rid);
        }
        return $return;
    }
    function getdynamicmappingtableinvoices($get){
        global $callConfig;
        $return = array();
        $transaction_table = $get['transaction_table'];
        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND transaction_date >= '".$this->dateconvertdbformat($get['from_date'])."'  AND transaction_date <= '".$this->dateconvertdbformat($get['to_date'])."'";
        }
        $transactionssql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND is_active =0".$condition;
        $transactionsres=$callConfig->getAllRows($transactionssql);
        $return['transactions'] = $transactionsres;
        
        $credit_notessql = "SELECT ref_id, SUM(total_amount) as total_amount FROM `credit_notes` WHERE company_id = '".$get['company_id']."' AND ref_type = '".$transaction_table."' AND is_active = 0 GROUP BY ref_id";
        $credit_notesres = $callConfig->getAllRows($credit_notessql);
        $credit_notes_sum =  array();
        foreach($credit_notesres as $pwd){
            $credit_notes_sum[$pwd->ref_id]= $pwd->total_amount;
        }
        $return['credit_notes_sum']= $credit_notes_sum;
        $debit_notessql = "SELECT ref_id, SUM(total_amount) as total_amount FROM `debit_notes` WHERE company_id = '".$get['company_id']."' AND ref_type = '".$transaction_table."' AND is_active = 0 GROUP BY ref_id";
        $debit_notesres = $callConfig->getAllRows($debit_notessql);
        $debit_notes_sum =  array();
        foreach($debit_notesres as $pwd){
            $debit_notes_sum[$pwd->ref_id]= $pwd->total_amount;
        }
        $return['debit_notes_sum_sql']= $debit_notessql;
        $return['debit_notes_sum']= $debit_notes_sum;

        $salesmensql = "SELECT id,salesman_name FROM salesmen WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $salesmenres=$callConfig->getAllRows($salesmensql);
        $salesmen =  array();
        foreach($salesmenres as $smres){
            $salesmen[$smres->id]= $smres;
        }
        $return['salesmen'] = $salesmen;


        return $return;

    }
    function getCustomerLedger($get){
        global $callConfig;
        $transactionwithdates = array();
        $getFinanceialYear = $this->getFinanceialYear($get['from_date']);
        $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];
        $customerssql = "SELECT id,display_name,company, account_code FROM `customers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        $customers = array();
        foreach($customersres as $custres){
            $customers[$custres->id]  = $custres;
        }
        $return['customers'] =$customers; 

        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= transaction_date";
           $paycondition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= rec.transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= rec.transaction_date";
        }
        if($get['customer_id'] !=""){

            $lastbalancesql ="SELECT * FROM `account_balances` WHERE company_id = ".$get['company_id']." AND finance_year = '".$financeyear."' AND  account_code = '".$customers[$get['customer_id']]->account_code."' AND balance_date < '".$this->dateconvertdbformat($get['from_date'])."' AND is_active = 0 ORDER BY balance_date desc limit 1";
            $lastbalanceres = $callConfig->getRow($lastbalancesql);
            $return['last_account_balances_sql']= $lastbalancesql;
            $return['last_account_balances']= $lastbalanceres;

            
            $sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id, ref_type, ref_no, amount, total_amount as p_total_amount FROM `sales_invoices` WHERE company_id = ".$get['company_id']." AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);
            $return['sales_invoices_sql'] =$sales_invoicessql; 
            foreach($sales_invoicesres as $si){
                 $si->transaction_table ='sales_invoices';
                $transactionwithdates[date('Ymd',strtotime($si->transaction_date))][] =$si; 
            }

            $debit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount FROM `debit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'sales_invoices' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $debit_notesres = $callConfig->getAllRows($debit_notessql);
            $return['debit_notessql'] =$debit_notessql; 
            foreach($debit_notesres as $dn){
                $dn->transaction_table ='debit_notes';
                $transactionwithdates[date('Ymd',strtotime($dn->transaction_date))][] =$dn; 
            }

            $credit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount FROM `credit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'sales_invoices' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $credit_notesres = $callConfig->getAllRows($credit_notessql);
            $return['credit_notessql'] =$credit_notessql; 
            foreach($credit_notesres as $cn){
                $cn->transaction_table ='credit_notes';
                $transactionwithdates[date('Ymd',strtotime($cn->transaction_date))][] =$cn; 
            }

            $receiptssql = "SELECT rec.id, rec.transaction_no as transaction_number, rec.payment_date as transaction_date, rec_aloc.total_amount, rec_aloc.pending_amount, rec_aloc.paid_amonut, rec_aloc.tds_amount, rec_aloc.total_paid_amonut as p_total_amount, rec_aloc.party_id FROM receipts rec, receipt_allocations rec_aloc WHERE rec.company_id = ".$get['company_id']." AND  rec.company_id = rec_aloc.company_id AND rec_aloc.party_id = '".$get['customer_id']."' AND rec.id = rec_aloc.receipt_id AND  rec.is_active = rec_aloc.is_active AND rec.is_active =0 AND rec.current_status ='".ST_APPROVED."' ".$paycondition;
            $receiptsres = $callConfig->getAllRows($receiptssql);
            $return['receiptssql'] =$receiptssql; 
            foreach($receiptsres as $rec){
                $rec->transaction_table ='receipts';
                $transactionwithdates[date('Ymd',strtotime($rec->transaction_date))][] =$rec; 
            } 

        }
        $return['transactionwithdates'] =$transactionwithdates; 
        return $return;
    }
    function getCustomerStatements($get){
        global $callConfig;
        $transactionwithdates = array();
        $getFinanceialYear = $this->getFinanceialYear($get['from_date']);
        $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];
        $customerssql = "SELECT id,display_name,company, account_code, email, phone FROM `customers` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        $customers = array();
        foreach($customersres as $custres){
            $customers[$custres->id]  = $custres;
        }
        $return['customers'] =$customers; 

        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= transaction_date";
            $paycondition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= rec.transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= rec.transaction_date";
        }
        if($get['customer_id'] !=""){

            $lastbalancesql ="SELECT * FROM `account_balances` WHERE company_id = ".$get['company_id']." AND finance_year = '".$financeyear."' AND  account_code = '".$customers[$get['customer_id']]->account_code."' AND balance_date < '".$this->dateconvertdbformat($get['from_date'])."' AND is_active = 0 ORDER BY balance_date desc limit 1";
            $lastbalanceres = $callConfig->getRow($lastbalancesql);
            $return['last_account_balances_sql']= $lastbalancesql;
            $return['last_account_balances']= $lastbalanceres;
            $sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id, ref_type, ref_no, amount, total_amount as p_total_amount, due_date, payment_term_history, ref_id FROM `sales_invoices` WHERE company_id = ".$get['company_id']." AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            //$sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id, ref_type, ref_no, amount, total_amount as p_total_amount, due_date, payment_term_history, ref_id FROM `sales_invoices` WHERE company_id = ".$get['company_id']." AND is_active =0 ".$condition;
            $sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);
            $return['sales_invoices_sql'] =$sales_invoicessql; 
            foreach($sales_invoicesres as $si){
                 $si->transaction_table ='sales_invoices';
                $transactionwithdates[date('Ymd',strtotime($si->transaction_date))][] =$si; 
                $sales_invoices_amt[$si->id] += $si->p_total_amount;
                $sales_invoices[$si->id] = $si;
            }
            $return['sales_invoices_amt'] =$sales_invoices_amt;
            $return['sales_invoices'] =$sales_invoices;
            $debit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount, due_date, ref_id FROM `debit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'sales_invoices' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $debit_notesres = $callConfig->getAllRows($debit_notessql);
            $return['debit_notessql'] =$debit_notessql; 
            foreach($debit_notesres as $dn){
                $dn->transaction_table ='debit_notes';
                $transactionwithdates[date('Ymd',strtotime($dn->transaction_date))][] =$dn; 
                $debit_notes_amt[$dn->ref_id] += $dn->p_total_amount;
            }
            $return['debit_notes_amt'] =$debit_notes_amt;
            $credit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount, due_date, ref_id FROM `credit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'sales_invoices' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $credit_notesres = $callConfig->getAllRows($credit_notessql);
            $return['credit_notessql'] =$credit_notessql; 
            foreach($credit_notesres as $cn){
                $cn->transaction_table ='credit_notes';
                $transactionwithdates[date('Ymd',strtotime($cn->transaction_date))][] =$cn; 
                $credit_notes_amt[$cn->ref_id] += $cn->p_total_amount;
            }
            $return['credit_notes_amt'] =$credit_notes_amt;
            $receiptssql = "SELECT rec.id, rec.transaction_no as transaction_number, rec.payment_date as transaction_date, rec_aloc.total_amount, rec_aloc.pending_amount, rec_aloc.paid_amonut, rec_aloc.tds_amount, rec_aloc.total_paid_amonut as p_total_amount, rec_aloc.party_id, rec_aloc.transaction_id  as ref_id FROM receipts rec, receipt_allocations rec_aloc WHERE rec.company_id = ".$get['company_id']." AND rec.company_id = rec_aloc.company_id AND rec_aloc.party_id = '".$get['customer_id']."' AND rec.id = rec_aloc.receipt_id AND rec.is_active = rec_aloc.is_active AND rec_aloc.transaction_table = 'sales_invoices' AND rec.is_active =0 ".$paycondition;
            $receiptsres = $callConfig->getAllRows($receiptssql);
            $return['receiptssql'] =$receiptssql; 
            foreach($receiptsres as $rec){
                $rec->transaction_table ='receipts';
                $transactionwithdates[date('Ymd',strtotime($rec->transaction_date))][] =$rec; 
                 $receipts_amt[$rec->ref_id] += $rec->p_total_amount;
            } 
            $return['receipts_amt'] =$receipts_amt;
        }
        $return['transactionwithdates'] =$transactionwithdates; 
        return $return;
    }



    function getVendorledger($get){
        global $callConfig;
        $transactionwithdates = array();
        $getFinanceialYear = $this->getFinanceialYear($get['from_date']);
        $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];
        $customerssql = "SELECT id,display_name,company, account_code FROM `vendors` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        $customers = array();
        foreach($customersres as $custres){
            $customers[$custres->id]  = $custres;
        }
        $return['customers'] =$customers; 

        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= transaction_date";
           // $paycondition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= rec.transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= rec.transaction_date";
        }
        if($get['customer_id'] !=""){

            $lastbalancesql ="SELECT * FROM `account_balances` WHERE company_id = ".$get['company_id']." AND finance_year = '".$financeyear."' AND  account_code = '".$customers[$get['customer_id']]->account_code."' AND balance_date < '".$this->dateconvertdbformat($get['from_date'])."' AND is_active = 0 ORDER BY balance_date desc limit 1";
            $lastbalanceres = $callConfig->getRow($lastbalancesql);
            $return['last_account_balances_sql']= $lastbalancesql;
            $return['last_account_balances']= $lastbalanceres;

            
            $sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id, ref_type, ref_no, amount, total_amount as p_total_amount FROM `supplier_bills` WHERE company_id = ".$get['company_id']." AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);
            $return['sales_invoices_sql'] =$sales_invoicessql; 
            foreach($sales_invoicesres as $si){
                 $si->transaction_table ='supplier_bills';
                $transactionwithdates[date('Ymd',strtotime($si->transaction_date))][] =$si; 
            }

            $debit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount FROM `debit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'supplier_bills' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $debit_notesres = $callConfig->getAllRows($debit_notessql);
            $return['debit_notessql'] =$debit_notessql; 
            foreach($debit_notesres as $dn){
                $dn->transaction_table ='debit_notes';
                $transactionwithdates[date('Ymd',strtotime($dn->transaction_date))][] =$dn; 
            }

            $credit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount FROM `credit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'supplier_bills' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $credit_notesres = $callConfig->getAllRows($credit_notessql);
            $return['credit_notessql'] =$credit_notessql; 
            foreach($credit_notesres as $cn){
                $cn->transaction_table ='credit_notes';
                $transactionwithdates[date('Ymd',strtotime($cn->transaction_date))][] =$cn; 
            }

            $receiptssql = "SELECT rec.id, rec.transaction_no as transaction_number, rec.payment_date as transaction_date, rec_aloc.total_amount, rec_aloc.pending_amount, rec_aloc.paid_amonut, rec_aloc.tds_amount, rec_aloc.total_paid_amonut as p_total_amount, rec_aloc.party_id FROM payments rec, payment_allocations rec_aloc WHERE rec.company_id = ".$get['company_id']." AND  rec.company_id = rec_aloc.company_id AND rec_aloc.party_id = '".$get['customer_id']."' AND rec.id = rec_aloc.receipt_id AND  rec.is_active = rec_aloc.is_active AND rec.is_active =0 ".$paycondition;
            $receiptsres = $callConfig->getAllRows($receiptssql);
            $return['receiptssql'] =$receiptssql; 
            foreach($receiptsres as $rec){
                $rec->transaction_table ='payments';
                $transactionwithdates[date('Ymd',strtotime($rec->transaction_date))][] =$rec; 
            } 

        }
        $return['transactionwithdates'] =$transactionwithdates; 
        return $return;
    }
    function getVendorstatements($get){
        global $callConfig;
        $transactionwithdates = array();
        $getFinanceialYear = $this->getFinanceialYear($get['from_date']);
        $financeyear = $getFinanceialYear['fullstartyear'].$getFinanceialYear['fullendyear'];
        $customerssql = "SELECT id,display_name,company, account_code, email, phone FROM `vendors` WHERE company_id = ".$get['company_id']." AND is_active =0";
        $customersres = $callConfig->getAllRows($customerssql);
        $customers = array();
        foreach($customersres as $custres){
            $customers[$custres->id]  = $custres;
        }
        $return['customers'] =$customers; 

        if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= transaction_date";
            $paycondition .= " AND '".$this->dateconvertdbformat($get['from_date'])."' <= rec.transaction_date  AND '".$this->dateconvertdbformat($get['to_date'])."' >= rec.transaction_date";
        }
        if($get['customer_id'] !=""){

            $lastbalancesql ="SELECT * FROM `account_balances` WHERE company_id = ".$get['company_id']." AND finance_year = '".$financeyear."' AND  account_code = '".$customers[$get['customer_id']]->account_code."' AND balance_date < '".$this->dateconvertdbformat($get['from_date'])."' AND is_active = 0 ORDER BY balance_date desc limit 1";
            $lastbalanceres = $callConfig->getRow($lastbalancesql);
            $return['last_account_balances_sql']= $lastbalancesql;
            $return['last_account_balances']= $lastbalanceres;
            $sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id, ref_type, ref_no, amount, total_amount as p_total_amount, due_date, payment_term_history, ref_id FROM `supplier_bills` WHERE company_id = ".$get['company_id']." AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            //$sales_invoicessql = "SELECT id, transaction_number, transaction_date, party_id, ref_type, ref_no, amount, total_amount as p_total_amount, due_date, payment_term_history, ref_id FROM `sales_invoices` WHERE company_id = ".$get['company_id']." AND is_active =0 ".$condition;
            $sales_invoicesres = $callConfig->getAllRows($sales_invoicessql);
            $return['sales_invoices_sql'] =$sales_invoicessql; 
            foreach($sales_invoicesres as $si){
                 $si->transaction_table ='supplier_bills';
                $transactionwithdates[date('Ymd',strtotime($si->transaction_date))][] =$si; 
                $sales_invoices_amt[$si->id] += $si->p_total_amount;
                $sales_invoices[$si->id] = $si;
            }
            $return['sales_invoices_amt'] =$sales_invoices_amt;
            $return['sales_invoices'] =$sales_invoices;
            $debit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount, due_date, ref_id FROM `debit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'supplier_bills' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $debit_notesres = $callConfig->getAllRows($debit_notessql);
            $return['debit_notessql'] =$debit_notessql; 
            foreach($debit_notesres as $dn){
                $dn->transaction_table ='debit_notes';
                $transactionwithdates[date('Ymd',strtotime($dn->transaction_date))][] =$dn; 
                $debit_notes_amt[$dn->ref_id] += $dn->p_total_amount;
            }
            $return['debit_notes_amt'] =$debit_notes_amt;
            $credit_notessql = "SELECT id, transaction_number, transaction_date, party_id, amount, ref_type, ref_no, total_amount as p_total_amount, due_date, ref_id FROM `credit_notes` WHERE company_id = ".$get['company_id']." AND ref_type = 'supplier_bills' AND party_id = '".$get['customer_id']."' AND is_active =0 ".$condition;
            $credit_notesres = $callConfig->getAllRows($credit_notessql);
            $return['credit_notessql'] =$credit_notessql; 
            foreach($credit_notesres as $cn){
                $cn->transaction_table ='credit_notes';
                $transactionwithdates[date('Ymd',strtotime($cn->transaction_date))][] =$cn; 
                $credit_notes_amt[$cn->ref_id] += $cn->p_total_amount;
            }
            $return['credit_notes_amt'] =$credit_notes_amt;
            $receiptssql = "SELECT rec.id, rec.transaction_no as transaction_number, rec.payment_date as transaction_date, rec_aloc.total_amount, rec_aloc.pending_amount, rec_aloc.paid_amonut, rec_aloc.tds_amount, rec_aloc.total_paid_amonut as p_total_amount, rec_aloc.party_id, rec_aloc.transaction_id  as ref_id FROM payments rec, payment_allocations rec_aloc WHERE rec.company_id = ".$get['company_id']." AND rec.company_id = rec_aloc.company_id AND rec_aloc.party_id = '".$get['customer_id']."' AND rec.id = rec_aloc.receipt_id AND rec.is_active = rec_aloc.is_active AND rec_aloc.transaction_table = 'supplier_bills' AND rec.is_active =0 ".$paycondition;
            $receiptsres = $callConfig->getAllRows($receiptssql);
            $return['receiptssql'] =$receiptssql; 
            foreach($receiptsres as $rec){
                $rec->transaction_table ='payments';
                $transactionwithdates[date('Ymd',strtotime($rec->transaction_date))][] =$rec; 
                 $receipts_amt[$rec->ref_id] += $rec->p_total_amount;
            } 
            $return['receipts_amt'] =$receipts_amt;
        }
        $return['transactionwithdates'] =$transactionwithdates; 
        return $return;
    }


    function getEnquiryVendorOrCustomer($get){
        global $callConfig;
        $return = array();
        $return['get'] =$get;
        $value = $get['value'];
        $vencustsql = "SELECT id, display_name, company, image FROM ".$get['searchvendorcustomertype']." WHERE company_id = ".$get['company_id']." AND (display_name like '%".$value."%' OR company like '%".$value."%') AND is_active =0 ";
        $vencustres = $callConfig->getAllRows($vencustsql);
        $return['search_result'] =$vencustres; 
        return $return;
    }
    function setAccountCode($get, $post){
        global $callConfig;
        $sep = "-";
        $serialLen = 2;
        $prefix = ""; 
        if (!empty($post['parent_account_code'])) {
            $parentRow = $callConfig->getRow("SELECT account_code FROM chart_of_accounts WHERE account_code = '" . $post['parent_account_code'] . "' LIMIT 1");
            if ($parentRow) {
                $prefix = $parentRow->account_code;
            }
        } else {
            $components = []; 
            if (!empty($post['account_type'])) {
                $typeRow = $callConfig->getRow("SELECT account_type_short_name FROM account_type WHERE id = '" . (int) $post['account_type'] . "' AND is_active = 0 LIMIT 1");
                if ($typeRow && !empty($typeRow->account_type_short_name)) {
                    $components[] = strtoupper(trim($typeRow->account_type_short_name));
                }
            } 
            if (!empty($post['account_groups'])) {
                $groupRow = $callConfig->getRow("SELECT group_short_name FROM account_groups WHERE id = '" . (int) $post['account_groups'] . "' AND is_active = 0 LIMIT 1");
                if ($groupRow && !empty($groupRow->group_short_name)) {
                    $components[] = strtoupper(trim($groupRow->group_short_name));
                }
            } 
            $prefix = implode($sep, $components);
        } 
        $lastRow = $callConfig->getRow("SELECT account_code FROM account_code_list WHERE account_code LIKE '" . $prefix . $sep . "%' ORDER BY id DESC LIMIT 1");
        if ($lastRow && preg_match('/(\d+)$/', $lastRow->account_code, $matches)) {
            $serial = (int) $matches[1] + 1;
        } else {
            $serial = 1;
        } 
        $serialFormatted = str_pad($serial, $serialLen, "0", STR_PAD_LEFT);
        $finalCode = $prefix . $sep . $serialFormatted; 
        $insertArray = [ "account_type" => $post['account_type'] ?? "", "account_groups" => $post['account_groups'] ?? "", "length" => $serialLen, "sepration_symbol" => $sep, "account_code" => $finalCode, "is_active" => 0, "current_status" => 0, "created_by" => $get['user_id'], "created_date" => date("Y-m-d H:i:s"), ]; 
        $callConfig->insertRecord("account_code_list", $insertArray); 
        return ["account_code" => $finalCode];
    }
    function chartOfAccountLockAndUnlock($get){
        global $callConfig;
        $return['get'] = $get;
        $ca_status = $get['ca_status']!=""?$get['ca_status']:'N';
        $ca_id = $get['ca_id'];
        $updateca = array('lock_and_unlock_status'=>$ca_status,'lock_and_unlock_user_id'=>$get['user_id'],'updated_date'=>date('Y-m-d h:i:s'), 'updated_by'=>$get['user_id']);
        $res= $callConfig->updateRecord('chart_of_accounts',$updateca,'id',$ca_id);
        $return['res'] = $res;
        return $return;
    }


}

?>