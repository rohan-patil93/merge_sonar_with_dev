<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$page =  $_GET['pagename'];
if($_GET['tablename'] == "hsn_and_sac_codes"){ 
    $selectfiledextra = $_GET['fieldsname1'].",".$_GET['fieldsname2'].",".$_GET['fieldsname3'];
    $url1 = API."?mode=getcitydatasautotype&table=".$_GET['tablename']."&selectfiled=".$_GET['fieldsname']."&namewords=".$_GET['q']."&extracat=".$selectfiledextra;
    $data_details1 = callAPI($url1);
    $values =  json_decode($data_details1,1);
  // echo "reewds"; print_r($values);
    foreach($values as $row){
      $arraybdbd[] = array('id'=>$row['id'],'full_name'=>$row[$_GET['fieldsname1']]." - ".$row[$_GET['fieldsname2']]."(".$row[$_GET['fieldsname3']]."%)");
    }
    $arrara = array('total_count'=>1,'incomplete_results'=>true,'items'=>$arraybdbd); 
    echo json_encode($arrara);
}elseif($_GET['tablename'] == "chart_of_accounts"){ 
    $selectfiledextra = "account_code,account_name";
    $url1 = API."?mode=getcitydatasautotype&table=".$_GET['tablename']."&selectfiled=".$_GET['fieldsname']."&namewords=".$_GET['q']."&extracat=".$selectfiledextra;
    $data_details1 = callAPI($url1);
    $values =  json_decode($data_details1,1);
    foreach($values as $row){
      $arraybdbd[] = array('id'=>$row['account_code'],'full_name'=>$row['account_code']." (".$row['account_name'].")");
    }
    $arrara = array('total_count'=>1,'incomplete_results'=>true,'items'=>$arraybdbd); 
    echo json_encode($arrara);
}else{
  if($page=="comman_form"){
    $arraybdbd = dyanamicselectAutoSelect($_GET['tablename'],$_GET['fieldsname'],$_GET['q']);
    $arrara = array('total_count'=>1,'incomplete_results'=>true,'items'=>$arraybdbd); 
    echo json_encode($arrara);

  }else{

    if(SAMEPARTNUMBERALLOWED == "N"){
        $tringselect = rtrim($_GET['stringids'], ",");
    }else{
        $tringselect = 0;
    }
    if(($tringselect=="" OR $tringselect=="0")){
       	$url1 = API."?mode=getcitydatasautotype&table=".$_GET['tablename']."&selectfiled=".$_GET['fieldsname']."&namewords=".$_GET['q']."&extracat=".$_GET['extracat']."&transaction_date=".$_GET['transaction_date'];  
    }else{
      $url1 = API."?mode=getcitydatasautotype&table=".$_GET['tablename']."&selectfiled=".$_GET['fieldsname']."&namewords=".$_GET['q']."&selectvalue=".$tringselect."&extracat=".$_GET['extracat']."&transaction_date=".$_GET['transaction_date'];    
    }
      $data_details1 = callAPI($url1);
      $values =  json_decode($data_details1,1);
      //print_r($values);
      if($_GET['extracat1'] !=""){
          $explode2 = explode(',',$_GET['extracat1']);
      } 
      if($_GET['refitemid'] !=""){
          $refitemid = explode(',',$_GET['refitemid']);
      }
      foreach($values as $row){

        if($_GET['amendment'] =='Y'){
          $arraybdbd[] = array('id'=>$row['id'],'full_name'=>$row[$_GET['fieldsname']]);
        }elseif(count((array)$explode2) !=0 AND count((array)$refitemid) !=0){
            if(in_array($row[$_GET['extracat']], (array)$explode2) AND in_array($row['id'],(array)$refitemid)){
                  $arraybdbd[] = array('id'=>$row['id'],'full_name'=>$row['sku']." - ".$row[$_GET['fieldsname']]);
            }
        }elseif(count((array)$refitemid) !=0){
          if(in_array($row['id'], (array)$refitemid)){
              $arraybdbd[] = array('id'=>$row['id'],'full_name'=>$row['sku']." - ".$row[$_GET['fieldsname']]);
          }
        }elseif(count((array)$explode2)  != 0){
          if(in_array($row[$_GET['extracat']], (array)$explode2)){
            $arraybdbd[] = array('id'=>$row['id'],'full_name'=>$row['sku']." - ".$row[$_GET['fieldsname']]);
          }
        }else{
          $arraybdbd[] = array('id'=>$row['id'],'full_name'=>$row['sku']." - ".$row[$_GET['fieldsname']]);
        }
      }
      //$arraybdbd[] = array('id'=>'-1','full_name'=>'Other');
      if($_GET['ref_id'] !='' AND  $_GET['amendment'] =='N'){
        $arraybdbd[] = array('id'=>'-0','full_name'=>'Amendment');
      }
      $arrara = array('total_count'=>1,'incomplete_results'=>true,'items'=>$arraybdbd); 
      echo json_encode($arrara);
  }
}
?>
