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
            <div class="col-lg-6">
               <!--begin::Card-->
               <div class="card card-custom gutter-b">
                  <!--begin::Header-->
                  <div class="card-header h-auto">
                     <!--begin::Title-->
                     <div class="card-title py-5">
                        <h3 class="card-label">Payments</h3>
                     </div>
                     <!--end::Title-->
                  </div>
                  <!--end::Header-->
                  <div class="card-body">
                     <!--begin::Chart-->
                     <div id="chart_1"></div>
                     <!--end::Chart-->
                  </div>
               </div>
               <!--end::Card-->
            </div>
            <div class="col-lg-6">
               <div class="col-lg-12 row">
                  <?php
                  $box_array['0']=array('name'=>"Open Jobs",'count'=>'123','color'=>'primary');
                  $box_array['1']=array('name'=>"Apply Jobs",'count'=>'123','color'=>'warning');
                  $box_array['2']=array('name'=>"Approved Jobs",'count'=>'110','color'=>'success');
                  $box_array['3']=array('name'=>"Sned Candidates",'count'=>$vdash['4']['all_candidates'],'color'=>'info');
                  $box_array['4']=array('name'=>"Approved Candidates",'count'=>$vdash['4']['selected'],'color'=>'success');
                  $box_array['5']=array('name'=>"Rejected Candidates",'count'=>$vdash['4']['rejected'],'color'=>'danger');
                  $box_array['6']=array('name'=>"Submited Time Sheet",'count'=>'30','color'=>'dark');
                  $box_array['7']=array('name'=>"Approved Time Sheet",'count'=>'14','color'=>'success');
                  $box_array['8']=array('name'=>"Pending Time Sheet",'count'=>'16','color'=>'warning');
                  foreach($box_array as $box){ ?>
                  <div class="col-lg-4">
                     <div class="card card-custom bg-<?php echo $box['color']; ?> gutter-b" style="height: 150px">
                        <div class="card-body">
                           <span class="svg-icon svg-icon-3x svg-icon-white ml-n2">

                              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                 <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24"></rect>
                                    <rect fill="#000000" x="4" y="4" width="7" height="7" rx="1.5"></rect>
                                    <path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#000000" opacity="0.3"></path>
                                 </g>
                              </svg>
                              <!--end::Svg Icon-->
                           </span>
                           <div class="text-inverse-<?php echo $box['color']; ?> font-weight-bolder font-size-h2 mt-3"><?php echo $box['count']; ?></div>
                           <a href="#" class="text-inverse-<?php echo $box['color']; ?> font-weight-bold font-size-sm mt-1"><?php echo $box['name']; ?></a>
                        </div>
                     </div>
                  </div>
                  <?php } ?>
               </div>
            </div>
            <?php
               $table_list['0']= array('title'=>'Candidate List','sub_title'=>'','tab'=>array('All','Seleted','Rejected'),'sub'=>$vdash['0'],'th_title'=>array('','Name','Job Title','Apply'));
               $table_list['1']= array('title'=>'Seleted Candidates','sub_title'=>'','tab'=>'','sub'=>$vdash['1'],'th_title'=>array('','Name','Job Title','Status Date'));
               $table_list['2']= array('title'=>'Time Sheet','sub_title'=>'','tab'=>'','sub'=>$vdash['2'],'th_title'=>array('','Name','Job Title','Apply'));
               $table_list['3']= array('title'=>'Contracts','sub_title'=>'','tab'=>'','sub'=>$vdash['3'],'th_title'=>array('','Name','Job Title','Apply'));
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
