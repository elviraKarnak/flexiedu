
    <!-- Card 2: Diabetes Awareness -->
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="course-card">
        <div class="course-thumb">
            <a href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('medium'); ?>
                <?php else : ?>
                <img src="https://via.placeholder.com/400x200" alt="<?php the_title(get_the_ID()); ?>">
                <?php endif; ?>
            </a>
          
          <div class="badge-group">
                <span class="badge-price">
                    <?php 
                    $price_type = learndash_get_setting(get_the_ID(), 'course_price_type');
                    $price      = learndash_get_setting(get_the_ID(), 'course_price');

                    if ($price_type === 'open' || empty($price)) {
                        echo 'Free';
                    } else {
                        echo get_woocommerce_currency_symbol() . number_format((float)$price, 2);
                    }
                    ?>
                  </span>

                <span class="badge-status">
                <?php echo ucfirst(get_post_status()); ?>
                </span>
          </div>
        </div>
        <div class="course-body">
          <div class="course-title"><?php the_title(); ?></div>
          <div class="course-meta">
            <div class="avatar"><i class="fas fa-user-circle" style="font-size:18px;color:#888;"></i></div>
            <span><?php the_author(); ?></span>
            <span class="divider">|</span>
            <span><?php echo get_the_date('Y-m-d H:i:s'); ?></span>
          </div>
                <!-- Categories -->
            <div class="course-tag-row">
                <i class="fas fa-th"></i>
                <?php
                $cats = get_the_terms(get_the_ID(), 'ld_course_category');
                if (!empty($cats) && !is_wp_error($cats)) {
                    echo esc_html($cats[0]->name);
                } else {
                    echo 'No categories';
                }
                ?>
            </div>

            <!-- Tags -->
            <div class="course-tag-row">
                <i class="fas fa-tag"></i>
                <?php
                $tags = get_the_terms(get_the_ID(), 'ld_course_tag');
                if (!empty($tags) && !is_wp_error($tags)) {
                    echo esc_html($tags[0]->name);
                } else {
                    echo 'No tags';
                }
                ?>
            </div>
        </div>

        <?php if(!$isRequiredCourse){ ?>

        <div class="learndash-wrapper learndash-widget" style="margin-top:10px;">
        <div class="ld-progress ld-progress-inline">

          <div class="ld-progress-heading">
            <div class="ld-progress-stats">

              <div class="ld-progress-percentage ld-secondary-color">
                <?php echo $percent; ?>% Complete
              </div>

              <div class="ld-progress-steps">
                <?php echo $steps . '/' . $total; ?> Steps
              </div>

            </div>
          </div>

          <div class="ld-progress-bar">
            <div class="ld-progress-bar-percentage ld-secondary-background"
                 style="width:<?php echo $percent; ?>%">
            </div>
          </div>

        </div>
      </div>

 

            <!-- Actions -->
            <div class="card-actions">

            <button title="Edit" onclick="window.location.href='<?php echo home_url('/course-builder/'.get_the_ID().'/');?>'">
                <i class="fas fa-pencil-alt"></i>
            </button>

            <button title="View" onclick="window.location.href='<?php the_permalink(); ?>'">
                <i class="far fa-eye"></i>
            </button>

            <button class="btn-delete" title="Delete" data-id="<?php echo get_the_ID(); ?>">
                <i class="fas fa-trash"></i>
            </button>

            </div>
            <?php } else { 
              
              global $wpdb;

              $table = $wpdb->prefix . 'flexi_enrollment_requests';

              $requested = $wpdb->get_row($wpdb->prepare(
                  "SELECT status, reapply_after 
                  FROM $table 
                  WHERE user_id = %d AND course_id = %d",
                  $user_id, $course_id
              ));

              $status = $requested->status ?? '';
              $reapply_after = $requested->reapply_after ?? '';

              $current_time = strtotime(current_time('mysql'));
              $reapply_time = $reapply_after ? strtotime($reapply_after) : 0;

              // Days left
              $days_left = ($reapply_time > $current_time)
                  ? ceil(($reapply_time - $current_time) / (24 * 60 * 60))
                  : 0;

              // Can reapply
              $can_reapply = ($status === 'rejected' && $current_time >= $reapply_time);
              ?>

              <?php if (!$requested) : ?>

                  <!-- Fresh -->
                  <a class="btn btn-primary mt-2 request-enroll"
                      data-course="<?php echo $course_id; ?>"
                      data-org="<?php echo $organizationID; ?>">
                      Request Enrollment
                  </a>

              <?php elseif ($status === 'pending') : ?>

                  <!-- Hide or disable (your choice) -->
                  <!-- You can also skip rendering completely -->
                  <a class="btn btn-secondary mt-2" disabled>
                      Requested
                  </a>

              <?php elseif ($status === 'rejected') : ?>

                  <?php if ($can_reapply) : ?>

                      <!-- Reapply -->
                      <a class="btn btn-primary mt-2 request-enroll"
                          data-course="<?php echo $course_id; ?>"
                          data-org="<?php echo $organizationID; ?>">
                          Request Enrollment
                      </a>

                  <?php else : ?>

                      <!-- Show days -->
                      <a class="btn btn-danger mt-2" disabled>
                          Reapply after <?php echo $days_left; ?> day<?php echo $days_left > 1 ? 's' : ''; ?>
                      </a>

                  <?php endif; ?>

              <?php endif; ?>

                            <?php } ?>          
      </div>
    </div>














           