<?php
class purchasingUserClass extends userClass{
    function getSubcontractWorkOrders($get){
        global $callConfig;
        $return = array();
        

        $vendorssql = "SELECT id, company, display_name FROM vendors WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $vendorsres=$callConfig->getAllRows($vendorssql);
        $return['vendors'] = $vendorsres;

        $itemssql = "SELECT id, sku, item_name, stocking_type, purchase_price,purchase_uom FROM items WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $itemsres=$callConfig->getAllRows($itemssql);
        $return['items'] = $itemsres;


        $warehousessql = "SELECT id, warehouse_name, warehous_type FROM warehouses WHERE company_id = '".$get['company_id']."' AND is_active =0";
        $warehousesres=$callConfig->getAllRows($warehousessql);

        $warehouseslist = array();
        foreach($warehousesres as $warehs){
            $warehouseslist[$warehs->warehous_type][] = $warehs;
        }
        $return['warehouses'] = $warehouseslist;

        // Get unit of measurements
        $uomsql = "SELECT id, uom_name FROM unit_of_measurements WHERE company_id = '".$get['company_id']."' AND is_active = 0";
        $uomres = $callConfig->getAllRows($uomsql);
        $return['unit_of_measurements'] = $uomres;

        // Get item work type/stocking type mapping
        $itemworktype = array('F' => 'FINISHED GOODS', 'W' => 'WIP', 'R' => 'RAW MATERIAL', 'C' => 'CHILD PART');
        $return['itemworktype'] = $itemworktype;

        // Get existing subcontract work order data if rid is provided
        if($get['rid'] != ""){
            $subcontractsql = "SELECT * FROM subcontract_work_orders WHERE id = '".$get['rid']."' AND company_id = '".$get['company_id']."' AND is_active = 0";
            $subcontractres = $callConfig->getRow($subcontractsql);
            $return['subcontract_work_order'] = $subcontractres;

            $subcontractitemssql = "SELECT * FROM subcontract_work_order_items WHERE transaction_id = '".$get['rid']."' AND company_id = '".$get['company_id']."' AND is_active = 0";
            $subcontractitemsres = $callConfig->getAllRows($subcontractitemssql);
            $return['subcontract_work_order_items'] = $subcontractitemsres;
        }

        return $return;
    }
    function insertSubcontractWorkOrders($get, $post){
        global $callConfig;
        $return = array();
        
        try {
            // Fetch vendor details
            $vendorsql = "SELECT company, display_name, email, phone,  billing_address, shipping_address FROM vendors WHERE id = '".$post['party_id']."' AND company_id = '".$get['company_id']."' AND is_active = 0";
            $vendorres = $callConfig->getRow($vendorsql);
            
            if(!$vendorres){
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '404', 'message' => 'Vendor not found');
                return $return;
            }

            $party_name = $vendorres->company != "" ? $vendorres->company : $vendorres->display_name;
            
            // Prepare parent table data
            $postdata = array(
                'company_id' => $get['company_id'],
                'transaction_date' => $post['transaction_date'] ? date('Y-m-d', strtotime($post['transaction_date'])) : date('Y-m-d'),
                'request_warehouse_id' => $post['request_warehouse_id'],
                'party_id' => $post['party_id'],
                'party_warehouse_id' => $post['party_warehouse_id'],
                'party_name' => $party_name,
                'party_email' => $vendorres->email,
                'party_phone' => $vendorres->phone,
                'party_address' => $vendorres->billing_address,
                'product_id' => $post['product_id'],
                'work_order_item_qty' => $post['work_order_item_qty'],
                'manufacturing_bom_id' => $post['manufacturing_bom_id'],
                'work_order_item_price' => $post['work_order_item_price'],
                'work_order_total_amount' => $post['work_order_total_amount'],
                'ref_date' => date('Y-m-d'),
                'originator' => $get['user_id'],
                'party_ship_adderss' => $vendorres->shipping_address
            );

            // Handle INSERT or UPDATE
            if($post['rid'] != ""){
                // UPDATE mode
                $rid = $post['rid'];
                $postdata['updated_date'] = date('Y-m-d H:i:s');
                $postdata['updated_by'] = $get['user_id'];
                $res = $callConfig->updateRecord('subcontract_work_orders', $postdata, 'id', $rid);
                $return['parent_update'] = $res;
                
                // Soft-delete existing child records
            $upary = array('is_active' => NULL);
                $callConfig->updateRecord('subcontract_work_order_items', $upary, 'transaction_id', $rid);
            } else {
                // INSERT mode
                $get['transaction_date'] = $post['transaction_date'];
                $transaction_number = $this->toGetApplicationSerialNumber('subcontract_work_orders', $get);
                $postdata['transaction_number'] = $transaction_number;
                $postdata['is_active'] = '0';
                $postdata['current_status'] = '0';
                $postdata['created_date'] = date('Y-m-d H:i:s');
                $postdata['created_by'] = $get['user_id'];
                $res = $callConfig->insertRecord('subcontract_work_orders', $postdata);
                $return['parent_insert'] = $res;
                $rid = $res['LASTID'];
            }

            // Handle child items
            $total_amount = 0;
            if($rid != "" && isset($post['item_id']) && is_array($post['item_id'])){
                foreach($post['item_id'] as $seq => $item_id){
                    if($item_id != ""){
                        // Fetch item pricing
                        $itemsql = "SELECT purchase_price,item_name,inventory_asset_account FROM items WHERE id = '".$item_id."' AND company_id = '".$get['company_id']."' AND is_active = 0";
                        $itemres = $callConfig->getRow($itemsql);
                        
                        $item_price = $post['item_price'][$seq] ?? $itemres->purchase_price ?? 0;
                        $item_quantity = $post['request_qty'][$seq] ? $post['request_qty'][$seq] : 0;
                        $item_amount = $post['item_total_amount'][$seq] ??  $item_quantity * $item_price;
                        $total_amount += $item_amount;

                        // Prepare child item data
                        $itempostdata = array(
                            'company_id' => $get['company_id'],
                            'transaction_id' => $rid,
                            'item_id' => $item_id,
                            'purchase_uom' => $post['uom'][$seq],
                            'item_quantity' => $item_quantity,
                            'item_name' => $itemres->item_name,
                            'account_code' => $itemres->inventory_asset_account,
                            'item_price' => $item_price,
                            'item_amount' => $item_amount,
                            'total_amount' => $item_amount,
                            'description' => $post['description'][$seq],
                            'is_active' => 0,
                            'req_date' => date('Y-m-d'),
                            'approved_quantity' => $item_quantity,
                            'transaction_qty' => $item_quantity,
                            'pending_qty' => $item_quantity,
                            
                        );

                        // Check if updating existing item or inserting new
                        if(isset($post['subcontract_work_order_item_id'][$seq]) && $post['subcontract_work_order_item_id'][$seq] != "" && $post['form_updatetion_type'] == "update"){
                            $itempostdata['updated_date'] = date('Y-m-d H:i:s');
                            $itempostdata['updated_by'] = $get['user_id'];
                            $itemres = $callConfig->updateRecord('subcontract_work_order_items', $itempostdata, 'id', $post['subcontract_work_order_item_id'][$seq]);
                            $return['child_items'][] = $itemres;
                        } else {
                            $itempostdata['current_status'] = '0';
                            $itempostdata['created_date'] = date('Y-m-d H:i:s');
                            $itempostdata['created_by'] = $get['user_id'];
                            $itemres = $callConfig->insertRecord('subcontract_work_order_items', $itempostdata);
                            $return['child_items'][] = $itemres;
                        }
                    }
                }
            }

            // Update parent with total amount
            if($rid != ""){
                $amountdata = array('amount' => $total_amount, 'total_amount' => $total_amount);
                $callConfig->updateRecord('subcontract_work_orders', $amountdata, 'id', $rid);
            }

            // Return success response
            $return['parent_insert_update'] = array(
                'status' => 'success',
                'LASTID' => $post['rid'] != "" ? 0 : $rid,
                'message' => 'Record saved successfully'
            );

        } catch (Exception $e) {
            $return['parent_insert_update'] = array(
                'status' => 'fail',
                'error_no' => '500',
                'message' => 'Error: ' . $e->getMessage()
            );
        }

        return $return;
    }

    function getIssueRawMaterialsToVendor($get){
        global $callConfig;
        $return = array();
        // Case A: Dropdown list - no rid provided
            $sql = "SELECT id, transaction_number
                    FROM subcontract_work_orders 
                    WHERE company_id = '".$get['company_id']."' 
                    AND is_active = 0 
                    AND current_status = '".ST_APPROVED."'
                    ORDER BY transaction_date DESC";
            $records = $callConfig->getAllRows($sql);
            $return['subcontract_work_orders_list'] = $records;
            $return['status'] = 'success';
        // Case B: Detailed data - rid provided
        if($get['rid'] != ""){
            $rid = $get['rid'];
            // Fetch parent record with warehouse joins
            $parent_sql = "SELECT swo.*,
                                   rw.warehouse_name as request_warehouse_name,
                                   pw.warehouse_name as party_warehouse_name,
                                   itm.item_name as product_name
                           FROM subcontract_work_orders swo
                           LEFT JOIN warehouses rw ON swo.request_warehouse_id = rw.id AND rw.is_active = 0
                           LEFT JOIN warehouses pw ON swo.party_warehouse_id = pw.id AND pw.is_active = 0
                           LEFT JOIN items itm ON swo.product_id = itm.id AND itm.company_id = '".$get['company_id']."' AND itm.is_active = 0
                           WHERE swo.id = '".$rid."' 
                           AND swo.company_id = '".$get['company_id']."' 
                           AND swo.is_active = 0";
            $parent = $callConfig->getRow($parent_sql);
            
            if(!$parent){
                $return['status'] = 'error';
                $return['message'] = 'Record not found';
                return $return;
            }
            // Fetch child items
            $child_sql = "SELECT 
                            swoi.*,
                            i.sku,
                            i.item_name,
                            u.uom_name,
                            swoi.item_price,
                            (swoi.item_quantity * swoi.item_price) as total_amount
                            FROM subcontract_work_order_items swoi
                            LEFT JOIN items i ON swoi.item_id = i.id AND i.company_id = '".$get['company_id']."' AND i.is_active = 0
                            LEFT JOIN unit_of_measurements u ON swoi.purchase_uom = u.id AND u.company_id = '".$get['company_id']."' AND u.is_active = 0
                            WHERE swoi.transaction_id = '".$rid."' 
                            AND swoi.company_id = '".$get['company_id']."' 
                            AND swoi.is_active = 0
                            ORDER BY swoi.id ASC";
            $child_items = $callConfig->getAllRows($child_sql);

            // Fetch issue history with one query (by work order ref_id).
                 $history_sql = "SELECT scwoi.id,
                            scwoi.transaction_number,
                            scwoi.transaction_date,
                            scwoi.party_name,
                            scwoi.work_order_item_qty,
                            scwoi.work_order_total_amount,
                            scwoi.current_status
                        FROM sub_contract_work_order_issues scwoi
                            WHERE scwoi.company_id = '".$get['company_id']."'
                            AND scwoi.ref_id = '".$rid."'
                            AND scwoi.is_active = 0
                            ORDER BY scwoi.id DESC";
            $issues_history = $callConfig->getAllRows($history_sql);

            // Fetch all child issue items in one query and group by issue transaction_id.
            $issue_history_details = array();
            $history_issue_ids = array();
            foreach((array)$issues_history as $history_row){
                $history_issue_id = is_object($history_row) ? ($history_row->id ?? '') : ($history_row['id'] ?? '');
                if($history_issue_id != ''){
                    $history_issue_ids[] = $history_issue_id;
                }
            }

            if(count($history_issue_ids) > 0){
                $history_issue_ids = array_unique($history_issue_ids);
                $issue_ids_in = "'".implode("','", $history_issue_ids)."'";

                  $history_items_sql = "SELECT scwoii.transaction_id,
                                   scwoii.item_quantity,
                                   scwoii.issued_quantity,
                                   scwoii.issued_amount,
                                             itm.sku,
                                             itm.item_name,
                                   u.uom_name
                                      FROM sub_contract_work_order_issue_items scwoii
                                      LEFT JOIN items itm ON scwoii.item_id = itm.id AND itm.company_id = '".$get['company_id']."' AND itm.is_active = 0
                                      LEFT JOIN unit_of_measurements u ON scwoii.purchase_uom = u.id AND u.company_id = '".$get['company_id']."' AND u.is_active = 0
                                      WHERE scwoii.company_id = '".$get['company_id']."'
                                      AND scwoii.transaction_id IN (".$issue_ids_in.")
                                      AND scwoii.is_active = 0
                                      ORDER BY scwoii.transaction_id DESC, scwoii.id ASC";
                $history_items = $callConfig->getAllRows($history_items_sql);

                foreach((array)$history_items as $history_item_row){
                    $history_transaction_id = is_object($history_item_row) ? ($history_item_row->transaction_id ?? '') : ($history_item_row['transaction_id'] ?? '');
                    if($history_transaction_id == ''){
                        continue;
                    }
                    $issue_history_details[$history_transaction_id][] = $history_item_row;
                }
            }
            
            $return['parent'] = $parent;
            $return['child_items'] = $child_items;
            $return['issues_history'] = $issues_history;
            $return['issue_history_details'] = $issue_history_details;
            $return['status'] = 'success';
        }
        
        return $return;
    }

    function saveIssueRawMaterialsToVendor($get, $post){
        global $callConfig;
        $return = array();

        try {
            $rid = $post['rid'] ?? '';
            $issues_qty = isset($post['issues_qty']) && is_array($post['issues_qty']) ? $post['issues_qty'] : array();
            $child_ids = isset($post['id']) && is_array($post['id']) ? $post['id'] : array();
            $item_ids = isset($post['item_id']) && is_array($post['item_id']) ? $post['item_id'] : array();

            if (!$rid) {
                $return['status'] = 'error';
                $return['message'] = 'Invalid work order ID';
                return $return;
            }

            // 1. Fetch parent record from subcontract_work_orders
            $parent_sql = "SELECT * FROM subcontract_work_orders 
                           WHERE id = '$rid' 
                           AND company_id = '" . $get['company_id'] . "' 
                           AND is_active = 0";
            $parent_record = $callConfig->getRow($parent_sql);

            if (!$parent_record) {
                $return['status'] = 'error';
                $return['message'] = 'Work order not found';
                return $return;
            }

            // 2. Fetch child items from subcontract_work_order_items for selected IDs
            $child_items = array();
            if (count($child_ids) > 0) {
                $child_ids_str = "'" . implode("','", $child_ids) . "'";
                $child_sql = "SELECT * FROM subcontract_work_order_items 
                              WHERE id IN ($child_ids_str) 
                              AND transaction_id = '$rid' 
                              AND company_id = '" . $get['company_id'] . "' 
                              AND is_active = 0";
                $child_items = $callConfig->getAllRows($child_sql);
            }

            if (count($child_items) === 0) {
                $return['status'] = 'error';
                $return['message'] = 'No items selected for issue';
                return $return;
            }

            // 3. Insert parent record into sub_contract_work_order_issues
            $get['transaction_date'] = $parent_record->transaction_date;
            // $transaction_number = '';
            $transaction_number = $this->toGetApplicationSerialNumber('sub_contract_work_order_issues', $get);

            $issue_parent_data = array(
                'company_id' => $get['company_id'],
                'transaction_number' => $transaction_number ?? null,
                'ref_type' => 'subcontract_work_orders',
                'ref_id' => $rid,
                'ref_no' => $parent_record->transaction_number,
                'ref_date' => $parent_record->transaction_date,
                'transaction_date' => $parent_record->transaction_date,
                'request_warehouse_id' => $parent_record->request_warehouse_id,
                'party_id' => $parent_record->party_id,
                'party_warehouse_id' => $parent_record->party_warehouse_id,
                'party_name' => $parent_record->party_name,
                'party_email' => $parent_record->party_email,
                'party_phone' => $parent_record->party_phone,
                'party_address' => $parent_record->party_address,
                'product_id' => $parent_record->product_id,
                'manufacturing_bom_id' => $parent_record->manufacturing_bom_id,
                'work_order_item_price' => $parent_record->work_order_item_price,
                'work_order_item_qty' => $parent_record->work_order_item_qty,
                'originator' => $get['user_id'],
                'party_ship_adderss' => $parent_record->party_ship_adderss,
                'is_active' => ST_ACTIVE,
                'current_status' => ST_APPROVED,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $get['user_id']
            );

            $issue_parent_insert = $callConfig->insertRecord('sub_contract_work_order_issues', $issue_parent_data);
            $issue_parent_id = $issue_parent_insert['LASTID'];

            if (!$issue_parent_id) {
                $return['status'] = 'error';
                $return['message'] = 'Failed to create issue record';
                return $return;
            }

            // 4. Insert child items into sub_contract_work_order_issue_items
            $total_issued_amount = 0;
            $inserted_items = 0;

            foreach ($child_items as $index => $child_item) {
                if (isset($issues_qty[$index]) && $issues_qty[$index] > 0) {
                    $issued_qty = floatval($issues_qty[$index]);
                    $issued_amount = $issued_qty * floatval($child_item->item_price);
                    $total_issued_amount += $issued_amount;

                    $issue_child_data = array(
                        'company_id' => $get['company_id'],
                        'transaction_id' => $issue_parent_id,
                        'ref_transaction_table_id' => $rid,
                        'ref_transaction_item_table_id' => $child_item->id,
                        'item_id' => $child_item->item_id,
                        'purchase_uom' => $child_item->purchase_uom,
                        'item_quantity' => $child_item->item_quantity,
                        'item_name' => $child_item->item_name,
                        'account_code' => $child_item->account_code,
                        'item_price' => $child_item->item_price,
                        'issued_quantity' => $issued_qty,
                        'issued_amount' => $issued_amount,
                        'description' => $child_item->description,
                        'is_active' => 0,
                        'current_status' => ST_APPROVED,
                        'created_date' => date('Y-m-d H:i:s'),
                        'created_by' => $get['user_id']
                    );

                    $issue_child_insert = $callConfig->insertRecord('sub_contract_work_order_issue_items', $issue_child_data);
                    if ($issue_child_insert['status'] === 'success' || isset($issue_child_insert['LASTID'])) {
                        $inserted_items++;
                    }
                }
            }

            // 5. Update parent issue record with total issued amount and item count
            if ($inserted_items > 0) {
                $update_data = array(
                    // 'work_order_item_qty' => $inserted_items,
                    'work_order_total_amount' => $total_issued_amount
                );
                $callConfig->updateRecord('sub_contract_work_order_issues', $update_data, 'id', $issue_parent_id);
            }

            $return['status'] = 'success';
            $return['message'] = 'Raw materials issued successfully';
            $return['issue_id'] = $issue_parent_id;
            $return['items_issued'] = $inserted_items;
            $return['total_amount'] = $total_issued_amount;

            $this->updateSubcontractWorkOrderPendingQty(array(
                'transaction_id' => $issue_parent_id,
                'company_id' => $get['company_id']
            ));

        } catch (Exception $e) {
            $return['status'] = 'error';
            $return['message'] = 'Error: ' . $e->getMessage();
        }

        return $return;
    }
    function getSubContractWorkOrderIssues($get){
        global $callConfig;
        $return = array();

        if($get['rid'] != ""){
            $rid = $get['rid'];
            $parent_sql = "SELECT scwoi.*, 
                                  swo.transaction_number as subcontract_work_order_number,
                                  rw.warehouse_name as request_warehouse_name,
                                  pw.warehouse_name as party_warehouse_name,
                                  i.item_name as product_name,
                                  i.sku as product_sku
                           FROM sub_contract_work_order_issues scwoi
                           LEFT JOIN subcontract_work_orders swo ON scwoi.ref_id = swo.id AND swo.company_id = '".$get['company_id']."' AND swo.is_active = 0
                           LEFT JOIN warehouses rw ON scwoi.request_warehouse_id = rw.id AND rw.company_id = '".$get['company_id']."' AND rw.is_active = 0
                           LEFT JOIN warehouses pw ON scwoi.party_warehouse_id = pw.id AND pw.company_id = '".$get['company_id']."' AND pw.is_active = 0
                           LEFT JOIN items i ON scwoi.product_id = i.id AND i.company_id = '".$get['company_id']."' AND i.is_active = 0
                           WHERE scwoi.id = '".$rid."'
                           AND scwoi.company_id = '".$get['company_id']."'
                           AND scwoi.is_active = 0";
            $parent = $callConfig->getRow($parent_sql);

            if(!$parent){
                $return['status'] = 'error';
                $return['message'] = 'Issue record not found';
                return $return;
            }

            $child_sql = "SELECT scwoii.*, 
                                 itm.sku,
                                 itm.item_name,
                                 u.uom_name,
                                 (scwoii.issued_quantity * scwoii.item_price) as total_amount
                          FROM sub_contract_work_order_issue_items scwoii
                          LEFT JOIN items itm ON scwoii.item_id = itm.id AND itm.company_id = '".$get['company_id']."' AND itm.is_active = 0
                          LEFT JOIN unit_of_measurements u ON scwoii.purchase_uom = u.id AND u.company_id = '".$get['company_id']."' AND u.is_active = 0
                          WHERE scwoii.transaction_id = '".$rid."'
                          AND scwoii.company_id = '".$get['company_id']."'
                          AND scwoii.is_active = 0
                          ORDER BY scwoii.id ASC";
            $child_items = $callConfig->getAllRows($child_sql);

            $return['parent'] = $parent;
            $return['child_items'] = $child_items;
            $return['status'] = 'success';
            return $return;
        }

        $list_sql = "SELECT scwoi.id,
                            scwoi.transaction_number,
                            scwoi.ref_no,
                            scwoi.ref_date,
                            scwoi.transaction_date,
                            scwoi.party_name,
                            scwoi.work_order_item_qty,
                            scwoi.work_order_total_amount,
                            scwoi.current_status,
                            rw.warehouse_name as request_warehouse_name,
                            pw.warehouse_name as party_warehouse_name,
                            swo.transaction_number as subcontract_work_order_number,
                            itm.item_name as product_name
                     FROM sub_contract_work_order_issues scwoi
                     LEFT JOIN subcontract_work_orders swo ON scwoi.ref_id = swo.id AND swo.company_id = '".$get['company_id']."' AND swo.is_active = 0
                     LEFT JOIN warehouses rw ON scwoi.request_warehouse_id = rw.id AND rw.company_id = '".$get['company_id']."' AND rw.is_active = 0
                     LEFT JOIN warehouses pw ON scwoi.party_warehouse_id = pw.id AND pw.company_id = '".$get['company_id']."' AND pw.is_active = 0
                     LEFT JOIN items itm ON scwoi.product_id = itm.id AND itm.company_id = '".$get['company_id']."' AND itm.is_active = 0
                     WHERE scwoi.company_id = '".$get['company_id']."'
                     AND scwoi.is_active = 0
                     ORDER BY scwoi.id DESC";
        $issues_list = $callConfig->getAllRows($list_sql);

        $return['issues_list'] = $issues_list;
        $return['status'] = 'success';
        return $return;
    }
    
    function updateSubcontractWorkOrderPendingQty($get){
        global $callConfig;
        $return = array();

        try {
            $transaction_id = $get['transaction_id'] ?? '';
            if($transaction_id == ''){
                $return['status'] = 'error';
                $return['message'] = 'Invalid transaction_id';
                return $return;
            }

            $issue_parent_sql = "SELECT id, ref_id
                                 FROM sub_contract_work_order_issues
                                 WHERE id = '".$transaction_id."'
                                 AND company_id = '".$get['company_id']."'
                                 AND is_active = 0";
            $issue_parent = $callConfig->getRow($issue_parent_sql);

            if(!$issue_parent){
                $return['status'] = 'error';
                $return['message'] = 'Issue transaction not found';
                return $return;
            }

            $issue_items_sql = "SELECT id, ref_transaction_table_id, ref_transaction_item_table_id, issued_quantity
                                FROM sub_contract_work_order_issue_items
                                WHERE transaction_id = '".$transaction_id."'
                                AND company_id = '".$get['company_id']."'
                                AND is_active = 0";
            $issue_items = $callConfig->getAllRows($issue_items_sql);

            if(count((array)$issue_items) == 0){
                $return['status'] = 'error';
                $return['message'] = 'No issue items found for this transaction';
                return $return;
            }

            $updated_count = 0;
            foreach($issue_items as $issue_item){
                $source_transaction_id = $issue_item->ref_transaction_table_id;
                $source_item_id = $issue_item->ref_transaction_item_table_id;
                $issued_qty = (float)$issue_item->issued_quantity;

                if($source_item_id == '' || $issued_qty <= 0){
                    continue;
                }

                $source_child_sql = "SELECT id, pending_qty
                                     FROM subcontract_work_order_items
                                     WHERE id = '".$source_item_id."'
                                     AND transaction_id = '".$source_transaction_id."'
                                     AND company_id = '".$get['company_id']."'
                                     AND is_active = 0";
                $source_child = $callConfig->getRow($source_child_sql);

                if(!$source_child){
                    continue;
                }

                $current_pending = (float)$source_child->pending_qty;
                $new_pending = $current_pending - $issued_qty;
                if($new_pending < 0){
                    $new_pending = 0;
                }

                $up_data = array(
                    'pending_qty' => $new_pending,
                    'updated_date' => date('Y-m-d H:i:s'),
                    'updated_by' => $get['user_id']
                );
                $callConfig->updateRecord('subcontract_work_order_items', $up_data, 'id', $source_item_id);
                $updated_count++;
            }

            $return['status'] = 'success';
            $return['message'] = 'Pending qty updated successfully';
            $return['transaction_id'] = $transaction_id;
            $return['updated_items'] = $updated_count;

        } catch (Exception $e) {
            $return['status'] = 'error';
            $return['message'] = 'Error: ' . $e->getMessage();
        }

        return $return;
    }

    function getSubcontractWorkOrderReceiptMetrics($company_id, $work_order_id, $exclude_receipt_id = ''){
        global $callConfig;

        $receipt_where = "company_id = '".$company_id."' AND ref_id = '".$work_order_id."' AND is_active = 0 AND (current_status IS NULL OR current_status NOT IN ('".ST_REJECTED."', '".ST_CANCEL."', '".ST_INACTIVE."'))";
        if($exclude_receipt_id != ''){
            $receipt_where .= " AND id != '".$exclude_receipt_id."'";
        }

        $receipt_qty_sql = "SELECT COALESCE(SUM(received_qty), 0) as total_received_qty FROM subcontract_work_order_receipts WHERE ".$receipt_where;
        $receipt_qty_res = $callConfig->getRow($receipt_qty_sql);
        $total_received_qty = isset($receipt_qty_res->total_received_qty) ? (float)$receipt_qty_res->total_received_qty : 0;

        return array(
            'total_received_qty' => $total_received_qty
        );
    }

    function getSubcontractReceiptWorkOrderDetails($company_id, $work_order_id, $exclude_receipt_id = ''){
        global $callConfig;
        
        $work_order_sql = "SELECT swo.id, swo.transaction_number, swo.transaction_date, swo.product_id, swo.party_id, swo.party_name, swo.request_warehouse_id, swo.party_warehouse_id, swo.work_order_item_qty, swo.work_order_item_price, swo.work_order_total_amount  as item_total_amount, ven.company as vendor_company, ven.display_name as vendor_display_name, ven.email as vendor_email, ven.phone as vendor_phone, ven.billing_address, ven.shipping_address, ven.billing_state, ven.shipping_state, ven.gstin, ven.pan_no, ownw.warehouse_name as requested_warehouse_name, venw.warehouse_name as vendor_warehouse_name, itm.item_name as product_name, itm.sku as product_sku, itm.purchase_uom as product_uom, uom.uom_name as product_uom_name
                           FROM subcontract_work_orders swo
                           LEFT JOIN vendors ven ON swo.party_id = ven.id AND ven.company_id = swo.company_id AND ven.is_active = 0
                           LEFT JOIN warehouses ownw ON swo.request_warehouse_id = ownw.id AND ownw.company_id = swo.company_id AND ownw.is_active = 0
                           LEFT JOIN warehouses venw ON swo.party_warehouse_id = venw.id AND venw.company_id = swo.company_id AND venw.is_active = 0
                           LEFT JOIN items itm ON swo.product_id = itm.id AND itm.company_id = swo.company_id AND itm.is_active = 0
                           LEFT JOIN unit_of_measurements uom ON itm.purchase_uom = uom.id AND uom.company_id = swo.company_id AND uom.is_active = 0
                           WHERE swo.id = '".$work_order_id."' AND swo.company_id = '".$company_id."' AND swo.is_active = 0";
        $work_order_res = $callConfig->getRow($work_order_sql);

        if(isset($work_order_res->id)){
            $metrics = $this->getSubcontractWorkOrderReceiptMetrics($company_id, $work_order_id, $exclude_receipt_id);
            $total_received_qty = isset($metrics['total_received_qty']) ? (float)$metrics['total_received_qty'] : 0;
            $work_order_qty = isset($work_order_res->work_order_item_qty) ? (float)$work_order_res->work_order_item_qty : 0;
            $pending_receipt_qty = $work_order_qty - $total_received_qty;
            if($pending_receipt_qty < 0){
                $pending_receipt_qty = 0;
            }

            $work_order_res->total_received_qty = $total_received_qty;
            $work_order_res->pending_receipt_qty = $pending_receipt_qty;
        }

        return $work_order_res;
    }

    function getSubcontractWorkOrderReceipts($get){
        global $callConfig;

        $return = array();
        $return['status'] = 'success';
        $return['subcontract_work_orders_list'] = array();
        

        $dropdown_sql = "SELECT swo.id, swo.transaction_number
                         FROM subcontract_work_orders swo
                         WHERE swo.company_id = '".$get['company_id']."'
                         AND swo.is_active = 0
                         AND swo.current_status = '".ST_APPROVED."'
                         AND swo.work_order_item_qty > COALESCE((SELECT SUM(swr.received_qty)
                                                                 FROM subcontract_work_order_receipts swr
                                                                 WHERE swr.company_id = swo.company_id
                                                                 AND swr.ref_id = swo.id
                                                                 AND swr.is_active = 0
                                                                 AND (swr.current_status IS NULL OR swr.current_status NOT IN ('".ST_REJECTED."', '".ST_CANCEL."', '".ST_INACTIVE."'))), 0)
                         ORDER BY swo.transaction_date DESC, swo.id DESC";
        $dropdown_rows = $callConfig->getAllRows($dropdown_sql);
                                                        $return['subcontract_work_orders_list'] = (array)$dropdown_rows;
        
        $selected_work_order_id = isset($get['work_order_id']) ? $get['work_order_id'] : '';

        if($selected_work_order_id != ''){
            $return['work_order_details'] = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $selected_work_order_id, '');
        }

        return $return;
    }
    function calculateSubcontractWorkOrderReceiptTaxDetails($get, $work_order_id, $received_qty){
        global $callConfig;

        $return = array(
            'status' => 'success',
            'amount' => 0,
            'tax_amount' => 0,
            'total_amount' => 0,
            'tax_type' => 'I',
            'gst_percentage' => 0,
            'hsn_code_id' => 0,
            'hsn_code' => '',
            'tax_split_details' => array()
        );

        $work_order_details = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $work_order_id, '');
        if(count((array)$work_order_details) == 0){
            $return['status'] = 'fail';
            $return['message'] = 'Subcontract work order not found';
            return $return;
        }

        $item_hsn_sql = "SELECT hsn.id as hsn_id, hsn.code, hsn.gst FROM items itm LEFT JOIN hsn_and_sac_codes hsn ON itm.hsn_and_sac_code_id = hsn.id AND hsn.company_id = itm.company_id AND hsn.is_active = 0 WHERE itm.id = '".$work_order_details->product_id."' AND itm.company_id = '".$get['company_id']."' AND itm.is_active = 0";
        $item_hsn_res = $callConfig->getRow($item_hsn_sql);
        $gst_percentage = ($item_hsn_res && isset($item_hsn_res->gst) && $item_hsn_res->gst != '') ? (float)$item_hsn_res->gst : 0;
        $hsn_code_id = ($item_hsn_res && isset($item_hsn_res->hsn_id) && $item_hsn_res->hsn_id != '') ? (int)$item_hsn_res->hsn_id : 0;
        $hsn_code = ($item_hsn_res && isset($item_hsn_res->code) && $item_hsn_res->code != '') ? $item_hsn_res->code : '';
        $tax_type = 'I';

        $item_price = isset($work_order_details->work_order_item_price) ? (float)$work_order_details->work_order_item_price : 0;
        $amount = round($received_qty * $item_price, 2);
        $tax_amount = round(($amount * $gst_percentage) / 100, 2);
        $total_amount = round($amount + $tax_amount, 2);

        $total_tax_split_details = array();
        if($gst_percentage > 0){
            if($tax_type == 'S'){
                $total_tax_split_details[] = array(
                    'tax_name' => 'SGST',
                    'tax_percentage' => round($gst_percentage * 0.5, 2),
                    'tax_amount' => round($tax_amount * 0.5, 2),
                    'account_code' => ''
                );
                $total_tax_split_details[] = array(
                    'tax_name' => 'CGST',
                    'tax_percentage' => round($gst_percentage * 0.5, 2),
                    'tax_amount' => round($tax_amount * 0.5, 2),
                    'account_code' => ''
                );
            } else {
                $total_tax_split_details[] = array(
                    'tax_name' => 'IGST',
                    'tax_percentage' => round($gst_percentage, 2),
                    'tax_amount' => round($tax_amount, 2),
                    'account_code' => ''
                );
            }
        }

        $return['amount'] = $amount;
        $return['tax_amount'] = $tax_amount;
        $return['total_amount'] = $total_amount;
        $return['tax_type'] = $tax_type;
        $return['gst_percentage'] = $gst_percentage;
        $return['hsn_code_id'] = $hsn_code_id;
        $return['hsn_code'] = $hsn_code;
        $return['tax_split_details'] = $total_tax_split_details;

        return $return;
    }

    function getSubcontractWorkOrderReceiptTaxPreview($get, $post){
        $return = array();
        $work_order_id = isset($post['subcontract_work_order_id']) ? $post['subcontract_work_order_id'] : '';
        $received_qty = isset($post['received_qty']) ? (float)$post['received_qty'] : 0;

        if($work_order_id == ''){
            $return['status'] = 'fail';
            $return['message'] = 'Please select a subcontract work order';
            return $return;
        }

        if($received_qty <= 0){
            $return['status'] = 'fail';
            $return['message'] = 'Received qty should be greater than zero';
            return $return;
        }

        return $this->calculateSubcontractWorkOrderReceiptTaxDetails($get, $work_order_id, $received_qty);
    }
    function saveSubcontractWorkOrderReceipt($get, $post){
        global $callConfig;

        $return = array();

        try {
            $work_order_id = isset($post['subcontract_work_order_id']) ? $post['subcontract_work_order_id'] : '';
            $received_qty = isset($post['received_qty']) ? (float)$post['received_qty'] : 0;

            if($work_order_id == ''){
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '400', 'message' => 'Please select a subcontract work order');
                return $return;
            }

            if($received_qty <= 0){
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '400', 'message' => 'Received qty should be greater than zero');
                return $return;
            }

            $work_order_details = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $work_order_id, '');
            if(count((array)$work_order_details) == 0){
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '404', 'message' => 'Subcontract work order not found');
                return $return;
            }

            $metrics = $this->getSubcontractWorkOrderReceiptMetrics($get['company_id'], $work_order_id, '');
            $total_received_qty = isset($metrics['total_received_qty']) ? (float)$metrics['total_received_qty'] : 0;
            $work_order_item_qty = isset($work_order_details->work_order_item_qty) ? (float)$work_order_details->work_order_item_qty : 0;
            $allowed_pending_qty = $work_order_item_qty - $total_received_qty;
            if($allowed_pending_qty < 0){
                $allowed_pending_qty = 0;
            }
            if($received_qty > $allowed_pending_qty){
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '400', 'message' => 'Received qty cannot be greater than pending receipt qty');
                return $return;
            }

            $transaction_date = isset($post['transaction_date']) && $post['transaction_date'] != '' ? date('Y-m-d', strtotime($post['transaction_date'])) : date('Y-m-d');
            $gst_percentage = isset($post['receipt_gst_percentage']) ? (float)$post['receipt_gst_percentage'] : 0;
            $hsn_code_id = isset($post['receipt_hsn_code_id']) ? (int)$post['receipt_hsn_code_id'] : 0;
            $hsn_code = isset($post['receipt_hsn_code']) ? $post['receipt_hsn_code'] : '';
            $tax_type = isset($post['receipt_tax_type']) && $post['receipt_tax_type'] != '' ? $post['receipt_tax_type'] : 'I';
            $amount = isset($post['receipt_amount']) ? (float)$post['receipt_amount'] : 0;
            $tax_amount = isset($post['receipt_tax_amount']) ? (float)$post['receipt_tax_amount'] : 0;
            $total_amount = isset($post['receipt_total_amount']) ? (float)$post['receipt_total_amount'] : 0;
            $pending_qty = round(($work_order_item_qty - $total_received_qty) - $received_qty, 2);
            if($pending_qty < 0){
                $pending_qty = 0;
            }
            $total_tax_split_details = array();
            if(isset($post['receipt_tax_split_details']) && $post['receipt_tax_split_details'] != ''){
                $decoded_tax_split_details = json_decode($post['receipt_tax_split_details'], true);
                if(is_array($decoded_tax_split_details)){
                    $total_tax_split_details = $decoded_tax_split_details;
                }
            }

            $party_additional_details = json_encode(array(
                'gstin' => $work_order_details->gstin,
                'pan_no' => $work_order_details->pan_no
            ));

            $party_name = $work_order_details->party_name != '' ? $work_order_details->party_name : ($work_order_details->vendor_company != '' ? $work_order_details->vendor_company : $work_order_details->vendor_display_name);

            $postdata = array(
                'company_id' => $get['company_id'],
                'transaction_date' => $transaction_date,
                'transaction_warehouse_id' => isset($post['transaction_warehouse_id']) && $post['transaction_warehouse_id'] != '' ? $post['transaction_warehouse_id'] : $work_order_details->request_warehouse_id,
                'request_warehouse_id' => $work_order_details->party_warehouse_id,
                'originator' => $get['user_id'],
                'party_id' => $work_order_details->party_id,
                'party_name' => $party_name,
                'party_email' => $work_order_details->vendor_email,
                'party_tax_number' => $work_order_details->gstin,
                'party_phone' => $work_order_details->vendor_phone,
                'party_address' => $work_order_details->billing_address,
                'party_ship_adderss' => $work_order_details->shipping_address,
                'notes' => isset($post['notes']) ? $post['notes'] : '',
                'amount' => $amount,
                'tax_amount' => $tax_amount,
                'tax_type' => $tax_type,
                'discount_type' => 'P',
                'discount_value' => 0,
                'discount_amount' => 0,
                'total_amount' => $total_amount,
                'pending_amount' => $total_amount,
                'tax_state' => $tax_type,
                'opex_cpex' => 0,
                'ref_type' => 'subcontract_work_orders',
                'ref_id' => $work_order_details->id,
                'ref_no' => $work_order_details->transaction_number,
                'ref_date' => $work_order_details->transaction_date,
                'received_qty' => $received_qty,
                'pending_qty' => $pending_qty,
                'additional_charges' => json_encode(array()),
                'party_additional_details' => $party_additional_details,
                'total_tax_split_details' => json_encode($total_tax_split_details),
                'discount_mode' => 'DO',
                'multi_reference' => 'N'
            );

            $get['transaction_date'] = $transaction_date;
            $transaction_number = $this->toGetApplicationSerialNumber('subcontract_work_order_receipts', $get);
            $postdata['transaction_number'] = $transaction_number;
            $postdata['is_active'] = 0;
            $postdata['current_status'] = ST_APPROVED;
            $postdata['created_date'] = date('Y-m-d H:i:s');
            $postdata['created_by'] = $get['user_id'];
            $insert_res = $callConfig->insertRecord('subcontract_work_order_receipts', $postdata);
            $rid = isset($insert_res['LASTID']) ? $insert_res['LASTID'] : 0;

            if($rid == 0){
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '500', 'message' => 'Unable to save receipt');
                return $return;
            }

            $item_account_code = '';
            $item_account_sql = "SELECT inventory_asset_account FROM items WHERE id = '".$work_order_details->product_id."' AND company_id = '".$get['company_id']."' AND is_active = 0";
            $item_account_res = $callConfig->getRow($item_account_sql);
            if($item_account_res && isset($item_account_res->inventory_asset_account)){
                $item_account_code = $item_account_res->inventory_asset_account;
            }

            $receipt_item_data = array(
                'company_id' => $get['company_id'],
                'transaction_id' => $rid,
                'item_id' => $work_order_details->product_id,
                'account_code' => $item_account_code,
                'item_name' => isset($work_order_details->product_name) ? $work_order_details->product_name : '',
                'description' => isset($post['notes']) ? $post['notes'] : '',
                'purchase_uom' => isset($work_order_details->product_uom) ? $work_order_details->product_uom : '',
                'invoice_item_quantity' => $received_qty,
                'item_quantity' => $received_qty,
                'item_price' => isset($work_order_details->work_order_item_price) ? (float)$work_order_details->work_order_item_price : 0,
                'item_amount' => $amount,
                'tax_amount' => $tax_amount,
                'discount_amount' => 0,
                'total_amount' => $total_amount,
                'doc_ref_type' => 'subcontract_work_orders',
                'doc_ref_id' => $work_order_details->id,
                'doc_ref_no' => $work_order_details->transaction_number,
                'doc_ref_date' => $work_order_details->transaction_date,
                'approved_quantity' => $received_qty,
                'transaction_qty' => $received_qty,
                'pending_qty' => $received_qty,
                'ref_transaction_item_table_id' => 0,
                'ref_transaction_table_id' => $work_order_details->id,
                'is_active' => 0,
                'current_status' => ST_APPROVED,
                'created_date' => date('Y-m-d H:i:s'),
                'created_by' => $get['user_id']
            );
            $receipt_item_insert = $callConfig->insertRecord('subcontract_work_order_receipt_items', $receipt_item_data);
            $receipt_item_id = isset($receipt_item_insert['LASTID']) ? $receipt_item_insert['LASTID'] : 0;

            if($receipt_item_id == 0){
                $return['parent_insert_update'] = array('status' => 'fail', 'error_no' => '500', 'message' => 'Unable to save receipt item');
                return $return;
            }

            if($gst_percentage > 0){
                $taxfield = array(
                    'company_id' => $get['company_id'],
                    'transaction_id' => $rid,
                    'transaction_item_id' => $work_order_details->product_id,
                    'tax_name' => $hsn_code,
                    'tax_value' => $gst_percentage,
                    'tax_id' => $hsn_code_id,
                    'amount' => $amount,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $amount,
                    'tax_type' => $tax_type,
                    'tax_split_details' => json_encode($total_tax_split_details),
                    'transaction_item_row_id' => $receipt_item_id,
                    'is_active' => 0,
                    'current_status' => 0,
                    'created_date' => date('Y-m-d H:i:s'),
                    'created_by' => $get['user_id']
                );
                $callConfig->insertRecord('subcontract_work_order_receipt_item_taxes', $taxfield);
            }
            
            $return['parent_insert_update'] = array(
                'status' => 'success',
                'LASTID' => isset($insert_res['LASTID']) ? $insert_res['LASTID'] : 0,
                'message' => 'Receipt saved successfully'
            );
            // Update close flag only when pending qty becomes non-positive.
            if($pending_qty <= 0){
                $work_order_update = array(
                    'work_order_closed' => 'Y'
                );
                $callConfig->updateRecord('subcontract_work_orders', $work_order_update, 'id' ,$work_order_details->id);
            }
        } catch (Exception $e) {
            $return['parent_insert_update'] = array(
                'status' => 'fail',
                'error_no' => '500',
                'message' => 'Error: ' . $e->getMessage()
            );
        }

        return $return;
    }

    function getSubcontractWorkOrderReceiptHistory($get){
        global $callConfig;

        $return = array();
        $work_order_id = isset($get['work_order_id']) ? $get['work_order_id'] : '';

        if($work_order_id == ''){
            $return['status'] = 'fail';
            $return['message'] = 'Please select a subcontract work order';
            return $return;
        }

        $work_order_details = $this->getSubcontractReceiptWorkOrderDetails($get['company_id'], $work_order_id, '');
        if(count((array)$work_order_details) == 0){
            $return['status'] = 'fail';
            $return['message'] = 'Subcontract work order not found';
            return $return;
        }

        $history_sql = "SELECT id, transaction_number, transaction_date, received_qty, pending_qty, amount, tax_amount, total_amount, tax_type, total_tax_split_details, notes, created_date
                        FROM subcontract_work_order_receipts
                        WHERE company_id = '".$get['company_id']."'
                        AND ref_id = '".$work_order_id."'
                        AND is_active = 0
                        AND (current_status IS NULL OR current_status NOT IN ('".ST_REJECTED."', '".ST_CANCEL."', '".ST_INACTIVE."'))
                        ORDER BY transaction_date ASC, id ASC";
        $history_rows = $callConfig->getAllRows($history_sql);
        $history = array();

        foreach((array)$history_rows as $row){
            $tax_split_details = array();
            if(isset($row->total_tax_split_details) && $row->total_tax_split_details != ''){
                $decoded_tax_split_details = json_decode($row->total_tax_split_details, true);
                if(is_array($decoded_tax_split_details)){
                    $tax_split_details = $decoded_tax_split_details;
                }
            }

            if(count($tax_split_details) == 0){
                $tax_sql = "SELECT tax_name, tax_value as tax_percentage, tax_amount
                            FROM subcontract_work_order_receipt_item_taxes
                            WHERE company_id = '".$get['company_id']."'
                            AND transaction_id = '".$row->id."'
                            AND is_active = 0
                            ORDER BY id ASC";
                $tax_rows = $callConfig->getAllRows($tax_sql);
                foreach((array)$tax_rows as $tax_row){
                    $tax_split_details[] = array(
                        'tax_name' => isset($tax_row->tax_name) ? $tax_row->tax_name : '',
                        'tax_percentage' => isset($tax_row->tax_percentage) ? (float)$tax_row->tax_percentage : 0,
                        'tax_amount' => isset($tax_row->tax_amount) ? (float)$tax_row->tax_amount : 0
                    );
                }
            }

            $history[] = array(
                'id' => $row->id,
                'transaction_number' => $row->transaction_number,
                'transaction_date' => $row->transaction_date,
                'received_qty' => isset($row->received_qty) ? (float)$row->received_qty : 0,
                'pending_qty' => isset($row->pending_qty) ? (float)$row->pending_qty : 0,
                'amount' => isset($row->amount) ? (float)$row->amount : 0,
                'tax_amount' => isset($row->tax_amount) ? (float)$row->tax_amount : 0,
                'total_amount' => isset($row->total_amount) ? (float)$row->total_amount : 0,
                'tax_type' => isset($row->tax_type) ? $row->tax_type : '',
                'tax_split_details' => $tax_split_details,
                'notes' => isset($row->notes) ? $row->notes : '',
                'created_date' => isset($row->created_date) ? $row->created_date : ''
            );
        }

        $return['status'] = 'success';
        $return['work_order'] = array(
            'transaction_number' => $work_order_details->transaction_number,
            'work_order_item_qty' => isset($work_order_details->work_order_item_qty) ? (float)$work_order_details->work_order_item_qty : 0,
            'total_received_qty' => isset($work_order_details->total_received_qty) ? (float)$work_order_details->total_received_qty : 0,
            'pending_receipt_qty' => isset($work_order_details->pending_receipt_qty) ? (float)$work_order_details->pending_receipt_qty : 0,
            'product_name' => isset($work_order_details->product_name) ? $work_order_details->product_name : '',
            'product_sku' => isset($work_order_details->product_sku) ? $work_order_details->product_sku : '',
            'party_name' => isset($work_order_details->party_name) ? $work_order_details->party_name : ''
        );
        $return['history'] = $history;

        return $return;
    }

}
?>