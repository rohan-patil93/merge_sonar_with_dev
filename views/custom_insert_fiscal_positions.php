<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submitfiscalpositions'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertfiscalpositions&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
            $_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
               header("location:index?option=COM_LIST_FISCAL_POSITIONS");
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }

}
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getfiscalpositions&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$countries = $result['countries'];
$fiscal_positions = $result['fiscal_positions'];
$tax_authorities = $result['tax_authorities'];
$chart_of_accounts = $result['chart_of_accounts'];
$fiscal_positions_mappings = $result['fiscal_positions_mappings'];
foreach($fiscal_positions_mappings as $fpm){
    if($fpm['fiscal_type'] == 'T'){
        $taxmapping[] = $fpm;
    }elseif($fpm['fiscal_type'] == 'A'){
        $accountmappping[] = $fpm;
    }
}

?>

<script type="text/javascript">
    var maxrow = 10000;
    function addMore(type){
        var html = '';

        if(type == 'T'){
            html +='<div class="row mt-2" id="delete_row__2"+maxrow+'"><input type="hidden" class="form-control" name="fiscal_type[]" value="T"><input type="hidden" class="form-control" name="srid[]" value=""><div class="col-lg-5 w-full"><select aria-label="Get Type[]" name="get_type[]" class="form-control form-select js-example-basic-single" required><option value="">Select Any One </option><?php foreach($tax_authorities as $ta) {  ?><option value="<?php echo $ta['id']; ?>"><?php echo $ta['tax_name'];?></option><?php
} ?></select></div>';
            html +='<div class="col-lg-6 w-full"><select aria-label="Apply Type[]" name="apply_type[]" class="form-control form-select js-example-basic-single" required><option value="">Select Any One </option><?php foreach($tax_authorities as $ta) {  ?><option value="<?php echo $ta['id']; ?>" ><?php echo $ta['tax_name'];?></option><?php
} ?></select></div><div class="form-group col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRow('+maxrow+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div></div>';

            $("#addmoretax").append(html);
        }else{
            html +='<div class="row mt-2" id="delete_row__3"+maxrow+'"><input type="hidden" class="form-control" name="fiscal_type[]" value="A"><input type="hidden" class="form-control" name="srid[]" value=""><div class="col-lg-5 w-full"><select aria-label="Get Type[]" name="get_type[]" class="form-control form-select js-example-basic-single" required><option value="">Select Any One </option><?php foreach($chart_of_accounts as $coa) {  ?><option value="<?php echo $coa['id']; ?>" ><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option><?php
} ?></select></div>';
            html +='<div class="col-lg-6 w-full"><select aria-label="Apply Type[]" name="apply_type[]" class="form-control form-select js-example-basic-single" required><option value="">Select Any One </option><?php foreach($chart_of_accounts as $coa) {  ?><option value="<?php echo $coa['id']; ?>"><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option><?php
} ?></select></div><div class="form-group col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRow('+maxrow+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div></div>';

            $("#addmoreaccount").append(html);
        }
        $(".js-example-basic-single").select2();
    }
    function deletRow(rowid){
        $("#delete_row_"+rowid).remove();

    }
</script>

<form method="post">
    <div class="card">
        <div class="card-header">
            <h5>Fiscal Positions</h5>
            <div class="card-header-right table-card-header">
                <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
            </div>
        </div>
        <div class="card-body p-l-50 p-r-50">
            <div class="row">
                <div class="form-group col-lg-6">
                    <span class="form-label">Fiscal Positions Name <span class="text-danger">*</span></span>
                    <input aria-label="Fiscal Positions Name" type="text" class="form-control text-capitalize" name="fiscal_positions_name" value="<?php echo $fiscal_positions['fiscal_positions_name']; ?>" required>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Country <span class="text-danger">*</span></span>
                    <select aria-label="Country"  name="country_id" class="form-control form-select js-example-basic-single" required>
                        <option value="">Select Any One </option>
                        <?php foreach($countries as $countr) {  ?>
                            <option value="<?php echo $countr['id']; ?>" <?php echo $countr['id'] ==$fiscal_positions['country_id']?"selected":""; ?>><?php echo $countr['country_name'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

        <div class="row distribution" <?php echo $tax_authorities['tax_computation_id']!='1'?'':'style="display:none"';?>>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Tax Mappings</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-5 f-w-700">tax On Product</div>
                        <div class="col-lg-6 f-w-700">Tax To Apply</div>
                    </div>
                    <div id="addmoretax">
                        <?php $mk=1;
                        if(count((array)$taxmapping) =='0'){
                            $taxmapping[] = array();
                        }
                        foreach($taxmapping as $di){ ?>
                            <div class="row mt-2" id="delete_row_<?php echo $mk; ?>">
                                <input type="hidden" class="form-control" name="fiscal_type[]" value="T">
                                <input type="hidden" class="form-control" name="srid[]" value="<?php echo $di['id']; ?>">
                                <div class="col-lg-5 w-full">
                                    <select aria-label="Get Type[]" name="get_type[]" class="form-control form-select js-example-basic-single" required>
                                        <option value="">Select Any One </option>
                                        <?php foreach($tax_authorities as $ta) {  ?>
                                            <option value="<?php echo $ta['id']; ?>" <?php echo $ta['id']==$di['get_type']?"selected":""; ?>><?php echo $ta['tax_name'];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 w-full">
                                    <select aria-label="Apply Type[]" name="apply_type[]" class="form-control form-select js-example-basic-single" required>
                                        <option value="">Select Any One </option>
                                        <?php foreach($tax_authorities as $ta) {  ?>
                                            <option value="<?php echo $ta['id']; ?>" <?php echo $ta['id']==$di['apply_type']?"selected":""; ?>><?php echo $ta['tax_name'];?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-1"><i class="ti-trash"  onclick="deletRow('<?php echo $mk; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i> </div>
                            </div>
                        <?php $mk++;
} ?>
                    </div>

                    <div class="mt-3">
                        <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" id="addmore_btn" onclick="addMore('T')"><i class="icofont icofont-plus icon-white "></i> Add More</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Account Mappings</h5>
                    <div class="card-header-right table-card-header">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-5 f-w-700">Account On Product</div>
                        <div class="col-lg-6 f-w-700">Account To Use</div>
                    </div>
                    <div id="addmoreaccount">
                        <?php  $mc=1000;
                        $itotal = 0;
                        if(count((array)$accountmappping) =='0'){
                             $accountmappping[] = array();
                        }
                        foreach($accountmappping as $df){  $itotal +=$di['tax_rate'];?>

                            <div class="row mt-2" id="delete_row_<?php echo $mc; ?>">
                                <input type="hidden" class="form-control" name="fiscal_type[]" value="A">
                                <input type="hidden" class="form-control" name="srid[]" value="<?php echo $df['id']; ?>">
                                <div class="col-lg-5 w-full">
                                    <select aria-label="Get Type[]" name="get_type[]" class="form-control form-select js-example-basic-single" required>
                                        <option value="">Select Any One </option>
                                        <?php foreach($chart_of_accounts as $coa) {  ?>
                                            <option value="<?php echo $coa['id']; ?>" <?php echo $df['get_type'] ==$coa['id']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 w-full">
                                    <select aria-label="Apply Type[]" name="apply_type[]" class="form-control form-select js-example-basic-single" required>
                                        <option value="">Select Any One </option>
                                        <?php foreach($chart_of_accounts as $coa) {  ?>
                                            <option value="<?php echo $coa['id']; ?>" <?php echo $df['apply_type'] ==$coa['id']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-1"><i class="ti-trash"  onclick="deletRow('<?php echo $mc; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i> </div>
                            </div>
                        <?php $mc++;
} ?>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" id="addmore_btn_2" onclick="addMore('A')"><i class="icofont icofont-plus icon-white "></i> Add More</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div>
                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="btn btn-primary waves-effect waves-light" name="submitfiscalpositions"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
            </div>
        </div>
    </div>
</form>
