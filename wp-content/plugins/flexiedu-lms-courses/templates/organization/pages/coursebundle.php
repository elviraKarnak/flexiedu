<?php
if (!defined('ABSPATH')) {
    exit;
}

$user = wp_get_current_user();

$organizationID        = isset($organization->ID) ? (int) $organization->ID : 0;
$organization_slug     = isset($organization->post_name) ? $organization->post_name : '';
$organization_author_id = isset($organization->post_author) ? (int) $organization->post_author : 0;
$user_id               = get_current_user_id();
$bundle_id             = 0;

if (isset($_GET['bundle_id'])) {
    $bundle_id = absint(wp_unslash($_GET['bundle_id']));



global $wpdb;

$bundle     = null;
$course_ids = [];
$query      = null;

if (!empty($bundle_id) && !empty($organization_slug)) {
    $table_name = $wpdb->prefix . 'course_bundles';

    $bundle = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} WHERE id = %d AND org_slug = %s",
            $bundle_id,
            $organization_slug
        )
    );
}

if (!empty($bundle) && (int) $bundle->created_by !== $organization_author_id) {
    $bundle = null;
}

if (!empty($bundle)) {
    $course_ids = json_decode($bundle->course_ids, true);
    $course_ids = is_array($course_ids)
        ? array_values(array_filter(array_map('absint', $course_ids)))
        : [];
}

if (!empty($course_ids)) {
    $query = new WP_Query([
        'post_type'      => 'sfwd-courses',
        'post_status'    => 'publish',
        'post__in'       => $course_ids,
        'orderby'        => 'post__in',
        'posts_per_page' => -1,
    ]);
}
?>

<style>
    .course-details .ld-profile-summary {
        display: none;
    }
</style>

<section class="course-details text-center w-100">
    <div class="text-center">
        <div id="course-admin" class="ld-course-list-content">
            <?php if (empty($bundle)) : ?>
                <p><?php esc_html_e('Course bundle not found.', 'flexiedu-lms-courses'); ?></p>
            <?php elseif (empty($course_ids)) : ?>
                <p><?php esc_html_e('No courses found in this bundle.', 'flexiedu-lms-courses'); ?></p>
            <?php elseif (!empty($query) && $query->have_posts()) : ?>
                <h3 class="mb-4"><?php echo esc_html($bundle->title); ?></h3>

                <div class="ld-course-list-items row">
                    <?php
                    while ($query->have_posts()) :
                        $query->the_post();

                        $course_id = get_the_ID();
                        
                        $isRequiredCourse = !sfwd_lms_has_access(get_the_ID(), $user_id);

                        $percent = 0;
                        $steps   = 0;
                        $total   = 0;

                        if (function_exists('learndash_user_get_course_progress')) {
                            $progress = learndash_user_get_course_progress($user_id, $course_id);
                            //var_dump($progress );
                            $completed = (int) ($progress['completed'] ?? 0);
                            $total     = (int) ($progress['total'] ?? 0);
                            $percent = $total ? round(($completed / $total) * 100) : 0;
                        }

                        $layout_path = FlexiEdu_Courses_PATH . 'templates/organization/layout/';

                        include $layout_path . 'course-content.php';
                    endwhile;
                    ?>
                </div>

                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p><?php esc_html_e('No courses found in this bundle.', 'flexiedu-lms-courses'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php } else { ?>

<section class="course-details text-center w-100">
    <div class="text-center">
        <div id="course-admin" class="ld-course-list-content">
            <p><?php esc_html_e('No course bundle selected.', 'flexiedu-lms-courses'); ?></p>
    </div>
</section>

<?php } ?>