<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$crurl = API."?mode=gettabledetailswithconditions&table=currencies";
$cr_data = callAPI($crurl);
$currency = json_decode($cr_data,1);

$crdata["field"] = "id,country_name,short_name";
$crdata = urlencode(json_encode($crdata));
$crurl = API."?mode=gettabledetailswithconditions&table=countries&data=".$crdata;
$cr_data = callAPI($crurl);
$countries = json_decode($cr_data,1);
$ppeermm = array();
if($_POST['rowid']!=""){
    $crdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','vrc_item_id'=>$_POST['rowid']);
    $crdata = urlencode(json_encode($crdata1));
    $crurl = API."?mode=gettabledetailswithconditions&table=vrc_per_diem&data=".$crdata;
    $cr_data = callAPI($crurl);
    $ppeermm = json_decode($cr_data,1);

}
if(count((array)$ppeermm)==0){
    $ppeermm = array('travel_expenses'=>"0.00");
}
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script type="text/javascript">
    function submitrom(){
         var  str = '';
         var arr = [];
         alert("hai");

             var count = $(".sfitemRow").length;
             str = 'rowid='+$('#rowid').val()+'&count='+count;
             for (i = 1; i < count+1 ; i++) {
                 str += '&country_'+i+'='+$('#country_'+i).val()+'&currenc_'+i+'='+$('#currenc_'+i).val()+'&pre_diem_'+i+'='+$('#pre_diem_'+i).val()+'&travel_'+i+'='+$('#travel_'+i).val();
             }


          console.log(str);

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=add_perdiem&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: "Record has been saved successfully ",
                            text: "Do you want to close ? ",
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                            confirmButtonClass: "btn btn-danger",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                            }
                        });

            }
        });
    }
    function addstatickRowsadataq(){
        var count = $(".sfitemRow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10"><div class="row align-items-center sfitemRow" id="static_div_'+count+'" style="margin-top: 10px;">'
        htmlRows += '<div class="col-lg-3"><select aria-label="Country" class="form-control form-select js-example-basic-single" name="" id="country__2"+count+'"><option value=""> Select Any One</option><?php foreach($countries as $country){ ?><option value="<?php echo $country[id]; ?>"> <?php echo $country[short_name]; ?></option><?php
} ?></select></div>';
        htmlRows += '<div class="col-lg-3"><select aria-label="Currenc" class="form-control form-select js-example-basic-single" name="" id="currenc_'+count+'"><option value=""> Select Any One</option><?php foreach($currency as $currenc){ ?><option value="<?php echo $currenc[id]; ?>"> <?php echo $currenc[currency_name]." - ".$currenc[currency_short_code]; ?></option><?php
} ?></select></div>';
        htmlRows += '<div class="col-lg-2"><input aria-label="Type" type="text" class="form-control"  id="pre_diem_'+count+'"  value=""  name="type"> </div>';
        htmlRows += '<div class="col-lg-2"><input aria-label="Type" type="text"class="form-control"  id="travel_'+count+'"  value=""  name="type"></div>';
        htmlRows += '<div class="col-lg-2"><input type="hidden" value="0" id="static_delete_value_'+count+'" ><button class="btn btn-warning" id="'+count+'" type="button" onclick="deleteRows(this.id)">Delete</button></div>';
        console.log("ravi"+htmlRows);
        $('#append_statick').append(htmlRows);
    }
    function deleteRows(val){
        $("#static_div_"+val).css("display", "none");
        $("#static_delete_value_"+val).val('1');
    }
</script>
<div class="modal-header card-header">

    <h5>Per-Diem (Food, Stay, Local C    ommute)</h5>

    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

</div>

<div class="modal-body" style="overflow-y: scroll;" >
    <form class="kt-form kt-form--label-right j-pro" action="" method="post">
        <div class="kt-scroll ps ps--active-x ps--active-y" data-scroll="true" data-scroll-y="true" style="max-height: 400px;">

                <div id='append_statick'>
                    <div class="row align-items-center" style="margin-top: 10px;">
                        <div class="col-lg-3">
                            <span><b>Country</b></span>
                        </div>
                        <div class="col-lg-3">
                            <span><b>Currency</b></span>
                        </div>
                        <div class="col-lg-2">
                            <span><b>Per-Diem</b></span>
                        </div>
                        <div class="col-lg-2">
                            <span><b>Travel Expenses</b></span>
                        </div>
                        <div class="col-lg-2">
                            <span><b></b></span>
                        </div>
                    </div>
                    <?php $i=1; foreach($ppeermm as $ppee){ ?>
                    <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                        <div class="row align-items-center sfitemRow" id="static_div_<?php echo $i; ?>" style="margin-top: 10px;">
                            <div class="col-lg-3">
                                <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="" id="country_<?php echo $i; ?>">
                                    <option value=""> Select Any One</option>
                                    <?php foreach($countries as $country){ ?>
                                        <option <?php if($country['id'] == $ppee['country_id'] ){ echo "selected";
} ?> value="<?php echo $country['id']; ?>"> <?php echo $country['country_name']."-".$country['short_name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="" id="currenc_<?php echo $i; ?>">
                                    <option value=""> Select Any One</option>
                                    <?php foreach($currency as $currenc){ ?>
                                        <option <?php if($currenc['id'] == $ppee['currency_id'] ){ echo "selected";
} ?>  value="<?php echo $currenc['id']; ?>"> <?php echo $currenc['currency_name']." - ".$currenc['currency_short_code']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-lg-2">
                                <input aria-label="Input field" type="text" class="form-control"  id="pre_diem_<?php echo $i; ?>"  value="<?php echo $ppee['per_diem'];?>"  name="type">
                            </div>
                            <div class="col-lg-2">
                                <input aria-label="Input field" type="text"class="form-control"  id="travel_<?php echo $i; ?>"  value="<?php echo $ppee['travel_expenses'];?>"   name="type">
                            </div>
                            <div class="col-lg-2">
                                <input type="hidden" value="0" id="static_delete_value_<?php echo $i; ?>" >
                                <button class="btn btn-warning" id="<?php echo $i; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
                            </div>
                        </div>
                    </div>
                <?php $i++;
} ?>
                </div>
                <div class="row align-items-center m-l-5 mt-4  col-lg-2" >
                    <button class="btn btn-success" id="addstatickRows" type="button" onclick="addstatickRowsadataq()">+ Add More</button>&nbsp;&nbsp;
                </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="table" id='table' value="<?php echo $table; ?>">
        <input type="hidden" name="rowid" id='rowid' value="<?php echo $_POST['rowid']; ?>">
        <input type="hidden" name="field" id='field' value="<?php echo $new_table; ?>">
        <input type="hidden" name="rid" id='rid' value="<?php echo $rid; ?>">
        <input type="hidden" name="tranid" id='tranid' value="<?php echo $tranid; ?>">
        <input type="hidden" name="user_id" id='user_id' value="<?php echo $_SESSION['userid']; ?>">
        <input type="hidden" name="qty" id='qty' value="<?php echo $qty; ?>">
        <input type="hidden" name="type" id='type' value="<?php echo $item_daata['tracking_type']; ?>">
        <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" >Submit</button>
    </div>

</form>
</div>
