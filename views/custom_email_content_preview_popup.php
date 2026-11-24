<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table_name = "email_content";
$wh1["where"] = array('apply_table_name'=>$table_name,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$wh1["field"] = "id,fields,label,application_table_name,preview_value";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=create_documents_fields&data=".$data1;
$jsondecode = callAPI($url1);
$jsondecode = json_decode($jsondecode,1);
$inputFildsArray =array();
foreach($jsondecode as $result){
    $inputFildsArray["{[".$result['application_table_name'].".".$result['fields']."]}"] = ucwords($result['preview_value']);
}
function replacestringwithpreview($data,$val){
    $values = urldecode($val);
    $str = "";
    foreach($data as $key => $value){
        $repl =  str_replace($key, $value, $values);
        $values = $repl;
    }
    return $values;
}
?>
<div class="modal-header">
    <h5 class="modal-title">
        <?= __('Email Preview') ?>
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= __('Close') ?>"></button>
</div>
<div class="modal-body" >
<div class="">
<?php if ($templateId === 2) { ?>
    <div class="card">
        <div class="card-header">
            <h5><?php echo replacestringwithpreview($inputFildsArray, $emailSubject); ?></h5>
            <h6 class="float-end"><?php echo date("h : i A"); ?></h6>
        </div>
    </div>
<div class="" style="  width: 100%;">
<table role="presentation" style="padding: 20px; background-color: #f5f5f5; width: 100%; display: inline-block; border-collapse: collapse;">
        <tr>
        <td style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424;padding:15px 0px;">
        <img class="fix" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo htmlspecialchars($displayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($companyName !== '' ? $companyName : __('Company Logo'), ENT_QUOTES, 'UTF-8'); ?>" />
        </td>
        </tr>
        <tr>
        <td>
        <table role="presentation" style="padding: 10px; background-color: #FFFFFF; width: 100%; display: inline-block; border-collapse: collapse;">
        <tr>
        <td style="padding-left:32px; padding-top:20px; font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424" >
        <?php  echo replacestringwithpreview($inputFildsArray, $emailHeader);?>
        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:18px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
        <?php  echo replacestringwithpreview($inputFildsArray, $emailSubHeader);?>
        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
        <div  style=" width: 100% !important; word-break: break-word;white-space: initial;"><?php echo replacestringwithpreview($inputFildsArray, $emailContent1);?> </div>

        </td>
        </tr>

        <tr>
        <td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;"><div  style=" width: 100% !important; word-break: break-word;white-space: initial;">
        <?php echo  replacestringwithpreview($inputFildsArray, $emailContent2);?>
            </div>
        </td>
        </tr>
        <tr>
        <td style="padding:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
        <button type="button" style="background-color: #00abfb; border-radius: 0.25rem; text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px; height: 45px; color: #FFFFFF; border: 1px solid transparent;"><span style="text-decoration: none; color: #FFF;"><?php echo replacestringwithpreview($inputFildsArray, $buttonLabel); ?></span></button>
        </td>
        </tr>
        <tr>
        <td style="padding:10px 33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
        <?= __('Button not working? Try pasting this URL into your browser') ?>

        </td>
        </tr>
        <tr>
        <td style="padding:3px 33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">

        </td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;"><?php echo replacestringwithpreview($inputFildsArray, $emailFooter);?>
        </td>
        </tr>
        </table>
    </div>

<?php } elseif ($templateId === 1) { ?>
<style>
        .hhml{
            font-family: sans-serif;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0); }
            body {margin: 0; padding: 0; min-width: 100%!important;}
            img {height: auto;}
            .content {width: 100%; max-width: 70%;}
            .header {padding: 40px 30px 20px 30px;}
            .innerpadding {padding: 30px 30px 30px 30px;}
            .borderbottom {border-bottom: 1px solid #f2eeed;}
            .subhead {font-size: 15px; color: #ffffff; font-family: sans-serif; letter-spacing: 10px;}
            .h1, .h2, .bodycopy {color: #153643; font-family: sans-serif;}
            .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
            .h2 {padding: 0 0 15px 0; font-size: 18px; line-height: 28px; font-weight: bold;}
            .bodycopy {font-size: 16px; line-height: 22px;}
            .button {
                background-color: #00abfb;
                border-radius: 0.25rem;
                text-align: center;
                font-size: 18px;
                font-family: sans-serif;
                font-weight: bold;
                padding: 0 30px 0 30px;
                height: 45px;
                color: #FFFFFF;
                border: 1px solid transparent;
            }
            .button a {color: #ffffff; text-decoration: none;}
            .footer {padding: 20px 30px 15px 30px;}
            .footercopy {font-family: sans-serif; font-size: 14px; color: #ffffff;}
            .footercopy a {color: #ffffff; text-decoration: underline;}

            @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
                body[yahoo] .hide {display: none!important;}
                body[yahoo] .buttonwrapper {background-color: transparent!important;}
                body[yahoo] .button {padding: 0px!important;}
                body[yahoo] .button a {background-color: #f0f0f0; padding: 15px 15px 13px!important;}
                body[yahoo] .unsubscribe {display: block; margin-top: 20px; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important; font-weight: bold;}
            }

                </style>
<div class="hhml">
    <div class="card">
        <div class="card-header">
            <h5><?php echo replacestringwithpreview($inputFildsArray, $emailSubject); ?></h5>
            <h6 class="float-end"><?php echo date("h : i A"); ?></h6>
        </div>
    </div>

    <table role="presentation" style="width: 100%; background-color: #f6f8f1; border-collapse: collapse;">
                <tr>
                <td>
                <!--[if (gte mso 9)|(IE)]>
                <table role="presentation" style="width: 600px; margin-left: auto; margin-right: auto; border-collapse: collapse;">
                <tr>
                <td>
                <![endif]-->
                <table role="presentation" class="content" style="background-color: #ffffff; margin-left: auto; margin-right: auto; border-collapse: collapse;">
                <tr>
                <td class="header" style="background-color: #f0f0f0;">
                <table role="presentation" style="width: 70px; border-collapse: collapse;">
                <tr>
                <td style="height: 70px; padding: 15px 0;">
                <img class="fix mb-2" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo htmlspecialchars($displayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($companyName !== '' ? $companyName : __('Company Logo'), ENT_QUOTES, 'UTF-8'); ?>" /><br>
<?= htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?>
            </td>
                </tr>
                </table>

                </td>
                </tr>
                <tr>
                <td class="innerpadding borderbottom">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                <tr>
                <td class="h2">
                <?php echo  replacestringwithpreview($inputFildsArray, $emailHeader);?>

                </td>
                </tr>
                <tr>
                <td class="bodycopy">

                <?php echo  replacestringwithpreview($inputFildsArray, $emailSubHeader);?>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                <tr>
                <td class="innerpadding borderbottom">
                <table role="presentation" style="width: 100%; border-collapse: collapse;">
                <tr>
                <td class="h2">
                <?php echo  replacestringwithpreview($inputFildsArray, $emailAddressed);?>
                </td>
                </tr>
                <tr>
                <td class="bodycopy"><div  style=" width: 100% !important; word-break: break-word;white-space: initial;">
                <?php echo  replacestringwithpreview($inputFildsArray, $emailContent1);?></div>
                </td>
                </tr>
                <td class="bodycopy"><div  style=" width: 100% !important; word-break: break-word;white-space: initial;">
                <?php echo  replacestringwithpreview($inputFildsArray, $emailContent2);?></div>
                </td>
                </tr>
                <tr>
                <td style="padding: 20px 0 0 0;">
                <table role="presentation">
                <tr>
                <td>
                <a class="buttonwrapper button" target="_blank" rel="noopener noreferrer" href="#"><?= __('View Order') ?></a>
                </td>
                </tr>
                </table>
                </td>
                </tr>
                </table>
                </td>
                </tr>

                </table>

                </td>
                </tr>
                <tr>
                <td style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
                <?php echo replacestringwithpreview($inputFildsArray, $emailFooter);?>

                </td>
                </tr>
                </table>
        </div>
<?php } elseif ($templateId === 3) { ?>
<div class="card">
        <div class="card-header">
            <h5><?php echo replacestringwithpreview($inputFildsArray, $emailSubject); ?></h5>
            <h6 class="float-end"><?php echo date("h : i A"); ?></h6>
        </div>
    </div>
    <table role="presentation" style="width: 70%; padding: 20px; background-color: #f5f5f5; margin-left: auto; margin-right: auto; border-collapse: collapse;">
        <tr>
        <td style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424; padding:15px 0px;">
        <img class="fix mb-2" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo htmlspecialchars($displayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($companyName !== '' ? $companyName : __('Company Logo'), ENT_QUOTES, 'UTF-8'); ?>" />

        </td>
        </tr>
        <tr>
        <td>
        <table role="presentation" style="width: 100%; padding: 10px; background-color: #FFFFFF; border-collapse: collapse;">
        <tr>
        <td style="padding-left:32px; padding-top:20px; font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424" >
                <?php echo  replacestringwithpreview($inputFildsArray, $emailHeader);?>

        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:18px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >

            <?php echo  replacestringwithpreview($inputFildsArray, $emailSubHeader);?>

        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
        <div  style=" width: 100% !important; word-break: break-word;white-space: initial;"><?php echo  replacestringwithpreview($inputFildsArray, $emailContent1);?> </div>

        </td>
        </tr>
        <tr>
        <td>
        &nbsp;
        </td>
        </tr>
        <tr>
        <td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
            <div  style=" width: 100% !important; word-break: break-word;white-space: initial;">
        <?php echo  replacestringwithpreview($inputFildsArray, $emailContent2);?></div>

        </td>
        </tr>


        </table>

        </td>
        </tr>
        <tr>
        <td style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">

        <?php echo  replacestringwithpreview($inputFildsArray, $emailFooter);?>

        </td>
        </tr>
        </table>

<?php } ?>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close') ?></button>
    </div>
</div>
