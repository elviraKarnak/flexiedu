<style>
    .learner-sidebar-left .learndash-wrapper .ld-item-list.ld-course-list,
    .learner-sidebar-left .learndash-wrapper .ld-profile-stats {
        display: none !important;
}
</style>

<?php 

$organizationID = $organization->ID;  

$user = wp_get_current_user();
$user_id = $user->ID;


if ( current_user_can('administrator') ) {
    // Your code here
    $profileLink = home_url('lms-console/user');
}else{
    $profileLink = get_permalink($organizationID) . "profile";
}




// Get custom avatar
$img_id = get_user_meta($user_id, 'profile_picture', true);

?>

    <div class="ld-profile-card">
        
        <div class="ld-profile-avatar">
            
            <?php if ($img_id): ?>
                
                <?php echo wp_get_attachment_image($img_id, [150,150], false, [
                    'class' => 'avatar avatar-150 photo'
                ]); ?>

            <?php else: ?>

                <?php echo get_avatar($user_id, 150); ?>

            <?php endif; ?>

        </div>

        <h2 class="ld-profile-heading">
            <?php echo esc_html($user->display_name); ?>
        </h2>

        <a class="ld-profile-edit-link"
            href="<?php echo esc_url($profileLink); ?>">
            <?php _e('Edit profile', 'flexiedu-lms-courses'); ?>
        </a>

    </div>


    <?php echo do_shortcode('[flexiedu-live-classes]'); 

        

    ?>