<?php
if($_POST['ajaxcall'] =="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $vendor_id = $_POST['vendor_id'];
    $transaction_table = $_POST['transaction_table'];
    $url = API."?mode=getvendorsdetails&transaction_table=".$transaction_table."&id=".$vendor_id."&admin_type=".$_SESSION['admin_type'];
    $response = callAPI($url);
    $result = json_decode($response,1);
    $role = $result['vendors'];
    $vendor_registers = $result['vendor_registers'];
    $vendordetail = $vendor_registers;
    if($_POST['vtype'] !="VR"){
        $vendordetail = $result['vendors'];
    }else{
        $vendordetail = $result['vendor_registers'];
    }
    $vendor_id = $vendordetail['id'];
    $aggrements =$result['vendor_agreements'];
    $payment_method =$result['payment_method'];
    $salestype =$result['salestype'];
    $emplist = $result['employess'];
    $countrieslist = $result['countries'];
    $states = $result['states'];
    $cities = $result['cities'];
    $vendorslist = $result['vendorslist'];
    $paymenttype = $result['paymenttype'];
    $vendor_type = $result['vendor_type'];
    $countrylist =$stateslist = $citieslist =  array();
    foreach($countrieslist as $country_list){
        $countrylist[$country_list['id']] = $country_list['country_name'];
    }
    foreach($states as $states_list){
        $stateslist[$states_list['id']] = $states_list['state_name'];
    }
    foreach($cities as $cities_list){
        $citieslist[$cities_list['id']] = $cities_list['city_name'];
    }
}
?>
<?php if($_POST['edittype'] == 'EDIT'){ ?>
<div class="card">
    <form method="post" id="submitvendordetailform" enctype="multipart/form-data">
        <div class="text-right">
            <input type="hidden" name="vendor_id" value=<?php echo $vendor_id; ?> />
            <input type="hidden" name="submit" value="Update" />
            <input type="hidden" name="transaction_table" value="<?php echo $transaction_table; ?>" />
            <button  class="btn btn-primary m-r-20 m-b-20"><i class="ti-check"></i>
            Update</button>
            <input type="hidden" name="hid" value=<?php echo $vendor_id; ?> />
        </div>
        <ul class="nav md-tabs infisuite_tab mt-2 ml-2">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#basic-info" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Basic Info</a>
                <div class="slide"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#contact-details" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Contact Details</a>
                <div class="slide"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#note" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Note</a>
                <div class="slide"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tax-info" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Tax Info</a>
                <div class="slide"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#payment-billing" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Payment and Billing</a>
                <div class="slide"></div>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#sale-discount" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Sale / Discount</a>
                <div class="slide"></div>
            </li>
            <?php if($transaction_table == "vendors"){ ?>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#agreements" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Agreements</a>
                    <div class="slide"></div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#attachments" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Attachments</a>
                    <div class="slide"></div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#employees" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Employees</a>
                    <div class="slide"></div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#no-hiring" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">No hiring clause</a>
                    <div class="slide"></div>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content card-block">
            <div class="tab-pane active" id="basic-info" role="tabpanel">
                <div class="row">
                    <div class="form-group row">

                        <div class="col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">Company Name</span>
                            <div class="kt-input-icon">
                                <input aria-label="Company" class="form-control" type="text" id="company" name="company" value="<?php echo $vendordetail['company']; ?>">
                            </div>
                            <div style="display: none;color: red;" id="title_err"> Please enter title </div>
                        </div>

                        <div class="col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">Vendor Type</span>
                            <select aria-label="Vendor Type" name="vendor_type" class="form-select js-example-basic-single">
                                <?php foreach($vendor_type as $vtypekey=>$vtypeval) { ?>
                                    <option <?php echo ($vendordetail['vendor_type']==$vtypekey || $vendordetail['vendor_type'] =="")?'selected':'' ?> value="<?php echo $vtypekey;?>"><?php echo $vtypeval; ?></option>
                                <?php } ?>
                            </select>
                            <div style="display: none;color: red;" id="title_err_2"> Please enter title </div>
                        </div>
                        <div class="col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">Title</span>
                            <div class="kt-input-icon">
                                <input aria-label="Title" class="form-control" type="text" id="title" name="title" value="<?php echo $vendordetail['title']; ?>">
                            </div>
                            <div style="display: none;color: red;" id="title_err_3"> Please enter title </div>
                        </div>
                        <div class="form-group col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">First Name</span>
                            <div class="kt-input-icon">
                                <input aria-label="First Name" class="form-control" type="text" id="first_name" name="first_name" value="<?php echo $vendordetail['first_name']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="first_name_err"> Please enter first_name </div>
                        </div>
                        <div class="form-group col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">Middle Name</span>
                            <div class="kt-input-icon">
                                <input aria-label="Middle Name" class="form-control" type="text" id="middle_name" name="middle_name" value="<?php echo $vendordetail['middle_name']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="middle_name_err"> Please enter middle name </div>
                        </div>
                        <div class="form-group col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">Last Name</span>
                            <div class="kt-input-icon">
                                <input aria-label="Last Name" class="form-control" type="text" id="last_name" name="last_name" value="<?php echo $vendordetail['last_name']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="last_name_err"> Please enter last name </div>
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Email</span>
                            <div class="kt-input-icon">
                                <input aria-label="Email" class="form-control" id="email" name="email" type="text" value="<?php echo $vendordetail['email']; ?>" readonly>
                            </div>
                            <div style="display: none;color: red;" id="email_err"> Please enter email </div>
                        </div>
                        <div class="form-group col-lg-3">
                            <span class="form-label">Phone</span>
                            <div class="kt-input-icon">
                                <input aria-label="Phone" class="form-control" type="text" id="phone" name="phone" value="<?php echo $vendordetail['phone']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="phone_err"> Please enter phone </div>
                        </div>
                        <div class="form-group col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">Suffix</span>
                            <div class="kt-input-icon">
                                <input aria-label="Suffix" class="form-control" type="text" id="suffix" name="suffix" value="<?php echo $vendordetail['suffix']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="suffix_err"> Please enter suffix name </div>
                        </div>

                        <div class="form-group col-lg-3 ">
                            <span class="form-label">Display Name As</span>
                            <div class="kt-input-icon">
                                <input aria-label="Display Name" class="form-control" type="text" id="display_name" name="display_name" value="<?php echo $vendordetail['display_name']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="display_name_err"> Please enter display name </div>
                        </div>
                        <div class="col-lg-3 p-r-5" style="float: left;">
                            <span class="form-label">GST  Registration type</span>
                            <div class="kt-input-icon">
                                <input aria-label="Gsttype" class="form-control" type="text" id="gsttype" name="gsttype" value="<?php echo $vendordetail['gsttype']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="gsttype_2"> Please enter GST Registration type</div>
                        </div>
                        <div class="form-group col-lg-3" style="float: left;">
                            <span class="form-label">GSTIN</span>
                            <div class="kt-input-icon">
                                <input aria-label="Gstin" class="form-control" type="text" id="gstin" name="gstin" value="<?php echo $vendordetail['gstin']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="gstin_err"> Please enter GST IN </div>
                        </div>
                        <div class="form-group col-lg-3 w-full">
                            <span class="form-label">Parent Vendor</span>
                            <select aria-label="Parent Vendor" name="parent_vendor" class="form-select js-example-basic-single">
                                <option value="">Select Any One</option>
                                <?php foreach($vendorslist as $venlist) { ?>
                                    <option <?php echo $vendordetail['parent_vendor']==$venlist['id'] ? 'selected':'' ?> value="<?php echo $venlist['id'];?>"><?php echo $venlist['display_name']; ?></option>
                                <?php } ?>
                            </select>

                            <div style="display: none;color: red;" id="email_err_2"> Please enter parent_customer </div>
                        </div>


                        <div class="col-lg-3" style="float: left;">
                            <span class="form-label">Logo</span>
                            <div class="col-lg-9 col-xl-6">
                                <div class="profile-pic">
                                    <label class="-label" for="display_logo">
                                        <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                    </label>
                                    <input type="file" name="image" id="display_logo" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                    <img src="<?php echo "uploads/all/".$vendordetail['image'] ; ?>" id="outputdisplay_logo" width="200" alt="Logo" />
                                    <input type="hidden" name="hid_image" value="<?php echo $vendordetail['image'] ;  ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="contact-details" role="tabpanel">
                <div class="from row ">
                    <div class="col-lg-6 row" >
                        <div class="form-group col-lg-12">
                            <span class="form-label">Billing Adderss</span>
                            <div class="kt-input-icon">
                                <textarea aria-label="Billing Adderss" type="text" class="form-control" placeholder="Billing Adderss" name="billing_address"><?php echo $vendordetail['billing_address']; ?></textarea>
                            </div>
                        </div>
                        <div class="form-group col-lg-6 p-r-5" >
                            <span  class="form-label">Pincode</span>
                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="text" id="billing_post_code" name="billing_post_code" value="<?php echo $vendordetail['billing_post_code']; ?>" >

                        </div>
                        <div class="form-group col-lg-6 w-full" >
                            <span class="form-label">Country</span>
                            <select aria-label="Billing Country" id="kt_select2_102" class="form-select js-example-basic-single" name="billing_country"  onchange="selectedcategory(this.value,'kt_select2_101','states','country_id','selectedbillcountry')">
                                <option value="">Select Any One</option>
                                <?php foreach($countrieslist as $countryvalue) { ?>
                                    <option <?php echo $vendordetail['billing_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id']; ?>"><?php echo $countryvalue['country_name']; ?></option>
                                <?php } ?>
                            </select>

                        </div>

                        <div class="form-group col-lg-6  w-full" >
                            <span class="form-label">State</span>
                            <select aria-label="Billing State" id="kt_select2_101" name="billing_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_103','cities','state_id','selectedbillstate')">
                                <option value="">Select Any One</option>
                                <?php foreach($states as $states1) { ?>
                                    <option <?php echo $vendordetail['billing_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
                                <?php } ?>
                            </select>

                        </div>
                        <div class="form-group col-lg-6 p-r-5  w-full" >
                            <span class="form-label">City/Town</span>
                            <select aria-label="Billing City" id="kt_select2_103" name="billing_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedbillcity')">
                                <option value="">Select Any One</option>
                                <?php foreach($cities as $cities1) { ?>
                                    <option <?php echo $vendordetail['billing_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group col-lg-6 p-r-5" style="float: right;">
                            <span class="form-label">Contact Person</span>
                            <input aria-label="Contact Person" class="form-control" placeholder="Contact Person" type="text" id="" name="contact_person" value="<?php echo $vendordetail['contact_person']; ?>" >

                        </div>
                        <div class="form-group col-lg-6 p-r-5" style="float: left;">
                            <span class="form-label">Mobile</span>
                            <div class="kt-input-icon">
                                <input aria-label="Mobile" class="form-control" type="text" id="mobile" name="mobile" value="<?php echo $vendordetail['mobile']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="mobile_err"> Please enter mobile </div>
                        </div>
                        <div class="form-group col-lg-6 p-r-5" style="float: right;">
                            <span class="form-label">Fax</span>
                            <div class="kt-input-icon">
                                <input aria-label="Fax" class="form-control" type="text" id="fax" name="fax" value="<?php echo $vendordetail['fax']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="fax_err"> Please enter Fax </div>
                        </div>
                        <div class="form-group col-lg-6 " style="float: left;">
                            <span class="form-label">Others</span>
                            <div class="kt-input-icon">
                                <input aria-label="Other" class="form-control" type="text" id="other" name="other" value="<?php echo $vendordetail['other']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="first_name_err_2"> Please enter first_name </div>
                        </div>
                        <div class="form-group col-lg-6 p-r-5" style="float: right;">
                            <span class="form-label">Website</span>
                            <div class="kt-input-icon">
                                <input aria-label="Website Url" class="form-control" type="text" id="website_url" name="website_url" value="<?php echo $vendordetail['other']; ?>" >
                            </div>
                            <div style="display: none;color: red;" id="fax_err_2"> Please enter Fax </div>
                        </div>
                    </div>
                    <div class="col-lg-6 row" style="float: left;">
                        <div class="form-group col-lg-12" >
                            <span class="form-label">Shipping Adderss</span>
                            <div class="kt-input-icon">
                                <textarea aria-label="Street" type="text" class="form-control" placeholder="Street" name="shipping_address">
                                    <?php echo $vendordetail['shipping_address']; ?>
                                </textarea>
                            </div>
                        </div>
                        <div class="form-group col-lg-6" >
                            <span class="form-label">Pincode</span>
                            <input aria-label="Pincode" class="form-control" placeholder="Pincode" type="text" id="shipping_post_code" name="shipping_post_code" value="<?php echo $vendordetail['shipping_post_code']; ?>" >

                        </div>
                        <div class="form-group col-lg-6  w-full" >
                            <span class="form-label">Country</span>
                            <select aria-label="Shipping Country" id="kt_select2_104" class="form-select js-example-basic-single"  name="shipping_country" onchange="selectedcategory(this.value,'kt_select2_105','states','country_id','selectedshipcountry')">
                                <option value="">Select Any One</option>
                                <?php foreach($countrieslist as $countryvalue) { ?>
                                    <option <?php echo $vendordetail['shipping_country']==$countryvalue['id'] ? 'selected':'' ?> value="<?php echo $countryvalue['id'];?>"><?php echo $countryvalue['country_name']; ?></option>
                                <?php } ?>
                            </select>

                        </div>

                        <div class="form-group col-lg-6  w-full">
                            <span class="form-label">State</span>
                            <select aria-label="Shipping State" id="kt_select2_105" name="shipping_state" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'kt_select2_106','cities','state_id','selectedshipstate')">
                                <option value="">Select Any One</option>
                                <?php foreach($states as $states1) { ?>
                                    <option <?php echo $vendordetail['shipping_state']==$states1['id'] ? 'selected':'' ?> value="<?php echo $states1['id'];?>"><?php echo $states1['state_name']; ?></option>
                                <?php } ?>

                            </select>

                        </div>
                        <div class="form-group col-lg-6 p-r-5 w-full">
                            <span class="form-label">City/Town</span>
                            <select aria-label="Shipping City" id="kt_select2_106" name="shipping_city" class="form-select js-example-basic-single" onchange="selectedcategory(this.value,'','city','','selectedshipcity')">
                                <option value="">Select Any One</option>
                                <?php foreach($cities as $cities1) { ?>
                                    <option <?php echo $vendordetail['shipping_city']==$cities1['id'] ? 'selected':'' ?> value="<?php echo $cities1['id'];?>"><?php echo $cities1['city_name']; ?></option>
                                <?php } ?>
                            </select>

                        </div>

                    </div>
                </div>
            </div>
            <div class="tab-pane" id="note" role="tabpanel">
                <div class="row ">
                    <div class="col-lg-6 p-r-5" style="float: left;">
                        <span class="form-label">PO Notes</span>
                        <textarea aria-label="Enter your Customer Statement Notes" type="text" name="vendor_po_notes"  class="form-control" style="height: 150px;" placeholder="Enter your Customer Statement Notes"><?php echo $vendordetail['vendor_po_notes']; ?></textarea>
                    </div>
                    <div class="col-lg-6" style="float: left;">
                        <span class="form-label">Other Notes</span>
                        <textarea aria-label="Enter your Customer Invoice Notes" type="text" name="vendor_other_notes"   class="form-control" style="height: 150px;" placeholder="Enter your Customer Invoice Notes"><?php echo $vendordetail['vendor_other_notes']; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="tax-info" role="tabpanel">
                <div class="from row">
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">Tax Reg.No.</span>
                        <div class="kt-input-icon">
                            <input aria-label="Vendor Tax Reg No" class="form-control" type="text" id="vendor_tax_reg_no" name="vendor_tax_reg_no" value="<?php echo $vendordetail['vendor_tax_reg_no']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">CST Reg.No</span>
                        <div class="kt-input-icon">
                            <input aria-label="Cst Reg No" class="form-control" type="text" id="cst_reg_no" name="cst_reg_no" value="<?php echo $vendordetail['cst_reg_no']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">PAN No.</span>
                        <div class="kt-input-icon">
                            <input aria-label="Pan No" class="form-control" type="text" id="pan_no" name="pan_no" value="<?php echo $vendordetail['pan_no']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">  </span>
                        <div class="kt-checkbox-inline">
                            <label class="kt-checkbox">
                                <input aria-label="Tds Apply" type="checkbox" name="tds_apply" class="js-switch" <?php echo $vendordetail['tds_apply'] == "0"?"checked":"";?>> Apply TDS for this vendor
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="payment-billing" role="tabpanel">
                <div class="from row">
                    <div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
                        <span class="form-label">Preferred Payment Method</span>
                        <select aria-label="Pref Payment Method" name="pref_payment_method" class="form-control form-select js-example-basic-single">
                            <option value="">Select Any One</option>
                            <?php foreach($payment_method as $key=>$pm) { ?>
                                <option value="<?php echo $key; ?>" <?php echo $vendordetail['pref_payment_method']==$key?'selected':'';?> ><?php echo $pm; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
                        <span class="form-label">Terms</span>
                        <select aria-label="Payment Terms" name="payment_terms" class="form-control form-select js-example-basic-single">
                            <option value="">Select Any One</option>
                            <?php foreach($paymenttype as $key=>$st) { ?>
                                <option value="<?php echo $key; ?>" <?php echo $vendordetail['payment_terms']==$key?'selected':'';?> ><?php echo $st; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">Preferred Delivery Method</span>
                        <div class="kt-input-icon">
                            <input aria-label="Preferred Deliver Method" class="form-control" type="text" id="preferred_deliver_method" name="preferred_deliver_method" value="<?php echo $vendordetail['preferred_deliver_method']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-4" style="margin-bottom: 2rem;">
                        <span class="form-label">Opening Blance</span>
                        <div class="kt-input-icon">
                            <input aria-label="Opening Balance" class="form-control text-right" type="text" id="opening_balance" name="opening_balance" value="<?php echo $vendordetail['opening_balance']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-2" style="margin-bottom: 2rem;">
                        <span class="form-label">As of</span>
                        <div class="kt-input-icon">
                            <input aria-label="Asof" class="form-control datepicker" type="text" id="asof" name="asof" value="<?php echo fromDate($vendordetail['asof']); ?>" >
                        </div>
                    </div>
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">The Vendor Pays Me With</span>
                        <div class="kt-input-icon">
                            <input aria-label="Pay With Me" class="form-control" type="text" id="pay_with_me" name="pay_with_me" value="<?php echo $vendordetail['pay_with_me']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-4" style="margin-bottom: 2rem;">
                        <span class="form-label">Credit Limit</span>
                        <div class="kt-input-icon">
                            <input aria-label="Credit Limit" class="form-control text-right" type="text" id="credit_limit" name="credit_limit" value="<?php echo $vendordetail['credit_limit']; ?>" >
                        </div>
                    </div>

                </div>
            </div>
            <div class="tab-pane" id="sale-discount" role="tabpanel">
                <div class="form-group row">
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">Default Discount Percentage (%)</span>
                        <div class="kt-input-icon">
                            <input aria-label="Default Discount Perc" class="form-control text-right" type="text" id="default_discount_perc" name="default_discount_perc" value="<?php echo $vendordetail['default_discount_perc']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-6" style="margin-bottom: 2rem;">
                        <span class="form-label">Payment Discount Percentage (%)</span>
                        <div class="kt-input-icon">
                            <input aria-label="Payment Discount Perc" class="form-control text-right" type="text" id="payment_discount_perc" name="payment_discount_perc" value="<?php echo $vendordetail['payment_discount_perc']; ?>" >
                        </div>
                    </div>
                    <div class="col-lg-6 w-full" style="margin-bottom: 2rem;">
                        <span class="form-label">Sales Type</span>
                        <select aria-label="Sales Type" name="sales_type" class="form-control form-select js-example-basic-single">
                            <option value="">Select Any One</option>
                            <?php foreach($salestype as $key=>$st) { ?>
                                <option value="<?php echo $key; ?>" <?php echo $vendordetail['sales_type']==$key?'selected':'';?> ><?php echo $st; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="tab-pane" id="agreements" role="tabpanel">
                <div class="p-3">
                    <h4 class="mb-10 font-weight-bold text-dark">Agreemets</h4>
                    <div class="row">
                        <div class="form-group col-lg-12">
                            <table class="table table-bordered " id="edutable">

                                <tr>
                                    <th scope="col" class="" style="width: 2%;">Sl NO</th>
                                    <th scope="col" class="" style="width: 25%;">Name</th>
                                    <th scope="col" class="thheader" style="width: 20%;">Number</th>
                                    <th scope="col" class="" style="width: 20%;"> Sign Date</th>
                                    <th scope="col" class="thheader" style="width: 20%;">Expiry Date </th>
                                    <th scope="col" class="thheader" style="width: 15%;">Files</th>
                                </tr>

                            </tr>
                            <?php $k=0;  if(count((array)$aggrements) != '0'){ $aggrements[] = array('agreement_name'=>'');
}

                            foreach ($aggrements as $inst) { $k++;?>
                                <tr class="rowcount">

                                    <td><?php echo $k; ?>

                                    <input type="hidden" name="ag_rowid[]" class="form-control itemRow" value="<?php echo $inst['id']; ?>">
                                </td>

                                <td><input aria-label="Ag Type[]" type="text" name="ag_type[]" class="form-control" value="<?php echo $inst['agreement_name']; ?>" autocomplete="off">  </td>
                                <td>
                                    <input aria-label="Ag Number[]" type="text" name="ag_number[]" class="form-control" value="<?php echo $inst['agreement_ref_no']; ?>" autocomplete="off">
                                </td>
                                <td><input aria-label="Ag Sin Date[]" type="text" name="ag_sin_date[]" class="form-control datepicker "  id="kt_datepicker_3<?php echo $k; ?>" value="<?php echo fromDate($inst['signed_date']); ?>"  autocomplete="off">  </td>
                                <td><input aria-label="Ag Ex Date[]" type="text" name="ag_ex_date[]" class="form-control datepicker" id="kt_datepicker_3_2<?php echo $k; ?>"  value="<?php echo fromDate($inst['expiry_date']); ?>" autocomplete="off">  </td>
                                <td>
                                    <div class="row m-t-15">
                                        <div class="image-input col-lg-3" id="kt_image_5" style="display: inherit; margin-top: -2px;">
                                            <label class="btn-hover-text-primary" data-action="change" data-toggle="tooltip" title="" data-original-title="Upload" >
                                                <i class="ti-cloud-up f-20 text-primary f-30 f-w-800" data-toggle="tooltip" title="Upload"></i>
                                                <input aria-label="Agreement File[]" type="file" name="agreement_file[]" />
                                                <input type="hidden" name="hidden_agreement_file[]" value="<?php echo $inst['file_url'][$k]; ?>"/>
                                            </label>
                                        </div>
                                        <?php if($inst['file_url']!=''){ ?>
                                            <div class="col-lg-3">
                                                <a href="<?php echo $File_Uploads_Path."/".$inst['file_url']; ?>" data-toggle="tooltip" title="Download" target="_blank" >
                                                    <i class="ti-cloud-down f-20 text-warning f-30 f-w-800"></i>
                                                </a></div>
                                            <?php } ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php }  ?>
                        </table>
                        <div class="row">
                            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">


                                <button class="btn btn-success float-left" onclick="addedurows()" id="addRows" type="button">+ Add More</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="attachments" vendordetail="tabpanel">
            <div class="form-group row">
                <div class="col-lg-4 col-md-9 col-sm-12">
                    <div class="dropzone dropzone-default dropzone-brand dz-clickable" id="kt_dropzone_2">
                        <div class="dropzone-msg dz-message needsclick">
                            <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                            <span class="dropzone-msg-desc">Upload up to 10 files</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="employees" vendordetail="tabpanel">
            <div class="card-body">
                <?php
                if(count((array)$emplist) != 0){
                    $i=1; ?>
                                        <table class="table table-bordered" id="kt_datatable1">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Email Id</th>
                                                    <th scope="col">Billable Status</th>
                                                    <th scope="col">Esgin transaction</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach($emplist as $emplist){ ?>
                                                    <tr>
                                                        <td><?php echo $i; ?></td>
                                                        <td><?php echo $emplist['display_name']; ?> </td>
                                                        <td><?php echo  $emplist['email_id']; ?> </td>
                                                        <td><?php echo $emplist['billable']==0? "Billable":"Non Billable"; ?></td>
                                                        <td><?php echo $emplist['transaction_number']; ?> </td>
                                                    </tr>
                                                    <?php $i++;
} ?>
                                                </tbody>
                                            </table>
                                        <?php }else{ ?>

                                            <div class="card-px text-center ">
                                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Employees</p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <!--end: Datatable-->
                                    </div>
                                </div>
                                <div class="tab-pane" id="no-hiring" role="tabpanel">
                                    <h4 class="mb-10 font-weight-bold text-dark">No Hiring clause</h4>
                                    <div class="form-group row">
                                        <div class="col-lg-2 "></div>
                                        <div class="col-lg-4 col-md-6 col-sm-12 row mt-3">
                                            <div class="col-7">
                                                <span class="switch switch-outline switch-icon switch-info">
                                                    <label>
                                                        <input aria-label="Checkbox" type="checkbox" onclick="checkboxclick()" <?php if($vendordetail['hiring_clause']==0){ echo "checked";
} ?> id="hiring_clause" name="hiring_clause" class="js-switch" onkeydown="">
                                                        <span></span>
                                                    </label>
                                                </span>
                                                <span class="col-form-label">No Hiring Clause </span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-12 coolingmonth">
                                            <span class="form-label">Cooling Period of Months</span>
                                            <div class="kt-input-icon">
                                                <input aria-label="Cooling Months" class="form-control"   type="text" id="cooling_months" name="cooling_months"   value="<?php echo $vendordetail['cooling_months']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 "></div>
                                    </div>
                                </div>
        </div>
        <button  class="btn btn-primary m-l-20 m-b-20"><i class="ti-check"></i>
            Update</button>
    </form>
    </div>

<?php }else{ ?>
<div class="row">
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Basic Info</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">Company Name</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['company']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Vendor Type</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendor_type[$vendordetail['vendor_type']]); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Title</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['title']); ?></td>
                        </tr>

                        <tr>
                            <th scope="row" class="p-2">Name</th>
                            <td class="p-2"><?php echo $vendordetail['first_name'].$vendordetail['middle_name']." ".$vendordetail['last_name']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Contact</th>
                            <td class="p-2"><?php echo $vendordetail['phone']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Email</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['email']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Phone</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['phone']); ?></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Tax Info</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">GST Registration type</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['gsttype']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">GSTIN</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['gstin']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Tax Reg.No.</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_tax_reg_no']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">CST Reg.No</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['cst_reg_no']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">PAN</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['pan_no']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Shipping Details</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">Shipping  Adderss</th>
                            <td class="address-basic p-2"><?php if($vendordetail['shipping_address']!=""){ echo trim($vendordetail['shipping_address']).",".$vendordetail['shipping_city'];
}else{ echo "-";
} ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Country</th>
                            <td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['shipping_country']]); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">State</th>
                            <td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['shipping_state']]); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">City</th>
                            <td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['shipping_city']]); ?></td>
                        </tr>

                        <tr>
                            <th scope="row" class="p-2">Pincode</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['shipping_post_code']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Billing Adderss</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">Billing Adderss</th>
                            <td class="address-basic p-2"><?php echo $vendordetail['billing_address'].",".$vendordetail['billing_city']; ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Country</th>
                            <td class="p-2"><?php echo emptytextdashShow($countrylist[$vendordetail['billing_country']]); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">State</th>
                            <td class=" p-2"><?php echo emptytextdashShow($stateslist[$vendordetail['billing_state']]); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">City</th>
                            <td class="p-2"><?php echo emptytextdashShow($citieslist[$vendordetail['billing_city']]); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Pincode</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['billing_post_code']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Contact Person</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['contact_person']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Contact Person Mobile</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['mobile']); ?></td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Payment & Billing</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">Preferred Payment Method</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['pref_payment_method']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Payment Trems</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['payment_terms']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Preferred Delivery Method</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['preferred_deliver_method']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Opening Blance</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['opening_balance']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">The Customer Pays Me With</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['pay_with_me']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Credit Limit</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['credit_limit']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Sale & Discount</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">Default Discount Percentage</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['default_discount_perc']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Payment Discount Percentage</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['payment_discount_perc']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Sales Type</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['sales_type']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 col-xl-6">
        <div class="table-responsive">
            <div class="card-header"><h5 class="text-primary">Notes</h5></div>
            <div class="card-body">
                <table class="table m-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="p-2">PO Notes</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_po_notes']); ?></td>
                        </tr>
                        <tr>
                            <th scope="row" class="p-2">Other Notes</th>
                            <td class="p-2"><?php echo emptytextdashShow($vendordetail['vendor_other_notes']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php if($transaction_table == "vendors"){ ?>
    <div class="col-lg-12 col-xl-6">
        <div class="row">
            <div class="col-lg-12">
                <div class="table-responsive">
                    <div class="card-header"><h5 class="text-primary">Agreements</h5></div>
                    <div class="card-body">
                        <?php if(count((array)$aggrements)>0){  ?>
                            <table class="table table-hover m-b-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Type</th>
                                        <th scope="col">Number</th>
                                        <th scope="col">Sign Date</th>
                                        <th scope="col">Expiry Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $k=0;
                                    foreach ($aggrements as $inst) { $k++;?>
                                        <tr>
                                            <td><?php echo $inst['agreement_name']; ?></td>
                                            <td><?php echo $inst['agreement_ref_no']; ?></td>
                                            <td><?php echo $inst['signed_date']; ?></td>
                                            <td><span class="label label-success"><?php echo $inst['expiry_date']; ?></span></td>
                                        </tr>
                                    <?php }  ?>
                                </tbody>
                            </table>
                        <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Aggrements</p>
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
<?php } ?>
</div>
<?php } ?>
