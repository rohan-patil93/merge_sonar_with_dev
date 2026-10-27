<?php
$url = API."?mode=getorganizationgoals&appraisal_id=".$performance_appraisal_setup_id;
$orggoarcall = callAPI($url);
$result = json_decode($orggoarcall,1);
$organization_goals = $result['organization_goals'];
$location_list = $result['location_list'];
$department_list = $result['department_list'];
$designation_list = $result['designation_list'];

?>
 <script type="text/javascript">
     function getOrganizationGoals(loc_id,dep_id,des_id){
         var appraisal_id = '<?php echo $performance_appraisal_setup_id; ?>';
         jQuery.ajax({
            url: "views/custom_view_kra_taggings_popup.php",
            data:'loc_id='+loc_id+'&dep_id='+dep_id+'&des_id='+des_id+"&appraisal_id="+appraisal_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
            }
        });
     }
 </script>
<div class="row">
    <?php foreach($organization_goals as $og){ ?>
    <div class="col-lg-2 cursor-pointer" onclick="getOrganizationGoals('<?php echo $og['location_id']; ?>','<?php echo $og['department_id']; ?>','<?php echo $og['job_title_id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
        <div class="card">
            <div class="card-body">
                <p  class="text-facebook f-w-700 f-12 mb-0 text-capitalize"><?php echo $location_list[$og['location_id']]['location_name']; ?></p>
                <p class="f-10 mb-0 text-capitalize"><?php echo $department_list[$og['department_id']]['department_name']; ?></p>
                <p class="f-10 mb-0 text-capitalize"><?php echo $designation_list[$og['job_title_id']]['designation_name']; ?></p>
            </div>
        </div>
    </div>
<?php } ?>
</div>
