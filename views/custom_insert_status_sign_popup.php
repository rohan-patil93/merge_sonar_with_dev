<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$nm = ViewStatusValue($_POST['status']);
?>
<script src="assets/js/signature_pad.umd.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script>
    var signaturePad;
    var signBast64JPEGContent;
    window.addEventListener('load', function() {
        var signPadCanvas = document.querySelector('#signPadCanvas');
        var ratio =  Math.max(window.devicePixelRatio || 1, 1);
        signPadCanvas.width = signPadCanvas.offsetWidth * ratio;
        signPadCanvas.height = signPadCanvas.offsetHeight * ratio;
        signPadCanvas.getContext("2d").scale(ratio, ratio);
        signaturePad = new SignaturePad(signPadCanvas, {backgroundColor: 'rgb(218, 220, 220)'});
    });

    function confirmSignature() {
        if(signaturePad.isEmpty()) {
            alert("Signature Pad Is Empty.");
            $("#submitpopup_btn").addClass("disabled");
        }else{
            $("#submitpopup_btn").removeClass("disabled");
            signBast64JPEGContent = signaturePad.toDataURL("image/jpeg");
            document.getElementById('signPadCanvas').setAttribute("style", "pointer-events: none;");
    document.getElementById('pickedSignContent').value = signBast64JPEGContent;///testing line, to be removed in production.
}

}

</script>

<script type="text/javascript">
    function submitrom(){
        var  str = '';

        var formdata = $("#signature-popup").serialize();
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=updatestatuscandidatesign&'+formdata,
            success: function(response) {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['9093']; ?>','');
                $('#kt_modal_KTDatatable_remote').modal('hide');
                location.reload();
            }
        });
    }
</script>
    <div class="modal-header">
        <h5 class="modal-title">
        <small>&nbsp;&nbsp; <b class="f-w-600">Transaction Type: </b>&nbsp; <?php echo viewtitle($_POST['table']); ?> &nbsp;&nbsp;
            <b class="f-w-600">Status: </b>&nbsp; <?php echo $nm['name']  ?> </small>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <form id="signature-popup" method="post">
        <div class="modal-body">
            <img src="" id="my_sign" alt="Preview">
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <span>Comment</span>
                        <textarea aria-label="Comment" class="form-control mt-2" name="comment" id="comment"></textarea>
                    </div>
                </div>
            </div>
            <span class="form-lable m-l-20">Signature</span>
            <div id="signPadContainer"  style="text-align: center;">
                <canvas id="signPadCanvas" disabled="disabled"  style="border: 0px solid lightblue; box-shadow: rgba(0, 0, 0, 0.75) 3px 5px 5px 0px;touch-action: none;" height="300" width="300" class="rr"></canvas>
                <div class="mt-2">
                    <button type="button" onclick="signaturePad.clear();document.getElementById('signPadCanvas').setAttribute('style', 'pointer-events: auto;');">Reset</button>
                    <button type="button" onclick="confirmSignature();">Confirm</button>
                </div>
                <p>Please click here to confirm button  after signature</p>
            </div>


            <input type="hidden" name="sign" id="pickedSignContent" value="">
            <div class="modal-footer">
                <input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status']; ?>">
                <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
                <input type="hidden" name="hid" id='rid' value="<?php echo $_POST['rid']; ?>">

                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary mr-2 disabled" onclick="submitrom()" id="submitpopup_btn" >Submit</button>
            </div>

        </div>
    </form>
