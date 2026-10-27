<?php 
if(isset($_POST['submitbellcurves'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertappraisalbellcurves&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    $response =$response2['insert'];
    if($response['status'] == 'sucess'){ 
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
            //$_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            $getid = $response["LASTID"];
            $Rrid = deocdeId("id:".$getid);
            header("location:index.php?option=COM_LIST_APPRAISAL_BELL_CURVES&Rid=".$Rrid); exit;
        }        
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                } 
            }
        </script>
        <?php
    }else{
        //($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}

$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$rid = $erid['1']; 
$apiurl = API."?mode=getappraisalbellcurves&rid=".$rid; 
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$appraisal_rating_slabs = $result['appraisal_rating_slabs'];
$appraisal_bell_curves = $result['appraisal_bell_curves'];
$slabsval = json_decode($appraisal_bell_curves['slabs'],1);
//echo "<pre>"; print_r($slabsval); echo "</pre>";
?>
<script type="text/javascript">
	function getTotalBellCurve(){
		var sumcnt = 0;
		$('.bell_curve_sum').each(function(){
			if($(this).val() !="" && $(this).val() !="0"){
				sumcnt += parseFloat($(this).val());
			}
		});
		$("#bell_curve_sum_text").text(sumcnt+"%");
		if(sumcnt ==100){
			$('#bell_curve_btn').prop('disabled', false);
		}else{
			$('#bell_curve_btn').prop('disabled', true);
		}
	}
</script>
<?php if($error_message !=''){ ?>
		<div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>	
<?php } ?>
<div class="card custom_card">
	<div class="card-header">
		<h5>Performance Bell Curves </h5>
		<div class="card-header-right table-card-header m-r-50">
			<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
				<i class="icofont icofont-reply"></i>
				Back
			</a>
		</div>
	</div>
	<div class="card-body ">
		<form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="form-group col-lg-4">
                    <label class="form-label">Bell Curve Name<span class="text-danger">*</span></label>
                    <input type="text" name="bell_curve_name" class="form-control" value="<?php echo $appraisal_bell_curves['bell_curve_name']; ?>" required>
                </div>
                <div class="form-group col-lg-8">
                	<div class="row">
		                <div class=" col-lg-6">
		                	 <label>Slab Name<span class="text-danger">*</span></label>
		                </div>
		                 <div class="col-lg-6 text-right">
		                	 <label>Percentage (%)<span class="text-danger">*</span></label>
		                </div>
		            </div>
                	<?php $sum = 0; foreach($appraisal_rating_slabs as $aprslab){ 
                		$slab11 = $slabsval[$aprslab['id']]; 
                		$sum += $slab11['percentage'];
                		?>
                	<div class="row">
                		<div class="form-group col-lg-6">
                			<input type="hidden" name="slabs[<?php echo $aprslab['id']; ?>][appraisal_rating_slab_id]" value="<?php echo $aprslab['id']; ?>">
		                    <input type="text" class="form-control" value="<?php echo $aprslab['slabs_name']; ?>" style="background-color:<?php echo $aprslab['background_color']; ?>; color: <?php echo $aprslab['font_color']; ?> " readonly>
			            </div>
			            <div class="form-group col-lg-6">
			            	<div class="input-group input-group-button"> 
		                    	<input type="number" name="slabs[<?php echo $aprslab['id']; ?>][percentage]" class="form-control bell_curve_sum text-right width1" value="<?php echo $slab11['percentage']; ?>" onblur="getTotalBellCurve()" required>
			                    <span class="input-group-text addon-style">
								    <label class="">%</label>
								</span>
							</div>
			            </div>
			        </div>
			    <?php } ?>
			    <div class="row">
		                <div class="form-group col-lg-6 text-right">
		                	 <label class="form-label">Total<span class="text-danger">*</span></label>
		                </div>
		                <div class="form-group col-lg-6 text-right">
		                	<label class="form-label " id="bell_curve_sum_text"><?php echo $sum; ?>%</label>
		                </div>
		            </div>
                 </div>
            </div>
            <input type="hidden" name="rid" value="<?php echo $rid; ?>">
            <button type="submit" class="btn btn-primary waves-effect waves-light mt-5" id="bell_curve_btn" name="submitbellcurves" <?php echo $sum ==100?"":"disabled";?>><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>   
        </form>
	</div>

</div>
