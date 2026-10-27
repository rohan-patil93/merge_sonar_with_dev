  <script>
   function convertToYearRent(){
       var start_month_date  = $("#start_month_date").val();
       var end_month_date  = $("#end_month_date").val();
       var monthly_rent = $("#monthly_rent").val();
       if(monthly_rent !=""){
           var monthcnt = monthDiff(new Date(start_month_date), new Date(end_month_date))+1;
           var yearly = monthly_rent*monthcnt;
           console.log(yearly);
           $("#annual_rent").val(yearly);
           calculationmetrononmetro();
       }
   }
   function calculationmetrononmetro(){

        var hr_p = $("#metro_percentage").val();
       var house_rent = $("#annual_rent").val();
       var basic_salary = $("#Basicwithda_amount").val();
       var ex_rent = 0;
       var ch_ex_rent  = (basic_salary * hr_p);
       var ch1_ex_rent  = house_rent - (basic_salary * 0.10);
       if(ch_ex_rent  > ch1_ex_rent){
           ex_rent = ch1_ex_rent;
       }else{
           ex_rent = ch_ex_rent;
       }
       var display_amonut  = ex_rent > 0?ex_rent:"0.00";
       console.log(ch1_ex_rent+" - "+ch_ex_rent+"- "+basic_salary+" - "+hr_p+"- "+house_rent+" - "+ex_rent+" - "+display_amonut);
       $(".METRONOTMETROAMT").val(display_amonut);
   }
   function monthDiff(dateFrom, dateTo) {
       return dateTo.getMonth() - dateFrom.getMonth() + (12 * (dateTo.getFullYear() - dateFrom.getFullYear()))
   }
   function checkpan(){
       var landlord_pan_flag = $("input[name='landlord_pan_flag']:checked").val();
       if(landlord_pan_flag == '0'){
           $(".landlord_pan_number").show("slow");
       }else{
           $(".landlord_pan_number").hide("slow");
       }
   }

  </script>
  <div class="card">
      <div class="card-header mb-3">
        <h5>House on Rent</h5>
      </div>
      <div id="collapseOne5" class="collapse show" data-parent="#accordionExample5">
         <div class="card-body">
            <form method="post" action="#">
                    <input type="hidden" name="version" value="<?php echo $_GET['version_no']; ?>">
                    <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
                    <input type="hidden" name="assessment_year" value="<?php echo $_GET['assessment_year']; ?>">
               <div class="form-group row">
                  <div class="form-group col-lg-6">
                     <span>From Date</span>
                     <div class="input-group date">
                        <?php   $date_pic = $emphousedata['start_date'] !=''? $emphousedata['start_date'] : nowDate(); ?>
                        <input aria-label="Input field" type="month" class="form-control"  value="<?php echo  date("Y-m",strtotime($date_pic)); ?>"  onchange="convertToYearRent()" name="start_date"  id="start_month_date" />

                     </div>
                  </div>
                  <div class="form-group  col-lg-6">
                     <span>To Date</span>
                     <div class="input-group date">
                        <?php $date_pic = $emphousedata['end_date'] !=''? $emphousedata['end_date'] : nowDate(); ?>
                        <input aria-label="Input field" type="month" class="form-control"  value="<?php echo  date("Y-m",strtotime($date_pic)); ?>"  onchange="convertToYearRent()" name="end_date"  id="end_month_date" />

                     </div>
                  </div>
                  <div class="form-group col-lg-6">
                     <span>Monthly Rent</span>
                     <input aria-label="Monthly Rent" type="text" class="form-control text-right" name="monthly_rent" id="monthly_rent" onblur="convertToYearRent()" value="<?php echo $emphousedata['monthly_rent']; ?>" />
                  </div>
                  <div class="form-group  col-lg-6">
                     <span>Annual Rent</span>
                     <input aria-label="Input field" type="text" class="form-control text-right" readonly value="<?php echo $emphousedata['annual_rent']; ?>"  id="annual_rent" name="annual_rent"   />
                  </div>
                  <div class="form-group  col-lg-6">
                     <span>Adderss</span>
                     <textarea aria-label="Address" class="form-control" name="address"><?php echo $emphousedata['address']; ?></textarea>
                  </div>
                  <div class="form-group  col-lg-6">
                     &nbsp;
                  </div>
                  <div class="form-group  col-lg-6">
                     <span>Landlord Name</span>
                     <input aria-label="Input field" type="text" class="form-control"  value="<?php echo $emphousedata['lanlord_name']; ?>"  name="lanlord_name"  />
                  </div>
                  <div class="form-group  col-lg-6">
                     <span>Landlord Adderss</span>
                     <textarea aria-label="Landlord Address" class="form-control" name="landlord_address"><?php echo $emphousedata['landlord_address']; ?></textarea>
                  </div>
                  <div class="form-group  col-lg-6">

                     <span>Does You Landlord has PAN?</span></br>
                     <div class="radio-inline">
                     <label class="radio"> <input aria-label="Radio option" type="radio"  <?php if($emphousedata['landlord_pan_flag']=='0'){ echo "checked";
} ?>  name="landlord_pan_flag" value='0' onchange="checkpan()">   <span></span>Yes </label>
                     <label class="radio"> <input aria-label="Radio option" type="radio" <?php if($emphousedata['landlord_pan_flag']=='1'){ echo "checked";
} ?>  name="landlord_pan_flag" value='1' onchange="checkpan()">   <span></span>No </label></div>
                  </div>
                  <div class="form-group  col-lg-6 landlord_pan_number" <?php if($emphousedata['landlord_pan_flag']=='1'){ ?> style="display:none" <?php
} ?>>

                     <span>PAN Number</span>
                     <input aria-label="Input field" type="text" class="form-control"  value="<?php echo $emphousedata['landlord_pan_number']; ?>"  name="landlord_pan_number"  />
                  </div>
               </div>
               <?php  if($viewfilemanger!="" ){
               $file_array = array('application_table_name'=>'emp_it_declaration_houserent','transaction_id'=>$emphousedata['id'] ,'is_active'=>'0','width'=>$file_data['fileuplodingwidth']!=""?$file_data['fileuplodingwidth']:'6','deletewidth'=>$file_data['fileuplodingdleetewidth']!=""?$file_data['fileuplodingdleetewidth']:'6','deleteview'=>$file_data['fileuplodingdeleteddisplay']!=""?$file_data['fileuplodingdeleteddisplay']:1,'file_size'=>$file_data['file_size']!=""?$file_data['file_size']:'6','file_limit'=>$file_data['number_of_files']!=""?$file_data['number_of_files']:'6','lable'=>$file_data['fileuplodinglabel']);
               fileUplodingUi($file_array);
               } ?>
               <div class="form-group  col-lg-12 mb-5">
                      <input type="hidden" value="<?php echo $emphousedata['id']; ?>" name="house_id">
                      <input type="hidden" value="<?php echo $assessment_year; ?>" name="assessment_year">
                      <input type="hidden" value="<?php echo $version; ?>" name="version">
                     <button type="submit" name="AddExemption" class="btn btn-primary waves-effect waves-light float-right" value="Submit"/>Submit</button>

               </div>
            </form>
         </div>
      </div>
   </div>

