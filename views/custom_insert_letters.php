<?php
if(isset($_POST['submit_value'])){
    $data['id'] = $_POST['id'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['is_active'] = 0;
    $data['current_status']=0;
    $data['originator'] = $_POST['originator'];
    $data['version_number'] = $_POST['version_number'];
    $data['transaction_number'] = $_POST['transaction_number'];
    $data['transaction_date'] = date("Y-m-d",strtotime($_POST['transaction_date']));
    $data['ref_no'] = $_POST['ref_no'];
    $data['ref_date'] = date("Y-m-d",strtotime($_POST['ref_date']));
    $data['subject'] = $_POST['subject'];
    $data['address_to'] = $_POST['address_to'];
    $data['content'] = base64_encode($_POST['content']);
    $url = API."?mode=insertletters&method=post";
    $response = file_post_contents($url, $data);
}
$inputFildsArray = getListCreatDocumentsFields();
if(isset($_GET['Rid'])){
    $rids = encodeId($_GET['Rid']);
    $rids = urldecode($rids);
    $rids = explode(":",$rids);
    $rid = $rids['1'];
    $table1 = getTableRow("letters","","id=".$rid,1);
}

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <form method="post" onsubmit="return toSubmit();">
            <input type="hidden" name="id" value="<?php echo $rid; ?>">
            <input type="hidden" id="encode" value="decode">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <div class="card-title">
                        <h3 class="card-label">&nbsp;<?php echo "Create Documents";  ?> </h3>
                    </div>
                    <div class="card-toolbar">
                        <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            Back
                        </a>
                    </div>
                </div>

                <?php $table_name="letters"; include_once "custom_insert_quotation_topup.php"; ?>

                <div class="container-fluid">

                    <div class="form-group row">
                        <div class="col-lg-2 col-md-2 col-sm-12">
                            <span class="col-form-label text-right">Dynamic Types</span>
                            <div class=" document_edit bexample-preview">
                                <ul class="navi navi-hover navi-active">
                                    <?php foreach($inputFildsArray  as $key => $value){ ?>
                                        <li class="navi-item">
                                            <button type="button" class="btn btn-hover-bg-success btn-xs btn-text-success btn-hover-text-white text_added" data-values="<?php echo $key; ?>">
                                                <span class="navi-icon">
                                                    <i class="flaticon2-analytics"></i>
                                                </span>
                                                <span class="navi-text textcapital"><?php echo $value[0]; ?></span>
                                            </button>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-9 col-md-9 col-sm-12">
                            <ul class="nav nav-tabs" id="myTab">
                                <li class="nav-item">
                                    <a class="nav-link  change_text" data-function="encode" id="home-tab" data-toggle="tab" href="#home" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-icon">
                                            <i class="flaticon2-chat-1"></i>
                                        </span>
                                        <span class="nav-text">Privew</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active change_text" data-function="decode" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                        <span class="nav-icon">
                                            <i class="flaticon2-layers-1"></i>
                                        </span>
                                        <span class="nav-text">Replace code</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="summernote" id="kt_summernote_1" ><?php echo base64_decode($table1['content']); ?></div>
                            <textarea aria-label="Content" type="text" name="content" value="" id="summernote" style="display: none;"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="kt-form__actions">
                        <div class="row">
                            <div class="col-lg-4">
                                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="exit"><i class="la la-check"></i>Save &amp; exit</button>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

