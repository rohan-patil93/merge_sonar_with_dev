
                    <div class="row">
                        <div class="col-lg-12 col-xl-4">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><?php echo __('Name'); ?></th>
                                            <td><?php echo $employees_detail['display_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php echo __('Department'); ?></th>
                                            <td><?php echo $employees_detail['department_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php echo __('Designation'); ?></th>
                                            <td><?php echo $employees_detail['designation_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php echo __('Qualification:'); ?></th>
                                            <td class="address-basic"><?php echo $employees_detail['employee_qualifications']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-4">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <tbody>

                                        <tr>
                                            <th scope="row"><?php echo __('Emp ID'); ?></th>
                                            <td><?php echo $employees_detail['emp_code']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php echo __('Date of Join'); ?></th>
                                            <td><?php echo fromDate($employees_detail['joining_date']); ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php echo __('Date of Confirmation'); ?></th>
                                            <td>-- </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php echo __('Previous years of Exp:'); ?></th>
                                            <td class="address-basic"><?php echo $employees_detail['total_experiance']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xl-4">
                            <div class="table-responsive">
                                <table class="table m-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row"><?php echo __('RO\'s Name'); ?></th>
                                            <td><?php echo $report_to_detail['display_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><?php echo __('RO Designation:'); ?></th>
                                            <td><?php echo $report_to_detail['designation_name']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

