<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $listcompliancesurl = API."?mode=getcompliancesdashbord&type=getsingletaskhistory&id=".$_POST['id'];
    $listcompliancesres = callAPI($listcompliancesurl);
    $listcompliances = json_decode($listcompliancesres, 1);
    $compliancetaskhistory = $listcompliances['compliance_task_histories'];

    $check_list = json_decode($compliancetaskhistory['check_list'], 1);
?>
<script>
function submitform(){
        var rid = $("#riddd").val();
        var formdata = $('#frmsubmit').serialize(); //This will capture all form input values, no need to reinvent the wheel
          $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertcompliancetaskanswer&'+formdata,
            success: function(response){
                  $('#kt_modal_dynamic_popup').modal("hide");
                var jsonres = JSON.parse(response);
                if(jsonres.status == "199"){
                    $("#statusid_"+rid).html('<span class="form-label label label-success">Completed</span>');
                }
            }
        });
    }

    </script>
<div class="modal-header">
    <h5 class="modal-title">
        <?php echo $compliancetaskhistory['name']; ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="closebutnfunction()"></button>
</div>
<div class="modal-body" >
    <form class="kt-form kt-form--label-right" action="" id="frmsubmit" method="post">
        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
            <div class="form-group row">
                <div class="col-lg-12">
                    <span><?php echo $check_list[$_POST['aryposition']]['name']; ?></span>
                    <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                </div>
            </div>
        </div>
        <input type="hidden" name="aryposition" id='aryposition' value="<?php echo $_POST['aryposition']; ?>">
    <input type="hidden" name="rid" id='riddd' value="<?php echo $_POST['id']; ?>">
    <input type="hidden" name="check_list" id='check_list' value='<?php echo $compliancetaskhistory['check_list']; ?>'>

    </form>
</div>
<div class="modal-footer">

    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal" onclick="closebutnfunction()">Close</button>
    <button type="button" class="btn btn-primary mr-2" onclick="submitform()" >Submit</button>
</div>
