<div class="col-12 col-sm-6 col-lg-4 doc-card" data-id="<?php the_ID(); ?>">
    <div class="course-card">
        <div class="course-thumb">
            <a href="<?php echo esc_url($file_url); ?>" target="_blank">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('medium'); ?>
                <?php else : ?>
                    <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/doc-dummy.webp" alt="<?php echo esc_attr(get_the_title()); ?>" >
                <?php endif; ?>
            </a>
        </div>

        <div class="course-body">
            <div class="course-title"><?php the_title(); ?></div>
            <p class="card-text mb-3">
                <?php echo esc_html(wp_trim_words(get_the_content(), 15)); ?>
            </p>

            <a href="<?php echo esc_url($file_url); ?>" class="btn btn-primary btn-primary-transparent btn-sm" target="_blank">
                <?php esc_html_e('Download', 'flexiedu-lms-courses'); ?>
            </a>

            <?php if (($is_group_leader && $current_user_id == $organization_author_id)  || current_user_can('administrator')) : ?>
                <a href="#" class="btn btn-warning btn-warning-transparent btn-sm edit-doc" data-id="<?php the_ID(); ?>">
                    <?php esc_html_e('Edit', 'flexiedu-lms-courses'); ?>
                </a>

                <button class="btn btn-danger btn-danger-transparent btn-sm delete-doc" data-id="<?php the_ID(); ?>">
                    <?php esc_html_e('Delete', 'flexiedu-lms-courses'); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</div>