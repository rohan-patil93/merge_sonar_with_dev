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
    function listchangebase(id,page_name){
    var preport = $('#page_id').val();
    var pagename = $('#ppage_name').val();
    pagename = "'"+pagename+"'";
    var arr = '<button type="button" class="btn p-0 border-0 bg-transparent ti-angle-left" onclick="listchangebase('+preport+','+pagename+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>';
         $.ajax({
        url: "ajax.php",
        data:'page=reportfloderlist&report_id='+id+'&page_name='+page_name,
        type: "POST",
        success:function(data){
            console.log(data);
        document.getElementById('backid').innerHTML = arr;
        document.getElementById('title').innerHTML = page_name;
            document.getElementById('displaylist').innerHTML = data;
        },
        error:function (){}
        });
    }
    function submitrom(){
        var  str = '';
         str = 'table=my_reports&fid=parent_id&hid='+$('#report_id').val()+'&rid='+$('#page_id').val();
          console.log(str);
          $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=updatedynamicfied&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                    title: "Folder is moved successfully",
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

    function createfloder(report_id){
         jQuery.ajax({
            url: "views/custom_insert_floderreport_save_popup.php",
            data:'report_id='+report_id+'&parent_id'+$('#page_id').val(),
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header bg-primary" style="background-image: none;">
    <span id="backid" class="m-t-5">  </span> &nbsp;
    <h5 class="modal-title" id="title"> Move To Folder </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body" >
        <form class="kt-form kt-form--label-right" action="" method="post">
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10" style="height: 336px;">
                <div class="form-group row">
                    <div class="col-lg-12  custom-scroll" id="displaylist" style="height:325px;">
                        <input type="hidden" id="page_id" value="0"/>
                        <input type="hidden" id="ppage_name" value="Move to folder"/>
                        <?php foreach ($parent_data as  $value){ ?>
                         <div class="col-lg-12 row mb-3 f-16" style="cursor: pointer;">
                            <div class=" col-lg-1">
                                <i class="ti-folder"></i>
                            </div>
                            <div class=" col-lg-10">
                                <a href="javascript:void(0)">
                                 <?php echo $value['page_name']; ?></a>
                            </div>
                            <div class=" col-lg-1" onclick="listchangebase(<?php echo $value['id'].",'".$value['page_name']."'"; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                <a href="javascript:void(0)"><i class="ti-angle-down"></i></a>
                            </div>
                        </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
            <div class="modal-footer pt-4 d-block">
                <button type="button" class="btn btn-success mr-2 float-left" onclick="createfloder(<?php echo $_POST['report_id']; ?>)" ><i class="ti-plus m-r-5"></i> Create Folder</button>
                <input type="hidden" name="status" id='report_id' value="<?php echo $_POST['report_id']; ?>">

                <button type="button" class="btn btn-primary mr-2 float-right" onclick="submitrom()" >Move To Folder</button>
            </div>
        </form>
    </div>
</div>
