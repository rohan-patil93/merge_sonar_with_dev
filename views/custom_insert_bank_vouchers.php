<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submitjournal'])){
      $_POST['company_id'] = $_SESSION['company_id'];
      $_POST['user_id'] = $_SESSION['userid'];
      $url = API."?mode=insertbankvouchers&method=post";
      $response1 = file_post_contents($url, $_POST, 'insert');
      $res = json_decode($response1,1);
      $response = $res['res'];
      if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_BANK_VOUCHERS");
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
$url1 = API."?mode=getbankvouchers&rid=".$rid."&tt=".$_GET['TT'];
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$chart_of_accounts = $result['chart_of_accounts'];
$currencies = $result['currencies'];
$bank_accounts = $result['bank_accounts'];
$partylist = $result['partylist'];
$journal_vouchers = $result['journal_vouchers'];
$jv_items = $result['jv_items'];
$vouchar_type = $result['vouchar_type'];
$cost_centers = $result['cost_centers'];
$departments = $result['departments'];
$locations =$result['locations'];
?>
<script type="text/javascript">
    function itemChange(item_id, id){
        $.ajax({
        url: "ajax.php",
        data:'page=chartofaccount&item_id='+item_id,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
               document.getElementById('particulars_'+id).value=obj.account_name;
               document.getElementById('account_name_'+id).value=obj.account_name;
            document.getElementById('account_code_'+id).value=obj.account_code;
            document.getElementById('account_type_'+id).value=obj.account_type;
        },
        error:function (){}
        });
    }
    function checkdebitandcredit(){
        var grandtotal = 0;
        var debittotal = 0;
        var credittotal = 0;
        var difference = 0;
        $('.all_amount').each( function() {
            if($(this).val() !=""){
                debittotal = parseFloat(debittotal)+parseFloat($(this).val());
            }
        });
        $("#debit_amount_lable").val(debittotal);
        $("#total_amount_lable").val(debittotal);

        if(debittotal != "0" && debittotal !=""){
            $(".submit_btn").removeClass('disabled');
            $(".submit_btn").removeClass('btn-danger');
        }else{
            $(".submit_btn").addClass('disabled');
            $(".submit_btn").addClass('btn-danger');
        }
    }
    var count = 100;
    function addMoreRow(){
        count = count+1;
        var rowid = "jvrow_"+count;
        var htmlRows = '';
        htmlRows += '<tr id="'+rowid+'">';

        htmlRows += '<td id="other_'+count+'" class="p-2"><select aria-label="Chart Of Account []" class="form-select js-example-basic-single" name="chart_of_account_id[]" id="productCode_'+count+'" onchange="itemChange(this.value,'+count+')" required><option value="">Select Any One </option><?php foreach($chart_of_accounts as $coa) {  ?><option value="<?php echo $coa['id']; ?>"><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option><?php
} ?></select><input type="hidden" name="account_code[]" id="account_code_'+count+'" class="form-control" value="" ><input type="hidden" name="account_name[]" id="account_name_'+count+'" class="form-control" value=""><input type="hidden" name="jv_item_id[]" class="form-control" value="" ><input type="hidden" name="account_type[]" id="account_type_'+count+'" class="form-control" value=""><textarea aria-label="Particulars[]"  name="particulars[]" id="particulars_'+count+'"  class="form-control mt-2"></textarea></td>';


        htmlRows += '<td class="p-2"><select aria-label="Party []" class="form-control form-select js-example-basic-single"  name= "party_id[]"  ><option value=""> Select Any Name  </option> <?php foreach ($partylist as $transtab => $parttable){ foreach($parttable as $party){?><option value ="<?php echo $party['id']."||".$transtab;  ?>" > <?php echo trim(str_replace(["\n", "\r"], '', $party['display_name']),)." (".$transtab.")"; ?> </option><?php
}
} ?> </select></td> ';

         htmlRows += '<td class="p-2"><select aria-label="Transaction Location []" class="form-control form-select js-example-basic-single"  name= "transaction_location_id[]"  ><option value="">Select Any Name</option><?php foreach($locations as $loc){?><option value ="<?php echo $loc['id'];  ?>"> <?php echo trim(str_replace(["\n", "\r"], '', $loc['location_name'])); ?> </option><?php
} ?></select></td>';

    htmlRows += '<td class="p-2"><select aria-label="Department []" class="form-control form-select js-example-basic-single"  name= "department_id[]"  ><option value="0">Select Any Name</option><?php foreach($departments as $dep){?><option value ="<?php echo $dep['id'];  ?>" > <?php echo trim(str_replace(["\n", "\r"], '', $dep['department_name'])); ?> </option><?php
}  ?> </select</td>';

    htmlRows += '<td class="p-2"><select aria-label="Cost Center []" class="form-control form-select js-example-basic-single"  name= "cost_center_id[]"  ><option value="">Select Any Name</option><?php foreach($cost_centers as $cc){?><option value ="<?php echo $cc['id'];  ?>"> <?php echo trim(str_replace(["\n", "\r"], '', $cc['cost_center_name'])); ?> </option><?php
}  ?></select></td>';

        htmlRows += '<td class="p-2"><input aria-label="Amount[]" type="text" name="amount[]" onblur="checkdebitandcredit()" id="debit_'+count+'" class="form-control price amount all_amount text-right" value="" autocomplete="off"></td>';

       htmlRows += '<td class="p-2"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRow(\''+rowid+'\')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></td>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
        $('.tooltip').remove();
         $('[data-bs-toggle="tooltip"]').tooltip();
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
    }
    function deletRow(id){
        $("#"+id).remove();
        $('.tooltip').remove();
        checkdebitandcredit();
    }
    function viewJournalVoucher(rid){
        jQuery.ajax({
            url: "views/custom_view_journal_vouchers_popup.php",
            data: "rid="+rid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
            }
        });
    }
</script>
<div class="card">
      <div class="card-header">
        <h5>Bank Voucher</h5>
        <div class="card-header-right table-card-header">
              <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
        </div>
      </div>
      <form method="post" class="<?php echo $journal_vouchers['current_status'] == ST_APPROVED?"event_disabled":""; ?>">
      <div class="card-body p-l-50 p-r-50">
        <div class="row">
            <div class="form-group col-lg-4">
                  <span class="form-label">Transaction Number <span class="text-danger">*</span></span>
                  <input aria-label="Transaction Number" type="text" class="form-control" name="transaction_number" id="transaction_number" value="<?php echo $journal_vouchers['transaction_number']; ?>" readonly>
            </div>
            <div class="form-group col-lg-4">
                  <span class="form-label">Transaction Date <span class="text-danger">*</span></span>
                  <input aria-label="Transaction Date" type="text" class="form-control datepicker" name="transaction_date" id="transaction_date" value="<?php echo fromDate($journal_vouchers['transaction_date']); ?>" required>
                <input type="hidden" class="form-control datepicker" name="old_transaction_date" id="transaction_date_2" value="<?php echo fromDate($journal_vouchers['transaction_date']); ?>">
            </div>
            <div class="form-group col-lg-4">
                  <span class="form-label">Currency <span class="text-danger">*</span></span>
                  <select aria-label="Currency" name="currency_id" class="form-control form-select js-example-basic-single"><option value="">Select Any One </option>
                    <?php foreach($currencies as $cur) {  ?>
                      <option value="<?php echo $cur['id']; ?>" <?php echo $cur['id'] ==$journal_vouchers['currency_id']?"selected":""; ?> ><?php echo $cur['currency_name']."(".$cur['currency_short_code']." - ".$cur['currency_symbol'].")";?></option>
                    <?php } ?>
                  </select>
            </div>
            <div class="form-group col-lg-4">
                  <span class="form-label">Reference Bill Number </span>
                  <input aria-label="Doc Ref No" type="text" class="form-control " name="doc_ref_no" id="doc_ref_no" value="<?php echo $journal_vouchers['doc_ref_no']; ?>">
            </div>
            <div class="form-group col-lg-4">
                  <span class="form-label">Reference Bill Date</span>
                  <input aria-label="Doc Ref Date" type="text" class="form-control datepicker" name="doc_ref_date" id="doc_ref_date" value="<?php echo fromDate($journal_vouchers['doc_ref_date']); ?>" >
            </div>

            <div class="form-group col-lg-4">
                  <span class="form-label">Bank <span class="text-danger">*</span></span>
                <input type="hidden" name="old_bank_account_code" value="<?php echo $journal_vouchers['bank_account_code']; ?>">
                  <select aria-label="Bank Account Code" name="bank_account_code" class="form-control form-select js-example-basic-single" required>
                    <option value="">Select Any One </option>
                    <?php foreach($bank_accounts as $dmt) {  ?>
                      <option value="<?php echo $dmt['account_code']; ?>" <?php echo $dmt['account_code'] ==$journal_vouchers['bank_account_code']?"selected":""; ?>><?php echo $dmt['bank_account_name']." (".$dmt['account_code'].")";?></option>
                    <?php } ?>
                  </select>
            </div>
            <div class="form-group col-lg-4">
                  <span class="form-label">Voucher Type <span class="text-danger">*</span></span>
                <input type="hidden" name="old_voucher_type" value="<?php echo $journal_vouchers['voucher_type']; ?>">
                 <div class="form-radio">
                     <?php foreach($vouchar_type as $key=>$valu){ ?>
                      <div class="radio radio-inline">
                        <label class="form-label">
                        <input aria-label="Voucher Type" type="radio" name="voucher_type"  value="<?php echo $key; ?>" <?php echo ($key ==$journal_vouchers['voucher_type'] || ($journal_vouchers['voucher_type']=="" && $key == 'PV'))?"checked":""; ?>>
                            <i class="helper"></i><?php echo $valu; ?>
                        </label>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
<?php foreach ($jv_items as $tab2) { ?>
      <input type="hidden" name="old_account_code[]" class="form-control" value="<?php echo $tab2['account_code']; ?>" >
           <input type="hidden" name="old_jv_item_id[]" class="form-control" value="<?php echo $tab2['id']; ?>" >
           <input type="hidden" name="old_account_name[]" class="form-control" value="<?php echo $tab2['account_name']; ?>">
           <input type="hidden" name="old_account_type[]" class="form-control" value="<?php echo $tab2['account_type']; ?>">
           <input type="hidden" name="old_transaction_location_id[]" class="form-control" value="<?php echo $tab2['transaction_location_id']; ?>">

           <input type="hidden" name="old_department_id[]" class="form-control" value="<?php echo $tab2['department_id']; ?>">

           <input type="hidden" name="old_cost_center_id[]" class="form-control" value="<?php echo $tab2['cost_center_id']; ?>">

           <input type="hidden" name="old_debit_amount[]" class="form-control" value="<?php echo $tab2['debit_amount']; ?>">
           <input type="hidden" name="old_credit_amount[]" class="form-control" value="<?php echo $tab2['credit_amount']; ?>">

    <?php } ?>


        <div class="table-responsive mt-3">
            <table class="table table-hover"  id="invoiceItem" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th scope="col" style="width: 23%;">Account <span class="requried"></span></th>

                    <th scope="col" style="width: 15%;">Party</th>
                    <th scope="col" style="width: 15%;">Location</th>
                    <th scope="col" style="width: 15%;">Department</th>
                    <th scope="col" style="width: 15%;">Cost Center</th>
                    <th scope="col" class="text-right" style="width: 15%;">Debits</th>
                    <th scope="col" class="text-right" style="width: 2%;"></th>
                </tr>
            </thead>
            <tbody id="invoiceItem_2">
            <?php
                if(count((array)$jv_items) == '0'){
                    $jv_items[] = array();
                }
                $i=1;
                $total_amount_lable = "0";
                foreach ($jv_items as $tab2) {
                    if($journal_vouchers['voucher_type'] == "RV"){
                        $total_amount_lable += $tab2['credit_amount'];
                        $amount = $tab2['credit_amount'];
                    }else{
                        $total_amount_lable += $tab2['debit_amount'];
                        $amount = $tab2['debit_amount'];
                    }
                ?>
                <tr id='jvrow_<?php echo $i; ?>'>
                    <td  class="p-2 w-full">
                        <select aria-label="Chart Of Account []" class="form-select js-example-basic-single" name="chart_of_account_id[]" id="productCode_<?php echo $i; ?>" onchange="itemChange(this.value,<?php echo $i; ?>)" required>
                          <option value="">Select Any One </option>
                          <?php foreach($chart_of_accounts as $coa) {  ?>
                             <option value="<?php echo $coa['id']; ?>" <?php echo $coa['account_code'] ==$tab2['account_code']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                          <?php } ?>
                        </select>
                        <input type="hidden" name="account_code[]" id="account_code_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['account_code']; ?>" >
                       <input type="hidden" name="jv_item_id[]" class="form-control" value="<?php echo $tab2['id']; ?>" >
                       <input type="hidden" name="account_name[]" id="account_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['account_name']; ?>">
                       <input type="hidden" name="account_type[]" id="account_type_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['account_type']; ?>">
                        <textarea aria-label="Particulars[]" name="particulars[]" id="particulars_<?php echo $i; ?>"  class="form-control mob_width_txtarea mt-2"><?php echo $tab2['particulars']; ?></textarea>
                    </td>

                    <td class="p-2">
                        <select aria-label="Party []" class="form-control form-select js-example-basic-single"  name= "party_id[]"  >
                            <option value="0">Select Any Name</option>
                            <?php foreach ($partylist as $transtab => $parttable){
                                foreach($parttable as $party){
                              ?>
                                 <option value ="<?php echo $party['id']."||".$transtab;  ?>" <?php echo ($tab2['party_id'] ==$party['id'] && $tab2['party_transaction_table'] == $transtab)?"selected":"";   ?>> <?php echo $party['display_name']." (".$transtab.")"; ?> </option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="p-2 w-full">
                        <select aria-label="Transaction Location []" class="form-control form-select js-example-basic-single"  name= "transaction_location_id[]"  >
                            <option value="0">Select Any Name</option>
                            <?php foreach($locations as $loc){
                              ?>
                                 <option value ="<?php echo $loc['id'];  ?>" <?php echo $tab2['transaction_location_id'] ==$loc['id']?"selected":"";   ?>> <?php echo $loc['location_name']; ?> </option>
                            <?php }  ?>
                        </select>
                    </td>
                    <td class="p-2 w-full">
                        <select aria-label="Department []" class="form-control form-select js-example-basic-single"  name= "department_id[]"  >
                            <option value="0">Select Any Name</option>
                            <?php foreach($departments as $dep){
                              ?>
                                 <option value ="<?php echo $dep['id'];  ?>" <?php echo $tab2['department_id'] ==$dep['id']?"selected":"";   ?>> <?php echo $dep['department_name']; ?> </option>
                            <?php }  ?>
                        </select>
                    </td>
                    <td class="p-2 w-full">
                        <select aria-label="Cost Center []" class="form-control form-select js-example-basic-single"  name= "cost_center_id[]"  >
                            <option value="0">Select Any Name</option>
                            <?php foreach($cost_centers as $cc){
                              ?>
                                 <option value ="<?php echo $cc['id'];  ?>" <?php echo $tab2['cost_center_id'] ==$cc['id']?"selected":"";   ?>> <?php echo $cc['cost_center_name']; ?> </option>
                            <?php }  ?>
                        </select>
                    </td>
                    <td class="p-2">
                        <input aria-label="Amount[]" type="text" name="amount[]" onblur="checkdebitandcredit()" id="debit_<?php echo $i; ?>" class="form-control text-right all_amount" value="<?php echo $amount; ?>" autocomplete="off">
                    </td>
                    <td class="p-2">
                        <i class="ti-trash"  onclick="deletRow('jvrow_<?php echo $i; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                    </td>
                </tr>
                <?php $i++;
}
                $difference_amt = ($debit_total-$credit_total);
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td class="p-2">
                            <button class="btn btn-success btn_mobile" id="addRows" type="button" onclick="addMoreRow()">+ Add More</button>
                        </td>
                        <td colspan="4" class="p-2 text-right f-w-700"> Total </td>
                        <td class="text-right" class="p-2">
                            <input aria-label="Debit Amount Lable" type="text" class="form-control text-right" id="debit_amount_lable" value="<?php echo customeummarmonyformate($total_amount_lable); ?>" readonly="">
                            <input type="hidden" class="form-control text-right" name="total_amount" id="total_amount_lable" value="<?php echo $total_amount_lable; ?>" readonly="">
                    <input type="hidden" class="form-control text-right" name="old_total_amount" id="old_total_amount_lable" value="<?php echo $total_amount_lable; ?>" readonly="">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row mt-4">
            <div class="col-lg-7" style="">
               <div class="row">
                    <div class="col-lg-6 mb-3" style="">
                        <span class="form-label">Internal Note(s) </span>
                        <textarea aria-label="Notes" style="height: 120px;" name="notes" class="form-control" ><?php echo $journal_vouchers['notes']; ?></textarea>
                    </div>
                    <div class="col-lg-6 mb-3" style="">
                        <span class="form-label">Printing Note(s) </span>
                        <textarea aria-label="Printing Notes" style="height: 120px;" name="printing_notes" class="form-control" ><?php echo $journal_vouchers['printing_notes']; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-5" style="">
                            </div>
        </div>
    </div>
    <div class="card-footer">
          <div class="col-lg-4 p-0">
              <?php
                  if($total_amount_lable == '0' || $rid ==""){
                      $disbledbtn = "disabled btn-danger";
                  }
              ?>
            <?php if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ ?>
                    <input type="hidden" id="rid"name="rid" value="<?php echo $rid; ?>" />
                    <button class="btn btn-primary mr-2 mb-2 submit_btn <?php echo $disbledbtn; ?>" value="update"  name="submitjournal" value="exit"><i class="la la-check"></i>Update</button>

            <?php } else{ ?>
                    <input type="hidden" name="submit1" value="Add" />
                    <button class="btn btn-primary mr-2 mb-2 submit_btn <?php echo $disbledbtn; ?>" name="submitjournal" value="exit"><i class="la la-check"></i>Save & exit</button>
                    <button class="btn btn-primary mr-2 mb-2 submit_btn <?php echo $disbledbtn; ?>" name="submitjournal" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                    <button type="reset" class="btn btn-secondary mb-2" ><i class="la la-close"></i>Cancel</button>
            <?php } ?>
            </div>
        </div>
    </form>
    </div>
</div>
