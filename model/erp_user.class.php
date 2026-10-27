<?php 
/*require_once "dompdf/autoload.inc.php";
use Dompdf\Dompdf;
use Dompdf\Options;		
*/
class erpUserClass extends userClass{
	function togetinventorysummaryrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND tl.document_date >= '".$get['from_date']."' AND tl.document_date <= '".$get['to_date']."'"; 
		}
		//$query = "SELECT it.id item_id, it.sku, it.item_name, sum(tl.in_qty) in_qty, sum(tl.out_qty) out_qty, 0 ord_qty, 0 sh_qty, 0 cm_qty  FROM item_ledger_st tl, items it WHERE tl.company_id = it.company_id AND tl.company_id =".$get['company_id']." AND tl.item_id = it.id ".$whr." GROUP BY tl.item_id";
		$query = "SELECT 
		it.id item_id, 
        it.sku, it.item_name, 
        sum(tl.in_qty) in_qty, 
        sum(tl.out_qty) out_qty,
        (
            SELECT  SUM(poitm.pending_qty)
            FROM 	purchase_orders po, po_items poitm
            WHERE	po.company_id = poitm.company_id
            AND		po.id = poitm.transaction_id
            AND		poitm.item_id = tl.item_id
            -- AND		po.transaction_date >= '2024-07-01'
            AND		po.transaction_date <= '2024-07-31'
            )ord_qty,
		(
            SELECT 	iwb.closing_qty
            FROM 	item_wa_balances iwb
            WHERE 	iwb.company_id = it.company_id
            AND		iwb.item_id = it.id
            AND 	iwb.balance_date <= '2024-07-31'
            ORDER BY iwb.balance_date DESC
            LIMIT 1
	    ) AS sh_qty, 
        (
            SELECT  SUM(soitm.pending_qty)
            FROM 	sales_orders so, so_items soitm
            WHERE	so.company_id = soitm.company_id
            AND		so.id = soitm.transaction_id
            AND		soitm.item_id = tl.item_id
            -- AND		so.transaction_date >= '2024-07-01'
            AND		so.transaction_date <= '2024-07-31'
            )cm_qty
FROM 	item_ledger_st tl, 
		items it 
WHERE	tl.company_id = it.company_id 
AND		tl.company_id ='".$get['company_id']."' 
AND		tl.item_id = it.id ".$whr."
GROUP BY tl.item_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togeticommittedstockdetailsrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT it.id item_id, it.sku, it.item_name, si.pending_qty qty, so.id so_id, so.transaction_number  FROM sales_orders so, so_items si, items it WHERE  so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.current_status = 199 AND si.pending_qty > 0 AND so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY si.item_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetstocksummaryrepot($get){
		global $callConfig;
		$return['get']= $get;
		//print_r($return); exit;
		// $whr = "";
		// if($get['from_date']!="" && $get['to_date']!=""){
		// 	$whr = " AND tl.document_date >= '".$get['from_date']."' AND tl.document_date <= '".$get['to_date']."'"; 
		// }
		//$query = "SELECT it.id item_id, it.sku, it.item_name, sum(tl.in_qty) in_qty, sum(tl.out_qty) out_qty, 0 open_qty FROM item_ledger_st tl, items it WHERE tl.company_id = it.company_id AND tl.company_id =".$get['company_id']." AND tl.item_id = it.id ".$whr." GROUP BY tl.item_id";
		
		$query = "SELECT it.id item_id,
		(SELECT iwb.closing_qty
         FROM item_wa_balances iwb
         WHERE company_id = it.company_id
         AND   iwb.item_id = it.id
         AND 	iwb.balance_date < '".$get['from_date']."'
         ORDER BY balance_date DESC
         LIMIT 0,1 
         ) open_qty,
		it.sku, 
        it.item_name, 
        sum(tl.in_qty) in_qty, 
        sum(tl.out_qty) out_qty,
		(SELECT iwb.closing_qty
         FROM item_wa_balances iwb
         WHERE company_id = it.company_id
         AND   iwb.item_id = it.id
         AND 	iwb.balance_date <= '".$get['to_date']."'
         ORDER BY balance_date DESC
         LIMIT 0,1 
        ) clos_qty
FROM	item_ledger_st tl, 
		items it 
WHERE 	tl.company_id = it.company_id 
AND 	tl.company_id =".$get['company_id']."
AND 	tl.item_id = it.id 
AND 	tl.document_date >= '".$get['from_date']."' AND tl.document_date <= '".$get['to_date']."' 
GROUP BY tl.item_id";

		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}

	function togetStockValueSummaryRepot($get){
		global $callConfig;
		$return['get']= $get;
		$query = "SELECT it.id item_id,
		(SELECT iwb.closing_qty
         FROM item_wa_balances iwb
         WHERE company_id = it.company_id
         AND   iwb.item_id = it.id
         AND 	iwb.balance_date < '".$get['from_date']."'
         ORDER BY balance_date DESC
         LIMIT 0,1 
         ) open_qty,
		it.sku, 
        it.item_name, 
        sum(tl.in_qty) in_qty, 
        sum(tl.out_qty) out_qty,
		(SELECT iwb.closing_qty
         FROM item_wa_balances iwb
         WHERE company_id = it.company_id
         AND   iwb.item_id = it.id
         AND 	iwb.balance_date <= '".$get['to_date']."'
         ORDER BY balance_date DESC
         LIMIT 0,1 
        ) clos_qty
FROM	item_ledger_st tl, 
		items it 
WHERE 	tl.company_id = it.company_id 
AND 	tl.company_id =".$get['company_id']."
AND 	tl.item_id = it.id 
AND 	tl.document_date >= '".$get['from_date']."' AND tl.document_date <= '".$get['to_date']."' 
GROUP BY tl.item_id";

		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}

	function togetactivepurchaseorderrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT it.id item_id, it.sku, it.item_name, si.pending_qty qty, si.item_amount , si.pending_qty * si.item_amount total,  so.id so_id, so.transaction_number  FROM purchase_orders so, po_items si, items it WHERE  so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.current_status = 0 AND so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY si.item_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetproductsalesrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT it.id item_id, it.sku, it.item_name, si.pending_qty qty, so.id so_id, so.transaction_number, it.purchase_price, it.sale_price, it.sale_price-it.purchase_price margin, (it.sale_price*0.18)+it.sale_price salewithprice   FROM sales_orders so, so_items si, items it WHERE  so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.current_status = 199 AND si.pending_qty > 0 AND so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY si.item_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetinventoryvaluationsummaryrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND tl.document_date >= '".$get['from_date']."' AND tl.document_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT it.id item_id, it.sku, it.item_name, it.purchase_price, sum(tl.in_qty) in_qty, sum(tl.out_qty) out_qty, 0 ord_qty, 0 sh_qty, 0 cm_qty  FROM item_ledger_st tl, items it WHERE tl.company_id = it.company_id AND tl.company_id =".$get['company_id']." AND tl.item_id = it.id ".$whr." GROUP BY tl.item_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetfifocostlottrackingrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND tl.document_date >= '".$get['from_date']."' AND tl.document_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT it.id item_id, it.sku, it.item_name, it.purchase_price, tl.in_qty, tl.out_qty, tl.document_date_time, tl.document_type, tl.document_no, it.purchase_price, tl.in_value, tl.out_value FROM item_ledger_st tl, items it, item_lot_nos iln WHERE tl.company_id = it.company_id AND tl.company_id = it.company_id AND tl.company_id = ".$get['company_id']." AND tl.item_id = it.id AND it.id = iln.item_id AND tl.document_no = iln.transaction_id AND tl.document_type = iln.transaction_table";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	
	function togetpurchasesbyitemrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT it.id item_id, it.sku, it.item_name, sum(si.item_quantity) qty, sum(si.item_amount) total FROM purchase_invoices so, pi_items si, items it WHERE so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.current_status = '".ST_APPROVED."' AND so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY si.item_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetreceiveshistoryrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT  so.transaction_date, so.party_name, so.party_id, sum(si.item_quantity) qty,  so.transaction_number, po.transaction_number po_transaction_number FROM goods_receipt_notes so, grn_items si, items it,purchase_orders po WHERE  so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id = po.company_id AND  so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.ref_id = po.id AND so.current_status = 199 AND so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY si.transaction_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetpurchasebyvendorsrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT  so.transaction_date, so.party_name, so.party_id, sum(si.item_quantity) qty, so.transaction_number, sp.name status_name FROM purchase_orders so, po_items si, status_progress sp WHERE  so.company_id = si.company_id AND so.company_id = sp.company_id AND so.company_id =".$get['company_id']."  AND so.id = si.transaction_id AND  so.current_status = sp.status AND  so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY si.transaction_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}	
	function togetpaymentreceivedrepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND pay.payment_date >= '".$get['from_date']."' AND pay.payment_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT pay.id, pay.transaction_no, pay.payment_date, pay.payment_type, pay.payment_process, pay.ref_no, pay.remarks ,  pa.transaction_no, pa.party_id, pa.total_paid_amonut, ven.company , ven.display_name FROM payments pay, payment_allocations pa, vendors ven WHERE pay.company_id = ".$get['company_id']." AND pay.company_id = pa.company_id AND pay.company_id = ven.company_id AND pay.id = pa.payment_id AND pay.is_active = pa.is_active AND pay.is_active = ven.is_active AND pay.is_active = 0 AND pay.transaction_type = 'vendors' AND ven.id = pa.party_id ".$whr;
		//$query = "SELECT py.*, vn.display_name FROM payments py, vendors vn WHERE py.company_id = vn.company_id AND py.company_id = ".$get['company_id']." AND py.party_id = vn.id ".$whr." AND py.is_active =0";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function itemwarehouseledger($get){
		global $callConfig;
		$return['get']= $get;
		$item_con = "";
		$return['data']= array();
		if($get['itemids']!=""){
			$item_con = " AND ilw.item_id IN (".$get['itemids'].")";
			$opening_item_con = " AND itm.id IN (".$get['itemids'].")";
			//$opening_balance_condition = " AND ilw.item_id IN (".$get['itemids'].")";
		/**$query = "SELECT 
		    itm.id,
		    itm.sku,
		    itm.item_name,
		    itm.item_type,
		    (
		        SELECT  its.item_type_name
		        FROM 	item_types its
		        WHERE	its.company_id = itm.company_id
		        AND		its.id = itm.item_type
		     )item_type_name,
		    (
		        SELECT  itc.category_description
		        FROM 	item_categories itc
		        WHERE	itc.company_id = itm.company_id
		        AND		itc.id = itm.category_id
		     )item_category_name,     
		    itm.item_image,
		    ilw.document_type,
		    ilw.document_ref_no,
		    ilw.document_date,
		    ilw.in_value,
		    ilw.out_value,
		    ilw.in_qty,
		    ilw.out_qty,
		    @running_total := @running_total + ilw.in_qty - ilw.out_qty AS running_total
		FROM
		    items itm
		JOIN
		    item_ledger_wa ilw ON ilw.company_id = itm.company_id AND ilw.item_id = itm.id
		JOIN
		    (SELECT @running_total := 0) r
		WHERE
		    itm.company_id = 1
		    ".$item_con."
		    AND ilw.document_date >= '".$get['from_date']."'
		    AND ilw.document_date <= '".$get['to_date']."'
		ORDER BY
		    itm.id,
		    ilw.document_date,
		    ilw.id"; **/
		//  $query = "SELECT itm.id, itm.sku, itm.item_name, itm.item_type,(SELECT its.item_type_name FROM item_types its WHERE its.company_id = itm.company_id AND its.id = itm.item_type) AS item_type_name,(SELECT itc.category_description FROM item_categories itc WHERE itc.company_id = itm.company_id AND itc.id = itm.category_id ) AS item_category_name, itm.item_image, ilw.document_type, ilw.document_ref_no, ilw.document_date, ilw.in_value, ilw.out_value, ilw.in_qty, ilw.out_qty, @running_total := @running_total + IFNULL(ilw.in_qty, 0) - IFNULL(ilw.out_qty, 0) AS running_total FROM items itm LEFT JOIN item_ledger_wa ilw ON ilw.company_id = itm.company_id AND ilw.item_id = itm.id AND ilw.document_date >= '".$get['from_date']."' AND ilw.document_date <= '".$get['to_date']."' JOIN (SELECT @running_total := 0) r WHERE itm.company_id = '".$get['company_id']."'  ".$opening_item_con." ORDER BY itm.id, ilw.document_date, ilw.id"; 
		    $query = "SELECT itm.id,itm.sku,itm.item_name,itm.item_type, (SELECT its.item_type_name FROM item_types its WHERE its.company_id = itm.company_id AND its.id = itm.item_type) AS item_type_name,(SELECT itc.category_description FROM item_categories itc WHERE itc.company_id = itm.company_id AND itc.id = itm.category_id) AS item_category_name, itm.item_image, ilw.document_type, ilw.document_ref_no, ilw.document_date,ilw.in_value, ilw.out_value, ilw.in_qty, ilw.out_qty, @running_qty := IF(ilw.document_date IS NULL, opening_balances.opening_qty, @running_qty + IFNULL(ilw.in_qty, 0) - IFNULL(ilw.out_qty, 0)) AS running_quantity,    @running_value := IF(ilw.document_date IS NULL, opening_balances.opening_value, @running_value + IFNULL(ilw.in_value, 0) - IFNULL(ilw.out_value, 0)) AS running_value FROM items itm LEFT JOIN item_ledger_wa ilw ON ilw.company_id = itm.company_id AND ilw.item_id = itm.id AND ilw.document_date >= '".$get['from_date']."' AND ilw.document_date <= '".$get['to_date']."' LEFT JOIN (SELECT ilw_open.item_id, SUM(IFNULL(ilw_open.in_qty, 0) - IFNULL(ilw_open.out_qty, 0)) AS opening_qty, SUM(IFNULL(ilw_open.in_value, 0) - IFNULL(ilw_open.out_value, 0)) AS opening_value FROM item_ledger_wa ilw_open WHERE ilw_open.document_date < '".$get['from_date']."' GROUP BY ilw_open.item_id) opening_balances ON opening_balances.item_id = itm.id JOIN (SELECT @running_qty := 0, @running_value := 0) r WHERE '".$get['company_id']."'  ".$opening_item_con." ORDER BY itm.id, ilw.document_date,ilw.id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		}
		$query = "SELECT ilw.item_id, SUM(ilw.in_qty)- SUM(ilw.out_qty) cc FROM item_ledger_wa ilw WHERE 
		ilw.company_id = '".$get['company_id']."'  AND ilw.document_date < '".$get['from_date']."' ".$item_con." AND ilw.is_active =0 GROUP BY ilw.item_id";
		$itembalances = $callConfig->getAllRows($query);
		foreach($itembalances as $itembalan){
			$data_banlace[$itembalan->item_id] = $itembalan->cc;
		}
		$return['open_balances_sql'] = $query;
		$return['open_balances']= $data_banlace;
		$query = "SELECT id, item_name,sku  FROM items";
		$return['items_sql'] = $query;
		$items = $callConfig->getAllRows($query);
		$return['items']= $items;

		return $return;
	}
	function togetthreewaymatch($get){
		global $callConfig;
		$return['get']= $get;
		$return['data']= array();
		if($get['vendors']!=""){
			$query = "
			SELECT
			    sb.transaction_number,
			    sb.transaction_date,
			    sb.party_id,
			    (
			        SELECT vend.display_name
			        FROM 	vendors vend
			        WHERE	vend.company_id = sb.company_id
			        AND		vend.id = sb.party_id 
			        ) vendor_name,
			    sb.currency_id,
			    (
			        SELECT cur.currency_short_code
			        FROM 	currencies cur
			        WHERE	cur.company_id = sb.company_id
			        AND		cur.id = sb.currency_id 
			        ) curr_name,
			    sb.payment_terms payment_term_id,
			    (SELECT pt.payment_term_name
                    FROM 	payment_terms pt
                    WHERE	pt.company_id = sb.company_id
                    AND		pt.id = sb.payment_terms
                    )payment_terms,

			    sb.amount,
			    sb.tax_amount,
			    sb.discount_amount,
			    sb.total_amount,
			    sb.ref_type,
			    sb.ref_id AS po_id,
			    sb.ref_no AS grn_no,
			    poitm.item_quantity AS po_item_quantity,
			    sbitm.item_quantity AS sb_item_quantity,
			    grnitm.item_quantity AS grn_item_quantity,
			    CASE
			        WHEN ABS(grnitm.item_quantity - sbitm.item_quantity) > 0 THEN 'PO & SB Qty mismatch'
			        ELSE NULL
			    END AS qty_remark,
			    CASE
			        WHEN ABS(poitm.item_price  - sbitm.item_price ) > 0.01 THEN 'PO & SB Price mismatch'
			        ELSE NULL
			    END AS price_remark,
			    CASE
			        WHEN ABS(poitm.tax_amount / poitm.item_quantity - sbitm.tax_amount / sbitm.item_quantity) > 0.01 THEN 'PO & SB Tax mismatch'
			        ELSE NULL
			    END AS tax_remark,
			    CASE
			        WHEN ABS(poitm.total_amount / poitm.item_quantity - sbitm.total_amount / sbitm.item_quantity) > 0.01 THEN 'PO & SB Total Amount mismatch'
			        ELSE NULL
			    END AS total_remarks,
			    ((po.amount / poitm.item_quantity) * sbitm.item_quantity) AS po_amount,
			    ((po.tax_amount / poitm.item_quantity) * sbitm.item_quantity) AS po_tax_amount,
			    ((po.total_amount / poitm.item_quantity) * sbitm.item_quantity) AS po_total_amount,
			    ((grn.amount / grnitm.item_quantity) * sbitm.item_quantity) AS grn_amount,
			    ((grn.tax_amount / grnitm.item_quantity) * sbitm.item_quantity) AS grn_tax_amount,
			    ((grn.total_amount / grnitm.item_quantity) * sbitm.item_quantity) AS grn_total_amount
			FROM
			    supplier_bills sb
			LEFT JOIN sb_items sbitm ON sb.company_id = sbitm.company_id AND sb.id = sbitm.transaction_id
			LEFT JOIN purchase_orders po ON sb.ref_id = po.id
			LEFT JOIN po_items poitm ON po.company_id = poitm.company_id AND po.id = poitm.transaction_id AND sbitm.item_id = poitm.item_id
			LEFT JOIN goods_receipt_notes grn ON sb.ref_no = grn.id
			LEFT JOIN grn_items grnitm ON grn.company_id = grnitm.company_id AND grn.id = grnitm.transaction_id 
			AND sbitm.item_id = grnitm.item_id
			WHERE   
			sb.company_id = ".$get['company_id']."
			AND 	sb.transaction_date >= '".$get['from_date']."'
			AND 	sb.transaction_date  <= '".$get['to_date']."'
			AND     sb.party_id IN (".$get['vendors'].")
			AND 	sb.current_status = ".ST_APPROVED."
			AND 	po.current_status = ".ST_APPROVED."
			AND 	grn.current_status = ".ST_APPROVED."
			AND     sb.is_active = 0
			AND     poitm.is_active = 0
			AND     sbitm.is_active = 0
			AND     grnitm.is_active = 0";
			$return['query'] = $query;
			$data = $callConfig->getAllRows($query);
			$return['data']= $data;
		}
		$query = "SELECT id, company, display_name  FROM vendors";
		$vendors = $callConfig->getAllRows($query);
		$return['vendors']= $vendors;
		return $return;
	}
	//Koteswar code added start
	function togetvendorbalances($get){
		// return $get;
		global $callConfig;
		$return['get']= $get;
		$vendor_ids = $get['vendor_ids'];


		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND  pi.transaction_date >= '".$get['from_date']."' AND pi.transaction_date <= '".$get['to_date']."'"; 
		}
		if($vendor_ids != ''){
			$whr .= " AND v.id IN ($vendor_ids) ";
		}
		$query = "SELECT 
			v.company AS vendor_name,                          -- Vendor Name
			pi.transaction_number AS invoice_number,           -- Invoice Number
			pi.transaction_date AS invoice_date,               -- Invoice Date
			pi.total_amount AS amount_due,                           -- Amount Due
			COALESCE(SUM(p.paid_amonut), 0) AS paid_amount,    -- Paid Amount
			(pi.total_amount - COALESCE(SUM(p.paid_amonut), 0)) AS balance_remaining,  -- Balance Remaining
			MAX(pi.due_date) AS payment_due_date,                   -- Payment Due Date
			CASE 
				WHEN pa.payment_process = 'Completed' THEN 'Paid' 
				ELSE 'Pending' 
			END AS payment_status,                            -- Payment Status
			pi.notes AS notes                                  -- Notes
		FROM 
			vendors v
		LEFT JOIN 
			purchase_invoices pi ON v.id = pi.party_id AND pi.is_active = 0 AND v.company_id = pi.company_id
		LEFT JOIN 
			payment_allocations p ON pi.id = p.transaction_id AND p.is_active = 0 AND v.company_id = p.company_id 
       LEFT JOIN payments pa ON pa.id = p.payment_id AND v.company_id = pa.company_id
			WHERE v.company_id = '".$get['company_id']."' ".$whr." AND  v.is_active=0 	   
		GROUP BY v.id
		ORDER BY vendor_name, pi.transaction_date";
		$return['query'] = $query;
		// return $return;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetbilldetails($get){
		// return $get;
		global $callConfig;
		$return['get']= $get;
		$vendor_ids = $get['vendor_ids'];

		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " pi.transaction_date >= '".$get['from_date']."' AND pi.transaction_date <= '".$get['to_date']."' AND "; 
		}
		if($vendor_ids != ''){
			$whr .= " v.id IN ($vendor_ids) AND ";
		}
		$query = "SELECT
		    pi.transaction_number AS bill_number,  -- Bill Number (Invoice Number)
		    pi.transaction_date AS bill_date,      -- Bill Date (Invoice Date)
		    v.company AS vendor_name,         -- Vendor Name
		    SUM(pi.amount) AS amount_due,               -- Amount Due
		    COALESCE(SUM(p.paid_amonut), 0) AS amount_paid,  -- Amount Paid
		    (SUM(pi.amount) - COALESCE(SUM(p.paid_amonut), 0)) AS balance_due,  -- Balance Due
		    MAX(pi.due_date) as due_date,
		    MAX(pa.payment_date) as payment_date,                          -- Payment Due Date
		    CASE 
		        WHEN (pi.amount - COALESCE(SUM(p.paid_amonut), 0)) = 0 THEN 'Paid' 
		        ELSE 'Pending' 
		    END AS payment_status,                -- Payment Status (Paid/Pending)
		    pi.notes                               -- Notes
		FROM 
		    purchase_invoices pi
		LEFT JOIN 
		    vendors v ON pi.party_id = v.id AND pi.company_id = v.company_id AND v.is_active=0  
		LEFT JOIN 
		    payment_allocations p ON p.transaction_id = pi.id AND pi.company_id = p.company_id AND p.is_active=0  
		LEFT JOIN payments pa ON pa.id = p.payment_id AND pa.company_id = p.company_id AND pa.is_active=0
		WHERE ".$whr." pi.company_id= ".$get['company_id']." AND
		    pi.is_active = 0 
		GROUP BY 
		    pi.transaction_number, pi.transaction_date, v.company
		ORDER BY 
		    pi.transaction_date";
		$return['query'] = $query;
		// return $return;

		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetvendorcreditdetails($get){
		// return $get;
		global $callConfig;
		$return['get']= $get;
		$vendor_ids = $get['vendor_ids'];

		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " cn.transaction_date >= '".$get['from_date']."' AND cn.transaction_date <= '".$get['to_date']."' AND "; 
		}
		if($vendor_ids != ''){
			$whr .= " v.id IN ($vendor_ids) AND ";
		}
		$query = "SELECT     cn.transaction_number AS credit_note_number,                -- Credit Note Number (Invoice Number)
		    cn.transaction_date AS credit_date,                           -- Credit Date (Invoice Date)
		    v.company AS vendor_name,                                -- Vendor Name
		    SUM(cn.total_amount) AS amount,                                    -- Credit Amount (Total Credit Amount)
		    COALESCE(SUM(p.paid_amonut), 0) AS amount_paid,               -- Amount Paid
		    (SUM(cn.total_amount) - COALESCE(SUM(p.paid_amonut), 0)) AS balance_due, -- Balance Due
		    MAX(cn.due_date) as due_date,                                                  -- Due Date
		    CASE 
		        WHEN (cn.total_amount - COALESCE(SUM(p.paid_amonut), 0)) = 0 THEN 'Paid' 
		        ELSE 'Pending' 
		    END AS status,                                                -- Payment Status (Paid or Pending)
		    cn.notes                                                      -- Notes
		FROM 
		    credit_notes cn
		LEFT JOIN 
		    vendors v ON cn.party_id = v.id  AND cn.company_id = v.company_id AND v.is_active=0 
		LEFT JOIN 
		    payment_allocations p ON  v.id = p.party_id  AND p.company_id = v.company_id AND p.is_active=0 
		WHERE ".$whr." cn.company_id= ".$get['company_id']." AND
		    cn.is_active = 0                                            
		    AND cn.total_amount > 0                                      
		GROUP BY 
		    cn.transaction_number, v.company, cn.transaction_date
		ORDER BY 
    		cn.transaction_date DESC";
		$return['query'] = $query;

		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetpaymentsmade($get){
		// return $get;
		global $callConfig;
		$return['get']= $get;
		$vendor_ids = $get['vendor_ids'];

		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " p.payment_date >= '".$get['from_date']."' AND p.payment_date <= '".$get['to_date']."' AND "; 
		}
		if($vendor_ids != ''){
			$whr .= " v.id IN ($vendor_ids) AND ";
		}
		$query = "SELECT 
		    pa.transaction_no AS payment_number,       -- Payment Number
		    v.company AS vendor_name,             -- Vendor Name
		    p.payment_date AS payment_date,            -- Payment Date (from the payments table)
		    p.ref_no,
		    SUM(pa.total_paid_amonut) AS amount_paid,       -- Amount Paid (from payment allocations table)
		    p.payment_type AS payment_method,     -- Payment Method (from payment allocations table)
		    CASE 
		        WHEN (sum(pa.total_amount) - SUM(pa.total_paid_amonut)) = 0 THEN 'Completed' 
		        ELSE 'Pending' 
		    END AS payment_status,                    -- Payment Status (based on allocation)
		    p.transaction_no AS reference_number,     -- Reference Number (from payments table)
		    p.remarks AS remarks                               -- No Notes column (placeholder)
		FROM 
		    payment_allocations pa
		LEFT JOIN 
		    payments p ON pa.payment_id = p.id AND pa.company_id = p.company_id AND p.is_active=0  
		LEFT JOIN 
		    vendors v ON pa.party_id = v.id AND pa.company_id = v.company_id AND v.is_active=0  
		WHERE ".$whr." pa.company_id= ".$get['company_id']." AND
		    pa.is_active = 0                       
		GROUP BY v.company, p.payment_date
		ORDER BY 
		    p.payment_date DESC";
		$return['query'] = $query;

		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function togetpodetails($get){
		// return $get;
		global $callConfig;
		$return['get']= $get;
		$vendor_ids = $get['vendor_ids'];

		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " po.transaction_date >= '".$get['from_date']."' AND po.transaction_date <= '".$get['to_date']."' AND "; 
		}
		if($vendor_ids != ''){
			$whr .= " v.id IN ($vendor_ids) AND ";
		}
		$query = "SELECT 
		    po.transaction_number AS po_number,              -- PO Number
		    po.transaction_date AS po_date,                  -- PO Date
		    v.company AS vendor_name,                   -- Vendor Name
		    pi.item_name AS item_name,                       -- Item Name
		    sum(pi.item_quantity) AS item_quantity,               -- Item Quantity
		    sum(pi.pending_qty) AS pending_qty,
		    pi.item_price AS item_price,                     -- Item Price
		    sum(pi.total_amount) AS item_total_amount            -- Item Total Amount
		FROM 
		    purchase_orders po
		LEFT JOIN 
		    vendors v ON po.party_id = v.id AND po.company_id = v.company_id AND v.is_active=0  
		LEFT JOIN 
		    po_items pi ON po.transaction_number = pi.transaction_id  AND po.company_id = pi.company_id AND pi.is_active=0 
		WHERE ".$whr." 
		   po.company_id= ".$get['company_id']." AND po.is_active = 0  
		GROUP BY v.company,po.transaction_number, po.transaction_date                          
		ORDER BY 
		    po.transaction_date DESC";
		$return['query'] = $query;
		// return $return;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	//koteswar code added end

	function getBreakingDownItemProcess($get){
		global $callConfig;
		$return['get']= $get;
		$itemsql = "SELECT id, sku, item_name FROM items WHERE company_id =".$get['company_id']." AND is_active = 0";
		$itemres = $callConfig->getAllRows($itemsql);
		$return['items']= $itemres;

		$uomsql = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements WHERE company_id =".$get['company_id']." AND is_active = 0";
		$uomres = $callConfig->getAllRows($uomsql);
		$return['unit_of_measurements']= $uomres;


			

		return $return;
	}
	function togetSalesByItemRepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT it.id item_id, it.sku, it.item_name, sum(si.item_quantity) qty, sum(si.item_amount) total FROM sales_invoices so, si_items si, items it WHERE so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.current_status = '".ST_APPROVED."' AND so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY si.item_id";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;

	}
	function toGetOrderFulfilmentByItemRepot($get){
		global $callConfig;
		$return['get']= $get;
		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."'"; 
		}
		$query = "SELECT so.id, si.item_id, it.sku, it.item_name, sum(si.item_quantity) as order_qty FROM sales_orders so, so_items si, items it WHERE so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.current_status = '".ST_APPROVED."' AND so.is_active = 0 AND si.is_active =0  ".$whr." GROUP BY so.id, si.item_id, it.sku, it.item_name";
		$return['query'] = $query;
		$data = $callConfig->getAllRows($query);
		$so_id_array = array();
		foreach($data as $do_detail){
			$so_id_array[$do_detail->id] = $do_detail->id;
		}
		$return['data_sales_orders']= $data;
		$return['so_id_array']= $so_id_array;

		if(count((array)$so_id_array)!=0){

			$query1 = "SELECT so.id, si.item_id, it.sku, it.item_name, sum(si.item_quantity) as fulfilment_qty FROM delivery_orders so, do_items si, items it WHERE so.company_id = si.company_id AND so.company_id = it.company_id AND so.company_id =".$get['company_id']." AND si.item_id = it.id AND so.id = si.transaction_id AND so.current_status = '".ST_APPROVED."' AND so.is_active = 0 AND so.ref_id IN (".implode(',', $so_id_array).") AND si.is_active =0 GROUP BY so.id, si.item_id, it.sku, it.item_name";
			$return['query1'] = $query1;
			$data1 = $callConfig->getAllRows($query1);
			$data_delivery_orders = array();
			foreach($data1 as $do_detail){
				$data_delivery_orders[$do_detail->item_id] = $do_detail;
			}
			$return['data_delivery_orders']= $data_delivery_orders;
		}


		return $return;
	}
		function togetCustomerBalances($get){
		// return $get;
		global $callConfig;
		$return['get']= $get;
		$vendor_ids = $get['vendor_ids'];


		$whr = "";
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr = " AND  pi.transaction_date >= '".$get['from_date']."' AND pi.transaction_date <= '".$get['to_date']."'"; 
		}
		if($vendor_ids != ''){
			$whr .= " AND v.id IN ($vendor_ids) ";
		}
		$query = "SELECT 
			v.company AS vendor_name,                          -- Vendor Name
			pi.transaction_number AS invoice_number,           -- Invoice Number
			pi.transaction_date AS invoice_date,               -- Invoice Date
			pi.total_amount AS amount_due,                           -- Amount Due
			COALESCE(SUM(p.paid_amonut), 0) AS paid_amount,    -- Paid Amount
			(pi.total_amount - COALESCE(SUM(p.paid_amonut), 0)) AS balance_remaining,  -- Balance Remaining
			MAX(pi.due_date) AS payment_due_date,                   -- Payment Due Date
			CASE 
				WHEN pa.payment_process = 'Completed' THEN 'Paid' 
				ELSE 'Pending' 
			END AS payment_status,                            -- Payment Status
			pi.notes AS notes                                  -- Notes
		FROM 
			customers v
		LEFT JOIN 
			sales_invoices pi ON v.id = pi.party_id AND pi.is_active = 0 AND v.company_id = pi.company_id
		LEFT JOIN 
			receipt_allocations p ON pi.id = p.transaction_id AND p.is_active = 0 AND v.company_id = p.company_id 
       LEFT JOIN receipts pa ON pa.id = p.receipt_id AND v.company_id = pa.company_id
			WHERE v.company_id = '".$get['company_id']."' ".$whr." AND  v.is_active=0 	   
		GROUP BY v.id
		ORDER BY vendor_name, pi.transaction_date";
		$return['query'] = $query;
		// return $return;
		$data = $callConfig->getAllRows($query);
		$return['data']= $data;
		return $return;
	}
	function getStockBalanceAvailability($get){
		global $callConfig;
		//echo "<pre>";
		$return['get']= $get;
		$transaction_table = $get['transaction_table'];
		$transaction_id = $get['transaction_id'];
		
		if($transaction_table =='kittings' || $transaction_table =='Kittings'){
			$tranitmsql = "SELECT kiti.item_id, SUM(kiti.quantity) as sum_item_quantity, (SELECT CONCAT(item_name,' (',SKU,')') FROM items itm WHERE itm.id = kiti.item_id) as item_name FROM kitting_items kiti WHERE kiti.company_id = '".$get['company_id']."' AND kiti.transaction_id = '".$transaction_id."' AND kiti.is_active = 0 GROUP BY kiti.item_id";	
			$tranitmresult = $callConfig->getAllRows($tranitmsql);
			$transactionitems = $item_wa_balances =$errorthrow =  array();
			foreach($tranitmresult as $tritm){
				$transactionitemsid[$tritm->item_id] = $tritm->item_id;
				$transactionitemsdetail[$tritm->item_id] = $tritm;
			}
		}elseif($transaction_table =='dekittings'){
			$tranitmsql = "SELECT diki.parent_item_id as item_id, SUM(diki.parent_quantity) as sum_item_quantity, (SELECT CONCAT(item_name,' (',SKU,')') FROM items itm WHERE itm.id = kiti.item_id) as item_name FROM dekittings diki WHERE diki.company_id = '".$get['company_id']."' AND diki.id = '".$transaction_id."' AND diki.is_active = 0 GROUP BY diki.parent_item_id";	
			$tranitmresult = $callConfig->getAllRows($tranitmsql);
			$transactionitems = $item_wa_balances =$errorthrow =  array();
			foreach($tranitmresult as $tritm){
				$transactionitemsid[$tritm->item_id] = $tritm->item_id;
				$transactionitemsdetail[$tritm->item_id] = $tritm;
			}
		}else{
			$fieldarray  = $this->toGetDynamicTables($transaction_table,$get['page_option']);
			$return['fieldarray'] =$fieldarray; 
			$tranitmsql = "SELECT item_id, item_name, SUM(item_quantity) as sum_item_quantity FROM ".$fieldarray[$transaction_table]['item']." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$transaction_id."' AND is_active = 0 GROUP BY item_id, item_name";	
			$tranitmresult = $callConfig->getAllRows($tranitmsql);
			$transactionitems = $item_wa_balances =$errorthrow =  array();
			foreach($tranitmresult as $tritm){
				$transactionitemsid[$tritm->item_id] = $tritm->item_id;
				$transactionitemsdetail[$tritm->item_id] = $tritm;
			}
		}
		$return['tranitmsql'] =$tranitmsql; 
		$return['transactionitemsid'] =$transactionitemsid; 
		$return['transactionitemsdetail'] =$transactionitemsdetail; 
	
		if(count((array)$transactionitemsid)!=0){
			$itemids = implode(",",(array)$transactionitemsid);
			
			$isbsql =  "SELECT iwb.item_id, iwb.balance_date, iwb.closing_qty FROM item_wa_balances iwb JOIN ( SELECT item_id, MAX(balance_date) AS max_date FROM item_wa_balances WHERE company_id = '".$get['company_id']."' AND item_id IN (".$itemids.") GROUP BY item_id ) latest ON iwb.item_id = latest.item_id AND iwb.balance_date = latest.max_date WHERE iwb.company_id = '".$get['company_id']."'";
			$isbres = $callConfig->getAllRows($isbsql);
			foreach($isbres as $balitem){
				$item_wa_balances[$balitem->item_id]= $balitem;
			}
			$return['isbsql'] =$isbsql; 
			$return['item_wa_balances'] =$item_wa_balances; 
		}	

		$stocknotcheckrequird = array();
		if(count((array)$transactionitemsid)!=0){
			$itemids = implode(",",(array)$transactionitemsid);
			
			$itmsql =  "SELECT id FROM items WHERE company_id = '".$get['company_id']."' AND id IN (".$itemids.") AND negative_stock = '0' AND is_active =0";
			$itmres = $callConfig->getAllRows($itmsql);
			foreach($itmres as $balitem){
				$stocknotcheckrequird[$balitem->id]= $balitem->id;
			}
		}
		foreach($transactionitemsdetail as $itmid => $itmdetails){
			if(!isset($stocknotcheckrequird[$itmid])){
				if(isset($item_wa_balances[$itmid])){
					$balancedetail = $item_wa_balances[$itmid];
					$sum_item_quantity = $itmdetails->sum_item_quantity;
					if($balancedetail->closing_qty < $sum_item_quantity){
						$itmdetails->closing_qty = $balancedetail->closing_qty;
						$errorthrow[] = $itmdetails;
					}
				}else{
					$itmdetails->closing_qty = 0;
					$errorthrow[] = $itmdetails;
				}
			}	
		}
		$return['errorthrow'] = array('details'=>$errorthrow,'errorcount'=>count((array)$errorthrow));
		//print_r($return);
		return $return;	
	
	}

	function getKittings($get){
	    global $callConfig;
	    $queryitm = "SELECT itm.id, itm.sku, itm.item_name, itm.purchase_uom, itm.sold_individually, itm.sold_in_groups, iwb.balance_date, iwb.day_rate FROM items itm LEFT JOIN item_wa_balances iwb ON itm.company_id = '".$get['company_id']."' AND itm.id = iwb.item_id AND iwb.company_id = itm.company_id AND itm.is_active = 0 AND iwb.balance_date = (SELECT MAX(iwb1.balance_date) FROM item_wa_balances iwb1 WHERE iwb1.item_id = itm.id AND iwb1.company_id = itm.company_id) GROUP BY itm.id, itm.sku, itm.item_name, itm.purchase_uom";
	    $item = $callConfig->getAllRows($queryitm);

	    $query = "SELECT id, uom_short_code FROM unit_of_measurements WHERE company_id = '" . $get['company_id'] . "' AND is_active=0";
	    $uom = $callConfig->getAllRows($query);

	 	if($get['rid'] !=""){
	        $kittingQuery = "SELECT * FROM kittings WHERE company_id = '" . $get['company_id'] . "' AND id = '" . $get['rid'] . "' AND is_active = 0";
	        $kittingData = $callConfig->getRow($kittingQuery);
	 
	        $itemsQuery = "SELECT * FROM kitting_items WHERE company_id = '" . $get['company_id'] . "' AND transaction_id = '" . $get['rid'] . "' AND is_active = 0";
	        $itemsData = $callConfig->getAllRows($itemsQuery);
	 	}
        return ['kittings' => $kittingData,'items' => $itemsData,'item' => $item,'uom' => $uom];
    }
    function getDekittings($get){
        global $callConfig;
        $queryitm = "SELECT itm.id, itm.sku, itm.item_name, itm.purchase_uom, itm.sold_individually, itm.sold_in_groups, iwb.balance_date, iwb.day_rate FROM items itm LEFT JOIN item_wa_balances iwb ON itm.company_id = '".$get['company_id']."' AND itm.id = iwb.item_id AND iwb.company_id = itm.company_id AND itm.is_active = 0 AND iwb.balance_date = (SELECT MAX(iwb1.balance_date) FROM item_wa_balances iwb1 WHERE iwb1.item_id = itm.id AND iwb1.company_id = itm.company_id) GROUP BY itm.id,itm.sku, itm.item_name, itm.purchase_uom";
	    $item = $callConfig->getAllRows($queryitm);

	    $query = "SELECT id, uom_short_code FROM unit_of_measurements WHERE company_id = '" . $get['company_id'] . "' AND is_active=0";
	    $uom = $callConfig->getAllRows($query);

	 	if($get['rid'] !=""){
	        $dekittingQuery = "SELECT * FROM dekittings WHERE company_id = '" . $get['company_id'] . "' AND id = '" . $get['rid'] . "' AND is_active = 0";
	        $dekittingData = $callConfig->getRow($dekittingQuery);
	 
	        $itemsQuery = "SELECT * FROM dekitting_items WHERE company_id = '" . $get['company_id'] . "' AND transaction_id = '" . $get['rid'] . "' AND is_active = 0";
	        $itemsData = $callConfig->getAllRows($itemsQuery);
	 	}
        return ['dekittings' => $dekittingData,'items' => $itemsData,'item' => $item,'uom' => $uom];
    }
    function getDailyAndWeeklyTransactions($get){
    	global $callConfig;
    	$returntrans = array();
    	$return['get'] = $get; 
    	$transaction_tables = json_decode(base64_decode($get['transaction_tables']),1);
    	$return['transaction_tables'] = $transaction_tables;
    	foreach($transaction_tables as $transtab){
    		$dateyearary = array();
	        $query = "SELECT count(id) as cnt, DATE_FORMAT(transaction_date,'%Y%m%d') as daymonthyear, transaction_date FROM ".$transtab." WHERE company_id = '" . $get['company_id'] . "' AND current_status = '".ST_APPROVED."' AND is_active=0 AND transaction_date >= '".$this->dateconvertdbformat($get['from_date'])."' AND transaction_date <= '".$this->dateconvertdbformat($get['to_date'])."'  GROUP BY transaction_date";
	        $resl = $callConfig->getAllRows($query);
	        foreach($resl as $res1){
	         	$dateyearary[$res1->daymonthyear] = $res1;
	        }	
	        $returntrans[$transtab] = $dateyearary;
	    }
	    $return['transaction'] = $returntrans;
        return $return;
    }
    function insertDeKittings($get, $post)
	{
		global $callConfig;
		$return = array();

		$return['post'] = $post;

		// Prepare parent data for dekitting table
		$parentData = array(
			'company_id' => $get['company_id'],
			'originator' => $get['user_id'],
			'dekitting_name' => $post['dekitting_name'],
			'parent_item_id' => $post['parent_item_id'],
			'parent_quantity' => $post['parent_quantity'],
			'parent_item_day_rate' => $post['parent_item_day_rate'],
			'parent_item_total' => $post['parent_item_total'],
			'parent_total' => $post['parent_total'],
			'child_total' => $post['child_total'],
			'suspense_total' => $post['suspense_total'],
			'transaction_date' => $this->dateconvertdbformat($post['transaction_date'])
		);
		// Check if this is an update or new record
		if (isset($post['rid']) && !empty($post['rid'])) {
			$parentData['updated_by'] = $get['user_id'];
			$parentData['updated_date'] = date('Y-m-d H:i:s');
			
			$parentUpdate = $callConfig->updateRecord('dekittings', $parentData, 'id', $post['rid']);
			$return['parent_insert_update'] = $parentUpdate;

			// Use existing transaction_id for the child records
			$transaction_id = $post['rid'];

			// Delete existing child records before adding new one
			$whr = "transaction_id =" . $transaction_id;
            $updateData = array('is_active' => null);
            // $return['update_items_is_active_null'] = $updateData;
            $updateResult = $callConfig->updateRecord('dekitting_items', $updateData, '', '', $whr);
            $return['update_items_is_active_null'] = $updateResult;
            $updatedComponentIds = array();

		} else {
			// Add creation fields for new record
			$parentData['is_active'] = 0;
			$parentData['current_status'] = 0;
			$parentData['created_by'] = $get['user_id'];
			$parentData['created_date'] = date('Y-m-d H:i:s');

			// Insert parent row
			$parentInsert = $callConfig->insertRecord('dekittings', $parentData);
			$return['parent_insert_update'] = $parentInsert;

			// Stop if parent insert failed
			if ($parentInsert['status'] !== 'sucess' || empty($parentInsert['LASTID'])) {
				$return['error'] = 'Parent insert failed';
				return $return;
			}
			$transaction_id = $parentInsert['LASTID'];
		}

		$return['transaction_id'] = $transaction_id;
		//$return['components'] = $post['components'];

		// Build components from nested or parallel arrays
		$components = [];
		if (isset($post['components']) && is_array($post['components'])) {
			$components = $post['components'];
		} elseif (isset($post['component_item']) && is_array($post['component_item'])) {
			$count = count($post['component_item']);
			for ($i = 0; $i < $count; $i++) {
				$components[] = array(
					'component_items_ids' => isset($post['component_items_ids'][$i]) ? $post['component_items_ids'][$i] : null,
					'item_id' => $post['component_item'][$i],
					'uom_id' => $post['component_uom'][$i],
					'quantity' => $post['component_quantity'][$i],
					'component_day_rate' => $post['component_day_rate'][$i],
					'component_total' => $post['component_total'][$i]
				);
			}
		}
		$return['components'] = $components;

		// Process child rows
        $childResults = array();
        foreach ($components as $component) {
        	$childData = array(
                'item_id' => $component['item_id'],
                'uom_id' => $component['uom_id'],
                'quantity' => $component['quantity'],
                'component_day_rate' => $component['component_day_rate'],
                'component_total' => $component['component_total'],
                'is_active' => 0,
                'transaction_id'=>$transaction_id
            );

            // Check if this is an update or insert
            if ($component['component_items_ids'] != "") {
                // Update existing component
                $childData['updated_by'] =  $get['user_id'];
           		$childData['updated_date'] = date('Y-m-d H:i:s');
                $childResults['art'][] = $childData;
                $childResults['ins'] = $callConfig->updateRecord('dekitting_items', $childData, 'id', $component['component_items_ids']);
                $updatedComponentIds[] = $component['component_items_ids'];
            } else {
                // Insert new component
                $childData['company_id'] =  $get['company_id'];
                $childData['created_by'] =  $get['user_id'];
           		$childData['created_date'] = date('Y-m-d H:i:s');
           		$childResults['art'][] = $childData;
                $childResults['ins'] = $callConfig->insertRecord('dekitting_items', $childData);
            }
        }
        $return['child_results'] = $childResults;
		return $return;
	}

	function insertKittings($get, $post){
		global $callConfig;
		$return = array();
		$return['post'] = $post;
		// Prepare parent data for kitting table
		$parentData = array(
			'company_id' => $get['company_id'],
			'originator' => $get['user_id'],
			'kitting_name' => $post['kitting_name'],
			'parent_item_id' => $post['parent_item_id'],
			'parent_quantity' => $post['parent_quantity'],
			'parent_item_day_rate' => $post['parent_item_day_rate'],
			'parent_item_total' => $post['parent_item_total'],
			'parent_total' => $post['parent_total'],
			'child_total' => $post['child_total'],
			'suspense_total' => $post['suspense_total'],
			'transaction_date' => $this->dateconvertdbformat($post['transaction_date'])
		);

		if ($post['rid'] != "") {
			// Update existing record
			$parentData['updated_by'] = $get['user_id'];
			$parentData['updated_date'] = date('Y-m-d H:i:s');
			$parentUpdate = $callConfig->updateRecord('kittings', $parentData, 'id', $post['rid']);
			$return['parent_insert_update'] = $parentUpdate;

			// Use existing transaction_id for the child records
			$transaction_id = $post['rid'];

			$whr = "transaction_id =" . $transaction_id;
            $updateData = array('is_active' => null);
            // $return['update_items_is_active_null'] = $updateData;
            $updateResult = $callConfig->updateRecord('kitting_items', $updateData, '', '', $whr);
            $return['update_items_is_active_null'] = $updateResult;
            $updatedComponentIds = array();
		} else {
			// Add creation fields for new record
			$parentData['is_active'] = 0;
			$parentData['current_status'] = 0;
			$parentData['created_by'] = $get['user_id'];
			$parentData['created_date'] = date('Y-m-d H:i:s');

			// Insert parent row
			$parentInsert = $callConfig->insertRecord('kittings', $parentData);
			$return['parent_insert_update'] = $parentInsert;

			// Stop if parent insert failed
			if ($parentInsert['status'] !== 'sucess' || empty($parentInsert['LASTID'])) {
				$return['error'] = 'Parent insert failed';
				return $return;
			}
			$transaction_id = $parentInsert['LASTID'];
		}

		$return['transaction_id'] = $transaction_id;
		$return['components'] = $post['components'];

		// Build components from nested or parallel arrays
		$components = [];
		if (isset($post['components']) && is_array($post['components'])) {
			$components = $post['components'];
		} elseif (isset($post['component_items']) && is_array($post['component_items'])) {
			$count = count($post['component_items']);
			for ($i = 0; $i < $count; $i++) {
				$components[] = array(
					'component_items_ids' => isset($post['component_items_ids'][$i]) ? $post['component_items_ids'][$i] : null,
					'item_id' => $post['component_items'][$i],
					'uom_id' => $post['component_uoms'][$i],
					'quantity' => $post['component_quantities'][$i],
					'component_day_rate' => $post['component_day_rate'],
               		'component_total' => $post['component_total']
				);
			}
		}
		       // Process child rows
        $childResults = array();
        foreach ($components as $component) {
            // Check if this is an update or insert
            $childData = array(
                'item_id' => $component['item_id'],
                'uom_id' => $component['uom_id'],
                'quantity' => $component['quantity'],
                'component_day_rate' => $component['component_day_rate'],
                'component_total' => $component['component_total'],
                'is_active' => 0,
                'transaction_id'=>$transaction_id
            );
           if ($component['component_items_ids'] != "") {
                // Update existing component
                $childData['updated_by'] =  $get['user_id'];
           		$childData['updated_date'] = date('Y-m-d H:i:s');
                $childResults[] = $callConfig->updateRecord('kitting_items', $childData, 'id', $component['component_items_ids']);
                $updatedComponentIds[] = $component['id'];
            } else {
                // Insert new component
                $childData['company_id'] =  $get['company_id'];
                $childData['created_by'] =  $get['user_id'];
           		$childData['created_date'] = date('Y-m-d H:i:s');
                $childResults[] = $callConfig->insertRecord('kitting_items', $childData);
            }
        }
 
		$return['child_inserts'] = $childResults;
		return $return;
	}

	function insertKittingAndDekkitingStockUpdate($get){
		global $callConfig;
		$return = array();
		$transaction_id = $get['transaction_no'];
		$transaction_table = $get['transaction_table'];
		if($transaction_table == 'dekittings'){
			$child_trans_table = 'dekitting_items';
		}else{
			$child_trans_table = 'kitting_items';
		}
		//$ausql = "SELECT * FROM application_users WHERE company_id = '".$get['company_id']."' AND id = '".$get['user_id']."' AND is_active=0";
		$ausql = "SELECT au.*, (SELECT department_id FROM cost_centers cc WHERE au.company_id = cc.company_id AND au.cost_center = cc.id AND au.is_active = cc.is_active) as department_id FROM application_users au WHERE au.company_id = '".$get['company_id']."' AND au.id = '".$get['user_id']."' AND au.is_active = 0";
	    $aures = $callConfig->getRow($ausql);

 //parent related transaction
		$sql = "SELECT * FROM ".$transaction_table." WHERE company_id = '".$get['company_id']."' AND id = '".$transaction_id."' AND is_active=0";
	    $res_parent = $callConfig->getRow($sql);
 		$itemids[$res_parent->parent_item_id] = $res_parent->parent_item_id;
 		$mainitem = $this->togetitemvalues($res_parent->parent_item_id);
		//$day_rate = $this->togetitemwadayrate($res_parent->parent_item_id, $get['company_id']);	
		$day_rate = $res_parent->parent_item_day_rate;	
		
		$len = array('company_id' =>$get['company_id'] , 'location_id' =>$aures->location_id, 'transaction_warehouse_id' => $aures->default_warehouse_id, 'transaction_date' => $res_parent->transaction_date, 'item_id' =>$res_parent->parent_item_id,'document_no'=>$res_parent->id,'document_type' =>$transaction_table,'document_ref_no' =>'','in_qty' =>$res_parent->parent_quantity,'in_value'=>0,'item_in_value' =>0,'reason_id'=>0,'out_qty' =>$res_parent->parent_quantity,'out_value' =>0,'sale_price'=>0,'sale_value'=>0,'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price,'is_active'=>'0','current_status'=>'0','sales_qty'=>0,'purchase_qty'=>0,'day_rate'=>$day_rate);
		if($transaction_table == 'dekittings'){
			$len['purchase_uom'] =$mainitem->sale_base_uom;
			$len['purchase_qty'] = $itemres->quantity;
			$len['purchase_base_uom'] = $mainitem->sale_base_uom;
		}else{
			$len['sales_uom'] = $mainitem->purchase_base_uom;
			$len['sales_qty'] = $itemres->quantity;
			$len['sale_base_uom'] = $mainitem->purchase_base_uom;
		}
		if($transaction_table=="kittings"){
			$len['out_qty'] = '0';
			$len['out_value'] = '0'; 
		}elseif($transaction_table=="dekittings"){
			$len['in_qty'] = '0';
			$len['in_value'] = '0'; 
		}else{
			$len = array();
		}
		if(count((array)$len) !=0){
			$ddf = $this->invoiceUpdates($transaction_table,$len);
		} 
 //parent related transaction
 //child related transaction
	    $sql1 = "SELECT * FROM ".$child_trans_table." WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$transaction_id."' AND is_active=0";
	    $res_child = $callConfig->getAllRows($sql1);
	    $childitemdetail = array();
		foreach($res_child as $itemres){
			$itemids[$itemres->item_id] = $itemres->item_id;
 		
			$mainitem = $this->togetitemvalues($itemres->item_id);
			//$day_rate = $this->togetitemwadayrate($itemres->item_id, $get['company_id']);
			$day_rate = $itemres->component_day_rate;
			$itemres->mainitem = $mainitem;
			$itemres->day_rate = $day_rate;	
			$childitemdetail[] = $itemres;
			$len = array('company_id' =>$get['company_id'] , 'location_id' =>$aures->location_id, 'transaction_warehouse_id' => $aures->default_warehouse_id, 'transaction_date' => $res_parent->transaction_date, 'item_id' =>$itemres->item_id,'document_no'=>$res_parent->id,'document_type' =>$child_trans_table,'document_ref_no' =>'','in_qty' =>$itemres->quantity,'in_value'=>0,'item_in_value' =>0,'reason_id'=>0,'out_qty' =>$itemres->quantity,'out_value' =>0,'sale_price'=>0,'sale_value'=>0,'item_wa_cost'=>$day_rate,'item_st_cost'=>$mainitem->purchase_price,'is_active'=>'0','current_status'=>'0','sales_qty'=>0,'purchase_qty'=>0,'day_rate'=>$day_rate);

			if($transaction_table == 'dekittings'){
				$len['purchase_uom'] =$itemres->uom_id;
				$len['purchase_qty'] = $itemres->quantity;
				$len['purchase_base_uom'] = $itemres->uom_id;
				$parent_uom_id= $mainitem->purchase_base_uom;
				$child_uom_id= $mainitem->sale_base_uom;
			}else{
				$len['sales_uom'] = $itemres->uom_id;
				$len['sales_qty'] = $itemres->quantity;
				$len['sale_base_uom'] = $itemres->uom_id;
				$parent_uom_id= $mainitem->sale_base_uom;
				$child_uom_id= $mainitem->purchase_base_uom;
			}

			$convsql = "SELECT id,conversion_ratio FROM uom_conversions WHERE company_id = '".$get['company_id']."' AND  parent_uom_id = '".$parent_uom_id."' AND child_uom_id = '".$child_uom_id."' AND is_active = 0";
			$convresd = $callConfig->getRow($convsql);
			if($convresd->id !=""){
				//ok coneversion
				if($convresd->conversion_ratio !="0" AND $convresd->conversion_ratio !=""){
					$len['in_qty'] = $itemres->item_quantity*$convresd->conversion_ratio;
					$len['in_value'] = $itemres->item_amount*$convresd->conversion_ratio;
				}
			}else{
				//reverse coneversion
				$convsql = "SELECT id,conversion_ratio FROM uom_conversions WHERE company_id = '".$get['company_id']."' AND  parent_uom_id = '".$child_uom_id."' AND child_uom_id = '".$parent_uom_id."' AND is_active = 0";
				$convresd = $callConfig->getRow($convsql);
				if($convresd->id !=""){
					if($convresd->conversion_ratio !="0" AND $convresd->conversion_ratio !=""){
						$len['in_qty'] = $post['in_qty']/$convresd->conversion_ratio;
						$len['in_value'] = $itemres->item_amount/$convresd->conversion_ratio;
					}
				}
			}
			if($transaction_table=="dekittings"){
				$len['out_qty'] = '0';
				$len['out_value'] = '0'; 
			}elseif($transaction_table=="kittings"){
				$len['in_qty'] = '0';
				$len['in_value'] = '0'; 
			}else{
				$len = array();
			}
			if(count((array)$len) !=0){
				$ddf = $this->invoiceUpdates($transaction_table,$len);
			} 
		}
//End Child Related transaction










/**/


		// account parts 
		$defaultaccount = DEFAULTACCOUNTS;
		$rounddigits = 2;
		//$aures //$user
	
		$whrs = "company_id= '".$get['company_id']."' AND is_active = 0";
		$query = $callConfig->selectQuery('config','*',$whrs,'','','');
		$config = $callConfig->getRow($query);

		$suspense_account = $config->jv_suspense_acc!=""?$config->jv_suspense_acc:$defaultaccount['jv_suspense_acc'];
		$default_round_off_account = $config->default_round_off_account!=""?$config->default_round_off_account:$defaultaccount['default_round_off_account'];

		$coasql = "SELECT account_code FROM `chart_of_accounts` WHERE company_id = '".$get['company_id']."' AND allow_posting = 0 AND is_active = 0";	
		$coares = $callConfig->getAllRows($coasql);
		$chartofaccount = array();
		foreach($coares as $coa){
			$chartofaccount[$coa->account_code] = $coa->account_code;
		}
		$transaction_location_id = $aures->location_id;
		$department_id = $aures->department_id;
		$cost_center_id = $aures->cost_center;
		if(count((array)$itemids) !=0){ 
			$itemsitemids = implode(',', (array)$itemids);
			$itemssql = "SELECT id, category_id, sku, item_name, item_description, inventory_asset_account, inventory_adjustment_account, inventory_cost_variance_account, cogs_account, purchase_expense_account, sales_revenue_account, sales_discount_account, sales_return_account, purchase_price_variance_account, freight_expense_account, packaging_expense_account, sales_tax_payable_account, purchase_tax_payable_account, promotions_and_discounts_account, sales_allowances_account, deferred_revenue_account, ap_account, ar_account, accrued_expenses_account, prepaid_expenses_account, wip_asset_account, goods_received_not_invoiced_account, invoiced_not_received_account, goods_sold_not_invoiced_account FROM items WHERE company_id = '".$get['company_id']."' AND id IN(".$itemsitemids.") AND is_active = 0";	
				$itemsres = $callConfig->getAllRows($itemssql);
			foreach($itemsres as $itm){
				$items[$itm->id] = $itm;
				$itemcatid[$itm->category_id] = $itm->category_id;
			}	
		}
		if(count((array)$itemcatid) !=0){
			$itemcatids = implode(',', (array)$itemcatid);
			$item_categoriessql = "SELECT id, inventory_asset_account, inventory_adjustment_account, inventory_cost_variance_account, cogs_account, purchase_expense_account, sales_revenue_account, sales_discount_account, sales_return_account, purchase_price_variance_account, freight_expense_account, packaging_expense_account, sales_tax_payable_account, purchase_tax_payable_account, promotions_and_discounts_account, sales_allowances_account, deferred_revenue_account, ap_account, ar_account, accrued_expenses_account, prepaid_expenses_account, wip_asset_account, goods_received_not_invoiced_account, invoiced_not_received_account, goods_sold_not_invoiced_account FROM item_categories WHERE company_id = '".$get['company_id']."' AND id IN(".$itemcatids.") AND is_active = 0";	
				$item_categoriesres = $callConfig->getAllRows($item_categoriessql);
				$item_categories = array();	
			foreach($item_categoriesres as $itcat){
				$item_categories[$itcat->id] = $itcat;
			}
		}
		

		foreach($childitemdetail as $itemrow){
			$itemrow = (array)$itemrow;

			$itemtabledetails = $items[$itemrow['item_id']];
			$itemcategoorytabledetails = $item_categories[$itemtabledetails->category_id];
			$mainitem = $itemrow['mainitem'];
			$day_rate = $itemrow['day_rate'];
			$totalamt = ($itemrow['quantity']*$day_rate);
			$jvary = array('company_id'=>$get['company_id'], 'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$transaction_id,  'transaction_item_id'=>$itemrow['item_id'],  'item_id'=>$itemrow['item_id'], 'particulars'=>$transaction_table, 'item_name'=>$itemtabledetails->item_name,  'description'=>$itemtabledetails->item_description,  'purchase_uom'=>$itemrow['uom_id'],  'item_quantity'=>$itemrow['quantity'],  'item_price'=>$day_rate,  'item_amount'=>$day_rate,  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>$totalamt,  'name_type'=>'C',  'doc_ref_no'=>'',  'doc_ref_date'=>'',  'req_date'=>$res_parent->transaction_date,  'approved_quantity'=>$itemrow['quantity'],  'transaction_type'=>'',  'transaction_qty'=>$itemrow['quantity'],  'pending_qty'=>$itemrow['quantity'],  'is_active'=>'0',  'current_status'=>'0','created_by'=>$get['user_id']);
			if($child_trans_table == 'dekitting_items'){
				//inventory_asset_account
				$itemaccount_code = "";
				if($itemtabledetails->inventory_asset_account !="" AND $itemtabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemtabledetails->inventory_asset_account;
				}elseif($itemcategoorytabledetails->inventory_asset_account !="" AND $itemcategoorytabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemcategoorytabledetails->inventory_asset_account;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['inventory_asset_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
				$jvary['account_code'] = $itemaccount_code;
				$jvary['credit_amount'] = round($totalamt,$rounddigits);
			 	$jvary['debit_amount'] = 0;
				$itemref[$itemaccount_code][] = $jvary;
			
			}elseif($child_trans_table == 'kitting_items'){
					
				//inventory_asset_account
				$itemaccount_code = "";
				if($itemtabledetails->inventory_asset_account !="" AND $itemtabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemtabledetails->inventory_asset_account;
				}elseif($itemcategoorytabledetails->inventory_asset_account !="" AND $itemcategoorytabledetails->inventory_asset_account !="0"){
					$itemaccount_code = $itemcategoorytabledetails->inventory_asset_account;
				}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
					$itemaccount_code = $config->config_inventory_asset_account;
				}
				$itemaccount_code!=""?$itemaccount_code:$defaultaccount['inventory_asset_account'];
				if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
					$itemaccount_code =$suspense_account;
				}	
				$jvary['account_code'] = $itemaccount_code;
				$jvary['credit_amount'] = 0;
			 	$jvary['debit_amount'] = round($totalamt,$rounddigits);
				$itemref[$itemaccount_code][] = $jvary;
			}

		}
		foreach($itemref as $refaccountno=>$list){
			$item_amount = 0;
			$creditamount = 0;
			$debitamount = 0;
			foreach($list as $detis){
				$creditamount += $detis['credit_amount'];
				$debitamount += $detis['debit_amount'];
			}
			$witemlistary =array('company_id'=>$get['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$transaction_id, 'account_code'=>$refaccountno,'particulars'=>$transaction_table, 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>date('Y-m-d h:i:s'),  'created_by'=>$get['user_id'],'credit_amount'=>$creditamount,'debit_amount'=>$debitamount);
			$witemlist[]  = $witemlistary;
			$total_debit_amt += $debitamount;
			$total_credit_amt += $creditamount;
		}



//Start parent data
		$itemtabledetails = $items[$res_parent->parent_item_id];
		$itemcategoorytabledetails = $item_categories[$itemtabledetails->category_id];
		
		//$mainitem = $this->togetitemvalues($res_parent->parent_item_id);
		//$day_rate = $this->togetitemwadayrate($res_parent->parent_item_id, $get['company_id']);
		$day_rate = $res_parent->parent_item_day_rate;
		$totalamt = ($res_parent->parent_quantity*$day_rate);

		$jvary = array('company_id'=>$get['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$transaction_id,  'transaction_item_id'=>$res_parent->parent_item_id,  'item_id'=>$res_parent->parent_item_id, 'particulars'=>$transaction_table, 'item_name'=>$itemtabledetails->item_name,  'description'=>$itemtabledetails->item_description,  'purchase_uom'=>$itemtabledetails->purchase_base_uom,  'item_quantity'=>$res_parent->parent_quantity,  'item_price'=>0,  'item_amount'=>0,  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>0,  'name_type'=>'C',  'doc_ref_no'=>'',  'doc_ref_date'=>'',  'req_date'=>$res_parent->transaction_date,  'approved_quantity'=>$res_parent->parent_quantity,  'transaction_type'=>'',  'transaction_qty'=>$res_parent->parent_quantity,  'pending_qty'=>$res_parent->parent_quantity,  'is_active'=>'0',  'current_status'=>'0','created_by'=>$get['user_id']);

		$witemlistary =array('company_id'=>$get['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$transaction_id, 'account_code'=>'','particulars'=>$transaction_table, 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>date('Y-m-d h:i:s'),  'created_by'=>$get['user_id']); // credit row
		if($transaction_table == 'kittings'){
			//inventory_asset_account
			$itemaccount_code = "";
			if($itemtabledetails->inventory_asset_account !="" AND $itemtabledetails->inventory_asset_account !="0"){
				$itemaccount_code = $itemtabledetails->inventory_asset_account;
			}elseif($itemcategoorytabledetails->inventory_asset_account !="" AND $itemcategoorytabledetails->inventory_asset_account !="0"){
				$itemaccount_code = $itemcategoorytabledetails->inventory_asset_account;
			}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
				$itemaccount_code = $config->config_inventory_asset_account;
			}
			$itemaccount_code!=""?$itemaccount_code:$defaultaccount['inventory_asset_account'];
			if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
				$itemaccount_code =$suspense_account;
			}	
			$witemlistary['account_code'] = $itemaccount_code;
			$jvary['account_code'] = $itemaccount_code;
			$jvary['credit_amount'] = round($totalamt,$rounddigits);
			$jvary['debit_amount'] =0;
			$witemlistary['credit_amount'] =  round($totalamt,$rounddigits);
		 	$witemlistary['debit_amount'] = 0;
		 	$total_credit_amt +=round($totalamt,$rounddigits);
		 	
		}elseif($transaction_table == 'dekittings'){
			//inventory_asset_account
			$itemaccount_code = "";
			if($itemtabledetails->inventory_asset_account !="" AND $itemtabledetails->inventory_asset_account !="0"){
				$itemaccount_code = $itemtabledetails->inventory_asset_account;
			}elseif($itemcategoorytabledetails->inventory_asset_account !="" AND $itemcategoorytabledetails->inventory_asset_account !="0"){
				$itemaccount_code = $itemcategoorytabledetails->inventory_asset_account;
			}elseif($config->config_inventory_asset_account !="" AND $config->config_inventory_asset_account !="0"){
				$itemaccount_code = $config->config_inventory_asset_account;
			}
			$itemaccount_code!=""?$itemaccount_code:$defaultaccount['inventory_asset_account'];
			if($chartofaccount[$itemaccount_code] =="" || $itemaccount_code ==""){ // chart Of Account Not Found Suspens Account Enter data
				$itemaccount_code =$suspense_account;
			}	
			$witemlistary['account_code'] = $itemaccount_code;
			$jvary['account_code'] = $itemaccount_code;
			
			$jvary['credit_amount'] = 0;
			$jvary['debit_amount'] = round($totalamt,$rounddigits);
			$witemlistary['credit_amount'] = 0;
	 		$witemlistary['debit_amount'] = round($totalamt,$rounddigits);
	 		$total_debit_amt += round($totalamt,$rounddigits);
		}
		$witemlist[]  = $witemlistary;
		$itemref[$party_accountt][] = $jvary;
//End parent data




		$tot_debit_amount = $total_debit_amt;
		$tot_credit_amount = $total_credit_amt;

		$suspensearry =array('company_id'=>$get['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=>$cost_center_id, 'transaction_id' =>$transaction_id, 'account_code'=>$suspense_account,'particulars'=>'JV Suspense', 'is_active'=>'0',  'current_status'=>'0',  'created_date'=>date('Y-m-d h:i:s'),  'created_by'=>$get['user_id']); // credit row

		$jvary = array('company_id'=>$get['company_id'],  'transaction_location_id'=>$transaction_location_id,  'transaction_id'=>$transaction_id,  'transaction_item_id'=>'',  'item_id'=>'', 'particulars'=>'JV Suspense', 'item_name'=>'JV Suspense',  'description'=>'JV Suspense',  'purchase_uom'=>'',  'item_quantity'=>1,  'item_price'=>0,  'item_amount'=>0,  'tax_amount'=>0,  'discount_amount'=>0,  'total_amount'=>0,  'name_type'=>'C',  'doc_ref_no'=>'',  'doc_ref_date'=>'',  'req_date'=>$res_parent->transaction_date,  'approved_quantity'=>'',  'transaction_type'=>'',  'transaction_qty'=>'',  'pending_qty'=>'',  'is_active'=>'0',  'current_status'=>'0','created_by'=>$get['user_id'],'account_code'=>$suspense_account);

		if(round($tot_debit_amount,2) > round($tot_credit_amount,2)){
		//if($tot_debit_amount > $tot_credit_amount){	
			$suspencamount = round($tot_debit_amount,2) - round($tot_credit_amount,2);
			//$suspencamount = $tot_debit_amount - $tot_credit_amount;
			$total_credit_amt +=round($suspencamount,$rounddigits);
			$suspensearry['credit_amount'] = $suspencamount;
			$jvary['credit_amount'] = $suspencamount;
			$suspensearry['debit_amount'] = 0;
			$jvary['debit_amount'] = 0;
			$itemref[$suspense_account][] = $jvary;	
			$witemlist[] =$suspensearry;
		}elseif(round($tot_debit_amount,2) < round($tot_credit_amount,2)){
		//}elseif($tot_debit_amount < $tot_credit_amount){
			$suspencamount = round($tot_credit_amount,2) - round($tot_debit_amount,2);
			//$suspencamount = $tot_credit_amount - $tot_debit_amount;
			$total_debit_amt += round($suspencamount,$rounddigits);
			$suspensearry['debit_amount'] = $suspencamount;
			$jvary['debit_amount'] = $suspencamount;
			$suspensearry['credit_amount'] = 0;
			$jvary['credit_amount'] = 0;
			$itemref[$suspense_account][] = $jvary;	
			$witemlist[] =$suspensearry;
		}


		$senddata = array('company_id'=>$get['company_id'],'transaction_location_id'=>$transaction_location_id,  'department_id'=>$department_id,  'cost_center_id'=> $cost_center_id,  'transaction_number'=>'',  'transaction_date'=>$res_parent->transaction_date,  'currency_id'=>$aures->default_currency_id,  'currency_rate'=>1,  'originator'=>$res_parent->originator, 'created_date'=>date('Y-m-d h:i:s'),  'created_by'=>$get['user_id'],'credit_amount'=>$total_credit_amt, 'debit_amount'=>$total_debit_amt,'debit_tax'=>0,'credit_tax'=>'0','current_status'=>ST_APPROVED); // master table entry

		//echo "<pre>";print_r($witemlist); 
		$postdata = array('main'=>$senddata,'items'=>$witemlist,'itemref'=>$itemref,'update_id'=>'','ref_id'=>$ref_id,'company_id'=>$get['company_id']);
		$return['postdatajvdata'] = $postdata;
		//echo "<pre>"; print_r($postdata); exit;
		//echo "working 11 ";
		$addjournalvouchers = $this->addjournalvouchers($postdata, $table_name);
		$return['addjournalvouchers'] = $addjournalvouchers;
//**/





		return $return;
	}
	function getkittingdekittinglist($get){
		global $callConfig;
		$itemquery = "SELECT `id`, `company_id`, `sku`, `item_name`, `item_type`, `item_description`, `purchase_uom` FROM items WHERE company_id = '" . $get['company_id'] . "' AND is_active=0";
		$item = $callConfig->getAllRows($itemquery);
		$uomquery = "SELECT id, uom_short_code, uom_name FROM unit_of_measurements WHERE company_id = '" . $get['company_id'] . "' AND is_active=0";
		$uom = $callConfig->getAllRows($uomquery);
		

		$kittingQuery = "SELECT id, originator, kitting_name as process_name, parent_item_id, parent_quantity, transaction_date FROM kittings WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$kittingData = $callConfig->getAllRows($kittingQuery);
		
		$itemsQuery = "SELECT id, transaction_id, item_id, uom_id, quantity FROM kitting_items WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$itemsData = $callConfig->getAllRows($itemsQuery);
		$dekittingQuery = "SELECT id, originator, dekitting_name  as process_name, parent_item_id, parent_quantity, transaction_date FROM dekittings WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$dekittingData = $callConfig->getAllRows($dekittingQuery);
		$dekittingitemsQuery = "SELECT id, transaction_id, item_id, uom_id, quantity FROM dekitting_items WHERE company_id = '" . $get['company_id'] . "' AND is_active = 0";
		$dekittingitemsData = $callConfig->getAllRows($dekittingitemsQuery);
		return ['dekitting' => $dekittingData,'dekittingitems' => $dekittingitemsData,'kitting' => $kittingData,'kittingitems' => $itemsData,'item' => $item,'uom' => $uom];
	}
	function checkitemcategoryaccountdata($get, $post){
	    global $callConfig;
	    $category_sql = "SELECT  ic.id,  ic.category_description,  ic.hsn_and_sac_code_id, hsc.code AS hsn_code, hsc.code_description AS hsn_description, ic.inventory_asset_account, ic.inventory_adjustment_account, ic.inventory_cost_variance_account, ic.cogs_account, ic.purchase_expense_account, ic.sales_revenue_account, ic.sales_discount_account, ic.sales_return_account, ic.purchase_price_variance_account, ic.freight_expense_account, ic.packaging_expense_account, ic.sales_tax_payable_account, ic.purchase_tax_payable_account, ic.promotions_and_discounts_account, ic.sales_allowances_account, ic.deferred_revenue_account, ic.ap_account, ic.ar_account, ic.accrued_expenses_account, ic.prepaid_expenses_account, ic.wip_asset_account, ic.goods_received_not_invoiced_account, ic.invoiced_not_received_account, ic.goods_sold_not_invoiced_account FROM item_categories ic LEFT JOIN hsn_and_sac_codes hsc ON hsc.id = ic.hsn_and_sac_code_id WHERE ic.company_id = '" . $get['company_id'] . "' AND ic.id = '" . $post['categoryId'] . "' AND ic.is_active = '0' ";
	    $category_res = $callConfig->getRow($category_sql);
	    if (!$category_res) {
	        return ["category" => null, "accounts" => []];
	    }
	    $fields = [ "inventory_asset_account", "inventory_adjustment_account", "inventory_cost_variance_account", "cogs_account", "purchase_expense_account", "sales_revenue_account", "sales_discount_account", "sales_return_account", "purchase_price_variance_account", "freight_expense_account", "packaging_expense_account", "sales_tax_payable_account", "purchase_tax_payable_account", "promotions_and_discounts_account", "sales_allowances_account", "deferred_revenue_account", "ap_account", "ar_account", "accrued_expenses_account", "prepaid_expenses_account", "wip_asset_account", "goods_received_not_invoiced_account", "invoiced_not_received_account", "goods_sold_not_invoiced_account" ];
	    $accountCodes = [];
	    foreach ($fields as $field) {
	        if (!empty($category_res->$field)) {
	            $accountCodes[$field] = $category_res->$field;
	        }
	    }
	    $accounts = [];
	    if (!empty($accountCodes)) {
	        $inClause = "'" . implode("','", array_values($accountCodes)) . "'";
	        $account_sql = "SELECT id, account_code, account_name FROM chart_of_accounts WHERE allow_posting = '0' AND is_active = '0' AND company_id = '" . $get['company_id'] . "' AND account_code IN ($inClause) ";
	        $account_res = $callConfig->getAllRows($account_sql);
	        $lookup = [];
	        foreach ($account_res as $row) {
	            $lookup[$row->account_code] = $row;
	        }
	        foreach ($accountCodes as $field => $code) {
	            if (isset($lookup[$code])) {
	                $accounts[$field] = $lookup[$code];
	            }
	        }
	    }
	    $category_sql = "SELECT id, category_description , parent_item_category_id FROM  item_categories WHERE company_id = '".$get['company_id']."' AND parent_item_category_id = '".$post['categoryId'] ."' AND is_active ='0'  ORDER BY category_description ASC";
        $childcategory_res = $callConfig->getAllRows($category_sql);

	    return [
	        "category" => $category_res,
	        "accounts" => $accounts,
	        "child_categroy" => $childcategory_res,
	    ];
	}

	// added by koti start
	function togetpurchaserequisition($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$whr = '';	
		if($get['from_date']!="" && $get['to_date']!=""){
			$whr .= " AND transa.transaction_date >= '".$get['from_date']."' AND transa.transaction_date <= '".$get['to_date']."' ";

		}

		$get['table'] =$get['transaction_table'];
		$status_progress = $this->getStatusProgressList($get);
		foreach($status_progress as $statusprogress){
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}
		
		$listsql ="SELECT transa.id, transa.transaction_number, transa.transaction_date, transa.originator, transa.party_id, transa.party_name,transa.amount, transa.discount_amount, transa.tax_amount,transa.total_amount, itemtb.item_id, itemtb.item_name, itemtb.item_quantity, itemtb.pending_qty, itemtb.item_amount as atm_item_amount , itemtb.tax_amount as itm_tax_amount ,itemtb.total_amount as atm_total_amount, transa.current_status, transa.ref_type, transa.ref_no FROM requisitions transa, rq_items itemtb WHERE transa.company_id = '".$get['company_id']."' AND transa.company_id = itemtb.company_id AND transa.id = itemtb.transaction_id AND transa.is_active = itemtb.is_active AND transa.is_active = 0 $whr ORDER BY transa.id DESC";	
		$listres=$callConfig->getAllRows($listsql);
		
		$list = array();
		foreach($listres as $listre){
			$list[$listre->id][] = $listre;
		}
		$return['requisition_list'] = $list;
		$return['sql'] = $listsql;
		return $return;
		
	}

	function  getStatusProgressList($data){
		global $callConfig;
		$statussql = "SELECT id,name,status,action_label FROM status_progress WHERE table_name = '".$data['table']."' ORDER BY `id` ASC";
		$statusress = $callConfig->getAllRows($statussql);
		if(!isset($statusress[0]->id)){
			$statusres[] = array('status'=>'0','name'=>'Active');
		}else{
			$statusres = $statusress;
		}
		return $statusres;
	}

	function togetpovsgrndata($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		if($get['rid'] && $get['type']){
			if($get['type'] == 'PO'){
				$query = "SELECT * FROM po_items WHERE company_id = '".$get['company_id']."' AND transaction_id = ".$get['rid']." AND is_active = 0";
			}else if($get['type'] == 'GRN'){
				$query = "SELECT * FROM grn_items WHERE company_id = '".$get['company_id']."' AND transaction_id = ".$get['rid']." AND is_active = 0";
			}
			$data = $callConfig->getAllRows($query);
			$return['items_res']= $data;
		}else{
			$get['table'] =$get['transaction_table'];
			$status_progress = $this->getStatusProgressList($get);
			foreach($status_progress as $statusprogress){
				$return['status_progress'][$statusprogress->status] = $statusprogress;
			}
			$whr = '';
			if ($get['from_date'] != "" && $get['to_date'] != "") {
				$whr .= " AND po.transaction_date >= '".$get['from_date']."' 
						AND po.transaction_date <= '".$get['to_date']."' ";
			}
			// build query: aggregate po_items and grn_items separately
			$listsql = "SELECT 
					po.id AS purchase_id,po.transaction_number AS po_no,po.transaction_date AS po_date,po.party_name,po.originator AS po_requested,po.total_amount AS po_amount,grn.id AS goods_id,grn.transaction_number AS grn_no,grn.transaction_date AS grn_date,grn.party_name AS grn_party,grn.total_amount AS grn_amount,grn.originator AS grn_requested_by,poi_summary.po_qty,grni_summary.grn_qty,(poi_summary.po_qty - grni_summary.grn_qty) AS qty_variance
					FROM purchase_orders po
				-- sum po_items
				LEFT JOIN (
					SELECT transaction_id, SUM(item_quantity) AS po_qty
					FROM po_items
					WHERE is_active = 0
					GROUP BY transaction_id
				) AS poi_summary ON po.id = poi_summary.transaction_id
				-- join GRN header
				LEFT JOIN goods_receipt_notes grn 
					ON po.id = grn.ref_id
					AND po.company_id = grn.company_id 
					AND grn.is_active = 0
				-- sum grn_items
				LEFT JOIN (
					SELECT transaction_id, SUM(item_quantity) AS grn_qty
					FROM grn_items
					WHERE is_active = 0
					GROUP BY transaction_id
				) AS grni_summary ON grn.id = grni_summary.transaction_id
				WHERE po.company_id = '".$get['company_id']."' AND po.is_active = 0 $whr ORDER BY purchase_id DESC, goods_id ASC";

			$listres = $callConfig->getAllRows($listsql);
			$return['sql'] = $listsql;
			// group results by PO id (same style as requisition_list)
			$list = array();
			foreach ($listres as $row) {
				$list[$row->purchase_id][] = $row;
			}
			$return['povsgrn_list'] = $list;
		}
		return $return;
	}

	function togetpendinggrn($get){
		global $callConfig;
		$return = array();
		$whr = '';
		if ($get['from_date'] != "" && $get['to_date'] != "") {
			$whr .= " AND grn.transaction_date >= '".$get['from_date']."' AND grn.transaction_date <= '".$get['to_date']."' ";
		}
		// SQL: Pending GRNs not invoiced fully
		$listsql = "SELECT grn.id AS grn_id, grn.transaction_number AS grn_no, grn.transaction_date AS grn_date, grn.party_name AS vendor_name, po.id AS po_id, po.transaction_number AS po_no, po.transaction_date AS po_date, grni.item_id, grni.item_name, SUM(grni.item_quantity) AS grn_qty, COALESCE(SUM(pii.item_quantity), 0) AS invoiced_qty, (SUM(grni.item_quantity) - COALESCE(SUM(pii.item_quantity), 0)) AS pending_qty, SUM(grni.total_amount) AS grn_amount
			FROM goods_receipt_notes grn
			-- Join PO header
			LEFT JOIN purchase_orders po ON grn.ref_id = po.id AND po.company_id = grn.company_id AND po.is_active = 0
			-- Join GRN items
			INNER JOIN grn_items grni ON grn.id = grni.transaction_id AND grni.is_active = 0
			-- Join PO items (for matching item_id consistency)
			LEFT JOIN po_items poi ON po.id = poi.transaction_id AND poi.item_id = grni.item_id AND poi.is_active = 0
			-- Join Purchase Invoices referencing PO
			LEFT JOIN purchase_invoices pi ON pi.ref_id = po.id AND pi.company_id = po.company_id AND pi.is_active = 0
			-- Join Invoice items
			LEFT JOIN pi_items pii ON pi.id = pii.transaction_id AND pii.item_id = grni.item_id AND pii.is_active = 0
			WHERE grn.company_id = '".$get['company_id']."' AND grn.is_active = 0 $whr GROUP BY grn.id, grn.transaction_number, grn.transaction_date,grn.party_name, po.id, po.transaction_number, po.transaction_date, grni.item_id, grni.item_name
			HAVING pending_qty > 0 ORDER BY grn.transaction_date DESC, grn.id DESC";
		$listres = $callConfig->getAllRows($listsql);
		// group by GRN id for frontend
		$list = array();
		foreach($listres as $row){
			$list[$row->grn_id][] = $row;
		}
		$return['pending_grn_list'] = $list;
		$return['sql'] = $listsql;
		return $return;
	}

	function togetmaterialshortagebasedonpurchaseorder($get){
		global $callConfig;
		$return = array();
		$from_date = $get['from_date'] ?? "";
		$to_date   = $get['to_date'] ?? "";
		$whr = "";
		if($from_date != "" && $to_date != ""){
			$whr .= " AND po.transaction_date >= '".$from_date."' AND po.transaction_date <= '".$to_date."' ";
		}
		// SQL to calculate shortage
		$listsql = "SELECT i.id item_id, i.item_name, COALESCE(SUM(poi.item_quantity), 0) AS required_qty, COALESCE(bal.closing_qty, 0) AS available_qty, (COALESCE(SUM(poi.item_quantity), 0) - COALESCE(bal.closing_qty, 0)) AS shortage_qty FROM po_items poi
			INNER JOIN purchase_orders po ON poi.transaction_id = po.id AND po.company_id = '".$get['company_id']."' AND po.is_active = 0
			LEFT JOIN ( SELECT t1.item_id, t1.closing_qty FROM item_wa_balances t1
				INNER JOIN (SELECT item_id, MAX(balance_date) AS max_date FROM item_wa_balances WHERE company_id = '".$get['company_id']."'
					AND balance_date <= '".$to_date."' GROUP BY item_id ) t2 ON t1.item_id = t2.item_id AND t1.balance_date = t2.max_date
			) bal ON poi.item_id = bal.item_id
			LEFT JOIN items i ON i.id = poi.item_id AND i.company_id = poi.company_id AND  i.is_active=0 
			WHERE poi.is_active = 0 $whr 
			GROUP BY i.id HAVING shortage_qty > 0 ORDER BY shortage_qty DESC ";
		$listres = $callConfig->getAllRows($listsql);
		$return['material_shortage_list'] = $listres;
		$return['sql'] = $listsql;
		return $return;
	}

	function togetmaterialshortagebasedonsalesorder($get){
		global $callConfig;
		$return = array();
		$from_date = $get['from_date'] ?? "";
		$to_date   = $get['to_date'] ?? "";
		$whr = "";
		if($from_date != "" && $to_date != ""){
			$whr .= " AND so.transaction_date >= '".$from_date."' AND so.transaction_date <= '".$to_date."' ";
		}
		// SQL to calculate shortage
		$listsql = "SELECT i.id item_id, i.item_name, COALESCE(SUM(soi.item_quantity), 0) AS required_qty, COALESCE(MAX(bal.closing_qty), 0) AS available_qty, (COALESCE(SUM(soi.item_quantity), 0) - COALESCE(MAX(bal.closing_qty), 0)) AS shortage_qty FROM so_items soi
			INNER JOIN sales_orders so ON soi.transaction_id = so.id AND so.company_id = '".$get['company_id']."' AND so.is_active = 0
			LEFT JOIN ( SELECT t1.item_id, t1.closing_qty FROM item_wa_balances t1
				INNER JOIN (SELECT item_id, MAX(balance_date) AS max_date FROM item_wa_balances WHERE company_id = '".$get['company_id']."'
					AND balance_date <= '".$to_date."' GROUP BY item_id ) t2 ON t1.item_id = t2.item_id AND t1.balance_date = t2.max_date
			) bal ON soi.item_id = bal.item_id
			LEFT JOIN items i ON i.id = soi.item_id AND i.company_id = soi.company_id AND  i.is_active=0 
			WHERE soi.is_active = 0 $whr 
			GROUP BY i.id, i.item_name HAVING shortage_qty > 0 ORDER BY shortage_qty DESC ";
		$listres = $callConfig->getAllRows($listsql);
		$return['material_shortage_list'] = $listres;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetduplicateinvoicedata($get){
		global $callConfig;
		$return = array();
		$whr = '';
		// ✅ Date filter
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whr .= " AND pi.doc_ref_date >= '" . $get['from_date'] . "' AND pi.doc_ref_date <= '" . $get['to_date'] . "' ";
		}
		// ✅ SQL Query
		$listsql = "SELECT pi.transaction_number, pi.transaction_date, pi.party_name,pi.doc_ref_no AS invoice_no,pi.doc_ref_date AS invoice_date,pi.total_amount,COUNT(*) AS duplicate_count,CASE WHEN COUNT(*) > 1 THEN 'Duplicate Invoice' ELSE 'Unique' END AS duplicate_type FROM purchase_invoices pi WHERE pi.company_id = '" . $get['company_id'] . "' AND pi.is_active = 0 AND pi.doc_ref_no IS NOT NULL AND pi.doc_ref_no <> '' $whr GROUP BY pi.party_id, pi.doc_ref_no HAVING COUNT(*) > 1 ORDER BY pi.party_name, pi.doc_ref_no ";
		// ✅ Fetch data
		$listres = $callConfig->getAllRows($listsql);
		$return['duplicate_invoices'] = $listres;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetwithholdingtax($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;

		$company_id = $get['company_id'];

		// Add your date filter here if/when you want:
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
		    $whr .= " AND pi.doc_ref_date >= '".$get['from_date']."' AND pi.doc_ref_date <= '".$get['to_date']."' ";
		}

		$listsql = "SELECT pi.id AS invoice_id, pi.transaction_number, pi.transaction_date, pi.doc_ref_no  AS invoice_no,
				pi.doc_ref_date AS invoice_date, pi.party_id, pi.party_name, pi.tax_state, pi.tax_type, pi.amount AS taxable_amount,
				COALESCE(pi.tax_amount, pisum.item_tax_sum, 0)  AS gst_vat_amount, COALESCE(pi.total_tds_amount, pisum.tds_items_sum, 0) AS tds_withheld, pi.total_amount  AS invoice_total,
				(pi.total_amount - COALESCE(pi.total_tds_amount, pisum.tds_items_sum, 0)) AS net_payable
			FROM purchase_invoices pi 
			LEFT JOIN (SELECT transaction_id,SUM(COALESCE(tds_amount,0))  AS tds_items_sum,SUM(COALESCE(tax_amount,0))  AS item_tax_sum
				FROM pi_items WHERE is_active = 0 GROUP BY transaction_id) pisum ON pisum.transaction_id = pi.id
			WHERE pi.company_id = '".$company_id."' AND pi.is_active = 0 $whr ORDER BY pi.doc_ref_date DESC, pi.id DESC";
		$rows = $callConfig->getAllRows($listsql);
		// Flat list (per invoice)
		$return['withholding_tax_list'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetopensalesorders($get) {
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$whr = '';

		// ✅ Date Filter
		if ($get['from_date'] != "" && $get['to_date'] != "") {
			$whr .= " AND so.transaction_date >= '" . $get['from_date'] . "'  AND so.transaction_date <= '" . $get['to_date'] . "' ";
		}
		// ✅ Status Color and Label Reference
		$get['table'] = "sales_orders";
		$status_progress = $this->getStatusProgressList($get);
		foreach ($status_progress as $statusprogress) {
			$return['status_progress'][$statusprogress->status] = $statusprogress;
		}
		// ✅ SQL Query
		$listsql = "SELECT so.id AS sales_order_id, so.transaction_number AS so_no, so.transaction_date AS so_date, so.party_name AS customer_name, so.ref_no AS reference_no, so.salesmen_id AS salesperson_id, so.current_status AS status_id, so.due_date AS expected_delivery_date, soi.item_id, soi.item_name, soi.item_quantity, soi.pending_qty, soi.item_amount, soi.tax_amount, soi.total_amount FROM sales_orders so LEFT JOIN so_items soi ON soi.transaction_id = so.id AND soi.company_id = so.company_id AND soi.is_active = 0 WHERE so.company_id = '" . $get['company_id'] . "' AND so.is_active = 0 AND COALESCE(soi.pending_qty, 0) > 0 $whr ORDER BY so.transaction_date DESC, so.id DESC ";
		$listres = $callConfig->getAllRows($listsql);
		$list = array();
		foreach ($listres as $listre) {
			$list[$listre->sales_order_id][] = $listre;
		}
		$return['open_sales_orders'] = $list;
		$return['sql'] = $listsql;
		return $return;
	}

	function togetordervsshipmentdata($get) {
		global $callConfig;
		$return = array();
		$return['get'] = $get;

		// ======================
		// Popup item details (like PO vs GRN modal)
		// ======================
		if ($get['rid'] && $get['type']) {

			if ($get['type'] == 'SO') {
				$query = "SELECT * FROM so_items 
						WHERE company_id = '".$get['company_id']."' 
							AND transaction_id = ".$get['rid']." 
							AND is_active = 0";
			} else if ($get['type'] == 'DO') {
				$query = "SELECT * FROM do_items 
						WHERE company_id = '".$get['company_id']."' 
							AND transaction_id = ".$get['rid']." 
							AND is_active = 0";
			}

			$data = $callConfig->getAllRows($query);
			$return['items_res'] = $data;

		} else {

			// ======================
			// Main report section
			// ======================
			$get['table'] = $get['transaction_table'];
			$status_progress = $this->getStatusProgressList($get);
			foreach ($status_progress as $statusprogress) {
				$return['status_progress'][$statusprogress->status] = $statusprogress;
			}

			$whr = '';
			if ($get['from_date'] != "" && $get['to_date'] != "") {
				$whr .= " AND so.transaction_date >= '".$get['from_date']."' 
						AND so.transaction_date <= '".$get['to_date']."' ";
			}

			// ======================
			// Main SQL Query
			// ======================
			$listsql = "
				SELECT 
					so.id AS sales_id,
					so.transaction_number AS so_no,
					so.transaction_date AS so_date,
					so.party_name,
					so.originator AS so_requested_by,
					so.total_amount AS so_amount,

					do.id AS delivery_id,
					do.transaction_number AS do_no,
					do.transaction_date AS do_date,
					do.party_name AS do_party,
					do.total_amount AS do_amount,
					do.originator AS do_requested_by,

					soi_summary.so_qty,
					doi_summary.do_qty,
					(soi_summary.so_qty - doi_summary.do_qty) AS qty_variance

				FROM sales_orders so

				-- sum of sales order items
				LEFT JOIN (
					SELECT transaction_id, SUM(item_quantity) AS so_qty
					FROM so_items
					WHERE is_active = 0
					GROUP BY transaction_id
				) AS soi_summary ON so.id = soi_summary.transaction_id

				-- join delivery order header
				LEFT JOIN delivery_orders do 
					ON so.id = do.ref_id
					AND so.company_id = do.company_id
					AND do.is_active = 0

				-- sum of delivery order items
				LEFT JOIN (
					SELECT transaction_id, SUM(item_quantity) AS do_qty
					FROM do_items
					WHERE is_active = 0
					GROUP BY transaction_id
				) AS doi_summary ON do.id = doi_summary.transaction_id

				WHERE so.company_id = '".$get['company_id']."' 
				AND so.is_active = 0 $whr
				ORDER BY sales_id DESC, delivery_id ASC
			";

			$listres = $callConfig->getAllRows($listsql);
			$return['sql'] = $listsql;

			// ======================
			// Group results (same as PO vs GRN)
			// ======================
			$list = array();
			foreach ($listres as $row) {
				$list[$row->sales_id][] = $row;
			}
			$return['so_vs_do_list'] = $list;
		}

		return $return;
	}

	function togetspendanalysisbyvendor($get){
		global $callConfig;
		$return = array();
		// $return['get'] = $get;
		$company_id = $get['company_id'];
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = $get['from_date'];
			$to   = $get['to_date'];
			$whr .= " AND ((po.transaction_date BETWEEN '".$from."' AND '".$to."')
						OR (grn.transaction_date BETWEEN '".$from."' AND '".$to."')
						OR (inv.transaction_date BETWEEN '".$from."' AND '".$to."'))";
		}
		$listsql = "SELECT v.id AS vendor_id, po.party_name, v.display_name,
				-- Total PO Value
				COALESCE(SUM(DISTINCT po.total_amount), 0) AS total_po_value,
				-- Total GRN Value
				COALESCE(SUM(DISTINCT grn.total_amount), 0) AS total_grn_value,
				-- Total Invoice Value
				COALESCE(SUM(DISTINCT inv.total_amount), 0) AS total_invoice_value,
				-- % of Total Spend
				ROUND(
					(COALESCE(SUM(DISTINCT inv.total_amount), 0) /
					NULLIF(
						(SELECT SUM(total_amount) FROM purchase_invoices WHERE is_active = 0 AND company_id = '".$company_id."'),
						0
					)) * 100, 2
				) AS spend_percent, COUNT(DISTINCT po.id) AS no_of_pos, COUNT(DISTINCT grn.id) AS no_of_grns, COUNT(DISTINCT inv.id) AS no_of_invoices, MAX(po.transaction_date) AS last_purchase_date
			FROM vendors v
			LEFT JOIN purchase_orders po 
				ON v.id = po.party_id AND po.is_active = 0 AND po.company_id = '".$company_id."'
			LEFT JOIN goods_receipt_notes grn 
				ON v.id = grn.party_id AND grn.is_active = 0 AND grn.company_id = '".$company_id."'
			LEFT JOIN purchase_invoices inv 
				ON v.id = inv.party_id AND inv.is_active = 0 AND inv.company_id = '".$company_id."'
			WHERE v.company_id = '".$company_id."' $whr
			GROUP BY v.id, po.party_name, v.display_name ORDER BY total_invoice_value DESC";
		$rows = $callConfig->getAllRows($listsql);
		$return['spend_analysis_vendor_list'] = $rows;
		$return['sql'] = $listsql;

		return $return;
	}

	function togetspendanalysisbycategory($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		// ============================
		// 📅 Date Filter Variables
		// ============================
		$from_date = !empty($get['from_date']) ? $get['from_date'] : date('Y-m-01');
		$to_date   = !empty($get['to_date']) ? $get['to_date'] : date('Y-m-t');
		// ============================
		// 🧾 Main Query with transaction_date filter
		// ============================
		$listsql = "SELECT ic.id AS category_id, ic.category_description AS category_name,
				COALESCE(SUM(DISTINCT poi.total_amount), 0) AS total_po_value,
				COALESCE(SUM(DISTINCT gi.total_amount), 0) AS total_grn_value,
				COALESCE(SUM(DISTINCT pii.total_amount), 0) AS total_invoice_value,
				ROUND(
					(COALESCE(SUM(DISTINCT pii.total_amount), 0) /
					NULLIF(
						(SELECT SUM(total_amount) 
						FROM pi_items 
						WHERE is_active = 0 
						AND company_id = '".$company_id."'),
					0)
					) * 100, 2
				) AS spend_percent,
				COUNT(DISTINCT poi.transaction_id) AS no_of_pos,
				COUNT(DISTINCT gi.transaction_id) AS no_of_grns,
				COUNT(DISTINCT pii.transaction_id) AS no_of_invoices
			FROM item_categories ic
			LEFT JOIN items i 
				ON ic.id = i.category_id 
				AND i.is_active = 0 
				AND i.company_id = '".$company_id."'
			LEFT JOIN po_items poi
				ON i.id = poi.item_id
				AND poi.is_active = 0
				AND poi.company_id = '".$company_id."'
				AND poi.transaction_id IN (
					SELECT id FROM purchase_orders 
					WHERE is_active = 0 
					AND company_id = '".$company_id."'
					AND transaction_date BETWEEN '".$from_date."' AND '".$to_date."'
				)
			LEFT JOIN grn_items gi
				ON i.id = gi.item_id
				AND gi.is_active = 0
				AND gi.company_id = '".$company_id."'
				AND gi.transaction_id IN (
					SELECT id FROM goods_receipt_notes 
					WHERE is_active = 0 
					AND company_id = '".$company_id."'
					AND transaction_date BETWEEN '".$from_date."' AND '".$to_date."'
				)
			LEFT JOIN pi_items pii
				ON i.id = pii.item_id
				AND pii.is_active = 0
				AND pii.company_id = '".$company_id."'
				AND pii.transaction_id IN (
					SELECT id FROM purchase_invoices 
					WHERE is_active = 0 
					AND company_id = '".$company_id."'
					AND transaction_date BETWEEN '".$from_date."' AND '".$to_date."'
				)
			WHERE ic.company_id = '".$company_id."' GROUP BY ic.id, ic.category_description ORDER BY total_invoice_value DESC ";
		$rows = $callConfig->getAllRows($listsql);
		$return['spend_analysis_category_list'] = $rows;
		$return['sql'] = $listsql;

		return $return;
	}

	function togetcancelledoramendedorders($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		// ============================
		// 📅 Date Filter
		// ============================
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = $get['from_date'];
			$to   = $get['to_date'];
			$whr .= " AND so.transaction_date BETWEEN '".$from."' AND '".$to."' ";
		}

		// ============================
		// 🧾 Main Query
		// ============================
		$listsql = "SELECT so.id AS order_id, so.transaction_number AS order_number, so.transaction_date AS order_date, 
				so.party_id, so.party_name, so.total_amount AS original_amount,
				CASE 
					WHEN so.cancel_invoice = 'Y' THEN 'Cancelled'
					WHEN am.id IS NOT NULL THEN 'Amended'
					ELSE 'Open'
				END AS order_status,
				am.transaction_date AS amended_date, am.transaction_number AS amendment_number, am.total_amount AS revised_amount,
				ROUND(
					((COALESCE(am.total_amount, 0) - so.total_amount) / 
					NULLIF(so.total_amount, 0)) * 100, 2
				) AS percentage_change,
				CASE 
					WHEN so.cancel_invoice = 'Y' THEN so.cancel_invoice_reason
					WHEN am.id IS NOT NULL THEN 'Amendment Made'
					ELSE NULL
				END AS reason_description, am.created_by AS amended_by, so.cancel_invoice_date AS cancelled_date
			FROM sales_invoices so
			LEFT JOIN amendments am 
				ON so.id = am.ref_id 
				AND am.ref_type = 'sales_invoices'
				AND am.is_active = 0
				AND am.company_id = '".$company_id."'
			WHERE so.company_id = '".$company_id."'
			AND (so.cancel_invoice = 'Y' OR am.id IS NOT NULL)
			$whr
			ORDER BY so.transaction_date DESC";
		$rows = $callConfig->getAllRows($listsql);
		$return['cancelled_amended_orders_list'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}

	function togetsalesbyproduct($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		// ============================
		// 📅 Date Filter
		// ============================
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = $get['from_date'];
			$to   = $get['to_date'];
			$whr .= " AND si.transaction_date BETWEEN '".$from."' AND '".$to."' ";
		}
		// ============================
		// 🧾 Main Query
		// ============================
		$listsql = "SELECT i.id AS product_id,i.sku AS product_code,i.item_name AS product_name,ic.category_description AS product_category,uom.uom_short_code AS uom,
				SUM(soi.item_quantity) AS total_quantity_sold,SUM(soi.item_amount) AS total_sales_value,SUM(soi.discount_amount) AS total_discount,SUM(soi.tax_amount) AS total_tax,SUM(soi.total_amount) AS net_sales_value,COUNT(DISTINCT soi.transaction_id) AS no_of_orders,
				ROUND(
					CASE WHEN SUM(soi.item_quantity) > 0 
						THEN SUM(soi.total_amount) / SUM(soi.item_quantity)
						ELSE 0 END, 2
				) AS avg_selling_price
			FROM so_items soi
			INNER JOIN sales_orders si ON soi.transaction_id = si.id AND si.is_active = 0 AND si.company_id = '".$company_id."'
			INNER JOIN items i ON soi.item_id = i.id AND i.is_active = 0 AND i.company_id = '".$company_id."'
			LEFT JOIN item_categories ic ON i.category_id = ic.id AND ic.company_id = '".$company_id."'
			LEFT JOIN unit_of_measurements uom ON i.purchase_uom = uom.id AND uom.company_id = '".$company_id."'
			WHERE soi.is_active = 0 AND soi.company_id = '".$company_id."' $whr GROUP BY i.id, i.sku, i.item_name, ic.category_description, i.purchase_uom ORDER BY net_sales_value DESC";
		$rows = $callConfig->getAllRows($listsql);
		$return['sales_by_product_list'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetsalesbycustomer($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		// ============================
		// 📅 Date Filter
		// ============================
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = $get['from_date'];
			$to   = $get['to_date'];
			$whr .= " AND si.transaction_date BETWEEN '".$from."' AND '".$to."' ";
		}
		// ============================
		// 🧾 Main Query
		// ============================
		$listsql = "SELECT v.id AS customer_id,v.display_name AS customer_name,COUNT(DISTINCT si.id) AS no_of_orders,SUM(soi.item_quantity) AS total_quantity_sold,SUM(soi.item_amount) AS total_sales_value,SUM(soi.discount_amount) AS total_discount,SUM(soi.tax_amount) AS total_tax,SUM(soi.total_amount) AS net_sales_value,
				ROUND(CASE WHEN COUNT(DISTINCT si.id) > 0 
						THEN SUM(soi.total_amount) / COUNT(DISTINCT si.id)
						ELSE 0 END, 2
				) AS avg_order_value,
				(SELECT i.item_name FROM so_items soi2
					INNER JOIN items i ON soi2.item_id = i.id
					WHERE soi2.transaction_id IN (
						SELECT so2.id FROM sales_orders so2
						WHERE so2.party_id = si.party_id
						AND so2.is_active = 0
						AND so2.company_id = si.company_id
						AND so2.transaction_date BETWEEN '$from' AND '$to'
					)
					GROUP BY i.item_name ORDER BY SUM(soi2.item_quantity) DESC LIMIT 1
				) AS top_product
			FROM sales_orders si
			INNER JOIN so_items soi ON si.id = soi.transaction_id AND soi.is_active = 0 AND soi.company_id = '".$company_id."'
			LEFT JOIN vendors v ON v.id = si.party_id AND v.is_active = 0 AND v.company_id = '".$company_id."'
			WHERE si.is_active = 0 AND si.company_id = '".$company_id."' $whr GROUP BY v.id, v.display_name ORDER BY net_sales_value DESC ";
		$rows = $callConfig->getAllRows($listsql);
		$return['sales_by_customer_list'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetsalesbyregion($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		// ============================
		// 📅 Date Filter
		// ============================
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = $get['from_date'];
			$to   = $get['to_date'];
			$whr .= " AND si.transaction_date BETWEEN '".$from."' AND '".$to."' ";
		}
		// ============================
		// 🧾 Main Query
		// ============================
		$listsql = "SELECT l.id AS region_id,l.location_name AS region_name,COUNT(DISTINCT si.id) AS no_of_orders,COUNT(DISTINCT si.party_id) AS total_customers,SUM(soi.item_quantity) AS total_quantity_sold,SUM(soi.item_amount) AS total_sales_value,SUM(soi.discount_amount) AS total_discount,SUM(soi.tax_amount) AS total_tax,SUM(soi.total_amount) AS net_sales_value,
				ROUND(CASE WHEN COUNT(DISTINCT si.id) > 0 
						THEN SUM(soi.total_amount) / COUNT(DISTINCT si.id)
						ELSE 0 END, 2
				) AS avg_order_value,
				(SELECT i.item_name 
					FROM so_items soi2
					INNER JOIN sales_orders si2 ON soi2.transaction_id = si2.id
					INNER JOIN items i ON soi2.item_id = i.id
					WHERE si2.transaction_location_id = si.transaction_location_id
					AND si2.is_active = 0
					AND si2.company_id = si.company_id
					AND si2.transaction_date BETWEEN '$from' AND '$to'
					GROUP BY i.item_name
					ORDER BY SUM(soi2.item_quantity) DESC
					LIMIT 1
				) AS top_product
			FROM sales_orders si
			INNER JOIN so_items soi ON si.id = soi.transaction_id AND soi.is_active = 0 AND soi.company_id = '".$company_id."'
			LEFT JOIN locations l ON si.transaction_location_id = l.id AND l.company_id = '".$company_id."'
			WHERE si.is_active = 0 AND si.company_id = '".$company_id."' $whr GROUP BY l.id, l.location_name ORDER BY net_sales_value DESC";
		$rows = $callConfig->getAllRows($listsql);
		$return['sales_by_region_list'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetdeliverypendingreport($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;

		$company_id = $get['company_id'];
		$whr = "";

		// 📅 Date filter
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whr .= " AND do.transaction_date >= '".$get['from_date']."' AND do.transaction_date <= '".$get['to_date']."' ";
		}

		$listsql = "
			SELECT 
				do.id AS delivery_order_id,
				do.transaction_number AS delivery_order_no,
				do.transaction_date AS delivery_order_date,
				do.party_id,
				do.party_name,
				l.location_name AS branch_name,

				SUM(doi.item_quantity) AS total_order_qty,
				SUM(doi.approved_quantity) AS total_dispatched_qty,
				SUM(doi.pending_qty) AS pending_qty,

				SUM(doi.item_quantity * doi.item_price) AS order_value,
				SUM(doi.approved_quantity * doi.item_price) AS dispatched_value,
				SUM(doi.pending_qty * doi.item_price) AS pending_value,

				do.due_date AS required_delivery_date,
				do.vehicle_no,

				CASE 
					WHEN SUM(doi.approved_quantity) = 0 THEN 'Not Dispatched'
					WHEN SUM(doi.pending_qty) > 0 THEN 'Partially Dispatched'
					ELSE 'Fully Dispatched'
				END AS dispatch_status

			FROM delivery_orders do
			INNER JOIN do_items doi
				ON do.id = doi.transaction_id
				AND doi.is_active = 0
				AND doi.company_id = '".$company_id."'
			LEFT JOIN locations l
				ON l.id = do.transaction_location_id
				AND l.company_id = do.company_id

			WHERE do.is_active = 0
			AND do.company_id = '".$company_id."'
			$whr

			GROUP BY 
				do.id, do.transaction_number, do.transaction_date,
				do.party_id, do.party_name, l.location_name,
				do.due_date, do.vehicle_no

			HAVING SUM(doi.pending_qty) > 0
			ORDER BY do.transaction_date DESC
		";

		$rows = $callConfig->getAllRows($listsql);
		$return['delivery_pending_list'] = $rows;
		$return['sql'] = $listsql;

		return $return;
	}
	function togetcustomerbalancereport($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		$whrInvoice = "";
		$whrPayment = "";
		// 📅 Date filter
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whrInvoice .= " AND si.transaction_date >= '".$get['from_date']."' AND si.transaction_date <= '".$get['to_date']."' ";
			$whrPayment .= " AND p.payment_date >= '".$get['from_date']."' AND p.payment_date <= '".$get['to_date']."' ";
		}

		$listsql = "SELECT c.id AS customer_id,c.display_name AS customer_name,
				COALESCE(c.opening_balance, 0) AS opening_balance,COALESCE(c.credit_limit, 0) AS credit_limit,
				-- 🧾 Total invoices in period
				COALESCE(SUM(DISTINCT si.total_amount), 0) AS total_invoices,
				-- 💰 Total payments received (extract party_id from JSON)
				COALESCE(SUM(DISTINCT p.amount), 0) AS total_payments,
				-- 🧾 Total credit notes (placeholder)
				0 AS total_credit_notes,
				-- 🧾 Total debit notes (placeholder)
				0 AS total_debit_notes,
				-- 📊 Closing Balance
				(
					COALESCE(c.opening_balance, 0)
					+ COALESCE(SUM(DISTINCT si.total_amount), 0)
					- COALESCE(SUM(DISTINCT p.amount), 0)
				) AS closing_balance,
				-- 💳 Available Credit
				(
					COALESCE(c.credit_limit, 0)
					- (COALESCE(c.opening_balance, 0)
					+ COALESCE(SUM(DISTINCT si.total_amount), 0)
					- COALESCE(SUM(DISTINCT p.amount), 0))
				) AS available_credit,
				-- 🕒 Overdue amount
				COALESCE(SUM(
					CASE 
						WHEN si.due_date < CURDATE() AND si.pending_amount > 0
						THEN si.pending_amount
						ELSE 0
					END
				), 0) AS overdue_amount,
				-- 🕓 Last payment date
				MAX(p.payment_date) AS last_payment_date
			FROM customers c
			LEFT JOIN sales_invoices si ON si.party_id = c.id AND si.is_active = 0 AND si.company_id = '".$company_id."' $whrInvoice
			LEFT JOIN payments p ON JSON_UNQUOTE(JSON_EXTRACT(p.party_id, '$[0]')) = c.id AND p.is_active = 0 AND p.company_id = '".$company_id."' AND p.cancel_payment = 'N' $whrPayment
			WHERE c.is_active = 0
			AND c.company_id = '".$company_id."' GROUP BY c.id, c.display_name, c.opening_balance, c.credit_limit ORDER BY closing_balance DESC";
		$rows = $callConfig->getAllRows($listsql);
		$return['customer_balance_list'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetcollectionsreport($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		$whrInvoice = "";
		$whrPayment = "";
		// 📅 Date filter
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whrInvoice .= " AND si.transaction_date >= '".$get['from_date']."' AND si.transaction_date <= '".$get['to_date']."' ";
			$whrPayment .= " AND p.payment_date >= '".$get['from_date']."' AND p.payment_date <= '".$get['to_date']."' ";
		}
		$listsql = "SELECT c.id AS customer_id,c.display_name AS customer_name,
				COALESCE(c.opening_balance, 0) AS opening_balance,
				-- 🧾 Total invoices in period
				COALESCE(SUM(DISTINCT si.total_amount), 0) AS total_invoices,
				-- 💰 Total receivable
				(COALESCE(c.opening_balance, 0) + COALESCE(SUM(DISTINCT si.total_amount), 0)) AS total_receivable,
				-- 💵 Total payments received
				COALESCE(SUM(DISTINCT p.amount), 0) AS payments_received,
				-- 📊 Collection %
				ROUND(
					CASE 
						WHEN (COALESCE(c.opening_balance, 0) + COALESCE(SUM(DISTINCT si.total_amount), 0)) > 0
						THEN (COALESCE(SUM(DISTINCT p.amount), 0) /
							(COALESCE(c.opening_balance, 0) + COALESCE(SUM(DISTINCT si.total_amount), 0))
							) * 100
						ELSE 0
					END
				, 2) AS collection_percent,
				-- 🏦 Outstanding balance
				(COALESCE(c.opening_balance, 0) + COALESCE(SUM(DISTINCT si.total_amount), 0) - COALESCE(SUM(DISTINCT p.amount), 0)) AS outstanding_balance,
				-- ⏰ Overdue amount
				COALESCE(SUM(
					CASE 
						WHEN si.due_date < CURDATE() AND si.pending_amount > 0
						THEN si.pending_amount
						ELSE 0
					END
				), 0) AS overdue_amount,
				-- 🕓 Last payment date
				MAX(p.payment_date) AS last_payment_date
			FROM customers c
			LEFT JOIN sales_invoices si ON si.party_id = c.id AND si.is_active = 0 AND si.company_id = '".$company_id."' $whrInvoice
			LEFT JOIN payments p ON JSON_UNQUOTE(JSON_EXTRACT(p.party_id, '$[0]')) = c.id AND p.is_active = 0 AND p.company_id = '".$company_id."' AND p.cancel_payment = 'N' $whrPayment  
			WHERE c.is_active = 0  AND c.company_id = '".$company_id."' GROUP BY c.id, c.display_name, c.opening_balance ORDER BY outstanding_balance DESC ";
		$rows = $callConfig->getAllRows($listsql);
		$return['collections_report'] = $rows;
		$return['sql'] = $listsql;

		return $return;
	}
	function togetcustomercreditutilizationreport($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		$whrInvoice = "";
		$whrPayment = "";
		// 📅 Date filters
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whrInvoice .= " AND si.transaction_date >= '".$get['from_date']."' AND si.transaction_date <= '".$get['to_date']."' ";
			$whrPayment .= " AND p.payment_date >= '".$get['from_date']."' AND p.payment_date <= '".$get['to_date']."' ";
		}
		$listsql = "SELECT c.id AS customer_id,c.display_name AS customer_name,
				COALESCE(c.credit_limit, 0) AS credit_limit,
				COALESCE(SUM(DISTINCT si.total_amount), 0) AS total_receivables,
				COALESCE(SUM(DISTINCT si.pending_amount), 0) AS outstanding_balance,
				COALESCE(SUM(DISTINCT si.pending_amount), 0) AS credit_utilized,
				ROUND(
					CASE 
						WHEN COALESCE(c.credit_limit, 0) > 0
						THEN (COALESCE(SUM(DISTINCT si.pending_amount), 0) / COALESCE(c.credit_limit, 0)) * 100
						ELSE 0
					END
				, 2) AS utilization_percent,
				(COALESCE(c.credit_limit, 0) - COALESCE(SUM(DISTINCT si.pending_amount), 0)) AS available_credit,
				CASE 
					WHEN ROUND(
							CASE 
								WHEN COALESCE(c.credit_limit, 0) > 0
								THEN (COALESCE(SUM(DISTINCT si.pending_amount), 0) / COALESCE(c.credit_limit, 0)) * 100
								ELSE 0
							END
						, 2) <= 80 THEN 'Safe'
					WHEN ROUND(
							CASE 
								WHEN COALESCE(c.credit_limit, 0) > 0
								THEN (COALESCE(SUM(DISTINCT si.pending_amount), 0) / COALESCE(c.credit_limit, 0)) * 100
								ELSE 0
							END
						, 2) <= 100 THEN 'Near Limit'
					ELSE 'Breached'
				END AS credit_status,
				MAX(si.transaction_date) AS last_invoice_date,
				MAX(p.payment_date) AS last_payment_date
			FROM customers c
			LEFT JOIN sales_invoices si ON si.party_id = c.id AND si.is_active = 0 AND si.company_id = '".$company_id."' $whrInvoice
			LEFT JOIN payments p ON JSON_UNQUOTE(JSON_EXTRACT(p.party_id, '$[0]')) = c.id AND p.is_active = 0 AND p.company_id = '".$company_id."' AND p.cancel_payment = 'N' $whrPayment
			WHERE c.is_active = 0 AND c.company_id = '".$company_id."'
			GROUP BY c.id, c.display_name, c.credit_limit
			ORDER BY utilization_percent DESC";
		$rows = $callConfig->getAllRows($listsql);
		$return['credit_utilization_report'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}
	function togetblockedorderscreditreport($get){
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		$whr = "";
		// 📅 Date filters
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whr .= " AND so.transaction_date >= '".$get['from_date']."' AND so.transaction_date <= '".$get['to_date']."' ";
		}
		$listsql = "SELECT so.id AS order_id,so.transaction_number AS order_number,so.transaction_date AS order_date,c.id AS customer_id,c.display_name AS customer_name,
				COALESCE(c.credit_limit, 0) AS credit_limit,
				COALESCE((
					SELECT SUM(si.pending_amount)
					FROM sales_invoices si
					WHERE si.party_id = c.id
					AND si.company_id = so.company_id
					AND si.is_active = 0
				), 0) AS current_utilization,
				COALESCE(so.total_amount, 0) AS order_value,
				(COALESCE((
					SELECT SUM(si.pending_amount)
					FROM sales_invoices si
					WHERE si.party_id = c.id
					AND si.company_id = so.company_id
					AND si.is_active = 0
				), 0) + COALESCE(so.total_amount, 0)) AS new_total,
				(COALESCE(c.credit_limit, 0) - COALESCE((
					SELECT SUM(si.pending_amount)
					FROM sales_invoices si
					WHERE si.party_id = c.id
					AND si.company_id = so.company_id
					AND si.is_active = 0
				), 0)) AS available_credit,
				GREATEST(
					(COALESCE((
						SELECT SUM(si.pending_amount)
						FROM sales_invoices si
						WHERE si.party_id = c.id
						AND si.company_id = so.company_id
						AND si.is_active = 0
					), 0) + COALESCE(so.total_amount, 0)) - COALESCE(c.credit_limit, 0),
					0
				) AS excess_amount,
				ROUND(
					CASE 
						WHEN COALESCE(c.credit_limit, 0) > 0
						THEN ((COALESCE((
							SELECT SUM(si.pending_amount)
							FROM sales_invoices si
							WHERE si.party_id = c.id
							AND si.company_id = so.company_id
							AND si.is_active = 0
						), 0) + COALESCE(so.total_amount, 0)) / COALESCE(c.credit_limit, 0)) * 100
						ELSE 0
					END
				, 2) AS utilization_percent_after_order,
				so.originator AS salesperson_id
			FROM sales_orders so
			INNER JOIN customers c ON c.id = so.party_id AND c.company_id = so.company_id AND c.is_active = 0
			WHERE so.is_active = 0 AND so.company_id = '".$company_id."' $whr AND COALESCE(c.credit_limit, 0) > 0
			AND (
				(COALESCE((
					SELECT SUM(si.pending_amount)
					FROM sales_invoices si
					WHERE si.party_id = c.id
					AND si.company_id = so.company_id
					AND si.is_active = 0
				), 0) + COALESCE(so.total_amount, 0)) >= COALESCE(c.credit_limit, 0) * 0.8
			)
			ORDER BY utilization_percent_after_order DESC ";
		$rows = $callConfig->getAllRows($listsql);
		$return['blocked_orders_report'] = $rows;
		$return['sql'] = $listsql;
		return $return;
	}

	function togetcustomerpaymenthistoryreport($get){
		global $callConfig;
		$return = array();
		$company_id = $get['company_id'];
		// 📅 Handle date filters
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whr .= " AND p.payment_date BETWEEN '".$get['from_date']."' AND '".$get['to_date']."' ";
		}
		$listsql = "SELECT c.display_name AS customer_name,p.payment_date,p.transaction_no AS payment_reference_no,p.payment_type AS payment_mode,COALESCE(p.amount, 0) AS payment_amount,
				COALESCE(p.tds_amount, 0) AS tds_deductions,
				(COALESCE(p.amount, 0) - COALESCE(p.tds_amount, 0)) AS net_received,
				COALESCE(p.unallocated_amount, 0) AS balance_after_payment,
				p.remarks
			FROM payments p
			LEFT JOIN customers c ON JSON_UNQUOTE(JSON_EXTRACT(p.party_id, '$[0]')) = c.id AND c.company_id = p.company_id AND c.is_active = 0
			WHERE p.is_active = 0 AND p.cancel_payment = 'N' AND p.company_id = '".$company_id."' $whr ORDER BY p.payment_date DESC, c.display_name ASC";
		$return['sql'] = $listsql;
		$return['payment_history_list'] = $callConfig->getAllRows($listsql);
		return $return;
	}
	function togettaxreportbyregion($get) {
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		// ✅ Company ID is mandatory
		$company_id = isset($get['company_id']) ? $get['company_id'] : 0;
		// ✅ Dynamic WHERE for dates
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whr .= " AND si.transaction_date BETWEEN '".$get['from_date']."' AND '".$get['to_date']."' ";
		}
		// ✅ Main Query
		$query = "SELECT l.location_name AS region_name,
				SUM(sii.item_amount) AS total_taxable_amount,
				SUM(sit.tax_amount) AS total_tax_amount,
				ROUND(AVG(sit.tax_value), 2) AS avg_tax_rate_percent,
				COUNT(DISTINCT si.id) AS total_invoices,
				COUNT(DISTINCT sii.id) AS total_items,
				COUNT(sit.id) AS total_tax_lines
			FROM sales_invoices si
			JOIN si_items sii  ON si.id = sii.transaction_id AND sii.is_active = 0 AND sii.company_id = '".$company_id."'
			JOIN si_item_taxes sit  ON sii.id = sit.transaction_item_id AND sit.is_active = 0 AND sit.company_id = '".$company_id."'
			LEFT JOIN locations l  ON si.transaction_location_id = l.id  AND l.company_id = '".$company_id."'
			WHERE si.is_active = 0 AND si.company_id = '".$company_id."'  $whr
			GROUP BY l.location_name ORDER BY l.location_name";
		// ✅ Execute
		$res = $callConfig->getAllRows($query);
		// ✅ Return
		$return['report_data'] = $res;

		return $return;
	}
	function togettopcustomercontributionreport($get) {
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		// ✅ Company ID (Required)
		$company_id = isset($get['company_id']) ? $get['company_id'] : 0;
		// ✅ Optional Date Filter
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whr .= " AND si.transaction_date BETWEEN '".$get['from_date']."' AND '".$get['to_date']."' ";
		}
		// ✅ Subquery for total gross (for contribution%)
		$totalGrossSubquery = "
			(SELECT SUM(total_amount) 
			FROM sales_invoices 
			WHERE is_active = 0 
			AND company_id = '".$company_id."'
			$whr
			)";
		// ✅ Main Query
		$query = "SELECT si.party_id,si.party_name AS customer_name,
				SUM(si.amount) AS total_sales_amount,
				SUM(si.tax_amount) AS total_tax_amount,
				SUM(si.total_amount) AS gross_total_amount,
				COUNT(si.id) AS total_invoices,
				ROUND(AVG(si.total_amount), 2) AS avg_invoice_value,
				ROUND((SUM(si.total_amount) / $totalGrossSubquery) * 100, 2) AS contribution_percentage
			FROM sales_invoices si
			WHERE si.is_active = 0 AND si.company_id = '".$company_id."' $whr
			GROUP BY si.party_id, si.party_name ORDER BY total_sales_amount DESC";
		$res = $callConfig->getAllRows($query);
		$return['report_data'] = $res;
		return $return;
	}
	function togetprofitabilityreport($get) {
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		// ✅ Company ID required
		$company_id = isset($get['company_id']) ? $get['company_id'] : 0;
		// ✅ Date filter condition
		$whr = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$whr .= " AND si.transaction_date BETWEEN '".$get['from_date']."' AND '".$get['to_date']."' ";
		}
		// ✅ Report Level (customer / product)
		$level = isset($get['report_level']) ? strtolower($get['report_level']) : 'customer';
		if ($level == 'customer') {
			$query = "SELECT si.party_id,si.party_name AS name,
					SUM(si.total_amount) AS total_sales,
					SUM(sii.item_price * sii.item_quantity) AS total_cost,
					SUM(si.tax_amount) AS total_tax,
					(SUM(si.total_amount) - SUM(sii.item_price * sii.item_quantity)) AS gross_profit,
					ROUND(((SUM(si.total_amount) - SUM(sii.item_price * sii.item_quantity)) / SUM(si.total_amount)) * 100, 2) AS profit_percentage,
					COUNT(DISTINCT si.id) AS total_invoices
				FROM sales_invoices si
				JOIN si_items sii ON si.id = sii.transaction_id
				WHERE si.is_active = 0 AND si.company_id = '".$company_id."' $whr GROUP BY si.party_id, si.party_name ORDER BY gross_profit DESC";
		} else {
			$query = "SELECT sii.item_id, sii.item_name AS name,
					SUM(sii.item_quantity) AS total_qty,
					SUM(sii.total_amount) AS total_sales,
					SUM(sii.item_price * sii.item_quantity) AS total_cost,
					SUM(sii.tax_amount) AS total_tax,
					(SUM(sii.total_amount) - SUM(sii.item_price * sii.item_quantity)) AS gross_profit,
					ROUND(((SUM(sii.total_amount) - SUM(sii.item_price * sii.item_quantity)) / SUM(sii.total_amount)) * 100, 2) AS profit_percentage,
					COUNT(DISTINCT sii.transaction_id) AS total_invoices
				FROM si_items sii
				JOIN sales_invoices si ON si.id = sii.transaction_id
				WHERE si.is_active = 0 AND si.company_id = '".$company_id."' $whr GROUP BY sii.item_id, sii.item_name ORDER BY gross_profit DESC";
		}
		$res = $callConfig->getAllRows($query);
		$return['report_data'] = $res;
		return $return;
	}
	function togetfreightcostreport($get) {
		global $callConfig;
		$return = array();
		$return['get'] = $get;
		$company_id = $get['company_id'];
		$from_date  = !empty($get['from_date']) ? $get['from_date'] : '';
		$to_date    = !empty($get['to_date']) ? $get['to_date'] : '';
		// ✅ Build WHERE condition
		$whr = " WHERE si.is_active = 0 AND si.company_id = '".$company_id."' ";
		if ($from_date && $to_date) {
			$whr .= " AND si.transaction_date BETWEEN '".$from_date."' AND '".$to_date."' ";
		}
		// ✅ Get invoice data with additional charges
		$query = "SELECT si.transaction_number, si.transaction_date, si.party_name, si.additional_charges
				FROM sales_invoices si $whr ORDER BY si.transaction_date DESC";
		$res = $callConfig->getAllRows($query);
		$reportData = [];
		foreach ($res as $row) {
			$baseAmount = 0;
			$taxAmount = 0;
			$totalAmount = 0;
			if (!empty($row->additional_charges)) {
				$charges = json_decode($row->additional_charges, true);
				if (is_array($charges)) {
					foreach ($charges as $ch) {
						foreach ($ch as $key => $val) {
							if (is_numeric($val)) {
								if (stripos($key, '_tax_amount') !== false) {
									$taxAmount += (float)$val;
								} elseif (stripos($key, '_amount') !== false) {
									$baseAmount += (float)$val;
								}
							}
						}
					}
				}
			}

			$totalAmount = $baseAmount + $taxAmount;
			$reportData[] = [
				'transaction_number' => $row->transaction_number,
				'transaction_date'   => $row->transaction_date,
				'party_name'         => $row->party_name,
				'base_amount'        => $baseAmount,
				'tax_amount'         => $taxAmount,
				'total_amount'       => $totalAmount
			];
		}

		$return['freight_report'] = $reportData;
		return $return;
	}

	// added by koti end
	
	// added by omkar start
	function getOpenPOReport($get){
        global $callConfig;
        $sql = "SELECT po.id AS po_id, po.transaction_number AS po_no, po.transaction_date AS po_date, po.party_name AS vendor_name, poi.item_id, poi.item_name, poi.description, poi.item_quantity AS ordered_qty, IFNULL(SUM(gri.item_quantity), 0) AS received_qty, (poi.item_quantity - IFNULL(SUM(gri.item_quantity), 0)) AS pending_qty, po.due_date AS due_date, po.total_amount AS po_value FROM purchase_orders po INNER JOIN po_items poi ON po.id = poi.transaction_id AND po.company_id = '" . $get['company_id'] . "' AND po.is_active = '0' LEFT JOIN goods_receipt_notes grn ON grn.ref_id = po.id AND grn.company_id = po.company_id LEFT JOIN grn_items gri ON gri.transaction_id = grn.id AND gri.item_id = poi.item_id GROUP BY po.id, poi.item_id ORDER BY po.transaction_date DESC, po.transaction_number ASC ";
        $rows = $callConfig->getAllRows($sql);
        $result['open_po_report'] = $rows;
        return $result;
    }
	function getUrgentEmergencyPurchaseDetails($get){
		global $callConfig;
		$whereDate = "";
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from_date = date('Y-m-d', strtotime($get['from_date']));
			$to_date = date('Y-m-d', strtotime($get['to_date']));
			$whereDate = " AND vpi.transaction_date BETWEEN '{$from_date}' AND '{$to_date}' ";
		}
		$whereCompany = "";
		if (!empty($get['company_id'])) {
			$whereCompany = " AND vpi.company_id = '" . $get['company_id'] . "' ";
		}
		$sql = "SELECT vpi.id AS vpi_id, vpi.transaction_number AS request_no, vpi.transaction_date AS request_date, vpi.party_name AS vendor_name, vpi.party_email AS vendor_email, vpi.party_phone AS vendor_phone, vpi.total_amount AS estimated_cost, vpi.notes, vpi.printing_notes, vpi.current_status AS status_name, vpi.status_date, vi.item_id, vi.item_name, vi.description AS item_description, vi.item_quantity AS qty, vi.purchase_uom AS unit, vi.item_price AS unit_price, vi.total_amount AS item_total FROM vendor_proforma_invoices vpi INNER JOIN vpi_items vi ON vpi.id = vi.transaction_id AND vi.is_active = '0' WHERE vpi.is_active = '0' $whereDate $whereCompany ORDER BY vpi.transaction_date DESC, vpi.transaction_number ASC ";
		$res = $callConfig->getAllRows($sql);
		$result['urgent_purchase_report'] = $res;
		$result['whereDate'] = $whereDate;
		$result['whereCompany'] = $whereCompany;
		$result['sql'] = $sql;
		return $result;
	}

	function getStockAgingReportDetails($get){
		global $callConfig;
		$selectedDate = !empty($get['date'])
			? date('Y-m-d', strtotime($get['date']))
			: date('Y-m-d');
		$whereCompany = "";
		if (!empty($get['company_id'])) {
			$whereCompany = " AND i.company_id = '" . $get['company_id'] . "' ";
		}
		$whereCategory = "";
		if (!empty($get['category'])) {
			$whereCategory = " AND i.category_id = '" . $get['category'] . "' ";
		}
		if (!empty($get['category'])) {
			$sql = "SELECT i.id AS item_id, i.company_id, i.category_id, ic.category_description AS category_name, i.warehouse_id, w.warehouse_name, i.sku AS product_code, i.item_name, i.item_description, i.purchase_uom AS unit, i.opening_quantity, COALESCE(SUM(CASE WHEN iwb.balance_date <= '{$selectedDate}' THEN iwb.in_qty ELSE 0 END), 0) AS total_in_qty, COALESCE(SUM(CASE WHEN iwb.balance_date <= '{$selectedDate}' THEN iwb.out_qty ELSE 0 END), 0) AS total_out_qty, ( SELECT iwb2.closing_qty FROM item_wa_balances iwb2 WHERE iwb2.item_id = i.id AND iwb2.balance_date <= '{$selectedDate}' ORDER BY iwb2.balance_date DESC LIMIT 1 ) AS closing_stock, ( SELECT MAX(iwb3.balance_date) FROM item_wa_balances iwb3 WHERE iwb3.item_id = i.id AND iwb3.balance_date <= '{$selectedDate}' ) AS last_movement_date, SUM(CASE WHEN DATEDIFF('{$selectedDate}', iwb.balance_date) BETWEEN 0 AND 30  THEN iwb.closing_qty ELSE 0 END) AS days_0_30, SUM(CASE WHEN DATEDIFF('{$selectedDate}', iwb.balance_date) BETWEEN 31 AND 60 THEN iwb.closing_qty ELSE 0 END) AS days_31_60, SUM(CASE WHEN DATEDIFF('{$selectedDate}', iwb.balance_date) BETWEEN 61 AND 90 THEN iwb.closing_qty ELSE 0 END) AS days_61_90, SUM(CASE WHEN DATEDIFF('{$selectedDate}', iwb.balance_date) > 90 THEN iwb.closing_qty ELSE 0 END) AS days_90_plus FROM items i LEFT JOIN item_wa_balances iwb ON i.id = iwb.item_id LEFT JOIN item_categories ic ON i.category_id = ic.id LEFT JOIN warehouses w ON i.warehouse_id = w.id WHERE i.is_active = '0' {$whereCompany} {$whereCategory} GROUP BY i.id ORDER BY ic.category_description ASC, i.id ASC ";
			$res = $callConfig->getAllRows($sql);
			$result['stock_aging_report'] = $res;
			$result['sql'] = $sql;
		} else {
			$result['stock_aging_report'] = [];
			$result['sql'] = "Query not executed — category not provided.";
		}
		$category_sql = "SELECT id, category_description FROM item_categories WHERE company_id = '" . $get['company_id'] . "' AND is_active = '0' ";
		$category_res = $callConfig->getAllRows($category_sql);
		$result['category'] = $category_res;
		$result['filters'] = [
			'selected_date' => $selectedDate,
			'Company' => $whereCompany,
			'Category' => $whereCategory,
		];
		return $result;
	}

	function getDebitOrCreditNoteRegisterDetails($get){
		global $callConfig;
		$from = '';
		$to = '';
		$date = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDateCredit = " AND c.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateDebit = " AND d.transaction_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDateCredit = " AND c.transaction_date = '{$date}'";
			$whereDateDebit = " AND d.transaction_date = '{$date}'";
		} else {
			$whereDateCredit = "";
			$whereDateDebit = "";
		}
		$whereCompanyCredit = "";
		$whereCompanyDebit = "";
		if (!empty($get['company_id'])) {
			$whereCompanyCredit = " AND c.company_id = '" . $get['company_id'] . "'";
			$whereCompanyDebit = " AND d.company_id = '" . $get['company_id'] . "'";
		}
		$creditSQL = "SELECT 'Credit Note' AS note_type, c.id, c.company_id, c.transaction_number, c.transaction_date, c.party_name, c.party_tax_number, c.doc_ref_no AS reference_no, c.doc_ref_date AS reference_date, c.amount, c.tax_amount, c.total_amount, c.current_status, s.name AS status_name, s.background_color AS status_bg, s.color AS status_color, c.created_by FROM credit_notes c LEFT JOIN status s ON s.name_value = c.current_status WHERE c.is_active = '0' {$whereCompanyCredit} {$whereDateCredit} ";
		$debitSQL = "SELECT 'Debit Note' AS note_type, d.id, d.company_id, d.transaction_number, d.transaction_date, d.party_name, d.party_tax_number, d.doc_ref_no AS reference_no, d.doc_ref_date AS reference_date, d.amount, d.tax_amount, d.total_amount, d.current_status, s.name AS status_name, s.background_color AS status_bg, s.color AS status_color, d.created_by FROM debit_notes d LEFT JOIN status s ON s.name_value = d.current_status WHERE d.is_active = '0' {$whereCompanyDebit} {$whereDateDebit} ";
		$finalSQL = $creditSQL . " UNION ALL " . $debitSQL . " ORDER BY transaction_date DESC, note_type ASC";
		$res = $callConfig->getAllRows($finalSQL);
		$result['debit_credit_note_register'] = $res;
		$result['sql'] = $finalSQL;
		return $result;
	}

	function getAPReconciliationReport($get){
		global $callConfig;
		$whereDatePO = $whereDateGRN = $whereDateINV = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDatePO = " AND po.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateGRN = " AND grn.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateINV = " AND inv.transaction_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDatePO = " AND po.transaction_date = '{$date}'";
			$whereDateGRN = " AND grn.transaction_date = '{$date}'";
			$whereDateINV = " AND inv.transaction_date = '{$date}'";
		}
		$whereCompany = '';
		if (!empty($get['company_id'])) {
			$whereCompany = " AND po.company_id = '" . $get['company_id'] . "'";
		}
		$sql = "SELECT po.transaction_number AS po_no, po.transaction_date AS po_date, po.party_name AS vendor_name, poi.item_id, poi.item_name, poi.description AS item_description, poi.item_quantity AS po_qty, poi.item_price AS unit_price, (poi.item_quantity * poi.item_price) AS po_value, IFNULL(SUM(grni.item_quantity), 0) AS gr_qty, IFNULL(SUM(grni.item_quantity * grni.item_price), 0) AS gr_value, IFNULL(inv.total_amount, 0) AS ir_value, ROUND(IFNULL(inv.total_amount / NULLIF(SUM(grni.item_quantity), 0), 0), 2) AS approx_unit_ir_rate,
            CASE WHEN SUM(grni.item_quantity) > 0 AND inv.total_amount > 0 THEN ROUND(inv.total_amount / IFNULL(SUM(grni.item_quantity * grni.item_price) / SUM(grni.item_quantity), 1), 2) ELSE 0 END AS ir_qty, (IFNULL(SUM(grni.item_quantity * grni.item_price), 0) - IFNULL(inv.total_amount, 0)) AS difference_value, 
            CASE WHEN IFNULL(SUM(grni.item_quantity), 0) = 0 THEN 'No GRN' WHEN IFNULL(inv.total_amount, 0) = 0 THEN 'GRN Not Invoiced' WHEN SUM(grni.item_quantity * grni.item_price) > inv.total_amount THEN 'Partial Invoice Pending' WHEN SUM(grni.item_quantity * grni.item_price) < inv.total_amount THEN 'Over Invoiced' ELSE 'Matched' END AS reconciliation_status FROM purchase_orders po LEFT JOIN po_items poi ON poi.transaction_id = po.id LEFT JOIN goods_receipt_notes grn ON grn.ref_id = po.id LEFT JOIN grn_items grni ON grni.transaction_id = grn.id AND grni.item_id = poi.item_id LEFT JOIN purchase_invoices inv ON inv.ref_id = po.id AND inv.party_id = po.party_id  WHERE po.is_active = '0' {$whereCompany} {$whereDatePO} GROUP BY po.id, poi.item_id ORDER BY po.transaction_date DESC ";
		$res = $callConfig->getAllRows($sql);
		$result['ap_reconciliation_report'] = $res;
		$result['sql'] = $sql;

		return $result;
	}

	function getAccruedLiabilitiesReport($get){
		global $callConfig;
 
		$whereDatePO = $whereDateGRN = $whereDateINV = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDatePO = " AND po.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateGRN = " AND grn.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateINV = " AND inv.transaction_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDatePO = " AND po.transaction_date = '{$date}'";
			$whereDateGRN = " AND grn.transaction_date = '{$date}'";
			$whereDateINV = " AND inv.transaction_date = '{$date}'";
		}
 
		$whereCompany = '';
		if (!empty($get['company_id'])) {
			$whereCompany = " AND po.company_id = '" . $get['company_id'] . "'";
		}
 
		$sql = "SELECT po.transaction_number AS po_no, po.transaction_date   AS po_date, po.party_name AS vendor_name, grn.transaction_number AS grn_no, grn.transaction_date   AS grn_date, SUM(grni.item_quantity) AS received_qty, ROUND(SUM(grni.item_price * grni.item_quantity), 2) AS received_value, IFNULL(inv.total_amount, 0) AS invoiced_value,  
		(ROUND(SUM(grni.item_price * grni.item_quantity), 2) - IFNULL(inv.total_amount, 0)) AS accrued_amount, 
		CASE WHEN IFNULL(inv.total_amount, 0) = 0 THEN 'Pending Invoice' WHEN IFNULL(inv.total_amount, 0) < ROUND(SUM(grni.item_price * grni.item_quantity), 2) THEN 'Partially Invoiced' ELSE 'Fully Invoiced'
		END AS status FROM purchase_orders po INNER JOIN goods_receipt_notes grn ON grn.ref_id = po.id INNER JOIN grn_items grni ON grni.transaction_id = grn.id LEFT JOIN purchase_invoices inv ON inv.ref_id = po.id AND inv.party_id = po.party_id AND inv.is_active = '0' WHERE po.is_active = '0' {$whereCompany} {$whereDateGRN} GROUP BY po.id, grn.id HAVING accrued_amount > 0 ORDER BY grn.transaction_date DESC ";

		$res = $callConfig->getAllRows($sql);
		$result['accrued_liabilities_report'] = $res;
		$result['sql'] = $sql;

		return $result;
	}

	function getPaymentProposalReport($get)	{
		global $callConfig; 
		$whereDate = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDate = " AND inv.due_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDate = " AND inv.due_date = '{$date}'";
		} 
		$whereCompany = '';
		if (!empty($get['company_id'])) {
			$whereCompany = " AND inv.company_id = '" . $get['company_id'] . "'";
		}
 
		$sql = "SELECT inv.transaction_number AS invoice_no, inv.transaction_date AS invoice_date, inv.due_date, po.party_name AS vendor_name, po.party_id AS vendor_id, inv.total_amount, COALESCE(p.payments_allocated, 0) AS paid_amount, (inv.total_amount - COALESCE(p.payments_allocated, 0)) AS pending_amount, CASE WHEN COALESCE(p.payments_allocated, 0) = 0 THEN 'Pending' WHEN COALESCE(p.payments_allocated, 0) < inv.total_amount THEN 'Partially Paid' ELSE 'Paid' END AS payment_status, inv.payment_terms, inv.notes AS remarks FROM purchase_invoices inv LEFT JOIN purchase_orders po ON po.id = inv.ref_no LEFT JOIN ( SELECT ref_no, SUM(allocated_amount) AS payments_allocated FROM payments WHERE is_active = '0' GROUP BY ref_no ) p ON p.ref_no = inv.id WHERE inv.is_active = '0' {$whereCompany} {$whereDate} ORDER BY inv.due_date ASC ";

		$res = $callConfig->getAllRows($sql);
		$result['payment_proposal_report'] = $res;
		$result['sql'] = $sql;

		return $result;
	}

	function getAdvancePaymentReport($get){
		global $callConfig;
 
		$whereDate = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDate = " AND inv.transaction_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDate = " AND inv.transaction_date = '{$date}'";
		}
 
		$whereCompany = '';
		if (!empty($get['company_id'])) {
			$whereCompany = " AND inv.company_id = '" . $get['company_id'] . "'";
		}
 
		$sql = "SELECT inv.transaction_number AS invoice_no, inv.transaction_date AS invoice_date, inv.due_date, po.party_name AS vendor_name, po.party_id AS vendor_id, inv.advance_payment AS advance_paid, inv.total_amount AS total_invoice_amount, COALESCE(p.total_payment, 0) AS total_payment_done, CASE WHEN COALESCE(p.total_payment, 0) = 0 THEN 'No Payment' WHEN COALESCE(p.total_payment, 0) < inv.advance_payment THEN 'Partially Paid' WHEN COALESCE(p.total_payment, 0) >= inv.advance_payment THEN 'Advance Cleared' ELSE 'Unknown' END AS payment_status, inv.payment_terms, inv.notes AS remarks FROM purchase_invoices inv LEFT JOIN purchase_orders po ON po.id = inv.ref_no LEFT JOIN (ELECT  ref_no, SUM(allocated_amount) AS total_payment FROM payments WHERE is_active = '0' GROUP BY ref_no ) p ON p.ref_no = inv.id WHERE inv.is_active = '0' {$whereCompany} {$whereDate} ORDER BY inv.transaction_date ASC ";

		$res = $callConfig->getAllRows($sql);
		$result['advance_payment_report'] = $res;
		$result['sql'] = $sql;

		return $result;
	}

	function getVendorPaymentAgingReport($get) {
	    global $callConfig;

	    $selectedDate = !empty($get['date'])
	        ? date('Y-m-d', strtotime($get['date']))
	        : date('Y-m-d');

	    $whereCompany = "";
	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND pi.company_id = '" . $get['company_id'] . "' ";
	    }

		$sql = "SELECT pi.id AS invoice_id, pi.party_id, pi.party_name, pi.transaction_number AS invoice_no, pi.transaction_date AS invoice_date, pi.due_date, pi.total_amount AS invoice_amount, IFNULL(pi.advance_payment, 0) AS paid_amount, (pi.total_amount - IFNULL(pi.advance_payment, 0)) AS outstanding_amount, DATEDIFF('{$selectedDate}', pi.due_date) AS days_overdue, 
	    CASE WHEN DATEDIFF('{$selectedDate}', pi.due_date) BETWEEN 0 AND 30 THEN (pi.total_amount - IFNULL(pi.advance_payment,0)) ELSE 0 END AS days_0_30,
	    CASE WHEN DATEDIFF('{$selectedDate}', pi.due_date) BETWEEN 31 AND 60 THEN (pi.total_amount - IFNULL(pi.advance_payment,0)) ELSE 0 END AS days_31_60,
	    CASE WHEN DATEDIFF('{$selectedDate}', pi.due_date) BETWEEN 61 AND 90 THEN (pi.total_amount - IFNULL(pi.advance_payment,0)) ELSE 0 END AS days_61_90,
	    CASE WHEN DATEDIFF('{$selectedDate}', pi.due_date) BETWEEN 91 AND 180 THEN (pi.total_amount - IFNULL(pi.advance_payment,0)) ELSE 0 END AS days_91_180,
	    CASE WHEN DATEDIFF('{$selectedDate}', pi.due_date) > 180 THEN (pi.total_amount - IFNULL(pi.advance_payment,0)) ELSE 0 END AS days_181_plus, 
	    pi.current_status, pi.company_id FROM purchase_invoices pi WHERE pi.is_active = '0' AND pi.cancel_invoice = 'N' {$whereCompany} AND pi.transaction_date <= '{$selectedDate}' ORDER BY pi.party_name ASC, pi.transaction_date ASC ";

	    $invoices = $callConfig->getAllRows($sql); 
	    $result['vendor_payment_aging_report'] = $invoices;
	    $result['sql'] = $sql;
	    $result['filters'] = [
	        'selected_date' => $selectedDate,
	        'company_filter' => $whereCompany
	    ];

	    return $result;
	}


	function getVendorReconciliationStatement($get)	{
		global $callConfig;
 
		$whereDatePO = $whereDateGRN = $whereDateINV = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDatePO = " AND po.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateGRN = " AND grn.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateINV = " AND pi.transaction_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDatePO = " AND po.transaction_date = '{$date}'";
			$whereDateGRN = " AND grn.transaction_date = '{$date}'";
			$whereDateINV = " AND pi.transaction_date = '{$date}'";
		}
 
		$whereCompany = '';
		if (!empty($get['company_id'])) {
			$whereCompany = " AND po.company_id = '" . $get['company_id'] . "'";
		}
 
		$sql = "SELECT po.party_id, po.party_name, po.transaction_number AS po_no, po.transaction_date AS po_date, po.total_amount AS po_amount, 
        grn.transaction_number AS grn_no, grn.transaction_date AS grn_date, grn.total_amount AS grn_amount, pi.transaction_number AS invoice_no, pi.transaction_date AS invoice_date, pi.total_amount AS invoice_amount, 
        SUM(poi.item_quantity) AS qty_ordered, SUM(grni.item_quantity) AS qty_received, CASE WHEN pi.id IS NULL AND grn.id IS NULL THEN 'Only PO Created' WHEN grn.id IS NOT NULL AND pi.id IS NULL THEN 'Pending Invoice' WHEN grn.id IS NULL AND pi.id IS NOT NULL THEN 'Pending GRN' WHEN ABS(po.total_amount - pi.total_amount) < 1 THEN 'Matched' ELSE 'Mismatch' END AS reconciliation_status
        FROM purchase_orders po LEFT JOIN po_items poi ON poi.transaction_id = po.id LEFT JOIN goods_receipt_notes grn  ON grn.ref_id = po.id  AND grn.ref_type = 'PO'  {$whereDateGRN} LEFT JOIN grn_items grni ON grni.transaction_id = grn.id LEFT JOIN purchase_invoices pi ON pi.ref_id = grn.id  AND pi.ref_type = 'GRN' {$whereDateINV} WHERE po.is_active = '0' {$whereCompany} {$whereDatePO} GROUP BY po.id ORDER BY po.party_name ASC, po.transaction_date ASC ";

		$res = $callConfig->getAllRows($sql); 
		$result['vendor_reconciliation_statement'] = $res;
		$result['sql'] = $sql;
		$result['filters'] = [
			'company_filter' => $whereCompany,
			'from_date' => !empty($get['from_date']) ? $get['from_date'] : null,
			'to_date' => !empty($get['to_date']) ? $get['to_date'] : null,
			'single_date' => !empty($get['date']) ? $get['date'] : null,
		];

		return $result;
	}

	function getPendingInvoiceReport($get){
		global $callConfig;

		$whereDate = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDate = " AND pi.transaction_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDate = " AND pi.transaction_date = '{$date}'";
		}

		$whereCompany = '';
		if (!empty($get['company_id'])) {
			$companyId = intval($get['company_id']);
			$whereCompany = " AND pi.company_id = '{$companyId}'";
		}

		$sql = "SELECT pi.id, pi.transaction_number AS invoice_no, pi.transaction_date AS invoice_date, pi.due_date, pi.party_name, pi.total_amount, pi.pending_amount, st.name AS status_name, st.name_value, st.background_color, st.color, CASE WHEN LOWER(st.name) IN ('pending','draft','awaiting approval') THEN 'Pending Invoice' WHEN LOWER(st.name) IN ('generated','completed','issued','done','approved') THEN 'Completed Invoice' ELSE 'Other' END AS report_status FROM purchase_invoices pi LEFT JOIN `status` st ON st.name_value = pi.current_status  WHERE pi.is_active = '0' AND pi.cancel_invoice = 'N' {$whereCompany} {$whereDate} ORDER BY pi.transaction_date ASC ";

		$res = $callConfig->getAllRows($sql); 
		$result['pending_invoice_report'] = $res;
		$result['sql'] = $sql;
		$result['filters'] = [
			'company_filter' => $whereCompany,
			'from_date' => !empty($get['from_date']) ? $get['from_date'] : null,
			'to_date' => !empty($get['to_date']) ? $get['to_date'] : null,
			'single_date' => !empty($get['date']) ? $get['date'] : null,
		];

		return $result;
	}

	function getSalesTrendReport($get){
	    global $callConfig;

	    $category = $get['category'] ?? 'monthly';  
	 
	    $whereDate = '';
	    if (!empty($get['from_date']) && !empty($get['to_date'])) {
	        $from = date('Y-m-d', strtotime($get['from_date']));
	        $to   = date('Y-m-d', strtotime($get['to_date']));
	        $whereDate = " AND so.transaction_date BETWEEN '{$from}' AND '{$to}'";
	    }
	 
	    $whereCompany = '';
	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND so.company_id = '" . $get['company_id'] . "'";
	    } 
	    switch ($category) {
	        case 'monthly':
	            $groupBy = "DATE_FORMAT(so.transaction_date, '%Y-%m')";
	            $selectPeriod = $groupBy . " AS period";
	            break;

	        case 'quaterly':
	            $groupBy = "CONCAT(YEAR(so.transaction_date), '-Q', QUARTER(so.transaction_date))";
	            $selectPeriod = $groupBy . " AS period";
	            break;

	        case 'yearly':
	            $groupBy = "YEAR(so.transaction_date)";
	            $selectPeriod = $groupBy . " AS period";
	            break;

	        default:
	            $groupBy = "DATE_FORMAT(so.transaction_date, '%Y-%m')";
	            $selectPeriod = $groupBy . " AS period";
	    }
	 
	    $sql = "SELECT {$selectPeriod}, COUNT(so.id) AS total_orders, SUM(so.total_amount) AS total_sales, SUM(so.tax_amount) AS total_tax, SUM(so.discount_amount) AS total_discount, AVG(so.total_amount) AS avg_order_value FROM sales_orders so WHERE so.is_active = '0' {$whereCompany} {$whereDate} GROUP BY {$groupBy} ORDER BY so.transaction_date ASC ";

	    $res = $callConfig->getAllRows($sql);

	    return [
	        'sales_trend_report' => $res,
	        'sql' => $sql
	    ];
	}


	function getDaysSalesOutstandingReport($get){
	    global $callConfig;

	    $whereDate = '';
	    if (!empty($get['from_date']) && !empty($get['to_date'])) {
	        $from = date('Y-m-d', strtotime($get['from_date']));
	        $to = date('Y-m-d', strtotime($get['to_date']));
	        $whereDate = " AND si.transaction_date BETWEEN '{$from}' AND '{$to}'";
	    } elseif (!empty($get['date'])) {
	        $date = date('Y-m-d', strtotime($get['date']));
	        $whereDate = " AND si.transaction_date = '{$date}'";
	    }

	    $whereCompany = '';
	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND si.company_id = '" . $get['company_id'] . "'";
	    }

	    $sql = " SELECT si.party_name, si.transaction_number AS invoice_no, si.transaction_date AS invoice_date, si.due_date, si.total_amount AS invoice_amount, IFNULL(SUM(ra.paid_amonut), 0) AS payment_received_amount, IFNULL(DATEDIFF(NOW(), si.transaction_date), 0) AS days_outstanding, CASE WHEN si.total_amount - IFNULL(SUM(ra.paid_amonut), 0) > 0 THEN 'Pending' WHEN IFNULL(SUM(ra.paid_amonut), 0) = si.total_amount THEN 'Paid On Time' ELSE 'Paid Late' END AS status FROM sales_invoices si LEFT JOIN receipt_allocations ra ON ra.transaction_table = 'sales_invoices' AND ra.transaction_id = si.id AND ra.is_active = '0' WHERE si.is_active = '0' {$whereCompany} {$whereDate} GROUP BY si.id ORDER BY si.transaction_date ASC ";
	    $res = $callConfig->getAllRows($sql); 
	    $result['dso_report'] = $res;
	    $result['sql'] = $sql;

	    return $result;
	}


	function getARAgingReport($get) {
	    global $callConfig;
	 
	    $selectedDate = !empty($get['date'])
	        ? date('Y-m-d', strtotime($get['date']))
	        : date('Y-m-d');

	    $whereCompany = "";
	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND si.company_id = '" . $get['company_id'] . "' ";
	    }
	 
	    $sql = "SELECT  si.id AS invoice_id, si.party_id, si.party_name, si.transaction_number AS invoice_no, si.transaction_date AS invoice_date, si.due_date, si.total_amount AS invoice_amount, IFNULL(si.total_amount - si.pending_amount, 0) AS paid_amount, si.pending_amount AS outstanding_amount, DATEDIFF('{$selectedDate}', si.due_date) AS days_overdue, 
	        CASE WHEN DATEDIFF('{$selectedDate}', si.due_date) BETWEEN 0 AND 30 THEN si.pending_amount ELSE 0 END AS days_0_30,
	        CASE WHEN DATEDIFF('{$selectedDate}', si.due_date) BETWEEN 31 AND 60 THEN si.pending_amount ELSE 0 END AS days_31_60,
	        CASE WHEN DATEDIFF('{$selectedDate}', si.due_date) BETWEEN 61 AND 90 THEN si.pending_amount ELSE 0 END AS days_61_90,
	        CASE WHEN DATEDIFF('{$selectedDate}', si.due_date) BETWEEN 91 AND 180 THEN si.pending_amount ELSE 0 END AS days_91_180,
	        CASE WHEN DATEDIFF('{$selectedDate}', si.due_date) > 180 THEN si.pending_amount ELSE 0 END AS days_181_plus, si.current_status, s.name AS status_name, s.background_color AS status_bg_color, s.color AS status_text_color, si.company_id FROM purchase_invoices si LEFT JOIN status s  ON si.current_status = s.name_value AND s.is_active = '0' WHERE si.is_active = '0' AND si.cancel_invoice = 'N' {$whereCompany} AND si.transaction_date <= '{$selectedDate}' ORDER BY si.party_name ASC, si.transaction_date ASC "; 
	    $invoices = $callConfig->getAllRows($sql); 
	    $result['ar_aging_report'] = $invoices;
	    $result['sql'] = $sql;
	    $result['filters'] = [
	        'selected_date' => $selectedDate,
	        'company_filter' => $whereCompany
	    ];

	    return $result;
	}

	function getPriceVarianceReport($get){
	    global $callConfig;
	 
	    $fromDate = !empty($get['from_date']) ? date('Y-m-d', strtotime($get['from_date'])) : '1970-01-01';
	    $toDate   = !empty($get['to_date']) ? date('Y-m-d', strtotime($get['to_date'])) : date('Y-m-d');
	 
	    $whereCompany = "";
	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND po.company_id = '" . $get['company_id'] . "' ";
	    }
	 
	    $sql = " SELECT  po.id AS purchase_order_id, po.transaction_number AS po_number, po.transaction_date AS po_date, po.party_name AS supplier_name, poi.item_id, poi.item_name, i.sku AS item_code, i.purchase_price AS list_price, poi.item_price AS ordered_price, gri.item_price AS actual_price, ROUND((gri.item_price - i.purchase_price), 2) AS variance_amount, ROUND(((gri.item_price - i.purchase_price) / i.purchase_price) * 100, 2) AS variance_percent, gri.item_quantity AS received_qty, ROUND((gri.item_quantity * (gri.item_price - i.purchase_price)), 2) AS total_variance_value, po.total_amount AS po_total, po.current_status, s.name AS status_name, s.background_color AS status_bg_color, s.color AS status_text_color, po.company_id FROM purchase_orders po INNER JOIN po_items poi ON po.id = poi.transaction_id LEFT JOIN goods_receipt_notes grn  ON grn.ref_id = po.id  AND grn.is_active = '0' LEFT JOIN grn_items gri ON grn.id = gri.transaction_id  AND gri.item_id = poi.item_id LEFT JOIN items i  ON poi.item_id = i.id LEFT JOIN status s ON po.current_status = s.name_value AND s.is_active = '0' WHERE po.is_active = '0' {$whereCompany} AND po.transaction_date BETWEEN '{$fromDate}' AND '{$toDate}' ORDER BY po.transaction_date DESC, po.transaction_number ASC "; 
	    $reportData = $callConfig->getAllRows($sql); 
	    $result['price_variance_report'] = $reportData;
	    $result['sql'] = $sql;
	    $result['filters'] = [
	        'from_date' => $fromDate,
	        'to_date' => $toDate,
	        'company_filter' => $whereCompany
	    ];

	    return $result;
	}

	function getDebitOrCreditMemoRegisterDetails($get){
		global $callConfig;
		$from = '';
		$to = '';
		$date = '';
		if (!empty($get['from_date']) && !empty($get['to_date'])) {
			$from = date('Y-m-d', strtotime($get['from_date']));
			$to = date('Y-m-d', strtotime($get['to_date']));
			$whereDateCredit = " AND c.transaction_date BETWEEN '{$from}' AND '{$to}'";
			$whereDateDebit = " AND d.transaction_date BETWEEN '{$from}' AND '{$to}'";
		} elseif (!empty($get['date'])) {
			$date = date('Y-m-d', strtotime($get['date']));
			$whereDateCredit = " AND c.transaction_date = '{$date}'";
			$whereDateDebit = " AND d.transaction_date = '{$date}'";
		} else {
			$whereDateCredit = "";
			$whereDateDebit = "";
		}
		$whereCompanyCredit = "";
		$whereCompanyDebit = "";
		if (!empty($get['company_id'])) {
			$whereCompanyCredit = " AND c.company_id = '" . $get['company_id'] . "'";
			$whereCompanyDebit = " AND d.company_id = '" . $get['company_id'] . "'";
		}
		$creditSQL = "SELECT 'Credit Note' AS note_type, c.id, c.company_id, c.transaction_number, c.transaction_date, c.party_name, c.party_tax_number, c.doc_ref_no AS reference_no, c.doc_ref_date AS reference_date, c.amount, c.tax_amount, c.total_amount, c.current_status, s.name AS status_name, s.background_color AS status_bg, s.color AS status_color, c.created_by FROM credit_notes c LEFT JOIN status s ON s.name_value = c.current_status WHERE c.is_active = '0' {$whereCompanyCredit} {$whereDateCredit} ";
		$debitSQL = "SELECT 'Debit Note' AS note_type, d.id, d.company_id, d.transaction_number, d.transaction_date, d.party_name, d.party_tax_number, d.doc_ref_no AS reference_no, d.doc_ref_date AS reference_date, d.amount, d.tax_amount, d.total_amount, d.current_status, s.name AS status_name, s.background_color AS status_bg, s.color AS status_color, d.created_by FROM debit_notes d LEFT JOIN status s ON s.name_value = d.current_status WHERE d.is_active = '0' {$whereCompanyDebit} {$whereDateDebit} ";
		$finalSQL = $creditSQL . " UNION ALL " . $debitSQL . " ORDER BY transaction_date DESC, note_type ASC";
		$res = $callConfig->getAllRows($finalSQL);
		$result['debit_credit_note_register'] = $res;
		$result['sql'] = $finalSQL;
		return $result;
	}
	
	function getCashApplicationReport($get){
	    global $callConfig;

	    $from = '';
	    $to = '';
	    $whereDate = '';
	    $whereCompany = '';
	 
	    if (!empty($get['from_date']) && !empty($get['to_date'])) {
	        $from = date('Y-m-d', strtotime($get['from_date']));
	        $to = date('Y-m-d', strtotime($get['to_date']));
	        $whereDate = " AND r.payment_date BETWEEN '{$from}' AND '{$to}'";
	    } elseif (!empty($get['date'])) {
	        $date = date('Y-m-d', strtotime($get['date']));
	        $whereDate = " AND r.payment_date = '{$date}'";
	    }
	 
	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND r.company_id = '" . $get['company_id'] . "'";
	    }
	 
	    $sql = "SELECT r.id AS receipt_id, r.transaction_no AS receipt_no, r.payment_date, si.party_id, si.party_name, si.transaction_number AS invoice_no, si.transaction_date AS invoice_date, r.amount AS receipt_amount, COALESCE(SUM(ra.total_paid_amonut), 0) AS allocated_amount, (r.amount - COALESCE(SUM(ra.total_paid_amonut), 0)) AS unallocated_amount,
	        CASE WHEN COALESCE(SUM(ra.total_paid_amonut), 0) = 0 THEN 'Unallocated' WHEN COALESCE(SUM(ra.total_paid_amonut), 0) = r.amount THEN 'Fully Allocated' ELSE 'Partially Allocated' END AS allocation_status, GROUP_CONCAT(DISTINCT si.transaction_number SEPARATOR ', ') AS allocated_invoices, r.remarks, r.current_status, s.name AS status_name, s.background_color AS status_bg, s.color AS status_color, r.created_by FROM receipts r
	        LEFT JOIN receipt_allocations ra ON ra.receipt_id = r.id AND ra.is_active = '0' LEFT JOIN sales_invoices si ON si.id = ra.transaction_id AND ra.transaction_table = 'sales_invoices'
	        LEFT JOIN status s ON s.name_value = r.current_status WHERE r.is_active = '0' {$whereCompany} {$whereDate} GROUP BY r.id ORDER BY r.payment_date DESC, r.transaction_no ASC
	    ";

	    $reportData = $callConfig->getAllRows($sql);
	 
	    $summarySql = "SELECT COUNT(r.id) AS total_receipts, SUM(r.amount) AS total_receipt_amount, SUM(COALESCE(SUM_RA.total_allocated, 0)) AS total_allocated, SUM(r.amount - COALESCE(SUM_RA.total_allocated, 0)) AS total_unallocated FROM receipts r
	        LEFT JOIN ( SELECT receipt_id, SUM(total_paid_amonut) AS total_allocated FROM receipt_allocations WHERE is_active = '0' GROUP BY receipt_id ) AS SUM_RA ON SUM_RA.receipt_id = r.id WHERE r.is_active = '0' {$whereCompany} {$whereDate} ";

	    $summaryData = $callConfig->getRow($summarySql);

	    $result['cash_application_report'] = $reportData;
	    $result['summary'] = $summaryData;
	    $result['sql'] = $sql;

	    return $result;
	}


	function getTopVendorSpendReport($get){
	    global $callConfig;
	 
	    $fromDate = !empty($get['from_date']) ? date('Y-m-d', strtotime($get['from_date'])) : '1970-01-01';
	    $toDate   = !empty($get['to_date']) ? date('Y-m-d', strtotime($get['to_date'])) : date('Y-m-d');
	 
	    $whereCompany = "";
	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND po.company_id = '" . $get['company_id'] . "' ";
	    }
	 
	    $sql = "
	        SELECT vendor_id, vendor_name, total_pos, total_invoices, total_spend, avg_po_value, ROUND((total_spend / grand_total.total_overall_spend) * 100, 2) AS percent_of_total
	        FROM ( SELECT  po.party_id AS vendor_id, po.party_name AS vendor_name, COUNT(DISTINCT po.id) AS total_pos, COUNT(DISTINCT pi.id) AS total_invoices, ROUND(SUM(COALESCE(pi.total_amount, po.total_amount)), 2) AS total_spend, ROUND(AVG(COALESCE(po.total_amount, 0)), 2) AS avg_po_value
	        FROM purchase_orders po LEFT JOIN purchase_invoices pi ON pi.ref_id = po.id  AND pi.is_active = '0' WHERE  po.is_active = '0' AND po.transaction_date BETWEEN '{$fromDate}' AND '{$toDate}' GROUP BY po.party_id, po.party_name ) AS vendor_summary CROSS JOIN (
	        SELECT  ROUND(SUM(COALESCE(pi2.total_amount, po2.total_amount)), 2) AS total_overall_spend FROM purchase_orders po2
	        LEFT JOIN purchase_invoices pi2 ON pi2.ref_id = po2.id AND pi2.is_active = '0'
	        WHERE po2.is_active = '0'  AND po2.transaction_date BETWEEN '{$fromDate}' AND '{$toDate}' ) AS grand_total ORDER BY total_spend DESC LIMIT 10 ";

	    $reportData = $callConfig->getAllRows($sql);

	    $result['top_vendor_spend_report'] = $reportData;
	    $result['filters'] = [
	        'from_date' => $fromDate,
	        'to_date' => $toDate,
	        'company_filter' => $whereCompany
	    ];
	    $result['sql'] = $sql;

	    return $result;
	}


	function getDiscountAndRebateUtilizationReport($get){
	    global $callConfig;
	 
	    $fromDate = !empty($get['from_date']) ? date('Y-m-d', strtotime($get['from_date'])) : '1970-01-01';
	    $toDate   = !empty($get['to_date']) ? date('Y-m-d', strtotime($get['to_date'])) : date('Y-m-d');
	    $whereCompany = "";

	    if (!empty($get['company_id'])) {
	        $whereCompany = " AND po.company_id = '" . $get['company_id'] . "' ";
	    }
	 
	    $sql = "
	        SELECT po.party_id AS vendor_id, po.party_name AS vendor_name, ROUND(SUM(po.total_amount), 2) AS total_purchase_value,  ROUND(SUM(COALESCE(po.discount_amount, 0)), 2) AS eligible_discount,  ROUND(SUM(COALESCE(pi.discount_amount, 0)), 2) AS utilized_discount,  ROUND(SUM(COALESCE(po.discount_amount, 0)) - SUM(COALESCE(pi.discount_amount, 0)), 2) AS balance_discount, 0 AS eligible_rebate, 0 AS utilized_rebate, 0 AS balance_rebate, 
	        CASE  WHEN SUM(COALESCE(po.discount_amount, 0)) > 0 THEN  ROUND((SUM(COALESCE(pi.discount_amount, 0)) / SUM(COALESCE(po.discount_amount, 0))) * 100, 2) ELSE 0 END AS utilization_percent 
	        FROM purchase_orders po LEFT JOIN purchase_invoices pi  ON pi.ref_id = po.id AND pi.is_active = '0' AND pi.transaction_date BETWEEN '{$fromDate}' AND '{$toDate}' 
	        WHERE  po.is_active = '0' AND po.transaction_date BETWEEN '{$fromDate}' AND '{$toDate}' {$whereCompany} GROUP BY po.party_id, po.party_name ORDER BY total_purchase_value DESC "; 
	    $reportData = $callConfig->getAllRows($sql);

	    $result['discount_rebate_utilization_report'] = $reportData;
	    $result['filters'] = [
	        'from_date' => $fromDate,
	        'to_date' => $toDate,
	        'company_filter' => $whereCompany
	    ];
	    $result['sql'] = $sql;

	    return $result;
	}
	// added by omkar end
	function getBankReconciliations($get){
		global $callConfig;
		$return = array();
		$query = "SELECT id, account_code, bank_account_name, bank_account_number, bank_contact_person, ifsc_code FROM bank_accounts WHERE company_id =".$get['company_id']." AND is_active = 0";
		$data = $callConfig->getAllRows($query);
		$return['bank_accounts']= $data;
		return $return;

	}
}
?>


