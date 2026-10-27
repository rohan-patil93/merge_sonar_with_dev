<?php
$_SESSION['apply_js']['multiselest'] = '1';
if(isset($_POST['submitreconcilation'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
      $url = API."?mode=insertreconciliations&method=post";
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
            $rid = deocdeId("id:".$response['LASTID']);
            header("location:index?option=COM_LIST_RECONCILIATIONS");
        }
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
    if($_SESSION['insert_msg'] !=""){
        $msgcode = $_SESSION['insert_msg'];
        $_SESSION['insert_msg'] = "";
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php

    }

$amounttype = array(array('label'=>'Paid','value'=>'P'),array('label'=>'Recived','value'=>'R'),array('label'=>'Both','value'=>'B'));
$amountcondition = array(array('value'=>'L','label'=>'Is Lower Than'),array('value'=>'U','label'=>'Upper Than'),array('value'=>'E','label'=>'Equal'));
$transamounttype = array(array('value'=>'P','label'=>'Percentage'),array('value'=>'A','label'=>'Amount'));
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$cusurl = API."?mode=getreconciliations&rid=".$rid;
$apicall = callAPI($cusurl);
$result = json_decode($apicall,1);
$reconciliationtype = $result['reconciliationtype'];
$journals = $result['journals'];
$chart_of_accounts = $result['chart_of_accounts'];
$transactiontype = $result['transactiontype'];
$vendors= $result['vendors'];
$customers= $result['customers'];
$reconciliations = $result['reconciliations'];
if($reconciliations['reconciliation_type'] == "BTG"){
        $rts = "display:none";
}elseif($reconciliations['reconciliation_type'] == "RTS"){

}elseif($reconciliations['reconciliation_type'] == "RTM"){
    $counterpartentry = "display:none";
}

?>
<script type="text/javascript">
    function addMorerows(){
        var count = $(".itemRow").length;
         count++;
        var html = '';
        html = '<td> <select aria-label="Cont[]" name="cont[]" class="form-control form-select js-example-basic-single" ><option value="0">Select Any One </option><?php foreach($chart_of_accounts as $coa){ ?><option  value="<?php echo $coa['account_code']; ?>"><?php echo $coa['account_name']; ?></option><?php
} ?></select></td>';
        html +='<td><input aria-label="Cont[" type="number" name="cont['+count+'][value]" class="form-control text-right"></td>';
        html +='<td><input aria-label="Cont[" type="number" name="cont['+count+'][days]" class="form-control text-right"></td>';
        html +='<td><div class="form-group cursor-pointer"  onclick="deletRow('+count+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-trash" ></i> </div></td></tr>';
        $("#addmoreinvoice").append(html);

        $(".js-example-basic-single").select2();


        var elemdefault = document.querySelector('.js-primary-sm-e-'+count);
        var elemdefaultq = document.querySelector('.js-primary-sm-m-'+count);
        var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff',size: 'small' });
        var switchery = new Switchery(elemdefaultq, { color: '#4099ff', jackColor: '#fff',size: 'small' });

    }
    function deletRow(rowid){
        $("#delete_row_"+rowid).remove();

    }
    function getReconciliationsType(valu){
        if(valu == "BTG"){
            $(".class_rts").hide();
            $("#counterpartentry").show('slow');
        }else if(valu == "RTS"){
            $(".class_rts").show('slow');
            $("#counterpartentry").show('slow');
        }else if(valu == "RTM"){
            $("#counterpartentry").hide();
            $(".class_rts").show('slow');
        }
    }
</script>
<div class="card">
    <div class="card-header">
        <h5>Reconciliations</h5>
        <div class="card-header-right table-card-header">
            <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
        </div>
    </div>
    <div class="card-body p-l-50 p-r-50">
        <form method="post">
            <div class="row">
                <input type="hidden" name="depreciation_method"  id="depreciation_method" value="SL">
                <div class="form-group col-lg-6">
                    <span class="form-label">Reconciliations Name</span>
                    <input aria-label="Reconciliation Name" class="form-control"  type="text"  name="reconciliation_name"  value="<?php echo $reconciliations['reconciliation_name']; ?>" required>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <span class="form-label">Type</span>
                            <div class="form-radio">
                                <?php foreach($reconciliationtype as $codeval=>$codelabel){ ?>
                                <div class="radio radio-inline d-block">
                                      <label class="form-label">
                                        <input aria-label="Reconciliation Type" type="radio" name="reconciliation_type" value="<?php echo $codeval; ?>"  onclick="getReconciliationsType(this.value)"  <?php echo $reconciliations['reconciliation_type'] ==$codeval?"checked":""; ?> onkeydown="">
                                        <i class="helper"></i><?php echo $codelabel; ?>
                                      </label>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <span class="form-label d-block">Auto Validate</span>
                            <input aria-label="Auto Validate" class="js-primary-sm" type="checkbox" name="auto_validate" <?php echo $reconciliations['auto_validate'] =='0'?"checked":""; ?> value="0">
                        </div>
                        <div class="form-group">
                            <span class="form-label">Search Month Limit</span>
                            <input aria-label="Search Month Limit" class="form-control"  type="text" name="search_month_limit"  value="<?php echo $reconciliations['search_month_limit']; ?>" >
                        </div>

                    </div>
                </div>
                <div class="row">


                            <div class="card mb-2">
                                <div class="card-header">
                                    <h5> Bank Transaction Condition </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="form-group col-lg-4">
                                            <?php $journal_availability = json_decode($reconciliations['journal_availability'],1);?>
                                            <span class="form-label">Journal Availability</span>
                                            <select aria-label="Journal Availability[]" class="form-control form-select js-example-basic-multiple" name="journal_availability[]" multiple>
                                                <?php foreach ($journals as $jour) {  ?>
                                                    <option  <?php if(in_array($jour['id'], (array)$journal_availability)){ ?> selected  <?php
} ?> value = <?php echo $jour['id']; ?>   > <?php echo $jour['journal_name']; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2 class_rts" style="<?php echo $rts; ?>">
                                            <span class="form-label">Amount Type</span>
                                             <select aria-label="Amount Type" class="form-control form-select js-example-basic-single"  name="amount_type">
                                                <option value=""> Select Any One </option>
                                                <?php foreach ($amounttype as $at) {  ?>
                                                    <option  <?php if($reconciliations['amount_type'] == $at['value']){ ?> selected  <?php
} ?> value = <?php echo $at['value']; ?>   > <?php echo $at['label']; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2 class_rts" style="<?php echo $rts; ?>">
                                            <span class="form-label">Transaction Type</span>
                                             <select aria-label="Transaction Type" class="form-control form-select js-example-basic-single"  name="transaction_type">
                                                <option value=""> Select Any One </option>
                                                <?php foreach ($transactiontype as $keyval=>$lable) {  ?>
                                                    <option  value="<?php echo $keyval; ?>" <?php if($reconciliations['transaction_type'] == $keyval){ ?> selected  <?php
} ?> > <?php echo $lable; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2 class_rts" style="<?php echo $rts; ?>">
                                            <span class="form-label">Amount Condition</span>
                                             <select aria-label="Amount Condition" class="form-control form-select js-example-basic-single" name="amount_condition">
                                                <option value=""> Select Any One </option>
                                                <?php foreach ($amountcondition as $ac) {  ?>
                                                    <option  <?php if($reconciliations['amount_condition'] == $ac['id']){ ?> selected  <?php
} ?> value = <?php echo $ac['value']; ?>   > <?php echo $ac['label']; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-2 class_rts" style="<?php echo $rts; ?>">
                                            <span class="form-label">Amount</span>
                                            <input aria-label="Amount" class="form-control text-right"  type="text"  id="amount" name="amount"  value="<?php echo $reconciliations['amount']; ?>" >
                                        </div>
                                        <div class="form-group col-lg-6 class_rts" style="<?php echo $rts; ?>">
                                            <?php $vendor_ids = json_decode($reconciliations['vendor_ids'],1); ?>
                                            <span class="form-label">Vendor</span>
                                            <select aria-label="Vendor Ids[]" class="form-control form-select js-example-basic-multiple" name="vendor_ids[]" multiple>
                                                <?php foreach ($vendors as $ven) {  ?>
                                                    <option  value="<?php echo $ven['id']; ?>" <?php if(in_array($ven['id'], (array)$vendor_ids)){ ?> selected  <?php
} ?>> <?php echo $ven['display_name']; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 class_rts" style="<?php echo $rts; ?>">
                                            <?php $customer_ids = json_decode($reconciliations['customer_ids'],1);?>
                                            <span class="form-label">Customer</span>
                                            <select aria-label="Customer Ids[]" class="form-control form-select js-example-basic-multiple" name="customer_ids[]" multiple>
                                                <?php foreach ($customers as $cust) {  ?>
                                                    <option value ="<?php echo $cust['id']; ?>"    <?php if(in_array($cust['id'], (array)$customer_ids)){ ?> selected  <?php
} ?>> <?php echo $cust['display_name']; ?>
                                                </option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-4 class_rts" style="<?php echo $rts; ?>">
                                            <span class="form-label d-block">Same Currency</span>
                                            <input aria-label="Same Currency" class="js-primary-sm" type="checkbox" name="same_currency" <?php echo $reconciliations['same_currency'] =='0'?"checked":""; ?> value="0">
                                        </div>


                                    </div>
                                    <div class="row" id="counterpartentry" style="<?php echo $counterpartentry; ?>">
                                         <table class="table table-hover m-b-0" style="font-size:13px;"  id="addmoreinvoice">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 50%;">Account</th>
                                    <th scope="col" style="width: 25%;">Amount Type</th>
                                    <th scope="col" style="width: 20%;">Amount</th>
                                    <th scope="col" style="width: 5%;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;
                                $counterpart_entry = json_decode($reconciliations['counterpart_entry'],1);
                                if(count((array)$counterpart_entry) == 0){
                                    $counterpart_entry[] = array();
                                }
                                    foreach($counterpart_entry as $row){  ?>

                                <tr class="itemRow" id="delete_row_<?php echo $i; ?>">
                                    <td>
                                        <select aria-label="Chart Of Account []" name="chart_of_account_id[]" class="form-control form-select js-example-basic-single" >
                                            <option value="">Select Any One </option>
                                            <?php foreach($chart_of_accounts as $coa){ ?>
                                            <option value="<?php echo $coa['account_code']; ?>" <?php echo $row['chart_of_account_id']==$coa['account_code']?"selected":""; ?>><?php echo $coa['account_name']; ?></option>
                                        <?php } ?>

                                        </select>
                                    </td>
                                    <td>
                                        <select aria-label="Counter Part Amount Type[]" name="counter_part_amount_type[]" class="form-control form-select js-example-basic-single" >
                                            <option value="">Select Any One </option>
                                            <?php foreach($transamounttype as $tat){ ?>
                                            <option  value="<?php echo $tat['value']; ?>" <?php echo $row['counter_part_amount_type']==$tat['value']?"selected":""; ?>><?php echo $tat['label']; ?></option>
                                        <?php } ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input aria-label="Counter Part Amount[]" type="number" name="counter_part_amount[]" class="form-control text-right" value="<?php echo $row['counter_part_amount'];  ?>" >
                                    </td>
                                    <td>
                                        <div class="form-group cursor-pointer" onclick="deletRow('<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                            <i class="ti-trash"  ></i>
                                        </div>
                                    </td>
                                </tr>
                                <?php $i++;
} ?>
                            </tbody>
                        </table>
                        <div class="mt-3">
                            <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" id="addmore_btn" onclick="addMorerows()"><i class="icofont icofont-plus icon-white "></i> Add More</button>
                        </div>
                    </div>
                </div>
            </div>

                </div>
            </div>
            <div class="card-footer">
                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submitreconcilation" value="submitreconcilation"> Submit</button>
            </div>
        </form>
    </div>
</div>
