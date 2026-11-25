<?php
class UserClass
{
    public const SQL_IS_ACTIVE_0 = "is_active = '0'";

    /* start */
    public function toGetBannerDetails() {
        global $callConfig;
        $whr = self::SQL_IS_ACTIVE_0;
        $query=$callConfig->selectQuery('banner','*',$whr,'','','');
        return $callConfig->getAllRows($query);
    }
    public function toGetMenuDetails() {
        global $callConfig;
        $whr = self::SQL_IS_ACTIVE_0;
        $query=$callConfig->selectQuery('category','*',$whr,'','','');
        $data = $callConfig->getAllRows($query);
        $retun['category'] = $data;
        $query1=$callConfig->selectQuery('sub_category','*',$whr,'','','');
        $data1 = $callConfig->getAllRows($query1);
        $retun['subcategory'] = $data1;
        return $retun;
    }
    public function toGetSubCategoryProductDetails($post) {
        global $callConfig;
        $whr = self::SQL_IS_ACTIVE_0 . " And sub_category_id ='".$post['sub_id']."'";
        $query=$callConfig->selectQuery('product','*',$whr,'','','');
        return $callConfig->getAllRows($query);
    }
    public function toGetProductDetails($post) {
        global $callConfig;
        $whr = self::SQL_IS_ACTIVE_0 . " And id ='".$post['id']."'";
        $query=$callConfig->selectQuery('product','*',$whr,'','','');
        return $callConfig->getAllRows($query);
    }
}
