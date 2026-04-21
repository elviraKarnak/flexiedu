<?php 
        //Restrict access
        if (!current_user_can('manage_options')) {
            return '<p>' . esc_html__('Access Denied', 'flexiedu-lms-courses') . '</p>';
        }

 ob_start(); ?>


<?php echo ob_get_clean();
