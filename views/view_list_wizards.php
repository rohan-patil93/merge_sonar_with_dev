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
<div class="card card-custom  gutter-b">
   <div class="card-header border-0">
      <h3 class="card-title font-weight-bolder text-dark"><?php echo $title; ?></h3>
      <div class="card-toolbar">
         <div class="dropdown dropdown-inline">
            <a href="#" class="btn btn-light-primary btn-sm font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select</a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
               <!--begin::Navigation-->
               <ul class="nav">
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
               <!--end::Navigation-->
            </div>
         </div>
      </div>
    </div>
   <!--end::Header-->
   <!--begin::Body-->
   <div class="tab-content mt-0" id="myTabContent">
                                             <!--begin::Tap pane-->
      <div class="tab-pane fade active show" id="kt_<?php echo $option; ?>_10" role="tabpanel" aria-labelledby="kt_<?php echo $option; ?>_10">
         <div class="card-body pt-2 max-h-200px scroll-pull" data-scroll="true">
            <?php   foreach ($bills as $bill) {
                  $to_day = date("Y-m-d");
               if($to_day ==  date("Y-m-d", strtotime($bill['created_date']))){ ?>
            <div class="d-flex align-items-center mb-10">
               <!--begin::Symbol-->

               <!--end::Symbol-->
               <!--begin::Text-->
               <div class="d-flex flex-column flex-grow-1 font-weight-bold">

                  <?php $i=1;
                     foreach ($dis as $header) { ?>
                  <span class="text-muted"><?php echo $bill[$header['name']]; ?> </span>
                  <?php } ?>
               </div>
               <!--end::Text-->
               <!--begin::Dropdown-->
               <div class="dropdown dropdown-inline ml-2" data-toggle="tooltip" title="Quick actions" data-placement="left">
                  <a href="#" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                  <i class="ki ki-bold-more-hor"></i>
                  </a>
                  <div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right">
                     <!--begin::Navigation-->
                     <ul class="navi navi-hover">

                        <li class="navi-item">
                           <a href="#" class="navi-link">
                           <span class="navi-text">
                           <span class="label label-xl label-inline label-light-success">Customer</span>
                           </span>
                           </a>
                        </li>
                     </ul>
                     <!--end::Navigation-->
                  </div>
               </div>
               <!--end::Dropdown-->
            </div>
            <?php } ?>
            <?php } ?>
         </div>
      </div>
        <!--begin::Tap pane-->
      <div class="tab-pane fade " id="kt_<?php echo $option; ?>_11" role="tabpanel" aria-labelledby="kt_<?php echo $option; ?>_11">
         <div class="card-body pt-2 max-h-200px scroll-pull" data-scroll="true">
            <?php   foreach ($bills as $bill) {
               $to_day = date("Y-m-d"); $week = date("Y-m-d",strtotime("-7 days"));
               if($to_day >= date("Y-m-d", strtotime($bill['created_date']))  &&  date("Y-m-d", strtotime($bill['created_date'])) >= $week ){ ?>
            <div class="d-flex align-items-center mb-10">
               <!--begin::Symbol-->

               <!--end::Symbol-->
               <!--begin::Text-->
               <div class="d-flex flex-column flex-grow-1 font-weight-bold">

                  <?php $i=1;
                     foreach ($dis as $header) { ?>
                  <span class="text-muted"><?php echo $bill[$header['name']]; ?> </span>
                  <?php } ?>
               </div>
               <!--end::Text-->
                 <div class="btn-group">
                  <button onclick="displaystausdrop(<?php echo $bill['id']; ?>,<?php echo "'".$table."'"; ?>,<?php echo $bill['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$bill[$header['name']]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$bill[$header['name']]]['name']; ?> </button>
                                       <div class="dropdown-menu" id="dropdownlist_<?php echo $bill['id']; ?>">

                                                     </div>
                                                 </div>
               <!--begin::Dropdown-->
               <div class="dropdown dropdown-inline ml-2" data-toggle="tooltip" title="Quick actions" data-placement="left">
                  <a href="#" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                  <i class="ki ki-bold-more-hor"></i>
                  </a>
                  <div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right">
                     <!--begin::Navigation-->
                     <ul class="navi navi-hover">

                        <li class="navi-item">
                           <a href="#" class="navi-link">
                           <span class="navi-text">
                           <span class="label label-xl label-inline label-light-success">Customer</span>
                           </span>
                           </a>
                        </li>
                     </ul>
                     <!--end::Navigation-->
                  </div>
               </div>
               <!--end::Dropdown-->
            </div>
            <?php } ?>
            <?php } ?>
         </div>
      </div>
       <div class="tab-pane fade " id="kt_<?php echo $option; ?>_12" role="tabpanel" aria-labelledby="kt_<?php echo $option; ?>_12">
         <div class="card-body pt-2 max-h-200px scroll-pull" data-scroll="true">
            <?php   foreach ($bills as $bill) {
              $to_day = date("Y-m-d"); $week = date("Y-m-d",strtotime("-30 days"));
              if($to_day >= date("Y-m-d", strtotime($bill['created_date']))  &&  date("Y-m-d", strtotime($bill['created_date'])) >= $week ){ ?>
            <div class="d-flex align-items-center mb-10">
               <!--begin::Symbol-->

               <!--end::Symbol-->
               <!--begin::Text-->
               <div class="d-flex flex-column flex-grow-1 font-weight-bold">

                  <?php $i=1;
                     foreach ($dis as $header) { ?>
                  <span class="text-muted"><?php echo $bill[$header['name']]; ?> </span>
                  <?php } ?>
               </div>
               <!--end::Text-->
               <!--begin::Dropdown-->
               <div class="dropdown dropdown-inline ml-2" data-toggle="tooltip" title="Quick actions" data-placement="left">
                  <a href="#" class="btn btn-hover-light-primary btn-sm btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                  <i class="ki ki-bold-more-hor"></i>
                  </a>
                  <div class="dropdown-menu p-0 m-0 dropdown-menu-md dropdown-menu-right">
                     <!--begin::Navigation-->
                     <ul class="navi navi-hover">

                        <li class="navi-item">
                           <a href="#" class="navi-link">
                           <span class="navi-text">
                           <span class="label label-xl label-inline label-light-success">Customer</span>
                           </span>
                           </a>
                        </li>
                     </ul>
                     <!--end::Navigation-->
                  </div>
               </div>
               <!--end::Dropdown-->
            </div>
            <?php } ?>
            <?php } ?>
         </div>
      </div>
   </div>
</div>
