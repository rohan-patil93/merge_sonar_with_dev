<?php
$_SESSION['apply_js']['multiselest'] = 1;
if(isset($_POST['submit'])){
    $_POST['company_id']=$_SESSION['company_id'];
    $_POST['user_id']=$_SESSION['userid'];
    $_POST['email_check'] =0;
    $_POST['display_name'] = $_POST['first_name']." ".$_POST['last_name'];
    $url = API."?mode=insertregistiredgroups&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
    $response = $response1['application_users_insert'];
    if($response1['error'] !=""){
        $error_message = $_SESSION['error_message'][$response1['error']];
    }else{
        $_SESSION['insert_msg'] = '9095';
        header("location:index.php?option=COM_LIST_REGISTERED_GROUPS"); exit;
    }

}

$wh2["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$wh2["field"] = "id,module_id,module_name";
$data2 = urlencode(json_encode($wh2));
$url2 = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$data2;
$moduleres = callAPI($url2);
$moduleres = json_decode($moduleres,1);
foreach($moduleres as $moduleres){
    if($moduleres['module_id'] != 0){
        $module[$moduleres['module_id']] = $moduleres['module_name'];
    }
}

?>
<script type="text/javascript">
    function chnageModule(){
        var module_id = $("#module_id").val();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=getroleswithmodulid&module_id='+module_id,
            success: function(response){
                console.log(response);
            }
        });
    }
    function checkdomainname(id,field,type){
      var value = $("#"+id).val();
      $.ajax({
          url: "ajax.php",
          data:'page=checkdomainavailability&domain='+value+'&field='+field,
          type: "POST",
          success:function(data){
            var res = JSON.parse(data);
            $("#"+type).hide();
            if(res.res == 'true'){
              $("#"+type).show();
              $("."+type).val('1');
            }else{
              $("."+type).val('0');
            }
            validationcheck();
          },
          error:function (){}
      });
}
function validationcheck(){
    if($(".doimainregistererrer").val() == '0' && $(".emailregistererrer").val() == '0'){
        console.log("No");
        $("#submit_value_exit").removeClass("disabled");
    }else{
        console.log("Yes");
        $("#submit_value_exit").addClass("disabled");

    }
}

</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <?php
 if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
        <div class="card card-custom card-sticky ">
            <div class="card-header">
                <h5>Invited User</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-block ">
                <form method="post">
                <div class="card-body card-infisuite">
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <span class="form-label">Domain Name</span>
                            <div class="input-group input-group-button mb-0">
                                <input aria-label="Domain Name" type="text" class="b-radius-5 f-w-600 text-muted form-control width1 text-lowercase" name="domain_name" id="domain_name" data-bs-toggle="tooltip" value="" data-placement="bottom" title="Domain Name" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)" onblur="checkdomainname('domain_name','domain_name','doimainregistererrer')">
                                <span class="input-group-text addon-style">
                                    <span class=""><?php echo getMainDomainName(); ?></span>
                                    <input type="hidden" name="host_domain_name" value="<?php echo $_SERVER['HTTP_HOST']; ?>">
                                </span>
                            </div>
                            <span class="text-danger f-12" id="doimainregistererrer" style="display:none">Domain Already Register</span>
                        </div>

                        <div class="form-group col-lg-4">
                            <span class="form-label">Company Name</span>
                            <input aria-label="Company Name" class="form-control" type="text" id="company_name" name="company_name" value="" required="">
                        </div>
                        <div class="form-group col-lg-4">
                            <span class="form-label">Module</span>
                            <select aria-label="Module" class="form-control js-example-basic-single" required="" name="module_id">
                            <?php foreach($module as $key=>$modulename){
                                    ?>
                                    <option value="<?php echo $key; ?>"><?php echo $modulename; ?></option>
                            <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">First Name</span>
                            <input aria-label="First Name" class="form-control" type="text" id="first_name" name="first_name" value="" required="">
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Last Name</span>
                            <input aria-label="Last Name" class="form-control" type="text" id="last_name" name="last_name" value="" required="">
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Email Id</span>
                            <input aria-label="Email" class="form-control" type="email" id="email_id" name="email_id" value="" required="" onblur="checkdomainname('email_id','email_id','emailregistererrer')">
                            <span class="text-danger f-12" id="emailregistererrer" style="display:none">Email Id Already Register</span>
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Mobile No.</span>
                            <input aria-label="User Mobile" class="form-control" type="number" id="user_mobile" name="user_mobile" value="" required="">
                        </div>

                    </div>
                </div>
                <div class="card-footer p-0">
                    <div class="row">
                        <div class="col-lg-9">
                            <input type="hidden" class="doimainregistererrer" value="1">
                              <input type="hidden" class="emailregistererrer" value="1">
                            <input type="hidden" name="submit" value="submit">
                            <button  class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 disabled" name="submit_value_exit" id="submit_value_exit" value="exit" ><i class="la la-check"></i>Save &amp; exit</button>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
