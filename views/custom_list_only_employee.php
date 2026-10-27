<?php
$display_id = $_GET['NI']!="" ? $_GET['NI'] : '1';
$i=1; $k=1;
foreach ($_SESSION['employee'] as  $value) {
    if($value!=""){
        $k++;
    }
$i++;
}
if($i !=0 && $i!=""){
        $pp = round(($k/$i)*(100));
    }else{
        $pp =$k*100;
    }

    $vdata1["where"] = array('id'=> $_SESSION['employee']['designation_id']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=designations&data=".$vdata;
    $configesponse = callAPI($vurl);
    $desig = json_decode($configesponse,1);
    $desig = $desig[0];

    $vdata1["where"] = array('id'=> $_SESSION['employee']['location_id']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$vdata;
    $configesponse = callAPI($vurl);
    $location = json_decode($configesponse,1);
    $location = $location[0];

    $vdata1["where"] = array('emp_id'=> $_SESSION['emp_id'],'is_active'=>'0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=emp_skills&data=".$vdata;
    $configesponse = callAPI($vurl);
    $empskills = json_decode($configesponse,1);


    $vurl = API."?mode=gettabledetailswithconditions&table=skills&data=";
    $configesponse = callAPI($vurl);
    $skills = json_decode($configesponse,1);

    $vurl = API."?mode=employeeshift&employee_id=".$_SESSION['emp_id'];
    $configesponse = callAPI($vurl);
    $shift = json_decode($configesponse,1);


    foreach ($skills as  $value) {
        $skill[$value['id']] = $value;
    }
    include_once "new_from_submit.php";
?>
<script type="text/javascript">
    function dynamicinsertpopup(option){
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option+'&emp_id=1',
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

                                <!--begin::Navbar-->
                                <div class="card mb-5 mb-xl-10">
                                    <div class="card-body pt-9 pb-0">
                                        <!--begin::Details-->
                                        <div class="d-flex flex-wrap flex-sm-nowrap mb-3">
                                            <div class="flex-shrink-0 mr-7 mt-lg-0 mt-3">
                                                <?php if( $_SESSION['employee']['image']!=""){ ?>
                                                <div class="symbol symbol-50 symbol-lg-120">
                                                    <img src="uploads/all/<?php echo $_SESSION['employee']['image']; ?>" alt="Details" class="img-120" />
                                                </div>
                                            <?php }else{ ?>
                                                <div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
                                                    <span class="font-size-h3 symbol-label font-weight-boldest"><?php echo initials($_SESSION['employee']['display_name']); ?></span>
                                                </div>
                                            <?php } ?>
                                                <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                            <span class="f-12 f-w-600">Profile </span>
                                                            <span class="f-12 f-w-600"><?php echo $pp; ?>%</span>
                                                        </div>
                                                        <div class="col-md-12">
                                                                <div class="progress progress-xs">
                                                                    <div class="progress-bar progress-bar-success" role="progressbar" style="width:  <?php echo $pp; ?>%;" aria-valuenow="<?php echo $pp; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                            </div>
                                                </div>
                                            <!--begin: Info-->
                                            <div class="flex-grow-1 m-l-10">
                                                <!--begin::Title-->
                                                <div class="d-flex justify-content-between flex-wrap m-t-1 ">

                                                    <div class="d-flex mr-3">
                                                        <a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $_SESSION['employee']['display_name'];?></a>
                                                        <a href="#">
                                                            <i class="flaticon2-correct text-success font-size-h5 mr-2"></i>
                                                        </a>
                                                        <a href="<?php echo $_SESSION['employee']['soc_facebook']; ?>" target="_blank" class="btn waves-effect waves-light btn-facebook btn-icon-30 m-l-5">
                                                             <i class="icofont icofont-social-facebook"></i>
                                                          </a>
                                                          <a href="<?php echo ($_SESSION['employee']['soc_twitter'])!='' ? $_SESSION['employee']['soc_twitter'] : '#'; ?>" target="_blank" class="btn waves-effect waves-light btn-twitter btn-icon-30 m-l-5">
                                                             <i class="icofont icofont-social-twitter"></i>
                                                          </a>
                                                          <a href="<?php echo $_SESSION['employee']['soc_linkedin']; ?>" target="_blank" class="btn waves-effect waves-light btn-linkedin btn-icon-30 m-l-5">
                                                             <i class="icofont icofont-brand-linkedin "></i>
                                                          </a>

                                                    </div>
                                                    <div class="my-lg-0 my-3">

                                        <?php 
                                        $check_in_display = $clock_data['clock_in'] !="" ? $clock_data['clock_in'] : "00:00:00" ;
                                            $check_out_display = $clock_data['clock_out'] !="" ? $clock_data['clock_out'] : "00:00:00" ;
                                            if($clock_data['clock_out'] !='' || count((array)$clock_data) == 0){

                                        ?>
                                        <button type="button" onclick="checkinattendence('You have clocked in')" class="btn btn-primary font-weight-bold btn-pill" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;<?php echo $check_out_display; ?> </div> </div> "> Check-in &nbsp; <i class='icon-lg far fa-clock'></i></button>
                                        <?php }else{?>
                                        <button type="button" onclick="checkinattendence('You have clocked out')" class="btn btn-success font-weight-bold btn-pill" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;00:00:00  </div> </div> "> Check-Out &nbsp; <i class='icon-lg far fa-clock'></i></button>
                                        <?php } ?>


                                                    </div>
                                                </div>
                                                <!--end::Title-->
                                                <!--begin::Content-->
                                            <div class="col-lg-12">
                                                <div class="d-flex flex-wrap justify-content-between mt-1 ">
                                                    <div class="d-flex flex-column flex-grow-1 pr-8 col-lg-12 ">
                                                        <div class="d-flex flex-wrap mb-2">
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                                            <i class="ti-email f-14 text-primary"></i>&nbsp; <?php echo $_SESSION['employee']['email_id'];?></a>
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                                            <i class="ti-user f-14 text-primary"></i>&nbsp;<?php echo $desig['designation_name'];?></a>
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold m-l-5">
                                                            <i class="ti-location-pin f-14 text-primary"></i>&nbsp;<?php echo $location['location_name'];?></a>
                                                        </div>
                                                        <div class="btn-group max-w-100px">
                                                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Key Skills</button>
                                                            <div class="dropdown-menu">
                                                                <?php foreach ($empskills as $eskill) {
                                                                    $irat = $skill[$eskill['skill_id']]['max_ratting']/5;
                                                                    $rate = $eskill['rating']/$irat;
                                                                    ?>
                                                                    <a class="dropdown-item" href="#"><?php echo $skill[$eskill['skill_id']]['skill']; ?> &nbsp;
                                                                    <?php for ($i=1; $i < 6 ; $i++) {
                                                                            if($rate >= $i){ ?>
                                                                                <i class="fas fa-star  text-warning mt-3"></i>
                                                                            <?php }elseif ($rate >=($i-0.5)) { ?>
                                                                                <i class="fas fa-star-half-alt  text-warning mt-3"></i>
                                                                            <?php }else{?>
                                                                                <i class="far fa-star  text-warning mt-3"></i>
                                                                            <?php }

                                                                    } ?>
                                                                </a>
                                                                <?php } ?>
                                                                <div class="dropdown-divider"></div>
                                                                <button type="button" class="dropdown-item" onclick="dynamicinsertpopup('COM_INSERT_EMP_SKILLS')">+ Add New</button>
                                                            </div>
                                                        </div>
                                                        <span class="font-weight-bold text-dark-50 mt-1"><b>About Me : </b><?php echo  $_SESSION['employee']['about_me'];?></span>
                                                    </div>


                                                </div>
                                            </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Navs-->
                                        <div class="d-flex mt-2 h-55px">

                                            <ul class="nav md-tabs">
                                                <!--begin::Nav item-->
                                                    <?php
                                                    $display_array = array('','COM_INSERT_BASIC_INFO','COM_LIST_EMPLOYEE_LEAVES','COM_LIST_PROJECTS','COM_INSERT_PROJECT_TIMESHEET_CALENDER');
                                                    $display_name = array('Home','My Information','Leaves','My Projects','Time Sheets');
                                                    ?>

                                                <?php $option_name=""; foreach ($display_array as $key=>$disarray) {
                                                    $active =0;
                                                    if($display_id==$key){ $active =1; $option_name =$disarray;
}
                                                    if($_SESSION['user_type'] != 4 ){  ?>
                                                <li class="nav-item">
                                                    <a class="nav-link text-active-primary me-6 <?php if($active==1){ echo "active";
} ?>"  href="index.php?option=COM_LIST_ONLY_EMPLOYEE&NI=<?php echo $key; ?>&Rid=<?php echo $_GET['Rid']; ?>">
                                                        <?php echo $display_name[$key] ?></a>
                                                </li>
                                                <?php }else{
                                                    if(!in_array($disarray, $hr_only)){ ?>
                                                        <li class="nav-item">
                                                    <a class="nav-link text-active-primary me-6 <?php if($active==1){ echo "active";
} ?>"  href="index.php?option=COM_LIST_ONLY_EMPLOYEE&NI=<?php echo $key; ?>&Rid=<?php echo $_GET['Rid']; ?>">
                                                        <?php echo $display_name[$key] ?></a>

                                                </li>
                                                <?php } ?>
                                                <?php } ?>
                                                <?php } ?>

                                            </ul>
                                        </div>
                                        <!--begin::Navs-->
                                    </div>
                                </div>
                                <!--end::Navbar-->

                                <div class="card mb-5 ">
                                    <?php $_GET['option'] = $option_name;
                    if($_GET['option']!=""){
                        include_once "includes/controller.php";
                        $dispage = str_replace("views/", '', $disptemp);
                        include_once $dispage;
                    }else{
                        include_once "custom_view_emp_timesheets.php";
                    }
                                    ?>
                                </div>


    </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
