<?php
$_SESSION['apply_js']['multiselest'] = 1;
  $_SESSION['apply_js']['multiselest'] = 1;
  $_SESSION['apply_js']['rangeslide'] = 1;
    $url = API."?mode=getvendors";
    $api = callAPI($url);
    $result = json_decode($api,1);
    $locations = $result['locations'];
    foreach((array)$locations as $location){
      $locationslist[$location['id']] = $location['location_name'];
    }
    $job_titles = $result['job_titles'];
    $candidates = $result['candidates'];
    $email_logs = $result['email_logs'];

$allpermision = $_SESSION['role_permission']['candidates'];
if($allpermision['permission_view'] == '1'){$permission_listbtn = "disabled";
}
if($allpermision['permission_insert'] == '1'){$insert_permissionbtn = "disabled";
}
if($allpermision['permission_import'] == '1'){$permission_importbtn = "disabled";
}
if($allpermision['permission_update'] == '1'){$edit_permissionbtn = "disabled";
}
if($allpermision['permission_approval'] == '1'){$approval_permissionbtn = "disabled";
}

foreach((array)$result['genders'] as $gender){
  $genders[$gender['genc_detail_code']] = $gender['genc_detail_name'];
}

foreach((array)$result['jobtype'] as $jobtypes){
  $jobtype[$jobtypes['genc_detail_code']] = $jobtypes['genc_detail_name'];
}
foreach((array)$result['job_posting_candidates'] as $jpc){
  $jobpostingcandidates[$jpc['candidate_id']] = $jpc['cnt'];
}

    $jobttlecnt = array();
    $gendercnt = array();
    $jobtypecnt = array();
    $skillist = array();
    foreach((array)$candidates as $candidate1){
      $jobttlecnt[$candidate1['job_tittle_id']][] = $candidate1['job_tittle_id'];
      $gendercnt[$candidate1['gender']][] = $candidate1['gender'];
      $jobtypecnt[$candidate1['job_type']][] = $candidate1['job_type'];
      if($candidate1['skills'] !=""){
        $skilsets = json_decode($candidate1['skills'],1);
        foreach((array)$skilsets as $skilsets){
          $skillist[$skilsets] =$skilsets;
        }
      }

    }
?>
<script type="text/javascript">
function vendorhistories(vendor_id){
    console.log("vendor_id:"+vendor_id);
    jQuery.ajax({
    url: "views/custom_view_vendor_feedback_response_popup.php",
    data:'vendor_id='+vendor_id,
    type: "POST",
    success:function(data){
      $('#dynamic_popup11').html(data);
      $('#kt_modal_dynamic_popup2').modal("show");
    },
    error:function (){}
  });
}
function appliedvendorfeedbackold(vendor_id){
    console.log("vendor_id:"+vendor_id);
    jQuery.ajax({
    url: "views/custom_view_vendor_feedback_questions_popup.php",
    data:'vendor_id='+vendor_id,
    type: "POST",
    success:function(data){
      $('#dynamic_popup11').html(data);
      $('#kt_modal_dynamic_popup2').modal("show");
    },
    error:function (){}
  });
}
function appliedvendorfeedback(vendor_id){
    console.log("vendor_id:"+vendor_id);
    jQuery.ajax({
    url: "views/custom_view_apply_list_vendor_feedback_popup.php",
    data:'vendor_id='+vendor_id,
    type: "POST",
    success:function(data){
      $('#dynamic_popup11').html(data);
      $('#kt_modal_dynamic_popup2').modal("show");
    },
    error:function (){}
  });
}
function filterapplay(reset=""){
    var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
    var parent_option  = '<?php echo $_GET['option']; ?>';
    insertadittrails(parent_option,'COM_LIST_CANDIDATES_TABLE',extra_paramiter);
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#formview").html(loader);
    if(reset !="Y"){
      var postarray = $("#filterfrom").serialize();
    }
    $.ajax({
        url: "views/custom_list_vendor_table.php",
        type: "POST",
        data: "filter=Y&"+postarray,
        success: function(response){
            $("#formview").html(response);
        }
    });
}
function selectAlphabet(alpha){
  $(".activealpha").removeClass('active');
  $("#activealpha"+alpha).addClass("active");
  $("#display_name_alphabet").val(alpha);
  filterapplay();
}
function selectTableType(type){
  $(".btnactive").removeClass('active');
  $("#btnactive"+type).addClass("active");

  $("#table_type").val(type);
  filterapplay();
}

function AddCandidate(){
    var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
    var parent_option  = '<?php echo $_GET['option']; ?>';
    insertadittrails(parent_option,'COM_INSERT_CANDIDATES_POPUP',extra_paramiter);
    var job_posting_id = $("#job_posting_id_ajax").val();
    jQuery.ajax({
        url: "views/custom_insert_candidates_popup.php",
        data:'job_posting_id='+job_posting_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}
function assignedCandidateFiletr(cand_id){
  var job_posting_id = $("#job_posting_id_ajax").val();
  var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
  var parent_option  = '<?php echo $_GET['option']; ?>';
  insertadittrails(parent_option,'COM_INSERT_ASSIGNED_CANDIDATE_JOB_POSTING_POPUP',extra_paramiter);

    jQuery.ajax({
        url: "views/custom_insert_assigned_candidate_job_posting_popup.php",
        data:'cand_id='+cand_id+'&job_posting_id='+job_posting_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-single").select2();
        },
        error:function (){}
    });
}
function sendEmailCandidate(cand_id,email_id,temp_id){
    var job_posting_id = $("#job_posting_id_ajax").val();
    var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
    var parent_option  = '<?php echo $_GET['option']; ?>';
    insertadittrails(parent_option,'COM_INSERT_CANDIDATE_EMAIL_POPUP',extra_paramiter);
    jQuery.ajax({
        url: "views/custom_insert_candidate_email_popup.php",
        data:'cand_id='+cand_id+'&job_posting_id='+job_posting_id+"&email_id="+email_id+"&temp_id="+temp_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-signle").select2();
CKEDITOR.replace('editor11', {
        toolbar: [{
            name: 'clipboard',
            items: ['Undo', 'Redo']
        }, {
            name: 'styles',
            items: ['Styles', 'Format']
        }, {
            name: 'basicstyles',
            items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
        }, {
            name: 'paragraph',
            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
        }, {
            name: 'links',
            items: ['Link', 'Unlink']
        }, {
            name: 'insert',
            items: ['Image', 'EmbedSemantic', 'Table']
        }, {
            name: 'tools',
            items: ['Maximize']
        }, {
            name: 'editing',
            items: ['Scayt']
        }],
     customConfig: '',
        extraPlugins: 'autoembed,embedsemantic,image2,uploadimage,uploadfile',
        imageUploadUrl: '/uploader/upload.php?type=Images',
        uploadUrl: '/uploader/upload.php',
        removePlugins: 'image',
        height: 461,
        contentsCss: ['assets/pages/ckeditor/contents.css', 'assets/pages/ckeditor/artical.css'],
        bodyClass: 'article-editor editor11_new',
        format_tags: 'p;h1;h2;h3;pre',
        removeDialogTabs: 'image:advanced;link:advanced',
        stylesSet: [
            /* Inline Styles */
            {
                name: 'Marker',
                element: 'span',
                attributes: {
                    'class': 'marker'
                }
            }, {
                name: 'Cited Work',
                element: 'cite'
            }, {
                name: 'Inline Quotation',
                element: 'q'
            },

            /* Object Styles */
            {
                name: 'Special Container',
                element: 'div',
                styles: {
                    padding: '5px 10px',
                    background: '#eee',
                    border: '1px solid #ccc'
                }
            }, {
                name: 'Compact table',
                element: 'table',
                attributes: {
                    cellpadding: '5',
                    cellspacing: '0',
                    border: '1',
                    bordercolor: '#ccc'
                },
                styles: {
                    'border-collapse': 'collapse'
                }
            }, {
                name: 'Borderless Table',
                element: 'table',
                styles: {
                    'border-style': 'hidden',
                    'background-color': '#E6E6FA'
                }
            }, {
                name: 'Square Bulleted List',
                element: 'ul',
                styles: {
                    'list-style-type': 'square'
                }
            },

            /* Widget Styles */
            {
                name: 'Illustration',
                type: 'widget',
                widget: 'image',
                attributes: {
                    'class': 'image-illustration'
                }
            },
            // Media embed
            {
                name: '240p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-240p'
                }
            }, {
                name: '360p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-360p'
                }
            }, {
                name: '480p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-480p'
                }
            }, {
                name: '720p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-720p'
                }
            }, {
                name: '1080p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-1080p'
                }
            }
        ]
    });

        },
        error:function (){}
    });
}
function showUpdateratting(id){
    $(".custom-popup-open").hide();
    $(".custom-popup-slide-updateratting-"+id).show('slow');
}
function closedPopupcompliance22(){
      $(".custom-popup-open").hide();
      console.log("closed");
}
</script>
<style type="text/css">
    .icon-disable{
        color: #9d8c8e !important;
    }
</style>
<div class="card-body">
  <div class="text-center p-3 b-radius-10" style="background: #e6efff;">

    <h3 class="mt-3 f-w-600">Browse Vendors  </h3>

    <ul class="alphabet-filetr bg-white p-2 border b-radius-10 mt-4 mb-4">
       <li><button type="button" class="d-inline-block active activealpha" id="activealpha" !" onclick="selectAlphabet('')">All</button></li>
      <?php $alpha='A';  while($alpha < 'Z'){ ?>
        <li><a class="d-inline-block activealpha" id="activealpha<?php echo $alpha; ?>" href="#!" onclick="selectAlphabet('<?php echo $alpha; ?>')"><?php echo $alpha; ?></a></li onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
      <?php  $alpha++;
} ?>
      <li><button type="button" class="d-inline-block activealpha" id="activealphaZ" !" onclick="selectAlphabet('Z')">Z</button></li>
    </ul>
  </div>
  <form id="filterfrom">
    <input type="hidden" name="display_name_alphabet" id="display_name_alphabet" value="">
  <div class="row mt-3">
    <div class="col-lg-3">
      <!-- Checkbox card start -->
      <div class="card">
        <div class="card-header">
          <h5> Filter</h5>
          <div class="card-header-right">

            <button type="reset" class="btn btn-danger" value="reset" onclick="filterapplay('Y')">Reset</button>
          </div>
        </div>
        <div class="card-block">
          <div class="form-grou row border-bottom mb-3 pb-3">
            <span class="form-label col-lg-8">Perfect Match</span>
            <div class="col-lg-4 text-right">
            <input aria-label="Perfect Match" type="checkbox" class="js-switch" checked="" name="perfect_match" value="0" onchange="filterapplay()">
          </div>
          </div>
          <div class="form-group border-bottom mb-3 pb-3">
             <h5 class="f-w-600 mb-3">Location</h5>
            <select aria-label="Location" class="form-control js-example-basic-single" name="location_id" onchange="filterapplay()">
              <option value="">All</option>
              <?php foreach((array)$locations as $location){ ?>
                <option value="<?php echo $location['id']; ?>"><?php echo $location['location_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="row border-bottom mb-3">
            <div class="col-lg-12">
              <h5 class="f-w-600 mb-3">Vendor Name</h5>
            </div>
            <div class="form-group">
                <div class="row p-l-10 p-r-10">
                    <input aria-label="Vendor Name" class="form-control" type="text" id="vendor_name" name="vendor_name" value="" onblur="filterapplay()">
                 </div>
            </div>
          </div>
          <div class="row border-bottom mb-3">
            <div class="col-lg-12">
              <h5 class="f-w-600 mb-3">Vendor Email</h5>
            </div>
            <div class="form-group">
                <div class="row p-l-10 p-r-10">
                    <input aria-label="Vendor Email" class="form-control" type="text" id="vendor_email" name="vendor_email" value="" onblur="filterapplay()">
                 </div>
            </div>
          </div>
          <div class="row border-bottom mb-3">
            <div class="col-lg-12">
              <h5 class="f-w-600 mb-3">GST No</h5>
            </div>
            <div class="form-group">
                <div class="row p-l-10 p-r-10">
                    <input aria-label="Gstin" class="form-control" type="text" id="gstin" name="gstin" value="" onblur="filterapplay()">
                 </div>
            </div>
          </div>
         <div class="row border-bottom mb-3">
            <div class="col-lg-12">
              <h5 class="f-w-600 mb-3">Tax Registation No</h5>
            </div>
            <div class="form-group">
                <div class="row p-l-10 p-r-10">
                    <input aria-label="Vendor Tax Reg No" class="form-control" type="text" id="vendor_tax_reg_no" name="vendor_tax_reg_no" value="" onblur="filterapplay()">
                 </div>
            </div>
          </div>
          <div class="row border-bottom mb-3">
            <div class="col-lg-12">
              <h5 class="f-w-600 mb-3">CST Registation No</h5>
            </div>
            <div class="form-group">
                <div class="row p-l-10 p-r-10">
                    <input aria-label="Cst Reg No" class="form-control" type="text" id="cst_reg_no" name="cst_reg_no" value="" onblur="filterapplay()">
                 </div>
            </div>
          </div>
         <div class="row border-bottom mb-3">
            <div class="col-lg-12">
              <h5 class="f-w-600 mb-3">PAN No</h5>
            </div>
            <div class="form-group">
                <div class="row p-l-10 p-r-10">
                    <input aria-label="Pan No" class="form-control" type="text" id="pan_no" name="pan_no" value="" onblur="filterapplay()">
                 </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="col-lg-9">
      <div class="card mb-4">
        <div class="card-block">
          <div class="row align-items-center border-bottom pb-3">
            <div class="col-lg-3">
              <p class="mb-0">Showing <b class="f-w-600">10</b> of <b class="f-w-600">50</b> Vendors</p>
            </div>
            <div class="col-lg-9">
              <div class="row align-items-center">
                <div class="col-lg-3">
                  <div class="form-group row align-items-center mb-0">
                    <span class="form-label col-lg-5 text-right mb-0">Show :</span>
                    <div class="col-lg-7">
                      <select aria-label="Limit" class=" border " name="limit" onchange="filterapplay()">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-lg-4">
                  <div class="form-group row align-items-center mb-0">
                    <span class="form-label col-lg-4 text-right mb-0">Sort :</span>
                    <div class="col-lg-8">
                      <select aria-label="Sort By" class=" border " class="form-select js-example-basic-single" name="sort_by" onchange="filterapplay()">
                        <option value="display_name">Name</option>
                        <option value="company">Company </option>
                        <option value="established_year">Established Year</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-lg-1">
                  <input type="hidden" name="table_type" id="table_type" value="C">
                  <button type="button" class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon btnactive  active" id="btnactiveC" onclick="selectTableType('C')">
                    <i class="ti-view-grid m-0"></i>
                  </button>
                </div>
                <div class="col-lg-1">
                  <button type="button" class="btn waves-effect waves-light btn-primary btn-outline-primary btn-icon btnactive" id="btnactiveL" onclick="selectTableType('L')">
                    <i class="ti-menu m-0"></i>
                  </button>
                </div>
                <div class="col-lg-3">
                <input type="hidden" id="job_posting_id_ajax" value="<?php echo $jobpost; ?>">

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row" id="formview">
            <?php include_once "views/custom_list_vendor_table.php"; ?>
      </div>
    </div>
  </div>
  </form>
</div>
<!-- Page body end -->
