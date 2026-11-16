<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['emp_fd_id'];
$apiurl = API."?mode=getemployeefeedbacks&rid=".$rid;
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$employee_feedbacks = $result['employee_feedbacks'];
$emp_answer = $employee_feedbacks['response'];
$question_options = json_decode($employee_feedbacks['question_options'],1);
$display_answer = json_decode($employee_feedbacks['display_answer'],1);
?>
<script type="text/javascript">
    function submitfeedbackform(){
        var formdata = $("#feedback_form").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updateemployeefeedbacks&'+formdata,
            type: "POST",
            success:function(res){
                $('#popup1').html('');
                $("#kt_modal_KTDatatable_remote").modal("hide");
                swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['3001']; ?>', "success");
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title"><?= __('Feed Back Respons') ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body" <?php if($_POST['emp_id'] == $_SESSION['emp_id']){
}else{ ?>style="pointer-events: none; opacity: 0.7;" <?php
} ?>>
    <form id="feedback_form">
        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
        <input type="hidden" name="answer_type" value="<?php echo $employee_feedbacks['answer_type']; ?>">
    <span class="form-label d-block"><?php echo $employee_feedbacks['question']; ?></span>
    <?php
            if($employee_feedbacks['answer_type'] == 'MC'){
                $emp_answer = json_decode($emp_answer,1); ?>
                <?php foreach($question_options as $qo){ ?>
                    <div class="checkbox-fade fade-in-primary mr-5">
                        <label class="form-label">
                            <input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" value="<?php echo $qo; ?>" name="response[]" <?php echo in_array($qo,(array)$emp_answer)?"checked":""; ?>>
                            <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span><span><?php echo $qo; ?></span>
                        </label>
                    </div>
                <?php } ?>
                <?php }elseif($employee_feedbacks['answer_type'] == 'O'){?>
                    <select aria-label="<?= htmlspecialchars(__('Response'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control js-example-basic-single" name="response">
                        <option value=""><?= __('Select Any One') ?></option>
                        <?php foreach($question_options as $qo){ ?>
                                <option value="<?php echo $qo; ?>" <?php echo $qo ==$emp_answer?"selected":""; ?>><?php echo $qo; ?></option>
                        <?php } ?>
                    </select>
                <?php }else{ ?>
                    <textarea aria-label="<?= htmlspecialchars(__('Response'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" name="response"><?php echo $emp_answer; ?></textarea>
                <?php } ?>
        </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close') ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitfeedbackform()"><?= __('Submit') ?></button>
</div>
