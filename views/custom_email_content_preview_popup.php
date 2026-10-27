<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
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
		Email Preview
	</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" > 
<div class="">	
<?php if($_POST['template_id']==2){ ?>
	<div class="card">
		<div class="card-header">
			<h5><?php echo replacestringwithpreview($inputFildsArray,$_POST['email_subject']); ?></h5>
			<h6 class="float-end"><?php echo date("h : i A"); ?></h6>
		</div>
	</div>
<div class="" style="  width: 100%;">
<table  style="padding:20px;background-color:#f5f5f5;width: 100%;display: inline-block;" align="middle" cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424;padding:15px 0px;">
		<img class="fix" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo $_SESSION['config']['display_logo']; ?>" border="0" alt="" />
		</td>
		</tr>
		<tr>
		<td>
		<table  style="padding:10px;background-color:#FFFFFF; width: 100%;display: inline-block;"  >
		<tr>
		<td style="padding-left:32px; padding-top:20px; font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424" >
		<?php  echo replacestringwithpreview($inputFildsArray,$_POST['email_header']);?>
		</td>
		</tr>
		<tr>
		<td style="padding-left:33px; font-size:18px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
		<?php  echo replacestringwithpreview($inputFildsArray,$_POST['email_sub_header']);?>
		</td>
		</tr>
		<tr>
		<td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
		<div  style=" width: 100% !important; word-break: break-word;white-space: initial;"><?php echo replacestringwithpreview($inputFildsArray,$_POST['email_content_1']);?> </div>

		</td>
		</tr>
		
		<tr>
		<td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;"><div  style=" width: 100% !important; word-break: break-word;white-space: initial;">
		<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_content_2']);?>
			</div>
		</td>
		</tr>
		<tr>
		<td style="padding:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
		<button style="background-color: #00abfb;border-radius: 0.25rem;text-align: center;font-size: 18px;font-family: sans-serif;font-weight: bold;    padding: 0 30px 0 30px;height: 45px;color: #FFFFFF; border: 1px solid transparent;" ><a style="text-decoration:none;color:#FFF;" ><?php echo replacestringwithpreview($inputFildsArray,$_POST['button_label']);?></a></button>
		</td>
		</tr>
		<tr>
		<td style="padding:10px 33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
		Button not working? Try pasting this URL into your browser 

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
		<td style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;"><?php echo replacestringwithpreview($inputFildsArray,$_POST['email_fotter']);?>
		</td>
		</tr>
		</table>
	</div>

<?php  }elseif($_POST['template_id']==1){ ?>
<style type="text/css">
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
			<h5><?php echo replacestringwithpreview($inputFildsArray,$_POST['email_subject']); ?></h5>
			<h6 class="float-end"><?php echo date("h : i A"); ?></h6>
		</div>
	</div>

	<table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
				<tr>
				<td>
				<!--[if (gte mso 9)|(IE)]>
				<table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
				<td>
				<![endif]-->     
				<table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
				<tr>
				<td bgcolor="#fofofo" class="header">
				<table width="70" align="left" border="0" cellpadding="0" cellspacing="0">  
				<tr>
				<td height="70" style="padding:15px 0px;">
				<img class="fix mb-2" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo $_SESSION['config']['display_logo']; ?>" border="0" alt="" /></br>
<?php echo $_SESSION['config']['name']; ?>
			</td>
				</tr>
				</table>
			
				</td>
				</tr>
				<tr>
				<td class="innerpadding borderbottom">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				<td class="h2">
				<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_header']);?>
				
				</td>
				</tr>
				<tr>
				<td class="bodycopy">
				
				<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_sub_header']);?>
				</td>
				</tr>
				</table>
				</td>
				</tr>
				<tr>
				<td class="innerpadding borderbottom">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				<td class="h2">	
				<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_addressed']);?>
				</td>
				</tr>
				<tr>
				<td class="bodycopy"><div  style=" width: 100% !important; word-break: break-word;white-space: initial;">
				<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_content_1']);?></div>
				</td>
				</tr>
				<td class="bodycopy"><div  style=" width: 100% !important; word-break: break-word;white-space: initial;">
				<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_content_2']);?></div>
				</td>
				</tr>
				<tr>
				<td style="padding: 20px 0 0 0;">
				<table>
				<tr>
				<td>
				<button class=" buttonwrapper button"><a target="_blank" href="#">View Order</a></button>
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
				<?php echo replacestringwithpreview($inputFildsArray,$_POST['email_fotter']);?>

				</td>
				</tr>
				</table>
		</div>	
<?php  }elseif($_POST['template_id']==3){ ?>
<div class="card">
		<div class="card-header">
			<h5><?php echo replacestringwithpreview($inputFildsArray,$_POST['email_subject']); ?></h5>
			<h6 class="float-end"><?php echo date("h : i A"); ?></h6>
		</div>
	</div>
	<table width="70%" style="padding:20px;background-color:#f5f5f5;" align="center" cellpadding="0" cellspacing="0" border="0">
		<tr>
		<td style="font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424; padding:15px 0px;">
		<img class="fix mb-2" src="<?php echo UPLOADIMGURL; ?>/all/<?php echo $_SESSION['config']['display_logo']; ?>" border="0" alt="" />

		</td>
		</tr>
		<tr>
		<td>
		<table width="100%" style="padding:10px;background-color:#FFFFFF;"  >
		<tr>
		<td style="padding-left:32px; padding-top:20px; font-size:24px; letter-spacing:normal; font-stretch:normal; font-weight:bold; line-height:1.79; color:#252424" >
				<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_header']);?>

		</td>
		</tr>
		<tr>
		<td style="padding-left:33px; font-size:18px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
	
			<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_sub_header']);?>

		</td>
		</tr>
		<tr>
		<td style="padding-left:33px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b" >
		<div  style=" width: 100% !important; word-break: break-word;white-space: initial;"><?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_content_1']);?> </div>

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
		<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_content_2']);?></div>

		</td>
		</tr>


		</table>

		</td>
		</tr>
		<tr>
		<td style="padding:15px 0px; font-size:14px; font-stretch:normal; line-height:1.79; letter-spacing:normal; font-weight:normal; color:#5c5b5b;">
	
		<?php echo  replacestringwithpreview($inputFildsArray,$_POST['email_fotter']);?>

		</td>
		</tr>
		</table>
		
<?php } ?>   
</div>            
	<div class="modal-footer">
		<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>  
	</div>
</div>
