<?php
if($_POST['option']!=""){
  $_GET['option'] = $_POST['option'];
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
}
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $_GET['option']);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$page_details['application_table_name'];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] :  viewTitle($_GET['option']);
$table = $list['application_table_name'];
$insert = $page_details['insert_page_name'];
$view_page =  $page_details['view_page_name'];
$edit_insert =  $page_details['edit_page_name'];
$content = $list['help_text']!='' ? $list['help_text'] : 'Help Test';
$lists = json_decode($list['list_attributes'],1);
$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
foreach ($lists as $key => $value) {
  if( isset($value['display_list']) && $value['display_list'] ==1 ) {
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
include_once "new_from_submit.php";

$url = API."?mode=gettabledetailswithconditions&table=".$table."&data=".$vdata;
$response = callAPI($url);
$bills = json_decode($response,1);
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
function assignemployeelistnew(pay_area_id){
    console.log(pay_area_id);
    var table_name = document.getElementById('table_name').value;
    jQuery.ajax({
        url: "views/custom_insert_pay_area_assign_employee_popup.php",
        data:'table='+table_name+'&pay_area_id='+pay_area_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popupxxl').html(data);
            $('#kt_modal_dynamic_popupxxl').modal("show");
        },
        error:function (){}
    });
}
</script>
<div class="pcoded-inner-content mob-pd p-0">
  <div class="main-body">
    <div class="page-wrapper mob-pd p-0">
     <div class="page-body">
      <div class="card">
        <div class="card-header table-card-header">
          <div class="col-lg-2">
            <h5 class=""><?php echo $title;  ?></h5>
            <div class="card-header-right table-card-header text-right">
            <a href="javascript:history.go(-1)" class="mb-2 mob-import ml-1 float-right">
              <button class="btn btn-primary btn-outline-primary btn-icon m-l-10"><i class="icofont icofont-reply text-primary"></i></button>
            </a>

            <a target="_blank"  href="index.php?option=COM_INSERT_DYNAMIC_IMPORT&table=<?php echo $table; ?>"  class="btn btn-primary mb-2 desktop-view">
            <i class="icofont icofont-cloud-upload icon-white"></i>
            <?php echo "Import";  ?>
          </a>

            <a href="index.php?option=<?php echo $insert; ?>" class="btn btn-success mb-2 desktop-view">
             <i class="icofont icofont-plus icon-white"></i>
              <?php echo $title;  ?>
            </a>
              <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
          </div>
            </a>
          </div>
        </div>
        <div class="card-block">
        <input type="hidden" name="table_name" id="table_name" value="<?php echo $table; ?>">
         <?php if(count((array)$bills)>0){ ?>
           <table id="new-cons" class="table table-striped table-bordered nowrap">
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
                  $rid = deocdeId("id:".$bill['id']);
                  $trid = $bill['id'];
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
                       <td style="text-align: right;"><?php echo customeummarmonyformate($bill[$header['name']]); ?></td>

                     <?php }  elseif (in_array($header['name'], $status_array)) { ?>
                      <td>
                        <?php $bill[$header['name']] = ($bill[$header['name']]== null || $bill[$header['name']]== "") ? '1' :  $bill[$header['name']] ; ?>
                        <span  class="form-label label label-lg  label-primary label-inline f-w-900 "><?php echo $stausresult[$bill[$header['name']]]['name'];?></span>
                      </td>
                    <?php } elseif( $header['name'] == 'current_status') { ?>
                      <td>
                        <div class="btn-group">
                         <button onclick="displaystausdrop(<?php echo $bill['id']; ?>,<?php echo "'".$table."'"; ?>,<?php echo $bill['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$bill[$header['name']]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$bill[$header['name']]]['name']; ?> </button>
                         <div class="dropdown-menu" id="dropdownlist_<?php echo $bill['id']; ?>">

                         </div>
                       </div>
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
                $edit = $edit_permission == 1 ? "index.php?option=".$insert."&Rid=".$rid : "javascript:showpermission()";
                $link_copy = $insert_permission == 1 ? "index.php?option=".$insert."&copy=1&Rid=".$rid : "javascript:showpermission()";
                $edit =  "index.php?option=".$insert."&Rid=".$rid;
                $link_copy =  "index.php?option=".$insert."&copy=1&Rid=".$rid;

                $view =  "index.php?option=".$view_page."&Rid=".$rid
                ?>
                <a href="<?php echo $edit; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm"   title="Edit Datails" ><i class="fa fa-edit"></i></a>
                <div class="btn btn-icon btn-primary btn-circle mr-2" onclick="assignemployeelistnew(<?php echo $trid; ?>)" data-container="body" data-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Allocate Resources" aria-label="Allocate Resources" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-user"></i></div>
              </td>

            </tr>
            <?php $i++;
} ?>

          </tbody>
        <?php }else{ ?>
            <div class="card-px text-center py-20 m-t-30">
                  <h2 class="f-40 fw-bolder m-b-30">Hello!</h2>
                  <p class="text-muted f-16 fw-bold m-b-30"><?php echo $empty_data; ?></p>
                  <button type="button" onclick="dynamicinsertpopup(<?php echo "'".$insert."'"; ?>)" class=" btn btn-success  ">
                  <i class="icofont icofont-plus icon-white "></i>
                  <?php echo $title;  ?>
                </button>
                <div class="text-center px-4">
              <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        <?php } ?>
      </table>
    </div>
  </div>
</div>

</div>
</div>
</div>
<!-- Page-body end -->
</div>
</div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" >
    <div class="modal-content"  id="popup2">

    </div>
  </div>
</div>
