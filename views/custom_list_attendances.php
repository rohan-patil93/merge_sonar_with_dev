<?php
$cur_year = date('Y');
$cur_month = date('m');
$datakey["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$dkey = urlencode(json_encode($datakey));
$url = API."?mode=gettabledetailswithconditions&table=attendance_key_types&data=".$dkey;
$cudata = callAPI($url);
$key_details = json_decode($cudata,1);

?>
<script type="text/javascript">

    function displaystausdrop(id,table,status_id){
       $.ajax({
         url: "ajax.php",
         data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
         type: "POST",
         success:function(data){
           console.log(data);
           $("#dropdownlist").html(data);
         },
         error:function (){}
       });

     }

    function showpopup(){
        var filter_id = document.getElementById('filtertbl_rowid').value;
        jQuery.ajax({
            url: "views/custom_insert_filters_attendance.php",
            data:'filter_id='+filter_id,
            type: "GET",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error:function (){}
        }).done(function(){
        });
    }
    function getnamelist(emplist){
        var selectednames = emplist;
        var attendancemonth = document.getElementById('att_month_year').value;
        var monyr = attendancemonth.split("-");
        var att_year = monyr[0];
        var att_month = monyr[1];
        $.ajax({
            url: "ajax.php",
            data:'page=selectedattendancelist&names='+selectednames+"&month="+att_month+"&year="+att_year,
            type: "POST",
            success: function(response) {
                var division = document.getElementById('attendance_table');
                console.log("response ="+response);
                division.innerHTML = response;
                $('#kt_modal_dynamic_popup').modal('hide');
            }
        });
    }
</script>

<style>
    table, td, th {
  border: 1px solid black;
}
</style>

<div class="d-flex flex-column-fluid ">
    <div class="container-fluid ">
        <div class="card card-custom mb-5 j-content">

<div class="card-header">
    <h5>Work Entry</h5>
    <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>

            <form class="form">
                <div class="">
                    <div class="form-group row">
                        <div class="col-lg-3">
                            <div class="row">

                            <div class="ml-12">
                                <div class="form-group row">
                                    <div class="col-10">
                                        <input aria-label="Att Month Year" class="form-control" type="month" name="att_month_year" value="<?php echo $cur_year."-".$cur_month?>"  id="att_month_year" />
                                        <input class="form-control" type="hidden" name="shiftkeys" value="<?php echo $cudata;?>"  id="shiftkeys" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="row">
                            <div class="mr-2">

                            </div>
                            <div class="mr-2">
                                <button type="button" onclick="getnamelist('All Employees');" class="btn btn-primary waves-effect waves-light">Generate For All</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">

                            <input type="hidden" name="task_for" id="filter" value="<?php echo urlencode($task_for); ?>">

                            <input type="hidden" name="attendancemonth" id="attendancemonth" value="<?php echo date('Y-m-d');?>">
                            <input type="hidden" name="process_type" id="process_type" value="1">
                            <div class="dropdown mr-1">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Select Employees </button>

                            </div>
                            <div class="dropdown mr-1 mt-3">

<div class="form-radio">
<div class="radio radio-inline">
  <label class="form-label">
    <input aria-label="Ios2" type="radio" name="radios2"/>
    <i class="helper"></i>Discrepancy Records
  </label>
</div>
<div class="radio radio-inline">
  <label class="form-label">
    <input aria-label="Ios2" type="radio" name="radios2"/>
    <i class="helper"></i>All Records
  </label>
</div>
</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 text-right">

                    </div>
                </div>
            </div>
            <div class="mb-5">
            <?php
            foreach ($key_details as $attname) {
                ?>
                <div class="btn btn-icon w-auto btn-clean d-inline-flex align-items-center btn-sm px-2 mr-5">
                    <span class="form-label badge badge-lg bg-<?php echo strtolower($attname['key_bgcolor']);?> m-r-10"><?php echo $attname['key_short_code'];?></span>

                    <span class="text-dark-50 font-weight-bolder font-size-base mb-2">&nbsp;<?php echo $attname['key_name'];?></span>
                </div>

                <?php
            }
            ?>
        </div>
        </form>

        <div id="attendance_table" class="">


<div class="alert alert-info icons-alert alert-dismissible">
    <p><strong>Please Select Employees</strong> </p>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

</div>
    </table>
</div>
<!--begin::Footer-->
