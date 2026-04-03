<?php 
        //Restrict access
        if (!current_user_can('manage_options')) {
            return '<p>Access Denied</p>';
        }

 ob_start(); ?>


<?php echo ob_get_clean();