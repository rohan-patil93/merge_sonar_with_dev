<?php
$table_name = "application_users";
if(isset($_POST['submit'])){
   $_POST['company_id'] = $_SESSION['company_id'];
   $_POST['current_status'] = 0;
   $_POST['is_active'] = 0;
   $_POST['status_date'] = nowDate();
   $_POST['from_date'] = date('Y-m-d');
   $_POST['to_date'] = date('Y-m-d');
   $_POST['display_name'] = $_POST['first_name']." ".$_POST['last_name'];

   if($_POST['submit']=='Add'){
      unset($_POST['submit']);unset($_POST['submit_value']);
      $_POST['status_date'] = nowDate();
      $_POST['created_date'] = nowDate();
      $_POST['created_by'] = $_SESSION['userid'];
      $url = API."?mode=adddata&table=".$table_name."&method=post";
      $response = file_post_contents($url, $_POST, 'insert');
      $res = json_decode($response,1);
      $_POST['user_id'] = $res['LASTID'];
      $url = API."?mode=adddata&table=application_user_invitations&method=post";
      $response = file_post_contents($url, $_POST, 'insert');
   }
}
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid" >
    <div class="row">
        <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                  <div class="card-header mb-3">
                      <h5> <?= __('Invite Vendor'); ?></h5>
                      <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply" aria-hidden="true"></i>
                                <?= __('Back'); ?>
                              </a>
                      </div>
                  </div>
                    <div class="card-body">
                  <form class="kt-form j-pro" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                        <div class="form-group row ml-20 mr-20">
                            <div class="form-group col-lg-6">
                                <label for="user_name"><?= __('User Name') ?>&nbsp;<span class="requried">*</span></label>
                                    <input class="form-control"  type="text"  id="user_name" name="user_name"  value="" aria-label="<?= htmlspecialchars(__('User Name'), ENT_QUOTES, 'UTF-8'); ?>">

                                <div style="display: none;color: red;" id="user_name_err"> <?= __('Please enter User Name'); ?> </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="first_name"><?= __('First Name') ?>&nbsp;<span class="requried">*</span></label>

                                    <input class="form-control"  type="text"  id="first_name" name="first_name"  value="" aria-label="<?= htmlspecialchars(__('First Name'), ENT_QUOTES, 'UTF-8'); ?>">

                                <div style="display: none;color: red;" id="first_name_err"> <?= __('Please enter First Name'); ?> </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="last_name"><?= __('Last Name') ?></label>

                                    <input class="form-control"  type="text"  id="last_name" name="last_name"  value="" aria-label="<?= htmlspecialchars(__('Last Name'), ENT_QUOTES, 'UTF-8'); ?>">

                                <div style="display: none;color: red;" id="last_name_err"> <?= __('Please enter Last Name'); ?> </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="user_email"><?= __('Email') ?></label>

                                    <input class="form-control"  type="text"  id="user_email" name="user_email"  value="" aria-label="<?= htmlspecialchars(__('User Email'), ENT_QUOTES, 'UTF-8'); ?>">

                                <div style="display: none;color: red;" id="user_email_err"> <?= __('Please enter Email'); ?> </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="user_mobile"><?= __('Mobile') ?></label>

                                    <input class="form-control"  type="text"  id="user_mobile" name="user_mobile"  value="" aria-label="<?= htmlspecialchars(__('User Mobile'), ENT_QUOTES, 'UTF-8'); ?>">

                                <div style="display: none;color: red;" id="user_mobile_err"> <?= __('Please enter Mobile'); ?> </div>
                            </div>
                            <input type="hidden" name="user_type" value="3">
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9">
                                    <input type="hidden" name="submit" value="Add" />
                                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check" aria-hidden="true"></i><?= __('Save & exit') ?></button>
                                    <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="continue"><i class="la la-check-circle" aria-hidden="true"></i><?= __('Save & continue') ?></button>
                                    <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close" aria-hidden="true"></i><?= __('Cancel') ?></button>
                                </div>
                                <div class="col-lg-3 text-right">
                                    <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash" aria-hidden="true"></i><?= __('Delete') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
