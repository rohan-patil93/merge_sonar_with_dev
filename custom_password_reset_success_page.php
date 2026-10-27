<?php 

session_start();
session_destroy ();

?>
<!DOCTYPE html>

<html lang="en">
<head>
  <title>Infistaq | Home</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="Admindek Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
  <meta name="keywords" content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
  <meta name="author" content="colorlib" />
  <!-- Favicon icon -->
  <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <!-- Google font-->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
  <!-- Required Fremwork -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
  <!-- waves.css -->
  <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all">
  <!-- feather icon -->
  <link rel="stylesheet" type="text/css" href="assets/icon/feather/css/feather.css">
  <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
  <!-- ico font -->
  <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
  <!-- Data Table Css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/extensions/buttons/css/buttons.dataTables.min.css">
  <!-- font-awesome-n -->
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome-n.min.css">
  <!-- Chartlist chart css -->
  <link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
  <link rel="stylesheet" type="text/css" href="assets/pages/advance-elements/css/bootstrap-datetimepicker.css">
  <!-- Date-range picker css  -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-daterangepicker/css/daterangepicker.css" />
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datedropper/css/datedropper.min.css" />
  <link rel="stylesheet" href="assets/bower_components/select2/css/select2.min.css" />
  <!-- Multi Select css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-multiselect/css/bootstrap-multiselect.css" />
  <link rel="stylesheet" type="text/css" href="assets/bower_components/multiselect/css/multi-select.css" />
  <!-- Color Picker css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/spectrum/css/spectrum.css" />
  <!-- Mini-color css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-minicolors/css/jquery.minicolors.css" />

  <!-- Switch component css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/switchery/css/switchery.min.css">

  <link rel="stylesheet" type="text/css" href="assets/css/component.css">
  <!-- animation nifty modal window effects css -->
  <link rel="stylesheet" type="text/css" href="assets/css/component.css">
  <!-- Calender css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.print.css" media='print'>
  <!-- star rating theme css -->    
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/bars-1to10.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/bars-horizontal.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/bars-movie.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/bars-pill.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/bars-reversed.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/bars-square.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/css-stars.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-bar-rating/css/fontawesome-stars-o.css">
  <!-- End star rating theme css -->
  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
  <!--forms-wizard css-->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery.steps/css/jquery.steps.css">
  <!-- Notification.css -->
  <link rel="stylesheet" type="text/css" href="assets/pages/notification/notification.css">

  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/widget.css">
  <link rel="stylesheet" type="text/css" href="assets/css/pages.css">
  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
  <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>    
  <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
  <script src="assets/js/sweetalert.js"></script>
</head>

<body class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
  <div class="loader-bg">
    <div class="loader-bar"></div>
  </div>
  <div id="pcoded" class="pcoded">
    <div class="pcoded-overlay-box"></div>
    <div class="pcoded-container navbar-wrapper">
      <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
          <div class="card-px text-center py-20 ">
            <img class="img-fluid m-t-20" src="assets/images/logos/logo-dark_2.png" alt="Theme-Logo" />
            <h2 class="f-40 fw-bolder m-b-30 mt-5">Password is changed</h2>
            <p class="text-muted f-16 fw-bold m-b-30">Your password is successfully changed. </br>Please Sign in to your account to start working.</p>
            <a  href="login.php"  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">
              <i class="la la-plus"></i>
              <?php echo "Sign In";  ?>
            </a>
          </div>
          <div class="text-center px-4">
            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Required Jquery -->
  <script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>
  <!-- waves js -->
  <script src="assets/pages/waves/js/waves.min.js"></script>
  <!-- jquery slimscroll js -->
  <script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
  <!-- Float Chart js -->
  <script src="assets/pages/chart/float/jquery.flot.js"></script>
  <script src="assets/pages/chart/float/jquery.flot.categories.js"></script>
  <script src="assets/pages/chart/float/curvedLines.js"></script>
  <script src="assets/pages/chart/float/jquery.flot.tooltip.min.js"></script>
  <!-- Chartlist charts -->
  <script src="assets/bower_components/chartist/js/chartist.js"></script>
  <!-- amchart js -->
  <script src="assets/pages/widget/amchart/amcharts.js"></script>
  <script src="assets/pages/widget/amchart/serial.js"></script>
  <script src="assets/pages/widget/amchart/light.js"></script>
  <!-- Custom js -->
  <script src="assets/js/pcoded.min.js"></script>

  <script type="text/javascript" src="assets/pages/dashboard/custom-dashboard.min.js"></script>
  <script type="text/javascript" src="assets/js/script.min.js"></script>
  <!--------------  data table   --------------->
  <!-- modernizr js -->
  <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
  <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>

  <!-- notification js -->
  <script type="text/javascript" src="assets/js/bootstrap-growl.min.js"></script>
  <script type="text/javascript" src="assets/pages/notification/notification.js"></script>
  <!-- data-table js -->
  <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
  <script src="assets/pages/data-table/js/jszip.min.js"></script>
  <script src="assets/pages/data-table/js/pdfmake.min.js"></script>
  <script src="assets/pages/data-table/js/vfs_fonts.js"></script>

  <script src="assets/pages/data-table/extensions/buttons/js/dataTables.buttons.min.js"></script>
  <script src="assets/pages/data-table/extensions/buttons/js/buttons.flash.min.js"></script>
  <script src="assets/pages/data-table/extensions/buttons/js/jszip.min.js"></script>
  <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
  <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
  <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
  <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
  <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
  <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

  <!-- Select 2 js -->
  <script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>
  <!-- Multiselect js -->
  <script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
  </script>
  <script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
  <script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
  <!-- Custom js -->
  <script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
  <script type="text/javascript" src="assets/pages/advance-elements/moment-with-locales.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
  <!-- Date-range picker js -->
  <script type="text/javascript" src="assets/bower_components/bootstrap-daterangepicker/js/daterangepicker.js"></script>
  <!-- Date-dropper js -->
  <script type="text/javascript" src="assets/bower_components/datedropper/js/datedropper.min.js"></script>
  <!-- Color picker js -->
  <script type="text/javascript" src="assets/bower_components/spectrum/js/spectrum.js"></script>
  <script type="text/javascript" src="assets/bower_components/jscolor/js/jscolor.js"></script>
  <!-- Mini-color js -->
  <script type="text/javascript" src="assets/bower_components/jquery-minicolors/js/jquery.minicolors.min.js"></script>
  <script type="text/javascript" src="assets/pages/advance-elements/custom-picker.js"></script>
  <!-- tinymce js -->
  <script src="assets/pages/wysiwyg-editor/js/tinymce.min.js"></script> -
  <!-- Custom js -->
  <script src="assets/pages/wysiwyg-editor/wysiwyg-editor.js"></script>
  <!-- Masking js -->
  <script src="assets/pages/form-masking/inputmask.js"></script>
  <script src="assets/pages/form-masking/jquery.inputmask.js"></script>
  <script src="assets/pages/form-masking/autoNumeric.js"></script>
  <script src="assets/pages/form-masking/form-mask.js"></script>
  <!-- Switch component js -->
  <script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
  <script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>

  <!-- modernizr js -->
  <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
  <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>

  <!-- notification js -->
  <script type="text/javascript" src="assets/js/bootstrap-growl.min.js"></script>
  <script type="text/javascript" src="assets/pages/notification/notification.js"></script>

  <!-- modalEffects js nifty modal window effects -->
  <script src="assets/assets/js/classie.js"></script>
  <script type="text/javascript" src="/assets/js/modalEffects.js"></script>
  <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>

  <!-- modalEffects js nifty modal window effects -->
  <script src="assets/js/classie.js"></script>
  <script type="text/javascript" src="assets/js/modalEffects.js"></script>



  <!-- calender js -->



  <script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
  <!-- Custom js -->
  <script type="text/javascript" src="assets/pages/full-calender/calendar.js" defer></script>




  <!-- Custom js -->
  <script type="text/javascript" src="assets/pages/chart/knob/knob-custom-chart.js"></script>
  <!-- amchart js -->
  <script src="assets/pages/widget/amchart/amcharts.js"></script>
  <script src="assets/pages/widget/amchart/gauge.js"></script>
  <script src="assets/pages/widget/amchart/serial.js"></script>
  <script src="assets/pages/widget/amchart/light.js"></script>
  <script src="assets/pages/widget/amchart/pie.min.js"></script>
  <script src="assets/pages/widget/amchart/ammap.min.js"></script>
  <script src="assets/pages/widget/amchart/usaLow.js"></script>
  <script type="text/javascript" src="assets/pages/widget/widget-statistic.js"></script>
  <script type="text/javascript" src="assets/bower_components/jquery-bar-rating/js/jquery.barrating.js"></script>
  <script type="text/javascript" src="assets/js/rating.js"></script>
  <script type="text/javascript" src="assets/js/dropzone.js"></script>

  <script type="text/javascript" src="assets/js/custom_file_uploading.js"></script>
  <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
  <script src="assets/bower_components/jquery.cookie/js/jquery.cookie.js"></script>
  <script src="assets/bower_components/jquery.steps/js/jquery.steps.js"></script>
  <script src="assets/bower_components/jquery-validation/js/jquery.validate.js"></script>
  <script src="assets/pages/forms-wizard-validation/form-wizard.js"></script>

  <script type="text/javascript" src="assets/js/custom.js"></script>
  <!-- <script type="text/javascript" src="assets/pages/widget/demo_dashboard4.js"></script> -->
  <script type="text/javascript" src="assets/pages/todo/todo.js"></script>

  <script src="assets/pages/ckeditor/ckeditor.js"></script>
  <script type="text/javascript" src="assets/pages/ckeditor/ckeditor-custom.js"></script>
  <script src="assets/js/custom_insert_latter.js"></script>
  <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
  <script src="assets/js/pcoded.min.js"></script>
  <!-- <script src="assets/js/vertical/vertical-layout.min.js"></script> -->
  <script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>


</body>

</html>
