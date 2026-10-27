<style type="text/css">
    .table.table-sm td,
    .table.table-sm th {
        padding: 0.6rem 1rem;
    }
</style>

<div class="page-body">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Feasibility</h5>
                </div>
                <div class="card-body">
                    <span class="form-label m-r-5">Feasibility</span>
                    <input aria-label="Checkbox" type="checkbox" class="js-single" />
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">

                <div class="card-body">
                    <!-- Wrapper to toggle -->
                    <div class="mt-3" id="feasibilityWrapper" style="display: none;">
                        <ul class="nav md-tabs infisuite_tab mt-2 ml-2" id="tabList">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#feasibility_type" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Feasibility Type</a>
                            </li>
                        </ul>
                        <div class="tab-content tabs card-block" id="feasibilityTab">
                            <div class="tab-pane active" id="feasibility_type" role="tabpanel">
                                <div class="slides-container" id="feasibilitySlides">
                                    <?php include_once 'views/custom_insert_feasibility_requirements.php'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Switch on off -->
<script>
    const checkbox = document.querySelector('.js-single');
    const wrapper = document.getElementById('feasibilityWrapper');

    checkbox.addEventListener('change', () => {
        wrapper.style.display = checkbox.checked ? 'block' : 'none';
    });
</script>

<!-- Table row add -->
<script>
    function reinitSelect2() {
        if (!$.fn.select2) {
            console.warn("Select2 library not loaded");
            return;
        }

        document.querySelectorAll('.js-example-basic-single').forEach(function(el) {
            const $el = $(el);
            if ($el.hasClass("select2-hidden-accessible")) {
                $el.select2('destroy');
            }
            $el.select2({
                allowClear: true
            });
        });
    }

    function previous(page, previous_id) {
        $.ajax({
            url: `views/${page}.php`, // Replace with your backend URL
            type: 'GET',
            data: {
                'previous_id': previous_id
            },
            success: function(response) {
                $('#feasibilitySlides').html(response);
                reinitSelect2(); // Reinitialize select2 after loading new content
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    }
</script>
