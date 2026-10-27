<?php  $Rid = (isset($_POST['Rid']))?$_POST['Rid']:$_SESSION['userid']; ?>

<div class="card" >
    <div class="card-header">
        <h5>Mobile OTP</h5>
    </div>
    <div class="card-body j-wrapper  j-wrapper-40p" >
        <form method="post" id="passresetfrm" >
             <div class="row p-2" id="error_message"></div>
                <input type="hidden" name="Rid" value="<?php echo $Rid; ?>">
                <div class="m-b-20 ">

                    <div class="row">
                        <span class="form-label col-sm-4 col-lg-4 col-form-label">OTP</span>
                        <div class="col-sm-8 col-lg-8">
                            <input aria-label="Input field" type="text" readonly class="form-control" value="<?php echo $result['otp']; ?>">
                        </div>
                    </div>

                </div>

            </div>
        </form>
    </div>
</div>
