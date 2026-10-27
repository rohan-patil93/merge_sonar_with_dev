<?php
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $_GET['option']);
$menu_details= toGetMenuDetailsBaseMenuPageName($_GET['option']);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$page_details['application_table_name'];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] : $list['page_title'];
$table = $list['application_table_name'];
$insert_permission = $_SESSION['role_permission'][$table]['permission_list'];
$edit_permission = $_SESSION['role_permission'][$table]['permission_insert'];
$insert = $page_details['insert_page_name'];
$view_page =  $page_details['view_page_name'];
$content = $list['help_text'];
$lists = json_decode($list['list_attributes'],1);
$url = API."?mode=gettabledetails&table=".$table;
$response = callAPI($url);
$data = json_decode($response,1);
$primary_url = API."?mode=togetprimarykey&table=".$table;
$primary = callAPI($primary_url);
$primary = json_decode($primary,1);
$stausresult = ViewStatusData();
$type_array = array('select','multi_select','radio','checklist', 'color_pick');
foreach ($lists as $key => $value) {
    if ($value['display_list'] ==1 ) {
        $dis[$value['display_postion']] ['name'] = $key;
        $dis[$value['display_postion']] ['lable'] = $value['lable'];
        $dis[$value['display_postion']] ['type'] = $value['type'];
        $dis[$value['display_postion']] ['add_custom_field'] = $value['add_custom_field'];

        if(in_array($value['type'], $type_array)){
            $field[$key] =  dyanamicselectdata($table,$key);
        }

    }
}
 ksort($dis);
print_r($dis);
?>
<div class="kt-subheader   kt-grid__item" id="kt_subheader">
                            <div class="kt-container  kt-container--fluid ">
                                <div class="kt-subheader__main">
                                    <h3 class="kt-subheader__title">
                                        <?php echo $title;  ?>
                                    </h3>
                                    <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                                    <div class="kt-subheader__group" id="kt_subheader_search">
                                        <span class="kt-subheader__desc" id="kt_subheader_total">
                                            <?php echo count((array)$data); ?> Total </span>

                                    </div>

                                </div>
                                <div class="kt-subheader__toolbar">
                                    <?php
                                        $link = "index.php?option=".$insert  ?>
                                    <a href="<?php echo $link; ?>" class="btn btn-label-brand btn-bold">
                                        New <?php echo $title; ?> </a>

                                </div>
                            </div>
                        </div>

                        <!-- end:: Content Head -->

                        <!-- begin:: Content -->
                        <div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

                            <!--Begin::Section-->

                            <div class="row">
                                <?php foreach ($data as $details){ print_r($details); exit;?>
                                <div class="col-xl-3">

                                    <!--Begin::Portlet-->
                                    <div class="kt-portlet kt-portlet--height-fluid">
                                        <div class="kt-portlet__head kt-portlet__head--noborder">
                                            <div class="kt-portlet__head-label">
                                                <div class="kt-portlet__head-title">
                                                </div>
                                            </div>
                                            <div class="kt-portlet__head-toolbar">
                                                <a href="#" class="btn btn-icon" data-toggle="dropdown" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                    <i class="flaticon-more-1 kt-font-brand"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <ul class="kt-nav">
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-line-chart"></i>
                                                                <span class="kt-nav__link-text">Reports</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-send"></i>
                                                                <span class="kt-nav__link-text">Messages</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-pie-chart-1"></i>
                                                                <span class="kt-nav__link-text">Charts</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-avatar"></i>
                                                                <span class="kt-nav__link-text">Members</span>
                                                            </a>
                                                        </li>
                                                        <li class="kt-nav__item">
                                                            <a href="#" class="kt-nav__link">
                                                                <i class="kt-nav__link-icon flaticon2-settings"></i>
                                                                <span class="kt-nav__link-text">Settings</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="kt-portlet__body">

                                            <!--begin::Widget -->
                                            <div class="kt-widget kt-widget--user-profile-2">
                                                <div class="kt-widget__head">
                                                    <div class="kt-widget__media">
                                                        <img class="kt-widget__img kt-hidden-" src="assets/media/project-logos/2.png" alt="Logo">
                                                        <img class="kt-widget__img kt-hidden" src="assets/media/users/300_19.jpg" alt="User Profile">
                                                        <div class="kt-widget__pic kt-widget__pic--danger kt-font-danger kt-font-boldest  kt-hidden">
                                                            MP
                                                        </div>
                                                    </div>
                                                    <div class="kt-widget__info">
                                                        <a href="#" class="kt-widget__titel kt-hidden-">
                                                            <?php echo $bank['Bank_Name'] ?>
                                                        </a>
                                                        <a href="#" class="kt-widget__username kt-hidden">
                                                            Charlie Stone
                                                        </a>
                                                        <span class="kt-widget__desc">
                                                            <?php echo $bank['Bank_Short_Code'] ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="kt-widget__body">
                                                    <div class="kt-widget__section">
                                                        Lorem ipsum dolor sit amet <a href="#" class="kt-font-brand kt-link kt-font-transform-u kt-font-bold"> #xrs-23pq</a>, sed
                                                        <b>USD342/Annual</b> doloremagna
                                                    </div>
                                                    <div class="kt-widget__item">
                                                        <div class="kt-widget__contact">
                                                            <span class="kt-widget__label">From Date:</span>
                                                            <a href="#" class="kt-widget__data"> <?php echo  date('M d, Y', strtotime($bank['From_Date']) ); ?> </a>
                                                        </div>
                                                        <div class="kt-widget__contact">
                                                            <span class="kt-widget__label">To Date:</span>
                                                            <a href="#" class="kt-widget__data"><?php echo  date('M d, Y', strtotime($bank['To_Date']) ); ?> </a>
                                                        </div>
                                                        <div class="kt-widget__contact">
                                                            <span class="kt-widget__label">Status:</span>
                                                            <span class="kt-widget__data"><?php echo isacStatusDescrption($bank['Current_Status']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="kt-widget__footer">
                                                    <button type="button" class="btn btn-label-danger btn-lg btn-upper">view project</button>
                                                </div>
                                            </div>

                                            <!--end::Widget -->
                                        </div>
                                    </div>

                                    <!--End::Portlet-->
                                </div>
                                <?php $i++;
} ?>
                            </div>

                            <!--End::Section-->

                            <!--Begin::Section-->
                            <div class="row">
                                <div class="col-xl-12">


                                    <div class="kt-portlet">
                                        <div class="kt-portlet__body">

                                            <!--begin: Pagination-->
                                            <div class="kt-pagination kt-pagination--brand">
                                                <ul class="kt-pagination__links">
                                                    <li class="kt-pagination__link--first">
                                                        <a href="#"><i class="fa fa-angle-double-left kt-font-brand"></i></a>
                                                    </li>
                                                    <li class="kt-pagination__link--next">
                                                        <a href="#"><i class="fa fa-angle-left kt-font-brand"></i></a>
                                                    </li>
                                                    <li>
                                                        <a href="#">...</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">29</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">30</a>
                                                    </li>
                                                    <li class="kt-pagination__link--active">
                                                        <a href="#">31</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">32</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">33</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">34</a>
                                                    </li>
                                                    <li>
                                                        <a href="#">...</a>
                                                    </li>
                                                    <li class="kt-pagination__link--prev">
                                                        <a href="#"><i class="fa fa-angle-right kt-font-brand"></i></a>
                                                    </li>
                                                    <li class="kt-pagination__link--last">
                                                        <a href="#"><i class="fa fa-angle-double-right kt-font-brand"></i></a>
                                                    </li>
                                                </ul>
                                                <div class="kt-pagination__toolbar">
                                                    <select aria-label="Select option" class="form-control kt-font-brand" style="width: 60px">
                                                        <option value="10">10</option>
                                                        <option value="20">20</option>
                                                        <option value="30">30</option>
                                                        <option value="50">50</option>
                                                        <option value="100">100</option>
                                                    </select>
                                                    <span class="pagination__desc">
                                                        Displaying 10 of 230 records
                                                    </span>
                                                </div>
                                            </div>

                                            <!--end: Pagination-->
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <!--End::Section-->
                        </div>

                        <!-- end:: Content -->

