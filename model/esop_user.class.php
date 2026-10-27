<?php 
class esopUserClass extends userClass{
	function insertCompanyValuations($post){
		global $callConfig;
		//$return['post'] = $post;
		$insertary=array('company_id'=>$post['company_id'],'valuation_name'=>$post['valuation_name'],'valuation_date'=>$this->dateconvertdbformat($post['valuation_date']),'company_value'=>$post['company_value'],'old_share_qty'=>$post['old_share_qty'],'valuation_shareholding'=>$post['valuation_shareholding'],'new_share_qty'=>$post['new_share_qty'],'total_share_qty'=>$post['total_share_qty'],'per_share_value'=>$post['per_share_value'],'notes'=>$post['notes']);
		if($post['rid'] !=""){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$res= $callConfig->updateRecord('company_valuations',$insertary,'id',$post['rid']);
			$return['res'] = $res;
			$rid = $post['rid'];
		}else{
			$insertary['is_active']=0;
			$insertary['current_status']=0;;
			$insertary['created_by']=$post['user_id'];
			$insertary['created_date']=date("Y-m-d H:i:s");
			$res = $callConfig->insertRecord('company_valuations',$insertary);
			$return['res'] = $res;
			$rid = $res['LASTID'];
		}
		$updateary = array('is_active'=>null);
		$callConfig->updateRecord('company_valuation_distributions',$updateary,'company_valuation_id',$rid);
		foreach($post['distributer_name'] as $keyid=>$keyvalue){
			if($post['distributer_name'][$keyid] !="" AND $post['distribute_percentage'][$keyid] !=""){
				$dilution_seq = $post['dilution_seq'][$keyid];
				$dilution = $post['dilution'][$dilution_seq]=='0'?'0':'1';
				$distributamount = round($post['total_share'][$keyid]*$post['per_share_value']);
				//$distributshare = round(($post['distribute_percentage'][$keyid]/100)*$post['total_share_qty'],2);

				$equity_shares_percentage_share = round(($post['equity_shares_percentage'][$keyid]/100)*$post['total_share'][$keyid]);
				$equity_shares_percentage_amt = round(($post['equity_shares_percentage'][$keyid]/100)*$distributamount,2);

				$preference_shares_percentage_share = round(($post['preference_shares_percentage'][$keyid]/100)*$post['total_share'][$keyid]);
				$preference_shares_percentage_amt = round(($post['preference_shares_percentage'][$keyid]/100)*$distributamount,2);

				$insertary=array('company_id'=>$post['company_id'],'company_valuation_id'=>$rid,'distributer_name'=>$post['distributer_name'][$keyid], 'old_percentage'=>$post['old_percentage'][$keyid],'new_percentage'=>$post['new_percentage'][$keyid], 'distribute_percentage'=>$post['distribute_percentage'][$keyid],'company_contribution_user_id'=>$post['ccuid'][$keyid],'total_amount'=>$distributamount,'old_shares'=>$post['old_shares_qty'][$keyid], 'new_shares'=>$post['new_shares_qty'][$keyid], 'total_shares'=>$post['total_share'][$keyid],'equity_shares_percentage'=>$post['equity_shares_percentage'][$keyid],'equity_shares'=>$equity_shares_percentage_share,'equity_shares_amount'=>$equity_shares_percentage_amt,'preference_shares_percentage'=>$post['preference_shares_percentage'][$keyid],'preference_shares'=>$preference_shares_percentage_share,'preference_shares_amount'=>$preference_shares_percentage_amt,'dilution'=>$dilution);

				

				if($post['irid'][$keyid] !=""){
					$insertary['updated_date']=date('Y-m-d h:i:s');
					$insertary['updated_by']=$post['user_id'];
					$insertary['is_active']=0;
					$return['item'][]= $callConfig->updateRecord('company_valuation_distributions',$insertary,'id',$post['irid'][$keyid]);
				}else{
					$insertary['is_active']=0;
					$insertary['current_status']=0;;
					$insertary['created_by']=$post['user_id'];
					$insertary['created_date']=date("Y-m-d H:i:s");
					$return['item'][] = $callConfig->insertRecord('company_valuation_distributions',$insertary);
				}
				if($dilution == '0'){
					$insertary =array('dilution'=>$dilution,'dilution_percentage'=>$post['distribute_percentage'][$keyid]);
					$return['ccu'][]= $callConfig->updateRecord('company_contribution_users',$insertary,'id',$post['ccuid'][$keyid]);
				}	
			}
			
		}
		return $return;
	}
	function getCompanyValuations($get){
		global $callConfig;
		$return = array();

    	$companyvaluationssql = "SELECT id, valuation_name, valuation_date, company_value, old_share_qty,valuation_shareholding, new_share_qty, total_share_qty, per_share_value, notes FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date ASC";
    	$paymentalocres = $callConfig->getAllRows($companyvaluationssql);

    	$lastvaluationsql = "SELECT id, valuation_date, company_value, total_share_qty FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date DESC";
    	$lastvaluationres = $callConfig->getRow($lastvaluationsql);
    	$return['last_company_valuations'] = $lastvaluationres;

    	$company_contribution_userssql = "SELECT id, display_name, total_contribuition, user_type, dilution, dilution_percentage,image FROM company_contribution_users WHERE company_id = '".$get['company_id']."' AND user_type !='EMP' AND is_active=0";
    	$company_contribution_usersres = $callConfig->getAllRows($company_contribution_userssql);
    	$return['company_contribution_users'] = $company_contribution_usersres;


    	$typsql = " SELECT  genc_detail_code, genc_detail_name  FROM  general_code_details WHERE company_id = '".$get['company_id']."' AND is_active ='0' AND genc_code ='DISTRIBUTIONTYPES' ORDER BY id ASC";
    	$typsres = $callConfig->getAllRows($typsql);
    	$return['contribution_users_type'] = $typsres;

    	$companyvaluationsdistributionssql = "SELECT id, `company_contribution_user_id`, `distributer_name`,dilution,new_percentage, `old_percentage`, `distribute_percentage`, `total_amount`, `old_shares`, `new_shares`, `total_shares`, `dilution`, `equity_shares_percentage`, `equity_shares`, `equity_shares_amount`, `preference_shares_percentage`, `preference_shares`, `preference_shares_amount` FROM company_valuation_distributions WHERE company_id = '".$get['company_id']."' AND company_valuation_id = '".$get['rid']."' AND is_active=0";
    	$companyvaluationsdistributionsres = $callConfig->getAllRows($companyvaluationsdistributionssql);
    	$comvaldist = array();
    	foreach($companyvaluationsdistributionsres as $cvd){
    		$comvaldist[$cvd->company_contribution_user_id] = $cvd;
    	}
    	$return['company_valuation_distributions'] = $comvaldist;

    	$companyval =array();
    	foreach($paymentalocres as $cval){
    		$companyval[$cval->id] = $cval;
    	}
    	$return['company_valuations'] = $companyval[$get['rid']];
    	$return['company_valuations_all'] = $companyval;

    	$lastvaluationsql = "SELECT distribute_percentage, company_contribution_user_id,equity_shares_percentage, preference_shares_percentage ,total_shares  FROM company_valuation_distributions WHERE company_id = '".$get['company_id']."' AND company_valuation_id = (SELECT id FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active = 0 ORDER BY valuation_date DESC LIMIT 1) AND is_active=0";
    	$lastvaluationres = $callConfig->getAllRows($lastvaluationsql);
    	$lastvaluationresult =$lastvaluationtotalshare= array();
    	foreach($lastvaluationres as $cvd){
    		$lastvaluationresult[$cvd->company_contribution_user_id] = $cvd->distribute_percentage;
    		$lastvaluationtotalshare[$cvd->company_contribution_user_id] = $cvd->total_shares;
    	}
    	$return['last_valuation_result'] = $lastvaluationresult;
    	$return['last_valuation_total_share'] = $lastvaluationtotalshare;
    	return $return;
	}
	function insertESOPPlans($post){
		global $callConfig;
		$select_company_valuation = $post['select_company_valuation'];
		$explodevaluation = explode("||",$select_company_valuation);
		$insertary=array('company_id'=>$post['company_id'],'company_valuation_id'=>$explodevaluation[0],'company_valuation_distribution_id'=>$explodevaluation[1],'plan_name'=>$post['plan_name'],'date_of_grant'=>$this->dateconvertdbformat($post['date_of_grant']),'share'=>$post['share'],'company_valuation'=>$post['company_valuation'],'share_price_at_grant_time'=>$post['share_price_at_grant_time'],'note'=>$post['note']);
		if($post['rid'] !=""){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$res= $callConfig->updateRecord('esop_plans',$insertary,'id',$post['rid']);
			$return['res'] = $res;
			$rid = $post['rid'];
		}else{
			$insertary['is_active']=0;
			$insertary['current_status']=0;;
			$insertary['created_by']=$post['user_id'];
			$insertary['created_date']=date("Y-m-d H:i:s");
			$res = $callConfig->insertRecord('esop_plans',$insertary);
			$return['res'] = $res;
			$rid = $res['LASTID'];
		}

		$return['post'] = $post;


		$updateary = array('is_active'=>null);
		$callConfig->updateRecord('esop_plan_buckets',$updateary,'esop_plans_id',$rid);
		foreach($post['bucket_name'] as $keyid=>$keyvalue){
			 $vesting_schedule = array();
			$seq = $_POST['vesting_schedule_seq'][$keyid]; // loop seq get
			foreach($_POST['vesting_percentage'][$seq] as $position=>$val){
				$vesting_date = $this->dateconvertdbformat($_POST['vesting_date'][$seq][$position]);
				$till_date = $this->dateconvertdbformat($_POST['till_date'][$seq][$position]);
				$vesting_percentage = $_POST['vesting_percentage'][$seq][$position];
				if($vesting_date !="" AND $vesting_percentage !=""){
		        	$vesting_schedule[] = array('vesting_date'=>$vesting_date,'vesting_percentage'=>$vesting_percentage,'till_date'=>$till_date);
		    	}
		    }
		    $vestingScheduleencode = json_encode((array)$vesting_schedule);
			$insertary=array('company_id'=>$post['company_id'],'esop_plans_id'=>$rid,'bucket_name'=>$post['bucket_name'][$keyid],'grant_price'=>$post['grant_price'][$keyid],'discount'=>$post['discount'][$keyid],'expected_option_benefit'=>$post['expected_option_benefit'][$keyid],'bs_option_value'=>$post['bs_option_value'][$keyid],'options_granted'=>$post['options_granted'][$keyid],'total_discount'=>$post['total_discount'][$keyid],'discount_per_option'=>$post['discount_per_option'][$keyid],'vesting_schedule'=>$vestingScheduleencode,'unallocated_shares'=>$post['options_granted'][$keyid], 'allocated_shares'=>'0');
			if($post['bucket_name'][$keyid] !=""){
				if($post['irid'][$keyid] !=""){
					$insertary['updated_date']=date('Y-m-d h:i:s');
					$insertary['updated_by']=$post['user_id'];
					$insertary['is_active']=0;
					$return['item'][]= $callConfig->updateRecord('esop_plan_buckets',$insertary,'id',$post['irid'][$keyid]);
				}else{
					$insertary['is_active']=0;
					$insertary['current_status']=0;;
					$insertary['created_by']=$post['user_id'];
					$insertary['created_date']=date("Y-m-d H:i:s");
					$return['item'][] = $callConfig->insertRecord('esop_plan_buckets',$insertary);
				}
			}	
		}
		return $return;
	}
	function geteSopPlans($get){
		global $callConfig;
		$return = array();

		$companyvaluationssql = "SELECT cv.id as cv_id, cv.valuation_name, cv.company_value ,cv.valuation_date, cv.per_share_value , cvd.id as cvd_id ,cvd.distributer_name, cvd.total_shares, cvd.new_shares FROM company_valuations cv, company_valuation_distributions cvd, company_contribution_users ccu WHERE cv.company_id = '".$get['company_id']."' AND cv.company_id = cvd.company_id AND cv.company_id = ccu.company_id AND cv.id = cvd.company_valuation_id AND cvd.company_contribution_user_id = ccu.id AND ccu.user_type = 'E' AND cvd.new_shares != 0 AND cv.is_active = cvd.is_active AND cv.is_active = ccu.is_active AND cv.is_active = 0";
    	$companyvaluationsres = $callConfig->getAllRows($companyvaluationssql);
    	$return['company_valuations'] = $companyvaluationsres;

    	$esopplanssql = "SELECT  id, company_valuation_id, company_valuation_distribution_id, plan_name, date_of_grant, company_valuation, share, share_price_at_grant_time, note FROM esop_plans WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active=0";
    	$esopplansres = $callConfig->getRow($esopplanssql);
    	$return['esop_plans'] = $esopplansres;

    	$esopplanbucketssql = "SELECT id, esop_plans_id, bucket_name, grant_price, discount, expected_option_benefit, bs_option_value, options_granted, total_discount, discount_per_option, vesting_schedule FROM esop_plan_buckets WHERE company_id = '".$get['company_id']."' AND esop_plans_id = '".$get['rid']."' AND is_active=0";
    	$esopplansres = $callConfig->getAllRows($esopplanbucketssql);
    	$return['esop_plan_buckets'] = $esopplansres;
    	return $return;
	}
	function getESOPAllocations($get){
		global $callConfig;
		$return = array();

		if($get['page_type']=="APPROVAL"){ //only used approavl page
			$esopallocationsql = "SELECT ea.*, ep.plan_name, ep.date_of_grant, epb.bucket_name, epb.grant_price,epb.grant_price ,epb.grant_price, epb.discount ,epb.discount_per_option ,epb.expected_option_benefit ,epb.bs_option_value ,epb.options_granted,epb.total_discount ,epb.vesting_schedule FROM esop_allocations ea, esop_plans ep, esop_plan_buckets epb WHERE ea.company_id = '".$get['company_id']."' AND ea.company_id = ep.company_id AND ea.company_id = epb.company_id AND ea.esop_plan_id = ep.id AND ea.esop_bucket_id = epb.id AND ea.id = '".$get['rid']."' AND ea.is_active=0 AND ea.is_active =ep.is_active AND ea.is_active = epb.is_active";
	    	$esopallocationres = $callConfig->getRow($esopallocationsql);
	    	$return['esop_allocations_sql'] = $esopallocationsql;
	    	$return['esop_allocations'] = $esopallocationres;

	    	$esopvestingschedulecntsql = "SELECT SUM(allocation_share) total_share, SUM(IF(current_status = ".ST_APPROVED.",allocation_share,0)) allocated_share, SUM(IF(current_status = 0 OR current_status = ".ST_REJECTED.",allocation_share,0)) un_allocated_share, SUM(total_amount) as total_ammount, SUM(IF(current_status = ".ST_APPROVED.",total_amount,0)) allocated_ammount, SUM(IF(current_status = 0 OR current_status = ".ST_REJECTED.",total_amount,0)) un_allocated_ammount FROM esop_vesting_schedule WHERE company_id = '".$get['company_id']."' AND esop_allocation_id='".$get['rid']."' AND is_active=0";
	    	$esopvestingschedulecntres = $callConfig->getRow($esopvestingschedulecntsql);
	    	$return['esop_vesting_schedule_cnt'] = $esopvestingschedulecntres;

	    	$esopvestingschedulesql = "SELECT * FROM esop_vesting_schedule WHERE company_id = '".$get['company_id']."' AND esop_allocation_id='".$get['rid']."' AND is_active=0 ORDER BY allocation_date ASC";
	    	$esopvestingscheduleres = $callConfig->getAllRows($esopvestingschedulesql);
	    	$return['esop_vesting_schedule'] = $esopvestingscheduleres;

		}else{ //insert and update page get detals
	    	$esopallocationsql = "SELECT * FROM esop_allocations WHERE company_id = '".$get['company_id']."' AND is_active=0";
	    	$esopallocationres = $callConfig->getAllRows($esopallocationsql);
			$esop_allocations =array();
	    	foreach($esopallocationres as $cval){
	    		$esop_allocations[$cval->id] = $cval;
	    	}
	    	$return['esop_allocations'] = $esop_allocations[$get['rid']];
	    	$return['esop_allocations_all'] = $esop_allocations;

	    	$esopplanssql = "SELECT id, plan_name FROM esop_plans WHERE company_id = '".$get['company_id']."' AND is_active=0";
	    	$esopplansres = $callConfig->getAllRows($esopplanssql);
	    	foreach($esopplansres as $esopval){
	    		$esopplansval[$esopval->id] = $esopval;
	    	}
	    	$return['esop_plans'] = $esopplansval;

	    	$esopplanbucketssql = "SELECT id, esop_plans_id,bucket_name FROM esop_plan_buckets WHERE company_id = '".$get['company_id']."' AND is_active=0";
	    	$esopplanbucketsres = $callConfig->getAllRows($esopplanbucketssql);
	    	$esop_plan_buckets = array();
	    	foreach($esopplanbucketsres as $esopbuckval){
	    		$esop_plan_buckets[$esopbuckval->esop_plans_id][$esopbuckval->id] = $esopbuckval;
	    	}
	    	$return['esop_plan_buckets'] = $esop_plan_buckets;
	    	
	    	$company_contribution_userssql = "SELECT id, display_name FROM company_contribution_users WHERE company_id = '".$get['company_id']."' AND user_type ='EMP' AND is_active=0";
	    	$company_contribution_usersres = $callConfig->getAllRows($company_contribution_userssql);
	    	$return['company_contribution_users'] = $company_contribution_usersres;

	    	$like = "'".'%"'.ESOP_ADMIN_ROLE.'"%'."'";
	    	$application_userssql = "SELECT id, display_name FROM application_users WHERE company_id = '".$get['company_id']."' AND role_id like ".$like." AND is_active=0";
	    	$application_usersres = $callConfig->getAllRows($application_userssql);
	    	$return['application_users'] = $application_usersres;

	    	
	    }	
	    $get['table'] ='esop_allocations';
		$status_progress = $this->getStatusProgressList($get);
		foreach($status_progress as $statusprogress){
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}
    	return $return;
	}
	function insertESOPAllocations($post){
		global $callConfig;
		$explname = explode(" ",$post['emp_name']);
		$auquery = "SELECT id FROM application_users where company_id = '".$post['company_id']."' AND user_email ='".trim($post['user_email'])."' AND is_active = 0 ";
		$aures = $callConfig->getRow($auquery);
		$return['aures'] = $aures;
		if(!isset($aures->id)){
			$role = json_encode(array(ESOP_USER_ROLE));
			$fieldnames=array('company_id'=>$post['company_id'],'user_name'=>$post['emp_code'],'first_name'=>$explname[0],"last_name"=>$explname[1],"display_name"=>$post['emp_name'],'user_email'=>$post['user_email'],'current_status'=>'0','is_active' =>'0',"created_date"=>date('Y-m-d H:i:s'),"created_by"=>$post['user_id'],'user_type'=>'1','role_id'=>$role,"esop_connect"=>$post['esop_connect']);
			$user=$callConfig->insertRecord('application_users',$fieldnames);
			$return['insertau'] = $user;
			$userid = $user['LASTID'];
			$returnarray['user_create'] = $user;
			if(isset($user['LASTID']) AND $user['LASTID'] !=""){
				$query = "SELECT application_module,id,role_short_code FROM application_roles where company_id = '".$post['company_id']."' AND role_short_code ='".ESOP_USER_ROLE."' AND is_active = 0 ";
				$roleres = $callConfig->getRow($query);
				
				$insertarray = array("company_id"=>$post['company_id'],"user_id"=>$userid,"module_setup_id"=>$roleres->application_module,"application_module_id"=>$roleres->application_module,"role_id"=>$roleres->id,"role_short_code"=>$roleres->role_short_code,"is_active"=>"0","current_status"=>"0","created_date"=>date('Y-m-d h:i:s'),"created_by"=>$post['user_id']);
				$insert = $callConfig->insertRecord('application_user_module_roles',$insertarray);
				
				$emailarray['user_type'] =1;
				$emailarray['user_company_id'] = $post['company_id'];
				$emailarray['session_company_id'] = $post['company_id'];
				$emailarray['to_company_id'] = $post['company_id'];
				$emailarray['user_display_name'] = $post['emp_name'];
				$emailarray['user_email_id'] = $post['user_email'];
				$emailarray['user_module_id'] = $roleres->application_module;
				$emailarray['user_id'] =$userid;
				$emailarray['created_by'] = $post['user_id'];
				$emailarray['user_email'] =  $post['user_email'];
				$emailarray['company_id'] = $post['company_id'];
				$emailarray['template_reference_id'] = 3;
				$data = $this->NewUserformate($emailarray,$callConfig->dbname);
			}
		}else{
			$userid = $aures->id;
		}	
		$inseruser =array('company_id'=>$post['company_id'],'first_name'=>$explname[0],'last_name'=>$explname[1],'emp_code'=>$post['emp_code'],'user_id'=>$userid,'date_of_joining'=>$this->dateconvertdbformat($post['date_of_joining']),'display_name'=>$post['emp_name'],'designation'=>$post['designation'],'annual_ctc'=>$post['annual_ctc'],'user_type'=>'EMP','user_email'=>$post['user_email'], 'esop_connect'=>$post['esop_connect']);
		if($post['company_contribution_user_id'] !=0){
			$inseruser['updated_date']=date('Y-m-d h:i:s');
			$inseruser['updated_by']=$post['user_id'];
			$company_contribution_user_id = $post['company_contribution_user_id'];
			$res1= $callConfig->updateRecord('company_contribution_users',$insertary,'id',$company_contribution_user_id);
		}else{
			$inseruser['user_id']=$userid;
			$inseruser['is_active']=0;
			$inseruser['current_status']=0;;
			$inseruser['created_by']=$post['user_id'];
			$inseruser['created_date']=date("Y-m-d H:i:s");
			$res1 = $callConfig->insertRecord('company_contribution_users',$inseruser);
			$company_contribution_user_id = $res1['LASTID'];
		}

		$getuseridsql = "SELECT user_id FROM company_contribution_users where company_id = '".$post['company_id']."' AND id ='".$company_contribution_user_id."' AND is_active = 0 ";
		$getuseridsres = $callConfig->getRow($getuseridsql);


		foreach($post['vesting_percentage'] as $seq=>$val){
			$vesting_date = $post['vesting_date'][$seq];
			$till_date = $post['till_date'][$seq];
			if($val !="" AND $till_date !="" AND $vesting_date !=""){
        		$vesting_schedule[] = array('vesting_date'=>$vesting_date,'vesting_percentage'=>$val,'till_date'=>$till_date);
    		}
    	}
    	$vestingScheduleencode = json_encode((array)$vesting_schedule);

		$insertary=array('company_id'=>$post['company_id'],'user_id'=>$getuseridsres->user_id,'company_contribution_user_id'=>$company_contribution_user_id,'emp_code'=>$post['emp_code'],'date_of_joining'=>$this->dateconvertdbformat($post['date_of_joining']),'emp_name'=>$post['emp_name'],'designation'=>$post['designation'],'tenure'=>$post['tenure'],'esop_plan_id'=>$post['esop_plan_id'],'esop_bucket_id'=>$post['esop_bucket_id'],'annual_ctc'=>$post['annual_ctc'],'grant_price'=>$post['grant_price'],'option_granted'=>$post['option_granted'],'discount_per_option'=>$post['discount_per_option'],'total_discount'=>$post['total_discount'],'expected_option_benefit'=>$post['expected_option_benefit'],'total_expected_benefit'=>$post['total_expected_benefit'],'benefit_ctc'=>$post['benefit_ctc'],'note'=>$post['note'],'user_email'=>$post['user_email'], 'esop_connect'=>$post['esop_connect'], 'stock'=>$post['stock'], 'value'=>$post['value'], 'stock_value'=>$post['stock_value'], 'grant_date'=>$this->dateconvertdbformat($post['grant_date']), 'vesting_schedule'=>$vestingScheduleencode);
		if($post['rid'] !=""){
			$esopallocatsql = "SELECT esop_bucket_id, option_granted FROM esop_allocations where company_id = '".$post['company_id']."' AND id ='".$post['rid']."' AND is_active = 0 ";
			$esopallocatres = $callConfig->getRow($esopallocatsql);
			$return['esopallocatres'] = $esopallocatres;

			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$res= $callConfig->updateRecord('esop_allocations',$insertary,'id',$post['rid']);
			$return['res'] = $res;
			$rid = $post['rid'];

			
			$updatesql = "UPDATE esop_plan_buckets SET allocated_shares = allocated_shares  - ".$esopallocatres->option_granted." , unallocated_shares = unallocated_shares + ".$esopallocatres->option_granted." WHERE company_id = ".$post['company_id']." AND id = '".$esopallocatres->esop_bucket_id."' AND is_active = 0";
			$callConfig->executeQuery($updatesql);
			$return['updatesql1'] = $updatesql;

		}else{
			$insertary['is_active']=0;
			$insertary['current_status']=0;;
			$insertary['created_by']=$post['user_id'];
			$insertary['created_date']=date("Y-m-d H:i:s");
			$insertary['user_id'] = $userid;
			$res = $callConfig->insertRecord('esop_allocations',$insertary);
			$return['res'] = $res;
			$rid = $res['LASTID'];
		}
		$updatesql1 = "UPDATE esop_plan_buckets SET allocated_shares = allocated_shares  + ".$post['option_granted']." , unallocated_shares = unallocated_shares - ".$post['option_granted']." WHERE company_id = ".$post['company_id']." AND id = '".$post['esop_bucket_id']."' AND is_active = 0";
		$callConfig->executeQuery($updatesql1);
		$return['updatesql'] = $updatesql;
		return $return;
	}


	function getESOPReports($get){
		global $callConfig;
		if($get['type']=="ALL"){

			$usertype = $investortype = array();
			$company_contribution_userssql = "SELECT id, display_name, user_type, investor_type FROM company_contribution_users WHERE company_id = '".$get['company_id']."' AND is_active=0";
	    	$company_contribution_usersres = $callConfig->getAllRows($company_contribution_userssql);
	    	$companycontributionusers = array();
	    	foreach($company_contribution_usersres as $ccu){
	    		$usertype[$ccu->id] =$ccu->user_type;
	    		$investortype[$ccu->id] =$ccu->investor_type;
	    	}


			$companyvaluationssql = "SELECT id, valuation_name, valuation_date, company_value, old_share_qty,valuation_shareholding, new_share_qty, total_share_qty, per_share_value, notes FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date ASC";
			$companyvaluationsres = $callConfig->getAllRows($companyvaluationssql);
			$return['company_valuations_all'] = $companyvaluationsres;

			$cvid =  array();
	    	foreach($companyvaluationsres as $result){
	    		$cvid[$result->id]= $result->id;
	    	}
	    	$cvids = implode(',', $cvid);

	    	$company_valuation_distributionssql = "SELECT cvd.id, cvd.company_contribution_user_id, cvd.distributer_name, cvd.distribute_percentage, cvd.total_amount, cvd.dilution, cvd.old_shares, cvd.new_shares, cvd.total_shares, cvd.dilution, cvd.equity_shares_percentage, cvd.equity_shares, cvd.equity_shares_amount, cvd.preference_shares_percentage, cvd.preference_shares, cvd.preference_shares_amount, cvd.company_valuation_id FROM company_valuation_distributions cvd WHERE cvd.company_id = '".$get['company_id']."' AND cvd.company_valuation_id IN(".$cvids.") AND cvd.is_active=0 ";
	    	$company_valuation_distributionsres = $callConfig->getAllRows($company_valuation_distributionssql);
	    	$percentagecheck =$sharescheck =array();
	    	foreach($company_valuation_distributionsres as $result1){
	    		if($sharescheck[$result1->company_contribution_user_id] !=$result1->total_shares){ // any change percentage
		    		$sharescheck[$result1->company_contribution_user_id] = $result1->total_shares;
		    		
		    		$result1->user_type = $usertype[$result1->company_contribution_user_id];
		    		$result1->investor_type = $investortype[$result1->company_contribution_user_id];
		    		$cvd[$result1->company_valuation_id][] =$result1;
	    		}
	    		$percentagecheck[$result1->company_contribution_user_id] = $result1->distribute_percentage;
	    	}
	    	$return['percentagecheck'] = $percentagecheck;
	    	$return['company_valuation_distributions'] = $cvd;

	    	$typsql = " SELECT genc_detail_code, genc_detail_name  FROM  general_code_details WHERE company_id = '".$get['company_id']."' AND is_active ='0' AND genc_code ='DISTRIBUTIONTYPES' ORDER BY id ASC";
	    	$typsres = $callConfig->getAllRows($typsql);
	    	$return['contribution_users_type'] = $typsres;

	    	$itypsql = " SELECT genc_detail_code, genc_detail_name  FROM  general_code_details WHERE company_id = '".$get['company_id']."' AND is_active ='0' AND genc_code ='ESOPINVESTERTYPE' ORDER BY id ASC";
	    	$itypres = $callConfig->getAllRows($itypsql);
	    	$return['invester_type'] = $itypres;


		}else{
	    	$companyvaluationssql = "SELECT id, valuation_name FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date ASC";
	    	$companyvaluationsres = $callConfig->getAllRows($companyvaluationssql);
	    	$return['company_valuations_all'] = $companyvaluationsres;

	    	//$companyvaluationssql = "SELECT id, valuation_name, valuation_date, company_value, old_share_qty,valuation_shareholding, new_share_qty, total_share_qty, per_share_value, notes FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date DESC limit 1";
	    	$companyvaluationssql = "SELECT id, valuation_name, valuation_date, company_value, old_share_qty,valuation_shareholding, new_share_qty, total_share_qty, per_share_value, notes FROM company_valuations WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active=0";

	    	$companyvaluationssingle = $callConfig->getRow($companyvaluationssql);
	    	$return['company_valuations'] = $companyvaluationssingle;

	    	$company_valuation_distributionssql = "SELECT cvd.id, cvd.company_contribution_user_id, cvd.distributer_name, cvd.distribute_percentage, cvd.total_amount, cvd.dilution, cvd.old_shares, cvd.new_shares, cvd.total_shares, cvd.dilution, cvd.equity_shares_percentage, cvd.equity_shares, cvd.equity_shares_amount, cvd.preference_shares_percentage, cvd.preference_shares, cvd.preference_shares_amount, (SELECT cv.valuation_name FROM company_valuation_distributions cvd1, company_valuations cv WHERE cvd1.company_id = '".$get['company_id']."' AND cvd1.company_id = cv.company_id AND cvd1.company_valuation_id = cv.id AND cvd1.is_active = 0 AND cvd1.is_active = cv.is_active AND cvd1.distribute_percentage !=0 AND  cvd1.company_contribution_user_id = cvd.company_contribution_user_id ORDER BY cv.valuation_date ASC limit 1) start_contribution_valuation FROM company_valuation_distributions cvd WHERE cvd.company_id = '".$get['company_id']."' AND cvd.company_valuation_id = '".$companyvaluationssingle->id."' AND cvd.is_active=0 ";
	    	//
	    	$company_valuation_distributionsres = $callConfig->getAllRows($company_valuation_distributionssql);
	    	$return['company_valuation_distributions_sql'] = $company_valuation_distributionssql;
	    	$return['company_valuation_distributions'] = $company_valuation_distributionsres;


			// $esopallocationsql = "SELECT ea.id, (ea.annual_ctc+ea.total_expected_benefit) as total, ea.emp_code, ea.emp_name, ep.date_of_grant  FROM esop_allocations ea, esop_plans ep WHERE ea.company_id =  '".$get['company_id']."' AND ea.company_id = ep.company_id AND ea.esop_plan_id = ep.id AND ea.is_active = ep.is_active AND ea.is_active = 0 ORDER BY ep.date_of_grant ASC";
	  //   	$esopallocationres = $callConfig->getAllRows($esopallocationsql);
	  //   	$return['esop_allocations'] = $esopallocationres;
	    }	


    	return $return;
	}
	function getESOPAdminDashboard($get){
		global $callConfig;

		if($get['result_type']== "USER"){

			$esopplanssql = "SELECT * FROM esop_allocations WHERE company_id = '".$get['company_id']."' AND user_id = '".$get['user_id']."' AND is_active=0";
	    	$esopallocationsres = $callConfig->getAllRows($esopplanssql);
	    	$return['esop_allocations'] = $esopallocationsres;
	    	$esopplansid =$esopplanbucketsid =  array();
	    	foreach($esopallocationsres as $esopplans){
	    		$esopallocationid[$esopplans->id]= $esopplans->id;
	    		$esopplansid[$esopplans->esop_plan_id]= $esopplans->esop_plan_id;
	    		$esopplanbucketsid[$esopplans->esop_bucket_id]= $esopplans->esop_bucket_id;
	    	}
	    	$esopplansids = implode(',', $esopplansid);
	    	$esopplanbucketsids = implode(',', $esopplanbucketsid);
	    	$esopallocationids = implode(',', $esopallocationid);

	    	$esopplanssql = "SELECT id,plan_name FROM esop_plans WHERE company_id = '".$get['company_id']."' AND id IN(".$esopplansids.") AND is_active=0";
	    	$esopplansres = $callConfig->getAllRows($esopplanssql);
	    	foreach($esopplansres as $esopplan){
	    		$esopplansname[$esopplan->id] = $esopplan->plan_name;
	    	}
	    	$return['esop_plans'] = $esopplansname;

	    	$esopplanbucketssql = "SELECT id, bucket_name FROM esop_plan_buckets WHERE company_id = '".$get['company_id']."' AND id IN(".$esopplanbucketsids.") AND is_active=0";
	    	$esopplanbucketsres = $callConfig->getAllRows($esopplanbucketssql);
	    	foreach($esopplanbucketsres as $esopplanbucket){
	    		$esopplanbuckets[$esopplanbucket->id] = $esopplanbucket->bucket_name;
	    	}
	    	$return['esop_plan_buckets'] = $esopplanbuckets;

	    	$esopplanscntsql = "SELECT COUNT(id) as total_cnt, SUM(IF(current_status =".ST_APPROVED.",1,0)) as approved_cnt, SUM(IF(current_status !=".ST_APPROVED.",1,0)) as pendding_cnt, SUM(IF(current_status=".ST_APPROVED.",option_granted,0)) as option_granted FROM esop_allocations WHERE company_id = '".$get['company_id']."' AND id IN(".$esopallocationids.") AND is_active=0";
	    	$esopplanscntres = $callConfig->getRow($esopplanscntsql);
	    	$return['esop_allocations_cnt'] = $esopplanscntres;


	    	$esopvestingschedulecntsql = "SELECT SUM(allocation_share) total_share, SUM(IF(current_status = ".ST_APPROVED.",allocation_share,0)) allocated_share, SUM(IF(current_status = 0 OR current_status = ".ST_REJECTED.",allocation_share,0)) un_allocated_share, SUM(total_amount) as total_ammount, SUM(IF(current_status = ".ST_APPROVED.",total_amount,0)) allocated_ammount, SUM(IF(current_status = 0 OR current_status = ".ST_REJECTED.",total_amount,0)) un_allocated_ammount FROM esop_vesting_schedule WHERE company_id = '".$get['company_id']."' AND esop_allocation_id IN(".$esopallocationids.") AND is_active=0";
	    	$esopvestingschedulecntres = $callConfig->getRow($esopvestingschedulecntsql);
	    	$return['esop_vesting_schedule_cnt'] = $esopvestingschedulecntres;



		}else{

			$companyvaluationssql = "SELECT id, valuation_name, valuation_date, company_value,  new_share_qty, total_share_qty, per_share_value FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date ASC LIMIT ".$get['limit'];
	    	$companyvaluationsres = $callConfig->getAllRows($companyvaluationssql);
	    	$return['company_valuations'] = $companyvaluationsres;

	    	$esopplanssql = "SELECT id, plan_name, date_of_grant, company_valuation, share FROM esop_plans WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY date_of_grant ASC LIMIT ".$get['limit'];
	    	$esopplansres = $callConfig->getAllRows($esopplanssql);
	    	$return['esop_plans'] = $esopplansres;

	    	$typsql = " SELECT genc_detail_code, genc_detail_name  FROM  general_code_details WHERE company_id = '".$get['company_id']."' AND is_active ='0' AND genc_code ='DISTRIBUTIONTYPES' AND genc_detail_code != 'EMP' ORDER BY id ASC";
	    	$typsres = $callConfig->getAllRows($typsql);
	    	$return['contribution_users_type'] = $typsres;

	   		$graphsql = "SELECT YEAR(cv.valuation_date) year, cvd.distributer_name, sum(cvd.distribute_percentage) total_percentage, sum(cvd.total_amount) as total_amount, sum(cvd.total_shares) as total_shares, ccu.display_name, ccu.user_type FROM company_valuation_distributions cvd, company_contribution_users ccu, company_valuations cv WHERE cvd.company_id = '".$get['company_id']."' AND cvd.company_id = ccu.company_id AND cvd.company_contribution_user_id = ccu.id AND cvd.company_id = cv.company_id AND cvd.company_valuation_id  = cv.id AND cvd.is_active = ccu.is_active AND cvd.is_active = cv.is_active AND cvd.is_active=0 AND cv.valuation_date = (SELECT MAX(scv.valuation_date) FROM company_valuations scv WHERE scv.company_id = '".$get['company_id']."' AND YEAR(scv.valuation_date) = YEAR(cv.valuation_date) AND ccu.user_type !='EMP' AND scv.is_active = 0 GROUP BY YEAR(scv.valuation_date) limit 1) GROUP BY YEAR(cv.valuation_date), ccu.user_type";
	    	$graphres = $callConfig->getAllRows($graphsql);
	    	foreach($graphres as $gp){
	    		$graph[$gp->year][$gp->user_type]=$gp;
	    	}
	    	$return['company_valuation_graph_sql'] = $graphsql;
	    	$return['company_valuation_graph'] = $graph;
//----------------------------------------------------------------
	    	$lastcopanyvaluationsql = "SELECT id,valuation_date, company_value, total_share_qty, per_share_value FROM `company_valuations` WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date DESC LIMIT 1";
	    	$lastcopanyvaluationres = $callConfig->getRow($lastcopanyvaluationsql);
	    	$return['last_company_valuations'] = $lastcopanyvaluationres;

	    	$lastcopanyvaluationdistrsql = "SELECT sum(cvd.distribute_percentage) total_percentage, sum(cvd.total_amount) as total_amount, sum(cvd.total_shares) as total_shares, ccu.display_name, ccu.user_type FROM company_valuation_distributions cvd, company_contribution_users ccu WHERE cvd.company_id = '".$get['company_id']."' AND cvd.company_id = ccu.company_id AND cvd.company_contribution_user_id = ccu.id AND cvd.is_active = ccu.is_active AND cvd.company_valuation_id = '".$lastcopanyvaluationres->id."' AND cvd.is_active=0 GROUP BY ccu.user_type";
	    	$lastcopanyvaluationdistrres = $callConfig->getAllRows($lastcopanyvaluationdistrsql);
	    	$return['last_company_valuation_distributions'] = $lastcopanyvaluationdistrres;
//---------------------------------------------------------------------

	    	$totalusersql = "SELECT SUM(IF(user_type ='EMP',1,0)) as employee_cnt, SUM(IF(user_type ='I',1,0)) as invester_cnt, SUM(IF(user_type ='F',1,0)) as founder_cnt  FROM company_contribution_users WHERE company_id = '".$get['company_id']."' AND is_active=0";
	    	$totaluserres = $callConfig->getRow($totalusersql);
	    	$return['total_users_with_type_cnt'] = $totaluserres;

	    	$esopplanssql = "SELECT SUM(id) as cnt  FROM esop_plans WHERE company_id = '".$get['company_id']."' AND is_active=0";
	    	$esopplansres = $callConfig->getRow($esopplanssql);
	    	$return['esop_plans_cnt'] = $esopplansres->cnt;

	    	$esop_allocationssql = "SELECT SUM(IF(current_status =".ST_APPROVED.",1,0)) as approved_cnt, SUM(IF(current_status !=".ST_APPROVED.",1,0)) as pendding_cnt  FROM esop_allocations WHERE company_id = '".$get['company_id']."' AND is_active=0";
	    	$esop_allocationsres = $callConfig->getRow($esop_allocationssql);
	    	$return['esop_allocations_cnt'] = $esop_allocationsres;

	    	$trendgraphsql = "SELECT valuation_date, company_value, per_share_value FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date ASC";
	    	$trendgraphres = $callConfig->getAllRows($trendgraphsql);
	    	$return['company_valuations_trend'] = $trendgraphres;

	    	$esop_plansgraphsql = "SELECT date_of_grant, company_valuation, share_price_at_grant_time FROM esop_plans WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY date_of_grant ASC";
	    	$esop_plansgraphres = $callConfig->getAllRows($esop_plansgraphsql);
	    	$return['esop_plans_trend'] = $esop_plansgraphres;

	    	$esopplanssql = "SELECT * FROM esop_allocations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY created_date ASC";
	    	$esopallocationsres = $callConfig->getAllRows($esopplanssql);
	    	$return['esop_allocations'] = $esopallocationsres;
	    }	
    	return $return;
	}
	function getValuationReport($get){
		global $callConfig;
		
		$company_contribution_userssql = "SELECT id, display_name FROM company_contribution_users WHERE company_id = '".$get['company_id']."' AND is_active=0";
    	$company_contribution_usersres = $callConfig->getAllRows($company_contribution_userssql);
    	$companycontributionusers = array();
    	foreach($company_contribution_usersres as $ccu){
    		$companycontributionusers[$ccu->id] =$ccu->display_name;
    	}
    	$return['company_contribution_users'] = $companycontributionusers;

		$companyvaluationssql = "SELECT id, valuation_name, valuation_date, company_value,  new_share_qty,total_share_qty,per_share_value FROM company_valuations WHERE company_id = '".$get['company_id']."' AND is_active=0 ORDER BY valuation_date ASC";
    	$companyvaluationsres = $callConfig->getAllRows($companyvaluationssql);
    	$return['company_valuations'] = $companyvaluationsres;

    	$companyvaluationsdistsql = "SELECT id, company_valuation_id, company_contribution_user_id, total_shares, total_amount,  distribute_percentage FROM company_valuation_distributions WHERE company_id = '".$get['company_id']."' AND distribute_percentage !='0' AND is_active=0";
    	$companyvaluationsdistres = $callConfig->getAllRows($companyvaluationsdistsql);
    	$return['company_valuation_distributions'] = $companyvaluationsdistres;

    	return $return;

	}
	function getESOPDetails($get){
		global $callConfig;
		
		// $esop_planscntsql = "SELECT count(*) as esop_cnt, SUM(share) as total_share_cnt, (SELECT SUM(epb.allocated_shares) FROM esop_plan_buckets epb WHERE ep.company_id = epb.company_id AND ep.is_active = epb.is_active) as allocated_shares_cnt FROM esop_plans ep WHERE ep.company_id = '".$get['company_id']."' AND ep.is_active=0";
  //   	$esop_planscntres = $callConfig->getRow($esop_planscntsql);
  //   	$return['esop_plans_cnt'] = $esop_planscntres;

		$esop_planssql = "SELECT ep.id, ep.plan_name, ep.date_of_grant, ep.company_valuation, ep.share, ep.share_price_at_grant_time FROM esop_plans ep WHERE ep.company_id = '".$get['company_id']."' AND ep.is_active=0";
    	$esop_plansres = $callConfig->getAllRows($esop_planssql);
    	$return['esop_plans'] = $esop_plansres;
    	$esopplansid = '';
    	$esopplansallids = array(); 
    	foreach($esop_plansres as $esopp){
    		if($esopplansid ==""){
    			$esopplansid = $esopp->id;
    		}
    		$esopplansallids[$esopp->id] =$esopp->id;
    	}
    	$esopplansallid = implode(',', (array)$esopplansallids);
    	
    	$esopdeatilcntsql = "SELECT epb.esop_plans_id, COUNT(*) as total_bucket_cnt, SUM(epb.allocated_shares) as allocated_shares, SUM(epb.unallocated_shares) as unallocated_shares FROM esop_plan_buckets epb WHERE epb.company_id = '".$get['company_id']."' AND epb.esop_plans_id IN(".$esopplansallid.") AND epb.is_active = 0 GROUP BY epb.esop_plans_id";
    	$esopdeatilcntres = $callConfig->getAllRows($esopdeatilcntsql);
    	$esopdetails = array();
    	foreach($esopdeatilcntres as $esoppb){
    		$esopdetails[$esoppb->esop_plans_id]= $esoppb;
    	}
    	$return['esop_plans_details'] = $esopdetails;



    	if($get['esop_id'] !=""){
    		$esopplansid = $get['esop_id'];
    	}
    	$esop_allocationssql = "SELECT ea.id, ea.date_of_joining, ea.designation, ea.annual_ctc, ea.esop_bucket_id, ea.option_granted, ccu.display_name, ccu.image, (SELECT SUM(evs.allocation_share) as allocation_share FROM esop_vesting_schedule evs WHERE ea.company_id = evs.company_id AND evs.current_status = ".ST_APPROVED." AND ea.id =evs.esop_allocation_id AND ea.is_active = evs.is_active) as allocation_share FROM esop_allocations ea, company_contribution_users ccu WHERE ea.company_id = '".$get['company_id']."' AND ea.company_id = ccu.company_id AND ea.esop_plan_id =  '".$esopplansid."' AND ea.company_contribution_user_id = ccu.id AND ea.is_active=ccu.is_active AND ea.is_active=0 ORDER BY ea.created_date ASC";
	    $esop_allocationsres = $callConfig->getAllRows($esop_allocationssql);
	    $return['esop_allocations'] = $esop_allocationsres;

	    $return['esop_allocations_cnt']['cnt'] = count((array)$esop_allocationsres);

		$esopplanbucketssql = "SELECT id, esop_plans_id, bucket_name, grant_price, discount, expected_option_benefit, bs_option_value, options_granted, total_discount, discount_per_option, vesting_schedule, allocated_shares, unallocated_shares FROM esop_plan_buckets WHERE company_id = '".$get['company_id']."' AND esop_plans_id =  '".$esopplansid."' AND is_active=0";
    	$esopplansres = $callConfig->getAllRows($esopplanbucketssql);
    	$return['esop_plan_buckets'] = $esopplansres;
    	$options_granted =$allocated_shares = $unallocated_shares =  0; 
    	foreach($esopplansres as $esopp){
    		$options_granted += $esopp->options_granted;
    		$allocated_shares += $esopp->allocated_shares;
    		$unallocated_shares += $esopp->unallocated_shares;
    	}
    	$return['esop_allocations_cnt']['total_share'] = $options_granted;
    	$return['esop_allocations_cnt']['total_allocated_shares'] = $allocated_shares;
    	$return['esop_allocations_cnt']['total_unallocated_shares'] = $unallocated_shares;

    	
    	return $return;
	}

}