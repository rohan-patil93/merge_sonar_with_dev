<?php
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['table']= 1;
$_SESSION['apply_js']['inputmask'] = 1;
if(isset($_POST['submitcompanyvaluations'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_value'] = convertamountnumber($_POST['company_value']);
    $_POST['per_share_value'] = convertamountnumber($_POST['per_share_value']);
    $url = API."?mode=insertcompanyvaluations&method=post";
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
            header("location:index?option=COM_LIST_COMPANY_VALUATIONS");
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
$url1 = API."?mode=getcompanyvaluations&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$companyvallist = $result['company_valuations_all'];
$companyval = $result['company_valuations'];
$company_valuation_distributions = $result['company_valuation_distributions'];
$companycontributionusers = $result['company_contribution_users'];
foreach($companycontributionusers as $ccu){
    $company_contribution_users[$ccu['id']] = $ccu;
}
$contribution_users_type = $result['contribution_users_type'];
$last_valuation_result = $result['last_valuation_result'];
$last_valuation_total_share = $result['last_valuation_total_share'];
$last_company_valuations = $result['last_company_valuations'];
$companyvaluationyearly = array();
foreach($companyvallist as $cvl){
    $year = date("Y",strtotime($cvl['valuation_date']));
    $companyvaluationyearly[$year][] = $cvl;
}
ksort($companyvaluationyearly);

foreach($contribution_users_type as $cvl){
    $contributionutype[$cvl['genc_detail_code']] = $cvl['genc_detail_name'];
}


?>
<script type="text/javascript">

    function roundWithoutExtraAdded(num, places) {
      return Math.trunc(num * Math.pow(10, places)) / Math.pow(10, places);
  }
  function maxamountemplye(value){
    value = value.split("<?php echo $_SESSION['config']['currency_symbol']; ?>");
    value = value[1].replaceAll(",", '');
    return value;
}

function calculateShareQTY(){

    var valuation_shareholding = $("#valuation_shareholding").val();
    var old_share_qty = $("#old_share_qty").val();
    if(valuation_shareholding !="" && valuation_shareholding !='0'){
        var calculateshare = (parseInt(old_share_qty)/(100-parseFloat(valuation_shareholding))*100);
        calculateshare = parseInt(calculateshare)-parseInt(old_share_qty);
    }else{
        var calculateshare =0;
    }
    var new_share_qty = parseInt(calculateshare);
    $("#new_share_qty").val(new_share_qty);
        var company_value = $("#company_value").val();
        company_value = maxamountemplye(company_value);
        var total_share_qty  = 0;
        if(old_share_qty !=""){
            var total_share_qty = parseInt(old_share_qty)+parseInt(new_share_qty);

            if(company_value !=0 && company_value !=""){
                var per_share_value = parseFloat(company_value)/parseInt(total_share_qty);
                $("#per_share_value").val(per_share_value.toFixed(2));

           }else{
            $("#per_share_value").val(0);
        }
    }
    $("#total_share_qty").val(parseInt(total_share_qty));

    var total_distribution = 0;
    $(".oldshareuser").each(function(){
        var oldshareid = $(this).attr('data-id');
        var oldshare = $(this).val();
        if(oldshare !=0 && oldshare !=""){
            if($("#dilution_"+oldshareid).is(":checked")){

                var old_percentage_temp = $("#old_percentage_temp_"+oldshareid).val();
                var calculateper1 = (parseInt(oldshare)/parseInt(total_share_qty))*100;
                var calculateper = old_percentage_temp !=''?old_percentage_temp:0.00;
                calculateper1 = roundWithoutExtraAdded(calculateper1,3);
                calculateper = roundWithoutExtraAdded(calculateper,3);
                $("#new_percentage_temp_"+oldshareid).val(calculateper1);
                $("#distribute_percentage_"+oldshareid).val(calculateper);



                }else{
                     var calculateper = (parseInt(oldshare)/parseInt(total_share_qty))*100;
                     calculateper = roundWithoutExtraAdded(calculateper,3);
                     $("#new_percentage_temp_"+oldshareid).val(calculateper);
                     $("#distribute_percentage_"+oldshareid).val(calculateper);

                }


                total_distribution +=parseFloat(calculateper);

            }
        });

    calculateDistributionQTY();
    $("#cap_table").removeClass('event_disabled');
}

function openPopUp(){
    $(".custom-popup-open").hide();
    $(".custom-popup-slide-query-contact_details").show('slow');
}
function closePopUp(){
    $(".custom-popup-open").hide();
    console.log("closed");
}

var maxrows = 100;
function addMore(){
    $(".done_panel").show('slow');
    $("#addmore_btn").hide();

}
var maxrows = 100;
function creatInvestor(){
    maxrows = maxrows+1;
    var new_invesert =$("#new_invesert_id").val();
    console.log(new_invesert);
    if (new_invesert === null || new_invesert =="") {
        $('#error_msg').text('*Please Select Any Investor Or Please Re-Select Investor');
    }else{
        $('#error_msg').text(' ');

        console.log(new_invesert);
        if(new_invesert == "E"){
            jQuery.ajax({
              url: "ajax.php",
              data:'page=insertesopnewrecord',
              type: "POST",
              success:function(data){
                 console.log(data);
                 var res= JSON.parse(data);
                 console.log(res);
                 var new_invesert_id = res.LASTID;
                 var new_invesert_name = 'ESOP';
                 var new_invesert_type = 'E';
                 addMoreInvester(maxrows,new_invesert_id,new_invesert_name,new_invesert_type);
             },
             error:function (){}
         });
        }else{
          var selectElement = $("#new_invesert_id");
          selectElement.find('option[value="'+new_invesert+'"]').prop('disabled', true);
          details = new_invesert.split('-');
          var new_invesert_id = details[0];
          var new_invesert_name = details[1];
          var new_invesert_type = details[2];
          addMoreInvester(maxrows,new_invesert_id,new_invesert_name,new_invesert_type);
      }


  }
}

function addMoreInvester(maxrows,new_invesert_id,new_invesert_name,new_invesert_type){
  var html = '';
  var antcss = "";
  var antinfocss = "";
  html += '<div class="row" id="deleteid__2"+maxrows+'">';
  html += '<input type="hidden"  name="irid[]"  value=""><input type="hidden"  name="ccuid[]"  value="'+new_invesert_id+'">';
  html += '<div class="form-group col-lg-3"><input type="hidden" class="form-control" name="distributer_name[]"  value="'+new_invesert_name+'" ><div class="align-middle"><span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" >N</span><div class="d-inline-block width73"><span class="f-12 text-capitalize">'+new_invesert_name+'</span><p class="text-muted m-b-0 text-capitalize">'+new_invesert_type+'</p></div></div></div>';

  html += '<div class="form-group col-lg-1 text-right p-t-15"><input type="hidden" id="old_percentage_temp_'+new_invesert_id+'" value="0"><input type="hidden" name="old_percentage[]" value="0">0%</div>';

  html += '<div class="form-group col-lg-1 text-right p-t-15"><input type="hidden" class="form-control text-right oldshareuser" name="old_shares_qty[]" id="old_shares_qty_text_'+new_invesert_id+'"  data-id="'+new_invesert_id+'" value="0"><input type="hidden" class="form-control text-right" name="new_shares_qty[]" id="new_share_qty_text_'+new_invesert_id+'" value="0">0</span> </div>';
  if(new_invesert_type == 'ESOP'){
    antcss = 'style="display:none"';
    antinfocss ='style="display:block"';
}else{
    antcss = 'style="display:block"';
    antinfocss = 'style="display:none"';
}
html +='<div class="form-group col-lg-1 p-t-15 text-center" '+antinfocss+'><i class="fa fa-info-circle f-22" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="ESOP Not Allowed Anti Dilution" ></i></div>';

html += '<div class="form-group col-lg-1 p-t-15 text-center" '+antcss+'><input type="hidden" name="dilution_seq[]" value="'+new_invesert_id+'"><button type="button" onclick="dilutionCheck('+new_invesert_id+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent"><input aria-label="Dilution[" class="js-primary-sm-'+maxrows+'" type="checkbox" name="dilution['+new_invesert_id+']" value="0"  id="dilution_'+new_invesert_id+'"></button></div>';

html += '<div class="form-group col-lg-1 p-0"><input type="hidden" id="new_percentage_temp_'+new_invesert_id+'" name="new_percentage[]"  value="0"><input aria-label="Distribute Percentage[]" type="number" class="form-control text-right distributionqty" name="distribute_percentage[]" id="distribute_percentage_'+new_invesert_id+'" value="0"  onblur="calculateDistributionQTY()" data-id="'+new_invesert_id+'" ></div>';

html += '<div class="form-group col-lg-1 text-right p-t-15"><span id="new_share_qty_label_'+new_invesert_id+'"> 0</span></div>';

html += '<div class="form-group col-lg-1 text-right p-t-15"><input type="hidden" class="form-control text-right total_share_class" name="total_share[]" id="total_share_qty_text_'+new_invesert_id+'" value="0"><span id="total_share_qty_label_'+new_invesert_id+'"> 0</span></div>';

html += '<div class="form-group col-lg-1 p-1"><input aria-label="Equity Shares Percentage[]" type="number" class="form-control text-right" name="equity_shares_percentage[]" value="100"></div>';

html += '<div class="form-group col-lg-1 p-1"><input aria-label="Preference Shares Percentage[]" type="number" class="form-control text-right" name="preference_shares_percentage[]" value="0"></div></div>';

$("#addmorediv").append(html);
var elemdefault = document.querySelector('.js-primary-sm-'+maxrows);
var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff',size: 'small' });

$(".done_panel").hide();
$("#addmore_btn").show('slow');
}


function deletRow(id){
    $("#deleteid_"+id).remove();
    calculateDistributionQTY();
}
function calculateDistributionQTY(){
    var totaldistributionqty = 0;
    var total_share_qty = $("#total_share_qty").val();

    var total_old_share = 0;
    var total_new_share = 0;
    var total_share = 0;

    $(".distributionqty").each(function(){
        var rowidid = $(this).attr('data-id');
        var old_shares_qty = $('#old_shares_qty_text_'+rowidid).val();
        old_shares_qty = (old_shares_qty!="" && old_shares_qty!="0")?old_shares_qty:0;
        if($(this).val() !=""){
            var percentage = $(this).val();
            percentage = roundWithoutExtraAdded(percentage,3)
            totaldistributionqty +=percentage;
            if($("#dilution_"+rowidid).is(":checked") || old_shares_qty ==0){
              var calc = parseInt((percentage/100)*total_share_qty);
          }else{
              var calc = old_shares_qty;
          }
          if(old_shares_qty != 0 || old_shares_qty != ''){
            var new_share_qty =calc-old_shares_qty;
        }else{
            var new_share_qty =calc;
        }
        $("#new_share_qty_text_"+rowidid).val(new_share_qty);
        $("#new_share_qty_label_"+rowidid).text(new_share_qty);
        $("#total_share_qty_text_"+rowidid).val(calc);
        $("#total_share_qty_label_"+rowidid).text(calc);
        total_old_share =parseInt(total_old_share) + parseInt((old_shares_qty != '0' || old_shares_qty != '')?old_shares_qty:0);
        total_new_share =parseInt(total_new_share) + parseInt((new_share_qty != '0' || new_share_qty != '')?new_share_qty:0);
        total_share = parseInt(total_share) + parseInt((calc != '0' || calc != '')?calc:0);
    }
});

    if(isNaN(totaldistributionqty)){
        totaldistributionqty = 0;
    }
    var  totaldistributionqtyround =  parseFloat(totaldistributionqty).toFixed(2);

    var  totaldistributionpending =  parseFloat((parseInt(100.000)-totaldistributionqtyround).toFixed(2));
    $("#totaldistributionqty").text(totaldistributionqtyround);
    $("#totaldistributionpending").text(totaldistributionpending);

    $("#total_newper").text(totaldistributionqtyround);
  $("#total_oldshare_and_newshare").text(parseInt(total_new_share));
  $("#total_tatshare").text(parseInt(total_share));


}
  //       swal({
  //           text: msg,
  //           type: "warning",
  //           showCancelButton: true,
  //           confirmButtonClass: "btn-danger",
  //           confirmButtonText: "Yes",
  //           cancelButtonText: "No",
  //           closeOnConfirm: true,
  //           closeOnCancel: true
  function formSubmit(){

    var  total_share_cnt =  0;
    var error = 0;
    var formsubmit = 0;
    var error_message_9074 = $("#error_message_9074").val();
    var error_message_9075 = $("#error_message_9075").val();
    var error_message_9076 = $("#error_message_9076").val();
    var error_message_9077 = $("#error_message_9077").val();
    var total_share_qty = $("#total_share_qty").val();
    var validationary = ['valuation_name','valuation_date','company_value','old_share_qty','valuation_shareholding'];
    jQuery.each(validationary, (index, idname) => {
        if($("#"+idname).val() =="" || $("#"+idname).val() ===undefined){
            error = 1;
            $("#"+idname+"_error").text("Required This Field");
        }else{
          $("#"+idname+"_error").text(" ");
      }
    });
    if(error == 0){
        var totaldistributionqty = 0;
        $(".distributionqty").each(function(){
            if($(this).val() !=""){
                var distper = $(this).val();
                totaldistributionqty = parseFloat(totaldistributionqty)+parseFloat(distper);
                console.log(distper+" ------toto  ----- "+totaldistributionqty);
            }
        });
        totaldistributionqty = totaldistributionqty.toFixed(2);
        console.log("totaldistributionqty ----- "+totaldistributionqty);
        if(totaldistributionqty != 100){
            if(totaldistributionqty > 100){
                msg = error_message_9074;
            }else{
                msg = error_message_9075;
                formsubmit = 1;
            }
            error = 1;
            console.log(msg);
        }
        $(".total_share_class").each(function(){
            if($(this).val() !=""){
                total_share_cnt +=parseFloat($(this).val());
                console.log($(this).val()+" --- "+total_share_cnt);
            }
        });
        if(total_share_cnt != total_share_qty){
            if(total_share_cnt < total_share_qty){
                msg = error_message_9076+" "+total_share_qty;
            }else{
                msg = error_message_9077+" "+total_share_qty;
            }
            formsubmit = 1;
            error = 1;
            console.log(msg);
       }
       if(error == 0){
           $("#formsubmitid").submit();
       }else{
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: msg,
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if(isConfirm) {
                if(totaldistributionqty <= 100 && total_share_cnt == total_share_qty){
                    $("#formsubmitid").submit();
                }
            }
        });
    }
}else{
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
}



  function dilutionCheck(rowid){

    var old_percentage_temp = $("#old_percentage_temp_"+rowid).val();
    var new_percentage_temp = $("#new_percentage_temp_"+rowid).val();
    console.log(old_percentage_temp+" -- "+new_percentage_temp);
    if($("#dilution_"+rowid).is(":checked")){
        $("#distribute_percentage_"+rowid).val(old_percentage_temp);
    }else{
        $("#distribute_percentage_"+rowid).val(new_percentage_temp);
    }
    calculateDistributionQTY();
}

</script>
<?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>
<form method="post" id="formsubmitid">
    <div class="row">
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h5>Company Valuation</h5>
                    <div class="card-header-right table-card-header">

              </div>
          </div>
          <div class="card-body">
            <input type="hidden" id="error_message_9074" value="<?php echo $_SESSION['error_message']['9074']; ?>">
            <input type="hidden" id="error_message_9075" value="<?php echo $_SESSION['error_message']['9075']; ?>">
            <input type="hidden" id="error_message_9076" value="<?php echo $_SESSION['error_message']['9076']; ?>">
            <input type="hidden" id="error_message_9077" value="<?php echo $_SESSION['error_message']['9077']; ?>">


            <div class="row">
                <div class="form-group">
                    <span class="form-label">Valuation Name <span class="text-danger">*</span></span>
                    <input aria-label="Valuation Name" type="text" class="form-control text-capitalize" name="valuation_name" id="valuation_name" value="<?php echo $companyval['valuation_name']; ?>">
                    <span id="valuation_name_error" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <span class="form-label">Valuation Date <span class="text-danger">*</span></span>
                    <input aria-label="Valuation Date" type="text" class="form-control datepicker" name="valuation_date" id="valuation_date" value="<?php echo $companyval['valuation_date']; ?>">
                    <span id="valuation_date_error" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <span class="form-label">Company Value <span class="text-danger">*</span></span>
                    <?php
                    $company_valuationsamt = 0;
                    if($companyval['company_value'] !=""){
                      $company_valuationsamt = $companyval['company_value'];
                  }elseif($last_company_valuations['company_value'] !=""){
                    $company_valuationsamt = $last_company_valuations['company_value'];
                }
                ?>
                <input aria-label="Company Value" type="text" class="form-control text-right autonumber" name="company_value" id="company_value" value="<?php echo $company_valuationsamt; ?>" data-a-sign="<?php echo $_SESSION['config']['currency_symbol']; ?>">
                <span id="company_value_error" class="text-danger"></span>

            </div>
            <div class="form-group">
                <span class="form-label">Number of Share At The Time Of Valuation <span class="text-danger">*</span></span>
                <?php
                $old_share_qty = 0;
                if($companyval['old_share_qty'] !=""){
                  $old_share_qty = $companyval['old_share_qty'];
              }elseif($last_company_valuations['total_share_qty'] !=""){
                $old_share_qty = $last_company_valuations['total_share_qty'];
            }
            ?>
            <input aria-label="Old Share Qty" type="number" class="form-control text-right" name="old_share_qty"  id="old_share_qty" value="<?php echo $old_share_qty; ?>" onblur="calculateShareQTY()">
            <span id="old_share_qty_error" class="text-danger"></span>
        </div>
        <div class="form-group">
            <span class="form-label">Valuation For % Of Shareholding Including Convertible Pref Shares If Any <span class="text-danger">*</span></span>
            <div class="input-group mb-0">
                <input aria-label="Valuation Shareholding" type="text" class="form-control text-right" name="valuation_shareholding" id="valuation_shareholding" value="<?php echo $companyval['valuation_shareholding']; ?>" onblur="calculateShareQTY()">
                <span class="input-group-text addon-style">
                    <span class="">%</span>
                </span>
            </div>
            <span id="valuation_shareholding_error" class="text-danger"></span>
        </div>
        <div class="form-group">
            <span class="form-label">New Share To Be Issue To The New Investor Including Esop</span>
            <input aria-label="New Share Qty" type="number" class="form-control text-right" name="new_share_qty" id="new_share_qty" onblur="calculateShareQTY()" value="<?php echo $companyval['new_share_qty']; ?>" readonly>
        </div>
        <div class="form-group col-lg-6">
            <span class="form-label">Total Share</span>
            <input aria-label="Total Share Qty" type="text" class="form-control text-right" name="total_share_qty" readonly="" id="total_share_qty" value="<?php echo $companyval['total_share_qty']; ?>">
        </div>
        <div class="form-group col-lg-6">
            <span class="form-label">Per Share Value</span>
            <input aria-label="Per Share Value" type="text" class="form-control text-right autonumber" name="per_share_value" value="<?php echo $companyval['per_share_value']; ?>" id="per_share_value" data-a-sign="<?php echo $_SESSION['config']['currency_symbol']; ?>" readonly>
        </div>
        <div class="form-group">
            <span class="form-label">Notes</span>
            <textarea aria-label="Notes" class="form-control" name="notes"><?php echo $companyval['notes']; ?></textarea>
        </div>
    </div>
    <div>
        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
        <input type="hidden" name="submitcompanyvaluations">
        <?php if($rid ==""){ ?>
         <button type="button" class="btn btn-primary waves-effect waves-light" name="submitcompanyvaluations" onclick="formSubmit()"><i class="fa fa-check m-r-10"></i>Submit</button>
     <?php }else{ ?>
        <button type="button" class="btn btn-primary waves-effect waves-light" name="submitcompanyvaluations" onclick="formSubmit()"><i class="fa fa-check m-r-10"></i>Update</button>
    <?php } ?>


</div>

</div>
</div>
</div>
<div class="col-lg-9" >
    <div class="card">
        <div class="card-header">
          <h5>CAP Table</h5>
          <div class="card-header-right">
            <a href="index.php?option=COM_INSERT_COMPANY_CONTRIBUTION_USERS" class="btn btn-success mb-2 desktop-view m-r-10" ><i class="icofont icofont-plus icon-white "></i> Contributors </a>
        </div>
    </div>
    <div class="card-body <?php echo ($rid !="")?"":"event_disabled"; ?>" id="cap_table">
        <div id="addmorediv">
            <div class="row">
                <div class="form-group col-lg-3 f-w-700">
                    <span >Name</span>
                </div>
                <div class="form-group col-lg-1 text-center f-w-700 ">
                    <span class="d-block">Current </span>(%)
                </div>
                <div class="form-group col-lg-1 text-center f-w-700">
                    <span class="d-block">Old </span>Shares
                </div>
                <div class="form-group col-lg-1 text-center f-w-700">
                    <span class="d-block">Anti</span> Dilution
                </div>
                <div class="form-group col-lg-1 text-center f-w-700">
                    <span class="d-block">New </span> (%)
                </div>
                <div class="form-group col-lg-1 text-center f-w-700">
                    <span class="d-block">New </span>Shares
                </div>
                <div class="form-group col-lg-1 text-center f-w-700">
                    <span class="d-block">Total </span>Shares
                </div>
                <div class="form-group col-lg-1 text-center f-w-700">
                    <span class="d-block">Equity </span>(%)
                </div>
                <div class="form-group col-lg-1 text-center f-w-700">
                    <span class="d-block">Pref. </span>(%)
                </div>
            </div>


        <?php

        $alreadyselectuser = array();
        $oldper =$newper =$oldshare=$newshare = $tatshare = $eqper=$eqpref =  0;
        $i=1;
        if(count((array)$company_valuation_distributions) != 0){
          $loopuserlist = $company_valuation_distributions;
          $last_valuation_result = $last_valuation_total_share = array();
      }else{
          $loopuserlist = $last_valuation_total_share;
      }

      foreach($loopuserlist as $uuid=>$valu){

        $values = $company_contribution_users[$uuid];

        $alreadyselectuser[$values['id']]= $values['id'];
        $insertval = $company_valuation_distributions[$values['id']];

        $dilution  =$insertval['dilution'] !=""?$insertval['dilution']:$values['dilution'];
        $dilution = $dilution !=""?$dilution:1;

        $distribute_percentage = $insertval['distribute_percentage']!=""?$insertval['distribute_percentage']:$last_valuation_result[$values['id']];

        $oldpercentage = $insertval['old_percentage']!=""?$insertval['old_percentage']:$distribute_percentage;
        $oldpercentage = $oldpercentage!=""?$oldpercentage:0.00;
        $totaldis +=round($distribute_percentage,3);

        $new_percentage = $insertval['new_percentage']!=""?$insertval['new_percentage']:$distribute_percentage;
        $new_percentage = $new_percentage!=""?$new_percentage:0.00;

        $total_share = $insertval['total_shares']!=""?$insertval['total_shares']:$last_valuation_total_share[$values['id']];

        $old_shares_qty = $insertval['old_shares']!=""?$insertval['old_shares']:$total_share;

        $new_shares_qty = $insertval['new_shares']!=""?$insertval['new_shares']:'0';

        $oldper +=$oldpercentage;
        $newper +=$distribute_percentage;
        $oldshare +=$old_shares_qty;
        $newshare +=$new_shares_qty;
        $tatshare +=$total_share;
        $percentredonly = '';
        if($oldpercentage != '0' && $oldpercentage != ''){
            $percentredonly = 'readonly';
        }
        ?>
        <div class="row" id="deleteid_<?php echo $i; ?>">
            <input type="hidden"  name="irid[]"  value="<?php echo $insertval['id']; ?>">
            <input type="hidden"  name="ccuid[]"  value="<?php echo $values['id']; ?>">

            <div class="form-group col-lg-3">

               <input type="hidden" class="form-control" name="distributer_name[]"  value="<?php echo $values['display_name']; ?>" >

               <div class="align-middle">
                <?php if($values['image'] !="") {?>
                    <img src="uploads/all/<?php echo $values['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                <?php } else { ?>

                    <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($values['display_name']); ?></span>
                <?php } ?>
                <div class="d-inline-block width73">
                    <span class="f-12 text-capitalize"><?php echo $values['display_name']; ?></span>
                    <p class="text-muted m-b-0 text-capitalize"><?php echo $contributionutype[$values['user_type']]; ?></p>
                </div>
            </div>

        </div>
        <div class="form-group col-lg-1 text-right p-t-15">
         <input type="hidden" id="old_percentage_temp_<?php echo $values['id']; ?>" value="<?php echo $oldpercentage; ?>">
         <input type="hidden" name="old_percentage[]" value="<?php echo $oldpercentage; ?>">
         <?php echo $oldpercentage; ?>%
     </div>
     <div class="form-group col-lg-1 text-right p-t-15">
        <input type="hidden" class="form-control text-right oldshareuser" name="old_shares_qty[]" id="old_shares_qty_text_<?php echo $values['id']; ?>"  data-id="<?php echo $values['id']; ?>" value="<?php echo $old_shares_qty; ?>">

        <input type="hidden" class="form-control text-right" name="new_shares_qty[]" id="new_share_qty_text_<?php echo $values['id']; ?>" value="<?php echo $new_shares_qty; ?>">

        <?php echo $old_shares_qty !=""?$old_shares_qty:0; ?></span>
    </div>
    <?php   if($values['user_type'] == 'E'){
        $antcss = 'style="display:none"';
        $antinfocss ='style="display:block"';
    }else{
        $antcss = 'style="display:block"';
        $antinfocss = 'style="display:none"';
    } ?>
    <div class="form-group col-lg-1 p-t-15 text-center" <?php echo $antcss; ?>>
        <input type="hidden" name="dilution_seq[]" value="<?php echo $values['id']; ?>">
        <span onclick="dilutionCheck('<?php echo $values['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
          <input aria-label="Checkbox" class="js-primary-sm" type="checkbox" name="dilution[<?php echo $values['id']; ?>]" <?php echo ($dilution=='0')?"checked":"";?> value="0"  id="dilution_<?php echo $values['id']; ?>">
      </span>
  </div>
  <div class="form-group col-lg-1 p-t-15 text-center" <?php echo $antinfocss; ?>>
     <i class="fa fa-info-circle f-22" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="ESOP Not Allowed Anti Dilution" ></i>

 </div>

                                       <div class="form-group col-lg-1 p-0">

                                        <input type="hidden" id="new_percentage_temp_<?php echo $values['id']; ?>" name="new_percentage[]"  value="<?php echo $new_percentage; ?>">
                                        <input aria-label="Distribute Percentage[]" type="number" class="form-control text-right distributionqty" name="distribute_percentage[]" id="distribute_percentage_<?php echo $values['id']; ?>" value="<?php echo $distribute_percentage; ?>"  onblur="calculateDistributionQTY()" data-id="<?php echo $values['id']; ?>" <?php echo $percentredonly; ?>>
                                    </div>
                                    <div class="form-group col-lg-1 text-right p-t-15">
                                        <span id="new_share_qty_label_<?php echo $values['id']; ?>"> <?php echo $new_shares_qty; ?></span>
                                    </div>
                                    <div class="form-group col-lg-1 text-right p-t-15">
                                        <input type="hidden" class="form-control text-right total_share_class" name="total_share[]" id="total_share_qty_text_<?php echo $values['id']; ?>" value="<?php echo $total_share; ?>">

                                        <span id="total_share_qty_label_<?php echo $values['id']; ?>"> <?php echo $total_share !=""?$total_share:0; ?></span>
                                    </div>
                                    <div class="form-group col-lg-1 p-1">


                                        <input aria-label="Equity Shares Percentage[]" type="number" class="form-control text-right" name="equity_shares_percentage[]" value="<?php echo $insertval['equity_shares_percentage'] !=""?$insertval['equity_shares_percentage']:100; ?>">
                                    </div>
                                    <div class="form-group col-lg-1 p-1">
                                        <input aria-label="Preference Shares Percentage[]" type="number" class="form-control text-right" name="preference_shares_percentage[]" value="<?php echo $insertval['preference_shares_percentage']!=""?$insertval['preference_shares_percentage']:0.00; ?>">
                                    </div>
                                </div>
                                <?php $i++;
} ?>

                            </div>
                            <div class="row mt-3">
                                <div class="form-group col-lg-3">
                                    <span class="f-w-600">Total</span>
                                </div>
                                <div class="form-group col-lg-1 text-right">
                                    <span class="f-w-600" id="total_oldper"><?php echo customNanAndEmptyValidation(round($oldper,2)); ?></span>
                                </div>
                                <div class="form-group col-lg-1 text-right">
                                   <span class="f-w-600"><?php echo customNanAndEmptyValidation($oldshare); ?></span>
                               </div>
                               <div class="form-group col-lg-1">

                               </div>

                               <div class="form-group col-lg-1 text-right" >
                                <span class="f-w-600" id="total_newper"><?php echo customNanAndEmptyValidation(round($newper,2)); ?></span>
                            </div>
                            <div class="form-group col-lg-1 text-right">
                               <span class="f-w-600" id="total_oldshare_and_newshare"><?php echo customNanAndEmptyValidation($newshare); ?></span>
                           </div>
                           <div class="form-group col-lg-1 text-right">
                            <span class="f-w-600" id="total_tatshare"><?php echo customNanAndEmptyValidation($tatshare); ?></span>
                        </div>
                        <div class="form-group col-lg-1 text-right">

                        </div>
                        <div class="form-group col-lg-1 text-right">

                        </div>
                    </div>
                </div>
                <div class="card-footer pt-0">
                    <div class="row">
                        <span class="text-danger" id="error_msg"></span>
                        <div class="col-lg-3 done_panel w-full" style="display: none">
                            <select aria-label="New Invesert" id="new_invesert_id" class="form-control form-select js-example-basic-single" >
                              <option value="">Select Any One </option>
                              <?php foreach($company_contribution_users as $ccu ) {  ?>
                                <option value="<?php echo $ccu['id']."-".$ccu['display_name']."-".$contributionutype[$ccu['user_type']];?>" <?php echo (in_array($ccu['id'], $alreadyselectuser))?"disabled":""; ?>><?php echo $ccu['display_name'];?></option>
                            <?php } ?>
                            <option value="E">Add ESOP</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-2 done_panel" onclick="creatInvestor()"style="display: none"><i class="fa fa-check m-r-10 " ></i>Add</button>

                    <button type="button" class="btn btn-success waves-effect waves-light float-left col-lg-2" id="addmore_btn" onclick="addMore()"><i class="fa fa-check m-r-10"></i>Add More</button>
                </div>
                <span class="float-end">
                    <span class="text-danger">Un-Allocated (%) : </span>
                    <span id="totaldistributionpending"><?php echo 100-round($totaldis,2); ?></span>
                    <span class="text-warning m-l-20">Total Distributions (%) : </span>
                    <span id="totaldistributionqty"><?php echo round($totaldis,2); ?></span></span>
                </div>
            </div>
        </div>
    </form>
</div>
</div>
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>Valuations</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive custom-nowrap">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th scope="col" style="width:30%;"></th>
                            <?php foreach($companyvaluationyearly as $yearkey=>$cval){ ?>
                                <th scope="col" class="text-center" colspan="<?php echo count((array)$cval); ?>"><?php echo $yearkey; ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th scope="col">Valuation Name</th>
                            <?php foreach($companyvaluationyearly as $cval){
                                foreach($cval as $valuse){ ?>
                                    <th scope="col" class="text-center"><?php echo $valuse['valuation_name']; ?></th>
                                <?php } ?>
                                <?php } ?>
                            </tr>
                            <tr>
                                <th scope="col">Valuation Date</th>
                                <?php foreach($companyvaluationyearly as $cval){
                                    foreach($cval as $valuse){ ?>
                                        <th scope="col" class="text-center"><?php echo fromDate($valuse['valuation_date']); ?></th>
                                    <?php } ?>
                                    <?php } ?>
                                </tr>
                                <tr>
                                    <th scope="col">Company Value</th>
                                    <?php foreach($companyvaluationyearly as $cval){
                                        foreach($cval as $valuse){ ?>
                                            <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($valuse['company_value']); ?></th>
                                        <?php } ?>
                                        <?php } ?>
                                    </tr>
                                    <tr>
                                        <th scope="col">Number of Share at the time of Valuation</th>
                                        <?php foreach($companyvaluationyearly as $cval){
                                            foreach($cval as $valuse){ ?>
                                                <th scope="col" class="text-right"><?php echo $valuse['old_share_qty']; ?></th>
                                            <?php } ?>
                                            <?php } ?>
                                        </tr>
                                        <tr>
                                            <th scope="col">Valuation for % of shareholding including Convertible Pref Shares if any</th>
                                            <?php foreach($companyvaluationyearly as $cval){
                                                foreach($cval as $valuse){ ?>
                                                    <th scope="col" class="text-right"><?php echo $valuse['valuation_shareholding']; ?></th>
                                                <?php } ?>
                                                <?php } ?>
                                            </tr>
                                            <tr>
                                                <th scope="col">New total Number of share</th>
                                                <?php foreach($companyvaluationyearly as $cval){
                                                    foreach($cval as $valuse){ ?>
                                                        <th scope="col" class="text-right"><?php echo $valuse['new_share_qty']; ?></th>
                                                    <?php } ?>
                                                    <?php } ?>
                                                </tr>
                                                <tr>
                                                    <th scope="col">New share to be issue to the new investor including ESOP</th>
                                                    <?php foreach($companyvaluationyearly as $cval){
                                                        foreach($cval as $valuse){ ?>
                                                            <th scope="col" class="text-right"><?php echo $valuse['total_share_qty']; ?></th>
                                                        <?php } ?>
                                                        <?php } ?>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">Per Share Value</th>
                                                        <?php foreach($companyvaluationyearly as $cval){
                                                            foreach($cval as $valuse){ ?>
                                                                <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($valuse['per_share_value']); ?></th>
                                                            <?php } ?>
                                                            <?php } ?>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
