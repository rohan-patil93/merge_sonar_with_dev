
<?php
$filterdata = "";
$empid_filter = "";
if($_GET['EID']!=""){
    $eids =  encodeId($_GET['EID']);
}
$type_array = array('select','multi_select','disable_select','radio','checklist');
$cur_year = date('Y');
$cur_month = date('m');
$vurl = API."?mode=getempreport&table=employees&company_id=".$_SESSION['company_id']."&is_active='0'&filterby=".$filterdata."&empidlist=".$eids;
$hcdetails = callAPI($vurl);
$headcount_data = json_decode($hcdetails,1);
$emp_data1 = $headcount_data['emp_data'];
$employeeslist = array_keys($emp_data1);
$vurl = API."?mode=getbulkchangecategories&empidlist=".implode(',',(array)$employeeslist);
$bulkhcdetails = callAPI($vurl);
$bulk_data = json_decode($bulkhcdetails,1);
$emp_details = $bulk_data['emp_details'];
$employee_data = $bulk_data['employee_data'];
$employees_fieldlist = $bulk_data['employees_fieldlist'];
$defalut_fields = array('location_id','joining_date','designation_id','department_id','reporting_manager_id');

if(isset($_POST['applyform'])) {
    if($_GET['EID']!=""){
    $eids =  encodeId($_GET['EID']);
    }
    $defalut_fields = $_POST['displayattributes'];
    if(count((array)$defalut_fields) == 0) {
        $defalut_fields = array('location_id','joining_date','designation_id','department_id','reporting_manager_id');
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
    $vurl = API."?mode=getempreport&table=employees&company_id=".$_SESSION['company_id']."&is_active='0'&filterby=".$filterdata."&empidlist=".$eids;
    $hcdetails = callAPI($vurl);
    $headcount_data = json_decode($hcdetails,1);
    $emp_data1 = $headcount_data['emp_data'];
    $employeeslist = array_keys($emp_data1);
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
<div class="d-flex flex-column-fluid1">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" >
                    <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>" >
                    <div class="card card-custom  " id="kt_page_sticky_card">
                        <div class="card-header mt-5">

                            <h3 class="card-label">Employee Details</h3>
                            <div class="col-lg-3">
                                <button type="button" class="btn btn-outline-secondary" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Filter</button>
                            </div>
                            <div class="col-lg-3">
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

                        </div>

                        <!--end::Charts Widget 2-->
                    </div>
                </form>
            </div>
        </div>

        <!--begin::Main-->
        <!--begin::Card-->
        <div class="card">
            <div class="card-toolbar">
                                            <!--begin::Dropdown-->
                                            <div class="dropdown dropdown-inline mr-2 float-right p-5">
                                                <button type="button" class="btn btn-light-primary font-weight-bolder dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <span class="svg-icon svg-icon-md">

                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path d="M3,16 L5,16 C5.55228475,16 6,15.5522847 6,15 C6,14.4477153 5.55228475,14 5,14 L3,14 L3,12 L5,12 C5.55228475,12 6,11.5522847 6,11 C6,10.4477153 5.55228475,10 5,10 L3,10 L3,8 L5,8 C5.55228475,8 6,7.55228475 6,7 C6,6.44771525 5.55228475,6 5,6 L3,6 L3,4 C3,3.44771525 3.44771525,3 4,3 L10,3 C10.5522847,3 11,3.44771525 11,4 L11,19 C11,19.5522847 10.5522847,20 10,20 L4,20 C3.44771525,20 3,19.5522847 3,19 L3,16 Z" fill="#000000" opacity="0.3" />
                                                            <path d="M16,3 L19,3 C20.1045695,3 21,3.8954305 21,5 L21,15.2485298 C21,15.7329761 20.8241635,16.200956 20.5051534,16.565539 L17.8762883,19.5699562 C17.6944473,19.7777745 17.378566,19.7988332 17.1707477,19.6169922 C17.1540423,19.602375 17.1383289,19.5866616 17.1237117,19.5699562 L14.4948466,16.565539 C14.1758365,16.200956 14,15.7329761 14,15.2485298 L14,5 C14,3.8954305 14.8954305,3 16,3 Z" fill="#000000" />
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>Export</button>
                                                <!--begin::Dropdown Menu-->
                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                                    <!--begin::Navigation-->
                                                    <ul class="navi flex-column navi-hover py-2">
                                                        <li class="navi-header font-weight-bolder text-uppercase font-size-sm text-primary pb-2">Choose an option:</li>
                                                        <li class="navi-item">
                                                            <a href="#" class="navi-link">
                                                                <span class="navi-icon">
                                                                    <i class="la la-print"></i>
                                                                </span>
                                                                <span class="navi-text">Print</span>
                                                            </a>
                                                        </li>
                                                        <li class="navi-item">
                                                            <a href="#" class="navi-link">
                                                                <span class="navi-icon">
                                                                    <i class="la la-copy"></i>
                                                                </span>
                                                                <span class="navi-text">Copy</span>
                                                            </a>
                                                        </li>
                                                        <li class="navi-item">
                                                            <a href="#" class="navi-link">
                                                                <span class="navi-icon">
                                                                    <i class="la la-file-excel-o"></i>
                                                                </span>
                                                                <span class="navi-text">Excel</span>
                                                            </a>
                                                        </li>
                                                        <li class="navi-item">
                                                            <a href="#" class="navi-link">
                                                                <span class="navi-icon">
                                                                    <i class="la la-file-text-o"></i>
                                                                </span>
                                                                <span class="navi-text">CSV</span>
                                                            </a>
                                                        </li>
                                                        <li class="navi-item">
                                                            <a href="#" class="navi-link">
                                                                <span class="navi-icon">
                                                                    <i class="la la-file-pdf-o"></i>
                                                                </span>
                                                                <span class="navi-text">PDF</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                    <!--end::Navigation-->
                                                </div>
                                                <!--end::Dropdown Menu-->
                                            </div>
                                            <!--end::Dropdown-->


                                        </div>
            <div class="card-body">
                <!--begin: Datatable-->
                <table class="table table-separate table-head-custom table-checkable" id="kt_datatable1">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 25%;">NAME </th>
                            <?php

                             foreach ($employees_fieldlist as $empkey => $empfield) {

                                if(in_array($empkey, $defalut_fields))  {
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
                                $serialno++;
                                ?>
                                <tr>
                                    <td> <span >
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

                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <!--end: Datatable-->
                    </div>
                </div>
            </div>
        </div>

        <!--end::Card-->

        <script src="assets/js/pages/features/charts/apexcharts.js"></script>

        <div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
                <div class="modal-content"  id="popup2">

                </div>
            </div>
        </div>


        <script src="assets/plugins/custom/datatables/datatables.bundle.js"></script>
        <!--end::Page Vendors-->

        <script src="assets/js/pages/myscrpit/scrollable_datatable.js"></script>

