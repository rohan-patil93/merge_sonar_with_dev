<?php

$respexpuryurl = API."?mode=getcheckexpirydetails&adminrole=".$_SESSION['admin_type']."&emp_id=".$_SESSION['emp_id'];
      $respexpurydata = callAPI($respexpuryurl);
      $respexpurydatas = json_decode($respexpurydata,1);
    if($respexpurydatas['cnt']!=0){
?>
  <div class="modal fade" id="expiryalertpopup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog expirylist_popup" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h8 class="modal-title text-white"> Expiry Alert ss </h8>
            <button type="button" class="btn-close close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body custom-scroll" style=" height: 300px;">
         <table class="table table-xs">
            <thead><tr><th scope="col" class="p-2 ">Name</th><th scope="col" class="p-2">Expiry In</th></tr></thead>
            <tbody>
              <?php if($_SESSION['admin_type']=='1'){ foreach($respexpurydatas['comman'] as $expirycomman){
$expirybgcolo = $expirycomman['remainingdays'] <= 0?"danger":"warning";
                ?>
                <tr>
                  <td class="p-2 " >

                    <div class="align-middle">
                      <span  alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_workentry text-center" ><?php echo initials($expirycomman['display_name']); ?></span>
                    <div class="d-inline-block width73" style="white-space: initial;">
                      <a href="index.php?option=<?php echo $expirycomman['option']."&Rid=".deocdeId('id:'.$expirycomman['id']); ?>" class="text-primary text-capitalize"><h6 class="mb-0"> <?php echo $expirycomman['display_name'];?></h6></a>
                      <p class="f-10 mb-0"><?php echo $expirycomman['msg']; ?></p>
                    </div>
                  </div></td>
                  <td class="p-2 text-center"><span class="form-label label label-inverse-<?php echo $expirybgcolo; ?> f-16"><?php echo $expirycomman['remainingdays'];?> days</span></td></tr>
            <?php } ?>
            <?php } ?>
             <?php foreach($respexpurydatas['emp'] as $expirycommanemp){ $expirybgcolo = $expirycommanemp['remainingdays'] <= 0?"danger":"warning";?>
                <tr>
                <td class="p-2 ">
                  <div class="align-middle ">
                      <span  alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_workentry text-center f-12" ><?php echo initials($expirycommanemp['display_name']); ?></span>
                    <div class="d-inline-block width73 " style="white-space: initial;">
                      <a <?php if($expirycommanemp['option']!=""){ ?>href="index.php?option=<?php echo $expirycommanemp['option']."&Rid=".deocdeId('id:'.$expirycommanemp['id']); ?>" <?php
} ?> class="text-primary text-capitalize"><h8 class="mb-0"> <?php echo $expirycommanemp['display_name'];?></h8></a>
                      <p class="f-10 mb-0"><?php echo $expirycommanemp['msg']; ?></p>
                    </div>
                  </div>
              </td>
              <td class="p-2 text-center"><span class="form-label label label-inverse-<?php echo $expirybgcolo; ?> f-16"><?php echo $expirycommanemp['remainingdays'];?> days</span>

              </td></tr>
            <?php } ?>
            </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
    <script type="text/javascript">
        setTimeout(function(){ $('#expiryalertpopup').modal('show'); }, 5000);
     </script>
 <?php  }  ?>
