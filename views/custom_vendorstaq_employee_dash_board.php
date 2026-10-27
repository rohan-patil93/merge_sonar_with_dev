<?php
   $vendor_url =  API."?mode=toGetVendorDashBoardData";
   $vendor_dashboard = callAPI($vendor_url);
   $vdash = json_decode($vendor_dashboard,1);

   $stausresult = ViewStatusData();
?>
<div class="d-flex flex-column-fluid mt-5">
   <!--begin::Container-->
   <div class="container-fluid">
      <!--end::Row-->
      <!--begin::Row-->
      <div class="tab-content m-0 p-0">

         <div class="col-lg-12 row">

            <?php
               $table_list['0']= array('title'=>'Timesheet List','sub_title'=>'','tab'=>array('Approved','Tobe Approved'),'sub'=>$vdash['0'],'th_title'=>array('','Name','Spent','Status'));
               $table_list['1']= array('title'=>'Conracts','sub_title'=>'','tab'=>array('Approved','Tobe Approved'),'sub'=>$vdash['1'],'th_title'=>array('','Name','Details','Status Date'));
               $n=1; foreach($table_list as $table){ ?>
            <div class="col-lg-6">
               <div class="">
                  <!--begin::Base Table Widget 2-->
                  <div class="card card-custom card-stretch gutter-b">
                     <!--begin::Header-->
                     <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                           <span class="card-label font-weight-bolder text-dark"><?php echo $table['title']; ?></span>
                           <?php if($table['sub_title']!=""){ ?>
                           <span class="text-muted mt-3 font-weight-bold font-size-sm"><?php echo $table['sub_title']; ?></span>
                           <?php } ?>
                        </h3>
                        <?php if(count((array)$table['tab'])>0){ ?>
                        <div class="card-toolbar">
                           <ul class="nav nav-pills nav-pills-sm nav-dark-75">
                              <?php $k=1; foreach($table['tab'] as $tab_list){ ?>
                              <li class="nav-item">
                                 <a class="nav-link py-2 px-4  <?php if($k==1){ echo "active";
} ?>" data-toggle="tab" href="#kt_tab_pane_2_<?php echo $n.$k; ?>"><?php echo $tab_list; ?></a>
                              </li>
                              <?php $k++;
} ?>
                           </ul>
                        </div>
                        <?php } ?>
                     </div>
                     <!--end::Header-->
                     <!--begin::Body-->
                     <div class="card-body pt-2 pb-0 mt-n3 scroll-pull" data-scroll="true" style="height:500px;">
                        <div class="tab-content mt-5" id="myTabTables2">
                           <!--begin::Tap pane-->
                           <?php $k=1; foreach($table['sub'] as $utab ){ ?>
                           <div class="tab-pane fade <?php if($k==1){ echo "active show";
} ?>" id="kt_tab_pane_2_<?php echo $n.$k; ?>" role="tabpanel" aria-labelledby="kt_tab_pane_2_<?php echo $n.$k; ?>">
                              <!--begin::Table-->
                              <div class="table-responsive">
                                 <table class="table table-borderless table-vertical-center">
                                    <thead>
                                       <tr>
                                          <?php $p=1; foreach($table['th_title'] as $thtile){ ?>
                                          <th scope="col" class="p-0 font-weight-boldest font-size-h6 text-center <?php if($p==1){ echo "w-50px";
}else{ echo"min-w-140px";
} ?>" ><?php echo $thtile; ?></th>
                                         <?php $p++;
} ?>


                                       </tr>
                                    </thead>
                                    <tbody>
                                       <?php foreach($utab as $tabs){ ?>
                                       <tr>
                                          <td class="pl-0 py-5">
                                             <div class="symbol symbol-50 symbol-light mr-2">
                                                <span class="symbol-label">
                                                   <img src="assets/media/svg/misc/006-plurk.svg" class="h-50 align-self-center" alt="" />
                                                </span>
                                             </div>
                                          </td>
                                          <td class="pl-0">
                                             <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg"><?php echo $tabs['name']; ?></a>
                                             <span class="text-muted font-weight-bold d-block font-size-sm"><?php echo $tabs['sub_name']; ?></span>
                                          </td>
                                          <td class="text-right">
                                             <span class="text-muted font-weight-bold"><?php echo $tabs['field']; ?></span>
                                          </td>
                                          <td class="text-center">
                                             <div>
                                                <span class="label label-lg  label-inline" style="background-color:<?php echo "#".$stausresult[$tabs['status']]['background_color']; ?>; color:<?php echo "#".$stausresult[$tabs['status']]['color']; ?>;"><?php echo $stausresult[$tabs['status']]['name']; ?></span>
                                             </div>
                                             <a href="#" class="text-dark font-weight-bold text-hover-primary mt-1 font-size-lg"><?php echo date('d M, Y', strtotime($tabs['field2'])); ?></a>


                                          </td>

                                       </tr>
                                       <?php } ?>
                                    </tbody>
                                 </table>
                              </div>
                              <!--end::Table-->
                           </div>
                           <!--end::Tap pane-->
                           <?php $k++;
} ?>

                        </div>
                     </div>
                     <!--end::Body-->
                  </div>
                  <!--end::Base Table Widget 2-->
               </div>
            </div>
         <?php $n++;
} ?>
         </div>

      </div>
   </div>
</div>
