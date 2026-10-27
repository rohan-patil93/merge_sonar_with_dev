<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$toplist = array('Project', 'Team', 'Task');
if(isset($_POST['id']) && $_POST['id']!=""){
    $configurl = API."?mode=singlerowtabledata&fid=id&rid=".$_POST['id']."&table=project_work_spaces";
    $data_details = callAPI($configurl);
    $data = json_decode($data_details,1);
}
$list_project = array('0'=>'Work Space','1'=>'Project Type', '2'=>'Projeject Settings', '3'=>'Project Team');
?>

<script type="text/javascript">
    function submitdrom(){
        console.log("Data");
        var id =$('#id').val();
        var work =$('#work_space').val();
        if(work !=""){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=setupcomponents&table_name=project_work_spaces&name='+work+'&id='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                var msg = 'Project Work Space Inserted Successfully';
                if(id!=""){
                    msg = 'Project Work Space Updated Successfully';
                }
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
                window.location.reload();
            },
            error:function (){}
        });
    }else{
        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','Please Fill Workspace Name','');
    }
    }

</script>

<div class="modal-header">
    <h5 class="modal-title"> <?php if($_POST['id']!=""){ echo "Edit";
}else{ echo "Create";
} ?> Work Space</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form class="kt-form kt-form--label-right" action="" id="submitnextform" method="post">
    <div class="modal-body" >
        <div class="pcoded-content">
            <div class="pcoded-inner-content">
                <div class="main-body">

                    <div class="row " id="checknext0" >
                        <div class="col-lg-5 mx-auto" >
                            <div class="form-group mt-4 w-full">
                                <span class="f-w-600 mb-2"> Work Space</span></br>
                                <input aria-label="Work Space" class="form-control " type="work_space" placeholder="" name="work_space" id="work_space" value="<?php echo $data['name']; ?>">
                            </div>
                            <input type="hidden" name="id" id="id" value="<?php echo $data['id']; ?>">
                            <button class="btn btn-primary waves-effect waves-light float-end p-2" type="button" onclick="submitdrom()" >Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

