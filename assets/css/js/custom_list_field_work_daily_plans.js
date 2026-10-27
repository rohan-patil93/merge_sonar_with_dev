function selectUser(element) {
    const selectedManagers = $(element).val();
    if (!selectedManagers || selectedManagers.length === 0) {
        $("#user_id").html('<option value="0">Select Any one</option>');
        return;
    }
    console.log(selectedManagers);
    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: {
            page: 'getEmployeeData',
            user_id: selectedManagers,
        },
        success: function (response) {

            response = JSON.parse(response);
            const data = response['qeury_reslut'];
            // Clear existing options
            $("#user_id").html('<option value="0">Select Any one</option>');

            // Check if response contains data
            if (data && data.length > 0) {
                data.forEach(user => {
                    console.log('user:', user);
                    // Append each user to the select box
                    $("#user_id").append(
                        `<option value="${user.id}">${user.display_name}</option>`
                    );
                });
            } else {
                // Show error if no users are found
                $("#user_id_err").text("No users found for the selected managers").show();
            }
        },
        error: function (error) {
            console.error("Error fetching users:", error);
            $("#user_id_err").text("Failed to fetch users. Please try again.").show();
        }
    });

}
function handleFromDateChange() {
    var fromDateInput = document.getElementById("from_date");
    var toDateInput = document.getElementById("to_date");

    // Set the minimum value of the To Date field to the From Date value
    toDateInput.min = fromDateInput.value;
    if (toDateInput.value < fromDateInput.value) {
        toDateInput.value = fromDateInput.value;
    }
}

$(document).ready(function () {
    // Attach change listener for manager_id using jQuery (required for Select2 compatibility)
    $('#manager_id').on('change', function () {
        console.log('Manager ID changed:', $(this).val());
        selectUser(this);
    });
    
    // Attach change listener for from_date
    $('#from_date').on('change', function() {
        console.log('From date changed:', $(this).val());
        handleFromDateChange();
    });

    const pageEl = document.getElementById('fieldWorkDailyPlansPage');

    const adminType = pageEl.dataset.adminType;
    const userId = pageEl.dataset.userId;
    const applicationUsers = JSON.parse(pageEl.dataset.appUsers);
    if (adminType == "2") {
        const managerInput = $('<input>', {
            type: 'hidden',
            id: 'manager_id',
            name: 'manager_id',
        });

        $(".card-body").append(managerInput);
        $("#manager_id").val(userId).trigger("change");
        selectUser(managerInput);
    } else if (adminType == "3") {
        const userInput = $('<input>', {
            type: 'hidden',
            id: 'user_id',
            name: 'user_id',
            value: userId,
        });
        $(".card-body").append(userInput);
    }
    // Handle button click
    $('#getDataButton').on('click', function () {
        // Get selected values
        let manager_ids = $('#manager_id').val();
        let user_ids = $('#user_id').val();
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        console.log('manager_ids: ', manager_ids);
        console.log('user_ids: ', user_ids);

        // Validate input
        // if (!user_ids || user_ids.length === 0) {
        //     $('#user_id_err').text('Please select at least one user').show();
        //     return;
        // } else {
        //     $('#user_id_err').hide();
        // }

        if (!from_date) {
            $('#from_date_err').text('Please select a from date').show();
            return;
        } else {
            $('#from_date_err').hide();
        }
        if (!to_date) {
            $('#to_date_err').text('Please select a from date').show();
            return;
        } else {
            $('#to_date_err').hide();
        }

        // Send AJAX request
        $.ajax({
            url: 'ajax.php', // Replace with your backend script
            method: 'POST',
            data: {
                page: 'getFieldWorkDailyPlanData',
                manager_id: manager_ids,
                user_id: user_ids,
                from_date: from_date,
                to_date: to_date,
            },
            success: function (response) {
                console.log('response', response);
                const data = JSON.parse(response);
                document.getElementById('workType1').classList.remove('d-none');

                const tableBody = document.querySelector('#fiedWorkTable tbody');

                // Clear existing content
                tableBody.innerHTML = '';

                // Check if data is empty
                if (data.length === 0) {
                    // Show the "No Records" message
                    tableBody.innerHTML = `
                        <tr>
                        <td colspan="8">
                            <div class="text-center">
                                <p class="text-muted f-16 fw-bold m-b-30">
                                    <i class="fa fa-warning"></i> No Records
                                </p>   
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px work-img" alt="" src="assets/images/work.png" >
                                </div>   
                            </div>
                            </td>
                            </tr>
                        `;
                } else {
                    let i = 1;
                    const currentDate = new Date().toISOString().split('T')[0];
                    // Loop through the data and populate the table

                    data.forEach(item => {
                        const isDisabled = item.from_date <= currentDate ? 'disabled-link' : '';
                        const newRow = `
                                <tr>
                                    <td class=''>${i}</td>
                                    <td>${item.display_name || item.id}</td>
                                    <td>${item.location || ''}</td>
                                    <td>${item.lat || ''}</td>
                                    <td >${item.lng || ''}</td>
                                    <td class=''>${item.from_date || ''}</td>
                                    <td class=''>${item.to_date || ''}</td>
                                    <td class=''>
                                    <a  href="index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS&user_id=${item.id}&date=${item.from_date}" class="btn btn-sm btn-clean btn-icon btn-icon-sm ${isDisabled}" title="Edit Details"><i class="fa fa-edit"></i></a>
                                    <a href="index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS&user_id=${item.id}&date=${item.from_date}&view=1" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                    <a href="index.php?option=COM_INSERT_FIELD_WORK_DAILY_PLANS&user_id=${item.id}&date=${item.from_date}&copy=1" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Copy Details"><i class="fa fa-clone"></i></a>
                                
                                    </td>
                                </tr>`;
                        tableBody.innerHTML += newRow;
                        i++;
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
                $('#result-container').html('<p>Error fetching data</p>');
            }
        });
    });
});
