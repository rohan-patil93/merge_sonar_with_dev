<?php
require_once "dompdf/autoload.inc.php";
		use Dompdf\Dompdf;
		use Dompdf\Options;
class demoClass extends userClass{
		function generateTenantInvoicePDF11($get,$rid=""){
			global $callConfig;
			if($rid ==""){
			$rid = $get['rid'];
			}
			$return = array();
			$configsql = "SELECT * FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$configres=$callConfig->getRow($configsql);

			$rental_invoicessql = "SELECT * FROM rental_invoices WHERE company_id = '".$get['company_id']."' AND id = '".$rid."'  AND is_active =0";
			$rental_invoicesres=$callConfig->getRow($rental_invoicessql);

			$rental_invoice_itemsaql = "SELECT * FROM rental_invoice_items WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$rid."'  AND is_active =0";
			$rental_invoice_items=$callConfig->getAllRows($rental_invoice_itemsaql);
			$emailarray['to_company_id'] = $get['company_id'];
			$emailarray['session_company_id'] = $get['company_id'];
			$emailarray['user_company_id'] = $get['company_id'];
			$emailarray['user_display_name'] = $rental_invoicesres->party_name;
			$emailarray['user_email_id'] = $rental_invoicesres->party_email;
			$emailarray['transaction_number'] = $rental_invoicesres->transaction_number;
			$emailarray['transaction_date'] = $rental_invoicesres->transaction_date;
			$emailarray['total_amount'] = $rental_invoicesres->total_amount;
			$emailarray['created_by'] = $get['user_id'];
			$content = '';
		    $content .= '<html><body style="">';
	    	$content .= '<div class="invoice-top" >
                            <table class="table" style="width:100%;margin:0;">
                                <tr>
                                    <td>
                                        <div class="invoice">
                                            <h4 class="inv-header-1">Rental Invoice</h4>
                                            <h4 class="inv-header-1">'.$rental_invoicesres->transaction_number.'</h4>
                                        </div>
                                    </td>
                                    <td>
                                     	<div class="informeshon" style="float: right;">
                                        	<img style="width:150px;" class="logo" src="uploads/all/'.$configres->display_logo.'" alt="logo"></br>
                                        	<p>'.$configres->name.'</p>
                                       	<div class="informeshon">
                                    </td>
                                </tr>
                            </table>';
            $content .= '<table class="table" style="width:100%; margin-top: 10px;">
					        <tr>
					            <td>
					                <div class="informeshon">
					                	<p style="margin:0;">Rental Name: '.$rental_invoicesres->party_name.'<br/><br/>
						                	Email: '.$rental_invoicesres->party_email.'<br/><br/>
						                	Phone: '.$rental_invoicesres->party_phone.'<br/><br/>
						                    Taxes: '.$rental_invoicesres->party_tax_number.'
					                    </p>
					                </div>
					            </td>
					            <td>
					           	 	<div class="informeshon">
					                    <p>Transaction Date &nbsp;&nbsp;: &nbsp;&nbsp; '.$rental_invoicesres->transaction_date.'<br/><br/>
					                    Payment Terms &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp; NET +  '.$rental_invoicesres->payment_terms.' DAYS<br/><br/>
					                    Place Of Supply &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;'.$rental_invoicesres->place_of_supply.'
					                    </p>
					                </div>    
					            </td>
					        </tr>
					    </table>
					    <table class="table" style="width:100%;margin:0;">
					        <tr>
					            <td>
					                <div class="informeshon">
					                    <p class="inv-title-1" style="margin:0;padding-top:5pt;">Billing Adderss:</p>
					                    <p class="invo-addr-1" style="margin:0;padding-top:5pt;">'.$rental_invoicesres->party_address.'<br/></p>
					                    <p class="inv-title-1" style="margin:0;padding-top:5pt;">Shiping Adderss:</p>
					                    <p class="invo-addr-1" style="margin:0;padding-top:5pt;">'.$rental_invoicesres->party_ship_adderss.'<br/>
					                    </p>
					                </div>
					            </td>
					        </tr>
					    </table>
                    </div>'; 
           $content .= '<div class="invoice-center">
                            <div class="table-section table-responsive clearfix">
                                <table class="table caption-top invoice-table" style="width:100%;margin:0;">
                                    <thead class="bg-active">
	                                    <tr>
						                    <th style="padding:5pt;text-align:left;">Name</th>
						                    <th style="padding:5pt;text-align:left;">Descrpition</th>
						                    <th style="text-align:right;padding:5pt;" class="">Price</th>
						                    <th style="text-align:right;padding:5pt;" class="">Amount</th>
						                    <th style="text-align:right;padding:5pt;" class="">Total</th>
						                </tr>
                                    </thead>
                                    <tbody>';
                                    $i=1;  foreach ($rental_invoice_items as $item) {
                                    $tablecontent .= '<tr>
							                    <td style="text-align:left;padding:5pt;font-size:10px;">
							                    	<p style="text-align:left;margin:0;">'.$item->item_name.'</p>
							                    </td>
							                    <td style="text-align:left;padding:5pt;font-size:10px;">
							                    	<p style="text-align:left;margin:0;">'.$item->description.'</p>
							                    </td>
							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
							                    	<p style="text-align:right;margin:0;">'.$item->item_price.'</p></td>
							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
							                    	<p style="text-align:right;margin:0;">'.$item->item_amount.'</p>
							                    </td>
							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
							                    	<p style="text-align:right;margin:0;">'.$item->total_amount.'</p>
							                    </td>
							                </tr>';
                                  $i++; }
                                  	$content .= $tablecontent;
                                    $content .= '<tr>
								                    <td style="padding:5pt;text-align:right;" colspan="4" class="">
								                    	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">SubTotal</p>
								                    </td>
								                    <td style="padding:5pt;text-align:right;" class="text-right text-end">
								                    	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->total_amount.'</p>
								                    </td>
								            </tr>
								            <tr>
								                <td style="padding:5pt;text-align:right;" colspan="4" class="text-end">
								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">Tax</p>
								                </td>
								                <td style="padding:5pt;text-align:right;" class="text-right">
								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->tax_amount.'</p>
								                </td>
								            </tr>
								            <tr>
								                <td style="padding:5pt;text-align:right;" colspan="4" class="text-end fw-bold">
								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">Grand Total</p>
								                </td>
								                <td style="padding:5pt;text-align:right;" class="text-right fw-bold text-end">
								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->total_amount.'</p>
								                </td>
								            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>'; 
          $content .= '<div class="invoice-bottom">
                            <div class="row">
                                <div class="col-sm-6">
                                
                                </div>
                                <div class="col-sm-6">
                                    <div class="amount text-end">
                                        <h3 class="inv-title-1" style="text-align:right;">Total Amount</h3>
                                        <h1 style="text-align:right;">'.$rental_invoicesres->total_amount.'</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="note mt-3">
                                <p class="text-muted">'.$rental_invoicesres->printing_notes.'</p>
                            </div>
                        </div>
                    </div>'; 

        	$content .= '</body></html>';
        	echo $content;
        	echo realpath('');
        	$options = new Options();
			$options->set('chroot', realpath(''));
			$dompdf = new Dompdf($options);
			$dompdf->loadHtml($content);
			// Set paper size (optional)
			$dompdf->setPaper('A4', 'portrait');
			// Render PDF (first pass to get the total number of pages)
			$dompdf->render();
			$filename = "Test_invoice".time();
			$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$filename.'.pdf';
			if(file_exists('assets/images/sign-in/infistaq_logo.png')){
				echo "file is available";
			}else{
				echo "file not availble";
			}
			echo '<a href="http://www.tickday.infistaq.com/uploads/rental_invoices/'.$filename.'.pdf" download>'.$filename.'</a>';
			$output = $dompdf->output();
			file_put_contents($sFilePath, $output); 




			/**$dompdf = new Dompdf();
			$dompdf->loadHtml($content);
			$dompdf->render();
			$output = $dompdf->output();
			$filename = "Test_invoice".time();
			$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$filename.'.pdf';
			echo '<a href="http://www.tickday.infistaq.com/uploads/rental_invoices/'.$filename.'.pdf" download>'.$filename.'</a>';
			file_put_contents($sFilePath, $output); 
			**//**
			$options = new Options();
			$options->set('isHtml5ParserEnabled', true);
			$options->set('isPhpEnabled', true);

			$dompdf = new Dompdf($options);
			// Set image-related options
			$dompdf->set_option('dpi', 300); // Dots Per Inch
			$dompdf->set_option('isPhpEnabled', true); // Enable PHP rendering
			$dompdf->set_option('isHtml5ParserEnabled', true); // Enable HTML5 parsing
			$dompdf->set_option('isPhpEnabled', true); // Enable PHP in HTML
			$dompdf->set_option('isPhpEnabled', true); // Enable embedded PHP scripts

			// Load HTML to dompdf
			$dompdf->loadHtml($content);

			// Set paper size (optional)
			$dompdf->setPaper('A4', 'portrait');

			// Render PDF
			$dompdf->render();

			// Output PDF to the browser
			//$dompdf->stream('output.pdf', array('Attachment' => 0));
			$filename = "Test_invoice".time();
			$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$filename.'.pdf';
			echo '<a href="http://www.tickday.infistaq.com/uploads/rental_invoices/'.$filename.'.pdf" download>'.$filename.'</a>';
			//$dompdf->stream($sFilePath, array('Attachment' => 0));
			$output = $dompdf->output();

			file_put_contents($sFilePath, $output);  
			**/
		}
		//http://www.tickday.infistaq.com//api.php/?mode=generatetenantinvoicePDF&page_option=&data_name=infistaq_infisuite_2&user_id=1&company_id=1&module_id=&module_group_id=&ipaddress=59.95.107.153
		function generateTenantInvoicePDF($get,$rid=""){
			global $callConfig;
			$return = array();
			echo "<pre>"; 
			$tables = array('account_type','act_categories', 'action_email_settings','action_log','additional_charges', 'announcement_categories', 'application_attach_labels','application_attachments','application_check_lists','application_emails','application_filters','application_labels','application_logs','application_menu','application_menu_permission','application_menu_settings','application_messages','application_missing_data_check','application_monthly_lock','application_no_permission_pages','application_page_list','application_pages','application_process','application_process_pages','application_process_role','application_query','application_report_download_histories','application_reports','application_roles','application_serial_numbers','application_share','application_table_histories','application_table_quality_check','application_tags','application_temp','application_tours','application_user_invitations','application_user_logins','application_user_module_roles','application_user_widgets','application_users','application_widget_groups','application_widgets','appraisal_bell_curves','appraisal_key_performance_indicators','appraisal_key_result_areas','appraisal_kpi_tasks','appraisal_rating_slabs','assessment_responses','assessment_responses_details','assessment_setup','assessment_setup_question_options','assessment_setup_questions','asset_attributes','asset_categories','asset_condition_type','asset_status','asset_trans_requests','asset_transactions','asset_transfer_requests','asset_transfers','asset_transfers_assets','asset_verifications','assets','attedenace_regularization_settings','attedenace_regularizaton_reasons','attendance_key_types','attendance_regularization_settings','attendance_settings','attendenace_regularization_requests','audit_trail_exceptions','audit_trail_items','audit_trails','bank_account_users','bank_accounts','banks','benefit_categories','benefit_plan_options','benefit_plans','bill_cancellations','bill_histories','bill_item_cancellations','bill_item_taxe_cancellations','bill_item_taxes','bill_items','bill_payments','bills','blood_groups','budget','budget_areas','budget_assumptions','budget_balances','budget_categories','budget_group_units','budget_groups','building_floor_spaces','building_floors','buildings','bulk_approval_base','bulk_approval_transactions','bulk_approvals_dummy','bulk_transfer_categories','candidate_display_fields','candidate_documents','candidate_history','candidate_hold_rejection_reasons','candidate_interview_schedule','candidate_offer_histories','candidates','castes','chart_of_accounts','cities','companies','company_announcements','company_contribution_users','company_document_categories','company_documents','company_key_link_categories','company_key_links','company_subscription_plan_licenses','company_subscription_plans','company_subscription_users','company_transports','company_types','company_valuation_distributions','company_valuations','competencies','competency_levels','competency_types','compliance_task_histories','compliances','config','contact_details','contacts','copy_candidate_display_fields','cost_centers','countries','couriers','create_documents_fields','credit_note_item_taxes','credit_note_items','credit_notes','crm_lead_details','crm_lead_types','crm_leads','currencies','currency_convertor','customer_items','customer_summary','customers','debit_note_item_taxes','debit_note_items','debit_notes','deductions','deferred_revenues','delivery_orders','departments','depreciation_codes','designations','designations_vms','display_attribute_types','display_attribute_types_old','display_attributes','display_attributes_old','divisions','do_item_taxes','do_items','doa','document_access_setup_users','document_access_setups','document_categories','document_list_types','document_lists','drivers','dynamic_mapping_tables','dynamic_reference_tables','earnings','earnings_old','eba_vendors_allocations','edi_data','edi_dump','edi_master','edi_read','eeo_categories','email','email_content','email_group_emails','email_groups','email_logs','email_template_types','emp_assign_release_documents','emp_category_change_reasons','emp_contacts','emp_contract_types','emp_contracts','emp_dependents','emp_educations','emp_expenses','emp_expenses_details','emp_histories','emp_hr_services','emp_it_declaration','emp_it_declaration_80','emp_it_declaration_houserent','emp_it_declaration_other_income','emp_it_form_16a','emp_languages','emp_leave_allocations','emp_leave_balances','emp_perevious_details','emp_performance','emp_performance_peer_feedback','emp_positions','emp_provident_funds','emp_relieving','emp_salary_advance','emp_shifts','emp_skills','emp_state_insaurance','emp_state_sub_insaurance','emp_tax_deposit_respect','emp_transfer_history','emp_visa','emp_work_experiences','emp_work_order','emp_working_schedules','employe_esi','employee_benefit_plan_options','employee_benefit_plans','employee_buddies','employee_daily_attendance_details','employee_daily_attendances','employee_events','employee_feedbacks','employee_food_requests','employee_goals','employee_kpi_task_histories','employee_kpi_tasks','employee_leaves','employee_notes','employee_other_earnings','employee_performance_appraisal_details','employee_performance_appraisal_headers','employee_performance_appraisals','employee_recognitions','employee_terminations','employee_trainings','employee_transport_requests','employees','employees_backup_before_update','employees_display_fields','employees_empid_creation','employees_old','employees_vms','employment_bands','employment_categories','employment_levels','employment_roles','employment_statuses','enquiries','equipment_allocations','equipment_categories','equipment_incidents','equipment_types','equipments','es_item_taxes','es_items','esgin_budget_approvals','esgin_employees','esign_budget_consumed','esop_allocations','esop_plan_buckets','esop_plans','esop_vesting_schedule','ess_dummy','ess_process','ess_process_documents','estimates','event_categories','exemption_groups','expense_advance_summary','expense_categories','expenses_mileages_perdiem','faq_categories','faq_contents','feedbacks','feeds','field_secruity_configurations','filter_tables','finance_report_chart_of_account','finance_report_groups','finance_reports','fiscal_positions','fiscal_positions_mappings','fiscal_years','focus_areas','follow_up_levels','food_options','general_code_details','general_code_role_permissions','general_codes','general_ledger','gl_account_users','goals','goods_receipt_notes','grade_salary_template','grade_salary_template_components','gratuty','grn_item_taxes','grn_items','grn_returns','grnret_item_taxes','grnret_items','grrn_item_taxes','grrn_items','gst_setting','holidays','hsn_and_sac_codes','id_card_requests','import_mappings','import_swap_in_out_details','import_swap_in_outs','incoterms','industries','it_declaration','it_declaration_categories','it_declaration_settings','item_attributes','item_balances_st','item_balances_wa','item_categories','item_cost_centers','item_fifo_balances','item_head_quality_check','item_ledger','item_ledger_fifo','item_ledger_lifo','item_ledger_st','item_ledger_wa','item_lifo_balances','item_locations','item_lot_nos','item_quality_check','item_quality_parameters_mapping','item_serial_lot_histories','item_serial_nos','item_specifications','item_st_balances','item_types','item_wa_balances','items','job_applications','job_candidate_interview_schedules','job_categories','job_categories_2','job_descrption_categories','job_descrptions','job_feedback_question_options','job_feedback_questions','job_feedback_responses','job_feedback_responses_details','job_feedback_setup','job_grade','job_grade_templates','job_hiring_source','job_posting_candidates','job_posting_distribute','job_posting_publish_detail_histories','job_posting_publish_details','job_posting_views','job_postings','job_req_agency_referrals','job_selection_process','job_selection_process_steps','job_title_competencies','job_title_employment_statuses','job_titles','journal_groupings','journal_settings','journal_type','journal_vouchers','jv_item_taxes','jv_items','jv_items_references','key_performance_indicators','key_result_areas','kpi_tasks','labour_welfare_fund','languages','leads','leave_categories','leave_regularization_requests','leave_type_levels','leave_types','leaving_feedback','letters','loc_item_fifo_balances','loc_item_fifo_lifo_balances','loc_item_lifo_balances','loc_item_st_balances','loc_item_wa_balances','locations','machines','manpower_contracts','manpower_requisitions','marital_statuses','meeting_room_bookings','module_setup','module_setup_linkings','monthly_payments','mpc_item_taxes','mpc_items','my_reports','nationalities','notes','notification_user','notifications','offer_details','organization_goals','organization_tasks','other_earnings','pay_area_employees','pay_areas','pay_periods','pay_run_summary','pay_run_summary_components','pay_summary','pay_summary_components','payment_allocations','payment_providers','payment_terms','payments','payroll','payroll_admins','payroll_area_admins','payroll_exclusions','payroll_steps','pc_expense_categories','pc_item_taxes','pc_items','performance_appraisal_headers','performance_appraisal_questions','performance_appraisal_setting_histories','performance_appraisal_settings','performance_appraisal_setups','performance_responses','performance_responses_details','performance_setup','performance_setup_questions','petty_cash','petty_cash_balances','petty_cash_box_owner','petty_cash_box_topup_requests','petty_cash_boxes','petty_cash_expenses','petty_cash_items','petty_cash_transactions','petty_cash_types','petty_cash_yearend','pf_leaving_reasons','pf_schemes','pi_item_taxes','pi_items','po_item_taxes','po_items','poll_responses','poll_responses_details','polls_setup','polls_setup_question_options','polls_setup_questions','presentation','presentation_content','print_designs','process_task_settings','product_setup','professional_tax','project_billing_method','project_budget_type','project_machine_material','project_phases','project_roles','project_task_activities','project_task_deliverables','project_task_dependencies','project_task_details','project_task_logs','project_task_machine_material','project_task_status','project_task_teams','project_task_time_log','project_tasks','project_teams','project_timesheet_logs','project_timesheets','project_type','project_work_spaces','projects','property_owner_detail_areas','property_owner_details','property_owners','property_tenant_billings','property_tenant_details','property_types','purchase_invoices','purchase_orders','qo_item_taxes','qo_items','qt_item_taxes','qt_items','qualification_areas','qualification_levels','qualifications','quality_parameters','quality_parameters_groups','quality_parameters_mapping','quotations','quote','races','rationalization_requests','receipts_alloations','recognition_badges','reconciliations','reimbursement_policies','reimbursement_policy_categories','relations','religions','rental_invoice_item_taxes','rental_invoice_items','rental_invoices','rental_utilities','report_attributes','report_columns','report_fields','report_filters','report_histories','reports','request_work_from_home','requisition_quote_vendors','requisitions','residential_statuses','role_permissions','route_stops','rq_item_taxes','rq_items','rq_qo_items','salary_categories','salary_hold_reasons','salary_reasons','salary_release_reasons','salary_reporting_groups','salary_revisions','salary_tax_components','salary_template','salary_template_components','sales_categories','sales_invoice_cancellations','sales_invoices','sales_orders','sales_returns','salesmen','sb_item_cancellations','sb_item_taxe_cancellations','sb_item_taxes','sb_items','sections','setup_page_logs','setup_pages','shift_breaks','shift_regularization_requests','shift_rotations','shift_settings','shifts','si_item_cancellations','si_item_taxe_cancellations','si_item_taxes','si_items','skills','so_item_taxes','so_items','space_types','sr_item_taxes','sr_items','states','stationery_cart','stationery_item_ledger','stationery_items','stationery_requests','status','status_progress','status_progress_actions','stkadj_item_taxes','stkadj_items','stkrec_item_taxes','stkrec_items','stkreq_item_taxes','stkreq_items','stktrn_item_taxes','stktrn_items','stktrn_items_taxes','stktrnrec_item_taxes','stktrnrec_items','stktrnret_item_taxes','stktrnret_items','stock_adjustment_reasons','stock_adjustments','stock_requests','stock_transfer_receipts','stock_transfer_returns','stock_transfers','stocktake_items','stocktakes','streq_item_taxes','sub_sections','subscription_groups','subscription_plans','summary_announcements','supplier_bill_cancellations','supplier_bills','survey_responses','survey_responses_details','survey_setup','survey_setup_question_options','survey_setup_questions','system_document_sub_topics','system_document_topics','system_documents','system_earnings','system_other_earnings','talent_pools','tasks_categories','tax_authorities','tax_authorities_rates','tax_authorities_rates_split','tax_categories','tax_computations','tax_group_taxes','tax_groups','tax_provinces','tax_slads','taxes','tds_rates','temp_attendance_details','text_of_contents','toc_headers','training_categories','trainings','transaction_access_permission','transaction_access_permission_users','transaction_activity_schedule','transaction_approval_logs','transaction_documents','transaction_quality_check','transaction_queries','transaction_requirement_types','transport_requests','transport_routes','transport_schedule','trial_balance','two_step_verification','unit_of_measurements','uom','uom_conversions','user_assets','user_cost_centers','user_departments','user_divisions','user_items','user_locations','user_reimbursement_policies','user_settings','user_warehouse','vendor_agreements','vendor_contract_details','vendor_contracts','vendor_documents','vendor_feedback_responses','vendor_feedback_responses_details','vendor_feedback_setup','vendor_feedback_setup_question_options','vendor_feedback_setup_questions','vendor_items','vendor_rate_cards','vendor_registers','vendor_relationship_mangers','vendor_summary','vendors','verticals','veterans','visiting_card_requests','vms_application_users','vms_employees','vrc_items','vrc_per_diem','warehouses','work_attendance_logs','work_from_home_setting','workflow_configuration','workflow_configuration_details','workflow_configuration_histories');
				//$tables = array('account_type','act_categories');
			$totalcnt = 0;
			$notshowlistary = array('id','company_id', 'is_active', 'current_status', 'status_date', 'from_date', 'to_date', 'created_date', 'created_by', 'updated_by', 'updated_date');
				foreach($tables as $tablesname){
					
					$whr="application_table_name='".$tablesname."' AND is_active = 0";
					$query=$callConfig->selectQuery('display_attributes','*',$whr,'','','');
					$data22 = $callConfig->getRow($query);
					//$res = $data->id;
					if($data22->id ==''){
						//$sql = "SELECT * FROM ".$tablesname;
						$sql = "select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME='".$tablesname."'";
						//$sql = "SHOW COLUMNS FROM infistaq_infisuite_2.".$tablesname;
						$res=$callConfig->getAllRows($sql);
						$display_postion = 10; 
						$data1 = $data = array();
						$data['company_id'] = $gte['company_id'];
						//print_r($res);
						foreach($res as $dsfvdgv){
							//print_r($dsfvdgv->COLUMN_NAME);
							$postvalue = $dsfvdgv->COLUMN_NAME;
							$data1[$postvalue]['display_postion'] = $display_postion;
							
							$data1[$postvalue]['lable'] = ucwords(str_replace("_"," ",$postvalue));
							$data1[$postvalue]['mandatory'] = 2;
							$data1[$postvalue]['type'] = 'text';
							if(in_array($postvalue, $notshowlistary)){
								$data1[$postvalue]['insert_display_list'] = 2;
								$data1[$postvalue]['display_list'] = 2;
							}else{
								$data1[$postvalue]['insert_display_list'] = 1; 
								$data1[$postvalue]['display_list'] = 1;
							}
							$data1[$postvalue]['insert_view_width'] = 6;
							$data1[$postvalue]['insert_view_wizard'] = 1;
							$data1[$postvalue]['add_custom_field'] = null;
							$data1[$postvalue]['add_exter'] = 0;
							$data1[$postvalue]['accordions'] = 'N';
							$display_postion+10;
						}
						$data_json = json_encode($data1);
						$data['application_table_name'] = $tablesname;
						$data['list_attributes'] = $data_json;
						$data['wizard'] = 1;
						$data['wizard_details'] ='[""]';
						$data['display_list_type'] = 1;
						$data['insert_page_type'] = 0;
						$data['help_text'] = "This function is used for maintaining <strong>Employee Performance Appraisals</strong>. * represents mandatory fields.";
						$data['originator_check_required_or_not'] = 'Y';
						$data['additional_attributes'] = '{"right":"","left":""}';
						$data['company_id'] = $get['company_id'];
						$data['file_upload'] = '{"file_upload":"0","fileuplodinglabel":"","fileuplodingwidth":"1","number_of_files":"","file_size":"","fileuplodingdeleteddisplay":"0","fileuplodingdleetewidth":"1"}';
						$data['current_status'] = 0;
						$data['is_active'] = 0;
						//$res_rg_group_user = $callConfig->insertRecord('display_attributes',$data);
						$return[$tablesname][] = $res_rg_group_user;
					}	

					$whr="application_table_name='".$tablesname."' AND is_active = 0";
					$query=$callConfig->selectQuery(TBL_APPLICATION_PAGES,'*',$whr,'','','');
					$data33 = $callConfig->getRow($query);
					//$res = $data->id;
					if($data33->id ==''){
						$field=array('company_id'=>$get['company_id'],'application_table_name'=>$tablesname,'list_page_name'=>strtoupper("COM_LIST_".$tablesname),'insert_page_name'=>strtoupper("COM_INSERT_".$tablesname),'edit_page_name'=>strtoupper("COM_EDIT_".$tablesname),'view_page_name'=>strtoupper("COM_view_".$tablesname),'list_page_type'=>1,'insert_page_type'=>1,'edit_page_type'=>1,'view_page_type'=>1,'Current_Status'=>0,'is_active'=>'0');
					//	$res222=$callConfig->insertRecord(TBL_APPLICATION_PAGES,$field);
						$return[$tablesname][] = $res222;
					}
					$totalcnt++;
				}
				echo "$totalcnt == ".$totalcnt;
				print_r($return);
		} 
/**
		function generateTenantInvoicePDF11($get,$rid=""){
			global $callConfig;
			if($rid ==""){
			$rid = $get['rid'];
			}
			$return = array();
			$configsql = "SELECT * FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
			$configres=$callConfig->getRow($configsql);

			// Create an instance of the Dompdf class
			$options = new Options();
			$options->set('chroot', realpath(''));
			$dompdf = new Dompdf($options);
			//$realpath = realpath("uploads/all/".$configres->display_logo);
			//echo $realpath; 
			// Load HTML content
			//$html = file_get_contents('path/to/your/html-file.html');
$html = '<img style="width:150px; " class="logo" src="assets/images/sign-in/infistaq_logo.png" alt="logo">';
echo "step 1";



			// Load the HTML to dompdf
			echo $html;
			$dompdf->loadHtml($html);
			// Set paper size (optional)
			$dompdf->setPaper('A4', 'portrait');
			// Render PDF (first pass to get the total number of pages)
			$dompdf->render();
			$filename = "Test_invoice".time();
			$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$filename.'.pdf';
			if(file_exists('assets/images/sign-in/infistaq_logo.png')){
				echo "file is available";
			}else{
				echo "file not availble";
			}
			echo '<a href="http://www.tickday.infistaq.com/uploads/rental_invoices/'.$filename.'.pdf" download>'.$filename.'</a>';
			$output = $dompdf->output();
			file_put_contents($sFilePath, $output); 

		}
**/
// 	function generateTenantInvoicePDF($get,$rid=""){
// 		global $callConfig;
// 		if($rid ==""){
// 			$rid = $get['rid'];
// 		}
// 		$return = array();
// 		$configsql = "SELECT * FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
// 		$configres=$callConfig->getRow($configsql);

// 		$rental_invoicessql = "SELECT * FROM rental_invoices WHERE company_id = '".$get['company_id']."' AND id = '".$rid."'  AND is_active =0";
// 		$rental_invoicesres=$callConfig->getRow($rental_invoicessql);

// 		$rental_invoice_itemsaql = "SELECT * FROM rental_invoice_items WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$rid."'  AND is_active =0";
// 		$rental_invoice_items=$callConfig->getAllRows($rental_invoice_itemsaql);
// 		$emailarray['to_company_id'] = $get['company_id'];
// 		$emailarray['session_company_id'] = $get['company_id'];
// 		$emailarray['user_company_id'] = $get['company_id'];
// 		$emailarray['user_display_name'] = $rental_invoicesres->party_name;
// 		$emailarray['user_email_id'] = $rental_invoicesres->party_email;
// 		$emailarray['transaction_number'] = $rental_invoicesres->transaction_number;
// 		$emailarray['transaction_date'] = $rental_invoicesres->transaction_date;
// 		$emailarray['total_amount'] = $rental_invoicesres->total_amount;
// 		$emailarray['created_by'] = $get['user_id'];

// 		require('htmltopdfconverterTCPDF/tcpdf.php');   
// 	    $obj_pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);  
// 	    $obj_pdf->SetCreator(PDF_CREATOR);  
// 	    // $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
// 	    // $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
// 	    // $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
// 	    //$obj_pdf->SetDefaultMonospacedFont('helvetica');  
// 	    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
// 	    $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
// 	    $obj_pdf->setPrintHeader(false);  
// 	    $obj_pdf->setPrintFooter(false);  
// 	    $obj_pdf->SetAutoPageBreak(TRUE, 10); 
// 	    //$obj_pdf->SetAutoPageBreak('PDF_PAGE_FORMAT', 'A4'); 
// 	    //$obj_pdf->SetFont('helvetica', '', 12);  
// 	    //$AddPage->AddPage('P','A5');
// 	    $obj_pdf->AddPage(); 


// 	    $content = '';
// 	    $content .= '<html><body style=" width: 210mm; min-height: 297mm; padding: 20mm;margin: 10mm auto;border: 1px #D3D3D3 solid;border-radius: 5px;background: white;box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">';
// 	    $content .= '<table style="width:100%"><tr><td>A1</td><td style="text-align: end;">A2</td></tr>';
// 	      $content .= '<table style="width:100%"><tr><td>A1</td><td style="text-align: end;">A2</td></tr>';
// 	        $content .= '<table style="width:100%"><tr><td>A1</td><td style="text-align: end;">A2</td></tr>';
// 	          $content .= '<table style="width:100%"><tr><td>A1</td><td style="text-align: end;">A2</td></tr>';
// 	    $content .= '<div class="invoice-top" >
//                             <table class="table" style="width:100%;margin:0;">
//                                 <tr>
//                                     <td>
//                                         <div class="invoice">
//                                             <h4 class="inv-header-1" style="margin:0;">Rental Invoice</h4>
//                                             <h4 class="inv-header-1" style="margin:0;">'.$rental_invoicesres->party_name.'</h4>
//                                         </div>
//                                     </td>
//                                     <td>
//                                         <div class="invoice-name text-end">
//                                             <div class="logo text-white">
//                                                 <img style="width:150px; float: right;" class="logo" src="uploads/all/'.$configres->display_logo.'" alt="logo">
//                                             </div>
                                            
//                                         </div>
//                                     </td>
//                                 </tr>
//                             </table>';
//                             $content .= '<table class="table" style="width:100%;margin:0;">
// 						        <tr>
// 						            <td>
// 						                <div class="informeshon">
// 						                	<p style="margin:0;">Rental Name: '.$rental_invoicesres->party_name.'<br/><br/>
// 							                	Email: '.$rental_invoicesres->party_email.'<br/><br/>
// 							                	Phone: '.$rental_invoicesres->party_phone.'<br/><br/>
// 							                    Taxes: '.$rental_invoicesres->party_tax_number.'
// 						                    </p>
// 						                </div>
// 						            </td>
// 						            <td>
// 						                <div class="informeshon">
// 						                    <p style="margin:0;">Transaction Date &nbsp;&nbsp;: &nbsp;&nbsp; '.$rental_invoicesres->transaction_date.'<br/><br/>
// 						                    Payment Terms &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp; NET +  '.$rental_invoicesres->payment_terms.' DAYS<br/><br/>
// 						                    Place Of Supply &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;'.$rental_invoicesres->place_of_supply.'
// 						                    </p>
// 						                </div>
// 						            </td>
// 						        </tr>
// 						    </table>
// 						    <table class="table" style="width:100%;margin:0;">
// 						        <tr>
// 						            <td>
// 						                <div class="informeshon">
// 						                    <p class="inv-title-1" style="margin:0;padding-top:5pt;">Billing Adderss:</p>
// 						                    <p class="invo-addr-1" style="margin:0;padding-top:5pt;">'.$rental_invoicesres->party_address.'<br/></p>
// 						                    <p class="inv-title-1" style="margin:0;padding-top:5pt;">Shiping Adderss:</p>
// 						                    <p class="invo-addr-1" style="margin:0;padding-top:5pt;">'.$rental_invoicesres->party_ship_adderss.'<br/>
// 						                    </p>
// 						                </div>
// 						            </td>
// 						        </tr>
// 						    </table>
//                         </div>'; 
//            $content .= '<div class="invoice-center">
//                             <div class="table-section table-responsive clearfix">
//                                 <table class="table caption-top invoice-table" style="width:100%;margin:0;">
//                                     <thead class="bg-active">
// 	                                    <tr>
// 						                    <th style="padding:5pt;text-align:left;">Name</th>
// 						                    <th style="padding:5pt;text-align:left;">Descrpition</th>
// 						                    <th style="text-align:right;padding:5pt;" class="">Price</th>
// 						                    <th style="text-align:right;padding:5pt;" class="">Amount</th>
// 						                    <th style="text-align:right;padding:5pt;" class="">Total</th>
// 						                </tr>
//                                     </thead>
//                                     <tbody>';
//                                     $i=1;  foreach ($rental_invoice_items as $item) {
//                                     $tablecontent .= '<tr>
// 							                    <td style="text-align:left;padding:5pt;font-size:10px;">
// 							                    	<p style="text-align:left;margin:0;">'.$item->item_name.'</p>
// 							                    </td>
// 							                    <td style="text-align:left;padding:5pt;font-size:10px;">
// 							                    	<p style="text-align:left;margin:0;">'.$item->description.'</p>
// 							                    </td>
// 							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
// 							                    	<p style="text-align:right;margin:0;">'.$item->item_price.'</p></td>
// 							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
// 							                    	<p style="text-align:right;margin:0;">'.$item->item_amount.'</p>
// 							                    </td>
// 							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
// 							                    	<p style="text-align:right;margin:0;">'.$item->total_amount.'</p>
// 							                    </td>
// 							                </tr>';
//                                   $i++; }
//                                   	$content .= $tablecontent;
//                                     $content .= '<tr>
// 								                    <td style="padding:5pt;text-align:right;" colspan="4" class="">
// 								                    	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">SubTotal</p>
// 								                    </td>
// 								                    <td style="padding:5pt;text-align:right;" class="text-right text-end">
// 								                    	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->total_amount.'</p>
// 								                    </td>
// 								            </tr>
// 								            <tr>
// 								                <td style="padding:5pt;text-align:right;" colspan="4" class="text-end">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">Tax</p>
// 								                </td>
// 								                <td style="padding:5pt;text-align:right;" class="text-right">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->tax_amount.'</p>
// 								                </td>
// 								            </tr>
// 								            <tr>
// 								                <td style="padding:5pt;text-align:right;" colspan="4" class="text-end fw-bold">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">Grand Total</p>
// 								                </td>
// 								                <td style="padding:5pt;text-align:right;" class="text-right fw-bold text-end">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->total_amount.'</p>
// 								                </td>
// 								            </tr>
//                                     </tbody>
//                                 </table>
//                             </div>
//                         </div>'; 
//           $content .= '<div class="invoice-bottom">
//                             <div class="row">
//                                 <div class="col-sm-6">
//                                 </div>
//                                 <div class="col-sm-6">
//                                     <div class="amount text-end">
//                                         <h3 class="inv-title-1" style="text-align:right;">Total Amount</h3>
//                                         <h1 style="text-align:right;">'.$rental_invoicesres->total_amount.'</h1>
//                                     </div>
//                                 </div>
//                             </div>
//                             <div class="note mt-3">
//                                 <p class="text-muted">'.$rental_invoicesres->printing_notes.'</p>
//                             </div>
//                         </div>
//                     </div>'; 

//         $content .= '</body></html>';

//         echo $content;
//         $return['content'] = $content;
//       	$html = $obj_pdf->writeHTML($content);
// 		//$obj_pdf->writeHTML($html, true, false, false, false, '');
// 		$filename = "Test_invoice".time();
// 		$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$filename.'.pdf';
// 		// $obj_pdf->Output($sFilePath,'I');
// 		// $obj_pdf->Output($sFilePath,'F');
// 		// $obj_pdf->Output($sFilePath,'D');
// 		$obj_pdf->Output($sFilePath,'S');
// 		//$obj_pdf->Output($filename,'D');
// 		echo '<a href="http://www.tickday.infistaq.com/uploads/rental_invoices/'.$filename.'.pdf" download>'.$filename.'</a>';
// 		//$obj_pdf->Output($sFilePath, 'D');
// 		$return['pdf_filename'] = $filename.'.pdf';
// 		//$pdfContent = $obj_pdf->Output($sFilePath,'S');
// 		// $emailarray['attachment'] = "Y";
// 		// $emailarray['attachment_data'] = $pdfContent;
// 		// $emailarray['attachment_file_name'] = $time.'.pdf';
// 		// $notarry = array();
// 		// $email  =$this->sendNotificationAndEmails($notarry,$emailarray,33);
// 		// $retrun['email'] = $email; 
// 		//return $return;
// }

// function generateTenantInvoicePDF($get,$rid=""){
// 		global $callConfig;
// 		if($rid ==""){
// 			$rid = $get['rid'];
// 		}
// 		$return = array();
// 		$configsql = "SELECT * FROM config WHERE company_id = '".$get['company_id']."' AND is_active =0";
// 		$configres=$callConfig->getRow($configsql);

// 		$rental_invoicessql = "SELECT * FROM rental_invoices WHERE company_id = '".$get['company_id']."' AND id = '".$rid."'  AND is_active =0";
// 		$rental_invoicesres=$callConfig->getRow($rental_invoicessql);

// 		$rental_invoice_itemsaql = "SELECT * FROM rental_invoice_items WHERE company_id = '".$get['company_id']."' AND transaction_id = '".$rid."'  AND is_active =0";
// 		$rental_invoice_items=$callConfig->getAllRows($rental_invoice_itemsaql);
// 		$emailarray['to_company_id'] = $get['company_id'];
// 		$emailarray['session_company_id'] = $get['company_id'];
// 		$emailarray['user_company_id'] = $get['company_id'];
// 		$emailarray['user_display_name'] = $rental_invoicesres->party_name;
// 		$emailarray['user_email_id'] = $rental_invoicesres->party_email;
// 		$emailarray['transaction_number'] = $rental_invoicesres->transaction_number;
// 		$emailarray['transaction_date'] = $rental_invoicesres->transaction_date;
// 		$emailarray['total_amount'] = $rental_invoicesres->total_amount;
// 		$emailarray['created_by'] = $get['user_id'];

// 		require('htmltopdfconverterTCPDF/tcpdf.php');   
// 	    $obj_pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);  
// 	    $obj_pdf->SetCreator(PDF_CREATOR);  
// 	    // $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
// 	    // $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
// 	    // $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
// 	    //$obj_pdf->SetDefaultMonospacedFont('helvetica');  
// 	    $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
// 	    $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
// 	    $obj_pdf->setPrintHeader(false);  
// 	    $obj_pdf->setPrintFooter(false);  
// 	    $obj_pdf->SetAutoPageBreak(TRUE, 10); 
// 	    //$obj_pdf->SetAutoPageBreak('PDF_PAGE_FORMAT', 'A4'); 
// 	    //$obj_pdf->SetFont('helvetica', '', 12);  
// 	    //$AddPage->AddPage('P','A5');
// 	    $obj_pdf->AddPage();  
// 	    $content = '';
// 	    $content .= '<html><body style=" width: 210mm; min-height: 297mm; padding: 20mm;margin: 10mm auto;border: 1px #D3D3D3 solid;border-radius: 5px;background: white;box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);">';
// 	    $content .= '<div class="invoice-top" >
//                             <table class="table" style="width:100%;margin:0;">
//                                 <tr>
//                                     <td>
//                                         <div class="invoice">
//                                             <h4 class="inv-header-1" style="margin:0;">Rental Invoice</h4>
//                                             <h4 class="inv-header-1" style="margin:0;">'.$rental_invoicesres->party_name.'</h4>
//                                         </div>
//                                     </td>
//                                     <td>
//                                         <div class="invoice-name text-end">
//                                             <div class="logo text-white">
//                                                 <img style="width:150px; float: right;" class="logo" src="uploads/all/'.$configres->display_logo.'" alt="logo">
//                                             </div>
                                            
//                                         </div>
//                                     </td>
//                                 </tr>
//                             </table>
//                             <table class="table" style="width:100%;margin:0;">
// 						        <tr>
// 						            <td>
// 						                <div class="informeshon">
// 						                	<p style="margin:0;">Rental Name: '.$rental_invoicesres->party_name.'<br/><br/>
// 							                	Email: '.$rental_invoicesres->party_email.'<br/><br/>
// 							                	Phone: '.$rental_invoicesres->party_phone.'<br/><br/>
// 							                    Taxes: '.$rental_invoicesres->party_tax_number.'
// 						                    </p>
// 						                </div>
// 						            </td>
// 						            <td>
// 						                <div class="informeshon">
// 						                    <p style="margin:0;">Transaction Date &nbsp;&nbsp;: &nbsp;&nbsp; '.$rental_invoicesres->transaction_date.'<br/><br/>
// 						                    Payment Terms &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp; NET +  '.$rental_invoicesres->payment_terms.' DAYS<br/><br/>
// 						                    Place Of Supply &nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;'.$rental_invoicesres->place_of_supply.'
// 						                    </p>
// 						                </div>
// 						            </td>
// 						        </tr>
// 						    </table>
// 						    <table class="table" style="width:100%;margin:0;">
// 						        <tr>
// 						            <td>
// 						                <div class="informeshon">
// 						                    <p class="inv-title-1" style="margin:0;padding-top:5pt;">Billing Adderss:</p>
// 						                    <p class="invo-addr-1" style="margin:0;padding-top:5pt;">'.$rental_invoicesres->party_address.'<br/></p>
// 						                    <p class="inv-title-1" style="margin:0;padding-top:5pt;">Shiping Adderss:</p>
// 						                    <p class="invo-addr-1" style="margin:0;padding-top:5pt;">'.$rental_invoicesres->party_ship_adderss.'<br/>
// 						                    </p>
// 						                </div>
// 						            </td>
// 						        </tr>
// 						    </table>
//                         </div>'; 
//            $content .= '<div class="invoice-center">
//                             <div class="table-section table-responsive clearfix">
//                                 <table class="table caption-top invoice-table" style="width:100%;margin:0;">
//                                     <thead class="bg-active">
// 	                                    <tr>
// 						                    <th style="padding:5pt;text-align:left;">Name</th>
// 						                    <th style="padding:5pt;text-align:left;">Descrpition</th>
// 						                    <th style="text-align:right;padding:5pt;" class="">Price</th>
// 						                    <th style="text-align:right;padding:5pt;" class="">Amount</th>
// 						                    <th style="text-align:right;padding:5pt;" class="">Total</th>
// 						                </tr>
//                                     </thead>
//                                     <tbody>';
//                                     $i=1;  foreach ($rental_invoice_items as $item) {
//                                     $tablecontent .= '<tr>
// 							                    <td style="text-align:left;padding:5pt;font-size:10px;">
// 							                    	<p style="text-align:left;margin:0;">'.$item->item_name.'</p>
// 							                    </td>
// 							                    <td style="text-align:left;padding:5pt;font-size:10px;">
// 							                    	<p style="text-align:left;margin:0;">'.$item->description.'</p>
// 							                    </td>
// 							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
// 							                    	<p style="text-align:right;margin:0;">'.$item->item_price.'</p></td>
// 							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
// 							                    	<p style="text-align:right;margin:0;">'.$item->item_amount.'</p>
// 							                    </td>
// 							                    <td style="text-align:right;padding:5pt;font-size:10px;" class="">
// 							                    	<p style="text-align:right;margin:0;">'.$item->total_amount.'</p>
// 							                    </td>
// 							                </tr>';
//                                   $i++; }
//                                   	$content .= $tablecontent;
//                                     $content .= '<tr>
// 								                    <td style="padding:5pt;text-align:right;" colspan="4" class="">
// 								                    	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">SubTotal</p>
// 								                    </td>
// 								                    <td style="padding:5pt;text-align:right;" class="text-right text-end">
// 								                    	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->total_amount.'</p>
// 								                    </td>
// 								            </tr>
// 								            <tr>
// 								                <td style="padding:5pt;text-align:right;" colspan="4" class="text-end">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">Tax</p>
// 								                </td>
// 								                <td style="padding:5pt;text-align:right;" class="text-right">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->tax_amount.'</p>
// 								                </td>
// 								            </tr>
// 								            <tr>
// 								                <td style="padding:5pt;text-align:right;" colspan="4" class="text-end fw-bold">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">Grand Total</p>
// 								                </td>
// 								                <td style="padding:5pt;text-align:right;" class="text-right fw-bold text-end">
// 								                	<p style="font-weight:bold;text-align:right;margin:0;display:inline-block;padding:0;">'.$rental_invoicesres->total_amount.'</p>
// 								                </td>
// 								            </tr>
//                                     </tbody>
//                                 </table>
//                             </div>
//                         </div>'; 
//           $content .= '<div class="invoice-bottom">
//                             <div class="row">
//                                 <div class="col-sm-6">
//                                 </div>
//                                 <div class="col-sm-6">
//                                     <div class="amount text-end">
//                                         <h3 class="inv-title-1" style="text-align:right;">Total Amount</h3>
//                                         <h1 style="text-align:right;">'.$rental_invoicesres->total_amount.'</h1>
//                                     </div>
//                                 </div>
//                             </div>
//                             <div class="note mt-3">
//                                 <p class="text-muted">'.$rental_invoicesres->printing_notes.'</p>
//                             </div>
//                         </div>
//                     </div>'; 

//         $content .= '</body></html>';

//         echo $content;
//         $return['content'] = $content;
//       	$html = $obj_pdf->writeHTML($content);
// 		//$obj_pdf->writeHTML($html, true, false, false, false, '');
// 		$filename = "Test_invoice".time();
// 		$sFilePath = '/home/infistaq/live_infistaq_com/uploads/rental_invoices/'.$filename.'.pdf';
// 		$obj_pdf->Output($sFilePath,'F');
// 		//$obj_pdf->Output($filename,'D');
// 		echo '<a href="http://www.tickday.infistaq.com/uploads/rental_invoices/'.$filename.'.pdf" download>'.$filename.'</a>';
// 		//$obj_pdf->Output($sFilePath, 'D');
// 		$return['pdf_filename'] = $filename.'.pdf';
// 		//$pdfContent = $obj_pdf->Output($sFilePath,'S');
// 		// $emailarray['attachment'] = "Y";
// 		// $emailarray['attachment_data'] = $pdfContent;
// 		// $emailarray['attachment_file_name'] = $time.'.pdf';
// 		// $notarry = array();
// 		// $email  =$this->sendNotificationAndEmails($notarry,$emailarray,33);
// 		// $retrun['email'] = $email; 
// 		//return $return;

// 	}
	function getStandardMissiongReport($get){
		global $callConfig;
		$return = array();
		$db = $get['data_name'];
		$tname =$get['tname'];
		$return['table_list'] = $this->listofTables($db);
		if($tname !=""){
			$sql = "SELECT * FROM ".$tname." WHERE company_id = '".$get['company_id']."' AND is_active = 0";
			$res = $callConfig->getAllRows($sql);
		}	
		$return['transaction_res'] = $res;
		return $return;
	}

	function NewUserformateNew($post,$db_name){
		global $callConfig;
		$whr="db_name = '".$db_name."'";
		$query=$callConfig->selectQuery(MAINDB.'.'.TBL_DATABASE_ALLOCATIONS,'*',$whr,'','','');
		$row=$callConfig->getRow($query);
		if(isset($row->id)){
			$db_id = $row->id;
			$whrs="zqr_connect= '".$db_id."'";
			$querys=$callConfig->selectQuery(MAINDB.'.'.TBL_REGISTIRED_GROUPS,'*',$whrs,'','','');
			$rowg=$callConfig->getRow($querys);
			if(isset($rowg->id)){	
				$user_registired_groups = array('group_id'=>$rowg->id,'user_email'=>$post['user_email'],'zqr_connect'=>$db_id,'current_status'=>'0','is_active' =>'0');
				$res_rg_group_user = $callConfig->insertRecord(MAINDB.'.'.TBL_USER_REGISTIRED_GROUPS,$user_registired_groups);
				$post['user_registered_group_id'] = $res_rg_group_user['LASTID'];
				
				$notarry = array();
				if($post['user_type'] != '5'){
					$useridd[$post['user_id']] = $post['user_id'];
					$msglist = $post['display_name']." User successfully created";
					$notarry = array('company_id'=>$get['company_id'],'notification_message'=>$msglist, 'id'=>'','application_table_name'=>'application_users','type'=>'INFORMATION', 'notification_users'=>$useridd,'option'=>'','assigner_id'=>$post['user_id']);
					$emill = $this->sendNotificationAndEmails($notarry,$post,$post['template_reference_id']);
					$return['emill']= $emill;
				}
			}else{
				$return['error'] = "Register Group not found";
			}	
		}else{
			$return['error'] = "Database Allocation not found";
		}	
		return $return;
	}  





}
?>