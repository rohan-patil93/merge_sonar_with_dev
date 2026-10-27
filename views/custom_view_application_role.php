<?php
if(isset($_POST['submit'])){
    $_POST['company_id']=$_SESSION['company_id'];
    $_POST['user_id']=$_SESSION['userid'];
    $url = API."?mode=inserttransactionaccesspermission&method=post";
    $response = file_post_contents($url,$_POST,'insert');
    $response2  = json_decode($response,1);
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function () {
          if (document.readyState == "complete") {
           tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
          }
        }
    </script>
    <?php
}
$rid =  encodeId($_REQUEST['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$vurl = API."?mode=userlistwithrole&roleid=".$rid;
$vdata_details = callAPI($vurl);
$result  = json_decode($vdata_details,1);
$application_roles = $result['application_roles'];
$application_process = $result['application_process'];
$application_users = $result['application_users'];
$application_process_pages = $result['application_process_pages'];
$applist = array();
foreach($application_process_pages as $app){
    $applist[$app['application_process_id']][] = $app;
}


?>
<script type="text/javascript">
    function getTransactionAccessPermission(roll_id,app_id,sync="",transaction_access_permission_id=""){
        $(".notselect").html('');
        $("#selecticon_"+app_id).html('<i class="fa fa-check-circle text-warning f-16"></i>');
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#userassignajax").html(loader);
        $.ajax({
            url: "views/custom_view_application_role_ajax.php",
            type: "POST",
            data: 'roll_id='+roll_id+'&app_id='+app_id+'&ajax_call=Y&sync='+sync+'&transaction_access_permission_id='+transaction_access_permission_id,
            success: function(response){
                console.log(response);
                $("#userassignajax").html(response);
                if($(".js-success-sm").length  > 0){
                    var elem = Array.prototype.slice.call(document.querySelectorAll('.js-success-sm'));
                    elem.forEach(function(html) {
                        var switchery = new Switchery(html, { color: '#2ed8b6', jackColor: '#fff',size: 'small' });
                    });
                }
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: date_format,
                    minDate: +1,
                    maxDate: "+10D",
                    setDate: new Date(),
                    changeMonth: true,
                    changeYear: true
                }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
            }
        });
    }
</script>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid " >
        <div class="card card-custom card-sticky " id="kt_page_sticky_card">
            <div class="card-header">
                <h5> Permissions List</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light ">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body" >
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="text-capitalize mb-0"><?php echo $application_roles['display_name']; ?></h5>
                            </div>
                            <div class="card-body">
                                    <div class="card-block accordion-block color-accordion-block p-0">
                                        <div class="color-accordion" id="color-accordion">
                                        <?php
                                            foreach($application_process as $resutlvalue2){
                                        ?>
                                            <a class="accordion-msg b-none waves-effect waves-light"><?php echo $resutlvalue2['process_name']; ?></a>
                                            <div class="accordion-desc">
                                            <?php if(count((array)$applist[$resutlvalue2['id']])){ ?>
                                                <ul class="legend list-unstyled text-gray-600" >
                                                <?php foreach($applist[$resutlvalue2['id']] as $resutlvalue){
                                                        ?>
                                                    <li class="mt-3 cursor-pointer">
                                                        <a href="#!" class="text-reset text-decoration-none d-block" onclick="getTransactionAccessPermission('<?php echo $rid; ?>','<?php echo $resutlvalue['application_process_pages_id']; ?>')">
                                                            <span class="icon"><i class="fa fa-minus"></i></span>
                                                            <span class="name"><?php echo mb_convert_case($resutlvalue['name'], MB_CASE_TITLE, "UTF-8"); ?></span>
                                                            <span class="notselect ml-2" id="selecticon_<?php echo $resutlvalue['application_process_pages_id']; ?>"></span>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                </ul>
                                            <?php }else{ ?>
                                                <div class="card-px text-center ">
                                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Process Page</p>
                                                    <div class="text-center px-4">
                                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 100px!important;">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            </div>
                                        <?php } ?>
                                        </div>
                                    </div>

                                <div class="ln_solid"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9" id="userassignajax">
                        <?php

                         <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Please Select Process Page?</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
