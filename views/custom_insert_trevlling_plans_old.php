
<?php $_SESSION['apply_js']['multiselest'] = 1; ?>


<div class="card">
    <div class="card-header">
        <h5>New Trip</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group m-l-5">
                    <label class="form-label">Trip Name <b class="text-danger">*</b></label>
                    <input type="text" class="form-control b-radius-5">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group m-l-5">
                    <label class="form-label">Trip Type <b class="text-danger">*</b></label>
                    <div class="form-radio">
                        <form>
                            <div class="radio radio-inline">
                                <label class="form-label mb-0">
                                    <input type="radio" name="radio" checked="checked">
                                    <i class="helper"></i>Domestic
                                </label>
                            </div>
                            <div class="radio radio-inline">
                                <label class="form-label mb-0">
                                    <input type="radio" name="radio">
                                    <i class="helper"></i>International
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group m-l-5">
                    <label class="form-label">Business Purpose</label>
                    <textarea class="form-control"></textarea>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group m-l-5">
                    <label class="form-label">Project</label>
                    <select class="form-control js-example-basic-single" style="-webkit-appearance:auto;-moz-appearance:auto;appearance:auto;">
                        <option>Project 1</option>
                        <option>Project 2</option>
                        <option>Project 3</option>
                        <option>Project 4</option>
                    </select>   
                </div>
            </div>
            <div class="col-lg-2">
                <div class="form-group m-l-5">
                    <label class="form-label">Budget Amount</label>
                    <div class="input-group">
                        <span class="input-group-text addon-style">
                            <label class=""><i class="fa fa-inr"></i></label>
                        </span>
                        <input type="text" class="form-control" placeholder="0.00">
                    </div>   
                </div>
            </div>
            <div class="col-lg-2" style="">
                <div class="form-group m-l-5">
                    <label class="form-label">Final Amount</label>
                    <div class="input-group">
                        <span class="input-group-text addon-style">
                            <label class=""><i class="fa fa-dollar"></i></label>
                        </span>
                        <input type="text" class="form-control" placeholder="0.00">
                    </div>   
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group m-l-5">
                    <label class="form-label">Client</label>
                    <select class="form-control js-example-basic-single" style="-webkit-appearance:auto;-moz-appearance:auto;appearance:auto;">
                        <option>Client 1</option>
                        <option>Client 2</option>
                        <option>Client 3</option>
                        <option>Client 4</option>
                    </select>   
                </div>
            </div>
        </div>
    </div>
    <div class="card-header mt-1">
        <h5 class="f-w-600">Trip Itinerary</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12 m-l-5 mt-2">
                <div class="form-radio" id="tripTypeGroup">
                    <label class="radio radio-inline d-inline-block">
                        <input type="radio" name="tripType" class="trip-radio" value="oneway" data-target=".one-way" checked>
                        <i class="helper"></i> One Way
                    </label>
                    <label class="radio radio-inline d-inline-block">
                        <input type="radio" name="tripType" class="trip-radio" value="roundtrip" data-target=".round-trip">
                        <i class="helper"></i> Round Trip
                    </label>
                    <label class="radio radio-inline d-inline-block">
                        <input type="radio" name="tripType" class="trip-radio" value="multicity" data-target=".multi-city">
                        <i class="helper"></i> Multi City
                    </label>
                </div>

            </div>
        </div>
        <div class="row mt-3 one-way">
            <div class="col-lg-12 new-trip">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>#</th>
                                <th>Depart From</th>
                                <th>Arrive At</th>
                                <th>Departure Date</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Flight</option>
                                        <option>Car</option>
                                        <option>Bus</option>
                                        <option>Traveller</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Mumbai</option>
                                        <option>Goa</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Mumbai</option>
                                        <option>Goa</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control">
                                </td>
                                <td>
                                    <textarea class="form-control"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <h4 class="mt-4">Hotel</h4>
                <div class="table-responsive mt-1">
                    <table class="table table-hover" id="locationTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>Location</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="locationTableBody">
                            <tr>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control checkin-input">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control checkout-input">
                                </td>
                                <td>
                                    <textarea class="form-control description-text"></textarea>
                                </td>
                                <td>
                                    <i class="fa fa-trash text-danger remove-row" style="cursor:pointer"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" id="addLocationRowBtn" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Hotel
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <h4 class="mt-4">Car Rent</h4>
                <div class="table-responsive mt-1">
                    <table class="table table-hover" id="carRentalTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>Pick-Up Location</th>
                                <th>Drop Location</th>
                                <th>Pick-Up Date</th>
                                <th>Drop Date</th>
                                <th>Car Type</th>
                                <th>Driver Needed</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="carRentalTableBody">
                            <tr>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control car-pickup">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control car-dropoff">
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Small</option>
                                        <option>Medium</option>
                                        <option>Large</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-radio">
                                        <form>
                                            <div class="radio radio-inline">
                                              <label class="form-label">
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Yes
                                              </label>
                                            </div>
                                            <div class="radio radio-inline">
                                              <label class="form-label">
                                                <input type="radio" name="radio">
                                                <i class="helper"></i>No
                                              </label>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <textarea class="form-control car-description"></textarea>
                                </td>
                                <td>
                                    <i class="fa fa-trash text-danger car-remove-row" style="cursor:pointer"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7">
                                    <button type="button" id="addCarRentalBtn" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Car Rental
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-3 round-trip" style="">
            <div class="col-lg-12 new-trip">
                <div class="table-responsive">
                    <table class="table table-hover" id="myTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>#</th>
                                <th>Depart From</th>
                                <th>Arrive At</th>
                                <th>Departure Date</th>
                                <th>Return Date</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Flight</option>
                                        <option>Car</option>
                                        <option>Bus</option>
                                        <option>Traveller</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Mumbai</option>
                                        <option>Goa</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Mumbai</option>
                                        <option>Goa</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control">
                                </td>
                                <td>
                                    <textarea class="form-control"></textarea>
                                </td>
                                <td><i class="fa fa-trash text-danger deleteRow" style="cursor:pointer"></i></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button id="addRowBtn" type="button" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add More
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <h4 class="mt-4">Hotel</h4>
                <div class="table-responsive mt-1">
                    <table class="table table-hover" id="locationTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>Location</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="locationTableBody">
                            <tr>
                                <td>
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control checkin-input">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control checkout-input">
                                </td>
                                <td>
                                    <textarea class="form-control description-text"></textarea>
                                </td>
                                <td>
                                    <i class="fa fa-trash text-danger remove-row" style="cursor:pointer"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" id="addLocationRowBtn" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Hotel
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <h4 class="mt-4">Car Rent</h4>
                <div class="table-responsive mt-1">
                    <table class="table table-hover" id="carRentalTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>Pick-Up Location</th>
                                <th>Drop Location</th>
                                <th>Pick-Up Date</th>
                                <th>Drop Date</th>
                                <th>Car Type</th>
                                <th>Driver Needed</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="carRentalTableBody">
                            <tr>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control car-pickup">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control car-dropoff">
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Small</option>
                                        <option>Medium</option>
                                        <option>Large</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-radio">
                                        <form>
                                            <div class="radio radio-inline">
                                              <label class="form-label">
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Yes
                                              </label>
                                            </div>
                                            <div class="radio radio-inline">
                                              <label class="form-label">
                                                <input type="radio" name="radio">
                                                <i class="helper"></i>No
                                              </label>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <textarea class="form-control car-description"></textarea>
                                </td>
                                <td>
                                    <i class="fa fa-trash text-danger car-remove-row" style="cursor:pointer"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7">
                                    <button type="button" id="addCarRentalBtn" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Car Rental
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="row mt-3 multi-city" style="">
            <div class="col-lg-12 new-trip">
                <div class="table-responsive">
                    <table class="table table-hover" id="myTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>#</th>
                                <th>Depart From</th>
                                <th>Arrive At</th>
                                <th>Departure Date</th>
                                <th>Return Date</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <tr>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Flight</option>
                                        <option>Car</option>
                                        <option>Bus</option>
                                        <option>Traveller</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Mumbai</option>
                                        <option>Goa</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Mumbai</option>
                                        <option>Goa</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control">
                                </td>
                                <td>
                                    <textarea class="form-control"></textarea>
                                </td>
                                <td><i class="fa fa-trash text-danger deleteRow" style="cursor:pointer"></i></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button id="addRowBtn" type="button" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add More
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <h4 class="mt-4">Hotel</h4>
                <div class="table-responsive mt-1">
                    <table class="table table-hover" id="locationTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>Location</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="locationTableBody">
                            <tr>
                                <td>
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control checkin-input">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control checkout-input">
                                </td>
                                <td>
                                    <textarea class="form-control description-text"></textarea>
                                </td>
                                <td>
                                    <i class="fa fa-trash text-danger remove-row" style="cursor:pointer"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <button type="button" id="addLocationRowBtn" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Hotel
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <h4 class="mt-4">Car Rent</h4>
                <div class="table-responsive mt-1">
                    <table class="table table-hover" id="carRentalTable">
                        <thead>
                            <tr bgcolor="#eeeeee">
                                <th>Pick-up Location</th>
                                <th>Drop Location</th>
                                <th>Pick-Up Date</th>
                                <th>Drop Date</th>
                                <th>Car Type</th>
                                <th>Driver Needed</th>
                                <th>Description</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="carRentalTableBody">
                            <tr>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>New Delhi</option>
                                        <option>Jaipur</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control car-pickup">
                                </td>
                                <td>
                                    <input type="datetime-local" class="form-control car-dropoff">
                                </td>
                                <td class="w-full">
                                    <select class="form-select js-example-basic-single">
                                        <option>Small</option>
                                        <option>Medium</option>
                                        <option>Large</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-radio">
                                        <form>
                                            <div class="radio radio-inline">
                                              <label class="form-label">
                                                <input type="radio" name="radio" checked="checked">
                                                <i class="helper"></i>Yes
                                              </label>
                                            </div>
                                            <div class="radio radio-inline">
                                              <label class="form-label">
                                                <input type="radio" name="radio">
                                                <i class="helper"></i>No
                                              </label>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                                <td>
                                    <textarea class="form-control car-description"></textarea>
                                </td>
                                <td>
                                    <i class="fa fa-trash text-danger car-remove-row" style="cursor:pointer"></i>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7">
                                    <button type="button" id="addCarRentalBtn" class="btn btn-primary">
                                        <i class="fa fa-plus"></i> Add Car Rental
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Radio button click section change JS -->
<script>
document.addEventListener("DOMContentLoaded", function () {
  const sections = [
    document.querySelector(".one-way"),
    document.querySelector(".round-trip"),
    document.querySelector(".multi-city")
  ];

  function hideAll() {
    sections.forEach(s => { if (s) s.style.display = "none"; });
  }

  function show(selector) {
    hideAll();
    const sec = document.querySelector(selector);
    if (sec) sec.style.display = "block";
  }

  // Initial: agar koi selected hai to uska section dikhao,
  // warna sab hide (aapne One Way ko checked rakha hai to wo dikhega)
  const selected = document.querySelector(".trip-radio:checked");
  if (selected && selected.dataset.target) {
    show(selected.dataset.target);
  } else {
    hideAll();
  }

  // Change handler: sirf trip radios group par listen karo
  document.getElementById("tripTypeGroup").addEventListener("change", function (e) {
    if (e.target && e.target.classList.contains("trip-radio")) {
      const target = e.target.dataset.target;
      if (target) show(target);
    }
  });
});
</script>




<!-- Flight add JS -->
<script>
document.getElementById("addRowBtn").addEventListener("click", function () {
    let tableBody = document.getElementById("tableBody");

    // Create new row
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td class="w-full">
            <select class="form-select js-example-basic-single">
                <option>Flight</option>
                <option>Car</option>
            </select>
        </td>
        <td class="w-full">
            <select class="form-select js-example-basic-single">
                <option>Mumbai</option>
                <option>Goa</option>
            </select>
        </td>
        <td class="w-full">
            <select class="form-select js-example-basic-single">
                <option>Mumbai</option>
                <option>Goa</option>
            </select>
        </td>
        <td>
            <input type="datetime-local" class="form-control">
        </td>
        <td>
            <input type="datetime-local" class="form-control">
        </td>
        <td>
            <textarea class="form-control"></textarea>
        </td>
        <td><i class="fa fa-trash text-danger deleteRow" style="cursor:pointer"></i></td>
    `;

    // Insert new row at the top of tbody (just below header)
    tableBody.insertBefore(newRow, tableBody.firstChild);
});

// Delete row functionality
document.addEventListener("click", function(e){
    if(e.target && e.target.classList.contains("deleteRow")){
        e.target.closest("tr").remove();
    }
});
</script>



<!-- Hotel Location JS -->
<script>
document.getElementById("addLocationRowBtn").addEventListener("click", function () {
    let tableBody = document.getElementById("locationTableBody");

    // Create a new row
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td class="w-full">
            <select class="form-select js-example-basic-single">
                <option>New Delhi</option>
                <option>Jaipur</option>
            </select>
        </td>
        <td>
            <input type="datetime-local" class="form-control checkin-input">
        </td>
        <td>
            <input type="datetime-local" class="form-control checkout-input">
        </td>
        <td>
            <textarea class="form-control description-text"></textarea>
        </td>
        <td>
            <i class="fa fa-trash text-danger remove-row" style="cursor:pointer"></i>
        </td>
    `;

    tableBody.appendChild(newRow);
});

// Delete row functionality
document.addEventListener("click", function(e){
    if(e.target && e.target.classList.contains("remove-row")){
        e.target.closest("tr").remove();
    }
});
</script>


<!-- Car Rental JS -->
<script>
let carRowCount = 1;

document.getElementById("addCarRentalBtn").addEventListener("click", function () {
    carRowCount++;
    let tableBody = document.getElementById("carRentalTableBody");

    // Create a new row
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td class="w-full">
            <select class="form-select js-example-basic-single">
                <option>New Delhi</option>
                <option>Jaipur</option>
            </select>
        </td>
        <td class="w-full">
            <select class="form-select js-example-basic-single">
                <option>New Delhi</option>
                <option>Jaipur</option>
            </select>
        </td>
        <td>
            <input type="datetime-local" class="form-control car-pickup">
        </td>
        <td>
            <input type="datetime-local" class="form-control car-dropoff">
        </td>
        <td class="w-full">
            <select class="form-select js-example-basic-single">
                <option>Small</option>
                <option>Medium</option>
                <option>Large</option>
            </select>
        </td>
        <td>
            <div class="form-radio">
                <div class="radio radio-inline">
                    <label class="form-label">
                        <input type="radio" name="driverNeeded${carRowCount}" checked="checked">
                        <i class="helper"></i>Yes
                    </label>
                </div>
                <div class="radio radio-inline">
                    <label class="form-label">
                        <input type="radio" name="driverNeeded${carRowCount}">
                        <i class="helper"></i>No
                    </label>
                </div>
            </div>
        </td>
        <td>
            <textarea class="form-control car-description"></textarea>
        </td>
        <td>
            <i class="fa fa-trash text-danger car-remove-row" style="cursor:pointer"></i>
        </td>
    `;

    tableBody.appendChild(newRow);
});

// Delete row functionality
document.addEventListener("click", function(e){
    if(e.target && e.target.classList.contains("car-remove-row")){
        e.target.closest("tr").remove();
    }
});
</script>





