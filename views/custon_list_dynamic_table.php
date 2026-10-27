<?php
 if($_POST['filterajax'] =="Y"){
  $_GET['option'] = $_POST['option'];
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
if(count((array)$_POST['displayattributesfilterfields'])!='0'){
  $selected_fields = $_POST['displayattributesfilterfields'];
}
$current_page = $_POST['current_page'];
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $_POST['option']);
$menu_details= toGetMenuDetailsBaseMenuPageName($_GET['option']);
$filtercondition = "";
$dynamictableurl = API."?mode=getdynamictabledetails&application_table_name=".$page_details['application_table_name']."&curr_status=".$_POST['current_status']."&status=".$_POST['status']."&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&vendor_id=".$_SESSION['vendor_id']."&emp_id=".$_SESSION['emp_id']."&admin_access_employee=".$_SESSION['admin_access_employee']."&limit_select=".$_POST['limit_select']."&current_page=".$current_page."&filter_text=".$_POST['filter_text'].'&filter_dynamic_select='.$_POST['filter_dynamic_select'];
$dynamictableresult = callAPI($dynamictableurl);
$dynamictableres = json_decode($dynamictableresult,1);
$list = $dynamictableres['display_attributes'];
$statusjsondecode = $dynamictableres['status_progress'];
$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] :  viewTitle($_GET['option']);
$table = $list['application_table_name'];
$insert_permission = $_SESSION['role_permission'][$table]['permission_insert'];
$edit_permission = $_SESSION['role_permission'][$table]['permission_update'];
$delete_permission = $_SESSION['role_permission'][$table]['permission_delete'];
$permission_print = $_SESSION['role_permission'][$table]['permission_print'];
$permission_import = $_SESSION['role_permission'][$table]['permission_import'];
$permission_download = $_SESSION['role_permission'][$table]['permission_download'];
if($insert_permission == '1' && $_SESSION['admin_type'] !='1'){$insert_permissionbtn = "disabled";
}
if($permission_import == '1' && $_SESSION['admin_type'] !='1'){$permission_importbtn = "disabled";
}
if($edit_permission == '1' && $_SESSION['admin_type'] !='1'){$edit_permissionbtn = "disabled";
}

$insert = $page_details['insert_page_name'];
$view_page =  $page_details['view_page_name'];
$edit_insert =  $page_details['edit_page_name'];
$content = $list['help_text']!='' ? $list['help_text'] : 'Help Test';
$lists = json_decode($list['list_attributes'],1);
$empty_data = $list['empty_table_message'];
$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
$whrcon = array();
$emp_array   = array();

foreach ($lists as $key => $value) {
  $tablefiledslist[$key] = $value['lable'];
  $tablefileddtype[$key] = $value['type'];
  if($value['display_list'] ==1 || in_array($key, (array)$selected_fields)){
    $dis[$value['display_postion']] ['name'] = $key;
    $dis[$value['display_postion']] ['lable'] = $value['lable'];
    $dis[$value['display_postion']] ['type'] = $value['type'];
    $dis[$value['display_postion']] ['add_custom_field'] = $value['add_custom_field'];
    $defalut_fields[] = $key;
  }
}
if(count((array)$dis) !=0){
  ksort($dis);
}
 $field =  $dynamictableres['field_result'];
 $primary = $dynamictableres['primarykey'];

$stausresult = ViewStatusData();
$bills = $dynamictableres['dynmictabler'];
}

?>

         <?php if(count((array)$bills)>0){
      $tableclass = "basic-btn-non";
      if(($permission_print == '0' && $permission_download == '0') || $_SESSION['admin_type'] =='1'){
          $tableclass = "basic-btn";
      }elseif($permission_download == '0'){
          $tableclass = "basic-btn-download";
      }elseif($permission_print == '0'){
          $tableclass = "basic-btn-print";
      }

$total_pages = $dynamictableres['total_pages'];
      ?>

                 <table id="" class="table table-striped table-bordered nowrap">
                  <thead>
                    <tr>
                      <?php foreach ($dis as $header) {
                          $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                          ?>
                            <th scope="col"><?php echo $header['lable']; ?></th>
                            <?php if($after == 1){
                              $afterdata=dyanamicafterdata($table, $header['name']);
                              if($afterdata['display'] != 'insert'){
                                $afterfield[$header['name']] =  $afterdata['value'];
                             ?>
                              <th scope="col"><?php echo  $afterdata['lable']; ?></th>
                             <?php } else{
                                $afterfield[$header['name']] =  '#no#';
                             }
}?>
                      <?php } ?>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i =1;
                    $active = "kt-badge kt-badge--brand kt-badge--inline kt-badge--pill";
                    foreach ($bills as $bill) {

                        $send = array();
                        foreach ($primary as  $value) {
                            $send[]= "$value:".$bill[$value];
                        }
                        $send = implode(",", (array)$send);
                        $rid = deocdeId($send);
                     ?>
                     <tr>
                        <?php   $date_array = array('status_date','from_date','to_date','created_date','updated_date');
                            $status_array = array('is_active','status_name');
                            $user_names = array('created_by','updated_by');

                            foreach ($dis as $header) {
                            $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                            if(in_array($header['type'], $type_array)){
                              $str = $bill[$header['name']];
                              $str =  json_decode($str);
                              if(is_array($str)){
                                foreach ($str as  $st) {
                                  $sts[] = $field[$header['name']][$st];
                                }
                                $str = implode(",", (array)$sts);

                              }elseif( $header['type'] == 'color_pick'){
                                $str = "<div style='background: #".$bill[$header['name']].";width: 20px;height: 20px;'>&nbsp;</div>";
                              }elseif( $header['type'] == 'image'){
                                $bimage = 'uploads/all/'.$bill[$header['name']];
                                $str = "<img src='$bimage' style='width:35px;' alt='Preview'>";
                              }else{
                                $str =  $bill[$header['name']];

                                $str = $field[$header['name']][$str];
                              }
                              ?>

                                 <td><?php echo todisplayviewfiled($str,$header['type']); ?></td>
                            <?php }elseif( $header['type'] == 'date_pick'){
                 ?>
                               <td><?php echo  $str = $bill[$header['name']] != "" ? fromDate($bill[$header['name']]) : ""; ?></td>
                            <?php }else{
                            if ($header['name'] == 'company_id') { ?>
                                  <td><?php echo $_SESSION['company'][$bill['company_id']]; ?></td>
                            <?php }elseif($header['type'] == 'number_type'){?>
                               <td style="text-align: right;"><?php echo customeNumberFormate($bill[$header['name']]); ?></td>

                          <?php }elseif($header['type'] == 'currency'){?>
                               <td style="text-align: right;"><?php echo customeummarmonyformate($bill[$header['name']]); ?></td>

                          <?php }  elseif (in_array($header['name'], $status_array)) { ?>
                                <td>
                                  <?php $bill[$header['name']] = ($bill[$header['name']]== null || $bill[$header['name']]== "") ? '1' :  $bill[$header['name']] ; ?>
                                  <span  class="form-label label label-lg  label-primary label-inline f-w-900 "><?php echo $stausresult[$bill[$header['name']]]['name'];?></span>
                                </td>
                            <?php } elseif( $header['name'] == 'current_status') { ?>
                            <td>

                              <span class="form-label label label-md label-warning float-right" style="color: <?php echo $stausresult[$bill[$header['name']]]['color']; ?>;    background:  <?php echo $stausresult[$bill[$header['name']]]['background_color']; ?>; border-color: <?php echo $stausresult[$bill[$header['name']]]['background_color']; ?>;"><?php echo $statusjsondecode[$bill[$header['name']]]['name']; ?> </span>

                            </td>

                          <?php   }elseif (in_array($header['name'], $user_names)) { ?>
                                 <td><?php echo todisplayviewfiled($_SESSION['users'][$bill[$header['name']]],$header['type']); ?></td>
                            <?php } elseif(in_array($header['name'], $date_array)){?>
                                  <td><?php echo dateformates($bill[$header['name']],$configdata['date_format'],$configdata['date_separator']); ?></td>
                              <?php } else { ?>
                                    <td>   <?php echo todisplayviewfiled($bill[$header['name']],$header['type']); ?> </td>
                              <?php }
                          } ?>
                            <?php if($after == 1 && $afterfield[$header['name']] != '#no#' ){ ?>
                            <td><?php echo todisplayviewfiled($afterfield[$header['name']],$header['type']); ?></td>
                            <?php } ?>
                        <?php }?>
                        <td>
                          <?php
                            if($table=="employees" || $table=="emp_expenses" || $table=="application_users"){
                                  $insert = $edit_insert;
                            }
                            if($table=="employees" && $_SESSION['work_module_setup_id']==19){
                                $edit_insert = 'COM_VIEW_VMS_EMPLOYEE';
                                $insert = $edit_insert;
                            }
                            $edit = $edit_permission == 1 ? "index.php?option=".$insert."&Rid=".$rid : "javascript:showpermission()";
                            $link_copy = $insert_permission == 1 ? "index.php?option=".$insert."&copy=1&Rid=".$rid : "javascript:showpermission()";
                            $edit =  "index.php?option=".$insert."&Rid=".$rid;
                            $link_copy =  "index.php?option=".$insert."&copy=1&Rid=".$rid;

                            $view =  "index.php?option=".$view_page."&Rid=".$rid
                          ?>

                  <a href="<?php echo $edit; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm"   title="Edit Details" <?php echo $edit_permissionbtn; ?>><i class="fa fa-edit" ></i></a>
                             <?php if($page_details['insert_page_type'] == 1){ ?>
                 <a href="<?php echo $link_copy; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Copy Details"><i class="fa fa-clone"></i> </a>
                              <a href="<?php echo $view; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                            <?php } ?>

             </td>

                      </tr>
                      <?php $i++;
} ?>
                  </tbody>
                   <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <h2 class="f-40 fw-bolder m-b-30">Hello!</h2>
                        <p class="text-muted f-16 fw-bold m-b-30"><?php echo $empty_data; ?></p>
                        <?php if($_SESSION['user_type']!=3){ ?>
                        <?php if($list['insert_page_type'] ==2){?>
                        <a target="_blank"  href="<?php echo $link; ?>"  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" <?php echo $insert_permissionbtn; ?>>
                            <i class="la la-plus"></i>
                            <?php echo $title;  ?>
                        </a>
                        <?php }elseif($list['insert_page_type'] ==1){?>
                                <button type="button" onclick="dynamicinsertpopup(<?php echo "'".$insert."'"; ?>)" class=" btn btn-success  " <?php echo $insert_permissionbtn; ?>>
                            <i class="icofont icofont-plus icon-white "></i>
                            <?php echo $title;  ?>
                        </button>
                        <?php }else{ ?>
                            <a href="<?php echo $link; ?>"  class=" btn btn-success  " <?php echo $insert_permissionbtn; ?>>
                            <i class="icofont icofont-plus icon-white "></i>
                            <?php echo $title;  ?>
                            </a>
                        <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                <?php } ?>
                </table>

<div class="mt-5" >
<nav aria-label="Page navigation example p-2" style="overflow-x: scroll !important;">
    <ul class="pagination">
      <li class="page-item <?php echo ($current_page =="1" || $current_page =="")?"disabled":""; ?>">
          <button type="button" class="page-link" tabindex="-1" onclick="dynamicFilterTable('','<?php echo $current_page-1; ?>')">Previous</button>
      </li>
    <?php
       for ($i = 1; $i <= $total_pages; $i++){
          if ($i == $current_page){ ?>
              <li class="page-item active"><button type="button" class="page-link active" onclick="dynamicFilterTable('','<?php echo $i; ?>')"><?php echo $i; ?></button></li>
        <?php }else{ ?>
              <li class="page-item"><button type="button" class="page-link" onclick="dynamicFilterTable('','<?php echo $i; ?>')"><?php echo $i; ?></button></li>
        <?php } ?>
        <?php } ?>

        <li class="page-item <?php echo ($total_pages <= $current_page)?"disabled":""; ?>">
            <button type="button" class="page-link" tabindex="-1" onclick="dynamicFilterTable('','<?php echo $current_page+1; ?>')">Next</button>
        </li>
    </ul>
</nav>
</div>
<?php if($_POST['option']!="" && $_POST['notshow']==""){  ?>
    <!--------------  data table   --------------->
    <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script src="assets/pages/data-table/js/jszip.min.js"></script>
    <?php } ?>
