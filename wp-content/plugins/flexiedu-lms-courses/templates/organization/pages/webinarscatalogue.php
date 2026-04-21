<?php

    $current_user_id = get_current_user_id();

    // Organization
    $organizationID = $organization->ID;
    $organization_author_id = $organization->post_author;

    // LearnDash check
    $is_group_leader = function_exists('learndash_is_group_leader_user') 
        ? learndash_is_group_leader_user($current_user_id) 
        : false;

    // Tax filter (change dynamically if needed)
    $term_slug = 'workshops-webinars-catalogue';

    $args = [
        'post_type'      => 'document',
        'posts_per_page' => -1,
        'tax_query'      => [
            [
                'taxonomy' => 'document-type',
                'field'    => 'slug',
                'terms'    => $term_slug
            ]
        ]
    ];

    $query = new WP_Query($args); ?>

    <div class="row">

            <?php if ($query->have_posts()) : ?>

            <?php while ($query->have_posts()) : $query->the_post(); 

                $file_id  = get_post_meta(get_the_ID(), '_document_file', true);
                $file_url = $file_id ? wp_get_attachment_url($file_id) : '#';

                $layout_path = FlexiEdu_Courses_PATH . 'templates/organization/layout/';

                 include $layout_path . 'document-content.php';
            ?>

          

        <?php endwhile; wp_reset_postdata(); ?>

        <?php else : ?>

        <p><?php esc_html_e('No documents found.', 'flexiedu-lms-courses'); ?></p>

        <?php endif; ?>

    </div>
