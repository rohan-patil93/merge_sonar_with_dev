<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$crm_history_id = $_POST['crm_history_id'];
$transaction_table = "crm_companies";
$apiusrl = API."?mode=getcrmleads&transaction_table=".$transaction_table."&rid=".$rid;
$page_list = callAPI($apiusrl);
$res = json_decode($page_list,1);
$transaction = $res['transaction'];
$application_users = $res['application_users'];
$countries =$res['countries'];
$states =$res['states'];
$cities =$res['cities'];
$crm_lead_types =$res['crm_lead_types'];
$crmleaddetailstypes = array();
foreach($crm_lead_types as $crmlt){
        $crmleaddetailstypes[$crmlt['id']] =$crmlt;
}
$company_types = array();
foreach($res['company_types'] as $ctype) {
    $company_types[$ctype['id']] = $ctype;
}
$company_with_contacts = $res['company_with_contacts'];
$company_with_deals = $res['company_with_deals'];
$without_company_contacts = $res['without_company_contacts'];
?>
<script type="text/javascript">
    function pageCallHistories(rid){
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#crmleadhistories").html(loader);
        jQuery.ajax({
            url: "views/custom_insert_crm_lead_histories.php",
            data: "rrid="+rid,
            type: "POST",
            success:function(data){
                $("#crmleadhistories").html(data);

            }
        });
    }
    function submitLeadForm(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            $(".sbt_loading_en_dis").text("Please Wait........");
            $(".sbt_loading_en_dis").attr("disabled", true);
            var crm_lead_id = $("#crm_lead_id").val();
            var formData = new FormData();
            formData.append("page", "insertcrmleads");
            var dataArray = $("#submit_lead_form").serializeArray(),
            dataObj = {
};
            $(dataArray).each(function(i, field){
              formData.append(field.name, field.value);
            });
            var display_image = document.getElementById('display_image').files[0];
            console.log(display_image);
            formData.append("file",display_image);
            jQuery.ajax({
                url: "ajax.php",
                data: formData,
                type: "POST",
                contentType: false,
                processData: false,
                //async:false,
                success:function(data){
                    console.log(data);
                    var jsonres = JSON.parse(data);
                    console.log(jsonres.res.status);
                    if(jsonres.res.status == "sucess"){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                            closeOpenBox();
                            $('#htmlsetpage').html('');
                            var urlParams = new URLSearchParams(window.location.search);
                            if(urlParams.has('TRANST')) {
                                param_value = urlParams.get('TRANST');
                                param_value = (param_value !=null || param_value !="")?param_value:"<?php echo $transaction_table; ?>";
                                getCRMListDashboard(param_value)
                            }else{
                                location.reload();
                            }
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
                        $(".sbt_loading_en_dis").text("Submit");
                        $(".sbt_loading_en_dis").attr("disabled", false);
                    }
                }
            });
        }
    }

    function closeOpenBox(){
        var my_val = $('.pcoded').attr('vertical-placement');
        if (my_val == 'right') {
            var options = {
                direction: 'left'
            };
        } else {
            var options = {
                direction: 'right'
            };
        }
        $('.leadsidebar-form').toggle('slide', options, 500);
        $('.leadsidebar-form').css('display', 'block');
    }

    function selectedcategory(selected_id,element_id,table_name,field_name) {
      var selectedvalues = selected_id;
     if(table_name=='states' || table_name=='cities'){
       $.ajax({
        url: "ajax.php",
        data:'page=getselectedlist&table_name='+table_name+'&id='+selectedvalues+'&field='+field_name,
        type: "POST",
        success: function(response) {
          console.log(response);
          var response = JSON.parse(response);
          console.log(response.length);
          var select = document.getElementById(element_id);
          document.getElementById(element_id).innerHTML = null;
          for (var i = 0; i < response.length; i++)
          {
           var el = document.createElement("option");
           if(table_name=='states'){
            el.textContent = response[i].state_name;
            el.value = response[i].id;
          }
          if(table_name=='cities'){
            el.textContent = response[i].city_name;
            el.value = response[i].id;
          }
          select.add(el);

          console.log(el);
        }
      }
    });
     }

   }
     function submitLeadFormDetail(formid){
        var formvalue =  $("#"+formid).serialize();

        var dataArray = $("#"+formid).serializeArray();
        var assetid = [];
        $(dataArray).each(function(i, field){
            console.log(field.name+' -- inside work --- '+field.value);
            if(field.value == ""){
                assetid.push(field.name);
            }
        });
        if (assetid.length != 0){
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "Please Enter Required Fields",
                type: "error",
                showCancelButton: false,
                confirmButtonClass: "btn-primary",
                confirmButtonText: "Ok",
                closeOnConfirm: true
            });
            return false;
        }

        var transaction_table = '<?php echo $transaction_table; ?>';
        var transaction_id = '<?php echo $rid; ?>';
        var crm_history_id = '<?php echo $crm_history_id; ?>';
        jQuery.ajax({
            url: "ajax.php",
            data: "page=insertcrmleaddetails&crm_lead_id="+transaction_id+"&transaction_id="+transaction_id+"&transaction_table="+transaction_table+"&"+formvalue,
            type: "POST",
            success:function(data){
                console.log(data);
                var jsonres = JSON.parse(data);
                if(jsonres.res.status == "sucess"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                    if(crm_history_id != ""){
                        if(transaction_table =="crm_contacts"){
                            var pagecalltype ="CONTACT";
                        }elseif(transaction_table =="crm_leads"){
                            var pagecalltype ="LEAD";
                        }
                        $('#'+formid)[0].reset();

                        leadPageCall(pagecalltype,transaction_id);
                    }else{
                        pageCallHistories(transaction_table, transaction_id);
                        $('#'+formid)[0].reset();
                    }
                    $('.resetmulyiselect').val(null).trigger('change');
                }else{
                    tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
                }
            }
        });
    }
   function addContactWithCompany(){
        var crm_contact_id = $("#add_more_contact").val();
        var crm_company_id = "<?php echo $rid; ?>";
        $("#error_message_companyid").text('');
        if(crm_contact_id !=""){
            jQuery.ajax({
                url: "ajax.php",
                data: "page=margecompanywithcontact&crm_contact_id="+crm_contact_id+"&crm_company_id="+crm_company_id,
                type: "POST",
                success:function(data){
                    console.log(data);
                    var jsonres = JSON.parse(data);
                    if(jsonres.status == "sucess"){
                        leadPageCall('COMPANY',crm_company_id);
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');

                    }else{
                        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
                    }
                }
            });
        }else{
          $("#error_message_companyid").text('Please Select Any One Contact');
        }
   }

function setDefaultContactWithCompany(crm_contact_id){
    var crm_company_id = "<?php echo $rid; ?>";
    jQuery.ajax({
        url: "ajax.php",
        data: "page=setdefaultcontactwithcompany&crm_contact_id="+crm_contact_id+"&crm_company_id="+crm_company_id,
        type: "POST",
        success:function(data){
            console.log(data);
            var jsonres = JSON.parse(data);
            if(jsonres.status == "sucess"){
                leadPageCall('COMPANY',crm_company_id);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');

            }else{
                tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',jsonres.res.message,'');
            }
        }
    });
}

function checkCompanyNameAvilableOrNotInCustomer(company_name){
    $('.companynameavilableornotmsg').text('');
    $.ajax({
            url: "ajax.php",
            data:'page=checkcompanynameavilableornotincustomer&company_name='+company_name,
            type: "POST",
            async: false,
            success:function(data){
              var res = JSON.parse(data);
              var company_name_res = res.company_name;
              if(company_name_res == "Y"){
                $('.companynameavilableornotmsg').text(company_name+' - Company name is already available');
                $('.crm_contact_sbt_btn').prop('disabled', true);

              }else{
                  $('.crm_contact_sbt_btn').prop('disabled', false);
              }
            },
            error:function (){
}
        });
}
</script>
<div class="user-box">
    <div class="chat-search-box border-bottom">
        <a class="back_leadsidebar-form">
            <i class="ti-close icon-x"></i>
        </a>
        <div class="right-icon-control">
            <h4 class="text-dark f-20 f-w-600 text-left">Add Company</h4>
        </div>
    </div>
    <div class="main-friend-list card-body form-design custom-scroll" style="height: 600px">
        <div class="row">
            <div class="col-lg-5">
                <form id="submit_lead_form">
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
                 <input type="hidden" name="transaction_table" id="transaction_table" value="<?php echo $transaction_table; ?>">
                 <input type="hidden" name="crm_customer_id" id="crm_customer_id" value="<?php echo $transaction['customer_id']; ?>">
                <div class="bg-white p-3 b-radius-10">
                    <h5 class="f-18 f-w-600 bg-gray p-2">Company Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Company Name<span class="requried">*</span></span>
                            <span class="d-block error-message text-danger companynameavilableornotmsg"></span>
                            <input aria-label="Company Name" class="form-control requried_field" type="text" name="company_name" placeholder="Company Name" value="<?php echo $transaction['company_name']; ?>" onblur='checkCompanyNameAvilableOrNotInCustomer(this.value)'>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Originator <span class="requried">*</span></span>
                             <span class="d-block error-message text-danger"></span>
                            <select aria-label="Originator" class="form-control js-example-basic-single requried_field" name="originator">
                                <option value="">Select Any One</option>
                               <?php foreach($application_users as $uval){ ?>
                                    <option value="<?php echo $uval['id']; ?>" <?php echo ($transaction['originator'] == $uval['id'] || ($transaction['originator'] == "" && $_SESSION['userid']== $uval['id']))?"selected":"";?>><?php echo $uval['display_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php  ?>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Website URL</span>
                            <input aria-label="Website URL" class="form-control" type="text" name="website_url" placeholder="Website URL" value="<?php echo $transaction['website_url']; ?>">
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Pan No.</span>
                            <input aria-label="Pan No." class="form-control" type="tel" name="pan_no" placeholder="Pan No." value="<?php echo $transaction['pan_no']; ?>">
                        </div>

                        <div class="col-lg-6  form-group">
                            <span class="form-label">GST No.</span>
                            <input aria-label="GST Number" class="form-control" type="text" name="gstin" placeholder="GST Number" value="<?php echo $transaction['gstin']; ?>">
                        </div>
                        <div class="col-lg-6  form-group">
                            <span class="form-label">Fax Number</span>
                            <input aria-label="Fax Number" class="form-control" type="text" name="fax" placeholder="Fax Number" value="<?php echo $transaction['fax']; ?>">
                        </div>
                        <div class="col-lg-6">
                            <span class="form-label">Image</span>
                            <div class="col-lg-9 col-xl-6 ">
                                <div class="profile-pic">
                                    <label class="-label" for="display_image">
                                        <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                    </label>
                                    <input type="file" name="image" id="display_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                    <img src="<?php echo "uploads/all/".$transaction['image'] ; ?>" id="outputdisplay_image" width="200" alt="Attachment Preview" />
                                    <input type="hidden" name="hid_image" value="<?php echo $transaction['image'] ;  ?>">
                                </div>
                            </div>
                        </div>
                   </div>
                    <h5 class="f-18 f-w-600 bg-gray p-2">Address Information</h5>
                    <div class="row mt-3">
                        <div class="col-lg-12 form-group">
                            <span class="form-label">Address</span>
                            <div class="grow-wrap">
                                <textarea aria-label="Address" name="address" placeholder="Address" class="border form-control" rows="3" name="address" oninput="this.parentNode.dataset.replicatedValue = this.value"><?php echo $transaction['address']; ?></textarea>
                            </div>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">Country</span>
                            <select aria-label="Country" class="form-control js-example-basic-single" name="country_id" onchange="selectedcategory(this.value,'state_id','states','country_id')">
                                <option value="">Select Any One</option>
                                <?php foreach($countries as $cval){ ?>
                                    <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['country_id'] == $cval['id']?"selected":"";?>><?php echo $cval['country_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">State</span>
                            <select aria-label="State" class="form-control js-example-basic-single" name="state_id" id="state_id" onchange="selectedcategory(this.value,'city_id','cities','state_id')">
                                <option value="">Select Any One</option>
                                <?php foreach($states as $cval){ ?>
                                    <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['state_id'] == $cval['id']?"selected":"";?>><?php echo $cval['state_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group w-full">
                            <span class="form-label">City</span>
                            <select aria-label="City" class="form-control js-example-basic-single" name="city_id" id="city_id">
                                <option value="">Select Any One</option>
                                <?php foreach($cities as $cval){ ?>
                                    <option value="<?php echo $cval['id']; ?>" <?php echo $transaction['city_id'] == $cval['id']?"selected":"";?>><?php echo $cval['city_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <span class="form-label">Post Code</span>
                            <input aria-label="Postal Code" class="form-control" type="text" name="post_code" placeholder="Postal Code" value="<?php echo $transaction['post_code']; ?>">
                        </div>
                         <div class="col-lg-12 form-group">
                            <span class="form-label">Description</span>
                            <div class="grow-wrap">
                                <textarea aria-label="Description" name="remark" placeholder="Description" class="border form-control" rows="3" oninput="this.parentNode.dataset.replicatedValue = this.value"><?php echo $transaction['remark']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="button" class="btn btn-primary waves-effect waves-light sbt_loading_en_dis crm_contact_sbt_btn" onclick="submitLeadForm()">Submit</button>
                        <button type="reset" class="btn btn-default waves-effect " data-bs-dismiss="modal">Reset</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="col-lg-7">
                <div class="card-block accordion-block color-accordion-block itemcategory_sec">
                    <div class="color-accordion" id="color-accordion">
                        <a class="accordion-msg b-none waves-effect waves-light">Contacts</a>
                        <div class="accordion-desc">
                            <?php if(count((array)$company_with_contacts) !=0){ ?>
                            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                                <thead>
                                    <tr><th scope="col">Name</th>
                                        <th scope="col">Number</th>
                                        <th scope="col">Originator</th>
                                       <th scope="col" class="text-center">Default Contact</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($company_with_contacts as $crmleads){ ?>
                                    <tr>
                                        <td>
                                            <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $crmleads['id']; ?>')"><?php echo $crmleads['display_name']; ?></button>
                                        </td>
                                        <td><?php echo $crmleads['phone']; ?></td>
                                        <td><?php echo $_SESSION['users'][$crmleads['originator']]; ?></td>
                                        <td class="text-center">
                                            <?php if($crmleads['id'] == $transaction['default_crm_contact_id']){
                                                echo '<span class="form-label label label-md label-warning">Active</span>';
                                            }else{ ?>

                                                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="setDefaultContactWithCompany('<?php echo $crmleads['id']; ?>')">Set Default Contact</button>

                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Contacts</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                        <?php if($rid !=""){ ?>
                            <div class="row mt-3">
                                <div class="col-lg-7">

                                    <select aria-label="Add More Contact" class="form-control js-example-basic-single " id="add_more_contact">
                                        <option value="">Select Any One</option>
                                       <?php foreach($without_company_contacts as $uval){ ?>
                                            <option value="<?php echo $uval['id']; ?>"><?php echo $uval['display_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-5">
                                    <span class="text-danger" id="error_message_companyid"></span>
                                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="addContactWithCompany()">Add Contact With Company</button>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <a class="accordion-msg b-none waves-effect waves-light">Deals</a>
                        <div class="accordion-desc">

                        <?php if(count((array)$company_with_deals) !=0){ ?>
                            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                                <thead>
                                    <tr><th scope="col">Deal Name</th>
                                        <th scope="col">Originator</th>
                                        <th scope="col">Company</th>
                                        <th scope="col">Due Date</th>
                                        <th scope="col">Expected Close Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($company_with_deals as $campaignsres){
                                        ?>
                                    <tr>
                                        <td>
                                            <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('DEALS','<?php echo $campaignsres['id']; ?>')"><?php echo $campaignsres['deal_name']; ?></button>
                                        </td>
                                        <td ><?php echo $_SESSION['users'][$campaignsres['originator']]; ?></td>
                                         <td ><?php echo $campaignsres['crm_company_id']; ?></td>
                                        <td ><?php echo fromDate($campaignsres['due_date']); ?></td>
                                        <td ><?php echo fromDate($campaignsres['expected_close_date']); ?></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Deal</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                        </div>

                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
       $('.back_leadsidebar-form').on('click', function() {
        var my_val = $('.pcoded').attr('vertical-placement');
        if (my_val == 'right') {
            var options = {
                direction: 'left'
            };
        } else {
            var options = {
                direction: 'right'
            };
        }
        $('.leadsidebar-form').toggle('slide', options, 500);
        $('.leadsidebar-form').css('display', 'block');
    });
</script>
<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
