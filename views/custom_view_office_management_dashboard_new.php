<?php
    $_SESSION['apply_js']['office_management_transport'] = 1;
    $url = API."?mode=togetofficemanagementdashboard";
   $response = callAPI($url);
   $data = json_decode($response,1);
   $food = $data['food'];
?>
<script type="text/javascript">
    function recicedcourier(id=""){
        jQuery.ajax({
            url: "views/custom_insert_received_courier_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function sendcourier(id=""){
        jQuery.ajax({
            url: "views/custom_insert_send_courier_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function cartviewreqlist(val){
        jQuery.ajax({
            url: "views/custom_view_stationery_cart_details_popup.php",
            data:'option=noption&request_no='+val,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function viewfoodinsert(val){
        if(val!=0){
            jQuery.ajax({
                url: "views/custom_insert_food_request_popup.php",
                data:'option=noption&rowid='+val,
                type: "POST",
                success:function(data){
                    $('#popup1').html(data);
                    $('#kt_modal_KTDatatable_remote').modal("show");

                },
                error:function (){}
            });
        }
    }
     function viewtransportrequest(val){
        console.log("UUUU");
        jQuery.ajax({
            url: "views/customtransportrequestpopup.php",
            data:'option=noption&rowid='+val,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");

            },
            error:function (){}
        });
     }
     function addandupdatestationery(item_id){
        jQuery.ajax({
            url: "views/custom_insert_stationery_popup.php",
            data:'option=noption&item_id='+item_id+'&type=topup',
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");

            },
            error:function (){}
        });
    }
</script>
<div class="row col-lg-12">
    <div class="col-lg-12">
        <div class="text-right">
            <a href="index.php?option=COM_VIEW_OFFICE_MANAGEMENT" class="btn btn-primary m-b-10">
                View Details  aaaa
            </a>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card new-cust-card">
            <div class="card-header">
                <h5>In Couriers dd</h5>
                <div class="card-header-right" style="top:0px;right: 0px" >
                    <ul class="nav nav-tabs md-tabs infisuite_tab font-weight-bold" style="border:0px;top:0px;right: 0px">
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link active" data-bs-toggle="tab" href="#month" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>

                        </li>
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link" data-bs-toggle="tab" href="#week" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>

                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content card-block table-border-style custom-scroll" style="height: 280px;">
                <div class="tab-pane active" id="month" role="tabpanel">
                    <div class="card-block">
                        <?php $checka =0;
                            foreach($data['list_couriers'] as $down){
                            if($down['courier_type']==2 &&  $down['current_status']!=ST_APPROVED){
                                $checka =1;
                         ?>
                        <div class="align-middle m-b-25">
                            <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" style="margin-top: 0 !important;" alt=""></a>
                            <?php }else{ ?>
                                <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                            <?php } ?>
                            <div class="d-inline-block cursor-pointer" onclick="recicedcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <a href="#!"><h6>
                                    <?php echo $down['courier_company']; ?>
                                </h6></a>
                                <span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
                                <p class="text-muted m-b-0"><?php echo $down['courier_ref_no']; ?></p>
                                <span class="status deactive text-mute"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['courier_date'], 'N'); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <?php }
                            if($checka ==0){?>
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                    </div>
                                </div>
                            <?php }
                        ?>
                    </div>
                </div>
                <div class="tab-pane" id="week" role="tabpanel">
                    <div class="card-block">
                        <?php $checkb =0; foreach($data['list_couriers'] as $down){
                            if($down['courier_type']==2 &&  $down['current_status']==ST_APPROVED){
                                $checkb =1;
                         ?>
                        <div class="align-middle m-b-25">
                            <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
                            <?php }else{ ?>
                                <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                            <?php } ?>
                            <div class="d-inline-block cursor-pointer" onclick="recicedcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <a href="#!"><h6>
                                    <?php echo $down['courier_company']; ?>
                                </h6></a>
                                <span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
                                <p class="text-muted m-b-0"><?php echo $down['courier_ref_no']; ?></p>
                                <span class="status deactive text-mute"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['courier_date'], 'N'); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <?php }
                            if($checkb ==0){?>
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                    </div>
                                </div>
                            <?php }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card new-cust-card">
            <div class="card-header">
                <h5>Out Couriers</h5>
                <div class="card-header-right" style="top:0px;right: 0px" >
                    <ul class="nav nav-tabs md-tabs infisuite_tab font-weight-bold" style="border:0px;top:0px;right: 0px;">
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link active" data-bs-toggle="tab" href="#OOmonth" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>

                        </li>
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link" data-bs-toggle="tab" href="#OOweek" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>

                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content card-block table-border-style custom-scroll" style="height: 280px;">
                <div class="tab-pane active" id="OOmonth" role="tabpanel">
                    <div class="card-block">
                        <?php $checka =0;
                            foreach($data['list_couriers'] as $down){
                            if($down['courier_type']==1 &&  $down['current_status']!=ST_APPROVED){
                                $checka =1;
                         ?>
                        <div class="align-middle m-b-25">
                            <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
                            <?php }else{ ?>
                                <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                            <?php } ?>
                            <div class="d-inline-block cursor-pointer"  onclick="sendcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <a href="#!"><h6>
                                    <?php echo $down['courier_name']; ?>
                                </h6></a>
                                <span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
                                <p class="text-muted m-b-0"><?php echo $down['courier_mobile']; ?></p>
                                <span class="status deactive text-mute"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['courier_date'], 'N'); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <?php }
                            if($checka ==0){?>
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                    </div>
                                </div>
                            <?php }
                        ?>
                    </div>
                </div>
                <div class="tab-pane" id="OOweek" role="tabpanel">
                    <div class="card-block">
                        <?php $checkb =0; foreach($data['list_couriers'] as $down){
                            if($down['courier_type']==1 &&  $down['current_status']==ST_APPROVED){
                                $checkb =1;
                         ?>
                        <div class="align-middle m-b-25">
                            <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
                            <?php }else{ ?>
                                <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                            <?php } ?>
                            <div class="d-inline-block cursor-pointer" onclick="sendcourier(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <a href="#!"><h6>
                                    <?php echo $down['courier_company']; ?>
                                </h6></a>
                                <span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
                                <p class="text-muted m-b-0"><?php echo $down['courier_ref_no']; ?></p>
                                <span class="status deactive text-mute"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['courier_date'], 'N'); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <?php }
                            if($checkb ==0){?>
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                    </div>
                                </div>
                            <?php }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card new-cust-card">
            <div class="card-header">
                <h5>Stationery</h5>
                <div class="card-header-right" style="top:0px;right: 0px" >
                    <ul class="nav nav-tabs md-tabs infisuite_tab font-weight-bold" style="border:0px;top:0px;right: 0px;">
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link active" data-bs-toggle="tab" href="#stmonth" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>

                        </li>
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link" data-bs-toggle="tab" href="#stweek" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>

                        </li>
                    </ul>
                </div>
            </div>
            <div class="tab-content card-block table-border-style custom-scroll" style="height: 280px;">
                <div class="tab-pane active" id="stmonth" role="tabpanel">
                    <div class="card-block">
                        <?php $checka =0;
                            foreach($data['list_stationery_requests'] as $down){
                            if($down['current_status']!=ST_APPROVED){
                                $checka =1;
                         ?>
                        <div class="align-middle m-b-25">
                            <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
                            <?php }else{ ?>
                                <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                            <?php } ?>
                            <div class="d-inline-block cursor-pointer" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <a href="#!"><h6>
                                    <?php echo $down['request_no']; ?>
                                </h6></a>
                                <span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
                                <p class="text-muted m-b-0"><?php echo displayDate($down['require_date']); ?></p>
                                <span class="status deactive text-mute"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['request_date'], 'N'); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <?php }
                            if($checka ==0){?>
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                    </div>
                                </div>
                            <?php }
                        ?>
                    </div>
                </div>
                <div class="tab-pane" id="stweek" role="tabpanel">
                    <div class="card-block">
                        <?php $checkb =0; foreach($data['list_stationery_requests'] as $down){
                            if(  $down['current_status']==ST_APPROVED){
                                $checkb =1;
                         ?>
                        <div class="align-middle m-b-25">
                            <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt="" style="margin-top: 0 !important;"></a>
                            <?php }else{ ?>
                                <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black "><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                            <?php } ?>
                            <div class="d-inline-block cursor-pointer" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <a href="#!"><h6>
                                    <?php echo $down['request_no']; ?>
                                </h6></a>
                                <span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
                                <p class="text-muted m-b-0"><?php echo displayDate($down['require_date']); ?></p>
                                <span class="status deactive text-mute"><i class="far fa-clock m-r-10"></i><?php echo notificationdatesdiff($down['request_date'], 'N'); ?></span>
                            </div>
                        </div>
                        <?php } ?>
                        <?php }
                            if($checkb ==0){?>
                                <div class="card-px text-center ">
                                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                    </div>
                                </div>
                            <?php }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card new-cust-card">
            <div class="card-header">
                <h5>Stationery Below Minmum Qty</h5>
            </div>
            <div class="tab-content card-block table-border-style custom-scroll" style="height: 280px;">
                <div class="card-block">
                    <?php $checkb =0; foreach($data['all_min_sat'] as $down){
                            $checkb =1;
                     ?>
                    <div class="align-middle m-b-25">

                        <div class="d-inline-block cursor-pointer" onclick="addandupdatestationery(<?php echo $down['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <a href="#!"><h6>
                                <?php echo $down['item_name']; ?>
                            </h6></a>
                            <span class="status active" style="margin-top:-15px;background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>" ></span>
                            <p class="text-muted m-b-0">
                                Available Qty: <span class="mt-16 f-w-600"><?php echo $down['available_qty']; ?></span>
                                Minmum Qty: <span class="mt-16 f-w-600"><?php echo $down['minmum_qty']; ?> </span>
                            </p>
                        </div>
                    </div>
                    <?php }
                        if($checkb ==0){?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Couriers</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            </div>
                        <?php }
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>
