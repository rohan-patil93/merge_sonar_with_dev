<?php

    if(isset($_POST['submit'])){
        $_POST['company_id'] = $_SESSION['company_id'];
        if($_POST['submit'] == 'Add'){
            $_POST['created_by'] = $_SESSION['userid'];
            $_POST['created_date'] = nowDate();
            $url = API."?mode=addupdateproject&method=post";
            $response = file_post_contents($url, $_POST, 'insert');
             if ($response) {
                header("location:index.php?option=".$_GET['option']."&role=".$_GET['role']."&version=".$_GET['version']);
            }
        }
        if($_POST['submit'] == 'Add'){
            $_POST['created_by'] = $_SESSION['userid'];
            $_POST['created_date'] = nowDate();
            $url = API."?mode=addupdateproject&method=post";
            $response = file_post_contents($url, $_POST, 'insert');
             if ($response) {
                header("location:index.php?option=COM_LIST_PROJECTS");
            }
        }
        if($_POST['submit'] == 'Update'){
            $_POST['created_by'] = $_SESSION['userid'];
            $_POST['created_date'] = nowDate();
            $url = API."?mode=addupdateproject&method=post";
            $response = file_post_contents($url, $_POST, 'insert');
             if ($response) {
                header("location:index.php?option=COM_LIST_PROJECTS");
            }
        }

        exit;
    }
    $data1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
    $data = urlencode(json_encode($data1));
    $curl = API."?mode=gettabledetailswithconditions&table=application_users&data=".$data;
    $cudata = callAPI($curl);
    $app_users = json_decode($cudata,1);
    $data1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
    $data = urlencode(json_encode($data1));
    $curl = API."?mode=gettabledetailswithconditions&table=customers&data=".$data;
    $cudata = callAPI($curl);
    $customers = json_decode($cudata,1);

    $data1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
    $data = urlencode(json_encode($data1));
    $curl = API."?mode=gettabledetailswithconditions&table=project_billing_method&data=".$data;
    $cudata = callAPI($curl);
    $project_billing_method = json_decode($cudata,1);

    $data1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
    $data = urlencode(json_encode($data1));
    $curl = API."?mode=gettabledetailswithconditions&table=project_budget_type&data=".$data;
    $cudata = callAPI($curl);
    $project_budget_type = json_decode($cudata,1);

    if($_GET['Rid'] !=""){
        $rid_en = encodeId($_GET['Rid']);
        $ex_rid = explode(':', $rid_en);
        $rid = $ex_rid['1'];
        $rid=1;
        $curl = API."?mode=togetprojectdetails&rid=".$rid;
        $cudata = callAPI($curl);
        $pddata = json_decode($cudata,1);
        $pdata = $pddata['project'];
    }else{
    $pdata['project_users'] = array('value'=>1);
    $pdata['project_tasks'] = array('value'=>1);
    }
?>
<script type="text/javascript">
    function adduser(){
        alert("Man");
        console.log("Ravi check");
        var count = $(".userrow").length;
        count++;
        console.log(count);
        var htmlRows = '';
        htmlRows +='<tr class="datatable-row userrow" id="userrow_'+count+'" >';
        console.log(htmlRows);


    }
    function useremail(id,count){
        $.ajax({
            url: "ajax.php",
            data:'page=userDetails&user_id='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                var obj = $.parseJSON(data);
                document.getElementById('email_'+count).value=obj.email;

            },
            error:function (){}
        });
    }
    function removeuser(val){
         $('#userrow_'+val).css('display','none');
    }
    function addtask(){
        var count = $(".taskrow").length;
        count++;
        var htmlRows = '';
        htmlRows +='<tr class="datatable-row taskrow" id="taskrow_'+count+'">';
         htmlRows += '<td  class="p-3" style="width: 20%;"> <span><input aria-label="Task Name"  class="form-control mt-3 mb-3" type="text" value="" id="project-name" name="task_name[]" placeholder="Task Name"></span>  </td>';
         htmlRows += '<td class="p-3" style="width: 20%;"> <textarea aria-label="Task Description[]" name="task_description[]" class="form-control"></textarea> </td>';
         htmlRows += '  <td class="p-3"  style="width: 20%;"><div class="input-group col-md-7 mt-3 mb-3 pl-0"><div class="input-group-prepend">   <button type="button" class="btn btn-secondary">USD</button>   </div><input type="text" class="form-control" aria-label="Text input with dropdown button"  name="task_rate_hour[]" ></div></td>';
         htmlRows += '<td class="p-3" style="width: 20%;"> <input type="text" class="form-control"  name="task_budget_hour[]" placeholder="HH:MM" aria-label="Text input with dropdown button">  </td>';
           htmlRows += '<td class="p-3" style="width: 10%;"> <div class="checkbox-list "> <label class="checkbox checkbox-outline checkbox-outline-2x checkbox-primary"><input aria-label="Bill Type[]" type="checkbox" name="bill_type[]"> <span class="float-left"></span>Billable </label> </div> </td>';
        htmlRows += '<td class="p-3"  style="width: 10%;"> <div onclick="removetask('+count+')" style="cursor: pointer;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> <i class="flaticon2-rubbish-bin-delete-button"></i></div></td>';
         htmlRows += ' </tr>';
        $('#table_task').append(htmlRows);
    }
    function removetask(val){
         $('#taskrow_'+val).css('display','none');
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <?php if($content !=''  && $_POST['option']==''){ ?>
        <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
            <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
            <div class="alert-text"><?php echo $content; ?></div>
            <div class="alert-close">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true"><i class="ki ki-close"></i></span>
                </button>
            </div>
        </div>
        <?php } ?>
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title"><h3 class="card-label">&nbsp;<?php echo "Project";  ?> </h3>  </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="card-toolbar">
                        <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                            <i class="ki ki-long-arrow-back icon-xs"></i>
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <form name="myForm" action="" method="post" enctype="multipart/form-data">
        <div class="card card-custom">
            <div class="card-body">
                <div class="content  d-flex flex-column flex-column-fluid" id="kt_content">
                    <div class="col-md-6">
                        <div class="card-body">
                            <div class="form-group row">
                                <span class="col-3 col-form-label">Project Name</span>
                                <div class="col-9">
                                    <input aria-label="Name" class="form-control" type="text"  id="project-name_2" name="name" value="<?php echo $pdata['name']; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-3 col-form-label">Description</span>
                                <div class="col-9">
                                    <textarea aria-label="Descrption" class="form-control" name="descrption"  id="description" rows="3"><?php echo $pdata['description']; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-3 col-form-label">Customer Name</span>
                                <div class="col-9">
                                    <select aria-label="Customer" class="form-control" id="" name="customer_id">
                                        <option value="0"> Select Any One</option>
                                         <?php foreach($customers as $customer){ ?>
                                            <option  <?php if($customer['id'] == $pdata['customer_id']) { echo "selected";
} ?> value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-3 col-form-label">Billing Method</span>
                                <div class="col-9">
                                   <select aria-label="Billing Method" class="form-control" id="" name="billing_method_id">
                                        <option value="0"> Select Any One</option>
                                        <?php foreach($project_budget_type as $type){ ?>
                                            <option <?php if($type['id'] == $pdata['billing_method_id']) { echo "selected";
} ?> value="<?php echo $type['id']; ?>"><?php echo $type['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-3 col-form-label">Total Project Cost</span>
                                <div class="col-5">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <button type="button" class="btn btn-secondary">USD</button>
                                        </div>
                                        <input aria-label="Project Cost" type="text" class="form-control" name="project_cost" value="<?php echo $pdata['project_cost']; ?>">
                                    </div>
                                    <div class="checkbox-list mt-6">
                               <label class="checkbox checkbox-outline checkbox-outline-2x checkbox-primary">
                                  <input aria-label="Add Budget Project" type="checkbox" name="add_budget_project"  <?php if( $pdata['add_budget_project'] == '1') { echo "checked";
} ?> >
                                  <span class="float-left"></span>
                                  Add Budget for this project
                               </label>
                            </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <span class="col-3 col-form-label">Budget Type</span>
                                <div class="col-9">
                                    <select aria-label="Budget Type" class="form-control" id="" name="budget_type">
                                        <option value="0"> Select Any One</option>
                                        <?php foreach($project_billing_method as $method){ ?>
                                            <option <?php if($method['id'] == $pdata['budget_type']) { echo "selected";
} ?> value="<?php echo $method['id']; ?>"><?php echo $method['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="form-text text-muted">If you select this option, you can track your budget for the task.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container">
                        <div class="card card-custom gutter-b">
                            <div class="card-body">
                                <h2 class="text-dark font-weight-bold mt-2 mb-2 mr-5">User</h2>
                                <div class=" col-lg-10 datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded mt-10" id="kt_datatable">
                                    <table class="datatable-table p-3" id="table_user">
                                        <thead class="datatable-head">
                                            <tr class="datatable-row">
                                                <th scope="col"  class="p-3 text-left" style="width: 15%;"><span>User</span></th>
                                                <th scope="col" class="p-3 text-left" style="width: 15%;"><span>Email</span></th>
                                                <th scope="col" class="p-3 text-left" style="width: 10%;"><span>Rate Per Hours</span></th>
                                                <th scope="col" class="p-3 text-left" style="width: 2%;"></th>
                                            </tr>
                                        </thead>
                                        <?php foreach($pddata['project_users'] as $puser){ ?>
                                            <tr class="datatable-row userrow" id="userrow_1">
                                                <td class="p-3" style="width: 10%;">
                                                    <select aria-label="User[]" class="form-control kt-select2" id="kt_select2_41_2" name="user[]" onchange="useremail(this.value,1)">
                                                        <option value=""> Select Any One </option>
                                                        <?php foreach($app_users as $appuser){ ?>
                                                            <option <?php if($puser['user_id'] == $appuser['id']) { echo "selected";
} ?> value="<?php echo $appuser['id']; ?>"><?php echo $appuser['display_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                                <td  class="p-3" style="width: 15%;">
                                                    <input aria-label="User Email[]" type="text" class="form-control" readonly name="user_email[]" id="email_1" value="<?php echo $puser['email']; ?>">
                                                </td>
                                                <td class="p-3" style="width: 15%;">
                                                    <div class="input-group  mt-3 mb-3 pl-0">
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-secondary">USD</button>
                                                        </div>
                                                        <input aria-label="User Rate Hour[]" type="text" name="user_rate_hour[]" class="form-control" value="<?php echo $puser['rate_hours']; ?>">
                                                    </div>
                                                </td>
                                                <td class="p-3" style="width: 2%;">
                                                    <div onclick="removeuser(1)" style="cursor: pointer;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="flaticon2-rubbish-bin-delete-button"></i></div>
                                                </td>
                                            </tr>
                                            <input type="hidden" name="puser_id[]" value="<?php echo $puser['id']; ?>">
                                        <?php } ?>
                                    </table>
                                    <div class="btn btn-primary mt-10" onclick="adduser()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">+ Add User</div>
                                </div>
                                <h2 class="text-dark font-weight-bold mt-10 mb-2 mr-5">Task <span class="float-right float-right font-weight-normal font-size-lg text-muted">Import task from existing projects</span></h2>
                                <div class="datatable datatable-bordered datatable-head-custom datatable-default datatable-primary datatable-loaded mt-10" id="kt_datatable_2">
                                    <table class="datatable-table" id="table_task">
                                        <thead class="datatable-head">
                                            <tr class="datatable-row">
                                                <th scope="col" class="p-3"style="width: 20%;"><span>Task Name</span></th>
                                                <th scope="col" class="p-3" style="width: 20%"><span>Description</span></th>
                                                <th scope="col" class="p-3" style="width: 20%"><span>Rate Per Hours</span></th>
                                                <th scope="col" class="p-3" style="width: 20%"><span>Budget Hours(HH:MM)</span></th>
                                                <th scope="col" class="p-3" style="width: 10%"></th>
                                                <th scope="col" class="p-3" style="width: 10%"></th>
                                            </tr>
                                        </thead>
                                        <?php foreach($pddata['project_tasks'] as $ptask){ ?>
                                            <tr class="datatable-row taskrow" id="taskrow_1">
                                                <td  class="p-3" style="width: 20%;">
                                                    <span><input aria-label="Input field"  class="form-control mt-3 mb-3" type="text" value="<?php echo $ptask['task_name']; ?>" id="project-name_3" name="task_name[]" placeholder="Task Name"></span>
                                                </td>
                                                <td class="p-3" style="width: 20%;">
                                                    <textarea aria-label="Task Description[]" name="task_description[]" class="form-control"><?php echo $ptask['description']; ?></textarea>

                                                </td>
                                                <td class="p-3"  style="width: 20%;">
                                                    <div class="input-group col-md-7 mt-3 mb-3 pl-0">
                                                        <div class="input-group-prepend">
                                                            <button type="button" class="btn btn-secondary">USD</button>
                                                        </div>
                                                        <input aria-label="Input field" type="text" class="form-control"  value="<?php echo $ptask['rate_hours']; ?>" name="task_rate_hour[]" >
                                                    </div>
                                                </td>
                                                <td class="p-3" style="width: 20%;">
                                                    <input aria-label="Task Budget Hour[]" type="text" class="form-control"  name="task_budget_hour[]" value="<?php echo $ptask['budget_hours']; ?>" placeholder="HH:MM" >
                                                </td>
                                                <td class="p-3" style="width: 10%;">
                                                    <div class="checkbox-list ">
                                                        <label class="checkbox checkbox-outline checkbox-outline-2x checkbox-primary">
                                                            <input aria-label="Bill Type[]" type="checkbox"  name="bill_type[]" <?php if( $ptask['bill_type'] == '1') { echo "checked";
} ?>>
                                                            <span class="float-left"></span>
                                                            Billable
                                                        </label>
                                                    </div>
                                                </td>
                                                <td class="p-3"  style="width: 10%;">
                                                    <div onclick="removetask(1)" style="cursor: pointer;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> <i class="flaticon2-rubbish-bin-delete-button"></i></div>
                                                </td>
                                            </tr>
                                            <input type="hidden" name="ptask_id[]" value="<?php echo $ptask['id']; ?>">
                                         <?php } ?>
                                    </table>
                                    <div class="btn btn-primary mt-10" onclick="addtask()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">+ Add Task</div>

                                    <div class="mt-10">
                                        <?php if($_GET['Rid']!=""){ ?>
                                        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                                         <input type="hidden" name="submit" value="Update">
                                            <button class="btn btn-primary">Update</button>
                                        <?php }else{ ?>
                                           <input type="hidden" name="submit" value="Add">
                                        <button class="btn btn-primary">Save</button>
                                        <?php } ?>
                                        <button class="btn btn-secondary">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>
