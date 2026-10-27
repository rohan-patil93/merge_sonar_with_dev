<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submitfollowupactivity'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertfollowuplevels&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
    if($response['LASTID'] == 0){
            $msgcode = 3001; //update
            $_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            $rid = deocdeId("id:".$response['LASTID']);
            header("location:index?option=COM_INSERT_FOLLOW_UP_LEVELS&Rid=".$rid);
        }
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}
if($_SESSION['insert_msg'] !=""){
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = "";
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
            }
        }
    </script>
    <?php
}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getfollowuplevels&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$email_content = $result['email_content'];
$follow_up_levels = $result['follow_up_levels'];
$follow_up_levels_all = $result['follow_up_levels_all'];
?>
<script type="text/javascript">
    function getSendEmail(){
        if($("#send_email").is(':checked')){
            $(".email_content").show('slow');
        }else{
            $(".email_content").hide('slow');
        }
    }


</script>
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h5>Follow Up Levels</h5>
                <div class="card-header-right table-card-header">
                    <a href="index.php?option=COM_INSERT_FOLLOW_UP_LEVELS" class="btn btn-success mb-2 desktop-view"><i class="icofont icofont-plus icon-white "></i>Follow Up Levels</a>
                    <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col" class="text-right">Days</th>
                                <th scope="col">SMS</th>
                                <th scope="col">Call</th>
                                <th scope="col">Email</th>
                                <th scope="col">Attach Invoice</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $mk=1;foreach($follow_up_levels_all as $ful){ ?>
                            <tr>
                                <td class="p-2"><?php echo $mk; ?></td>
                                <td class="p-2"><a href="index.php?option=COM_INSERT_FOLLOW_UP_LEVELS&Rid=<?php echo deocdeId("id:".$ful['id']); ?>" class="text-primary"><?php echo $ful['follow_up_level_name']; ?></a></td>
                                <td class="text-right p-2"><?php echo $ful['days']; ?></td>
                                <td class=" p-2"><?php echo $ful['send_sms']=="0"?"Y":"N"; ?></td>
                                <td class=" p-2"><?php echo $ful['follow_up_call']=="0"?"Y":"N"; ?></td>
                                <td class=" p-2"><?php echo $ful['send_email']=="0"?"Y":"N"; ?></td>
                                <td class=" p-2"><?php echo $ful['attach_invoice']=="0"?"Y":"N"; ?></td>
                            </tr>
                        <?php  $mk++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h5>Follow Up Levels</h5>
                <div class="card-header-right table-card-header">

                </div>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="row">
                        <input type="hidden" name="depreciation_method"  id="depreciation_method" value="SL">
                        <div class="form-group col-lg-8 col-md-8 col-sm-8 col-12">
                            <span class="form-label">Follow Up Level Name</span>
                            <input aria-label="Follow Up Level Name" class="form-control"  type="text"  id="follow_up_level_name" name="follow_up_level_name"  value="<?php echo $follow_up_levels['follow_up_level_name']; ?>" required>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-12">
                            <span class="form-label">Days</span>
                            <input aria-label="Days" class="form-control text-right"  type="number"  id="follow_up_level_name_2" name="days"  value="<?php echo $follow_up_levels['days']; ?>" required>
                        </div>

                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-4">
                            <span class="form-label d-block" >Send SMS</span>
                            <input aria-label="Send Sms" class="js-primary-sm" type="checkbox" name="send_sms" <?php echo $follow_up_levels['send_sms'] =='0'?"checked":""; ?> value="0">
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-4">
                            <span class="form-label d-block" >Call</span>
                            <input aria-label="Follow Up Call" class="js-primary-sm" type="checkbox" name="follow_up_call" <?php echo $follow_up_levels['follow_up_call'] =='0'?"checked":""; ?> value="0">
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-4 col-4">
                            <span class="form-label d-block">Send Email</span>
                            <input aria-label="Send Email" class="js-primary-sm" type="checkbox" name="send_email" id="send_email" <?php echo $follow_up_levels['send_email'] =='0'?"checked":""; ?> value="0" onchange="getSendEmail()">
                        </div>
                        <div class="form-group col-lg-8 col-md-12 col-sm-12 col-12 w-full email_content"  <?php echo $follow_up_levels['send_email'] =='0'?"":"style='display:none'"; ?> >
                            <span class="form-label d-block">Email Template</span>
                            <select aria-label="Email Content" name="email_content_id" class="form-control form-select js-example-basic-single">
                                <option value="">Select Any One </option>
                                <?php foreach($email_content as $ecid=>$ecname) {  ?>
                                    <option value="<?php echo $ecid; ?>" <?php echo $follow_up_levels['email_content_id'] ==$ecid?"selected":""; ?>><?php echo $ecname;?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-4 email_content" <?php echo $follow_up_levels['send_email'] =='0'?"":"style='display:none'"; ?>>
                            <span class="form-label d-block">Attach Invoice</span>
                            <input aria-label="Attach Invoice" class="js-primary-sm" type="checkbox" name="attach_invoice" <?php echo $follow_up_levels['attach_invoice'] =='0'?"checked":""; ?> value="0">
                        </div>
                    </div>
                    <div class="mb-4">
                        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                        <button type="submit" class="btn btn-primary waves-effect waves-light" name="submitfollowupactivity"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
