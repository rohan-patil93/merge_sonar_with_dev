<?php
$_SESSION['apply_js']['SORTABLE']=1;
$_SESSION['apply_js']['multiselest']=1;
if(isset($_POST['submit'])){
    $data['page_title'] = $_POST['page_title'];
    $data['company_name'] = $_POST['company_name'];
    $data['company_address'] = $_POST['company_address'];
    $data['joining_bonus_applicable'] = isset($_POST['joining_bonus_applicable']) ? 0 :1;
    $data[' variable_applicable'] = isset($_POST['  variable_applicable']) ? 0 :1;
   $url = API."?mode=updaterowtabledata&rid=1&table=salary_stracture&method=post";
   $response = file_post_contents($url, $data, 'update');
}
$url = API."?mode=togetemloyeesalarydetails&rid=";
$response = callAPI($url);
$resdata = json_decode($response,1);
$earnings = $resdata['eraning'];
$res = $resdata['salary_stracture'];
$employees = $resdata['employees'];
$noshow = array('NETPAYABLE', 'CTC');
foreach((array)$earnings as $ern){
    if(in_array($ern['code'],$noshow)){
        $lsv[$ern['code']] = $ern;
    }
}

?>
<script type="text/javascript">
    function addpayshilpdetails(){
        var count = $(".pd_row").length;
        var htmlRows = "";
        count = count+1;
        htmlRows+='<div class="row align-items-end pd_row" id="pd_'+count+'">';
        htmlRows+='<div class="col-lg-5"> <div class="form-group"><span class="form-label f-w-600 f-14">Title</span><input aria-label="Input field" type="text" class="form-control"></div> </div>';
        htmlRows+='<div class="col-lg-5"><div class="form-group"><span class="form-label f-w-600 f-14">Value</span><input aria-label="Input field" type="text" class="form-control" placeholder=""> </div></div>';
        htmlRows+='<div class="col-lg-2 text-end mb-3"><button type="button" onclick="deleterow(\'pd_' + count + '\',\'paydetails\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent"><i class="fa fa-trash"></i> </button><button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>  </div>';
        htmlRows+='</div>';
        console.log(htmlRows);
        $('.draggablepaylList').append(htmlRows);
    }
    function addemloyedetails(){
        var htmlRows = "";
        var count = $(".emp_row").length;
         count = count+1;
        htmlRows+='<div class="row align-items-end emp_row" id="emp_'+count+'">';
        htmlRows+='<div class="col-lg-5"> <div class="form-group"><span class="form-label f-w-600 f-14">Title</span><input aria-label="Input field" type="text" class="form-control"></div> </div>';
        htmlRows+='<div class="col-lg-5"><div class="form-group"><span class="form-label f-w-600 f-14">Value</span><input aria-label="Input field" type="text" class="form-control" placeholder=""> </div></div>';
        htmlRows+='<div class="col-lg-2 text-end mb-3"> <button type="button" onclick="deleterow(\'emp_' + count + '\',\'paydetails\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent"> <i class="fa fa-trash"></i> </button> <button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>  </div>';
        htmlRows+='</div>';
        $('.draggableemplList').append(htmlRows);
    }
    function addearningdetails(type){
        var htmlRows = "";
        if(type=='deduction'){
            var count = $(".dd_row").length;
            count = count+1;
            var ids = 'dd_'+count;
        }else{
            var count = $(".er_row").length;
            count = count+1;
            var ids = 'er_'+count;
        }

        htmlRows+='<div class="row align-items-end" id="'+ids+'">';
        htmlRows+=' <div class="col-lg-5"><div class="form-group"> <input aria-label="Er Name" type="text" class="form-control" id="er_name_'+count+'"></div> </div>';
        htmlRows+='<div class="col-lg-5"><div class="form-group"><input aria-label="Er Code" type="text" readonly class="form-control" id="er_code_'+count+'"> </div></div>';
        htmlRows+='<div class="col-lg-2 text-end mb-3"> <button type="button" onclick="deleterow(\'' + ids + '\',\'paydetails\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent"> <i class="fa fa-trash"></i><button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button></div>';
        htmlRows+='</div>';
        if(type=='deduction'){
            $('.deductionclass').append(htmlRows);
            earningdata('','dd',count);
        }else{
            $('.eraningclass').append(htmlRows);
             earningdata('','er',count);
        }

    }

    function deleterow(divId,type){
        console.log("ID:"+divId+"/ Type"+type);
        $("#" + divId).remove();
    }
    function earningdata(id="",type, row){
        console.log("value id :"+id);
        jQuery.ajax({
            url: "views/custom_insert_earnings_popup.php",
            data:'Rid='+id+'&ptype='+type+'&prow='+row,
            type: "GET",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
            },
            error:function (){}
        });
    }
</script>
<form class="kt-form"  method="post" action=""  name="myForm" enctype="multipart/form-data">
<div class="card mt-4">
    <div class="card-header">
        <h5>Salary Stracture</h5>
    </div>

</div>
<div class="card">
    <div class="card-body">
        <h6 class="f-w-500 m-l-5 bg-gray">Employee Details</h6>
        <div class="card-block">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Employee Details</h4>
                            <div  id="draggablePanelList" class="draggableemplList">
                                <div class="row align-items-end emp_row"  id="emp_2">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Employee Name</button>
                                            <select aria-label="Select option" class="form-select js-example-basic-single">
                                                <option value="0">Select Any One</option>
                                                <?php foreach($employees as $emp){ ?>
                                                    <option value="<?php echo $emp['id']; ?>"><?php echo $emp['display_name']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Effective Date</span>
                                            <input aria-label="Emp Code" type="date" class="form-control" name="emp_code" id="emp_code" value="">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Description</span>
                                            <textarea aria-label="Descrption" class="form-control" name="descrption"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Notes</span>
                                            <textarea aria-label="Note" class="form-control" name="note"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Pay Details</h4>
                            <div  id="draggablePanelList_2" class="draggableemplList">

                                <div class="row align-items-end emp_row" id="emp_2_2">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14"> Joining Bonus Applicable</span>
                                            <div>
                                             <input aria-label="Joining Bonus Applicable" type="checkbox" name="joining_bonus_applicable"  class="js-switch" value="0" <?php if($res['  joining_bonus_applicable']== 0 ){ ?> checked <?php
} ?>  />
                                             </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Variable Pay With CTC</span>
                                            <div>
                                             <input aria-label="Joining Bonus Applicable" type="checkbox" name="joining_bonus_applicable"  class="js-switch" value="0" <?php if($res['  joining_bonus_applicable']== 0 ){ ?> checked <?php
} ?>  />
                                             </div>
                                        </div>
                                    </div>
                                     <div class="col-lg-6" id="joining_bouns_text" style="display: none;">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Joining Bonus</span>

                                             <input aria-label="Joining Bonus Amount" type="text" name="joining_bonus_amount" id="joining_bouns" value=""  class="form-control" />

                                        </div>
                                    </div>
                                     <div class="col-lg-6" style="display: none;">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14" >Variable Pay</span>

                                             <input aria-label="Variable Yearly" type="text" name="variable_yearly" value="" id="variable_yearly"  class="form-control" />

                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14"> CTC</span>
                                            <input aria-label="007" type="text" class="form-control" placeholder="007">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
       <?php include_once 'custom_view_salary_ajax.php'; ?>

        <div class="col-lg-12 text-center">

            <input type="hidden" name="submit" value="Add">
            <button   class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="continue"><i class="la la-check-circle"></i>Save</button>
        </div>
    </div>
</div>
</form>
