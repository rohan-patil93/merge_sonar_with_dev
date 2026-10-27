<?php
$display_id = $_GET['NI']!="" ? $_GET['NI'] : '0';
$rid =  encodeId($_GET['Ridd']);
$rid = urldecode($rid);
$cand = explode(':', $rid);
 $candidate_id = $cand['1'];
 $table_name = "vendors";
  $whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'id'=>$candidate_id,'is_active'=>0);
   $table = 'vendors';
   $vdata = urlencode(json_encode($whrcon));
   $url = API."?mode=gettabledetailswithconditions&table=".$table."&data=".$vdata;
   $response = callAPI($url);
   $data = json_decode($response,1);
   $data = $data[0];
   $i=1; $k=1;
foreach ($data as  $value) {
    if($value!="" && $value!=0){
        $k++;
    }
$i++;
}
    if($i !=0 && $i!=""){
        $pp = round(($k/$i)*(100));
    }else{
        $pp =$k*100;
    }
   $dispath = "uploads/customer/".$data['image'];

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
     function statuspopup(status,id){
    jQuery.ajax({
     url: "views/custom_insert_status_popup.php",
     data:'table=candidates'+'&status='+status+'&rid='+id,
     type: "POST",
     success:function(data){
       $('#popup123').html(data);
       $('#kt_modal_KTDatatable_remote123').modal({ show: true});
     },
     error:function (){}
   });
  }
  </script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

                                <!--begin::Navbar-->
                                <div class="card mb-5 mb-xl-10">
                                    <div class="card-body pt-9 pb-0">
                                        <!--begin::Details-->
                                        <div class="d-flex flex-wrap flex-sm-nowrap mb-3 m-l-20">
                                            <div class="flex-shrink-0 m-r-25 mt-lg-0 mt-3">
                                                <?php if( $data['image']!=""){ ?>
                            <div class="symbol symbol-50 symbol-lg-120">
                                <img src="<?php echo $dispath; ?>" class="img-120 " alt="Details" />
                            </div>
                        <?php }else{ ?>
                            <div class="symbol symbol-50 symbol-lg-120 symbol-primary d-none">
                                <span class="font-size-h3 symbol-label font-weight-boldest"><?php echo initials($data['first_name']); ?></span>
                            </div>
                        <?php } ?>

                                            </div>
                                            <!--begin: Info-->
                                            <div class="flex-grow-1">
                                                <!--begin::Title-->
                                                <div class="d-flex justify-content-between flex-wrap mt-1">
                                                    <div class="d-flex mr-3">
                                                        <a href="#" class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3"><?php echo $data['first_name'];?></a>
                                                        <a href="#">
                                                            <i class="flaticon2-correct text-success font-size-h5 mr-2"></i>
                                                        </a>
                                                        <a href="<?php echo $data['soc_facebook']; ?>" target="_blank" class="btn waves-effect waves-light btn-facebook btn-icon-30 m-l-5">
                                                             <i class="icofont icofont-social-facebook"></i>
                                                          </a>
                                                          <a href="<?php echo ($data['soc_twitter'])!='' ? $_SESSION['employee']['soc_twitter'] : '#'; ?>" target="_blank" class="btn waves-effect waves-light btn-twitter btn-icon-30 m-l-5">
                                                             <i class="icofont icofont-social-twitter"></i>
                                                          </a>
                                                          <a href="<?php echo $data['soc_linkedin']; ?>" target="_blank" class="btn waves-effect waves-light btn-linkedin btn-icon-30 m-l-5">
                                                             <i class="icofont icofont-brand-linkedin "></i>
                                                          </a>
                                                    </div>
                                                    <div class="my-lg-0 my-3">

                                                         <?php if($_GET['Ridd']!="") { $nmm= ViewStatusValue($data['current_status'],'candidates');  ?>

                                                          <?php } ?>
                                                    </div>
                                                </div>
                                                <!--end::Title-->
                                                <!--begin::Content-->
                                                <div class="d-flex flex-wrap justify-content-between mt-1">
                                                    <div class="d-flex flex-column flex-grow-1 pr-8">
                                                        <div class="d-flex flex-wrap mb-4">
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                                                            <i class="ti-email f-14 text-primary"></i>&nbsp;<?php echo $data['email'];?></a>
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2">
                                                            <i class="ti-mobile f-14 text-primary"></i>&nbsp;<?php echo $data['phone'];?></a>
                                                            <a href="#" class="text-dark-50 text-hover-primary font-weight-bold">
                                                            <i class="ti-location-pin f-14 text-primary"></i><?php echo $data['current_location'];?></a>
                                                        </div>
                                                        <span class="font-weight-bold text-dark-50"><b>Address: </b><?php echo $data['billing_address']." ".$data['post_code'];?> </span>
                                                        <span class="font-weight-bold text-dark-50">

                                                        </span>
                                                    </div>

                                                    <div class="d-flex align-items-center w-200px w-sm-300px flex-column mt-3">
                                                        <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                                            <span class="fw-bold f-12 text-gray-400">Profile Compleation</span>
                                                            <span class="fw-bolder f-12"><?php echo $pp; ?>%</span>
                                                        </div>
                                                        <div class="progress progress-xs" style="width:200px;">
                                                            <div class="bg-success rounded h-5px" role="progressbar" style="width: <?php echo $pp; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end::Content-->
                                            </div>
                                            <!--end::Info-->
                                        </div>
                                        <!--end::Details-->
                                        <!--begin::Navs-->
                                        <div class="d-flex overflow-auto mt-2 h-55px">

                                            <ul class="nav md-tabs">
                                                <!--begin::Nav item-->
                                                    <?php
                                                    $display_array = array('','COM_INSERT_VENDORS','COM_LIST_VENDOR_RATE_CARDS','COM_LIST_MANPOWER_CONTRACTS','COM_LIST_JOB_POSTING_CANDIDATES','COM_LIST_CANDIDATES','COM_LIST_EMPLOYEES','COM_VIEW_PROJECT_TIMESHEETS');
                                                    $display_name = array('Home','Over View','Vendor Rate Cards','Work Order','Jobs','Candidates','Employees','Timesheets');
                                                    ?>

                                                <?php $option_name=""; foreach ($display_array as $key=>$disarray) {
                                                    $active =0;
                                                    if($display_id==$key){ $active =1; $option_name =$disarray;
}
                                                    if($_SESSION['user_type'] != 4 ){  ?>
                                                <li class="nav-item">
                                                    <a class="nav-link text-active-primary me-6 <?php if($active==1){ echo "active";
} ?>"  href="index.php?option=COM_INSERT_VENDOR&NI=<?php echo $key; ?>&Rid=<?php echo $_GET['Ridd']; ?>">
                                                        <?php echo $display_name[$key] ?></a>
                                                </li>
                                                <?php }else{
                                                    if(!in_array($disarray, $hr_only)){ ?>
                                                        <li class="nav-item">
                                                    <a class="nav-link text-active-primary me-6 <?php if($active==1){ echo "active";
} ?>"  href="index.php?option=COM_INSERT_VENDOR&NI=<?php echo $key; ?>&Rid=<?php echo $_GET['Ridd']; ?>">
                                                        <?php echo $display_name[$key] ?></a>

                                                </li>
                                                <?php } ?>
                                                <?php } ?>
                                                <?php } ?>

                                            </ul>
                                        </div>
                                        <!--begin::Navs-->
                                    </div>
                                </div>
                                <!--end::Navbar-->

                                <div class="card mb-5 p-3 j-pro">
                                    <?php $_GET['option'] = $option_name;
                    if($_GET['option']!=""){
                        include_once "includes/controller.php";
                        $dispage = str_replace("views/", '', $disptemp);
                        include_once $dispage;
                    }else{
                        include_once "vendorstaq_vendor_dashboarad.php";
                    }
                                    ?>
                                </div>


    </div>
</div>
<div id="kt_modal_KTDatatable_remote123" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup123">
        </div>
    </div>
</div>
