<?php 
$_SESSION['apply_js']['multiselest'] = "1";
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=getchartofaccounts";
$resultapi = callAPI($vurl);
$result = json_decode($resultapi,1);
//print_r($result);
$chart_of_accounts =$result['chart_of_accounts'];
$account_type =$result['account_type'];
$taxes =$result['taxes'];
$journals =$result['journals'];
$currencies =$result['currencies'];
$locations =$result['locations'];
$departments =$result['departments'];
$cost_centers =$result['cost_centers'];
$account_groups = $result['account_groups'];


$chartofaccounts = array();

foreach($chart_of_accounts as $finanacerepogrp){
     $financereportgroupsreport[$finanacerepogrp['parent_account_code']][] = $finanacerepogrp;
       
   // $financereportgroupsreport[$finanacerepogrp['parent_account_code']][$finanacerepogrp['account_code']] = $finanacerepogrp;
    $financereportgroups[$finanacerepogrp['account_code']] = $finanacerepogrp;
}
//echo "<pre>"; print_r($balanceaccountarray); echo "</pre>";
//echo "<pre>"; echo "testing"; print_r($financereportgroupsreport['1000100010000000']); echo "</pre>";
//print_r($financereportgroupsreport);
?>
<script type="text/javascript">
    function addFinaanceAccGrp(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
           /** var opening_balance = $("#opening_balance").val();
            if(opening_balance =="0" || opening_balance =="" || opbal == ""){
                swal({
                    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                    text: '<?php echo $_SESSION['error_message']['9034']; ?>',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger",
                    confirmButtonText: "Yes",
                    cancelButtonText: "No",
                    closeOnConfirm: true,
                    closeOnCancel: true
                },
                function(isConfirm) {
                    if (isConfirm) {
                        addFinaanceAccGrp(0);
                    }
                });
                return true;
            } **/
            var postarray = $("#financereportgrpfrom").serialize();
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=insertchartofaccounts&'+postarray,
                success: function(response) {
                    console.log(response);
                    var respon = JSON.parse(response);
                    console.log(respon.res);
                    var result = respon.res;
                    if(result.status =="fail"){
                        //alert(result.message);
                       tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.message,'');
                    }else{
                        if(result.LASTID == '0'){
                             //alert('Record Updated Successfully');
                            GetViewReport();
                             editFinanaceGrp(0);
                           tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Updated Successfully','');
                        }else{
                             //alert('Record Inserted Successfully');
                             GetViewReport();
                         editFinanaceGrp(0);
                          tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Inserted Successfully','');
                        }   
                        
                    }
                }
            });
        }
    }
    var maxrows = 1000;
    function editFinanaceGrp(id="",pargrp="", account_type = "", account_group = ""){
        maxrows= maxrows+1;
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#insertform").html(loader);
        $.ajax({
            url: "views/custom_insert_chart_of_accounts_ajax.php",
            type: "POST",
            data: 'id='+id+'&pargrp='+pargrp,
            success: function(response){
                $("#insertform").html(response);
                $('html, body').animate({
                    scrollTop: $("#insertform").offset().top
                }, 20);
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();

                if (account_type !== "") {
                    $("select[name='account_type']").val(account_type).trigger('change');
                }
                if (account_group !== "") {
                    $("select[name='account_groups']").val(account_group).trigger('change');
                }

                var elem = Array.prototype.slice.call(document.querySelectorAll('.js-primary-sm'));
                elem.forEach(function(html) {
                    var switchery = new Switchery(html, { color: '#4099ff', jackColor: '#fff',size: 'small' });
                });


            }
        });

    }
    function GetViewReport(){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#viewtable").html(loader);
        $.ajax({
            url: "views/custom_insert_chart_of_accounts_table.php",
            type: "POST",
            data: 'pageshow=Y',
            success: function(response){
                $("#viewtable").html(response);
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
            }
        });
    }
    function showChartAccount(){
        $(".custom-popup-slide").show('slow');
    }
    function closedPopup(){
        $(".custom-popup-slide").hide('slow');
    }
    function selectChartOfAccont(){
        var chart_of_account_codes = $("#chart_of_account_codes").val();
        var Rid = $("#Rid").val();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=selectchartofaccont&chart_of_account_codes='+chart_of_account_codes+'&Rid='+Rid,
            success: function(response){
                console.log(response)
                $("#chartofaccountmsg").html(response);
                showChartAccount();
            }
        });
    }
    function openAccountBalances(){
        $(".custom-popup-open").hide();
        $(".account_balances_module").show();
    }
    function closeAccountBalances(){
        $(".custom-popup-open").hide();
    }

    
function getfilterSearch(){
    var location_filter = $("#location_filter").val();
    var department_filter = $("#department_filter").val();
    var cost_center_filter = $("#cost_center_filter").val();
    

    var classname= "";
    if(location_filter !="0"){
        classname += ".matchingid_loc_"+location_filter;
    }
    if(department_filter !="0"){
        classname += ".matchingid_dep_"+department_filter;
    }
    if(cost_center_filter !="0"){
        classname += ".matchingid_cost_"+cost_center_filter;
    }
    console.log(classname);
    if(classname !=""){
        $(".hideallbalancematch").hide();
        $(classname).show();
            console.log('show');
    }else{
         $(".hideallbalancematch").show();
    }
}


function updatebalanceamount(){
    var totalbalance = 0;
    $(".balance_cnt_text").each(function() {
        var loopvalue = $(this).val();
        loopvalue = (loopvalue !="0" && loopvalue !="")?loopvalue:0;
        totalbalance = parseFloat(totalbalance)+parseFloat(loopvalue);
    });
    $("#balance_cnt_label").text("Total : "+totalbalance);
    $("#opening_balance").val(totalbalance);
    console.log(totalbalance);
}

function clickChartOfAccountLockAndUnlock(ca_status, ca_id){
    if(ca_status == 'Y'){
        var msg = "Are you sure you want to lock this account? Once locked, you will not be able to update the account balance or make any changes related to this account.";
    }else{
        var msg = "Are you sure you want to Unlock this account?";
    }
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
        if (isConfirm) {
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=chartofaccountlockandunlock&ca_status='+ca_status+'&ca_id='+ca_id,
                success: function(response){
                    console.log(response);
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Record Updated Successfully','');
                    //location.reload();
                    GetViewReport();
                    editFinanaceGrp(ca_id);
                }
            });
        }
    });
  
}

</script>

    <div class="row">
        <div class="col-lg-12">
            <div class="card p-2" id="scrollpage">
               <div class="card-header">
                    <h5>Chart Of Accounts</h5>
                    <div class="card-header-right table-card-header ">
                        <button type="button" onclick="editFinanaceGrp(0)" class="btn btn-primary mb-2 desktop-view">
                         <i class="icofont icofont-plus text-white"></i>
                          Chart Of Accounts
                        </a>
                        <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</a>
                    </div>
                </div>


                <div class="card-body">
                  <div class="row">
                    <div class="col-lg-4" id="insertform" >
                    <?php 
                        $pageurl = 'custom_insert_chart_of_accounts_ajax.php';
                         if(file_exists("views/".$pageurl)){ 
                             include_once "views/".$pageurl;
                         }else{
                            //echo $pageurl;
                             include_once $pageurl;
                            //include('custom_insert_chart_of_accounts_ajax.php');
                         }
                        ?>  
                    </div>
                    <div class="col-lg-8" id="viewtable">
                    <?php 
                        $pageurl = 'custom_insert_chart_of_accounts_table.php';
                        if(file_exists("views/".$pageurl)){ 
                            include_once "views/".$pageurl;
                        }else{
                            include_once $pageurl;
                        } 
                    ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>          