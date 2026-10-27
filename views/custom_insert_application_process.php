<?php
$_SESSION['apply_js']['multiselest'] = "1";
if (isset($_POST['submit'])) {
    
    // Process form submission
    $selected_module = $_POST['module_id'] ?? '';
    $processes = $_POST['processes'] ?? [];
    $process_pages = $_POST['process_pages'] ?? [];
    $process_page_ids = $_POST['process_page_ids'] ?? [];
    // Prepare data to send to API
    $postData = [
        'module_id' => $selected_module,
        'processes' => []
    ];
    // Format processes and their pages
    foreach ($processes as $idx => $process) {
        $processData = [
            'name' => $process['name'],
            'module_id' => $process['module_id'],
            'pages' => []
        ];  
        // Add process ID if it exists (for existing processes)
        if (isset($process['id'])) {
            $processData['id'] = $process['id'];
        }
        // Get all page data for this process - fix to handle array structure properly
        if (isset($process_pages[$idx]) && isset($process_pages[$idx]['name']) && is_array($process_pages[$idx]['name'])) {
            $pageNames = $process_pages[$idx]['name'];
            $pageTypes = isset($process_pages[$idx]['type']) ? $process_pages[$idx]['type'] : [];
            $pageCustoms = isset($process_pages[$idx]['custom']) ? $process_pages[$idx]['custom'] : [];
            $pageIds = isset($process_page_ids[$idx]) ? $process_page_ids[$idx] : [];
            
            for ($i = 0; $i < count($pageNames); $i++) {
                $pageName = $pageNames[$i];
                $pageType = isset($pageTypes[$i]) ? $pageTypes[$i] : '';
                
                // If type is 'other', use the custom name
                // if ($pageType == 'other') {
                //     $pageName = $pageCustoms[$i];
                // }       
                $pageData = [
                    'name' => $pageName,
                    'type' => $pageType,
                    'custom' => isset($pageCustoms[$i]) ? $pageCustoms[$i] : ''
                ];
                // Add page ID if it exists (for existing pages)
                if (isset($pageIds[$i]) && !empty($pageIds[$i])) {
                    $pageData['id'] = $pageIds[$i];
                }
                
                $processData['pages'][] = $pageData;
            }
        }
        $postData['processes'][] = $processData;
    }
    // Send to API
    //echo "<pre>"; print_r($postData); echo "</pre>";
    $url = API . "?mode=insertmoduleprocess&method=post";
    $response = file_post_contents($url, $postData, 'insert');
    $response = json_decode($response, 1);
    //echo "<pre>"; print_r($response); echo "</pre>"; 
    ?>
    <script type="text/javascript">
        window.addEventListener("load", function() {
            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
        });
    </script>
<?php }

$url = API . "?mode=getmoduleprocesslist&method=get";
$che = callAPI($url);
$return = json_decode($che, 1);
$modules = $return['module_setup'];
$application_process = $return['application_process'];
$application_process_pages = $return['application_process_pages'];
$application_pages = $return['application_pages'];

?>

<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Module Process Setup</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body  m-r-50 m-l-50">
        <form class="kt-form" id="moduleProcessForm" method="post" action="" name="moduleProcessForm">
            <div class="row mb-4">
                <div class="col-md-5">
                    <label for="module_id" class="form-label">Select Module <span
                            class="text-danger">*</span></label>
                    <select class="js-example-basic-single form-select" id="module_id" name="module_id"
                        onchange="handleModuleChange(this.value)" required>
                        <option value="">Select Any one</option>
                        <?php foreach ($modules as $module): ?>
                            <option value="<?= $module['module_id'] ?>"><?= $module['module_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="process-container" class="mt-4" style="display: none;">
                <h6 class="mb-3">Process Configuration</h6>

                <div class="table-responsive">
                    <table class="table table-bordered" id="process-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="25%">Process Name</th>
                                <th width="65%">Pages</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Process rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-4 d-flex align-items-end mt-2 mb-4">
                        <button type="button" id="add-process-btn" class="btn btn-success">
                            + Add More Process
                        </button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12 text-left">
                        <button type="submit" name="submit"
                            class="btn btn-primary  mr-2">
                            <i class="la la-check"></i> Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Template for Process Pages -->
<template id="process-pages-template">
    <div class="process-pages mt-3">
        <div class="process-pages-container">
            <div class="row process-page-row mb-2">
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-success add-page-btn mt-2  mb-3">
            <i class="fa fa-plus"></i> Add Page
        </button>
    </div>
</template>

<style>
.error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px 15px;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    margin: 10px 0;
    display: none;
    position: relative;
}

.error-message .close-btn {
    position: absolute;
    right: 10px;
    top: 8px;
    background: none;
    border: none;
    font-size: 18px;
    color: #721c24;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-message .close-btn:hover {
    opacity: 0.7;
}

.success-message {
    background-color: #d4edda;
    color: #155724;
    padding: 10px 15px;
    border: 1px solid #c3e6cb;
    border-radius: 4px;
    margin: 10px 0;
    display: none;
    position: relative;
}

.success-message .close-btn {
    position: absolute;
    right: 10px;
    top: 8px;
    background: none;
    border: none;
    font-size: 18px;
    color: #155724;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>


<script>
    // Global variables for access outside the DOMContentLoaded event
    let applicationProcess = [];
    let applicationProcessPages = [];
    let applicationPages = [];
    let processTable;
    let processContainer;
    let processTemplate;
    let processCounter = 0;

    function showLoader(targetContainer, loaderId = 'loader-show') {
        if (!targetContainer) return;

        // Prevent duplicate loader
        if (!targetContainer.querySelector(`#${loaderId}`)) {
            const loaderDiv = document.createElement('div');
            loaderDiv.id = loaderId;
            loaderDiv.className = 'text-center py-3';
            loaderDiv.innerHTML = `<img src="assets/images/spiner/spiner3.gif" style="width: 100px;">`;
            targetContainer.appendChild(loaderDiv);
        }
    }

    function hideLoader(loaderId = 'loader-show') {
        const loader = document.getElementById(loaderId);
        if (loader) loader.remove();
    }


    // Function to show error messages
    function showErrorMessage(message) {
        // Remove any existing error messages
        const existingErrors = document.querySelectorAll('.error-message');
        existingErrors.forEach(error => error.remove());

        // Create new error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.innerHTML = `
            ${message}
            <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        `;

        // Insert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(errorDiv, cardBody.firstChild);

        // Show the error message
        errorDiv.style.display = 'block';

        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorDiv.parentElement) {
                errorDiv.remove();
            }
        }, 5000);

        // Scroll to top to show the error
        errorDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Function to show success messages
    function showSuccessMessage(message) {
        // Remove any existing success messages
        const existingSuccess = document.querySelectorAll('.success-message');
        existingSuccess.forEach(success => success.remove());

        // Create new success message
        const successDiv = document.createElement('div');
        successDiv.className = 'success-message';
        successDiv.innerHTML = `
            ${message}
            <button class="close-btn" onclick="this.parentElement.remove()">&times;</button>
        `;

        // Insert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(successDiv, cardBody.firstChild);

        // Show the success message
        successDiv.style.display = 'block';

        // Auto-hide after 3 seconds
        setTimeout(() => {
            if (successDiv.parentElement) {
                successDiv.remove();
            }
        }, 3000);

        // Scroll to top to show the success
        successDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Global function to handle module change (called from inline onchange)
    function handleModuleChange(selectedModuleId) {
        // // console.log('Module changed via inline handler to:', selectedModuleId);
        loadProcessesForModule(selectedModuleId);
    }

    // Function to load processes for a module
    function loadProcessesForModule(selectedModuleId) {
        const tableBody = document.querySelector('#process-table tbody');
        tableBody.innerHTML = ''; // clear any old rows
        showLoader(tableBody, 'loader-process-table');

        if (!selectedModuleId) {
            processContainer.style.display = 'none';
            processTable.innerHTML = '';
            loader.style.display = 'none'; // Hide loader if nothing to do
            return;
        }

        // // console.log('Loading processes for module ID:', selectedModuleId);

        // Filter processes by module ID
        const moduleProcesses = applicationProcess.filter(process => {
            // Get module_setup_id, handling both object and direct property access
            const processModuleId = process.module_setup_id || (process.module_setup_id === 0 ? 0 : null);
            // // console.log('Checking process:', process, 'Module Setup ID:', processModuleId, 'Selected ID:', selectedModuleId);
            return processModuleId == selectedModuleId;
        });

        // console.log('Filtered processes for module ' + selectedModuleId + ':', moduleProcesses);

        // Clear existing processes
        processTable.innerHTML = '';
        processCounter = 0;

        // Show the process container
        processContainer.style.display = 'block';

        // Add existing processes
        if (moduleProcesses && moduleProcesses.length > 0) {
            // console.log('Adding existing processes:', moduleProcesses.length);
            let addedCount = 0;
            moduleProcesses.forEach(process => {
                addExistingProcessRow(process);
                addedCount++;
                console.log('existing processes found for this module');
                if (addedCount === moduleProcesses.length) {
                    console.log('existing processes found for this module loader working');
                    hideLoader('loader-process-table');
                }
            });
        } else {
            console.log('No existing processes found for this module');
            hideLoader('loader-process-table');
        }
    }

    function addExistingProcessRow(process) {
        // console.log('Adding existing process row:', process);
        const row = document.createElement('tr');
        row.className = 'process-row';

        const currentProcessIndex = processCounter++;
        const processName = process.process_name || process.name || '';
        const processId = process.id || '';
        const moduleId = process.module_setup_id || '';

        // console.log('Process details - ID:', processId, 'Name:', processName, 'Module ID:', moduleId);
        const processPages = applicationProcessPages.filter(page => {
            const pageProcessId = page.application_process_id || '';
            return pageProcessId == processId;
        });
        row.innerHTML = `
        <td>
            <input type="text" class="form-control process-input" name="processes[${currentProcessIndex}][name]" value="${processName}" required data-index="${currentProcessIndex}" data-process-id="${processId}">
            <input type="hidden" name="processes[${currentProcessIndex}][id]" value="${processId}">
            <input type="hidden" name="processes[${currentProcessIndex}][module_id]" value="${moduleId}">
        </td>
        <td class="process-pages-cell">
            <button type="button" class="btn btn-sm btn-info add-process-pages-btn">
                <i class="fa fa-list mr-1"></i> Configure Pages
            </button>
            <div class="process-pages-container custom-scroll" style="display: none; max-height: 300px; min-height: 300px;"></div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-process ">
                <i class="fa fa-trash"></i>
            </button>
        </td>
        `;
        processTable.appendChild(row);
        row.querySelector('.remove-process').addEventListener('click', function () {
            row.remove();
            if (processTable.children.length === 0) {
                processContainer.style.display = 'none';
            }
        });

        const configPagesBtn = row.querySelector('.add-process-pages-btn');
        const processPagesContainer = row.querySelector('.process-pages-container');

        configPagesBtn.addEventListener('click', function () {
            const processInput = row.querySelector('.process-input');
            const processId = processInput.dataset.processId;
            // console.log('Configure pages clicked for process ID:', processId);
            if (processPagesContainer.style.display === 'none') {
                processPagesContainer.style.display = 'block';
                configPagesBtn.innerHTML = '<i class="fa fa-times mr-1"></i> Hide Pages';
                if (processPagesContainer.children.length === 0) {
                    showLoader(processPagesContainer, `loader-pages-${currentProcessIndex}`);
                    setTimeout(() => {
                        initializeProcessPages(processPagesContainer, processId || null, currentProcessIndex, processPages || []);
                        hideLoader(`loader-pages-${currentProcessIndex}`);
                    }, 100);
                }
            } else {
                processPagesContainer.style.display = 'none';
                configPagesBtn.innerHTML = '<i class="fa fa-list mr-1"></i> Configure Pages';
            }
        });
    }

    function addNewProcessRow(moduleId) {
        // console.log('Adding new process row for module ID:', moduleId);
        const row = document.createElement('tr');
        row.className = 'process-row';
        const currentProcessIndex = processCounter++;
        row.innerHTML = `
        <td>
            <input type="text" class="form-control process-input" name="processes[${currentProcessIndex}][name]" required data-index="${currentProcessIndex}">
            <input type="hidden" name="processes[${currentProcessIndex}][module_id]" value="${moduleId}">
        </td>
        <td class="process-pages-cell">
            <button type="button" class="btn btn-sm btn-info add-process-pages-btn">
                <i class="fa fa-list mr-1"></i> Configure Pages
            </button>
            <div class="process-pages-container" style="display: none;"></div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger remove-process">
                <i class="fa fa-trash"></i>
            </button>
        </td>
        `;
        processTable.appendChild(row);
        row.querySelector('.remove-process').addEventListener('click', function () {
            row.remove();
            if (processTable.children.length === 0) {
                processContainer.style.display = 'none';
            }
        });
        const configPagesBtn = row.querySelector('.add-process-pages-btn');
        const processPagesContainer = row.querySelector('.process-pages-container');

        configPagesBtn.addEventListener('click', function () {
            const processInput = row.querySelector('.process-input');
            const selectedProcessName = processInput.value.trim();
            // console.log('Configure pages clicked for new process:', selectedProcessName);
            if (!selectedProcessName) {
                showErrorMessage('Please enter a process name first');
                return;
            }
            // Toggle display of the process pages container
            if (processPagesContainer.style.display === 'none') {
                processPagesContainer.style.display = 'block';
                configPagesBtn.innerHTML = '<i class="fa fa-times mr-1"></i> Hide Pages';
                if (processPagesContainer.children.length === 0) {
                    showLoader(processPagesContainer, `loader-pages-${currentProcessIndex}`);
                    setTimeout(() => {
                        initializeProcessPages(processPagesContainer, processId || null, currentProcessIndex, processPages || []);
                        hideLoader(`loader-pages-${currentProcessIndex}`);
                    }, 100);
                }
            } else {
                processPagesContainer.style.display = 'none';
                configPagesBtn.innerHTML = '<i class="fa fa-list mr-1"></i> Configure Pages';
            }
        });

        // Replace process input listener to clear pages on user input
        row.querySelector('.process-input').addEventListener('input', function () {
            processPagesContainer.innerHTML = '';
            processPagesContainer.style.display = 'none';
            configPagesBtn.innerHTML = '<i class="fa fa-list mr-1"></i> Configure Pages';
        });
    }

    // Function to initialize process pages for a process
    function initializeProcessPages(container, processId, processIndex, existingPages = []) {
        // console.log('Initializing process pages for process ID:', processId, 'with existing pages:', existingPages);
        // Get the template content
        const template = document.importNode(processTemplate.content, true);

        // Update the template with the process index
        const selects = template.querySelectorAll('select');
        selects.forEach(select => {
            select.name = select.name.replace('{process_index}', processIndex);
        });

        // Add the template to the container
        container.appendChild(template);

        // Get the process pages container and add page button
        const pagesContainer = container.querySelector('.process-pages-container');
        const addPageBtn = container.querySelector('.add-page-btn');

        // If we have existing pages, add them
        if (existingPages && Array.isArray(existingPages) && existingPages.length > 0) {
            // console.log('Adding existing pages:', existingPages.length);

            // Ensure all page names from existing data are available in our application_process_pages
            // This ensures dropdowns include all needed options
            existingPages.forEach(page => {
                const pageName = page.name || page.application_page_name || '';
                if (pageName && !applicationProcessPages.some(p => (p.name === pageName || p.application_page_name === pageName))) {
                    // Add this page name to our collection if it doesn't exist
                    applicationProcessPages.push({
                        application_page_name: pageName,
                        id: page.id || ''
                    });
                }
            });

            existingPages.forEach(page => {
                addProcessPageRow(pagesContainer, processIndex, page);
            });
        } else {
            // console.log('No existing pages, adding empty page row');
            // Add initial page row
            addProcessPageRow(pagesContainer, processIndex);
        }

        // Add event listener to add page button
        addPageBtn.addEventListener('click', function () {
            // console.log('Add page button clicked');
            addProcessPageRow(pagesContainer, processIndex);

            // Re-initialize Select2 for all new elements
            if (window.initializeSelect2) {
                window.initializeSelect2();
            }
        });
    }

    // Function to add a process page row
    function addProcessPageRow(container, processIndex, existingPage = null) {
        // console.log('Adding process page row for index:', processIndex, 'Existing page:', existingPage);
        const row = document.createElement('div');
        row.className = 'row process-page-row mb-2';

        // Get page properties, handling different possible structures
        let pageName = '';
        let pageId = '';
        let customPageName = '';
        let applicationPageName = '';

        if (existingPage) {
            pageName = existingPage.name || existingPage.application_page_name || '';
            pageId = existingPage.id || '';
            applicationPageName = existingPage.application_page_name || '';
            // console.log('Page details - ID:', pageId, 'Name:', pageName, 'Application Page Name:', applicationPageName);
        }

        // Use the global applicationPages variable
        // console.log('Using global Application Pages:', applicationPages);

        // Extract list_page_name values from application_pages
        const pageNames = [];
        if (applicationPages && typeof applicationPages === 'object') {
            // console.log('Processing application_pages to extract list_page_name values');

            // Handle both array and object formats
            if (Array.isArray(applicationPages)) {
                // console.log('Application pages is an array with', applicationPages.length, 'items');
                applicationPages.forEach(page => {
                    if (page && page.list_page_name) {
                        pageNames.push(page.list_page_name);
                        // console.log(`Added list_page_name: ${page.list_page_name}`);
                    }
                });
            } else {
                // console.log('Application pages is an object');
                // Handle object format where keys might be table names
                Object.entries(applicationPages).forEach(([key, page]) => {
                    // console.log(`Processing key: ${key}, value:`, page);

                    if (page && page.list_page_name) {
                        pageNames.push(page.list_page_name);
                        // console.log(`Added list_page_name: ${page.list_page_name}`);
                    } else if (typeof page === 'string') {
                        if (page.includes('(')) {
                            // Handle format like "table_name (page_name)"
                            const match = page.match(/\((.*?)\)/);
                            if (match && match[1]) {
                                pageNames.push(match[1]);
                                // console.log(`Added extracted page name: ${match[1]} from ${page}`);
                            }
                        } else {
                            // It might be a direct page name
                            pageNames.push(page);
                            // console.log(`Added direct page name: ${page}`);
                        }
                    } else if (typeof page === 'object' && page !== null) {
                        // Handle nested objects
                        Object.entries(page).forEach(([subKey, subValue]) => {
                            if (subKey === 'list_page_name' && subValue) {
                                pageNames.push(subValue);
                                // console.log(`Added nested list_page_name: ${subValue}`);
                            }
                        });
                    }
                });
            }
        }

        // Also check application_process_pages for application_page_name values
        if (applicationProcessPages && Array.isArray(applicationProcessPages)) {
            // console.log('Adding application_page_name values from application_process_pages');
            applicationProcessPages.forEach(page => {
                if (page && page.application_page_name && !pageNames.includes(page.application_page_name)) {
                    pageNames.push(page.application_page_name);
                    // console.log(`Added application_page_name: ${page.application_page_name}`);
                }
            });
        }

        // Remove duplicates
        const uniquePageNames = [...new Set(pageNames)];
        // console.log(`Found ${uniquePageNames.length} unique page names from ${pageNames.length} total`);

        // Create page options HTML - set selected option if it matches application_page_name
        let pageOptions = uniquePageNames.map(name => {
            // Check if this option matches the current page's application_page_name
            let isSelected = false;
            if (existingPage && (existingPage.application_page_name === name || existingPage.name === name)) {
                isSelected = true;
                // console.log(`Found matching option for ${name}, marking as selected`);
            }
            return `<option value="${name}" ${isSelected ? 'selected' : ''}>${name}</option>`;
        }).join('');

        // Check if the page name exists in our list of application pages
        const isCustomPage = !uniquePageNames.includes(pageName) && pageName !== '';
        if (isCustomPage) {
            customPageName = pageName;
            // console.log(`Page "${pageName}" not found in application_pages, marking as custom`);
        } else {
            // console.log(`Page "${pageName}" found in application_pages list`);
        }

        row.innerHTML = `
            <div class="col-4">
                <input type="text" class="form-control page-name-input" name="process_pages[${processIndex}][name][]" value="${pageName}" required>
                ${pageId ? `<input type="hidden" name="process_page_ids[${processIndex}][]" value="${pageId}" class="page-id-input">` : ''}
            </div>
            <div class="col-4 w-full">
                <select class="js-example-basic-single form-select page-select" name="process_pages[${processIndex}][type][]" required onchange="if(this.value==='other'){this.closest('.row').querySelector('.other-input-container').style.display='';this.closest('.row').querySelector('.other-page-input').required=true;}else{this.closest('.row').querySelector('.other-input-container').style.display='none';this.closest('.row').querySelector('.other-page-input').required=false;if(this.value){this.closest('.row').querySelector('.page-name-input').value=this.value;}}">
                    <option value="">Select Page</option>
                    ${pageOptions}
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-2 other-input-container" style="display:none;">
                <input type="text" class="form-control other-page-input" name="process_pages[${processIndex}][custom][]" value="${customPageName}">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-sm btn-danger remove-page-btn">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        `;

        // Initialize Select2 for the newly added row
        $(row).find(".js-example-basic-single").select2();

        // Set up the page with proper selection based on application_page_name
        if (pageName) {
            const selectElement = row.querySelector('.page-select');
            const otherContainer = row.querySelector('.other-input-container');
            const otherInput = row.querySelector('.other-page-input');
            const mainInput = row.querySelector('.page-name-input');

            // console.log(`Setting up page "${pageName}" with application_page_name matching`);

            // Set the main input value
            mainInput.value = pageName;

            // Try to find a matching option in the select dropdown
            let matchFound = false;

            // First check if we have a direct match with application_page_name
            if (existingPage && existingPage.application_page_name) {
                const appPageName = existingPage.application_page_name;
                // console.log(`Looking for option matching application_page_name: "${appPageName}"`);

                const matchingOption = Array.from(selectElement.options).find(opt =>
                    opt.value === appPageName
                );

                if (matchingOption) {
                    // console.log(`Found matching option for application_page_name: "${appPageName}"`);
                    matchingOption.selected = true;
                    otherContainer.style.display = 'none';
                    matchFound = true;
                }
            }

            // If no match with application_page_name, try with the page name
            if (!matchFound) {
                const matchingOption = Array.from(selectElement.options).find(opt =>
                    opt.value === pageName
                );

                if (matchingOption) {
                    // console.log(`Found matching option for page name: "${pageName}"`);
                    matchingOption.selected = true;
                    otherContainer.style.display = 'none';
                    matchFound = true;
                }
            }

            // If still no match found, use "other" option
            if (!matchFound) {
                // console.log(`No match found for "${pageName}", using "other" option`);
                const otherOption = Array.from(selectElement.options).find(opt => opt.value === 'other');
                if (otherOption) {
                    otherOption.selected = true;
                    otherContainer.style.display = '';
                    otherInput.value = pageName;
                }
            }
        }

        // Add event listener to remove button
        row.querySelector('.remove-page-btn').addEventListener('click', function () {
            row.remove();
        });

        // Add event listener to select dropdown
        const selectEl = row.querySelector('.page-select');
        const otherContainer = row.querySelector('.other-input-container');
        const otherInput = row.querySelector('.other-page-input');
        const mainNameInput = row.querySelector('.page-name-input');

        selectEl.addEventListener('change', function () {
            // console.log(`Select value changed to: "${this.value}"`);

            if (this.value === 'other') {
                // Show the custom input field
                otherContainer.style.display = '';
                otherInput.required = true;

                // If main input already has a value, copy it to the custom input
                if (mainNameInput.value && mainNameInput.value !== '') {
                    otherInput.value = mainNameInput.value;
                } else {
                    otherInput.value = '';
                }
            } else if (this.value) {
                // Hide the custom input and update the main input with the selected value
                otherContainer.style.display = 'none';
                otherInput.required = false;
                mainNameInput.value = this.value;
                // console.log(`Updated main input to: "${this.value}"`);
            } else {
                // Empty selection
                otherContainer.style.display = 'none';
                otherInput.required = false;
                mainNameInput.value = '';
            }
        });

        // Add the row to the container
        container.appendChild(row);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Select2
        function initializeSelect2() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('.js-example-basic-single').each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2();
                    }
                });

                // Force trigger change event after select2 initialization
                $('.js-example-basic-single').on('select2:select', function (e) {
                    // console.log('Select2 selection changed:', e.params.data);
                    $(this).trigger('change');
                    // Also manually call our handler for module select
                    if ($(this).attr('id') === 'module_id') {
                        handleModuleChange(e.params.data.id);
                    }
                });
            }
        }

        // Initial Select2 initialization
        initializeSelect2();

        // Make the function globally available for later use
        window.initializeSelect2 = initializeSelect2;

        // Add Select2 class to any missing select elements
        function fixMissingSelect2() {
            // Find all select elements without js-example-basic-single class
            $('select').not('.js-example-basic-single').each(function () {
                $(this).addClass('js-example-basic-single');

                // Add onchange event for "Other" option if it doesn't have one
                if (!$(this).attr('onchange')) {
                    $(this).attr('onchange', "if(this.value==='other'){this.closest('.row').querySelector('.other-input-container').style.display='';this.closest('.row').querySelector('.other-page-input').required=true;}else{this.closest('.row').querySelector('.other-input-container').style.display='none';this.closest('.row').querySelector('.other-page-input').required=false;if(this.value){this.closest('.row').querySelector('.page-name-input').value=this.value;}}");
                }
            });

            // Re-initialize Select2 for all elements
            initializeSelect2();
        }

        // Run the fix function after a short delay to ensure all elements are loaded
        setTimeout(fixMissingSelect2, 500);

        // Also run it when the DOM changes (for dynamically added elements)
        const observer = new MutationObserver(function (mutations) {
            fixMissingSelect2();
        });

        // Start observing the document with the configured parameters
        observer.observe(document.body, { childList: true, subtree: true });

        // Set the global variables
        processTable = document.getElementById('process-table').getElementsByTagName('tbody')[0];
        processContainer = document.getElementById('process-container');
        processTemplate = document.getElementById('process-pages-template');
        const moduleSelect = document.getElementById('module_id');
        const addProcessBtn = document.getElementById('add-process-btn');

        // Process data - making it available to JS
        applicationProcess = <?= json_encode($application_process) ?>;
        applicationProcessPages = <?= json_encode($application_process_pages) ?>;
        applicationPages = <?= json_encode($application_pages) ?>;

        // Check if applicationProcess is an object instead of an array and convert if needed
        if (applicationProcess && typeof applicationProcess === 'object' && !Array.isArray(applicationProcess)) {
            // console.log('Converting application process from object to array');
            applicationProcess = Object.values(applicationProcess);
        }

        // Check if applicationProcessPages is an object instead of an array and convert if needed
        if (applicationProcessPages && typeof applicationProcessPages === 'object' && !Array.isArray(applicationProcessPages)) {
            // console.log('Converting application process pages from object to array');
            applicationProcessPages = Object.values(applicationProcessPages);
        }

        // Log application pages data for debugging
        // console.log('Application Pages Data:', applicationPages);

        // Debug logging
        // console.log('Application Process Data (processed):', applicationProcess);
        // console.log('Application Process Pages Data (processed):', applicationProcessPages);

        // Manually trigger the change event on page load if a module is selected
        if (moduleSelect.value) {
            // console.log('Module already selected on page load:', moduleSelect.value);
            handleModuleChange(moduleSelect.value);
        }

        // Show process container when Add Process button is clicked
        addProcessBtn.addEventListener('click', function () {
            const selectedModuleId = moduleSelect.value;
            // console.log('Add process button clicked, selected module:', selectedModuleId);

            if (!selectedModuleId) {
                showErrorMessage('Please select a module first');
                return;
            }

            // Show the process container
            processContainer.style.display = 'block';

            // Add a new process row
            addNewProcessRow(selectedModuleId);
        });

        // Handle form submission
        document.getElementById('moduleProcessForm').addEventListener('submit', function (e) {
            // Uncomment to prevent form submission during debugging for testing
            // e.preventDefault();

            // Prepare data for submission - handle both regular and custom page names
            document.querySelectorAll('.process-page-row').forEach(row => {
                const selectEl = row.querySelector('.page-select');
                const mainInput = row.querySelector('.page-name-input');
                const otherInput = row.querySelector('.other-page-input');

                // If "Other" is selected, update the main input with the custom value
                if (selectEl.value === 'other' && otherInput.value.trim()) {
                    mainInput.value = otherInput.value.trim();
                    // Make sure type is set to 'other' for proper backend processing
                    selectEl.value = 'other';
                } else if (selectEl.value && selectEl.value !== 'other') {
                    // If a valid option is selected, update the main input with the selected value
                    mainInput.value = selectEl.value;
                } else if (!selectEl.value) {
                    // If nothing is selected, ensure the main input is cleared
                    mainInput.value = '';
                }
            });

            // Check for empty process pages
            let hasEmptyPages = false;
            document.querySelectorAll('.process-row').forEach(processRow => {
                const pagesContainer = processRow.querySelector('.process-pages-container');
                if (pagesContainer.style.display !== 'none' && pagesContainer.children.length > 0) {
                    const pageRows = pagesContainer.querySelectorAll('.process-page-row');
                    if (pageRows.length === 0) {
                        hasEmptyPages = true;
                    }

                    pageRows.forEach(pageRow => {
                        const nameInput = pageRow.querySelector('.page-name-input');
                        const selectEl = pageRow.querySelector('.page-select');
                        if (!nameInput.value || !selectEl.value) {
                            hasEmptyPages = true;
                        }
                    });
                }
            });

            if (hasEmptyPages) {
                showErrorMessage('Please complete all page information or remove empty page rows');
                e.preventDefault();
                return false;
            }

            console.log('Form submitted with data:', {
                moduleId: moduleSelect.value,
                processes: Array.from(document.querySelectorAll('.process-input')).map(input => ({
                    name: input.value,
                    processId: input.dataset.processId || 'new',
                    moduleId: input.closest('tr').querySelector('input[name^="processes"][name$="[module_id]"]').value
                })),
                pages: Array.from(document.querySelectorAll('.process-page-row')).map(row => {
                    const select = row.querySelector('.page-select');
                    const mainInput = row.querySelector('.page-name-input');
                    const otherInput = row.querySelector('.other-page-input');
                    const idInput = row.querySelector('.page-id-input');
                    return {
                        type: select.value,
                        name: mainInput.value,
                        customName: select.value === 'other' ? otherInput.value : '',
                        id: idInput ? idInput.value : ''
                    };
                })
            });
        });
    });
</script>