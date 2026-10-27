<?php
class userClass
{ 
	/* start */
	function toGetBannerDetails() {
		global $callConfig;
		$whr = "is_active = '0'";
		$query=$callConfig->selectQuery('banner','*',$whr,'','','');
		//echo $query;
		$data = $callConfig->getAllRows($query);
		return $data;
	}
	function toGetMenuDetails() {
		global $callConfig;
		$whr = "is_active = '0'";
		$query=$callConfig->selectQuery('category','*',$whr,'','','');
		$data = $callConfig->getAllRows($query);
		$retun['category'] = $data;
		$query1=$callConfig->selectQuery('sub_category','*',$whr,'','','');
		$data1 = $callConfig->getAllRows($query1);
		$retun['subcategory'] = $data1;
		return $retun;
	}
	function toGetSubCategoryProductDetails($post) {
		global $callConfig;
		$whr = "is_active = '0' And sub_category_id ='".$post['sub_id']."'";
		$query=$callConfig->selectQuery('product','*',$whr,'','','');
		$data = $callConfig->getAllRows($query);
		return $data;
	}
	function toGetProductDetails($post) {
		global $callConfig;
		$whr = "is_active = '0' And id ='".$post['id']."'";
		$query=$callConfig->selectQuery('product','*',$whr,'','','');
		$data = $callConfig->getAllRows($query);
		return $data;
	}
}

