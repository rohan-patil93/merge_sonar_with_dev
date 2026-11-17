<?php
$_SESSION['apply_js']['Switch']= '1';
$super_admin_type = 0;
if(in_array(SUPERADMIN,$_SESSION['roles'])){
    $super_admin_type = SUPERADMIN;
}
$url = API."?mode=getcomplianceuserlist&admin_type=".$super_admin_type;
$subscripusers = callAPI($url);
$subscriptusers = json_decode($subscripusers,1);
$applicationuser = $subscriptusers['application_users'];
$stausresult = ViewStatusData();
$all_application_users = $subscriptusers['all_application_users'];
foreach ($all_application_users as $all_application_user){
        $allusercnt[$all_application_user['company_id']] = $all_application_user;
}
?>
<script type="text/javascript">
    function userChangeActivationType(userid,companyid){
        var status = "";
        var msg = <?php echo json_encode(__('User Deactivated Successfully'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        if($("#useractiveType_"+userid).is(':checked')){
            status = '0';
            var msg = <?php echo json_encode(__('User Active Successfully'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
        }
         $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=updateactivationtype&userid='+userid+'&companyid='+companyid,
            success: function(response) {
                console.log(response);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
            }
        });




    }
</script>>

<div class="card">
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-lg-12">
                <div class="page-header-title">
                    <div class="d-inline">
                        <h5 class="f-24"><?= __('User List'); ?></h5>
                        <div class="page-header-breadcrumb mt-2">
                            <ul class=" breadcrumb breadcrumb-title">
                                <li class="breadcrumb-item">
                                    <a href="index.php"><i class="fa fa-home"></i></a>
                                </li>
                                <li class="breadcrumb-item"><a href="#"><?= __('User List'); ?></a> </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <div class="card-header-right">
            <div class="input-group input-group-button mb-3">
                <input aria-label="<?= htmlspecialchars(__('Search The Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="filter1111" class="form-control form-control-sm width1"  placeholder="<?= htmlspecialchars(__('Search The Name'), ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Search The Name'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="input-group-text addon-style m-0">
                    <span class="form-label m-t-5"><i class="ti-search f-w-600 text-white"></i></span>
                </span>
            </div>
        </div>
    </div>
    <div class="card-block">
        <div class="card-body">

            <div class="row custom-tooltip" id="results">
                <?php if(count((array)$applicationuser) !='0'){  foreach($applicationuser as $applicationusers){
                    $category_list = $applicablecompliancescategory[$applicationusers['company_id']];
                    $compliances_list = $applicablecompliances[$applicationusers['company_id']]; ?>
                    <div class="col-xl-3 col-md-12 results custom-ribbon-accordian position-relative invited" >
                        <div class="ribbon ribbon-clip ribbon-right d-block" style="right: 0;">
                            <div class="ribbon-target d-block<?php echo $applicationusers['user_activated']!='Y'?"bg-danger":""; ?>" style="right: 3px;">
                                <span class="ribbon-inner <?php echo $applicationusers['user_activated']!='Y'?"bg-danger":""; ?>"></span> <?php echo $applicationusers['user_activated']!='Y'?"In-Active":"Active"; ?>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class=" align-items-center">
                                    <a href="<?php echo string_to_ascii('PAGE=CUSTOM_VIEW_COMPLIANCE_USERS&EID='.$applicationusers['id']); ?>">
                                        <div class="align-middle">
                                            <?php if($applicationusers['user_photo'] !="") {?>
                                                <img src="uploads/all/<?php echo $applicationusers['user_photo']; ?>"  class="img-radius img-50 align-top m-r-15" alt="<?= htmlspecialchars(__('User Profile'), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php } else { ?>
                                                <span  alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($applicationusers['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <h6 ><?php echo $applicationusers['display_name']; ?></h6>
                                                <p><?php echo $_SESSION['company'][$applicationusers['company_id']]; ?></p>
                                            </div>

                                        </div></a>
                                        <p class="text-muted m-b-0 mt-3"><?php echo $applicationusers['user_email']; ?></p>
                                        <div class="row mt-1 d-block">
                                             <div class="col-lg-6 d-inline-block">

                                                    <span class="form-label label label-md label-primary " data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="<?= htmlspecialchars(__('Used Count'), ENT_QUOTES, 'UTF-8'); ?>"><?php echo count((array)$allusercnt[$applicationusers['company_id']]); ?></span>
                                                </div>
                                                <div class="col-lg-6  text-right d-inline-block float-right">
                                                    <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" id="useractiveType_<?php echo $applicationusers['id']; ?>" class="js-small position-absolute float-right"  <?php echo $applicationusers['user_activated']=="Y"?"checked":"" ?> onchange="userChangeActivationType('<?php echo $applicationusers['id'];?>','<?php echo $applicationusers['company_id'];?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="<?= htmlspecialchars(__('Category Activation Switch'), ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php } ?>
                        <?php } else { ?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> <?= __('No Users'); ?> </p>
                                    <div class="text-center px-4">
                                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                  </div>
                              </div>
                          <?php } ?>
                    </div>
                </div>
            </div>
        </div>
