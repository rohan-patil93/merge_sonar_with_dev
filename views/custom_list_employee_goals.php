 <?php
 $drid = encodeId($_GET['Rid']);
 $erid = explode(":",$drid);
 $appraisal_id  = $erid['1'];
 $assessment_year = $_GET['AY'];
 $url = API."?mode=getemployeegoalsdetails&assessment_year=".$assessment_year."&appraisal_id=".$appraisal_id;
 $empgoalsres = callAPI($url);
 $empgoalsresult = json_decode($empgoalsres,1);
$location_list = $empgoalsresult['location_list'];
$department_list = $empgoalsresult['department_list'];
$designation_list = $empgoalsresult['designation_list'];
 $employees = $empgoalsresult['employees'];
 $employees_list = array();
 $employees_job = array();
 $employees_dep = array();
 foreach($employees as $employees){
     $employees_loc[$employees['location_id']][$employees['id']]= $employees;
     $employees_job[$employees['job_title_id']][$employees['id']]= $employees;
     $employees_dep[$employees['department_id']][$employees['id']]= $employees;
 }
 ?>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
 <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
 <style>
     tr{}
     td, th{border-bottom: 1px solid #EBEDF3; padding: 10px;}
     table.data-table tr th{color: #B5B5C3; font-weight: 500; font-size: 14px; letter-spacing: 1px;}
     table.data-table .custom-check{position: relative;}
     table.data-table .custom-check input[type="checkbox"]{width: 17px; height: 17px;}
     table.data-table .dropdown .btn{padding: 0px 8px 2px 8px;}

     span.online-text{width: 8px; height: 8px; display: inline-block; background: #dc3545; border-radius: 50%;}
     span.direct-text{width: 8px; height: 8px; display: inline-block; background: #007bff; border-radius: 50%;}
     table.data-table .dropdown-toggle:after{display: none;}
     table.data-table a.edit{display: inline-block; vertical-align: middle;}
 </style>
 <script type="text/javascript">
     function viewemployeeGoals(emp_id,appraisal_id){
         jQuery.ajax({
            url: "views/custom_view_employee_goal_popup.php",
            data:'emp_id='+emp_id+'&appraisal_id='+appraisal_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            }
        });
     }
 </script>
 <div class="flex-column-fluid ">
     <div class="">
         <div class="row">
             <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                 <div class="card-header">
                     <h5>Organization Goals</h5>
                 </div>
                 <div class="card-body">
                     <div class="accordion-block color-accordion-block">
                         <div class="color-accordion" id="color-accordion">
                             <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2 ">Locations <span class="pcoded-badge label label-warning float-right"><?php echo count((array)$location_list); ?></span></a>

                             <div class="accordion-desc">
                                 <div class="row dt-responsive table-responsive">
                                     <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
                                         <thead>
                                             <tr>
                                                 <th scope="col" style="width: 3%;"></th>
                                                 <th scope="col" style="width: 7%;">#</th>
                                                 <th scope="col" style="width: 60%;">Location Name</th>
                                                 <th scope="col" style="width: 30%;">Employee Count</th>
                                             </tr>
                                         </thead>
                                         <?php $k=1; foreach($location_list as  $location_lists){ $empdetails = $employees_loc[$location_lists['id']]; ?>
                                         <tr class="main">
                                             <td>
                                                 <a href="javascript:void(0)" class="main">
                                                     <svg xmlns="http:
                                                         <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                                     </svg>
                                                 </a>
                                             </td>
                                             <td><?php echo $k; ?></td>
                                             <td><?php echo  $location_lists['location_name']; ?></td>
                                             <td><span class="pcoded-badge label label-warning"><?php echo count((array)$empdetails); ?></span></td>
                                         </tr>
                                         <tr class="data">
                                             <td colspan="15">
                                                 <table class="mb-3" style="width: 100%">
                                                     <tr>
                                                         <th scope="col" style="width: 3%;"></th>
                                                         <th scope="col" class="text-center" style="width: 7%;">SL.NO</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Employee Name</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Emp Code</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Action</th>

                                                     </tr>
                                                     <?php $l=1; foreach($empdetails as $empdetail){ ?>
                                                         <tr>
                                                             <td></td>
                                                             <td class="text-center"><?php echo $l; ?></td>
                                                             <td class="text-center"><?php echo $empdetail['display_name']; ?></td>
                                                             <td class="text-center"><?php echo $empdetail['emp_code']; ?></td>
                                                             <td class="text-center"><span class="pcoded-badge label label-primary cursor-pointer" onclick="viewemployeeGoals('<?php echo $empdetail['id']; ?>','<?php echo $appraisal_id; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">View Goals</span></td>
                                                         </tr>
                                                         <?php  $l++;
} ?>

                                                     </table>
                                                 </td>
                                             </tr>
                                             <?php $k++;
} ?>
                                         </table>
                                     </div>
                                 </div>

                                 <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Departments <span class="pcoded-badge label label-warning float-right"><?php echo count((array)$department_list); ?></span></a>
                                 <div class="accordion-desc">
                                     <div class="row dt-responsive table-responsive">
                                     <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1_2">
                                         <thead>
                                             <tr>
                                                 <th scope="col" style="width: 3%;"></th>
                                                 <th scope="col" style="width: 7%;">#</th>
                                                 <th scope="col" style="width: 60%;">Department Name</th>
                                                 <th scope="col" style="width: 30%;">Employee Count</th>
                                             </tr>
                                         </thead>
                                         <?php $k=1; foreach($department_list as  $department_lists){ $empdetails = $employees_dep[$department_lists['id']]; ?>
                                         <tr class="main">
                                             <td>
                                                 <a href="javascript:void(0)" class="main">
                                                     <svg xmlns="http:
                                                         <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                                     </svg>
                                                 </a>
                                             </td>
                                             <td><?php echo $k; ?></td>
                                             <td><?php echo  $department_lists['department_name']; ?></td>
                                             <td><span class="pcoded-badge label label-warning"><?php echo count((array)$empdetails); ?></span></td>
                                         </tr>
                                         <tr class="data">
                                             <td colspan="15">
                                                 <table class="mb-3" style="width: 100%">
                                                     <tr>
                                                         <th scope="col" style="width: 3%;"></th>
                                                         <th scope="col" class="text-center" style="width: 7%;">SL.NO</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Employee Name</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Emp Code</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Action</th>
                                                     </tr>
                                                     <?php $l=1; foreach($empdetails as $empdetail){ ?>
                                                         <tr>
                                                             <td></td>
                                                             <td class="text-center"><?php echo $l; ?></td>
                                                             <td class="text-center"><?php echo $empdetail['display_name']; ?></td>
                                                             <td class="text-center"><?php echo $empdetail['emp_code']; ?></td>
                                                             <td class="text-center"><span class="pcoded-badge label label-primary cursor-pointer" onclick="viewemployeeGoals('<?php echo $empdetail['id']; ?>','<?php echo $appraisal_id; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">View Goals</span></td>
                                                         </tr>
                                                         <?php  $l++;
} ?>

                                                     </table>
                                                 </td>
                                             </tr>
                                             <?php $k++;
} ?>
                                         </table>
                                     </div>
                                 </div>

                                 <a class="accordion-msg b-none waves-effect waves-light f-16 f-w-600 bg-gray mb-2">Designations <span class="pcoded-badge label label-warning float-right"><?php echo count((array)$designation_list); ?></span></a>
                                 <div class="accordion-desc">
                                         <div class="row dt-responsive table-responsive">
                                     <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1_3">
                                         <thead>
                                             <tr>
                                                 <th scope="col" style="width: 3%;"></th>
                                                 <th scope="col" style="width: 7%;">#</th>
                                                 <th scope="col" style="width: 60%;">Designation Name</th>
                                                 <th scope="col" style="width: 30%;">Employee Count</th>
                                             </tr>
                                         </thead>
                                         <?php $k=1; foreach($designation_list as  $designation_lists){ $empdetails = $employees_job[$designation_lists['id']]; ?>
                                         <tr class="main">
                                             <td>
                                                 <a href="javascript:void(0)" class="main">
                                                     <svg xmlns="http:
                                                         <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                                     </svg>
                                                 </a>
                                             </td>
                                             <td><?php echo $k; ?></td>
                                             <td><?php echo  $designation_lists['designation_name']; ?></td>
                                             <td><span class="pcoded-badge label label-warning"><?php echo count((array)$empdetails); ?></span></td>
                                         </tr>
                                         <tr class="data">
                                             <td colspan="15">
                                                 <table class="mb-3" style="width: 100%">
                                                     <tr>
                                                         <th scope="col" style="width: 3%;"></th>
                                                         <th scope="col" class="text-center" style="width: 7%;">SL.NO</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Employee Name</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Emp Code</th>
                                                         <th scope="col" class="text-center" style="width: 30%;">Action</th>
                                                     </tr>
                                                     <?php $l=1; foreach($empdetails as $empdetail){ ?>
                                                         <tr>
                                                             <td></td>
                                                             <td class="text-center"><?php echo $l; ?></td>
                                                             <td class="text-center"><?php echo $empdetail['display_name']; ?></td>
                                                             <td class="text-center"><?php echo $empdetail['emp_code']; ?></td>
                                                             <td class="text-center"><span class="pcoded-badge label label-primary cursor-pointer" onclick="viewemployeeGoals('<?php echo $empdetail['id']; ?>','<?php echo $appraisal_id; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">View Goals</span></td>
                                                         </tr>
                                                         <?php  $l++;
} ?>

                                                     </table>
                                                 </td>
                                             </tr>
                                             <?php $k++;
} ?>
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

     <script type="text/javascript">
         jQuery(document).ready(function() {
jQuery(".data").hide();
jQuery(".main").click(function() {
    jQuery(this).parent().parent().next(".data").toggle("fast");
});
});
</script>

