<?php

$filterdata = ""; $chosse_list=1;
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

$projecttimesheets = $headcount_data['project_timesheets'];

$jobtitles = $headcount_data['job_titles'];
$costcenter = $headcount_data['costcenter'];
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

    if($_POST['filterby']=='All Employees'){
        $filterdata = "";
    }else{
        $filterby = json_decode($_POST['filterby'],1);
        unset($filterby['matches_count']);
        unset($filterby['matches_names']);
        $filterdata = urlencode(json_encode($filterby));
    }
    $vurl = API."?mode=getheadcountreport&table=employees&month=".$cur_month."&year=".$cur_year."&filterby=".$filterdata."&fromdatetimesheet=".$_POST['fromdatetimesheet']."&todatetimesheet=".$_POST['todatetimesheet'];
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
$projecttimesheets = $headcount_data['project_timesheets'];

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
        if(in_array($employees_fieldlist[$dfield]['type'], $type_array)){
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
        }else {
            document.getElementById('bulkcategory').style.display = "none";
            document.getElementById('effectdate').style.display = "none";
            document.getElementById('previewbtn').style.display = "none";
            document.getElementById('application_tablename_filter').style.display = "none";
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
        console.log(emplist);
        document.getElementById('filterby').value = emplist;
        $('#kt_modal_dynamic_popup').modal('hide');
    }

    function tocheckallboxes(){
        var val = $('#val_select_all').val();
        if(val==0){
            $('#val_select_all').val('1');
             $('.checkbox1').each(function(){
                this.checked = true;
            });
         }else{
             $('#val_select_all').val('0');
            $('.checkbox1').each(function(){
                this.checked = false;
            });
        }

    }

   function notifyuser(id){
      jQuery.ajax({
            url: "views/custom_insert_user_notify.php",
            data:'id='+id+'&comments=Please submit your timesheet ',
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
            },
            error:function (){}
        });
   }

function sendallnotify(){
const id = [];
    $('.checkbox1').each(function(){

    if(this.checked){
        console.log(this.value);
        id.push(this.value);

    }
    });

console.log(JSON.stringify(id));
      jQuery.ajax({
            url: "views/custom_insert_user_timesheet_notify.php",
            data:'id='+JSON.stringify(id)+'&comments=Please submit your timesheet ',
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
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
                            <div class="card-title col-lg-12 row">
                                <h3 class="card-label col-lg-2">Employee List</h3>


                                <div class="col-lg-1">
                                    <button type="button" class="btn btn-outline-secondary" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Filter</button>
                                </div>


<div class="form-group  col-lg-2">
    <label for="example-date-input-from" class="col-4 col-form-label">From</label>
    <div class="col-10">
        <input aria-label="Fromdatetimesheet" class="form-control" type="date" name="fromdatetimesheet" value="<?php echo isset($_POST['fromdatetimesheet'])?$_POST['fromdatetimesheet']:date("Y-m-d", strtotime('monday this week', strtotime(date('Y-m-d')))); ?>" id="example-date-input-from">
    </div>
</div>
<div class="form-group  col-lg-2">
    <label for="example-date-input-to" class="col-4 col-form-label">To</label>
    <div class="col-10">
        <input aria-label="Todatetimesheet" class="form-control" type="date" name="todatetimesheet" value="<?php echo isset($_POST['todatetimesheet'])?$_POST['todatetimesheet']:date("Y-m-d", strtotime('sunday this week', strtotime(date('Y-m-d')))); ?>" id="example-date-input-to">
    </div>
</div>
<div class="form-group  col-lg-2">
    <label for="example-date-input-from"  class="col-5 col-form-label">Display By</label>
    <div class="col-10">
        <select aria-label="Displayby" class="form-control" name="displayby" id="exampleSelectd">
            <option value="0" <?PHP echo $_POST['displayby']==0?"selected":""; ?>>All</option>
            <option value="1" <?PHP echo $_POST['displayby']==1?"selected":""; ?>>Submited</option>
            <option value='2' <?PHP echo $_POST['displayby']==2?"selected":""; ?>>Not Submited</option>
        </select>
    </div>
</div>
<div class="col-lg-2">
    <input type="hidden" id="filterby" name="filterby">
    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
    <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="sendallnotify()">Send Notify</button>
</div>
</div>
</div>

                        <!--end::Charts Widget 2-->
                    </div>

                </div>
            </div>
        </form>

        <div class="card card-custom col-lg-12">
            <div class="row" style="display: none">
                <div class="form-group col-lg-3 mt-10 mb-10"  id="bulkcategory" style="display: none">
                    <span>Transfer Category</span>
                    <select aria-label="Bulkchangecategory" onchange="showbulkchangefilter(this.value);" id="bulkchangecategory" name="bulkchangecategory" class="form-control" >
                        <?php
                        foreach ($bulkchangecategory as $filter) {
                            ?>
                            <option <?php if($selectedoption==$filter['application_tablename']) {?> selected <?php
} ?> value="<?php echo $filter['application_tablename']."|".$filter['application_table_fieldname'].'|'.$filter['id'].'|'.$filter['update_employees_fieldname'].'|'.$filter['insert_history_tablename'];?>"><?php echo $filter['category_name'];?></option>
                        <?php } ?>
                    </select>
                    <span class="form-text text-muted">Select Bulk Transfer Category</span>

                </div>

                <div class="col-lg-3 mt-10 mb-10" id="application_tablename_filter">

                </div>
                <div class="col-lg-3 mt-10 mb-10" id="effectdate" style="display: none">
                    <span>Effective From</span>
                    <div class="input-group date">
                        <input aria-label="Effective Date" type="text" class="form-control"  id="kt_datepicker_4_1"  name="effective_date" />
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-calendar-check-o"></i>
                            </span>
                        </div>
                    </div>
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
                    <button id="previewbtn"  style="display: none" class="btn btn-primary btn-shadow-hover font-weight-bold mt-8" onclick="bulkconfirmpopup()">Preview</button>

                </div>


            </div>
        </div>

        <div class="card card-custom ">
                                    <div class="card-body">
            <!--begin: Datatable -->
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col" class="py-0 min-w-50px text-center">
                                <div class="checkbox-inline">
                            <label class="checkbox checkbox-success">
                            <input aria-label="Ect All" type="checkbox" id="select_all" class="itemRow" onclick="tocheckallboxes()" onkeydown="">
                            <span></span></label>
                            <input type="hidden" name="" id="val_select_all" value="0">
                        </div>
                            </th>
                            <th scope="col">NAME </th>
                            <th scope="col">Department  </th>
                            <th scope="col">Designation  </th>
                        <th scope="col">Location Id   </th>
                            <th scope="col">Job Title  </th>
                            <th scope="col">CoastCenter </th>
                            <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $displayby  = $_POST['displayby']!="" ?$_POST['displayby']:'0';
                            $serialno = 1;
                            foreach ($employee_data as $empidkey => $empvalue) {
                                $timeshetavlb = 0;
                                if(!isset($projecttimesheets[$empvalue['id']])){ $timeshetavlb = 1;
}
                                if($timeshetavlb == '0' && $displayby == '1' || $displayby == '0'){
                                ?>
                                <tr>
                                    <td class="text-center pr-0">
                                        <div class="checkbox-inline">
                                            <label class="checkbox checkbox-success">
                                            <input aria-label="Checkbox" type="checkbox"  class="itemRow checkbox1" name="checkbox" value="<?php echo $empvalue['id']; ?>">
                                            <span></span></label>
                                        </div>
                                    </td>
                                        <td>
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
                                        </td>
                                <td><?php echo $departments[$empvalue['department_id']]; ?> </td>
                                <td><?php echo $designations[$empvalue['designation_id']]; ?> </td>
                                <td><?php echo $locations[$empvalue['location_id']]; ?> </td>
                                <td> <?php echo $jobtitles[$empvalue['job_title_id']]; ?></td>
                                <td> <?php echo $costcenter[$empvalue['cost_center_id']]; ?></td>
            <td>
                    <a href="#"><?php  if(!isset($projecttimesheets[$empvalue['id']])){ echo "Notified";
}  ?></a>
            </td>
</tr>

        <?php     }elseif($timeshetavlb == '1' && $displayby == '2' || $displayby == '0'){  ?>
<tr>
    <td class="text-center pr-0">
        <div class="checkbox-inline">
        <label class="checkbox checkbox-success">
        <input aria-label="Checkbox" type="checkbox" class="itemRow checkbox1" name="checkbox" value="<?php echo $empvalue['id']; ?>">
        <span></span></label>
        </div>
    </td>
    <td>
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
                                        </td>

                                <td><?php echo $departments[$empvalue['department_id']]; ?> </td>
                                <td><?php echo $designations[$empvalue['designation_id']]; ?> </td>
                                <td><?php echo $locations[$empvalue['location_id']]; ?> </td>
                                <td> <?php echo $jobtitles[$empvalue['job_title_id']]; ?></td>
                                <td> <?php echo $costcenter[$empvalue['cost_center_id']]; ?></td>

<td class="pr-0">
<?php  if(!isset($projecttimesheets[$empvalue['id']])){ ?>

    <button class="btn btn-success font-size-sm float-right" onclick="notifyuser(<?php echo $empvalue['id']; ?>)">Notify</button>

<?php  }  ?>
</td>
</tr>

                            <?php } $serialno++;
} ?>
                        </tbody>
                    </table>
                </div></div>
            </div>
        </div>
    </div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable"  >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
