<?php

class uniformClass extends userClass{
	function getUniformStockData($get) {
	    global $callConfig;
	    $company_id  = $get['company_id'];
	    $location_id = isset($get['location_id']) && $get['location_id'] != "" 
	                    ? $get['location_id']
	                    : 1; // Default location
	    $query1 = "SELECT id, name
	        FROM stationery_categories
	        WHERE company_id = '{$company_id}'
	          AND code = 'UNIFORM'
	          AND is_active = 0 LIMIT 1";

	    $category = $callConfig->getRow($query1);
	    if (!$category) {
	        return json_encode([
	            "status" => "error",
	            "msg" => "Uniform category not found"
	        ]);
	    }
	    $category_id = $category->id;

	    $item_id    = $get['rid'];
        $item = array();
        $items = array();
        if($item_id != '' && $item_id>0){
		    $sql = "SELECT  si.*, 
		                CONCAT(c.genc_detail_name,' - ',c.description) AS color_name,
		                CONCAT(s.genc_detail_name,' - ',s.description) AS size_name
		            FROM stationery_items si
		            LEFT JOIN general_code_details c 
		                ON c.company_id=si.company_id 
		               AND c.genc_code='UNIFORM_COLOR' 
		               AND c.genc_detail_code=si.color 
		            LEFT JOIN general_code_details s 
		                ON s.company_id=si.company_id 
		               AND s.genc_code='UNIFORM_SIZE' 
		               AND s.genc_detail_code=si.size 
		            WHERE si.category_id='{$category_id}' 
		              AND si.id='{$item_id}' 
		              AND si.company_id='{$company_id}' 
		              AND si.is_active=0";

		    $item = $callConfig->getRow($sql);
	    }else{
	    	$sql = "SELECT  si.*, 
		                CONCAT(c.genc_detail_name,' - ',c.description) AS color_name,
		                CONCAT(s.genc_detail_name,' - ',s.description) AS size_name
		            FROM stationery_items si
		            LEFT JOIN general_code_details c 
		                ON c.company_id=si.company_id 
		               AND c.genc_code='UNIFORM_COLOR' 
		               AND c.genc_detail_code=si.color 
		            LEFT JOIN general_code_details s 
		                ON s.company_id=si.company_id 
		               AND s.genc_code='UNIFORM_SIZE' 
		               AND s.genc_detail_code=si.size 
		            WHERE si.category_id='{$category_id}' 
		              AND si.company_id='{$company_id}' 
		              AND si.is_active=0";

		    $items = $callConfig->getAllRows($sql);
	    }

		// get locations
		$q1="SELECT id,CONCAT(short_name,' - ',location_name) AS loc_name FROM locations WHERE company_id='{$company_id}' AND is_active=0 ORDER BY location_name ASC";
		$loc_arr=$callConfig->getAllRows($q1);
		$locations = array();
		foreach ($loc_arr as $row) {
			$locations[$row->id] = $row;
		}
		// colors
		$q2="SELECT genc_detail_code,CONCAT(genc_detail_name,' - ',description) AS color_name FROM general_code_details WHERE company_id='{$company_id}' AND genc_code='UNIFORM_COLOR' AND is_active=0 ORDER BY genc_detail_name ASC";
		$colors=$callConfig->getAllRows($q2);

		// sizes
		$q3="SELECT genc_detail_code,CONCAT(genc_detail_name,' - ',description) AS size_name FROM general_code_details WHERE company_id='{$company_id}' AND genc_code='UNIFORM_SIZE' AND is_active=0 ORDER BY genc_detail_name ASC";
		$sizes=$callConfig->getAllRows($q3);

	    return array("status"=>"success","category_id"=> $category_id,"category_name" => $category->name,"item"=> $item,"items"=> $items,"locations"=>$locations,"colors"=>$colors,"sizes"=>$sizes);
	}
	function getEmployeeUniformData($get){
	    global $callConfig;
	    $company_id = $get['company_id'];
	    $user_id    = $get['user_id'];
	    $cfg_jobgrade = $get['config_jobgrade']; // pass from API call 

	    // get all employees
	    $q1 = "SELECT id, first_name, last_name, display_name, location_id, job_grade_id,user_id 
	           FROM employees 
	           WHERE company_id='{$company_id}' AND is_active=0";
	    $emp_arr = $callConfig->getAllRows($q1);

	    // separate logged-in employee
	    $logged_emp = array();
	    foreach($emp_arr as $e){
	        if($e->user_id == $user_id){
	            $logged_emp = $e;
	            break;
	        }
	    }
	    if(empty($logged_emp)){
	        return array("status"=>"error","msg"=>"Employee not found");
	    }
	    // default values
	    $employee_uniforms = array();
	    $uniform_types = array();
	    $job_grade_color = null;

	    // if job grade is ON
	    if($cfg_jobgrade == "Y"){
	        // get job grade row
	        if(!empty($logged_emp->job_grade_id)){
	            $q2 = "SELECT id, job_grade_name, uniform_color 
	                   FROM job_grade 
	                   WHERE id='{$logged_emp->job_grade_id}' 
	                   AND company_id='{$company_id}' AND is_active=0";
	            $job_grade = $callConfig->getRow($q2);
	            $job_grade_color = $job_grade ? $job_grade->uniform_color : null;
	        }
 		}

	        // get employee uniform mapping
        $q3 = "SELECT eu.id, eu.uniform_type_id, eu.size_id,
              ut.uniform_type_name,
              CONCAT(s.genc_detail_name,' - ',s.description) AS size_name
	           FROM emp_uniforms eu
	           LEFT JOIN uniform_types ut
	                ON ut.id = eu.uniform_type_id AND ut.company_id='{$company_id}'
	           LEFT JOIN general_code_details s
	                ON s.genc_detail_code = eu.size_id
	               AND s.company_id='{$company_id}'
	               AND s.genc_code='UNIFORM_SIZE'
	           WHERE eu.company_id='{$company_id}'
	             AND eu.employee_id='{$logged_emp->id}'
	             AND eu.is_active=0";

	    $employee_uniforms = $callConfig->getAllRows($q3);

	     /* Build arrays for filtering stock */
	    $allowed_types = array_column($employee_uniforms, "uniform_type_id");
	    $allowed_sizes = array_column($employee_uniforms, "size_id");

	    // get UNIFORM category_id
	    $qc = "SELECT id FROM stationery_categories 
	           WHERE company_id='{$company_id}' AND code='UNIFORM' AND is_active=0 LIMIT 1";
	    $cat = $callConfig->getRow($qc);
	    $category_id = $cat ? $cat->id : 0;

	    // load stationery_items for this category + employee location
	    $loc = $logged_emp->location_id ?? 1;

	    $add_color_filter = "";
	    if($cfg_jobgrade == "Y" && $job_grade_color != ""){
	        $add_color_filter = " AND si.color = '{$job_grade_color}' ";
	    }
	    $q5 = "SELECT si.*,
                ut.uniform_type_name, si.uniform_type_id,
	            c.genc_detail_name AS color_name,c.genc_detail_code AS color_id,
	            s.genc_detail_name AS size_name,s.genc_detail_code AS size_id
           FROM stationery_items si
           LEFT JOIN uniform_types ut
                ON ut.id = si.uniform_type_id
           LEFT JOIN general_code_details c
                ON c.genc_detail_code = si.color
               AND c.company_id='{$company_id}'
               AND c.genc_code='UNIFORM_COLOR'
           LEFT JOIN general_code_details s
                ON s.genc_detail_code = si.size
               AND s.company_id='{$company_id}'
               AND s.genc_code='UNIFORM_SIZE'
           WHERE si.company_id='{$company_id}'
             AND si.category_id='{$category_id}'
             AND si.location_id='{$loc}'
             AND si.is_active=0
             AND si.uniform_type_id IN (" . implode(",", $allowed_types) . ")
             AND si.size IN (" . implode(",", $allowed_sizes) . ")
             $add_color_filter
           ORDER BY si.item_name ASC";

	    $stationery_items = $callConfig->getAllRows($q5);

	    $uniform_types = $callConfig->getAllRows("
	        SELECT id, uniform_type_name
	        FROM uniform_types
	        WHERE company_id='{$company_id}' AND is_active=0
	        ORDER BY uniform_type_name ASC");

	    $sizes = $callConfig->getAllRows("
	        SELECT genc_detail_code, CONCAT(genc_detail_name) AS size_name
	        FROM general_code_details
	        WHERE company_id='{$company_id}' AND genc_code='UNIFORM_SIZE' AND is_active=0
	        ORDER BY genc_detail_name ASC");

	    $colors = $callConfig->getAllRows("
	        SELECT genc_detail_code, CONCAT(genc_detail_name) AS color_name
	        FROM general_code_details
	        WHERE company_id='{$company_id}' AND genc_code='UNIFORM_COLOR' AND is_active=0
	        ORDER BY genc_detail_name ASC");
	    $locations = $callConfig->getAllRows("
	        SELECT id, location_name
	        FROM locations
	        WHERE company_id='{$company_id}' AND is_active=0
	        ORDER BY location_name ASC");
	    $job_grades = $callConfig->getAllRows("
		    SELECT id, job_grade_name, uniform_color
		    FROM job_grade
		    WHERE company_id='{$company_id}' 
		      AND is_active=0
		    ORDER BY job_grade_name ASC
		");


	    return array(
	    		// "sql" => $q5,
			"status" => "success",
			"employee" => $logged_emp,
			"category_id" => $category_id,
			"stationery_items" => $stationery_items,
			"locations" => $locations,
	          //  NEW FOR EDIT MODAL
			"colors" => $colors,
			"employee_uniforms" => $employee_uniforms,
			"uniform_types"     => $uniform_types,
			"sizes"             => $sizes,
			"job_grades"        => $job_grades

	    );
	}

	function saveUniformStock($get, $post){
	    global $callConfig;

	    $return = array();
	    if($post['rid'] != '' && $post['rid']>0){
	    	$rid = $post['rid'];
		    $company_id = $get['company_id'];

		    $sql = "SELECT available_qty FROM stationery_items 
		            WHERE id='{$rid}' AND company_id='{$company_id}' AND is_active=0";
		    $old = $callConfig->getRow($sql);
		    $old_qty = intval($old->available_qty);

		    $new_qty = intval($post['available_qty']);

		    $update = array(
		        'location_id'   => $post['location_id'] ?? 0,
		        'item_name'     => $post['item_name'] ?? NULL,
		        'description'   => $post['description'] ?? NULL,
		        'available_qty' => $new_qty,
		        'minmum_qty'    => $post['minmum_qty'] ?? 0,
		        'color'         => $post['color'] ?? NULL,
		        'size'          => $post['size'] ?? NULL,
		        'updated_by'    => $get['user_id'],
		        'updated_date'  => date("Y-m-d H:i:s")
		    );

		    $res[] = $callConfig->updateRecord('stationery_items', $update, 'id', $rid);

		    if($old_qty != $new_qty){
		        $diff = $new_qty - $old_qty;

		        $ledger = array(
		            'company_id'    => $company_id,
		            'item_id'       => $rid,
		            'in_qty'        => ($diff > 0 ? $diff : 0),
		            'out_qty'       => ($diff < 0 ? abs($diff) : 0),
		            'request_no'    => '',
		            'run_qty'       => $new_qty,
		            'comments'      => 'Stock Correction (Edit Page)',
		            'is_active'     => 0,
		            'current_status'=> 0,
		            'created_by'    => $get['user_id'],
		            'created_date'  => date("Y-m-d H:i:s")
		        );

		       $res[] = $callConfig->insertRecord('stationery_item_ledger', $ledger);
		    }
		    return array("status"=>"success","msg"=>"Updated Successfully", 'res' => $res);
	    }else{

		    $insertary = array(
				'company_id'    => $get['company_id'],
				'location_id'   => $post['location_id'] ?? 0,
				'category_id'   => $post['category_id'] ?? 0,
				'item_name'     => $post['item_name'] ?? NULL,
				'description'   => $post['description'] ?? NULL,
				'available_qty' => $post['available_qty'] ?? 0,
				'minmum_qty'    => $post['minmum_qty'] ?? 0,
				'color'         => $post['color'] ?? NULL,
				'size'          => $post['size'] ?? NULL,
				'cost'          => isset($post['cost']) ? $post['cost'] : 0
			);

			$insertary['is_active']     = 0;
			$insertary['current_status']= 0;
			$insertary['created_by']    = $get['user_id'];
			$insertary['created_date']  = date("Y-m-d H:i:s");

			$res = $callConfig->insertRecord('stationery_items', $insertary);
			$return['res'] = $res;
			$item_id = $res['LASTID'];
			if($item_id){ 
				// insert ledger
				$ledger = array(
				  'company_id'    => $get['company_id'],
				  'item_id'       => $item_id,
				  'in_qty'        => $post['available_qty'] ?? 0,
				  'out_qty'       => 0,
				  'run_qty'       => $post['available_qty'] ?? 0,
				  'comments'      => 'Initial Stock Entry',
				  'is_active'     => 0,
				  'current_status'=> 0,
				  'created_by'    => $get['user_id'],
				  'created_date'  => date("Y-m-d H:i:s")
				);

				$res1 = $callConfig->insertRecord('stationery_item_ledger', $ledger);
				$return['res1'] = $res1;
			}
		    return  array("status"=>"success","return"=>$return);
		}

	}
	function getUniformItemsForTopup($get){
	    global $callConfig;

	    $company_id  = $get['company_id'];
	    $location_id = ($get['location_id'] != "" ? $get['location_id'] : 1);

	    // get UNIFORM category_id
	    $q1 = "SELECT id,name 
	           FROM stationery_categories
	           WHERE company_id='{$company_id}'
	             AND code='UNIFORM'
	             AND is_active=0 LIMIT 1";
	    $cat = $callConfig->getRow($q1);


	    if(!$cat){
	        return array("status"=>"error","msg"=>"Uniform category not found");
	    }

	    $category_id = $cat->id;

	    // get all items for this category + location
	    $q2 = "SELECT si.id, si.item_name, available_qty
	    	    FROM stationery_items si
	           WHERE si.company_id='{$company_id}'
	             AND si.location_id='{$location_id}'
	             AND si.category_id='{$category_id}'
	             AND si.is_active=0
	           ORDER BY si.item_name ASC";
	    $items = $callConfig->getAllRows($q2);


	    // locations
	    $q3 = "SELECT id,CONCAT(short_name,' - ',location_name) AS loc_name
	           FROM locations
	           WHERE company_id='{$company_id}'
	             AND is_active=0
	           ORDER BY location_name ASC";
	    $locations = $callConfig->getAllRows($q3);

	    // colors
	    $q4 = "SELECT genc_detail_code,
	                  CONCAT(genc_detail_name,' - ',description) AS color_name
	           FROM general_code_details
	           WHERE company_id='{$company_id}'
	             AND genc_code='UNIFORM_COLOR'
	             AND is_active=0
	           ORDER BY genc_detail_name ASC";
	    $colors = $callConfig->getAllRows($q4);

	    // sizes
	    $q5 = "SELECT genc_detail_code,
	                  CONCAT(genc_detail_name,' - ',description) AS size_name
	           FROM general_code_details
	           WHERE company_id='{$company_id}'
	             AND genc_code='UNIFORM_SIZE'
	             AND is_active=0
	           ORDER BY genc_detail_name ASC";
	    $sizes = $callConfig->getAllRows($q5);


	    return array(
	        "status"        => "success",
	        "category_id"   => $category_id,
	        "category_name" => $cat->name,
	        "items"         => $items,
	        "locations"     => $locations,
	        "colors"        => $colors,
	        "sizes"         => $sizes
	    );
	}
	function loadUniformItemsByLocation($get){
	    global $callConfig;

	    $company_id  = $get['company_id'];
	    $location_id = $get['location_id'];
	    $category_id = $get['category_id'];
	    // fetch item names only
	    $q1 = "SELECT DISTINCT item_name 
	           FROM stationery_items
	           WHERE company_id='{$company_id}'
	             AND location_id='{$location_id}'
	             AND category_id='{$category_id}'
	             AND is_active=0
	           ORDER BY item_name ASC";
	    $items = $callConfig->getAllRows($q1);

	    return array("items"=>$items);

	}
	function checkUniformCombination($get){
		global $callConfig;

	    $company_id  = $get['company_id'];
	    $data = json_decode($get['data'], 1);

	    $item_name   = $data['item_name'];
	    $color       = $data['color'];
	    $size        = $data['size'];
	    $location_id = $data['location_id'];
	    $category_id = $data['category_id'];

	    $q = "SELECT id,available_qty 
	          FROM stationery_items 
	          WHERE company_id='{$company_id}'
	            AND location_id='{$location_id}'
	            AND category_id='{$category_id}'
	            AND item_name='{$item_name}'
	            AND color='{$color}'
	            AND size='{$size}'
	            AND is_active=0
	          LIMIT 1";

	    $row = $callConfig->getRow($q);

	    if($row){
	        return array(
	            "exists" => 1,
	            "item_id" => $row->id,
	            "qty" => $row->available_qty
	        );
	    }

	    return array(
	        "exists" => 0,
	        "item_id" => 0,
	        "qty" => 0
	    );
	}


	function saveUniformTopup($get, $post){
	    global $callConfig;

	    $company_id  = $get['company_id'];
	    $user_id     = $get['user_id'];
	    $location_id = $post['location_id'];
	    $category_id = $post['category_id'];
	    $item_name   = $post['item_id']; // item_name is sent as item_id in front-end
	    $color       = $post['color'];
	    $size        = $post['size'];
	    $topup_qty   = intval($post['topup_qty']);

	    // check existing combination
	    $q = "SELECT id,available_qty 
	          FROM stationery_items
	          WHERE company_id='{$company_id}'
	            AND location_id='{$location_id}'
	            AND category_id='{$category_id}'
	            AND item_name='{$item_name}'
	            AND color='{$color}'
	            AND size='{$size}'
	            AND is_active=0
	          LIMIT 1";

	    $row = $callConfig->getRow($q);

	    if($row){
	        // update existing item
	        $item_id = $row->id;
	        $old_qty = $row->available_qty;
	        $new_qty = $old_qty + $topup_qty;

	        $update = array(
	            "available_qty" => $new_qty,
	            "updated_by" => $user_id,
	            "updated_date" => date("Y-m-d H:i:s")
	        );

	        $callConfig->updateRecord("stationery_items", $update, "id", $item_id);

	    } else {
	        // insert new item
	        $insert = array(
	            "company_id" => $company_id,
	            "location_id" => $location_id,
	            "category_id" => $category_id,
	            "item_name" => $item_name,
	            "color" => $color,
	            "size" => $size,
	            "available_qty" => $topup_qty,
	            "minmum_qty" => 0,
	            "description" => '',
	            "cost" => 0,
	            "is_active" => 0,
	            "current_status" => 0,
	            "created_by" => $user_id,
	            "created_date" => date("Y-m-d H:i:s")
	        );

	        $res = $callConfig->insertRecord("stationery_items", $insert);
	        $item_id = $res['LASTID'];
	        $new_qty = $topup_qty;
	    }

	    // insert ledger
	    $ledger = array(
	        "company_id" => $company_id,
	        "item_id" => $item_id,
	        "in_qty" => $topup_qty,
	        "out_qty" => 0,
	        "request_no" => '',
	        "run_qty" => $new_qty,
	        "comments" => "Top-up",
	        "is_active" => 0,
	        "current_status" => 0,
	        "created_by" => $user_id,
	        "created_date" => date("Y-m-d H:i:s")
	    );

	    $callConfig->insertRecord("stationery_item_ledger", $ledger);

	    return array("status"=>"success","new_qty"=>$new_qty);
	}

	function checkUniformItemNameExists($get, $post){
	    global $callConfig;

	    $company_id  = $get['company_id'];
	    $location_id = $post['location_id'];
	    $category_id = $post['category_id'];
	    $item_name   = trim($post['item_name']);
	    $exclude_id  = isset($post['exclude_id']) ? intval($post['exclude_id']) : 0;

	    $sql = "SELECT id FROM stationery_items
	            WHERE company_id='{$company_id}'
	              AND location_id='{$location_id}'
	              AND category_id='{$category_id}'
	              AND item_name='{$item_name}'
	              AND is_active=0";
	     // exclude same item in edit mode
	    if($exclude_id > 0){
	        $sql .= " AND id != '{$exclude_id}'";
	    }

	    $row = $callConfig->getRow($sql);

	    if($row){
	        return array("exists"=>1);
	    }

	    return array("exists"=>0);
	}
	function saveStationeryRequest($get, $post){
	    global $callConfig;

	    $company_id  = $get['company_id'];
	    $user_id     = $get['user_id'];
	    $item_id     = $post['item_id'];
	    $req_qty     = intval($post['qty']);
	    $require_date = $post['require_date'] ?? NULL;
	    $comment     = $post['comment'] ?? '';
	    $location_id = $post['location_id'] ?? ($_SESSION['location_id'] ?? 1);

	    $whr = "application_table_name = 'stationery_requests' ";
        $query = $callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS, '*', $whr, '', '', '');
        // $return['query'] = $query;
        $query_res = $callConfig->getRow($query);
        $res = $query_res;
        $le = $res->doc_no_length;
        $var = "%0" . $le . "d";
        $data = $res->pre_fix . sprintf($var, $res->serial_number+1) . $res->post_fix;

	    $req_no = trim($data);

	    // Insert into stationery_requests
	    $reqArr = array(
	        'company_id'            => $company_id,
	        'user_id'               => $user_id,
	        'location_id'           => $location_id,
	        'request_no'            => $req_no,
	        'item_id'               => $item_id,
	        'total_qty'             => $req_qty,
	        'request_date'          => date("Y-m-d"),
	        'require_date'          => $require_date,
	        'request_comment'       => $comment,
	        'is_active'             => 0,
	        'current_status'        => 0,
	        'created_by'            => $user_id,
	        'created_date'          => date("Y-m-d H:i:s")
	    );

	    $res1 = $callConfig->insertRecord('stationery_requests', $reqArr);
	    $request_id = $res1['LASTID'];

	    if (isset($res->id)) {
            $update = $callConfig->updateRecord(TBL_APPLICATION_SERIAL_NUMBERS, array('serial_number' => $res->serial_number + 1), 'id', $res->id);
        }

	    // Insert request item row


	    // Update stock & ledger
	    // $this->updateUniformStockAfterRequest($company_id, $item_id, $req_qty, $user_id);

	    return array("status"=>"success","request_id"=>$request_id,"request_no"=>$req_no);
	}
	function saveMultipleStationeryRequests($get, $post) {
	    global $callConfig;

	    $company_id  = $get['company_id'];
	    $user_id     = $get['user_id'];

	    // From frontend
	    $items = json_decode($post['items'], true); // array of {item_id, qty}
	    $require_date = $post['require_date'] ?? NULL;
	    $comment = $post['comment'] ?? '';
	    $location_id = $post['location_id'] ?? 0;

	    if (empty($items)) {
	        return array("status" => "error", "msg" => "No items received");
	    }


	    $whr = "application_table_name = 'stationery_requests' ";
        $query = $callConfig->selectQuery(TBL_APPLICATION_SERIAL_NUMBERS, '*', $whr, '', '', '');
        $query_res = $callConfig->getRow($query);

        if (!$query_res) {
            return array("status" => "error", "msg" => "Serial number config missing");
        }

        $res = $query_res;
        $le = $res->doc_no_length;
        $var = "%0" . $le . "d";
        $req_no = trim($res->pre_fix . sprintf($var, $res->serial_number + 1) . $res->post_fix);

        /* Insert into stationery_requests */
        $reqArr = array(
            'company_id'        => $company_id,
            'user_id'           => $user_id,
            'location_id'       => $location_id,
            'request_no'        => $req_no,
            'request_date'      => date("Y-m-d"),
            'require_date'      => $require_date,
            'request_comment'   => $comment,
            'is_active'         => 0,
            'current_status'    => 0,
            'created_by'        => $user_id,
            'created_date'      => date("Y-m-d H:i:s")
        );

        $res1 = $callConfig->insertRecord('stationery_requests', $reqArr);
        $request_id = $res1['LASTID'];

        /* Update serial number (same as old function) */
        if (isset($res->id)) {
            $callConfig->updateRecord(
                TBL_APPLICATION_SERIAL_NUMBERS,
                array('serial_number' => $res->serial_number + 1),
                'id',
                $res->id
            );
        }

	    foreach ($items as $itm) {
	        $item_id     = ($itm['item_id']);
	        $color_id     = ($itm['color_id']);
	        $size_id     = ($itm['size_id']);
	        $uniform_type_id = ($itm['uniform_type_id']);
	        $req_qty     = ($itm['qty']);

	        if ($req_qty <= 0) continue;
	        /*  Generate Serial Number (same as your old logic)*/
	        $itemArr = array(
		        'company_id'    => $company_id,
		        'item_id'    => $item_id,
		        'user_id'    => $user_id,
		        'qty'    => $req_qty,
		        'request_no'    => $req_no,
		        'request_id'    => $request_id,
		        'color_id'       => $color_id,
		        'size_id'       => $size_id,
		        'uniform_type_id' => $uniform_type_id,
		        'is_active'     => 0,
		        'current_status'=> 0,
		        'created_by'    => $user_id,
		        'created_date'  => date("Y-m-d H:i:s")
		    );

		     $res = $callConfig->insertRecord('stationery_cart', $itemArr);
	          /* Update Stock & Ledger */
	          $this->updateUniformStockAfterRequest($company_id, $item_id, $req_qty, $user_id, $req_no);
		     $get['transaction_table'] = 'stationery_requests';
			$get['current_status'] = 0;
			$get['transaction_no'] = $request_id;
			$get['next_status'] = 1;
		     $auto_forward_docs = $this->submitStatusDynamicWorkFlow($get);

	        /* Store each request response */
	        $responses[] = $res;
	    }

	    return array(
	        "status" => "success",
	        "request_no" => $req_no,
	        "responses"  => $responses,
	        "auto_forward_docs" => $auto_forward_docs
	    );
	}


	function updateUniformStockAfterRequest($company_id, $item_id, $req_qty, $user_id, $req_no){
	    global $callConfig;

	    // Get current qty
	    $sql = "SELECT available_qty FROM stationery_items 
	            WHERE id='{$item_id}' AND company_id='{$company_id}' AND is_active=0";
	    $row = $callConfig->getRow($sql);

	    if(!$row){ return false; }

	    $old_qty = intval($row->available_qty);
	    $new_qty = $old_qty - $req_qty;
	    if($new_qty < 0) $new_qty = 0;

	    // Update stock
	    $update = array(
	        'available_qty' => $new_qty,
	        'updated_by'    => $user_id,
	        'updated_date'  => date("Y-m-d H:i:s")
	    );
	    $callConfig->updateRecord('stationery_items', $update, 'id', $item_id);

	    // Insert ledger
	    $ledger = array(
	        'company_id'    => $company_id,
	        'item_id'       => $item_id,
	        'in_qty'        => 0,
	        'out_qty'       => $req_qty,
	        'run_qty'       => $new_qty,
	        'request_no'    => $req_no,
	        'comments'      => 'Request Issued',
	        'is_active'     => 0,
	        'current_status'=> 0,
	        'created_by'    => $user_id,
	        'created_date'  => date("Y-m-d H:i:s")
	    );
	    $callConfig->insertRecord('stationery_item_ledger', $ledger);

	    return true;
	}

	function searchUniformAdmin($get, $post) {
	    global $callConfig;

	    $company_id   = $get['company_id'];
	    $location_id  = $post['location_id'];
	    $employee_id  = $post['employee_id'];
	    $type_id      = $post['type_id'] ?? "";
	    $size_id      = $post['size_id'] ?? "";
	    $color_id     = $post['color_id'] ?? "";
	    $config_jobgrade  = trim($post['config_jobgrade'] ?? "");

	    if (!$location_id || !$employee_id) {
	        return array("status" => "error", "msg" => "Location and Employee required");
	    }

	    /*---------------------------------------------------------
	        1️⃣ Get emp_uniforms → allowed type_ids & size_ids
	    ----------------------------------------------------------*/
	    $q1 = "SELECT uniform_type_id, size_id
	        FROM emp_uniforms
	        WHERE company_id='{$company_id}' 
	          AND employee_id='{$employee_id}'
	          AND is_active=0";
	    $emp_uniforms = $callConfig->getAllRows($q1);

	    $allowed_types = array_unique(array_column($emp_uniforms, "uniform_type_id"));
	    $allowed_sizes = array_unique(array_column($emp_uniforms, "size_id"));

	    if (empty($allowed_types) || empty($allowed_sizes)) {
	        return array("status" => "success", "items" => []);
	    }

	    /*---------------------------------------------------------
	        2️⃣ Check job-grade color for this employee
	    ----------------------------------------------------------*/
	    $job_grade_color = "";
	    if($config_jobgrade != ''  && $config_jobgrade =='Y'){
		    $qEmp = "SELECT job_grade_id 
		        FROM employees 
		        WHERE id='{$employee_id}' 
		          AND company_id='{$company_id}' 
		          AND is_active=0 LIMIT 1";
		    $empRow = $callConfig->getRow($qEmp);

		    if (!empty($empRow->job_grade_id)) {

		        $qJG = "
		            SELECT uniform_color 
		            FROM job_grade 
		            WHERE id='{$empRow->job_grade_id}' 
		              AND company_id='{$company_id}' 
		              AND is_active=0
		            LIMIT 1
		        ";
		        $jg = $callConfig->getRow($qJG);

		        if ($jg && $jg->uniform_color != "") {
		            $job_grade_color = $jg->uniform_color;
		        }
		    }
	    }

	    /*---------------------------------------------------------
	        3️⃣ Build SQL Filters
	    ----------------------------------------------------------*/

	    $flt_type = "";
	    if ($type_id != "") {
	        $flt_type = " AND si.uniform_type_id = '{$type_id}' ";
	    } else {
	        $flt_type = " AND si.uniform_type_id IN (" . implode(",", $allowed_types) . ") ";
	    }

	    $flt_size = "";
	    if ($size_id != "") {
	        $flt_size = " AND si.size = '{$size_id}' ";
	    } else {
	        $flt_size = " AND si.size IN (" . implode(",", $allowed_sizes) . ") ";
	    }

	    $flt_color = "";
	    if ($job_grade_color != "") {
	        $flt_color = " AND si.color = '{$job_grade_color}' ";
	    } else if ($color_id != "") {
	        $flt_color = " AND si.color = '{$color_id}' ";
	    }

	    /*---------------------------------------------------------
	        4️⃣ Get UNIFORM category_id
	    ----------------------------------------------------------*/
	    $qCat = "
	        SELECT id 
	        FROM stationery_categories
	        WHERE company_id='{$company_id}' 
	          AND code='UNIFORM' 
	          AND is_active=0
	        LIMIT 1
	    ";
	    $cat = $callConfig->getRow($qCat);
	    $category_id = $cat ? $cat->id : 0;

	    /*---------------------------------------------------------
	        5️⃣ Final Query to Fetch Items
	    ----------------------------------------------------------*/
	    $qItems = "
	        SELECT 
	            si.*,
	            ut.uniform_type_name, si.uniform_type_id,
	            c.genc_detail_name AS color_name,c.genc_detail_code AS color_id,
	            s.genc_detail_name AS size_name,s.genc_detail_code AS size_id
	        FROM stationery_items si
	        LEFT JOIN uniform_types ut 
	               ON ut.id = si.uniform_type_id
	        LEFT JOIN general_code_details c
	               ON c.genc_detail_code = si.color
	              AND c.company_id='{$company_id}'
	              AND c.genc_code='UNIFORM_COLOR'
	        LEFT JOIN general_code_details s
	               ON s.genc_detail_code = si.size
	              AND s.company_id='{$company_id}'
	              AND s.genc_code='UNIFORM_SIZE'
	        WHERE si.company_id='{$company_id}'
	          AND si.location_id='{$location_id}'
	          AND si.category_id='{$category_id}'
	          AND si.is_active=0
	          {$flt_type}
	          {$flt_size}
	          {$flt_color}
	        ORDER BY si.item_name ASC";

	    $items = $callConfig->getAllRows($qItems);

	    return array(
	        "status" => "success",
	        "items"  => $items
	    );
	}


	function getUniformDashboardData($get) {
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $role = $get['role'] ?? '';
	    $whr = '';
	    if($role == ''){
			$whr = " AND r.user_id=".$get['user_id'];
	    }

	    // Base SELECT for main request table
	    $sql = "SELECT 
	            r.id,
	            r.request_no,
	            r.user_id,
	            r.location_id,
	            r.request_date,
	            r.require_date,
	            r.current_status,
	            e.display_name AS employee_name,
	            l.location_name

	        FROM stationery_requests r
	        LEFT JOIN employees e
	            ON e.user_id = r.user_id 
	            AND e.company_id = r.company_id

	        LEFT JOIN locations l
	            ON l.id = r.location_id
	            AND l.company_id = r.company_id

	        WHERE r.company_id = '{$company_id}'
	          AND r.is_active = 0 $whr
	        ORDER BY r.id DESC";

	    $rows = $callConfig->getAllRows($sql);

	    return array(
	        "status" => "success",
	        "requests" => $rows
	    );
	}
	function getUniformRequestItems($get) {
	    global $callConfig;

	    $company_id = $get['company_id'];
	    $request_id = $get['request_id'];

	    if (!$request_id) {
	        return array("status" => "error", "msg" => "Request ID missing");
	    }

	    // Fetch all items for the request
	    $sql = "
	        SELECT 
	            c.id,
	            c.item_id,
	            c.qty,
	            c.color_id,
	            c.size_id,
	            c.uniform_type_id,

	            si.item_name,

	            ut.uniform_type_name,

	            col.genc_detail_name AS color_name,
	            col.description AS color_desc,

	            sz.genc_detail_name AS size_name,
	            sz.description AS size_desc

	        FROM stationery_cart c

	        LEFT JOIN stationery_items si
	            ON si.id = c.item_id
	            AND si.company_id = c.company_id

	        LEFT JOIN uniform_types ut
	            ON ut.id = c.uniform_type_id
	            AND ut.company_id = c.company_id

	        LEFT JOIN general_code_details col
	            ON col.genc_detail_code = c.color_id
	            AND col.company_id = c.company_id
	            AND col.genc_code = 'UNIFORM_COLOR'

	        LEFT JOIN general_code_details sz
	            ON sz.genc_detail_code = c.size_id
	            AND sz.company_id = c.company_id
	            AND sz.genc_code = 'UNIFORM_SIZE'

	        WHERE c.company_id = '{$company_id}'
	          AND c.request_id = '{$request_id}'
	          AND c.is_active = 0

	        ORDER BY c.id ASC
	    ";

	    $rows = $callConfig->getAllRows($sql);

	    return array(
	        "status" => "success",
	        "items"  => $rows
	    );
	}

	function getStationeryRequestView($get) {
	    global $callConfig;
	    $company_id = $get['company_id'];
	    $rid        = $get['rid'];    // stationery_requests.id
	    $user_id    = $get['user_id'];
	    if (empty($rid)) {
	        return array("status" => "error", "msg" => "Request ID missing");
	    }
	    /* ---------------------------------------------------
	        1. FETCH PARENT REQUEST DETAILS
	    --------------------------------------------------- */
	    $q1 = "SELECT sr.id, sr.request_no, sr.request_date, sr.require_date,
	               sr.location_id, sr.user_id AS requested_by,
	               sr.request_comment, sr.current_status, sr.is_active,
	               u.display_name AS employee_name,
	               l.location_name,
	               st.name AS status_text
	        FROM stationery_requests sr
	        LEFT JOIN employees u 
	               ON u.user_id = sr.user_id
	        LEFT JOIN locations l 
	               ON l.id = sr.location_id
	        LEFT JOIN status_progress st
	               ON st.status = sr.current_status AND table_name='trips'
	        WHERE sr.company_id='{$company_id}' 
	          AND sr.id='{$rid}' AND sr.is_active=0
	        LIMIT 1";
	    $parent = $callConfig->getRow($q1);

	    if (!$parent) {
	        return array("status" => "error", "msg" => "Request not found");
	    }
	    /* ---------------------------------------------------
	        2. FETCH CHILD ITEMS (stationery_cart)
	    --------------------------------------------------- */
	    $q2 = "SELECT sc.id, sc.item_id, sc.qty, sc.color_id, sc.size_id, sc.uniform_type_id,
	               si.item_name,
	               ut.uniform_type_name,
	               c.genc_detail_name AS color_name,
	               s.genc_detail_name AS size_name
	        FROM stationery_cart sc
	        LEFT JOIN stationery_items si 
	               ON si.id = sc.item_id
	        LEFT JOIN uniform_types ut
	               ON ut.id = sc.uniform_type_id
	        LEFT JOIN general_code_details c 
	               ON c.genc_detail_code = sc.color_id 
	              AND c.company_id='{$company_id}' AND c.genc_code='UNIFORM_COLOR'
	        LEFT JOIN general_code_details s 
	               ON s.genc_detail_code = sc.size_id 
	              AND s.company_id='{$company_id}' AND s.genc_code='UNIFORM_SIZE'
	        WHERE sc.company_id='{$company_id}' 
	          AND sc.request_id='{$rid}'
	          AND sc.is_active=0";

	    $items = $callConfig->getAllRows($q2);
	    /* ---------------------------------------------------
	        3. BUILD RESPONSE
	    --------------------------------------------------- */
	    return array(
	        "status"  => "success",
	        "parent"  => $parent,
	        "items"   => $items
	    );
	}

	function getStationeryRequestList($get) {
	    global $callConfig;

	    $company_id = $get['company_id'];

	    $q = "SELECT sr.id, sr.request_no, sr.request_date, sr.require_date,
	               sr.current_status,
	               u.display_name AS employee_name,
	               l.location_name,
	               st.name AS status_text
	        FROM stationery_requests sr
	        LEFT JOIN employees u ON u.user_id = sr.user_id
	        LEFT JOIN locations l ON l.id = sr.location_id
	        LEFT JOIN status_progress st ON st.status = sr.current_status AND table_name='trips'
	        WHERE sr.company_id='{$company_id}'
	          AND sr.is_active=0
	        ORDER BY sr.id DESC";

	    $rows = $callConfig->getAllRows($q);

	    return array(
	        "status" => "success",
	        "requests" => $rows
	    );
	}

	function deleteStationeryRequest($get) {
	    global $callConfig;
	    $rid = $get['rid'] ?? 0;
	    if (!$rid) {
	        return array("status" => "error", "msg" => "Invalid Request ID");
	    }
	    $update = array("is_active" => NULL);
	    $res = $callConfig->updateRecord('stationery_requests',$update,'id',$rid);
	    return array(
	        "status" => "success",
	        "msg"    => "Request deleted successfully"
	    );
	}
	function saveEmpJobGradeAndUniforms($get, $post) {
	    global $callConfig;

	    $company_id   = $get['company_id'];   // or $get['company_id']
	    $user_id      = $get['userid'];       // or $get['user_id']
	    $employee_id  = $post['employee_id'];
	    $job_grade_id = $post['job_grade_id'] ?? null;

	    $uniforms          = json_decode($post['uniforms'] ?? '[]', true);
	    $deleted_uniforms  = json_decode($post['deleted_uniforms'] ?? '[]', true);

	    if (empty($employee_id)) {
	        return array("status" => "error", "msg" => "Employee ID missing");
	    }

	    /* 1) Update job grade if provided */
	    if (!empty($job_grade_id)) {
	    $res['A'] = $callConfig->updateRecord('employees',array('job_grade_id' => $job_grade_id),'id',$employee_id);
	    }

	    /* 2) Insert / Update uniforms */
	    foreach ($uniforms as $u) {

	        $existing_id = trim($u['existing_id'] ?? '');
	        $type_id     = $u['type_id'] ?? '';
	        $size_id     = $u['size_id'] ?? '';

	        if (!$type_id || !$size_id) {
	            continue;
	        }

	        if ($existing_id !== "") {
	            // UPDATE existing row
	            $updateArr = array(
	                'uniform_type_id' => $type_id,
	                'size_id'         => $size_id,
	                'updated_by'     => $user_id,
	                'updated_date'   => date("Y-m-d H:i:s")
	            );

	          $res['B'][] =  $callConfig->updateRecord('emp_uniforms',$updateArr,'id',$existing_id);

	        } else {
	            // INSERT new row
	            $insertArr = array(
	                'company_id'      => $company_id,
	                'employee_id'     => $employee_id,
	                'uniform_type_id' => $type_id,
	                'size_id'         => $size_id,
	                'is_active'       => 0,
	                'created_by'      => $user_id,
	                'created_date'    => date("Y-m-d H:i:s")
	            );

	          $res['C'][] =  $callConfig->insertRecord('emp_uniforms', $insertArr);
	        }
	    }

	    /* 3) Soft delete removed uniforms */
	    if (!empty($deleted_uniforms)) {
	        foreach ($deleted_uniforms as $del_id) {
	          $res['D'][] =  $callConfig->updateRecord('emp_uniforms',
	                array('is_active' => NULL, 'updated_by' => $user_id, 'updated_date' => date("Y-m-d H:i:s")),'id',$del_id);
	        }
	    }

	    return array("status" => "success", "msg" => "Job Grade & Uniforms updated successfully", "res" => $res);
	}

}