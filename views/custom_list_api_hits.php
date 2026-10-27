<?php
$url = API . "?mode=getapihits";
$response = callAPI($url);
$result = json_decode($response, 1);
$api_masters = $result['api_masters'];
?>
<script type="text/javascript">
     function getAPIHitsList(api_master_id){
         var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#apihitslistdetails").html(loader);
        $.ajax({
            url: "views/custom_list_api_hits_ajax.php",
            data:'api_master_id='+api_master_id,
            type: "POST",
            success:function(data){
               $("#apihitslistdetails").html(data);
               $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    }
    function syncAPIResponse(api_hit_id){
        $("#kt_modal_dynamic_popup").modal("show");
        var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#dynamic_popup1").html(loader);
        jQuery.ajax({
            url: "views/custom_list_api_hits_popup.php",
            data:'api_hit_id='+api_hit_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);

            },
            error:function (){}
        });
    }
</script>
<?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<div class="card">
    <div class="card-header">
        <h5>API Hits</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-block">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>API Masters</h5>
                        <div class="card-header-right table-card-header ">
                            <span class="form-label label label-md label-warning float-right"><?php echo count((array)$api_masters); ?></span>
                        </div>
                    </div>
                    <div class="card-block custom-scroll" style="min-height: 600px;  max-height: 500px;">

                        <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
                            <thead>
                                <tr>
                                    <th scope="col">API Type</th>
                                    <th scope="col">API Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($api_masters as $apim){ ?>
                                <tr>
                                    <td class="w-b-w-s p-2">
                                        <?php echo $apim['api_type']=="S"?"Send":"Receive"; ?>
                                    </td>
                                    <td class="w-b-w-s p-2">
                                        <span class="d-block"><?php echo $apim['api_service_provider']." (".$apim['api_name'].")"; ?></span>
                                        <div class="text-right">
                                            <div class="position-relative d-inline-block">
                                                <i class="ti ti-timer icon-dark cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Get API History" onclick="getAPIHitsList(<?php echo $apim['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                              </div>

                                        </div>

                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5>API Hits Respons</h5>
                    </div>
                    <div class="card-block custom-scroll" style="min-height: 600px; max-height: 600px;" id="apihitslistdetails">
                        <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Please Select API Master</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
