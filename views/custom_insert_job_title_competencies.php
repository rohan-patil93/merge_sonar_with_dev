<?php
$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$appraisal_id  = $erid['1'];
$assessment_year = $_GET['AY'];
if(isset($_POST['submit_competency_level'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertjobtitlecompetencies&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response,1);
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete"){
                 tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
            }
        }
    </script>
<?php
}
$url = API."?mode=getjobtitlecompetencies&assessment_year=".$assessment_year."&appraisal_id=".$appraisal_id;
$response = callAPI($url);
$resultcompetency = json_decode($response,1);
$competencieslist  = $resultcompetency['competencieslist'];
$jobtitleslist  = $resultcompetency['job_titles'];
$competencylevelslist  = $resultcompetency['competency_levels'];
$performance_setup  = $resultcompetency['performance_setup'];
$competencylevellist = array();
foreach($competencylevelslist as $competencylevelsres){
    $competencylevellist[$competencylevelsres['competency_id']][] = $competencylevelsres;
}
foreach($resultcompetency['job_title_competencies'] as $job_title_competencies){
    $jtcompetencieslist[$job_title_competencies['job_title_id']][$job_title_competencies['competency_id']] = $job_title_competencies;
}

?>
<script type="text/javascript">
    function checkAssemntYeaCopetency(){

        var assemntyearcopetency = $('#assemnt_year_copetency').val();
        window.location.href = 'index.php?option=COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS&AY='+assemntyearcopetency;


    }

    function updateCompetencyLevel(id,value){
        if(id != ""){
            jQuery.ajax({
                url: "ajax.php",
                data:'page=updatejoblevelcompetency&id='+id+'&competency_level_id='+value,
                type: "POST",
                success:function(data){
                     tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');

                }
            });
        }
    }

</script>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid">
        <div class="row j-content">  <!-- j-content -->
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h5>Job Title Competencies</h5>
                        <div class="card-header-right">
                            <?php if($_GET['option'] != "COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS"){ ?>
                     <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                  <i class="icofont icofont-reply"></i>  Back
                     </a>
                    <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">


                    <form method="post">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group mb-2"  >
                                    <span class="form-label">Appraisal Name</span>
                                    <input type="hidden" class="form-control" name="appraisal_id" id="kpiandkra_appraisal_id"  value="<?php echo $appraisal_id ?>">
                                    <input aria-label="Input field" type="text"  class="form-control"  value="<?php echo $performance_setup['name']; ?>" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3 col-xl-3 form-group"  >
                                <span class="form-label">Aassessment Year:</span>
                                <input aria-label="Assessment Year" type="text" class="form-control"  name="assessment_year" id="kpiandkra_assessment_year" value="<?php echo $assessment_year ?>" readonly>
                            </div>
                            <div class="col-lg-3"></div>
                            <div class="col-sm-3 form-group text-right"  >
                            <?php if(count((array)$resultcompetency['job_title_competencies']) =="0"){ ?>
                                <button type="submit" name="submit_competency_level"  class="btn btn-primary mr-2 mt-4"   ><i class="la la-check"></i>Submit Job Competency</button>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="dt-responsive table-responsive" >
                            <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer" >
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-left" style="width: 20%;">Compitency/Job Title</th>
                                        <?php foreach($competencieslist as $competenciesres){ ?>
                                            <th scope="col" class="text-center f-14">
                                                <span class="mytooltip tooltip-effect-1">
                                                    <span class="tooltip-item2"><?php echo $competenciesres['competency_name']; ?></span>
                                                      <span class="tooltip-content4 clearfix">
                                                          <span class="tooltip-text2">
                                                            <strong><?php echo $competenciesres['competency_name']; ?></strong>
                                                        </span>
                                                    </span>
                                                </span>
                                            </th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($jobtitleslist as $jobtitlesres){?>
                                    <tr>
                                        <td class="text-left f-14"><?php echo $jobtitlesres['job_title'];?></td>
                                        <?php foreach($competencieslist as $competenciesres){?>
                                            <td class="text-center w-full">

                                                <?php if(count((array)$competencylevellist[$competenciesres['id']]) !='0'){ $competecylid = $jtcompetencieslist[$jobtitlesres['id']][$competenciesres['id']]; ?>
                                                <select aria-label="Select option" class="form-control form-select js-example-basic-single" name="competency_level[<?php echo $jobtitlesres['id']; ?>][<?php echo $competenciesres['id']; ?>]"  onchange="updateCompetencyLevel('<?php echo $competecylid['id']; ?>',this.value)">
                                                    <option value="">Select Level</option>
                                                    <?php foreach ($competencylevellist[$competenciesres['id']] as  $competencyleveres) { ?>
                                                        <option value="<?php echo $competencyleveres['id']; ?>"   <?php if($competecylid['competency_level_id'] == $competencyleveres['id']){ echo "selected";
}?>><?php echo ucwords($competencyleveres['level_name']); ?></option>
                                                    <?php } ?>
                                                </select>
                                                <?php }else{ ?>
                                                    <input type="hidden" name="competency_level[<?php echo $jobtitlesres['id']; ?>][<?php echo $competenciesres['id']; ?>]" value="">     -
                                                <?php } ?>
                                            </td>
                                        <?php } ?>

                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
