<?php 
if(isset($_POST['submit'])){
  //  echo "<pre>"; print_r($_POST); exit;
    //if($_POST['submit1'] == 'Add'){
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_by'] = $_SESSION['userid'];
        $_POST['created_date'] = nowDate();
      // echo "<pre>"; print_r($_POST);  exit;
        $url = API."?mode=addsalary&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        //echo $response; exit;
         if ($response) {
            if ($_POST['submit_value'] == 'continue'){
                    $LASTID = json_decode($response,1);
                    $nrid = "id:".$LASTID['LASTID'];
                    header("location:index.php?option=".$option."&Rid=".deocdeId($nrid));
                }else{
                    header("location:index.php?option=".$list);
                }
        }
    //}
}

$pageurl = API."?mode=togetsalarycatdetaillist";
$data =  callAPI($pageurl);
$data = json_decode($data,1);
//print_r($data);


?>
<script type="text/javascript">
    function calucationstocat(){
        var count = $(".itemRow").length;
       // alert("test");
        var month =0;  var year =0;
        for(var i=1 ; i<count+1;i++ ){
            var cat_type = $("#cat_type_"+i).val();
           // alert(cat_type);
            ///var ccheck = $('#cat_type_'+i).val();
            if( cat_type =='P'){
                var cat_cat = $("#cat_cat_"+i).val();
                var cat_num = $("#cat_num_"+i).val();
                var basic = $("#"+cat_cat).val();
               // alert(cat_num);
                if(!basic){ basic = 0; }
               // alert(basic);
                month_amount = (parseFloat(cat_num)/100)* parseFloat(basic);
                $('#m_amount_'+i).val(month_amount);
                $('#y_amount_'+i).val(month_amount*12);
                
            }
        }
    }
    function spicalallowances(id){
        var basic = $("#total_month").val();
        var ctc = $("#ctc").val();
        sp_amount = ctc-basic;
        $('#m_amount_'+id).val(sp_amount);
        $('#y_amount_'+id).val(sp_amount*12);
        var month_amount = parseFloat(basic)+parseFloat(sp_amount);
        $('#total_month').val(month_amount);
        $('#total_year').val(month_amount*12);

    }
    function monthlytoyearlycat(val,id){
     //   alert("ravi");
       // alert(id);
        var cat_cat = $("#cat_cat_"+id).val();
        var cat_type = $("#cat_type_"+id).val();
        var cat_code =  $("#cat_code_"+id).val();
        //var cat_num = $("#cat_num_"+id).val();
         $('#cat_num_'+id).val(val);
        
        //alert(cat_cat+"//"+cat_type+"/"+cat_num+"/"+val);
        if(cat_cat !="" &&  cat_type != ''){
           // alert("ravi"+data.calculation_type);
           // alert("Goood");
            if(cat_type == 'P'){
                var basic = $("#"+cat_cat).val();
                if(!basic){ basic = 0; }
                //alert(basic);
                month_amount = (parseFloat(val)/100)* parseFloat(basic);
                //alert(month_amount);
                $('#m_amount_'+id).val(month_amount);
                $('#'+cat_code).val(month_amount);
                $('#y_amount_'+id).val(month_amount*12);
                

            }else{
               
                $('#m_amount_'+id).val(val);
                $('#y_amount_'+id).val(val*12);
            }
        }
        calucationstocat();
        calucations();
    }
    function ctcmonthlychange(val){
        var mvar = val/12;
        $('#ctc').val(parseFloat(mvar).toFixed(2));
    }
    function monthlytoyearly(val,id){
        //alert(val);
         $('#y_amount_'+id).val(val*12);
        //calucations();
    }
     function dynamicinformationopup(option){
       // alert("information");
        $('#popup1').html(decodeURI(option));
        $('#kt_modal_KTDatatable_remote').modal({ show: true});
    }
     function addsaldata(val){
        //alert(val);
        var check = $('#'+val+"check").val();

        if(check == 0){
            $('#'+val+"check").val(1);
            var data = $('#'+val+"_detail").val();
            //alert(data);
            var obj = JSON.parse(data);
            //lert(obj.name);
            addrows(obj,val);
        }else{
            $('#'+val+"check").val(0);
           // alert('shankar');
            removerows(val);
        }
    }
    function addrows(data,id){
        var count = $(".itemRow").length;
         count++;
        var month_amount = 0;
        if(data.calculation_type !="" &&  data.calculation_category != ''){
           // alert("ravi"+data.calculation_type);
         //  alert(data.calculation_type);
            if(data.calculation_type == 'P'){
                var codedetail1 = data.calculation_category.toUpperCase(); 
                var codedetail = "% of "+codedetail1.replace("_","  "); 
               // alert(data.calculation_category);
                var basic = $("#"+data.calculation_category).val();
                if(!basic){ basic = 0; }
                //alert(data.calculation_number);
                //alert(basic);
                month_amount = (parseFloat(data.calculation_number)/100)* parseFloat(basic);
            }else if(data.calculation_type == 'SP'){
                $('#SPS').val(count);
                var codedetail = "Fixed Amount";
              //  alert("TEST");
                var basic = $("#total_month").val();
              //  alert(basic);
                var ctc = $("#ctc").val();
              //  alert(ctc);
               month_amount = ctc-basic;
                
            }else{
                month_amount = data.calculation_number;
                var codedetail = "Fixed Amount";
            }
            month_amount = parseFloat(month_amount).toFixed(2);
        }
       // alert(month_amount); alert(data.code);
        
        
        var htmlRows = '';
        var note = "'"+data.note+"'";
        htmlRows += '<tr id="'+id+'">'; 
        htmlRows += '<td><div class="form-group col-lg-12" ><input type="text" class="form-control itemRow" readonly value="'+data.name+'" name="name[]"><input type="hidden" class="form-control itemRow" readonly value="'+data.id+'" name="id[]"></div></td>';     
        htmlRows += '<td><div class="form-group col-lg-12">  <div class="input-group"> <input type="text" onblur="monthlytoyearlycat(this.value,'+count+')" class="form-control text-right" value="'+data.calculation_number+'" name="calucation[]" aria-describedby="basic-addon2"/>   <div class="input-group-append"><span class="input-group-text">'+codedetail+'</span></div> </div></div> </td>';   
      htmlRows += '<td><div class="form-group col-lg-12"><input type="text" class="form-control text-right" readonly placeholder="0.00" name="monthly_amount[]" value ="'+month_amount+'"  id="m_amount_'+count+'"></div> <input type="hidden" name="cat_type[]" value="'+data.calculation_type+'" id="cat_type_'+count+'">  <input type="hidden" name="cat_code[]" value="'+data.code+'" id="cat_code_'+count+'">     <input type="hidden" name="cat_cat[]" value="'+data.calculation_category+'" id="cat_cat_'+count+'"> <input type="hidden" name="cat_num[]" value="'+data.calculation_number+'" id="cat_num_'+count+'"></td>';            
        htmlRows += '<td><div class="form-group col-lg-12"><div class="input-group"> <input type="hidden" name="'+data.code+'" value="'+month_amount+'" id="'+data.code+'"><input type="text" readonly id="y_amount_'+count+'" name="yearly_amount[]"  value ="'+month_amount*12+'" class="form-control text-right" placeholder="0.00"><a href="#" class="mt-6" onclick="dynamicinformationopup('+encodeURI(note)+')">&nbsp;<i class="icon-2x text-dark-50 flaticon-information "> </i> </a></div> </div></td>'; 
        htmlRows += '</tr>';
        $("#invoiceItem"+data.category_id).css("display", "block");
        $('#invoiceItem'+data.category_id).append(htmlRows);
        calucations();
    }
     function removerows(val){
       // $(".itemRow:checked").each(function() {
            //console.log(this);
            $('#'+val).remove();
           // calucationstocat();
             calucations();
        //});
        //$('#checkAll').prop('checked', false);
    }
    function calucations(){
       // alert("ravi");
        var count = $(".itemRow").length;
        //alert(count);
        var month =0;  var year =0;
       // alert("RAVI");
       $("[id^='cat_type_']").each(function() {
            var i = $(this).attr('id');
            i = i.replace("cat_type_",'');
           // alert(i);
            var ccheck = $('#cat_type_'+i).val();;
            if( ccheck!='SP'){
                var cal = $('#m_amount_'+i).val();
                var cal2 = $('#y_amount_'+i).val();
                month = parseFloat(month)+ parseFloat(cal);
                year = parseFloat(year)+ parseFloat(cal2);
            }
        });
         //alert(month);
         $('#total_month').val(month);
         $('#total_year').val(year);
        var sps = $('#SPS').val();
        if(sps !=0){
            spicalallowances(sps);
        }
    }
</script>
<div class="d-flex flex-column-fluid">

    <div class="container-fluid">
            <div class="card card-custom">
        <div class="card-body">

    <div class="d-flex flex-row">
        
        <!--begin::Content-->
        <div class="flex-row-fluid ml-lg-8">
            <!--begin::Row-->
       
                <div class="row salary-template">
                    <div class="col-md-3">
                        <p class="text-dark-75 font-weight-bolder font-size-h5 m-0">Build Your Template</p>
                        <div class="font-size-sm text-muted font-weight-bold mt-1 mb-8">Include active salary components</div>
                        <div class="accordion accordion-solid accordion-toggle-plus" id="accordionExample3">
                            
                            <?php $j=1; foreach ($data as  $dat) { ?>
                            <div class="card">
                                <div class="card-header" id="headingTwo3">
                                    <div class="card-title collapsed" data-toggle="collapse" data-target="#collapseTwo<?php echo $j; ?>">
                                        <?php echo $dat['name'];?>
                                    </div>
                                </div>
                                <div id="collapseTwo<?php echo $j; ?>" class="collapse" data-parent="#accordionExample3">
                                    <div class="card-body">
                                         <?php foreach ($dat['data'] as  $value) { ?>
                                        <div class="mt-3">
                                           <?php $addsaldata = $value['id'];
                                            echo $value['name']; ?>
                                            <label class="checkbox float-right">
                                            <input type="hidden" name="addsaldata" id="<?php echo $addsaldata."_detail"; ?>" value='<?php echo json_encode($value); ?>' >
                                            <input type="hidden" id="<?php echo $addsaldata."check"; ?>" value="0">
                                                <input type="checkbox" name="Checkboxes1" onclick="addsaldata(<?php echo "'".$addsaldata."'"; ?>)"><span></span>
                                            </label>
                                        </div>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php $j++; } ?>
                            
                        </div>
                    </div>
                    <?php $inn = "Basic salary Amount";?>
                    <div class="col-md-9">
                        <form class="form" method="post" action="">
                            <div class="card card-custom gutter-b">
                                <!--begin::Header-->
                                <div class="card-header border-0 py-0 pl-5">
                                    <h3 class="card-title align-items-start flex-column">
                                        <span class="card-label font-weight-bolder text-dark">Salary</span>
                                        <span class="text-muted mt-3 font-weight-bold font-size-sm">Experience in different organizations</span>
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-lg-4">
                                            <label>User Name</label>
                                            <input type="text" class="form-control" placeholder="Enter employee ID">
                                            <!-- <span class="form-text text-muted">Please enter your full name</span> -->
                                        </div>
                                        <div class="col-lg-4">
                                            <label>Description:</label>
                                            <textarea class="form-control" id="kt_autosize_2" rows="3" style="overflow: hidden; overflow-wrap: break-word; resize: none; height: 40px;">Max 5000 characters</textarea>
                                        </div>
                                        <div class="col-lg-4">
                                            <label>Notes:</label>
                                            <textarea class="form-control" id="kt_autosize_2" name="note" placeholder="Max 5000 characters" ></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Header-->
                                <div class="card-body row pl-5 py-0">
                                    <div class="col-md-2 mt-3">
                                        <span>Annual CTC</span>
                                    </div>  
                                    <div class="col-md-6">
                                        <div class="form-group">
                                          <!--   <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">$</span>
                                                </div> -->
                                                <input type="text" class="form-control text-right" placeholder="0.00" value="0.00" name="ctc_y" id="ctc_y" onblur="ctcmonthlychange(this.value)">
                                                <input type="hidden" class="form-control" placeholder="0.00" value="0.00" name="ctc" id="ctc">
                                                
                                            <!-- </div> -->
                                        </div>
                                    </div>  
                                    <div class="col-md-2 mt-3">
                                       <!--  <span>per year</span> -->
                                    </div>  
                                </div>  
                                <!--begin::Body-->
                                <div class="card-body pt-0 pb-3">
                                    <!--begin::Table-->
                                    <div class="table-responsive">
                                        <table class="table table-head-custom table-head-bg table-borderless table-vertical-center"  >
                                            <thead>
                                                <tr class="text-uppercase">
                                                    <th style="width: 30%" class="pl-7">
                                                        <span class="text-dark-75">Salary Component</span>
                                                    </th>
                                                    <th style="width: 20%">Calculation Type</th>
                                                    <th style="width: 20%;text-align: center;">Amount Monthly</th>
                                                    <th style="width: 20%">Amount Annually</th>
                                                    <!-- <th style="width: 15%">Remarks</th> -->
                                                </tr>
                                            </thead>
                                        </table>
                                          <?php $j=1; foreach ($data as  $dat) { ?>
                                        <table id="invoiceItem<?php echo $j; ?>" style="display: none;">
                                            <tbody>
                                                <tr>
                                                    <td class="pl-0 py-4" style="width: 30%" >
                                                        <b><?php echo $dat['name'];?></b>
                                                    </td>
                                                    <td style="width: 20%" ></td>
                                                    <td style="width: 20%"></td>
                                                    <td style="width: 20%"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    <?php $j++; } ?>
                                    <input type="hidden" name="sp" id="SPS" value="0">
                                                <!-- <tr>
                                                    <td>
                                                       <div class="form-group col-lg-12" >
                                                             <input type="text" class="form-control itemRow" readonly value="Basic" name="basic">

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" placeholder="Username" aria-describedby="basic-addon2"/>
                                                            <div class="input-group-append"><span class="input-group-text">@example.com</span></div>
                                                        </div>
                                                    </div>
                                                    </td>
                                                    <td align="right">
                                                        <div class="form-group">
                                                            <input type="text" class="form-control"  onblur="monthlytoyearly(this.value,1)" placeholder="0.00" id="m_amount_1" name ="monthly_amount[]" value="0.00">
                                                             <input type="hidden" name="cat_type[]" value="" id="cat_type_1">
                                                             <input type="hidden" name="cat_cat[]" value="" id="cat_cat_1">
                                                             <input type="hidden" name="cat_num[]" value="" id="cat_num_1">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <input type="text" class="form-control" name="yearly_amount[]"  id="y_amount_1" readonly placeholder="0.00" value="0.00">
                                                                <a href="#" class="mt-6" onclick="dynamicinformationopup(<?php echo "'".$inn."'" ?>)">
                                                                    &nbsp;<i class="icon-2x text-dark-50 flaticon-information "> </i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        
                                                    </td>
                                                </tr> -->
                                          
                                        <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                            <tbody>
                                               <!--  <tr>
                                                    <td class="pl-0 py-4">
                                                        <div class="align-items-center">
                                                            <p class="" style="margin: 0;">fixed Allowance</p>
                                                            
                                                            <span class="text-muted font-weight-bold">Monthly CTC - Sum of all other components</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">Fixed amount</span>
                                                        
                                                    </td>
                                                    <td>
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                           
                                                        </span>
                                                        
                                                    </td>
                                                    <td>
                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">
                                                            
                                                        </span>
                                                       
                                                    </td>
                                                </tr> -->
                                                <tr bgcolor="#eee">
                                                    <td class="pl-2 py-4">
                                                        <span class="text-dark-75">Cost to Company</span>
                                                    </td>
                                                    <td></td>
                                                    <td><!-- <span class="fas fa-rupee-sign"></span>  -->
                                                        <span class="text-dark-75 font-weight-bolder font-size-lg">
                                                             <input type="text" name="total_month" class="form-control text-right" id="total_month"  value="0.00">
                                                        </span> 
                                                    </td>
                                                    <td><!-- <span class="fas fa-rupee-sign"></span>  -->
                                                        <span class="text-dark-75 font-weight-bolder font-size-lg">
                                                            <input type="text" name="total_year" class="form-control text-right " id="total_year"  value="0.00">
                                                        </span>
                                                    </td>
                                                    <!-- <th style="min-width: 15%">Remarks</th> -->
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end::Table-->
                                </div>
                                <!--end::Body-->
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-lg-4"></div>
                                    <div class="col-lg-8">
                                        <input type="hidden" name="submit" value="submit">
                                        <button type="submit" class="btn btn-primary mr-2">Submit</button>
                                        <button type="reset" class="btn btn-secondary">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>      
                </div>
            </div>
        </div>
    </div>  
 <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content" >
            
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body"  id="popup1">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
