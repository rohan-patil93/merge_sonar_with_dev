<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['table']= 1;
if(isset($_POST['submitesopplanse'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertesopplans&method=post";
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
            header("location:index?option=COM_LIST_ESOP_PLANS");
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
$getrid =  encodeId($_GET['Rid']);
$decodrid = urldecode($getrid);
$spitrid = explode(':', $decodrid);
$rid = $spitrid['1'];
$url1 = API."?mode=getesopplans&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$esop_plans = $result['esop_plans'];
$esop_plan_buckets = $result['esop_plan_buckets'];
$company_valuations = $result['company_valuations'];
?>
<script type="text/javascript">

var maxrows = 1000;
var mk = 1000;
    function addMoreBuket(){
        var share_price_at_grant_time = $("#share_price_at_grant_time").val();
            maxrows = maxrows+1;
            mk= mk+1;
        var htmlrows = '<tr id="deleteid_'+maxrows+'">';
        htmlrows += '<td class="p-0"><input aria-label="Bucket Name[]" type="text" class="form-control border-0 box-shadow-0" placeholder="" name="bucket_name[]" required></td>';
        htmlrows += '<td class="p-0" style="text-align: right;"><input aria-label="Grant Price[]" type="text" class="form-control border-0 text-right box-shadow-0"  name="grant_price[]" id="grant_price_'+maxrows+'" value="'+share_price_at_grant_time+'" readonly required></td>';
        htmlrows += ' <td class="p-0"><input aria-label="Discount[]" type="text" class="form-control border-0 text-right box-shadow-0"  name="discount[]" id="discount_'+maxrows+'" onblur="calculateBucketValue('+maxrows+')" required></td>';
        htmlrows += '<td class="p-0" id="custom-popup"><input aria-label="Expected Option Benefit[]" type="text" class="form-control border-0 text-right box-shadow-0"  name="expected_option_benefit[]" id="expected_option_benefit_'+maxrows+'" readonly required>';

        htmlrows += '<div class="custom-popup-open open-popup-'+maxrows+' text-start" style="display: none; width: 500px;">';
        htmlrows += '<div class="card">';
        htmlrows += '<div class="card-header"><h5>Vesting Schedule</h5><div class="card-header-right"><button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closePopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>';
        htmlrows += '<div class="card-body" id="addmorediv-'+maxrows+'"><div class="row"><div class="form-group col-lg-4"><span >Date</span></div><div class="form-group col-lg-3"><span >Percentage(%)</span></div><div class="form-group col-lg-4"><span >Till Date</span></div></div>';

        htmlrows += '<div class="row" id="deleteidvesting_'+mk+'"><div class="form-group col-lg-4"><input aria-label="Vesting Date[" type="text" class="form-control datepicker vesategdatpicker_'+maxrows+'" name="vesting_date['+maxrows+'][]" value="" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></div><div class="form-group col-lg-3"><input aria-label="Vesting Percentage[" type="number" class="form-control text-right vestingpercentage_'+maxrows+' vesategpercentage" name="vesting_percentage['+maxrows+'][]"  onblur="calculateVestingPercentage('+maxrows+')"></div><div class="form-group col-lg-4"><input aria-label="Till Date[" type="text" class="form-control datepicker vesategetilldate_'+maxrows+'" name="till_date['+maxrows+'][]" ></div><div class="form-group col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRowVestage('+mk+','+maxrows+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>  </div></div>';


        htmlrows += '<div class="card-footer pt-0"><input type="hidden" name="vesting_schedule_seq[]" value="'+mk+'"><button type="button" class="btn btn-success waves-effect waves-light float-left  m-r-10" onclick="addMore('+mk+')"><i class="fa fa-check m-r-10"></i>Add More</button><button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" onclick="closePopUp()"> Done</button><span class="float-end m-t-20"> <span class="text-danger">Un-Schedule (%) : </span><span id="totaldistributionpending_'+maxrows+'">0</span><span class="text-warning m-l-20">Total (%) : </span><span id="totaldistributionqty_'+maxrows+'">0</span></span></div>';

        htmlrows += '</div></div>';
        htmlrows += '</td>';

        htmlrows += '<td class="p-0"><input aria-label="Options Granted[]" type="text" class="form-control border-0 text-right box-shadow-0 options_granted" name="options_granted[]" id="options_granted_'+maxrows+'" onblur="calculateBucketValue('+maxrows+')"></td>';
        htmlrows += '<td class="p-0"><input aria-label="Total Discount[]" type="text" class="form-control border-0 text-right box-shadow-0"  name="total_discount[]" id="total_discount_'+maxrows+'" readonly></td>';
        htmlrows +='<td class="p-0 text-center pt-2"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger mr-5" onclick="deletRow('+maxrows+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button><button type="button" class="btn p-0 border-0 bg-transparent ti-settings " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Vesting Schedule" onclick="openPopUp('+maxrows+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></td>';
        htmlrows += '</tr>';
        console.log(htmlrows);
        $("#bucketplans").append(htmlrows);
        $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
        dateFormat: date_format,
        yearRange: "-60:+10",
        setDate: new Date(),
        changeMonth: true,
        changeYear: true,
        //     }
        });
    }
function deletRow(id){
    $("#deleteid_"+id).remove();
}

function calculateSharePrice(){
    var select_company_valuation = $("#select_company_valuation").val();

    if(select_company_valuation !=""){
        var splitevalues = select_company_valuation.split("||");
        var company_valuation = splitevalues[2];
        var date_of_grant = splitevalues[3];
        var share = splitevalues[4];
        var share_price_at_grant_time = splitevalues[5];
        $("#company_valuation").val(company_valuation);
        $("#date_of_grant").val(date_of_grant);
        $("#share").val(share);
        $("#share_price_at_grant_time").val(share_price_at_grant_time);
        $(".grant_price").val(share_price_at_grant_time);
    }
}
function calculateBucketValue(rowid){
    var share = $("#share").val();
    console.log("share - "+share);
    var grant_price = $("#grant_price_"+rowid).val();
    var discount = $("#discount_"+rowid).val();

    var expected_option_benefit = (parseFloat(grant_price)*parseFloat(discount))/100;
    expected_option_benefit = parseFloat(expected_option_benefit).toFixed(2);
    var finalexpected_option_benefit= parseFloat(grant_price)-parseFloat(expected_option_benefit);
    if(isNaN(finalexpected_option_benefit)){
        finalexpected_option_benefit = 0;
    }
    finalexpected_option_benefit = parseFloat(finalexpected_option_benefit).toFixed(2);
    $("#expected_option_benefit_"+rowid).val(finalexpected_option_benefit);
    $("#discount_per_option_"+rowid).val(expected_option_benefit);
    var options_granted =  $("#options_granted_"+rowid).val();
    var grantdiscount =  parseFloat(grant_price)*parseFloat(options_granted);
    var benifitdiscount =  parseFloat(finalexpected_option_benefit)*parseFloat(options_granted);
    var total_discount = parseFloat(grantdiscount)-parseFloat(benifitdiscount)
    if(isNaN(total_discount)){
        total_discount = 0;
    }
    $("#total_discount_"+rowid).val(total_discount.toFixed(2));

    var tootaloptionsgranted = 0;
    $(".options_granted").each(function(){
        if($(this).val() !=""){
            tootaloptionsgranted +=parseFloat($(this).val());
        }
    });
    if(isNaN(tootaloptionsgranted)){
        tootaloptionsgranted = 0;
    }
    if(isNaN(share)){
        share = 0;
    }
    $("#totaloptions_granted").text(tootaloptionsgranted);
    $("#totaloptions_grantedpending").text(parseInt(share)-parseInt(tootaloptionsgranted));

}
 function openPopUp(rowid){
    $(".custom-popup-open").hide();
    $(".open-popup-"+rowid).show('slow');
  }
var maxrows = 1000;
  function addMore(rowid){
    maxrows = maxrows+1;
    var html = '';
    html += '<div class="row" id="deleteidvesting__2"+maxrows+'">';
    html += '<div class="form-group col-lg-4"><input aria-label="Vesting Date[" type="text" class="form-control datepicker vesategdatpicker_'+rowid+'" name="vesting_date['+rowid+'][]" ></div>';
    html += '<div class="form-group col-lg-3"><input aria-label="Vesting Percentage[" type="number" class="form-control text-right vestingpercentage_'+rowid+'" name="vesting_percentage['+rowid+'][]" onblur="calculateVestingPercentage('+rowid+')"></div>';
    html += '<div class="form-group col-lg-4"><input aria-label="Till Date[" type="text" class="form-control datepicker vesategetilldate_'+rowid+'" name="till_date['+rowid+'][]"></div>';

    html += '<div class="form-group col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRowVestage('+maxrows+','+rowid+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>';
    html += '</div>';
    $("#addmorediv-"+rowid).append(html);
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
        dateFormat: date_format,
        yearRange: "-60:+10",
        setDate: new Date(),
        changeMonth: true,
        changeYear: true,
        });
  }

function deletRowVestage(id){
    $("#deleteidvesting_"+id).remove();
    calculateVestingPercentage(rowid);
}
function closePopUp(rowid){
    var error = 0;
    $(".vesategdatpicker_"+rowid).each(function(){
        if($(this).val() ==""){
            error = 1;
            console.log('Empty vesategdatpicker -'+$(this).val());
        }
    });
    $(".vestingpercentage_"+rowid).each(function(){
        if($(this).val() ==""){
            error = 1;
            console.log('Empty vesategpercentage -'+$(this).val());
        }
    });
    $(".vesategetilldate_"+rowid).each(function(){
        if($(this).val() ==""){
            error = 1;
            console.log('Empty vesategetilldate -'+$(this).val());
        }
    });

    if(error ==0){
        $(".custom-popup-open").hide();
    }else{
       swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Please fill out all required fields.", "error");
    }
  }
function calculateVestingPercentage(rowid){
    var totaldistributionqty = 0;
    $(".vestingpercentage_"+rowid).each(function(){
        console.log("listtotal - "+$(this).val());
        totaldistributionqty +=parseFloat($(this).val());
    });
    console.log("total - "+totaldistributionqty);
     if(isNaN(totaldistributionqty)){
        totaldistributionqty = 0;
    }
    $("#totaldistributionqty_"+rowid).text(totaldistributionqty);
    $("#totaldistributionpending_"+rowid).text(100-parseFloat(totaldistributionqty));
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
                <h5>ESOP Plans</h5>
            </div>
            <div class="card-body" style="padding: 15px 12%;">
                <div class="row">
                    <div class="form-group col-lg-4 w-full">
                        <span class="form-label">Company Valuation</span>
                            <select aria-label="Ect Company Valuation" class ="form-control js-example-basic-single" id="select_company_valuation" name="select_company_valuation" onchange="calculateSharePrice()" required>
                                <option value="">Select Any One</option>
                                <?php foreach($company_valuations as $cv){ ?>
                                <option  value="<?php echo $cv['cv_id']."||".$cv['cvd_id']."||".$cv['company_value']."||".fromDate($cv['valuation_date'])."||".$cv['new_shares']."||".$cv['per_share_value']; ?>" <?php echo ($cv['cv_id']==$esop_plans['company_valuation_id'] && $cv['cvd_id']==$esop_plans['company_valuation_distribution_id'])?"selected":"";?>>
                                  <?php echo $cv['valuation_name']." (".fromDate($cv['valuation_date']).")"; ?>
                                </option>
                                <?php

                             } ?>
                           </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Plan Name</span>
                        <input aria-label="Plan Name" type="text" class="form-control" name="plan_name" value="<?php echo $esop_plans['plan_name']; ?>" required>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="form-label">Date of Grant</span>
                        <div class="input-group custom-date">
                            <span class="input-group-text addon-style" id="basic-addon3">
                                <span class=""><i class="fa fa-calendar"></i></span>
                            </span>
                            <input aria-label="Date Of Grant" type="text" class="form-control datepicker" name="date_of_grant" id="date_of_grant" value="<?php echo $esop_plans['date_of_grant']; ?>" required>
                        </div>
                    </div>
                    <div class="form-group col-lg-2">
                        <span class="form-label">Plan Valuation</span>
                        <input aria-label="Company Valuation" type="text" class="form-control text-right" name="company_valuation" id="company_valuation" value="<?php echo $esop_plans['company_valuation']; ?>" onblur="calculateSharePrice()" readonly required>
                    </div>
                    <div class="form-group col-lg-2">
                        <span class="form-label">Number of Shares</span>
                        <input aria-label="Share" type="text" class="form-control text-right" name="share" id="share" value="<?php echo $esop_plans['share']; ?>" onblur="calculateSharePrice()" readonly required>
                    </div>
                    <div class="form-group col-lg-2">
                        <span class="form-label">Shares Price (Grant)</span>
                        <input aria-label="Share Price At Grant Time" type="text" class="form-control text-right" name="share_price_at_grant_time" id="share_price_at_grant_time" value="<?php echo $esop_plans['share_price_at_grant_time']; ?>" readonly required>
                    </div>
                    <div class="form-group col-lg-6">
                        <span class="form-label">Notes</span>
                        <textarea aria-label="Note" class="form-control" name="note"><?php echo $esop_plans['note']; ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Grant Price, Discount & Value</h5>
            </div>
            <div class="card-body" style="padding: 15px 11%;">
                <div class="" style="">
                    <table class="table table-bordered table-hover" id="bucketplans">
                        <tr>
                            <th scope="col">Bucket</th>
                            <th scope="col" class="text-right ">Grant Price</th>
                            <th scope="col" class="text-right ">Discount(%)</th>
                            <th scope="col" class="text-right ">Option Price</th>


                            <th scope="col" class="text-right ">Options Granted</th>
                            <th scope="col" class="text-right ">Total Discount</th>
                            <th scope="col">Action</th>
                        </tr>
                        <?php $i=1;  $mk=0; if(count((array)$esop_plan_buckets) == 0){
                            $esop_plan_buckets[] = array('bucket_name'=>'');
                        }
                        $totaloptions = 0;
                        foreach($esop_plan_buckets as $epb){
                            $vesting_detail = json_decode($epb['vesting_schedule'],1);
                            $totaloptions += $epb['options_granted'];
                            ?>
                        <tr id="deleteid_<?php echo $i; ?>">
                            <td class="p-0">
                                <input type="hidden" name="irid[]" value="<?php echo $epb['id']; ?>">
                                <input aria-label="Bucket 1" type="text" class="form-control border-0 box-shadow-0 " placeholder="Bucket 1" name="bucket_name[]" value="<?php echo $epb['bucket_name']; ?>" required>
                            </td>
                            <td class="p-0" style="text-align: right;">
                                <input aria-label="Grant Price[]" type="text" class="form-control border-0 text-right box-shadow-0 grant_price"  name="grant_price[]" id="grant_price_<?php echo $i; ?>"  value="<?php echo $epb['grant_price']; ?>"  readonly required>
                            </td>
                            <td class="p-0" >
                                <input aria-label="Discount[]" type="text" class="form-control border-0 text-right box-shadow-0"  name="discount[]" id="discount_<?php echo $i; ?>" value="<?php echo $epb['discount']; ?>" onblur="calculateBucketValue('<?php echo $i; ?>')">
                                <input type="hidden" name="discount_per_option[]" id="discount_per_option_<?php echo $i; ?>" value="<?php echo $epb['discount_per_option']; ?>" required>
                            </td>
                            <td class="p-0" id="custom-popup_3">
                                <input aria-label="Expected Option Benefit[]" type="text" class="form-control border-0 text-right box-shadow-0"  name="expected_option_benefit[]" id="expected_option_benefit_<?php echo $i; ?>" value="<?php echo $epb['expected_option_benefit']; ?>" readonly required>
                                <div class="custom-popup-open open-popup-<?php echo $i; ?> text-start" style="display: none; width: 500px;">
    <div class="card">
        <div class="card-header">
          <h5>Vesting Schedule</h5>
          <div class="card-header-right">
            <i class="ti-close" onclick="closePopUp('<?php echo $i; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
          </div>
        </div>
        <div class="card-body" id="addmorediv-<?php echo $i; ?>">
            <div class="row">
                <div class="form-group col-lg-4">
                    <span>Allocation Date <span class="text-danger">*</span></span>
                </div>
                <div class="form-group col-lg-3">
                    <span>Percentage(%) <span class="text-danger">*</span></span>
                </div>
                <div class="form-group col-lg-4">
                    <span>Till Date <span class="text-danger">*</span></span>
                </div>
            </div>
            <?php
                if(count((array)$vesting_detail) == 0){
                    $vesting_detail[] = array('vesting_percentage'=>'100');
                }
                $totaldis = 0;
               foreach($vesting_detail as $vd){
                  $totaldis +=$vd['vesting_percentage'];
            ?>
                <div class="row" id="deleteidvesting_<?php echo $mk; ?>">
                    <div class="form-group col-lg-4">
                       <input aria-label="Input field" type="text" class="form-control datepicker vesategdatpicker_<?php echo $i; ?>" name="vesting_date[<?php echo $i; ?>][]" value="<?php echo $vd['vesting_date']; ?>" >
                    </div>
                    <div class="form-group col-lg-3">
                        <input aria-label="Enter number" type="number" class="form-control text-right vestingpercentage_<?php echo $i; ?> " name="vesting_percentage[<?php echo $i; ?>][]" value="<?php echo $vd['vesting_percentage']; ?>" onblur="calculateVestingPercentage(<?php echo $i; ?>)">
                    </div>
                    <div class="form-group col-lg-4">
                       <input aria-label="Input field" type="text" class="form-control datepicker vesategetilldate_<?php echo $i; ?>" name="till_date[<?php echo $i; ?>][]" value="<?php echo $vd['till_date']; ?>" >
                    </div>
                    <div class="form-group col-lg-1"><i class="ti-trash"  onclick="deletRowVestage('<?php echo $mk; ?>','<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i> </div>
                </div>
            <?php  $mk++;
} ?>
        </div>
        <div class="card-footer pt-0">
            <input type="hidden" name="vesting_schedule_seq[]" value="<?php echo $i; ?>">
            <button type="button" class="btn btn-success waves-effect waves-light float-left  m-r-10" onclick="addMore('<?php echo $i; ?>')"><i class="fa fa-check m-r-10"></i>Add More</button>
            <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" onclick="closePopUp('<?php echo $i; ?>')"> Done</button>
            <span class="float-end m-t-20">
                <span class="text-danger">Un-Schedule (%) : </span>
                <span id="totaldistributionpending_<?php echo $i; ?>"><?php echo 100-$totaldis; ?></span>
                <span class="text-warning m-l-20">Total (%) : </span>
                <span id="totaldistributionqty_<?php echo $i; ?>"><?php echo $totaldis; ?></span>
            </span>
        </div>
    </div>
</div>
                            </td>

                            <td class="p-0" >
                                <input aria-label="Options Granted[]" type="text" class="form-control border-0 text-right box-shadow-0 options_granted" name="options_granted[]" id="options_granted_<?php echo $i; ?>" onblur="calculateBucketValue('<?php echo $i; ?>')" value="<?php echo $epb['options_granted']; ?>">
                            </td>
                            <td class="p-0">
                                <input aria-label="Total Discount[]" type="text" class="form-control border-0 text-right box-shadow-0"  name="total_discount[]" id="total_discount_<?php echo $i; ?>" value="<?php echo $epb['total_discount']; ?>" readonly>
                            </td>
                            <td class="p-0 text-center pt-2" >
                                 <i class="ti-trash text-danger mr-5" onclick="deletRow('<?php echo $i; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Removed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                 <i class="ti-settings  f-20" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Vesting Schedule" onclick="openPopUp('<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                            </td>
                        </tr>
                    <?php $i++;
} ?>
                    </table>
                </div>
                <button class="btn btn-success m-t-20" id="addRows" type="button" onclick="addMoreBuket()"><i class="fa fa-plus m-r-5"></i>Add More</button>
                <span class="float-end m-t-20 m-r-50">
                    <span class="text-danger">Pendding Options Grant: </span>
                    <span id="totaloptions_grantedpending"><?php echo $esop_plans['share']-$totaloptions; ?></span>
                    <span class="text-warning m-l-20">Total Options Granted  : </span>
                    <span id="totaloptions_granted"><?php echo $totaloptions; ?></span>
                </span>
            </div>
            <div class="card-footer pt-0 mt-3">
                 <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" name="submitesopplanse"> <?php echo $rid !=""?"Update":"Submit";?></button>
            </div>
        </div>
</form>


