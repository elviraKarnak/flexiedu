<div  class="col-xl-4 col-md-6">
        <div class="course-card">
                <a href="<?php the_permalink(); ?>">
                    <?php if(has_post_thumbnail()){?>
                    <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" class="course-image" width="500"
                    height="300">
                    <?php } else { ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/feature1.jpg" alt="Health & Safety Online Training" class="course-image" width="500"
                                    height="300">
                    <?php } ?> 
                    </a>
                    <div class="course-content">
                    <div>
                    <a href="<?php the_permalink(); ?>"> <h3 class="course-title"><?php the_title(); ?></h3></a>
                        <p class="course-description"><?php //echo wp_trim_words(get_the_excerpt(), 15, '...' ); ?>
                        <?php echo get_the_excerpt(); ?>
                        </p>
                    </div>

                    <div class="course-footer">

                        <?php
                        global $product;

                        // Safety fallback
                        if ( ! $product && get_the_ID() ) {
                            $product = wc_get_product( get_the_ID() );
                        }
                        ?>

                        <?php if ( $product ) : ?>

                            <!-- PRICE -->
                            <p>
                                <span class="course-price">
                                    <?php echo $product->get_price_html(); ?>
                                </span>
                            </p>

                            <!-- ADD TO CART -->
                            <div class="course_buttons">
                                <?php
                                // woocommerce_template_loop_add_to_cart([
                                //     'product' => $product,
                                // ]);
                                ?>
                                <a href="<?php the_permalink(); ?>" class="view-course-btn button product_type_course" aria-label="Read more about “<?php the_title(); ?>”" rel="nofollow" role="button">Enroll Now</a>
                            </div>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>