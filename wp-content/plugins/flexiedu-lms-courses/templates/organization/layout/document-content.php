  <div class="col-md-4 mb-4 doc-card" data-id="<?php the_ID(); ?>">
                <div class="card">
                    <div class="card-body">

                    <h5 class="card-title"><?php the_title(); ?></h5>

                    <p class="card-text">
                        <?php echo wp_trim_words(get_the_content(), 15); ?>
                    </p>

                    <!-- Download -->
                    <a href="<?php echo esc_url($file_url); ?>" 
                        class="btn btn-primary btn-sm mb-2" 
                        target="_blank">
                        Download
                    </a>

                    <?php if ($is_group_leader && $current_user_id == $organization_author_id) : ?>

                        <!-- Edit -->
                      <a href="#" class="btn btn-warning btn-sm edit-doc" data-id="<?php the_ID(); ?>">Edit</a>

                        <!-- Delete -->
                        <button class="btn btn-danger btn-sm delete-doc" 
                                data-id="<?php the_ID(); ?>">
                        Delete
                        </button>

                    <?php endif; ?>

                    </div>
                </div>
            </div>