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


function getbredcrump($parentcoa,$parentchildacount,$parent_account_code){
    $retuenlist = array();
    foreach($parentcoa as $pcoa1){
        $retuenlist[1]['account_code'] = $pcoa1['account_code'];
        $retuenlist[1]['account_name'] = $pcoa1['account_name'];
        $retuenlist[2] = $retuenlist[3] = $retuenlist[4]= $retuenlist[5] =array();
        if($parent_account_code == $pcoa1['account_code']){
            return $retuenlist;
        }
        foreach($parentchildacount[$pcoa1['account_code']] as $pcoa2){
            $retuenlist[2]['account_code'] = $pcoa2['account_code'];
            $retuenlist[2]['account_name'] = $pcoa2['account_name'];
            $retuenlist[3] = $retuenlist[4]= $retuenlist[5] =array();
            if($parent_account_code == $pcoa2['account_code']){
                return $retuenlist;
            }
            foreach($parentchildacount[$pcoa2['account_code']] as $pcoa3){
                $retuenlist[3]['account_code'] = $pcoa3['account_code'];
                $retuenlist[3]['account_name'] = $pcoa3['account_name'];
                $retuenlist[4]= $retuenlist[5] =array();
                if($parent_account_code == $pcoa3['account_code']){
                    return $retuenlist;
                }
                foreach($parentchildacount[$pcoa3['account_code']] as $pcoa4){
                    $retuenlist[4]['account_code'] = $pcoa4['account_code'];
                    $retuenlist[4]['account_name'] = $pcoa4['account_name'];
                    $retuenlist[5] =array();
                    if($parent_account_code == $pcoa4['account_code']){
                        return $retuenlist;
                    }
                    foreach($parentchildacount[$pcoa4['account_code']] as $pcoa5){
                        $retuenlist[5]['account_code'] = $pcoa5['account_code'];
                        $retuenlist[5]['account_name'] = $pcoa5['account_name'];
                        if($parent_account_code == $pcoa5['account_code']){
                            return $retuenlist;
                        }
                    }
                }
            }
        }
    }
}
if($parent_account_code != '0'){
    $getbredcrump = getbredcrump($parentcoa,$parentchildacount,$parent_account_code);
    ksort($getbredcrump);
}

if(count((array)$getbredcrump) != 0){
?>
<div class="mt-3 mb-5">
    <div class="page-header-breadcrumb ">
        <ul class="breadcrumb breadcrumb-padding mb-4">
            <li class="breadcrumb-item">
                <a class="d-block" href="#!" onclick="getAccountDetail(0)" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                    <i class="icofont icofont-home"></i>
                </a>
            </li>
            <?php foreach($getbredcrump as $bcrup){
                if($bcrup['account_name'] !=""){
                ?>
            <li class="breadcrumb-item" >
                <a class="d-block" href="#!" onclick="getAccountDetail('<?php echo $bcrup['account_code']; ?>')">
                    <span class=""><?php echo $bcrup['account_name']; ?></span>
                    <span class=""> &nbsp; (<?php echo $bcrup['account_code']; ?>)</span>
                </a>
            </li>
            <?php } ?>
            <?php } ?>

        </ul>
    </div>
</div>
<?php } ?>
<table style="width:100%;border-collapse: collapse;" class="mt-5">
    <tbody>
        <tr></tr>
        <tr>
            <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-transform: uppercase; text-align: left;">Account Code</th>
            <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Description</th>
            <th scope="col" style="background-color: #eeeeee; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Debit</th>
            <th scope="col" style="background-color: #cccccc; font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;text-transform: uppercase; text-align: left;">Credit</th>
        </tr>
        <?php
        $totaldebit = $totalcredit = 0;
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
            $difference =  $accountcreditamount-$accountdebitamount;
            $onckiclfun = $textclasss = $icons= "";
             if($trialbal['allow_posting'] !="0"){
                 $onckiclfun = "getAccountDetail('".$trialbal['account_code']."')";
                 $textclasss="text-primary";
                 $icons = '<i class="ti-angle-double-right float-end"></i>';
             }elseif($accountcreditamount!="" && $accountcreditamount!="0"){
                 $onckiclfun = "getAccountTransactionsDetail('".$trialbal['account_code']."')";
                 $textclasss="text-primary";
                 $icons = '<i class="ti-link"></i>';
             }
         ?>
        <tr>
            <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><span class="<?php echo $textclasss; ?> cursor-pointer mb-0" onclick="<?php echo $onckiclfun; ?>" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $trialbal['account_code']; ?> <?php echo $icons; ?></span></td>
            <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px;"><?php echo $trialbal['account_name']; ?></td>
            <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;" class="<?php echo ($accountdebitamount < 0)?"text-danger":""; ?>"><?php //echo ($accountdebitamount!="" && $accountdebitamount!="0")?customeummarmonyformate($accountdebitamount):"-";
            echo ($accountdebitamount!="" && $accountdebitamount!="0")?accountingAmontFormat($accountdebitamount):"-"; ?></td>
            <td style="font-size:14px; font-family:'Tahoma'; font-weight:500; border:1px solid #eeeeee;padding: 5px; text-align: right;" class="<?php echo ($accountcreditamount < 0)?"text-danger":""; ?>"><?php //echo ($accountcreditamount!="" && $accountcreditamount!="0")?customeummarmonyformate($accountcreditamount):"-";

            echo ($accountcreditamount!="" && $accountcreditamount!="0")?accountingAmontFormat($accountcreditamount):"-";
             ?></td>





        </tr>
        <?php } ?>
        <tr>
            <td style="font-size:14px; font-family:'Tahoma';  border:1px solid #eeeeee;padding: 5px;" colspan="2"><p class="text-right cursor-pointer mb-0 f-w-600" >Total </p></td>
            <td style="font-size:14px; font-family:'Tahoma';  border:1px solid #eeeeee;padding: 5px; text-align: right;" class="f-w-600 <?php echo ($totaldebit < 0)?"text-danger":""; ?>"><?php echo ($totaldebit!="" && $totaldebit!="0")?accountingAmontFormat($totaldebit):"-"; ?></td>
            <td style="font-size:14px; font-family:'Tahoma'; border:1px solid #eeeeee;padding: 5px; text-align: right;" class="f-w-600 <?php echo ($totalcredit < 0)?"text-danger":""; ?>"><?php echo ($totalcredit!="" && $totalcredit!="0")?accountingAmontFormat($totalcredit):"-"; ?></td>
        </tr>
    </tbody>
</table>
