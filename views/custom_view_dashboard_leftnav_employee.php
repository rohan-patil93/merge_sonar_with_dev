
<?php $_SESSION['apply_js']['multiselest'] = '1'; ?>
<script type="text/javascript">
    function skillpopup(){
        jQuery.ajax({
        url: "views/custom_insert_employee_skill_popup.php",
        data:'option=1&emp_id='+$('#emp_id').val(),
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
    }
  function clicktransport(){
    jQuery.ajax({
            url: "views/customtransportrequestpopup.php",
            data:'option=1&emp_id='+$('#emp_id').val(),
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function DynamicPagerefrash(id=""){
        var option = $("#ajaxrefrashpagename").val();
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';

        $('#dashdis').hide();
        $('#dynamicpageshow').html(htmloader);

        jQuery.ajax({
            url: "views/custom_inbox_page_redirection.php",
            data:'option='+option+"&rid="+id+"&ajaxpage=Y",
            type: "POST",
            success:function(htmlRows){
                $('#dynamicpageshow').html(htmlRows);
                $('#dashdis').hide();
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
                $(".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: DateFormate,
                    changeMonth: true,
                    changeYear: true
                });
            },
            error:function (){}
        });
    }
       function loadDynamicPage(option,id="",tabid="",subtabid=""){
           console.log(option);
           $(".removealltab").removeClass("active");
           $(".activetab_"+tabid).addClass("active");
           if(subtabid !=""){
               $(".activesubtab_"+subtabid).addClass("active");
           }
           var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#dynamicpageshow').html(htmloader);
           jQuery.ajax({
            url: "views/custom_inbox_page_redirection.php",
            data:'option='+option+"&rid="+id+"&ajaxpage=Y",
            type: "POST",
            success:function(htmlRows){
                $('#dynamicpageshow').html(htmlRows);
                $(".js-example-basic-single").select2();
                $(".js-example-basic-multiple").select2();
                $(".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                    dateFormat: DateFormate,
                    changeMonth: true,
                    changeYear: true
                  }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");

          //           html: true,
            },
            error:function (){}
        });
       }

</script>
<?php
 include_once "new_from_submit.php";

$display_id = $_GET['NI']!="" ? $_GET['NI'] : '1';
$i=1; $k=1;
foreach ($_SESSION['employee'] as  $value) {
    if($value!=""){
        $k++;
    }
    $i++;
}
    $pp = round(($k/$i)*(100));
    if(count((array)$_SESSION['emp_info']) == 0){
      $vurl = API."?mode=getnewemployeedashboarddetails&employee_id=".$_SESSION['emp_id'];
     $detailsres = callAPI($vurl);
     $resultdash = json_decode($detailsres,1);
    $_SESSION['emp_info'] = $resultdash;
    }else{
    $resultdash = $_SESSION['emp_info'];
    }
    $desig = $resultdash['emp'];
    $location = $resultdash['emp'];
    $empskills = $_SESSION['emp_info']['emp_skills'];
    $skills = $resultdash['skills'];
    $emp_food_options = $resultdash['employee_food_requests'];
    $food_options = $resultdash['food_options'];
    $emptransport =  $resultdash['employee_transport_requests'];
    $shift =  $resultdash['shifts'];


    foreach ($skills as  $value) {
        $skill[$value['id']] = $value;
    }

    $emp_food = array();
    foreach($emp_food_options as $emp_food_option){
        $emp_food[] = $emp_food_option['food_options'];
    }
    $menu_check_array = array('1','2','3','4');
    ?>
    <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
    <div class="">
        <div class="">
            <div class="">
                <div class="card d-flex flex-wrap flex-sm-nowrap p-10 mb-1 pt-4 pb-4 mt-3 c-p-0">
                    <div class="profile-open" onClick="divFunction()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                        <i class="ti-user"></i>
                    </div>
                    <div class="profile-close">
                        <div class="row col-lg-12">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-12 p-r-10 d-r-n" style="border-right: 3px dashed #ccc;">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                                        <?php if( $_SESSION['employee']['image']!=""){ ?>
                                        <div class="symbol symbol-50 symbol-lg-120">
                                            <img src="../uploads/all/<?php echo $_SESSION['employee']['image']; ?>" class="img-120 " alt="Details"/>
                                        </div>

                                        <?php }else{ ?>
                                        <div class="symbol symbol-50 symbol-lg-120">
                                            <img src="../uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="img-120 " alt="Details"/>
                                        </div>
                                        <?php } ?>
                                        <div class="d-block position-relative text-center" style="margin: -13px 0 0 0;z-index: 1;">                                        <a href="<?php echo $_SESSION['employee']['soc_facebook']; ?>" target="_blank" class="btn waves-effect waves-light btn-facebook btn-icon-30 ">
                                                <i class="icofont icofont-social-facebook"></i>
                                            </a>
                                            <a href="<?php echo ($_SESSION['employee']['soc_twitter'])!='' ? $_SESSION['employee']['soc_twitter'] : '#'; ?>" target="_blank" class="btn waves-effect waves-light btn-twitter btn-icon-30 m-l-5">
                                                <i class="icofont icofont-social-twitter"></i>
                                            </a>
                                            <a href="<?php echo $_SESSION['employee']['soc_linkedin']; ?>" target="_blank" class="btn waves-effect waves-light btn-linkedin btn-icon-30 m-l-5">
                                                <i class="icofont icofont-brand-linkedin "></i>
                                            </a>
                                        </div>
                                        <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">

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
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between flex-wrap mt-1">
                                            <div class="d-flex mr-3">
                                                <span class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $_SESSION['employee']['display_name'];?></span>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="d-flex flex-wrap justify-content-between mt-1 ">
                                                <div class="d-flex flex-column flex-grow-1 pr-8  ">
                                                    <div class="d-flex flex-wrap mb-2 mt-2">
                                                        <?php if($_SESSION['employee']['email_id']!=""){ ?>
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                                                <i class="ti-email f-14 text-primary"></i>&nbsp; <?php echo $_SESSION['employee']['email_id'];?>
                                                            </a>
                                                        <?php }
if($desig['designation_name']!=""){ ?>
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                                                <i class="ti-user f-14 text-primary"></i>&nbsp;<?php echo $desig['designation_name'];?>
                                                            </a>
                                                        <?php }
if($location['location_name']!=""){ ?>
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold m-l-5">
                                                                <i class="ti-location-pin f-14 text-primary"></i>&nbsp;<?php echo $location['location_name'];?>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                    <div>

                                                        <div class="btn-group max-w-100px float-left mt-2">
                                                            <button type="button" class="btn btn-success dropdown-toggle pt-2 pb-2 b-radius" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Key Skills</button>
                                                            <div class="dropdown-menu p-0">

                                                            <?php foreach ((array)$empskills as $eskill) {
                                                                $max_ratting = (float)($skill[$eskill['skill_id']]['max_ratting'] ?? 0);
                                                                $rating = (float)($eskill['rating'] ?? 0);

                                                                $irat = ($max_ratting > 0) ? ($max_ratting / 5) : 0;
                                                                $rate = ($irat > 0) ? ($rating / $irat) : 0;
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

                                                            <div class=""></div>
                                                            <button type="button" class="dropdown-item" onclick="skillpopup()">+ Add New</button>
                                                        </div>
                                                    </div>
                                                    <?php if($shift['shift_name']!=""){ ?>
                                                        <div class="float-left mt-3">&nbsp;
                                                            <span classs="h4 font-weight-bolder">Shift : </span>
                                                            <span classs=" mr-2 font-size-lg"> </span><?php echo $shift['shift_name']; ?></span>
                                                        </div>
                                                    <?php } ?>
                                                    </div>
                                                    <?php if($_SESSION['employee']['about_me']!=""){ ?>
                                                        <span class="font-weight-bold text-dark-50 mt-3"><b>About Me : </b><?php echo  $_SESSION['employee']['about_me'];?></span>
                                                    <?php } ?>
                                                    <input type="hidden" id="location_id" value="<?php echo $_SESSION['employee']['location_id']; ?>">
                                                    <input type="hidden" id="start_date" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5 col-md-5 col-sm-12 col-12 mt-2 m-l-10">

                                <?php if($_SESSION['employee']['phone']!=""){?>
                                <div class="row mb-2">
                                    <div class=" col-sm-1  text-right">
                                        <i class="ti-mobile f-16 text-primary"></i>
                                    </div>
                                    <span class="col-sm-3  f-w-600">Phone Number</span>
                                    <span class="col-sm-1">:</span>
                                    <div class="col-sm-6">
                                        <span ><?php echo $_SESSION['employee']['phone']; ?></span>
                                    </div>
                                </div>
                                <?php }
if($_SESSION['employee']['date_of_birth']!=""){?>
                                <div class="row mb-2">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-1 text-right">
                                        <i class="ti-gift f-16 text-primary"></i>
                                    </div>
                                    <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Birthday</span>
                                    <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                        <span ><?php echo fromDate($_SESSION['employee']['date_of_birth']); ?></span>
                                    </div>
                                </div>
                                <?php }
if($_SESSION['employee']['address']!=""){?>
                                <div class="row mb-2">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-1 text-right">
                                        <i class="ti-home f-16 text-primary"></i>
                                    </div>
                                    <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Address</span>
                                    <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                        <span ><?php echo $_SESSION['employee']['address']; ?></span>
                                    </div>
                                </div>
                                <?php }
if($_SESSION['employee']['gender']!=""){?>
                                <div class="row mb-2">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-1 f-20 text-right">

                                        <i class="fa fa-venus-mars f-16 text-primary"></i>
                                    </div>
                                    <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Gender</span>
                                    <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                        <span ><?php echo $_SESSION['employee']['gender']=="F"?"Female":"Male"; ?></span>
                                    </div>
                                </div>
                                <?php }
if($_SESSION['employee']['gender']!=""){?>
                                <div class="row ">
                                    <div class="col-lg-1 col-md-1 col-sm-1 col-1 text-right">
                                            <i class="ti-user text-primary f-16"></i>
                                    </div>
                                    <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Reports To</span>
                                    <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                                        <span ><?php echo $_SESSION['users'][$_SESSION['user']['reports_to']]; ?></span>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="mt-2 row">
                                <?php if(in_array(FOOD_OPTION_MODULE_USED, $_SESSION['module_list']) && $_SESSION['employee']['email_id']!=""){   ?>
                                    <?php foreach($food_options as $food_option){ ?>
                                        <div class="col-lg-3 float-left">
                                            <span class="switch switch-outline switch-icon switch-success">
                                                <label>
                                                    <input aria-label="Checkbox" type="checkbox" class="js-primary-sm" onclick="checkempfoodoption(<?php echo $food_option['id'].",".$_SESSION['emp_id']; ?>)" value="<?php echo $food_option['id']; ?>"  name="" <?php if(in_array($food_option['id'],$emp_food)){ ?> checked <?php
} ?> onkeydown="">
                                                    <span></span><span class="col-form-label"><?php echo $food_option['food_option']; ?></span>
                                                </label>
                                            </span>
                                        </div>
                                    <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-------------- LEFT MENU------------->

<div class="col-lg-12">
    <div class="open_basic_info_other" onClick="div2Function()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
        <i class="ti-menu"></i>
    </div>
    <div class="card mb-xl-10 c-m-0">
        <div class="card-body mrgn-card">
        <?php if (count((array)$saidnav) >0 ){ ?>
            <div class="d-flex h-55px">
                <div class="basic_info_other">
                    <ul class="nav md-tabs nav-bold nav-tabs-line nav-line-tabs-3x border-transparent fs-5 fw-bolder flex-nowrap responsive-tab My_Informarion_Tab infisuite_tab" style="width:100%;">
                        <?php
                        foreach($saidnav as $nav){
                            $child = count((array)$nav['child']);
                            if(count((array)$nav['child'])>0){
                                foreach ($nav['child'] as  $list) {
                                    $check_nav[$nav['id']][]=$list['id'];
                                }
                            }
                        }
                        $jkm =0;
                        foreach($saidnav as $nav){
                            $child = count((array)$nav['child']);
                            $link = $child >0 ?  'javascript:;' : 'index.php?option='.$nav['Page_Name'] ;
                            $icon = ($nav['Menu_Icon'] == '') ?'' : $nav['Menu_Icon'];
                            $toggle_name = $child > 0 ?  '1' : '0' ;
                            if($display_id== $nav['id']){
                                $option_name = $nav['Page_Name'];
                            }
                            if(count((array)$nav['child'])>0){ ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle removealltab activetab_<?php echo $nav['id']; ?> <?php if(in_array($display_id,$check_nav[$nav['id']])){ echo "active";
} ?>" data-bs-toggle="dropdown" href="#" aria-haspopup="true" aria-expanded="false" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                    <?php echo $nav['Label']; ?>
                                </a>
                                <div class="dropdown-menu">
                                    <?php  foreach ($nav['child'] as  $list) {
                                        if($display_id== $list['id']){ $option_name = $list['Page_Name'];
} ?>
                                        <a  href="#!" class="dropdown-item removealltab activesubtab_<?php echo $list['id']; ?>"  onclick="loadDynamicPage('<?php echo $list['Page_Name']; ?>','','<?php echo $nav['id']; ?>','<?php echo $list['id']; ?>')" ><?php echo $list['Label']; ?></a>


                                    <?php } ?>
                                </div>
                            </li>
                            <?php $jkm++;
                            }else{ ?>
                                <li class="nav-item">
                                    <a class="nav-link text-active-primary me-6 removealltab activetab_<?php echo $nav['id']; ?> <?php if($display_id == $nav['id']){ echo "active";
}else{ echo ($jkm=='0' && $display_id=='1')?"active":"";
} ?>"
                                        href="#!"  onclick="loadDynamicPage('<?php echo $nav['Page_Name']; ?>','','<?php echo $nav['id']; ?>')">
                                        <?php echo $nav['Label'];  ?> </a>
                                </li>
                            <?php $jkm++;
                            }
                        } ?>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

                                     <div class="card  " >
                                         <div class="row" style="display: none;">
                                            <div class="col-lg-11"></div>
                                            <div class="col-lg-1 text-right">
                                                <button type="button" class="btn p-0 border-0 bg-transparent ti-reload m-r-10 text-primary" onclick="DynamicPagerefrash()" data-bs-toggle="tooltip" data-placement="bottom" title="Refrash Page" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                            </div>
                                        </div>
                                        <div id="dynamicpageshow">
                                         <input type="hidden" id="ajaxrefrashpagename" value="COM_LIST_DASH_BOARD">
                                        <input type="hidden" value="<?php echo $option_name; ?>">
                                        <?php $_GET['option'] = $option_name;
                                        if($_GET['option']!=""){ echo "optin";
                                            if($_GET['popup']=='Y'){
                                                include_once "../includes/controller.php";
                                            }else{
                                                include_once "includes/controller.php";
                                            }
                                            $dispage = str_replace("views/", '', $disptemp);
                                            $refrashpagename =$dispage;
                                        }else{
                                            if(count((array)$_POST) !='0' && $_SESSION['ajaxpage']!=""){
                                                $refrashpagename =$_SESSION['ajaxpage'];
                                                include_once $_SESSION['ajaxpage'];

                                            }else{
                                                $_POST['ajaxpage'] = "Y";
                                                include_once "custom_list_dash_board.php";
                                            }
                                        }

                                        ?>
                                    </div>


                <!---------------------------->
            </div>
        </div>
    </div>
