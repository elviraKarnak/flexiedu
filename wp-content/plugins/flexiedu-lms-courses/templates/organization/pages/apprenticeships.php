<?php
$user = wp_get_current_user();

$organizationID        = isset($organization->ID) ? (int) $organization->ID : 0;
$organizationLink      = $organizationID ? get_permalink($organizationID) : '';
$organization_slug     = isset($organization->post_name) ? $organization->post_name : '';
$organization_author_id = isset($organization->post_author) ? (int) $organization->post_author : 0;

$current_user_id        = get_current_user_id();

$user = wp_get_current_user();


$lms_group    = function_exists('get_field') ? get_field('groups', $organizationID  ) : null;
$lms_group_id = is_object($lms_group) && isset($lms_group->ID) ? (int) $lms_group->ID : 0;

$group_courses = !empty($lms_group_id) && function_exists('learndash_group_enrolled_courses')
    ? learndash_group_enrolled_courses($lms_group_id)
    : [];

$filtered_group_courses = [];

if (!empty($group_courses) && function_exists('sfwd_lms_has_access')) {
    foreach ($group_courses as $course_id) {
        if (sfwd_lms_has_access($course_id, $current_user_id)) {
            $filtered_group_courses[] = $course_id;
        }
    }
}

$author_courses = get_posts([
    'post_type'      => 'sfwd-courses',
    'post_status'    => 'publish',
    'author'         => $organization_author_id,
    'fields'         => 'ids',
    'posts_per_page' => -1,
]);

$final_course_ids = array_unique(array_merge($filtered_group_courses, $author_courses));

$courses = !empty($final_course_ids)
    ? get_posts([
        'post_type'      => 'sfwd-courses',
        'post_status'    => 'publish',
        'post__in'       => $final_course_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ])
    : [];

    global $wpdb;

    $table_name = $wpdb->prefix . 'course_bundles';
    $bundles    = (!empty($organization_slug) && !empty($organization_author_id))
        ? $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE org_slug = %s AND created_by = %d ORDER BY created_at DESC",
                $organization_slug,
                $organization_author_id
            )
        )
        : [];
    ?>

    <section class="learner-content">
        <div class="d-flex align-items-center justify-content-end mb-4">
            <?php if (in_array('organizer', (array) $user->roles, true)) : ?>
                    <a href="javascript:void(0);" class="btn btn-primary open-create-bundle" data-bs-toggle="modal" data-bs-target="#addBundles">
                       
                       <?php esc_html_e('+ Create Course Bundles', 'flexiedu-lms-courses'); ?>
                    </a>
                <?php endif; ?>
        </div>

        <div class="middle">
            <div class="learner-content__cards w-100">
            
                <?php if (!empty($bundles)) : ?>
                    <?php foreach ($bundles as $bundle) : ?>
                        <?php
                        $course_ids = json_decode($bundle->course_ids, true);
                        $course_ids = is_array($course_ids)
                            ? array_values(array_filter(array_map('absint', $course_ids)))
                            : [];
                        ?>

                        <div class="learner-content__card">
                            <a href="<?php echo $organizationLink . 'coursebundle/?bundle_id=' . (int) $bundle->id; ?>">
                                 <img src="<?php echo esc_url(FlexiEdu_Courses_URL . 'assets/images/apprenticeships.svg'); ?>" alt="<?php echo esc_attr($bundle->title); ?>">
                            </a>
                           
                           <a  href="<?php echo $organizationLink . 'coursebundle/?bundle_id=' . (int) $bundle->id; ?>">
                            <p class="learner-content__card-title" style="color: #000; font-weight: 600;"><?php echo esc_html($bundle->title); ?></p></a> 
                            <p class="mb-0 text-muted">
                                <?php
                                printf(
                                    esc_html(_n('%d course', '%d courses', count($course_ids), 'flexiedu-lms-courses')),
                                    count($course_ids)
                                );
                                ?>
                            </p>

                    
                                    <div class="card-actions w-100">
                                    <?php if (in_array('organizer', (array) $user->roles, true)) : ?>
                                        <button title="Edit" data-id="<?php echo (int) $bundle->id; ?>" class="edit-bundle-btn">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>

                                        <button title="View" onclick="window.location.href='<?php echo $organizationLink . 'coursebundle/?bundle_id=' . (int) $bundle->id; ?>'">
                                            <i class="far fa-eye"></i>
                                        </button>

                                        <button class="btn-delete" title="Delete" data-id="<?php echo (int) $bundle->id; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php else: ?>
                                          <button title="View" onclick="window.location.href='<?php echo $organizationLink . 'coursebundle/?bundle_id=' . (int) $bundle->id; ?>'">
                                            <i class="far fa-eye"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                         

                                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php esc_html_e('No course bundles found.', 'flexiedu-lms-courses'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <div class="modal fade" id="addBundles" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="addBundlesTitle">Add Bundle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                        <div class="w-100">
                         
                                    <div class=" shadow-sm border-0">
                                        <div class="bg-white border-0 ">
                                            <h1 class="h3 mb-1"><?php esc_html_e('Add Course Bundle', 'flexiedu-lms-courses'); ?></h1>
                                            <p class="text-muted mb-0"><?php esc_html_e('Create a new course bundle by selecting multiple courses.', 'flexiedu-lms-courses'); ?></p>
                                        </div>

                                        <div>
                                            <div id="course-bundle-form-message" class="mb-3" style="display:none;"></div>

                                            <form id="flexiedu-course-bundle-form" class="needs-validation" novalidate>
                                                <div class="mb-4">
                                                    <label for="course_bundle_title" class="form-label fw-semibold">
                                                        <?php esc_html_e('Bundle Title', 'flexiedu-lms-courses'); ?>
                                                    </label>
                                                    <input
                                                        type="text"
                                                        class="form-control form-control-lg"
                                                        id="course_bundle_title"
                                                        name="title"
                                                        value="<?php echo $course_bundle_title; ?>"
                                                        placeholder="<?php esc_attr_e('Enter bundle title', 'flexiedu-lms-courses'); ?>"
                                                        required
                                                    >
                                                    <div class="invalid-feedback">
                                                        <?php esc_html_e('Please enter a title.', 'flexiedu-lms-courses'); ?>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label for="course_bundle_description" class="form-label fw-semibold">
                                                        <?php esc_html_e('Description', 'flexiedu-lms-courses'); ?>
                                                    </label>
                                                    <textarea
                                                        class="form-control"
                                                        id="course_bundle_description"
                                                        name="description"
                                                        rows="6"
                                                        placeholder="<?php esc_attr_e('Describe the course bundle', 'flexiedu-lms-courses'); ?>"
                                                        required
                                                    ><?php echo $course_bundle_description; ?></textarea>
                                                    <div class="invalid-feedback">
                                                        <?php esc_html_e('Please enter a description.', 'flexiedu-lms-courses'); ?>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <div id="course_bundle_courses_label" class="form-label fw-semibold">
                                                        <?php esc_html_e('Select Courses', 'flexiedu-lms-courses'); ?>
                                                    </div>
                                                    <div
                                                        id="course_bundle_courses"
                                                        class="border rounded p-3 overflow-auto"
                                                        role="group"
                                                        aria-labelledby="course_bundle_courses_label"
                                                        style="max-height: 220px;"
                                                    >
                                                        <?php if (!empty($courses)) : ?>
                                                            <?php foreach ($courses as $course) : 
                                                                
                                                                $is_checked = in_array($course->ID, $selected_courses ?? []) ? 'checked' : '';
                                                                
                                                                
                                                                ?>
                                                                <div class="form-check mb-2">
                                                                    <input
                                                                        class="form-check-input course-bundle-course"
                                                                        type="checkbox"
                                                                        name="course_ids[]"
                                                                        id="course_bundle_course_<?php echo esc_attr($course->ID); ?>"
                                                                        value="<?php echo esc_attr($course->ID); ?>"
                                                                        <?php echo $is_checked; ?>
                                                                    >
                                                                    <label class="form-check-label" for="course_bundle_course_<?php echo esc_attr($course->ID); ?>">
                                                                        <?php echo esc_html($course->post_title); ?>
                                                                    </label>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php else : ?>
                                                            <p class="text-muted mb-0"><?php esc_html_e('No courses available', 'flexiedu-lms-courses'); ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="invalid-feedback">
                                                        <?php esc_html_e('Please select at least one course.', 'flexiedu-lms-courses'); ?>
                                                    </div>
                                                </div>
                                                <input type="hidden" id="bundle_id" name="bundle_id" value="">
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <?php esc_html_e('Create Bundle', 'flexiedu-lms-courses'); ?>
                                                    </button>
                                                    <a href="#" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                        <?php esc_html_e('Cancel', 'flexiedu-lms-courses'); ?>
                                                    </a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                            
                        </div>
                    </div>
            </div>
        </div>
    </div>
