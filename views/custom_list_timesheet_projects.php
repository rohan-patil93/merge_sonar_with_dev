<?php
$vurl = API."?mode=getprojecttimesheets&company_id=".$_SESSION['company_id']."&emp_id=".$_SESSION['emp_id'];
$configesponse = callAPI($vurl);
$projectData = json_decode($configesponse,1);
$projects = $projectData['projects'];

?>
<script type="text/javascript">

function addtimesheet(projectid) {
      jQuery.ajax({
         url: "views/custom_list_timesheet_project_popup.php",
         data:'projectid='+projectid,
         type: "GET",
         success:function(data){
            $('#popup2').html(data);
            $('#kt_modal_KTDatatable_remote1').modal({ show: true});
         },
         error:function (){}
      });
   }
</script>

<div class="d-flex flex-column-fluid">

   <div class="container-fluid">

   <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
               <div class="card-title">
            <span class="kt-portlet__head-icon">
               <i class="kt-font-brand flaticon2-line-chart"></i>
            </span>
            <h3 class="card-label">&nbsp;<?php echo "Projects";  ?> </h3>
         </div>
         <div class="kt-portlet__head-toolbar">
            <div class="card-toolbar">
                  <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                     <i class="ki ki-long-arrow-back icon-xs"></i>
                     Back
                  </a>
               &nbsp;
            </div>
         </div>
      </div>
      <div class="card card-custom">
      <div class="card-body">


                      <div class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded" id="kt_datatable" style="">
                        <table class="datatable-table" style="display: block;">
                           <thead class="datatable-head">
                              <tr class="datatable-row" style="left: 0px;">
                                 <th scope="col" data-field="RecordID" class="datatable-cell-center datatable-cell datatable-cell-check">
                                    <span style="width: 20px;">
                                       <label class="checkbox checkbox-single checkbox-all">
                                          <input aria-label="Checkbox" type="checkbox">&nbsp;<span></span>
                                       </label>
                                    </span>
                                 </th>
                                 <th scope="col" data-field="OrderID" class="datatable-cell datatable-cell-sort">
                                    <span style="width: 126px;">Date</span>
                                 </th>
                                 <th scope="col" data-field="Country" class="datatable-cell datatable-cell-sort">
                                    <span style="width: 126px;">Project</span>
                                 </th>
                                 <th scope="col" data-field="ShipAddress" class="datatable-cell datatable-cell-sort">
                                    <span style="width: 126px;">Customer</span>
                                 </th>

                                 <th scope="col" data-field="Type" data-autohide-disabled="false" class="datatable-cell datatable-cell-sort"style="text-align:center;">
                                    <span style="width: 126px;">Time</span>
                                 </th>
                                 <th scope="col" data-field="Actions" data-autohide-disabled="false" class="datatable-cell-left datatable-cell datatable-cell-sort">
                                    <span style="width: 125px;">Billing Start</span>
                                 </th>
                                 <th scope="col" data-field="Actions" data-autohide-disabled="false" class="datatable-cell-left datatable-cell datatable-cell-sort" style="text-align:center;">
                                    <span style="width: 50px;"></span>
                                 </th>
                              </tr>
                           </thead>
                           <tbody class="datatable-body" style="">
                              <?php
                                 foreach($projects as $key=>$proj) {
                              ?>
                              <tr data-row="0" class="datatable-row" style="left: 0px;">
                                 <td class="datatable-cell-center datatable-cell datatable-cell-check" data-field="RecordID" aria-label="1">
                                    <span style="width: 20px;">
                                       <label class="checkbox checkbox-single">
                                          <input aria-label="Checkbox" type="checkbox" value="1">&nbsp;<span></span>
                                       </label>
                                    </span>
                                 </td>
                                 <td data-field="OrderID" aria-label="64616-103" class="datatable-cell">
                                    <span style="width: 126px;"><?php echo date('d M Y', strtotime($proj['created_date'])); ?></span>
                                 </td>
                                 <td data-field="Country" aria-label="Brazil" class="datatable-cell" style="padding: 0px !important;">
                                    <span style="width: 126px;"><?php echo $proj['name']; ?></span>
                                 </td>
                                 <td data-field="ShipAddress" aria-label="698 Oriole Pass" class="datatable-cell">
                                    <span style="width: 126px;"><?php echo $proj['customer_id']; ?></span>
                                 </td>

                                 <td data-field="Type" data-autohide-disabled="false" aria-label="1" class="datatable-cell">
                                    <span style="width: 126px;">
                                       <span class="font-weight-bold text-danger"><?php $projectid = $proj['id']; echo date('H:i:s', strtotime($proj['created_date'])); ?></span>
                                    </span>
                                 </td>
                                 <td class="datatable-cell" data-field="Actions" data-autohide-disabled="false" aria-label="null">

                                    <button type="button" class="btn btn-primary" onclick="addtimesheet('<?php echo $projectid; ?>');">
                                       Add Timesheet
                                    </button>

                                 </td>
                              </tr>
                              <?php
                              } ?>

                        </tbody>
                     </table>
                  </div>

      </div>
   </div>
</div></div></div>

<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
   <div class="modal-dialog modal-md modal-dialog-scrollable"  >
      <div class="modal-content"  id="popup2">

      </div>
   </div>
</div>
