<?php
$url = API."?mode=togetjobtitleexploc";
$apires = callAPI($url);
$result = json_decode($apires,1);
$_SESSION['download_data'] = array();
?>
<style type="text/css">
  .ontable tr td{
    vertical-align: middle;
    padding: 5px;
  }
</style>
<div class="card">
  <div class="card-header">
    <h5 class="f-30"><?= __('Vendor'); ?></h5>
    <div class="card-header-right">

    </div>
    <div class="card-body">
      <div class="table-responsive mt-3">
        <!-- table table-hover -->
        <table class=" table table-bordered">
          <tr bgcolor="#eeeeee">
            <th scope="col" colspan="3"></th>
            <?php foreach ($result['vendor_data'] as $kyloc=>$locdata) { ?>
            <th scope="col" colspan="<?php echo count((array)$locdata); ?>"><?php echo $result['location'][$kyloc]; ?></th>
            <?php } ?>
          </tr>
          <tr bgcolor="#eeeeee">
            <th scope="col">#</th>
            <th scope="col"><?= __('Job Title'); ?></th>
            <th scope="col"><?= __('Exp'); ?></th>
            <?php foreach ($result['vendor_data'] as $locdata) {
              foreach ($locdata as $vendata) {
                $listvendors[] = $vendata['id'];
            ?>
             <th scope="col"><?php echo $vendata['display_name']; ?></th>
            <?php
              }
            } ?>
          </tr>
          <?php $sl=1; if(count((array)$result['job_data'])>0){
           foreach($result['job_data'] as $job_data ){
          ?>
            <td><?php echo $sl; ?></td>
            <td></td>
          <?php $sl++;
          }
            $_SESSION['download_data'] = $cssdata;
            ?>
            </table>
            <?php
          } ?>
        </table>
      </div>
  </div>
</div>
