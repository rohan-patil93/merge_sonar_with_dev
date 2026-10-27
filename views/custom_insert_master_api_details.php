<?php $_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submit'])) {
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
        $response_arry[$name] = $_POST['get_response_details_value'][$key];
    }
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
}
$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API . "?mode=getapimastersdetails&id=" . $rid;
$response = callAPI($url);
$result = json_decode($response, 1);
$details = $result['api_masters'];

?>
<style type="text/css">
    /* Table styling */
    table {
        width: 90%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        margin: 20px 0;
    }

    
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


    tr:hover {
        background-color: #e0e0e0;
    }
</style>
<div class="card">
    <div class="card-header">
        <h5>Basic Form Inputs</h5>
    </div>
    <div class="card-block">
        <h4 class="sub-title">Basic Inputs</h4>
        <form method="post" action="">
            <div class="row mb-3">
                <span class="form-label col-sm-2 col-form-label">Service Provider</span>
                <div class="col-sm-10">
                    <input aria-label="Api Service Provider" name="api_service_provider" type="text" class="form-control" id="api-service-provider" value="<?php echo $details['api_service_provider']; ?>" placeholder="">

                </div>
            </div>
            <div class="row mb-3">
                <span class="form-label col-sm-2 col-form-label">API Name</span>
                <div class="col-sm-10">
                    <input aria-label="Api Name" name="api_name" type="text" class="form-control" id="api-name" placeholder="" value="<?php echo $details['api_name']; ?>">

                </div>
            </div>
            <div class="row mb-3">
                <span class="form-label col-sm-2 col-form-label">API Code</span>
                <div class="col-sm-10">
                    <input aria-label="Api Code" name="api_code" type="text" class="form-control" id="api_code" placeholder="" value="<?php echo $details['api_code']; ?>">

                </div>
            </div>
            <div class="mb-3 row">
                <span class="form-label col-sm-2 col-form-label">Unique ID</span>
                <div class="col-sm-10">
                    <input aria-label="Unique" name="unique_id" type="text" class="form-control" placeholder="" value="<?php echo $details['unique_id']; ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <span class="form-label col-sm-2 col-form-label">Base(Test) URL</span>
                <div class="col-sm-10">
                    <input aria-label="Testing Url" name="testing_url" type="text" class="form-control" placeholder="" value="<?php echo $details['testing_url']; ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <span class="form-label col-sm-2 col-form-label">Production URL</span>
                <div class="col-sm-10">
                    <input aria-label="Production Url" name="production_url" type="text" class="form-control" placeholder="" value="<?php echo $details['production_url']; ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <span class="form-label col-sm-2 col-form-label">Endpoint</span>
                <div class="col-sm-10">
                    <input aria-label="End Point" name="end_point" type="text" class="form-control" placeholder="" value="<?php echo $details['end_point']; ?>">
                </div>
            </div>
            <div class="mb-3 row">
                <span class="form-label col-sm-2 col-form-label">Method
                    Input</span>
                <div class="col-sm-10">
                    <select aria-label="Api Method" name="api_method" class="form-control js-example-basic-single" id="method">
                        <option value="GET" <?php echo $details['api_method'] == 'GET' ? 'selected' : ''; ?>>GET</option>
                        <option value="POST" <?php echo $details['api_method'] == 'POST' ? 'selected' : ''; ?>>POST</option>
                        <option value="PUT" <?php echo $details['api_method'] == 'PUT' ? 'selected' : ''; ?>>PUT</option>
                        <option value="DELETE" <?php echo $details['api_method'] == 'DELETE' ? 'selected' : ''; ?>>DELETE</option>

                    </select>
                </div>
            </div>
            <div class="mb-3 row">
                <span class="form-label col-sm-2 col-form-label">Results Format</span>
                <div class="col-sm-10">
                    <select aria-label="Request Format" name="request_format" class="form-control js-example-basic-single" id="results-format">
                        <option value="json" <?php echo $details['request_format'] == 'json' ? 'selected' : ''; ?>>JSON string</option>
                        <option value="xml" <?php echo $details['request_format'] == 'xml' ? 'selected' : ''; ?>>XML</option>
                        <option value="text" <?php echo $details['request_format'] == 'text' ? 'selected' : ''; ?>>Plain Text</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <span class="form-label col-sm-2  col-form-label">Request Parameters</span>
                <div class="col-sm-10">
                    <table id="request_parameters">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <?php foreach (json_decode($details['api_request_parameters'], true) as $name => $value): ?>
                            <tr>

                                <td>
                                    <input aria-label="Get Name[]" type="text" class="form-control request-parameter-row" name="get_name[]" value="<?php echo $name; ?>">
                                </td>
                                <td>
                                    <input aria-label="Get Value[]" type="text" class="form-control" name="get_value[]" value="<?php echo $value; ?>">
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success" onclick="addrowrequest_parameters()"> Add Rows</button>
                        <button type="button" class="btn btn-danger" onclick="deleteLastRow('request_parameters')"> Delete Row</button>

                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <span class="form-label col-sm-2 col-form-label">Headers</span>
                <div class="col-sm-10">
                    <table id="headers">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <?php foreach (json_decode($details['request_header'], true) as $name => $value): ?>
                            <tr>
                                <td><input aria-label="Get Header Name[]" type="text" class="form-control headersrow" name="get_header_name[]" value="<?php echo $name; ?>"></td>
                                <td><input aria-label="Get Header Value[]" type="text" class="form-control" name="get_header_value[]" value="<?php echo $value; ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success" onclick="addrowheaderstring()"> Add Rows</button>
                        <button type="button" class="btn btn-danger" onclick="deleteLastRow('headers')"> Delete Row</button>

                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <span class="form-label col-sm-2 col-form-label">Body POST Fields</span>
                <div class="col-sm-10">
                    <table id="postField">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <?php foreach (json_decode($details['api_request_post_parameters'], true) as $name => $value): ?>
                            <tr>
                                <td><input aria-label="Get Post Field Name[]" type="text" class="form-control postfieldsrow" name="get_post_field_name[]" value="<?php echo $name; ?>"></td>
                                <td><input aria-label="Get Post Field Value[]" type="text" class="form-control" name="get_post_field_value[]" value="<?php echo $value; ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <div class="mt-2">
                        <button type="button" class="btn btn-success" onclick="addrowpostfieldstring()"> Add Rows</button>
                        <button type="button" class="btn btn-danger" onclick="deleteLastRow('postField')"> Delete Row</button>

                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <span class="form-label col-sm-2 col-form-label">Response Details</span>
                <div class="col-sm-10">
                    <table id="responseDetails">
                        <thead>
                            <tr>
                                <th scope="col">Name</th>
                                <th scope="col">Value</th>
                            </tr>
                        </thead>
                        <?php foreach (json_decode($details['api_response_details'], true) as $name => $value): ?>
                            <tr>
                                <td><input aria-label="Get Response Details Name[]" type="text" class="form-control responsedetailsrow" name="get_response_details_name[]" value="<?php echo $name; ?>"></td>
                                <td><input aria-label="Get Response Details Value[]" type="text" class="form-control" name="get_response_details_value[]" value="<?php echo $value; ?>"></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <div class="mt-2 ">
                        <button type="button" class="btn btn-success" onclick="addrowresponsedetailsstring()"> Add Rows</button>
                        <button type="button" class="btn btn-danger " onclick="deleteLastRow('responseDetails')"> Delete Row</button>

                    </div>
                </div>
            </div>
            <div class="card-footer border-top text-center">
                <?php if ($rid != "") {  ?>
                    <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                        <i class="fa fa-file-text-o"></i> Update Data
                    </button>
                    <input name="rid" class="form-control" type="hidden" value="<?php echo $rid; ?>">
                <?php } else {  ?>
                    <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary">
                        <i class="fa fa-file-text-o"></i> Save Data
                    </button>
                <?php } ?>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    function addrowrequest_parameters() {
        var count = $(".request-parameter-row").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input aria-label="Get Name[" type="text" class="form-control request-parameter-row" name="get_name[' + count + ']" value=""></td>';
        htmlRows += '<td><input aria-label="Get Value[" type="text" class="form-control" name="get_value[' + count + ']" value=""></td>';
        htmlRows += '</tr>';
        $('#request_parameters').append(htmlRows);
    }

    function addrowheaderstring() {
        var count = $(".headersrow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input aria-label="Get Header Name[" type="text" class="form-control headersrow" name="get_header_name[' + count + ']" value=""></td>';
        htmlRows += '<td><input aria-label="Get Header Value[" type="text" class="form-control" name="get_header_value[' + count + ']" value=""></td>';
        htmlRows += '</tr>';
        $('#headers').append(htmlRows);
    }

    function addrowpostfieldstring() {
        var count = $(".postfieldsrow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input aria-label="Get Post Field Name[" type="text" class="form-control postfieldsrow" name="get_post_field_name[' + count + ']" value=""></td>';
        htmlRows += '<td><input aria-label="Get Post Field Value[" type="text" class="form-control" name="get_post_field_value[' + count + ']" value=""></td>';
        htmlRows += '</tr>';
        $('#postField').append(htmlRows);
    }

    function addrowresponsedetailsstring() {
        var count = $(".responsedetailsrow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><input aria-label="Get Response Details Name[" type="text" class="form-control responsedetailsrow" name="get_response_details_name[' + count + ']" value=""></td>';
        htmlRows += '<td><input aria-label="Get Response Details Value[" type="text" class="form-control" name="get_response_details_value[' + count + ']" value=""></td>';
        htmlRows += '</tr>';
        $('#responseDetails').append(htmlRows);
    }

    function deleteLastRow(tableId) {
        var rowCount = $('#' + tableId + ' tr').length;
        if (rowCount > 2) {
            $('#' + tableId + ' tr:last').remove();
        }
    }
</script>
