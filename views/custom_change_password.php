<?php    session_start();   ?>

<script type="text/javascript">
    function submitresetdata(){
         $(".old_password").text("");
         $(".new_password").text("");
          $(".confirm_password").text("");
          $("#error_message").text("");
        var old_password = $("#old_password").val();
        var new_password = $("#new_password").val();
        var confirm_password = $("#confirm_password").val();
         var errorr = 0;
        var emty = 0;
      
        if(old_password==""){
            errorr = 1;
            emty = 1;
            $(".old_password").text("Requerd fileds");
        }
        if(new_password==""){
            errorr = 1;
            emty = 1;
            $(".new_password").text("Requerd fileds");
        }
        if(confirm_password==""){
            errorr = 1;
            emty = 1;
            $(".confirm_password").text("Requerd fileds");
        }

        if(old_password==confirm_password && emty == 0){
            errorr = 1;
        //Old password and confirm password are not same
              $("#error_message").html('<div class="alert alert-warning alert-dismissible"><strong class="f-14"><?php echo $_SESSION['error_message'][3027]; ?></strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        }
        
        if(new_password == confirm_password){
            if(errorr == 0){
        $.ajax({
            type:"POST",
            url:"ajax.php",
            data:$("#passresetfrm").serialize()+"&page=resetnewpassword",//only input
            success: function(response){
                     console.log(response);
                var data = JSON.parse(response);
             console.log(data);
                if(data.status == '404'){  
                   // tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger  pt-2 pb-2','animated flipInX','animated flipOutX','old password not match!','error');
                //code Old password entered is incorrect
                $("#error_message").html('<div class="alert alert-warning alert-dismissible"><strong class="f-14"><?php echo $_SESSION['error_message'][3026]; ?></strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                }else if(data.status == "fail"){
                      $("#error_message").html('<div class="alert alert-warning alert-dismissible"><strong class="f-14">'+data.message+'</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

                    //tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger  pt-2 pb-2','animated flipInX','animated flipOutX',,'Error');
                }else{
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Password Updated','Success');
                    window.location.href = "custom_password_reset_success_page.php";
                }

            }
        }); 
        }
        }else{
             $("#error_message").html('<div class="alert alert-warning alert-dismissible"><strong class="f-14"><?php echo $_SESSION['error_message'][3028]; ?></strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');

          //  tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger  pt-2 pb-2','animated flipInX','animated flipOutX','Both Value Not Same','');
        } 
    }
</script>

<?php  $Rid = (isset($_POST['Rid']))?$_POST['Rid']:$_SESSION['userid']; ?>

<div class="card" >
    <div class="card-header">
        <h5>Change password</h5>
    </div>
    <div class="card-body j-wrapper  j-wrapper-40p" >
        <form method="post" id="passresetfrm" > 
             <div class="row p-2" id="error_message"></div>  
                <input type="hidden" name="Rid" value="<?php echo $Rid; ?>"> 
                <div class="m-b-20 ">

                    <div class="row">
                        <label class="form-label col-sm-4 col-lg-4 col-form-label">Old Password</label>
                        <div class="col-sm-8 col-lg-8">
                            <input type="Password" name="old_password" id="old_password" class="form-control ">
                            <span class="f-12 text-danger old_password"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <label class="form-label col-sm-4 col-lg-4 col-form-label">New Password</label>
                        <div class="col-sm-8 col-lg-8">
                            <input type="Password" name="new_password"  id="new_password"  class="form-control ">
                            <span class="f-12 text-danger new_password"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <label class="form-label col-sm-4 col-lg-4 col-form-label">Confirm Password</label>
                        <div class="col-sm-8 col-lg-8">

                            <input type="Password" name="confirm_password" id="confirm_password" class="form-control">
                            <span class="f-12 text-danger confirm_password"></span>
                        </div>

                    </div>
                    <div class="row mt-5"><div class="col-lg-7"></div>
                    <div class="col-lg-5 text-right ">
                        <button  type="button" onclick="submitresetdata()" class="btn btn-info waves-effect waves-light " name="submit"> Change Password</button>
                    </div>
                </div>


            </div>
        </form> 
    </div>
</div>
