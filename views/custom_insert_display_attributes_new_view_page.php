<?php
if($_POST['ajaxpage'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $mastertablename = $_POST['mastertablename'];
    $apiurl = API."?mode=getdisplayattributesnewversion&Tname=".$mastertablename;
    $response = callAPI($apiurl);
    $result = json_decode($response,1);
    $table_list = $result['table_list'];
    $mtablefields = $result['table_field'];
    $position =10;
    foreach($mtablefields as $mtablefield){
        $mtfname = ucwords(str_replace('_',' ',$mtablefield));
        $mastertablefield[$mtablefield] = array("fieldname"=>$mtablefield,"display_name"=>$mtfname,"type"=>"text","display_position"=>$position,"mandatory"=>"1","insert_view_width"=>6,"accordions"=>"N","insert_view_wizard"=>1);
        $position = $position+10;
    }
}
    $editfield = $mastertablefield['id'];
    $pagetype = $_POST['pagetype'];
if($pagetype == "F"){ ?>
<div class="card">
    <div class="card-header">
        <h5>Insert Page Details</h5>
    </div>
    <div class="card-body">
        <div class="card-body">
            <div class="row">
             <?php foreach ($mastertablefield as $keyfield=>$mtf) { ?>
                 <div class="col-lg-6 form-group">
                    <span class="form-label d-block"><?php echo $mtf['display_name']; ?></span>
                    <input aria-label="Display Position" class="form-control" type="text" name="display_position" placeholder="<?php echo $editfield['display_name']; ?>">
                </div>
             <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php }elseif($pagetype == "V"){  ?>
<div class="card">
    <div class="card-header">
        <h5>View Page Details</h5>
    </div>
    <div class="card-body">
        <div class="card-body">
            <table class="table m-0">
                <tbody>
                     <?php foreach ($mastertablefield as $keyfield=>$mtf) { ?>
                    <tr>
                        <th scope="row"><?php echo $mtf['display_name']; ?></th>
                        <td>xxxx</td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php }elseif($pagetype == "G"){ ?>
<div class="card col-lg-6">
    <div class="card-header">
        <h5>Insert Page Details</h5>
    </div>
    <div class="card-body" >
      <table class="table table-hover m-b-0" style="font-size:13px;">
        <tbody>
              <tr>
                <th scope="col" class="p-2">Transaction No.</th>
                <td class="text-right p-2 "> <a href="index?option=COM_INSERT_RENTAL_INVOICES&amp;Rid=YVdRNk1RPT0=" class="text-primary"><i class="fa fa-edit"></i>  RI00187</a></td>
              </tr>
              <tr>
                <th scope="col" class="p-2">Transaction Date</th>
                <td class="text-right p-2">10-11-2023</td>
              </tr>
              <tr>
                <th scope="col" class="p-2">Part Name</th>
                <td class="text-right p-2">Batakrishna Pattanayak</td>
              </tr>
              <tr>
                <th scope="col" class="p-2">Amount</th>
                <td class="text-right p-2">37,568.00</td>
              </tr>
              <tr>
                <th scope="col" class="p-2">Tax</th>
                <td class="text-right p-2">4,968.00</td>
              </tr>
              <tr>
                <th scope="col" class="p-2">Discount</th>
                <td class="text-right p-2">0.00</td>
              </tr>
              <tr>
                <th scope="col" class="p-2">Total</th>
                <td class="text-right p-2">42,536.00</td>
            </tr>
          </tbody>
      </table>
    </div>
</div>

 <?php }else{ ?>
<div class="card">
    <div class="card-header">
        <h5>Table</h5>
    </div>
    <div class="card-body">
         <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                    <?php foreach ($mastertablefield as $keyfield=>$mtf) { ?>
                        <th class="pl-0 font-weight-bold text-muted"  id="thlabel<?php echo $m; ?>" ><?php echo $mtf['display_name']; ?></th>
                        <?php  } ?>
                    </tr>
                </thead>
                <tbody>
                    <tr class="font-weight-boldest">
                    <?php foreach ($mastertablefield as $keyfield=>$mtf){ ?>
                        <td class="pl-0 pt-7" id="thvalue<?php echo $k; ?>">xxxx</td>
                        <?php } ?>
                    </tr>
                   </tbody>
            </table>
        </div>
    </div>
</div>
<?php } ?>
