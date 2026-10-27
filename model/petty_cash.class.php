<?php 
class pettyCashUserClass extends userClass{
	function pettyCashDashboard($get){
		global $callConfig;
		$return = array();
		$todaydate = date("Y-m-d"); 
		if($get['admin_type'] != 1 OR $get['admin_access_employee'] == 'Y'){
			$condition .= " AND pcbo.owner_id = '".$get['user_id']."'";
		}
		$cntsql = "SELECT * FROM ((SELECT count(*) as total_petty_cash_box_topup_requests FROM petty_cash_box_topup_requests WHERE company_id = '".$get['company_id']."' AND is_active =0) as total_petty_cash_box_topup_requests , (SELECT count(*) as total_petty_cash FROM petty_cash WHERE company_id = '".$get['company_id']."' and is_active =0) as total_petty_cash, (SELECT count(*) as total_petty_cash_box_owner FROM petty_cash_box_owner pcbo, petty_cash_boxes pcb WHERE pcbo.company_id = '".$get['company_id']."' AND pcbo.company_id = pcb.company_id AND pcbo.pc_box_id = pcb.id  AND pcbo.is_active = pcb.is_active ".$condition." AND pcbo.is_active = 0) as total_petty_cash_box_owner)";
		$cntres = $callConfig->getRow($cntsql);
		$return['total_cnt']  = $cntres;
		$return['topuprequest'] = $this->getPettyCashBoxTopUpRequests($get);
		$return['expense'] = $this->getExpensePettyCash($get);

		$transapprlogsql = "SELECT tal.request_date,tal.response_date, tal.response_type, pay.transaction_no, pay.payment_date, pay.amount, pay.originator,pay.id FROM transaction_approval_logs tal, payments pay WHERE tal.company_id = '".$get['company_id']."'  AND tal.company_id = pay.company_id AND (tal.transaction_table = 'petty_cash_box_topup_requests' OR AND tal.transaction_table = 'petty_cash') AND tal.user_id = '".$get['user_id']."' AND tal.transaction_id = pay.id AND pay.is_active = 0";
		$transapprlogres = $callConfig->getAllRows($transapprlogsql);
		$return['transaction_approval_logs'] = $transapprlogres;


		$query = "SELECT id, payment_terms, total_amount, party_id, due_date FROM petty_cash WHERE company_id = ".$get['company_id']." AND (pending_amount IS NULL OR pending_amount !=0) AND is_active = 0";
		$return['bills_sql'] = $query;
		$return['bills'] = $callConfig->getAllRows($query);

		// $query = "SELECT ptb.id, ptb.total_amount, ptb.month_start_date, ptd.customer_id FROM property_tenant_billings ptb, property_tenant_details ptd WHERE ptb.company_id = ".$get['company_id']." AND ptb.company_id = ptd.company_id AND ptb.property_tenant_detail_id = ptd.id and ptb.month_start_date > '".$todaydate."' AND ptb.is_active = ptd.is_active AND ptb.is_active = 0";
		// $return['projection_report_sql'] = $query;
		// $return['projection_report'] = $callConfig->getAllRows($query);


		//$frequencysql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='TASKFREQUENCY' AND is_active=0"; 
		// $frequencyres = $callConfig->getAllRows($frequencysql);
		// $topup_frequency = array();
		// foreach($frequencyres as $frq){
		// 	$topup_frequency[$frq->genc_detail_code] = $frq->genc_detail_name;
		// }
		// $return['topup_frequency'] = $topup_frequency;
		// $sql = "SELECT pcbo.id, pcbo.pc_box_id, pcbo.pc_limit, pcbo.pc_type_id, pcb.pc_box_code, pcb.pc_box_description, pcb.topup_frequency, pcb.topup_amount FROM petty_cash_box_owner pcbo, petty_cash_boxes pcb WHERE pcbo.company_id = '".$get['company_id']."' AND pcbo.company_id = pcb.company_id AND pcbo.pc_box_id = pcb.id  AND pcbo.is_active = pcb.is_active ".$condition." AND pcbo.is_active = 0";
		// $res=$callConfig->getAllRows($sql);
		// $return['petty_cash_box_owner'] = $res;

		return $return;
	}
	function getPettyCashBoxTopUpRequests($get){
		global $callConfig;
		if($get['admin_type'] != 1 OR $get['admin_access_employee'] == 'Y'){
			$condition .= " AND pcbtr.originator = '".$get['user_id']."'";
		}
		if($get['petty_cash_box_id'] !=""){
			$condition .= " AND pcbtr.petty_cash_boxe_id = '".$get['petty_cash_box_id']."'";
		}
		$sql = "SELECT pcbtr.id, pcbtr.originator, pcbtr.request_amount, pcbtr.transaction_date, pcbtr.description, pcbtr.current_status, pcb.pc_box_code, pcb.pc_box_description  FROM petty_cash_box_topup_requests pcbtr, petty_cash_boxes pcb WHERE pcbtr.company_id = '".$get['company_id']."' ".$condition." AND pcbtr.company_id = pcb.company_id AND pcbtr.petty_cash_boxe_id = pcb.id AND pcbtr.is_active = pcb.is_active AND pcbtr.is_active = 0";
		$res=$callConfig->getAllRows($sql);
		$return['petty_cash_box_topup_requests']= $res;

		$get['table'] ="petty_cash_box_topup_requests";
		$status_progress = $this->getStatusProgressList($get);
		foreach($status_progress as $statusprogress){
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}
		return $return;	
	}
	function getExpensePettyCash($get){
		global $callConfig;
		if($get['admin_type'] != 1 OR $get['admin_access_employee'] == 'Y'){
			$condition .= " AND originator = '".$get['user_id']."'";
		}
		if($get['petty_cash_box_id'] !=""){
			$condition .= " AND ref_id = '".$get['petty_cash_box_id']."'";
		}
		// if($get['petty_cash_box_id'] != ""){
		// 	$condition .= " AND pcbtr.originator = '".$get['petty_cash_box_id']."'";
		// }
		$sql = "SELECT id, transaction_number, transaction_date, originator, total_amount, due_date, current_status  FROM `petty_cash` WHERE company_id = '".$get['company_id']."' ".$condition." AND is_active = 0";
		$res=$callConfig->getAllRows($sql);
		$return['petty_cash']= $res;

		$get['table'] ="petty_cash";
		$status_progress = $this->getStatusProgressList($get);
		foreach($status_progress as $statusprogress){
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}
		return $return;	
	}
	function insertPettyCashRequests($get, $post){
		global $callConfig;
		$return['post']= $post;
		$final_amount = ($post['final_amount'] !='0' AND $post['final_amount'] !='0')?$post['final_amount']:$post['request_amount'];
		$insertary=array('petty_cash_boxe_id'=>$post['petty_cash_boxe_id'],'request_amount'=>$post['request_amount'],'final_amount'=>$final_amount,'transaction_date'=>$post['transaction_date'],'request_type'=>$post['request_type'],'description'=>$post['description']);
		if($post['rid'] !=""){
			$insertary['updated_date']=date('Y-m-d h:i:s');
			$insertary['updated_by']=$post['user_id'];
			$res= $callConfig->updateRecord('petty_cash_box_topup_requests',$insertary,'id',$post['rid']);
			$return['res'] = $res;
			$property_owner_id = $post['rid'];
		}else{
			$insertary['originator']=$post['user_id'];
			$insertary['company_id']=$post['company_id'];
			$insertary['is_active']=0;
			$insertary['current_status']=0;;
			$insertary['created_by']=$post['user_id'];
			$insertary['created_date']=date("Y-m-d H:i:s");
			$res = $callConfig->insertRecord('petty_cash_box_topup_requests',$insertary);
			$return['res'] = $res;
			$property_owner_id = $res['LASTID'];
		}
		return $return;
	}
	function getPettyCashRequests($get){
		global $callConfig;
		$return['get']= $get;
		//if($get['rid'] !=""){
			$pcrsql = "SELECT * FROM petty_cash_box_topup_requests WHERE company_id = '".$get['company_id']."' AND id = '".$get['rid']."' AND is_active = 0";
			$pcrres=$callConfig->getRow($pcrsql);
			$return['petty_cash_box_topup_requests'] = $pcrres;
		//}
		if($pcrres->originator == $get['user_id'] || $pcrres->originator ==""){
			$condition ="  AND pcbo.owner_id = '".$get['user_id']."'";
		}
		$ptcbsql = "SELECT pcbo.id, pcbo.pc_box_id, pcbo.pc_limit, pcbo.pc_type_id, pcb.pc_box_code, pcb.pc_box_description, pcb.topup_frequency, pcb.topup_amount FROM petty_cash_box_owner pcbo, petty_cash_boxes pcb WHERE pcbo.company_id = '".$get['company_id']."' AND pcbo.company_id = pcb.company_id AND pcbo.pc_box_id = pcb.id  AND pcbo.is_active = pcb.is_active ".$condition." AND pcbo.is_active = 0";
		$ptcbres=$callConfig->getAllRows($ptcbsql);
		$return['petty_cash_boxes'] = $ptcbres;
		
		return $return;
	}
	function getPettyCashTopUpDashboardChart($get){
		global $callConfig;
		$exploderole = explode(',', $get['roleshortcode']);
		if(!in_array(PETTY_CASH_ADMIN_ROLE, $exploderole) OR $get['admin_type'] != '1' OR $get['admin_access_employee'] == 'Y'){
			$condition = " AND originator = ".$get['user_id'];
		}
		$year = $get['reimbursementsyear'];
		$startdate = date($year.'-01-01');
		$lastdate = date($year.'-31-12');
		$montharay = array('1'=>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09','10'=>'10','11'=>'11','12'=>'12');
		$expensessql = "SELECT sum(request_amount) as total_amount, sum(pending_amount) as pending_amount, MONTH(transaction_date) as month,YEAR(transaction_date) as year FROM `petty_cash_box_topup_requests` WHERE company_id = '".$get['company_id']."' ".$condition." AND transaction_date >= '".$startdate."' AND transaction_date <= '".$lastdate."' AND is_active = 0 GROUP BY YEAR(transaction_date), MONTH(transaction_date)";
		$expensesres = $callConfig->getAllRows($expensessql);
		foreach($expensesres as $empexp){
			$expensetotal[$empexp->month]= $empexp->total_amount;
			$expensepending[$empexp->month]= $empexp->pending_amount;
		}
		foreach($montharay as $monthary=>$moth){
			$expensetotalamt = isset($expensetotal[$monthary])?$expensetotal[$monthary]:"0";
			$expensependingamt = isset($expensepending[$monthary])?$expensepending[$monthary]:"0";
			$totalpendin = $expensetotalamt-$expensependingamt;
			$expensemontharray[] = array("date"=>date($year.'-'.$moth.'-01'),"market1"=>round($expensetotalamt),"market2"=>round($totalpendin));
		}
		return $expensemontharray;
	}
	function getPettyCashDashboardChart($get){
		global $callConfig;
		$exploderole = explode(',', $get['roleshortcode']);
		if(!in_array(PETTY_CASH_ADMIN_ROLE, $exploderole) OR $get['admin_type'] != '1' OR $get['admin_access_employee'] == 'Y'){
			$condition = " AND originator = ".$get['user_id'];
		}
		$year = $get['reimbursementsyear'];
		$startdate = date($year.'-01-01');
		$lastdate = date($year.'-31-12');
		$montharay = array('1'=>'01','2'=>'02','3'=>'03','4'=>'04','5'=>'05','6'=>'06','7'=>'07','8'=>'08','9'=>'09','10'=>'10','11'=>'11','12'=>'12');
		$expensessql = "SELECT sum(total_amount) as total_amount, sum(pending_amount) as pending_amount, MONTH(due_date) as month,YEAR(due_date) as year FROM `petty_cash` WHERE company_id = '".$get['company_id']."' ".$condition." AND due_date >= '".$startdate."' AND due_date <= '".$lastdate."' AND is_active = 0 GROUP BY YEAR(due_date), MONTH(due_date)";
		$expensesres = $callConfig->getAllRows($expensessql);
		foreach($expensesres as $empexp){
			$expensetotal[$empexp->month]= $empexp->total_amount;
			$expensepending[$empexp->month]= $empexp->pending_amount;
		}
		foreach($montharay as $monthary=>$moth){
			$expensetotalamt = isset($expensetotal[$monthary])?$expensetotal[$monthary]:"0";
			$expensependingamt = isset($expensepending[$monthary])?$expensepending[$monthary]:"0";
			$totalpendin = $expensetotalamt-$expensependingamt;
			$expensemontharray[] = array("date"=>date($year.'-'.$moth.'-01'),"market1"=>round($expensetotalamt),"market2"=>round($totalpendin));
		}
		return $expensemontharray;
	}
	function getPettyCashBalances($get,$petty_cash){
		global $callConfig;
		$return = array();
		if($petty_cash->id !=""){
			$pc_box_id =  $petty_cash->ref_id;
			$pt_owner_id = $petty_cash->party_id;
		}else{
			$petty_cashsql = "SELECT id, transaction_number, transaction_date, amount, notes, party_id, ref_id, current_status FROM petty_cash WHERE company_id='".$get['company_id']."' AND id = '".$get['rid']."' AND is_active=0"; 
			$petty_cashres = $callConfig->getRow($petty_cashsql);
			$pc_box_id =  $petty_cashres->ref_id;
			$pt_owner_id = $petty_cashres->party_id;
		}
		$sql = "SELECT closing_balance FROM `petty_cash_balances` WHERE company_id='".$get['company_id']."' AND pc_box_id = '".$pc_box_id."' AND pt_owner_id = '".$pt_owner_id."' ORDER BY balance_date DESC limit 1";
		$ptres = $callConfig->getRow($sql);
		return $ptres;
	}
	function getPettyCash($get){
		global $callConfig;
		$return = array();
		if($get['type'] == "add"){
			$petty_cash_itemssql = "SELECT id, item_name, description FROM petty_cash_items WHERE company_id='".$get['company_id']."' AND is_active=0"; 
			$petty_cash_itemsres = $callConfig->getAllRows($petty_cash_itemssql);
			$return['petty_cash_items'] = $petty_cash_itemsres;

			if($get['rid'] !=""){
				$petty_cashsql = "SELECT id, transaction_number, transaction_date, amount, notes, party_id, ref_id, current_status FROM petty_cash WHERE company_id='".$get['company_id']."' AND id = '".$get['rid']."' AND is_active=0"; 
				$petty_cashres = $callConfig->getRow($petty_cashsql);
				$return['petty_cash'] = $petty_cashres;

				$pc_itemssql = "SELECT id, transaction_id, item_id, item_name, description, total_amount FROM pc_items WHERE company_id='".$get['company_id']."' AND transaction_id = '".$get['rid']."' AND is_active=0"; 
				$pc_itemsres = $callConfig->getAllRows($pc_itemssql);
				$return['pc_items'] = $pc_itemsres;

				$return['petty_cash_balances'] = $this->getPettyCashBalances($get,$petty_cashres);
			}

		}else{ 
			$frequencysql = "SELECT genc_detail_code,genc_detail_name  FROM ".TBL_GENRAL_CODE_DETAILS." WHERE company_id='".$get['company_id']."' AND genc_code='TASKFREQUENCY' AND is_active=0"; 
			$frequencyres = $callConfig->getAllRows($frequencysql);
			$topup_frequency = array();
			foreach($frequencyres as $frq){
				$topup_frequency[$frq->genc_detail_code] = $frq->genc_detail_name;
			}
			$return['topup_frequency'] = $topup_frequency;
			$sql = "SELECT pcbo.id, pcbo.pc_box_id, pcbo.pc_limit, pcbo.pc_type_id, pcb.pc_box_code, pcb.pc_box_description, pcb.topup_frequency, pcb.topup_amount FROM petty_cash_box_owner pcbo, petty_cash_boxes pcb WHERE pcbo.company_id = '".$get['company_id']."' AND pcbo.company_id = pcb.company_id AND pcbo.pc_box_id = pcb.id  AND pcbo.is_active = pcb.is_active ".$condition." AND pcbo.is_active = 0";
			$res=$callConfig->getAllRows($sql);
			$return['petty_cash_box_owner'] = $res;
		}
		return $return;
	}
	function insertPettyCash($get,$post){
		global $callConfig;
		$return['post']= $post;
		$pcbsql = "SELECT pcb.id, pcb.pc_box_code, pcb.pc_box_description, pcbo.owner_id, au.id as au_id, au.display_name, au.user_email, au.user_mobile, au.location_id, au.country_id, au.default_state_id FROM petty_cash_boxes pcb, petty_cash_box_owner pcbo, application_users au WHERE pcb.company_id = '".$post['company_id']."' AND pcb.company_id = pcbo.company_id AND pcb.id = pcbo.pc_box_id  AND pcb.is_active = pcbo.is_active AND pcb.id = '".$post['petty_cash_box_id']."' AND pcb.company_id = au.company_id AND pcbo.owner_id = au.id  AND pcb.is_active = au.is_active AND pcb.is_active = 0";
		$pcbres=$callConfig->getRow($pcbsql);
		$return['petty_cash_boxes'] = $pcbres;

		$transaction_number = $this->toGetApplicationSerialNumber('petty_cash');
		$transaction_date = $this->dateconvertdbformat($post['transaction_date']);
		$insertcust = array('company_id'=>$post['company_id'],'transaction_number'=>$transaction_number,'transaction_date'=>$transaction_date,'originator'=>$post['user_id'],'party_id'=>$pcbres->owner_id,'party_name'=>$pcbres->display_name,'party_email'=>$pcbres->user_email,'party_phone'=>$pcbres->user_mobile,'party_address'=>'','party_ship_adderss'=>'','amount'=>$post['total_amount'],'tax_amount'=>0,'total_amount'=>$post['total_amount'],'due_date'=>$transaction_date,'ref_type'=>'petty_cash_boxes','ref_id'=>$post['petty_cash_box_id'],'ref_date'=>$transaction_date,'pending_amount'=>$post['total_amount'],'notes'=>$post['notes']);
		if($post['rid'] !=""){
			$insertcust['updated_date']=date('Y-m-d h:i:s');
			$insertcust['updated_by']=$post['user_id'];
			$petty_cashres= $callConfig->updateRecord('petty_cash',$insertcust,'id',$post['rid']);
			$return['petty_cash'] = $petty_cashres;
			$petty_cash_id = $post['rid'];
			$updateary = array('is_active'=>null,'updated_date'=>date('Y-m-d h:i:s'),'updated_by'=>$post['user_id']);
			$uppc_items= $callConfig->updateRecord('pc_items',$updateary,'transaction_id',$petty_cash_id);
			$return['uppc_items'] = $uppc_items;
		}else{
			$insertcust['company_id']=$post['company_id'];
			$insertcust['is_active']=0;
			$insertcust['current_status']=0;
			$insertcust['created_by']=$post['user_id'];
			$insertcust['created_date']=date("Y-m-d H:i:s");
			$petty_cashres = $callConfig->insertRecord('petty_cash',$insertcust);
			$return['petty_cash'] = $petty_cashres;
			$petty_cash_id = $petty_cashres['LASTID'];
		}

		foreach($post['item_id'] as $key=>$pcitemid){
			$insertcust = array('company_id'=>$get['company_id'],'transaction_id'=>$petty_cash_id,'item_id'=>$pcitemid,'item_name'=>$post['item_name'][$key],'description'=>$post['item_description'][$key],'purchase_uom'=>'UN','item_quantity'=>'1','item_price'=>$post['item_amt'][$key],'item_amount'=>$post['item_amt'][$key],'tax_amount'=>0,'total_amount'=>$post['item_amt'][$key],'approved_quantity'=>'1','transaction_qty'=>'1','pending_qty'=>'1');
			if($post['pc_item_id'][$key] !=""){
				$insertcust['is_active']='0';
				$insertcust['updated_date']=date('Y-m-d h:i:s');
				$insertcust['updated_by']=$post['user_id'];
				$pc_itemsres= $callConfig->updateRecord('pc_items',$insertcust,'id',$post['pc_item_id'][$key]);
			}else{
				$insertcust['is_active']='0';
				$insertcust['current_status']='0';
				$insertcust['created_by']=$post['user_id'];
				$insertcust['created_date']=date("Y-m-d H:i:s");
				$pc_itemsres = $callConfig->insertRecord('pc_items',$insertcust);
			}
			$return['pc_items'][] = $pc_itemsres;
			
		}
		return $return;
	}
	function insertPettyCashTopUpBalances($post, $balancetype){
		global $callConfig;
		$return['post']= $post;
		$todaydate = date("Y-m-d");
		$transaction_no = $post['transaction_no']; // workflow field used
		$transaction_table = $post['transaction_table'];
		if($transaction_table=="petty_cash_box_topup_requests"){
			$pcbrsql = "SELECT id, originator, petty_cash_boxe_id, final_amount, transaction_date FROM petty_cash_box_topup_requests WHERE company_id='".$post['company_id']."' AND id='".$transaction_no."' AND is_active=0"; 
			$pcbrres = $callConfig->getRow($pcbrsql);
			$return['pcbrsql'] = $pcbrsql;
			$return['pcbrsqlres'] = $pcbrres;
			if($pcbrres->id !=""){
				$pcbsql = "SELECT id, pc_box_id, pt_owner_id, balance_date, debit_amount, credit_amount, opening_balance, closing_balance FROM petty_cash_balances WHERE company_id='".$post['company_id']."' AND pc_box_id='".$pcbrres->petty_cash_boxe_id."' AND pt_owner_id='".$pcbrres->originator."' AND is_active=0 ORDER BY balance_date DESC limit 1"; 
				$pcbres = $callConfig->getRow($pcbsql);
				$return['pcbsqlres'] = $pcbsql;
				$return['pcbres'] = $pcbres;
				if(isset($pcbres->id)){
					$return['balance_date_todaydate'] = $pcbres->balance_date."==".$pcbrres->transaction_date;
					if($pcbres->balance_date == $todaydate){
						$updatequery =  "UPDATE petty_cash_balances SET credit_amount = credit_amount".$balancetype.$pcbrres->final_amount.",closing_balance= closing_balance".$balancetype.$pcbrres->final_amount." WHERE id = '".$pcbres->id."'";
						$samedate = $callConfig->executeQuery($updatequery);
						$return['samedate'] = $samedate;
					}else{
						$closing_balance = $pcbres->closing_balance.$balancetype.$pcbrres->final_amount;
						$insertcust = array('company_id'=>$post['company_id'],'pt_owner_id'=>$pcbrres->originator,'pc_box_id'=>$pcbrres->petty_cash_boxe_id,'balance_date'=>$todaydate,'debit_amount'=>0,'credit_amount'=>$pcbrres->final_amount,'opening_balance'=>$pcbres->closing_balance,'closing_balance'=>$closing_balance,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
						$pc_balance = $callConfig->insertRecord('petty_cash_balances',$insertcust);
						$return['pc_balance'] = $pc_balance;
					}
				}else{
					$insertcust = array('company_id'=>$post['company_id'],'pt_owner_id'=>$pcbrres->originator,'pc_box_id'=>$pcbrres->petty_cash_boxe_id,'balance_date'=>$todaydate,'debit_amount'=>0,'credit_amount'=>$pcbrres->final_amount,'opening_balance'=>0,'closing_balance'=>$pcbrres->final_amount,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
					$pc_itemsres = $callConfig->insertRecord('petty_cash_balances',$insertcust);
					$return['pc_itemsres'] = $pc_itemsres;
				}
			}else{
				$return['not_inside'] = "Y";
			}
		}
		return $return;
	}

	function insertPettyCashBalances($post, $balancetype){
		global $callConfig;
		$return['post']= $post;
		$todaydate = date("Y-m-d");
		$transaction_no = $post['transaction_no']; // workflow field used
		$transaction_table = $post['transaction_table'];
		if($transaction_table=="petty_cash"){
			$pcbrsql = "SELECT id, party_id, ref_id, total_amount, transaction_date FROM petty_cash WHERE company_id='".$post['company_id']."' AND id='".$transaction_no."' AND is_active=0"; 
			$pcbrres = $callConfig->getRow($pcbrsql);
			$return['pcbrsql'] = $pcbrsql;
			$return['pcbrsqlres'] = $pcbrres;
			if($pcbrres->id !=""){
				$pcbsql = "SELECT id, pc_box_id, pt_owner_id, balance_date, debit_amount, credit_amount, opening_balance, closing_balance FROM petty_cash_balances WHERE company_id='".$post['company_id']."' AND pc_box_id='".$pcbrres->ref_id."' AND pt_owner_id='".$pcbrres->party_id."' AND is_active=0 ORDER BY balance_date DESC limit 1"; 
				$pcbres = $callConfig->getRow($pcbsql);
				$return['pcbsqlres'] = $pcbsql;
				$return['pcbres'] = $pcbres;
				if(isset($pcbres->id)){
					$return['balance_date_transaction_date'] = $pcbres->balance_date."== ".$pcbrres->transaction_date;


					$balance_date_str = new DateTime($pcbres->balance_date);
     				$transaction_date_str    = new DateTime($pcbrres->transaction_date);
    
   


					if($pcbres->balance_date == $pcbrres->transaction_date){
						
						$updatequery =  "UPDATE petty_cash_balances SET debit_amount = debit_amount".$balancetype.$pcbrres->total_amount.",closing_balance= closing_balance".$balancetype.$pcbrres->total_amount." WHERE id = '".$pcbres->id."'";
						$samedate = $callConfig->executeQuery($updatequery);
						$return['samedate'] = $samedate;
					
					}elseif($balance_date_str > $transaction_date_str){  // back date entry


						$beforepcbsql = "SELECT id, pc_box_id, pt_owner_id, balance_date, debit_amount, credit_amount, opening_balance, closing_balance FROM petty_cash_balances WHERE company_id='".$post['company_id']."' AND pc_box_id='".$pcbrres->ref_id."' AND pt_owner_id='".$pcbrres->party_id."' AND balance_date >= '".$pcbrres->transaction_date."' AND is_active=0 ORDER BY balance_date ASC"; 
						$beforepcbres = $callConfig->getAllRows($beforepcbsql);
						$seq=1;
						foreach($beforepcbres as $bres){
							if($seq ==1){ 
								if($bres->balance_date != $pcbrres->transaction_date){
						
									$closing_balanceee = $bres->opening_balance.$balancetype.$pcbrres->total_amount;
									$closing_balance = eval('return '.$closing_balanceee.';');
									$insertcust = array('company_id'=>$post['company_id'],'pt_owner_id'=>$pcbrres->party_id,'pc_box_id'=>$pcbrres->ref_id,'balance_date'=>$pcbrres->transaction_date,'debit_amount'=>$pcbrres->total_amount,'credit_amount'=>0,'opening_balance'=>$bres->opening_balance,'closing_balance'=>$closing_balance,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
									$pc_balance = $callConfig->insertRecord('petty_cash_balances',$insertcust);
									$return['beforsamedate'][] = $samedate;	
								}
							}
							$updatequery =  "UPDATE petty_cash_balances SET opening_balance= opening_balance".$balancetype.$pcbrres->total_amount.", closing_balance= closing_balance".$balancetype.$pcbrres->total_amount." WHERE id = '".$bres->id."'";
							$samedate = $callConfig->executeQuery($updatequery);
							$return['beforanotherdate'][] = $samedate;	
						$seq++;	
						} 
						
						$return['pc_balance_already_beffor_date'] = $beforepcbres;
						

					}else{
						
						$balancetype = htmlspecialchars((string)filter_input(INPUT_POST, 'balancetype', FILTER_UNSAFE_RAW),ENT_QUOTES,'UTF-8');
						$closing_balanceee = (float)$pcbres->closing_balance . $balancetype . (float)$pcbrres->total_amount;
						$closing_balance = eval('return '.$closing_balanceee.';');
						$insertcust = array('company_id'=>$post['company_id'],'pt_owner_id'=>$pcbrres->party_id,'pc_box_id'=>$pcbrres->ref_id,'balance_date'=>$pcbrres->transaction_date,'debit_amount'=>$pcbrres->total_amount,'credit_amount'=>0,'opening_balance'=>$pcbres->closing_balance,'closing_balance'=>$closing_balance,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
						$pc_balance = $callConfig->insertRecord('petty_cash_balances',$insertcust);
						$return['pc_balance_already_after_date'] = $pc_balance;
						$return['pc_balance_already_after_date_insert_ary'] = $insertcust;
						$return['pc_balance_already_after_date_insert_symb'] = $balancetype;
					
					}
				}else{
					//$insertcust = array('company_id'=>$post['company_id'],'pt_owner_id'=>$pcbrres->party_id,'pc_box_id'=>$pcbrres->ref_id,'balance_date'=>$pcbrres->transaction_date,'debit_amount'=>$pcbrres->total_amount,'credit_amount'=>0,'opening_balance'=>0,'closing_balance'=>$pcbrres->total_amount,'is_active'=>0,'current_status'=>0,'created_by'=>$post['user_id'],'created_date'=>date("Y-m-d H:i:s"));
					//$pc_itemsres = $callConfig->insertRecord('petty_cash_balances',$insertcust);
					//$return['pc_itemsres'] = $pc_itemsres;
				}
			}else{
				$return['not_inside'] = "Y";
			}
		}
		return $return;
	}

	// function pettyCashDashboard($get){
	// 	global $callConfig;
	// 	$return = array();
	// 	if($get['location_transaction'] =="Y"){
	// 		$sql = "SELECT pc.transaction_number,pc.transaction_date, pc.party_name, au.display_name, au.location_id, pc.total_amount, pc.current_status, pcbo.pc_box_id FROM petty_cash pc, application_users au, petty_cash_box_owner pcbo  WHERE pc.company_id  = ".$get['company_id']." AND pc.company_id = au.company_id AND pc.company_id = pcbo.company_id AND pc.originator = au.id AND au.location_id = '".$get['location_id']."' AND pc.originator = pcbo.owner_id AND pc.is_active = au.is_active AND pc.is_active = pcbo.is_active AND pc.is_active = 0";
	// 		$res=$callConfig->getAllRows($sql);
	// 		$return['location_pettycashbox'] = $res;

	// 		$lsql = "SELECT id,pc_box_description FROM `petty_cash_boxes` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
	// 		$lres=$callConfig->getAllRows($lsql);
	// 		$return['petty_cash_boxes'] = $lres;

	// 	}else{
	// 		$lsql = "SELECT id,location_name FROM `locations` WHERE company_id = '".$get['company_id']."' AND is_active = 0";
	// 		$lres=$callConfig->getAllRows($lsql);
	// 		$return['locations'] = $lres;

	// 		$sql = "SELECT pcbo.pc_box_id, pcbo.owner_id, pcbo.pc_limit, au.location_id, (SELECT pcb.closing_balance FROM petty_cash_balances pcb WHERE pcb.company_id = pcbo.company_id AND pcb.pc_box_id = pcbo.pc_box_id AND pcb.is_active = pcbo.is_active ORDER BY pcb.balance_date DESC limit 1) as pc_balance FROM petty_cash_box_owner pcbo, application_users au WHERE pcbo.company_id = ".$get['company_id']." AND au.id = pcbo.owner_id AND au.is_active = pcbo.is_active AND pcbo.is_active = 0";
	// 		$res=$callConfig->getAllRows($sql);
	// 		$return['location_pettycashbox'] = $res;
	// 	}

	// 	return $return;
	// }

}