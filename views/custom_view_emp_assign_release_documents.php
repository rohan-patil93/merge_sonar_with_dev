<?php
$_SESSION['apply_js']['multiselest'] = 1;
$empdata =array();
$ress =$_POST;
if(isset($_POST['submit']) && $_POST['submit']=='search'){

if($_POST['department']!=""){ $vdata1["where"]["department_id"] = $_POST['department'];
}
if($_POST['designation']!=""){ $vdata1["where"]["designation_id"] = $_POST['designation'];
}
if($_POST['location']!=""){ $vdata1["where"]["location_id"] = $_POST['location'];
}
if($_POST['vendor']!=""){ $vdata1["where"]["vendor_id"] = $_POST['vendor'];
}
if($_POST['cost_center_pp']!=""){ $vdata1["where"]["cost_center_id"] = $_POST['cost_center_pp'];
}
if($_POST['emp_name_code']!=""){ $vdata1["where"]["emp_name_code"] = $_POST['emp_name_code'];
}
}
$vdata1["where"]["company_id"] = $_SESSION['company_id'];
$vdata1["where"]["is_active"] = 0;
$url = API."?mode=getassignemployeelistpopup&method=post";
$response = file_post_contents($url, $vdata1);
$empdata = json_decode($response,1);
$empdep = $empdata['department'];
$empdes = $empdata['designation'];
$emploc = $empdata['locations'];
$empven = $empdata['vendors'];
$empcos = $empdata['cost_centers'];
$aemp = array();
if($_REQUEST['assessment_year']!="" && $_REQUEST['list']!=""){
  $vdata1["where"] = array('is_active'=> '0', 'assessment_year'=>$_REQUEST['assessment_year'], 'document_list_id'=>$_REQUEST['list']);
  $vdata = urlencode(json_encode($vdata1));
  $vurl = API."?mode=gettabledetailswithconditions&table=emp_assign_release_documents&data=".$vdata;
  $configesponse = callAPI($vurl);
  $data = json_decode($configesponse,1);
  foreach($data as $eemp){
      $aemp[$eemp['emp_id']] = $eemp;
  }
}
$vdata1["where"] = array('is_active'=> '0', 'category_id'=>'4');
  $vdata1["field"] = "id, document_name";
  $vdata = urlencode(json_encode($vdata1));
  $vurl = API."?mode=gettabledetailswithconditions&table=document_lists&data=".$vdata;
  $configesponse = callAPI($vurl);
  $typedata = json_decode($configesponse,1);

?>
<script type="text/javascript">

  function tocheckallboxes(){
    console.log("Data");
    var val = $('#val_select_all').val();
    console.log(val);
    if(val==0){
      $('#val_select_all').val('1');
      $('.checkbox1').each(function(){
        this.checked = true;
      });
    }else{
      $('#val_select_all').val('0');
      $('.checkbox1').each(function(){
        this.checked = false;
      });
    }
    assignemp('all', 'all', val)
  }
  function assignemp(empid,type,chck=""){

    var assement = $('#assement').val();
    console.log("assement:"+assement+"empid:"+empid+",type:"+type);
    if(assement!=0){
      console.log("empid:"+empid+",type:"+type);
      $.ajax({
        url: "ajax.php",
        data:'page=assignemptofrom16a&empid='+empid+'&type='+type+'&assement='+assement+"&check="+chck,
        type: "POST",
        success:function(data){
          console.log(data);
        },
        error:function (){}
      });
    }else{
       $('#val_select_all').val('0');
      document.getElementById("checkall").checked = false;
      $('.checkbox1').each(function(){
        this.checked = false;
      });

      swal({
          title: <?php echo json_encode(__('Please Select Assement Year'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
          type: "error",
          showCancelButton: false,
          closeOnConfirm: true,
          showLoaderOnConfirm: true
      });

    }
  }
  function assementdetail(val){
    console.log(val)
    window.location.href = 'index.php?option=COM_VIEW_ASSIGN_EMPLOYEE_FROM_16A&assessment_year='+val;
  }
  function showfilter(){
    var fval = $('#filterval').val();
    if(fval == 0){
      $('#showfilter').show();
      $('#filterval').val('1');
    }
    if(fval == 1){
      $('#showfilter').hide();
      $('#filterval').val('0');
    }

  }
</script>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h5> <?= __('Assign Employees'); ?></h5>
        <div class="card-header-right">
          <button type="button" class="btn btn-primary" onclick="showfilter()">
            <?= __('Filter'); ?>
          </button>

          <input type="hidden" name="filterval" id="filterval" value="0">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>  <?= __('Back'); ?>
          </a>
        </div>
      </div>
      <div class="card-body">
        <form class="kt-form kt-form--label-right" action="" method="post">
        <div class="row col-lg-12" style="display:none;" id="showfilter">
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
              <span><?php echo __("Cost Center"); ?>:</span>
              <select aria-label="<?= htmlspecialchars(__('Cost Center Pp'), ENT_QUOTES, 'UTF-8'); ?>" class=" form-select js-example-basic-single" id="cost_center_pp" name="cost_center_pp">
                <option value=""> <?= __('All'); ?>  </option>
                <?php foreach($empcos as $cos){ $emp_cos[$cos['id']]= $cos['cost_center_name']; ?>
                <option   <?php if($ress['cost_center_pp'] == $cos['id'] ){ ?> selected <?php
} ?> value="<?php echo $cos['id']; ?>"><?php echo $cos['cost_center_name']; ?></option>
              <?php } ?>
               </select>
          </div>
          <div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo __("Vendors"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Vendor'), ENT_QUOTES, 'UTF-8'); ?>" class=" form-select js-example-basic-single" id="vendor" name="vendor">
              <option value=""> <?= __('All'); ?>  </option>
              <?php foreach($empven as $ven){ $emp_ven[$ven['id']]= $ven['display_name']; ?>
              <option   <?php if($ress['vendor'] == $ven['id'] ){ ?> selected <?php
} ?> value="<?php echo $ven['id']; ?>"><?php echo $ven['display_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo __("Department"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Department'), ENT_QUOTES, 'UTF-8'); ?>" class=" form-select js-example-basic-single" id="department" name="department">
              <option value=""> <?= __('All'); ?>  </option>
              <?php foreach($empdep as $dep){ $emp_dep[$dep['id']]= $dep['department_name'];  ?>
              <option  <?php if($ress['department'] == $dep['id'] ){ ?> selected <?php
} ?> value="<?php echo $dep['id']; ?>"><?php echo $dep['department_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo __("Designation"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Designation'), ENT_QUOTES, 'UTF-8'); ?>" class=" form-select js-example-basic-single" id="designation" name="designation">
              <option value=""> <?= __('All'); ?>  </option>
              <?php foreach($empdes as $des){  $emp_des[$des['id']]= $des['designation_name'];  ?>
              <option  <?php if($ress['designation'] == $des['id'] ){ ?> selected <?php
} ?> value="<?php echo $des['id']; ?>"><?php echo $des['designation_name']; ?></option>
            <?php } ?>
            </select>
          </div>
          <div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile mt-2">
            <span><?php echo __("Location"); ?>:</span>
            <select aria-label="<?= htmlspecialchars(__('Location'), ENT_QUOTES, 'UTF-8'); ?>" class=" form-select js-example-basic-single" id="location" name="location">
              <option value=""> <?= __('All'); ?>  </option>
              <?php foreach($emploc as $loc){ $emp_loc[$loc['id']]= $loc['location_name']; ?>
              <option   <?php if($ress['location'] == $loc['id'] ){ ?> selected <?php
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile mt-2">
            <span><?= __('Enter Employee Name/Code'); ?></span>
            <input aria-label="<?= htmlspecialchars(__('Emp Name Code'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="emp_name_code"  value="<?php echo $_POST['emp_name_code']; ?>" id="emp_name_code">
          </div>
          <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span>&nbsp;</span>
            <input type="hidden" name="assessment_year" value="<?php echo $_REQUEST['assessment_year'] ?>">
            <input type="hidden" name="submit" value="search" />
            <div>
              <button class="btn btn-primary btn-brand--icon" id="kt_search" >
                <span><span><?= __('Search'); ?></span></span>
              </button>
            </div>
          </div>
        </div>
        </form>
        <div class="separator separator-solid mb-3"></div>
        <div class="row col-lg-12">
          <div class="col-lg-3"> <span><?= __('Type'); ?>:&nbsp;</span> </div>
          <div class="col-lg-3">
            <select aria-label="<?= htmlspecialchars(__('Type'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="type" id="type" onchange="assementdetail(this.value)">
              <option value="0"><?= __('Select Year'); ?></option>
              <?php foreach($typedata as $type){ ?>
                  <option <?php if($_REQUEST['list']==$type['id']){ echo "selected";
} ?> value="<?php echo $type['id']; ?>"><?php echo $type['document_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3"> <span><?= __('Assement Year'); ?>:&nbsp;</span> </div>
          <div class="col-lg-3">
            <select aria-label="<?= htmlspecialchars(__('Assement'), ENT_QUOTES, 'UTF-8'); ?>" class="js-example-basic-single" name="assement" id="assement" onchange="assementdetail(this.value)">
              <option value="0"><?= __('Select Year'); ?></option>
              <?php $asyear = date('Y'); $asyear = $asyear-5;
                  for($i=1;$i<6;$i++){
                      $vaass = $asyear."-".($asyear+1);
                      $asyear = $asyear+1;
              ?>
                      <option <?php if($_REQUEST['assessment_year']==$vaass){ echo "selected";
} ?> value="<?php echo $vaass; ?>"><?php echo $vaass; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="dt-responsive table-responsive">
          <table class="table table-hover custom_asign_emp_mpc" id="kt_datatable1" >
            <thead >
              <tr>
                <th scope="col" style="width: 2%;">#</th>
                <th scope="col" style="width: 15%;"><?= __('Name'); ?> </th>
                <th scope="col" style="width: 10%;"><?= __('Location'); ?></th>
                <th scope="col" style="width: 10%;"><?= __('Send'); ?></th>
                <th scope="col" style="width: 10%;"><?= __('Send Date'); ?></th>
                <th scope="col" style="width: 10%;"><?= __('Read'); ?></th>
                <th scope="col" style="width: 10%;"><?= __('Read Date'); ?></th>
              </tr>
            </thead>
            <tbody >
              <?php  $i=1; foreach ($empdata['emp_list'] as $row){ ?>
                <tr >
                  <td class="p-2"><?php echo $i; ?></td>
                  <td class="p-2">
                    <div class="align-middle">
                      <?php if($row['image']!="") {?>
                        <img src="uploads/all/<?php echo $row['image']; ?>"   class="img-radius img-50 align-top m-r-15" alt="<?= htmlspecialchars(__('Attachment Preview'), ENT_QUOTES, 'UTF-8'); ?>">
                      <?php } else { ?>
                        <span  alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
                      <?php } ?>
                      <div class="d-inline-block width73">
                        <a href="#!" class="text-capitalize"><h6>   <?php echo $row['display_name']; ?></h6></a>
                        <p class="text-muted m-b-0"><?php echo $row['emp_code']; ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="p-2"><p><?php echo $emp_loc[$row['location_id']]; ?></p></td>
                  <td class="p-2">
                    <p>
                    <?php if(isset($aemp[$row['id']])){ ?>
                      <span class="f-14 f-w-600  text-success"><?= __('Yes'); ?></span>
                    <?php }else{ ?>
                      <span class="f-14 f-w-600 text-danger"><?= __('No'); ?></span>
                    <?php }  ?>
                    </p>
                  </td>
                  <td class="p-2">
                    <p>
                      <?php if(isset($aemp[$row['id']])){ ?>
                      <span class="f-14 f-w-600  text-success"><?php echo fromDate($aemp[$row['send_date']]); ?></span>
                    <?php } ?>
                    </p>
                  </td>
                  <td class="p-2">
                    <p>
                    <?php if(isset($aemp[$row['id']]) && $aemp[$row['id']]['read_type']=='Y'){ ?>
                      <span class="f-14 f-w-600  text-success"><?= __('Yes'); ?></span>
                    <?php }else{ ?>
                      <span class="f-14 f-w-600 text-danger"><?= __('No'); ?></span>
                    <?php }  ?>
                    </p>
                  </td>
                  <td class="p-2">
                    <p>
                      <?php if((isset($aemp[$row['id']]) && $aemp[$row['id']]['read_type']=='Y')){ ?>
                      <span class="f-14 f-w-600  text-success"><?php echo fromDate($aemp[$row['send_date']]); ?></span>
                    <?php } ?>
                    </p>
                  </td>

                </tr>
              <?php $i++;
} ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
