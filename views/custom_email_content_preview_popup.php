<?php
defined('EMAIL_PREVIEW_TIME_FORMAT') || define('EMAIL_PREVIEW_TIME_FORMAT', 'h : i A');
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
if (!function_exists('replacestringwithpreview')) {
    function replacestringwithpreview($data,$val){
        $values = urldecode((string)$val);
        foreach((array)$data as $key => $value){
            $values = str_replace($key, $value, $values);
        }
        return $values;
    }
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
            <h6 class="float-end"><?php echo date(EMAIL_PREVIEW_TIME_FORMAT); ?></h6>
        </div>
    </div>
<div class="" style="width: 100%;">
    <div style="padding: 20px; background-color: #f5f5f5; width: 100%; box-sizing: border-box;">
        <div style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424; padding:15px 0px;">
            <img class="fix" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo htmlspecialchars($displayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($companyName !== '' ? $companyName : __('Company Logo'), ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div style="padding: 10px; background-color: #FFFFFF; width: 100%; box-sizing: border-box;">
            <div style="padding-left:32px; padding-top:20px; font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424;">
                <?php echo replacestringwithpreview($inputFildsArray, $emailHeader);?>
            </div>
            <div style="padding-left:33px; font-size:18px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
                <?php echo replacestringwithpreview($inputFildsArray, $emailSubHeader);?>
            </div>
            <div style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
                <div style="width: 100% !important; word-break: break-word; white-space: initial;"><?php echo replacestringwithpreview($inputFildsArray, $emailContent1);?></div>
            </div>
            <div style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
                <div style="width: 100% !important; word-break: break-word; white-space: initial;">
                    <?php echo replacestringwithpreview($inputFildsArray, $emailContent2);?>
                </div>
            </div>
            <div style="padding:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
                <button type="button" style="background-color: #00abfb; border-radius: 0.25rem; text-align: center; font-size: 18px; font-family: sans-serif; font-weight: bold; padding: 0 30px 0 30px; height: 45px; color: #FFFFFF; border: 1px solid transparent;"><span style="text-decoration: none; color: #FFF;"><?php echo replacestringwithpreview($inputFildsArray, $buttonLabel); ?></span></button>
            </div>
            <div style="padding:10px 33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
                <?= __('Button not working? Try pasting this URL into your browser') ?>
            </div>
            <div style="padding:3px 33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
            </div>
        </div>
        <div style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
            <?php echo replacestringwithpreview($inputFildsArray, $emailFooter);?>
        </div>
    </div>
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
            <h6 class="float-end"><?php echo date(EMAIL_PREVIEW_TIME_FORMAT); ?></h6>
        </div>
    </div>

    <div style="width: 100%; background-color: #f6f8f1; box-sizing: border-box;">
        <div class="content" style="background-color: #ffffff; margin-left: auto; margin-right: auto; box-sizing: border-box;">
            <div class="header" style="background-color: #f0f0f0;">
                <div style="width: 70px;">
                    <div style="height: 70px; padding: 15px 0;">
                        <img class="fix mb-2" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo htmlspecialchars($displayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($companyName !== '' ? $companyName : __('Company Logo'), ENT_QUOTES, 'UTF-8'); ?>" /><br>
                        <?= htmlspecialchars($companyName, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                </div>
            </div>
            <div class="innerpadding borderbottom">
                <div style="width: 100%;">
                    <div class="h2">
                        <?php echo replacestringwithpreview($inputFildsArray, $emailHeader);?>
                    </div>
                    <div class="bodycopy">
                        <?php echo replacestringwithpreview($inputFildsArray, $emailSubHeader);?>
                    </div>
                </div>
            </div>
            <div class="innerpadding borderbottom">
                <div style="width: 100%;">
                    <div class="h2">
                        <?php echo replacestringwithpreview($inputFildsArray, $emailAddressed);?>
                    </div>
                    <div class="bodycopy">
                        <div style="width: 100% !important; word-break: break-word; white-space: initial;">
                            <?php echo replacestringwithpreview($inputFildsArray, $emailContent1);?>
                        </div>
                    </div>
                    <div class="bodycopy">
                        <div style="width: 100% !important; word-break: break-word; white-space: initial;">
                            <?php echo replacestringwithpreview($inputFildsArray, $emailContent2);?>
                        </div>
                    </div>
                    <div style="padding: 20px 0 0 0;">
                        <div>
                            <a class="buttonwrapper button" target="_blank" rel="noopener noreferrer" href="#"><?= __('View Order') ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
                <?php echo replacestringwithpreview($inputFildsArray, $emailFooter);?>
            </div>
        </div>
    </div>
</div>
<?php } elseif ($templateId === 3) { ?>
<div class="card">
    <div class="card-header">
        <h5><?php echo replacestringwithpreview($inputFildsArray, $emailSubject); ?></h5>
        <h6 class="float-end"><?php echo date(EMAIL_PREVIEW_TIME_FORMAT); ?></h6>
    </div>
</div>
<div style="width: 70%; padding: 20px; background-color: #f5f5f5; margin-left: auto; margin-right: auto; box-sizing: border-box;">
    <div style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424; padding:15px 0px;">
        <img class="fix mb-2" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo htmlspecialchars($displayLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($companyName !== '' ? $companyName : __('Company Logo'), ENT_QUOTES, 'UTF-8'); ?>" />
    </div>
    <div style="width: 100%; padding: 10px; background-color: #FFFFFF; box-sizing: border-box;">
        <div style="padding-left:32px; padding-top:20px; font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424;">
            <?php echo replacestringwithpreview($inputFildsArray, $emailHeader);?>
        </div>
        <div style="padding-left:33px; font-size:18px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
            <?php echo replacestringwithpreview($inputFildsArray, $emailSubHeader);?>
        </div>
        <div style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
            <div style="width: 100% !important; word-break: break-word; white-space: initial;"><?php echo replacestringwithpreview($inputFildsArray, $emailContent1);?></div>
        </div>
        <div>
            &nbsp;
        </div>
        <div style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
            <div style="width: 100% !important; word-break: break-word; white-space: initial;">
                <?php echo replacestringwithpreview($inputFildsArray, $emailContent2);?>
            </div>
        </div>
    </div>
    <div style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
        <?php echo replacestringwithpreview($inputFildsArray, $emailFooter);?>
    </div>
</div>

<?php } ?>
</div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close') ?></button>
    </div>
</div>
