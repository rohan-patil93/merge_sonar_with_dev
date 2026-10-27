(function() {
    'use strict';

    var selectedemp = "";

    function getPhpData() {
        var dataContainer = document.querySelector('.filter-bulktransfer-php-data');
        if (!dataContainer) return {};
        return {
            empListNames: dataContainer.dataset.empListNames ? JSON.parse(dataContainer.dataset.empListNames) : null,
            selectValue: dataContainer.dataset.selectValue ? JSON.parse(dataContainer.dataset.selectValue) : {}
        };
    }

    function populatenamelist() {
        console.log("******************");
        jQuery(document).ready(function() {    
            if (typeof KTBootstrapDatepicker !== 'undefined') {
                KTBootstrapDatepicker.init();
            }
        });
        
        var phpData = getPhpData();
        var data = phpData.empListNames;
        if (!data) return;
        
        var result = JSON.stringify(data);
        result = JSON.parse(result);
    }

    function selectemployee() {
        var phpData = getPhpData();
        var select_value = phpData.selectValue;
        var application_table = document.getElementById('tablename').value;

        console.log(select_value);
        var str = "";
        var obj = {}; 
        
        for(var key in select_value) {
            console.log("Key: " + key + " value: " + select_value[key]);
            var elements = document.getElementById(select_value[key]).childNodes;
            var selectedKeyValue = {};
            var arrayOfSelecedIDs = [];

            for(var i = 0; i < elements.length; i++) {
                if(elements[i].selected) {
                    selectedKeyValue[elements[i].value] = elements[i].textContent;
                    obj[key] = selectedKeyValue;
                    arrayOfSelecedIDs.push(elements[i].value);
                }
            }
            str += '&' + key + '=' + arrayOfSelecedIDs;
        }
        
        console.log('sha' + str);

        $.ajax({
            url: "ajax.php",
            data: 'page=selectedfilter&table=' + application_table + str,
            type: "POST",
            success: function(data) {
                console.log('data' + data);
                var result = JSON.parse(data);
                document.getElementById("matches_count").innerHTML = result.count;
                obj['matches_count'] = result.count;
                obj['matches_names'] = result.details;

                var jsonData = {};
                for(var i = 0; i < result.count; i++) {
                    var ids = result.details[i].id_value;
                    jsonData[ids] = ids;
                }
                var json_empids = JSON.stringify(jsonData);
                console.log('data' + json_empids);
                document.getElementById('employee_names_list').value = json_empids;

                var maxlimit = 10;
                var more = 0;
                if(result.count > maxlimit) {
                    more = result.count - maxlimit;
                } else {
                    maxlimit = result.count;
                } 
                
                var imagelist = '  <label>Employees</label> <div class="text-center text-center"><div class="symbol-group symbol-hover justify-content-center">';
                for(var i = 0; i < maxlimit; i++) {
                    var imgpath = "uploads/all/" + result.details[i].image;
                    if(result.details[i].image != '') {
                        imagelist += '<div class="symbol symbol-35 symbol-circle" data-toggle="tooltip" title="' + result.details[i].display_name + '"><img alt="Pic" class="img-30" src="' + imgpath + '" /></div>';
                    } else {
                        imagelist += '<div class="symbol symbol-35 symbol-circle symbol-light-success img-30" data-toggle="tooltip" title="' + result.details[i].display_name + '"><span class="symbol-label font-weight-bold">' + getInitials(result.details[i].display_name) + '</span></div>';
                    }
                }
                
                if(more > 0) {
                    imagelist += '<div class="symbol symbol-35 symbol-circle symbol-light-warning" data-toggle="tooltip" title="' + more + ' More"><a class="filter-bulktransfer-more-employees"><span class="symbol-label font-weight-bold">' + more + '+</span></a></div>';
                }
                imagelist += '</div></div>';
                
                document.getElementById("empimages").innerHTML = imagelist;

                var jsonresponse = JSON.stringify(obj); 
                console.log('inserted to filtertable' + jsonresponse);
                selectedemp = jsonresponse;
                
                document.getElementById('filterlist').value = jsonresponse;
            },
            error: function() {}
        });
    }

    function moreemployees() {
        var employee_names_list = document.getElementById("employee_names_list").value;
        jQuery.ajax({
            url: "views/custom_view_employees_popup.php",
            data: 'empids=' + employee_names_list,
            type: "GET",
            success: function(data) {
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true });
            },
            error: function() {}
        });
    }

    function showfilter(value) {
        if(value == 'SE') {
            document.getElementById('filtershow').style.display = 'block';
        } else {
            document.getElementById('filtershow').style.display = 'none';
        }
    }

    function submitrom() {
        var application_table = document.getElementById('tablename').value;
        var selectmain = document.getElementById('filterfor').value;
        
        if(selectmain === 'AE') {
            selectedemp = 'All Employees';
        } else {
            var selectsub = document.getElementById('filterlist').value;
        }
        
        var sendjsoncontent = document.getElementById('filterlist').value;
        sendjsoncontent = JSON.stringify(sendjsoncontent); 

        console.log(sendjsoncontent + application_table);
        
        $.ajax({
            url: "ajax.php",
            data: 'page=sendtofiltertable&appln_table=' + application_table + '&jsoncontent=' + sendjsoncontent,
            type: "POST",
            success: function(response) {
                console.log("Inserted row id =" + response);
                document.getElementById('filtertbl_rowid').value = response;
                $('#kt_modal_dynamic_popup').modal('hide');
                if (typeof getnamelist === 'function') {
                    getnamelist(selectedemp);
                }
            }
        });
    }

    function getInitials(name) {
        var parts = name.split(' ');
        var initials = '';
        for (var i = 0; i < parts.length; i++) {
            if (parts[i].length > 0 && parts[i] !== '') {
                initials += parts[i][0];
            }
        }
        return initials;
    }

    function bindEvents() {
        $(document).on('change', '.filter-bulktransfer-filterfor', function() {
            showfilter(this.value);
        });

        $(document).on('change', '.filter-bulktransfer-empstatus', function() {
            selectemployee();
        });

        $(document).on('click', '.filter-bulktransfer-submit-btn', function(e) {
            e.preventDefault();
            submitrom();
        });

        $(document).on('click', '.filter-bulktransfer-more-employees', function(e) {
            e.preventDefault();
            moreemployees();
        });
    }

    function initSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.js-example-basic-single').select2({ width: '100%' });
            $('.js-example-basic-multiple').select2({ width: '100%' });
        }
    }

    function init() {
        bindEvents();
        initSelect2();
        
        var phpData = getPhpData();
        if (phpData.empListNames) {
            populatenamelist();
        }
    }

    $(document).ready(function() {
        init();
    });

})();
