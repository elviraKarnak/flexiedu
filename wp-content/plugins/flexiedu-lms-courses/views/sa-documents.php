<?php

    $current_user_id = get_current_user_id();

    $args = [
        'post_type'      => 'document',
        'posts_per_page' => -1,
        // 'tax_query'      => [
        //     [
        //         'taxonomy' => 'document_type',
        //         'field'    => 'slug',
        //         'terms'    => 'admin-group'
        //     ]
        //   ]
        ];

    $query = new WP_Query($args); ?>

    <div class="d-flex justify-content-end mb-4">
        <button
            type="button"
            class="btn btn-primary btn-primary-transparent"
            data-bs-toggle="modal"
            data-bs-target="#addDocModal"
        >
            <?php esc_html_e('Add Document', 'flexiedu-lms-courses'); ?>
        </button>
    </div>

    <div class="row">

            <?php if ($query->have_posts()) : ?>

            <?php while ($query->have_posts()) : $query->the_post(); 

                $file_id  = get_post_meta(get_the_ID(), '_document_file', true);
                $file_url = $file_id ? wp_get_attachment_url($file_id) : '#';

                $layout_path = FlexiEdu_Courses_PATH . 'templates/organization/layout/';

                 include $layout_path . 'document-content.php';
            ?>

          

        <?php endwhile; wp_reset_postdata(); ?>

        <?php else : ?>

        <p><?php esc_html_e('No documents found.', 'flexiedu-lms-courses'); ?></p>

        <?php endif; ?>

    </div>


    
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
            <?php esc_html_e('Add New Document', 'flexiedu-lms-courses'); ?>
          </h5>
          <button type="button" class="btn-close-wp" data-bs-dismiss="modal" aria-label="Close">&#x2715;</button>
        </div>

        <!-- Body -->
        <div class="modal-body">
          <form id="addDocForm" novalidate>

           <input type="hidden" name="docID" id="docID" value="">
           <input type="hidden" name="docgroup" id="docgroup" value="other-group">

            <!-- Title -->
            <div class="field-group">
              <label for="docTitle" class="form-label">
                Title <span class="required-star">*</span>
              </label>
              <input
                type="text"
                class="form-control"
                id="docTitle"
                placeholder="<?php echo esc_attr__('Enter document title...', 'flexiedu-lms-courses'); ?>"
                required
                autocomplete="off"
              />
              <div class="invalid-feedback"><?php esc_html_e('Please enter a document title.', 'flexiedu-lms-courses'); ?></div>
            </div>

            <hr class="section-divider">

            <div class="field-group">
              <label class="form-label">
                <?php esc_html_e('Featured Image', 'flexiedu-lms-courses'); ?>
              </label>
              <input type="file" class="form-control" id="docFeaturedImage" name="featured_image" accept="image/*" />
              <div id="featuredImagePreview" class="mt-3" style="display:none;">
                <img
                  id="featuredImagePreviewImg"
                  src=""
                  alt="<?php echo esc_attr__('Featured image preview', 'flexiedu-lms-courses'); ?>"
                  style="max-width:100%; height:auto; border-radius:12px;"
                />
              </div>
            </div>

            <hr class="section-divider">

            <!-- File Upload -->
            <div class="field-group">
              <label class="form-label">
                Document File <span class="required-star">*</span>
              </label>
              <div class="file-drop-zone" id="dropZone">
                <input type="file" id="docFile" name="document_file" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip" required/>
                <div class="upload-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="16 16 12 12 8 16"/>
                    <line x1="12" y1="12" x2="12" y2="21"/>
                    <path d="M20.39 18.39A5 5 0 0 0 18 9h-1.26A8 8 0 1 0 3 16.3"/>
                  </svg>
                </div>
                <p class="drop-text"><strong><?php esc_html_e('Click to upload', 'flexiedu-lms-courses'); ?></strong> <?php esc_html_e('or drag & drop', 'flexiedu-lms-courses'); ?></p>
                <p class="file-hint"><?php esc_html_e('PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP', 'flexiedu-lms-courses'); ?></p>
              </div>
              <!-- File preview pill -->
              <div id="filePreview">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--wp-blue);flex-shrink:0">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                  <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span class="file-pill-name" id="fileName">-</span>
                <span class="file-pill-size" id="fileSize"></span>
                <button type="button" class="btn-remove-file" id="removeFile" title="Remove file">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                  </svg>
                </button>
              </div>
              <div id="fileError" class="text-danger mt-1" style="font-size:12px;display:none;"><?php esc_html_e('Please select a file to upload.', 'flexiedu-lms-courses'); ?></div>
            </div>

            <hr class="section-divider">

            <div class="field-group mb-1">
                <label class="form-label d-block mb-2"><?php esc_html_e('Document Categories', 'flexiedu-lms-courses'); ?></label>

                <div class="doc-types-grid">

                  <?php
                  $terms = get_terms([
                      'taxonomy'   => 'document_cat',
                      'hide_empty' => false,
                  ]);

                  if (!empty($terms) && !is_wp_error($terms)) :

                      foreach ($terms as $term) :
                  ?>

                      <div class="form-check">
                        <input 
                          class="form-check-input" 
                          type="checkbox" 
                          name="document-type[]"
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
                      echo '<p style="font-size:12px;color:red;">' . esc_html__('No document types found', 'flexiedu-lms-courses') . '</p>';
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
          <button type="button" class="btn-cancel" data-bs-dismiss="modal"><?php esc_html_e('Cancel', 'flexiedu-lms-courses'); ?></button>
          <!-- <button type="button" class="btn-draft" id="saveDraftBtn">Save Draft</button> -->
          <button type="button" class="btn-publish" id="publishBtn">
            <?php esc_html_e('Publish', 'flexiedu-lms-courses'); ?>
          </button>
        </div>

      </div>
    </div>
  </div>


<!-- add document modal end -->