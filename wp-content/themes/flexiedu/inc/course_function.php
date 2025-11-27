<?php 

function flexiedu_filter_enqueue_scripts() {
    wp_enqueue_script('flexiedu_filter', get_template_directory_uri() . '/assets/js/course-filter.js', array('jquery'), time(), true);

    wp_localize_script('flexiedu_filter', 'flexiedu_filter_object', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('filter_courses_nonce')
    ));
}

add_action('load_filter_scripts', 'flexiedu_filter_enqueue_scripts');


add_action('wp_ajax_filter_courses', 'filter_courses_cb');
add_action('wp_ajax_nopriv_filter_courses', 'filter_courses_cb');

function filter_courses_cb() {

    
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'filter_courses_nonce') ) {
        wp_send_json_error('Invalid nonce');
        wp_die();
    }

    $categories = $_POST['categories'] ?? [];
    $levels     = $_POST['levels'] ?? [];
    $min_price  = intval($_POST['min_price'] ?? 0);
    $max_price  = intval($_POST['max_price'] ?? 999999);
    $search     = sanitize_text_field($_POST['search'] ?? '');

    $postPerPage = 9;

    $paged = (isset($_POST['page']) && $_POST['page'] > 0) ? intval($_POST['page']) : 1;
    
    $args = [
        'post_type' => 'courses',
        'posts_per_page' => $postPerPage,
        'paged' => $paged,
        's' => $search,
        'meta_query' => [
            [
                'key' => 'price_course',
                'value' => [$min_price, $max_price],
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ]
        ],
        'tax_query' => [
            'relation' => 'AND'
        ]
    ];

    if (!empty($categories)) {
        $args['tax_query'][] = [
            'taxonomy' => 'c-cat',
            'field' => 'term_id',
            'terms' => $categories
        ];
    }

    if (!empty($levels)) {
        $args['tax_query'][] = [
            'taxonomy' => 'c-level',
            'field' => 'term_id',
            'terms' => $levels
        ];
    }

    $courses = new WP_Query($args);
     ob_start();
   if ($courses->have_posts()) {
       
        $total_posts = $courses->found_posts;
        if($total_posts <= $postPerPage ){
            $postPerPage = $total_posts;
        }
    
        ?>


       <?php if($paged < 2 ){?>

       <div class="filter-bar d-flex justify-content-between align-items-center">
            <div class="total-result">Showing 1-<?php echo $postPerPage; ?> of <?php echo $total_posts; ?> Results</div>
            <!-- <div class="select-sc d-flex align-items-center">
              <label for="">Sort by:</label>
              <select name="" id="course-sort">
                <option value="d">Dafault</option>
              </select>
            </div> -->
          </div>
          <?php } ?>

            <div  class="row gy-5">

            
                <?php while ($courses->have_posts()) : $courses->the_post(); 
                        
                        $price_course = get_field('price_course');
                        $button_course = get_field('button_course');
                    ?>

                <div class="col-xl-4 col-md-6">
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
                        <input type="hidden" name="<?php echo $total_posts; ?>" value="<?php echo $postPerPage; ?>">
                        <div class="load-more-sc text-center mt-4">

                       <button id="load-more" class="load-more" 
                        data-page="<?php echo $paged; ?>"
                        data-perpage="<?php echo $postPerPage; ?>"
                        data-total="<?php echo $total_posts; ?>"
                        data-page="1">Load More</button>
         
                        </div>
                    <?php } ?>


        </div>

            
    <?php } else {?>
    
    <div class="no-course-found">
        <p>Looks like no courses match your current options. Try selecting different ones.</p>
    </div>

    <?php } 
    wp_reset_postdata();
    echo ob_get_clean();
    wp_die();
}


