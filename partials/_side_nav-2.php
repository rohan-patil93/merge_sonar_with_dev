<?php 
function menudisplaytop($menuarray,$type){
    $str .= '<ul class="pcoded-submenu">';
    foreach ($menuarray as $menuarrayvalue) {
        $child = count($menuarrayvalue['child']);
        $link = $child >0 ?  'javascript:;' : 'index.php?option='.$menuarrayvalue['Page_Name'];
        $drop =  $child >0 ?  'pcoded-hasmenu' : '';
            //$icon = ($menuarrayvalue['Menu_Icon']== '' )?'kt-menu__link-bullet kt-menu__link-bullet--dot' : $menuarrayvalue['Menu_Icon'].'icon-xl';
        $icon = "";
        $str .= '<li class="'.$drop.'" >
                <a href="'.$link.'" class="waves-effect waves-dark"> &nbsp;<i class="'.$icon.'"><span></span></i><span class="pcoded-mtext">'.$menuarrayvalue['Label'].'</span>';
        
        $str.='</a>';
        if(count($menuarrayvalue['child'])>0){
            $str.=menudisplaytop($menuarrayvalue['child'],'right');
        }
        $str.='</li>';
    }
    $str.='</ul>';
    return $str;
}


?>
<script type="text/javascript">
    function checkinattendence(msg){
        //alert(dat+'/'+tim);
        //str = 'date='+dat+'&time='+tim;
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=employeeclocks',
            success: function(response) {
                console.log(response);
                //alert(response);
                //$('.success').html(response);
                //document.getElementById("myForm").submit();
                swal.fire({              
                    title: msg,              
                            //text: "Do you want to close ? ",              
                            type: "success",           
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",    
                            confirmButtonClass: "btn btn-danger", 
                            showCancelButton: false,     
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
                            cancelButtonClass: "btn btn-default"        
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                                window.location.reload();
                            }
                        }); 
                    }
                });
    }
</script>
<!-- [ navigation menu ] start -->
<nav class="pcoded-navbar">
    <div class="nav-list">
        <div class="pcoded-inner-navbar main-menu">
                <div class="mt-3 border-bottom">
                    <div class="sale-card ">
                        <!-- <div class="card-header">
                            <h5 class="card-herder-left">
                                <a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php // echo $_SESSION['employee']['display_name'];?>
                                </a>
                                <a href="#">
                                    <i class="flaticon2-correct text-success font-size-h5 mr-2"></i>
                                </a>
                            </h5> -->
                            <!--<div class="card-header-right">
                                <div class="dropdown-primary dropdown open c-drop">
                                    <button class="btn btn-white dropdown-toggle waves-effect waves-light " type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                        <i class="ti-more-alt"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                       <a class="dropdown-item waves-light waves-effect" href="#">Action</a>
                                       <a class="dropdown-item waves-light waves-effect" href="#">Another action</a>
                                       <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                                       <div class="dropdown-divider"></div>
                                       <a class="dropdown-item waves-light waves-effect" href="#">Something else</a>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-block text-center">
                            <div data-label="" class="radial-bar radial-bar-100 radial-bar-md radial-bar-danger mb-2 mt-0 custom-radial">
                                <img src="uploads/all/<?php echo $_SESSION['employee']['image']; ?>" class="img-120 " alt="image" />
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-12">
                                    <h6>
                                        <a href="#" class="text-dark-75 text-hover-primary font-size-h6 font-weight-bold mr-3 f-14 f-w-600 text-white"><?php echo $_SESSION['employee']['display_name'];?>
                                        </a>
                                    </h6>
                                    <h6 class="f-14"><?php // echo $desig['designation_name'];?></h6>
                                    <h6 class="f-14"><?php // echo $location['location_name'];?></h6>
                                    <h6 class="text-white f-14"><?php echo 'CFO'; ?></h6>
                                    <h6 class="text-white f-14"><?php echo 'Hydrabad'; ?></h6>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-6">
                                    <div class="s-cont d-inline-block">
                                       <div class="">
                                        <div id="accordion" role="tablist" aria-multiselectable="true">
                                        <div class="accordion-panel">
                                           <div class="accordion-heading" role="tab" id="headingOne">
                                              <h3 class="mb-0">
                                                 <a class="accordion-msg waves-effect waves-dark border-0 f-w-600 f-16 t-icon text-white" data-bs-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                                                    aria-expanded="true" aria-controls="collapseOne">Details <i class="ti-angle-down"></i>
                                                    </a>
                                                </h3>
                                            </div>
                                         </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 b-l-default">
                                    <div class="s-cont d-inline-block m-t-10">
                                        <a href="<?php echo $_SESSION['employee']['soc_facebook']; ?>" target="_blank" class="m-r-10 text-facebook">
                                            <i class="ti-facebook text-white"></i>
                                        </a>
                                        <a href="<?php echo ($_SESSION['employee']['soc_twitter'])!='' ? $_SESSION['employee']['soc_twitter'] : '#'; ?>" target="_blank" class="m-r-10 text-twitter">
                                            <i class="ti-twitter text-white"></i>
                                        </a>
                                        <a href="<?php echo $_SESSION['employee']['soc_linkedin']; ?>" target="_blank" class="m-r-5 text-linkedin">
                                            <i class="ti-linkedin text-white"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3" style="border-top:1px dotted #d2d2d2;">
                                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                                        <div class="">
                                            <p>
                                                <span style="display: block; text-align: justify;" class="font-weight-bold text-dark-50 mt-1 text-white"><b>About Me : </b><?php echo  $_SESSION['employee']['about_me'];?></span>
                                            </p>
                                        </div>
                                        <div class="mt-4 pt-2" style="border-top:1px dotted #d2d2d2;">
                                            <?php foreach($food_options as $food_option){ ?>
                                            <div class="col-lg-6 float-left text-center">
                                                <span class="switch switch-outline switch-icon switch-success">
                                                    <label>
                                                        <label class="col-form-label m-r-10"><?php echo $food_option['food_option']; ?>
                                                        </label><br />
                                                        <input type="checkbox" class="js-primary-sm" onclick="checkempfoodoption(<?php echo $food_option['id'].",".$_SESSION['emp_id']; ?>)" value="<?php echo $food_option['id']; ?>"  name="" <?php if(in_array($food_option['id'],$emp_food)){ ?> checked <?php } ?> >
                                                        <span></span>
                                                    </label>
                                                </span>
                                            </div>
                                            <?php } ?>
                                            <div class="col-lg-6 float-left text-center">
                                                <span class="switch switch-outline switch-icon switch-success"> 
                                                <label class="col-form-label m-r-10">Transport </label><br />       
                                                <input type="checkbox" class="js-primary-sm" onclick="checkemptransportrequest(<?php echo $_SESSION['emp_id'].",".$emptransport['id']; ?>)" value="<?php echo $emptransport['id']; ?>"  name="" <?php if($emptransport['id']!=""){ ?> checked <?php } ?> >
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <?php if (count($toplistnav) >0 && in_array('TNL', $_SESSION['role_nav'])){ foreach ($toplistnav as  $listnav) {?>
                       <!--  <ul class="pcoded-item pcoded-left-item">
                            <li class="pcoded-hasmenu ">
                                <a href="javascript:void(0)" class="waves-effect waves-dark">
                                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                                    <span class="pcoded-mtext">Dashboard</span>
                                </a>
                            </li>
                        </ul> -->
                <?php } } ?>
                <?php  if (count($topnav) >0 && in_array('TN', $_SESSION['role_nav'])){ foreach($topnav as $nav){    $child = count($nav['child']);
                        $link = $child >0 ?  'javascript:;' : 'index.php?option='.$nav['Page_Name'];
                        $drop =  $child >0 ?  'pcoded-hasmenu ' : ''; 
			$nav['Menu_Icon'] = $nav['Menu_Icon']!='' ? $nav['Menu_Icon'] : "icon-control-play";                                     
                ?>
                        <ul class="pcoded-item pcoded-left-item custom-menu-nav">
                            <li class="<?php echo  $drop; ?>">
                                <a href="<?php echo $link; ?>" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
					<?php if(strpos($nav['Menu_Icon'], "svg")){ ?>
                            <span class="svg-icon menu-icon">
                                <?php echo $nav['Menu_Icon']; ?>
                            </span>
                        <?php }else{ ?>                                 
                            <i class="<?php echo $nav['Menu_Icon']; ?>"></i> 
                        <?php } ?>
                        <!-- <i class="feather icon-sidebar"></i> -->
				    </span>
                                    <span class="pcoded-mtext"><?php echo $nav['Label']; ?></span>
                                </a>
                            
                            <?php if(count($nav['child'])>0){  echo menudisplaytop($nav['child'],'left');  } ?>
                            </li>
                        </ul>

                <?php } } ?>
                <?php if (count($topnavcenter) >0  && in_array('TNC', $_SESSION['role_nav'])){ foreach ($topnavcenter as  $nav) {
                        $child = count($nav['child']);
                    $listnav_pagename = $nav['Page_Name'];
                    if($_SESSION['userid']==50 && $listnav['Page_Name']=='COM_BULK_PROJECT_TIMESHEET'){ $listnav_pagename  = "COM_BULK_HR_PROJECT_TIMESHEET"; }
                    if($_SESSION['userid']==100 && $listnav['Page_Name']=='COM_BULK_PROJECT_TIMESHEET' ){ $listnav_pagename  = "COM_BULK_FINANCE_PROJECT_TIMESHEET"; }                  
                    $link = $child >0 ?  'javascript:;' : 'index.php?option='.$listnav_pagename;
                    $drop =  $child >0 ?  'menu-toggle' : '';
                ?>
                        <!--  <ul class="pcoded-item pcoded-left-item">
                            <li class="<?php echo  $drop; ?>">
                                <a href="<?php echo $link; ?>" class="waves-effect waves-dark">
                                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                                    <span class="pcoded-mtext"><?php echo $nav['Label']; ?></span>
                                </a>
                            
                            <?php if(count($nav['child'])>0){  echo menudisplaytop($nav['child'],'left');  } ?>
                            </li>
                        </ul> -->


                        

                <?php } } ?>


                

    </div>
</div>
</nav>
<!-- [ navigation menu ] end -->