<?php 
$user = wp_get_current_user();

    // ==========================
    // ✅ ORGANIZER VIEW
    // ==========================

if (in_array('organizer', (array) $user->roles)) : 

    $organizationID = $organization->ID;  
    $organization_author_id = $organization->post_author;

    $lmsGroup   = get_field('groups', $organizationID);
    $lmsGroupId = $lmsGroup->ID;

    $user_id  = get_current_user_id();

    // ✅ Group courses
    $courses = learndash_group_enrolled_courses($lmsGroupId);

    $filtered_courses = [];

    foreach ($courses as $course_id) {
        if (sfwd_lms_has_access($course_id, $user_id)) {
            $filtered_courses[] = $course_id;
        }
    }

    // ✅ Organizer's own courses
    $author_courses = get_posts([
        'post_type'      => 'sfwd-courses',
        'author'         => $organization_author_id,
        'fields'         => 'ids',
        'posts_per_page' => -1
    ]);

    // ✅ Merge both
    $final_courses = array_unique(array_merge($filtered_courses, $author_courses));

    // ✅ Query
    $args = [
        'post_type'      => 'sfwd-courses',
        'post__in'       => !empty($final_courses) ? $final_courses : [0],
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
        ];

        $query = new WP_Query($args);

            if ($query->have_posts()) : ?>

                <section class="course-details text-center w-100">



                    <div class="text-center">

                        <div id="course-admin" class="ld-course-list-content">
                            <div class="ld-course-list-items row">

                                <?php
                                while ($query->have_posts()) :
                                    $query->the_post();

                                    $course_id = get_the_ID();

                                    // Progress
                                    $progress = learndash_user_get_course_progress($user_id, $course_id);
                                    $percent  = $progress['percentage'] ?? 0;
                                    $steps    = $progress['completed'] ?? 0;
                                    $total    = $progress['total'] ?? 0;

                                    $layout_path = FlexiEdu_Courses_PATH . 'templates/organization/layout/';

                                    include $layout_path . 'course-content.php';
                                ?>

                                
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </section>

            <?php wp_reset_postdata(); endif; ?>


    <?php 
        // ==========================
        // ✅ SUBSCRIBER (LEARNER VIEW)
        // ==========================
    elseif (in_array('subscriber', (array) $user->roles)) : ?>

        <style>
            /* Hide unwanted parts */
            .course-details .ld-profile-card,
            .course-details .ld-course-list {
                display: none !important;
            }
        </style>

        <section class="course-details text-center w-100">

            <?php echo do_shortcode('[ld_profile]'); 

              echo do_shortcode('[ld_course_list orderby="ID" mycourses="enrolled" progress_bar="true"]');

              ?>
            
        </section>    


    <?php endif; ?>