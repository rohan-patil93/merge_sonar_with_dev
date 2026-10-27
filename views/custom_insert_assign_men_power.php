<?php
$budget_url =  API."?mode=togetjobdetails";
$budget_n_data = callAPI($budget_url);
$budget_n_data= json_decode($budget_n_data,1);
$budget_data  = $budget_n_data['main'];
$job_data = $budget_n_data['job_data'];
$vendors = $budget_n_data['vendors'];
$locations = $budget_n_data['locations'];
$departments = $budget_n_data['departments'];
$job_tittles = $budget_n_data['job_titles'];

if(isset($_POST['submit1'])){
    $post = $_POST;
    $url =  API."?mode=toinsertempcontracts&method=post";
    $response = file_post_contents($url, $post, 'insert');
}

 ?>
 <script type="text/javascript">
     function searchvalues(){
         var year=$('#kt_select2_41').val();
         var location=$('#kt_select2_42').val();
         var department=$('#kt_select2_43').val();
         var vendor=$('#kt_select2_44').val();
         window.location.href = "index.php?option=COM_INSERT_ASSIGN_MEN_POWER&year="+year+"&location="+location+"&department="+department+"&job_title="+vendor;

     }
     function inveral123(val){
         alert('hai');
         var vals = "month,quater,yearly";
         var split_val = vals.split(",");
         for(var i=0; i<4; i++){
             if(split_val[i] == val){
                 document.getElementById(split_val[i]).style.display = "block";
             }else{
                 document.getElementById(split_val[i]).style.display = "none";
             }
         }
     }
     function totalcaluclationyear(id,val){
         var spilt_data =  id.split("_");
        var inveral = $('#inveral').val();
        counts = 2;
        var column_total = 0;
        var kt_count = $('#ykt_'+spilt_data[1]).val();
        for(var k=1; k < kt_count; k++){
            var cloumn_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+k+"_"+spilt_data[4]+"_"+spilt_data[5];
            var cl_money = $('#'+cloumn_id).val();
            if(cl_money == ''){ cl_money = 0;}
            column_total = parseFloat(column_total) + parseFloat(cl_money);
        }
        $("#ykt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[5]).html(column_total);

        var gd_total = 0;
        var gd_count = $('#ygd_count').val();
        for (var m = 1; m < gd_count; m++) {
            var grand_id = "ykt_"+m+"_"+spilt_data[2]+"_"+m+"_"+spilt_data[4]+"_"+spilt_data[5];
            var gd_money = $('#'+grand_id).html();
            if(gd_money == ''){ gd_money = 0;}
            gd_total = parseFloat(gd_total) + parseFloat(gd_money);
        }
        $("#ygd_"+spilt_data[5]).html(gd_total);
        $("#ygdv_"+spilt_data[5]).val(gd_total);
     }
     function totalcaluclationquaterly(id,val){
         var spilt_data =  id.split("_");
        var inveral = $('#inveral').val();
        counts = 5;

        var row_total = 0;
        for(i=1;i<counts;i++){
            var row_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_"+i;
            var money = $('#'+row_id).val();
            if(money == ''){ money = 0;}
            row_total = parseFloat(row_total) + parseFloat(money);

        }
        $('#'+spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_total").val(row_total);
        var column_total = 0;
        var kt_count = $('#mkt_'+spilt_data[1]).val();
        for(var k=1; k < kt_count; k++){
            var cloumn_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+k+"_"+spilt_data[4]+"_"+spilt_data[5];
            var cl_money = $('#'+cloumn_id).val();
            if(cl_money == ''){ cl_money = 0;}
            column_total = parseFloat(column_total) + parseFloat(cl_money);
        }
        $("#mkt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[5]).html(column_total);

        var kt_total = 0;
        for(var n=1; n<counts;n++){
            var cats_id = "#mkt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+n;
            console.log(cats_id);
            var k_total = $(cats_id).html();
            if(k_total == ''){ k_total = 0;}
            console.log(k_total);
            kt_total = parseFloat(kt_total) + parseFloat(k_total);
            console.log(kt_total);
        }
        $("#mcat_total_"+spilt_data[1]).html(kt_total);

        var gd_total = 0;
        var gd_count = $('#mgd_count').val();
        for (var m = 1; m < gd_count; m++) {
            var grand_id = "mkt_"+m+"_"+spilt_data[2]+"_"+m+"_"+spilt_data[4]+"_"+spilt_data[5];
            var gd_money = $('#'+grand_id).html();
            if(gd_money == ''){ gd_money = 0;}
            gd_total = parseFloat(gd_total) + parseFloat(gd_money);
        }
        $("#mgd_"+spilt_data[5]).html(gd_total);
        $("#mgdv_"+spilt_data[5]).val(gd_total);
        var total_gd = 0;
        for(var n=1; n<counts;n++){
            var gdv_total = $('#mgdv_'+n).val();
            if(gdv_total == ''){ gdv_total = 0;}
            console.log(gdv_total);
            total_gd = parseFloat(total_gd) + parseFloat(gdv_total);
            console.log(total_gd);
        }
        $("#total_mgd").html(total_gd);
        $("#total_mgdv").val(total_gdv);
     }
     function totalcaluclation(id,val){
        var spilt_data =  id.split("_");
        var inveral = $('#inveral').val();
        counts = 13;

        var row_total = 0;
        for(i=1;i<counts;i++){
            var row_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_"+i;
            var money = $('#'+row_id).val();
            if(money == ''){ money = 0;}
            row_total = parseFloat(row_total) + parseFloat(money);

        }
        $('#'+spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[3]+"_"+spilt_data[4]+"_total").val(row_total);
        var column_total = 0;
        var kt_count = $('#kt_'+spilt_data[1]).val();
        for(var k=1; k < kt_count; k++){
            var cloumn_id = spilt_data[0]+"_"+spilt_data[1]+"_"+spilt_data[2]+"_"+k+"_"+spilt_data[4]+"_"+spilt_data[5];
            var cl_money = $('#'+cloumn_id).val();
            if(cl_money == ''){ cl_money = 0;}
            column_total = parseFloat(column_total) + parseFloat(cl_money);
        }
        $("#kt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+spilt_data[5]).html(column_total);

        var kt_total = 0;
        for(var n=1; n<counts;n++){
            var cats_id = "#kt_"+spilt_data[1]+"_"+spilt_data[2]+"_"+spilt_data[1]+"_"+spilt_data[4]+"_"+n;
            console.log(cats_id);
            var k_total = $(cats_id).html();
            if(k_total == ''){ k_total = 0;}
            console.log(k_total);
            kt_total = parseFloat(kt_total) + parseFloat(k_total);
            console.log(kt_total);
        }
        $("#cat_total_"+spilt_data[1]).html(kt_total);

        var gd_total = 0;
        var gd_count = $('#gd_count').val();
        for (var m = 1; m < gd_count; m++) {
            var grand_id = "kt_"+m+"_"+spilt_data[2]+"_"+m+"_"+spilt_data[4]+"_"+spilt_data[5];
            var gd_money = $('#'+grand_id).html();
            if(gd_money == ''){ gd_money = 0;}
            gd_total = parseFloat(gd_total) + parseFloat(gd_money);
        }
        $("#gd_"+spilt_data[5]).html(gd_total);
        $("#gdv_"+spilt_data[5]).val(gd_total);
        var total_gd = 0;
        for(var n=1; n<counts;n++){
            var gdv_total = $('#gdv_'+n).val();
            if(gdv_total == ''){ gdv_total = 0;}
            console.log(gdv_total);
            total_gd = parseFloat(total_gd) + parseFloat(gdv_total);
            console.log(total_gd);
        }
        $("#total_gd").html(total_gd);
        $("#total_gdv").val(total_gdv);
     }
 </script>
 <div class="d-flex flex-column-fluid">

    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <div class="card-title">
                <span class="kt-portlet__head-icon">

                </span>
                <h3 class="card-label">&nbsp;<?php echo "Contracts";  ?> </h3>
            </div>
    <div class="kt-portlet__head-toolbar">
                <div class="card-toolbar">
                        <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            Back
                        </a>
                    &nbsp;

                                </div>
                            </div>
                        </div>
<div class="card card-custom">
    <div class="card-body">
                <form class="kt-form kt-form--label-right" name="myForm" id="myForm" action="" method="post" enctype="multipart/form-data"  >
                <div class="budget-section">

                    <div class="input-field input-field-pre">
                        <span>Year</span>
                        <select aria-label="Fiscal Year" class="form-control kt_select2" name="fiscal_year" id="kt_select2_41">
                            <?php $year = date('Y'); for($i=1; $i<10; $i++) { $to_year = $year+1; ?>
                            <option value="<?php echo $to_year; ?>"><?php echo $year; ?></option>
                            <?php $year = $year+1;
} ?>
                        </select>
                    </div>
                    <div class="input-field  col-lg-4">
                        <span>Location</span>
                        <select aria-label="Location" class="form-control kt_select2 " name="location" id="kt_select2_42">

                            <option value="0">All</option>
                            <?php foreach($locations as $location){ ?>
                            <option value="<?php echo $location['id']; ?> "><?php echo $location['location_name']; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-field  col-lg-4">
                        <span>Department</span>
                        <select aria-label="Department" class="form-control kt_select2 " name="department" id="kt_select2_43">

                            <option value="0">All</option>
                            <?php foreach($departments as $department){ ?>
                            <option value="<?php echo $department['id']; ?> "><?php echo $department['department_name']; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-field  col-lg-4">
                        <span>Job Title</span>
                        <select aria-label="Department" class="form-control kt_select2 " name="department" id="kt_select2_44">

                            <option value="0">All</option>
                            <?php foreach($job_tittles as $job_title){ ?>
                            <option value="<?php echo $job_title['id']; ?> "><?php echo $job_title['job_title']; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="input-field  col-lg-4">
                        <span>Vendor</span>
                        <select aria-label="Vendor" class="form-control kt_select2" name="vendor" id="kt_select2_45">

                            <option value="0">ALL</option>
                            <?php foreach($vendors as $vendor){ ?>
                            <option value="<?php echo $vendor['id']; ?> "><?php echo $vendor['first_name']; ?> </option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary mr-2" onclick="searchvalues()"> Search </button>



                    <div class="budget-table" id="month">
                        <h3>Contract Monthly</h3>
                        <table>
                            <tr style="background-color: #22b9ff;">
                                <th scope="col">Job Title</th>
                                <th scope="col">JAN</th>
                                <th scope="col">FEB</th>
                                <th scope="col">MAR</th>
                                <th scope="col">APRIL</th>
                                <th scope="col">MAY</th>
                                <th scope="col">JUN</th>
                                <th scope="col">JUL</th>
                                <th scope="col">AUG</th>
                                <th scope="col">SEP</th>
                                <th scope="col">OCT</th>
                                <th scope="col">NOV</th>
                                <th scope="col">DEC</th>
                                <th scope="col">Total</th>
                            </tr>
                            <?php $k=1; $z=1; $gp= array(); foreach ($budget_data as $budget_details) { ?>
                                <tr style="background-color: #eee; height: 40px;">
                                    <td style="padding-left: 5px;"><?php echo $budget_details['category_name'];?></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>

                                <?php $l=1 ; $dep_dis= array(); foreach ($budget_details['data'] as $budget) {
                                        $value_id= "k_".$k."_r_".$l."_c_";
                                        $name= "name_".$z."_";
                                        $bug_id = $budget['id']; $total_no=0;
                                        $no_array =array('01','02','03','04','05','06','07','08','09','10','11','12');
                                        $s=1;
                                        foreach ($no_array as $mont) {
                                            if($job_data[$mont][$bug_id]['share_no']==0 && $job_data[$mont][$bug_id]['pending_no']==0 ){
                                                    $no_dis[$s] = $job_data[$mont][$bug_id]['no_of_vacancy'];
                                            }else{
                                                $no_dis[$s] = $job_data[$mont][$bug_id]['pending_no'];

                                            }
                                            $dep_dis[$s] = $dep_dis[$s]+$no_dis[$s];
                                            $total_no = $total_no+$no_dis[$s];
                                        $s++;
}

                                 ?>
                                    <tr>
                                        <td class="padlft" style="min-width: 200px; text-align: left;"><?php echo $budget['name'];?>
                                            <input type="hidden" name="<?php echo $name.'account_code'; ?>" value="<?php echo $budget['id'];?>">
                                        </td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'1'; ?>"  onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'1'; ?>" <?php if($no_dis['1']==""){ echo "readonly";
} ?>  value="<?php echo $no_dis['1']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'2'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'2'; ?>" <?php if($no_dis['2']==""){ echo "readonly";
} ?>   value="<?php echo $no_dis['2']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'3'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'3'; ?>" <?php if($no_dis['3']==""){ echo "readonly";
} ?>   value="<?php echo $no_dis['3']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'4'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'4'; ?>" <?php if($no_dis['4']==""){ echo "readonly";
} ?>  value="<?php echo $no_dis['4']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'5'; ?>" <?php if($no_dis['5']==""){ echo "readonly";
} ?>  onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'5'; ?>"  value="<?php echo $no_dis['5']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'6'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'6'; ?>" <?php if($no_dis['6']==""){ echo "readonly";
} ?>  value="<?php echo $no_dis['6']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'7'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'7'; ?>" <?php if($no_dis['7']==""){ echo "readonly";
} ?>  value="<?php echo $no_dis['7']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'8'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'8'; ?>"  <?php if($no_dis['8']==""){ echo "readonly";
} ?> value="<?php echo $no_dis['8']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'9'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'9'; ?>" <?php if($no_dis['9']==""){ echo "readonly";
} ?> value="<?php echo $no_dis['9']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'10'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'10'; ?>" <?php if($no_dis['10']==""){ echo "readonly";
} ?> value="<?php echo $no_dis['10']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'11'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'11'; ?>"  <?php if($no_dis['11']==""){ echo "readonly";
} ?> value="<?php echo $no_dis['11']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" name="<?php echo $name.'12'; ?>" onblur="totalcaluclation(this.id,this.value)" id="<?php echo $value_id.'12'; ?>" <?php if($no_dis['12']==""){ echo "readonly";
} ?>  value="<?php echo $no_dis['12']; ?>" class="form-control text-right"></td>
                                        <td><input aria-label="Input field" type="text" readonly name="<?php echo $name.'total'; ?>" id="<?php echo $value_id.'total'; ?>"  value="<?php echo $total_no; ?>" class="form-control text-right"></td>
                                    </tr>
                            <?php $l++; $z++;
}  ?>
                            <?php $cat_id = "kt_".$k."_r_".$k."_c_";
                                  $cat_idss = "kt_".$k;
                                     ?>
                                    <tr class="totalbudget" style="background-color: #f6f7f9; height: 40px;">
                                        <td style="padding-left: 10px;"> Total</td>

                <?php $dep_total ="";
                    for($g =1 ; $g<13;$g++){
                ?>
                <td><div id="<?php echo $cat_id.$g; ?>" style="padding-right: 5px;" >
                    <?php echo $dep_dis[$g]; ?>
                    </div></td>
            <?php
             } ?>

            <td><div id="<?php echo "cat_total_".$k; ?>" style="padding-right: 5px;"><?php echo $dep_total; ?></div> </td>
                                    </tr>

                                    <input type="hidden" name="" id="<?php echo $cat_idss; ?>" value="<?php echo $l; ?>">
                            <?php $k++;
} ?>
                            <input type="hidden" name="" id="gd_count" value="<?php echo $k; ?>">
                            <tr class="totalbudget" style="background-color: #f6f7f9; height: 40px;">
                                <td style="padding-left: 10px;">Grand Total</td>
                                <?php
                                 for($g =1 ; $g<13;$g++) { ?>
                                    <td><div id="<?php echo "gd_".$g; ?>" style="padding-right: 5px;"> <?php echo $gp[$g]; ?></div></td>
                                    <input type="hidden" name="" id="<?php echo "gdv_".$g; ?>" value=" <?php echo $gp[$g]; ?>">
                                <?php } ?>
                                <td id="total_gd" style="padding-right: 5px;"><?php echo $gp['total']; ?></td>
                                <input type="hidden" name="" id="total_gdv" value="0">
                            </tr>
                        </table>
                    </div>

                </div>
<div class="card-footer">

        <div class="row">
            <div class="col-lg-4">
                 <input type="hidden" id="table_name" name="table_name" value="<?php echo $table_name; ?>" />
                 <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
                 <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                 <input type="hidden" name="old_place1" id="old_place1" value="<?php echo $location_state;?>">
                 <input type="hidden" name="count" id="count" value="<?php echo $z;?>">
                <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $copy == '1'){ ?>
                        <input type="hidden" id="hid"name="hid" value="<?php echo $rid; ?>" />
                        <input type="hidden" name="hid_itemds" value="<?php echo $item_ids; ?>" />
                        <input type="hidden" name="submit1" value="Update" />
                        <button id="kt_llogin_signup_submit" <?php echo $disable; ?> class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } else{ ?>
                        <input type="hidden" name="submit1" value="Add" />
                        <button id="kt_login_signin_submit" class="btn btn-primary mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                        <button id="kt_login_signin_submit_continue" class="btn btn-primary mr-2"   name="submit_value" value="continue"><i class="la la-check-circle"></i>Save & continue</button>
                        <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                <?php } ?>
            </div>
            <div class="col-lg-6">
            <?php if(count((array)$vdata_details) > 0){ ?>
                <div class="accordion accordion-light  accordion-toggle-arrow" id="accordionExample2">

                    <div class="card">
                        <div class="card-header" id="headingTwo2">
                            <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo2" aria-expanded="false" aria-controls="collapseTwo2">
                                Transaction Timeline
                            </div>
                        </div>
                        <div id="collapseTwo2" class="collapse" aria-labelledby="headingTwo2" data-parent="#accordionExample2">
                            <div class="card-body">
                                <div class="kt-timeline-v2">
                                    <div class="kt-timeline-v2__items  kt-padding-top-25 kt-padding-bottom-30">
                                        <?php foreach ($vdata_details as  $vdvalue) { //print_r($vdvalue); ?>
                                        <div class="kt-timeline-v2__item">

                                            <div class="kt-timeline-v2__item-cricle">
                                                <?php if($vdvalue['transaction_type'] == 'INSERT'){ ?>
                                                <i class="fa fa-genderless kt-font-success"></i>
                                                <?php } ?>
                                                <?php if($vdvalue['transaction_type'] == 'UPDATE'){ ?>
                                                <i class="fa fa-genderless kt-font-warning"></i>
                                                <?php } ?>
                                            </div>
                                            <div class="kt-timeline-v2__item-text  kt-padding-top-5">
                                                <b> <?php echo date("M d, Y g:i A", strtotime($vdvalue['action_date'])); ?></b>
                                                <?php echo " ".$vdvalue['action_log']. " ".$_SESSION['users'][$vdvalue['user_id']]." with Status ".$_SESSION['status'][$vdvalue['status_value']]['name']; ?>

                                            </div>
                                        </div>
                                        <?php  } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            </div>
            <div class="col-lg-2 kt-align-right">
                <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
            </div>
        </div>
    </div>
        </div>
        </div>
    </div>
</div>
</form>
</div>
