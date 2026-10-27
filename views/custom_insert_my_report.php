<?php
$rid = encodeId($_GET['Rid']);
$reporturl = API."?mode=singlerowtabledata&rid=".$rid."&table=my_reports";
$report_lists = callAPI($reporturl);
$report_lists = json_decode($report_lists,1);
$page_title = $report_lists['page_name'];
$report_list = json_decode($report_lists['report_list'],1);
print_r($report_list);

$table = $report_lists['application_table_name'];

$vdata1["where"] = array('application_table_name'=> $report_lists['application_table_name']);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=display_attributes&data=".$vdata;
$page_response = callAPI($vurl);
$list = json_decode($page_response,1);
$list = $list['0'];
$lists = json_decode($list['list_attributes'],1);
$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
$whrcon = array();
$whrcon["where"] = array('company_id'=> $_SESSION['company_id']);
foreach ($report_list as $key => $value){
    if ($value['display'] ==1 ) {
        $dis[$value['Priority']] ['name'] = $value['feildname'];
        $dis[$value['Priority']] ['lable'] = $value['lable'];
        $dis[$value['Priority']] ['heddingpositiontable'] = $value['heddingpositiontable'];
        $dis[$value['Priority']] ['add_custom_field'] = $value['add_custom_field'];
        $key_lists[] = $value['feildname'];
        if(in_array($lists[ $value['feildname']]['type'], (array)$type_array)){
            $field[$key] =  dyanamicselectdata($table,$key);
        }

    }
}
ksort($dis);

$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=".$table."&data=".$vdata;
$response = callAPI($url);
$bills = json_decode($response,1);
print_r($bills); exit;
$stausresult = ViewStatusData();
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="card-label">&nbsp;<?php echo $page_title;  ?> </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="card-toolbar">
                        <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            Back
                        </a>
                    </div>
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
                                                <?php     $date_array = array('status_date','from_date','to_date','created_date','updated_date');
                                                $status_array = array('is_active','status_name');
                                                $user_names = array('created_by','updated_by');

                                                foreach ($dis as $header) {
                                                    $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                                                    if(in_array($header['type'], (array)$type_array)){
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

                                                        <?php    }  elseif (in_array($header['name'], (array)$status_array)) { ?>
                                                            <td>
                                                                <?php $bill[$header['name']] = ($bill[$header['name']]== null || $bill[$header['name']]== "") ? '1' :  $bill[$header['name']] ; ?>
                                                                <span  class="label label-lg label-light-primary label-inline font-weight-bold py-4"><?php echo $stausresult[$bill[$header['name']]]['name'];?></span>
                                                            </td>
                                                        <?php } elseif( $header['name'] == 'current_status') { ?>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button onclick="displaystausdrop(<?php echo $bill['id']; ?>,<?php echo "'".$table."'"; ?>,<?php echo $bill['current_status']; ?>)" class="btn btn-primary dropdown-toggle" style="color: <?php echo "#".$stausresult[$bill[$header['name']]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$bill[$header['name']]]['background_color']); ?>;" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$bill[$header['name']]]['name']; ?> </button>
                                                                    <div class="dropdown-menu" id="dropdownlist_<?php echo $bill['id']; ?>">

                                                                    </div>
                                                                </div>
                                                            </td>

                                                        <?php     }elseif (in_array($header['name'], (array)$user_names)) { ?>
                                                            <td><?php echo $_SESSION['users'][$bill[$header['name']]]; ?></td>
                                                        <?php } elseif(in_array($header['name'], (array)$date_array)){?>
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
                                        <?php }else{ ?>

                                            <div class="text-center px-4">
                                                <img class="mw-100 mh-300px" alt="" src="assets/media/misc/work.png" style="max-height: 300px!important;">
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <input type="hidden" name="table_name" id="table_name" value="<?php echo $table; ?>">
                            </div>
                        </div>
                    </div>
