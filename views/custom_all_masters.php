<?php 
$emailurl = API."?mode=togetmastertablesdetais";
//echo $emailurl; exit; togetmastertablesdetais
$emailgroupdata = callAPI($emailurl); //file_get_contents($emailurl, false, $context);
$datas = json_decode($emailgroupdata,1);
//echo"<pre>"; print_r($datas); exit;		
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="alert alert-warning alert-dismissible">
            <strong><?php echo "This function is used for maintaining <strong>Display Attributes</strong>. * represents mandatory fields."; ?></strong> 
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h3 class="card-title"> <?php echo viewTitle($_GET['option']); ?> </h3>
                        <div class="card-header-right table-card-header ">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light btn-inverse btn-outline-inverse"><i class="icofont icofont-reply"></i> Back </a>
                        </div>

        <div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
            <!-- begin:: Content Head -->
            <!-- <div class="kt-subheader   kt-grid__item" id="kt_subheader">
              <div class="kt-subheader__main">
                 <h3 class="kt-subheader__title">
                    <?php echo viewTitle($_GET['option']); ?>
                </h3>
                <span class="kt-subheader__separator kt-subheader__separator--v"></span>
                <div class="kt-subheader__group" id="kt_subheader_search">
                    <span class="kt-subheader__desc" id="kt_subheader_total">
                       <?php echo  count((array)$datas); ?> Total </span>
                       <form class="kt-margin-l-20" id="kt_subheader_search_form">
                           <div class="kt-input-icon kt-input-icon--right kt-subheader__search">
                              <input type="text" class="form-control" placeholder="Search..." id="generalSearch">
                              <span class="kt-input-icon__icon kt-input-icon__icon--right">
                                 <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                                       <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                          <rect x="0" y="0" width="24" height="24" />
                                          <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                          <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero" />
                                      </g>
                                  </svg>

                                 
                              </span>
                          </span>
                      </div>
                  </form>
              </div>
              <div class="kt-subheader__group kt-hidden" id="kt_subheader_group_actions">
                <div class="kt-subheader__desc"><span id="kt_subheader_group_selected_rows"></span> Selected:</div>
                <div class="btn-toolbar kt-margin-l-20">
                   <div class="dropdown" id="kt_subheader_group_actions_status_change">
                      <button type="button" class="btn btn-label-brand btn-bold btn-sm dropdown-toggle" data-toggle="dropdown">
                         Update Status
                     </button>
                     <div class="dropdown-menu">
                        <ul class="kt-nav">
                            <li class="kt-nav__section kt-nav__section--first">
                            <span class="kt-nav__section-text">Change status to:</span>
                            </li>
                            <li class="kt-nav__item">
                            <a href="#" class="kt-nav__link" data-toggle="status-change" data-status="1">
                            <span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-success kt-badge--inline kt-badge--bold">Approved</span></span>
                            </a>
                            </li>
                            <li class="kt-nav__item">
                            <a href="#" class="kt-nav__link" data-toggle="status-change" data-status="2">
                            <span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-danger kt-badge--inline kt-badge--bold">Rejected</span></span>
                            </a>
                            </li>
                            <li class="kt-nav__item">
                            <a href="#" class="kt-nav__link" data-toggle="status-change" data-status="3">
                            <span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-warning kt-badge--inline kt-badge--bold">Pending</span></span>
                            </a>
                            </li>
                            <li class="kt-nav__item">
                            <a href="#" class="kt-nav__link" data-toggle="status-change" data-status="4">
                            <span class="kt-nav__link-text"><span class="kt-badge kt-badge--unified-info kt-badge--inline kt-badge--bold">On Hold</span></span>
                            </a>
                            </li>
                        </ul>
                     </div>
                </div>
              <button class="btn btn-label-success btn-bold btn-sm btn-icon-h" id="kt_subheader_group_actions_fetch" data-toggle="modal" data-target="#kt_datatable_records_fetch_modal">
                  Fetch Selected
              </button>
              <button class="btn btn-label-danger btn-bold btn-sm btn-icon-h" id="kt_subheader_group_actions_delete_all">
                  Delete All
              </button>
            </div> -->
        </div>
    </div>
   </div>
</div>

<!-- end:: Content Head -->

<!-- begin:: Content -->
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

   <!--Begin::Section-->
   <div class="row">
      <?php foreach ($datas as $data) { 
         $page = toGetTableNameBaseApplicationPagesList('application_table_name', $data['application_table_name'],'normal');
         $display ['1'] = "Dynamic";
         $display ['2'] = "Custom";
         $discolor['1'] = "93a2dd";
         $discolor['2'] = "595d6e";
    			//print_r($page); exit;
         ?>
         <div class="col-xl-3">

             <!--begin:: Portlet-->
             <div class="card card-custom gutter-b card-stretch">
                <div class="card-body" style="min-height:250px;">
                  <div style="position: absolute;right: 10px;">
                     <!--begin::Widget -->
                     <div class="kt-portlet__head-toolbar">
                        <a href="#" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-toggle="dropdown">
                           <i class="flaticon-more-1"></i>
                       </a>
                       <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">
                           <ul class="kt-nav">
                              <li class="kt-nav__item">
                                 <a target="_blank" href="index.php?option=<?php echo $page['list_page_name']; ?>" class="kt-nav__link">
                                    <i class="kt-nav__link-icon flaticon-list-2"></i>
                                    <span class="kt-nav__link-text">List</span>
                                </a>
                            </li>
                            <li class="kt-nav__item">
                             <a target="_blank" href="index.php?option=<?php echo $page['insert_page_name']; ?>" class="kt-nav__link">
                                <i class="kt-nav__link-icon flaticon-add-circular-button"></i>
                                <span class="kt-nav__link-text">Insert</span>
                            </a>
                        </li>		
                        <li class="kt-nav__item">
                         <a target="_blank" href="index.php?option=COM_INSERT_DISPLAY_ATTRIBUTES&table=<?php echo $data['application_table_name']; ?>"  class="kt-nav__link">
                            <i class="kt-nav__link-icon flaticon2-settings"></i>
                            <span class="kt-nav__link-text">Settings</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="kt-widget kt-widget--project-1">
      <div class="kt-widget__head">
         <div class="d-flex align-items-center">
            <div class="flex-shrink-0 mr-4 symbol symbol-60 symbol-circle">
               <span class="kt-media kt-media--lg kt-media--circle">
                  <!-- <img src="assets/media/project-logos/3.png" alt="image"> -->
                  <div style="background: #fbce44;width: 50px;height: 50px; border-radius: 50%;text-align: center;"> 
                    <span style="color: #FFF;font-size: 25px;margin: 8px; font-weight: bold;"><?php echo initials(ucwords($data['application_table_name'])); ?></span>
                </div>
            </span>
        </div>
        <div class="d-flex flex-column m-l-10">
           <a href="#" class="kt-widget__title mb-2">
              <?php echo  ucwords(strtolower(str_replace("_", " ", $data['application_table_name']))); ?>
          </a>
          <span class="kt-widget__desc">
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['list_page_type']]; ?>;color:#fff;"  data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="List <?php echo $display[$page['list_page_type']]; ?>"> L </a>
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['insert_page_type']]; ?>;color:#fff;" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="Insert <?php echo $display[$page['insert_page_type']]; ?>"><?php echo "I"; ?> </a>
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['edit_page_type']]; ?>;color:#fff;" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="Edit <?php echo $display[$page['edit_page_type']]; ?>"><?php echo "E"; ?>	</a>
              <a href="#" class="btn btn-icon btn-circle" style="background-color: #<?php echo $discolor[$page['view_page_type']]; ?>;color:#fff;" data-toggle="kt-tooltip" data-skin="brand" data-placement="top" title="" data-original-title="View <?php echo $display[$page['view_page_type']]; ?>">V	</a>
          </span>
      </div>
  </div>

</div>
<div class="kt-widget__body mt-2">
 <span class="kt-widget__text">
    <?php echo $data['help_text'] ?>
</span>
<div class="kt-widget__content mt-2">
    <?php ksort($data['status']); foreach ($data['status'] as  $value) {
        if($value['status_name'] != 'total' && $value['status_name'] != '' ){ 
           $count_value = $value['count']!='' ? $value['count'] : '0'; ?>

           <div class="kt-widget__details" style="margin-right: 2.7rem;">
               <span class="kt-widget__subtitle font-weight-bold mr-4">
                  <?php echo $value['status_name']; ?>
              </span>
              <div class="kt-widget__label">
                  <span class="btn btn-primary btn-sm font-weight-bold btn-upper btn-text"><?php echo $count_value  ; ?></span>
              </div>
          </div>
      <?php } elseif($value['status_name'] == 'total' ){ $total =$value['count'];  } } ?>
  </div>
</div>
</div>
</div>

<div class="card-footer  align-items-center">
 <div class="kt-widget__wrapper">
    <div class="kt-widget__section float-left">
       <div class="kt-widget__blog">
          <i class="ti-list"></i>
          <a href="#" class="kt-widget__value kt-font-brand"><?php echo $total; ?> Recards</a>
      </div>
    									<!-- <div class="kt-widget__blog">
    										<i class="flaticon2-talk"></i>
    										<a href="#" class="kt-widget__value kt-font-brand">648 Comments</a>
    									</div> -->
    								</div>
    								
    								<div class="kt-widget__section float-right ml-5">
    									<a href="index.php?option=<?php echo $page['list_page_name']; ?>"><button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold">Details</button></a>
    								</div>
    							</div>
    						</div>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
</div>

