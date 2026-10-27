<?php 
  include('dbconfig.php');
  include('comm.php');
  if(isset($_POST['btnsubmited'])){
        $url = API."?mode=insertcandidateschedule&method=post&data_name=".base64_decode($_POST['softversion']);
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response,1);
       	?>
       	<script type="text/javascript">
			document.onreadystatechange = function () {
			  if (document.readyState == "complete") {
			    var mesg = "Time Slot Succesfully Book";
			    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',mesg,'');
			  } 
			}
		</script>
       	<?php
  }
  $candidate_details= json_decode(base64_decode($_GET['OPTION']),1);
  $timezonelist = array();
  // $tzlist = DateTimeZone::listIdentifiers();
$url = API."?mode=getcandidateschedule&method=post";
$getresponse = file_post_contents($url, $candidate_details, 'update');
$getreturnresponse = json_decode($getresponse,1);
$getresponse = $getreturnresponse['candidate_interview_schedule'];
$countries = $getreturnresponse['countries'];

  foreach($countries as $tzlist1){
  		$timezonelists = json_decode($tzlist1['time_zones'],1);
  	foreach($timezonelists as $timezonelists1){
  		$explode = explode("/",$timezonelists1['zoneName']);
  		$timezonelist[$explode[0]][$timezonelists1['zoneName']] = gettTimeZone('',$timezonelists1['zoneName']);
	}
  }
  //echo "<pre>";print_r($getreturnresponse['candidate_interview_schedule_all']); echo "</pre>";


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Recruitstaq | Candidate Schedule</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords" content="bootstrap, bootstrap admin template, admin theme, admin dashboard, dashboard template, admin template, responsive" />
    <meta name="author" content="colorlib" />
    <!-- Favicon icon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <!-- Google font-->     
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
    <!-- waves.css -->
    <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all"><!-- feather icon --> <link rel="stylesheet" type="text/css" href="assets/icon/feather/css/feather.css">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
<link rel="stylesheet" href="assets/bower_components/select2/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-multiselect/css/bootstrap-multiselect.css" />
<link rel="stylesheet" type="text/css" href="assets/bower_components/multiselect/css/multi-select.css" />
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"><link rel="stylesheet" type="text/css" href="assets/css/pages.css">
    <link rel="stylesheet" type="text/css" href="assets/css/animate.css">

    <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
   <script type="text/javascript">
      function timeSelection(time){
          $(".timeempty").removeClass("label-warning");
          $(".timeempty").addClass("label-primary");
          $(".time-"+time).addClass("label-warning");
          $(".emptyicon").html('');
          $(".icon-"+time).html('<i class="fa fa-check"></i>');
          $("#time").val(time);
          $('#btnsubmited').prop('disabled', false);
      }
      function selectTimezone(){
        var schedule_date = $("#schedule_date").val();
        var time_zone = $("#time_zone").val();
        var Rid = $("#Rid").val();
        var option = $("#option").val();
        
        $('#btnsubmited').prop('disabled', true);
        $.ajax({
            url: "views/custom_list_candidate_timezone_ajax.php",
            data: "page=gettimezonetimes&timezone="+time_zone+"&schedule_date="+schedule_date+"&Rid="+Rid+"&option="+option,
            type: "POST",
            success:function(data){
              console.log(data);
              var respons = JSON.parse(data);
              if(respons.officetimeon == 0){
                  $("#timeslectiondiv").html(respons.timezonelabel);
              }else{
                  $("#timeslectiondiv").html("<p>Unfortunately, we regret to inform you that the interview slot you were hoping for is not currently available.Unfortunately, we regret to inform you that the interview slot you were hoping for is not currently available.</p>");
              }
            }
        }); 

      }
   </script>
 </head>
 <body class="" style="background-image:url('assets/images/sign-in/bg4.jpg'); background-size: cover;">
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-bar"></div>
  </div>

  <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-lg-1"></div>
      <div class="col-lg-10 creative pt-2">
        <div class="width-70 mt-5">
          <div class="card">
            <div class="card-body p-l-50 p-r-50 p-b-50">
                <div class="text-center">
                   <img class="img-200" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">  
                </div>
                <?php if($getresponse['current_status'] == ST_ACTIVE){ ?>
                <form class="md-float-material form-material m-t-50" action="" method="post"> 
                      <div class="row mt-5">
                          <div class="col-lg-4">
                              <h4><i class="ti-briefcase text-success"></i> <?php echo $candidate_details['Job_title']; ?></h4>
                              <div>
                                  <span class="d-block">Hi <span class="f-w-700"><?php echo $candidate_details['candidate_display_nama']; ?></span></span>
                              </div>
                              <div class="mt-2">
                                  <span class="d-block">Recruiting Manager</span>
                                  <span class="f-w-700"><?php echo $candidate_details['rm_name']; ?></span>
                              </div>
                               <div class="mt-2">
                                  <i class="ti-alarm-clock"></i>
                                  <span class="f-w-700"><?php echo CANDIDATE_TIMESLOT_MINUTS; ?> Minutes</span>
                              </div>  
                              <div class="mt-2">
                              	<?php if($getresponse['schedule_type'] == "1"){ ?>
                                  <i class="ti-mobile"></i>
                                  <span class="f-w-700">Telephonic Interview</span>
                              	<?php }else{ ?>
                              		<i class="ti-user"></i>
                                  	<span class="f-w-700">Face To Tace Interview</span>
                              	<?php } ?>
                              </div>
                          </div>
                          <div class="col-lg-8">
                              <div class="form-group">
                                  <label class="form-label d-block">Schedule Date</label>
                                  <input class="form-control datepicker" type="text"  id="schedule_date" name="schedule_date" onchange="selectTimezone()" required>
                              </div>
                              <div class="form-group">
                                  <label class="form-label d-block">Time zone</label>
                                  <select class="form-control js-example-basic-single" name="timezone" id="time_zone" onchange="selectTimezone()" required>
                                    <?php foreach($timezonelist as $timezonekey=>$timezonedetail){ ?>
										
										<optgroup label="<?php echo $timezonekey; ?>" >
										    <?php foreach($timezonedetail as $timezonekey1=>$timezonedetail1){ ?>
										    	<option value="<?php echo $timezonekey1; ?>" ><?php echo $timezonekey1." (".$timezonedetail1['timezonetime12'].")"; ?></option>
										    <?php } ?>
										</optgroup>
                                    <?php } ?>
                                  </select>
                              </div>
                              <div class="form-group" id="timeslectiondiv">
                              </div>
                              <div class="form-group mt-5"> 
                                <input type="hidden" name="time" id="time" required>
                                <input type="hidden" name="Rid" id="Rid" value="<?php echo $candidate_details['Rid']; ?>" >
                                <input type="hidden" name="softversion" id="softversion" value="<?php echo base64_encode($candidate_details['dbname']); ?>" >
                                <input type="hidden" name="option" id="option" value="<?php echo $_GET['OPTION']; ?>">
                                <button class="btn btn-primary waves-effect waves-light" name="btnsubmited" id="btnsubmited" disabled>Submit Schedule</button>
                              </div>
                          </div>
                      </div>

                </form>
            <?php }else{ ?>
            	<div class="card-px text-center py-20 m-t-30">
					<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Already Scheduled Time</p>	 
					<div class="text-center px-4">
						<img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
					</div>	 
				</div>
            <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-1"></div>
    </div>
  </div>



  <script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
  <script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>
  <!-- waves js -->
  <script src="assets/pages/waves/js/waves.min.js"></script>
  <!-- jquery slimscroll js -->
  <script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
  <!-- modernizr js -->
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js" ></script>
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js" ></script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js" ></script>
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js" ></script>

<script>
           var DateFormate = "dd-mm-yy";
       </script>

<!-- Date-range picker js -->
<script type="text/javascript" src="assets/bower_components/bootstrap-daterangepicker/js/daterangepicker.js"></script>
<!-- Date-dropper js -->
<script type="text/javascript" src="assets/bower_components/datedropper/js/datedropper.min.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/custom-picker.js"></script>
  <!-- Date picker js -->
  <script type="text/javascript" src="assets/js/custom_datepicker.js"></script>


  <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
  <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
  <script type="text/javascript" src="assets/js/common-pages.js"></script>
  <script type="text/javascript" src="assets/js/script.js"></script>
  <script>
   function registerform(){
    $(".forgotpasswordon").hide();
    $(".custom-login-on").hide();
    $(".regsterform").show('slide');
  }
  function loginpagedisplay(){
   $(".regsterform").hide();
   $(".forgotpasswordon").hide();
   $(".custom-login-on").show('slide');
 }
 function fergotpassworddisplay(){
  $(".custom-login-on").hide();
  $(".regsterform").hide();
  $(".forgotpasswordon").show('slide');

}

</script>

</body>

</html>
