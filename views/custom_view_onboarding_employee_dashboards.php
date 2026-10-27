<?php
$_SESSION['apply_js']['multiselest'] = '1';
$document_category_id = 1;
$rids =  encodeId($_GET['Rid']);
$rid = urldecode($rids);
$empidd = explode(':', $rid);
$empiid = $empidd['1'];
$employee_id = $empiid !=""?$empiid:$_SESSION['emp_id'];
$finance_tax_year = $_GET['FY']!=""?$_GET['FY']:date("Y");
$url = API."?mode=getemployeeonboardingtask&employee_id=".$employee_id."&admin_type=".$_SESSION['admin_type']."&document_category_id=".$document_category_id;
$api = callAPI($url);
$result = json_decode($api,1);
$stausresult = ViewStatusData();
$task_cnt = $result['task_cnt'];
$employees = $result['employees'];
  ?>
  <script type="text/javascript">
    function DynamicPagerefrash(id=""){
      var employee_id = '<?php echo $employee_id; ?>';
      var option = $("#ajaxrefrashpagename").val();
      var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamicpageshow').html(htmloader);
    jQuery.ajax({
      url: "views/custom_inbox_page_redirection.php",
      data:'option='+option+"&rid="+id+"&ajaxpage=Y&employee_id="+employee_id,
      type: "POST",
      success:function(htmlRows){
        $('#dynamicpageshow').html(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-basic-multiple").select2();
        $(".js-example-basic-multiple-limit_custom").select2();
            $(".js-example-disabled-results").select2();
        $(".datepicker" ).datepicker({
          showOn: "both",
          buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
          dateFormat: DateFormate,
          changeMonth: true,
          changeYear: true
        });
      },
      error:function (){}
    });
  }

  function loadDynamicPage(option,id="",tabid="",subtabid=""){
      console.log(option);
      var employee_id = '<?php echo $employee_id; ?>';
      $(".removealltab").removeClass("active");
      $(".activetab_"+tabid).addClass("active");
      if(subtabid !=""){
        $(".activesubtab_"+subtabid).addClass("active");
      }
      var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $('#dynamicpageshow').html(htmloader);
      jQuery.ajax({
      url: "views/custom_inbox_page_redirection.php",
      data:'option='+option+"&rid="+id+"&ajaxpage=Y&employee_id="+employee_id,
      type: "POST",
      success:function(htmlRows){
        $('#dynamicpageshow').html(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-basic-multiple").select2();
        $(".js-example-basic-multiple-limit_custom").select2();
        $(".js-example-disabled-results").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
        $(".datepicker" ).datepicker({
          showOn: "both",
          buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
          dateFormat: DateFormate,
          changeMonth: true,
          changeYear: true
        });

      //           html: true,
      },
      error:function (){}
    });
    }
  window.addEventListener("load", function() {
     getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab');
  });

function getAjaxDynamicPage(option,divid,pagetype="",rid=""){
  console.log(rid);
  updateUrlParameter('T1',divid);
  var finance_tax_year = $("#finance_tax_year").val();
  updateUrlParameter('FY',finance_tax_year);
  var transaction_table = 'employees';
  var transaction_id = '<?php echo $employee_id; ?>';
  var currentUrl = window.location.href;
  var urlParts = currentUrl.split('?');
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#"+divid).html(loader);
    $.ajax({
        url: "views/"+option+".php",
        data:'option='+option+'&ajaxpage=Y&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&finance_tax_year='+finance_tax_year+'&'+urlParts['1'],
        type: "POST",
        async: true,
        success:function(data){
            $('#'+divid).html(data);
            $('[data-bs-toggle="tooltip"]').tooltip();
            if(rid !=""){
              getCheckList(rid);
          }
        },
        error:function (){}
    });
}

function SubmitForApprovalTask(){
  var finance_tax_year = $("#finance_tax_year").val();
  var transaction_table = 'employees';
  var transaction_id = '<?php echo $employee_id; ?>';
  jQuery.ajax({
      url: "views/custom_view_onboarding_employee_task_for_approved.php",
      data:'transaction_table='+transaction_table+"&transaction_id="+transaction_id+"&ajaxcall=Y&finance_tax_year="+finance_tax_year,
      type: "POST",
      success:function(data){
        $('#dynamic_popup11').html(data);
        $('#kt_modal_dynamic_popup2').modal("show");
      }
  });
}

function UpdateSendVerificationProfileStatus(){
  swal({
    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
    text: '<?php echo $_SESSION['error_message']['9123']; ?>',
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn-danger",
    confirmButtonText: "Submit",
    cancelButtonText: "Cancel",
    closeOnConfirm: true,
    closeOnCancel: true
  },
  function(isConfirm) {
    if (isConfirm) {
      $("#popsubmitforreview_btn").text("Please Wait........");
      $("#popsubmitforreview_btn").addClass("disabled");
      var finance_tax_year = $("#finance_tax_year").val();
      var transaction_table = 'employees';
      var transaction_id = '<?php echo $employee_id; ?>';
        $.ajax({
            url: "ajax.php",
            data:'page=updatesendverificationdocumentsem&transaction_table='+transaction_table+'&transaction_id='+transaction_id+'&finance_tax_year='+finance_tax_year,
            type: "POST",
            async: true,
            success:function(data){
              console.log(data);
              window.location.reload();
            },
            error:function (){}
        });
    }
  });
}
  </script>
  <div class="">
    <div class="container-fluid">
      <div class="">
        <div class="card d-flex flex-wrap flex-sm-nowrap p-10 mb-1 pt-4 pb-4 mt-3 c-p-0">
          <div class="profile-open" onClick="divFunction()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-user"></i></div>
          <div class="profile-close">
            <div class="row col-lg-12">
            <div class="col-lg-6 col-md-6 col-sm-12 col-12 p-r-10 d-r-n" style="border-right: 3px dashed #ccc;">
              <div class="d-flex">
                <div class="flex-shrink-0 m-r-30 m-l-25 mt-lg-0 mt-3">
                  <?php if($employees['image']!=""){ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $employees['image']; ?>" class="img-120 " alt="Details" onerror="this.src='uploads/all/no-image.jpg';"/>
                    </div>

                  <?php }else{ ?>
                    <div class="symbol symbol-50 symbol-lg-120">
                      <img src="../uploads/all/<?php echo $employees['gender'].".png"; ?>" class="img-120 " alt="Details" onerror="this.src='uploads/all/no-image.jpg';"/>
                    </div>
                  <?php } ?>
                  <div class="d-block mt-1" style="margin:-16px 0 0 0;">
                    <a href="<?php echo $employees['soc_facebook']; ?>" target="_blank" class="btn waves-effect waves-light btn-facebook btn-icon-30 m-l-5">
                      <i class="icofont icofont-social-facebook"></i>
                    </a>
                    <a href="<?php echo ($employees['soc_twitter'])!='' ? $_SESSION['employee']['soc_twitter'] : '#'; ?>" target="_blank" class="btn waves-effect waves-light btn-twitter btn-icon-30 m-l-5">
                      <i class="icofont icofont-social-twitter"></i>
                    </a>
                    <a href="<?php echo $employees['soc_linkedin']; ?>" target="_blank" class="btn waves-effect waves-light btn-linkedin btn-icon-30 m-l-5">
                      <i class="icofont icofont-brand-linkedin "></i>
                    </a>
                  </div>
                  <div class="d-flex align-items-center w-100px w-sm-120px flex-column mt-3 ">

                    <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                      <span class="f-12 f-w-600">Profile </span>
                      <span class="f-12 f-w-600"><?php echo customNanAndEmptyValidation($pp); ?>%</span>
                    </div>
                    <div class="col-md-12">
                      <div class="progress progress-xs">
                        <div class="progress-bar progress-bar-success" role="progressbar" style="width:  <?php echo $pp; ?>%;" aria-valuenow="<?php echo $pp; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                      </div>
                    </div>

                  </div>
                </div>
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between flex-wrap mt-1">
                    <div class="d-flex mr-3">
                      <span class="text-dark-75 text-hover-primary font-size-h5 font-weight-bold mr-3 f-16 f-w-600"><?php echo $employees['display_name'];?></span>
                    </div>
                  </div>
            <div class="">
              <div class="d-flex flex-wrap justify-content-between mt-1 ">
                <div class="d-flex flex-column flex-grow-1 pr-8  ">
                  <div class="d-flex flex-wrap mb-2 mt-2">
                    <?php if($employees['email_id']!=""){ ?>
                    <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                      <i class="ti-email f-14 text-primary"></i>&nbsp; <?php echo $employees['email_id'];?></a>
                      <?php }
if($desig['designation_name']!=""){ ?>
                      <a href="#" class="text-dark-50 text-hover-primary font-weight-bold mr-lg-8 mr-5 mb-lg-0 mb-2 m-l-5">
                        <i class="ti-user f-14 text-primary"></i>&nbsp;<?php echo $desig['designation_name'];?></a>
                        <?php }
if($location['location_name']!=""){ ?>
                        <a href="#" class="text-dark-50 text-hover-primary font-weight-bold m-l-5">
                          <i class="ti-location-pin f-14 text-primary"></i>&nbsp;<?php echo $location['location_name'];?></a> <?php
} ?>
                        </div>

                        <?php if($_SESSION['employee']['about_me']!=""){ ?>
                          <span class="font-weight-bold text-dark-50 mt-3"><b>About Me : </b><?php echo  $_SESSION['employee']['about_me'];?></span>
                        <?php } ?>
                      </div>
                      <input type="hidden" id="location_id" value="<?php echo $employees['location_id']; ?>">
                      <input type="hidden" id="start_date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                  </div>
                </div> <!-- 11 -->
              </div><!-- 22 -->
            </div><!-- 23 -->
            <div class="col-lg-6 col-md-6 col-sm-12 col-12 mt-2">

              <?php if($employees['phone']!=""){?>
                <div class="row mb-2">
                  <div class=" col-sm-1  text-right">
                    <i class="ti-mobile f-16 text-primary"></i>
                  </div>
                  <span class="col-sm-3  f-w-600">Phone Number</span>
                  <span class="col-sm-1">:</span>
                  <div class="col-sm-6">
                    <span ><?php echo $employees['phone']; ?></span>
                  </div>
                </div>
              <?php }
if($employees['date_of_birth']!=""){?>
                <div class="row mb-2">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-1 text-right">
                    <i class="ti-gift f-16 text-primary"></i>
                  </div>
                  <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Birthday</span>
                  <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <span ><?php echo fromDate($employees['date_of_birth']); ?></span>
                  </div>
                </div>
              <?php }
if($employees['address']!=""){?>
                <div class="row mb-2">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-1 text-right">
                    <i class="ti-home f-16 text-primary"></i>
                  </div>
                  <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Address</span>
                  <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <span ><?php echo $employees['address']; ?></span>
                  </div>
                </div>
              <?php }
if($employees['gender']!=""){?>
                <div class="row mb-2">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-1 f-20 text-right">

                    <i class="fa fa-venus-mars f-16 text-primary"></i>
                  </div>
                  <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Gender</span>
                  <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <span ><?php echo $employees['gender']=="F"?"Female":"Male"; ?></span>
                  </div>
                </div>
              <?php }
if($employees['gender']!=""){?>
                <div class="row ">
                  <div class="col-lg-1 col-md-1 col-sm-1 col-1 text-right">
                      <i class="ti-user text-primary f-16"></i>
                  </div>
                  <span class="col-lg-3 col-md-3 col-sm-3 col-3 f-w-600">Reports To</span>
                  <span class="col-lg-1 col-md-1 col-sm-1 col-1">:</span>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <span ><?php echo $_SESSION['users'][$_SESSION['user']['reports_to']]; ?></span>
                  </div>
                </div>
              <?php } ?>
                <div class="mt-2 row">
                  <div class="row mt-3">
                      <div class="col-2 text-center">
                          <div class="s-caption  text-primary"><i class="ti-pin f-20"></i></div>
                          <div class="s-cont d-inline-block">
                              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($task_cnt['total_task_cnt']); ?></h5>
                              <p class="m-b-0 f-12">Total Task</p>
                          </div>
                      </div>
                      <div class="col-2 b-l-default text-center">
                          <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                          <div class="s-cont d-inline-block">
                              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($task_cnt['yet_to_start_cnt']); ?></h5>
                              <p class="m-b-0 f-12">Yet To Start</p>
                          </div>
                      </div>
                      <div class="col-2 b-l-default text-center">
                          <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                          <div class="s-cont d-inline-block">
                              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($task_cnt['inprogress_cnt']); ?></h5>
                              <p class="m-b-0  f-12">In Progress</p>
                          </div>
                      </div>
                      <div class="col-2 b-l-default text-center">
                          <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                          <div class="s-cont d-inline-block">
                              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($task_cnt['review_cnt']); ?></h5>
                              <p class="m-b-0 f-12">Review</p>
                          </div>
                      </div>
                      <div class="col-2 b-l-default text-center">
                          <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                          <div class="s-cont d-inline-block">
                              <h5 class="f-w-700 m-b-0"><?php echo customNanAndEmptyValidation($task_cnt['approve_cnt']); ?></h5>
                              <p class="m-b-0 f-12">Completed</p>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
      </div>

        <!----End --->


    <div class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-7">
            <ul class="nav md-tabs infisuite_tab mt-2 ml-2">

              <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#task_tab" role="tab" onclick="getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab')">Task</a>
                <div class="slide"></div>
              </li>
            </ul>
          </div>
          <div class="col-lg-3">

          </div>
          <div class="col-lg-2">
              <select aria-label="Finance Tax Year" class ="form-control form-control-lg form-select js-example-basic-single" id="finance_tax_year" onchange="getAjaxDynamicPage('custom_insert_transaction_activity_schedules','task_tab')">
                  <?php for($i=date("Y")-10; $i <=date("Y"); $i++){ ?>
                      <option value="<?php echo $i; ?>" <?php echo ($i == $finance_tax_year)?"selected":""; ?>>
                      <?php echo "Apr ".$i." - Mar ".($i+1); ?>
                      </option>
                <?php } ?>
              </select>
          </div>
        </div>
      </div>
    </div>

    <div class="tab-content card-block p-0">

      <div class="tab-pane active " id="task_tab" role="tabpanel">
      </div>
    </div>

      </div>
    </div>
  </div>
</div>
        <!--end::Navbar-->

        <div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-xl" >
            <div class="modal-content"  id="popup2">
            </div>
          </div>
        </div>

<?php if($_POST['ajaxpage'] =="Y"){ ?>
  <script src="assets/bower_components/jquery.cookie/js/jquery.cookie.js"></script>
  <script src="assets/bower_components/jquery.steps/js/jquery.steps.js"></script>
  <script src="assets/bower_components/jquery-validation/js/jquery.validate.js"></script>
  <script src="assets/pages/forms-wizard-validation/form-wizard.js"></script>
  <?php } ?>
