<?php

class reportUserClass extends userClass{
	function getDynamicDownloadPDFInvoices($get){
		 global $callConfig;
        $return = array();
        $transaction_table = $get['transaction_table'];
       if($get['from_date'] !="" AND $get['to_date'] !=""){
            $condition .= " AND transaction_date >= '".$this->dateconvertdbformat($get['from_date'])."'  AND transaction_date <= '".$this->dateconvertdbformat($get['to_date'])."'";
        }
        if(!empty($get['party_id'])){
            $condition .= " AND party_id IN(".implode(',', (array)json_decode($get['party_id'],1)).")";
        }
        $transactionssql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND is_active =0".$condition;
        $transactionsres=$callConfig->getAllRows($transactionssql);
        
        $return['transactions'] = $transactionsres;
        $return['transactionssql'] = $transactionssql;

    	$display_attribute_typessql = "SELECT content FROM display_attribute_types WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$transaction_table."' AND field_name = 'party_id'";	
		$parttable_name = $callConfig->getRow($display_attribute_typessql);
		$qquery = urldecode($parttable_name->content);
		$explosql = explode("FROM", $qquery); // splite from to get table name and other part get
		if(isset($explosql[1])){
			$explosql = explode(" ", trim($explosql[1])); //splite to space only get table name
		}
		$partytable = trim($explosql[0]); //get Table name
		$partysql = "SELECT id, company, display_name FROM ".$partytable." WHERE company_id = '".$get['company_id']."' AND is_active = 0";	
		$partyres = $callConfig->getAllRows($partysql);
		$return['party'] = $partyres;
        return $return;
	}
	function togetlistamendedorders($get)
{
	global $callConfig;
	$return = array();
	$return['get'] = $get;
	$company_id = $get['company_id']; 
	$whr = "";
	if (!empty($get['from_date']) && !empty($get['to_date'])) {
		$from = $get['from_date'];
		$to   = $get['to_date'];
		$whr .= " AND am.transaction_date BETWEEN '".$from."' AND '".$to."' ";
	} 
	$listsql = "SELECT CONCAT('AMND-', am.transaction_number) AS transaction_number, am.transaction_date, am.ref_type, am.ref_no FROM amendments am WHERE am.company_id = '".$company_id."' AND am.is_active = 0 $whr ORDER BY am.transaction_date DESC "; 
	$rows = $callConfig->getAllRows($listsql); 
	$return['amended_orders_list'] = $rows;
	$return['sql'] = $listsql;

	return $return;
}
/**
function getSalesRegisterReport($get)
	{
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$originator = $get['fieldaccess'] != "" ? "party_id" : "originator";
		$checkuserid = $get['fieldaccess'] != "" ? $get['vendor_id'] : $get['user_id'];

		$condition = "";
		if ($get['admin_type'] != 1) {
			$condition = " AND transa." . $originator . " = '" . $checkuserid . "'";
		}

		$get['table'] = $get['transaction_table'];
		$status_progress = $this->getStatusProgressList($get);
		foreach ($status_progress as $statusprogress) {
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}

		$dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $get['transaction_table'] . "' AND is_active = 0";
		$dmtres = $callConfig->getRow($dmtsql);
		$return['dynamic_mapping_tables'] = $dmtres;

		if ($get['refrance_res'] == "Y") {
			$condition = " AND transa.ref_type = '" . $get['ref_table'] . "' AND transa.ref_id = '" . $get['ref_id'] . "'";
		}
 
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$condition .= " AND transa.transaction_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		}
 
		if (!empty($get['party_id'])) {
			$condition .= " AND transa.party_id = '" . $get['party_id'] . "'";
		}
 
		if (!empty($get['status_filter'])) {
			$condition .= " AND transa.current_status = '" . $get['status_filter'] . "'";
		}
 
		$listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.party_id, transa.party_name, transa.party_tax_number as gstin, transa.current_status, itemtb.id as item_row_id, itemtb.item_id, itemtb.account_code, itemtb.item_name, itemtb.description as item_description, itemtb.purchase_uom, itemtb.item_quantity, itemtb.item_price, itemtb.item_amount as taxable_amount, itemtb.discount_amount, itemtb.total_amount as net_amount, itemtb.item_details FROM " . $dmtres->application_table_name . " transa, " . $dmtres->item_table . " itemtb  WHERE transa.company_id = '" . $get['company_id'] . "' AND transa.company_id = itemtb.company_id AND transa.id = itemtb.transaction_id AND transa.is_active = itemtb.is_active AND transa.is_active = 0 " . $condition . " ORDER BY transa.transaction_date DESC, transa.transaction_number DESC";
		
		$listres = $callConfig->getAllRows($listsql); 

		$list = array();
		foreach ($listres as $listre) { 
			if (!empty($listre->item_details)) {
				$item_details = json_decode($listre->item_details, true);
				$listre->cgst = 0;
				$listre->sgst = 0;
				$listre->igst = 0;
				$listre->hsn_sac = '';
				
				if (isset($item_details['tax_details']) && is_array($item_details['tax_details'])) {
					foreach ($item_details['tax_details'] as $tax) {
						if (isset($tax['tax_name'])) {
							$tax_name_upper = strtoupper($tax['tax_name']);
							$tax_amount = isset($tax['tax_amount']) ? floatval($tax['tax_amount']) : 0;
							
							if (strpos($tax_name_upper, 'CGST') !== false) {
								$listre->cgst += $tax_amount;
							} elseif (strpos($tax_name_upper, 'SGST') !== false) {
								$listre->sgst += $tax_amount;
							} elseif (strpos($tax_name_upper, 'IGST') !== false) {
								$listre->igst += $tax_amount;
							}
						}
					}
				}
				 
				if (isset($item_details['hsn_sac_code'])) {
					$listre->hsn_sac = $item_details['hsn_sac_code'];
				}
			} else {
				$listre->cgst = 0;
				$listre->sgst = 0;
				$listre->igst = 0;
				$listre->hsn_sac = '';
			}
			 
			$listre->gross_amount = $listre->taxable_amount + $listre->cgst + $listre->sgst + $listre->igst;
			$listre->taxable_value = $listre->taxable_amount; 
			
			$list[] = $listre;
		}
		
		$return['transaction_list'] = $list;
		$return['sql'] = $listsql;
		return $return;

	}

    function getsalesregisterreport($get)
	{
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$originator = $get['fieldaccess'] != "" ? "party_id" : "originator";
		$checkuserid = $get['fieldaccess'] != "" ? $get['vendor_id'] : $get['user_id'];

		$condition = "";
		if ($get['admin_type'] != 1) {
			$condition = " AND transa." . $originator . " = '" . $checkuserid . "'";
		}

		$get['table'] = $get['transaction_table'];
		$status_progress = $this->getStatusProgressList($get);
		foreach ($status_progress as $statusprogress) {
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}

		$dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $get['transaction_table'] . "' AND is_active = 0";
		$dmtres = $callConfig->getRow($dmtsql);
		$return['dynamic_mapping_tables'] = $dmtres;

		if ($get['refrance_res'] == "Y") {
			$condition = " AND transa.ref_type = '" . $get['ref_table'] . "' AND transa.ref_id = '" . $get['ref_id'] . "'";
		}
 
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$condition .= " AND transa.transaction_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		}
 
		if (!empty($get['party_id'])) {
			$condition .= " AND transa.party_id = '" . $get['party_id'] . "'";
		}
 
		if (!empty($get['status_filter'])) {
			$condition .= " AND transa.current_status = '" . $get['status_filter'] . "'";
		}
 
		$listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.party_id, transa.party_name, transa.party_tax_number as gstin, transa.current_status, itemtb.id as item_row_id, itemtb.item_id, itemtb.account_code, itemtb.item_name, itemtb.description as item_description, itemtb.purchase_uom, itemtb.item_quantity, itemtb.item_price, itemtb.item_amount as taxable_amount, itemtb.discount_amount, itemtb.total_amount as net_amount, itemtb.item_details FROM " . $dmtres->application_table_name . " transa, " . $dmtres->item_table . " itemtb  WHERE transa.company_id = '" . $get['company_id'] . "' AND transa.company_id = itemtb.company_id AND transa.id = itemtb.transaction_id AND transa.is_active = itemtb.is_active AND transa.is_active = 0 " . $condition . " ORDER BY transa.transaction_date DESC, transa.transaction_number DESC";

		$listres = $callConfig->getAllRows($listsql); 
		$list = array();
		foreach ($listres as $listre) { 
			if (!empty($listre->item_details)) {
				$item_details = json_decode($listre->item_details, true);
				$listre->cgst = 0;
				$listre->sgst = 0;
				$listre->igst = 0;
				$listre->hsn_sac = '';
				
				if (isset($item_details['tax_details']) && is_array($item_details['tax_details'])) {
					foreach ($item_details['tax_details'] as $tax) {
						if (isset($tax['tax_name'])) {
							$tax_name_upper = strtoupper($tax['tax_name']);
							$tax_amount = isset($tax['tax_amount']) ? floatval($tax['tax_amount']) : 0;
							
							if (strpos($tax_name_upper, 'CGST') !== false) {
								$listre->cgst += $tax_amount;
							} elseif (strpos($tax_name_upper, 'SGST') !== false) {
								$listre->sgst += $tax_amount;
							} elseif (strpos($tax_name_upper, 'IGST') !== false) {
								$listre->igst += $tax_amount;
							}
						}
					}
				}
				 
				if (isset($item_details['hsn_sac_code'])) {
					$listre->hsn_sac = $item_details['hsn_sac_code'];
				}
			} else {
				$listre->cgst = 0;
				$listre->sgst = 0;
				$listre->igst = 0;
				$listre->hsn_sac = '';
			} 
			$listre->gross_amount = $listre->taxable_amount + $listre->cgst + $listre->sgst + $listre->igst;
			$listre->taxable_value = $listre->taxable_amount;  
			$list[] = $listre;
		}
		
		$return['transaction_list'] = $list;
		$return['sql'] = $listsql;
		return $return;

	}

    function getordersalesreport($get)
	{
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$originator = $get['fieldaccess'] != "" ? "party_id" : "originator";
		$checkuserid = $get['fieldaccess'] != "" ? $get['vendor_id'] : $get['user_id'];

		if ($get['admin_type'] != 1) {
			$condition = " AND transa." . $originator . " = '" . $checkuserid . "'";
		}
 
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$condition .= " AND transa.transaction_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		}

		$get['table'] = $get['transaction_table'];
		$status_progress = $this->getStatusProgressList($get);
		foreach ($status_progress as $statusprogress) {
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}

		$dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $get['transaction_table'] . "' AND is_active = 0";
		$dmtres = $callConfig->getRow($dmtsql);
		$return['dynamic_mapping_tables'] = $dmtres;

		if ($get['refrance_res'] == "Y") {
			$condition = " AND transa.ref_type = '" . $get['ref_table'] . "' AND transa.ref_id = '" . $get['ref_id'] . "'";
		}
		$listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.party_tax_number, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.total_tax_split_details, transa.current_status, itemtb.id as item_row_id, itemtb.item_id, itemtb.account_code, itemtb.item_name, itemtb.description, itemtb.item_quantity, itemtb.purchase_uom, itemtb.item_price, itemtb.pending_qty, itemtb.item_amount as atm_item_amount, itemtb.tax_amount as itm_tax_amount, itemtb.discount_amount as itm_discount_amount, itemtb.total_amount as atm_total_amount, itemtb.item_details FROM " . $dmtres->application_table_name . " transa, " . $dmtres->item_table . " itemtb WHERE transa.company_id = '" . $get['company_id'] . "' AND transa.company_id = itemtb.company_id AND transa.id = itemtb.transaction_id AND transa.is_active = itemtb.is_active AND transa.is_active = 0 " . $condition;
		$listres = $callConfig->getAllRows($listsql);

		$list = array();
		foreach ($listres as $listre) {
			$list[$listre->id][] = $listre;
		}
		$return['transaction_list'] = $list;
		$return['sql'] = $listsql;
		return $return;
	} **/


	   	function getSalesSummaryReport($get)
	{
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$originator = $get['fieldaccess'] != "" ? "party_id" : "originator";
		$checkuserid = $get['fieldaccess'] != "" ? $get['vendor_id'] : $get['user_id'];

		$condition = "";
		if ($get['admin_type'] != 1) {
			$condition = " AND transa." . $originator . " = '" . $checkuserid . "'";
		}

		$get['table'] = $get['transaction_table'];
		$status_progress = $this->getStatusProgressList($get);
		foreach ($status_progress as $statusprogress) {
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}

		$dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $get['transaction_table'] . "' AND is_active = 0";
		$dmtres = $callConfig->getRow($dmtsql);
		$return['dynamic_mapping_tables'] = $dmtres;

		if ($get['refrance_res'] == "Y") {
			$condition = " AND transa.ref_type = '" . $get['ref_table'] . "' AND transa.ref_id = '" . $get['ref_id'] . "'";
		}
 
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$condition .= " AND transa.transaction_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		}
 
		if (!empty($get['party_id'])) {
			$condition .= " AND transa.party_id = '" . $get['party_id'] . "'";
		}
 
		if (!empty($get['status_filter'])) {
			$condition .= " AND transa.current_status = '" . $get['status_filter'] . "'";
		}
 
		$listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.party_id, transa.party_name, transa.party_additional_details, transa.tax_state, transa.ref_id, transa.current_status FROM " . $dmtres->application_table_name . " transa WHERE transa.company_id = '" . $get['company_id'] . "' AND transa.is_active = 0 " . $condition . " ORDER BY transa.transaction_date DESC, transa.transaction_number DESC";
		
		$listres = $callConfig->getAllRows($listsql);
		$return['listres'] = $listsql;

		$list = array();
		foreach ($listres as $listre) { 
			$listre->gstin = '';
			if (!empty($listre->party_additional_details)) {
				$party_details = json_decode($listre->party_additional_details, true);
				if (isset($party_details['gstin'])) {
					$listre->gstin = $party_details['gstin'];
				}
			}
			 
			$listre->po_number = '';
			$listre->po_date = '';
			if (!empty($listre->ref_id)) {
				$posql = "SELECT transaction_number, transaction_date FROM purchase_orders WHERE company_id = '" . $get['company_id'] . "' AND id = '" . $listre->ref_id . "' AND is_active = 0";
				$pores = $callConfig->getRow($posql);
				if ($pores) {
					$listre->po_number = $pores->transaction_number;
					$listre->po_date = $pores->transaction_date;
				}
			}
			 
			$itemsql = "SELECT id as item_row_id, item_id, item_name, description as item_description, purchase_uom, item_quantity, item_price, item_amount, discount_amount, tax_amount, total_amount as net_amount FROM " . $dmtres->item_table . " WHERE company_id = '" . $get['company_id'] . "' AND transaction_id = '" . $listre->id . "' AND is_active = 0";
			$itemres = $callConfig->getAllRows($itemsql);
			
			$listre->items = array();
			
			foreach ($itemres as $item) { 
				$taxsql = "SELECT tax_name, tax_amount FROM " . $dmtres->tax_table . " WHERE company_id = '" . $get['company_id'] . "' AND transaction_id = '" . $listre->id . "' AND transaction_item_id = '" . $item->item_row_id . "' AND is_active = 0";
				$taxres = $callConfig->getAllRows($taxsql);
				
				$item->hsn_sac = '';
				$item->cgst = 0;
				$item->sgst = 0;
				$item->igst = 0;
				$total_tax = 0;
				 
				if (!empty($taxres)) {
					$item->hsn_sac = $taxres[0]->tax_name;
					 
					foreach ($taxres as $tax) {
						$total_tax += floatval($tax->tax_amount);
					}
				}
				 
				if (!empty($listre->tax_state) && strtolower($listre->tax_state) == 's') { 
					$item->cgst = 0;
					$item->sgst = $total_tax;
					$item->igst = 0;
				} else { 
					$item->cgst = $total_tax / 2;
					$item->sgst = $total_tax / 2;
					$item->igst = 0;
				}
				 
				$item->taxable_amount = $item->item_amount;
				$item->gross_amount = $item->item_amount + $item->discount_amount;
				
				$listre->items[] = $item;
			}
			
			$list[] = $listre;
		}
		
		$return['transaction_list'] = $list;
		$return['sql'] = $listsql;
		return $return;

	}


    	function getordersalesreport($get)
	{
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$originator = $get['fieldaccess'] != "" ? "party_id" : "originator";
		$checkuserid = $get['fieldaccess'] != "" ? $get['vendor_id'] : $get['user_id'];

		$condition = "";
		if ($get['admin_type'] != 1) {
			$condition = " AND transa." . $originator . " = '" . $checkuserid . "'";
		}
 
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$condition .= " AND transa.transaction_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		}

		$get['table'] = $get['transaction_table'];
		$status_progress = $this->getStatusProgressList($get);
		foreach ($status_progress as $statusprogress) {
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}

		$dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $get['transaction_table'] . "' AND is_active = 0";
		$dmtres = $callConfig->getRow($dmtsql);
		$return['dynamic_mapping_tables'] = $dmtres;

		if ($get['refrance_res'] == "Y") {
			$condition = " AND transa.ref_type = '" . $get['ref_table'] . "' AND transa.ref_id = '" . $get['ref_id'] . "'";
		}
 
		$uomsql = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$uomres = $callConfig->getAllRows($uomsql);
		$uom_list = array();
		foreach ($uomres as $uom) {
			$uom_list[$uom->id] = $uom->uom_name;
		}
 
		$listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.originator, transa.party_id, transa.party_name, transa.party_phone, transa.party_address, transa.party_additional_details, transa.tax_state, transa.ref_id, transa.amount, transa.discount_amount, transa.tax_amount, transa.total_amount, transa.additional_charges, transa.type, transa.current_status FROM " . $dmtres->application_table_name . " transa WHERE transa.company_id = '" . $get['company_id'] . "' AND transa.is_active = 0 " . $condition . " ORDER BY transa.transaction_date DESC, transa.transaction_number DESC";
		
		$listres = $callConfig->getAllRows($listsql);
		$return['listres'] = $listsql;

		$list = array();
		foreach ($listres as $listre) { 
			$listre->gstin = '';
			if (!empty($listre->party_additional_details)) {
				$party_details = json_decode($listre->party_additional_details, true);
				if (isset($party_details['gstin'])) {
					$listre->gstin = $party_details['gstin'];
				}
			}
			 
			$listre->po_number = '';
			$listre->po_date = '';
			if (!empty($listre->ref_id)) {
				$posql = "SELECT transaction_number, transaction_date FROM purchase_orders WHERE company_id = '" . $get['company_id'] . "' AND id = '" . $listre->ref_id . "' AND is_active = 0";
				$pores = $callConfig->getRow($posql);
				if ($pores) {
					$listre->po_number = $pores->transaction_number;
					$listre->po_date = $pores->transaction_date;
				}
			}
			 
			$itemsql = "SELECT id as item_row_id, item_id, account_code, item_name, description, item_quantity, purchase_uom, item_price, pending_qty, item_amount, tax_amount, discount_amount, total_amount FROM " . $dmtres->item_table . " WHERE company_id = '" . $get['company_id'] . "' AND transaction_id = '" . $listre->id . "' AND is_active = 0";
			$itemres = $callConfig->getAllRows($itemsql);
			
			$listre->items = array();
			
			foreach ($itemres as $item) { 
				$item->uom_name = isset($uom_list[$item->purchase_uom]) ? $uom_list[$item->purchase_uom] : $item->purchase_uom;
				 
				$taxsql = "SELECT tax_name, tax_amount FROM " . $dmtres->tax_table . " WHERE company_id = '" . $get['company_id'] . "' AND transaction_id = '" . $listre->id . "' AND transaction_item_id = '" . $item->item_row_id . "'  AND is_active = 0";
				$taxres = $callConfig->getAllRows($taxsql);
				
				$item->hsn_sac = '';
				$item->cgst = 0;
				$item->sgst = 0;
				$item->igst = 0;
				$total_tax = 0;
				 
				if (!empty($taxres)) {
					$item->hsn_sac = $taxres[0]->tax_name;
					 
					foreach ($taxres as $tax) {
						$total_tax += floatval($tax->tax_amount);
					}
				}
				 
				if (!empty($listre->tax_state) && strtolower($listre->tax_state) == 's') { 
					$item->cgst = 0;
					$item->sgst = $total_tax;
					$item->igst = 0;
				} else { 
					$item->cgst = $total_tax / 2;
					$item->sgst = 0;
					$item->igst = $total_tax / 2;
				}
				 
				$item->taxable_value = $item->item_amount;
				$item->gross_value = $item->item_amount + $item->tax_amount;
				
				$listre->items[] = $item;
			}
			
			$list[] = $listre;
		}
		
		$return['transaction_list'] = $list;
		$return['sql'] = $listsql;
		return $return;

	}


	function getSalesRegisterReport($get)
	{
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$originator = $get['fieldaccess'] != "" ? "party_id" : "originator";
		$checkuserid = $get['fieldaccess'] != "" ? $get['vendor_id'] : $get['user_id'];

		$condition = "";
		if ($get['admin_type'] != 1) {
			$condition = " AND transa." . $originator . " = '" . $checkuserid . "'";
		}

		$get['table'] = $get['transaction_table'];
		$status_progress = $this->getStatusProgressList($get);
		foreach ($status_progress as $statusprogress) {
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}

		$dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM `dynamic_mapping_tables` WHERE company_id = '" . $get['company_id'] . "' AND application_table_name = '" . $get['transaction_table'] . "' AND is_active = 0";
		$dmtres = $callConfig->getRow($dmtsql);
		$return['dynamic_mapping_tables'] = $dmtres;

		if ($get['refrance_res'] == "Y") {
			$condition = " AND transa.ref_type = '" . $get['ref_table'] . "' AND transa.ref_id = '" . $get['ref_id'] . "'";
		}
  
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$condition .= " AND transa.transaction_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		}
 
		if (!empty($get['party_id'])) {
			$condition .= " AND transa.party_id = '" . $get['party_id'] . "'";
		}
 
		if (!empty($get['status_filter'])) {
			$condition .= " AND transa.current_status = '" . $get['status_filter'] . "'";
		}
 
		$listsql = "SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.doc_ref_no, transa.doc_ref_date, transa.party_id, transa.party_name, transa.party_additional_details, transa.tax_state, transa.ref_id, transa.current_status FROM " . $dmtres->application_table_name . " transa WHERE transa.company_id = '" . $get['company_id'] . "' AND transa.is_active = 0 " . $condition . " ORDER BY transa.transaction_date DESC, transa.transaction_number DESC";
		
		$listres = $callConfig->getAllRows($listsql);
		$return['listres'] = $listsql;

		$list = array();
		foreach ($listres as $listre) {
			// Extract GSTIN from party_additional_details JSON
			$listre->gstin = '';
			if (!empty($listre->party_additional_details)) {
				$party_details = json_decode($listre->party_additional_details, true);
				if (isset($party_details['gstin'])) {
					$listre->gstin = $party_details['gstin'];
				}
			}
			
			// Get PO details from purchase_orders table if ref_id exists
			$listre->po_number = '';
			$listre->po_date = '';
			if (!empty($listre->ref_id)) {
				$posql = "SELECT transaction_number, transaction_date 
					FROM purchase_orders 
					WHERE company_id = '" . $get['company_id'] . "' 
					AND id = '" . $listre->ref_id . "' 
					AND is_active = 0";
				$pores = $callConfig->getRow($posql);
				if ($pores) {
					$listre->po_number = $pores->transaction_number;
					$listre->po_date = $pores->transaction_date;
				}
			}
			 
			$itemsql = "SELECT itemtb.id as item_row_id, itemtb.item_id, itemtb.item_name, itemtb.description as item_description, itemtb.purchase_uom, itemtb.item_quantity, itemtb.item_price, itemtb.item_amount, itemtb.discount_amount, itemtb.tax_amount, itemtb.total_amount as net_amount, items.sku as sku_code FROM " . $dmtres->item_table . " itemtb LEFT JOIN items ON itemtb.item_id = items.id AND itemtb.company_id = items.company_id WHERE itemtb.company_id = '" . $get['company_id'] . "'  AND itemtb.transaction_id = '" . $listre->id . "' AND itemtb.is_active = 0";
			$itemres = $callConfig->getAllRows($itemsql);
			
			$listre->items = array();
			
			foreach ($itemres as $item) { 
				$taxsql = "SELECT tax_name, tax_amount FROM " . $dmtres->tax_table . " WHERE company_id = '" . $get['company_id'] . "' AND transaction_id = '" . $listre->id . "' AND transaction_item_id = '" . $item->item_row_id . "'  AND is_active = 0";
				$taxres = $callConfig->getAllRows($taxsql);
				
				$item->hsn_sac = '';
				$item->cgst = 0;
				$item->sgst = 0;
				$item->igst = 0;
				$total_tax = 0;
				 
				if (!empty($taxres)) {
					$item->hsn_sac = $taxres[0]->tax_name;
					 
					foreach ($taxres as $tax) {
						$total_tax += floatval($tax->tax_amount);
					}
				}
				 
				if (!empty($listre->tax_state) && strtolower($listre->tax_state) == 's') {
					$item->cgst = 0;
					$item->sgst = $total_tax;
					$item->igst = 0;
				} else { 
					$item->cgst = $total_tax / 2;
					$item->sgst = $total_tax / 2;
					$item->igst = 0;
				}
				
				// Calculate taxable amount, gross amount and taxable value
				$item->taxable_amount = $item->item_amount;
				$item->gross_amount = $item->item_amount + $item->discount_amount;
				$item->taxable_value = $item->item_amount; // Same as taxable_amount for this report
				
				$listre->items[] = $item;
			}
			
			$list[] = $listre;
		}
		
		$return['transaction_list'] = $list;
		$return['sql'] = $listsql;
		return $return;

	}

	function toGetStandardTransactionDetailsReports($get){
		global $callConfig;
		$return = array();
		$transaction_table = $get['transaction_table'];
		$transaction_list = $unit_of_measurements =$items = array();
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$condition .= " AND par.transaction_date BETWEEN '" . $get['from_date'] . "' AND '" . $get['to_date'] . "'";
		}

		$dmtsql = "SELECT id, page_title, application_table_name,display_table,item_table,tax_table FROM dynamic_mapping_tables WHERE company_id = '".$get['company_id']."' AND application_table_name = '".$transaction_table."' AND item_table !='' AND tax_table !='' AND is_active = 0";
		$dmtres = $callConfig->getRow($dmtsql);
		$return['dynamic_mapping_tables'] = $dmtres;

		$itemssql = "SELECT id, sku, item_name FROM items WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$itemsres = $callConfig->getAllRows($itemssql);
		foreach($itemsres as $iteml){
			$items[$iteml->id] = $iteml;
		}
		$return['items'] = $items;
		
		
		$unit_of_measurementssql = "SELECT id, uom_name FROM unit_of_measurements WHERE company_id = '".$get['company_id']."' AND is_active = 0";
		$unit_of_measurementsres = $callConfig->getAllRows($unit_of_measurementssql);
		foreach($unit_of_measurementsres as $uoml){
			$unit_of_measurements[$uoml->id] = $uoml->uom_name;
		}
		$return['unit_of_measurements'] = $unit_of_measurements;
			

		$listsql = "SELECT  par.id, par.transaction_number, par.transaction_date, par.originator, party_id, par.party_name, par.payment_terms, par.amount,  par.type, par.tax_type,  par.shipping_tax, par.shipping_amount, par.shipping_tax_amount, par.doc_ref_no, par.doc_ref_date, par.due_date, par.ref_type, par.ref_id, par.ref_no, par.ref_date, par.old_ref_no, par.old_ref_date, par.cancel_invoice, par.cancel_invoice_date, par.cancel_invoice_reason, par.additional_charges, par.additional_attributes, par.total_tds_amount, par.advance_payment, par.pending_advance_payment,  par.rounding_off, par.discount_mode, par.multi_reference, par.originator_state_id, par.supplier_state_id, par.total_amount as par_total_amount, par.current_status, soi.id as childtabid ,soi.item_id, soi.item_name, soi.description, soi.purchase_uom, soi.item_quantity, soi.item_price, soi.item_amount, soi.tax_amount, soi.discount_amount, soi.total_amount, soi.tds_amount, soi.pending_qty, soi.ref_transaction_table_id FROM ".$dmtres->application_table_name." par, ".$dmtres->item_table." soi WHERE par.company_id = '" . $get['company_id'] . "' ".$condition." AND par.company_id = soi.company_id AND par.id = soi.transaction_id AND par.is_active = soi.is_active  AND par.current_status = 199 AND par.is_active = 0 AND (par.cancel_invoice = 'N' OR par.cancel_invoice IS NULL)  ORDER BY par.id Asc";
		$listres = $callConfig->getAllRows($listsql);
		foreach($listres as $transres){
			$transaction_list[$transres->id][] = $transres;
		}
		$return['transaction_list_sql'] = $listsql;
		$return['transaction_list'] = $transaction_list;

		if($transaction_table == "purchase_invoices"){
			$reftranssql = "SELECT id, transaction_number, transaction_date FROM purchase_orders WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$reftransres = $callConfig->getAllRows($reftranssql);
			foreach($reftransres as $reftra){
				$refrancetransactions[$reftra->id] = $reftra;
			}
			$return['refrance_transactions'] = $refrancetransactions;	
		}elseif($transaction_table == "sales_invoices" OR $transaction_table == "purchase_orders"){
			$reftranssql = "SELECT id, transaction_number, transaction_date FROM sales_orders WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$reftransres = $callConfig->getAllRows($reftranssql);
			foreach($reftransres as $reftra){
				$refrancetransactions[$reftra->id] = $reftra;
			}
			$return['refrance_transactions'] = $refrancetransactions;
		}

		return $return;
	}

	function toGetStandardDayStockBalanceReport($get){
		global $callConfig;
		$return = array();
		$balance_date = $this->dateconvertdbformat($get['balance_date']);
		$dmtsql = "SELECT itm.sku, itm.item_name, iwb.balance_date, iwb.opening_qty, iwb.in_qty, iwb.out_qty, iwb.closing_qty FROM items itm LEFT JOIN item_wa_balances iwb ON itm.company_id = iwb.company_id AND itm.id = iwb.item_id AND iwb.balance_date = ( SELECT MAX(iwb1.balance_date) FROM item_wa_balances iwb1 WHERE itm.company_id = iwb1.company_id AND iwb1.balance_date <= '".$balance_date."' AND itm.id = iwb1.item_id) WHERE itm.company_id = '".$get['company_id']."' AND itm.is_active = 0";
		$dmtres = $callConfig->getAllRows($dmtsql);
		$return['stock_list'] = $dmtres;
		return $return;
	}
	function getProjectTaskTimeLogReport($get){
		global $callConfig;
		$return = array();

		$application_user_id = $get['application_user_id'];
		$select_month = $get['select_month'];
		$dmtsql = "SELECT id, display_name, user_email FROM application_users WHERE company_id = '".$get['company_id']."' AND  user_type = 1 AND is_active = 0";
		$dmtres = $callConfig->getallRows($dmtsql);
		$return['application_users'] = $dmtres;
		

		$dmtsql = "SELECT id, task_name FROM project_task_details WHERE company_id = 1 AND is_active = 0";
		$project_task_detailsres = $callConfig->getAllRows($dmtsql);
		$project_task_details = array();
		foreach($project_task_detailsres as $ptd){
			$project_task_details[$ptd->id] = $ptd;
		}

		$arr = explode('-', $select_month);
		$month = $arr[1];
		$year  = $arr[0];
		$dates = [];
	    $start = new DateTime("$year-$month-01");
	    $end   = new DateTime($start->format('Y-m-t'));
	    while ($start <= $end) {
	        $dates[] = $start->format('Y-m-d');
	        $start->modify('+1 day');
	    }	
	    $return['project_task_dates'] = $dates;
	    if($application_user_id != ""){
	    	$whrcond = " AND  p.user_id =".$application_user_id;
	    }
		$dmtsql = "SELECT p.time_date, p.user_id, u.display_name, p.time_date, GROUP_CONCAT(p.task_id) AS task_ids, SEC_TO_TIME( ROUND(SUM(p.task_end - p.task_start) / 1000) ) AS total_working_time FROM project_task_time_log p LEFT JOIN application_users u ON u.id = p.user_id WHERE p.company_id = 1 AND p.company_id = u.company_id AND u.id = p.user_id ".$whrcond." GROUP BY p.time_date, p.user_id ORDER BY p.time_date";
		$project_task_time_logres = $callConfig->getAllRows($dmtsql);
		$project_task_time_log = array();
		foreach($project_task_time_logres as $ptl){
			$explodeid = explode(',',$ptl->task_ids);
			$task_names = "";
			foreach($explodeid as $exlid){
				$task_names .= $project_task_details[$exlid]->task_name;
			}
			$ptl->task_names = $task_names;
			$project_task_time_log[$ptl->user_id][$ptl->time_date] = $ptl;
		}
		$return['project_task_time_log_Sql'] = $dmtsql;
		$return['project_task_time_log'] = $project_task_time_log;
		


		return $return;
	}
// added by koti 24-07-2026

    function getCourierReportFilterLookups($get){
        global $callConfig;
        $return = array();
        $return['locations'] = array();
        $return['employees'] = array();
        $return['statuses'] = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == ''){
            return $return;
        }

        $locations_sql = "SELECT id, location_name FROM locations WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY location_name ASC";
        $locations_res = $callConfig->getAllRows($locations_sql);
        if(count((array)$locations_res) > 0){
            foreach($locations_res as $loc_row){
                $return['locations'][] = $loc_row;
            }
        }

        $employees_sql = "SELECT id, display_name, emp_code FROM employees WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY display_name ASC";
        $employees_res = $callConfig->getAllRows($employees_sql);
        if(count((array)$employees_res) > 0){
            foreach($employees_res as $emp_row){
                $return['employees'][] = $emp_row;
            }
        }

        $status_sql = "SELECT id, name, status FROM status_progress WHERE company_id = '".$company_id."' AND table_name = 'couriers' AND is_active = 0 ORDER BY status ASC";
        $status_res = $callConfig->getAllRows($status_sql);
        if(count((array)$status_res) > 0){
            foreach($status_res as $status_row){
                $return['statuses'][] = $status_row;
            }
        }

        return $return;
    }

    function buildCouriersReportWhere($get, $courier_type){
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $where = " company_id = '".$company_id."' AND is_active = 0 AND courier_type = '".$courier_type."' AND courier_date >= '".$from_date."' AND courier_date <= '".$to_date."' ";

        $location_id = isset($get['location_id']) ? $get['location_id'] : '';
        if($location_id != ''){
            $where .= " AND location_id = '".$location_id."' ";
        }

        $tracking_no = isset($get['tracking_no']) ? trim($get['tracking_no']) : '';
        if($tracking_no != ''){
            $where .= " AND courier_ref_no LIKE '%".addslashes($tracking_no)."%' ";
        }

        $courier_company = isset($get['courier_company']) ? trim($get['courier_company']) : '';
        if($courier_company != ''){
            $where .= " AND courier_company LIKE '%".addslashes($courier_company)."%' ";
        }

        $received_by = isset($get['received_by']) ? $get['received_by'] : '';
        if($received_by != ''){
            $where .= " AND requested_by = '".$received_by."' ";
        }

        $taken_by = isset($get['taken_by']) ? trim($get['taken_by']) : '';
        if($taken_by != ''){
            $where .= " AND courier_name LIKE '%".addslashes($taken_by)."%' ";
        }

        $current_status = isset($get['current_status']) ? $get['current_status'] : '';
        if($current_status != ''){
            $where .= " AND current_status = '".$current_status."' ";
        }

        return $where;
    }

    function getCouriersEmployeeNameMap($company_id, $ids){
        global $callConfig;
        $map = array();
        if(count((array)$ids) == 0){
            return $map;
        }
        $clean_ids = array();
        if(count((array)$ids) > 0){
            foreach($ids as $id_val){
                if($id_val != '' && $id_val != 0){
                    $clean_ids[] = (int)$id_val;
                }
            }
        }
        if(count((array)$clean_ids) == 0){
            return $map;
        }
        $sql = "SELECT id, display_name FROM employees WHERE company_id = '".$company_id."' AND id IN (".implode(',', $clean_ids).")";
        $res = $callConfig->getAllRows($sql);
        if(count((array)$res) > 0){
            foreach($res as $emp_row){
                $eid = is_array($emp_row) ? $emp_row['id'] : $emp_row->id;
                $ename = is_array($emp_row) ? $emp_row['display_name'] : $emp_row->display_name;
                $map[$eid] = $ename;
            }
        }
        return $map;
    }

    function getInwardCouriersReport($get){
        global $callConfig;
        $return = $this->getCourierReportFilterLookups($get);
        $return['rows'] = array();

        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        if($filters_only == 'Y'){
            return $return;
        }

        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildCouriersReportWhere($get, 2);
        $sql = "SELECT * FROM couriers WHERE ".$where." ORDER BY courier_date DESC, id DESC";
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $req_ids = array();
        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                if(isset($r['requested_by']) && $r['requested_by'] != ''){
                    $req_ids[] = $r['requested_by'];
                }
            }
        }
        $emp_map = $this->getCouriersEmployeeNameMap($company_id, $req_ids);

        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                $received_by_id = isset($r['requested_by']) ? $r['requested_by'] : '';
                $return['rows'][] = array(
                    'coming_date' => isset($r['courier_date']) ? $r['courier_date'] : '',
                    'coming_time' => isset($r['courier_time']) ? $r['courier_time'] : '',
                    'tracking_no' => isset($r['courier_ref_no']) ? $r['courier_ref_no'] : '',
                    'vehicle_no' => '',
                    'coming_from' => isset($r['from_detail']) ? $r['from_detail'] : '',
                    'to_address' => isset($r['to_detail']) ? $r['to_detail'] : '',
                    'description' => isset($r['description']) ? $r['description'] : '',
                    'qty' => isset($r['courier_qty']) ? $r['courier_qty'] : '',
                    'brought_by_name' => isset($r['courier_name']) ? $r['courier_name'] : '',
                    'received_by_name' => (isset($emp_map[$received_by_id]) ? $emp_map[$received_by_id] : ''),
                    'received_date' => isset($r['courier_date']) ? $r['courier_date'] : '',
                    'received_time' => isset($r['courier_time']) ? $r['courier_time'] : '',
                    'security_sign' => '',
                    'remarks' => isset($r['remarks']) ? $r['remarks'] : ''
                );
            }
        }
        return $return;
    }

    function getOutwardCouriersReport($get){
        global $callConfig;
        $return = $this->getCourierReportFilterLookups($get);
        $return['rows'] = array();

        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        if($filters_only == 'Y'){
            return $return;
        }

        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildCouriersReportWhere($get, 1);
        $sql = "SELECT * FROM couriers WHERE ".$where." ORDER BY courier_date DESC, id DESC";
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);

        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                $return['rows'][] = array(
                    'courier_date' => isset($r['courier_date']) ? $r['courier_date'] : '',
                    'to_address' => isset($r['to_detail']) ? $r['to_detail'] : '',
                    'from_address' => isset($r['from_detail']) ? $r['from_detail'] : '',
                    'tracking_no' => isset($r['courier_ref_no']) ? $r['courier_ref_no'] : '',
                    'vehicle_no' => '',
                    'description' => isset($r['description']) ? $r['description'] : '',
                    'qty' => isset($r['courier_qty']) ? $r['courier_qty'] : '',
                    'taken_by_name' => isset($r['courier_name']) ? $r['courier_name'] : '',
                    'taken_by_date' => isset($r['courier_date']) ? $r['courier_date'] : '',
                    'taken_by_time' => isset($r['courier_time']) ? $r['courier_time'] : '',
                    'taken_by_contact' => isset($r['courier_mobile']) ? $r['courier_mobile'] : '',
                    'security_sign' => '',
                    'remarks' => isset($r['remarks']) ? $r['remarks'] : ''
                );
            }
        }
        return $return;
    }

    function getGateReportFilterLookups($get){
        global $callConfig;
        $return = array();
        $return['locations'] = array();
        $return['employees'] = array();
        $return['departments'] = array();
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == ''){
            return $return;
        }

        $locations_sql = "SELECT id, location_name FROM locations WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY location_name ASC";
        $locations_res = $callConfig->getAllRows($locations_sql);
        if(count((array)$locations_res) > 0){
            foreach($locations_res as $loc_row){
                $return['locations'][] = $loc_row;
            }
        }

        $employees_sql = "SELECT id, display_name, emp_code FROM employees WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY display_name ASC";
        $employees_res = $callConfig->getAllRows($employees_sql);
        if(count((array)$employees_res) > 0){
            foreach($employees_res as $emp_row){
                $return['employees'][] = $emp_row;
            }
        }

        $departments_sql = "SELECT id, department_name FROM departments WHERE company_id = '".$company_id."' AND is_active = 0 ORDER BY department_name ASC";
        $departments_res = $callConfig->getAllRows($departments_sql);
        if(count((array)$departments_res) > 0){
            foreach($departments_res as $dept_row){
                $return['departments'][] = $dept_row;
            }
        }

        return $return;
    }

    function getGateReportEmployeeMap($company_id){
        global $callConfig;
        $map = array();
        // Include inactive employees for name resolution on historical whom_to_meet IDs
        $employees_sql = "SELECT id, display_name, emp_code, user_id FROM employees WHERE company_id = '".$company_id."'";
        $employees_res = $callConfig->getAllRows($employees_sql);
        if(count((array)$employees_res) > 0){
            foreach($employees_res as $emp_row){
                $eid = is_array($emp_row) ? $emp_row['id'] : $emp_row->id;
                $ename = is_array($emp_row) ? $emp_row['display_name'] : $emp_row->display_name;
                $user_id = is_array($emp_row) ? (isset($emp_row['user_id']) ? $emp_row['user_id'] : '') : (isset($emp_row->user_id) ? $emp_row->user_id : '');
                $map[$eid] = $ename;
                // if($user_id != ''){
                //     $map[$user_id] = $ename;
                // }
            }
        }
        return $map;
    }

    function getGateReportUserMap($company_id){
        global $callConfig;
        $map = array();
        $users_sql = "SELECT id, display_name FROM application_users WHERE company_id = '".$company_id."'";
        $users_res = $callConfig->getAllRows($users_sql);
        if(count((array)$users_res) > 0){
            foreach($users_res as $user_row){
                $uid = is_array($user_row) ? $user_row['id'] : $user_row->id;
                $uname = is_array($user_row) ? $user_row['display_name'] : $user_row->display_name;
                $map[$uid] = $uname;
            }
        }
        return $map;
    }

    function resolveGateReportPersonName($id, $emp_map, $user_map){
        if($id == '' || $id == 0 || $id == '0'){
            return '';
        }
        if(isset($emp_map[$id]) && $emp_map[$id] != ''){
            return $emp_map[$id];
        }
        if(isset($user_map[$id]) && $user_map[$id] != ''){
            return $user_map[$id];
        }
        return '';
    }

    function buildVisitorMovementsWhere($get, $visit_type){
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $where = " vl.company_id = '".$company_id."' AND vl.is_active = 0 AND vl.visit_type = '".$visit_type."' AND DATE(vl.in_datetime) >= '".$from_date."' AND DATE(vl.in_datetime) <= '".$to_date."' ";

        $location_id = isset($get['location_id']) ? $get['location_id'] : '';
        if($location_id != ''){
            $where .= " AND vl.location_id = '".$location_id."' ";
        }

        $movement = isset($get['movement']) ? $get['movement'] : '';
        if($movement == 'inside'){
            $where .= " AND (vl.out_datetime IS NULL OR vl.out_datetime = '' OR vl.out_datetime = '0000-00-00 00:00:00') ";
        }else if($movement == 'exited'){
            $where .= " AND vl.out_datetime IS NOT NULL AND vl.out_datetime != '' AND vl.out_datetime != '0000-00-00 00:00:00' ";
        }

        $whom_to_meet = isset($get['whom_to_meet']) ? $get['whom_to_meet'] : '';
        if($whom_to_meet != ''){
            $where .= " AND vl.whom_to_meet = '".$whom_to_meet."' ";
        }

        $department_id = isset($get['department_id']) ? $get['department_id'] : '';
        if($department_id != ''){
            $where .= " AND vl.vendor_supplier_department_id = '".$department_id."' ";
        }

        $employee_id = isset($get['employee_id']) ? $get['employee_id'] : '';
        if($employee_id != ''){
            $where .= " AND (vl.whom_to_meet = '".$employee_id."' OR vl.emp_temp_whom_to_meet = '".$employee_id."' OR vl.employee_code IN (SELECT emp_code FROM employees WHERE id = '".$employee_id."' AND company_id = '".$company_id."' AND is_active = 0)) ";
        }

        $temp_reason = isset($get['temp_reason']) ? $get['temp_reason'] : '';
        if($temp_reason != ''){
            $where .= " AND vl.emp_temp_reason = '".$temp_reason."' ";
        }

        $card_return = isset($get['card_return']) ? $get['card_return'] : '';
        if($card_return == 'returned'){
            $where .= " AND EXISTS (SELECT 1 FROM visitor_badges vb2 WHERE vb2.visitor_log_id = vl.id AND vb2.company_id = vl.company_id AND vb2.returned_at IS NOT NULL AND vb2.returned_at != '' AND vb2.returned_at != '0000-00-00 00:00:00') ";
        }else if($card_return == 'not_returned'){
            $where .= " AND NOT EXISTS (SELECT 1 FROM visitor_badges vb2 WHERE vb2.visitor_log_id = vl.id AND vb2.company_id = vl.company_id AND vb2.returned_at IS NOT NULL AND vb2.returned_at != '' AND vb2.returned_at != '0000-00-00 00:00:00') ";
        }

        $search = isset($get['search']) ? trim($get['search']) : '';
        if($search != ''){
            $search_esc = addslashes($search);
            if($visit_type == 'vendor_supplier'){
                $where .= " AND (vl.vendor_supplier_driver_name LIKE '%".$search_esc."%' OR vl.vendor_supplier_driver_mobile LIKE '%".$search_esc."%' OR vl.vendor_supplier_vehicle_number LIKE '%".$search_esc."%' OR vl.name LIKE '%".$search_esc."%' OR vl.visitor_idcard_no LIKE '%".$search_esc."%' OR vl.phone LIKE '%".$search_esc."%' OR EXISTS (SELECT 1 FROM visitor_badges vb3 WHERE vb3.visitor_log_id = vl.id AND vb3.full_code LIKE '%".$search_esc."%')) ";
            }else if($visit_type == 'employee'){
                $where .= " AND (vl.employee_name LIKE '%".$search_esc."%' OR vl.emp_temp_name LIKE '%".$search_esc."%' OR vl.employee_code LIKE '%".$search_esc."%' OR vl.visitor_idcard_no LIKE '%".$search_esc."%' OR EXISTS (SELECT 1 FROM visitor_badges vb3 WHERE vb3.visitor_log_id = vl.id AND vb3.full_code LIKE '%".$search_esc."%')) ";
            }else{
                $where .= " AND (vl.name LIKE '%".$search_esc."%' OR vl.phone LIKE '%".$search_esc."%' OR vl.visitor_idcard_no LIKE '%".$search_esc."%' OR EXISTS (SELECT 1 FROM visitor_badges vb3 WHERE vb3.visitor_log_id = vl.id AND vb3.full_code LIKE '%".$search_esc."%')) ";
            }
        }

        return $where;
    }

    function getVisitorsMovementsReport($get){
        global $callConfig;
        $return = $this->getGateReportFilterLookups($get);
        $return['rows'] = array();

        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        if($filters_only == 'Y'){
            return $return;
        }

        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildVisitorMovementsWhere($get, 'visitor');
        $sql = "SELECT vl.*,
            (SELECT vb.full_code FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_code,
            (SELECT vb.returned_at FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_returned_at
            FROM visitor_logs vl
            WHERE ".$where."
            ORDER BY vl.in_datetime DESC";
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $emp_map = $this->getGateReportEmployeeMap($company_id);
        $user_map = $this->getGateReportUserMap($company_id);

        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                $whom_id = isset($r['whom_to_meet']) ? $r['whom_to_meet'] : '';
                $sec_id = isset($r['in_security_id']) ? $r['in_security_id'] : '';
                $purpose = isset($r['purpose']) ? $r['purpose'] : '';
                if($purpose == 'others' && isset($r['purpose_other']) && $r['purpose_other'] != ''){
                    $purpose = $r['purpose_other'];
                }
                $coming_from = '';
                if(isset($r['company_name']) && $r['company_name'] != ''){
                    $coming_from = $r['company_name'];
                }else if(isset($r['address']) && $r['address'] != ''){
                    $coming_from = $r['address'];
                }
                $card_no = '';
                if(isset($r['visitor_idcard_no']) && $r['visitor_idcard_no'] != ''){
                    $card_no = $r['visitor_idcard_no'];
                }else if(isset($r['badge_code']) && $r['badge_code'] != ''){
                    $card_no = $r['badge_code'];
                }
                $return['rows'][] = array(
                    'name' => isset($r['name']) ? $r['name'] : '',
                    'contact_no' => isset($r['phone']) ? $r['phone'] : '',
                    'government_id' => isset($r['idcard_number']) ? $r['idcard_number'] : '',
                    'coming_from' => $coming_from,
                    'purpose' => $purpose,
                    // 'whom_to_meet' => $this->resolveGateReportPersonName($whom_id, $emp_map, $user_map),
                    'whom_to_meet' => $emp_map[$whom_id],
                    'card_no' => $card_no,
                    'in_time' => isset($r['in_datetime']) ? $r['in_datetime'] : '',
                    'out_time' => isset($r['out_datetime']) ? $r['out_datetime'] : '',
                    // 'security_sign' => $this->resolveGateReportPersonName($sec_id, $emp_map, $user_map),
                    'security_sign' => $user_map[$sec_id],
                    'remarks' => isset($r['remarks']) ? $r['remarks'] : ''
                );
            }
        }
        return $return;
    }

    function getVendorsMovementsReport($get){
        global $callConfig;
        $return = $this->getGateReportFilterLookups($get);
        $return['rows'] = array();

        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        if($filters_only == 'Y'){
            return $return;
        }

        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildVisitorMovementsWhere($get, 'vendor_supplier');
        $sql = "SELECT vl.*,
            (SELECT vb.full_code FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_code
            FROM visitor_logs vl
            WHERE ".$where."
            ORDER BY vl.in_datetime DESC";
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $emp_map = $this->getGateReportEmployeeMap($company_id);
        $user_map = $this->getGateReportUserMap($company_id);


        $query = "SELECT id, company, display_name FROM vendors WHERE company_id='{$get['company_id']}' AND is_active=0";
        $vendors_rows = $callConfig->getAllRows($query);
        $vendors = array();
        foreach ($vendors_rows as $row) {
        	$vendors[$row->id] = $row->company ?? $row->display_name;
        }

        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                $whom_id = isset($r['whom_to_meet']) ? $r['whom_to_meet'] : '';
                $sec_id = isset($r['in_security_id']) ? $r['in_security_id'] : '';
                $name = '';
                if(isset($r['vendor_supplier_driver_name']) && $r['vendor_supplier_driver_name'] != ''){
                    $name = $r['vendor_supplier_driver_name'];
                }else if(isset($r['name']) && $r['name'] != ''){
                    $name = $r['name'];
                }
                $contact = '';
                if(isset($r['vendor_supplier_driver_mobile']) && $r['vendor_supplier_driver_mobile'] != ''){
                    $contact = $r['vendor_supplier_driver_mobile'];
                }else if(isset($r['phone']) && $r['phone'] != ''){
                    $contact = $r['phone'];
                }
                $coming_from = '';
                if(isset($r['vendor_supplier_vendor_id']) && $r['vendor_supplier_vendor_id'] != ''){
                	$coming_from = $vendors[$r['vendor_supplier_vendor_id']];
                }

                // if(isset($r['company_name']) && $r['company_name'] != ''){
                //     $coming_from = $r['company_name'];
                // }else if(isset($r['address']) && $r['address'] != ''){
                //     $coming_from = $r['address'];
                // }
                $card_no = '';
                if(isset($r['visitor_idcard_no']) && $r['visitor_idcard_no'] != ''){
                    $card_no = $r['visitor_idcard_no'];
                }else if(isset($r['badge_code']) && $r['badge_code'] != ''){
                    $card_no = $r['badge_code'];
                }
                $gov_id = isset($r['vendor_supplier_proof_document_no']) ? $r['vendor_supplier_proof_document_no'] : '';
                if(is_array($gov_id) || $gov_id == 'Array'){
                    $gov_id = '';
                }
                $return['rows'][] = array(
                    'name' => $name,
                    'contact_no' => $contact,
                    'government_id' => $gov_id,
                    'coming_from' => $coming_from,
                    'purpose' => isset($r['vendor_supplier_purpose']) ? $r['vendor_supplier_purpose'] : '',
                    'whom_to_meet' => $emp_map[$whom_id],
                    'card_no' => $card_no,
                    'in_time' => isset($r['in_datetime']) ? $r['in_datetime'] : '',
                    'out_time' => isset($r['out_datetime']) ? $r['out_datetime'] : '',
                    'security_sign' => $user_map[$sec_id],
                    'remarks' => isset($r['remarks']) ? $r['remarks'] : ''
                );
            }
        }
        return $return;
    }

    function getEmployeeTempIdReport($get){
        global $callConfig;
        $return = $this->getGateReportFilterLookups($get);
        $return['rows'] = array();

        $filters_only = isset($get['filters_only']) ? $get['filters_only'] : '';
        if($filters_only == 'Y'){
            return $return;
        }

        $from_date = isset($get['from_date']) ? $get['from_date'] : '';
        $to_date = isset($get['to_date']) ? $get['to_date'] : '';
        $company_id = isset($get['company_id']) ? $get['company_id'] : '';
        if($company_id == '' || $from_date == '' || $to_date == ''){
            return $return;
        }

        $where = $this->buildVisitorMovementsWhere($get, 'employee');
        $where .= " AND vl.emp_temp_enable = 1 ";
        $sql = "SELECT vl.*,
            (SELECT vb.full_code FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_code,
            (SELECT vb.returned_at FROM visitor_badges vb WHERE vb.visitor_log_id = vl.id AND vb.company_id = vl.company_id ORDER BY vb.id DESC LIMIT 1) AS badge_returned_at
            FROM visitor_logs vl
            WHERE ".$where."
            ORDER BY vl.in_datetime DESC";
        $return['sql'] = $sql;
        $rows = $callConfig->getAllRows($sql);
        $user_map = $this->getGateReportUserMap($company_id);

        if(count((array)$rows) > 0){
            foreach($rows as $row){
                $r = is_array($row) ? $row : (array)$row;
                $sec_id = isset($r['in_security_id']) ? $r['in_security_id'] : '';
                $name = '';
                if(isset($r['employee_name']) && $r['employee_name'] != ''){
                    $name = $r['employee_name'];
                }else if(isset($r['emp_temp_name']) && $r['emp_temp_name'] != ''){
                    $name = $r['emp_temp_name'];
                }else if(isset($r['name']) && $r['name'] != ''){
                    $name = $r['name'];
                }
                $card_no = '';
                if(isset($r['badge_code']) && $r['badge_code'] != ''){
                    $card_no = $r['badge_code'];
                }else if(isset($r['visitor_idcard_no']) && $r['visitor_idcard_no'] != ''){
                    $card_no = $r['visitor_idcard_no'];
                }
                $in_dt = isset($r['in_datetime']) ? $r['in_datetime'] : '';
                $visit_date = '';
                if($in_dt != '' && $in_dt != '0000-00-00 00:00:00'){
                    $visit_date = date('Y-m-d', strtotime($in_dt));
                }
                $card_submitted = '';
                if(isset($r['badge_returned_at']) && $r['badge_returned_at'] != '' && $r['badge_returned_at'] != '0000-00-00 00:00:00'){
                    $card_submitted = $r['badge_returned_at'];
                }
                $return['rows'][] = array(
                    'visit_date' => $visit_date,
                    'id_no' => isset($r['employee_code']) ? $r['employee_code'] : '',
                    'name' => $name,
                    'card_no' => $card_no,
                    'access_no' => '',
                    'in_time' => $in_dt,
                    'access_enable_date' => '',
                    'out_time' => isset($r['out_datetime']) ? $r['out_datetime'] : '',
                    'access_disable_date' => '',
                    'card_submitted_date' => $card_submitted,
                    'security_sign' => $this->resolveGateReportPersonName($sec_id, array(), $user_map),
                    'remarks' => isset($r['remarks']) ? $r['remarks'] : ''
                );
            }
        }
        return $return;
    }

// added by koti 24-07-2026
	


}
?>