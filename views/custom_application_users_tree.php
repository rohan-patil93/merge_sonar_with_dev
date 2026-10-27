<?php 
$wh1["where"] = array('user_type'=>1,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$wh1["field"] = "id,display_name";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data1; 
$jsondecode = callAPI($url1);
$jsondecode = json_decode($jsondecode,1);
?>
<style>
	#tree{
  width:100%;
  height:100%;
}
[data-item = "details"], [data-item = "edit"], [data-item = "add"]{
    color: #1b9ce2 !important;
}
[data-item] {
  border-width: 0px !important; 
  padding: 15px !important;
}
.chart-menu {
  box-shadow:  0px 5px 10px -2px #aeaeae !important;
}
</style>
<div class="page-body">
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header table-card-header mb-3">
					<h5> User Lists </h5>
					<div class="card-header-right table-card-header mb-2">
				<label class="form-label  col-form-label m-r-20">User List</label>		

<select class="form-select js-example-basic-single " id="hid_userid" onchange="usertree(this.value)">
	<?php foreach($jsondecode as $dt){ ?>
		<option value="<?php echo $dt['id']; ?>" <?php echo $dt['id']==$_SESSION['userid']?"selected":"";  ?>><?php echo $dt['display_name']; ?></option>
	
	<?php } ?>
</select>
						<a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
							<i class="icofont icofont-reply"></i>
							Back
						</a>
					</div>
				</div>
				<div class="card card-custom card-sticky" id="kt_page_sticky_card">
					<div class="card-body">
						<div class="form-group row ml-20 mr-20 j-wrapper  ">

							<div id="tree"/></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>