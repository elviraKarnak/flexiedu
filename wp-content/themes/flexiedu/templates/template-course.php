<?php 
/**
* Template Name: Page: Course Listing
**/
get_header();     

do_action('load_filter_scripts');

    $dummyImg = get_template_directory_uri().'/assets/images/inner-banner.jpg';

    if(has_post_thumbnail()){
        $bannerImg = get_the_post_thumbnail_url();
    }else{
         $bannerImg = $dummyImg;
    }

    $banner_title_os = get_field('banner_title_cl');

?>



    <section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
        <?php if($banner_title_os){ ?>
        <div class="container">
            <h1><?php echo $banner_title_os; ?></h1>
        </div>
        <?php } ?>
    </section>



  <div class="course-listing-sc space-mr">
    <div class="container">
      <div class="d-lg-flex course-listing-row">
        <div class="lft-sc">

        <button class="course-list-filter"><img src="<?php echo get_template_directory_uri(); ?>'/assets/images/filter.png';" alt="">Filter</button>
          <form id="course-filters">
          <div class="filter-sidebar">
            <!-- Search -->
                <div class="filter-section">
                <div class="filter-header">
                    <h3>Search</h3>
                    <span class="toggle-icon"><img src="<?php echo get_theme_file_uri(); ?>/assets/images/down.svg" alt=""></span>
                </div>
                <div class="filter-content">
                    <input type="text" class="filter-search" placeholder="Search Course" />
                </div>
                </div>

            <!-- Categories -->
            <div class="filter-section">
                <div class="filter-header">
                    <h3>Categories</h3>
                    <span class="toggle-icon">
                    <img src="<?php echo get_theme_file_uri(); ?>/assets/images/down.svg" alt="">
                    </span>
                </div>

                <div class="filter-content">
                    <?php
                    $terms = get_terms(array(
                        'taxonomy'   => 'c-cat',
                        'hide_empty' => false
                    ));

                    if (!empty($terms) && !is_wp_error($terms)) :
                        foreach ($terms as $term) :
                    ?>
                        <label>
                            <input type="checkbox" value="<?php echo esc_attr($term->term_id); ?> " name="course-categories" />
                            <?php echo esc_html($term->name); ?>
                            <span>(<?php echo intval($term->count); ?>)</span>
                        </label>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>


            <!-- Level -->
            <div class="filter-section">
              <div class="filter-header">
                <h3>Level</h3>
                <span class="toggle-icon"><img src="<?php echo get_theme_file_uri(); ?>/assets/images/down.svg" alt=""></span>
              </div>
              <div class="filter-content">
                    <?php
                    $terms = get_terms(array(
                        'taxonomy'   => 'c-level',
                        'hide_empty' => false
                    ));

                    if (!empty($terms) && !is_wp_error($terms)) :
                        foreach ($terms as $term) :
                    ?>
                        <label>
                            <input type="checkbox" value="<?php echo esc_attr($term->term_id); ?> " name="course-levels" />
                            <?php echo esc_html($term->name); ?>
                            <span>(<?php echo intval($term->count); ?>)</span>
                        </label>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>

            <!-- Price -->
            <div class="filter-section">
              <div class="filter-header">
                <h3>Price</h3>
                <span class="toggle-icon"><img src="<?php echo get_theme_file_uri(); ?>/assets/images/down.svg" alt=""></span>
              </div>
              <div class="filter-content">
                <div class="price-range">
                  <label>Min <input type="number" id="minPrice" value="10" /></label>
                  <span>-</span>
                  <label>Max <input type="number" id="maxPrice" value="100" /></label>
                </div>
                <div id="priceSlider"></div>
              </div>
            </div>
          </form>
          </div>
        </div>
        <div class="rgt-sc">

        <div id="loader_coursefilter"><span class="spinner"></span></div>

        <?php

            $postPerPage = 9;
            $tax_args   = array( 'relation' => 'AND' );
            $meta_args  = array('relation' => 'AND');

            $paged = (isset($_POST['page']) && $_POST['page'] > 0) ? intval($_POST['page']) : 1;

            $args = array(
                    
                    'posts_per_page'   => $postPerPage,
                    'post_type' => 'courses',
                    'post_status' => 'publish',
                    'paged' => $paged,
                    // 'meta_query' => $meta_args,
                    // 'tax_query' => $tax_args,
                    'order'   => 'DESC',
            );

            $courses = new WP_Query( $args );
            $total_posts = $courses->found_posts;
            ?>
      

         <div id="course-results">
          <div class="filter-bar d-flex justify-content-between align-items-center">
            <div class="total-result">Showing 1-<?php echo $postPerPage; ?> of <?php echo $total_posts; ?> Results</div>
            <!-- <div class="select-sc d-flex align-items-center">
              <label for="">Sort by:</label>
              <select name="" id="">

                <option value="">Dafault</option>

              </select>
            </div> -->
          </div>
          <div  class="row gy-5">

            <?php while ($courses->have_posts()) : $courses->the_post(); 
                
                $price_course = get_field('price_course');
                $button_course = get_field('button_course');
            ?>
                <div  class="col-xl-4 col-md-6">

            <div class="course-card">
                <?php if(has_post_thumbnail()){?>
                <img src="<?php echo get_the_post_thumbnail_url(); ?>" alt="<?php the_title(); ?>" class="course-image" width="500"
                height="300">
                <?php } else { ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/feature1.jpg" alt="Health & Safety Online Training" class="course-image" width="500"
                                height="300">
                <?php } ?> 
                <div class="course-content">
                <div>
                    <h3 class="course-title"><?php the_title(); ?></h3>
                    <p class="course-description"><?php echo wp_trim_words(get_the_excerpt(), 15, '...' ); ?>
                    </p>
                </div>

                <div class="course-footer">
                    <?php if($price_course){
                    $currency_symbol_flexiedu = get_field('currency_symbol_flexiedu', 'option');
                    ?>
                    <span class="course-price"><?php echo  $currency_symbol_flexiedu.$price_course; ?></span>
                    <?php } 

                    if($button_course) {
                        $link_url = $button_course['url'];
                        $link_title = $button_course['title'];
                        $link_target = $button_course['target'] ? $button_course['target'] : '_self';

                        $link_target = '_self';
                        $link_url = get_the_permalink();
                        $link_title = "View Course";

                    ?>
                        <a href="<?php echo $link_url;  ?>" class="view-course-btn" <?php echo  $link_target; ?>>
                        <?php echo $link_title;  ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow2.svg" alt="arrow">
                        </a>
                    <?php } ?>
                </div>
                </div>
            </div>
          </div>
            <?php endwhile; wp_reset_query(); ?>

          <?php  if($courses->max_num_pages > 1 && $paged < $courses->max_num_pages ){?>
            <div class="load-more-sc text-center mt-4">
              <button id="load-more" class="load-more" 
                data-page="<?php echo $paged; ?>"
                data-perpage="<?php echo $postPerPage; ?>"
                data-total="<?php echo $total_posts; ?>"
                data-page="1">Load More</button>
            </div>
          <?php } ?>


        </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>