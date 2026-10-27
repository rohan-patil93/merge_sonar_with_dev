<?php
$table_array = array('COM_SELECT_MEN_POWER'=>'job_man_power_requisitions');
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$table_array[$_GET['option']];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$title =  $menu_details['menu_label']!= '' ?  $menu_details['menu_label'] :  viewTitle($_GET['option']);
$table = $list['application_table_name'];
$insert_permission = $_SESSION['role_permission'][$table]['permission_list'];
$edit_permission = $_SESSION['role_permission'][$table]['permission_insert'];
$delete_permission = $_SESSION['role_permission'][$table]['permission_delete'];
$insert = $page_details['insert_page_name'];
$view_page =  $page_details['view_page_name'];
$content = $list['help_text']!='' ? $list['help_text'] : 'Help Test';
$lists = json_decode($list['list_attributes'],1);
$empty_data = $list['empty_table_message'];
$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
$whrcon = array();
$whrcon["where"] = array('company_id'=> $_SESSION['company_id']);
$emp_array= array('emp_id','employee_id');
if($_SESSION['user_type']==3){
   $emp_array= array('vendor_id');
}
foreach ($lists as $key => $value) {
    if (in_array($key, $emp_array) && $_SESSION['user_type']==3) {
           $whrcon["where"][$key] = $_SESSION['vendor_id'];
        }
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
include_once "new_from_submit.php";
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=".$table."&data=".$vdata;
$response = callAPI($url);
$primary_url = API."?mode=togetprimarykey&table=".$table;
$primary = callAPI($primary_url);
$primary = json_decode($primary,1);
$bills = json_decode($response,1);
$stausresult = ViewStatusData();
?>
<script type="text/javascript">
    function dynamicinsertpopup(option){
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option,
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });
    }
   function statuspopup(status,id){
     var table_name = document.getElementById('table_name').value;
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
            },
            error:function (){}
        });
   }

    function displaystausdrop(id,table,status_id){
        $.ajax({
            url: "ajax.php",
            data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#dropdownlist_"+id).html(data);
            },
            error:function (){}
        });

    }

</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
    <?php if($content !=''  && $_POST['option']==''){ ?>
    <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text"><?php echo $content; ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
    <?php } ?>
    <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <div class="card-title">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-line-chart"></i>
                </span>
                <h3 class="card-label">&nbsp;<?php echo $title;  ?> </h3>
            </div>

        </div>
        <div class="card card-custom">
            <div class="card-body">
                <?php if(count((array)$bills)>0){ ?>
                <!--begin: Datatable -->
                <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
                    <thead>
                        <tr>
                            <?php foreach ($dis as $header) {
                                $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                                ?>
                                    <th scope=\"col\"><?php echo $header['lable']; ?></th>
                                    <?php if($after == 1){
                                        $afterdata=dyanamicafterdata($table, $header['name']);

                                        if($afterdata['display'] != 'insert'){
                                            $afterfield[$header['name']] =  $afterdata['value'];
                                     ?>
                                        <th scope=\"col\"><?php echo  $afterdata['lable']; ?></th>
                                     <?php } else{
                                             $afterfield[$header['name']] =  '#no#';
                                     }
}?>
                            <?php } ?>
                            <th scope=\"col\">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $i =1;
                        $active = "kt-badge kt-badge--brand kt-badge--inline kt-badge--pill";
                        foreach ($bills as $bill) {
                            $send = array();
                            foreach ($primary as  $value) {
                                $send[]= "$value:".$bill[$value];
                            }
                            $send = implode(",", (array)$send);
                            $rid = deocdeId($send);?>
                        <tr>
                            <?php     $date_array = array('status_date','from_date','to_date','created_date','updated_date');
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

                                            <td><?php echo $str; ?></td>
                                    <?php }else{
                                    if ($header['name'] == 'company_id') { ?>
                                                <td><?php echo $_SESSION['company'][$bill['company_id']]; ?></td>
                                    <?php }elseif($header['type'] == 'number_type'){?>
                                        <td style="text-align: right;"><?php echo customeummarmonyformate($bill[$header['name']]); ?></td>

                                <?php    }  elseif (in_array($header['name'], $status_array)) { ?>
                                            <td>
                                                <?php $bill[$header['name']] = ($bill[$header['name']]== null || $bill[$header['name']]== "") ? '1' :  $bill[$header['name']] ; ?>
                                                <span  class="label label-lg label-light-primary label-inline font-weight-bold py-4"><?php echo $stausresult[$bill[$header['name']]]['name'];?></span>
                                            </td>
                                    <?php } elseif( $header['name'] == 'current_status') { ?>
                                    <td>
                                        <button class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$bill[$header['name']]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>;" type="button"  ><?php echo $stausresult[$bill[$header['name']]]['name']; ?> </button>
                                     </td>

                                <?php     }elseif (in_array($header['name'], $user_names)) { ?>
                                            <td><?php echo $_SESSION['users'][$bill[$header['name']]]; ?></td>
                                    <?php } elseif(in_array($header['name'], $date_array)){?>
                                                <td><?php echo dateformates($bill[$header['name']],$configdata['date_format'],$configdata['date_separator']); ?></td>
                                        <?php } else { ?>
                                                    <td>      <?php echo $bill[$header['name']]; ?> </td>
                                        <?php }
                                } ?>
                                    <?php if($after == 1 && $afterfield[$header['name']] != '#no#' ){ ?>
                                    <td><?php echo $afterfield[$header['name']]; ?></td>
                                    <?php } ?>
                            <?php }?>

                        </tr>
                    <?php $i++;
} ?>
                    </tbody>

                </table>

                <!--end: Datatable -->
                <?php } ?>
            </div>
    </div>
<input type="hidden" name="table_name" id="table_name" value="<?php echo $table; ?>">

 <div id="kt_modal_KTDatatable_remote_2" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1_2">

        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
</div></div></div>
