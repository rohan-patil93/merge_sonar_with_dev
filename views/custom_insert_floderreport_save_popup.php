<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$data12["where"]['is_active'] = '0';
$data = urlencode(json_encode($data12));
$itemurl = API."?mode=gettabledetailswithconditions&table=my_reports&data=".$data;
$item_data = callAPI($itemurl);
$row = json_decode($item_data, 1);
foreach($row as $rowdata){
    $list_breadcum[$rowdata['id']] = array('parent_id'=>$rowdata['parent_id'],'name'=>$rowdata['page_name'],'id'=>$rowdata['id'],'type'=>'MYR');
    if(($rowdata['parent_id'] == '0' && $fld=="") || ( $fld == $rowdata['parent_id'] && $rowdata['header'] == '')){
        $parent_data[] = $rowdata;
    }else{
        $submain_data[$rowdata['parent_id']][] = $rowdata;
    }
}
?>
<script type="text/javascript">

    function submitflderrom(){
        var  str = '';
         str = 'report_id='+$('#report_id').val()+'&floder_name='+$('#floder_name').val()+'&parent_id'+$('#parent_id').val();
          console.log(str);
          $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=addfloderupdatereportid&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                    title: "Folder is create successfully",
                    type: "success",
                    buttonsStyling: false,
                    confirmButtonText: "<i class='la la-hand-o-up'></i>OK",
                    confirmButtonClass: "btn btn-success",
                    showCancelButton: false

                }).then(function(result) {
                    if (result.value) {
                        $('#kt_modal_KTDatatable_remote').modal('hide');
                        window.location.reload();
                    }
                });
            }
        });
    }


</script>
<div class="modal-header">
    <span id="backid">  </span> &nbsp;
    <h5 class="modal-title" id="title"> Save to floder </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" >
        <form class="kt-form kt-form--label-right" action="" method="post">
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <textarea aria-label="Name new floder" class="form-control" name="floder_name" id="floder_name" placeholder="Name new floder"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="report_id" id='report_id' value="<?php echo $_POST['report_id']; ?>">
                <input type="hidden" name="parent_id" id='parent_id' value="<?php echo $_POST['parent_id']; ?>">

                <button type="button" class="btn btn-primary mr-2" onclick="submitflderrom()" >ADD to new floder</button>
            </div>
        </form>
    </div>
</div>
