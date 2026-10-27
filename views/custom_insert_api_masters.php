<?php $_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submit'])) {
    //echo "<pre>"; print_r($_POST); echo "</pre>";
    $request_arry = array();
    foreach ((array)$_POST['get_name'] as $key => $name) {
        $request_arry[$name] = $_POST['get_value'][$key];
    }
    $_POST['api_request_parameters'] = json_encode($request_arry);

    $headers_arry = array();
    foreach ((array)$_POST['get_header_name'] as $key => $name) {
        $headers_arry[$name] = $_POST['get_header_value'][$key];
    }
    $_POST['request_header'] = json_encode($headers_arry);

    $post_parameters_arry = array();
    foreach ((array)$_POST['get_post_field_name'] as $key => $name) {
        $post_parameters_arry[$name] = $_POST['get_post_field_value'][$key];
    }
    $_POST['api_request_post_parameters'] = json_encode($post_parameters_arry);

    $response_arry = array();
    foreach ((array)$_POST['get_response_details_name'] as $key => $name) { 
        $response_arry[$name]['response_field'] = $_POST['get_response_details_name'][$key];
        $response_arry[$name]['application_table_field'] = $_POST['get_response_details_value'][$key];
        $response_arry[$name]['uniqueness'] = $_POST['uniqueness'][$key] =="Y"?"Y":"N";
       // $response_arry[$name]['application_table_field'] = $_POST['application_table_field'][$key];
    }
    //print_r($response_arry);
    $_POST['api_response_details'] = json_encode($response_arry);
    $table_name = 'api_masters';
    unset($_POST['get_name']);
    unset($_POST['get_value']);
    unset($_POST['get_header_name']);
    unset($_POST['get_header_value']);
    unset($_POST['get_post_field_name']);
    unset($_POST['get_post_field_value']);
    unset($_POST['get_response_details_name']);
    unset($_POST['get_response_details_value']);
    unset($_POST['application_table_field']);
    unset($_POST['uniqueness']);
    
    
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $_POST['current_status'] = '0';
        $_POST['is_active'] = '0';
        $_POST['status_date'] = nowDate();
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
    } else {
        unset($_POST['submit']);
        unset($_POST['existing_image']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }
    $res = json_decode($response, 1);
  // echo "<pre>"; print_r($res); echo "</pre>";
    if($res['status'] == 'sucess'){ 
        if($res['LASTID'] == 0){
            $msgcode = 3001; //update
           // $_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_API_MASTERS");
        }
        ?>
       <script type="text/javascript">
            // document.onreadystatechange = function(){
            //     if(document.readyState == "complete") {
            //         tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            //     } 
            // }
        </script> 
    <?php  }else{
            if(isset($_SESSION['error'][$res['error_no']])){
              $dis_err = explode(",", $res['other']);
              //print_r($dis_labels); exit;
              foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
              }
              $error_message = $res['error_no'] ." : ".$_SESSION['error'][$res['error_no']]." : ".implode(", ", (array)$dis_er);
            }else{
              $error_message = $res['message'];
            }
        }
    // if($result11['LASTID'] != '0' AND $result11['LASTID']){
    //      //header("location:index.php?option=COM_LIST_API_MASTERS"); exit; 
    // }
    //echo $response;
    // print_r($response);
}
// echo deocdeId("id:9");
$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API . "?mode=getapimastersdetails&id=" . $rid;
$response = callAPI($url);
$result = json_decode($response, 1);
$details = $result['api_masters'];
$display_attributes = $result['display_attributes'];
$display_attributes_list_attributes = $result['display_attributes_list_attributes'];
if($display_attributes_list_attributes['list_attributes'] !=""){
    $list_attributes = json_decode($display_attributes_list_attributes['list_attributes'],1);
}
//echo "<pre>"; print_r($_POST); echo "</pre>";
?>
<style type="text/css">
    /* Table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        margin: 20px 0;
    }

    /* Table Header styling */
    th {
        background-color: #4099ff;
        color: white;
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    /* Table Row styling */
    tr {
        border-bottom: 1px solid #ddd;
    }

    /* Table Cell styling */
    td {
        padding: 10px;
        text-align: left;
        border: 1px solid #ddd;
    }

    /* Alternating row colors */
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    /* Hover effect for rows */
    tr:hover {
        background-color: #e0e0e0;
    }
</style>
<script type="text/javascript">
    function getApplicationTable(){
        var api_application_table = $("#api_application_table").val();
        $.ajax({
            url: "ajax.php",
            data:'page=getdisplayattributesfieldslist&table_name='+api_application_table,
            type: "POST",
            success:function(data){
                var options = "<option value=''>Select Any One</option>"; 
                var response = JSON.parse(data);
                options += response.option;
                $(".application_table_fields").html(options);
               /// gettTransactionIDRecords();
            }
        });
    }
    function buildDynamicUrl(){
        var api_code = $("#api_code").val();
        var api_type = $("#api_type").val();
        var buildsql = "";
        var domainName = window.location.origin;
        buildsql += domainName+"/tallycallapi.php?";
        buildsql += "uniquecode="+api_code;
        if(api_type=="R"){
            buildsql += "&method=post";
        }
        $(".requestparmfield").each(function() {
            let keyid = $(this).data("keyid");
            let key = $(this).val();
            if(key !=""){
                let fieldvalue = $(".requestparmvalue[data-keyid='" + keyid + "']").val();
                buildsql += "&"+key+"="+fieldvalue;
            }
        });
        //console.log("Domain Name:", buildsql);
        $("#testing_url").val(buildsql);
        $("#production_url").val(buildsql);
        $("#end_point").val(buildsql);
//requestparmfield
//requestparmvalue
    }
</script>
  <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>  
<?php } ?>
<div class="card">
    <div class="card-header">
        <h5>API Masters</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-block m-r-50 m-l-50">
        <form method="post" action="">
            <div class="row"> 
                <div class="col-md-6 form-group">
                    <label class="form-label">Service Provider</label>
                    <input name="api_service_provider" type="text" class="form-control" id="api-service-provider" value="<?php echo $details['api_service_provider']; ?>" placeholder="">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">API Name</label>
                    <input name="api_name" type="text" class="form-control" id="api-name" placeholder="" value="<?php echo $details['api_name']; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">API Code</label>
                    <input name="api_code" type="text" class="form-control" id="api_code" placeholder="" value="<?php echo $details['api_code']; ?>" onkeypress="if (event.keyCode === 32) return false;" onblur="buildDynamicUrl()">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">Unique ID</label>
                    <input name="unique_id" id="unique_id" type="text" class="form-control" placeholder="" value="<?php echo $details['unique_id']; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">Method
                        Input</label>
                    <select name="api_method" class="form-control js-example-basic-single" id="method" onchange="buildDynamicUrl()">
                        <option value="GET" <?php echo $details['api_method'] == 'GET' ? 'selected' : ''; ?>>GET</option>
                        <option value="POST" <?php echo $details['api_method'] == 'POST' ? 'selected' : ''; ?>>POST</option>
                        <option value="PUT" <?php echo $details['api_method'] == 'PUT' ? 'selected' : ''; ?>>PUT</option>
                        <option value="DELETE" <?php echo $details['api_method'] == 'DELETE' ? 'selected' : ''; ?>>DELETE</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">Results Format</label>
                    <select name="request_format" class="form-control js-example-basic-single" id="results-format" >
                        <option value="json" <?php echo $details['request_format'] == 'json' ? 'selected' : ''; ?>>JSON string</option>
                        <option value="xml" <?php echo $details['request_format'] == 'xml' ? 'selected' : ''; ?>>XML</option>
                        <option value="text" <?php echo $details['request_format'] == 'text' ? 'selected' : ''; ?>>Plain Text</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">API Type</label>
                    <select name="api_type" class="form-control js-example-basic-single" id="api_type" onchange="getAPIType()">
                        <option value="S" <?php echo $details['api_type'] == 'S' ? 'selected' : ''; ?>>Send</option>
                        <option value="R" <?php echo $details['api_type'] == 'R' ? 'selected' : ''; ?>>Receive</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">Base(Test) URL</label>
                    <input name="testing_url" id="testing_url" type="text" class="form-control" placeholder="" value="<?php echo $details['testing_url']; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label class="form-label">Production URL</label>
                    <input name="production_url" id="production_url" type="text" class="form-control" placeholder="" value="<?php echo $details['production_url']; ?>">
                </div>
                <div class="col-md-12 form-group">
                    <label class="form-label">Endpoint</label>
                    <input name="end_point" id="end_point" type="text" class="form-control" placeholder="" value="<?php echo $details['end_point']; ?>" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Request Parameters</h5>
                        </div>
                        <div class="card-block">
                            <table id="request_parameters">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <?php $mk=1;  foreach (json_decode($details['api_request_parameters'], true) as $name => $value): ?>
                                    <tr>

                                        <td>
                                            <input type="text" class="form-control request-parameter-row requestparmfield" name="get_name[]" value="<?php echo $name; ?>" onkeypress="if (event.keyCode === 32) return false;" data-keyid="<?php echo $mk; ?>" onblur="buildDynamicUrl()">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control requestparmvalue" name="get_value[]" value="<?php echo $value; ?>" data-keyid="<?php echo $mk; ?>" onblur="buildDynamicUrl()">
                                        </td>
                                    </tr>
                                <?php $mk++; endforeach; ?>
                            </table>
                            <div class="mt-2">
                                <button type="button" class="btn btn-success" onclick="addrowrequest_parameters()"> Add Rows</button>
                                <button type="button" class="btn btn-danger" onclick="deleteLastRow('request_parameters')"> Delete Row</button>

                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5>Request Header Parameters</h5>
                        </div>
                        <div class="card-block">
                            <table id="headers" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <?php foreach (json_decode($details['request_header'], true) as $name => $value): ?>
                                    <tr>
                                        <td><input type="text" class="form-control headersrow" name="get_header_name[]" value="<?php echo $name; ?>" onkeypress="if (event.keyCode === 32) return false;"></td>
                                        <td><input type="text" class="form-control" name="get_header_value[]" value="<?php echo $value; ?>"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                            <div class="mt-2">
                                <button type="button" class="btn btn-success" onclick="addrowheaderstring()"> Add Rows</button>
                                <button type="button" class="btn btn-danger" onclick="deleteLastRow('headers')"> Delete Row</button>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5>Request Body POST Parameters</h5>
                        </div>
                        <div class="card-block">
                            <table id="postField" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <?php foreach (json_decode($details['api_request_post_parameters'], true) as $name => $value): ?>
                                    <tr>
                                        <td><input type="text" class="form-control postfieldsrow" name="get_post_field_name[]" value="<?php echo $name; ?>" onkeypress="if (event.keyCode === 32) return false;"></td>
                                        <td><input type="text" class="form-control" name="get_post_field_value[]" value="<?php echo $value; ?>"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                            <div class="mt-2">
                                <button type="button" class="btn btn-success" onclick="addrowpostfieldstring()"> Add Rows</button>
                                <button type="button" class="btn btn-danger" onclick="deleteLastRow('postField')"> Delete Row</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Response</h5>
                        </div>
                        <div class="card-block">
                            <div class="form-group">
                                <label class="form-label">Transaction</label>
                                <select name="application_table" class="form-control js-example-basic-single" id="api_application_table"  onchange="getApplicationTable()">
                                     <option value="" >Select Any One</option>
                                    <?php foreach($display_attributes as $datr){ ?>
                                        <option value="<?php echo $datr['application_table_name']; ?>" <?php echo $datr['application_table_name'] == $details['application_table']? 'selected' : ''; ?>> <?php echo ucwords(str_replace('_',' ',$datr['application_table_name'])); ?> </option>
                                    <?php } ?> 
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Response Records</label>
                                <select name="response_row_type" class="form-control js-example-basic-single" id="response_row_type">
                                    <option value="" >Select Any One</option>
                                    <option value="M" <?php echo $details['response_row_type'] == 'M' ? 'selected' : ''; ?>>Multiple</option>
                                    <option value="S" <?php echo $details['response_row_type'] == 'S' ? 'selected' : ''; ?>>Single</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Response Mapping</label>
                                <table id="responseDetails" width="100%" class="mt-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <!-- <th>Value</th> -->
                                            <th>Mapping Field</th>
                                            <th>Uniqueness</th>
                                        </tr>
                                    </thead>
                                    <?php $sqo = 1; foreach(json_decode($details['api_response_details'], true) as $name => $value): ?>
                                        <tr>
                                            <td><input type="text" class="form-control responsedetailsrow" name="get_response_details_name[<?php echo $sqo; ?>]" value="<?php echo $name; ?>" id="get_response_details_name_<?php echo $sqo; ?>" <?php echo $details['api_type'] == 'S'?"readonly":""; ?> onkeypress="if (event.keyCode === 32) return false;"></td>
                                            <!-- <td><input type="text" class="form-control" name="get_response_details_value[]" value="<?php //echo $value['value']; ?>" readonly></td> -->
                                            <td>
                                                <select name="get_response_details_value[<?php echo $sqo; ?>]" class="form-control js-example-basic-single application_table_fields" onchange="getMappingField('<?php echo $sqo; ?>')" id="get_response_details_value_<?php echo $sqo; ?>">
                                                    <option value="" >Select Any One</option>
                                                    <?php foreach($list_attributes as $feild=>$datr){ ?>
                                                        <option value="<?php echo $feild; ?>" <?php echo $feild == $value['application_table_field']? 'selected' : ''; ?>> <?php echo ucwords(str_replace('_',' ',$datr['lable'])); ?> </option>
                                                    <?php } ?>
                                                </select> 
                                            </td>
                                            <td>
                                                <input class="js-primary-sm" type="checkbox" name="uniqueness[<?php echo $sqo; ?>]" <?php echo ($value['uniqueness']=='Y')?"checked":"";?> value="Y"  id="api_response_uniqueness_<?php echo $sqo; ?>">
                                            </td>
                                                
                                        </tr>
                                    <?php $sqo++; endforeach; ?>
                                </table>
                            </div>
                            <div class="mt-2 ">
                                <button type="button" class="btn btn-success" onclick="addrowresponsedetailsstring()"> Add Rows</button>
                                <button type="button" class="btn btn-danger " onclick="deleteLastRow('responseDetails')"> Delete Row</button>
                            </div>    
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top ">
                    <?php if ($rid != "") {  ?>
                        <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                            <i class="fa fa-file-text-o"></i> Update
                        </button>
                        <input name="rid" class="form-control" type="hidden" value="<?php echo $rid; ?>">
                    <?php } else {  ?>
                        <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary">
                            <i class="fa fa-file-text-o"></i> Save
                        </button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    function getAPIType(){
        var api_type = $("#api_type").val();
        if(api_type == "S"){
            $(".responsedetailsrow").prop("readonly", true);
        }else{
            $(".responsedetailsrow").prop("readonly", false);
        }
        buildDynamicUrl();
    }

    function getMappingField(rowid){
        var api_type = $("#api_type").val();
        var valuess = $("#get_response_details_value_"+rowid).val();
        if(api_type == "S"){
            $("#get_response_details_name_"+rowid).val(valuess);
            $("#get_response_details_name_"+rowid).prop("readonly", true);
        }else{
            $("#get_response_details_name_"+rowid).prop("readonly", false);
        }
    }
    var sqon = 10000;
    function addrowrequest_parameters() {
         sqon = sqon+1;
        var count = $(".request-parameter-row").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input type="text" class="form-control request-parameter-row requestparmfield" name="get_name[]" value="" onkeypress="if (event.keyCode === 32) return false;" data-keyid="'+sqon+'" onblur="buildDynamicUrl()"></td>';
        htmlRows += '<td><input type="text" class="form-control requestparmvalue" name="get_value[]" value="" data-keyid="'+sqon+'" onblur="buildDynamicUrl()"></td>';
        htmlRows += '</tr>';
        $('#request_parameters').append(htmlRows);
    }





    function addrowheaderstring() {
        var count = $(".headersrow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input type="text" class="form-control headersrow" name="get_header_name[]" value="" onkeypress="if (event.keyCode === 32) return false;"></td>';
        htmlRows += '<td><input type="text" class="form-control" name="get_header_value[]" value=""></td>';
        htmlRows += '</tr>';
        $('#headers').append(htmlRows);
    }

    function addrowpostfieldstring() {
        var count = $(".postfieldsrow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input type="text" class="form-control postfieldsrow" name="get_post_field_name[]" value="" onkeypress="if (event.keyCode === 32) return false;"></td>';
        htmlRows += '<td><input type="text" class="form-control" name="get_post_field_value[]" value=""></td>';
        htmlRows += '</tr>';
        $('#postField').append(htmlRows);
    }
    var sqo = 10000;
    function addrowresponsedetailsstring() {
        sqo = sqo+1;
        var count = $(".responsedetailsrow").length;
        var api_application_table = $("#api_application_table").val();
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input type="text" class="form-control responsedetailsrow" name="get_response_details_name['+sqo+']" value="" id="get_response_details_name_'+sqo+'" onkeypress="if (event.keyCode === 32) return false;"></td>';
      // htmlRows += '<td><input type="text" class="form-control" name="get_response_details_value[]" value="" readonly></td>';

        htmlRows += '<td>';
        htmlRows += '<select name="get_response_details_value['+sqo+']" class="form-control js-example-basic-single application_table_fields" onchange="getMappingField('+sqo+')" id="get_response_details_value_'+sqo+'"><option value="" >Select Any One</option>';



        $.ajax({
            url: "ajax.php",
            data:'page=getdisplayattributesfieldslist&table_name='+api_application_table,
            type: "POST",
            async: false, 
            success:function(data){
                var response = JSON.parse(data);
                htmlRows += response.option;
               /// gettTransactionIDRecords();
            }
        });

        htmlRows += '</select></td>';

        htmlRows += '<td><input class="js-primary-sm-'+sqo+'" type="checkbox" name="uniqueness['+sqo+']" value="Y"  id="api_response_uniqueness_'+sqo+'"></td>';
    
        htmlRows += '</tr>';
        $('#responseDetails').append(htmlRows);
        $(".js-example-basic-single").select2();
        var elemdefault = document.querySelector('.js-primary-sm-'+sqo);
        var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff',size: 'small' });

    }

    function deleteLastRow(tableId) {
        var rowCount = $('#' + tableId + ' tr').length;
        if (rowCount > 2) { // Ensures there's at least one row left (header row + one data row)
            $('#' + tableId + ' tr:last').remove();
        }
    }
</script>