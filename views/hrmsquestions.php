
<!DOCTYPE html>
<html lang="en" >
<!--begin::Head-->
<head><base href="">
   <meta charset="utf-8"/>
   <title>HRMS QUESTIONS</title>
   <meta name="description" content="Pricing table example"/>
   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

   <!--begin::Fonts-->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" integrity="sha384-16ODfSarOdCPp2mmN8IcwR5p70KlMF4LF6r0qnbRby6Suy7viriYE3jJxncuuNJr" crossorigin="anonymous"/>        <!--end::Fonts-->

   <link href="../assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css"/>
   <link href="../assets/plugins/custom/prismjs/prismjs.bundle.css" rel="stylesheet" type="text/css"/>
   <link href="../assets/css/style.bundle.css" rel="stylesheet" type="text/css"/>


   <link href="../assets/css/themes/layout/header/base/light.css" rel="stylesheet" type="text/css"/>
   <link href="../assets/css/themes/layout/header/menu/light.css" rel="stylesheet" type="text/css"/>
   <link href="../assets/css/themes/layout/brand/dark.css" rel="stylesheet" type="text/css"/>
   <link href="../assets/css/themes/layout/aside/dark.css" rel="stylesheet" type="text/css"/>


   <link rel="shortcut icon" href="../assets/media/logos/favicon.ico"/>

</head>
<!--end::Head-->

<!--begin::Body-->
<body  id="kt_body"  class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading"  >


<div class="container">
  <h2>HRMS QUESTIONS</h2>
  <br>

         <div class="row">
            <div class="col-4">
 <ul class="nav flex-column nav-pills">
                  <li class="nav-item mb-2">
                     <a class="nav-link active" id="home-tab-5" data-toggle="tab" href="#home-5" aria-controls="home" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="nav-icon">
                           <i class="flaticon2-chat-1"></i>
                       </span>
                       <span class="nav-text">General Info</span>
                   </a>
               </li>
                <li class="nav-item mb-2">
                     <a class="nav-link" id="attend-tab-5" data-toggle="tab" href="#attend-5" aria-controls="attend" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="nav-icon">
                           <i class="flaticon2-chat-1"></i>
                       </span>
                       <span class="nav-text">Attendance Management</span>
                   </a>
               </li>

       <li class="nav-item mb-2">
                     <a class="nav-link" id="leave-tab-5" data-toggle="tab" href="#leave-5" aria-controls="leave" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="nav-icon">
                           <i class="flaticon2-chat-1"></i>
                       </span>
                       <span class="nav-text">Leave Management</span>
                   </a>
               </li>
                      <li class="nav-item mb-2">
                     <a class="nav-link" id="payroll-tab-5" data-toggle="tab" href="#payroll-5" aria-controls="payroll" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="nav-icon">
                           <i class="flaticon2-chat-1"></i>
                       </span>
                       <span class="nav-text">Payroll Management</span>
                   </a>
               </li>


           </ul>
                 </div>

       <div class="col-8">
        <div class="tab-content" id="myTabContent5">
              <div class="tab-pane fade show active" id="home-5" role="tabpanel" aria-labelledby="home-tab-5">
<?php include_once 'hrmsquestions_sub_organization.php'; ?>
             </div>

              <div class="tab-pane fade show active" id="attend-5" role="tabpanel" aria-labelledby="attend-tab-5">

                <?php include_once 'hrmsquestions_sub_attendance.php'; ?>
             </div>

<div class="tab-pane fade show active" id="leave-5" role="tabpanel" aria-labelledby="leave-tab-5">
    <?php include_once 'hrmsquestions_sub_leavemanagement.php'; ?>
             </div>

              <div class="tab-pane fade show active" id="payroll-5" role="tabpanel" aria-labelledby="payroll-tab-5">
                <?php include_once 'hrmsquestions_sub_payroll.php'; ?>
             </div>


             <!-- end content -->
         </div>
     </div>
 </div>

</body>
</html>

  <script src="../assets/plugins/global/plugins.bundle.js"></script>
    <script src="../assets/plugins/custom/prismjs/prismjs.bundle.js"></script>
    <script src="assets/js/pages/myscrpit/hrmsqustions_sidebar.js"></script>


    <script src="../assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
    <!--end::Page Vendors-->

    <script src="../assets/js/pages/widgets.js"></script>

