<?php
$_GET['option'] = $option;
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $_GET['option']);
$menu_details= toGetMenuDetailsBaseMenuPageName($_GET['option']);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&display_list_type=3&view=".$page_details['application_table_name'];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] :  viewTitle($_GET['option']);
$table = $list['application_table_name'];
$insert = $page_details['insert_page_name'];
$view_page =  $page_details['view_page_name'];
$lists = json_decode($list['list_attributes'],1);
$whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'created_by'=>$_SESSION['userid']);
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=".$table;
$response = callAPI($url);

$primary_url = API."?mode=togetprimarykey&table=".$table;
$primary = callAPI($primary_url);
$primary = json_decode($primary,1);
$bills = json_decode($response,1);
$stausresult = ViewStatusData();
$type_array = array('select','multi_select','radio','checklist', 'color_pick','image');
$dis = array();
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
?>
<div class="card card-custom gutter-b draggable" role="region" style="">
                                <div class="card-header">
                                    <div class="card-title">
                                        <h3 class="card-label"><?php echo $title; ?></h3>
                                    </div>
                                    <div class="card-toolbar">
                                        <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                                            <i class="ki ki-menu"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="padding-less">
                                        <div class="card card-custom gutter-b">
                                            <!--begin::Header-->
                                            <div class="card-header border-0">
                                                <div class="card-title align-items-start flex-column"></div>
                                                <div class="card-toolbar">
                                                    <ul class="nav nav-pills nav-pills-sm nav-dark-75">

                                                        <li class="nav-item">
                                                            <a class="nav-link py-2 px-4 font-weight-bolder active" data-toggle="tab" href="#kt_<?php echo $option; ?>_10" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Today</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link py-2 px-4 font-weight-bolder" data-toggle="tab" href="#kt_<?php echo $option; ?>_11" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Week</a>
                                                        </li>
                                                        <li class="nav-item">
                                                            <a class="nav-link py-2 px-4 font-weight-bolder" data-toggle="tab" href="#kt_<?php echo $option; ?>_12" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Month</a>
                                                        </li>


                                                    </ul>
                                                </div>
                                            </div>
                                            <!--end::Header-->

                                            <!--begin::Body-->
                                            <div class="card-body pt-2 pb-0 mt-n3">
                                                <div class="tab-content mt-0" id="myTabContent">
                                                    <!--begin::Tap pane-->
                                                    <div class="tab-pane fade active show" id="kt_<?php echo $option; ?>_10" role="tabpanel" aria-labelledby="kt_<?php echo $option; ?>_10">
                                                        <!--begin::Table-->
                                                        <div class="scroll-pull" data-scroll="true" style="height:200px;">
                                                            <table class="table table-borderless table-vertical-center">


                                                                <!--begin::Tbody-->
                                                                <tbody>
                                                                    <?php   foreach ($bills as $bill) {
                                                                        $to_day = date("Y-m-d");
                                                                        if($to_day ==  date("Y-m-d", strtotime($bill['created_date']))){?>

                                                                    <tr>
                                                                        <th scope="col" data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">

                                                                                    <div class="ml-4">
                                                                                        <?php $i=1;
                                                                                         foreach ($dis as $header) { ?>
                                                                                        <?php if($i%2 !=0){ ?>
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">
                                                                                            <?php echo $bill[$header['name']]; ?>
                                                                                        </div>
                                                                                    <?php }else{ ?>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                           <?php echo $bill[$header['name']]; ?>
                                                                                        </span>
                                                                                        <?php } ?>
                                                                                        <?php $i++;
} ?>
                                                                                    </div>

                                                                                </div>
                                                                            </span>
                                                                        </th>
                                                                        <th scope="col">
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>

                                                                        </th>
                                                                    </tr>
                                                                <?php } ?>
                                                                <?php } ?>
                                                                </tbody>
                                                                <!--end::Tbody-->
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                    <div class="tab-pane fade" id="kt_<?php echo $option; ?>_11" role="tabpanel" aria-labelledby="kt_<?php echo $option; ?>_11">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive table-scroll">
                                                            <table class="table table-borderless table-vertical-center">


                                                                <!--begin::Tbody-->
                                                                <tbody>
                                                                   <?php
                                                                       $to_day = date("Y-m-d"); $week = date("Y-m-d",strtotime("-7 days"));
                                                                    foreach ($bills as $bill) {
                                                                        if($to_day >= date("Y-m-d", strtotime($bill['created_date']))  &&  date("Y-m-d", strtotime($bill['created_date'])) >= $week ){?>

                                                                    <tr>
                                                                        <th scope="col" data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">

                                                                                    <div class="ml-4">
                                                                                        <?php $i=1;
                                                                                         foreach ($dis as $header) { ?>
                                                                                        <?php if($i%2 !=0){ ?>
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">
                                                                                            <?php echo $bill[$header['name']]; ?>
                                                                                        </div>
                                                                                    <?php }else{ ?>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                           <?php echo $bill[$header['name']]; ?>
                                                                                        </span>
                                                                                        <?php } ?>
                                                                                        <?php $i++;
} ?>
                                                                                    </div>

                                                                                </div>
                                                                            </span>
                                                                        </th>
                                                                        <th scope="col">
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>

                                                                        </th>
                                                                    </tr>
                                                                <?php } ?>
                                                                <?php } ?>
                                                                </tbody>
                                                                <!--end::Tbody-->
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                    <!--end::Tap pane-->

                                                    <!--begin::Tap pane-->
                                                    <div class="tab-pane fade" id="kt_<?php echo $option; ?>_12" role="tabpanel" aria-labelledby="kt_<?php echo $option; ?>_12">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive table-scroll">
                                                            <table class="table table-borderless table-vertical-center">


                                                                <!--begin::Tbody-->
                                                                <tbody>
                                                                     <?php
                                                                       $to_day = date("Y-m-d"); $week = date("Y-m-d",strtotime("-30 days"));
                                                                    foreach ($bills as $bill) {
                                                                        if($to_day >= date("Y-m-d", strtotime($bill['created_date']))  &&  date("Y-m-d", strtotime($bill['created_date'])) >= $week ){?>

                                                                    <tr>
                                                                        <th scope="col" data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">

                                                                                    <div class="ml-4">
                                                                                        <?php $i=1;
                                                                                         foreach ($dis as $header) { ?>
                                                                                        <?php if($i%2 !=0){ ?>
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">
                                                                                            <?php echo $bill[$header['name']]; ?>
                                                                                        </div>
                                                                                    <?php }else{ ?>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                           <?php echo $bill[$header['name']]; ?>
                                                                                        </span>
                                                                                        <?php } ?>
                                                                                        <?php $i++;
} ?>
                                                                                    </div>

                                                                                </div>
                                                                            </span>
                                                                        </th>
                                                                        <th scope="col">
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>

                                                                        </th>
                                                                    </tr>
                                                                <?php } ?>
                                                                <?php } ?>
                                                                </tbody>
                                                                <!--end::Tbody-->
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>

                                                    <div class="tab-pane fade show" id="kt_tab_pane_14" role="tabpanel" aria-labelledby="kt_tab_pane_12">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive table-scroll">
                                                            <table class="table table-borderless table-vertical-center">


                                                                <!--begin::Tbody-->
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="col" data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                                                                        <span class="symbol-label font-size-h4 font-weight-bold">H</span>
                                                                                    </div>
                                                                                    <div class="ml-4">
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">Hayes Boule</div>
                                                                                        <a href="#" class="text-muted font-weight-bold text-hover-primary">hboule0@hp.com</a>
                                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                                                            03-Sep-20
                                                                                        </span>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                            Developer
                                                                                        </span>
                                                                                    </div>

                                                                                </div>
                                                                            </span>
                                                                        </th>
                                                                        <th scope="col">
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>
                                                                        </th>
                                                                    </tr>
                                                                    <tr>
                                                                        <td data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                                                                        <span class="symbol-label font-size-h4 font-weight-bold">H</span>
                                                                                    </div>
                                                                                    <div class="ml-4">
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">Hayes Boule</div>
                                                                                        <a href="#" class="text-muted font-weight-bold text-hover-primary">hboule0@hp.com</a>
                                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                                                            03-Sep-20
                                                                                        </span>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                            Developer
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                                                                        <span class="symbol-label font-size-h4 font-weight-bold">H</span>
                                                                                    </div>
                                                                                    <div class="ml-4">
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">Hayes Boule</div>
                                                                                        <a href="#" class="text-muted font-weight-bold text-hover-primary">hboule0@hp.com</a>
                                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                                                            03-Sep-20
                                                                                        </span>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                            Developer
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                                                                        <span class="symbol-label font-size-h4 font-weight-bold">H</span>
                                                                                    </div>
                                                                                    <div class="ml-4">
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">Hayes Boule</div>
                                                                                        <a href="#" class="text-muted font-weight-bold text-hover-primary">hboule0@hp.com</a>
                                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                                                            03-Sep-20
                                                                                        </span>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                            Developer
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                                                            <span style="width: 250px;">
                                                                                <div class="d-flex align-items-center">
                                                                                    <div class="symbol symbol-40 symbol-light-primary flex-shrink-0">
                                                                                        <span class="symbol-label font-size-h4 font-weight-bold">H</span>
                                                                                    </div>
                                                                                    <div class="ml-4">
                                                                                        <div class="text-dark-75 font-weight-bolder font-size-lg mb-0">Hayes Boule</div>
                                                                                        <a href="#" class="text-muted font-weight-bold text-hover-primary">hboule0@hp.com</a>
                                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                                                            03-Sep-20
                                                                                        </span>
                                                                                        <span class="text-muted font-weight-bold d-block font-size-sm">
                                                                                            Developer
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </span>
                                                                        </td>
                                                                        <td>
                                                                            <div class="btn-group">
                                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Primary</button>
                                                                                <div class="dropdown-menu" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;" x-placement="bottom-start">
                                                                                    <a class="dropdown-item" href="#">Apporve</a>
                                                                                    <a class="dropdown-item" href="#">Rejection</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                                <!--end::Tbody-->
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                    <!--end::Tap pane-->
                                                </div>
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                    </div>
                                </div>
                            </div>
