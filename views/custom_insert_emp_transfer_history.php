<?php
$filterdata = "";
$monthlist = array('1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec');
$type_array = array('select','multi_select','disable_select','radio','checklist');
$cur_year = date('Y');
$cur_month = date('m');
$vurl = API."?mode=getheadcountreport&table=employees&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
$hcdetails = callAPI($vurl);
$headcount_data = json_decode($hcdetails,1);
$curyear_monthwise_headcount = $headcount_data['curyear_monthwise_headcount'];
$prevyear_monthwise_headcount = $headcount_data['prevyear_monthwise_headcount'];
$Total_Head_Count = $headcount_data['Total_Head_Count'];
$Total_Head_Count_CurrentYear = $headcount_data['Total_Head_Count_CurrentYear'];
$Total_Head_Count_PrevYear = $headcount_data['Total_Head_Count_PrevYear'];

$yearslist = $headcount_data['years'];
$years = implode('|', (array)$yearslist);
$prev_year_monthly_headcount = implode('|', (array)$prevyear_monthwise_headcount);
$cur_year_monthly_headcount =implode('|', (array)$curyear_monthwise_headcount);
$empidlist = $headcount_data['selected_employees'];
$employeeslist = array_keys($empidlist);
$employment_categories = $headcount_data['employment_categories'];
$designations = $headcount_data['designations'];
$departments = $headcount_data['departments'];
$locations = $headcount_data['locations'];

$vurl = API."?mode=getbulkchangecategories&empidlist=".implode(',',(array)$employeeslist);
$bulkhcdetails = callAPI($vurl);
$bulk_data = json_decode($bulkhcdetails,1);
$bulkchangecategory = $bulk_data['bulkchange_categories'];
$emp_details = $bulk_data['emp_details'];
$emp_history = $bulk_data['emp_history'];
$employee_data = $bulk_data['employee_data'];
$employees_fieldlist = $bulk_data['employees_fieldlist'];

$defalut_fields = array('emp_code','designation_id','department_id','location_id','employment_category_id');
if(isset($_POST['applyform'])) {
    $defalut_fields = $_POST['displayattributes'];
    if(count((array)$defalut_fields) == 0) {
        $defalut_fields = array('emp_code','designation_id','department_id','location_id','employment_category_id');
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
    $vurl = API."?mode=getheadcountreport&table=employees&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata;
    $hcdetails = callAPI($vurl);
    $headcount_data = json_decode($hcdetails,1);
    $curyear_monthwise_headcount = $headcount_data['curyear_monthwise_headcount'];
    $prevyear_monthwise_headcount = $headcount_data['prevyear_monthwise_headcount'];
    $Total_Head_Count = $headcount_data['Total_Head_Count'];
    $Total_Head_Count_CurrentYear = $headcount_data['Total_Head_Count_CurrentYear'];
    $Total_Head_Count_PrevYear = $headcount_data['Total_Head_Count_PrevYear'];
    $yearslist = $headcount_data['years'];
    $years = implode('|', (array)$yearslist);
    $prev_year_monthly_headcount = implode('|', (array)$prevyear_monthwise_headcount);
    $cur_year_monthly_headcount =implode('|', (array)$curyear_monthwise_headcount);
    $empidlist = $headcount_data['selected_employees'];
    $employeeslist = array_keys($empidlist);
    $employment_categories = $headcount_data['employment_categories'];
    $designations = $headcount_data['designations'];
    $departments = $headcount_data['departments'];
    $locations = $headcount_data['locations'];

    $vurl = API."?mode=getbulkchangecategories&empidlist=".implode(',',(array)$employeeslist);
    $bulkhcdetails = callAPI($vurl);
    $bulk_data = json_decode($bulkhcdetails,1);
    $bulkchangecategory = $bulk_data['bulkchange_categories'];
    $emp_details = $bulk_data['emp_details'];
    $emp_history = $bulk_data['emp_history'];
    $employee_data = $bulk_data['employee_data'];
    $employees_fieldlist = $bulk_data['employees_fieldlist'];


}
foreach ($defalut_fields as $dfield) {
    if(in_array($employees_fieldlist[$dfield]['type'], (array)$type_array)){
        $field[$dfield] =  dyanamicselectdata("employees",$dfield);
    }
}
?>
<script type="text/javascript">

    function selectempdata(value) {
        var count =0;
        var bulkemplist =  document.getElementsByName("bulkemplist[]");
        for (var i = 0; i < bulkemplist.length; i++) {
            if(bulkemplist[i].checked){
                count++;
            }
        }
        if(count > 0) {
            document.getElementById('bulkcategory').style.display = "block";
            $('.hidedive').show();
        }else {
            $('.hidedive').css("display","none");
            document.getElementById('bulkcategory').style.display = "none";
            document.getElementById('effectdate').style.display = "none";
            document.getElementById('previewbtn').style.display = "none";
            document.getElementById('application_tablename_filter').style.display = "none";
            document.getElementById('hidedive').style.display = "none";
        }
    }
    function showpopup(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        jQuery.ajax({
            url: "views/custom_filter_bulktransfer.php",
            data:'filter_id='+filter_id,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal('show');
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
                $('#kt_modal_KTDatatable_remote1').modal('show');
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
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
            }
        });

    }

    function getnamelist(emplist){
        console.log(emplist);
        document.getElementById('filterby').value = emplist;
        $('#kt_modal_dynamic_popup').modal('hide');
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
                            <h5>Employees Bulk Transfer</h5>
                            <div class="row mt-4">
                                <div class="col-lg-2 text-right"><span>Month</span></div>
                                <div class="col-lg-2">
                                    <input aria-label="Month Year Cal" class="form-control" type="month" name="month_year_cal" value="<?php echo $cur_year."-".$cur_month?>"  id="att_month_year" />
                                </div>
                                <div class="col-lg-1">
                                    <button type="button" class="btn btn-outline-secondary" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Filter</button>
                                </div>

                                <div class="col-lg-4">
                                    <select aria-label="Displayattributes[]" class="form-control selectpicker form-select js-example-basic-multiple-limit_custom" name="displayattributes[]" multiple="multiple">
                                        <?php $jd=0; foreach ($employees_fieldlist as $fkey => $fieldvalue) { if(in_array($fkey, (array)$defalut_fields)){ $jd++;
}  ?>
                                            <option value="<?php echo $fkey; ?>" <?php echo in_array($fkey, (array)$defalut_fields) ? 'selected' : ''; ?>  <?php echo $jd==1?'locked="locked"':''; ?>> <?php echo $fieldvalue['lable']; ?> </option>
                                        <?php }    ?>

                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <input type="hidden" id="filterby" name="filterby">
                                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-t-5"  name="applyform" value="apply">Apply</button>
                                </div>
                            </div>
                        </div>
                        <!--end::Charts Widget 2-->
                    </div>

                </div>
            </div>
        </form>

        <div class="card card-custom col-lg-12 hidedive" style="display: none;">
            <div class="row p-4">
                <div class="form-group col-lg-3 mt-10 mb-10"  id="bulkcategory" style="display: none;">
                    <span class="mb-2">Transfer Category</span></br>
                    <select aria-label="Bulkchangecategory" onchange="showbulkchangefilter(this.value);" id="bulkchangecategory" name="bulkchangecategory" class="form-control form-select js-example-basic-single mt-2" >
                        <?php
                        foreach ($bulkchangecategory as $filter) {
                            ?>
                            <option <?php if($selectedoption==$filter['application_tablename']) {?> selected <?php
} ?> value="<?php echo $filter['application_tablename']."|".$filter['application_table_fieldname'].'|'.$filter['id'].'|'.$filter['update_employees_fieldname'].'|'.$filter['insert_history_tablename'];?>"><?php echo $filter['category_name'];?></option>
                        <?php } ?>
                    </select></br>
                    <span class="form-text text-muted">Select Bulk Transfer Category</span>
                </div>
                <div class="col-lg-3 mt-10 mb-10" id="application_tablename_filter">

                </div>
                <div class="col-lg-3 mt-10 mb-10" id="effectdate" style="display: none">
                    <span>Effective From</span></br>
                    <input aria-label="Effective Date" type="date" class="form-control mt-2"  id="kt_datepicker_4_1"  name="effective_date" style="height: 45px;"/>
                    <span class="form-text text-muted">Enter Effective From</span>
                </div>
                <div class="col-lg-2 mt-10 mb-10">
                    <input type="hidden" id="application_tablename">
                    <input type="hidden" id="application_table_fieldname">
                    <input type="hidden" id="update_employees_fieldname">
                    <input type="hidden" id="insert_history_tablename">
                    <input type="hidden" id="bulkmove_empids">
                    <input type="hidden" id="bulkchange_categoryid">
                    <input type="hidden" id="bulkmove_toid">
                    <button id="previewbtn"  style="display: none" class="btn btn-primary btn-shadow-hover font-weight-bold m-t-30" onclick="bulkconfirmpopup()">Preview</button>

                </div>

            </div>
        </div>

        <div class="card card-body dataTables_wrapper">

            <!--begin: Datatable -->
            <table class="table table-separate table-head-custom table-checkable dataTable" id="kt_datatable1">

                    <thead>
                        <tr>
                            <th scope="col" style="width: 5%;">&nbsp;&nbsp;&nbsp;&nbsp;<i class="flaticon-list-3"> </i> </th>
                            <th scope="col" style="width: 15%;">NAME </th>

                            <?php foreach ($employees_fieldlist as $empkey => $empfield) {
                                if(in_array($empkey, (array)$defalut_fields))  {
                                    ?>
                                    <th scope="col">
                                        <?php echo $empfield['lable']; ?> </th>
                                    <?php } ?>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $serialno = 0;
                                foreach ($employee_data as $empidkey => $empvalue) {
                                    ?>
                                    <tr>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;

                                            <div class="checkbox-fade fade-in-primary">
                                                <label class="form-label">
                                                    <input aria-label="Bulkemplist[]" type="checkbox" name="bulkemplist[]" id="bulkemplist" onchange="selectempdata(this.value);"  value="<?php echo $empvalue['id']; ?>">
                                                    <span class="cr">
                                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                    </span>

                                                </label>
                                            </div>
                                        </td>
                                        <td style="width: 15%;">
                                            <?php if($empvalue['image']!="") { ?>
                                                <img src="uploads/all/<?php echo $empvalue['image']; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($empvalue['display_name']); ?></span>
                                            <?php } ?>
                                            <?php echo $empvalue['display_name']; ?>
                                        </td>

                                        <?php

                                        foreach ($employees_fieldlist as $empkey => $empfield) {
                                            if(in_array($empkey, (array)$defalut_fields))  { ?>
                                                <td>
                                                    <?php if(in_array($empfield['type'], (array)$type_array)){
                                                        echo $field[$empkey][$empvalue[$empkey]];
                                                    }else {
                                                        echo $empvalue[$empkey];
                                                    } ?>
                                                </td>
                                            <?php } ?>
                                            <?php } ?>
                                        </tr>

                                    <?php } ?>
                                </tbody>
                            </table>
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
