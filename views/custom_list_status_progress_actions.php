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

$response = callAPI($url);
$primary_url = API."?mode=togetstatusprogressactiondetails";
$primary = callAPI($primary_url);
$bills = json_decode($primary,1);

$stausresult = ViewStatusData();
?>

<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <?php if($content !=''){ ?>
    <div class="alert alert-light alert-elevate" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text">
            <?php echo $content; ?>
        </div>
    </div>
    <?php } ?>
    <div class="kt-portlet">
        <div class="kt-portlet__head kt-portlet__head--lg">
            <div class="kt-portlet__head-label">
                <span class="kt-portlet__head-icon">
                    <i class="kt-font-brand flaticon2-line-chart"></i>
                </span>
                <h3 class="kt-portlet__head-title">
                    <?php echo $title;  ?>
                </h3>
            </div>
            <div class="kt-portlet__head-toolbar">
                <div class="kt-portlet__head-wrapper">
                    <a href="#" class="btn btn-clean btn-icon-sm">
                        <i class="la la-long-arrow-left"></i>
                        Back
                    </a>
                    &nbsp;
                    <?php
                                        $link = "index.php?option=".$insert  ?>
                    <div class="dropdown dropdown-inline">
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
                        <th scope="col">SL No.</th>
                        <th scope="col">Table Name</th>
                        <th scope="col">Label</th>
                        <th scope="col">Target Label</th>
                        <th scope="col">Action</th>
                    </tr>

                </thead>
                <tbody>
                    <?php $i=1; foreach($bills as $bill){?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo viewTitle($bill['table_name']); ?></td>
                            <td><?php echo $bill['action_label']; ?></td>
                            <td><?php echo $bill['statu_labels']; ?></td>
                            <td>
                                <?php
                                    $edit = "index.php?option=".$insert."&table=". $bill['table_name']."&progress=". $bill['id'];
                                    $link_copy = $insert_permission == 1 ? "index.php?option=".$insert."&copy=1&Rid=".$rid : "javascript:showpermission()";
                                ?>

                                            <div style="float: left;">
                                                    <a href="<?php echo $edit; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm"   title="Edit Datails" ><i  class="flaticon2-edit"></i></a>
                                           </div>
                                            <div style="float: left;">
                                                <a href="<?php echo $edit; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Copy Datails">
                                                <i class="flaticon2-file"></i></a>
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
