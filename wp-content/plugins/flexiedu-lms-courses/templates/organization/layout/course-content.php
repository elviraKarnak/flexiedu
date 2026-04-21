
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

        <?php // var_dump($isRequiredCourse); ?>
        

        <?php if(!$isRequiredCourse){ 

         if (in_array('organizer', (array) $user->roles) || 
         in_array('administrator', (array) $user->roles) || 
         in_array('wdm_instructor', (array) $user->roles) ||
         in_array('group_leader', (array) $user->roles)) { ?>

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


         <?php } else {?>

                <div class="caption" bis_skin_checked="1">
				
					<div class="ld_course_grid_button" bis_skin_checked="1">
                        <a aria-label="Continue Study: Python Refresher" class="btn btn-primary" 
                        href="<?php the_permalink(); ?>">
                            Continue Study					
                        </a>
				    </div>
                    <div class="learndash-wrapper learndash-widget" bis_skin_checked="1">
		                <div class="ld-progress ld-progress-inline" bis_skin_checked="1">
					        <div class="ld-progress-heading" bis_skin_checked="1">
								<div class="ld-progress-stats" bis_skin_checked="1">
					                <div class="ld-progress-percentage ld-secondary-color" bis_skin_checked="1">
					                    <?php echo $percent; ?>% Complete			
                                    </div>
					            </div> <!--/.ld-progress-stats-->
			                </div>

                            <div class="ld-progress-bar" bis_skin_checked="1">
                                <div class="ld-progress-bar-percentage ld-secondary-background" style="width:<?php echo $percent; ?>%" bis_skin_checked="1"></div>
                            </div>
				        </div> <!--/.ld-progress-->
	                </div>
															
                </div>


            <?php } ?>

    
            <?php } else { 
              
              global $wpdb;

              $table = $wpdb->prefix . 'flexi_enrollment_requests';

              $requested = $wpdb->get_row($wpdb->prepare(
                  "SELECT status, reapply_after 
                  FROM $table 
                  WHERE user_id = %d AND course_id = %d",
                  $user_id, $course_id
              ));

              //var_dump($requested);

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
                          <?php
                          printf(
                              esc_html__('Reapply after %d day%s', 'flexiedu-lms-courses'),
                              (int) $days_left,
                              $days_left > 1 ? 's' : ''
                          );
                          ?>
                      </a>

                  <?php endif; ?>

               <?php elseif ($status === 'approved') : ?>

                <div class="caption" bis_skin_checked="1">
				
					<div class="ld_course_grid_button" bis_skin_checked="1">
                        <a aria-label="Continue Study: Python Refresher" class="btn btn-primary" 
                        href="<?php the_permalink(); ?>">
                            Continue Study					
                        </a>
				    </div>
                    <div class="learndash-wrapper learndash-widget" bis_skin_checked="1">
		                <div class="ld-progress ld-progress-inline" bis_skin_checked="1">
					        <div class="ld-progress-heading" bis_skin_checked="1">
								<div class="ld-progress-stats" bis_skin_checked="1">
					                <div class="ld-progress-percentage ld-secondary-color" bis_skin_checked="1">
					                    <?php echo $percent; ?>% Complete			
                                    </div>
					            </div> <!--/.ld-progress-stats-->
			                </div>

                            <div class="ld-progress-bar" bis_skin_checked="1">
                                <div class="ld-progress-bar-percentage ld-secondary-background" style="width:<?php echo $percent; ?>%" bis_skin_checked="1"></div>
                            </div>
				        </div> <!--/.ld-progress-->
	                </div>
															
                </div>
                <?php endif; ?>

                            <?php } ?>          
      </div>
    </div>














           
