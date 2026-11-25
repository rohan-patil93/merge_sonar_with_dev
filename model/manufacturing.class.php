<?php
interface ManufacturingConstants
{
    public const AND_IS_ACTIVE_0 = " AND is_active = 0";
    public const SQL_OP_COMPANY_ID = "SELECT * FROM manufacturing_operations WHERE company_id =";
    public const AND_ID = " AND id = ";
    public const SQL_BOM_COMPANY_ID = "SELECT * FROM manufacturing_boms WHERE company_id =";
    public const AND_MANUFACTURING_BOM_ID = " AND manufacturing_bom_id = ";
    public const SQL_BOM_OP_COMPANY_ID = "SELECT * FROM manufacturing_bom_operations WHERE company_id =";
    public const DATE_FORMAT_HIS = "Y-m-d H:i:s";
    public const AND_MB_BOM_ITEM_ID = " AND mb.bom_item_id = '";
    public const AND_MB_IS_ACTIVE_0 = "' AND mb.is_active = 0";
    public const AND_IS_ACTIVE_0_NOSPACE = "' AND is_active=0";
    public const AND_ID_QUOTE = "' AND id='";
    public const SQL_ITEMS_STOCKING_TYPE = "SELECT id, stocking_type FROM items WHERE company_id='";
    public const WHERE_COMPANY_ID = " WHERE company_id=";
    public const SQL_SEL_BOM_WITH_UOM = "SELECT mb.id, mb.bom_item_id, mb.bom_quantity, mb.transaction_date, mb.bom_amount, uoms.uom_name FROM manufacturing_boms mb LEFT JOIN unit_of_measurements uoms ON mb.company_id = uoms.company_id AND mb.bom_uom = uoms.id AND mb.is_active = uoms.is_active WHERE mb.company_id =";
    public const LIMIT_1 = " LIMIT 1";
}

trait ManufacturingBomTrait
{
    private function getBomDefaultLookups(array $get): array
    {
        global $callConfig;

        $return = array();
        $companyId = $get['company_id'];

        $itemsql = "SELECT id, item_name, sku, sale_uom, stocking_type FROM items WHERE company_id =" . $companyId . self::AND_IS_ACTIVE_0;
        $return['items'] = $callConfig->getAllRows($itemsql);

        $dessql = "SELECT id, uom_name FROM unit_of_measurements WHERE company_id =" . $companyId . self::AND_IS_ACTIVE_0;
        $return['unit_of_measurements'] = $callConfig->getAllRows($dessql);

        $dessql = "SELECT id, display_name, company FROM vendors WHERE company_id =" . $companyId . self::AND_IS_ACTIVE_0;
        $return['vendors'] = $callConfig->getAllRows($dessql);

        $locsql = "SELECT id, warehouse_name FROM warehouses WHERE company_id =" . $companyId . self::AND_IS_ACTIVE_0;
        $return['warehouses'] = $callConfig->getAllRows($locsql);

        $locsql = "SELECT id, operation_name, parent_id FROM manufacturing_operations WHERE company_id =" . $companyId . self::AND_IS_ACTIVE_0;
        $return['manufacturing_operations'] = $callConfig->getAllRows($locsql);

        $itemworktypesql = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '" . $companyId . "' AND genc_code ='ITEMWORKTYPE' AND is_active ='0' ORDER BY genc_detail_name ASC";
        $itemworktyperes = $callConfig->getAllRows($itemworktypesql);
        $itemworktype = array();
        foreach ($itemworktyperes as $gen) {
            $itemworktype[$gen->genc_detail_code] = $gen->genc_detail_name;
        }
        $return['itemworktype'] = $itemworktype;

        if (!empty($get['rid'])) {
            $rid = $get['rid'];
            $locsql = self::SQL_BOM_COMPANY_ID . $companyId . self::AND_ID . $rid . self::AND_IS_ACTIVE_0;
            $return['manufacturing_boms'] = $callConfig->getRow($locsql);

            $locsql = "SELECT * FROM manufacturing_bom_items WHERE company_id =" . $companyId . self::AND_MANUFACTURING_BOM_ID . $rid . self::AND_IS_ACTIVE_0;
            $locres = $callConfig->getAllRows($locsql);
            $bom_item_id = array();
            foreach ($locres as $itmbom) {
                $bom_item_id[$itmbom->item_id] = $itmbom->item_id;
            }
            $return['manufacturing_bom_items'] = $locres;

            if (!empty($bom_item_id)) {
                $alteritmsql = "SELECT ai.item_id, itm.id, itm.sku, itm.item_name, itm.stocking_type, itm.item_name FROM alternate_items ai, items itm WHERE ai.company_id = " . $companyId . " AND ai.item_id IN(" . implode(',', (array) $bom_item_id) . ") AND ai.company_id = itm.company_id AND ai.alternate_item_id = itm.id AND ai.is_active = itm.is_active" . self::AND_IS_ACTIVE_0;
                $alteritmres = $callConfig->getAllRows($alteritmsql);
                $alternate_items = array();
                foreach ($alteritmres as $itmbom) {
                    $alternate_items[$itmbom->item_id][] = $itmbom;
                }
                $return['alternate_items'] = $alternate_items;
            }

            $locsql = self::SQL_BOM_OP_COMPANY_ID . $companyId . self::AND_MANUFACTURING_BOM_ID . $rid . self::AND_IS_ACTIVE_0;
            $return['manufacturing_bom_operations'] = $callConfig->getAllRows($locsql);

            $locsql = "SELECT * FROM manufacturing_bom_byitems WHERE company_id =" . $companyId . self::AND_MANUFACTURING_BOM_ID . $rid . self::AND_IS_ACTIVE_0;
            $return['manufacturing_bom_byitems'] = $callConfig->getAllRows($locsql);
        }

        return $return;
    }

    public function getManufacturingBOMS($get)
    {
        global $callConfig;
        $return = array();

        if ($get['gettype'] == "SELECTBOMITEMID") {
            $itemid = $get['item_id'];
            $return['SELECTBOMITEMID'] = $this->togetitemvalues($itemid);

            $alteritmsql = "SELECT itm.id, itm.sku, itm.item_name FROM alternate_items ai, items itm WHERE ai.company_id = " . $get['company_id'] . " AND ai.item_id = " . $itemid . " AND ai.company_id = itm.company_id AND ai.alternate_item_id = itm.id AND ai.is_active = itm.is_active" . self::AND_IS_ACTIVE_0;
            $return['alternate_items'] = $callConfig->getAllRows($alteritmsql);

        } elseif ($get['gettype'] == "SELECTMANUFACTURINGOPERATION") {
            $manufacturing_operation_id = $get['manufacturing_operation_id'];

            $locsql = self::SQL_OP_COMPANY_ID . $get['company_id'] . self::AND_ID . $manufacturing_operation_id . self::AND_IS_ACTIVE_0;
            $return['SELECTMANUFACTURINGOPERATION'] = $callConfig->getRow($locsql);

            $locsql1 = self::SQL_OP_COMPANY_ID . $get['company_id'] . " AND parent_id = " . $manufacturing_operation_id . self::AND_IS_ACTIVE_0;
            $return['SELECTMANUFACTURINGOPERATION_MACHINE'] = $callConfig->getAllRows($locsql1);

        } elseif ($get['gettype'] == "SELECTMANUFACTURINGOPERATIONMACHINE") {
            $manufacturing_operation_id = $get['manufacturing_operation_id'];

            $locsql = self::SQL_OP_COMPANY_ID . $get['company_id'] . self::AND_ID . $manufacturing_operation_id . self::AND_IS_ACTIVE_0;
            $return['SELECTMANUFACTURINGOPERATION'] = $callConfig->getRow($locsql);

        } else {
            $return = $this->getBomDefaultLookups($get);
        }

        return $return;
    }

    private function saveBomMaterialItems($rid, int $companyId, int $userId, array $post): array
    {
        global $callConfig;
        $saved = array();

        foreach ((array) ($post['item_id'] ?? array()) as $seq => $itmid) {
            if (intval($itmid) <= 0 || floatval($post['quantity'][$seq] ?? 0) <= 0) {
                continue;
            }

            $manufacturing_bom_items_seq_no = $post['manufacturing_bom_items_seq_no'][$seq] ?? $seq;
            $alternate_manufacturing_bom_items = array();

            foreach ((array) ($post['alternate_item_id'][$manufacturing_bom_items_seq_no] ?? array()) as $sq1 => $alteritmid) {
                if ($alteritmid != "") {
                    $alternatelist = array();
                    $alternatelist['alternate_item_id'] = $post['alternate_item_id'][$manufacturing_bom_items_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_uom'] = $post['alternate_uom'][$manufacturing_bom_items_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_quantity'] = $post['alternate_quantity'][$manufacturing_bom_items_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_rate'] = $post['alternate_rate'][$manufacturing_bom_items_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_amount'] = $post['alternate_amount'][$manufacturing_bom_items_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_description'] = trim($post['alternate_description'][$manufacturing_bom_items_seq_no][$sq1] ?? '');
                    $alternate_manufacturing_bom_items[] = $alternatelist;
                }
            }

            $postdata = array(
                'company_id' => $companyId,
                'manufacturing_bom_id' => $rid,
                'item_id' => $itmid,
                'description' => $post['description'][$seq] ?? '',
                'uom' => $post['uom'][$seq] ?? 0,
                'quantity' => $post['quantity'][$seq] ?? 0,
                'rate' => $post['rate'][$seq] ?? 0,
                'amount' => $post['amount'][$seq] ?? 0,
                'scrap' => $post['scrap'][$seq] ?? 0,
                'party_id' => $post['party_id'][$seq] ?? 0,
                'warehouse_id' => $post['warehouse_id'][$seq] ?? 0,
                'bom_manufacturing_operation_id' => $post['bom_manufacturing_operation_id'][$seq] ?? 0,
                'is_active' => 0,
                'alternate_manufacturing_bom_items' => json_encode((array) $alternate_manufacturing_bom_items)
            );

            if (!empty($post['manufacturing_bom_items_id'][$seq])) {
                $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['updated_by'] = $userId;
                $saved[] = $callConfig->updateRecord('manufacturing_bom_items', $postdata, 'id', $post['manufacturing_bom_items_id'][$seq]);
            } else {
                $postdata['current_status'] = '0';
                $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['created_by'] = $userId;
                $saved[] = $callConfig->insertRecord('manufacturing_bom_items', $postdata);
            }
        }
        return $saved;
    }

    private function saveBomByItems($rid, int $companyId, int $userId, array $post): array
    {
        global $callConfig;
        $saved = array();

        foreach ((array) ($post['byitem_item_id'] ?? array()) as $seq => $itmid) {
            if (intval($itmid) <= 0) {
                continue;
            }

            $postdata = array(
                'company_id' => $companyId,
                'manufacturing_bom_id' => $rid,
                'byitem_item_id' => $itmid,
                'byitem_description' => $post['byitem_description'][$seq] ?? '',
                'byitem_uom' => $post['byitem_uom'][$seq] ?? 0,
                'byitem_quantity' => $post['byitem_quantity'][$seq] ?? 0,
                'is_active' => 0
            );

            if (!empty($post['manufacturing_bom_byitem_item_id'][$seq])) {
                $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['updated_by'] = $userId;
                $saved[] = $callConfig->updateRecord('manufacturing_bom_byitems', $postdata, 'id', $post['manufacturing_bom_byitem_item_id'][$seq]);
            } else {
                $postdata['current_status'] = '0';
                $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['created_by'] = $userId;
                $saved[] = $callConfig->insertRecord('manufacturing_bom_byitems', $postdata);
            }
        }
        return $saved;
    }

    private function saveBomOperations($rid, int $companyId, int $userId, array $post): array
    {
        global $callConfig;
        $saved = array();

        foreach ((array) ($post['manufacturing_operation_id'] ?? array()) as $seq => $opertid) {
            if (intval($opertid) <= 0) {
                continue;
            }

            $manufacturing_bom_operation_seq_no = $post['manufacturing_bom_operation_seq_no'][$seq] ?? $seq;
            $alternate_manufacturing_bom_operations = array();

            foreach ((array) ($post['alternate_manufacturing_operation_machine_id'][$manufacturing_bom_operation_seq_no] ?? array()) as $sq1 => $alteritmid) {
                if ($alteritmid != "") {
                    $alternatelist = array();
                    $alternatelist['alternate_manufacturing_operation_machine_id'] = $post['alternate_manufacturing_operation_machine_id'][$manufacturing_bom_operation_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_operation_time'] = $post['alternate_operation_time'][$manufacturing_bom_operation_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_operation_manpower'] = $post['alternate_operation_manpower'][$manufacturing_bom_operation_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_operation_cost_per_hours'] = $post['alternate_operation_cost_per_hours'][$manufacturing_bom_operation_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_operation_total'] = $post['alternate_operation_total'][$manufacturing_bom_operation_seq_no][$sq1] ?? '';
                    $alternatelist['alternate_operation_note'] = trim($post['alternate_operation_note'][$manufacturing_bom_operation_seq_no][$sq1] ?? '');
                    $alternate_manufacturing_bom_operations[] = $alternatelist;
                }
            }

            $postdata = array(
                'company_id' => $companyId,
                'manufacturing_bom_id' => $rid,
                'manufacturing_operation_id' => $opertid,
                'operation_time' => $post['operation_time'][$seq] ?? 0,
                'operation_manpower' => $post['operation_manpower'][$seq] ?? 0,
                'operation_cost_per_hours' => $post['operation_cost_per_hours'][$seq] ?? 0,
                'operation_total' => $post['operation_total'][$seq] ?? 0,
                'operation_note' => $post['operation_note'][$seq] ?? '',
                'manufacturing_operation_machine_id' => $post['manufacturing_operation_machine_id'][$seq] ?? 0,
                'is_active' => 0,
                'alternate_manufacturing_bom_operations' => json_encode((array) $alternate_manufacturing_bom_operations)
            );

            if (!empty($post['manufacturing_bom_operation_id'][$seq])) {
                $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['updated_by'] = $userId;
                $saved[] = $callConfig->updateRecord('manufacturing_bom_operations', $postdata, 'id', $post['manufacturing_bom_operation_id'][$seq]);
            } else {
                $postdata['current_status'] = '0';
                $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['created_by'] = $userId;
                $saved[] = $callConfig->insertRecord('manufacturing_bom_operations', $postdata);
            }
        }
        return $saved;
    }

    public function insertManufacturingBOMS($get, $post)
    {
        global $callConfig;
        $return = array();
        $return['post'] = $post;
        $return['get'] = $get;
        $rid = intval($post['rid'] ?? 0);
        $userId = intval($post['user_id'] ?? ($get['user_id'] ?? 0));
        $companyId = intval($post['company_id'] ?? ($get['company_id'] ?? 0));
        $bomItemId = intval($post['bom_item_id'] ?? 0);
        $bomUom = intval($post['bom_uom'] ?? 0);
        $bomQuantity = floatval($post['bom_quantity'] ?? 0);
        $transactionDate = trim($post['transaction_date'] ?? '');
        $bomAmount = floatval($post['bom_amount'] ?? 0);
        $materialItemIds = (array) ($post['item_id'] ?? array());

        $validMaterialRows = 0;
        foreach ($materialItemIds as $seq => $itmid) {
            if (intval($itmid) > 0 && floatval($post['quantity'][$seq] ?? 0) > 0) {
                $validMaterialRows++;
            }
        }

        $errors = array();
        if ($companyId <= 0) {
            $errors[] = 'Company is required.';
        }
        if ($userId <= 0) {
            $errors[] = 'User is required.';
        }
        if ($bomItemId <= 0) {
            $errors[] = 'BOM item is required.';
        }
        if ($bomUom <= 0) {
            $errors[] = 'BOM UOM is required.';
        }
        if ($bomQuantity <= 0) {
            $errors[] = 'BOM quantity must be greater than zero.';
        }
        if ($transactionDate == '') {
            $errors[] = 'Transaction date is required.';
        }
        if ($validMaterialRows == 0) {
            $errors[] = 'At least one material item with quantity is required.';
        }
        if (!empty($errors)) {
            $return['manufacturing_boms'] = array('status' => 'fail', 'message' => implode(' ', $errors));
            return $return;
        }

        $postdata = array('company_id' => $companyId, 'originator' => $userId, 'bom_item_id' => $bomItemId, 'bom_uom' => $bomUom, 'bom_quantity' => $bomQuantity, 'transaction_date' => $this->dateconvertdbformat($transactionDate), 'bom_amount' => $bomAmount);
        if ($rid > 0) {
            $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
            $postdata['updated_by'] = $userId;
            $res = $callConfig->updateRecord('manufacturing_boms', $postdata, 'id', $rid);
            $return['manufacturing_boms'] = $res;
            $upary = array('is_active' => null);
            $return['up_manufacturing_bom_items'][] = $callConfig->updateRecord('manufacturing_bom_items', $upary, 'manufacturing_bom_id', $rid);
            $return['up_manufacturing_bom_operations'][] = $callConfig->updateRecord('manufacturing_bom_operations', $upary, 'manufacturing_bom_id', $rid);
            $return['up_manufacturing_bom_byitems'][] = $callConfig->updateRecord('manufacturing_bom_byitems', $upary, 'manufacturing_bom_id', $rid);
        } else {
            $transaction_no = $this->toGetApplicationSerialNumber('manufacturing_boms', $post);
            $postdata['transaction_no'] = $transaction_no;
            $postdata['is_active'] = '0';
            $postdata['current_status'] = '0';
            $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
            $postdata['created_by'] = $userId;
            $res = $callConfig->insertRecord('manufacturing_boms', $postdata);
            $return['manufacturing_boms'] = $res;
            $rid = $res['LASTID'];
        }

        if ($rid != "") {
            $return['manufacturing_bom_items'] = $this->saveBomMaterialItems($rid, $companyId, $userId, $post);
            $return['manufacturing_bom_byitems'] = $this->saveBomByItems($rid, $companyId, $userId, $post);
            $return['manufacturing_bom_operations'] = $this->saveBomOperations($rid, $companyId, $userId, $post);
        }
        return $return;
    }
}

trait ManufacturingSubcontractWipTrait
{
    private function buildSubcontractPartyDetails($vendor): array
    {
        return array(
            'party_name' => $vendor->display_name != '' ? $vendor->display_name : $vendor->company,
            'party_phone' => $vendor->phone != '' ? $vendor->phone : $vendor->mobile,
            'party_tax_number' => $vendor->gstin != '' ? $vendor->gstin : $vendor->vendor_tax_reg_no,
            'party_address' => trim($vendor->billing_address . ", " . $vendor->billing_city . ", " . $vendor->billing_state, " ,"),
            'party_ship_address' => trim($vendor->shipping_address . ", " . $vendor->shipping_city . ", " . $vendor->shipping_state, " ,"),
        );
    }

    private function calculateBomComponentAmount(array $childRows, float $workorderitemqty): float
    {
        $componentAmount = 0;
        foreach ($childRows as $bomchild) {
            $quantity = floatval($bomchild->quantity) * $workorderitemqty;
            $price = $bomchild->rate != '' ? floatval($bomchild->rate) : 0;
            $componentAmount += ($quantity * $price);
        }
        return $componentAmount;
    }

    private function insertSubcontractWorkOrderItems($subcontractWorkOrderId, $bom, int $companyId, int $userId, float $workorderitemqty): void
    {
        global $callConfig;
        $childRows = (array) $bom['child_item'];
        foreach ($childRows as $bomchild) {
            $quantity = floatval($bomchild->quantity) * $workorderitemqty;
            $price = $bomchild->rate != '' ? floatval($bomchild->rate) : 0;
            $amount = $quantity * $price;
            $childData = array(
                'company_id' => $companyId,
                'transaction_id' => $subcontractWorkOrderId,
                'item_id' => $bomchild->item_id,
                'uom' => $bomchild->uom,
                'required_quantity' => $quantity,
                'available_quantity' => 0,
                'pending_qty' => $quantity,
                'rate' => $price,
                'amount' => $amount,
                'is_active' => 0,
                'current_status' => 0,
                'created_date' => date(self::DATE_FORMAT_HIS),
                'created_by' => $userId
            );
            $callConfig->insertRecord('subcontract_work_order_items', $childData);
        }
    }

    public function createSubcontractWorkOrderFromManufacturingWip($parentWorkOrder, $workOrderItem, $bomId, $bom, $get, $workorderitemqty)
    {
        global $callConfig;
        $return = array();
        $companyId = intval($get['company_id']);
        $userId = intval($get['user_id']);
        $vendorId = intval($workOrderItem->party_id);
        $vendorWarehouseId = intval($workOrderItem->warehouse_id);

        if ($vendorId > 0 && $vendorWarehouseId > 0) {
            $existingSql = "SELECT id FROM subcontract_work_orders WHERE company_id =" . $companyId . " AND ref_type = 'manufacturing_work_orders' AND ref_id = " . $parentWorkOrder->id . " AND product_id = " . $workOrderItem->item_id . " AND is_active = 0" . self::LIMIT_1;
            $existing = $callConfig->getRow($existingSql);

            if (!empty($existing->id)) {
                $return['existing_subcontract_work_order_id'] = $existing->id;
            } else {
                $vendor = $callConfig->getRow("SELECT id, display_name, company, email, phone, mobile, gstin, vendor_tax_reg_no, pan_no, billing_address, billing_city, billing_state, shipping_address, shipping_city, shipping_state, currency_id FROM vendors WHERE company_id =" . $companyId . self::AND_ID . $vendorId . self::AND_IS_ACTIVE_0);
                $party = $this->buildSubcontractPartyDetails($vendor);
                $finishedItemPrice = $workOrderItem->rate != '' ? floatval($workOrderItem->rate) : 0;
                $finishedTotalAmount = $finishedItemPrice * floatval($workorderitemqty);
                $childRows = (array) $bom['child_item'];
                $componentAmount = $this->calculateBomComponentAmount($childRows, floatval($workorderitemqty));

                $serialGet = $get;
                $serialGet['transaction_date'] = $parentWorkOrder->transaction_date;
                $postdata = array(
                    'company_id' => $companyId,
                    'transaction_number' => $this->toGetSubcontractWorkOrderNumber($serialGet),
                    'transaction_date' => $this->dateconvertdbformat($parentWorkOrder->transaction_date),
                    'transaction_warehouse_id' => $parentWorkOrder->warehouse,
                    'product_id' => $workOrderItem->item_id,
                    'currency_id' => $vendor->currency_id != '' ? $vendor->currency_id : 0,
                    'currency_rate' => 1,
                    'originator' => $parentWorkOrder->originator,
                    'party_id' => $vendorId,
                    'party_name' => $party['party_name'],
                    'party_email' => $vendor->email,
                    'party_tax_number' => $party['party_tax_number'],
                    'party_phone' => $party['party_phone'],
                    'party_address' => $party['party_address'],
                    'party_ship_adderss' => $party['party_ship_address'],
                    'party_warehouse_id' => $vendorWarehouseId,
                    'work_order_item_qty' => $workorderitemqty,
                    'manufacturing_bom_id' => $bomId,
                    'work_order_item_price' => $finishedItemPrice,
                    'work_order_total_amount' => $finishedTotalAmount,
                    'amount' => $componentAmount,
                    'tax_amount' => 0,
                    'total_amount' => $componentAmount,
                    'pending_amount' => 0,
                    'ref_type' => 'manufacturing_work_orders',
                    'ref_id' => $parentWorkOrder->id,
                    'is_active' => 0,
                    'current_status' => 0,
                    'created_date' => date(self::DATE_FORMAT_HIS),
                    'created_by' => $userId
                );

                $res = $callConfig->insertRecord('subcontract_work_orders', $postdata);
                $subcontractWorkOrderId = $res['LASTID'] ?? 0;
                if ($subcontractWorkOrderId > 0) {
                    $this->insertSubcontractWorkOrderItems($subcontractWorkOrderId, $bom, $companyId, $userId, floatval($workorderitemqty));
                }
                $return['subcontract_work_orders'] = $res;
            }
        }
        return $return;
    }

    public function toGetSubcontractWorkOrderNumber($get)
    {
        global $callConfig;
        $companyId = intval($get['company_id']);
        $transactionDate = $get['transaction_date'] != "" ? $this->dateconvertdbformat($get['transaction_date']) : date('Y-m-d');
        $serialSetup = $callConfig->getRow("SELECT id FROM application_serial_numbers WHERE company_id = '" . $companyId . "' AND application_table_name = 'subcontract_work_orders' AND from_date <= '" . $transactionDate . "' AND to_date >= '" . $transactionDate . self::AND_IS_ACTIVE_0 . self::LIMIT_1);
        $transactionNumber = !empty($serialSetup->id) ? $this->toGetApplicationSerialNumber('subcontract_work_orders', $get) : '';
        $existing = array();
        if ($transactionNumber != '') {
            $existing = $callConfig->getRow("SELECT id FROM subcontract_work_orders WHERE company_id =" . $companyId . " AND transaction_number = '" . $transactionNumber . self::AND_IS_ACTIVE_0 . self::LIMIT_1);
        }
        if ($transactionNumber != '' && empty($existing->id)) {
            return $transactionNumber;
        }

        $row = $callConfig->getRow("SELECT MAX(CAST(REPLACE(transaction_number, 'SWO', '') AS UNSIGNED)) AS last_no FROM subcontract_work_orders WHERE company_id =" . $companyId . " AND transaction_number LIKE 'SWO%'" . self::AND_IS_ACTIVE_0);
        $nextNo = intval($row->last_no) + 1;
        return 'SWO' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);
    }

    private function buildWipBomList(array $wip_products, int $companyId): array
    {
        global $callConfig;
        $manufacturing_bomslist = array();

        if (empty($wip_products)) {
            return $manufacturing_bomslist;
        }

        $wipproducts = implode(',', $wip_products);
        $mbomsql = self::SQL_BOM_COMPANY_ID . $companyId . " AND bom_item_id IN (" . $wipproducts . ")" . self::AND_IS_ACTIVE_0;
        $mbomres = $callConfig->getAllRows($mbomsql);
        $mboms_id = array();
        foreach ($mbomres as $mbomres1) {
            $mboms_id[$mbomres1->id] = $mbomres1->id;
            $manufacturing_bomslist[$mbomres1->id]['main'] = $mbomres1;
        }

        if (!empty($mboms_id)) {
            $mbomsid = implode(',', $mboms_id);

            $mbomisql = "SELECT mbi.*, itm.sku, itm.item_name, itm.purchase_expense_account FROM manufacturing_bom_items mbi LEFT JOIN items itm ON mbi.company_id = itm.company_id AND mbi.item_id = itm.id AND mbi.is_active = itm.is_active WHERE mbi.company_id =" . $companyId . " AND mbi.manufacturing_bom_id IN (" . $mbomsid . ")" . self::AND_IS_ACTIVE_0;
            $mbomires = $callConfig->getAllRows($mbomisql);
            foreach ($mbomires as $mbomires1) {
                $manufacturing_bomslist[$mbomires1->manufacturing_bom_id]['child_item'][] = $mbomires1;
            }

            $mbomosql = self::SQL_BOM_OP_COMPANY_ID . $companyId . " AND manufacturing_bom_id IN (" . $mbomsid . ")" . self::AND_IS_ACTIVE_0;
            $mbomores = $callConfig->getAllRows($mbomosql);
            foreach ($mbomores as $mbomores1) {
                $manufacturing_bomslist[$mbomores1->manufacturing_bom_id]['child_operations'][] = $mbomores1;
            }
        }

        return $manufacturing_bomslist;
    }

    public function autoInsertManufacturingWorkOrders($transaction_id, $get)
    {
        global $callConfig;
        $return = array();
        $mwosql = self::SQL_BOM_COMPANY_ID . $get['company_id'] . " AND id = " . $transaction_id . self::AND_IS_ACTIVE_0;
        $mwores = $callConfig->getRow($mwosql);

        $mwoisql = "SELECT mwoi.*, itm.sku, itm.item_name, itm.stocking_type FROM manufacturing_work_order_items mwoi, items itm WHERE mwoi.company_id =" . $get['company_id'] . " AND mwoi.company_id = itm.company_id AND mwoi.item_id = itm.id AND mwoi.transaction_id = " . $transaction_id . " AND mwoi.is_active = itm.is_active" . self::AND_IS_ACTIVE_0;
        $return['manufacturing_work_order_items_sql'] = $mwoisql;
        $mwoires = $callConfig->getAllRows($mwoisql);
        $return['manufacturing_work_order_items'] = $mwoires;

        $wip_products = array();
        $manufacturing_work_order_items = array();
        foreach ($mwoires as $mwoires1) {
            if ($mwoires1->stocking_type == "W") {
                $manufacturing_work_order_items[$mwoires1->item_id][] = $mwoires1;
                $wip_products[$mwoires1->item_id] = $mwoires1->item_id;
            }
        }

        $manufacturing_bomslist = $this->buildWipBomList($wip_products, intval($get['company_id']));

        foreach ($manufacturing_bomslist as $bomid => $boms) {
            $bommain = $boms['main'];
            $manufacturing_work_order_item = $manufacturing_work_order_items[$bommain->bom_item_id][0];
            $manufacturing_boms_qty = ($bommain->bom_quantity != "" && $bommain->bom_quantity != "0") ? $bommain->bom_quantity : 1;
            $workorderitemqty = ($manufacturing_work_order_item->request_qty / $manufacturing_boms_qty);
            if (intval($manufacturing_work_order_item->party_id) > 0) {
                $return['subcontract_work_orders'][] = $this->createSubcontractWorkOrderFromManufacturingWip($mwores, $manufacturing_work_order_item, $bomid, $boms, $get, $workorderitemqty);
                continue;
            }
            $postdata = array('company_id' => $get['company_id'], 'originator' => $mwores->originator, 'transaction_date' => $this->dateconvertdbformat($mwores->transaction_date), 'parent_manufacturing_work_order_id' => $transaction_id, 'manufacturing_firm_order_id' => null, 'work_order_item_id' => $bommain->bom_item_id, 'work_order_item_qty' => $workorderitemqty, 'manufacturing_bom_id' => $bomid, 'planned_start_date' => $this->dateconvertdbformat($mwores->planned_start_date), 'planned_end_date' => $this->dateconvertdbformat($mwores->planned_end_date), 'warehouse' => $mwores->warehouse, 'is_active' => 0, 'current_status' => 0, 'created_date' => date(self::DATE_FORMAT_HIS), 'created_by' => $get['user_id']);

            $postdata['transaction_no'] = $this->toGetApplicationSerialNumber('manufacturing_work_orders', $get);
            $res = $callConfig->insertRecord('manufacturing_work_orders', $postdata);
            $rid = $res['LASTID'];

            $bomchildlist = (array) ($boms['child_item'] ?? array());
            foreach ($bomchildlist as $bomchild) {
                $postdata = array('company_id' => $get['company_id'], 'transaction_id' => $rid, 'item_id' => $bomchild->item_id, 'uom' => $bomchild->uom, 'request_qty' => ($bomchild->quantity * $workorderitemqty), 'bom_qty' => $bomchild->quantity, 'description' => $bomchild->description, 'is_active' => 0, 'current_status' => 0, 'created_date' => date(self::DATE_FORMAT_HIS), 'created_by' => $get['user_id']);
                $res = $callConfig->insertRecord('manufacturing_work_order_items', $postdata);
                $return['manufacturing_work_order_items'][] = $res;
            }
            $bomchildoperationlist = (array) ($boms['child_operations'] ?? array());
            foreach ($bomchildoperationlist as $bomchildoperation) {
                $postdata = array('company_id' => $get['company_id'], 'transaction_id' => $rid, 'manufacturing_operation_id' => $bomchildoperation->manufacturing_operation_id, 'operation_time' => $bomchildoperation->operation_time, 'operation_manpower' => $bomchildoperation->operation_manpower, 'operation_note' => $bomchildoperation->operation_note, 'is_active' => 0, 'current_status' => 0, 'created_date' => date(self::DATE_FORMAT_HIS), 'created_by' => $get['user_id']);
                $callConfig->insertRecord('manufacturing_work_order_operations', $postdata);
            }
        }
    }

    public function toafterapprovaltoupdatemanuforecast($transaction_no, $get)
    {
        global $callConfig;
        $query = "SELECT * FROM manufacturing_forecast" . self::WHERE_COMPANY_ID . $get['company_id'] . " AND id=" . $transaction_no . " AND is_active=0";
        $data = $callConfig->getRow($query);
        if (!empty($data->id)) {
            $upquery = "UPDATE manufacturing_forecast_items SET current_status =" . ST_APPROVED . self::WHERE_COMPANY_ID . $get['company_id'] . " AND manufacturing_forecast_id='" . $data->id . self::AND_IS_ACTIVE_0_NOSPACE;
        } else {
            $query = "SELECT * FROM manufacturing_forecast_items" . self::WHERE_COMPANY_ID . $get['company_id'] . " AND id=" . $transaction_no;
            $data = $callConfig->getRow($query);
            $upquery = "UPDATE manufacturing_forecast_items SET current_status =" . ST_APPROVED . self::WHERE_COMPANY_ID . $get['company_id'] . " AND year='" . $data->year . self::AND_IS_ACTIVE_0_NOSPACE;
        }
        return $callConfig->executeQuery($upquery);
    }

    public function toafterapprovaltoupdatemanufirmorder($transaction_no, $get)
    {
        global $callConfig;
        $upquery = "UPDATE manufacturing_firm_order_items SET current_status =" . ST_APPROVED . self::WHERE_COMPANY_ID . $get['company_id'] . " AND manufacturing_firm_order_id='" . $transaction_no . self::AND_IS_ACTIVE_0_NOSPACE;
        return $callConfig->executeQuery($upquery);
    }
}

class ManufacturingUserClass extends userClass implements ManufacturingConstants
{
    use ManufacturingBomTrait;
    use ManufacturingSubcontractWipTrait;




    public function getmanufacturingworkorders($get)
    {
        global $callConfig;
        $return = array();

        if ($get['gettype'] == "SELECTMANUFACTURINGFIRMORDER") {
            $sql = "SELECT mfo.id, mfo.product_id, mfo.year_month, mfo.firm_month_1, mfo.firm_qty_1, firm.firm_order_no, itm.sku, itm.item_name, itm.stocking_type, itm.sale_uom, uoms.uom_name FROM manufacturing_firm_order_items mfo INNER JOIN manufacturing_firm_orders firm ON firm.company_id = mfo.company_id AND firm.id = mfo.manufacturing_firm_order_id AND firm.is_active = mfo.is_active AND firm.current_status = " . ST_APPROVED . " LEFT JOIN items itm ON mfo.company_id = itm.company_id AND mfo.product_id = itm.id AND mfo.is_active = itm.is_active LEFT JOIN unit_of_measurements uoms ON uoms.company_id = itm.company_id AND uoms.id = itm.sale_uom AND uoms.is_active = itm.is_active WHERE mfo.company_id =" . $get['company_id'] . " AND mfo.id = '" . $get['manufacturing_firm_order_id'] . "' AND mfo.is_active = 0 AND NOT EXISTS (SELECT 1 FROM manufacturing_work_orders used WHERE used.company_id = mfo.company_id AND used.manufacturing_firm_order_id = mfo.id AND used.is_active = 0 AND used.current_status != " . ST_REJECTED . ")";
            $res = $callConfig->getRow($sql);
            $return['SELECTMANUFACTURINGFIRMORDER'] = $res;

            if (!empty($res->product_id)) {
                $sql = self::SQL_SEL_BOM_WITH_UOM . $get['company_id'] . self::AND_MB_BOM_ITEM_ID . $res->product_id . self::AND_MB_IS_ACTIVE_0;
                $return['MANUFACTURINGBOMS'] = $callConfig->getAllRows($sql);
            } else {
                $return['MANUFACTURINGBOMS'] = array();
                $return['message'] = 'This firm order is already used or is not approved.';
            }

        } elseif ($get['gettype'] == "SELECTWORKOODERITEMID") {
            $sql = self::SQL_SEL_BOM_WITH_UOM . $get['company_id'] . self::AND_MB_BOM_ITEM_ID . $get['work_order_item_id'] . self::AND_MB_IS_ACTIVE_0;
            $return['SELECTWORKOODERITEMID'] = $callConfig->getAllRows($sql);
        } elseif ($get['gettype'] == "testapi") {
            $this->autoInsertManufacturingWorkOrders(1, $get);
        } else {
            $itemsql = "SELECT id, item_name, sku, sale_uom, stocking_type FROM items WHERE company_id =" . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $return['items'] = $callConfig->getAllRows($itemsql);

            $currentWorkOrderId = intval($get['rid'] ?? 0);
            $sql = "SELECT mfo.id, mfo.product_id, mfo.year_month, mfo.firm_month_1, mfo.firm_qty_1, firm.firm_order_no, itm.sku, itm.item_name, itm.stocking_type, itm.sale_uom, uoms.uom_name FROM manufacturing_firm_order_items mfo INNER JOIN manufacturing_firm_orders firm ON firm.company_id = mfo.company_id AND firm.id = mfo.manufacturing_firm_order_id AND firm.is_active = mfo.is_active AND firm.current_status = " . ST_APPROVED . " LEFT JOIN items itm ON mfo.company_id = itm.company_id AND mfo.product_id = itm.id AND mfo.is_active = itm.is_active LEFT JOIN unit_of_measurements uoms ON uoms.company_id = itm.company_id AND uoms.id = itm.sale_uom AND uoms.is_active = itm.is_active WHERE mfo.company_id =" . $get['company_id'] . " AND mfo.is_active = 0 AND NOT EXISTS (SELECT 1 FROM manufacturing_work_orders used WHERE used.company_id = mfo.company_id AND used.manufacturing_firm_order_id = mfo.id AND used.is_active = 0 AND used.current_status != " . ST_REJECTED . " AND NOT (used.id = " . $currentWorkOrderId . "))";
            $return['manufacturing_firm_orders'] = $callConfig->getAllRows($sql);

            $dessql = "SELECT id, uom_name FROM unit_of_measurements WHERE company_id =" . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $return['unit_of_measurements'] = $callConfig->getAllRows($dessql);

            $dessql = "SELECT id, display_name, company FROM vendors WHERE company_id =" . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $return['vendors'] = $callConfig->getAllRows($dessql);

            $locsql = "SELECT id, warehouse_name FROM warehouses WHERE company_id =" . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $return['warehouses'] = $callConfig->getAllRows($locsql);

            $locsql = "SELECT id, operation_name FROM manufacturing_operations WHERE company_id =" . $get['company_id'] . self::AND_IS_ACTIVE_0;
            $return['manufacturing_operations'] = $callConfig->getAllRows($locsql);

            $itemworktypesql = "SELECT genc_detail_code, genc_detail_name FROM general_code_details WHERE company_id = '" . $get['company_id'] . "' AND genc_code ='ITEMWORKTYPE' AND is_active ='0' ORDER BY genc_detail_name ASC";
            $itemworktyperes = $callConfig->getAllRows($itemworktypesql);
            $itemworktype = array();
            foreach ($itemworktyperes as $gen) {
                $itemworktype[$gen->genc_detail_code] = $gen->genc_detail_name;
            }
            $return['itemworktype'] = $itemworktype;

            if ($get['rid'] != "") {
                $locsql = self::SQL_BOM_COMPANY_ID . $get['company_id'] . self::AND_ID . $get['rid'] . self::AND_IS_ACTIVE_0;
                $mwores = $callConfig->getRow($locsql);
                $return['manufacturing_work_orders'] = $mwores;

                $locsql = "SELECT mwoi.*, itm.item_name, itm.sku, itm.stocking_type FROM manufacturing_work_order_items mwoi LEFT JOIN items itm ON mwoi.company_id = itm.company_id AND mwoi.item_id = itm.id AND mwoi.is_active = itm.is_active WHERE mwoi.company_id =" . $get['company_id'] . " AND mwoi.transaction_id = " . $get['rid'] . self::AND_IS_ACTIVE_0;
                $return['manufacturing_work_order_items'] = $callConfig->getAllRows($locsql);

                $locsql = "SELECT mwbp.*, itm.item_name FROM manufacturing_work_order_items_by_product mwbp LEFT JOIN items itm ON mwbp.company_id = itm.company_id AND mwbp.byitem_item_id = itm.id AND mwbp.is_active = itm.is_active WHERE mwbp.company_id =" . $get['company_id'] . " AND mwbp.transaction_id = " . $get['rid'] . self::AND_IS_ACTIVE_0;
                $return['manufacturing_work_order_items_by_product'] = $callConfig->getAllRows($locsql);

                $locsql = "SELECT * FROM manufacturing_work_order_operations WHERE company_id =" . $get['company_id'] . " AND transaction_id = " . $get['rid'] . self::AND_IS_ACTIVE_0;
                $return['manufacturing_work_order_operations'] = $callConfig->getAllRows($locsql);

                $sql = self::SQL_SEL_BOM_WITH_UOM . $get['company_id'] . self::AND_MB_BOM_ITEM_ID . $mwores->work_order_item_id . self::AND_MB_IS_ACTIVE_0;
                $return['manufacturing_boms_list'] = $callConfig->getAllRows($sql);

                $locsql = self::SQL_BOM_COMPANY_ID . $get['company_id'] . self::AND_ID . $mwores->manufacturing_bom_id . self::AND_IS_ACTIVE_0;
                $return['manufacturing_boms'] = $callConfig->getRow($locsql);
            }

            $manufacturing_bom_id = $get['manufacturing_bom_id'];
            if ($manufacturing_bom_id != "") {
                $locsql = self::SQL_BOM_COMPANY_ID . $get['company_id'] . self::AND_ID . $manufacturing_bom_id . self::AND_IS_ACTIVE_0;
                $return['manufacturing_boms'] = $callConfig->getRow($locsql);

                $locsql = "SELECT mbi.*, itm.item_name, itm.sku, itm.stocking_type FROM manufacturing_bom_items mbi LEFT JOIN items itm ON mbi.company_id = itm.company_id AND mbi.item_id = itm.id AND mbi.is_active = itm.is_active WHERE mbi.company_id =" . $get['company_id'] . self::AND_MANUFACTURING_BOM_ID . $manufacturing_bom_id . self::AND_IS_ACTIVE_0;
                $return['manufacturing_bom_items'] = $callConfig->getAllRows($locsql);

                $locsql = "SELECT mbi.*, itm.item_name FROM manufacturing_bom_byitems mbi LEFT JOIN items itm ON mbi.company_id = itm.company_id AND mbi.byitem_item_id = itm.id AND mbi.is_active = itm.is_active WHERE mbi.company_id =" . $get['company_id'] . self::AND_MANUFACTURING_BOM_ID . $manufacturing_bom_id . self::AND_IS_ACTIVE_0;
                $return['manufacturing_bom_by_items'] = $callConfig->getAllRows($locsql);

                $locsql = self::SQL_BOM_OP_COMPANY_ID . $get['company_id'] . self::AND_MANUFACTURING_BOM_ID . $manufacturing_bom_id . self::AND_IS_ACTIVE_0;
                $return['manufacturing_bom_operations'] = $callConfig->getAllRows($locsql);
            }
        }

        return $return;
    }

    private function validateWorkOrderRequired(array $post, array $ctx): ?string
    {
        $err = null;
        if ($ctx['companyId'] <= 0 || $ctx['userId'] <= 0) {
            $err = 'Company and user details are required.';
        } elseif ($ctx['transactionDate'] == '' || $ctx['plannedStartDate'] == '' || $ctx['plannedEndDate'] == '') {
            $err = 'Transaction date, planned start date and planned due date are required.';
        } elseif (strtotime($ctx['plannedEndDate']) < strtotime($ctx['plannedStartDate'])) {
            $err = 'Planned due date cannot be earlier than planned start date.';
        } elseif ($ctx['workOrderItemId'] <= 0 || $ctx['workOrderQty'] <= 0 || $ctx['bomId'] <= 0 || $ctx['warehouseId'] <= 0) {
            $err = 'Product, quantity, BOM and warehouse are required.';
        } elseif (empty($post['item_id']) || !is_array($post['item_id'])) {
            $err = 'Please select a BOM to load required components.';
        } elseif (empty($post['manufacturing_operation_id']) || !is_array($post['manufacturing_operation_id'])) {
            $err = 'Please select a BOM to load required operations.';
        }
        return $err;
    }

    private function validateExistingWorkOrder(int $companyId, int $rid): ?string
    {
        global $callConfig;
        if ($rid <= 0) {
            return null;
        }
        $existingWorkOrder = $callConfig->getRow("SELECT id, current_status, work_log_status FROM manufacturing_work_orders WHERE company_id='" . $companyId . self::AND_ID_QUOTE . $rid . self::AND_IS_ACTIVE_0_NOSPACE);
        if (empty($existingWorkOrder->id)) {
            $err = 'Work order not found.';
        } else {
            $editableStatuses = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK, ST_CALLBACK, '', null, 0, '0');
            if (!in_array($existingWorkOrder->current_status, $editableStatuses)) {
                $err = 'Approved work order cannot be edited.';
            } elseif ($existingWorkOrder->work_log_status !== null && $existingWorkOrder->work_log_status !== '') {
                $err = 'Production already started. Work order cannot be edited.';
            } else {
                $err = null;
            }
        }
        return $err;
    }

    private function validateFirmOrderMatch(int $companyId, int $firmOrderItemId, int $workOrderItemId, float $workOrderQty, int $rid): ?string
    {
        global $callConfig;
        if ($firmOrderItemId <= 0) {
            return null;
        }
        $firmOrderRow = $callConfig->getRow("SELECT mfo.id, mfo.product_id, mfo.firm_qty_1, firm.current_status FROM manufacturing_firm_order_items mfo INNER JOIN manufacturing_firm_orders firm ON firm.company_id=mfo.company_id AND firm.id=mfo.manufacturing_firm_order_id AND firm.is_active=mfo.is_active WHERE mfo.company_id='" . $companyId . self::AND_ID_QUOTE . $firmOrderItemId . self::AND_IS_ACTIVE_0_NOSPACE);
        if (empty($firmOrderRow->id) || $firmOrderRow->current_status != ST_APPROVED) {
            $err = 'Firm order must be approved before creating a work order.';
        } elseif (intval($firmOrderRow->product_id) != $workOrderItemId) {
            $err = 'Firm order product does not match selected work order product.';
        } elseif ($workOrderQty > floatval($firmOrderRow->firm_qty_1)) {
            $err = 'Work order quantity cannot exceed firm order quantity.';
        } else {
            $duplicateSql = "SELECT id FROM manufacturing_work_orders WHERE company_id='" . $companyId . "' AND manufacturing_firm_order_id='" . $firmOrderItemId . "' AND is_active=0 AND current_status != " . ST_REJECTED;
            if ($rid > 0) {
                $duplicateSql .= " AND id != '" . $rid . "'";
            }
            $duplicateWorkOrder = $callConfig->getRow($duplicateSql . self::LIMIT_1);
            $err = !empty($duplicateWorkOrder->id) ? 'A work order already exists for this firm order.' : null;
        }
        return $err;
    }

    private function validateSubcontractWipComponents(int $companyId, array $post): ?string
    {
        global $callConfig;
        foreach ((array) $post['item_id'] as $seq => $componentItemId) {
            $componentItemId = intval($componentItemId);
            if ($componentItemId <= 0) {
                continue;
            }
            $method = strtoupper(trim($post['manufacturing_method'][$seq] ?? 'INHOUSE'));
            if ($method != 'SUBCONTRACT') {
                continue;
            }
            $componentRow = $callConfig->getRow(self::SQL_ITEMS_STOCKING_TYPE . $companyId . self::AND_ID_QUOTE . $componentItemId . self::AND_IS_ACTIVE_0_NOSPACE);
            if (empty($componentRow->id) || $componentRow->stocking_type != 'W') {
                return 'Subcontract option is allowed only for WIP components.';
            }
            if (intval($post['party_id'][$seq] ?? 0) <= 0 || intval($post['warehouse_id'][$seq] ?? 0) <= 0) {
                return 'Please select Sub Contractor and Sub Contractor Warehouse for every subcontract WIP component.';
            }
        }
        return null;
    }

    private function validateWorkOrderInputs(array $post, array $ctx): ?string
    {
        global $callConfig;
        $reqErr = $this->validateWorkOrderRequired($post, $ctx);
        if ($reqErr !== null) {
            return $reqErr;
        }
        $existErr = $this->validateExistingWorkOrder($ctx['companyId'], $ctx['rid']);
        if ($existErr !== null) {
            return $existErr;
        }

        $itemRow = $callConfig->getRow(self::SQL_ITEMS_STOCKING_TYPE . $ctx['companyId'] . self::AND_ID_QUOTE . $ctx['workOrderItemId'] . self::AND_IS_ACTIVE_0_NOSPACE);
        if (empty($itemRow->id) || !in_array($itemRow->stocking_type, array('F', 'W'))) {
            $err = 'Work order product must be an active finished good or WIP item.';
        } else {
            $bomRow = $callConfig->getRow("SELECT id, bom_item_id FROM manufacturing_boms WHERE company_id='" . $ctx['companyId'] . self::AND_ID_QUOTE . $ctx['bomId'] . "' AND bom_item_id='" . $ctx['workOrderItemId'] . self::AND_IS_ACTIVE_0_NOSPACE);
            if (empty($bomRow->id)) {
                $err = 'Selected BOM does not belong to the selected product.';
            } else {
                $firmErr = $this->validateFirmOrderMatch($ctx['companyId'], $ctx['firmOrderItemId'], $ctx['workOrderItemId'], $ctx['workOrderQty'], $ctx['rid']);
                $err = ($firmErr !== null) ? $firmErr : $this->validateSubcontractWipComponents($ctx['companyId'], $post);
            }
        }
        return $err;
    }

    private function resolveComponentPartyWarehouse(int $companyId, array $post, int $seq): array
    {
        global $callConfig;
        $manufacturingMethod = strtoupper(trim($post['manufacturing_method'][$seq] ?? 'INHOUSE'));
        $componentItem = $callConfig->getRow(self::SQL_ITEMS_STOCKING_TYPE . $companyId . self::AND_ID_QUOTE . intval($post['item_id'][$seq]) . self::AND_IS_ACTIVE_0_NOSPACE);
        $isWipComponent = (!empty($componentItem->id) && $componentItem->stocking_type == 'W');
        if ($isWipComponent && $manufacturingMethod != 'SUBCONTRACT') {
            return array('party_id' => null, 'warehouse_id' => null);
        }
        return array(
            'party_id' => $post['party_id'][$seq] != '' ? intval($post['party_id'][$seq]) : null,
            'warehouse_id' => $post['warehouse_id'][$seq] != '' ? intval($post['warehouse_id'][$seq]) : null
        );
    }

    private function saveWorkOrderComponents($rid, int $companyId, array $post): array
    {
        global $callConfig;
        $saved = array();

        foreach ($post['item_id'] as $seq => $itmid) {
            $pw = $this->resolveComponentPartyWarehouse($companyId, $post, $seq);
            $postdata = array(
                'company_id' => $post['company_id'],
                'transaction_id' => $rid,
                'item_id' => $post['item_id'][$seq],
                'uom' => $post['uom'][$seq],
                'request_qty' => $post['request_qty'][$seq],
                'bom_qty' => $post['bom_qty'][$seq],
                'description' => $post['description'][$seq],
                'scrap' => $post['scrap'][$seq],
                'party_id' => $pw['party_id'],
                'warehouse_id' => $pw['warehouse_id'],
                'bom_manufacturing_operation_id' => $post['bom_manufacturing_operation_id'][$seq],
                'rate' => $post['rate'][$seq],
                'amount' => $post['amount'][$seq],
                'is_active' => 0
            );

            if ($post['manufacturing_work_order_item_id'][$seq] != "" && $post['form_updatetion_type'] == "update") {
                $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['updated_by'] = $post['user_id'];
                $saved[] = $callConfig->updateRecord('manufacturing_work_order_items', $postdata, 'id', $post['manufacturing_work_order_item_id'][$seq]);
            } else {
                $postdata['current_status'] = '0';
                $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['created_by'] = $post['user_id'];
                $saved[] = $callConfig->insertRecord('manufacturing_work_order_items', $postdata);
            }
        }
        return $saved;
    }

    private function saveWorkOrderByProducts($rid, array $post, int $userId): array
    {
        global $callConfig;
        $saved = array();

        if (isset($post['byitem_item_id']) && count((array) $post['byitem_item_id']) > 0) {
            foreach ($post['byitem_item_id'] as $seq => $itmid) {
                if ($itmid != "") {
                    $postdata = array('company_id' => $post['company_id'], 'transaction_id' => $rid, 'byitem_item_id' => $post['byitem_item_id'][$seq], 'byitem_uom' => $post['byitem_uom'][$seq], 'byitem_quantity' => $post['byitem_quantity'][$seq], 'byitem_description' => $post['byitem_description'][$seq], 'is_active' => 0);

                    if ($post['manufacturing_work_order_items_by_product_id'][$seq] != "" && $post['form_updatetion_type'] == "update") {
                        $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                        $postdata['updated_by'] = $userId;
                        $saved[] = $callConfig->updateRecord('manufacturing_work_order_items_by_product', $postdata, 'id', $post['manufacturing_work_order_items_by_product_id'][$seq]);
                    } else {
                        $postdata['current_status'] = '0';
                        $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
                        $postdata['created_by'] = $userId;
                        $saved[] = $callConfig->insertRecord('manufacturing_work_order_items_by_product', $postdata);
                    }
                }
            }
        }
        return $saved;
    }

    private function saveWorkOrderOperations($rid, array $post, int $userId): array
    {
        global $callConfig;
        $saved = array();

        foreach ($post['manufacturing_operation_id'] as $seq => $itmid) {
            $postdata = array('company_id' => $post['company_id'], 'transaction_id' => $rid, 'manufacturing_operation_id' => $post['manufacturing_operation_id'][$seq], 'operation_time' => $post['operation_time'][$seq], 'operation_manpower' => $post['operation_manpower'][$seq], 'operation_note' => $post['operation_note'][$seq], 'manufacturing_operation_machine_id' => $post['manufacturing_operation_machine_id'][$seq], 'operation_cost_per_hours' => $post['operation_cost_per_hours'][$seq], 'operation_total' => $post['operation_total'][$seq], 'is_active' => 0);

            if ($post['manufacturing_work_order_operation_id'][$seq] != "" && $post['form_updatetion_type'] == "update") {
                $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['updated_by'] = $userId;
                $saved[] = $callConfig->updateRecord('manufacturing_work_order_operations', $postdata, 'id', $post['manufacturing_work_order_operation_id'][$seq]);
            } else {
                $postdata['current_status'] = '0';
                $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
                $postdata['created_by'] = $userId;
                $saved[] = $callConfig->insertRecord('manufacturing_work_order_operations', $postdata);
            }
        }
        return $saved;
    }

    public function insertManufacturingWorkOrders($get, $post)
    {
        global $callConfig;
        $return = array();
        $return['get'] = $get;
        $companyId = intval($post['company_id'] ?? ($get['company_id'] ?? 0));
        $userId = intval($post['user_id'] ?? ($get['user_id'] ?? 0));
        $rid = intval($post['rid'] ?? 0);
        $firmOrderItemId = intval($post['manufacturing_firm_order_id'] ?? 0);
        $workOrderItemId = intval($post['work_order_item_id'] ?? 0);
        $workOrderQty = floatval($post['work_order_item_qty'] ?? 0);
        $bomId = intval($post['manufacturing_bom_id'] ?? 0);
        $warehouseId = intval($post['warehouse'] ?? 0);
        $transactionDate = $this->dateconvertdbformat($post['transaction_date'] ?? '');
        $plannedStartDate = $this->dateconvertdbformat($post['planned_start_date'] ?? '');
        $plannedEndDate = $this->dateconvertdbformat($post['planned_end_date'] ?? '');

        $ctx = array(
            'companyId' => $companyId,
            'userId' => $userId,
            'rid' => $rid,
            'firmOrderItemId' => $firmOrderItemId,
            'workOrderItemId' => $workOrderItemId,
            'workOrderQty' => $workOrderQty,
            'bomId' => $bomId,
            'warehouseId' => $warehouseId,
            'transactionDate' => $transactionDate,
            'plannedStartDate' => $plannedStartDate,
            'plannedEndDate' => $plannedEndDate
        );
        $errorMsg = $this->validateWorkOrderInputs($post, $ctx);
        if ($errorMsg !== null) {
            $return['manufacturing_work_orders'] = array('status' => 'fail', 'LASTID' => 0, 'message' => $errorMsg);
            return $return;
        }

        $postdata = array('company_id' => $companyId, 'originator' => $userId, 'transaction_date' => $transactionDate, 'parent_manufacturing_work_order_id' => 0, 'manufacturing_firm_order_id' => $firmOrderItemId > 0 ? $firmOrderItemId : null, 'work_order_item_id' => $workOrderItemId, 'work_order_item_qty' => $workOrderQty, 'manufacturing_bom_id' => $bomId, 'planned_start_date' => $plannedStartDate, 'planned_end_date' => $plannedEndDate, 'warehouse' => $warehouseId);
        if ($post['rid'] != "") {
            $postdata['updated_date'] = date(self::DATE_FORMAT_HIS);
            $postdata['updated_by'] = $userId;
            $res = $callConfig->updateRecord('manufacturing_work_orders', $postdata, 'id', $rid);
            $return['manufacturing_work_orders'] = $res;
            $upary = array('is_active' => null);
            $return['manufacturing_boms11'][] = $callConfig->updateRecord('manufacturing_work_order_items', $upary, 'transaction_id', $rid);
            $return['manufacturing_boms11'][] = $callConfig->updateRecord('manufacturing_work_order_items_by_product', $upary, 'transaction_id', $rid);
            $return['manufacturing_boms11'][] = $callConfig->updateRecord('manufacturing_work_order_operations', $upary, 'transaction_id', $rid);
        } else {
            $transaction_no = $this->toGetApplicationSerialNumber('manufacturing_work_orders', $post);
            $postdata['transaction_no'] = $transaction_no;
            $postdata['is_active'] = '0';
            $postdata['current_status'] = '0';
            $postdata['created_date'] = date(self::DATE_FORMAT_HIS);
            $postdata['created_by'] = $userId;
            $res = $callConfig->insertRecord('manufacturing_work_orders', $postdata);
            $return['manufacturing_work_orders'] = $res;
            $rid = $res['LASTID'];
        }

        if ($rid != "") {
            $return['manufacturing_work_order_items'] = $this->saveWorkOrderComponents($rid, $companyId, $post);
            $return['manufacturing_work_order_items_by_product'] = $this->saveWorkOrderByProducts($rid, $post, $userId);
            $return['manufacturing_work_order_operations'] = $this->saveWorkOrderOperations($rid, $post, $userId);
        }
        return $return;
    }


}
