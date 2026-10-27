<?php 
ini_set('display_errors' ,0);
include('includes/session.php');
include('dbconfig.php');
include('comm.php');
include('join_header.php');

$div_count_array = array('0'=>'0','1'=>'12','2'=>'6','3'=>'4','4'=>'3','5'=>'4', '6'=>'4','7'=>'3','8'=>'3','9'=>'4');
$div_count = array('0','1','2','3','4','5', '6','7','8','9');

$presentation_id = 1;
$vurl = API."?mode=togetpresentationdetails&presentation_id=".$presentation_id;
$configesponse = callAPI($vurl);
$presentation_array = json_decode($configesponse,1);
	//print_r($presentation_array); exit;
?>
<!--begin::Content-->
<div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
	<!--begin::Entry-->
	<div class="d-flex flex-column-fluid">
		<!--begin::Container-->
		<div class="container-fluid">
			<?php 
			$loop = 1;
			foreach ($presentation_array as $key => $data) {
				$required_divisions = count($data['data']);
				if(!in_array($required_divisions, $div_count)){
					$value = $div_count_array[4];
				}
				else{
					$value = $div_count_array[$required_divisions];
				}
				$border = " border-x-0 border-x-md border-y border-y-md-0";
				$column =  "col-md-".$value." col-xxl-".$value;
				?>
				<div class="card card-custom gutter-b" id="<?php echo 'column'.$loop;?>">
					<div class="card-header">
						<div class="card-title">
							<span class="card-icon"><i class="flaticon2-chart text-primary"></i></span>
							<h3 class="card-label"><?php echo $data['header_name']; ?> </h3>
						</div>
					</div>
					<div class="card-body">
						<?php 
						if($loop != 1) { 
							echo' <a href="#" class="btn btn-primary text-uppercase font-weight-bolder px-15 py-3 float-left">Previous</a>';
						} 
						$loop++;
						?>
						<div class="row justify-content-center my-20">
							<!--begin: Pricing-->
							<?php 
							foreach ($data['data'] as  $content) {
								?>
								<div class="<?php echo $column; if($i!=0 && $i!=$required_divisions) { echo $border; } ?>">
									<div class="pt-30 pt-md-25 pb-15 px-5 text-center">
										<!--begin::Icon-->
										<?php if($content['image']!="")
										{
											echo "<img src='".$content['image']."' width='300px' />";
										} 
										?>
										<!--end::Icon-->
										<!--begin::Content-->
										<span class="font-size-h1 d-block d-block font-weight-boldest text-dark-75 py-2"> <?php echo $content['title']; ?></span>
										<h4 class="font-size-h6 d-block d-block font-weight-bold mb-7 text-dark-50"><?php echo $content['sub_header']; ?></h4>
										<p class="mb-15 d-flex flex-column">
											<?php echo $content['title']; ?>
											<?php echo $content['description']; ?>
										</p>
										<?php if($content['button_flag'])
										{
											echo '<a href="'.$content['button_url'].'" class="btn btn-primary text-uppercase font-weight-bolder px-15 py-3">'.$content['button_label'].'</a>';
										} 
										?>
									</div>
								</div>
							<?php }    ?>
						</div>
						<?php if($loop==count($presentation_array)) { ?>
						<a href="<?php echo '#column'.$loop ?>" class="btn btn-primary text-uppercase font-weight-bolder px-15 py-3 float-right">Next</a> 						
						<?php } ?>
						<!--end: Pricing-->
					</div>
				<?php } ?>
			</div>
		</div>
		<!--end::Card-->
