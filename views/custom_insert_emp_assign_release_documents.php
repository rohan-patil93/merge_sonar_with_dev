<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['document'] = array();
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
if($_REQUEST['assessment_year']){
  $vdata1["where"] = array('is_active'=> '0', 'assessment_year'=>$_REQUEST['assessment_year'], 'document_list_id'=> $_REQUEST['list']);
  if(isset($_GET['month']) && $_GET['month']!="0"){
      $vdata1["where"]["document_month"] = $_GET['month'];
  }
  $vdata1["field"] = "id, emp_id";
  $vdata = urlencode(json_encode($vdata1));
  $vurl = API."?mode=gettabledetailswithconditions&table=emp_assign_release_documents&data=".$vdata;
  $configesponse = callAPI($vurl);
  $data = json_decode($configesponse,1);
  foreach($data as $eemp){
      $aemp[] = $eemp['emp_id'];
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
  function assignemp(empid,alltype,chck=""){


    var type = $("#type").val();
    var assement = $("#assement").val();
    console.log("type:"+type+",assement:"+assement);

    if(type!=0 && assement!=0){
      $('#assign_employee').show();
      $.ajax({
        url: "ajax.php",
        data:'page=addsessiondocumenttypeyear&emp_id='+empid+'&list_id='+type+'&assement_year='+assement,
        type: "POST",
        success:function(data){
          console.log(data);
        },
        error:function (){}
      });
    }else{
      assementdetail();
    }


  }
  function assementdetail(){

    var type = $("#type").val();
    var assement = $("#assement").val();
    var pay_type = $("#pay_type").val();
    console.log("type:"+type+",assement:"+assement);
    $("#assement_err").hide();
    $("#type_err").hide();
    if(type!=0 && assement!=0){
      if(type == pay_type ){

        $("#month_label").show();
        $("#month_drop").show();
        var month = $("#month").val();
        if(month!=0){
            window.location.href = 'index.php?option=COM_INSERT_EMP_ASSIGN_RELEASE_DOCUMENTS&assessment_year='+assement+'&list='+type+'&month='+month;
        }else{
           $("#month_err").show();
        }

      }else{
        window.location.href = 'index.php?option=COM_INSERT_EMP_ASSIGN_RELEASE_DOCUMENTS&assessment_year='+assement+'&list='+type;
      }
    }
    if(assement==0){
      $("#assement_err").show();
      $('.checkbox1').each(function(){
        this.checked = false;
      });
    }
    if(type==0){
      $("#type_err").show();
      $('.checkbox1').each(function(){
        this.checked = false;
      });
    }
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
  function submitpopup(){
    var type = $("#type").val();
    var assement = $("#assement").val();
    var pay_type = $("#pay_type").val();
    var mm = "";
    if(type == pay_type){
       var month = $("#month").val();
       mm ="&month="+month;
    }
    jQuery.ajax({
        url: "views/custom_document_employee_popup.php",
        data:'page=addsessiondocumenttypeyear&list_id='+type+'&assement_year='+assement+'&work=work'+mm,
        type: "POST",
        success:function(data){
          $('#popup1').html(data);
          $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
  }
</script>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <div class="card">
      <div class="card-header">
        <h5> Assign Employees</h5>
        <div class="card-header-right">
          <button type="button" class="btn btn-primary" onclick="showfilter()">
            Filter
          </button>

          <input type="hidden" name="filterval" id="filterval" value="0">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>  Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <form class="kt-form kt-form--label-right" action="" method="post">
        <div class="row col-lg-12" style="display:none;" id="showfilter">
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
              <span><?php echo "Cost Center"; ?>:</span>
              <select aria-label="Cost Center Pp" class=" form-select js-example-basic-single" id="cost_center_pp" name="cost_center_pp">
                <option value=""> All  </option>
                <?php foreach($empcos as $cos){ $emp_cos[$cos['id']]= $cos['cost_center_name']; ?>
                <option   <?php if($ress['cost_center_pp'] == $cos['id'] ){ ?> selected <?php
} ?> value="<?php echo $cos['id']; ?>"><?php echo $cos['cost_center_name']; ?></option>
              <?php } ?>
               </select>
          </div>
          <div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Vendors"; ?>:</span>
            <select aria-label="Vendor" class=" form-select js-example-basic-single" id="vendor" name="vendor">
              <option value=""> All  </option>
              <?php foreach($empven as $ven){ $emp_ven[$ven['id']]= $ven['display_name']; ?>
              <option   <?php if($ress['vendor'] == $ven['id'] ){ ?> selected <?php
} ?> value="<?php echo $ven['id']; ?>"><?php echo $ven['display_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Department"; ?>:</span>
            <select aria-label="Department" class=" form-select js-example-basic-single" id="department" name="department">
              <option value=""> All  </option>
              <?php foreach($empdep as $dep){ $emp_dep[$dep['id']]= $dep['department_name'];  ?>
              <option  <?php if($ress['department'] == $dep['id'] ){ ?> selected <?php
} ?> value="<?php echo $dep['id']; ?>"><?php echo $dep['department_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile">
            <span><?php echo "Designation"; ?>:</span>
            <select aria-label="Designation" class=" form-select js-example-basic-single" id="designation" name="designation">
              <option value=""> All  </option>
              <?php foreach($empdes as $des){  $emp_des[$des['id']]= $des['designation_name'];  ?>
              <option  <?php if($ress['designation'] == $des['id'] ){ ?> selected <?php
} ?> value="<?php echo $des['id']; ?>"><?php echo $des['designation_name']; ?></option>
            <?php } ?>
            </select>
          </div>
          <div class="col-lg-3  form-group kt-margin-b-10-tablet-and-mobile mt-2">
            <span><?php echo "Location"; ?>:</span>
            <select aria-label="Location" class=" form-select js-example-basic-single" id="location" name="location">
              <option value=""> All  </option>
              <?php foreach($emploc as $loc){ $emp_loc[$loc['id']]= $loc['location_name']; ?>
              <option   <?php if($ress['location'] == $loc['id'] ){ ?> selected <?php
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-lg-3 form-group kt-margin-b-10-tablet-and-mobile mt-2">
            <span>Enter Employee Name/Code</span>
            <input aria-label="Emp Name Code" type="text" class="form-control" name="emp_name_code"  value="<?php echo $_POST['emp_name_code']; ?>" id="emp_name_code">
          </div>
          <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span>&nbsp;</span>
            <input type="hidden" name="assessment_year" value="<?php echo $_REQUEST['assessment_year'] ?>">
            <input type="hidden" name="submit" value="search" />
            <div>
              <button class="btn btn-primary btn-brand--icon" id="kt_search" >
                <span><span>Search</span></span>
              </button>
            </div>
          </div>
        </div>
        </form>
        <div class="separator separator-solid mb-3"></div>
        <div class="row col-lg-12">
          <div class="col-lg-1"> <span>Assement Year:&nbsp;</span> </div>
          <div class="col-lg-2">
            <select aria-label="Assement" class="js-example-basic-single" name="assement" id="assement" onchange="assementdetail()">
              <option value="0">Select Year</option>
              <?php $asyear = date('Y'); $asyear = $asyear-3;
                  for($i=1;$i<6;$i++){
                      $vaass = $asyear."-".($asyear+1);
                      $asyear = $asyear+1;
              ?>
                      <option <?php if($_REQUEST['assessment_year']==$vaass){ echo "selected";
} ?> value="<?php echo $vaass; ?>"><?php echo $vaass; ?></option>
              <?php } ?>
            </select>
            <input type="hidden" name="pay_type" id="pay_type" value="<?php echo DOCUMENT_PAYSLIP_RELEASE; ?>">
            <div class="f-w-600 text-danger" id="assement_err" style="display:none;"> Please Choose Any One  </div>
          </div>
          <div class="col-lg-1"> <span>Type:&nbsp;</span> </div>
          <div class="col-lg-2">
            <select aria-label="Type" class="js-example-basic-single" name="type" id="type" onchange="assementdetail()">
              <option value="0">Select Year</option>
              <?php foreach($typedata as $type){ ?>
                  <option <?php if($_REQUEST['list']==$type['id']){ echo "selected";
} ?> value="<?php echo $type['id']; ?>"><?php echo $type['document_name']; ?></option>
              <?php } ?>
            </select>
            <div class="f-w-600 text-danger" id="type_err" style="display:none;"> Please Choose Any One  </div>
          </div>
          <div class="col-lg-1" id="month_label" <?php if(!isset($_GET['month'])){ ?> style="display:none" <?php
} ?> > <span>Month:&nbsp;</span> </div>
          <div class="col-lg-2" id="month_drop"  <?php if(!isset($_GET['month'])){ ?> style="display:none" <?php
} ?> >
            <select aria-label="Month" class="js-example-basic-single" name="month" id="month" onchange="assementdetail()">
              <option value="0">Select Year</option>
              <?php for($k=4; $k<16; $k++){
                    $m = $k;
                    if($m>12){$m=$m-12;
}
              ?>
                  <option <?php if($_REQUEST['month']==$m){ echo "selected";
} ?> value="<?php echo $m; ?>"><?php echo date("F", mktime(0, 0, 0, $m, 1));  ?></option>
              <?php } ?>
            </select>
            <div class="f-w-600 text-danger" id="month_err" style="display:none;"> Please Choose Any One  </div>
          </div>
          <div class="col-lg-1"></div>
          <div class="col-lg-2 text-right">
            <button type="button" onclick="submitpopup()" class="btn btn-success" style="display:none;" id="assign_employee" >Documents Release </button>
          </div>
        </div>
        <div class="dt-responsive table-responsive">
          <table class="table table-hover custom_asign_emp_mpc" id="kt_datatable1" >
            <thead >
              <tr>
                <th scope="col" style="width: 2%;">

                </th>
                <th scope="col" style="width: 2%;">#</th>
                <th scope="col" style="width: 15%;">Name </th>
                <th scope="col" style="width: 15%;">Cost Center</th>
                <th scope="col" style="width: 10%;">Location</th>
                <th scope="col" style="width: 10%;">Department</th>
                <th scope="col" style="width: 10%;">Designation</th>
              </tr>
            </thead>
            <tbody >
              <?php  $i=1; foreach ($empdata['emp_list'] as $row){ ?>
                <tr >
                  <td class="p-2">
                    <div class="checkbox-fade fade-in-primary">
                      <label class="form-label">
                        <input aria-label="Checkcolor"  type="checkbox" name="checkcolor" <?php if(in_array($row['id'],(array)$aemp)){ echo "checked";
} ?> class="checkbox1" onclick="assignemp(<?php echo $row['id']; ?>, 'single')" id="check_<?php echo $row['id']; ?>" onkeydown="" />

                        <span class="cr">
                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                        </span>
                        <span></span>
                      </label>
                    </div>

                  </td>
                  <td class="p-2"><?php echo $i; ?></td>
                  <td class="p-2">
                    <div class="align-middle">
                      <?php if($row['image']!="") {?>
                        <img src="uploads/all/<?php echo $row['image']; ?>"   class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                      <?php } else { ?>
                        <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($row['display_name']); ?></span>
                      <?php } ?>
                      <div class="d-inline-block width73">
                        <a href="#!" class="text-capitalize"><h6>   <?php echo $row['display_name']; ?></h6></a>
                        <p class="text-muted m-b-0"><?php echo $row['emp_code']; ?></p>
                      </div>
                    </div>
                  </td>
                  <td class="p-2"><p><?php echo $emp_cos[$row['cost_center_id']]; ?></p></td>
                  <td class="p-2"><p><?php echo $emp_loc[$row['location_id']]; ?></p></td>
                  <td class="p-2"><p><?php echo $emp_dep[$row['department_id']]; ?></p></td>
                  <td class="p-2"><p><?php echo $emp_des[$row['designation_id']]; ?></p></td>
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
