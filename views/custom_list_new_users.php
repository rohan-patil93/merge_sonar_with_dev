<?php $_SESSION['apply_js']['multiselest'] = '1'; ?>
<script type="text/javascript">
    function skillpopup(){
        jQuery.ajax({
        url: "views/custom_insert_employee_skill_popup.php",
        data:'option=1&emp_id='+$('#emp_id').val(),
        type: "POST",
        success:function(data){
            $('#popup2').html(data);
            $('#kt_modal_KTDatatable_remote1').modal("show");
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
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
    }
    function DynamicPagerefrash(id=""){
        var option = $("#ajaxrefrashpagename").val();
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
                });
            },
            error:function (){}
        });
       }

</script>
<?php
$display_id = $_GET['NI']!="" ? $_GET['NI'] : '1';
$i=1; $k=1; $totfield = $totnotemptyfield = 0;
    foreach($_SESSION['user'] as $filed=>$fvalue){
        $totfield++;
        if($fvalue !=""){
            $totnotemptyfield++;
        }
    }
    $pp = round($totfield !=0?($totnotemptyfield/$totfield)*100:0);
    $menu_check_array = array('1','2','3','4');
    ?>
    <input type="hidden" name="emp_id" id="emp_id" value="<?php echo $_SESSION['user_email']; ?>">
    <div class="">
        <div class="container-fluid">
            <div class="">
                <div class="card d-flex flex-wrap flex-sm-nowrap p-10 mb-1 pt-4 pb-4 mt-3 c-p-0">
                    <div class="profile-open" onClick="divFunction()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-user"></i></div>
                    <div class="profile-close">
                        <div class="row col-lg-12">
                        <div class="col-lg-6 col-md-6 col-sm-12 col-12 p-r-10 d-r-n" style="border-right: 3px dashed #ccc;">
                            <div class="d-flex">
                                <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                                    <div class="symbol symbol-50 symbol-lg-120">
                                        <img src="../uploads/all/<?php echo $_SESSION['user']['user_photo']; ?>" class="img-120 " alt="Details"/>
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
                                            <span class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $_SESSION['user']['display_name'];?></span>


                                        </div>
                                    </div>
                        <!--end::Title-->
                        <!--begin::Content-->
                        <div class="">
                            <div class="d-flex flex-wrap justify-content-between mt-1 ">
                                <div class="d-flex flex-column flex-grow-1 pr-8  ">
                                    <div class="d-flex flex-wrap mb-2 mt-2">
                                        <?php if($_SESSION['user']['user_email']!=""){ ?>
                                        <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                                            <i class="ti-email f-14 text-primary"></i>&nbsp; <?php echo $_SESSION['user']['user_email'];?></a>
                                            <?php } ?>
                                                </div>
                                            </div>
                                            <input type="hidden" id="location_id" value="<?php echo $_SESSION['user']['location_id']; ?>">
                                            <input type="hidden" id="start_date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                </div> <!-- 11 -->
                            </div><!-- 22 -->
                        </div><!-- 23 -->
                        <div class="col-lg-5 col-md-5 col-sm-12 col-12 mt-2 m-l-10">

                            <?php if($_SESSION['user']['user_mobile']!=""){?>
                                <div class="row mb-2">
                                    <div class=" col-sm-1  text-right">
                                        <i class="ti-mobile f-16 text-primary"></i>
                                    </div>
                                    <span class="col-sm-3  f-w-600">Phone Number</span>
                                    <span class="col-sm-1">:</span>
                                    <div class="col-sm-6">
                                        <span ><?php echo $_SESSION['user']['user_mobile']; ?></span>
                                    </div>
                                </div>
                            <?php }
if($_SESSION['user']['reports_to']!="" && $_SESSION['user']['reports_to']!="0"){?>
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
                        </div>


                </div>


</div>


                <!----End --->

                <div class="col-lg-12">
                    <div class="open_basic_info_other" onClick="div2Function()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-menu"></i></div>
                    <div class="card mb-xl-10 c-m-0">
                        <div class="card-body mrgn-card">
                            <!--begin::Navs-->
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

                                            $jkm =0; foreach($saidnav as $nav){
                                                $child = count((array)$nav['child']);
                                                $link = $child >0 ?  'javascript:;' : 'index.php?option='.$nav['Page_Name'] ;
                                                $icon = ($nav['Menu_Icon'] == '') ?'' : $nav['Menu_Icon'];
                                                $toggle_name = $child > 0 ?  '1' : '0' ;
                                                if($display_id== $nav['id']){
                                                    $option_name = $nav['Page_Name'];
                                                }
                                                if(count((array)$nav['child'])>0){
                                                    ?>
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


                                                            <?php $jkm++; ?>
                                                            <?php } ?>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <!--begin::Navs-->
                                            <?php } ?>
                                        </div>
                                    </div>




                                     <div class="card  " >
                                         <div class="row">
                                            <div class="col-lg-11"></div>
                                            <div class="col-lg-1 text-right">
                                                <button type="button" class="btn p-0 border-0 bg-transparent ti-reload m-r-10 text-primary" onclick="DynamicPagerefrash()" data-bs-toggle="tooltip" data-placement="bottom" title="Refrash Page" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                            </div>
                                        </div>
                                        <div id="dynamicpageshow">
                                         <input type="hidden" id="ajaxrefrashpagename" value="COM_LIST_DASH_BOARD">
                                        <input type="hidden" value="<?php echo $option_name; ?>">
                                        <?php $_GET['option'] = $option_name;
                                        if($_GET['option']!=""){
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
                                                $refrashpagename ="custom_list_dash_board.php";
                                                include_once "custom_list_dash_board.php";
                                            }
                                        }

                                        ?>
                                    </div>
                                </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Navbar-->



                <div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-xl" >
                        <div class="modal-content"  id="popup2">
                        </div>
                    </div>
                </div>
