<?php
if (isset($_POST['submit'])){
    print_r($_POST);
    if($_POST['submit'] == 'search'){
        $array =  array('category_id', 'vendor_id', 'cost_center_id', 'location_id');
        foreach ($array as  $value) {
            if($_POST[$value] != ''){
                $ress [$value] = $_POST[$value];
            }
        }
        $data1["where"] = $ress;
        $data = urlencode(json_encode($data1));
        $tableurl = API."?mode=serchforpos&method=post";
        $response = file_post_contents($tableurl, $_POST, '');
        $data = json_decode($response,1);
    }
}

$tablecat = API."?mode=gettabledetails&table=".TBL_ITEM_CATEGORIES;
$rescat = callAPI($tablecat);
$datacat = json_decode($rescat,1);
$tablecost = API."?mode=gettabledetails&table=".TBL_COST_CENTERS;
$rescost = callAPI($tablecost);
$datacost = json_decode($rescost,1);
$tablelocation = API."?mode=gettabledetails&table=".TBL_LOCATIONS;
$reslocation = callAPI($tablelocation);
$datalocation = json_decode($reslocation,1);
$tablevendor = API."?mode=gettabledetails&table=".TBL_VENDOR;
$resvendor = callAPI($tablevendor);
$datavendor = json_decode($resvendor,1);
$content = $data['help_text']!='' ? $data['help_text'] : "Help Test";
$table_name = $_GET['table'];
$rr_data = toGetpageOptionName($table_name);
$pr_data = toGetpageOptionName($rr_data['genrate_next_level']);
$pageurl = API."?mode=displayattributesview&fid=application_table_name&view=".$table_name;
$page_response = callAPI($pageurl);
$dis_list = json_decode($page_response,1);
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
    $get_table =$return_data['content_details'];
    echo $get_table;
  $stausresult = ViewStatusData();

?>
<script type="text/javascript">
        $(document).on('click', '#checkall1', function() {
            alert('haii');

    });
        function chackd(e){
            alert(check);
            var checkboxes = document.getelementbyid('check');
            if (e.checked) {
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = true;
                }
            } else {
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = false;
                }
            }
        }

</script>
    <div class="d-flex flex-column-fluid">
        <div class="container-fluid">
            <div class=" flex-row j-content">
                <?php if($content !=''){ ?>

                    <div class="alert alert-warning alert-dismissible">
                        <strong><?php echo $content; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">

                    <div class="card-header">
                        <h5><?php echo ucwords($_GET['table']) ?></h5>
                        <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>

                    <div class="card card-custom">
                        <div class="card-body  j-pro">
                            <form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post">
                                <div class="row kt-margin-b-20">
                                    <div class="col-lg-2 kt-margin-b-10-tablet-and-mobile">
                                        <span  class="form-label"><?php echo $cus_list['lable']; ?>:</span>
                                        <select aria-label="Vendor" class="form-control form-select js-example-basic-single" id="kt_select2_41" name="vendor_id">
                                            <option value=""> All <?php echo $cus_list['lable']; ?> </option>
                                                <?php foreach ($selectdata as $selectkey => $selectvalue) {  ?>
                                                    <option  <?php if($ress['vendor_id'] == $selectkey  ){ ?> selected <?php
} ?> value="<?php echo $selectkey; ?>"><?php echo $selectvalue; ?></option>
                                                <?php  } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 kt-margin-b-10-tablet-and-mobile">
                                        <span  class="form-label"><?php echo "Item Category"; ?>:</span>
                                        <select aria-label="Category" class="form-control form-select js-example-basic-single" id="kt_select2_42" name="category_id">
                                            <option value=""> All Categories </option>
                                            <?php foreach($datacat as $selectcat){ ?>
                                                <option  <?php if($ress['category_id'] == $selectcat['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectcat['id']; ?>"><?php echo $selectcat['category_description']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-2 kt-margin-b-10-tablet-and-mobile">
                                        <span  class="form-label"><?php echo "Cost Center"; ?>:</span>
                                        <select aria-label="Cost Center" class="form-control form-select js-example-basic-single" id="kt_select2_43" name="cost_center_id">
                                            <option value=""> All Cost Center </option>
                                            <?php foreach($datacost as $selectcost){ ?>
                                                <option   <?php if($ress['cost_center_id'] == $selectcost['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectcost['id']; ?>"><?php echo $selectcost['cost_center_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-4 kt-margin-b-10-tablet-and-mobile">

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <span class="form-label">Start Date</span>
                                                <input aria-label="From" type="date" class="form-control kt-input" name="from" placeholder="From" data-col-index="5" />
                                            </div><div class="col-lg-6">
                                                <span class="form-label">End Date</span>
                                                <input aria-label="To" type="date" class="form-control kt-input" name="to" placeholder="To" data-col-index="5" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="get_table" value="<?php echo $table_name; ?>">
                                <?php $csheck = $cus_list['lable']=="vendor" ?"vendors":"application_users"; ?>
                                <input type="hidden" name="get_display_table" value="<?php echo $csheck; ?>">
                                <div class="row mt-2 mb-2">
                                    <div class="col-lg-12">
                                        <input type="hidden" name="submit" value="search" />
                                        <button class="btn btn-primary btn-brand--icon" id="kt_search">
                                            <span>
                                                <i class="la la-search"></i>
                                                <span>Search</span>
                                            </span>
                                        </button>
                                        &nbsp;&nbsp;
                                        <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                                            <span>
                                                <i class="la la-close"></i>
                                                <span>Reset</span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <div>
                                <!--begin: Datatable -->
                                <div class="dt-responsive table-responsive">
                                    <table class="table table-striped- table-bordered table-hover table-checkable" style="margin-top: 15px;" id="kt_table_11">
                                        <thead>
                                            <tr>
                                                <th scope=\"col\">ID</th>
                                                <th scope=\"col\"><?php echo $cus_list['lable']; ?></th>
                                                <th scope=\"col\">Transaction Number</th>
                                                <th scope=\"col\">Transaction Date</th>
                                                <th scope=\"col\">Total</th>
                                                <th scope=\"col\">Status</th>
                                                <th scope=\"col\">Genrate <?php echo $rr_data['genrate_next_level']; ?> </th>
                                                <th scope=\"col\">View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=0; foreach ($data as $row){
                                                $rid = $row['id'];
                                                $send = "id:".$rid;
                                                $view =  "index.php?option=".$pr_data['option_url']."&table=".$table_name ."&Rid=".$rid;
                                                $only_view =  "index.php?option=".$rr_data['option_url']."&Rid=".deocdeId($send);
                                                ?>
                                                <tr>
                                                    <td><?php echo $row['id']; ?></td>
                                                    <td><?php echo $row['vendor_name']; ?></td>
                                                    <td><?php echo $row['transaction_number']; ?></td>
                                                    <td><?php echo fromDate($row['transaction_date']); ?></td>
                                                    <td><?php echo customeummarmonyformate($row['total_amount']); ?></td>
                                                    <td><span class="form-label badge badge-inverse-primary" style="background-color: <?php echo '#'.$stausresult[$row['current_status']]['background_color'];?> color:#4f2424" >Approved</span></td>
                                                    <td>
                                                        <a  target="_blank" href="<?php echo $view; ?>" >
                                                            <button class="btn btn-primary btn-brand--icon" id="kt_search_2">
                                                                <span>Genrate <?php echo $rr_data['genrate_next_level']; ?></span>
                                                            </button></a>
                                                        </td>
                                                    <td>
                                                        <a  target="_blank" href="<?php echo $view; ?>" >
                                                            <button class="btn btn-primary btn-brand--icon" id="kt_search_3">
                                                                <span>Genrate <?php echo $rr_data['genrate_next_level']; ?></span>
                                                            </button></a>
                                                        </td>
                                                    </td>
                                                    </tr>
                                                    <?php $i++;
} ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
