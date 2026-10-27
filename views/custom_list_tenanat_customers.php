<?php
$url = API."?mode=gettenantcustomers&admin_type=".$_SESSION['admin_type'];
  $response = callAPI($url);
  $result = json_decode($response,1);
  $customers = $result['customers'];
  $next_payment_invoices = $result['next_payment_invoices'];
  $payment_due = $result['payment_due'];
  $last_payment = $result['last_payment'];
  $total_property_cnt  = $result['total_property_cnt'];
  ?>
  <script type="text/javascript">
    function selectAlphabet(alpha){
      $(".activealpha").removeClass('active');
      $("#activealpha"+alpha).addClass("active");
      $("#display_name_alphabet").val(alpha);
      filterapplay();
    }
    function filterapplay(reset=""){
      var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
      $("#formview").html(loader);
      if(reset !="Y"){
        var postarray = $("#filterfrom").serialize();
      }
      $.ajax({
          url: "views/custom_list_tenanat_customers_table.php",
          type: "POST",
          data: "filter=Y&"+postarray,
          success: function(response){
              $("#formview").html(response);
          }
      });
  }
  function sendEmailCandidate(cand_id,email_id,temp_id){
    var job_posting_id = $("#job_posting_id_ajax").val();
    var extra_paramiter  = '<?php echo json_encode($_GET); ?>';
    var parent_option  = '<?php echo $_GET['option']; ?>';
    insertadittrails(parent_option,'COM_INSERT_CANDIDATE_EMAIL_POPUP',extra_paramiter);
    jQuery.ajax({
        url: "views/custom_insert_candidate_email_popup.php",
        data:'cand_id='+cand_id+'&job_posting_id='+job_posting_id+"&email_id="+email_id+"&temp_id="+temp_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
            $(".js-example-basic-signle").select2();
CKEDITOR.replace('editor11', {
        toolbar: [{
            name: 'clipboard',
            items: ['Undo', 'Redo']
        }, {
            name: 'styles',
            items: ['Styles', 'Format']
        }, {
            name: 'basicstyles',
            items: ['Bold', 'Italic', 'Strike', '-', 'RemoveFormat']
        }, {
            name: 'paragraph',
            items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote']
        }, {
            name: 'links',
            items: ['Link', 'Unlink']
        }, {
            name: 'insert',
            items: ['Image', 'EmbedSemantic', 'Table']
        }, {
            name: 'tools',
            items: ['Maximize']
        }, {
            name: 'editing',
            items: ['Scayt']
        }],
     customConfig: '',
        extraPlugins: 'autoembed,embedsemantic,image2,uploadimage,uploadfile',
        imageUploadUrl: '/uploader/upload.php?type=Images',
        uploadUrl: '/uploader/upload.php',
        removePlugins: 'image',
        height: 461,
        contentsCss: ['assets/pages/ckeditor/contents.css', 'assets/pages/ckeditor/artical.css'],
        bodyClass: 'article-editor editor11_new',
        format_tags: 'p;h1;h2;h3;pre',
        removeDialogTabs: 'image:advanced;link:advanced',
        stylesSet: [
            /* Inline Styles */
            {
                name: 'Marker',
                element: 'span',
                attributes: {
                    'class': 'marker'
                }
            }, {
                name: 'Cited Work',
                element: 'cite'
            }, {
                name: 'Inline Quotation',
                element: 'q'
            },

            /* Object Styles */
            {
                name: 'Special Container',
                element: 'div',
                styles: {
                    padding: '5px 10px',
                    background: '#eee',
                    border: '1px solid #ccc'
                }
            }, {
                name: 'Compact table',
                element: 'table',
                attributes: {
                    cellpadding: '5',
                    cellspacing: '0',
                    border: '1',
                    bordercolor: '#ccc'
                },
                styles: {
                    'border-collapse': 'collapse'
                }
            }, {
                name: 'Borderless Table',
                element: 'table',
                styles: {
                    'border-style': 'hidden',
                    'background-color': '#E6E6FA'
                }
            }, {
                name: 'Square Bulleted List',
                element: 'ul',
                styles: {
                    'list-style-type': 'square'
                }
            },

            /* Widget Styles */
            {
                name: 'Illustration',
                type: 'widget',
                widget: 'image',
                attributes: {
                    'class': 'image-illustration'
                }
            },
            // Media embed
            {
                name: '240p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-240p'
                }
            }, {
                name: '360p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-360p'
                }
            }, {
                name: '480p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-480p'
                }
            }, {
                name: '720p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-720p'
                }
            }, {
                name: '1080p',
                type: 'widget',
                widget: 'embedSemantic',
                attributes: {
                    'class': 'embed-1080p'
                }
            }
        ]
    });

        },
        error:function (){}
    });
}
function selectTableType(type){
  $(".btnactive").removeClass('active');
  $("#btnactive"+type).addClass("active");

  $("#table_type").val(type);
  filterapplay();
}
  </script>
  <div class="card-body">
    <div class="text-center p-3 b-radius-10" style="background: #e6efff;">
      <h3 class="mt-3 f-w-600">Browse Tenant  </h3>
      <p class="text-muted f-w-500">Filter your search to focus on Tenant & Properties</p>
      <ul class="alphabet-filetr bg-white p-2 border b-radius-10 mt-4 mb-4">
       <li><button type="button" class="d-inline-block active activealpha" id="activealpha" !" onclick="selectAlphabet('')">All</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaA" !" onclick="selectAlphabet('A')">A</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaB" !" onclick="selectAlphabet('B')">B</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaC" !" onclick="selectAlphabet('C')">C</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaD" !" onclick="selectAlphabet('D')">D</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaE" !" onclick="selectAlphabet('E')">E</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaF" !" onclick="selectAlphabet('F')">F</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaG" !" onclick="selectAlphabet('G')">G</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaH" !" onclick="selectAlphabet('H')">H</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaI" !" onclick="selectAlphabet('I')">I</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaJ" !" onclick="selectAlphabet('J')">J</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaK" !" onclick="selectAlphabet('K')">K</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaL" !" onclick="selectAlphabet('L')">L</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaM" !" onclick="selectAlphabet('M')">M</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaN" !" onclick="selectAlphabet('N')">N</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaO" !" onclick="selectAlphabet('O')">O</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaP" !" onclick="selectAlphabet('P')">P</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaQ" !" onclick="selectAlphabet('Q')">Q</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaR" !" onclick="selectAlphabet('R')">R</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaS" !" onclick="selectAlphabet('S')">S</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaT" !" onclick="selectAlphabet('T')">T</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaU" !" onclick="selectAlphabet('U')">U</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaV" !" onclick="selectAlphabet('V')">V</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaW" !" onclick="selectAlphabet('W')">W</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaX" !" onclick="selectAlphabet('X')">X</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaY" !" onclick="selectAlphabet('Y')">Y</button></li>
       <li><button type="button" class="d-inline-block activealpha" id="activealphaZ" !" onclick="selectAlphabet('Z')">Z</button></li>
     </ul>
   </div>
   <form id="filterfrom">
    <input type="hidden" name="display_name_alphabet" id="display_name_alphabet" value="">
    <div class="row mt-3">
      <div class="col-lg-3">
        <div class="card">
          <div class="card-header">
            <h5> Filter</h5>
            <div class="card-header-right">
              <button type="reset" class="btn btn-danger" value="reset" onclick="filterapplay('Y')" fdprocessedid="hf7j9k">Reset</button>
            </div>
          </div>
          <div class="card-block">
            <div class="form-group">
              <span class="form-label">Name/Email/Phone</span>
              <input aria-label="Searchname" type="text" name="searchname" class="form-control" onblur="filterapplay()">
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-9">
        <div class="card mb-4">
          <div class="card-block">
            <div class="row align-items-center border-bottom pb-3">
              <div class="col-lg-3">

              </div>
              <div class="col-lg-9">
                <div class="row align-items-center">
                  <div class="col-lg-3">
                    <div class="form-group row align-items-center mb-0">
                      <span class="form-label col-lg-5 text-right mb-0">Show :</span>
                      <div class="col-lg-7">
                        <select aria-label="Limit" class=" border " name="limit" onchange="filterapplay()" fdprocessedid="bjcso">
                          <option value="10">10</option>
                          <option value="20">20</option>
                          <option value="30">30</option>
                          <option value="40">40</option>
                          <option value="50">50</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4">
                    <div class="form-group row align-items-center mb-0">
                      <span class="form-label col-lg-4 text-right mb-0">Sort :</span>
                      <div class="col-lg-8">
                        <select aria-label="Sort By" class=" border " name="sort_by" onchange="filterapplay()" fdprocessedid="ps0e1">
                          <option value="display_name">Name</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-1">
                    <input type="hidden" name="table_type" id="table_type" value="C">
                    <button type="button" class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon btnactive  active" id="btnactiveC" onclick="selectTableType('C')" fdprocessedid="vpkgc">
                      <i class="ti-view-grid m-0"></i>
                    </button>
                  </div>
                  <div class="col-lg-1">
                    <button type="button" class="btn waves-effect waves-light btn-primary btn-outline-primary btn-icon btnactive" id="btnactiveL" onclick="selectTableType('L')" fdprocessedid="7stlni">
                      <i class="ti-menu m-0"></i>
                    </button>
                  </div>
                  <div class="col-lg-3">
                    <a href="index.php?option=COM_INSERT_CUSTOMERS" class="btn btn-primary"><i class="ti-plus text-white"></i> Tenanat</button></a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row" id="formview">
            <?php include_once "custom_list_tenanat_customers_table.php"; ?>
      </div>
    </div>
  </div>
</form>
</div>

