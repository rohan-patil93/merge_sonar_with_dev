<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">EEO-1</h5>
      <h6 class="mt-4"><?php echo count((array)$result['emp_data']); ?> Active Employees</h6>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <form method="post" action="">

        <?php include_once 'views/custom_standard_report_hr_employee_filter.php'; ?>
        <div class="col-lg-2 mt-1">
          <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
            Apply
          </button>
          <input type="hidden" name="submit" value="applyform">
        </div>

        </form>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover table-bordered">
          <tr style="text-align: center;" class="align-middle">
            <th scope="col" rowspan="6">Job Categories</th>
            <th scope="col" colspan="15" class="text-center">
              <span>Number of Employees</span>
              <span class="d-block">(Report employees in only one category)</span>
            </th>
          </tr>
          <tr style="text-align: center;">
            <th scope="col" colspan="15">Race/Ethnicity</th>
          </tr>
          <tr class="center">
            <th scope="col" class="text-center" colspan="2" rowspan="2">Hispanic or Latino</th>
            <th scope="col" class="text-center" colspan="12">Not-Hispanic or Latino</th>
            <th scope="col" class="text-center align-middle" rowspan="3">Total Col A-N</th>
          </tr>
          <tr>
            <th scope="col" class="text-center" colspan="6">Male</th>
            <th scope="col" class="text-center" colspan="6">Female</th>
          </tr>
          <tr style="text-align: center;">
            <th scope="col" class="align-middle">Male</th>
            <th scope="col" class="align-middle">Female</th>
            <th scope="col" class="align-middle">White</th>
            <th scope="col" class="align-middle">Black or<br />African<br />American</th>
            <th scope="col" class="align-middle">Native<br />Hawaiian or<br />Other<br />Pacific<br />Islander</th>
            <th scope="col" class="align-middle">Asian</th>
            <th scope="col" class="align-middle">American<br />Indian or<br />Alaska<br />Native</th>
            <th scope="col" class="align-middle">Two or<br />more<br />races</th>
            <th scope="col" class="align-middle">White</th>
            <th scope="col" class="align-middle">Black or<br />African<br />American</th>
            <th scope="col" class="align-middle">Native<br />Hawaiian or<br />Other<br />Pacific<br />Islander</th>
            <th scope="col" class="align-middle">Asian</th>
            <th scope="col" class="align-middle">American<br />Indian or<br />Alaska<br />Native</th>
            <th scope="col" class="align-middle">Two or<br />more<br />races</th>
          </tr>
          <tr style="text-align: center;">
            <th scope="col">A</th>
            <th scope="col">B</th>
            <th scope="col">C</th>
            <th scope="col">D</th>
            <th scope="col">E</th>
            <th scope="col">F</th>
            <th scope="col">G</th>
            <th scope="col">H</th>
            <th scope="col">I</th>
            <th scope="col">J</th>
            <th scope="col">K</th>
            <th scope="col">L</th>
            <th scope="col">M</th>
            <th scope="col">N</th>
            <th scope="col">O</th>
          </tr>
          <tr>
            <th scope="col">Executive/Senior Level Officials and Managers</th>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">4</td>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">2</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">9</td>
          </tr>
          <tr>
            <th scope="col">First/Mid Level Officials and Managers</th>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">5</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;">1</td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;"></td>
            <td style="text-align: center;">9</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
