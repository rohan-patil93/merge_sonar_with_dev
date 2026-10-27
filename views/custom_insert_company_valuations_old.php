<?php
$_SESSION['apply_js']['table']= 1;
if(isset($_POST['submitcompanyvaluations'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertcompanyvaluations&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
   //echo "<pre>"; print_r($res); echo "</pre>";
    $response = $res['res'];
    if($response['status'] == 'sucess'){ 
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
        }   
        $_SESSION['insert_msg'] = $msgcode;     
    }else{
        //($response['status'] == 'fail'){
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

    //header("location:index?option=COM_INSERT_COMPANY_VALUATIONS");
}else{
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
$company_contribution_users = $result['company_contribution_users'];
$contribution_users_type = $result['contribution_users_type'];
$last_valuation_result = $result['last_valuation_result'];
$last_valuation_total_share = $result['last_valuation_total_share'];
$companyvaluationyearly = array();
foreach($companyvallist as $cvl){
    $year = date("Y",strtotime($cvl['valuation_date']));
    $companyvaluationyearly[$year][] = $cvl;
}
ksort($companyvaluationyearly);

foreach($contribution_users_type as $cvl){
    $contributionutype[$cvl['genc_detail_code']] = $cvl['genc_detail_name'];
}


//echo "<pre>"; print_r($result); echo "</pre>";
?>
<script type="text/javascript">
    function calculateShareQTY(){

        var valuation_shareholding = $("#valuation_shareholding").val();
        var old_share_qty = $("#old_share_qty").val();
        if(valuation_shareholding !="" && valuation_shareholding !='0'){
            var calculateshare = Math.round(parseInt(old_share_qty)/(100-parseFloat(valuation_shareholding))*100);
            calculateshare = parseInt(calculateshare)-parseInt(old_share_qty);
        }else{
            var calculateshare =0;
        }
        var new_share_qty = parseInt(calculateshare);
        $("#new_share_qty").val(new_share_qty);
        //var new_share_qty = $("#new_share_qty").val();
        var company_value = $("#company_value").val();
        var total_share_qty  = 0;
        if(old_share_qty !=""){
            var total_share_qty = parseInt(old_share_qty)+parseInt(new_share_qty);

            if(company_value !=0 && company_value !=""){
                var per_share_value = parseFloat(company_value)/parseInt(total_share_qty);
                $("#per_share_value").val(per_share_value.toFixed(2));
               
               // console.log("total_share_qty -"+total_share_qty+" company_value -"+company_value+" per_share_value - "+per_share_value);
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
                    $("#new_percentage_temp_"+oldshareid).val(parseFloat(calculateper1).toFixed(2));
                    $("#distribute_percentage_"+oldshareid).val(parseFloat(calculateper).toFixed(2));
                }else{
                     //console.log("dilution  No---"+oldshareid);
                    var calculateper = (parseInt(oldshare)/parseInt(total_share_qty))*100;
                    $("#new_percentage_temp_"+oldshareid).val(parseFloat(calculateper).toFixed(2));
                    $("#distribute_percentage_"+oldshareid).val(parseFloat(calculateper).toFixed(2));
                }
                

                total_distribution +=parseFloat(calculateper);
               
            }
           // console.log(total_share_qty+" -- "+oldshare+" -- "+calculateper+" -- "+total_distribution);
            //totaldistributionqty +=parseFloat($(this).val());
        });
        
        calculateDistributionQTY();

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
    maxrows = maxrows+1;
    var html = '';
    html += '<div class="row" id="deleteid_'+maxrows+'">';
    html += '<div class="form-group col-lg-7"><input type="text" class="form-control" name="distributer_name[]" ></div>';
    html += '<div class="form-group col-lg-4"><input type="number" class="form-control text-right distributionqty" name="distribute_percentage[]" onblur="calculateDistributionQTY()"></div>';
    html += '<div class="form-group col-lg-1"><i class="ti-trash"  onclick="deletRow('+maxrows+')"></i> </div>';
    html += '</div>';
    $("#addmorediv").append(html);
  }    
  function deletRow(id){
    $("#deleteid_"+id).remove();
    calculateDistributionQTY();
  }
  function calculateDistributionQTY(){
    //var distributionqty = $(".new_share_qty_").val();
    //console.log(distributionqty);
    var totaldistributionqty = 0;
    var total_share_qty = $("#total_share_qty").val();

    var total_old_share = 0;
    var total_new_share = 0;
    var total_share = 0;
    $(".distributionqty").each(function(){
        var rowidid = $(this).attr('data-id');
        var old_shares_qty = $('#old_shares_qty_text_'+rowidid).val();
        old_shares_qty = (old_shares_qty!="" && old_shares_qty!="0")?old_shares_qty:0;
        //console.log("listtotal - "+$(this).val());
        //var newhsareallocated = 
        if($(this).val() !=""){
            var percentage = $(this).val();
            totaldistributionqty +=parseFloat(percentage);

            var calc = Math.round((percentage/100)*total_share_qty);
            if(old_shares_qty != 0 || old_shares_qty != ''){
                var new_share_qty =calc-old_shares_qty;
            }else{
                var new_share_qty =calc;
            }
            //console.log("rowidid - "+rowidid+" - old_shares_qty-"+old_shares_qty+" new_share_qty - "+new_share_qty+" calc - "+calc);
            $("#new_share_qty_text_"+rowidid).val(new_share_qty);
            $("#new_share_qty_label_"+rowidid).text(new_share_qty);
            $("#total_share_qty_text_"+rowidid).val(calc);
            $("#total_share_qty_label_"+rowidid).text(calc);
            
            total_old_share =parseInt(total_old_share) + parseInt((old_shares_qty != '0' || old_shares_qty != '')?old_shares_qty:0);
            total_new_share =parseInt(total_new_share) + parseInt((new_share_qty != '0' || new_share_qty != '')?new_share_qty:0);
            total_share = parseInt(total_share) + parseInt((calc != '0' || calc != '')?calc:0);
           // console.log("total_old_share - "+total_old_share+" - total_new_share-"+total_new_share+" total_share - "+total_share);
        }
    });
    if(isNaN(totaldistributionqty)){
        totaldistributionqty = 0;
    }
    var  totaldistributionqtyround =  totaldistributionqty.toFixed(2); 
    var  totaldistributionpending =  (100-parseFloat(totaldistributionqty)).toFixed(2);
    $("#totaldistributionqty").text(totaldistributionqtyround);
    $("#totaldistributionpending").text(totaldistributionpending);
  
  $("#total_newper").text(totaldistributionqtyround);
  //$("#total_oldshare_and_newshare").text(Math.round(total_old_share)+" / "+Math.round(total_new_share));
    $("#total_oldshare_and_newshare").text(Math.round(total_new_share));
  $("#total_tatshare").text(Math.round(total_share));
   // $("#total_oldper").text();
    // $("#total_newper").html(totaldistributionqtyround.toFixed(2));
    // $("#total_oldshare_and_newshare").html();
    // $("#total_tatshare").html(Math.round(total_share));


  }
  // function formSubmit(){
  //   var totaldistributionqty = 0;
  //   var error_message_9074 = $("#error_message_9074").val();
  //   var error_message_9075 = $("#error_message_9075").val();
  //   $(".distributionqty").each(function(){
  //       if($(this).val() !=""){
  //           totaldistributionqty +=parseFloat($(this).val());
  //       }
  //   });
  //   if(totaldistributionqty != 100){
  //       if(totaldistributionqty > 100){
  //           msg = error_message_9074;
  //       }else{
  //           msg = error_message_9075;
  //       }
  //       openPopUp();
  //       swal({
  //           title: "<?php //echo ucwords($_SESSION['config']['name']); ?>",
  //           text: msg,
  //           type: "warning",
  //           showCancelButton: true,
  //           confirmButtonClass: "btn-danger",
  //           confirmButtonText: "Yes",
  //           cancelButtonText: "No",
  //           closeOnConfirm: true,
  //           closeOnCancel: true
  //       },
  //       function(isConfirm) {
  //           if(isConfirm) {
  //               if(totaldistributionqty <= 100){
  //                   $("#formsubmitid").submit();
  //               }
  //           }
  //       });
  //   }else{
  //        $("#formsubmitid").submit();
  //   }
  // }
  function formSubmit(){
    var totaldistributionqty =total_share_cnt =  0;
    var error = 0;
    var formsubmit = 0;
    var error_message_9074 = $("#error_message_9074").val();
    var error_message_9075 = $("#error_message_9075").val();
    var error_message_9076 = $("#error_message_9076").val();
    var error_message_9077 = $("#error_message_9077").val();
    var total_share_qty = $("#total_share_qty").val();
    $(".distributionqty").each(function(){
        if($(this).val() !=""){
            totaldistributionqty +=parseFloat($(this).val());
        }
    });
    if(totaldistributionqty != 100){
        if(totaldistributionqty > 100){
            msg = error_message_9074;
        }else{
            msg = error_message_9075;
            formsubmit = 1;
        }
        error = 1;
        console.log(msg);
        openPopUp();
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
        openPopUp();
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
  }
  function checkTotal(){
        var totaldistributionqty = total_share_cnt =  0;
        var error_message_9074 = $("#error_message_9074").val();
        var error_message_9075 = $("#error_message_9075").val();
        var error_message_9076 = $("#error_message_9076").val();
        var error_message_9077 = $("#error_message_9077").val();
        var total_share_qty = $("#total_share_qty").val();

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
            openPopUp();
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: msg,
                type: "warning",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            });
            return false;
        }else{
            closePopUp();
        }
        $(".distributionqty").each(function(){
            if($(this).val() !=""){
                totaldistributionqty = parseFloat(totaldistributionqty) + parseFloat($(this).val());
                console.log($(this).val()+" --- "+totaldistributionqty);
            }
        });
        var msg ="";
        console.log(totaldistributionqty);
        if(totaldistributionqty != 100){
            if(totaldistributionqty > 100){
                msg = error_message_9074;
            }else{
                msg = error_message_9075;
            }
            openPopUp();
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: msg,
                type: "warning",
                showCancelButton: false,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            });
        }else{
             closePopUp();
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
    //var distribute_percentage = 
}

</script>
    <?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>  
<?php } ?>
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5>Valuation</h5>
                <div class="card-header-right table-card-header">
<!--                    <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                      <i class="ki ki-long-arrow-back icon-xs"></i>
                      Back
                   </a> -->
                </div>
            </div>
            <div class="card-body">
                <input type="hidden" id="error_message_9074" value="<?php echo $_SESSION['error_message']['9074']; ?>">
                <input type="hidden" id="error_message_9075" value="<?php echo $_SESSION['error_message']['9075']; ?>">
                <input type="hidden" id="error_message_9076" value="<?php echo $_SESSION['error_message']['9076']; ?>">
                <input type="hidden" id="error_message_9077" value="<?php echo $_SESSION['error_message']['9077']; ?>">

                <form method="post" id="formsubmitid">
                    <div class="row">
                        <div class="form-group">
                            <label class="form-label">Valuation Name</label>
                            <input type="text" class="form-control" name="valuation_name" value="<?php echo $companyval['valuation_name']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Valuation Date</label>
                            <input type="text" class="form-control datepicker" name="valuation_date" value="<?php echo $companyval['valuation_date']; ?>">   
                        </div>
                        <div class="form-group">
                            <label class="form-label">Company Value</label>
                            <input type="text" class="form-control text-right" name="company_value" id="company_value" value="<?php echo $companyval['company_value']; ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Number of Share At The Time Of Valuation</label>
                            <input type="number" class="form-control text-right" name="old_share_qty"  id="old_share_qty" value="<?php echo $companyval['old_share_qty']; ?>" onblur="calculateShareQTY()">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Valuation For % Of Shareholding Including Convertible Pref Shares If Any</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-right" name="valuation_shareholding" id="valuation_shareholding" value="<?php echo $companyval['valuation_shareholding']; ?>" onblur="calculateShareQTY()">
                                <span class="input-group-text addon-style">
                                    <label class="">%</label>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">New Share To Be Issue To The New Investor Including Esop</label>
                            <input type="number" class="form-control text-right" name="new_share_qty" id="new_share_qty" onblur="calculateShareQTY()" value="<?php echo $companyval['new_share_qty']; ?>" readonly>
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="form-label">Total Share</label>
                            <input type="text" class="form-control text-right" name="total_share_qty" readonly="" id="total_share_qty" value="<?php echo $companyval['total_share_qty']; ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <label class="form-label">Per Share Value</label>
                                <input type="text" class="form-control text-right" name="per_share_value" value="<?php echo $companyval['per_share_value']; ?>" id="per_share_value" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes"><?php echo $companyval['notes']; ?></textarea>
                        </div>
                    </div> 
                    <div id="custom-popup">
                        <input type="hidden" name="rid" value="<?php echo $rid; ?>">  
                          <input type="hidden" name="submitcompanyvaluations">  
                        <?php if($rid ==""){ ?>
                             <button type="button" class="btn btn-primary waves-effect waves-light" name="submitcompanyvaluations" onclick="formSubmit()"><i class="fa fa-check m-r-10"></i>Submit</button>
                        <?php }else{ ?>
                            <button type="button" class="btn btn-primary waves-effect waves-light" name="submitcompanyvaluations" onclick="formSubmit()"><i class="fa fa-check m-r-10"></i>Update</button>
                        <?php } ?>

                        <a herf="" class="btn btn-success waves-effect waves-light float-end" onclick="openPopUp()" ><i class="fa fa-sitemap"></i> Cap Table</a>

                        <div class="custom-popup-open custom-popup-slide-query-contact_details text-start" style="display: none;left: 100%;top: -55px; width: 350%;">
                            <div class="card">
                                <div class="card-header">
                                  <h5>Cap Table</h5>
                                  <div class="card-header-right">
                                    <a href="index.php?option=COM_INSERT_COMPANY_CONTRIBUTION_USERS" class="btn btn-success mb-2 desktop-view m-r-10" target="_blank"><i class="icofont icofont-plus icon-white "></i> Investor </a>

                                    <i class="ti-close" onclick="closePopUp()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                                  </div>
                                </div>
                                <div class="card-body" id="addmorediv"> 
                                    <div class="row">
                                         
                                        <div class="form-group col-lg-3">
                                            <label >Name</label>
                                        </div>
                                        <div class="form-group col-lg-1 text-center">
                                            Current </br> (%)
                                        </div>
                                        <div class="form-group col-lg-1 text-center">
                                            Old </br>Shares
                                        </div>
                                        <div class="form-group col-lg-1 text-center">
                                            Anti </br>Dilution 
                                        </div>
                                        
                                        <div class="form-group col-lg-1 text-center">
                                            <label>New </br>(%)</label>
                                        </div>
                                        
                                        <div class="form-group col-lg-1 text-center">
                                            New </br>Shares
                                        </div>
                                        <div class="form-group col-lg-1 text-center">
                                            Total </br>Shares
                                        </div>
                                        <div class="form-group col-lg-1 text-center">
                                            <label >Equity </br> (%)</label>
                                        </div>
                                        <div class="form-group col-lg-1 text-center">
                                            <label >Preference </br> (%)</label>
                                        </div>
                                    </div>
                                    <div class="custom-scroll" style="max-height: 500px;">
                                     <?php 
                                $oldper =$newper =$oldshare=$newshare = $tatshare = $eqper=$eqpref =  0;     
                                $i=1; foreach($company_contribution_users as $values){ 
                                    
                                    if(count((array)$company_valuation_distributions) != 0){
                                        $last_valuation_result = $last_valuation_total_share = array();
                                    }

                                    $insertval = $company_valuation_distributions[$values['id']];
                                    
                                    $dilution  =$insertval['dilution'] !=""?$insertval['dilution']:$values['dilution'];
                                    $dilution = $dilution !=""?$dilution:1;

                                    $distribute_percentage = $insertval['distribute_percentage']!=""?$insertval['distribute_percentage']:$last_valuation_result[$values['id']];
                                    
                                    $oldpercentage = $insertval['old_percentage']!=""?$insertval['old_percentage']:$distribute_percentage;
                                    $oldpercentage = $oldpercentage!=""?$oldpercentage:0.00; 
                                    $totaldis +=$distribute_percentage;
                                    
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
                                    if($oldpercentage != '0' AND $oldpercentage != ''){
                                        $percentredonly = 'readonly';
                                    }

                                    ?>
                                    <div class="row" id="deleteid_<?php echo $i; ?>">
                                        <input type="hidden"  name="irid[]"  value="<?php echo $insertval['id']; ?>">
                                        <input type="hidden"  name="ccuid[]"  value="<?php echo $values['id']; ?>">
                                       <!--  <div class="form-group col-lg-1"></div> -->
                                        <div class="form-group col-lg-3">
                                                   <!--  <span class="f-12 float-left">1</span> -->
                                            <input type="hidden" class="form-control" name="distributer_name[]"  value="<?php echo $values['display_name']; ?>" >
                                           <!--  <span><?php //echo $contributionutype[$values['user_type']]; ?></span> -->
  
            

<div class="align-middle">
    <?php if($values['image'] !="") {?>
        <img src="uploads/all/<?php echo $values['image']; ?>"   class="img-radius img-30 align-top m-r-15" onerror="this.src='uploads/all/no-image.jpg';">
    <?php } else { ?>

        <span  alt="user image" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($values['display_name']); ?></span>
    <?php } ?>
    <div class="d-inline-block width73">
        <span class="f-12"><?php echo $values['display_name']; ?></span>
        <p class="text-muted m-b-0"><?php echo $contributionutype[$values['user_type']]; ?></p>
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
                                        <div class="form-group col-lg-1 p-t-15 text-center">
                                            <input type="hidden" name="dilution_seq[]" value="<?php echo $values['id']; ?>">
                                            <span onclick="dilutionCheck('<?php echo $values['id']; ?>')">
                                          <input class="js-primary-sm" type="checkbox" name="dilution[<?php echo $values['id']; ?>]" <?php echo ($dilution=='0')?"checked":"";?> value="0"  id="dilution_<?php echo $values['id']; ?>">
                                          </span>
                                        </div>
                                        
                                        <div class="form-group col-lg-1 p-0">
                                           
                                            <input type="hidden" id="new_percentage_temp_<?php echo $values['id']; ?>" name="new_percentage[]"  value="<?php echo $new_percentage; ?>">
                                            
                                            <input type="number" class="form-control text-right distributionqty" name="distribute_percentage[]" id="distribute_percentage_<?php echo $values['id']; ?>" value="<?php echo $distribute_percentage; ?>"  onblur="calculateDistributionQTY()" data-id="<?php echo $values['id']; ?>" <?php echo $percentredonly; ?>>
                                        </div>
                                        <div class="form-group col-lg-1 text-right p-t-15">
                                                <span id="new_share_qty_label_<?php echo $values['id']; ?>"> <?php echo $new_shares_qty; ?></span>
                                        </div>
                                        <div class="form-group col-lg-1 text-right p-t-15">
        <input type="hidden" class="form-control text-right total_share_class" name="total_share[]" id="total_share_qty_text_<?php echo $values['id']; ?>" value="<?php echo $total_share; ?>">
        
        <span id="total_share_qty_label_<?php echo $values['id']; ?>"> <?php echo $total_share !=""?$total_share:0; ?></span>
                                        </div>
                                        <div class="form-group col-lg-1 p-1">


                                            <input type="number" class="form-control text-right" name="equity_shares_percentage[]" value="<?php echo $insertval['equity_shares_percentage'] !=""?$insertval['equity_shares_percentage']:100; ?>">
                                        </div>
                                        <div class="form-group col-lg-1 p-1">
                                            <input type="number" class="form-control text-right" name="preference_shares_percentage[]" value="<?php echo $insertval['preference_shares_percentage']!=""?$insertval['preference_shares_percentage']:0.00; ?>">
                                        </div> 
                                    </div>
                                <?php $i++; } ?>
                                </div>
                                <div class="row mt-3">
                                    <!--  <div class="form-group col-lg-1"></div> -->
                                        <div class="form-group col-lg-3">
                                            <span class="f-w-600">Total</span>
                                        </div>
                                        <div class="form-group col-lg-1 text-right">
                                            <span class="f-w-600" id="total_oldper"><?php echo $oldper; ?></span>
                                        </div>
                                        <div class="form-group col-lg-1 text-right">
                                           <span class="f-w-600"><?php echo $oldshare; ?></span>
                                        </div>
                                        <div class="form-group col-lg-1">
                                             
                                        </div>
                                        
                                        <div class="form-group col-lg-1 text-right" >
                                            <span class="f-w-600" id="total_newper"><?php echo $newper; ?></span>
                                        </div>
                                        <div class="form-group col-lg-1 text-right">
                                           <span class="f-w-600" id="total_oldshare_and_newshare"><?php echo $newshare; ?></span>
                                        </div>
                                        <div class="form-group col-lg-1 text-right">
                                            <span class="f-w-600" id="total_tatshare"><?php echo $tatshare; ?></span>
                                        </div>
                                        <div class="form-group col-lg-1 text-right">
                                           
                                        </div>
                                        <div class="form-group col-lg-1 text-right">
                                        
                                        </div>
                                    </div>

                                    <?php 
                                    //$as[] = array('sdfvdf'=>'sdf');
                                  /**  if(count((array)$company_valuation_distributions) =="0" || count((array)$company_valuation_distributions) ==""){
                                    	$company_valuation_distributions[] = array('distributer_name'=>'');
                                    }
                                    $i=1; foreach($company_valuation_distributions as $values){ $totaldis +=$values['distribute_percentage'];?>
                                    <div class="row" id="deleteid_<?php echo $i; ?>">
                                        <input type="hidden"  name="irid[]"  value="<?php echo $values['id']; ?>">
                                        <div class="form-group col-lg-7">
                                            <input type="text" class="form-control" name="distributer_name[]"  value="<?php echo $values['distributer_name']; ?>">
                                        </div>
                                        <div class="form-group col-lg-4">
                                            <input type="number" class="form-control text-right distributionqty" name="distribute_percentage[]" value="<?php echo $values['distribute_percentage']; ?>" onblur="calculateDistributionQTY()">
                                        </div>
                                        <div class="form-group col-lg-1">
                                            <i class="ti-trash" onclick="deletRow('<?php echo $i; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Removed"></i>
                                        </div>
                                    </div>
                                <?php $i++; } **/?>
                                </div>
                                <div class="card-footer pt-0">
                                   <!--  <button type="button" class="btn btn-success waves-effect waves-light float-left  m-r-10" onclick="addMore()"><i class="fa fa-check m-r-10"></i>Add More</button>
 -->
                                    <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" onclick="checkTotal()"> Done</button>
                                    <span class="float-end">
                                        <span class="text-danger">Un-Allocated (%) : </span>
                                        <span id="totaldistributionpending"><?php echo 100-$totaldis; ?></span>
                                        <span class="text-warning m-l-20">Total Distributions (%) : </span>
                                        <span id="totaldistributionqty"><?php echo $totaldis; ?></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-9 ui-sortable-handle">
        <div class="card">
            <div class="card-header">
                <h5>Recent Valuation</h5>
                <div class="card-header-right table-card-header">
                      <?php if($rid !=""){ ?>
                    <a href="index.php?option=COM_INSERT_COMPANY_VALUATIONS" class="btn btn-success mb-2 desktop-view"><i class="icofont icofont-plus icon-white "></i>Company Valuation</a>
                <?php } ?>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive" style="height: 800px;">
                    <?php if(count((array)$companyvallist) !=0){ ?>
                    <table id="basic-btn" class="table table-striped table-bordered nowrap" >
                        <thead>
                            <tr>
                                 <th>Valuation Date</th>
                                <th>Valuation Name</th>
                               
                                <th class="text-right">Company Value</th>
                                <th class="text-right">New Number of Share</th>
                                  <th class="text-right">Total Share</th>
                                <th class="text-right">Per Share Value</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>    
                            <?php foreach($companyvallist as $cval){?>
                            <tr>
                                <td><?php echo fromDate($cval['valuation_date']); ?></td>
                                <td><?php echo $cval['valuation_name']; ?></td>
                                
                                <td class="text-right"><?php echo customeummarmonyformateifine($cval['company_value']); ?></td>
                                <td class="text-right"><?php echo $cval['new_share_qty']; ?></td>
                                <td class="text-right"><?php echo $cval['total_share_qty']; ?></td>
                                <td class="text-right"><?php echo customeummarmonyformateifine($cval['per_share_value']); ?></td>
                                <td class="text-center"><a href="index.php?option=COM_INSERT_COMPANY_VALUATIONS&Rid=<?php echo deocdeId('id:'.$cval['id']); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit"><i class="fa fa-edit"></i></a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Company Valuations</p>   
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>   
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
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
                            <th style="width:30%;"></th>
                            <?php foreach($companyvaluationyearly as $yearkey=>$cval){ ?>
                                <th class="text-center" colspan="<?php echo count((array)$cval); ?>"><?php echo $yearkey; ?></th>
                            <?php } ?>
                        </tr>
                         <tr>
                            <th>Valuation Name</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-center"><?php echo $valuse['valuation_name']; ?></th>
                            <?php } } ?>
                        </tr>
                        <tr>
                            <th>Valuation Date</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-center"><?php echo fromDate($valuse['valuation_date']); ?></th>
                            <?php } } ?>
                        </tr>
                        <tr>
                            <th>Company Value</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-right"><?php echo customeummarmonyformateifine($valuse['company_value']); ?></th>
                            <?php } } ?>
                        </tr>
                        <tr>
                            <th>Number of Share at the time of Valuation</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-right"><?php echo $valuse['old_share_qty']; ?></th>
                            <?php } } ?>
                        </tr>
                        <tr>
                            <th>Valuation for % of shareholding including Convertible Pref Shares if any</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-right"><?php echo $valuse['valuation_shareholding']; ?></th>
                            <?php } } ?>
                        </tr>
                        <tr>
                            <th>New total Number of share</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-right"><?php echo $valuse['new_share_qty']; ?></th>
                            <?php } } ?>
                        </tr>
                        <tr>
                            <th>New share to be issue to the new investor including ESOP</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-right"><?php echo $valuse['total_share_qty']; ?></th>
                            <?php } } ?>
                        </tr>
                        <tr>
                            <th>Per Share Value</th>
                            <?php foreach($companyvaluationyearly as $cval){ 
                                    foreach($cval as $valuse){ ?>
                                <th class="text-right"><?php echo customeummarmonyformateifine($valuse['per_share_value']); ?></th>
                            <?php } } ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
