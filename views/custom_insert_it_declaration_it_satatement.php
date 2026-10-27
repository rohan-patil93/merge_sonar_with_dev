        <?php
        $com_ear = $saldata['components'];
        $house_rent = $saldata['house_rent'];
        $tax80c = $saldata['tax80c'];
        $basic_id = $com_ear['basic']['id'] ;
        $hra_id = $com_ear['hra']['id'] ;
    $eran_ids=array($com_ear['basic']['id'], $com_ear['hra']['id'], $com_ear['oa']['id']);
    $dedu_ids=array($com_ear['pf']['id'], $com_ear['esi']['id'], $com_ear['gratuity']['id']);
        foreach ($saldata['salary']['data'] as $value) {
          $sadata[$saldata['category'][$value['component_cat_id']]['code']][] = $value;
          if($value['component_id'] == $basic_id){
            $basic_data = $value;
          }
          if($value['component_id'] == $hra_id){
            $hra_data = $value;
          }
        }
       $psalary = $saldata['salary'];
       $pcsalary =$saldata['salary']['data'];

       foreach($pcsalary as $pcsala){
     if(in_array($pcsala['component_id'],(array)$eran_ids)){
          $pcsal[$pcsala['component_id']]= $pcsala;
    }
    if(in_array($pcsala['component_id'],(array)$dedu_ids)){
          $dedpcsal[$pcsala['component_id']]= $pcsala;
    }
       }
?>
<div class="accordion accordion-light accordion-light-borderless accordion-svg-toggle" id="accordionExample7_it">
  <div class="card">
    <div class="card-header" id="headingOne7">
      <div class="card-title" data-bs-toggle="collapse" href="#collapseOne7" data-target="#collapseOne7">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">A Income</div>
      </div>
    </div>
    <div id="collapseOne7" class="collapse show" data-parent="#accordionExample7_it">
      <div class="card-body pl-12">
          <div class="row col-lg-12">
            <div class="col-lg-3">
              <table class="table table-bordered">
                 <tr class="table-primary text-white"><th scope="col">Items</th> <th scope="col">Annual total</th></tr>
                 <?php $a_total =0 ;foreach ($pcsal as $earnings) { ?>
                   <tr><td><b><?php echo $earnings['component_name']; ?></b></td> <td><?php echo $earnings['yearly_amount']; ?></td></tr>
                <?php $a_total = $a_total+$earnings['yearly_amount'];
} ?>


                <tr><td><b>Total</b></td> <td><?php echo $a_total; ?> </td></tr>
              </table>
            </div>
            <div class="col-lg-9 ">
        <div class="table-responsive">
              <table class="table table-bordered fa-1x">

                   <tr class="table-primary">
                <?php
                    $start = $start_now;
                    $end = $end_now; ?>

                  <?php while($start < $end)
                    {
                        $display[] =  date('F Y', $start);        ?>

                        <th scope="col"> <?php echo date('M Y', $start); ?> </th>


                  <?php  $start = strtotime("+1 month", $start);
}?>
                    </tr>

                  <?php foreach ($pcsal as $earnings) { ?>
                      <tr>
                       <?php for($i=0;$i<12;$i++){ ?>
                        <td> <?php echo $earnings['monthly_amount']; ?> </td>
                      <?php }  ?>
                   </tr>
                 <?php } ?>
              </table>
        </div>
            </div>
          </div>
        </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingTwo7">
      <div class="card-title " data-bs-toggle="collapse" href="#collapseTwoB7" data-target="#collapseTwoB7"  >
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2 float-left">B Deduction</div>
      </div>
    </div>
    <div id="collapseTwoB7" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
             <div class="row col-lg-12">
                <div class="col-lg-3">
              <table class="table table-bordered">
                 <tr class="table-primary text-white"><th scope="col">Items</th> <th scope="col">Annual total</th></tr>
                 <?php $b_total =0 ;foreach ($dedpcsal as $deductions) { ?>
                   <tr><td><b><?php echo $deductions['component_name']; ?></b></td> <td><?php echo $deductions['yearly_amount']; ?></td></tr>
                <?php $b_total = $b_total+$deductions['yearly_amount'];
} ?>


                <tr><td><b>Total</b></td> <td><?php echo $b_total; ?> </td></tr>
              </table>
            </div>
            <div class="col-lg-9">
        <div class="table-responsive">
              <table class="table table-bordered">

                   <tr class="table-primary">
                <?php
                    $start = $start_now;
                    $end = $end_now; ?>

                  <?php while($start < $end)
                    {
                        $display[] =  date('F Y', $start);        ?>

                        <th scope="col"> <?php echo date('M Y', $start); ?> </th>


                  <?php  $start = strtotime("+1 month", $start);
}?>
                    </tr>

                  <?php foreach ($dedpcsal as $deductions) { ?>
                      <tr>
                       <?php for($i=0;$i<12;$i++){ ?>
                        <td> <?php echo $deductions['monthly_amount']; ?> </td>
                      <?php }  ?>
                   </tr>
                 <?php } ?>
              </table>
        </div>
            </div>
          </div>
            <!-- END -->
      </div>
    </div>
                    </div>
 <div class="card">
  <div class="card-header" id="headingThree7">
   <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseOneC7" data-target="#collapseThreeC7">
    <?php include_once 'it_declaration_icon.php'; ?>
    <div class="card-label p-2">C Perquisite Tax</div>
   </div>
  </div>
  <div id="collapseOneC7" class="collapse" data-parent="#accordionExample7">
   <div class="row col-lg-12 ">
                <div class="col-lg-3">
              <table class="table table-bordered">
                 <tr class="table-primary text-white"><th scope="col">Items</th> <th scope="col">Annual total</th></tr>
                 <?php $c_total =0 ;foreach ($sadata['perquisite'] as $perquisite) { ?>
                   <tr><td><b><?php echo $perquisite['component_name']; ?></b></td> <td><?php echo $perquisite['yearly_amount']; ?></td></tr>
                <?php $c_total = $c_total+$perquisite['yearly_amount'];
} ?>


                <tr><td><b>Total</b></td> <td><?php echo $c_total; ?> </td></tr>
              </table>
            </div>
            <div class="col-lg-9">
        <div class="table-responsive">
              <table class="table table-bordered">

                   <tr class="table-primary">
                <?php
                    $start = $start_now;
                    $end = $end_now; ?>

                  <?php while($start < $end)
                    {
                        $display[] =  date('F Y', $start);        ?>

                        <th scope="col"> <?php echo date('M Y', $start); ?> </th>


                  <?php  $start = strtotime("+1 month", $start);
}?>
                    </tr>

                  <?php foreach ($sadata['perquisite'] as $perquisite) { ?>
                      <tr>
                       <?php for($i=0;$i<12;$i++){ ?>
                        <td> <?php echo $perquisite['monthly_amount']; ?> </td>
                      <?php }  ?>
                   </tr>
                 <?php } ?>
              </table>
        </div>
            </div>
          </div>
  </div>
 </div>
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_2">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoD"  data-target="#collapseTwoD">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">D Income Excluded From Tax</div>
      </div>
    </div>
    <div id="collapseTwoD" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12 text-dark">
         <div class="col-lg-10 p-l-40">
              <b>Income Excluded From Tax </b>
            </div>
            <div class="col-lg-2">
              <b> <?php $d_total=0; echo  $d_total; ?> </b>
            </div>

          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card jumbotron-fluid">
    <div class="card-header" id="headingTwo7_3">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoE"  data-target="#collapseTwoE">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">E Gross Salary</div>
      </div>
    </div>
    <div id="collapseTwoE" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12 text-dark">
            <div class="col-lg-10 p-l-40">
              <b> Gross Salary (A+C-D) </b>
            </div>
            <div class="col-lg-2">
              <b> <?php $e_total =  $a_total+$c_total-$d_total; echo  $e_total; ?> </b>
            </div>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_4">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoF" data-target="#collapseTwoF">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2"> F Exemption Under Section 10</div>
      </div>
    </div>
    <div id="collapseTwoF" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12">
           <table class="table table-bordered">
                <tr class="table-primary text-white">
                  <th scope="col">Month</th>
                  <th scope="col">%</th>
                  <th scope="col">Rent</th>
                  <th scope="col">HRA</th>
                  <th scope="col">Basic DA</th>
                  <th scope="col">Basic DA Value</th>
                  <th scope="col">Excess Rent</th>
                  <th scope="col">HRA Exemption</th>
                </tr>
                    <?php
                    $start = $start_now;
                    $end = $end_now;
                    $total_hra_hu=0;
                    $total_basic_hu=0;
                    $total_rent_hu=0;
                     $total_ex__rent_hu=0;
                     $total_check=0;
                     $total_check1=0;
                     $hr_p = 0.50;

                    $ch_ex_rent  = ($basic_data['monthly_amount'] * $hr_p);
                    $ch1_ex_rent  = $house_rent['monthly_rent'] - ($basic_data['monthly_amount'] * 0.10);
                    if($ch_ex_rent  > $ch1_ex_rent ){ $ex_rent =$ch1_ex_rent;
}else{ $ex_rent =$ch_ex_rent;
}
                     while($start < $end){
                      $total_hra_hu = $total_hra_hu+$hra_data['monthly_amount'];
                      $total_basic_hu = $total_basic_hu+$basic_data['monthly_amount'];
                      $total_rent_hu = $total_rent_hu+$house_rent['monthly_rent'];

                      $total_ex__rent_hu = $total_ex__rent_hu+$ex_rent;
                      $total_check = $total_check+$ch_ex_rent;
                      $total_check1 = $total_check1+$ch1_ex_rent;
                    ?>
                    <tr>
                      <td><?php echo date('M', $start); ?></td>
                      <td><?php echo $hra_data['component_number']; ?></td>
                      <td><?php echo $house_rent['monthly_rent']; ?></td>
                      <td><?php echo $hra_data['monthly_amount']; ?></td>
                      <td><?php echo $basic_data['monthly_amount']; ?></td>
                      <td><?php echo $ch_ex_rent; ?></td>
                      <td><?php echo $ch1_ex_rent; ?> </td>
                      <td><?php echo $ex_rent; ?></td>
                    </tr>
                  <?php  $start = strtotime("+1 month", $start);
} ?>
                 <tr class="">
                  <td colspan="2">Total</td>

                  <td><?php echo $total_rent_hu; ?></td>
                  <td><?php echo $total_hra_hu; ?></td>
                  <td><?php echo $total_basic_hu; ?></td>
                  <td><?php echo $total_check; ?></td>
                  <td><?php echo $total_check1; ?></td>
                  <td><?php echo $total_ex__rent_hu; $f_total=$total_ex__rent_hu; ?></td>
                </tr>
              </table>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_5">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoG" data-target="#collapseTwoG">
        <?php include_once 'it_declaration_icon.php';  $g_total =0.00;?>
        <div class="card-label p-2">G Income From Perevious Employer</div>
      </div>
    </div>
    <div id="collapseTwoG" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12">
            <table class="table table-bordered">
                 <tr class="table-primary text-white">
                  <th scope="col"> Pay Items</th> <th scope="col">Amount </th></tr>
                <tr><td><b>Total Income</b></td> <td>0.00</td></tr>
                <tr><td><b>Income Tax</b></td> <td>0.00</td></tr>
                <tr><td><b>Professional Tax</b></td> <td>0.00</td></tr>
                <tr><td><b>Provident Fund </b></td> <td>0.00</td></tr>
              </table>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_6">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoH" data-target="#collapseTwoH">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">H Deduction Under Section 16(Profesional tax)</div>
      </div>
    </div>
    <div id="collapseTwoH" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12 text-dark">

            <div class="col-lg-10 p-l-40">
              <b>Tax on employment under section 16(Profesional tax) </b>
            </div>
            <div class="col-lg-2">
              <b> <?php $h_total = 2400.00 ; echo $h_total; ?> </b>
            </div>

          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion --><!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_7">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoI" data-target="#collapseTwoI">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">I Income chargeable under the head salaries</div>
      </div>
    </div>
    <div id="collapseTwoI" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12 text-dark">
             <div class="col-lg-10 p-l-40">
              <b>Income chargeable under the head salaries (E-F+G-H) </b>
            </div>
            <div class="col-lg-2">
              <b> <?php $i_total = $e_total-$f_total+$g_total-$h_total;  echo $i_total; ?>  </b>
            </div>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_8">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoJ" data-target="#collapseTwoJ">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">J Income from other sources (include house properties)</div>
      </div>
    </div>
    <div id="collapseTwoJ" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12">
            <table class="table table-bordered">
                <tr class="table-primary text-white">
                  <th scope="col"> Description</th> <th scope="col">Amount </th></tr>
                <tr><td><b>Other income 1</b></td> <td>0.00</td></tr>
                <tr><td><b>Total</b></td> <td>0.00</td></tr>
              </table>
              <?php $j_total=0.00;  ?>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_9">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoK" data-target="#collapseTwoK">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">K Deduction Under Chapter VI(A)</div>
      </div>
    </div>
    <div id="collapseTwoK" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12">
             <table class="table table-bordered">
                <tr class="table-primary text-white">
                  <th scope="col"> Description</th> <th scope="col">Amount </th></tr>
                  <?php $tax80_total=0; foreach ($tax80c as $tax80) {
                    $tax80_total = $tax80_total + $tax80['amount'];?>
                <tr><td><b><?php echo $tax80['name']; ?> </b></td> <td><?php echo $tax80['amount']; ?> </td></tr>
              <?php } ?>

                <tr><td><b>Total</b></td> <td><?php echo $tax80_total; ?></td></tr>
              </table>
              <?php $k_total = $tax80_total; ?>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_10">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoL" data-target="#collapseTwoL">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">L Standard Deduction</div>
      </div>
    </div>
    <div id="collapseTwoL" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12 text-dark">
           <div class="col-lg-10 p-l-40">
              <b>Standard Deduction </b>
            </div>
            <div class="col-lg-2">
              <b> <?php $l_total=0; ?> 0.00 </b>
            </div>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
 <div class="card">
    <div class="card-header" id="headingTwo7_11">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoM" data-target="#collapseTwoM">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">M Taxable Income</div>
      </div>
    </div>
    <div id="collapseTwoM" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12 text-dark">

            <div class="col-lg-10 p-l-40">
              <b>Taxable Income (I+J-K-L) </b>
            </div>
            <div class="col-lg-2">
              <b> <?php $m_total = $i_total+$j_total-$k_total-$l_total; echo $m_total; ?>  </b>
            </div>

          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <!-- End accodion -->
  <!-- start accodion -->
  <?php $calslab_total = $m_total;
    $cal_intax = 0;
    if($calslab_total >0){
    foreach ($tax_slab as $slab) {
       if($slab['slad_from'] < $calslab_total && $calslab_total < $slab['slad_to'] ){
           $cal_amount = $calslab_total - ($slab['slad_from']-1);
            $cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
            break;
       }else{
          $cal_amount = $slab['slad_to'] - ($slab['slad_from']-1);
          $cal_intax = $cal_intax + ($cal_amount * ($slab['rate']/100));
       }
    }
    }
    if($cal_intax<0){
      $cal_intax = 0;
    }
   ?>
 <div class="card">
    <div class="card-header" id="headingTwo7_12">
      <div class="card-title collapsed" data-bs-toggle="collapse" href="#collapseTwoN" data-target="#collapseTwoN">
        <?php include_once 'it_declaration_icon.php'; ?>
        <div class="card-label p-2">N Annual Tax</div>
      </div>
    </div>
    <div id="collapseTwoN" class="collapse" data-parent="#accordionExample7">
      <div class="card-body pl-12">
            <!-- start -->
          <div class="row col-lg-12">
              <table class="table table-bordered">
                  <tr class="table-primary text-white"><th scope="col"> Tax</th> <th scope="col">Amount </th></tr>
                  <tr><td><b>Income Tax</b></td> <td><?php echo $cal_intax; ?></td></tr>
                  <tr><td><b>Total </b></td> <td><?php echo $cal_intax; ?></td></tr>
              </table>
          </div>
            <!-- END -->
      </div>
    </div>
  </div>
  <form method="post" action="" >
    <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
    <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
    <input type="hidden" name="assessment_year" value="<?php echo $_GET['assessment_year']; ?>">
    <input type="hidden" name="taxable_income" value="<?php echo $maindata['potential_amount']; ?>">
    <input type="hidden" name="annual_tax" value="<?php echo $m_total; ?>">
    <input type="hidden" name="declaration_tax" value="<?php echo $m_total; ?>">
    <input type="hidden" name="gap_tax" value="<?php echo $maindata['potential_amount']-$m_total; ?>">
    <input type="hidden" name="it_plan" value="<?php echo $_GET['IT_PLAN']; ?>">
    <input type="hidden" name="it_plan" value="<?php echo $_GET['version_no']; ?>">
    <input type="hidden" name="is_active" value="0">
    <input type="hidden" name="current_status" value="0">
    <input type="hidden" name="it_declaration" value="it_declaration">

    <button type="submit" name="Submit" class="btn btn-primary waves-effect waves-light" value="Submit">Submit</button>
  </form>
  <!-- End accodion -->
</div>
