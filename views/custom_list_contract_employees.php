<?php

$vurl = API."?mode=getcontractemployees&company_id=".$_SESSION['company_id']."&vendor_id=".$_SESSION['vendor_id'];
$configesponse = callAPI($vurl);
$projectData = json_decode($configesponse,1);
$emplist_monthly = $projectData['emplist_month'];
$emplist_weekly = $projectData['emplist_week'];

?>
<div class="card card-custom">
   <div class="card-body">

      <div class="card card-custom gutter-b">
         <!--begin::Header-->
         <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
               <span class="card-label font-weight-bolder text-dark">Contract Employees</span>
               <span class="text-muted mt-3 font-weight-bold font-size-sm">Total Number of Employees : <?php echo count((array)$emplist_monthly);?></span>
            </h3>
            <div class="card-toolbar">
               <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                  <li class="nav-item">
                     <a class="nav-link py-2 px-4" data-toggle="tab" href="#kt_tab_pane_12_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Month</a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link py-2 px-4 active" data-toggle="tab" href="#kt_tab_pane_12_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Week</a>
                  </li>
               </ul>
            </div>
         </div>
         <!--end::Header-->
         <!--begin::Body-->
         <div class="card-body pt-2 pb-0 mt-n3">
            <div class="tab-content mt-5" id="myTabTables12">
               <!--begin::Tap pane-->
               <div class="tab-pane fade " id="kt_tab_pane_12_1" role="tabpanel" aria-labelledby="kt_tab_pane_12_1">
                  <!--begin::Table-->
                  <div class="table-responsive">
                    <table class="table table-borderless table-head-custom table-checkable" id="kt_datatable1">
                      <thead>
                        <tr>
                           <th scope=\"col\" class="p-0 w-50px">#</th>

                           <th scope=\"col\" class="p-0 w-50px">Employee</th>
                           <th scope=\"col\" class="p-0 min-w-200px"></th>
                           <th scope=\"col\" class="p-0 min-w-120px">Timesheet Status</th>
                           <th scope=\"col\" class="p-0 min-w-120px">Scheduled Time (Hrs)</th>
                           <th scope=\"col\" class="p-0 min-w-120px">Total Time Spent (Hrs)</th>

                        </tr>
                     </thead>
                     <tbody>
                      <?php  $sno =0;
                      foreach($emplist_monthly as $empm) {
                      $sno++; ?>
                        <tr>
                           <td class="p-0 py-4"> <?php echo $sno; ?></td>
                           <td class="p-0 py-4">
                              <div class="d-flex align-items-center mr-7">

                                 <?php if($emp['image']!="") { ?>
                                    <img src="uploads/all/<?php echo $empm['image']; ?>" data-toggle="tooltip" title="" data-original-title="<?php echo $empm['display_name']; ?>" style="
                                    width: 50px;
                                    height: auto;
                                    border-radius: 50%;
                                    " alt="<?php echo $empm['display_name']; ?>">
                                 <?php } else { ?>

                                    <div class="symbol symbol-50 symbol-circle symbol-primary" data-toggle="tooltip" title="" data-original-title="<?php echo $empm['display_name']; ?>">
                                       <span class="symbol-label font-size-h5 font-weight-boldest"><?php echo initials($empm['display_name']); ?></span>
                                    </div>
                                 <?php } ?>
                              </div>
                           </td>
                           <td class="pl-0">
                              <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $empm['display_name'];?></a>
                              <span class="text-muted font-weight-bold d-block">
                                 <span class="font-weight-bolder text-dark-75"></span><?php echo $empm['email_id'];?></span>
                              </td>
                              <td>
                                 <span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo $empm['timesheet'];?></span>
                              </td>
                              <td>
                                 <span class="label label-lg label-warning font-size-h6 label-inline"><?php echo $empm['total_scheduletime'];?></span>
                              </td>
                              <td>
                                  <?php if($empm['total_timespent'] == "0"){ ?>
                                 <span class="text-muted font-weight-bold">-</span>
                                 <?php }else{ ?>
                                    <span class="label label-lg label-<?php if($empm['total_scheduletime'] < $empm['total_timespent']){ echo "danger";
}else{ echo "success";
} ?> font-size-h6 label-inline"><?php echo $empm['total_timespent'];?></span>
                                 <?php } ?>
                              </td>


                           </tr>
                        <?php } ?>

                     </tbody>
                  </table>
               </div>
               <!--end::Table-->
            </div>
            <!--end::Tap pane-->
            <!--begin::Tap pane-->
            <div class="tab-pane fade show active" id="kt_tab_pane_12_2" role="tabpanel" aria-labelledby="kt_tab_pane_12_2">
               <!--begin::Table-->
               <div class="table-responsive">
                  <table class="table table-borderless table-head-custom table-checkable" id="kt_datatable1_2">
                     <thead>
                        <tr>
                           <th scope=\"col\" class="p-0 w-50px">#</th>
                           <th scope=\"col\" class="p-0 w-50px">Employee</th>
                           <th scope=\"col\" class="p-0 min-w-200px"></th>
                           <th scope=\"col\" class="p-0 min-w-120px">Timesheet Status</th>

                           <th scope=\"col\" class="p-0 min-w-120px">Scheduled Time (Hrs)</th>
                           <th scope=\"col\" class="p-0 min-w-120px">Total Time Spent (Hrs)</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php  $sno =0;
                        foreach($emplist_weekly as $emp) { $sno++; ?>
                           <tr>
                              <td class="p-0 py-4"> <?php echo $sno; ?></td>
                              <td class="p-0 py-4">
                                 <div class="d-flex align-items-center mr-7">

                                    <?php if($emp['image']!="") { ?>
                                       <img src="uploads/all/<?php echo $emp['image']; ?>" data-toggle="tooltip" title="" data-original-title="<?php echo $emp['display_name']; ?>" style="
                                       width: 50px;
                                       height: auto;
                                       border-radius: 50%;
                                       " alt="<?php echo $emp['display_name']; ?>">
                                    <?php } else { ?>

                                       <div class="symbol symbol-50 symbol-circle symbol-primary" data-toggle="tooltip" title="" data-original-title="<?php echo $emp['display_name']; ?>">
                                          <span class="symbol-label font-size-h5 font-weight-boldest"><?php echo initials($emp['display_name']); ?></span>
                                       </div>
                                    <?php } ?>
                                 </div>
                              </td>
                              <td class="pl-0">
                                 <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $emp['display_name'];?></a>
                                 <span class="text-muted font-weight-bold d-block">
                                    <span class="font-weight-bolder text-dark-75"></span><?php echo $emp['email_id'];?></span>
                                 </td>
                                 <td>
                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg"><?php echo $emp['timesheet'];?></span>
                                 </td>
                                 <td>
                                    <span class="label label-lg label-warning  font-size-h6 label-inline"><?php echo $emp['total_scheduletime'];?></span>
                                 </td>
                                 <td>
                                  <?php if($emp['total_timespent'] == "0"){ ?>
                                 <span class="text-muted font-weight-bold">-</span>
                                 <?php }else{ ?>
                                    <span class="label label-lg label-<?php if($emp['total_scheduletime'] < $emp['total_timespent']){ echo "danger";
}else{ echo "success";
} ?> font-size-h6 label-inline"><?php echo $emp['total_timespent'];?></span>
                                 <?php } ?>
                              </td>


                              </tr>
                           <?php } ?>
                        </tbody>
                     </table>
                  </div>
                  <!--end::Table-->
               </div>
               <!--end::Tap pane-->

            </div>
         </div>
         <!--end::Body-->
      </div>

   </div>
</div>
