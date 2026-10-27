
<?php
    include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';

      $url = API."?mode=getjobhiringsource&job_post_id=".$_POST['job_post_id'];
    $api = callAPI($url);
    $resultt = json_decode($api,1);
    $job_postings = $resultt['job_postings'];
    $tabledata = $resultt['job_hiring_source'];
    $hirdata = $resultt['job_posting_publish_details'];

  foreach($hirdata as $hirs){
      $publish[$hirs['job_hiring_source_id']] = $hirdata['current_status'];
      $pubids[] = $hirs['job_hiring_source_id'];
  }
?>
<script type="text/javascript">
    function submitrom(){
          var  str = '';
          status = $('#status_value').val();
             str = 'table='+$('#table').val()+'&comment='+$('#comment').val()+'&status='+status+'&hid='+$('#rid').val()+'&extra='+$('#extra').val();
          console.log(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=dynamicchangestatus&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: "Comments Updated ",
                            text: "Do you want to close ? ",
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                            confirmButtonClass: "btn btn-success",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                                window.location.reload();
                            }
                        });
            }
        });
    }
    function shareinfisuitejob(id,type){
        console.log('ID:'+id+'Type:'+type);
        $.ajax({
            url: "test_cul.php",
            data:'page=shareinfisuitejob&type='+type+'&post_id='+id,
            type: "POST",
            success:function(data){
                console.log(data);
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">
        <small>&nbsp;&nbsp; <b class="f-w-600"><?php echo $job_postings['job_name']; ?></b></small>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <form class="" action="" method="post">
            <div class="">
                <div class="form-group row">
                    <div class="col-lg-12 row" style="margin-left: 0px;">
                            <table class="table table-bordered nowrap">
                                <thead>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th scope="col" class="text-center">Action</th>
                                </thead>
                                <?php $m=1; foreach($tabledata as $data){ ?>
                                <tr>
                                    <td><?php echo $m; ?></td>
                                    <td><?php echo $data['name']; ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">

                                            <?php if(in_array($data['id'],(array)$pubids)){ ?>
                                                <?php if($publish[$data['id']]==1){ ?>
                                            <button onclick="displaystausdrop(<?php echo $jobpost; ?>,<?php echo "'job_postings'"; ?>,<?php echo $result[0]['current_status']; ?>)" class="btn btn-sm btn-success  dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'CREATE')" >Publish </button>
                                            <div class="dropdown-menu" id="dropdownlist">
                                                <div  onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'MODIFY')" class="dropdown-item" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Republish</div>
                                                <div onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'DRAFT')" class="dropdown-item" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Hold</div>
                                                <div onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'TRASH')" class="dropdown-item" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Unpublish</div>
                                            </div>
                                            <?php } ?>
                                            <?php if($publish[$data['id']]==2){ ?>
                                            <button onclick="displaystausdrop(<?php echo $jobpost; ?>,<?php echo "'job_postings'"; ?>,<?php echo $result[0]['current_status']; ?>)" class="btn btn-sm btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >Hold </button>
                                            <div class="dropdown-menu" id="dropdownlist_2">
                                                <div class="dropdown-item" onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'MODIFY')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Republish</div>
                                                <div class="dropdown-item" onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'TRASH')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Unpublish</div>
                                            </div>
                                            <?php } ?>
                                            <?php if($publish[$data['id']]==3){ ?>
                                            <button class="btn btn-sm btn-danger" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >Unpublish </button>
                                            <?php } ?>
                                            <?php }else{?>
                                                <button onclick="displays(<?php echo $result[0]['current_status']; ?>)" class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >To Be Publish </button>
                                            <div class="dropdown-menu" id="dropdownlist_3">
                                                <div class="dropdown-item" onclick="shareinfisuitejob(<?php echo $_POST['job_post_id']; ?>,'CREATE')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Publish</div>
                                            </div>
                                            <?php } ?>
                                        </div>

                                    </td>
                                </tr>
                                <?php $m++;
} ?>
                            </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>

        </div>
    </form>
</div>

