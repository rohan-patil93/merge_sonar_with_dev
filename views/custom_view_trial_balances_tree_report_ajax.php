<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$parent_account_code = $_POST['account_code'] !=""?$_POST['account_code']:0;
$balance_date = $_POST['balance_date'];
$getapi = API."?mode=gettrialbalancesreport&balance_date=".$balance_date;
$resapi = callAPI($getapi);
$result = json_decode($resapi,1);
$balances = $result['balances'];
$parentcoa = $parentchildacount = array();
foreach($balances as $coa){
    if($coa['parent_account_code'] == 0){
        $parentcoa[] = $coa;
    }
    $parentchildacountname[$coa['parent_account_code']][$coa['account_code']] = $coa['account_name'];

    $parentchildacount[$coa['parent_account_code']][] = $coa;
}
$parentchildaccount = $totalcoa = $parentlist =  array();
foreach($parentcoa as $pcoa1){
    $parentchildaccount[$pcoa1['account_code']][] = $pcoa1;

    $totalcoa[$pcoa1['account_code']]['debit_amount']  +=  $pcoa1['debit_amount'];
    $totalcoa[$pcoa1['account_code']]['credit_amount']  +=  $pcoa1['credit_amount'];
    foreach($parentchildacount[$pcoa1['account_code']] as $pcoa2){
        $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][] = $pcoa2;

        $totalcoa[$pcoa1['account_code']]['debit_amount']  +=  $pcoa2['debit_amount'];
        $totalcoa[$pcoa2['account_code']]['debit_amount']  +=  $pcoa2['debit_amount'];

        $totalcoa[$pcoa1['account_code']]['credit_amount']  +=  $pcoa2['credit_amount'];
        $totalcoa[$pcoa2['account_code']]['credit_amount']  +=  $pcoa2['credit_amount'];
        foreach($parentchildacount[$pcoa2['account_code']] as $pcoa3){
            $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][] = $pcoa3;

            $totalcoa[$pcoa1['account_code']]['debit_amount']  +=  $pcoa3['debit_amount'];
            $totalcoa[$pcoa2['account_code']]['debit_amount']  +=  $pcoa3['debit_amount'];
            $totalcoa[$pcoa3['account_code']]['debit_amount']  +=  $pcoa3['debit_amount'];

            $totalcoa[$pcoa1['account_code']]['credit_amount']  +=  $pcoa3['credit_amount'];
            $totalcoa[$pcoa2['account_code']]['credit_amount']  +=  $pcoa3['credit_amount'];
            $totalcoa[$pcoa3['account_code']]['credit_amount']  +=  $pcoa3['credit_amount'];
            foreach($parentchildacount[$pcoa3['account_code']] as $pcoa4){
                $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][] = $pcoa4;

                $totalcoa[$pcoa1['account_code']]['debit_amount']  +=  $pcoa4['debit_amount'];
                $totalcoa[$pcoa2['account_code']]['debit_amount']  +=  $pcoa4['debit_amount'];
                $totalcoa[$pcoa3['account_code']]['debit_amount']  +=  $pcoa4['debit_amount'];
                $totalcoa[$pcoa4['account_code']]['debit_amount']  +=  $pcoa4['debit_amount'];

                $totalcoa[$pcoa1['account_code']]['credit_amount']  +=  $pcoa4['credit_amount'];
                $totalcoa[$pcoa2['account_code']]['credit_amount']  +=  $pcoa4['credit_amount'];
                $totalcoa[$pcoa3['account_code']]['credit_amount']  +=  $pcoa4['credit_amount'];
                $totalcoa[$pcoa4['account_code']]['credit_amount']  +=  $pcoa4['credit_amount'];
                foreach($parentchildacount[$pcoa4['account_code']] as $pcoa5){
                    $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][] = $pcoa5;

                    $totalcoa[$pcoa1['account_code']]['debit_amount']  +=  $pcoa5['debit_amount'];
                    $totalcoa[$pcoa2['account_code']]['debit_amount']  +=  $pcoa5['debit_amount'];
                    $totalcoa[$pcoa3['account_code']]['debit_amount']  +=  $pcoa5['debit_amount'];
                    $totalcoa[$pcoa4['account_code']]['debit_amount']  +=  $pcoa5['debit_amount'];
                    $totalcoa[$pcoa5['account_code']]['debit_amount']  +=  $pcoa5['debit_amount'];

                    $totalcoa[$pcoa1['account_code']]['credit_amount']  +=  $pcoa5['credit_amount'];
                    $totalcoa[$pcoa2['account_code']]['credit_amount']  +=  $pcoa5['credit_amount'];
                    $totalcoa[$pcoa3['account_code']]['credit_amount']  +=  $pcoa5['credit_amount'];
                    $totalcoa[$pcoa4['account_code']]['credit_amount']  +=  $pcoa5['credit_amount'];
                    $totalcoa[$pcoa5['account_code']]['credit_amount']  +=  $pcoa5['credit_amount'];
                    foreach($parentchildacount[$pcoa5['account_code']] as $pcoa6){
                        $parentchildaccount[$pcoa1['account_code']][$pcoa2['account_code']][$pcoa3['account_code']][$pcoa4['account_code']][$pcoa5['account_code']][$pcoa6['account_code']][] = $pcoa6;
                        $totalcoa[$pcoa1['account_code']]['debit_amount']  +=  $pcoa6['debit_amount'];
                        $totalcoa[$pcoa2['account_code']]['debit_amount']  +=  $pcoa6['debit_amount'];
                        $totalcoa[$pcoa3['account_code']]['debit_amount']  +=  $pcoa6['debit_amount'];
                        $totalcoa[$pcoa4['account_code']]['debit_amount']  +=  $pcoa6['debit_amount'];
                        $totalcoa[$pcoa5['account_code']]['debit_amount']  +=  $pcoa6['debit_amount'];
                        $totalcoa[$pcoa6['account_code']]['debit_amount']  +=  $pcoa6['debit_amount'];

                        $totalcoa[$pcoa1['account_code']]['credit_amount']  +=  $pcoa6['credit_amount'];
                        $totalcoa[$pcoa2['account_code']]['credit_amount']  +=  $pcoa6['credit_amount'];
                        $totalcoa[$pcoa3['account_code']]['credit_amount']  +=  $pcoa6['credit_amount'];
                        $totalcoa[$pcoa4['account_code']]['credit_amount']  +=  $pcoa6['credit_amount'];
                        $totalcoa[$pcoa5['account_code']]['credit_amount']  +=  $pcoa6['credit_amount'];
                        $totalcoa[$pcoa6['account_code']]['credit_amount']  +=  $pcoa6['credit_amount'];
                    }
                }
            }
        }
    }
}
?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive" style="overflow: clip;">
            <table style="width:100%;border-collapse: collapse;" class="mt-4">
                <tr style="position: sticky; top: 0;">
                    <th scope="col" style="background-color: #C0C0C0; font-size:14px; font-family:'Tahoma'; width: 80%; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; text-align: left;">Account Code/ Description</th>
                    <th scope="col" style="background-color: #C0C0C0; font-size:14px; font-family:'Tahoma'; width: 10%; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;">Debit</th>
                    <th scope="col" style="background-color: #C0C0C0; font-size:14px; font-family:'Tahoma'; width: 10%; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;">Credit</th>
                </tr>
                <?php
                $a = 1; $totaldebit = $totalcredit = 0;
                foreach($parentchildacount[$parent_account_code] as $trialbal){
                    $accountdebitamount =$accountcreditamount =  0;
                    if($totalcoa[$trialbal['account_code']]['debit_amount'] !=""){
                        $accountdebitamount = $totalcoa[$trialbal['account_code']]['debit_amount'];
                    }else{
                        $accountdebitamount =0;
                    }
                    if($totalcoa[$trialbal['account_code']]['credit_amount'] !=""){
                        $accountcreditamount = $totalcoa[$trialbal['account_code']]['credit_amount'];
                    }else{
                        $accountcreditamount =0;
                    }
                    $totalcredit += $accountcreditamount;
                    $totaldebit += $accountdebitamount;
                 ?>
                <tr>
                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; text-align: left;" ><?php echo $a." - ".$trialbal['account_code']; ?> / <?php echo $trialbal['account_name']; ?></th>
                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                     ?></th>
                </tr>
                <?php  $b = 1;
                    foreach($parentchildacount[$trialbal['account_code']] as $trialbal1){
                        $accountdebitamount =$accountcreditamount =  0;
                        if($totalcoa[$trialbal1['account_code']]['debit_amount'] !=""){
                            $accountdebitamount = $totalcoa[$trialbal1['account_code']]['debit_amount'];
                        }else{
                            $accountdebitamount =0;
                        }
                        if($totalcoa[$trialbal1['account_code']]['credit_amount'] !=""){
                            $accountcreditamount = $totalcoa[$trialbal1['account_code']]['credit_amount'];
                        }else{
                            $accountcreditamount =0;
                        }
                        $totalcredit += $accountcreditamount;
                        $totaldebit += $accountdebitamount;
                     ?>
                    <tr>
                        <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 30px; text-align: left;" ><?php echo $a.".".$b." - ".$trialbal1['account_code']; ?> (<?php echo $trialbal1['account_name']; ?>)</th>
                        <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                        <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                         ?></th>
                    </tr>
                        <?php  $c = 1;
                        foreach($parentchildacount[$trialbal1['account_code']] as $trialbal2){
                            $accountdebitamount =$accountcreditamount =  0;
                            if($totalcoa[$trialbal2['account_code']]['debit_amount'] !=""){
                                $accountdebitamount = $totalcoa[$trialbal2['account_code']]['debit_amount'];
                            }else{
                                $accountdebitamount =0;
                            }
                            if($totalcoa[$trialbal2['account_code']]['credit_amount'] !=""){
                                $accountcreditamount = $totalcoa[$trialbal2['account_code']]['credit_amount'];
                            }else{
                                $accountcreditamount =0;
                            }
                            $totalcredit += $accountcreditamount;
                            $totaldebit += $accountdebitamount;
                         ?>
                        <tr>
                            <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 60px; text-align: left;" ><?php echo $a.".".$b.".".$c." - ".$trialbal2['account_code']; ?> (<?php echo $trialbal2['account_name']; ?>)</th>
                            <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                            <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                             ?></th>
                        </tr>
                            <?php $d = 1;
                            foreach($parentchildacount[$trialbal2['account_code']] as $trialbal3){
                                $accountdebitamount =$accountcreditamount =  0;
                                if($totalcoa[$trialbal3['account_code']]['debit_amount'] !=""){
                                    $accountdebitamount = $totalcoa[$trialbal3['account_code']]['debit_amount'];
                                }else{
                                    $accountdebitamount =0;
                                }
                                if($totalcoa[$trialbal3['account_code']]['credit_amount'] !=""){
                                    $accountcreditamount = $totalcoa[$trialbal3['account_code']]['credit_amount'];
                                }else{
                                    $accountcreditamount =0;
                                }
                                $totalcredit += $accountcreditamount;
                                $totaldebit += $accountdebitamount;
                             ?>
                            <tr>
                                <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 90px; text-align: left;" ><?php echo $a.".".$b.".".$c.".".$d." - ".$trialbal3['account_code']; ?> (<?php echo $trialbal3['account_name']; ?>)</th>
                                <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                                <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                                 ?></th>
                            </tr>
                            <?php  $e = 1;
                                foreach($parentchildacount[$trialbal3['account_code']] as $trialbal4){
                                    $accountdebitamount =$accountcreditamount =  0;
                                    if($totalcoa[$trialbal4['account_code']]['debit_amount'] !=""){
                                        $accountdebitamount = $totalcoa[$trialbal4['account_code']]['debit_amount'];
                                    }else{
                                        $accountdebitamount =0;
                                    }
                                    if($totalcoa[$trialbal4['account_code']]['credit_amount'] !=""){
                                        $accountcreditamount = $totalcoa[$trialbal4['account_code']]['credit_amount'];
                                    }else{
                                        $accountcreditamount =0;
                                    }
                                    $totalcredit += $accountcreditamount;
                                    $totaldebit += $accountdebitamount;
                                 ?>
                                <tr>
                                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 90px; text-align: left;" ><?php echo $a.".".$b.".".$c.".".$d.".".$e." - ".$trialbal4['account_code']; ?> (<?php echo $trialbal4['account_name']; ?>)</th>
                                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                                     ?></th>
                                </tr>
                                <?php  $f = 1;
                                    foreach($parentchildacount[$trialbal4['account_code']] as $trialbal5){
                                        $accountdebitamount =$accountcreditamount =  0;
                                        if($totalcoa[$trialbal5['account_code']]['debit_amount'] !=""){
                                            $accountdebitamount = $totalcoa[$trialbal5['account_code']]['debit_amount'];
                                        }else{
                                            $accountdebitamount =0;
                                        }
                                        if($totalcoa[$trialbal5['account_code']]['credit_amount'] !=""){
                                            $accountcreditamount = $totalcoa[$trialbal5['account_code']]['credit_amount'];
                                        }else{
                                            $accountcreditamount =0;
                                        }
                                        $totalcredit += $accountcreditamount;
                                        $totaldebit += $accountdebitamount;
                                     ?>
                                    <tr>
                                        <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 90px; text-align: left;" ><?php echo $a.".".$b.".".$c.".".$d.".".$e.".".$f." - ".$trialbal5['account_code']; ?> (<?php echo $trialbal5['account_name']; ?>)</th>
                                        <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                                        <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                                         ?></th>
                                    </tr>
                                    <?php  $g = 1;
                                        foreach($parentchildacount[$trialbal5['account_code']] as $trialbal6){
                                            $accountdebitamount =$accountcreditamount =  0;
                                            if($totalcoa[$trialbal6['account_code']]['debit_amount'] !=""){
                                                $accountdebitamount = $totalcoa[$trialbal6['account_code']]['debit_amount'];
                                            }else{
                                                $accountdebitamount =0;
                                            }
                                            if($totalcoa[$trialbal6['account_code']]['credit_amount'] !=""){
                                                $accountcreditamount = $totalcoa[$trialbal6['account_code']]['credit_amount'];
                                            }else{
                                                $accountcreditamount =0;
                                            }
                                            $totalcredit += $accountcreditamount;
                                            $totaldebit += $accountdebitamount;
                                         ?>
                                        <tr>
                                            <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 90px; text-align: left;" ><?php echo $a.".".$b.".".$c.".".$d.".".$e.".".$f.".".$g." - ".$trialbal6['account_code']; ?> (<?php echo $trialbal6['account_name']; ?>)</th>
                                            <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                                            <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                                             ?></th>
                                        </tr>
                                            <?php  $h = 1;
                                            foreach($parentchildacount[$trialbal6['account_code']] as $trialbal7){
                                                $accountdebitamount =$accountcreditamount =  0;
                                                if($totalcoa[$trialbal7['account_code']]['debit_amount'] !=""){
                                                    $accountdebitamount = $totalcoa[$trialbal7['account_code']]['debit_amount'];
                                                }else{
                                                    $accountdebitamount =0;
                                                }
                                                if($totalcoa[$trialbal7['account_code']]['credit_amount'] !=""){
                                                    $accountcreditamount = $totalcoa[$trialbal7['account_code']]['credit_amount'];
                                                }else{
                                                    $accountcreditamount =0;
                                                }
                                                $totalcredit += $accountcreditamount;
                                                $totaldebit += $accountdebitamount;
                                             ?>
                                            <tr>
                                                <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 90px; text-align: left;" ><?php echo $a.".".$b.".".$c.".".$d.".".$e.".".$f.".".$g.".".$h." - ".$trialbal7['account_code']; ?> (<?php echo $trialbal7['account_name']; ?>)</th>
                                                <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                                                <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                                                 ?></th>
                                            </tr>
                                                <?php  $i = 1;
                                                foreach($parentchildacount[$trialbal7['account_code']] as $trialbal8){
                                                    $accountdebitamount =$accountcreditamount =  0;
                                                    if($totalcoa[$trialbal8['account_code']]['debit_amount'] !=""){
                                                        $accountdebitamount = $totalcoa[$trialbal8['account_code']]['debit_amount'];
                                                    }else{
                                                        $accountdebitamount =0;
                                                    }
                                                    if($totalcoa[$trialbal8['account_code']]['credit_amount'] !=""){
                                                        $accountcreditamount = $totalcoa[$trialbal8['account_code']]['credit_amount'];
                                                    }else{
                                                        $accountcreditamount =0;
                                                    }
                                                    $totalcredit += $accountcreditamount;
                                                    $totaldebit += $accountdebitamount;
                                                 ?>
                                                <tr>
                                                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; padding-left: 90px; text-align: left;" ><?php echo $a.".".$b.".".$c.".".$d.".".$e.".".$f.".".$g.".".$h.".".$i." - ".$trialbal8['account_code']; ?> (<?php echo $trialbal8['account_name']; ?>)</th>
                                                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></th>
                                                    <th scope="col" style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: right;"><?php echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
                                                     ?></th>
                                                </tr>
                                            <?php $i++;
} ?>
                                        <?php $h++;
} ?>
                                    <?php $g++;
} ?>
                                <?php $f++;
} ?>
                            <?php $e++;
} ?>
                        <?php $d++;
} ?>
                    <?php $c++;
} ?>
                <?php $b++;
} ?>
            <?php $a++;
} ?>

            </table>
        </div>
    </div>
</div>

