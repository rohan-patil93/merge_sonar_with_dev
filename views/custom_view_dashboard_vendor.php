 <?php
 $vendordashboardurl = API."?mode=getvendordashboard&admin_type=".$_SESSION['admin_type'].".&admin_access_employee=".$_SESSION['admin_access_employee']."&vend_limit=3&vendor_id=".$_SESSION['vendor_id'];
 $vendordashboardres = callAPI($vendordashboardurl);
 $vendordashboardd = json_decode($vendordashboardres,1);
 $vendors = $vendordashboardd['vendors'];
 $holiday_list = array();
 $current_year = date("Y");
 $holidaywr["where"] = array('company_id'=> $_SESSION['company_id'],'DATE_FORMAT(holiday_date,"%Y")'=>$current_year);
 $holidaycon = urlencode(json_encode($holidaywr));
 $holidayurl = API."?mode=gettabledetailswithconditions&table=holidays&data=".$holidaycon;
 $holidaydata = callAPI($holidayurl);
 $holidaydata = json_decode($holidaydata,1);
 $total_holidays = count($holidaydata);
 ?>
 <script>
    function dashboardpopup(option,title){
      jQuery.ajax({
        url: "views/custom_view_dashboard_popup.php",
        data:'option='+option+"&title="+title,
        type: "POST",
        success:function(data){
            console.log(data);
            $('#popupm').html(data);
            $('#kt_modal_KTDatatable_remotem').modal("show");
        },
        error:function (){}
    });
  }
</script>
<div class="banner">
    <img src="assets/images/student-courses/banner-1.jpg" class="" alt="">
    <div class="banner-text">
        <h2>Start managing supplier invoices with ease!</h2>
        <p>Helps you in managing: Suppliers, Supplier bills and Payments. Bills Tak does this all through your modern Web Browser. Be assured to track your bills and payments from anywhere anytime.</p>
        <a href="index.php?option=COM_LIST_PURCHASE_INVOICES" class="btn-white b-radius-5 text-uppercase f-w-600">Browse Invoices</a>
    </div>
</div>
<div class="card">
    <div class="card-block">
        <div class="width-77">
            <div class="row align-items-center">
               <div class="d-flex align-items-center col-md" onclick="dashboardpopup('invice_cnt','Total Number Of Invoices')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">

                <div class="rounded-circle bg-primary p-3 pt-3 m-r-10 userimageround">
                 <i class="ti-files f-20"></i>
             </div>
             <div class="flex">
                <a href="#"><div class="card-title f-w-600 f-16"><?php echo count((array)$vendordashboardd['invice_cnt'])."+ Invoices"?></div>
                    <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Invoices</p>
                </a>
            </div>
        </div>
        <div class="d-flex align-items-center col-md" onclick="dashboardpopup('approve_cnt','Total Number Of Invoices Cleared')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
            <div class="rounded-circle bg-primary p-3 pt-3  m-r-10 userimageround">
             <i class="ti-file f-20"></i>
         </div>
         <div class="flex">
           <a href="#">
            <div class="card-title f-w-600 f-16"><?php echo count((array)$vendordashboardd['approve_cnt'])."+ Invoices"?></div>
            <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Invoices Cleared</p>
        </a>
    </div>
</div>
<div class="d-flex align-items-center col-md" onclick="dashboardpopup('pending_list','Total Number Of Invoices Pendding')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
    <div class="rounded-circle bg-primary p-3 pt-3 m-r-10 userimageround">
        <i class="ti-file f-20"></i>
    </div>
    <div class="flex">
       <a href="#">
        <div class="card-title f-w-600 f-16"><?php echo count((array)$vendordashboardd['pending_list'])."+ Invoices"?></div>
        <p class="card-subtitle text-muted mt-1 f-12"> Total Number Of Invoices Pendding</p>
    </a>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <?php if($notificationlist != ""){ ?>
                <div class="page-body border-bottom">
                    <div class="width-77">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="d-flex align-items-center col-md">
                                        <div class="flex" style="width:220px">
                                            <a href="#" class="card-title f-16 f-w-600"><?php echo $recent_notification['Notification_Message']; ?></a>
                                            <p class="card-subtitle text-muted f-12 mt-1"> <?php echo $recent_notification['Notification_Type']; ?></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center col-md">
                                        <i class="fa fa-envelope text-muted f-14 m-r-10"></i>
                                        <div class="flex">
                                            <p href="#" class="card-title f-14 f-w-600">INVISION</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center col-md">
                                        <div class="flex">
                                            <p class="text-muted mb-0"><?php echo $recent_notification['Notification_DateTime']; ?></p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center col-md cursor-p">
                                        <div class="flex">
                                            <div class="dropdown-primary dropdown">
                                                <div class="displayChatbox " data-bs-toggle="dropdown">
                                                    <span class="badge bg-c-red">More +<?php echo count($notificationlist);?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="page-body mt-4 border-bottom">
                <div class="width-77">
                    <div class="row">
                       <div class="col-lg-6 col-xl-6 col-sm-6 col-12">
                           <div class="card social-card custom_hover_card" style="min-height: 365px;">
                              <div class="card-body text-center">
                                 <div class="text-facebook m-b-20"><i class="ti-announcement "></i></div>
                                 <h3 class="text-facebook f-w-700">Announcements</h3>
                                 <?php
                                 $anouncement =  API."?mode=togetlatestannouncement";
                                 $anouncement_list = callAPI($anouncement);
                                 $anouncement_list = json_decode($anouncement_list,1);
                                 ?>
                                 <p class="f-16"><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
                                 <p class="m-b-0 m-t-15"><a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-info waves-effect waves-light"><?php echo $anouncement_list['button_label']; ?></a></p>
                             </div>
                         </div>
                     </div>
                     <div class="col-lg-6 col-xl-6 col-sm-6 col-12">

                       <div class="card new-cust-card custom_hover_card">
                          <div class="card-header">
                             <h5>Holidays List</h5> <span class="form-label label label-success"><?php echo $total_holidays; ?></span>
                         </div>
                         <div class="card-block custom-scroll" style=" height: 300px;">
                             <?php
                             if(!empty($holidaydata)) {
                                 foreach ($holidaydata as  $holiday) {
                                    $Notification_Message = $holiday['holiday_name'];
                                    $Notification_DateTime = $holiday['holiday_date'];
                                    ?>
                                    <div class="align-middle m-b-35">
                                       <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($Notification_Message); ?></span>
                                       <div class="d-inline-block">
                                          <a href="#!"><h6 class="f-14"><?php echo $Notification_Message; ?></h6></a>
                                          <p class="text-muted m-b-0"><?php echo $Notification_DateTime; ?></p>
                                      </div>
                                  </div>
                              <?php } ?>
                              <?php } else {
                                  echo "<h4><i class='fa fa-warning'></i> No Holidays Available</h4>";
                              } ?>
                          </div>
                      </div>
                  </div>
                  <div class="col-xl-12 col-md-12" >
                    <div class="card table-card">
                       <div class="card-header">
                          <h5>Pending  Invoices</h5>
                          <div class="card-header-right table-card-header ">
                              <a target="_blank" href="index.php?option=COM_LIST_TABLE_FILTERS&TBALE_NAME=purchase_invoices" class="btn btn-primary mb-2 desktop-view">
                              History </a>
                          </div>
                      </div>
                      <div class="card-block">
                          <div class="custom-scroll" style="height: 270px;">
                             <table class="table table-hover m-b-0">
                                <thead>
                                   <tr>
                                      <th scope="col">#</th>
                                      <th scope="col">Vendor</th>
                                      <th scope="col">Invoices Number</th>
                                      <th scope="col">Date</th>
                                      <th scope="col">Price</th>
                                      <th scope="col">Status</th>
                                      <th scope="col">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                <?php $mq = 1; if(count($vendordashboardd['pending_list'])!='0'){ foreach($vendordashboardd['pending_list'] as $pending_list){
                                    $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                                    $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                                    ?>
                                    <tr>
                                      <td><?php echo $mq; ?></td>
                                      <td><?php echo $vendors[$pending_list['party_id']]['display_name']; ?></td>
                                      <td><?php echo $pending_list['transaction_number']; ?></td>
                                      <td><?php echo $pending_list['transaction_date']; ?></td>
                                      <td><?php echo $pending_list['amount']; ?></td>
                                      <td>
                                          <span class="form-label label " style="background-color: <?php echo $stausresultpending_list['background_color'][0] == '#'?$stausresultpending_list['background_color']:"#".$stausresultpending_list['background_color']; ?>; color:<?php echo $stausresultpending_list['color'][0] =='#'?$stausresultpending_list['color']:'#'.$stausresultpending_list['color']; ?>"><?php echo $stausresultpending_list['name']?></span>
                                      </td>
                                      <td>

                                         <a href="index.php?option=COM_INSERT_PURCHASE_INVOICES&Rid=<?php echo $ridrecengtbils; ?>">
                                            <i class="fa fa-edit f-w-600 f-16 m-r-15 text-c-green"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php  $mq++;
}
}else{

                                  echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills Created</h4></td></tr>";
                              } ?>

                          </tbody>
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
</div>
