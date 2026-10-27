<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submitpaymentterms'])){
    $send['payment_term_name'] = $_POST['payment_term_name'];
    $send['is_display_invoice'] = isset($_POST['is_display_invoice']) ? 0 :1;
    $send['description'] = $_POST['description'];
    $send['content'] = urlencode(json_encode($_POST['cont']));
    if($_POST['rid']!=""){
    $send['updated_by'] = $_SESSION['userid'];
    $url = API."?mode=updaterowbasepimarydata&table=payment_terms&rid=".$_POST['rid']."&method=post";
    $response1 = file_post_contents($url, $send, 'insert');
    }else{
    $send['is_active'] = '0';
    $send['current_status'] = '0';
    $send['company_id'] = $_SESSION['company_id'];
    $send['created_by'] = $_SESSION['userid'];
    $url = API."?mode=adddata&table=payment_terms&method=post";
    $response1 = file_post_contents($url, $send, 'insert');
    }
    header("location:index?option=COM_LIST_PAYMENT_TERMS");
}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$url1 = API."?mode=togetdatabasepaimairy&table=payment_terms&rid=".$rid;
$data_details1 = callAPI($url1);
$data =  json_decode($data_details1,1);

?>
<script type="text/javascript">
    function addMorerows(){
        var count = $(".itemRow").length;
         count++;
        var html = '';
        html = '<tr class="itemRow" id="delete_row_'+count+'>"><td style="width: 20%;"><select aria-label="Cont[" name="cont['+count+'][due_type]" class="form-control form-select js-example-basic-single" ><option value="0">Select Any One </option> <option value="B">Balance</option> <option value="P">Percent</option> <option value="F">Fixed Amount</option> </select> </td>';
        html +='<td><input aria-label="Cont[" type="number" name="cont['+count+'][value]" class="form-control text-right"></td>';
        html +='<td><input aria-label="Cont[" type="number" name="cont['+count+'][days]" class="form-control text-right"></td>';
        html +='<td class="text-right"><input aria-label="Cont[" type="number" name="cont['+count+'][discount]" class="form-control text-right"></td>';
        html +='<td class="text-right"><input aria-label="Cont[" type="number" name="cont['+count+'][discount_day]" class="form-control text-right"></td>';
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

</script>
<form method="post">
    <div class="card">
        <div class="card-header">
            <h5>Payment Terms</h5>
            <div class="card-header-right table-card-header">
                <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="form-group col-lg-6">
                    <span class="form-label">Payment Terms <span class="text-danger">*</span></span>
                    <input aria-label="Payment Term Name" type="text" class="form-control" name="payment_term_name" value="<?php echo $data['payment_term_name']; ?>">
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Display terms on invoice </span>
                    <div>
                        <input aria-label="Is Display Invoice" class="js-primary-sm" type="checkbox" name="is_display_invoice" <?php echo ($data['is_display_invoice']=='0')?"checked":"";?> value="0" >
                    </div>
                </div>
                 <div class="form-group col-lg-6">
                    <span class="form-label">Descrption on the invoice</span>
                    <textarea aria-label="Ex : Payment has to be done with in 10 days" class="form-control " name="description" placeholder="Ex : Payment has to be done with in 10 days"><?php echo $data['description']; ?></textarea>
                </div>
            </div>
        </div>
    </div>
    <?php $rows = json_decode(str_replace("'", '', urldecode($data['content'])),1);
     ?>
    <div class="row distribution">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>Terms</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover m-b-0" style="font-size:13px;"  id="addmoreinvoice">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 20%;">Due Type</th>
                                    <th scope="col">Value</th>
                                    <th scope="col">Days</th>
                                    <th scope="col">Discount%</th>
                                    <th scope="col">Discount Days</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;
                                    foreach($rows as $row){  ?>
                                <tr class="itemRow" id="delete_row_<?php echo $i; ?>">
                                    <td style="width: 20%;">
                                        <select aria-label="Select option" name="cont[<?php echo $i; ?>]['due_type']" class="form-control form-select js-example-basic-single" >
                                            <option value="0">Select Any One </option>
                                            <option <?php if($row['due_type']=='P'){ echo "selected";
} ?> value="P">Percent</option>
                                            <option <?php if($row['due_type']=='F'){ echo "selected";
} ?> value="F">Fixed Amount</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input aria-label="Enter number" type="number" name="cont[<?php echo $i; ?>]['value']" class="form-control text-right" value="<?php echo $row['value'];  ?>" >
                                    </td>
                                    <td>
                                        <input aria-label="Enter number" type="number" name="cont[<?php echo $i; ?>]['days']" class="form-control text-right" value="<?php echo $row['days'];  ?>">
                                    </td>

                                    <td class="text-right">
                                         <input aria-label="Enter number" type="number" name="cont[<?php echo $i; ?>]['discount']" value="<?php echo $row['discount'];  ?>" class="form-control text-right">
                                    </td>
                                    <td class="text-right">
                                         <input aria-label="Enter number" type="number" name="cont[<?php echo $i; ?>]['discount_day']" value="<?php echo $row['discount_day'];  ?>" class="form-control text-right">
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
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" id="addmore_btn" onclick="addMorerows()"><i class="icofont icofont-plus icon-white "></i> Add More</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div>
                        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" name="submitpaymentterms"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
