<?php
if($_GET['pageajax']=='1'){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
}
$page_details= toGetTableNameBaseApplicationPagesList('view_page_name', $_GET['option']);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$page_details['application_table_name'];
$page_response = callAPI($pageurl);
$list = json_decode($page_response,1);
$content = $list['help_text'] != '' ? $list['help_text'] : "Help Text";
$title = $list['page_title'];
$add_url = $page_details['List_Page_Name'];
$view_url = $page_details['List_Page_Name'];
$table_name = $list['application_table_name'];
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$url = API."?mode=togetdatabasepaimairy&rid=$rid&table=".$table_name;
$response = callAPI($url);
$role = json_decode($response,1);
$title = viewTitle($_GET['option']);

$lists = json_decode($list['list_attributes'],1);
$dis= array();
foreach ($lists as $key => $value) {
    if($value['insert_display_list']== 1 ){
        $dis [$key]['lable'] = $value['lable'];
        $dis [$key]['type'] = $value['type'];
        $dis [$key]['width'] = (isset($value['insert_view_width']) && $value['insert_view_width']!='') ? $value['insert_view_width']: '6';
    }
}
?>
<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <!-- Page-body start -->
            <div class="page-body">
                <div class="alert alert-light alert-dismissible">
                    <strong><?php echo $content; ?></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header table-card-header">
                                <h5><?php echo $title;  ?></h5>
                                <div class="card-header-right table-card-header ">

                                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                        <i class="icofont icofont-reply"></i>
                                        Back
                                    </a>
                                </div>
                            </div>
                            <div class="card-custom <?php echo $card_sticky; ?>" id="kt_page_sticky_card">
                                <div class="card-body">
                                    <div class="row">
                                        <?php  $trr=0;
                                        $two = $one = "";
                                        $filed_array = array('checkbox', 'switch', 'radio' , 'select', 'multi_select');
                                        foreach($dis as $key => $fullvalue){
                                            $lable =  ucwords($fullvalue['lable']);
                                            $value =  todisplayviewfiled($role[$key],$fullvalue['type']);
                                            if(in_array($fullvalue['type'], $filed_array)){
                                                $selectdata = dyanamicselectdata($table_name,$key);
                                                $value = todisplayviewfiled($selectdata[$value],$fullvalue['type']);
                                            }
                                            if( $key =='current_status'){ $lable= "ID / ". $lable;  $value = $value.'/'.$stausresult[$value]['name'];
}
                                            $class = "form-group col-lg-6"; ?>

                                        <?php
                                                if($i%2==0){
                                                    $one.= "<tr>
                                                        <th scope='row'>".$lable."
                                                        </th>
                                                        <td>".$value." </td>
                                                    </tr>";
                                                }else{
                                                    $two.= "<tr>
                                                        <th scope='row'>".$lable."
                                                        </th>
                                                        <td> ".$value."</td>
                                                    </tr>";
                                                }

                                        ?>

                                    <?php $i++;
} ?>
                                    <div class="col-lg-12 col-xl-6 mb-3">
                                        <div class="table-responsive">
                                            <table class="table m-0"><thead><tr class="visually-hidden"><th scope="col">Data</th></tr></thead>
                                                <tbody>

                                                    <?php echo $one; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-xl-6">
                                        <div class="table-responsive">
                                            <table class="table m-0"><thead><tr class="visually-hidden"><th scope="col">Data</th></tr></thead>
                                                <tbody>

                                                    <?php echo $two; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
