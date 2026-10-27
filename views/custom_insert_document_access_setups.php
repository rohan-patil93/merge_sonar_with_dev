<?php
if(isset($_POST['submit']))
{
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $apiurl = API."?mode=insertdocumentaccesssetups&method=post";
    $postapi = file_post_contents($apiurl, $_POST);
    $response = json_decode($postapi,1);
    if($response['insertdoc']['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }else{
        if($_POST['rid'] == ''){
        }else{
        ?>
        <script type="text/javascript">
          document.onreadystatechange = function () {
            if (document.readyState == "complete") {
              tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
            }
          }
      </script>
        <?php
    }
    }
}
$_SESSION['apply_js']['multiselest'] = 1;
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$ex_rid = explode(':', $rid);
$Rid = $ex_rid['1'];
$apiurl = API."?mode=getdocumentaccesssetups&rid=".$Rid;
$callapi = callAPI($apiurl);
$result = json_decode($callapi,1);
$document_access_setups = $result['document_access_setups'];
$application_users = $result['application_users'];
$table_list = $result['table_list'];
$create_access_users = json_decode($document_access_setups['create_access_users'],1);

?>
<script type="text/javascript">
    function whoCreateType(vl){
        if(vl=='S'){
            $("#selectlistusers").show('slow');
            $("#uservalidationtable").hide('slow');
        }else if(vl=='A'){
            $('.userchecklist').prop("checked", true);
            $("#selectlistusers").hide('slow');
            $("#uservalidationtable").show('slow');
        }else{
            $("#selectlistusers").hide('slow');
            $("#uservalidationtable").hide('slow');
        }
    }
    function viewcheckallUsers(val){
        if ($('#checkalluser').prop("checked")){
            $('.userchecklist').prop("checked", true);
        } else {
            $('.userchecklist').prop("checked", false);
        }
    }

    function nextSteps(){
        var userarray = [];
        var rid = $("#rid").val();
        var transaction_table = $("#transaction_table").val();
        $('.userchecklist').each(function() {
            if($(this).is(":checked")) {
                userarray.push($(this).val());
            }
        });
        console.log(userarray);
        $.ajax({
            url: "views/custom_insert_document_access_setups_ajax.php",
            type: "POST",
            data: 'user_ids='+userarray+'&ajaxcall=Y&Rid='+rid+'&transaction_table='+transaction_table,
            success: function(response){
               console.log(response);
                $("#uservalidationtable").html(response);
                $("#uservalidationtable").show('slow');
                 $(".js-example-basic-single").select2();
                 $(".js-example-basic-multiple").select2();
            }
        });
    }
    function onBeHalfType(val,uid){
        $("#foruser_"+uid).hide();
        if(val == "U"){
            $("#foruser_"+uid).show();
        }
    }

</script>
<div class="card">
    <div class="card-header table-card-header">
        <h5>Document Access Setups</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body ">
        <form  method="post">
            <input type="hidden" name="rid"  id="rid" value="<?php echo $Rid; ?>">
         <div class="row bg-gray">
                 <p class="f-16 f-w-600">Who Can Create
                     <span>
                         <select aria-label="Transaction Table" class="js-example-basic-single" name="transaction_table" id="transaction_table">
                            <option value="">Select Any One</option>
                            <?php foreach($table_list as $res){?>
                                <option value="<?php echo $res; ?>" <?php echo $document_access_setups['transaction_table'] == $res?"selected":""; ?>><?php echo ucwords(str_replace("_"," ",($res))); ?></option>
                            <?php } ?>
                        </select>
                     </span> ?
                     <span>
                         <select aria-label="User Access Type" class="js-example-basic-single" onchange="whoCreateType(this.value)" name="user_access_type">
                             <option value="0" >Select Any One</option>
                             <option value="A" <?php echo $document_access_setups['user_access_type'] == "A"?"selected":""; ?>>All Users</option>
                             <option value="S" <?php echo $document_access_setups['user_access_type'] == "S"?"selected":""; ?>>Specific Users</option>
                         </select>
                     </span>
                  </p>
         </div>
         <div class="bg-gray align-items-center" id="selectlistusers" style="<?php echo ($document_access_setups['user_access_type'] == "A" || $document_access_setups['user_access_type'] =="")?"display: none":""; ?>">
                     <div class="row mb-2">
                         <di class="col-lg-2">
                             <p class="f-16 f-w-600">Select Users?</p>
                         </di>
                         <div class="col-lg-2">
                             <div class="checkbox-fade fade-in-primary ">
                                  <label class="form-label ">
                                <input aria-label="Checkalluser" type="checkbox" value="0" id="checkalluser" onchange="viewcheckallUsers()" checked="">
                                <span class="cr" >
                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                                <span>Check All</span>
                              </label>
                             </div>
                        </div>
                        <div class="col-lg-6" ></div>
                        <div class="col-lg-2" >
                            <div class="input-group input-group-button mb-0" >
                                <input aria-label="Search" type="text" id="dynamicfilterhideshow" data-id='userselectfilter' class="form-control form-control-sm width1"  placeholder="Search" data-type="div" data-bs-toggle="tooltip" data-placement="bottom" title="Search">
                                <span class="input-group-text addon-style">
                                    <span class=""><i class="ti-search"></i></span>
                                </span>
                            </div>
                        </div>
                    </div>
                <div class="row">
                 <?php foreach($application_users as $appuser){ ?>
             <div class="col-lg-2 p-0" id="userselectfilter_2">
                 <div class="align-middle float-left ml-2">
                     <div class="checkbox-fade fade-in-primary m-0 d-inline-block">
                      <label class="form-label m-0">
                        <input aria-label="Checkbox" type="checkbox" value="<?php echo $appuser['id']; ?>" name="create_access_users[]" class="userchecklist" <?php echo in_array($appuser['id'],(array)$create_access_users)?"checked":""; ?>>
                        <span class="cr m-0" style="height: 10px;width:10px">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span>
                      </label>
                    </div>

                  <?php if($appuser['user_photo']!=""){?>
                    <img src="uploads/all/<?php echo $appuser['user_photo']; ?>"   class="img-radius img-30 align-top m-r-15" alt="User Profile">
                  <?php }else{ ?>
                    <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($appuser['display_name']); ?></span>
                  <?php } ?>
                  <div class="d-inline-block">
                    <p class="f-10"><?php echo $appuser['display_name']; ?></p>
                  </div>
                </div>
             </div>
             <?php } ?>

         </div>
         <div class="text-right">
         <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold" type="button" onclick="nextSteps()"><i class="la la-check"></i>Next</button>
        </div>
    </div>
    <div class="row bg-gray align-items-center" id="uservalidationtable" style="<?php echo ($document_access_setups['id'] =="")?"display:none":""; ?>">
        <?php
            $_POST['ajaxcall'] ="Y";
            $_POST['Rid'] =$Rid;
            $_POST['user_ids'] = implode(",",(array)$create_access_users);
            $_POST['transaction_table'] = $document_access_setups['transaction_table'];
            include_once 'views/custom_insert_document_access_setups_ajax.php'; ?>

    </div>

    </form>
    </div>
</div>
