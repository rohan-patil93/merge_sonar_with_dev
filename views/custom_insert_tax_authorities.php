<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submittaxauthorities'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=inserttaxauthorities&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
           header("location:index?option=COM_LIST_TAX_AUTHORITIES");
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
$url1 = API."?mode=gettaxauthorities&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$tax_type = $result['tax_type'];
$tax_scope = $result['tax_scope'];
$tax_computations = $result['tax_computations'];
$tax_authorities = $result['tax_authorities'];
$tax_authorities_rates = $result['tax_authorities_rates'];
if($tax_authorities['tax_computation_id'] == '1'){
    $group_tax = json_decode($tax_authorities['group_tax'],1);
}
$chart_of_accounts = $result['chart_of_accounts'];

$tax_authorities_rates_split = $result['tax_authorities_rates_split'];
$all_tax_authorities = $result['all_tax_authorities'];
$distributioninvoices = $distributionrefunds = array();
foreach($tax_authorities_rates_split as $tars){
    if($tars['split_type'] == 'I'){
        $distributioninvoices[$tars['authority_rate_id']][] = $tars;
    }elseif($tars['split_type'] == 'R'){
        $distributionrefunds[$tars['authority_rate_id']][] = $tars;
    }
}
$hsn_and_sac_codes = $result['hsn_and_sac_codes'];

?>
<script type="text/javascript">
    function getComputationType(computation_id){
        $(".group_of_tax").hide('slow');
        $(".distribution").hide('slow');
        console.log(computation_id);
        if(computation_id == '1'){
            $(".group_of_tax").show('slow');
        }else{
            $(".distribution").show('slow');
        }
    }

    var maxrow = 10000;
    function addMore(type,seq){
        var html = '';
        var idtype = type+"_"+seq;
        var deletworid  = 'I_'+maxrow+'_'+seq;
        html +='<div class="row mt-2" id="delete_row_'+deletworid+'"><input type="hidden" class="form-control " name="split_type['+seq+'][]" value="'+type+'">';
        html +='<div class="col-lg-7 w-full"><select aria-label="Accountcode Split[" name="accountcode_split['+seq+'][]" class="form-control form-select js-example-basic-single" ><option value="">Select Any One </option><?php foreach($chart_of_accounts as $coa) {  ?><option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['id'] ==$di['tax_rate']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option><?php
} ?></select></div>';
        html +='<div class="col-lg-4"><input aria-label="Tax Rate Split[" type="number" class="form-control text-right distribution'+idtype+'" name="tax_rate_split['+seq+'][]" value="0" onblur="calculateTotal(\''+idtype+'\')"></div>';
        html +='<div class="form-group col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRow('+deletworid+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div></div>';
            $("#addmoreinvoice_"+seq).append(html);
        $(".js-example-basic-single").select2();
    }
    function deletRow(rowid){
        $("#delete_row_"+rowid).remove();

    }
    function calculateTotal(type){
         var totaldistribution = 0;
         console.log('working');
        $(".distribution"+type).each(function(){
            if($(this).val() !=""){
                totaldistribution +=parseFloat($(this).val());
            }
        });
        console.log(totaldistribution);
        if(isNaN(totaldistribution)){
            totaldistribution = 0;
        }
        $("#totaldistribution"+type).text(totaldistribution);
        $("#totaldistributionpending"+type).text(100-parseInt(totaldistribution));
    }

    var taxauthsq = 39000;
    function addMoreTaxAuth(){
        taxauthsq = taxauthsq+1;
        var html = '';
        html += '<div class="col-lg-12">';
        html += '<div class="card">';
        html += '<div class="card-header"><h5>Tax Authorities Rate</h5></div>';
        html += '<div class="card-body p-l-50 p-r-50">';
        html += '<div class="row">'
        html += '<div class="form-group col-lg-6">'
        html += '<span class="form-label">Tax Authorities Rates Name<span class="text-danger">*</span></span>'
        html += '<input type="hidden" class="form-control" name="tax_authorities_rates_seq['+taxauthsq+']" value="'+taxauthsq+'">';
        html += '<input type="hidden" class="form-control" name="tax_authorities_rates_id['+taxauthsq+']" value="">';
        html += '<input aria-label="Tax Description[" type="text" class="form-control text-capitalize" name="tax_description['+taxauthsq+']" value="">';
        html += '</div>';
        html += '<div class="col-lg-6">';
        html += '<div class="row">';
        html += '<div class="col-lg-7 f-w-700">Rates Split Account</div>';
        html += '<div class="col-lg-4 text-right f-w-700">Split Base On (%)</div>';
        html += '</div>';
        html += '<div id="addmoreinvoice_'+taxauthsq+'">';
        html += '</div>';
        html += '<div class="mt-3">';
        html += '<button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" id="addmore_btn" onclick="addMore(\""I"\",'+taxauthsq+')"><i class="icofont icofont-plus icon-white "></i> Add More</button>';
        html += '<span class="float-end">';
        html += '<span class="text-danger">Pendding (%) : </span>';
        html += '<span id="totaldistributionpendingI_'+taxauthsq+'"><?php echo 100-round($itotal); ?></span>';
        html += '<span class="text-warning m-l-20">Total (%) : </span>';
        html += '<span id="totaldistributionI_'+taxauthsq+'"><?php echo round($itotal); ?></span>';
        html += '</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        console.log(html);
        $("#addmoretax_authorities_rates").append(html);
        addMore('I',taxauthsq);
    }


</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>

<form method="post">
    <div class="card">
        <div class="card-header">
            <h5>Tax Authorities</h5>
            <div class="card-header-right table-card-header">
                <button type="button" onclick="history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i> Back</button>
            </div>
        </div>
        <div class="card-body p-l-50 p-r-50">
            <div class="row">
                <div class="form-group col-lg-12 w-full">
                    <span class="form-label">HSN And SAC Code <span class="text-danger">*</span></span>
                    <select aria-label="Hsn And Sac Code []" name="hsn_and_sac_code_id[]" class="form-control form-select js-example-basic-multiple" multiple>

                        <?php foreach($hsn_and_sac_codes as $hsnsac) {  ?>
                            <option value="<?php echo $hsnsac['id']; ?>" <?php echo $hsnsac['tax_authority_id'] ==$rid?"selected":""; ?>><?php echo $hsnsac['code']." - ".$hsnsac['code_description']." (".$hsnsac['gst'].")";?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <span class="form-label">Tax Name <span class="text-danger">*</span></span>
                    <input aria-label="Tax Name" type="text" class="form-control text-capitalize" name="tax_name" value="<?php echo $tax_authorities['tax_name']; ?>">
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Description <span class="text-danger">*</span></span>
                    <textarea aria-label="Description" class="form-control" name="description" ><?php echo $tax_authorities['description']; ?></textarea>
                </div>
                <div class="form-group col-lg-3 w-full">
                    <span class="form-label">Tax Computations <span class="text-danger">*</span></span>
                    <select aria-label="Tax Computation"  name="tax_computation_id" class="form-control form-select js-example-basic-single" onchange="getComputationType(this.value)">
                        <option value="">Select Any One </option>
                        <?php foreach($tax_computations as $tc) {  ?>
                            <option value="<?php echo $tc['id']; ?>" <?php echo $tc['id'] ==$tax_authorities['tax_computation_id']?"selected":""; ?>><?php echo $tc['tax_computations_name'];?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card group_of_tax" <?php echo $tax_authorities['tax_computation_id']=='1'?'':'style="display:none"';?>>
        <div class="card-header">
            <h5>Group of Tax</h5>
        </div>
        <div class="card-body p-l-50 p-r-50">
            <?php  if(count((array)$all_tax_authorities) !=0){ ?>
            <table class="table table-hover m-b-0" style="font-size:13px;" >
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Tax Name</th>
                        <th scope="col">Tax Type</th>
                        <th scope="col">Tax Scope</th>
                        <th scope="col" class="text-right">tax_rate</th>
                    </tr>
                </thead>
                <tbody>
                <?php $i=1; foreach($all_tax_authorities as $ata){ ?>
                    <tr>
                        <td class="p-2">
                            <div class="checkbox-fade fade-in-primary">
                                  <label class="form-label">
                                <input aria-label="Group Tax[]" type="checkbox" name="group_tax[]" value="<?php echo $ata['id']; ?>" <?php echo in_array($ata['id'], (array)$group_tax)?"checked":""; ?>>
                                <span class="cr">
                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                              </label>
                            </div>
                        </td>

                        <td class="p-2"><?php echo $ata['tax_name']; ?></td>
                        <td class="p-2"><?php echo $tax_type[$ata['tax_type']]['genc_detail_name']; ?></td>
                        <td class="p-2"><?php echo $tax_computations[$ata['tax_computation_id']]['tax_computations_name']; ?></td>
                        <td class="text-right p-2"><?php echo $ata['tax_rate']; ?></td>
                    </tr>
                <?php  $i++;
} ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Taxes</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
        </div>
    </div>

    <div class="row distribution" <?php echo $tax_authorities['tax_computation_id']!='1'?'':'style="display:none"';?>>
        <div id="addmoretax_authorities_rates">
    <?php $taseq = 1; foreach($tax_authorities_rates as $tars){ ?>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5>Tax Authorities Rate</h5>
                </div>

                <div class="card-body p-l-50 p-r-50">
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <span class="form-label">Tax Authorities Rates Name<span class="text-danger">*</span></span>
                            <input type="hidden" class="form-control" name="tax_authorities_rates_seq[<?php echo $taseq; ?>]" value="<?php echo $taseq; ?>">
                            <input type="hidden" class="form-control" name="tax_authorities_rates_id[<?php echo $taseq; ?>]" value="<?php echo $tars['id']; ?>">
                            <input aria-label="Input field" type="text" class="form-control text-capitalize" name="tax_description[<?php echo $taseq; ?>]" value="<?php echo $tars['tax_description']; ?>">
                        </div>
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-7 f-w-700">Rates Split Account</div>
                                <div class="col-lg-4 text-right f-w-700">Split Base On (%)</div>

                            </div>
                            <div id="addmoreinvoice_<?php echo $taseq; ?>">
                            <?php $mk=1;
                            if(count((array)$distributioninvoices[$tars['id']]) =='0'){
                                $tars['id'] = 0;
                                $distributioninvoices[] = array('tax_rate'=>'100');
                            }
                            $itotal = 0;
                            foreach($distributioninvoices[$tars['id']] as $di){ $itotal +=$di['tax_rate']; ?>
                                <div class="row mt-2" id="delete_row_I_<?php echo $mk; ?>_<?php echo $taseq; ?>">
                                    <input type="hidden" class="form-control" name="split_type[<?php echo $taseq; ?>][]" value="I">
                                    <input type="hidden" class="form-control" name="srid[<?php echo $taseq; ?>][]" value="<?php echo $di['id']; ?>">
                                    <div class="col-lg-7 w-full">
                                        <select aria-label="Select option" name="accountcode_split[<?php echo $taseq; ?>][]" class="form-control form-select js-example-basic-single" >
                                            <option value="">Select Any One </option>
                                            <?php foreach($chart_of_accounts as $coa) {  ?>
                                                <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$di['account_code']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4">
                                        <input aria-label="Enter number" type="number" class="form-control text-right distributionI_<?php echo $taseq; ?>" name="tax_rate_split[<?php echo $taseq; ?>][]" value="<?php echo $di['tax_rate']; ?>" onblur="calculateTotal('I_<?php echo $taseq; ?>')">
                                    </div>
                                    <div class="form-group col-lg-1"><i class="ti-trash"  onclick="deletRow('I_<?php echo $mk; ?>_<?php echo $taseq; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i> </div>
                                </div>
                            <?php $mk++;
} ?>
                        </div>

                        <div class="mt-3">
                            <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" id="addmore_btn_2" onclick="addMore('I','<?php echo $taseq; ?>')"><i class="icofont icofont-plus icon-white "></i> Add More</button>

                            <span class="float-end">
                                <span class="text-danger">Pendding (%) : </span>
                                <span id="totaldistributionpendingI_<?php echo $taseq; ?>"><?php echo 100-round($itotal); ?></span>
                                <span class="text-warning m-l-20">Total (%) : </span>
                                <span id="totaldistributionI_<?php echo $taseq; ?>"><?php echo round($itotal); ?></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $taseq++;
} ?>

</div>
<div class="mt-3">
        <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-3" id="addmore_btn_3" onclick="addMoreTaxAuth()"><i class="icofont icofont-plus icon-white "></i> Add More Tax Authorities Rate</button>
    </div>
</div>

    <div class="card mt-3">
        <div class="card-body">
            <div>
                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="btn btn-primary waves-effect waves-light" name="submittaxauthorities"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
            </div>
        </div>
    </div>
</form>
