<?php

get_header(); 

    if(have_posts()) : while (have_posts() ) : the_post(); 

    $banner_image_sin_course = get_field('banner_image_sin_course');

    $currency_symbol_flexiedu = get_field('currency_symbol_flexiedu', 'option');

    if(empty($banner_image_sin_course)){
        $banner_image_sin_course = get_template_directory_uri().'/assets/images/product-details-banner.jpg';
    }


?>


  <section class="inner-banner position-relative"
    style="background-image: url(<?php echo $banner_image_sin_course; ?>);">
    <div class="container">
      <h1><?php the_title(); ?></h1>
    </div>
  </section>

  <section class="course-details space-mr">
    <div class="container">
      <div class="row gy-lg-0 gy-4">
        <div class="col-lg-8">
          <section class="training-section">
            <div class="training-header">
              <h2><?php the_title(); ?></h2>
              <?php the_excerpt(); ?>
            </div>
            
          <?php if(have_rows('course_features')){ ?>
            <div class="training-features">
              <?php while (have_rows('course_features')){ 
                the_row(); 

                 $icon_sin_wc = get_sub_field('icon_sin');
                $title_sin_wc = get_sub_field('features_sin');
                
                
                ?>
                  <div class="feature-item">
                    <?php if($icon_sin_wc){ ?>
                      <img src="<?php echo $icon_sin_wc['url'];?>" alt="<?php echo $icon_sin_wc['alt'];?>">
                    <?php } ?>
                    <?php if($title_sin_wc)?>
                    <span><?php echo $title_sin_wc; ?></span>
                  </div>
             <?php } ?>
            </div>
          <?php } ?>

          <?php $title_tfcs = get_field('title_tfcs');
                $description_tfcs = get_field('description_tfcs'); 
                
              ?>

            <?php if($title_tfcs || $description_tfcs){ ?>

            <hr />
         

            <div class="training-info">
              <?php if($title_tfcs){ ?>
                <h3><?php echo $title_tfcs; ?></h3>
              <?php } ?>

              <?php if($description_tfcs){
                 echo $description_tfcs; 
                }?>
            </div>
            <?php } ?>

             <?php $title_wylcs = get_field('title_wylcs');
                $description_wylcs = get_field('description_wylcs'); 
                
              ?>

              <?php if($title_wylcs || $description_wylcs){ ?>
              <hr />

              
            <div class="training-learn">

              <?php if($title_wylcs){ ?>
                  <h3><?php echo $title_wylcs; ?></h3>
                <?php } ?>

                <?php if($description_wylcs){
                  echo $description_wylcs; 
                  }?>
            </div>
            <?php } ?>


            <?php 
            
            $title_cc_sin = get_field('title_cc_sin');
            $sub_title_ccsin = get_field('sub_title_ccsin');
            
            
            ?>

            <?php if(have_rows('faq_cc_sin') || $title_cc_sin || $sub_title_ccsin){ ?>
            <hr />
            <div class="training-learn course-curriculam">
              <?php if($title_cc_sin){ ?>
                <h3><?php echo $title_cc_sin; ?></h3>
              <?php } ?>
              <?php if($sub_title_ccsin){ ?>
             <div class="d-flex align-items-center cur-row">
              <?php echo $sub_title_ccsin; ?>
             
             </div>
             <?php } ?>
            </div>
            <?php if(have_rows('faq_cc_sin')){ ?>

            <div class="accordion faq-accordion" id="numberAccordion">


            <?php $i=1;  while (have_rows('faq_cc_sin')){ 
                the_row(); 

                 $title_ccin = get_sub_field('title_ccin');
                $description_ccin = get_sub_field('description_ccin');
                
                
                ?>
              <!-- Item 1 -->
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $i; ?>">
                    <span class="item-number">0<?php echo $i; ?>.</span>
                    <?php echo $title_ccin; ?>
                  </button>
                </h2>
                <div id="faq<?php echo $i; ?>" class="accordion-collapse collapse">
                  <div class="accordion-body">
                     <?php echo $description_ccin; ?>
                  </div>
                </div>
              </div>
          <?php $i++; } ?>
          
            </div>
            <?php } } ?>
          </section>


        </div>
        <div class="col-lg-4">
          <?php 
          
           if(has_post_thumbnail()){

           $courseImg = get_the_post_thumbnail_url();
           }else{
            $courseImg =cget_template_directory_uri()."/assets/images/feature1.jpg";
           }

           $video_url_csin = get_field('video_url_csin');
           
            $price_course = get_field('price_course');
            $button_course = get_field('button_course');
            $duration_sincourse = get_field('duration_sincourse');
            $prerequisites_coursesin = get_field('prerequisites_coursesin');
            $curriculum_cooursesin = get_field('curriculum_cooursesin');
            $language_coursesin = get_field('language_coursesin');

           

              // Main text values
              $level          = get_field('level_cst','option');
              $duration       = get_field('duration_cst','option');
              $prerequisites  = get_field('prerequisites_cst','option');
              $curriculum     = get_field('curriculum_cst','option');
              $language       = get_field('language_cst','option');

              // Icon images (ACF returns array)
              $level_icon           = get_field('level_icon_cst','option');
              $duration_icon        = get_field('duration_icon_cst','option');
              $prerequisites_icon   = get_field('prerequisites_icon_cst','option');
              $curriculum_icon     = get_field('curriculum_image_cst','option');
              $language_icon        = get_field('language_icon_cst','option');

              $assignedLevels = get_the_terms( get_the_ID(), 'c-level' );

              $levels = [];

              $play_icon_cst = get_field('play_icon_cst', 'option');

               if($assignedLevels && is_array($assignedLevels)){ 

                   foreach ( $assignedLevels as $term ) {
                        array_push($levels, $term->name);
                    }


                } ?>


                    
          
          <div class="course-card-rgt">
            <div class="course-image1">
              <img src="<?php echo $courseImg; ?>" alt="<?php the_title(); ?>" />
            
              <?php if($video_url_csin){ ?>
                <a class="play-button1" data-fancybox data-src="<?php echo $video_url_csin; ?>" >
                  <span><img src="<?php echo $play_icon_cst; ?>" alt=""></span>
                </a>
              <?php } ?>
              
            </div>


            <?php if($price_course ){ ?>
              <div class="course-price1"><?php echo $currency_symbol_flexiedu.$price_course; ?></div>
            <?php } ?>

            <?php if($button_course ){ ?>
            <a class="enroll-btn1" href="<?php echo $button_course['url'] ?>" target="<?php echo  $button_course['target']; ?>">
              <?php echo $button_course['title'] ?> <span class="arrow"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/white-arrow.svg" alt=""></span>
            </a>
          <?php } ?>

          <?php if($levels  || $duration_sincourse || $prerequisites_coursesin || $curriculum_cooursesin || $language_coursesin || $language_coursesin){ ?>
            <ul class="course-details1">
              <?php if($levels){ ?>
                <li>
                  <span class="icon"><img src="<?php echo $level_icon; ?>" alt="<?php echo  $level;  ?>"></span>
                  <?php echo  $level;  ?><strong><?php echo implode(", ", $levels);?></strong> 
                </li>
              <?php } ?>

              <?php if($duration_sincourse){ ?>
                <li>
                  <span class="icon"><img src="<?php echo $duration_icon; ?>" alt="<?php echo $duration; ?>"></span>
                  <?php echo $duration; ?> <?php echo $duration_sincourse; ?>
                </li>
              <?php } ?>
              <?php if($prerequisites_coursesin){ ?>
              <li>
                <span class="icon"><img src="<?php echo $prerequisites_icon; ?>" alt="<?php echo $prerequisites; ?>"></span>
                <?php echo $prerequisites; ?> <?php echo $prerequisites_coursesin; ?>
              </li>
              <?php } ?>
              <?php if($curriculum_cooursesin){ ?>
              <li>
                <span class="icon"><img src="<?php echo $curriculum_icon; ?>" alt=" <?php echo $curriculum; ?>"></span>
                <?php echo $curriculum; ?> <?php echo $curriculum_cooursesin; ?> 
              </li>
              <?php } ?>
               <?php if($language_coursesin){ ?>
              <li>
                <span class="icon"><img src="<?php echo $language_icon; ?>" alt="<?php echo $language; ?>"></span>
                <?php echo $language; ?> <?php echo $language_coursesin; ?>  
              </li>
              <?php } ?>
            </ul>
            <?php } ?>
          </div>

        </div>
      </div>
    </div>
  </section>


<?php endwhile; endif; 

get_footer(); ?>