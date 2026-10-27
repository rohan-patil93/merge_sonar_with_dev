<?php
  $whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'user_id'=>$_SESSION['userid'],'is_active'=>0);
   $vdata = urlencode(json_encode($whrcon));
   $url = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
   $response = callAPI($url);
   $data = json_decode($response,1);
   $data = $data[0];
   $employee_id =$data['id'];
   ?>
<div class="card card-custom">
<div class="card-body pt-5 label-mrgn">
   <div class="text-center mb-5">
      <div class="symbol symbol-60 symbol-circle symbol-xl-90">
         <div class="symbol-label" style="background-image:url('uploads/all/<?php echo $data['image']; ?>')"></div>
         <i class="symbol-badge symbol-badge-bottom bg-success"></i>
      </div>
      <h4 class="font-weight-bold my-2"><?php echo ucwords( $data['display_name']); ?></h4>
      <div class="text-muted mb-2"><?php echo $data['emp_code'];  ?></div>
   <?php if($_SESSION['user_type'] !=4){ ?>
         <div class="topbar-item my-1 mr-4">
            <?php
            $check_in_display = $clock_data['clock_in'] !="" ? $clock_data['clock_in'] : "00:00:00" ;
               $check_out_display = $clock_data['clock_out'] !="" ? $clock_data['clock_out'] : "00:00:00" ;
               if($clock_data['clock_out'] !='' || count((array)$clock_data) == 0){

            ?>
            <button type="button" onclick="checkinattendence('You have clocked in at')" class="btn btn-primary font-weight-bold btn-pill" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;<?php echo $check_out_display; ?> </div> </div> "> Check-in &nbsp; <i class='icon-lg far fa-clock'></i></button>
            <?php }else{?>
            <button type="button" onclick="checkinattendence('You have clocked out at')" class="btn btn-success font-weight-bold btn-pill" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;00:00:00  </div> </div> "> Check-Out &nbsp; <i class='icon-lg far fa-clock'></i></button>
            <?php } ?>
         </div>
      <?php } ?>

   </div>
   <div class="mb-5 text-center">
      <a href="#" class="btn btn-icon btn-circle btn-light-facebook mr-2">
         <i class="socicon-facebook"></i>
      </a>
      <a href="#" class="btn btn-icon btn-circle btn-light-twitter mr-2">
         <i class="socicon-twitter"></i>
      </a>
      <a href="#" class="btn btn-icon btn-circle btn-light-google">
         <i class="socicon-google"></i>
      </a>
   </div>
   <?php foreach($saidnav as $nav){
         $child = count((array)$nav['child']);
         $link =  'index.php?option='.$nav['Page_Name'] ;
         $icon = ($nav['Menu_Icon'] == '') ?'' : $nav['Menu_Icon'];
         $toggle_name = $child > 0 ?  '1' : '0' ;
         $active = $menu_id!=$nav['id'] ? '' : 'active';
      ?>
      <a href="<?php echo $link; ?>" class="btn btn-hover-light-primary font-weight-bold  mb-2 text-left btn-block <?php echo $active; ?> ">

      <span class="nav-icon py-2 w-auto">
               <span class="svg-icon  svg-icon-primary">
                  <?php echo $icon; ?>

               </span>
            </span>
            <span class="nav-text font-weight-bold "><?php echo $nav['Label']; ?></span>
      </a>
      <?php } ?>
</div>
</div>


