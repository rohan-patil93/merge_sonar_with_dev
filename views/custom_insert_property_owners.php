<?php
$_SESSION['apply_js']['multiselest'] = "1";

if(isset($_POST['submitesopplanse'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    foreach($_POST['old_attachment'] as $rowid=>$imgdetail){
        foreach($imgdetail as $key=>$image){
            $attachedfiles = array();
            $attachedfiles['attachment'] = $_POST['old_attachment'][$rowid][$key];
            $attachedfiles['attachment_name'] = $_POST['old_attachment_name'][$rowid][$key];
            $_POST['all_attachment'][$rowid][] = $attachedfiles;
        }
    }
    foreach($_FILES['attachment']['name'] as $rowid=>$imgdetail){
        foreach($imgdetail as $key=>$image){
            if($_FILES['attachment']['error'][$rowid][$key] != 4){
                $temp = explode(".", basename($_FILES['attachment']['name'][$rowid][$key]));
                $Filename=  "file_".time()."_".uniqid();
                $newfilename = $Filename.'.' .end($temp);
                $attachedfiles = array();
                $attachedfiles['attachment'] = $newfilename;
                $attachedfiles['attachment_name'] = $_POST['attachment_name'][$rowid][$key];
                $_POST['all_attachment'][$rowid][] = $attachedfiles;
                move_uploaded_file($_FILES['attachment']['tmp_name'][$rowid][$key], 'uploads/all/'.$newfilename);
            }
        }
    }
      $url = API."?mode=insertpropertyowners&method=post";
       $response1 = file_post_contents($url, $_POST, 'insert');
       $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
        }
            ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php

    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getpropertyowners&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$property_types = $result['property_types'];
$property_owners = $result['property_owners'];
$banks = $result['banks'];
$property_owner_details = $result['property_owner_details'];
$property_owner_detail_areas = $result['property_owner_detail_areas'];
$property_owners_list = $result['property_owners_list'];
$rentaluom = $result['rentaluom'];
$account_types = $result['account_types'];
$property_owner_detail_areas_ary = array();
foreach($property_owner_detail_areas as $poda){
    $property_owner_detail_areas_ary[$poda['property_owner_detail_id']][] = $poda;
}
foreach($property_types as $pt){
    if($pt['parent_id'] =='0'){
        $propertyparenttypes[$pt['id']] = $pt;
    }else{
        $propertychildtypes[$pt['parent_id']][] = $pt;
    }
}
?>
<script type="text/javascript">
    function getPropertParentType(typeid,rowid){
          $(".removeselected_"+rowid).removeClass("bg-success");
           $(".removeselected_"+rowid).removeClass("text-white");
           $("#propertyparenttype_"+rowid+"_"+typeid).addClass("bg-success");
          $("#propertyparenttype_"+rowid+"_"+typeid).addClass("text-white");
          $("#property_type_"+rowid).val(typeid);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=getpropertytypes&property_type_id='+typeid,
            type: "POST",
            success:function(data){
              console.log(data);
              $("#property_sub_type_"+rowid).html(data);
            },
            error:function (){}
        });
    }

    function deletRow(id,rowid){
        $("#deleterowid_"+id).remove();
    }
    function deletCard(id){
        $(".card_id_"+id).remove();
    }
    function removeFile(rowid){
        $("#"+rowid).remove();
    }
    var maxrows = 10000;
  function addMore(rowid){
    maxrows = maxrows+1;
    var html = '';
    html += '<div class="row" id="deleterowid_'+maxrows+'">';
    html += '<div class="form-group col-lg-4 p-1"><input aria-label="Property Area Name[" type="text" class="form-control" name="property_area_name['+rowid+'][]" ></div>';

    html += '<div class="form-group col-lg-2 p-1"><input aria-label="Property Area[" type="text" class="form-control" name="property_area['+rowid+'][]" value="0"></div>';

    html += '<div class="form-group col-lg-3 p-1"><select aria-label="Uom[" name="uom['+rowid+'][]"  class="form-control form-select js-example-basic-single" required><?php foreach($rentaluom as $uomkey=>$uomname){?><option value="<?php echo $uomkey; ?>" ><?php echo $uomname;?></option><?php
} ?></select></div>';

    html += '<div class="form-group col-lg-2 p-1"><input aria-label="Per Sq Ft Rate[" type="text" class="form-control" name="per_sq_ft_rate['+rowid+'][]" value="0"></div>';
    html += '<div class="form-group col-lg-1 p-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRow('+maxrows+','+rowid+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>';
    html += '</div>';
    $("#addmorediv_"+rowid).append(html);
    $(".js-example-basic-single").select2();
    $('[data-bs-toggle="tooltip"]').tooltip();
  }
  var maxrowid = 50000;
  function addMoreCard(){
      maxrowid = maxrowid+1;
      maxrows = maxrows+1;
      var html = '';
      html += '<a class="accordion-msg waves-effect waves-dark card_id_'+maxrowid+'"><div class="row"><div class="col-lg-11"><span class="text-capitalize">New Card</span></div><div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletCard('+maxrowid+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Delete Details" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div></a><div class="accordion-desc p-0">';
      html += '<div class="card card_id_'+maxrowid+'" ><input type="hidden" name="property_owner_detail_id['+maxrowid+']" value=""><div class="card-body"><div class="row">';
    html += '<div class="col-lg-5" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><div class="row"><div class="form-group col-lg-12 mb-0"><span class="form-label">Property Type<span class="text-danger">*</span></span><div class="row"><input type="hidden" name="property_type['+maxrowid+']" id="property_type_'+maxrowid+'" value=""><?php foreach($propertyparenttypes as $ppt){?><div class="col-lg-6" onclick="getPropertParentType(<?php echo $ppt['id']; ?>>,'+maxrowid+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><div class="card text-center removeselected_'+maxrowid+'" id="propertyparenttype_'+maxrowid+'_<?php echo $ppt['id']; ?>"><div class="card-body"><span class="d-block f-22"><?php echo $ppt['icon'];?></span><span><?php echo $ppt['property_type_name']; ?></span></div></div></div><?php
} ?></div></div>';
    html += '<div class="form-group col-lg-12"><span class="form-label">Sub Property Type<span class="text-danger">*</span></span><select aria-label="Property Sub Type[" name="property_sub_type['+maxrowid+']" id="property_sub_type_'+maxrowid+'" class="form-control form-select js-example-basic-single" required><option value="">Select Any One </option><?php foreach($propertychildtypes[$pod['property_type']] as $pct){?><option value="<?php echo $pct['id']; ?>"><?php echo $pct['property_type_name'];?></option><?php
} ?></select></div>';
    html += '<div class="form-group col-lg-12"><span class="form-label">Property Name<span class="text-danger">*</span></span><input aria-label="Property Name[" type="text" class="form-control text-capitalize" name="property_name['+maxrowid+']" value="" required></div>';
    html += '<div class="form-group col-lg-12"><span class="form-label">Address<span class="text-danger">*</span></span><textarea aria-label="Property Address[" class="form-control text-capitalize" name="property_address['+maxrowid+']" ></textarea></div>';

    html += '<div class="form-group col-lg-6"><span class="form-label">Longitude </span><input aria-label="Longitude[" type="text" class="form-control" name="longitude['+maxrowid+']" value=""></div>';
    html += '<div class="form-group col-lg-6"><span class="form-label">Latitude</span><input aria-label="Latitude[" type="text" class="form-control" name="latitude['+maxrowid+']" value=""></div>';

    html += '</div>';

    html += '<div class="form-group col-lg-12">';
    html += '<div class="row"><div class="col-lg-6"><span class="form-label">File Name</span></div><div class="col-lg-6"><span class="form-label">Upload File</span></div></div>';
    html += '<div id="addmoreattachements_id_'+maxrowid+'"><div class="row"><div class="form-group col-lg-6"><input aria-label="Attachment Name[" type="text" class="form-control" name="attachment_name['+maxrowid+'][]" value=""></div><div class="form-group col-lg-6"><input aria-label="Attachment[" type="file" class="form-control" name="attachment['+maxrowid+'][]" value=""></div></div></div>';
    html += '<div class=""> <button type="button" class="btn btn-success waves-effect waves-light" onclick="addMoreAttachment(\''+maxrowid+'\')"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More Attachment</button></div>';
    html += '</div>';

    html += '<div class="row"><div class="form-group col-lg-8"><span>Join Owners</span></div><div class="form-group col-lg-3"><span>Percentage(%)<span class="text-danger">*</span></span> </div></div>';

    html += '<div id="addmoredivowner_'+maxrowid+'">';

    var deleteid = ""+maxrowid+"deleteownerid_"+maxrows+"";

    html += '<div class="row" id="'+deleteid+'">';
    html += '<div class="form-group col-lg-8"><input aria-label="Joint Owner Names[" type="text" class="form-control" name="joint_owner_names['+maxrowid+'][]" value="<?php echo $property_owners['owner_name']; ?>" readonly></div>';
    html += '<div class="form-group col-lg-3"><input aria-label="Owner Percentage[" type="text" class="form-control  text-right" name="owner_percentage['+maxrowid+'][]" value="100"></div>';
    html += '<div class="form-group col-lg-1"></div>';
    html += '</div>';
    html += '</div>';

    html += '<div class=""> <button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMoreOwners(\''+maxrowid+'\')"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More Owner</button></div>';
    html += '</div>';
    html += '<div class="col-lg-7"><div class="row"><div class="form-group col-lg-4"> <span>Property Rental Name<span class="text-danger">*</span></span></div><div class="form-group col-lg-2"><span>Area Sq. Ft <span class="text-danger">*</span></span> </div><div class="form-group col-lg-3"><span>UOM <span class="text-danger">*</span></span> </div><div class="form-group col-lg-2"><span>rate Per Sq. Ft <span class="text-danger">*</span></span> </div></div>';
    html += '<div id="addmorediv_'+maxrowid+'">';
    html += '<div class="row" id="deleterowid__2"+maxrows+'"><input type="hidden" name="property_owner_detail_area_id['+maxrowid+'][]" value=""><div class="form-group col-lg-4 p-1"><input aria-label="Property Area Name[" type="text" class="form-control" name="property_area_name['+maxrowid+'][]" value="" required></div>';
    html += '<div class="form-group col-lg-2 p-1"><input aria-label="Property Area[" type="text" class="form-control " name="property_area['+maxrowid+'][]" value="0" required tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></div><div class="form-group col-lg-3 p-1"><select aria-label="Uom[" name="uom['+maxrowid+'][]"  class="form-control form-select js-example-basic-single" required><?php foreach($rentaluom as $uomkey=>$uomname){?><option value="<?php echo $uomkey; ?>" ><?php echo $uomname;?></option><?php
} ?></select></div><div class="form-group col-lg-2 p-1"><input aria-label="Per Sq Ft Rate[" type="text" class="form-control " name="per_sq_ft_rate['+maxrowid+'][]" value="0" required></div><div class="form-group col-lg-1 p-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRow('+maxrows+','+maxrowid+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div></div></div>';

    html += '<div class=""><button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMore('+maxrowid+')"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More</button> </div></div></div></div></div></div>';

      $(".addmorecard").append(html);
      $(".js-example-basic-single").select2();
    $('[data-bs-toggle="tooltip"]').tooltip();
  }
  maxrowsowner = 2566;
  function addMoreOwners(rowid){
      maxrowsowner = maxrowsowner+1;
      var deleteid = ""+rowid+"deleteownerid_"+maxrowsowner+"";
    var html = '';
    html += '<div class="row" id="'+deleteid+'">';
    html += '<div class="form-group col-lg-8"><input aria-label="Joint Owner Names[" type="text" class="form-control" name="joint_owner_names['+rowid+'][]" value=""></div>';
    html += '<div class="form-group col-lg-3"><input aria-label="Owner Percentage[" type="text" class="form-control  text-right" name="owner_percentage['+rowid+'][]" value=""></div>';
    html += '<div class="form-group col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRowjoinOwner(\''+deleteid+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>';
    html += '</div>';
    $("#addmoredivowner_"+rowid).append(html);
    $('[data-bs-toggle="tooltip"]').tooltip();
  }

function deletRowjoinOwner(rowid){
    $("#"+rowid).remove();
}
var maxrowsattachemets = 36980;
function addMoreAttachment(rowid){
    maxrowsattachemets = maxrowsattachemets+1;
      var deleteid = ""+rowid+"deleteattachementsid_"+maxrowsattachemets+"";
    var html = '';
    html += '<div class="row" id="'+deleteid+'">';
    html += '<div class="form-group col-lg-6"><input aria-label="Attachment Name[" type="text" class="form-control" name="attachment_name['+rowid+'][]" value=""></div>';
    html += '<div class="form-group col-lg-6"><input aria-label="Attachment[" type="file" class="form-control" name="attachment['+rowid+'][]" value=""></div>';
    html += '</div>';
    $("#addmoreattachements_id_"+rowid).append(html);
}

</script>
<form method="post"  enctype="multipart/form-data">
<div class="row">
    <div class="col-lg-2">
        <div class="card">
            <div class="card-header">
                <h5>Owners</h5>
                <div class="card-header-right table-card-header">
                    <?php if($rid !=""){ ?>
                    <a href="index?option=COM_INSERT_PROPERTY_OWNERS" class="btn btn-success waves-effect waves-light float-left"><i class="icofont icofont-plus icon-white  m-r-10"></i> Owner</a>
                    <?php } ?>
                </div>
            </div>
            <div class="card-block custom-scroll" style=" height: 570px;">
                <table class="table table-hover">
                    <thead><tr><th scope="col">Owner Details</th></tr></thead>
                    <tbody>
                <?php foreach($property_owners_list as $pol){
                    $ownerid = deocdeId("id:".$pol['id']);
                    ?>
                    <tr class="<?php echo $pol['id'] == $rid?"bg-info":""; ?>">
                        <th scope="col" class="p-2 w-b-w-s"><a href="index?option=COM_INSERT_PROPERTY_OWNERS&Rid=<?php echo $ownerid; ?>" class="" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Click To Open Owner Details"><?php echo $pol['owner_name']; ?> </a> <span class="form-label label label-primary float-end"><?php echo $pol['total_property_cnt']; ?></span></th>
                    </tr>
                <?php } ?>
                </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="card">
            <div class="card-header">
                <h5>Owner Details</h5>
                <div class="card-header-right table-card-header">
                </div>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <span class="form-label">Owner Name <span class="text-danger">*</span></span>
                    <input aria-label="Owner Name" type="text" class="form-control text-capitalize" name="owner_name" value="<?php echo $property_owners['owner_name']; ?>" required>
                </div>
        <div class="form-group">
          <span class="form-label">Company Name <span class="text-danger">*</span></span>
          <input aria-label="Owner Company Name" type="text" class="form-control text-capitalize" name="owner_company_name" value="<?php echo $property_owners['owner_company_name']; ?>" required>
        </div>
                <div class="form-group">
                    <span class="form-label">Address</span>
                    <textarea aria-label="Address" type="text" class="form-control" name="address" required><?php echo $property_owners['address']; ?></textarea>
                </div>
        <div class="form-group">
          <span class="form-label">Contact Number</span>
          <input aria-label="Contact No" type="text" class="form-control" name="contact_no" value="<?php echo $property_owners['contact_no']; ?>" >
        </div>
        <div class="form-group">
          <span class="form-label">Email Id</span>
          <input aria-label="Email" type="text" class="form-control" name="email_id" value="<?php echo $property_owners['email_id']; ?>" >
        </div>
                <div class="form-group">
                    <span class="form-label">GST No</span>
                    <input aria-label="Gst No" type="text" class="form-control" name="gst_no" value="<?php echo $property_owners['gst_no']; ?>" >
                </div>
        <div class="form-group">
          <span class="form-label">PAN No</span>
          <input aria-label="Pan No" type="text" class="form-control" name="pan_no" value="<?php echo $property_owners['pan_no']; ?>" >
        </div>
                <div class="form-group">
                    <span class="form-label">Bank Name</span>
                    <select aria-label="Bank" name="bank_id" class="form-control form-select js-example-basic-single">
                        <option value="">Select Any One </option>
                        <?php foreach($banks as $bnk) {  ?>
                            <option value="<?php echo $bnk['id']; ?>" <?php echo $property_owners['bank_id'] ==$bnk['id']?"selected":""; ?>><?php echo $bnk['bank_name'];?></option>
                        <?php } ?>
                    </select>
                </div>
        <div class="form-group">
          <span class="form-label">IFSC Code</span>
          <input aria-label="Ifsc Code" type="text" class="form-control" name="ifsc_code" value="<?php echo $property_owners['ifsc_code']; ?>">
        </div>
                <div class="form-group">
                    <span class="form-label">Bank Account No</span>
                    <input aria-label="Bank Account No" type="text" class="form-control" name="bank_account_no" value="<?php echo $property_owners['bank_account_no']; ?>">
                </div>
        <div class="form-group">
          <span class="form-label">Account Type</span>
          <select aria-label="Account Type" name="account_type" class="form-control form-select js-example-basic-single">
            <?php foreach($account_types as $bnktkey=>$bnkval) {  ?>
              <option value="<?php echo $bnktkey; ?>" <?php echo $property_owners['account_type'] ==$bnktkey?"selected":""; ?>><?php echo $bnkval;?></option>
            <?php } ?>
          </select>
        </div>
            </div>
            <div class="card-footer pt-0 mt-3">
                 <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" name="submitesopplanse"> <?php echo $rid !=""?"Update":"Submit";?></button>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <?php if($rid !=""){ ?>
        <div class="card">
            <div class="card-header">
                <h5>Owner Propert Detail</h5>
                <div class="card-header-right table-card-header">

                </div>
            </div>
            <div class="card-body">
                <div class="accordion-block">
                    <div class="accordion-box addmorecard" id="color-accordion">
                    <?php $i=$attchmentseq = 1; $mk = 0;
                    if(count((array)$property_owner_details) == 0){
                        $property_owner_details[] = array();
                    }
                    foreach($property_owner_details as $pod){

                    ?>
                        <a class="accordion-msg waves-effect waves-dark card_id_<?php echo $i; ?>" >
                            <div class="row">
                                <div class="col-lg-11"><span class="text-capitalize"><?php echo $pod['property_name']; ?></span></div>
                                <div class="col-lg-1"><i class="ti-trash text-danger"  onclick="deletCard('<?php echo $i; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Delete Details" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></div>
                            </div>
                        </a>
                        <div class="accordion-desc p-0 card_id_<?php echo $i; ?>">
                            <div class="row" >
                                <div class="card" >
                                    <input type="hidden" name="property_owner_detail_id[<?php echo $i; ?>]" value="<?php echo $pod['id'];?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-5">
                                                <div class="row">
                                                    <div class="form-group col-lg-12 mb-0">
                                                        <span class="form-label">Property Type<span class="text-danger">*</span></span>
                                                        <div class="row">
                                                            <input type="hidden" name="property_type[<?php echo $i; ?>]" id="property_type_<?php echo $i; ?>" value="<?php echo $pod['property_type']; ?>">
                                                            <?php foreach($propertyparenttypes as $ppt){?>
                                                            <div class="col-lg-6" onclick="getPropertParentType('<?php echo $ppt['id']; ?>','<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                                <div class="card text-center <?php echo $ppt['id']==$pod['property_type']?"bg-success text-white":"";?> removeselected_<?php echo $i; ?>" id="propertyparenttype_<?php echo $i; ?>_<?php echo $ppt['id']; ?>">
                                                                    <div class="card-body">
                                                                        <span class="d-block f-22"><?php echo $ppt['icon'];?></span>
                                                                        <span><?php echo ucwords($ppt['property_type_name']); ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <span class="form-label">Sub Property Type<span class="text-danger">*</span></span>
                                                        <select aria-label="Select option" name="property_sub_type[<?php echo $i; ?>]" id="property_sub_type_<?php echo $i; ?>" class="form-control form-select js-example-basic-single" required>
                                                            <option value="">Select Any One </option>
                                                            <?php foreach($propertychildtypes[$pod['property_type']] as $pct){?>
                                                                <option value="<?php echo $pct['id']; ?>" <?php echo $pod['property_sub_type'] ==$pct['id']?"selected":""; ?>><?php echo $pct['property_type_name'];?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <span class="form-label">Property Name<span class="text-danger">*</span></span>
                                                        <input aria-label="Input field" type="text" class="form-control text-capitalize" name="property_name[<?php echo $i; ?>]" value="<?php echo $pod['property_name']; ?>" required>
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                        <span class="form-label">Address<span class="text-danger">*</span></span>
                                                        <textarea aria-label="Text area" class="form-control" name="property_address[<?php echo $i; ?>]" ><?php echo $pod['property_address']; ?></textarea>
                                                    </div>
                                                    <div class="form-group col-lg-6">
                                                        <span class="form-label">Longitude </span>
                                                        <input aria-label="Input field" type="text" class="form-control" name="longitude[<?php echo $i; ?>]" value="<?php echo $pod['longitude']; ?>">
                                                    </div>
                                                    <div class="form-group col-lg-6">
                                                        <span class="form-label">Latitude</span>
                                                        <input aria-label="Input field" type="text" class="form-control" name="latitude[<?php echo $i; ?>]" value="<?php echo $pod['latitude']; ?>">
                                                    </div>
                                                    <div class="form-group col-lg-12">
                                                    <?php
                                                        $attachments = json_decode($pod['attachment'],1);
                                                        foreach($attachments as $attachment){ ?>
                                                            <div class="label-main d-block" id="remove_attache_<?php echo $attchmentseq; ?>">
                                    <a href="uploads/all/<?php echo $attachment['attachment']; ?>" target="_blank"><span class="form-label label label-success cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download"><button type="button" class="btn p-0 border-0 bg-transparent ti-cloud-down" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> &nbsp; <?php echo $attachment['attachment_name'] !=""?$attachment['attachment_name']:$attachment['attachment']; ?></span></a> <i class="ti-trash float-end text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeFile('remove_attache_<?php echo $attchmentseq; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                     <input type="hidden" name="old_attachment[<?php echo $i; ?>][]" class="form-control" value="<?php echo $attachment['attachment']; ?>">
                                     <input type="hidden" name="old_attachment_name[<?php echo $i; ?>][]" class="form-control" value="<?php echo $attachment['attachment_name']; ?>">
                              </div>
                                                    <?php $attchmentseq++;
}  ?>
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <span class="form-label">File Name</span>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <span class="form-label">Upload File</span>
                                                            </div>
                                                        </div>
                                                        <div id="addmoreattachements_id_<?php echo $i; ?>">
                                                            <div class="row">
                                                                <div class="form-group col-lg-6">
                                                                    <input aria-label="Input field" type="text" class="form-control" name="attachment_name[<?php echo $i; ?>][]" value="">
                                                                </div>
                                                                <div class="form-group col-lg-6">
                                                                    <input aria-label="File upload" type="file" class="form-control" name="attachment[<?php echo $i; ?>][]" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="">
                                                            <button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMoreAttachment('<?php echo $i; ?>')"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More Attachment</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                            <div class="form-group col-lg-8">
                                                <span>Join Owners</span>
                                            </div>
                                            <div class="form-group col-lg-3">
                                                <span>Percentage(%)<span class="text-danger">*</span></span>
                                            </div>
                                        </div>
                                        <div id="addmoredivowner_<?php echo $i; ?>">
                                            <?php
                                            $mck= 1;
                                            $property_joint_owner = json_decode($pod['property_joint_owner'],1);
                                                if(count((array)$property_joint_owner) == 0){
                                                    $property_joint_owner[] = array('joint_owner_names'=>$property_owners['owner_name'],'owner_percentage'=>100);
                                                }
                                                   foreach($property_joint_owner as $pjo){
                                                       $deleteid = $i."deleteownerid_".$mck;
                                               ?>
                                            <div class="row" id="<?php echo $deleteid; ?>">
                                                <div class="form-group col-lg-8">
                                                    <input aria-label="Input field" type="text" class="form-control" name="joint_owner_names[<?php echo $i; ?>][]" value="<?php echo $pjo['joint_owner_names']; ?>"<?php if($mck == 1){ echo "readonly";
} ?> >
                                                </div>
                                                <div class="form-group col-lg-3">
                                                   <input aria-label="Input field" type="text" class="form-control text-right" name="owner_percentage[<?php echo $i; ?>][]" value="<?php echo $pjo['owner_percentage']; ?>">
                                                </div>
                                                <div class="form-group col-lg-1">
                                                    <?php if($mck != 1){ ?>
                                                     <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRowjoinOwner('<?php echo $deleteid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php $mck++;
} ?>
                                        </div>
                                        <div class="">
                                            <button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMoreOwners('<?php echo $i; ?>')"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More Owner</button>
                                        </div>
                                            </div>
                                            <div class="col-lg-7">
                                                <div class="row">
                                                    <div class="form-group col-lg-4">
                                                        <span>Property Rental Name<span class="text-danger">*</span></span>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <span>Area<span class="text-danger">*</span></span>
                                                    </div>
                                                    <div class="form-group col-lg-3">
                                                        <span>UOM<span class="text-danger">*</span></span>
                                                    </div>
                                                    <div class="form-group col-lg-2">
                                                        <span>Rate<span class="text-danger">*</span></span>
                                                    </div>
                                                    <div class="form-group col-lg-1">
                                                    </div>
                                                </div>
                                                <div id="addmorediv_<?php echo $i; ?>">
                                                 <?php
                                                     $property_owner_detail_areas_array = $property_owner_detail_areas_ary[$pod['id']];
                                                    if(count((array)$property_owner_detail_areas_array) == 0){
                                                        $property_owner_detail_areas_array[] = array();
                                                    }
                                                   foreach($property_owner_detail_areas_array as $vd){
                                                ?>
                                                    <div class="row" id="deleterowid_<?php echo $mk; ?>">
                                                        <input type="hidden" name="property_owner_detail_area_id[<?php echo $i; ?>][]" value="<?php echo $vd['id']; ?>">
                                                        <div class="form-group col-lg-4 p-1">
                                                           <input aria-label="Input field" type="text" class="form-control" name="property_area_name[<?php echo $i; ?>][]" value="<?php echo $vd['property_area_name']; ?>" required>
                                                        </div>
                                                        <div class="form-group col-lg-2 p-1">
                                                           <input aria-label="Input field" type="text" class="form-control " name="property_area[<?php echo $i; ?>][]" value="<?php echo $vd['property_area']; ?>" required>
                                                        </div>
                                                        <div class="form-group col-lg-3 p-1">
                                                            <select aria-label="Select option" name="uom[<?php echo $i; ?>][]"  class="form-control form-select js-example-basic-single" required>
                                                                <?php foreach($rentaluom as $uomkey=>$uomname){?>
                                                                    <option value="<?php echo $uomkey; ?>" <?php echo $vd['uom'] ==$uomkey?"selected":""; ?>><?php echo $uomname;?></option>
                                                                <?php } ?>
                                                            </select>
                                                </div>
                                                <div class="form-group col-lg-2 p-1">
                                                   <input aria-label="Input field" type="text" class="form-control " name="per_sq_ft_rate[<?php echo $i; ?>][]" value="<?php echo $vd['per_sq_ft_rate']; ?>" required>
                                                </div>
                                                <div class="form-group col-lg-1 p-1">
                                    <?php if($vd['property_tenant_detail_id'] ==""){ ?>
                                                   <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRow('<?php echo $mk; ?>','<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                   <?php }else{ echo "<i class='ti ti-alert text-danger' data-bs-toggle='tooltip' data-placement='bottom' title='' data-bs-original-title='Already Area Available In Rent'></i>";
} ?>
                                                </div>
                                            </div>
                                                <?php  $mk++;
} ?>
                                                </div>
                                                <div class="">
                                                    <button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMore('<?php echo $i; ?>')"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More Property Area</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <?php  $i++; $attchmentseq++;
} ?>
                    </div>
                </div>

                <div class="card-footer pt-0 mt-4">
           <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-r-20" name="submitesopplanse"> <?php echo $rid !=""?"Update":"Submit";?></button>

                    <button type="button" class="btn btn-success waves-effect waves-light float-left" onclick="addMoreCard()"><i class="icofont icofont-plus icon-white  m-r-10"></i>Add More Property</button>
                </div>
            </div>
        </div>
    <?php }else{ ?>
        <div class="card">
            <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Owner Details</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        </div>
    <?php } ?>
    </div>
</div>

</form>
