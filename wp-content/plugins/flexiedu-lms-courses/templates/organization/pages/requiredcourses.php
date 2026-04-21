<?php 

$user = wp_get_current_user();

if (in_array('subscriber', (array) $user->roles)) :

    $user_id = get_current_user_id();

    $organizationID = $organization->ID;  
    $organization_author_id = $organization->post_author;

    // ==========================================
    // STEP 1: GET ALL COURSES (GROUP + ORGANIZER)
    // ==========================================

    $lmsGroup   = get_field('groups', $organizationID);
    $lmsGroupId = $lmsGroup ? $lmsGroup->ID : 0;

    // Group courses
    $group_courses = !empty($lmsGroupId) ? learndash_group_enrolled_courses($lmsGroupId) : [];

    // Organizer courses
    $author_courses = get_posts([
        'post_type'      => 'sfwd-courses',
        'author'         => $organization_author_id,
        'fields'         => 'ids',
        'posts_per_page' => -1
    ]);

    // Merge all courses
    $all_courses = array_unique(array_merge($group_courses, $author_courses));

    // ==========================================
    // STEP 2: SPLIT ENROLLED / UNENROLLED
    // ==========================================

    $enrolled_courses   = [];
    $unenrolled_courses = [];

    foreach ($all_courses as $course_id) {

        if (sfwd_lms_has_access($course_id, $user_id)) {
            $enrolled_courses[] = $course_id;
        } else {
            $unenrolled_courses[] = $course_id;
        }
    }

    $enrolled_count = count($enrolled_courses);

?>

<!-- ==========================================
❌ UNENROLLED COURSES (REQUEST)
========================================== -->

<section class="course-details text-center w-100 mt-5">

    <h3 class="mb-5"><?php esc_html_e('Available Courses', 'flexiedu-lms-courses'); ?></h3>

    <?php if (!empty($unenrolled_courses)) : 

        $args = [
            'post_type'      => 'sfwd-courses',
            'post__in'       => $unenrolled_courses,
            'orderby'        => 'post__in',
            'posts_per_page' => -1,
        ];

        $query = new WP_Query($args);

        if ($query->have_posts()) : ?>

            <div class="ld-course-list-items row">

                <?php while ($query->have_posts()) : $query->the_post();

                    $course_id = get_the_ID();

                    $isRequiredCourse = true;

                    $layout_path = FlexiEdu_Courses_PATH . 'templates/organization/layout/';
                ?>
                        <?php include $layout_path . 'course-content.php'; ?>

                <?php endwhile; ?>

            </div>

        <?php wp_reset_postdata(); endif; ?>

    <?php else : ?>

        <p><?php esc_html_e('No available courses.', 'flexiedu-lms-courses'); ?></p>

    <?php endif; ?>

</section>


<?php endif; ?>
