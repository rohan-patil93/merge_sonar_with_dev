<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if (isset($_POST['submit']) && $_POST['submit'] == 'search') {
        $array =  array('category_id', 'vendor_id', 'cost_center_id', 'location_id','opex_cpex','from','to');
        foreach ($array as  $value) {
            if($_POST[$value] != ''){
                $ress[$value] = $_POST[$value];
            }
        }

    }
$table_name = $_REQUEST['table'];
$_POST['get_table'] = $table_name;
$_POST['company_id'] = $_SESSION['company_id'];
$_POST['user_id'] = $_SESSION['userid'];
$_POST['clone_table'] = $_REQUEST['clone'];
$_POST['table'] = $table_name;
$_POST['get_display_table'] = $_POST['get_display_table']!="" ? $_POST['get_display_table'] : 'vendors';
$tableurl = API."?mode=serchforpos&method=post";
$response = file_post_contents($tableurl, $_POST, '');
$result = json_decode($response,1);
$quotations = $result['quotations'];
$purchase_orders = $result['purchase_orders'];
$data = $result['transaction_details'];
$already_trasnaction = $result['already_trasnaction'];
$capexopex = $result['capexopex'];
$datacost = $result['cost_centers'];
$datalocation = $result['locations'];
$datavendor = $result['vendors'];
$dis_list = $result['display_attributes'];
$status_progress = $result['status_progress'];
$content = $data['help_text']!='' ? $data['help_text'] : "Help Test";
$rr_data = toGetpageOptionName($table_name,$_REQUEST['get_option']);
$nextlevelsteptable = $_REQUEST['clone'] !=""?$_REQUEST['clone']:$rr_data['genrate_next_level'];
$rr_data['genrate_next_level'] = $nextlevelsteptable;
$pr_data = toGetpageOptionName($nextlevelsteptable);
$lists = json_decode($dis_list['list_attributes'],1);
$cus_list = $lists['party_id'];
$selectdata = dyanamicselectdata($table_name,'party_id');
$vdata1["where"] = array('application_table_name'=> $table_name,'field_name'=>'party_id');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=display_attribute_types&data=".$vdata;
$configesponse = callAPI($vurl);
$return = json_decode($configesponse,1);
$return = $return['0'];
$return_data = json_decode(urldecode($return['content_details']),1);
$get_table =$return_data['table_name'];
$viewpageotion = getPagenametotable($table_name,'insert_page_name');
$stausresult = ViewStatusData();
$edit_permission = $_SESSION['role_permission'][$nextlevelsteptable]['permission_insert'];
if($edit_permission == '1'){$newinsert_permissionbtn = "disabled";
}


$edit_permission = $_SESSION['role_permission'][$table_name]['permission_update'];
if($edit_permission == '1'){$edit_permissionbtn = "disabled";
}
$permission_approval = $_SESSION['role_permission'][$table_name]['permission_approval'];
if($permission_approval == '1'){$edit_permissionbtn = "disabled";
}
$get_option = $_POST['get_option'];


?>
<div class="dt-responsive table-responsive">
        <?php if(count((array)$data) !=0){?>
            <table  class="table table-hover m-b-0" style="font-size:13px;">
                <thead>
                    <tr>

                        <th scope="col">Trans No.</th>
                        <th scope="col">Date</th>
                        <th scope="col"><?php echo $cus_list['lable']; ?></th>



                        <th scope="col" class="text-center">Generate <?php //echo $rr_data['genrate_next_level']; ?> </th>
                        <th scope="col" class="text-center"></th>

                    </tr>
                </thead>
                <tbody>
                    <?php $i=0; foreach ($data as $row){

                        $rid = $row['id'];
                        $send = "id:".$rid;
                        $view =  "index.php?option=".$pr_data['option_url']."&table=".$table_name ."&Rid=".$rid ;
                        $only_view =  "index.php?option=".$viewpageotion."&Rid=".deocdeId($send);
                        if($table_name=='goods_receipt_notes' && $_POST['get_option']=='COM_LIST_QUALITY_CHECK_FORM_GRN'){
                            $view =  "index.php?option=CUSTOM_INSERT_APPLICATION_TABLE_QUALITY_CHECK&table=".$table_name ."&Rid=".$rid ;
                        }

                        ?>
                        <tr>

                            <td><a  target="_blank" href="<?php echo $only_view; ?>" <?php echo $edit_permissionbtn; ?>><?php echo $row['transaction_number']; ?></a></td>
                            <td><?php echo fromDate($row['transaction_date']); ?></td>
                            <td><?php echo $row['vendor_name']; ?></td>


                            <td class="text-center">
<?php
if($table_name == "requisitions" ||  $table_name == "quotations"){
    if($_REQUEST['clone'] == "quotations"){
        if(!isset($purchase_orders[$row['id']])){ ?>
            <button class="btn btn-primary btn-brand--icon" id="kt_search" onclick="changestatus('quotations','<?php echo $rid; ?>','<?php echo $row['itemtb_id']; ?>')" <?php echo $newinsert_permissionbtn; ?>>
            <span><?php echo $rr_data['genrate_next_level']; ?></span>
            </button>
        <?php
        }else{
            echo " Purchase Orders In Process";
        }
    }
    if($_REQUEST['clone'] == "purchase_orders"){
        if(!isset($quotations[$row['id']])){ ?>
            <button class="btn btn-primary btn-brand--icon" id="kt_search_2" onclick="changestatus('purchase_orders','<?php echo $rid; ?>','<?php echo $row['itemtb_id']; ?>')"  <?php echo $newinsert_permissionbtn; ?>><span>Purchase Order</span>
            </button>
        <?php
        }else{
            echo " Quotations In Process";
        }
    }
}elseif($table_name == "enquiries" ||  $table_name == "estimates"){
    if($_REQUEST['clone'] == "estimates"){
        if(!isset($purchase_orders[$row['id']])){ ?>
            <button class="btn btn-primary btn-brand--icon" id="kt_search_3" onclick="changestatus('estimates','<?php echo $rid; ?>','<?php echo $row['itemtb_id']; ?>','customers')" <?php echo $newinsert_permissionbtn; ?>>
            <span><?php echo $rr_data['genrate_next_level']; ?></span>
            </button>
        <?php
        }else{
            echo " Sales Orders In Process";
        }
    }
    if($_REQUEST['clone'] == "sales_orders"){
        if(!isset($quotations[$row['id']])){ ?>
            <button class="btn btn-primary btn-brand--icon" id="kt_search_4" onclick="changestatus('sales_orders','<?php echo $rid; ?>','<?php echo $row['itemtb_id']; ?>','customers')"  <?php echo $newinsert_permissionbtn; ?>><span>Sales Order</span>
            </button>
        <?php
        }else{
            echo " Estimates In Process";
        }
    }
}elseif($table_name == "sales_orders"){
    if($_REQUEST['clone'] == "purchase_orders"){ ?>
            <button class="btn btn-primary btn-brand--icon" id="kt_search_5" onclick="changestatus('purchase_orders','<?php echo $rid; ?>','<?php echo $row['itemtb_id']; ?>','vendors')" <?php echo $newinsert_permissionbtn; ?>>
            <span><?php echo ucwords(str_replace("_"," ",$rr_data['genrate_next_level'])); ?></span>
            </button>
        <?php
    }else{
        echo "---";
    }

}else{
        $disrrdata = array('goods_receipt_notes', 'grn_returns');
        $valdisrrdata =  array('goods_receipt_notes'=>'GRNs', 'grn_returns'=> 'GRN Returns');
        if(in_array($rr_data['genrate_next_level'],$disrrdata)){
            $rr_data['genrate_next_level'] = $valdisrrdata[$rr_data['genrate_next_level']];
        }
    ?>
    <a target="_blank" href="<?php echo $view; ?>" <?php echo $newinsert_permissionbtn; ?>>
    <button class="btn btn-primary btn-brand--icon" id="kt_search_6" >
    <span><?php echo ucwords(str_replace("_"," ",$rr_data['genrate_next_level'])); ?></span>
    </button>
    </a>
<?php } ?>
                                </td>
                                <td>
                                    <span class="form-label label label-xs label-success" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No. Of <?php echo ucwords(str_replace("_"," ",$rr_data['genrate_next_level'])); ?>"><?php
                                        if(count((array)$quotations[$row['id']])!=0){
                                            echo count((array)$quotations[$row['id']]);
                                        }elseif(count((array)$purchase_orders[$row['id']])!=0){
                                            echo count((array)$purchase_orders[$row['id']]);
                                        }else{ echo count((array)$already_trasnaction[$row['id']]);
} ?>

                                    </span>
                                </td>

                                </tr>
                                <?php $i++;
} ?>
                            </tbody>
                        </table>
                    <?php }else{ ?>
                        <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php     }?>
                </div>



<?php if(isset($_POST['submit'])){  ?>
    <!--------------  data table   --------------->
    <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script src="assets/pages/data-table/js/jszip.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
    <?php } ?>
