<?php
if (!defined('ABSPATH')) {
    exit;
}

$organization_id        = $organization->ID;
$organization_author_id = $organization->post_author;
$current_user_id        = get_current_user_id();

$lms_group    = function_exists('get_field') ? get_field('groups', $organization_id) : null;
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


     $args = [
        'post_type'      => 'live-class',
        'posts_per_page' => -1,
        'author'         => $organization_author_id,
        'orderby'        => 'id',
        'order'          => 'DESC',
        
    ];

    $query = new WP_Query($args); ?>

    <div class="container-fluid py-4">

     <div class="d-flex align-items-center  justify-content-end mb-4">
                <?php if (in_array('organizer', (array) $user->roles, true) || in_array('administrator', (array) $user->roles, true)) : ?>
                        <a href="javascript:void(0);" class="btn btn-primary addlive-classmodal" data-bs-toggle="modal" data-bs-target="#addliveClassModal">
                        
                        <?php esc_html_e('+ Create Live Class', 'flexiedu-lms-courses'); ?>
                        </a>
                <?php endif; ?>
            </div>

       <?php if($query->have_posts()){ ?>
    
        <table id="liveClassesTable" class="table table-striped live-classes-table" >
            <thead>
                <tr>
                    <th scope="col"><?php _e('Sr','flexiedu-lms-courses' ); ?></th>
                    <th scope="col"><?php _e('Class Name','flexiedu-lms-courses' ); ?></th>
                    <th scope="col"><?php _e('Class Schedule','flexiedu-lms-courses' ); ?></th>
                    <th scope="col"><?php _e('Course Name','flexiedu-lms-courses' ); ?></th>
                    <th scope="col"><?php _e('Action','flexiedu-lms-courses' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                while($query->have_posts()){

                    $query->the_post();
                    
                    $className = get_the_title();

                    $webinarLink = get_post_meta(get_the_ID(), 'webinar_link', true);

                    $classStartDateTime = get_post_meta(get_the_ID(), 'start_datetime', true);
                    if($classStartDateTime && strtotime($classStartDateTime)){
                        $classStartDateTime = date('d-M-y g A', strtotime($classStartDateTime));
                    }

                    $classEndDateTime = get_post_meta(get_the_ID(), 'end_datetime', true);
                    if($classEndDateTime && strtotime($classEndDateTime)){
                        $classEndDateTime = date('d-M-y g A', strtotime($classEndDateTime));
                    }

                    $courseName = get_post_meta(get_the_ID(), 'course_id_lc', true);

                    ?>
                    <tr>
                    <td scope="row"><?php echo $i; ?></td>
                    <td scope="row"><?php echo $className; ?></td>
                    <td scope="row"><?php echo $classStartDateTime ." to ".$classEndDateTime; ?></td>
                    <td scope="row"><?php echo get_the_title($courseName); ?></td>
                    <td scope="row" class="action-btns">

                    <?php echo "<a href='" . esc_url($webinarLink) . "' target='_blank' class='btn btn-primary'>" . esc_html__('Join', 'flexiedu-lms-courses') . "</a>"; ?>
                  
                    <?php if (in_array('organizer', (array) $user->roles, true) || in_array('administrator', (array) $user->roles, true)) : ?>
                        <?php echo "<a href='javascript:void(0);' data-row-id='" . esc_attr(get_the_ID()) . "' class='btn btn-success edit-class'>" . esc_html__('Edit', 'flexiedu-lms-courses') . "</a>"; ?>
                        
                        <?php echo "<a href='javascript:void(0);' data-row-id='" . esc_attr(get_the_ID()) . "' class='btn btn-danger delete-class'>" . esc_html__('Delete', 'flexiedu-lms-courses') . "</a>"; ?>
                    <?php endif; ?>

                    </td>
                    </tr>

                <?php $i++; } ?>
            </tbody>
        </table>

    <?php } wp_reset_postdata(); ?>


    <div class="row justify-content-center">
        <div class="col-md-12">

        <div class="modal fade" id="addliveClassModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="addliveClassModalTitle">
                            <?php esc_html_e('Add Live Classes', 'flexiedu-lms-courses'); ?></h5>
                        
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                            <div class="card-body">
                                <div id="live-class-form-message" class="mb-3" style="display:none;"></div>

                                <form id="flexiedu-live-class-form" class="needs-validation" novalidate>

                                <input type="hidden" name="class_id" id="class_id">
                                    <div class="mb-4">
                                        <label for="live_class_title" class="form-label fw-semibold">
                                            <?php esc_html_e('Title', 'flexiedu-lms-courses'); ?>
                                        </label>
                                        <input
                                            type="text"
                                            class="form-control form-control-lg"
                                            id="live_class_title"
                                            name="title"
                                            placeholder="<?php esc_attr_e('Enter live class title', 'flexiedu-lms-courses'); ?>"
                                            required
                                        >
                                        <div class="invalid-feedback">
                                            <?php esc_html_e('Please enter a title.', 'flexiedu-lms-courses'); ?>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="live_class_description" class="form-label fw-semibold">
                                            <?php esc_html_e('Description', 'flexiedu-lms-courses'); ?>
                                        </label>
                                        <textarea
                                            class="form-control"
                                            id="live_class_description"
                                            name="description"
                                            rows="8"
                                            placeholder="<?php esc_attr_e('Write the live class description', 'flexiedu-lms-courses'); ?>"
                                            required
                                        ></textarea>
                                        <div class="invalid-feedback">
                                            <?php esc_html_e('Please enter a description.', 'flexiedu-lms-courses'); ?>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label for="live_class_start_date_time" class="form-label fw-semibold">
                                                <?php esc_html_e('Start Date & Time', 'flexiedu-lms-courses'); ?>
                                            </label>
                                            <input
                                                type="datetime-local"
                                                class="form-control"
                                                id="live_class_start_date_time"
                                                name="start_date_time"
                                                required
                                            >
                                            <div class="invalid-feedback">
                                                <?php esc_html_e('Please select the start date and time.', 'flexiedu-lms-courses'); ?>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="live_class_end_date_time" class="form-label fw-semibold">
                                                <?php esc_html_e('End Date & Time', 'flexiedu-lms-courses'); ?>
                                            </label>
                                            <input
                                                type="datetime-local"
                                                class="form-control"
                                                id="live_class_end_date_time"
                                                name="end_date_time"
                                                required
                                            >
                                            <div class="invalid-feedback">
                                                <?php esc_html_e('Please select the end date and time.', 'flexiedu-lms-courses'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 mb-4">
                                        <label for="live_class_webinar_link" class="form-label fw-semibold">
                                            <?php esc_html_e('Webinar Link', 'flexiedu-lms-courses'); ?>
                                        </label>
                                        <input
                                            type="url"
                                            class="form-control"
                                            id="live_class_webinar_link"
                                            name="webinar_link"
                                            placeholder="<?php esc_attr_e('https://example.com/webinar-link', 'flexiedu-lms-courses'); ?>"
                                            required
                                        >
                                        <div class="invalid-feedback">
                                            <?php esc_html_e('Please enter a valid webinar URL.', 'flexiedu-lms-courses'); ?>
                                        </div>
                                    </div>

                                    <div class="row g-4 align-items-end">
                                        <div class="col-md-4">
                                            <label for="live_class_event_color" class="form-label fw-semibold">
                                                <?php esc_html_e('Event Color', 'flexiedu-lms-courses'); ?>
                                            </label>
                                            <input
                                                type="color"
                                                class="form-control form-control-color p-0"
                                                id="live_class_event_color"
                                                name="event_color"
                                                value="#0d6efd"
                                                title="<?php esc_attr_e('Select event color', 'flexiedu-lms-courses'); ?>"
                                            >
                                        </div>

                                        <div class="col-md-8">
                                            <label for="live_class_course" class="form-label fw-semibold">
                                                <?php esc_html_e('Course', 'flexiedu-lms-courses'); ?>
                                            </label>
                                            <select
                                                class="form-select"
                                                id="live_class_course"
                                                name="course"
                                                required
                                            >
                                                <option value=""><?php esc_html_e('Select Course', 'flexiedu-lms-courses'); ?></option>
                                                <?php foreach ($courses as $course) : ?>
                                                    <option value="<?php echo esc_attr($course->ID); ?>">
                                                        <?php echo esc_html($course->post_title); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                <?php esc_html_e('Please select a course.', 'flexiedu-lms-courses'); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-2 text-end">
                                        <button type="submit" class="btn btn-primary live-course-edit btn-lg px-4">
                                            <?php esc_html_e('Create Live Class', 'flexiedu-lms-courses'); ?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>