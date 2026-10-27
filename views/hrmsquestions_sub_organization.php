<?php

if(isset($_POST['organization_reset'])){
  $organization_name = "";
  $domain_name = "";
  $contact_person = "";
  $address = "";
  $contact_number = "";
  $email_id = "";
}

  $organization_name = "";
  $domain_name = "";
  $contact_person = "";
  $address = "";
  $contact_number = "";
  $email_id = "";

if(isset($_POST['organization_submit'])){
  $organization_name = $_POST['organization_name'];
  $domain_name = $_POST['domain_name'];
  $contact_person = $_POST['contact_person'];
  $address = $_POST['address'];
  $contact_number = $_POST['contact_number'];
  $email_id = $_POST['email_id'];

  echo $organization_name.$domain_name.$contact_number.$contact_person.$email_id.$address;
}

?>
<div class="card border-top-0">
 <div class="card-header">
   <form class="form" method="post" action="">
     <div class="card-body">
      <h3 class="font-size-lg text-dark font-weight-bold mb-6">1. Organization Details:</h3>
      <div class="mb-15">
       <div class="form-group row">
        <span class="col-lg-3 col-form-label text-right">Name of the orgnization:</span>
        <div class="col-lg-6">
         <div class="input-group input-group-lg">
           <div class="input-group-prepend"><span class="input-group-text"><i class="la la-building"></i></span></div>
           <input aria-label="Organization Name" type="text" name="organization_name" class="form-control" value="<?php echo $organization_name; ?>" placeholder=""/>
         </div>

       </div>
     </div>
     <div class="form-group row">
      <span class="col-lg-3 col-form-label text-right">Sub domain:</span>
      <div class="col-lg-6">

       <div class="input-group-append">

         <input aria-label="Domain Name" type="text" name="domain_name" class="form-control" value="<?php echo $domain_name; ?>" placeholder=""/>
         <span class="input-group-text">.com</span>
       </div>

     </div>
   </div>
   <div class="form-group row">
    <span class="col-lg-3 col-form-label text-right">Address:</span>
    <div class="col-lg-6">
     <div class="input-group input-group input-group-lg">
      <div class="input-group-prepend">
       <span class="input-group-text">
        <i class="la la-map-marker"></i>
      </span>
    </div>
    <textarea aria-label="Address" name="address" value="<?php echo $address; ?> " class="form-control form-control-lg" > </textarea>
  </div>
</div>
</div>
</div>

<h3 class="font-size-lg text-dark font-weight-bold mb-6">2. Contact Details:</h3>
<div class="mb-3">

  <div class="form-group row">
    <span class="col-lg-3 col-form-label text-right">Contact Person:</span>
    <div class="col-lg-6">
     <div class="input-group input-group-lg">
       <div class="input-group-prepend"><span class="input-group-text"><i class="la la-user"></i></span></div>
       <input aria-label="Contact Person" type="text" name="contact_person" value="<?php echo $contact_person; ?>" class="form-control" placeholder=""/>
     </div>

   </div>
 </div>
 <div class="form-group row">
  <span class="col-lg-3 col-form-label text-right">Email address:</span>
  <div class="col-lg-6">
   <div class="input-group input-group input-group-lg">
    <div class="input-group-prepend">
     <span class="input-group-text">
      <i class="la la-at"></i>
    </span>
  </div>
  <input aria-label="Input field" type="text" value="<?php echo $email_id; ?>" class="form-control form-control-lg" name="email_id" value="" />
</div>
</div>
</div>
<div class="form-group row">
  <span class="col-lg-3 col-form-label text-right">Contact Number:</span>
  <div class="col-lg-6">
   <div class="input-group">
    <div class="input-group-prepend"><span class="input-group-text"><i class="la la-chain"></i></span></div>
    <input aria-label="Contact Number" type="text" class="form-control" name="contact_number" value="<?php echo $contact_number; ?>" placeholder="Phone number"/>
  </div>
</div>
</div>

</div>

</div>
<div class="card-footer">
  <div class="row">
   <div class="col-lg-3"></div>
   <div class="col-lg-6">
    <input type="submit" name="organization_submit"  class="btn btn-success mr-2">
    <input type="submit" name="organization_reset" class="btn btn-success mr-2">

  </div>
</div>
</div>
</form>
</div>
</div>
