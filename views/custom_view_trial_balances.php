 <?php
$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$fr_id= $rid[1];
 $vurl = API."?mode=getfinanacereports&fr_id=".$fr_id;
 $resultapi = callAPI($vurl);
 $result = json_decode($resultapi,1);
 $finance_report_groups =$result['finance_report_groups'];
  $trial_balance =$result['trial_balance'];

 $chartofaccounts = array();

foreach($trial_balance as $tribal){
    $trialbalance[$tribal['finance_report_group_id']][$tribal['mapping_account_code']][] = $tribal;
}

$finalvalue =array();
foreach($finance_report_groups as $finanacerepogrp){
    $financereportgroupsreport[$finanacerepogrp['parent_group_id']][$finanacerepogrp['id']] = $finanacerepogrp;
    $financereportgroups[$finanacerepogrp['id']] = $finanacerepogrp;

    $trialbalanceresult = $trialbalance[$finanacerepogrp['id']];
    $creditamt = 0;
    $debittamt = 0;
    $worngcreditamt = 0;
    $worngdebitamt = 0;
    foreach($trialbalanceresult as $acountcode => $debitcreditdetails){
        foreach($debitcreditdetails as $debitcreditdetail){
            if($debitcreditdetails['transaction_type'] == '1'){
                    $creditamt = $creditamt+$debitcreditdetail['credit_amount'];
                    $worngdebitamt = $worngdebitamt+$debitcreditdetail['debit_amount'];
            }else{
                $debittamt = $debittamt+$debitcreditdetail['debit_amount'];
                $worngcreditamt = $worngcreditamt+$debitcreditdetail['credit_amount'];
            }

        }
    }
    $debitcredit[$finanacerepogrp['id']]['credit'] = $creditamt;
    $debitcredit[$finanacerepogrp['id']]['debit'] = $debittamt;
    $debitcredit[$finanacerepogrp['id']]['worngcredit'] = $worngcreditamt;
    $debitcredit[$finanacerepogrp['id']]['worngdebit'] = $worngdebitamt;
    $debitcredit[$finanacerepogrp['id']]['finalamt'] = ($creditamt+$worngcreditamt)-($debittamt+$worngdebitamt);
    $finalvalue[$finanacerepogrp['short_code']] = ($creditamt+$worngcreditamt)-($debittamt+$worngdebitamt);
}

function replaceAmount($array,$string){
    foreach($array as $key=>$value){
        $string = str_replace($key,$value,$string);
    }
    return $string;
}

function dashprint($amt){
    $returnamt = '';
    if($amt == '0' || $amt == ''){
        $returnamt = "-";
    }else{
        $returnamt = $amt;
    }
    return $returnamt;
}

?>
<script type="text/javascript">

function showPopupNote(id){
    console.log(id);
    $.ajax({
        url: "views/custom_insert_notes.php",
        data:'id='+id,
        type: "POST",
        success:function(data){
            console.log(data);
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal('show');
            $(document).ready(function() {
                $('[data-bs-toggle="tooltip"]').tooltip();
            });
        },
        error:function (){}
    });
}

</script>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h2 class="card-header-text f-24 f-w-600 text-center">XYZ PRIVATE LIMITED<br />CIN OF XYZ PRIVATE LTXD</h2>
            <h3 class="card-header-text f-20 f-w-600 text-center">REGISTERED OFFICE: Registered Address Of XYZ PRIVATE LTD</h3>
            <h4 class="card-header-text f-16 f-w-600 text-center text-muted">STANDALONE BALANCE  SHEET AS AT 31ST MARCH, 2022</h4>
        </div>
        <div class="card-block pt-0">
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Particulars</th>
                            <th scope="col">Note No.</th>
                            <th scope="col">As at 28st Feb, 2023</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach($financereportgroupsreport[0] as $level1){
                            $Alevel2 = $financereportgroupsreport[$level1['id']];
                            ?>
                            <tr>
                                <td class="<?php echo count((array)$Alevel2) !='0'?"f-w-700":"NNN"; ?> w-b-w-s"><span class=""><?php echo $i.") ".$level1['group_name']." - ".$level1['short_code']; ?></span></td>
                                 <td id="notenumberid_<?php echo $level1['id']; ?>" onclick="showPopupNote('<?php echo $level1['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-plus-square" data-bs-toggle="tooltip" data-placement="bottom" title="Select Note Number"></i></td>
                                 <td> <?php
                                 echo dashprint($debitcredit[$level1['id']]['finalamt']);
                                  ?> </td>

                            </tr>
                            <?php $mk ='A'; foreach($Alevel2 as $level2){
                                $Alevel3 = $financereportgroupsreport[$level2['id']];
                                ?>
                                <tr>
                                    <td   class="<?php echo count((array)$Alevel3) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-20"><?php echo $mk.") ".$level2['group_name']." - ".$level2['short_code']; ?></span></td>
                                    <td id="notenumberid_<?php echo $level2['id']; ?>" onclick="showPopupNote('<?php echo $level2['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-plus-square" data-bs-toggle="tooltip" data-placement="bottom" title="Select Note Number"></i></td>

                                    <td> <?php
                                    echo dashprint($debitcredit[$level2['id']]['finalamt']); ?> </td>
                                </tr>
                                <?php $mj ="a"; foreach($Alevel3 as $level3){
                                    $Alevel4 = $financereportgroupsreport[$level3['id']];
                                    ?>
                                    <tr>
                                        <td  class="<?php echo count((array)$Alevel4) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-40"><?php echo $mj.") ".$level3['group_name']." - ".$level3['short_code']; ?></span></td>
                                        <td id="notenumberid_<?php echo $level3['id']; ?>" onclick="showPopupNote('<?php echo $level3['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-plus-square" data-bs-toggle="tooltip" data-placement="bottom" title="Select Note Number"></i></td>
                                        <td> <?php
                                         echo dashprint($debitcredit[$level3['id']]['finalamt']);?> </td>

                                    </tr>
                                    <?php $mm =1; foreach($Alevel4 as $level4){
                                        $Alevel5 = $financereportgroupsreport[$level4['id']];
                                        ?>
                                        <tr>
                                            <td  class="<?php echo count((array)$Alevel5) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-60"><?php echo $mm.") ".$level4['group_name']." - ".$level4['short_code']; ?></span></td>
                                            <td id="notenumberid_<?php echo $level4['id']; ?>" onclick="showPopupNote('<?php echo $level4['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-plus-square" data-bs-toggle="tooltip" data-placement="bottom" title="Select Note Number"></i></td>
                                            <td> <?php
                                             echo dashprint($debitcredit[$level4['id']]['finalamt']);?> </td>
                                        </tr>
                                        <?php $jm =1; foreach($Alevel5 as $level5){
                                            $Alevel6 = $financereportgroupsreport[$level5['id']];
                                            ?>
                                            <tr>
                                                <td  class="<?php echo count((array)$Alevel6) !='0'?"f-w-700":""; ?> w-b-w-s"><span class="m-l-80"><?php echo $jm.") ".$level5['group_name']." - ".$level5['short_code']; ?></span></td>
                                                 <td id="notenumberid_<?php echo $level5['id']; ?>" onclick="showPopupNote('<?php echo $level5['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-plus-square" data-bs-toggle="tooltip" data-placement="bottom" title="Select Note Number"></i></td>
                                                 <td>
                                                  <?php echo dashprint($debitcredit[$level5['id']]['finalamt']);
                                                  ?> </td>

                                            </tr>

                                            <?php $jm++;
} //level5?>

                                            <?php $mm++;
}//level4 ?>
                                            <?php if($level3['footer_total'] == "0"){ ?>
                                            <tr>
                                                <td  class="f-w-700 w-b-w-s"> Total <?php echo $level3['group_name'];?></td>
                                                 <td  class="f-w-700 w-b-w-s"><?php echo replaceAmount($finalvalue,$level3['calculation_formula']); ?></td>
                                            </tr>
                                    <?php } ?>
                                            <?php $mj++;
}//level3 ?>
                                      <?php if($level2['footer_total'] == "0"){ ?>
                                            <tr>
                                                <td colspan="2" class="f-w-700 w-b-w-s"> Total <?php echo $level2['group_name'];?></td>
                                                 <td  class="f-w-700 w-b-w-s"><?php echo replaceAmount($finalvalue,$level2['calculation_formula']); ?></td>
                                            </tr>
                                    <?php } ?>
                                            <?php $mk++;
}//level2 ?>

                                      <?php if($level1['footer_total'] == "0"){ ?>
                                            <tr>
                                                <td colspan="2" class="f-w-700 w-b-w-s"> Total <?php echo $level1['group_name'];

                                                ?></td>
                                                <td  class="f-w-700 w-b-w-s"><?php echo replaceAmount($finalvalue,$level1['calculation_formula']); ?></td>
                                            </tr>
                                    <?php } ?>

                                            <?php  $i++;
} //level1 ?>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <h5 class="f-w-600">For<br />Chartered Accountants<br />Firm's Registration Number -</h5>
                                    </div>
                                    <div class="col-lg-6 text-right">
                                        <h6 class="f-w-600">For and on behalf of the Board of Directors</h6>
                                    </div>
                                </div>
                                <div class="row mt-5">
                                    <div class="col-lg-6">
                                        <h5 class="f-w-600">(CA)<br />Membership No.<br />Partner/Proprietor</h5>
                                        <h6 class="mt-3 f-w-600 f-14">Place:</h6>
                                        <h6 class="f-w-600 f-14">Dated:</h6>
                                    </div>
                                    <div class="col-lg-3 text-center">
                                        <h6 class="f-w-600 f-14">Name Of Director<br />Director<br />DIN OF Director</h6>
                                    </div>
                                    <div class="col-lg-3 text-center">
                                        <h6 class="f-w-600 f-14"> Name Of Director<br />Director<br />DIN OF Director</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
