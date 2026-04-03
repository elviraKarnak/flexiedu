<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('FlexiEdu_Courses_DB')) {

    class FlexiEdu_Courses_DB {

        public static function install() {

            global $wpdb;

            require_once ABSPATH . 'wp-admin/includes/upgrade.php';

            $charset_collate = $wpdb->get_charset_collate();

            /*
            ==========================================
            ENROLLMENT REQUEST TABLE
            ==========================================
            */

            $table_name = $wpdb->prefix . 'flexi_enrollment_requests';

            $sql = "CREATE TABLE $table_name (

                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                course_id BIGINT UNSIGNED NOT NULL,
                organization_id BIGINT UNSIGNED NOT NULL,

                status VARCHAR(20) DEFAULT 'pending',


                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                reapply_after DATETIME DEFAULT NULL,

                PRIMARY KEY (id),

                UNIQUE KEY user_course (user_id, course_id),

                KEY user_id (user_id),
                KEY course_id (course_id),
                KEY organization_id (organization_id),
                KEY status (status)

            ) $charset_collate;";

            dbDelta($sql);
        }
    }
}