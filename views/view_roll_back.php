<?php
$page_details= toGetTableNameBaseApplicationPagesList('list_page_name', $_GET['option']);
$menu_details= toGetMenuDetailsBaseMenuPageName($_GET['option']);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$page_details['application_table_name'];
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
$url = API."?mode=gettabledetails&table=".$table;
$response = callAPI($url);
$primary_url = API."?mode=togetprimarykey&table=".$table;
$primary = callAPI($primary_url);
$primary = json_decode($primary,1);
$bills = json_decode($response,1);
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
?>
<script>
    $(document).ready(function() {
            $('#kt_table_1').DataTable( {
                      rowReorder: {
                selector: 'tr',
            },
            colReorder: true,
            } );
});
</script>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <?php if($content !=''){ ?>
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">
            <?php echo $content; ?>
        </div>
    </div>
<?php } ?>

    <div class="kt-portlet kt-portlet--mobile">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon-list-2"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    <?php echo $title;  ?>
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <div class="kt-portlet__head-actions">
                        <div class="dropdown dropdown-inline">
                            <a href="javascript:history.go(-1)" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Back</span>
                            </a>
                            <button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="la la-download"></i> Export
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <ul class="kt-nav">
                                    <li class="kt-nav__section kt-nav__section--first">
                                        <span class="kt-nav__section-text">Export Tools</span>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link" id="export_print">
                                            <i class="kt-nav__link-icon la la-print"></i>
                                            <span class="kt-nav__link-text">Print</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link" id="export_copy">
                                            <i class="kt-nav__link-icon la la-copy"></i>
                                            <span class="kt-nav__link-text">Copy</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link" id="export_excel">
                                            <i class="kt-nav__link-icon la la-file-excel-o"></i>
                                            <span class="kt-nav__link-text">Excel</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link" id="export_csv">
                                            <i class="kt-nav__link-icon la la-file-text-o"></i>
                                            <span class="kt-nav__link-text">CSV</span>
                                        </a>
                                    </li>
                                    <li class="kt-nav__item">
                                        <a href="#" class="kt-nav__link" id="export_pdf">
                                            <i class="kt-nav__link-icon la la-file-pdf-o"></i>
                                            <span class="kt-nav__link-text">PDF</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        &nbsp;
                        <?php
                        $link = "index.php?option=".$insert  ?>
                        <a href="<?php echo $link; ?>"  class="btn btn-brand btn-elevate btn-icon-sm">
                            <i class="la la-plus"></i>
                            <?php echo $title;  ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="kt-portlet__body">

            <!--begin: Datatable -->
            <table class="table table-striped- table-bordered table-hover table-checkable" id="kt_table_1">

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
                        <?php     $date_array = array('Status_Date','From_Date','To_Date','Created_Date','Updated_Date');
                                $status_array = array('is_active','current_status','status_name');
                                $user_names = array('Created_By','Updated_By');

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
                                    }else{
                                        $str = $field[$header['name']][$str];
                                    }
                                    ?>

                                <td><?php echo $str; ?></td>
                                <?php }else{
                                if ($header['name'] == 'company_id') { ?>
                                <td><?php echo $company[$bill['company_id']]; ?></td>
                                <?php }  elseif (in_array($header['name'], $status_array)) { ?>
                                <td>
                                    <?php $bill[$header['name']] = ($bill[$header['name']]== null || $bill[$header['name']]== "") ? '1' :  $bill[$header['name']] ; ?>
                                    <span style="color: <?php echo "#".$stausresult[$bill[$header['name']]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>;" class="kt-badge  kt-badge--inline kt-badge--pill"><?php echo $stausresult[$bill[$header['name']]]['name'];?></span>
                                </td>
                                <?php } elseif (in_array($header['name'], $user_names)) { ?>
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
                        <td>
                            <?php
                                $edit = $edit_permission == 1 ? "index.php?option=".$insert."&Rid=".$rid : "javascript:showpermission()";
                                $link_copy = $insert_permission == 1 ? "index.php?option=".$insert."&copy=1&Rid=".$rid : "javascript:showpermission()";
                                $edit =  "index.php?option=".$insert."&Rid=".$rid;
                                $link_copy =  "index.php?option=".$insert."&copy=1&Rid=".$rid;

                                $view =  "index.php?option=".$view_page."&Rid=".$rid
                            ?>

                            <div style="float: left;">
                                    <a href="<?php echo $edit; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm"   title="Edit Datails" ><i  class="flaticon2-edit"></i></a>
                               </div>
                            <div style="float: left;">
                                <a href="<?php echo $link_copy; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Copy Datails">
                                <i class="flaticon2-file"></i></a>
                            </div>
                            <div style="float: left;">
                                <a href="<?php echo $view; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="view Datails">
                                <i class="flaticon-eye"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php $i++;
} ?>
                </tbody>
            </table>

            <!--end: Datatable -->
        </div>
    </div>
</div>
<script src="assets/js/pages/crud/datatables/advanced/row-callback.js" type="text/javascript"></script>
