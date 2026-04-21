<?php 

$organizationID = $organization->ID;

$havePermission = get_post_meta($organizationID, 'pro_panel_oc', true);

//var_dump($havePermission);

if(!$havePermission){
    echo '<div class="alert alert-warning" role="alert">
    ' . esc_html__('Your organization does not have permission to access this feature. Please contact the administrator.', 'flexiedu-lms-courses') . '
  </div>';
    return;
}


$page_id = get_field('pro_panel_page', 'option') ?? false; // page where you added ProPanel block

if ($page_id) {
    echo apply_filters('the_content', get_post_field('post_content', $page_id));
} else {
    echo '<p>' . esc_html__('Pro Panel page not set. Please set it in the options.', 'flexiedu-lms-courses') . '</p>';
}