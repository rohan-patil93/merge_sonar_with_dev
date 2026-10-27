<?php
$_SESSION['apply_js']['multiselest'] = 1;
$table_name='tax_slads';
if(isset($_POST['submit']) && ($_POST['submit'] == 'Add')){
    unset($_POST['submit']);
    if(isset($data))
    {
        $_POST['current_status'] = $data['current_status']!="" ? $data['current_status'] : '0';
    }
    else
    {
        $_POST['current_status']=0;
    }
    $_POST['created_date'] = nowDate();
    if(isset($_SESSION))
    {
        $_POST['created_by'] = $_SESSION['userid'];
    }
    else
    {
        $_POST['created_by'] = '1';
    }

    $url = API."?mode=inserttaxslab&table=".$table_name."&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    header("location:index.php?option=COM_LIST_TAX_SLABS");
}
if(isset($_POST['submit']) && ($_POST['submit'] == 'Update')){
    unset($_POST['submit']);
    $_SESSION['check'] = 1;
    $_POST['updated_date'] = nowDate();
    $_POST['updated_by'] = $_SESSION['userid'];
    $url = API."?mode=inserttaxslab&table=".$table_name."&method=post";
    $response = file_post_contents($url, $_POST, 'update');
    header("location:index.php?option=COM_LIST_TAX_SLABS");

}
if(isset($_GET['ASS'])&&isset($_GET['SNAME'])){
    $table_name='tax_slads';
    $data1["where"] = array('company_id'=>$_SESSION['company_id'],'assessment_year'=> $_GET['ASS'],'tax_slab_name'=> $_GET['SNAME'],'is_active'=>'0');
    $data = urlencode(json_encode($data1));
    $curl = API."?mode=gettabledetailswithconditions&table=".$table_name."&data=".$data;
    $cudata = callAPI($curl);
    $data = json_decode($cudata,1);
    $sdata = $data['0'];

}
    if(count((array)$data)==0){

        $data = array('0'=>array('slad_from'=>0));
    }
?>
<script>
function addslabRow()
{
            var count = $(".itemRow").length;
            count++;
            $('#tax_list').val(count);
            var htmlRows = '';
            htmlRows+='<div class="col-lg-12 row p-2 m-l-5 itemRow" class="slabrates" id="slabrates_'+count+'">';
            htmlRows += '<div class="col-lg-3  p-1" ><input aria-label="Slab From[]" type="text" class="form-control" name="slab_from[]" value="" id="slab_from_'+count+'"></div><div class="col-lg-4 p-1"><input aria-label="Slab To[]" type="text" class="form-control" name="slab_to[]" value="" id="slab_to_'+count+'"></div><div class="col-lg-4 p-1"><input aria-label="Rate[]" type="text" class="form-control" name="rate[]" value="" id="rate_'+count+'"></div><div class="col-lg-1 p-1"><button type="button" style="padding: 7px !important" name="remove" id="'+count+'" class="btn btn-danger btn_remove" onclick="removeList('+count+')"><i class="ti-trash"></i></button></div>';
            htmlRows+='</div>';
            $('#slablist').append(htmlRows);
}

function removeList(id)
{
    $('#slabrates_'+id).remove();
}

</script>

<div class="row">
    <div class="col-lg-12">
        <div class="card 1 ">
            <div class="card-header table-card-header">
                <h5>Tax Slabs</h5><button type="button" class="btn p-0 border-0 bg-transparent ti-info-alt f-20 text-primary" onclick="" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Go Tour" aria-label="Go Tour" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                <div class="card-header-right table-card-header ">

                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>

                </div>
            </div>
            <!--begin::Portlet-->
            <div class="card-infisuite">
                <div class="card-body p-4 card-sticky" id="kt_page_sticky_card">

                    <div class="form-group row ml-20 mr-20">
                        <form class="slab_form" id="slab_form" method="POST" action="" name="slab_form">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <span>Assessment Year</span>
                                    <?php $fass=2019; $sass = date('Y')+1; ?>
                                    <select aria-label="Assessment Year" class="form-select js-example-basic-single" id="assessment_year" name="assessment_year">
                                        <option value="0"> Select Any One </option>
                                        <?php for ($i=$fass; $i <= $sass; $i++) {
                                            $asp = $i."-".$i+1;
                                        ?>
                                            <option <?php if($asp == $sdata['assessment_year']){ echo 'selected';
} ?>  value="<?php echo $asp; ?>"><?php echo $asp; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group col-lg-6">
                                    <span>Tax Slab Name</span>
                                    <input aria-label="Input field" class="form-control" type="text" value="<?php echo $sdata['tax_slab_name'] ?>" name="tax_slab_name" id="tax_slab_name" <?php if(isset($_GET['Rid'])) { echo 'disabled';
} ?>>
                                </div>

                            </div>

                            <div class="col-lg-12 row p-2 m-l-5" class="slablist" id="slablist">

                                <div class="col-lg-3 p-1"> <span> Slab From </span> </div>
                                <div class="col-lg-4 p-1"> <span> Slab To </span> </div>
                                <div class="col-lg-4 p-1"> <span> Rate </span></div>
                                <div class="col-lg-1 p-1"></div>
                                <?php $k=1; foreach($data as $tdata){ ?>
                                <div class="col-lg-12 row p-2 slabrates m-l-5 itemRow" id="slabrates_<?php echo $k; ?>">
                                    <div class="col-lg-3  p-1"><input aria-label="Slab From[]" type="text" class="form-control slab_from" name="slab_from[]" value="<?php echo $tdata['slad_from'] ?>" id="slab_from">
                                    </div>
                                    <div class="col-lg-4 p-1"><input aria-label="Slab To[]" type="text" class="form-control" name="slab_to[]" value="<?php echo $tdata['slad_to'] ?>" id="slab_to">
                                    </div>
                                    <div class="col-lg-4 p-1"><input aria-label="Rate[]" type="text" class="form-control" name="rate[]" value="<?php echo $tdata['rate'] ?>" id="rate">
                                    </div>
                                    <div class="col-lg-1 p-1">
                                    <button type="button" name="remove" id="" style="padding: 7px !important" class="btn btn-danger btn_remove" onclick="removeList(<?php echo $k; ?>)">
                                        <i class="ti-trash"></i>
                                    </button>
                                    </div>
                                </div>
                                <?php $k++;
} ?>
                            </div>
                            <input type="hidden" id="tax_list"  value="<?php echo  $count;?>"/>
                            <div class="col-lg-12 p-1">
                                <div class="col-lg-2">
                                    <button type="button" onclick="if($('#tax_list').val()==0){ $('#slablist').show();$('#tax_list').val(1); }else { addslabRow(); }" id="add" class="btn btn-success">+ ADD</button>
                                </div>
                            </div>
                            <?php if($_GET['VIEW']!=1){ ?>
                            <div class="card-footer p-0">
                                <div class="row">
                                    <?php if(isset($_GET['ASS']) && $_GET['COPY']!=1 ) { ?>
                                        <div class="col-lg-9 mt-5">
                                            <input type="hidden" name="submit" value="Update">
                                            <input type="hidden" name="tax_slab_name_old" value="<?php echo $_GET['SNAME'] ?>">
                                            <input type="hidden" name="assessment_year_old" value="<?php echo $_GET['ASS'] ?>">
                                            <button id="taxform_update" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value_exit" value="exit"><i class="la la-check"></i>Update</button>
                                        </div>
                                        <?php } else { ?><div class="col-lg-9 mt-5">

                                            <input type="hidden" name="submit" value="Add">
                                            <button id="taxform_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value_exit" value="exit"><i class="la la-check"></i>Save &amp; Exit</button>
                                        </div> <?php } ?>


                                </div>
                            </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
