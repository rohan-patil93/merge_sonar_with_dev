<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=getrecognitionsettings&table=".TBL_EMPLOYEE_RECOGNITIONS."&recognition_id=".$_GET['recogid'];
$recog_data = callAPI($vurl);
$recogdata = json_decode($recog_data,1);
$emplist = $recogdata['employees'];
$userlist = $recogdata['users'];
$badges = $recogdata['badges'];
$recognition_row = $recogdata['recognition_rows'];
$File_Uploads_Path = "uploads/all/";

?>

<div class="modal-header">
    <h5 class="modal-title text-center">
        Recognition
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" data-scroll="true" >
    <div class="form-group row" >
        <div class="col-lg-12">
            <?php
            $size = 6;
            if(count((array)$recognition_row)=='1'){
                $size = 12;
                ?><script>
                    $("#modalsize").removeClass("modal-xl");
                    $("#modalsize").addClass("modal-lg");
                </script> <?php
            }
            foreach ($recognition_row as $recogemp => $remp) {
                $first_emp = $remp['recognition_to'];
                $given_emp = $remp['recognition_from'];
                $badgeid = $remp['badge_id'];
                $comments = $remp['comments'];
                ?>
                <div class="col-lg-<?php echo $size; ?> float-left">
                    <div class="card-stretch gutter-b">
                        <div class="newcrds card card-custom custom-ribbon">
                            <div class="card-header card-header-right ribbon ribbon-top ribbon-ver">
                                <div class="ribbon-target custom" style="top: -2px; left:0px;">
<?php if($emplist[$first_emp]['image']!="") { ?>
                                    <img src="uploads/all/<?php echo $emplist[$first_emp]['image']; ?>"  class="img-radius img-50 align-top m-r-15" style="z-index: 9;" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"  style="width: 51px; left: 4px; top: 4px; background-color: white;color: black;z-index: 9;"><?php echo initials($emplist[$first_emp]['display_name']); ?></span>
                                <?php } ?>

                            </div>

<h4 class="card-title">
        <?php echo $badges[$badgeid]['badge_name']; ?>&nbsp; <img class="ml-3" src="uploads/all/<?php echo $badges[$badgeid]['image']; ?>" style="
        width: 60px;
        height: auto;
        border-radius: 50%;
        z-index: 9;
        margin: -2px 0 0 -9px;
        " alt="Attachment Preview">
    </h4>

                        </div>
                        <div class="custom-text">
                            <?php echo $emplist[$first_emp]['display_name']; ?>
                        </div>
                        <div class="card-body">
                            <?php echo $comments; ?>
                        </div>
                        <!--begin::Footer-->
                                                <!--begin::Footer-->
                        <div class="row">
                            <div class="col-3 mt-3  p-l-20 ">
                                <p class="m-b-5"><a href="#" class="font-weight-bolder text-primary ml-2"><?php echo date('d M, Y'); ?></a></p>

                            </div>
                            <div class="col-4 mt-3">

                            </div>
                            <div class="col-5 p-b-10">
                                <?php $given_emp = $given_emp!="" ? $given_emp :$_SESSION['userid'];   ?>
                                <div class="d-inline-block mt-3">
                                    <a class="font-weight-bolder text-primary ml-2"> <?php echo $userlist[$given_emp]['display_name']; ?></a>

                                </div>

                                <?php if($userlist[$given_emp]['image']!="") { ?>
                                    <img src="uploads/all/<?php echo $userlist[$given_emp]['image']; ?>"  class="img-radius img-50 align-top m-r-15" style="z-index: 9;" alt="User Profile">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"  style="z-index: 9; left: 10px; top: 7px; z-index: 9;"><?php echo initials($userlist[$given_emp]['display_name']); ?></span>
                                <?php } ?>
                            </div>
                        </div>


                        <!--end::Footer-->
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal-footer">
    <button name="" type="button" class="btn btn-danger mr-2"  data-bs-dismiss="modal" >Close</button>
</div>

</div>
