<?php
$_SESSION['apply_js']['multiselest'] = 1;

if (isset($_POST['submit_value'])) {
    $data   = $_POST;
    $files  = $_FILES;
    // ===============================
    // 1) Handle image upload
    // ===============================
    $image = '';
    if (!empty($files['image_url']['name']) && is_uploaded_file($files['image_url']['tmp_name'])) {
        $ext     = pathinfo($files['image_url']['name'], PATHINFO_EXTENSION);
        $newName = "announcement_" . time() . "_" . uniqid() . ($ext ? "." . $ext : "");
        $uploadPath = "uploads/all/";
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        if (move_uploaded_file($files['image_url']['tmp_name'], $uploadPath . $newName)) {
            $image = $newName;
        }
    } else {
        $image = $_POST['hid_image'] ?? '';
    }
    // ===============================
    // 2) Survey JSON building
    // ===============================
    $survey_details = [];
    if (!empty($data['enable_survey']) && !empty($data['survey_type'])) {
        if ($data['survey_type'] == 'POLIS') {
            $survey_details = [
                'type' => 'POLIS',
                'data' => [
                    ['label' => $_POST['polis1'], 'value' => '1'],
                    ['label' => $_POST['polis2'],  'value' => '2']
                ]
            ];
        } elseif ($data['survey_type'] == 'RANKING') {
            $survey_details = [
                'type' => 'RANKING',
                'data' => [
                    'start' => $_POST['rating_start'] ?? '0',
                    'end'   => $_POST['rating_end'] ?? '10'
                ]
            ];
        } elseif ($data['survey_type'] == 'CHOICES') {
            $survey_details = [
                'type' => 'CHOICES',
                'data' => [
                    ['label' => $_POST['multi1'], 'value' => '1'],
                    ['label' => $_POST['multi2'],  'value' => '2'],
                    ['label' => $_POST['multi3'], 'value' => '3'],
                    ['label' => $_POST['multi4'], 'value' => '4'],
                    ['label' => $_POST['multi5'], 'value' => '5']
                ]
            ];
        }
    }

    // ===============================
    // 3) Prepare final data
    // ===============================
    $save = [];
    $save['company_id']              = $_SESSION['company_id'] ?? '';
    $save['announcement_category_id'] = $data['announcement_category_id'] ?? '';
    $save['announcement_heading']    = trim($data['announcement_heading'] ?? '');
    $save['announcement_message']    = trim($data['announcement_message'] ?? '');
    $save['start_date']              = !empty($data['start_date']) ? date('Y-m-d', strtotime($data['start_date'])) : '';
    $save['expiry_date']             = !empty($data['expiry_date']) ? date('Y-m-d', strtotime($data['expiry_date'])) : '';
    $save['image_url']               = $image;
    $save['video_url']               = $data['video_url'] ?? '';
    $save['survey_type']             = $data['enable_survey'] == '1' ? 'Y' : 'N';
    $save['comment_visibility']      = $data['comment_visibility'];
    $save['survey_result_visibility'] = $data['survey_result_visibility'];
    $save['application_filter_id'] = $data['application_filter_id'];

    $save['survey_details']          = !empty($survey_details) ? json_encode($survey_details) : '';

    // remove form-only keys
    unset($save['enable_survey'], $save['submit_value'], $save['hid_image']);

    // ===============================
    // ===============================
    if ($_POST['submit_value'] == 'insert') {
        $save['is_active']               = 0;
        $save['current_status']          = 0;

        $save['created_date'] = date('Y-m-d H:i:s');
        $save['created_by']   = $_SESSION['userid'] ?? '';

        $url = API . "?mode=adddata&table=company_announcements&method=post";
        $res = file_post_contents($url, $save, 'insert');
        $response = json_decode($res, true);

        if (!empty($response['status']) && $response['status'] == 'sucess') {
    ?>
            <script>
                window.addEventListener("load", function() {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success',
                        'animated flipInX', 'animated flipOutX', 'Announcement Added successfully', '');
                });
            </script>
        <?php
        header("location:index.php?option=COM_LIST_COMPANY_ANNOUNCEMENTS");
        }
    } elseif ($_POST['submit_value'] == 'update') {
        $ridParam = $_POST['rid'] ?? '';
        $save['updated_date'] = date('Y-m-d H:i:s');
        $save['updated_by']   = $_SESSION['userid'] ?? '';

        $url = API . "?mode=updaterowtabledata&rid={$ridParam
}&table=company_announcements&method=post";
        $res = file_post_contents($url, $save, 'update');
        $response = json_decode($res, true);

        if (!empty($response['status']) && $response['status'] == 'sucess') {
        ?>
            <script>
                window.addEventListener("load", function() {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success',
                        'animated flipInX', 'animated flipOutX', 'Announcement Updated successfully', '');
                });
            </script>
    <?php
    header("location:index.php?option=COM_LIST_COMPANY_ANNOUNCEMENTS");
            }
        }
    }
$announcement_id = '';
if ($_GET['Rid'] != "") {
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $announcement_id = explode(":", $rid);
    $announcement_id = $announcement_id['1'];
}
$url = API . "?mode=getCompanyAnnouncements&method=get&rid=" . $announcement_id;
$result = callAPI($url);
$result = json_decode($result, 1);
$announcement_categories = $result['announcement_categories'] ?? array();
$announcement = $result['announcement'] ?? array();
$filtertbl_rowid = "0";
$survey_type_value = '';
$survey_details_decoded = [];
if (!empty($announcement['survey_details'])) {
    $survey_details_decoded = json_decode($announcement['survey_details'], true);
    $survey_type_value = $survey_details_decoded['type'] ?? '';
}
?>
<script type="text/javascript">
    function showpopup(){
      var filter_id = document.getElementById('filtertbl_rowid').value;
      jQuery.ajax({
        url: "views/custom_filter_bulktransfer.php",
        data:'filter_id='+filter_id,
        type: "GET",
        success:function(data){
           $('#dynamic_popup1').html(data);
           $('#kt_modal_dynamic_popup').modal('show');
       },
       error:function (){}
     });
  }
</script>

<!-- Page-body start -->
<div class="page-body">

    <div class="row">
        <div class="col-lg-12">
            <div class="card 1 ">

                <div class="card-header table-card-header">
                    <h5> Company Announcements</h5><button type="button" class="btn p-0 border-0 bg-transparent ti-info-alt f-20 text-primary" onclick="proceduressteps()" data-bs-toggle="tooltip" data-placement="bottom" title="Go Tour" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                    <div class="card-header-right table-card-header ">

                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            Back
                        </a>

                    </div>
                </div>
                <!--begin::Portlet-->
                <div class="card-infisuite">
                    <div class="card-body p-4 card-sticky" id="kt_page_sticky_card">

                        <div class="form-group row ml-20 mr-20">
                            <form class="kt-form" id="kt_form" method="post" action="" name="myForm" enctype="multipart/form-data">
                                <input type="hidden" name="application_filter_id" id="filtertbl_rowid" value="<?php echo $filtertbl_rowid;?>">
                                <div class="row">
                                    <div class="form-group col-lg-6">
                                        <span>Announcement Category Id</span>
                                        <select aria-label="Announcement Category" class="form-select js-example-basic-single" id="announcement_category_id" name="announcement_category_id">
                                            <option value="0"> Select Any one Announcement Category Id</option>
                                            <?php foreach ($announcement_categories as $row): ?>
                                                <option value="<?php echo $row['id']; ?>"
                                                    <?php echo ($announcement['announcement_category_id'] == $row['id']) ? 'selected' : ''; ?>>
                                                    <?php echo $row['announcement_category_name']; ?>
                                                </option>
                                            <?php endforeach; ?>

                                        </select>
                                        <div style="display: none;color: red;" id="announcement_category_id_err"> Please Select Announcement Category Id </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Announcement Heading&nbsp;<span class="requried">*</span></span>
                                        <input aria-label="Announcement Heading" class="form-control" type="text" id="announcement_heading" name="announcement_heading" value="<?php echo htmlspecialchars($announcement['announcement_heading'] ?? ''); ?>">
                                        <div style="display: none;color: red;" id="announcement_heading_err"> Please enter Announcement Heading </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span>Announcement Message&nbsp;<span class="requried">*</span></span>
                                        <div class="kt-radio-inline">
                                            <textarea aria-label="Announcement Message" type="text" class="form-control " rows="5" cols="5" id="announcement_message" name="announcement_message"><?php echo htmlspecialchars($announcement['announcement_message'] ?? ''); ?></textarea>
                                        </div>
                                        <div style="display: none;color: red;" id="announcement_message_err"> Please Select Announcement Message </div>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span>Image Url&nbsp;<span class="requried">*</span></span>
                                        <input aria-label="Image Url" class="form-control" type="file" name="image_url" id="image_url" accept=".png, .jpg, .jpeg" />
                                        <?php if (!empty($announcement['image_url'])): ?>
                                            <img src="uploads/all/<?php echo $announcement['image_url']; ?>"
                                                alt="Saved File" style="max-height:80px; margin-top:5px;">
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group col-lg-6">
                                        <span class="">Video Url</span>
                                        <input aria-label="Video Url" class="form-control" type="text" id="video_url" name="video_url" value="<?php echo htmlspecialchars($announcement['video_url'] ?? ''); ?>">
                                        <div style="display: none;color: red;" id="video_url_err"> Please enter Video Url </div>
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <span>Start Date&nbsp;<span class="requried">*</span></span>
                                        <input aria-label="Input field" class="form-control datepicker" type="text" value="<?php echo !empty($announcement['start_date']) ? fromDate($announcement['start_date']) : ''; ?>" name="start_date">
                                    </div>

                                    <div class="form-group col-lg-3">
                                        <span>Expiry Date&nbsp;<span class="requried">*</span></span>
                                        <input aria-label="Input field" class="form-control datepicker" type="text" value="<?php echo !empty($announcement['expiry_date']) && $announcement['expiry_date'] != '1970-01-01' ? fromDate($announcement['expiry_date']) : ''; ?>" name="expiry_date">
                                    </div>
                                    <div class="form-group col-lg-6">

                                        <span class="form-label">Asign to <span class="text-danger">*</span></span>
                                        <div id="tsk_fortable">

                                        </div>
                                        <div class="col-xl-12">
                                             <i class="flaticon2-group text-success"></i>
                                             <button type="button" onclick="showpopup();">Edit </button>
                                         </div>

                                    </div>
                                    <div class="form-group col-lg-6">
                                        <span>Comment & Like / Dislike Visibility </span>
                                        <div class="col-12 col-form-label">
                                           <div class="radio-inline">
                                               <label class="radio radio-success">
                                                    <input aria-label="Comment Visibility" type="radio" name="comment_visibility" value="Y" <?php if($announcement['comment_visibility']=='Y'){ ?> checked="checked" <?php
} ?>>
                                                    <span></span>Public
                                                </label>
                                                <label class="radio radio-success">
                                                    <input aria-label="Comment Visibility" type="radio" name="comment_visibility" value="N" <?php if($announcement['comment_visibility']=='N'){ ?> checked="checked" <?php
} ?>>
                                                    <span></span>Private
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">

                                        <div class="form-check form-switch">
                                            <input aria-label="Enable Survey" class="form-check-input" type="checkbox" id="enable_survey" name="enable_survey" value="1" <?php echo ($announcement['survey_type'] == 'Y') ? 'checked' : ''; ?>>
                                            <span class="slider round"></span>
                                            <span>Enable Survey?</span>
                                        </div>

                                    </div>

                                    <!-- Survey Type Dropdown -->
                                    <div class="form-group col-lg-6 w-full"  id="survey_type_block" style="display:<?php echo ($announcement['survey_type'] == 'Y') ? 'block' : 'none'; ?>;">
                                        <span>Survey Type</span>
                                        <select aria-label="Survey Type" class="form-select js-example-basic-single" id="survey_type" name="survey_type">
                                            <option value="">Select Survey Type</option>
                                            <option value="POLIS" <?php echo ($survey_type_value == 'POLIS') ? 'selected' : ''; ?>>Polis</option>
                                            <option value="RANKING" <?php echo ($survey_type_value == 'RANKING') ? 'selected' : ''; ?>>Rating</option>
                                            <option value="CHOICES" <?php echo ($survey_type_value == 'CHOICES') ? 'selected' : ''; ?>>Multi Choice</option>
                                        </select>
                                    </div>


                                    <div class="form-group col-lg-6" id="survey_polis_block" style="display:<?php echo ($survey_type_value == 'POLIS') ? 'block' : 'none'; ?>;">
                                        <span>Polis Options</span>
                                        <input aria-label="Polis1" type="text" class="form-control mb-2" name="polis1" value="<?php echo $survey_details_decoded['data'][0]['label'] ?? 'YES'; ?>">
                                        <input aria-label="Polis2" type="text" class="form-control" name="polis2" value="<?php echo $survey_details_decoded['data'][1]['label'] ?? 'NO'; ?>">
                                    </div>


                                    <div class="form-group col-lg-6" id="survey_rating_block" style="display:<?php echo ($survey_type_value == 'RANKING') ? 'block' : 'none'; ?>;">
                                        <span>Rating Range</span>
                                        <div class="row">
                                            <div class="col-6">
                                                <input aria-label="Start (e.g. 0)" type="number" class="form-control" name="rating_start" id="rating_start" placeholder="Start (e.g. 0)" value="<?php echo $survey_details_decoded['data']['start'] ?? '0'; ?>">
                                            </div>
                                            <div class="col-6">
                                                <input aria-label="End (e.g. 10)" type="number" class="form-control" name="rating_end" id="rating_end" placeholder="End (e.g. 10)" value="<?php echo $survey_details_decoded['data']['end'] ?? '10'; ?>">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="form-group col-lg-6" id="survey_choices_block" style="display:<?php echo ($survey_type_value == 'CHOICES') ? 'block' : 'none'; ?>;">
                                        <span>Multi Choices</span>
                                        <?php if ($survey_type_value == 'CHOICES' && !empty($survey_details_decoded['data'])):
                                            foreach ($survey_details_decoded['data'] as $i => $choice): ?>
                                                <input aria-label="Input field" type="text" class="form-control mb-2" name="multi<?php echo $i + 1; ?>"
                                                    value="<?php echo htmlspecialchars($choice['label']); ?>">
                                            <?php endforeach;
                                        else: ?>
                                            <input aria-label="Option 1" type="text" class="form-control mb-2" name="multi1" placeholder="Option 1" value="">
                                            <input aria-label="Option 2" type="text" class="form-control mb-2" name="multi2" placeholder="Option 2" value="">
                                            <input aria-label="Option 3" type="text" class="form-control mb-2" name="multi3" placeholder="Option 3" value="">
                                            <input aria-label="Option 4" type="text" class="form-control mb-2" name="multi4" placeholder="Option 4" value="">
                                            <input aria-label="Option 5" type="text" class="form-control mb-2" name="multi5" placeholder="Option 5" value="">
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-group col-lg-6" id="survey_result_block" style="display:<?php echo ($announcement['survey_result_visibility'] == 'Y') ? 'block' : 'none'; ?>;">
                                        <span>Survey Result Visibility </span>
                                        <div class="col-12 col-form-label">
                                           <div class="radio-inline">
                                               <label class="radio radio-success">
                                                    <input aria-label="Survey Result Visibility" type="radio" name="survey_result_visibility" value="Y" <?php if($announcement['survey_result_visibility']=='Y'){ ?> checked="checked" <?php
} ?>>
                                                    <span></span>Public
                                                </label>
                                                <label class="radio radio-success">
                                                    <input aria-label="Survey Result Visibility" type="radio" name="survey_result_visibility" value="N" <?php if($announcement['survey_result_visibility']=='N'){ ?> checked="checked" <?php
} ?> >
                                                    <span></span>Private
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer p-0">
                                    <div class="row">
                                        <div class="col-lg-9">
                                            <?php if ($announcement_id != '') { ?>
                                                <input type="hidden" name="rid" value="<?php echo $announcement['id'] ?? ''; ?>">
                                                <input type="hidden" name="hid_image" value="<?php echo $announcement['image_url'] ?? ''; ?>">
                                                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value" value="update"><i class="la la-check-circle"></i>Update </button>
                                            <?php } else {   ?>
                                                <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value" value="insert"><i class="la la-check-circle"></i>Save </button>
                                            <?php  }  ?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            </form>

        </div>
    </div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // Toggle survey block
            $("#enable_survey").on("change", function() {
                if ($(this).is(":checked")) {
                    $("#survey_type_block").show();
                } else {
                    $("#survey_type_block").hide();
                    $("#survey_polis_block, #survey_rating_block, #survey_choices_block").hide();
                }
            });

            // Show based on survey type
            $("#survey_type").on("change", function() {
                let type = $(this).val();
                $("#survey_polis_block, #survey_rating_block, #survey_choices_block").hide();

                if (type === "POLIS") {
                    $("#survey_polis_block").show();
                } else if (type === "RANKING") {
                    $("#survey_rating_block").show();
                } else if (type === "CHOICES") {
                    $("#survey_choices_block").show();
                }
                $("#survey_result_block").show();

            });
        });
    </script>
