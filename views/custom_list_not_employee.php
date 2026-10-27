
<?php

$normalId = (isset($_GET['Rid']))?encodeId($_GET['Rid']):$_SESSION['userid'];
$Rid = (isset($_GET['Rid']))?deocdeId("id:".encodeId($_GET['Rid'])):deocdeId("id:".$_SESSION['userid']);

$whuser["where"] = array('id'=>$normalId);
$whuser["field"] = "id,reports_to,display_name,user_photo,user_email";
$data1user = urlencode(json_encode($whuser));
$urluser = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data1user;
$jsondecodeuser = callAPI($urluser);
$jsondecodeuserlist = json_decode($jsondecodeuser,1);
$jsondecodeuserlist = $jsondecodeuserlist[0];

?>
<script>

 function displayitemdata(page){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#personaldataget").html(loader);
  jQuery.ajax({
    url: "views/new_from.php",
    data:'option='+page+'&Rid=<?php echo $Rid; ?>&pageajax=1',
    type: "POST",
    success:function(data){
      console.log(data);
      $('#personaldataget').html(data);
    },
    error:function (){}
  });
}

 function displayitemviewdata(page){
   var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#personaldataget").html(loader);
  jQuery.ajax({
    url: "views/new_view.php",
    data:'option='+page+'&Rid=<?php echo $Rid; ?>&pageajax=1',
    type: "GET",
    success:function(data){
      console.log(data);
      $('#personaldataget').html(data);
    },
    error:function (){}
  });
}

function displayitemdatalist(option){
 var loader = '<div class="text-center" id="loader-show_3"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#personaldataget").html(loader);
 $.ajax({
   url: "views/view_roll_back1.php",
   data:'option='+option,
   type: "POST",
   success:function(data){

     $('#personaldataget').html(data);
   },
   error:function (){}
 });
}
function displaychangepassword(option){
   var loader = '<div class="text-center" id="loader-show_4"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#personaldataget").html(loader);
 $.ajax({
   url: "views/"+option+".php",
   data:'option='+option+'&Rid=<?php echo $normalId; ?>',
   type: "POST",
   success:function(data){
     $('#personaldataget').html(data);
   },
   error:function (){}
 });
}
</script>
<div class="page-body">
  <div class="row">
    <div class="col-lg-12">
      <div class="cover-profile" style="position: relative;">
        <div class="m-b-25">
          <img class="m-b-25 img-fluid" src="assets/images/user-profile/bg-img1.jpg" alt="bg-img">
          <div class="card-block user-info d-flex justify-content-center">
            <div class="col-md-11">
              <div class="media-left">
                <a href="#" class="profile-image">
                 <?php
                 $dis_name = $jsondecodeuserlist['display_name'];


                 if($jsondecodeuserlist['user_photo']!=""){ ?>
                   <img src="<?php echo "uploads/all/".$jsondecodeuserlist['user_photo']; ?>" class=" img-120 user-img b-radius-10" alt="User">
                 <?php }else{ ?>
                  <span class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-1 text-center bg-white text-black  mt-2"><?php echo initials($dis_name); ?></span>
                <?php } ?>
              </a>
            </div>
            <div class="media-body row">
              <div class="col-lg-12">
                <div class="user-title">
                  <h4 class="text-white"><?php echo $dis_name; ?></h4>
                  <span class="text-white"><?php echo $jsondecodeuserlist['user_email']; ?></span>
                </div>
              </div>
              <div>
                <div class="float-right cover-btn">
                  <button type="button" class="btn btn-primary m-r-20 m-b-20"><i class="icofont icofont-plus"></i>
                  Follow</button>
                  <button type="button" class="btn btn-primary m-b-20" ><i class="icofont icofont-ui-messaging"></i>
                  Message</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12">
    <div class="tab-header card">
      <ul class="nav  md-tabs infisuite_tab" id="mytab">
        <li class="nav-item p-10">
          <a class="nav-link f-w-600 <?php echo $_GET['tab']!="cp"?"active":""; ?>" data-bs-toggle="tab" href="#personal" role="tab" onclick="displayitemdata('COM_INSERT_APPLICATION_USERS')">Personal Info</a>
        </li>


        <li class="nav-item p-10">
          <a class="nav-link f-w-600 <?php echo $_GET['tab']=="cp"?"active":""; ?>" data-bs-toggle="tab" href="#Passwordchange" onclick="displaychangepassword('custom_change_password')" role="tab" >Change Password</a>
        </li>
      </ul>
    </div>
    <div class="tab-content ">
      <div class="tab-pane active" id="personal" role="tabpanel">
        <div class="flex-column-fluid">
          <div class="">
            <div class="page-body " >
              <div class="card ">
                <div class=" flex-row  " id="personaldataget">
                 <?php
                 if($_GET['tab']!="cp"){
                 $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name','application_users',$normalId);
                 $option_url = $tabledata['view_page_name'];
                 $_GET['option'] =  $tabledata['insert_page_name'];
                 $_GET['Rid'] = $Rid;
                 include_once "includes/controller.php";
                 $dispage = str_replace("views/", '', $disptemp);
                 }else{
                    $_POST['Rid'] = $normalId;
                    $dispage = "views/custom_change_password.php";
                 }
                 include_once $dispage;
                 ?>
               </div>
             </div>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>
</div>
</div>
