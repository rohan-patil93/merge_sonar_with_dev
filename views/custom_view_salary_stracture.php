<?php 
$_SESSION['apply_js']['SORTABLE']=1;
$_SESSION['apply_js']['multiselest']=1; 
if(isset($_POST['submit'])){
        $data['page_title'] = $_POST['page_title'];
    $data['company_name'] = $_POST['company_name'];
    $data['company_address'] = $_POST['company_address'];
    $data['joining_bonus_applicable'] = isset($_POST['joining_bonus_applicable']) ? 0 :1;
    $data[' variable_applicable'] = isset($_POST['variable_applicable']) ? 0 :1;
   $url = API."?mode=updaterowtabledata&rid=1&table=salary_stracture&method=post";
   $response = file_post_contents($url, $data, 'update');
}
$url = API."?mode=togetnewsalarydetails";
$response = callAPI($url);
$data = json_decode($response,1);
$earnings = $data['eraning'];
$res = $data['salary_stracture'];
$fixed_code = array('GS'=> 'GS (Gross Salary)', 'BASIC'=>'BASIC (Basic)' ,'HRA'=>'HRA (House Rent Allowance)', 'DA'=>'DA (Dearness Allowance)', 'OA'=> 'OA (Other Allowance)', 'PF'=>'PF (Provident Fund)', 'PT'=>'PT (Professional Tax)','ESI'=>'ESI (Employees State Insurance Corporation)', 'EMPLOYERPF'=>'EMPLOYERPF (Employer PF Contribution)', 'EMPLOYERESI'=>'EMPLOYERESI (Employer State Insurance Corporation)', 'TDS'=> 'TDS (Tax Deducted at Source)');
    foreach($fixed_code as $keyfix=>$fixed){
        $fixedcd [] = $keyfix;
        if(!in_array($keyfix,(array)$list_code)){
            $show_fixed[$keyfix] = $fixed;
        }
    }
$noshow = array('TOTAL_DEDUCTION', 'NETPAYABLE', 'TOTAL_EARNING_OTHER', 'PAY_CTC');

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
        htmlRows+='<div class="col-lg-5"> <div class="form-group"><span class="form-label f-w-600 f-14">Title</span><input type="text" class="form-control"></div> </div>';
        htmlRows+='<div class="col-lg-5"><div class="form-group"><span class="form-label f-w-600 f-14">Value</span><input type="text" class="form-control" placeholder=""> </div></div>';
        htmlRows+='<div class="col-lg-2 text-end mb-3"><span onclick="deleterow(\'pd_' + count + '\',\'paydetails\')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i> </span><button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>  </div>';
        htmlRows+='</div>';
        console.log(htmlRows);
        $('.draggablepaylList').append(htmlRows);
    }
    function addemloyedetails(){
        var htmlRows = "";
        var count = $(".emp_row").length;
         count = count+1;
        htmlRows+='<div class="row align-items-end emp_row" id="emp_'+count+'">';
        htmlRows+='<div class="col-lg-5"> <div class="form-group"><span class="form-label f-w-600 f-14">Title</span><input type="text" class="form-control"></div> </div>';
        htmlRows+='<div class="col-lg-5"><div class="form-group"><span class="form-label f-w-600 f-14">Value</span><input type="text" class="form-control" placeholder=""> </div></div>';
        htmlRows+='<div class="col-lg-2 text-end mb-3"> <span onclick="deleterow(\'emp_' + count + '\',\'paydetails\')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-trash"></i> </span> <button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>  </div>';
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
        htmlRows+=' <div class="col-lg-5"><div class="form-group"> <input type="text" class="form-control" id="er_name_'+count+'"></div> </div>';
        htmlRows+='<div class="col-lg-5"><div class="form-group"><input type="text" readonly class="form-control" id="er_code_'+count+'"> </div></div>';
        htmlRows+='<div class="col-lg-2 text-end mb-3"> <span onclick="deleterow(\'' + ids + '\',\'paydetails\')" > <i class="fa fa-trash"></i><button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button></div>';
        htmlRows+='</div>';        
        if(type=='deduction'){
            $('.deductionclass').append(htmlRows);
            earningdata('','dd',count);
        }else{
            $('.eraningclass').append(htmlRows);
             earningdata('','er',count);
        }
        
    }
    function deleterow(divId,type,hid=""){
        console.log("ID:"+divId+"/ Type"+type);
        jQuery.ajax({
              url: "ajax.php",
              data:'page=updatedynamicfied&table=earnings&fid=is_active&rid=1&hid='+hid,
              type: "POST",
              success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-danger','animated flipInX','animated flipOutX','Row Deleted Successfully','');
                            },
            error:function (){}
        });
        $("#" + divId).remove();
    }
    function earningdata(id="",type, row){
        console.log("value id :"+id);
        var calculation_mode_type=1;
        if(type=='dd'){
            calculation_mode_type=2;
        }
        jQuery.ajax({
            url: "views/custom_insert_earnings_popup.php",
            data:'Rid='+id+'&ptype='+type+'&prow='+row+'&calculation_mode_type='+calculation_mode_type,
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
    <div class="card-body">
        <h6 class="f-w-500 m-l-5 bg-gray">Header Details</h6>
        <div class="row mt-4">
            <div class="col-lg-4">
                <div class="form-group m-l-5">
                    <span class="form-label f-w-600 f-14">Page Title</span>
                    <input type="text" class="form-control" name="page_title" value="<?php echo $res['page_title']; ?>">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <span class="form-label f-w-600 f-14">Company Name</span>
                    <input type="text" class="form-control" name="company_name" value="<?php echo $res['company_name']; ?>">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <span class="form-label f-w-600 f-14">Company Address</span>
                    <textarea class="form-control" name="company_address"><?php echo $res['company_address']; ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <h6 class="f-w-500 m-l-5 bg-gray">Payslip & Employee Details</h6>
        <div class="card-block" style="display: none;">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Payslip Details</h4>
                            <div   id="draggablePanelList" class="draggablepaylList">
                                <div class="row align-items-end pd_row" id="pd_1">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Pay Date</span>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Pay Date</span>
                                            <input type="date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                        <span onclick="deleterow('pd_1', 'paydetails')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i></span>
                                        <button type="button" class="btn p-0 m-l-10"><i class="fa fa-pause-circle"></i></button>
                                       
                                    </div>
                                </div>
                                <div class="row align-items-end pd_row" id="pd_2">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Working Days</span>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Working Days</span>
                                            <input type="date" class="form-control" placeholder="26">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                        <span onclick="deleterow('pd_2', 'paydetails')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i></span>
                                        <button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button class="btn btn-primary" type="button" onclick="addpayshilpdetails()"><i class="fa fa-plus"></i> Add More</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Employee Details</h4>
                            <div  id="draggablePanelList" class="draggableemplList">
                                <div class="row align-items-end emp_row"  id="emp_2">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Employee Name</span>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">James Bond</span>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                        <span onclick="deleterow('emp_1','empdetails')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i></span>
                                        <button type="button" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>
                                        
                                    </div>
                                </div>
                                <div class="row align-items-end emp_row" id="emp_2">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Employee ID</span>
                                            <input type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Employee ID</span>
                                            <input type="date" class="form-control" placeholder="007">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                        <span onclick="deleterow('emp_2','empdetails')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i> </span>
                                        <button type="button" class="btn p-0 m-l-10"><i class="fa fa-pause-circle"></i></button>                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <button class="btn btn-primary" type="button" onclick="addemloyedetails()" ><i class="fa fa-plus"></i> Add More</button>
                            </div>
                        </div>
                    </div>    
                </div>
            </div>
        </div>
        <div class="card-block" >
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            
                            <div   id="draggablePanelList" class="draggablepaylList">
                                <div class="row align-items-end pd_row" id="pd_1">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Joining Bonus Applicable</span>
                                            <div>
                                            <input type="checkbox" name="joining_bonus_applicable"  class="js-switch" value="0" <?php if($res['  joining_bonus_applicable']== 0 ){ ?> checked <?php } ?>  />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Variable Applicable</span>
                                            <div>
                                            <input type="checkbox" name="variable_applicable" class="js-switch" value="0"  <?php if($res['variable_applicable']== 0 ){ ?> checked <?php } ?>  />
                                                </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Lable</span>
                                           <input type="text" class="form-control" id="er_name_tt" value="<?php echo $lsv['CTC']['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <span class="form-label f-w-600 f-14">Code</span>
                                            <input type="text" placeholder="10000"  id="er_code_tt" value="<?php echo $lsv['CTC']['code']; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                         <span >&nbsp;</span>
                                        <button type="button" id="er_code_tt"  onclick="earningdata(<?php echo $lsv['CTC']['id'].",'er','tt'"; ?>)" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <h6 class="f-w-500 m-l-5 m-r-5 bg-gray">Salary Details</h6>
        <div class="card-block">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Earnings</h4>
                            <div class="row align-items-end mt-3">
                                <div class="col-lg-5">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Lable</span>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Code</span>
                                    </div>
                                </div>
                                <div class="col-lg-2 text-end mb-3">
                                    
                                </div>
                            </div>
                            <div   id="draggablePanelList" class="eraningclass">
                                <?php $e=1; foreach((array)$earnings as $ern){
                                    if($ern['calculation_mode_type']==1 && !in_array($ern['code'],$noshow)){
                                        $show_select = 'N'; $show_text = 'Y';
                                ?> 
                                <div class="row align-items-end er_row" id="er_<?php echo $e; ?>">
                                    <div class="col-lg-5">
                                        <div class="form-group mt-3">
                                            <input type="text" class="form-control" id="er_name_<?php echo $e; ?>" value="<?php echo $ern['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group mt-3">
                                            <div id="code_text" <?php if($show_text == 'N'){ ?> style="display: none;" <?php } ?>>
                                                <input class="form-control" readonly type="text" id="er_code_<?php echo $e; ?>" name="code"  value="<?php echo $ern['code']; ?>">
                                            </div>
                                            <div id="code_select" <?php if($show_select == 'N'){ ?> style="display: none;" <?php } ?> >
                                                <select class="form-control js-example-basic-single" name="code_select" id="code" onchange="othervalchange(this.value)" >
                                                    <option value=""> Select Any</option>
                                                    <?php foreach($show_fixed as $keyfix=>$fixed){ ?>
                                                        <option value="<?php echo $keyfix; ?>"><?php echo $fixed; ?></option>
                                                    <?php } ?>
                                                    <option value="OT123">Other (Add New Text Label)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                          <span onclick="deleterow('er_<?php echo $e; ?>','earnings',<?php echo $ern['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i></span>
                                        <button type="button" onclick="earningdata(<?php echo $ern['id'].",'er',".$e; ?>)" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>
                                    </div>
                                </div>
                                 <?php $e++; }
                                } ?>
                            </div>
                            <div class="col-lg-12">
                                <button class="btn btn-primary" type="button" onclick="addearningdetails('earnings')"><i class="fa fa-plus"></i> Add More</button>
                            </div>
                            <div  id="draggablePanelList" >
                                <div class="row align-items-end mt-3">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                           <input type="text" class="form-control" id="er_name_tt" value="<?php echo $lsv['TOTAL_EARNING_OTHER']['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <input type="text" placeholder="10000"  id="er_code_tt" value="<?php echo $lsv['TOTAL_EARNING_OTHER']['code']; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                         <span >&nbsp;</span>
                                        <button type="button" id="er_code_tt"  onclick="earningdata(<?php echo $lsv['TOTAL_EARNING_OTHER']['id'].",'er','tt'"; ?>)" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Deductions</h4>
                             <div class="row align-items-end mt-3">
                                <div class="col-lg-5">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Lable</span>
                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Code</span>
                                    </div>
                                </div>
                                <div class="col-lg-2 text-end mb-3">
                                    
                                </div>
                            </div>
                            <div  id="draggablePanelList" class="deductionclass">
                                
                                <?php $e=1; foreach((array)$earnings as $ern){
                                    if($ern['calculation_mode_type']==2 && !in_array($ern['code'],$noshow)){
                                       $show_select = 'N'; $show_text = 'Y';
                                ?> 
                                <div class="row align-items-end dd_row" id="dd_<?php echo $e; ?>">
                                    <div class="col-lg-5">
                                        <div class="form-group mt-3">
                                            <input type="text" class="form-control" value="<?php echo $ern['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group mt-3">
                                           
                                            <div id="code_text" <?php if($show_text == 'N'){ ?> style="display: none;" <?php } ?>>
                                                <input class="form-control" id="dd_name_<?php echo $e; ?>" <?php if(in_array($ern['code'],(array)$fixedcd)){ echo "readonly"; } ?> type="text" name="code"  value="<?php echo $ern['code']; ?>">
                                            </div>
                                            <div id="code_select" <?php if($show_select == 'N'){ ?> style="display: none;" <?php } ?> >
                                                <select class="form-control js-example-basic-single" name="code_select" id="dd_code_<?php echo $e; ?>" onchange="othervalchange(this.value)" >
                                                    <option value=""> Select Any</option>
                                                    <?php foreach($show_fixed as $keyfix=>$fixed){ ?>
                                                        <option value="<?php echo $keyfix; ?>"><?php echo $fixed; ?></option>
                                                    <?php } ?>
                                                    <option value="OT123">Other (Add New Text Label)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                          <span onclick="deleterow('dd_<?php echo $e; ?>','deduction',<?php echo $ern['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i></span>
                                        <button type="button" onclick="earningdata(<?php echo $ern['id'].",'dd',".$e; ?>)" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>
                                    </div>
                                </div>
                                 <?php $e++; } } ?>
                            </div>
                            <div class="col-lg-12">
                                <button class="btn btn-primary" type="button" onclick ="addearningdetails('deduction')"><i class="fa fa-plus"></i> Add More</button>
                            </div>
                            <div class=""  id="draggablePanelList">
                                <div class="row align-items-end mt-3">
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                           <input type="text" class="form-control" id="dd_name_tt" value="<?php echo $lsv['TOTAL_DEDUCTION']['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="form-group">
                                            <input type="text" placeholder="10000"  id="dd_code_tt" value="<?php echo $lsv['TOTAL_DEDUCTION']['code']; ?>" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-lg-2 text-end mb-3">
                                         <span >&nbsp;</span>
                                        <button type="button" id="er_code_tt"  onclick="earningdata(<?php echo $lsv['TOTAL_DEDUCTION']['id'].",'dd','tt'"; ?>)" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mx-auto">
                                    <div class="row align-items-end mt-3" id="draggablePanelList">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                               <input type="text" class="form-control" id="er_name_nt" value="<?php echo $lsv['NETPAYABLE']['name']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <input type="text" placeholder="10000"  id="er_code_nt" value="<?php echo $lsv['NETPAYABLE']['code']; ?>" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 text-end mb-3">
                                             <span >&nbsp;</span>
                                            <button type="button" id="er_code_tt"  onclick="earningdata(<?php echo $lsv['NETPAYABLE']['id'].",'er','nt'"; ?>)" class="btn p-0 m-l-10" ><i class="fa fa-pause-circle"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h6 class="f-w-500 m-l-5 m-r-5 bg-gray">Footer Details</h6>
        <div class="col-lg-12 card-block">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Footer Amount</h4>
                    <div class="row">
                        <div class="col-lg-6 mx-auto">
                            <div class="row align-items-end mt-3" id="draggablePanelList">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <span class="form-label f-w-600 f-14">Amount</span>
                                        <input type="text" class="form-control" placeholder="Ex: 75800.00">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <span class="form-label f-w-600 f-14">On Words</span>
                                        <input type="text" placeholder="Ex: Seventy five thousand eight hundred" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 text-center">
                        <input type="hidden" name="submit" value="Add">
            <button   class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="continue"><i class="la la-check-circle"></i>Save</button>
        </div>
    </div>
</div>
</form>
