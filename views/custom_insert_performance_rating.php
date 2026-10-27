<?php
$filterdata = "";
$type_array = array('select','multi_select','disable_select','radio','checklist');
$cur_year = date('Y');
$cur_month = date('m');
$vurl = API."?mode=getperformanceassessment&table=performance_setup&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
$hcdetails = callAPI($vurl);
$headcount_data = json_decode($hcdetails,1);
$rating = $headcount_data['rating'];
$employeeslist = array_keys($rating);

$vurl = API."?mode=getbulkchangecategories&empidlist=".implode(',',(array)$employeeslist);
$bulkhcdetails = callAPI($vurl);
$bulk_data = json_decode($bulkhcdetails,1);
$emp_details = $bulk_data['emp_details'];
$employee_data = $bulk_data['employee_data'];
$employees_fieldlist = $bulk_data['employees_fieldlist'];
$defalut_fields = array('reporting_manager_id');

if(isset($_POST['applyform'])) {
    $defalut_fields = $_POST['displayattributes'];
    if(count((array)$defalut_fields) == 0) {
    $defalut_fields = array('reporting_manager_id');
    }
    $filtertbl_rowid = $_POST['filtertbl_rowid'];
    $monyr = explode('-',$_POST['month_year_cal']);
    $cur_year = $monyr[0];
    $cur_month = $monyr[1];
    if($_POST['filterby']=='All Employees'){
        $filterdata = "";
    }else{
        $filterby = json_decode($_POST['filterby'],1);
        unset($filterby['matches_count']);
        unset($filterby['matches_names']);
        $filterdata = urlencode(json_encode($filterby));
    }
 $vurl = API."?mode=getperformanceassessment&table=performance_setup&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
    $hcdetails = callAPI($vurl);
$headcount_data = json_decode($hcdetails,1);
$rating = $headcount_data['rating'];
$employeeslist = array_keys($rating);

$vurl = API."?mode=getbulkchangecategories&empidlist=".implode(',',(array)$employeeslist);
    $bulkhcdetails = callAPI($vurl);
    $bulk_data = json_decode($bulkhcdetails,1);
    $emp_details = $bulk_data['emp_details'];
    $employee_data = $bulk_data['employee_data'];
    $employees_fieldlist = $bulk_data['employees_fieldlist'];
}

foreach ($defalut_fields as $dfield) {
        if(in_array($employees_fieldlist[$dfield]['type'], $type_array)){
            $field[$dfield] =  dyanamicselectdata("employees",$dfield);
        }
    }

    if(isset($_POST['submit_rating'])){
        foreach($_POST['rowid'] as $index => $row){
            $row_id = $row;
            $postdata['manager_rating'] = $_POST['manager_rating'][$index];
            $postdata['final_rating'] = $_POST['final_rating'][$index];
            $postdata['remarks'] = $_POST['remarks'][$index];
            $postdata['updated_date'] = date('Y-m-d');
              $postdata['updated_by'] = $_SESSION['userid'];
              $url = API."?mode=updaterowtabledata&rid=".$row_id."&table=".TBL_PERFORMANCE_RESPONSES."&method=post";
              $response = file_post_contents($url, $postdata, 'update');
        }
    }
?>
<script type="text/javascript">

    function showpopup(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        jQuery.ajax({
            url: "views/custom_filter_bulktransfer.php",
            data:'filter_id='+filter_id,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal({ show: true});
            },
            error:function (){}
        }).done(function(){
        });
    }

    function selected_bulk_category(categoryid){
        var bulkmove_empids = [];
        var bulkemplist =  document.getElementsByName("bulkemplist[]");
        for (var i = 0; i < bulkemplist.length; i++) {
            if(bulkemplist[i].checked){
                bulkmove_empids.push(bulkemplist[i].value);
            }
        }
        document.getElementById('bulkmove_empids').value = bulkmove_empids.toString();
        document.getElementById('bulkmove_toid').value = categoryid;
        document.getElementById('effectdate').style.display = "block";
        document.getElementById('previewbtn').style.display = "block";
    }

    function bulkconfirmpopup(){
        var bulkmove_empids = document.getElementById('bulkmove_empids').value;
        var application_tablename = document.getElementById('application_tablename').value;
        var application_table_fieldname = document.getElementById('application_table_fieldname').value;
        var update_employees_fieldname = document.getElementById('update_employees_fieldname').value;
        var insert_history_tablename = document.getElementById('insert_history_tablename').value;

        var bulkchange_categoryid = document.getElementById('bulkchange_categoryid').value;
        var bulkmove_toid = document.getElementById('bulkmove_toid').value;
        var effective_date = document.getElementById('kt_datepicker_4_1').value;
        var pass_str = 'empids='+bulkmove_empids+'&application_tablename='+application_tablename+'&effective_date='+effective_date+'&application_table_fieldname='+application_table_fieldname+'&bulkchange_categoryid='+bulkchange_categoryid+'&update_employees_fieldname='+update_employees_fieldname+'&bulkmove_toid='+bulkmove_toid+'&insert_history_tablename='+insert_history_tablename;
        console.log(pass_str);
        jQuery.ajax({
            url: "views/custom_view_emp_bulktransfer_popup.php",
            data: pass_str,
            type: "GET",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });
    }

    function showbulkchangefilter(bulkcategory_tablefields){

        var bulkcategory_tablefields = bulkcategory_tablefields.split('|');
        var application_tablename = bulkcategory_tablefields[0];
        var application_table_fieldname = bulkcategory_tablefields[1];
        var bulkchange_categoryid = bulkcategory_tablefields[2];
        var update_employees_fieldname = bulkcategory_tablefields[3];
        var insert_history_tablename = bulkcategory_tablefields[4];


        document.getElementById('bulkchange_categoryid').value = bulkchange_categoryid;
        document.getElementById('application_tablename').value = application_tablename;
        document.getElementById('application_table_fieldname').value = application_table_fieldname;
        document.getElementById('update_employees_fieldname').value = update_employees_fieldname;
        document.getElementById('insert_history_tablename').value = insert_history_tablename;

        $.ajax({
            url: "ajax.php",
            data:'page=getapplicationtablevalues&application_tablename='+application_tablename+'&application_table_fieldname='+application_table_fieldname,
            type: "POST",
            success: function(response) {
                console.log("response ="+response);
                document.getElementById('application_tablename_filter').innerHTML = response;

            }
        });

    }

    function getnamelist(emplist){
        document.getElementById('filterby').value = emplist;
        $('#kt_modal_dynamic_popup').modal('hide');
         $.ajax({
         url: "ajax.php",
         data:'page=filternamelist&filterdata='+emplist,
         type: "POST",
         success:function(response){
                console.log("response ="+response);
                document.getElementById('filternames').innerHTML = response;
         },
         error:function (){}
          });
    }

</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <form class="kt-form" id="kt_form" method="post" action="" >
            <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >

            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header">
                            <div class="card-title col-lg-12">
                                <div class="col-lg-3">
                                <h3 class="card-label">Employees Final Rating</h3>
                                </div>
                                <div class="col-lg-3">
                                    <input aria-label="Month Year Cal" class="form-control" type="month" name="month_year_cal" value="<?php echo $cur_year."-".$cur_month?>"  id="att_month_year" />
                                </div>
                                <div class="col-lg-3">
                                    <button type="button" class="btn btn-outline-secondary" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Filter</button>
                                </div>

                                <div class="col-lg-2">
                                    <select aria-label="Displayattributes[]" class="form-control selectpicker" name="displayattributes[]" multiple="multiple">
                                        <?php foreach ($employees_fieldlist as $fkey => $fieldvalue) { ?>
                                            <option value="<?php echo $fkey; ?>" <?php echo in_array($fkey, $defalut_fields) ? 'selected' : ''; ?>  > <?php echo $fieldvalue['lable']; ?> </option>
                                        <?php }    ?>

                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <input type="hidden" id="filterby" name="filterby">
                                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
                                </div>
                                <div id="filternames"> </div>
                            </div>
                        </div>

                        <!--end::Charts Widget 2-->
                    </div>

                </div>
            </div>
        </form>

        <div class="card card-custom col-lg-12">
            <form method="post">
            <!--begin: Datatable -->
            <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">

                    <thead>
                        <tr>
                            <th scope="col" style="width: 5%;">&nbsp;&nbsp;&nbsp;&nbsp;#</th>

                            <th scope="col" style="width: 25%;">NAME </th>

                            <?php foreach ($employees_fieldlist as $empkey => $empfield) {
                            if(in_array($empkey, $defalut_fields))  {
                                ?>
                                <th scope="col">
                                    <?php echo $empfield['lable']; ?> </th>
                                <?php } ?>
                                <?php } ?>
                            <th scope="col" style="width: 10%;"> Manager Rating </th>
                            <th scope="col" style="width: 10%;"> Final Rating </th>
                            <th scope="col" style="width: 15%;"> Remarks </th>

                            </tr>

                        </thead>
                        <tbody>

                            <?php
                            $serialno = 0;
                            foreach ($employee_data as $empidkey => $empvalue) {
                            $serialno++;

                                ?>
                                <tr>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp; <span >
                                        <?php echo $serialno; ?>
                                    </span>  </td>

                                        <td style="width: 15%;">
                                             <?php if($empvalue['image']!="") { ?>
                                            <img src="uploads/all/<?php echo $empvalue['image']; ?>" style="
                                            width: 50px;
                                            height: auto;
                                            border-radius: 50%;
                                            " alt="Attachment Preview">
                                        <?php } else { ?>
                                            <div class="symbol symbol-40 symbol-circle symbol-primary" data-toggle="tooltip" title="" data-original-title="<?php echo $empvalue['display_name']; ?>">
                                                <span class="symbol-label font-size-h5 font-weight-boldest"><?php echo initials($empvalue['display_name']); ?></span>

                                            </div>

                                        <?php } ?>

                                            <?php echo $empvalue['display_name']; ?>
                                            <br><span class="card-label font-weight-bolder  font-size-sm text-muted"><?php echo $empvalue['emp_code']; ?></span>
                                        </td>

                                    <?php

                                    foreach ($employees_fieldlist as $empkey => $empfield) {
                                          if(in_array($empkey, $defalut_fields))  { ?>
                                        <td>
                                            <?php if(in_array($empfield['type'], $type_array)){
                                                echo $field[$empkey][$empvalue[$empkey]];
                                             }else {
                                                  echo $empvalue[$empkey];
                                             } ?>
                                        </td>
                                    <?php } ?>
                                    <?php } ?>

                                    <td> <input type="hidden" name="rowid[]" value="<?php echo $rating[$empvalue['id']]['id']; ?>">  <input aria-label="NOT RATED" type="text" readonly placeholder="NOT RATED" class="form-control" name="manager_rating[]" value="<?php echo $rating[$empvalue['id']]['manager_rating']=="0"?"":$rating[$empvalue['id']]['manager_rating']; ?>" > </td>
                                    <td> <input aria-label="NOT RATED" class="form-control" type="text" name="final_rating[]"  placeholder="NOT RATED" value="<?php echo $rating[$empvalue['id']]['final_rating']=="0"?"":$rating[$empvalue['id']]['final_rating']; ?>" > </td>
                                    <td> <textarea aria-label="Remarks[]" class="form-control" name="remarks[]"><?php echo $rating[$empvalue['id']]['remarks']; ?></textarea>  </td>

                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>


                    <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold ml-5" name="submit_rating">Submit</button>
                                    </div>
                                </div>
                            </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>

<script src="assets/js/pages/features/charts/apexcharts.js"></script>

<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
