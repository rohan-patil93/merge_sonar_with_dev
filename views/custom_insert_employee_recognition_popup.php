<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vurl = API."?mode=getrecognitionsettings&table=".TBL_EMPLOYEE_RECOGNITIONS."&userid=".$_SESSION['userid'];
$recog_data = callAPI($vurl);
$recogdata = json_decode($recog_data,1);
$recog_types = $recogdata['recognition_types'];
$emplist = $recogdata['employees'];
$badges = $recogdata['badges'];
$recognized_employees = explode(',',$_GET['recognized_employees']);
$recognition_type = "Recognition";
$File_Uploads_Path = "uploads/all/";
$badgeid = $_GET['badgeid'];
$comments = $_GET['comments'];
?>
<script type="text/javascript">
    function confirmrecognition(){
        var display_in_announcement =document.getElementById("display_announcement").value;
        var recognition_type =document.getElementById("recognition_type").value;
        var number_of_points =document.getElementById("number_of_points").value;
        var image =document.getElementById("image").value;
        var recognition_from =document.getElementById("recognition_from").value;
        var comments =document.getElementById("comments").value;
        var company_announcement_id =document.getElementById("company_announcement").value;
        var recognition_to =document.getElementById("recognition_to").value;
        var badge_id =document.getElementById("badge_id").value;
        var rowid =document.getElementById("rowid").value;
        var pass_data = "&display_in_announcement="+display_in_announcement+"&recognition_type="+recognition_type+"&number_of_points="+number_of_points+"&image="+image+"&recognition_from="+recognition_from+"&comments="+comments+"&company_announcement_id="+company_announcement_id+"&recognition_to="+recognition_to+"&badge_id="+badge_id+"&rowid="+rowid;
        console.log(pass_data);
        $.ajax({
            url: "ajax.php",
            data:'page=confirmrecognition'+pass_data,
            type: "POST",
            success: function(response) {
                console.log(response);
                redirecttolist();
            }
        });

    }

</script>

<div class="modal-header">
    <h4 class="modal-title">Confirm Recognition    </h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body" data-scroll="true" data-height="300" >
    <div class="form-group row" >
        <div class="col-lg-12">
            <?php
            $size = 6;
            if(count((array)$recognized_employees)=='1'){
                $size = 12;
                ?>
                <script>
                    $("#modalsize").removeClass("modal-xl");
                    $("#modalsize").addClass("modal-lg");
                </script>
            <?php      }else{    ?>
                <script>
                    $("#modalsize").removeClass("modal-lg");
                    $("#modalsize").addClass("modal-xl");
                </script>
            <?php          }
            foreach ($recognized_employees as $recogemp => $remp) {
                $first_emp = $remp;
                ?>
                <div class="col-lg-<?php echo $size; ?> float-left p-10">
                    <div class="card-stretch gutter-b shadow-lg">
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
                                <?php echo $badges[$badgeid]['badge_name']; ?> &nbsp;  <img class="ml-3" src="uploads/all/<?php echo $badges[$badgeid]['image']; ?>" style="
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

                        <div class="row">
                            <div class="col-3 mt-3  p-l-20 ">
                                <p class="m-b-5"><a href="#" class="font-weight-bolder text-primary ml-2"><?php echo date('d M, Y'); ?></a></p>

                            </div>
                            <div class="col-4 mt-3">


                            </div>
                            <div class="col-5 p-b-10">

                                <div class="d-inline-block mt-3">
                                    <a class="font-weight-bolder text-primary ml-2">  <?php echo $emplist[$_SESSION['emp_id']]['display_name']; ?></a>

                                </div>

                                <?php if($emplist[$_SESSION['userid']]['image']!="") { ?>
                                    <img src="uploads/all/<?php echo $emplist[$_SESSION['emp_id']]['image']; ?>"  class="img-radius img-50 align-top m-r-15" style="z-index: 9;" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"  style="z-index: 9; left: 10px; top: 7px; z-index: 9;"><?php echo initials($emplist[$_SESSION['emp_id']]['display_name']); ?></span>
                                <?php } ?>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        <?php  } ?>
    </div>
</div>

<input type="hidden" id="display_announcement" value="<?php echo $_GET['display_in_announcement'];?>">
<input type="hidden" id="recognition_type" value="<?php echo $recognition_type;?>">
<input type="hidden" id="number_of_points" value="0">
<input type="hidden" id="image" value="">
<input type="hidden" id="recognition_from" value="<?php echo $_SESSION['userid'];?>">
<input type="hidden" id="comments" value="<?php echo $_GET['comments'];?>">
<input type="hidden" id="company_announcement" value="<?php echo $_GET['company_announcement_id'];?>">
<input type="hidden" id="recognition_to" value="<?php echo $_GET['recognized_employees'];?>">
<input type="hidden" id="badge_id" value="<?php echo $_GET['badgeid'];?>">
<input type="hidden" id="rowid" value="<?php echo $_GET['rowid'];?>">
<div class="modal-footer">
    <button type="button" class="btn btn-danger waves-effect " data-bs-dismiss="modal">Cancel</button>
    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="confirmrecognition();" name="submitform" value="exit"><i class="la la-check"></i>Confirm</button>
</div>

</div>
