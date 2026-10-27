<?php
$cur_year = date('Y');
$cur_month = date('m');

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
                $('#kt_modal_dynamic_popup').modal({ show: true});
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
            data:'page=selectetimesheetlist&names='+selectednames+"&month="+att_month+"&year="+att_year,
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

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom mb-5">
            <div class="row col-lg-12 mt-5">
                <div class="col-lg-6">
                    <div class="align-items-center border-0">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="font-weight-bolder text-dark font-size-h1">Timesheet Summary</span>
                        </h3>
                    </div>
                </div>
            </div>
            <form class="form">
                <div class="">
                    <div class="form-group row">
                        <div class="col-lg-2">
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
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="mr-2">

                            </div>
                            <div class="mr-2">
                                <button type="button" onclick="getnamelist('All Employees');" class="btn btn-primary">Generate For All</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="row">
                            <input type="hidden" name="filtertbl_rowid" id="filtertbl_rowid" value="<?php echo $filter_id; ?>">

                            <input type="hidden" name="task_for" id="filter" value="<?php echo urlencode($task_for); ?>">

                            <input type="hidden" name="attendancemonth" id="attendancemonth" value="<?php echo date('Y-m-d');?>">
                            <input type="hidden" name="process_type" id="process_type" value="1">
                            <div class="dropdown mr-1">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" onclick="showpopup();"><i class="flaticon2-hourglass-1"></i>Select Employees </button>

                            </div>
                            <div class="dropdown mr-1 mt-3">
                                <div class="radio-inline">
                                    <label class="radio">
                                        <input aria-label="Ios2" type="radio" name="radios2"/>
                                        <span></span>
                                        Discrepancy Records
                                    </label>
                                    <label class="radio">
                                        <input aria-label="Ios2" type="radio" name="radios2"/>
                                        <span></span>
                                        All Records
                                    </label> </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 text-right">

                    </div>
                </div>
            </div>
            <div class="mb-5">
                 <div class="btn btn-icon w-auto btn-clean d-inline-flex align-items-center btn-sm px-2 mr-2">
                     <span class="symbol symbol-35 symbol-info">
                        <span class="label label-inline label-light-success font-weight-bold">
                        B</span>
                     </span>
                     <span class="text-dark-50 font-weight-bolder font-size-base mr-3">&nbsp;Billable</span>
                  </div>

                  <div class="btn btn-icon w-auto btn-clean d-inline-flex align-items-center btn-sm px-2 mr-2">
                     <span class="symbol symbol-35 symbol-info">
                      <span class="label label-inline label-light-warning font-weight-bold">NB</span>
                   </span>
                   <span class="text-dark-50 font-weight-bolder font-size-base mr-3">&nbsp;Non Billable</span>
                </div>




        </div>
        </form>

        <div id="attendance_table" class="p-5">
        <div class="alert alert-custom alert-notice alert-light-primary fade show" role="alert">
    <div class="alert-icon"><i class="flaticon-warning"></i></div>
    <div class="alert-text">Please Select Employees</div>
    <div class="alert-close">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true"><i class="ki ki-close"></i></span>
        </button>
    </div>
</div>
</div>
    </table>
</div>
<!--begin::Footer-->
