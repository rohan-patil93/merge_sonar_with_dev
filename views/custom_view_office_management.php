<?php
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['crop_image']='1';
$tab = $_GET['TAB']!="" ? $_GET['TAB'] :1;
$type = "";
if($_SESSION['admin_type']==1 || in_array('OFFICE_ADMIN', $_SESSION['role_ids']) || in_array('OFFICE_SECURITY_GUARD', $_SESSION['role_ids'])){
    $type = SECURITYGUARD_ROLE;
}
$url = API."?mode=togetofficemanagement&location_id=1&type=".$type."&admin_type=".$_SESSION['admin_type'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$sitem = $data['sitem'];
$ar = $data['all']['receive']!="" ? $data['all']['receive']:0;
$as = $data['all']['send']!="" ? $data['all']['send']:0;
$sr = $data['seven']['receive']!="" ? $data['seven']['receive']:0;
$ss = $data['seven']['send']!="" ? $data['seven']['send']:0;
$bus = $data['bus'];
$deliver_types = $data['deliver_types'];
$sub_role = $data['worksub'];
$recent15days = $recent30days =$recent45days = array();
foreach((array)$data['sidein'] as $rcc){
    $todaydate = date('Y-m-d');
    $sdate = new DateTime($rcc['courier_date']);
    $edate = new DateTime($todaydate);
    $totald= $sdate->diff($edate);
    $totaldays = $totald->days;
    if($totaldays < 15){
        $recent15days[] = $rcc;
    }elseif($totaldays < 30){
         $recent30days[] = $rcc;
    }elseif($totaldays < 60){
         $recent45days[] = $rcc;
    }
}


$expire15days = $expire30days =$expire45days = array();
foreach((array)$data['sideout'] as $rcc){
    $todaydate = date('Y-m-d');
    $sdate = new DateTime($rcc['courier_date']);
    $edate = new DateTime($todaydate);
    $totald= $sdate->diff($edate);
    $totaldays = $totald->days;
    if($totaldays < 15){
        $expire15days[] = $rcc;
    }elseif($totaldays < 30){
         $expire30days[] = $rcc;
    }elseif($totaldays < 60){
         $expire45days[] = $rcc;
    }
}
?>
<style type="text/css">
    .qty {
    width: 40px;
    height: 25px;
    text-align: center;
}
input.qtyplus { width:25px; height:25px;}
input.qtyminus { width:25px; height:25px;}
.infisuite_tab .nav-item .active{
background-color: #4099ff;
    color: #FFFFFF;
}
</style>
<style type="text/css">
/* TOGGLE STYLING */
.toggle {
  margin: 0 0 0.2rem;
  box-sizing: border-box;
  font-size: 0;
  display: flex;
  flex-flow: row nowrap;
  justify-content: flex-start;
  align-items: stretch;
}
.toggle input {
  width: 0;
  height: 0;
  position: absolute;
  left: -9999px;
}
.toggle input + label {
  margin: 0;
  padding: 0.25rem 1rem;
  box-sizing: border-box;
  position: relative;
  display: inline-block;
  border: solid 1px #DDD;
  background-color: #FFF;
  font-size: 1rem;
  line-height: 140%;
  font-weight: 600;
  text-align: center;
  box-shadow: 0 0 0 rgba(255, 255, 255, 0);
  transition: border-color 0.15s ease-out, color 0.25s ease-out, background-color 0.15s ease-out, box-shadow 0.15s ease-out;
  /* ADD THESE PROPERTIES TO SWITCH FROM AUTO WIDTH TO FULL WIDTH */

  /* ----- */
}
.toggle input + label:first-of-type {
  border-radius: 6px 0 0 6px;
  border-right: none;
}
.toggle input + label:last-of-type {
  border-radius: 0 6px 6px 0;
  border-left: none;
}
.toggle input:hover + label {
  border-color: #213140;
}
.toggle input:checked + label {
  background-color: #4B9DEA;
  color: #FFF;
  box-shadow: 0 0 10px rgba(102, 179, 251, 0.5);
  border-color: #4B9DEA;
  z-index: 1;
}
.toggle input:focus + label {
  outline: dotted 1px #CCC;
  outline-offset: 0.45rem;
}
@media (max-width: 800px) {
  .toggle input + label {
    padding: 0.75rem 0.25rem;
    flex: 0 0 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  }
</style>
<script type="text/javascript">
<?php  if($tab =="5" || $tab =="7"){ ?>
document.onreadystatechange = function () {
    if (document.readyState == "complete"){
      <?php if($tab =="5"){ ?>
        getDynamicPage('custom_list_asset_allocations','asset_tab','',5);
      <?php }else{  ?>
        getDynamicPage('custom_view_equipment_allocations','space_tab','',7);
      <?php } ?>
  }
}
<?php } ?>
    function searchtransportbus(){
        var pickup = $("#pickup").val();
        var droping = $("#droping").val();
        var pickdate = $("#pickdate").val();
        var str = "&pickup="+pickup+"&droping="+droping+"&pickdate="+pickdate;
        console.log(str);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=searchtransportbus'+str,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#viewbus").html(data);

            },
            error:function (){}
        });

    }
    function couriertype(){
        var in_out = $("#courier_type").val();
        if(in_out ==1){
            $("#courier_type").val(2);
        }
         if(in_out ==2){
            $("#courier_type").val(1);
        }
    }



    function searchcourier(){
         var courier_type = $("#courier_type").val();
         var courier_ref_no = $("#courier_ref_no").val();
         var courier_company = $("#courier_company").val();
         var courier_user_id = $("#courier_user_id").val();
         var courier_date = $("#courier_date").val();
          var courier_to_date = $("#courier_to_date").val();
         var from_detail = $("#from_detail").val();
         var to_detail = $("#to_detail").val();

         var str = "&courier_type="+courier_type+"&courier_ref_no="+courier_ref_no+"&courier_company="+courier_company+"&courier_user_id="+courier_user_id+"&courier_date="+courier_date+"&from_detail="+from_detail+"&to_detail="+to_detail+"&courier_to_date="+courier_to_date;
        console.log(str);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=searchcourierdata'+str,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);
                $("#searchdata").html(data);

            },
            error:function (){}
        });

    }
    function viewsidetab(val){
        console.log(val);
    }

    function viewrequest(val){
        console.log("UUUU");
        jQuery.ajax({
            url: "views/customtransportrequestpopup.php",
            data:'option=noption&id='+val,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");

            },
            error:function (){}
        });
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


    function viewlocation(item_id){
        var loc = $("#view_location_"+item_id).val();
        if(loc ==1){
            $("#dis_location_"+item_id).show();
            $("#view_location_"+item_id).val(2);
        }
        if(loc ==2){
            $("#dis_location_"+item_id).hide();
            $("#view_location_"+item_id).val(1);
        }
    }



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
    function foodinsert(val){
        if(val!=0){
            jQuery.ajax({
                url: "views/custom_insert_food_request_popup.php",
                data:'option=noption&option_id='+val,
                type: "POST",
                success:function(data){
                    $('#popup1').html(data);
                    $('#kt_modal_KTDatatable_remote').modal("show");

                },
                error:function (){}
            });
        }
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


    function getDynamicPage(option,divid,pagetype,tab){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#"+divid).html(loader);
        updateUrlParameter('TAB',tab)
        var currentUrl = window.location.href;
        var urlParts = currentUrl.split('?');
        if(pagetype =="LIST"){
            $.ajax({
               url: "views/view_roll_back1.php",
               data:'option='+option+'&'+urlParts['1'],
               type: "POST",
               success:function(data){
                 $('#'+divid).html(data);
                  $('[data-bs-toggle="tooltip"]').tooltip();
               },
               error:function (){}
            });
        }else{
            $.ajax({
                url: "views/"+option+".php",
                data:'option='+option+'&ajaxpage=Y&'+urlParts['1'],
                type: "POST",
                success:function(data){
                    $('#'+divid).html(data);
                    $('[data-bs-toggle="tooltip"]').tooltip();
                },
                error:function (){}
            });
        }
    }
    function meetingroombook(id=""){
        jQuery.ajax({
            url: "views/custom_insert_meeting_book_room_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");

            },
            error:function (){}
        });
    }
    function todisplayfloor(build_id){
        console.log(" build_id"+build_id);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=todisplayfloorbasedbuildid&build_id='+build_id,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);
                $("#building_floor_id").html(data);
            },
            error:function (){}
        });
    }
    function todisplayspace(floor_id){
        console.log("floor_id="+floor_id);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=todisplayspacebasedfloorid&floor_id='+floor_id,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);
                $("#building_floor_space_id").html(data);
            },
            error:function (){}
        });
    }

</script>
<div class="row courier mb-4">
    <div class="col-lg-12">
        <ul class="nav nav-tabs tabs dynamic_card_menu">
            <li class="nav-item <?php if($_SESSION['admin_type']!=1 &&  in_array('COM_LIST_COURIERS' ,(array)$sub_role)){ echo  "event_disabled";
} ?> ">
                <a class="nav-link position-relative text-center <?php if($tab==1){ echo "active";
} ?>" data-bs-toggle="tab" href="#courier" role="tab" onclick="updateUrlParameter('TAB','1')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-package f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Courier Management</span>
                </a>
            </li>
            <li class="nav-item <?php if($_SESSION['admin_type']!=1 && in_array('COM_LIST_STATIONERY' ,(array)$sub_role) ){ echo  "event_disabled";
} ?> ">
                <a class="nav-link position-relative text-center <?php if($tab==2){ echo "active";
} ?>" data-bs-toggle="tab" href="#stationery" role="tab"onclick="updateUrlParameter('TAB','2')"  onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-write f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Inventory Management</span>
                </a>
            </li>
            <li class="nav-item event_disabled ">
                <a class="nav-link position-relative text-center <?php if($tab==3){ echo "active";
} ?>" data-bs-toggle="tab" href="#transport" role="tab" onclick="updateUrlParameter('TAB','3')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-truck f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Transport Management</span>
                </a>
            </li>
            <li class="nav-item event_disabled">
                <a class="nav-link position-relative text-center <?php if($tab==4){ echo "active";
} ?>" data-bs-toggle="tab" href="#food" role="tab" onclick="updateUrlParameter('TAB','4')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-thumb-up f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Food Management</span>
                </a>
            </li>
            <li class="nav-item event_disabled ">
                <a class="nav-link position-relative text-center <?php if($tab==5){ echo "active";
} ?>" data-bs-toggle="tab" href="#asset_tab" role="tab" onclick="getDynamicPage('custom_list_asset_allocations','asset_tab','',5)" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                   <i class="ti-tablet f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Asset Management</span>
                </a>
            </li>
            <li class="nav-item event_disabled">
                <a class="nav-link position-relative text-center <?php if($tab==6){ echo "active";
} ?>" data-bs-toggle="tab" href="#maintenance_tab" role="tab" onclick="updateUrlParameter('TAB','6')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-cut f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Maintenance Management</span>
                </a>
            </li>
            <li class="nav-item <?php if($_SESSION['admin_type']!=1 &&  in_array('COM_LIST_SEATING_POOLS' ,(array)$sub_role) ){ echo  "event_disabled";
} ?>">
                <a class="nav-link position-relative text-center <?php if($tab==7){ echo "active";
} ?>" data-bs-toggle="tab" href="#space_tab" role="tab" onclick="getDynamicPage('custom_view_equipment_allocations','space_tab','',7)" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-bookmark-alt f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Space Management</span>
                </a>
            </li>

            <li class="nav-item event_disabled ">
                <a class="nav-link position-relative text-center <?php if($tab==8){ echo "active";
} ?>" data-bs-toggle="tab" href="#energy_tab" role="tab" onclick="updateUrlParameter('TAB','8')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                   <i class="ti-wand f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Energy Management</span>
                </a>
            </li>

            <li class="nav-item event_disabled">
                <a class="nav-link position-relative text-center <?php if($tab==13){ echo "active";
} ?>" data-bs-toggle="tab" href="#meeting_tab" role="tab" onclick="updateUrlParameter('TAB','13')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-bookmark-alt f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Meeting Room Bookings</span>
                </a>
            </li>
            <li class="nav-item event_disabled">
                <a class="nav-link position-relative text-center <?php if($tab==10){ echo "active";
} ?>" data-bs-toggle="tab" href="#security_tab" role="tab" onclick="updateUrlParameter('TAB','10')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-target f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Security Management</span>
                </a>
            </li>


            <li class="nav-item event_disabled">
                <a class="nav-link position-relative text-center <?php if($tab==12){ echo "active";
} ?>" data-bs-toggle="tab" href="#health_safty_tab" role="tab" onclick="updateUrlParameter('TAB','12')" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="ti-star f-20 text-info"></i>
                    <span class="text-info f-14 f-w-700">Health & Safty</span>
                </a>
            </li>

        </ul>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">

        <!-- Tab panes -->
        <div class="tab-content tabs">
            <div class="tab-pane  <?php if($tab==1){ echo "active";
} ?>" id="courier" role="tabpanel">

                <?php include_once 'custom_view_list_courier.php'; ?>
            </div>
            <!----- stationery start --------->
            <div class="tab-pane  <?php if($tab==2){ echo "active";
} ?>" id="stationery" role="tabpanel">
                <?php include_once 'custom_view_stationery.php'; ?>
            </div>

            <!----- stationery end --------->
            <div class="tab-pane  <?php if($tab==3){ echo "active";
} ?>" id="transport" role="tabpanel">

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card p-3">
                            <div class="row">
                                <div class="col-lg-12">
                                     <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                                        <li class="nav-item p-l-10 p-r-10">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#tssemyrequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Search Requests</a>
                                        </li>
                                        <li class="nav-item p-l-10 p-r-10">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tsmyrequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">My Requests</a>
                                        </li>
                                        <?php if($_SESSION['admin_type']==1){ ?>
                                        <li class="nav-item p-l-10 p-r-10">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tsaprequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">To Be Approved</a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-content card-block table-border-style">
                                <div class="tab-pane active" id="tssemyrequest" role="tabpanel">
                                    <div class="bus-bg m-b-10" style="background: url(../assets/images/bus.jpg); height:250px; background-size: 100%; background-repeat: no-repeat; position: relative;">
                                        <div class="banner-txt">
                                            <h2 class="f-w-700 f-35">Get your bus online,<br>Easy &amp; Safely</h2>
                                        </div>
                                        <div class="booking-form">
                                            <h3 class="">Choose Your Bus</h3>
                                            <div class="row mb-1 mt-4">
                                                <div class="col-lg-6 m-b-10">
                                                    <div class="input-group">
                                                        <span class="input-group-text addon-style">
                                                            <span class=""><i class="ti-location-arrow"></i></span>
                                                        </span>
                                                        <select aria-label="Pickup" class="form-control fill border" fdprocessedid="n6szf" id="pickup" name="pickup">
                                                            <option value="">Pickup Point</option>
                                                            <?php foreach($data['listschedlue'] as $listschedlue){ ?>
                                                                <option value="<?php echo $listschedlue; ?>"><?php echo $listschedlue; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 m-b-10">
                                                    <div class="input-group">
                                                        <span class="input-group-text addon-style">
                                                            <span class=""><i class="ti-location-pin"></i></span>
                                                        </span>
                                                        <select aria-label="Droping" class="form-control border" fdprocessedid="ft4ukt" name="droping" id="droping">
                                                            <option value="">Dropping Point</option>
                                                            <?php foreach($data['listschedlue'] as $listschedlue){ ?>
                                                                <option value="<?php echo $listschedlue; ?>"><?php echo $listschedlue; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 m-b-10">
                                                    <div class="input-group">
                                                        <span class="input-group-text addon-style">
                                                            <span class=""><i class="ti-time"></i></span>
                                                        </span>
                                                        <input aria-label="Pickdate" class="form-control" type="date" name="pickdate" id="pickdate">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 m-b-10">
                                                    <button class="btn btn-primary w-100 p-2" onclick="searchtransportbus()" fdprocessedid="s99pj">Find Bus</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="viewbus">
                                    <?php foreach((array)$data['routes'] as $route){
                                            $listroute[$route['id']] = $route;
                                    ?>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row mt-3">
                                                <div class="col-lg-3">
                                                    <h4 class="f-w-700 "><?php echo $bus[$route['company_transport_id']]['vehicle_name']; ?></h4>
                                                    <p>Seat Layout <?php echo $bus[$route['company_transport_id']]['seat_layout']; ?></p>
                                                    <span class="text-danger"><?php echo $bus[$route['company_transport_id']]['vehicle_type']; ?></span>
                                                </div>
                                                <div class="col-lg-5">
                                                    <div class="row">
                                                        <div class="col-lg-4">
                                                            <h4 class="f-w-700 f-20"><?php echo date("h:i A", strtotime($route['arrival_time'])); ?></h4>
                                                            <p class="f-16 f-w-500"><?php echo $route['route_starts']; ?></p>
                                                        </div>
                                                        <div class="col-lg-3 text-center">
                                                            <i class="ti-arrow-right text-primary f-w-700"></i>

                                                        </div>
                                                        <div class="col-lg-5">
                                                            <h4 class="f-w-700 f-20"><?php echo date("h:i A", strtotime($route['departure_time'])); ?></h4>
                                                            <p class="f-16 f-w-500"><?php echo $route['route_ends']; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-4 text-right">
                                                    <h4 class="f-w-700 f-22 text-success">&#8377 <?php echo $route['route_charges_monthly']; ?> </h4>
                                                    <p>Off Days: <span class="form-label label label-success"><?php echo implode(" , ",json_decode($route['off_days'],1)); ?></span></p>
                                                    <button class="btn btn-primary" onclick="viewlocation(<?php echo $route['id']; ?>)">Select Location</button>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="" id="view_location_<?php echo $route['id']; ?>" value="1">
                                        <div class="card-body" style="display: none;" id="dis_location_<?php echo $route['id']; ?>" >
                                            <div class="row col-lg-12">
                                                <?php foreach((array)$data['transport_schedule'][$route['id']] as $transport_schedule){ ?>
                                                    <div class="col-lg-4 p-2"> <?php echo $transport_schedule['stop_name']; ?> </div>
                                                    <div class="col-lg-2">
                                                        <button onclick="viewrequest(<?php echo $transport_schedule['id']; ?>)" class="btn btn-info" style="font-size: 12px; padding: 5px;" >
                                                        Request </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php } ?>
                                    </div>
                                </div>
                                <div class="tab-pane " id="tsmyrequest" role="tabpanel">
                                    <div class="card-header-right text-right">
                                        <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                                            <li class="nav-item p-l-10 p-r-10">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#stmyspending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>
                                            </li>
                                            <li class="nav-item p-l-10 p-r-10">
                                                <a class="nav-link" data-bs-toggle="tab" href="#stmysapproved" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content card-block table-border-style">
                                        <div class="tab-pane active" id="stmyspending" role="tabpanel">
                                            <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                <table class="table table-framed">
                                                    <tbody>
                                                    <?php foreach((array)$data['user_transport_schedule'] as $down){
                                                            if($down['current_status'] !=ST_APPROVED){ ?>
                                                    <tr>
                                                        <th scope="row" style="width: 10%;">
                                                           <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                               <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                           <?php }else{ ?>
                                                               <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                           <?php } ?>

                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                                        </th>
                                                        <th scope="col">
                                                            <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                                <i class="ti-pencil-alt m-0"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    <?php } ?>
                                                    <?php } ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane " id="stmysapproved" role="tabpanel">
                                            <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                <table class="table table-framed">
                                                    <tbody>
                                                    <?php foreach((array)$data['user_transport_schedule'] as $down){
                                                            if($down['current_status'] ==ST_APPROVED){ ?>
                                                    <tr>
                                                        <th scope="row" style="width: 10%;">
                                                           <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                               <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                           <?php }else{ ?>
                                                               <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                           <?php } ?>

                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                                        </th>
                                                        <th scope="col">
                                                            <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                                <i class="ti-pencil-alt m-0"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    <?php } ?>
                                                    <?php } ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane " id="tsaprequest" role="tabpanel">
                                    <div class="card-header-right text-right">
                                        <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                                            <li class="nav-item p-l-10 p-r-10">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#stapmyspending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>
                                            </li>
                                            <li class="nav-item p-l-10 p-r-10">
                                                <a class="nav-link" data-bs-toggle="tab" href="#stapmysapproved" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-content card-block table-border-style">
                                        <div class="tab-pane active" id="stapmyspending" role="tabpanel">
                                            <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                <table class="table table-framed">
                                                    <tbody>
                                                    <?php foreach((array)$data['all_transport_schedule'] as $down){
                                                            if($down['current_status'] !=ST_APPROVED){ ?>
                                                    <tr>
                                                        <th scope="row" style="width: 10%;">
                                                           <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                               <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                           <?php }else{ ?>
                                                               <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                           <?php } ?>

                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                                        </th>
                                                        <th scope="col">
                                                            <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                                <i class="ti-pencil-alt m-0"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    <?php } ?>
                                                    <?php } ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane " id="stapmysapproved" role="tabpanel">
                                            <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                <table class="table table-framed">
                                                    <tbody>
                                                    <?php foreach((array)$data['all_transport_schedule'] as $down){
                                                            if($down['current_status'] ==ST_APPROVED){ ?>
                                                    <tr>
                                                        <th scope="row" style="width: 10%;">
                                                           <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                               <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                           <?php }else{ ?>
                                                               <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                           <?php } ?>

                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                            <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                                        </th>
                                                        <th scope="col">
                                                            <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                                        </th>
                                                        <th scope="col">
                                                            <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                                <i class="ti-pencil-alt m-0"></i>
                                                            </button>
                                                        </th>
                                                    </tr>
                                                    <?php } ?>
                                                    <?php } ?>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4"  id="side_view_3"  <?php  ?> >
                        <div class="" style="background: url(../assets/images/transport.jpg);
                            width:100%;height:250px;background-repeat:no-repeat;background-size: 500px auto;border-radius: 15px;">
                            <h3 class="f-w-600 pt-5 f-25 text-white m-l-50 m-r-50">Ready to elevate your daily commute experience? Click the "Request Transport" button below</h3>
                            <button class="btn btn-warning waves-effect waves-light m-l-50 mt-4">Request Transport</button>
                        </div>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Transports</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <?php
                                    foreach((array)$data['user_transport_schedule'] as $sidein){
                                        $stas="warning";
                                        if($sidein['current_status']=="199"){ $stas="success";
}
                                    ?>

                                        <div class="col-lg-2 p-2">
                                             <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                               <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                           <?php }else{ ?>
                                               <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                                           <?php } ?>
                                        </div>
                                        <div class="col-lg-6 p-2">
                                            <h4 class="f-14 m-0 f-w-600"><?php echo $bus[$sidein['company_transport_id']]['vehicle_name']; ?></h4>
                                            <span class="f-12"> Pickup Point: <?php echo $sidein['pickup_point']." Transport Date".displayDate($sidein['transport_date']) ; ?> </span>
                                        </div>
                                        <div class="col-lg-4 p-2">
                                            <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['employee_transport_requests'][$sidein['current_status']]; ?></span>
                                        </div>

                                     <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="tab-pane  <?php if($tab==4){ echo "active";
} ?>" id="food" role="tabpanel">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card p-3">
                            <div class="row mt-3">
                                <?php foreach((array)$data['food_options'] as $food_options){
                                        $food_name[$food_options['id']] = $food_options['food_option'];
                                ?>
                                <div class="col-lg-4 col-md-4 sol-sm-4 col-6 m-b-15">
                                    <div class="pro-dsc-sec border">
                                        <div class="pro-img">
                                            <?php if($food_options['image']!=""){ ?>
                                            <img src="uploads/all/<?php echo $food_options['image'] ?>" class="img-fluid" alt="">
                                            <?php } ?>
                                        </div>
                                        <h4 class="mt-3 f-12 text-primary f-w-600 text-uppercase m-l-10"><?php echo $food_options['food_option'] ?></h4>
                                        <h3 class="f-16 f-w-600 m-l-10 m-b-15"><?php echo $food_options['description']; ?></h3>
                                        <div class="row">
                                            <div class="col-lg-6"></div>
                                            <div class="col-lg-6">
                                                <div class="">
                                                    <div class="mt-2 mb-2">
                                                        <div class="toggle" >
                                                           <?php $mn=1; $selectdata = array('0'=>'Yes','1'=>'No');
                                                           $check = 1;
                                                           foreach($selectdata as $selectkey => $selectvalue){

                                                            $id = "check_".$food_options['id']."_".$mn;
                                                            ?>
                                                            <input aria-label="Radio option"  type="radio"  <?php if($selectkey==1){ echo "checked";
} ?> name="descpass[<?php echo $food_options['id']; ?>]" onclick="foodinsert(<?php echo $food_options['id'];  ?>)" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' onkeydown="" />
                                                            <label for="<?php echo $id; ?>"><?php echo $selectvalue; ?></label>
                                                            <?php  $mn++;
} ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                            <div>
                                <div class="row">
                                    <div class="col-lg-12">
                                         <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">

                                            <li class="nav-item p-l-10 p-r-10">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#fdmyrequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">My Requests</a>

                                            </li>
                                            <?php if($_SESSION['admin_type']==1){ ?>
                                            <li class="nav-item p-l-10 p-r-10">
                                                <a class="nav-link" data-bs-toggle="tab" href="#fdaprequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">To Be Approved</a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="tab-content card-block table-border-style">
                                    <div class="tab-pane active" id="fdmyrequest" role="tabpanel">
                                        <div class="card-header-right text-right">
                                            <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                                                <li class="nav-item p-l-10 p-r-10">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#foodmyspending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>
                                                </li>
                                                <li class="nav-item p-l-10 p-r-10">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#foodapproved" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content card-block table-border-style">
                                            <div class="tab-pane active" id="foodmyspending" role="tabpanel">
                                                <div class="card">
                                                    <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                        <table class="table table-framed">
                                                            <tbody>
                                                            <?php foreach((array)$data['employee_food_requests'] as $down){
                                                                if($down['current_status'] !=ST_APPROVED){
                                                             ?>
                                                            <tr>
                                                                <th scope="row" style="width: 10%;">
                                                                   <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                                       <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                                   <?php }else{ ?>
                                                                       <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                                   <?php } ?>

                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $food_name[$down['food_option_id']]; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['start_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> ">
                                                                        <?php echo $data['status']['employee_food_requests'][$down['current_status']]; ?></span></br>
                                                                        <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['stop_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <button class="btn waves-effect waves-light btn-primary" onclick="viewfoodinsert(<?php echo $down['id']; ?>)" >
                                                                        <i class="ti-pencil-alt"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php } ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane " id="foodapproved" role="tabpanel">
                                                <div class="card">
                                                    <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                        <table class="table table-framed">
                                                            <tbody>
                                                            <?php foreach((array)$data['employee_food_requests'] as $down){
                                                                if($down['current_status'] ==ST_APPROVED){
                                                             ?>
                                                            <tr>
                                                                <th scope="row" style="width: 10%;">
                                                                   <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                                       <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                                   <?php }else{ ?>
                                                                       <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                                   <?php } ?>

                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $food_name[$down['food_option_id']]; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['start_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> ">
                                                                        <?php echo $data['status']['employee_food_requests'][$down['current_status']]; ?></span></br>
                                                                        <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['stop_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <button class="btn waves-effect waves-light btn-primary" onclick="viewfoodinsert(<?php echo $down['id']; ?>)" >
                                                                        <i class="ti-pencil-alt"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php } ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="fdaprequest" role="tabpanel">
                                        <div class="card-header-right text-right">
                                            <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                                                <li class="nav-item p-l-10 p-r-10">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#optionfoodmyspending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>
                                                </li>
                                                <li class="nav-item p-l-10 p-r-10">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#optionfoodapproved" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-content card-block table-border-style">
                                            <div class="tab-pane active" id="optionfoodmyspending" role="tabpanel">
                                                <div class="card">
                                                    <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                        <table class="table table-framed">
                                                            <tbody>
                                                            <?php foreach((array)$data['all_employee_food_requests'] as $down){
                                                                if($down['current_status'] !=ST_APPROVED){
                                                             ?>
                                                            <tr>
                                                                <th scope="row" style="width: 10%;">
                                                                   <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                                       <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                                   <?php }else{ ?>
                                                                       <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                                   <?php } ?>

                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $food_name[$down['food_option_id']]; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['start_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> ">
                                                                        <?php echo $data['status']['employee_food_requests'][$down['current_status']]; ?></span></br>
                                                                        <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['stop_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <button class="btn waves-effect waves-light btn-primary" onclick="viewfoodinsert(<?php echo $down['id']; ?>)" >
                                                                        <i class="ti-pencil-alt"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php } ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane " id="optionfoodapproved" role="tabpanel">
                                                <div class="card">
                                                    <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                        <table class="table table-framed">
                                                            <tbody>
                                                            <?php foreach((array)$data['all_employee_food_requests'] as $down){
                                                                if($down['current_status'] ==ST_APPROVED){
                                                             ?>
                                                            <tr>
                                                                <th scope="row" style="width: 10%;">
                                                                   <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                                       <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                                   <?php }else{ ?>
                                                                       <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                                   <?php } ?>

                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $food_name[$down['food_option_id']]; ?></a>
                                                                    <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['start_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> ">
                                                                        <?php echo $data['status']['employee_food_requests'][$down['current_status']]; ?></span></br>
                                                                        <span class="text-muted f-14 f-w-600"><?php echo  displayDate($down['stop_date']); ?></span>
                                                                </th>
                                                                <th scope="col">
                                                                    <button class="btn waves-effect waves-light btn-primary" onclick="viewfoodinsert(<?php echo $down['id']; ?>)" >
                                                                        <i class="ti-pencil-alt"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <?php } ?>
                                                            <?php } ?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>
                    <div class="col-lg-4"  id="side_view_4"  <?php  ?> >
                        <div class="" style="background:url(../assets/images/food.jpg);
                            width:100%;height:250px;background-repeat:no-repeat;background-size: 500px auto;border-radius: 15px;">
                            <h3 class="f-w-600 pt-5 f-25 m-l-50 m-r-50 text-white">Our office canteen is ready to serve you delicious meals that are just a click away.</h3>
                            <button class="btn btn-warning waves-effect waves-light m-l-50 mt-4">Book Canteen Food</button>


                        </div>
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Food</h5>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <?php
                                    foreach((array)$data['employee_food_requests'] as $sidein){
                                        $stas="warning";
                                        if($sidein['current_status']=="199"){ $stas="success";
}
                                    ?>

                                        <div class="col-lg-2 p-2">
                                             <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                               <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                           <?php }else{ ?>
                                               <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                                           <?php } ?>
                                        </div>
                                        <div class="col-lg-6 p-2">
                                            <h4 class="f-14 m-0 f-w-600"><?php echo $food_name[$sidein['food_option_id']]; ?></h4>
                                            <span class="f-12"> <?php echo displayDate($sidein['start_date'])." To ".displayDate($sidein['stop_date']) ; ?> </span>
                                        </div>
                                        <div class="col-lg-4 p-2">
                                            <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['employee_food_requests'][$sidein['current_status']]; ?></span>
                                        </div>

                                     <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==5){ echo "active";
} ?>" id="asset_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Assets</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==6){ echo "active";
} ?>" id="maintenance_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Maintenance</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==7){ echo "active";
} ?>" id="space_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Space</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==8){ echo "active";
} ?>" id="energy_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Energy</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==13){ echo "active";
} ?>" id="meeting_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <!------ Meetting --->
                         <div class="row">
                            <div class="col-lg-8">
                                <div class="card p-3">
                                    <div class="card mt-0">
                                        <div class="card-header">
                                            <h5> Meetting Room Booked </h5>
                                            <div class="card-header-right">
                                                <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                                                    <li class="nav-item p-l-10 p-r-10">
                                                        <a class="nav-link active" data-bs-toggle="tab" href="#todaymrb" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Today</a>

                                                    </li>
                                                    <li class="nav-item p-l-10 p-r-10">
                                                        <a class="nav-link" data-bs-toggle="tab" href="#last7datmrb" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Last 7 days</a>

                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tab-content card-block table-border-style">
                                            <div class="tab-pane active" id="todaymrb" role="tabpanel">
                                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                                    <table class="table table-framed">
                                                        <thead class="visually-hidden">
                                                            <tr>
                                                                <th scope="col">User</th>
                                                                <th scope="col">Name</th>
                                                                <th scope="col">Courier / Location</th>
                                                                <th scope="col">Contact / Time</th>
                                                                <th scope="col">Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach((array)$data['all_metting'] as $down){ ?>
                                                        <tr>
                                                            <th scope="row" style="width: 10%;">
                                                               <?php if($data['all_users'][$down['user_id']]['image']!=""){ ?>
                                                                   <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                               <?php }else{ ?>
                                                                   <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['all_users'][$down['user_id']]['display_name']); ?></span></a>
                                                               <?php } ?>

                                                            </th>
                                                            <th scope="col">
                                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['all_users'][$down['user_id']]['display_name']; ?></a>

                                                            </th>
                                                            <th scope="col">
                                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['courier_company']; ?></a>
                                                                <span class="text-muted f-14 f-w-600"><?php echo  $data['cabin_res'][$down['building_floor_space_id']]; ?></span>
                                                            </th>
                                                            <th scope="col">
                                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['courier_mobile']; ?></a>
                                                                <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['booking_date'])." ".$down['booking_start_time']." To ".$down['booking_end_time']; ?></span>
                                                            </th>

                                                            <th scope="col">
                                                                <button class="btn waves-effect waves-light btn-primary" onclick="meetingroombook(<?php echo $down['id']; ?>)" >
                                                                    <i class="ti-pencil-alt"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                        <?php } // } ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane" id="last7datmrb" role="tabpanel">
                                               <div class="table-responsive infisuite-hover">
                                                    <table class="table table-framed">
                                                        <tbody>
                                                        <?php foreach((array)$data['all_metting'] as $down){ ?>
                                                         ?>
                                                        <tr>
                                                            <th scope="row" style="width: 10%;">
                                                               <?php if($data['all_users'][$down['user_id']]['image']!=""){ ?>
                                                                   <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                               <?php }else{ ?>
                                                                   <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['all_users'][$down['user_id']]['display_name']); ?></span></a>
                                                               <?php } ?>

                                                            </th>
                                                            <th scope="col">
                                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['all_users'][$down['user_id']]['display_name']; ?></a>

                                                            </th>
                                                            <th scope="col">
                                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['courier_company']; ?></a>
                                                                <span class="text-muted f-14 f-w-600"><?php echo  $data['cabin_res'][$down['building_floor_space_id']]; ?></span>
                                                            </th>
                                                            <th scope="col">
                                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['courier_mobile']; ?></a>
                                                                <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['booking_date'])." ".$down['booking_start_time']." To ".$down['booking_end_time']; ?></span>
                                                            </th>

                                                            <th scope="col">
                                                                <button class="btn waves-effect waves-light btn-primary" onclick="meetingroombook(<?php echo $down['id']; ?>)" >
                                                                    <i class="ti-pencil-alt"></i>
                                                                </button>
                                                            </th>
                                                        </tr>
                                                        <?php }  ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="tab-pane " id="searchmrb" role="tabpanel">
                                                <div class="">
                                                    <div class="col-sm-12">
                                                        <div class="input-group input-group-button">

                                                            <div class="bus-bg" style="background: url(../assets/images/bus.jpg); height:250px; background-size: 100%; background-repeat: no-repeat; position: relative; width: 100%;">
                                                                <div class="banner-txt">
                                                                    <h2 class="f-w-700 f-20">Get your Search ,<br>Coureier &amp; Details</h2>
                                                                </div>
                                                                <div class="booking-form">
                                                                    <h3 class="">Search</h3>
                                                                    <div class="row mb-1 mt-4">
                                                                        <div class="col-lg-4 m-b-10">
                                                                            <div class="toggle-button-cover mt-2">
                                                                                <div class="button-cover">
                                                                                    <div class="button b2" id="button-17">
                                                                                        <input aria-label="Courier Type" type="checkbox" name="courier_type" class="checkbox" id="courier_type" onchange="" value="2" onclick="couriertype()" onkeydown="">
                                                                                        <div class="knobs"></div>
                                                                                        <div class="layer"></div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 m-b-10">
                                                                            <div class="input-group">
                                                                                <span class="input-group-text addon-style">
                                                                                    <span class=""><i class="ti-write"></i></span>
                                                                                </span>
                                                                                <input aria-label="Courier Number" type="text" name="courier_ref_no" id="courier_ref_no" class="form-control border" placeholder="Courier Number">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 m-b-10">
                                                                            <div class="input-group">
                                                                                <span class="input-group-text addon-style">
                                                                                    <span class=""><i class="ti-home"></i></span>
                                                                                </span>
                                                                                <input aria-label="Courier Company" type="text" name="courier_company" id="courier_company" class="form-control border" placeholder="Courier Company">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 m-b-10">
                                                                            <div class="input-group">

                                                                                <select aria-label="Courier User" class="form-control border js-example-basic-single" name="courier_user_id" id="courier_user_id">
                                                                                    <option value="">Select Employee</option>
                                                                                    <?php foreach($data['emps'] as $employ){ ?>
                                                                                        <option value="<?php echo $employ['id']; ?>"><?php echo $employ['display_name']; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 m-b-10">
                                                                            <div class="input-group">
                                                                                <span class="input-group-text addon-style">
                                                                                    <span class=""><i class="ti-time"></i></span>
                                                                                </span>
                                                                                <input aria-label="Courier Date" class="form-control" type="date" name="courier_date" id='courier_date'>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-4 m-b-10">
                                                                            <button class="btn btn-primary w-100 p-2" onclick="searchcourier()" fdprocessedid="s99pj">Find Courier</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;" id="searchdata">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4"  id="side_view_1"  <?php  ?> >
                                <div class="" style="background : url(../assets/images/courier.jpg);width:100%;height:250px;background-repeat:no-repeat;background-size: 500px auto;border-radius: 15px;">
                                    <h3 class="f-w-600 pt-5 f-25 m-l-50 m-r-50 text-white">Ready to enjoy hassle-free meeting room bookings? Just click the 'Book Now' button!</h3>
                                    <button class="btn btn-warning waves-effect waves-light m-l-50 mt-4" onclick="meetingroombook()">Book Now </button>
                                </div>

                                <div class="card mt-4">
                                    <div class="card-header">
                                        <h5>Meetting Room Booked</h5>

                                    </div>
                                    <div class="card-body" >
                                        <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
                                            <li class="nav-item">
                                                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#recent15days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  15 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$data['all_metting']);?></span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#recent30days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 30 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recentrmb30days);?></span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#recent45days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 60 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recentrmb45days);?></span></a>
                                            </li>
                                        </ul>
                                        <div class="tab-content tabs">
                                            <div class="tab-pane card-body active p-0 custom-scroll" id="recent15days" role="tabpanel"  style=" height: 250px;">
                                                <?php if(count((array)$data['all_metting']) !=0 ){ ?>
                                                    <div class="row col-lg-12 p-2">
                                                    <?php foreach($data['all_metting'] as $sidein){?>
                                                       <div class="col-lg-2 p-2">
                                                         <?php if($data['all_users'][$sidein['user_id']]['image']!=""){ ?>
                                                           <a href=""><img  src="/uploads/all/<?php echo $data['all_users'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                                       <?php }else{ ?>
                                                           <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['all_users'][$sidein['user_id']]['display_name']); ?></span></a>
                                                       <?php } ?>
                                                   </div>
                                                   <div class="col-lg-6 p-2">
                                                    <h4 class="f-14 m-0 f-w-600"><?php echo $data['all_users'][$sidein['user_id']]['display_name']; ?></h4>
                                                    <span><?php echo displayDate($sidein['booking_date'])." ".$sidein['booking_start_time']." To ".$sidein['booking_end_time']; ?></span>

                                                </div>
                                                <div class="col-lg-4 p-2">
                                                    <span class="f-12"> <?php echo  $data['cabin_res'][$sidein['building_floor_space_id']]; ?> </span>
                                                </div>
                                                    <?php } ?>
                                                   </div>
                                            <?php }else{ ?>
                                                <div class="card-px text-center py-20 m-t-30">
                                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
                                                    <div class="text-center px-4">
                                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            </div>
                                            <div class="tab-pane card-body p-0 custom-scroll" id="recent30days" role="tabpanel" style=" height: 250px;">
                                                <?php if(count((array)$recent30days) !=0 ){ ?>

                                                    <?php foreach($recent30days as $sidein){?>
                                                        <div class="col-lg-2 p-2">
                                                         <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                                           <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                                       <?php }else{ ?>
                                                           <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                                                       <?php } ?>
                                                   </div>
                                                   <div class="col-lg-6 p-2">
                                                    <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_company']; ?></h4>
                                                    <span class="f-12"> <?php echo $sidein['courier_ref_no']; ?> </span>
                                                </div>
                                                <div class="col-lg-4 p-2">
                                                    <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
                                                </div>
                                                    <?php } ?>

                                                <?php }else{ ?>
                                                <div class="card-px text-center py-20 m-t-30">
                                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
                                                    <div class="text-center px-4">
                                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            </div>
                                            <div class="tab-pane card-body p-0 custom-scroll" id="recent45days" role="tabpanel" style=" height: 250px;">
                                                 <?php if(count((array)$recent45days) !=0 ){ ?>

                                                    <?php foreach($recent45days as $sidein){?>
                                                        <div class="col-lg-2 p-2">
                                                         <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                                           <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                                       <?php }else{ ?>
                                                           <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                                                       <?php } ?>
                                                   </div>
                                                   <div class="col-lg-6 p-2">
                                                    <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_company']; ?></h4>
                                                    <span class="f-12"> <?php echo $sidein['courier_ref_no']; ?> </span>
                                                </div>
                                                <div class="col-lg-4 p-2">
                                                    <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
                                                </div>
                                                    <?php } ?>

                                                 <?php }else{ ?>
                                                <div class="card-px text-center py-20 m-t-30">
                                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
                                                    <div class="text-center px-4">
                                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-------Meetting --->
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==9){ echo "active";
} ?>" id="workplace_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Workplace</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==10){ echo "active";
} ?>" id="security_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Security</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==11){ echo "active";
} ?>" id="compliance_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Compliance</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane <?php if($tab==12){ echo "active";
} ?>" id="health_safty_tab" role="tabpanel">
                <div class="card">
                    <div class="card-body">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Healty & Safty</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





</div>




