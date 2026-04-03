<!-- add document modal start -->

<!-- Modal -->
  <div class="modal fade" id="addDocModal" tabindex="-1" aria-labelledby="addDocModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:540px;">
      <div class="modal-content">

     
        <!-- Header -->
        <div class="modal-header">
          <h5 class="modal-title" id="addDocModalLabel">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
              <polyline points="14 2 14 8 20 8"/>
              <line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/>
            </svg>
            Add New Document
          </h5>
          <button type="button" class="btn-close-wp" data-bs-dismiss="modal" aria-label="Close">&#x2715;</button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <form id="addDocForm" novalidate>

           <input type="hidden" name="docID" id="docID" value="">

            <!-- Title -->
            <div class="field-group">
              <label for="docTitle" class="form-label">
                Title <span class="required-star">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                id="docTitle"
                placeholder="Enter document title…"
                required
                autocomplete="off"
              />
              <div class="invalid-feedback">Please enter a document title.</div>
            </div>

            <hr class="section-divider">

            <!-- File Upload -->
            <div class="field-group">
              <label class="form-label">
                Document File <span class="required-star">*</span>
              </label>
              <div class="file-drop-zone" id="dropZone">
                <input type="file" id="docFile" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip" required/>
                <div class="upload-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 16 12 12 8 16"/>
                    <line x1="12" y1="12" x2="12" y2="21"/>
                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                  </svg>
                </div>
                <p class="drop-text"><strong>Click to upload</strong> or drag & drop</p>
                <p class="file-hint">PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP</p>
              </div>
              <!-- File preview pill -->
              <div id="filePreview">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--wp-blue);flex-shrink:0">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span class="file-pill-name" id="fileName">—</span>
                <span class="file-pill-size" id="fileSize"></span>
                <button type="button" class="btn-remove-file" id="removeFile" title="Remove file">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                  </svg>
                </button>
              </div>
              <div id="fileError" class="text-danger mt-1" style="font-size:12px;display:none;">Please select a file to upload.</div>
            </div>

            <hr class="section-divider">

            <div class="field-group mb-1">
                <label class="form-label d-block mb-2">Document Type</label>

                <div class="doc-types-grid">

                  <?php
                  $terms = get_terms([
                      'taxonomy'   => 'document-type',
                      'hide_empty' => false,
                  ]);

                  if (!empty($terms) && !is_wp_error($terms)) :

                      foreach ($terms as $term) :
                  ?>

                      <div class="form-check">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          id="type_<?php echo esc_attr($term->term_id); ?>" 
                          value="<?php echo esc_attr($term->slug); ?>"
                        >
                        <label 
                          class="form-check-label" 
                          for="type_<?php echo esc_attr($term->term_id); ?>"
                        >
                          <?php echo esc_html($term->name); ?>
                        </label>
                      </div>

                  <?php
                      endforeach;

                  else :
                      echo '<p style="font-size:12px;color:red;">No document types found</p>';
                  endif;
                  ?>

                </div>
              </div>

            <hr class="section-divider">

            <!-- Visibility -->
            <!-- <div class="field-group mb-0">
              <label for="docVisibility" class="form-label">Visibility</label>
              <select class="form-select" id="docVisibility">
                <option value="public" selected>Public</option>
                <option value="private">Private</option>
                <option value="password">Password Protected</option>
              </select>
              <div class="visibility-badge" id="visibilityBadge">
                <span class="badge-dot"></span> Visible to everyone
              </div>
            </div> -->

            <!-- Status strip -->
            <div id="statusStrip" class="mt-3"></div>

          </form>
        </div>

        <!-- Footer -->
        <div class="modal-footer">
          <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
          <!-- <button type="button" class="btn-draft" id="saveDraftBtn">Save Draft</button> -->
          <button type="button" class="btn-publish" id="publishBtn">
            Publish
          </button>
        </div>

      </div>
    </div>
  </div>


<!-- add document modal end -->


       <!-- Modal -->
 <!-- Bootstrap Modal -->
    <div class="modal fade" id="liveClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="liveClassModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="liveClassModalTime"></p>
                </div>

                <div class="modal-footer">
                    <a id="liveClassModalJoinBtn" href="#" target="_blank" class="btn btn-primary">
                        Join Class
                    </a>
                </div>

            </div>
        </div>
    </div>

    <?php
        $title_ftrcta = get_field('title_ftrcta', 'option');
    $footer_link_ftrcta = get_field('footer_link_ftrcta', 'option');
    $footer_logo_ftr = get_field('footer_logo_ftr', 'option');

    ?>

<footer>
    <div class="container">
      <?php 

          $privacy_policy_ftr = get_field('privacy_policy_ftr', 'option');
          $policies_ftr = get_field('policies_ftr', 'option');
          $copyright_text = get_field('copyright_text', 'option');
      
      
      ?>
      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <div class="footer-bottom-content">
          <?php if($copyright_text ){ ?>
          <div class="copyright">
             <?php echo $copyright_text; ?>
          </div>
          <?php } ?>
          <?php 
          
          $developer_name = get_field('developer_name', 'option');
          $developer_link = get_field('developer_link', 'option');
          $developer_logo_img = get_field('developer_logo_img', 'option');
          
          ?>
          <?php if($developer_name || $developer_link || $developer_logo_img){ ?>
            <div class="developer-credit">
              <span><?php echo $developer_name; ?></span>
              <span class="developer-logo">
                <a href="<?php echo $developer_link; ?>" target="_blank">
                  <img src="<?php echo $developer_logo_img['url']; ?>" alt="elvirainfortech">
                </a></span>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </footer>

  <script>
  jQuery(window).on('load', function () {
      if (typeof jQuery !== 'undefined') {
          jQuery(document).trigger('learndash-init');

          // Re-bind expand/collapse manually
          jQuery('.ld-expand-button').off().on('click', function(e){
              e.preventDefault();

              let container = jQuery(this).closest('.ld-item-list-item');
              container.toggleClass('ld-expanded');

              container.find('.ld-item-children').slideToggle(200);
          });

          // Expand All button fix
          jQuery('.ld-expand-all').off().on('click', function(){
              let expanded = jQuery(this).hasClass('ld-expanded');

              if (expanded) {
                  jQuery('.ld-item-list-item').removeClass('ld-expanded')
                      .find('.ld-item-children').slideUp();
              } else {
                  jQuery('.ld-item-list-item').addClass('ld-expanded')
                      .find('.ld-item-children').slideDown();
              }

              jQuery(this).toggleClass('ld-expanded');
          });
      }
  });
  </script>



  <script>
    jQuery(document).ready(function ($) {

        var ajaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';

        $(document).on('click', '.edit-doc', function (e) {

              e.preventDefault();

              var postID = $(this).data('id');

              $('#docID').val(postID);

              $.ajax({
                  url: ajaxUrl,
                  type: 'POST',
                  data: {
                      action: 'get_document',
                      post_id: postID
                  },
                  beforeSend: function () {
                      console.log('Fetching document...');
                  },
                  success: function (res) {

                      console.log('Response:', res);

                      if (res.success && res.data) {

                          // ✅ Title
                          $('#docTitle').val(res.data.title || '');

                          // ✅ Reset checkboxes
                          $('.doc-types-grid input').prop('checked', false);

                          // ✅ Safe taxonomy handling
                          if (Array.isArray(res.data.types)) {

                              res.data.types.forEach(function (slug) {
                                  $('.doc-types-grid input[value="' + slug + '"]').prop('checked', true);
                              });

                          } else {
                              console.warn('Types is not array:', res.data.types);
                          }

                          // ✅ File preview
                          if (res.data.file) {
                              $('#fileName').text('Existing file');
                              $('#fileSize').text('');
                              $('#filePreview').addClass('show');
                          } else {
                              $('#filePreview').removeClass('show');
                          }

                          // ✅ Modal title
                          $('#addDocModalLabel').text('Edit Document');

                          $("#publishBtn").text('Update');

                          // ✅ Open modal
                          $('#addDocModal').modal('show');

                      } else {
                          alert(res.data || 'Failed to load document');
                      }
                  },
                  error: function (xhr, status, error) {
                      console.error('AJAX Error:', error);
                      alert('Server error. Please try again.');
                  }
              });


          });


        // $(document).on('click', '.edit-doc', function (e) {
        //     e.preventDefault();

        //     var postID = $(this).data('id');

        //     $('#docID').val(postID);

        //     $.ajax({
        //         url: ajaxUrl,
        //         type: 'POST',
        //         data: {
        //         action: 'get_document',
        //         post_id: postID
        //         },
        //         success: function (res) {

        //         if (res.success) {

        //             // Title
        //             $('#docTitle').val(res.data.title);

        //             // Reset checkboxes
        //             $('.doc-types-grid input').prop('checked', false);

        //             // Set selected taxonomy
        //             res.data.types.forEach(function (slug) {
        //             $('.doc-types-grid input[value="' + slug + '"]').prop('checked', true);
        //             });

        //             // Show existing file (optional)
        //             if (res.data.file) {
        //             $('#fileName').text('Existing file');
        //             $('#fileSize').text('');
        //             $('#filePreview').addClass('show');
        //             }

        //             // Change modal title
        //             $('#addDocModalLabel').text('Edit Document');

        //             // Open modal
        //             $('#addDocModal').modal('show');

        //         } else {
        //             alert(res.data || 'Failed to load');
        //         }

        //         }
        //     });

        // });
        
      // ── Helpers ──────────────────────────────────────────────
      function formatBytes(bytes) {
        if (bytes < 1024)    return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
      }

      function showFile(file) {
        $('#fileName').text(file.name);
        $('#fileSize').text(formatBytes(file.size));
        $('#filePreview').addClass('show');
        $('#fileError').hide();
      }

      function clearFile() {
        $('#docFile').val('');
        $('#filePreview').removeClass('show');
        $('#fileName').text('—');
        $('#fileSize').text('');
      }

      function showStatus(type, msg) {
        $('#statusStrip').text(msg).attr('class', 'mt-3 show ' + type);
        setTimeout(function () { $('#statusStrip').attr('class', 'mt-3'); }, 3500);
      }

      function validate(publishing) {
        var valid = true;

        // Title
        if (!$.trim($('#docTitle').val())) {
          $('#docTitle').addClass('is-invalid');
          valid = false;
        } else {
          $('#docTitle').removeClass('is-invalid');
        }

        // File (required only on publish)
        if (publishing && !$('#docFile')[0].files.length) {
          $('#fileError').show();
          valid = false;
        } else {
          $('#fileError').hide();
        }

        return valid;
      }

      // ── File input change ────────────────────────────────────
      $('#docFile').on('change', function () {
        if (this.files.length) showFile(this.files[0]);
      });

      // ── Remove file ──────────────────────────────────────────
      $('#removeFile').on('click', clearFile);

      // ── Drag & drop ──────────────────────────────────────────
      $('#dropZone')
        .on('dragover', function (e) {
          e.preventDefault();
          $(this).addClass('dragover');
        })
        .on('dragleave', function () {
          $(this).removeClass('dragover');
        })
        .on('drop', function (e) {
          e.preventDefault();
          $(this).removeClass('dragover');
          var files = e.originalEvent.dataTransfer.files;
          if (files.length) showFile(files[0]);
        });

      // ── Visibility badge ─────────────────────────────────────
      var visLabels = {
        public:   { color: 'var(--wp-success)', text: 'Visible to everyone' },
        private:  { color: '#646970',           text: 'Only visible to admins & editors' },
        password: { color: '#d97706',           text: 'Protected by a password' }
      };

      $('#docVisibility').on('change', function () {
        var l = visLabels[$(this).val()];
        $('#visibilityBadge .badge-dot').css('background', l.color);
        $('#visibilityBadge').contents().last().replaceWith(' ' + l.text);
      });

      // ── Publish ──────────────────────────────────────────────
      // $('#publishBtn').on('click', function () {
      //   if (validate(true)) {
      //     showStatus('success', '✓ Document published successfully.');
      //     setTimeout(function () {
      //       bootstrap.Modal.getInstance($('#addDocModal')[0]).hide();
      //       $('#addDocForm')[0].reset();
      //       clearFile();
      //     }, 1400);
      //   }
      // });

      // ── Save Draft ───────────────────────────────────────────
      $('#saveDraftBtn').on('click', function () {
        if (validate(false)) {
          showStatus('success', '✓ Draft saved.');
        }
      });

      // ── Clear invalid on title input ─────────────────────────
      $('#docTitle').on('input', function () {
        if ($.trim($(this).val())) $(this).removeClass('is-invalid');
      });

      // ── Reset on modal close ─────────────────────────────────
      $('#addDocModal').on('hidden.bs.modal', function () {
        $('#addDocForm')[0].reset();
        $('#addDocForm .is-invalid').removeClass('is-invalid');
        clearFile();
        $('#statusStrip').attr('class', 'mt-3');
        $('#visibilityBadge .badge-dot').css('background', 'var(--wp-success)');
        $('#visibilityBadge').contents().last().replaceWith(' Visible to everyone');
      });


      $('#publishBtn').on('click', function () {

        console.log('clicked');

        // if (!validate(true)) return;

        var formData = new FormData();

       

        // Basic
        formData.append('action', 'add_document');
        formData.append('title', $('#docTitle').val());
        formData.append('post_id', $('#docID').val());

        // File
        var file = $('#docFile')[0].files[0];
        if (file) {
          formData.append('document_file', file);
        }

        // Taxonomy (checkbox loop — already dynamic)
        var types = [];
        $('.doc-types-grid input:checked').each(function () {
          types.push($(this).val());
        });

        formData.append('doc_types', JSON.stringify(types));

        $.ajax({
          url: ajaxUrl,
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,

              beforeSend: function () {
                $('#publishBtn').prop('disabled', true);
              },

              success: function (res) {

                if (res.success) {

                  showStatus('success', '✓ Document published successfully.');

                  setTimeout(function () {
                    bootstrap.Modal.getInstance($('#addDocModal')[0]).hide();
                    $('#addDocForm')[0].reset();
                    clearFile();
                  }, 1200);

                } else {
                  showStatus('error', res.data || 'Error occurred');
                }

              },

              error: function () {
                showStatus('error', 'Server error');
              },

              complete: function () {
                $('#publishBtn').prop('disabled', false);
              }

            });

      });


      $(document).on('click', '.delete-doc', function () {

          var btn = $(this);
          var postID = btn.data('id');

          if (!confirm('Are you sure you want to delete this document?')) {
              return;
          }

          $.ajax({
              url: ajaxUrl,
              type: 'POST',
              data: {
                  action: 'delete_document',
                  post_id: postID
              },
              beforeSend: function () {
                  btn.prop('disabled', true).text('Deleting...');
              },
              success: function (res) {

                  if (res.success) {
                      // ✅ Remove card from UI
                      btn.closest('.doc-card').fadeOut(300, function () {
                          $(this).remove();
                      });

                  } else {
                      alert(res.data || 'Delete failed');
                      btn.prop('disabled', false).text('Delete');
                  }
              },
              error: function () {
                  alert('Server error');
                  btn.prop('disabled', false).text('Delete');
              }
          });

      });

  });
  </script>