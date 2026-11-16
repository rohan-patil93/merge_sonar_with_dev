

<!-- Revenue vs Expense vs Profit Chart CSS -->
<link rel="stylesheet" type="text/css" href="assets/bower_components/morris.js/css/morris.css">

<div class="row mt-3">
  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Revenue (MTD)'); ?></p>
        <h3 class="f-w-600 f-34 mt-2 mb-2"><?= htmlspecialchars(__('₹4.8 Cr'), ENT_QUOTES, 'UTF-8'); ?></h3>
        <p class="text-success mb-0 f-w-600"><i class="ti-arrow-up"></i> <?= __('14% vs last month'); ?></p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Net Profit'); ?></p>
        <h3 class="f-w-600 f-34 mt-2 mb-2">&#8377;82 L</h3>
        <p class="text-success mb-0 f-w-600"><i class="ti-arrow-up"></i> <?= __('Healthy Growth'); ?></p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard b-radius-30">
      <div class="card-body">
        <p class="text-muted mb-0"><?= __('Outstanding'); ?></p>
        <h3 class="f-w-600 f-34 mt-2 mb-2"><?= htmlspecialchars(__('₹1.1 Cr'), ENT_QUOTES, 'UTF-8'); ?></h3>
        <p class="text-danger mb-0 f-w-600"><?= __('Needs Attention'); ?></p>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 col-12">
    <div class="card p-2 infisuite-dashboard infisuite-dashboard-black pulse b-radius-30">
      <div class="card-body">
        <p class="text-white mb-0"><?= __('AI Executive Insight'); ?></p>
        <h6 class="f-w-600 f-20 m-f-16 mt-2 mb-2 text-white"><?= __('South Region contributed 48% of this month growth.'); ?></h6>
      </div>
    </div>
  </div>

  <div class="col-lg-8 col-md-12 col-sm-12 col-12">
    <div class="card revenue-chart p-2 b-radius-30">
      <div class="card-body">
        <h4 class="f-w-600 f-20"><?= __('Revenue vs Expense vs Profit'); ?></h4>
        <div id="line-example" style="min-height: 350px;"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-12 col-sm-12 col-12">
    <div class="card sales-funnel p-2 b-radius-30">
      <div class="card-body">
        <h4 class="f-w-600 f-20"><?= __('Sales Funnel'); ?></h4>
        <div class="mt-4">
          <div class="d-flex justify-content-between">
            <span class="f-w-600 f-14"><?= __('Leads'); ?></span>
            <span class="f-w-600 f-14">420</span>
          </div>
          <div class="progress progress-sm mt-1">
            <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between">
            <span class="f-w-600 f-14"><?= __('Qualified'); ?></span>
            <span class="f-w-600 f-14">280</span>
          </div>
          <div class="progress progress-sm mt-1">
            <div class="progress-bar progress-bar-success" role="progressbar" style="width: 60%" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between">
            <span class="f-w-600 f-14"><?= __('Proposal'); ?></span>
            <span class="f-w-600 f-14">160</span>
          </div>
          <div class="progress progress-sm mt-1">
            <div class="progress-bar progress-bar-warning" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between">
            <span class="f-w-600 f-14"><?= __('Won'); ?></span>
            <span class="f-w-600 f-14">78</span>
          </div>
          <div class="progress progress-sm mt-1">
            <div class="progress-bar progress-bar-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card ai-business b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h4 class="f-w-600 f-20"><?= __('AI Business Insights'); ?></h4>
          <span class="live-ai bg-blue-100 b-radius-30 f-13 text-primary"><?= __('Live AI'); ?></span>
        </div>
        <div class="mt-4">
          <p class="f-16 bg-red-50 b-radius-20 p-3 text-danger border-red-100"><?= __('Collections slowing in Hyderabad by 18%'); ?></p>
          <p class="f-16 bg-yellow-50 b-radius-20 p-3 text-warning border-yellow-100"><?= __('Inventory carrying cost increased 11%'); ?></p>
          <p class="f-16 bg-green-50 b-radius-20 p-3 text-success border-green-100"><?= __('HR productivity improved by 9%'); ?></p>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-6 col-md-6 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h4 class="f-w-600 f-20"><?= __('Department Health Matrix'); ?></h4>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16"><?= __('Sales'); ?></span>
            <span class="f-16 text-success f-w-600"><?= __('Healthy'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-yellow-50 b-radius-20 p-3 border-yellow-100">
            <span class="f-16"><?= __('Purchase'); ?></span>
            <span class="f-16 text-warning f-w-600"><?= __('Delayed'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-red-50 b-radius-20 p-3 border-red-100">
            <span class="f-16"><?= __('Inventory'); ?></span>
            <span class="f-16 text-danger f-w-600"><?= __('High Risk'); ?></span>
          </div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between bg-green-50 b-radius-20 p-3 border-green-100">
            <span class="f-16"><?= __('Finance'); ?></span>
            <span class="f-16 text-success f-w-600"><?= __('Stable'); ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="row">
  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h4 class="f-w-600 f-20"><?= __('AI Business Insights'); ?></h4>
        </div>
        <div class="visitor-slider">
          <button type="button" class="scroll-btn left-btn" onclick="scrollVisitors(-300,'visitorWrapperA')">❮</button>
          <div class="visitor-wrapper mt-4 p-2" id="visitorWrapperA">
            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor" checked="">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Visitor 1'); ?></span>
            </label>

            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Visitor 2'); ?></span>
            </label>

            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Visitor 3'); ?></span>
            </label>

            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Visitor 4'); ?></span>
            </label>

            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Visitor 5'); ?></span>
            </label>

            <label class="visitor-card">
              <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
              <div class="icon-box">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="8" r="4"></circle>
                      <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                  </svg>
              </div>
              <span><?= __('Visitor 6'); ?></span>
            </label>
          </div>
          <button type="button" class="scroll-btn right-btn" onclick="scrollVisitors(300,'visitorWrapperA')">❯</button>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6 col-md-12 col-sm-12 col-12">
    <div class="card b-radius-30 p-2">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h4 class="f-w-600 f-20"><?= __('Gate Number'); ?></h4>
        </div>
        <div class="visitor-slider">
          <button type="button" class="scroll-btn left-btn" onclick="scrollVisitors(-300,'visitorWrapper')">❮</button>
          <div class="visitor-wrapper mt-4 p-2" id="visitorWrapper">
            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Gate'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="gate" checked="">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Gete Number 1'); ?></span>
            </label>
            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Gete Number 2'); ?></span>
            </label>
            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Gete Number 3'); ?></span>
            </label>
            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Gete Number 4'); ?></span>
            </label>
            <label class="visitor-card">
                <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
                <div class="icon-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                    </svg>
                </div>
                <span><?= __('Gete Number 5'); ?></span>
            </label>
            <label class="visitor-card">
              <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
              <div class="icon-box">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="8" r="4"></circle>
                      <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                  </svg>
              </div>
              <span><?= __('Gete Number 6'); ?></span>
            </label>
            <label class="visitor-card">
              <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
              <div class="icon-box">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="8" r="4"></circle>
                      <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                  </svg>
              </div>
              <span><?= __('Gete Number 7'); ?></span>
            </label>
            <label class="visitor-card">
              <input aria-label="<?= htmlspecialchars(__('Visitor'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="visitor">
              <div class="icon-box">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="8" r="4"></circle>
                      <path d="M4 20C4 16.5 7.5 14 12 14C16.5 14 20 16.5 20 20"></path>
                  </svg>
              </div>
              <span><?= __('Gete Number 8'); ?></span>
            </label>
          </div>
          <button type="button" class="scroll-btn right-btn" onclick="scrollVisitors(300,'visitorWrapper')">❯</button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function scrollVisitors(scrollAmount, divId) {
      const wrapper = document.getElementById(divId);

      wrapper.scrollBy({
          left: scrollAmount,
          behavior: "smooth"
      });

      setTimeout(() => {
          updateButtons(divId);
      }, 300);
  }

  function updateButtons(divId) {
      const wrapper = document.getElementById(divId);
      const slider = wrapper.closest('.visitor-slider');

      const leftBtn = slider.querySelector('.left-btn');
      const rightBtn = slider.querySelector('.right-btn');

      const totalCards = wrapper.querySelectorAll('.visitor-card').length;

      // Agar 5 ya usse kam cards hain to arrows hide
      if (totalCards <= 5) {
          leftBtn.style.display = 'none';
          rightBtn.style.display = 'none';
          return;
      }

      // Normal hide/show logic
      leftBtn.style.display =
          wrapper.scrollLeft <= 0 ? 'none' : 'flex';

      rightBtn.style.display =
          wrapper.scrollLeft + wrapper.clientWidth >= wrapper.scrollWidth - 5
              ? 'none'
              : 'flex';
  }

  document.querySelectorAll('.visitor-wrapper').forEach(wrapper => {

      updateButtons(wrapper.id);

      wrapper.addEventListener('scroll', function () {
          updateButtons(this.id);
      });
  });

  window.addEventListener('resize', function () {
      document.querySelectorAll('.visitor-wrapper').forEach(wrapper => {
          updateButtons(wrapper.id);
      });
  });
</script>

<!-- Revenue vs Expense vs Profit Chart JS -->
<script src="assets/bower_components/raphael/js/raphael.min.js"></script>
<script src="assets/bower_components/morris.js/js/morris.js"></script>
<script src="assets/pages/chart/morris/morris-custom-chart.js"></script>

</body>

</html>
