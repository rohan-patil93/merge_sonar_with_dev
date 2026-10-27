<?php
$wherecon["where"] = array('is_active'=> '0','company_id'=>$_SESSION['company_id']);
$wherecon['order'] = "display_priority ASC";
$data_con = urlencode(json_encode($wherecon));
$url = API."?mode=gettabledetailswithconditions&table=application_menu_settings&data=".$data_con;
$response = callAPI($url);
$menu_data = json_decode($response,1);

if($_GET['PI']=="" && $_GET['PAGENAME']==""){
    $_GET['PI']="4"; $_GET['PAGENAME']="COM_LIST_CONFIG";
}
foreach ($menu_data as $menu) {
    $display_title[$menu['id']]=$menu['menu_label'];
    if($_GET['PI']==$menu['id']){
        $submenu_id = $menu['id'];
        $mainmenu_id = $menu['parent_menu'];

    }
    $parentmenuid = $menu['id'];
    if($menu['parent_menu']==0) {
        $main_menu_array[]= $menu;
    } else {
        $sub_menu_array[$menu['parent_menu']][]= $menu;
    }

}

?>
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Todo-->
        <div class="p-0 m-0">
            <!--begin::Aside-->
            <div class="flex-row-auto offcanvas-mobile w-200px w-xxl-275px row col-lg-12 pt-5 pb-5 p-0 m-0 padding-0" id="kt_todo_aside">
                <!--begin::Card-->
                <div class="card card-custom card-stretch col-lg-3 col-md-12 col-sm-12 col-12">
                    <!--begin::Body-->
                    <div class="card-body">
                        <!--begin:Nav-->
                        <div class="navi navi-hover navi-active navi-link-rounded navi-bold navi-icon-center navi-light-icon">
                            <!--begin:Item-->
                            <div class="navi-item my-2">
                                <a href="#" class="navi-link active">
                                    <span class="navi-icon mr-4">
                                        <span class="svg-icon svg-icon-lg">
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"/>
                                                    <path d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z" fill="#000000"/>
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </span>
                                    <span class="navi-text font-weight-bolder font-size-lg">APPLICATION SETTINGS</span>
                                </a>
                            </div>
                            <!--end:Item-->
                            <!--begin:Item-->
                            <!--end:Item-->
                            <!--begin:Item-->
                            <div class="accordion-block color-accordion-block" id="accordionExample3">
                                 <div class="color-accordion" id="color-accordion">
                                <?php
                                $i=0; foreach ($main_menu_array as $main_data) {
                                    $heading = strtoupper($main_data['menu_label']);
                                    $maintitle = strtoupper($main_data['menu_label']);
                                if($main_data['application_module'] !=""){


                                    $manimodules = json_decode($main_data['application_module'],1);

                                    if(in_array('0',$manimodules) || in_array($_SESSION['config']['application_module'],$manimodules)){
                                    ?>
                                     <a class="accordion-msg b-none waves-effect waves-light accodianbg">
                                            <?php if(strpos($main_data['menu_icon'], "svg")){ ?>
                                                <span class="svg-icon menu-icon svg-icon-success mr-1">
                                                    <?php echo $main_data['menu_icon']; ?>
                                                </span>
                                            <?php }else{ ?>
                                                <i class="<?php echo $main_data['menu_icon']; ?>"></i>
                                            <?php } ?>
                                            <?php echo $heading; ?>

                                            <b class="float-right f-20 f-w-700">+</b>
                                            </a>
                                        <div class="accordion-desc" style="border: 1px #CCC solid; margin-top: 0px !important;border-radius: 8px;">
                                                <?php
                                                foreach ($sub_menu_array[$main_data['id']] as $sub_menu) {
                                                    $subtitle = strtoupper($sub_menu['menu_label']);
                                                    $submodules = json_decode($sub_menu['application_module'],1);

                                                    if(in_array('0',(array)$submodules) ||  array_intersect((array)$_SESSION['module_list'],(array)$submodules)){


                                                    ?>
                                                    <div class="navi-item my-2 <?php if($sub_menu['id'] == $_GET['PI']){ echo "bg-info p-2";
} ?>">
                                                        <a href="index?option=COM_LIST_APPLICATION_MENU_SETTINGS&PI=<?php echo $sub_menu['id']; ?>&PAGENAME=<?php echo $sub_menu['page_name']; ?>" class="navi-link">
                                                            <?php if(strpos($sub_menu['menu_icon'], "svg")){ ?>
                                                                <span class="svg-icon menu-icon svg-icon-success mr-1">
                                                                    <?php echo $sub_menu['menu_icon']; ?>
                                                                </span>
                                                            <?php }else{ ?>
                                                                <i class="<?php echo $sub_menu['menu_icon']; ?>"></i>
                                                            <?php } ?>
                                                            <span class="navi-text font-weight-bolder font-size-lg "> <?php echo strtoupper($sub_menu['menu_label']); ?></span>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                                <?php } ?>
                                            </div>

                                <?php $i++;
}
}
} ?>
                                </div>
                                <!--end:Item-->
                            </div>
                            <!--end:Nav-->
                        </div>
                        <!--end::Body-->
                    </div>
                    <!--end::Card-->
                </div>
                <!--end::Aside-->

                <div class="flex-row-fluid ml-lg-8 col-lg-9 col-md-12 col-sm-12 col-12 padding-0">
                    <div class="d-flex flex-column flex-grow-1">
                        <!--begin::Head-->
                        <div class="card card-custom gutter-b">
                            <!--begin::Body-->
                            <div class="card-body d-flex align-items-center justify-content-between flex-wrap py-3">
                                <!--begin::Info-->
                                <div class="d-flex align-items-center mr-2 py-2">
                                    <!--begin::Title-->
                                    <h6 class="font-weight-bold mb-0 mr-10" aria-hidden="true"></h6>
                                    <!--end::Title-->
                                </div>
                                <!--end::Info-->
                                <!--begin::Users-->
                                <div class="symbol-group symbol-hover py-2">
                                    <div class="symbol symbol-30 symbol-light-primary" data-toggle="tooltip" data-placement="top" title="New user" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                        <span class="symbol-label font-weight-bold">
                                            <i class="fa flaticon2-plus font-size-sm"></i>
                                        </span>
                                    </div>
                                </div>
                                <!--end::Users-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Head-->
                        <!--begin::Row-->

                        <div class="card card-custom card-stretch" id="kt_todo_list">

                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body p-0">
                                <?php     $_GET['option'] = $_GET['PAGENAME'];


                                if($_GET['option']!=""){
                                    include_once "includes/controller.php";
                                    $dispage = str_replace("views/", '', $disptemp);
                                    include_once $dispage;
                                }
                                ?>
                            </div>
                            <!--end::Body-->
                        </div>

                        <!--end::Row-->
                    </div>
                </div>

            </div>
            <!--end::Todo-->
        </div>
        <!--end::Container-->
    </div>
