<?php 
/**
* Template Name: Page: Home
**/
get_header(); 



?>

<?php if(have_rows('banner_slider_hb')): ?>

  <section class="home-banner position-relative d-flex align-items-end justify-content-center video_slider">
    <div class=" banner_slider">
      <?php while (have_rows('banner_slider_hb')): the_row(); 
      
          $sub_title_hb = get_sub_field('sub_title_bs_sin');
          $title_hb = get_sub_field('title_bs_sin');
          $button_hb = get_sub_field('button_hb_sin');
          $video_url_hb = get_sub_field('video_url_hb');
          $video_cover_hb = get_sub_field('video_cover_hb_sin');
                
      
      ?>
          <div class="item">
            <div class="banner-or-vd-sc">
              <!-- <img src="<?php //echo get_template_directory_uri(); ?>/assets/images/banner.jpg" alt=""> -->
              <video autoplay playsinline muted disablePictureInPicture poster="<?php echo $video_cover_hb['url'];?>">
                <source src="<?php echo $video_url_hb;?>"
                  type="video/mp4">
                Your browser does not support the video tag.
              </video>
            </div>
               <div class="container">
              <?php if($sub_title_hb){ ?>
                <div class="small-title text-white">
                  <?php echo $sub_title_hb; ?>
                </div>
              <?php } ?>
              <?php if($title_hb){ ?>
              <h1 class="text-white"><?php echo $title_hb; ?></h1>
              <?php } ?>

              <?php 

                if($button_hb) {
                    $link_url = $button_hb['url'];
                    $link_title = $button_hb['title'];
                    $link_target = $button_hb['target'] ? $button_hb['target'] : '_self';
                    ?>

                      <a href="<?php echo $link_url; ?>" class="btn btn-custom white-btn " <?php echo $link_target; ?>>
                        <span> <?php echo $link_title; ?></span>
                      </a>

              <?php } ?>

            </div>
          </div>
      <?php endwhile; ?>
    </div>
    </section>
<?php endif; ?>


<?php 

  $sub_title_wu = get_field('sub_title_wu');
  $title_wu = get_field('title_wu');
  $description_wu = get_field('decription_wu');


?>

<?php if(have_rows('info_section_wc')){ ?>

  <section class="why-section not-in-our-service space-mr">
    <div class="container">
      <div class="row justify-content-between align-items-center">
        <div class="col-md-6">
          <?php if($sub_title_wu){ ?>
            <div class="top-title"><?php echo $sub_title_wu; ?></div>
          <?php } ?>
          <?php if($title_wu){ ?>
            <h2 class="sc-hd mb-0"><?php echo $title_wu; ?></h2>
          <?php } ?>
        </div>
        <?php if($description_wu){ ?>
          <div class="col-xl-5 col-md-6">
            <p class="mb-0"><?php echo $description_wu; ?></p>
          </div>
        <?php } ?>
      </div>


      <div class="row gy-5 btm-row">

        <?php $i=1; 

           while (have_rows('info_section_wc')) {
            the_row();
            $icon_sin_wc = get_sub_field('icon_sin_wc');
            $title_sin_wc = get_sub_field('title_sin_wc');
            $description_sin_wc = get_sub_field('description_sin_wc');
          
          ?>

          <div class="col-md-4 col-wp">
            <div class="feature-box">
              <?php if($icon_sin_wc) { ?>
                  <div class="feature-icon">
                  <img src="<?php echo $icon_sin_wc['url']; ?>" alt="<?php echo $icon_sin_wc['alt']; ?>" title="<?php echo $icon_sin_wc ['title']; ?>"> 
                  </div>
              <?php } ?>
               <?php if($title_sin_wc) { ?>
                <h2><?php echo $title_sin_wc; ?></h2>
              <?php } ?>
              <?php if($description_sin_wc) { ?>
                <p><?php echo $description_sin_wc; ?></p>
              <?php } ?>
              <span class="feature-number">0<?php echo $i; ?></span>
            </div>
          </div>
   
        <?php $i++; } ?>

      </div>
    </div>
  </section>
  <?php } ?>

  <?php  

      $postPerPage = 10;
      $tax_args   = array( 'relation' => 'AND' );
      $meta_args  = array('relation' => 'AND');
       $courses = get_field('courses_hc');

     $args = array(
             
              'posts_per_page'   => $postPerPage,
              'post_type' => 'courses',
              'post_status' => 'publish',
              'post__in' => $courses,
              // 'meta_query' => $meta_args,
              // 'tax_query' => $tax_args,
              'order'   => 'ASC',
      );

      $courses = new WP_Query( $args );

      
  

      if($courses->have_posts()){
        
        $sub_title_hc = get_field('sub_title_hc');
        $title_hc = get_field('title_hc');
       
        $cta_button_hc = get_field('cta_button_hc');
        
        
        ?>

  <div class="featured-courses-section space-pd">
    <div class="container">
      <div class="section-header">
        <?php if($sub_title_hc){ ?>
          <div class="top-title"><?php echo $sub_title_hc; ?></div>
        <?php } ?>
        <?php if($title_hc){ ?>
          <h2 class="sc-hd"><?php echo $title_hc; ?></h2>
        <?php } ?>
      </div>

      <div class="owl-carousel owl-theme courses-carousel">

        <?php while ($courses->have_posts()){ 
          $courses->the_post(); 
          
          $price_course = get_field('price_course');
          $button_course = get_field('button_course');
          ?>
    
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
               <a href="<?php the_permalink(); ?>"><h3 class="course-title"><?php the_title(); ?></h3></a> 
                <p class="course-description"><?php echo wp_trim_words(get_the_excerpt(), 15, '...' ); ?>
                </p>
              </div>

               <div class="course-footer ">
                            <?php if($price_course){
                            $currency_symbol_flexiedu = get_field('currency_symbol_flexiedu', 'option');
                            ?>
                            <p><?php echo get_field('approved_by_sin');?>
                            <span class="course-price"><?php echo  $currency_symbol_flexiedu.$price_course; ?></span>
                            </p>
                            <?php } 


                              $buyNow = get_field('buy_now_url');
                              $tryNow = get_field('trial_url_sin');

                              $courseLogin = get_field('course_login_flexedu', 'option');
                              

                            if($button_course) {
                                $link_url = $button_course['url'];
                                $link_title = $button_course['title'];
                                $link_target = $button_course['target'] ? $button_course['target'] : '_self';

                                $link_target = '_self';
                                $link_url = get_the_permalink();
                                $link_title = "View Course";
                            ?>
                                <!-- <a href="<?php echo $link_url;  ?>" class="view-course-btn" <?php echo  $link_target; ?>>
                                <?php echo $link_title;  ?>
                                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow2.svg" alt="arrow">
                                </a> -->
                                <?php } 
                                
                                $link_target = '_blank';
                                
                                ?>
                               <div class="course_buttons">
                                <?php if($tryNow){ ?>
                                    <a href="<?php echo $tryNow;  ?>" class="view-course-btn" <?php echo  $link_target; ?>>
                                        <?php echo __("Free Trial", 'flexiedu'); ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow2.svg" alt="arrow">
                                    </a>
                                <?php } ?> 

                                <?php if($buyNow){ ?>
                                    <a href="<?php echo $buyNow;  ?>" class="view-course-btn" <?php echo  $link_target; ?>>
                                        <?php echo __("Buy Now", 'flexiedu'); ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow2.svg" alt="arrow">
                                    </a>
                                <?php } ?> 

                                <?php if($courseLogin){ ?>
                                    <a href="<?php echo $courseLogin;  ?>" class="view-course-btn" <?php echo  $link_target; ?>>
                                        <?php echo __("User Login", 'flexiedu'); ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arrow2.svg" alt="arrow">
                                    </a>
                                <?php } ?>
                                </div> 
                                 
                        </div>
            </div>
          </div>
        <?php } ?>
      </div>

        <?php if($cta_button_hc) {

            $link_url = $cta_button_hc['url'];
            $link_title = $cta_button_hc['title'];
            $link_target = $cta_button_hc['target'] ? $cta_button_hc['target'] : '_self';

          ?>
          <a href="<?php echo $link_url;  ?>"  class="btn-custom btn" <?php echo  $link_target; ?>><span><?php echo $link_title;  ?></span></a>
      <?php } ?>
    </div>
  </div>
   
  <?php 

    wp_reset_postdata();
    
  } ?>

  <?php
    $sub_title_ha = get_field('sub_title_ha');
    $title_ha = get_field('title_ha');
    $description_ha = get_field('description_ha');
    $cta_button_ha = get_field('cta_button_ha');
    $section_image_ha = get_field('section_image_ha');
  
  ?>

  <?php if($description_ha){ ?>

    <section class="about-us space-mr">
      <div class="container">
        <div class="row align-items-center gy-lg-0 gy-4">
          <div class="col-lg-6 lft-img">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/about.jpg" alt="">
          </div>
          <div class="col-lg-6">
            <?php if($sub_title_ha){ ?>
              <div class="top-title">
                <?php echo $sub_title_ha; ?>
              </div>
            <?php } ?>
            <?php if($title_ha){ ?>
              <h2 class="sc-hd"><?php echo $title_ha; ?></h2>
            <?php } ?>
            <?php echo $description_ha; ?>

            <?php if($cta_button_ha) {

              $link_url = $cta_button_ha['url'];
              $link_title = $cta_button_ha['title'];
              $link_target = $cta_button_ha['target'] ? $cta_button_ha['target'] : '_self';

            ?>
              <a href="<?php echo $link_url;  ?>"  class="btn btn-custom" <?php echo  $link_target; ?>><span><?php echo $link_title;  ?></span></a>
            <?php } ?>
          </div>
        </div>
      </div>
    </section>

  <?php } ?>

  <?php 
      $title_ht = get_field('title_ht');
      $company_logo_ht = get_field('company_logo_ht');
  
  ?>

 <?php if($company_logo_ht) { 
  
  
  ?>

  <section class="mq-wp space-mr">

    <?php if($title_ht){ ?>
      <div class="container">
        <h4 class="text-center"><?php echo $title_ht; ?></h4>
      </div>
    <?php } ?>

    <div class="marquee-container">
      <div class="marquee">
        <div class="marquee__group">
          <?php foreach($company_logo_ht as $galimg){ ?>
              <div class="logo-slider-item gsap-logo-item">
                <div class="logo-slider-in">
                  <img src="<?php echo  $galimg['url']; ?>" alt="<?php echo  $galimg['alt']; ?>" title="<?php echo  $galimg['title']; ?>">
                </div>
              </div>
          <?php } ?>
        </div>
        <div class="marquee__group">

          <?php foreach($company_logo_ht as $galimg){ ?>
              <div class="logo-slider-item gsap-logo-item">
                <div class="logo-slider-in">
                  <img src="<?php echo  $galimg['url']; ?>" alt="<?php echo  $galimg['alt']; ?>" title="<?php echo  $galimg['title']; ?>">
                </div>
              </div>
          <?php } ?>

        </div>
      </div>
    </div>
  </section>

  <?php } ?>

  <?php if(have_rows('video_banner_slider')): ?>

  <section class="home-banner home-video2-sc position-relative d-flex align-items-end justify-content-center video_slider">
    <div class="owl-carousel owl-theme banner_slider">
      <?php while (have_rows('video_banner_slider')): the_row(); 
      
          $video_banner_title_ht = get_sub_field('title_bs_sin');
          $video_banner_description_ht = get_sub_field('sub_title_bs_sin');
          $video_banner_cta_ht = get_sub_field('button_hb_sin');
          $video_url_hb = get_sub_field('video_url_hb');
          $video_cover_hb = get_sub_field('video_cover_hb_sin');
                
      
      ?>
          <div class="item">
            <div class="banner-or-vd-sc">
              <!-- <img src="<?php //echo get_template_directory_uri(); ?>/assets/images/banner.jpg" alt=""> -->
              <video autoplay playsinline muted disablePictureInPicture poster="<?php echo $video_cover_hb['url'];?>">
                <source src="<?php echo $video_url_hb;?>"
                  type="video/mp4">
                Your browser does not support the video tag.
              </video>
            </div>
              <div class="container">
              <?php if($video_banner_title_ht){ ?>
                <h2 class="text-white"><?php echo $video_banner_title_ht; ?></h2>
              <?php } ?>
              <?php if($video_banner_description_ht){ ?>
              <p class="text-white mt-4 mb-4"><?php echo $video_banner_description_ht; ?></p>
              <?php } ?>
              <?php 

                if($video_banner_cta_ht) {
                    $link_url = $video_banner_cta_ht['url'];
                    $link_title = $video_banner_cta_ht['title'];
                    $link_target = $video_banner_cta_ht['target'] ? $video_banner_cta_ht['target'] : '_self';
                    ?>

                      <a href="<?php echo $link_url; ?>" class="btn btn-custom white-btn " <?php echo $link_target; ?>>
                        <span> <?php echo $link_title; ?></span>
                      </a>

              <?php } ?>

            </div>
        </div>
    
      <?php endwhile; ?>
    </div>
    </section>
<?php endif; ?>


  <?php 


    $video_banner_title_ht = get_field('video_banner_title_ht');
    $video_banner_description_ht = get_field('video_banner_description_ht');
    $video_banner_cta_ht = get_field('video_banner_cta_ht');
    
    $video_banner_url_ht = get_field('video_banner_url_ht');
    $video_banner_cover_ht = get_field('video_banner_cover_ht');
  
  ?>

 

  <?php 

    $sub_title_hs = get_field('sub_title_hs');
    $title_hts = get_field('title_hts');
    $description_hts = get_field('description_hts');
  
  ?>


<?php if(have_rows('service_info_section_hs')){ ?>

  <section class="why-section our-services space-mr">
    <div class="container">
      <div class="row justify-content-between align-items-center">
        <div class="col-md-6">
          <?php if($sub_title_hs){ ?>
            <div class="top-title"><?php echo $sub_title_hs; ?></div>
          <?php } ?>

          <?php if($title_hts){ ?>
            <h2 class="sc-hd mb-0"><?php echo $title_hts; ?></h2>
          <?php } ?>
        </div>

        <?php if( $description_hts)?>
        <div class="col-lg-5 col-md-6">
          <p class="mb-0"><?php echo $description_hts; ?></p>
        </div>
      </div>

          <div class="row gy-5 btm-row">

            <?php $i=1; 

              while (have_rows('service_info_section_hs')) {
                the_row();
                $icon_sin_wc = get_sub_field('icon_sin_wc');
                $title_sin_wc = get_sub_field('title_sin_wc');
                $description_sin_wc = get_sub_field('description_sin_wc');
              
              ?>

              <div class="col-lg-4 col-md-6 col-wp">
                <div class="feature-box green h-100 ">
                  <?php if($icon_sin_wc) { ?>
                      <div class="feature-icon">
                      <img src="<?php echo $icon_sin_wc['url']; ?>" alt="<?php echo $icon_sin_wc['alt']; ?>" title="<?php echo $icon_sin_wc ['title']; ?>"> 
                      </div>
                  <?php } ?>
                  <?php if($title_sin_wc) { ?>
                    <h2><?php echo $title_sin_wc; ?></h2>
                  <?php } ?>
                  <?php if($description_sin_wc) { ?>
                    <p><?php echo $description_sin_wc; ?></p>
                  <?php } ?>
                </div>
              </div>
      
            <?php $i++; } ?>

          </div>
      </div>
    </div>
  </section>
<?php } ?>

<?php get_template_part('template-parts/testimonials')?>


<script>
  jQuery(document).ready(function($){
    
    const marquee = document.querySelector('.marquee');
    const content = marquee.querySelector('.marquee-content');
    // Duplicate the entire content once for seamless looping
   // marquee.appendChild(content.cloneNode(true));

  });
</script>

<?php get_footer(); ?>