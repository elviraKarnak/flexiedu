<?php 
/**
* Template Name: Page: Contact
**/
get_header();     


    $dummyImg = get_template_directory_uri().'/assets/images/inner-banner.jpg';

    if(has_post_thumbnail()){
        $bannerImg = get_the_post_thumbnail_url();
    }else{
         $bannerImg = $dummyImg;
    }

    $banner_title_os = get_field('banner_title_cu');

    $from_title_cu = get_field('from_title_cu');
    $from_description_cu = get_field('from_description_cu');
    $form_shortcode_cu = get_field('form_shortcode_cu');

?>


    <section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
        <?php if($banner_title_os){ ?>
        <div class="container">
            <h1><?php echo $banner_title_os; ?></h1>
        </div>
        <?php } ?>
    </section>

    
  <section class="contact-section space-mr">
    <div class="container">
      <div class="row row1">
        
        <?php if($form_shortcode_cu){ ?>
        <!-- Left Column: Contact Form -->
        <div class="col-md-7">
            <?php if($from_title_cu){ ?>
                <h2 class="sc-hd"><?php echo $from_title_cu; ?></h2>
            <?php } ?>

            <?php if($from_description_cu){ ?>
                <p><?php echo $from_description_cu;?></p>
            <?php } ?>
          

          <?php echo do_shortcode($form_shortcode_cu);?>
        </div>
       <?php } ?>


        <?php if(have_rows('contact_us_cu')) { ?>
                <!-- Right Column: Contact Info -->
            <div class="col-md-5 mt-5 mt-md-0 btm-sc rgt-sc d-flex align-items-center">
                <div class="w-100">
                    <?php while(have_rows('contact_us_cu')){ 
                        the_row();


                        $title_sin = get_sub_field('title_sin');
                        $link_sin = get_sub_field('link_sin');
                        $icon_sin = get_sub_field('icon_sin');
                        $contact_sin = get_sub_field('contact_sin');
                        $description_sin = get_sub_field('description_sin');

                        ?>

                
                    <?php if($link_sin){ ?>
                        <div class="col-wp">
                            <?php if($title_sin) { ?>      
                                <h3><?php echo $title_sin; ?></h3>
                            <?php } ?>
                            <?php if($description_sin) { ?>  
                            <p class=" mb-1"><?php echo $description_sin; ?></p>
                            <?php } ?>
                            <?php if($contact_sin){ ?>
                                <p>
                                    <a href="tel:(123) 456-7890" class=" flex-cnt d-flex align-items-center">
                                        <?php if($icon_sin ){ ?>
                                            <img src="<?php echo $icon_sin;?>" alt="<?php echo $title_sin; ?>"> 
                                        <?php } ?>
                                        <?php echo $contact_sin; ?>
                                    </a>
                                </p>
                            <?php } ?>        
                        </div>
                    <?php }else{?>
                        <div class="col-wp">
                            <?php if($title_sin) { ?>      
                                <h3><?php echo $title_sin; ?></h3>
                            <?php } ?>
                            <?php if($description_sin) { ?>  
                            <p class="mb-2"><?php echo $description_sin; ?></p>
                            <?php } ?>
                            <?php if($contact_sin){ ?>
                                <p class="flex-cnt d-flex align-items-center">
                                    
                                        <?php if($icon_sin ){ ?>
                                            <img src="<?php echo $icon_sin;?>" alt="<?php echo $title_sin; ?>"> 
                                        <?php } ?>
                                        <?php echo $contact_sin; ?>
                                </p>
                            <?php } ?>        
                        </div>

                <?php  }} ?>
                </div>
            </div>
        <?php } ?>
      </div>
    </div>
  </section>


  <?php 
  
  $title_faq = get_field('title_faq');
  $description_faq = get_field('description_faq');
    $form_shortcode_cu = get_field('form_shortcode_cu');

  
  
  ?>

  <?php if(have_rows('faq_main')){ ?>
  <section class="faq-section space-mr">
    <div class="container">
      <div class="row top-sc text-center justify-content-center">
        <div class="col-md-8">
          <?php if($title_faq){ ?>
          <h2 class="sc-hd"><?php echo $title_faq; ?></h2>
          <?php } ?>
          <?php if($description_faq){ ?>
          <p><?php echo $description_faq; ?></p>
          <?php } ?>
        </div>
      </div>

      <div class="row g-4">

      <?php
      
      $k=1;
      
      while (have_rows('faq_main')) {
            the_row();
        ?>

        <div class="col-md-6">
          <div class="accordion" id="faq<?php $k; ?>">

          <?php if(have_rows('faq_cs')) : 
            
            $i=1;
            while (have_rows('faq_cs')): the_row();?>
              <div class="accordion-item">
                <h2 class="accordion-header">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#faq<?php echo $k.$i; ?>">
                    <?php echo get_sub_field('title_faq'); ?>
                    <i class="fa-solid fa-arrow-up-right ms-auto"></i>
                  </button>
                </h2>
                <div id="faq<?php echo $k.$i; ?>" class="accordion-collapse collapse">
                  <div class="accordion-body">
                    <?php echo get_sub_field('description_faq'); ?>
                  </div>
                </div>
              </div>
            <?php $i++; endwhile; endif;  ?>
          </div>
        </div>
      <?php $k++; }  ?>


        <!-- <div class="col-md-6">
          <div class="accordion" id="faqRight">
            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq5">
                  What should I do if I forget my password?
                  <i class="fa-solid fa-arrow-up-right ms-auto"></i>
                </button>
              </h2>
              <div id="faq5" class="accordion-collapse collapse">
                <div class="accordion-body">
                  Absolutely! It has safety sensors that stop the blades immediately if lifted or
                  obstructed.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq6">
                  Does FlexiEdu offer free courses or trials?
                  <i class="fa-solid fa-arrow-up-right ms-auto"></i>
                </button>
              </h2>
              <div id="faq6" class="accordion-collapse collapse">
                <div class="accordion-body">
                  You can control it using the mobile app or preset weekly schedules.
                </div>
              </div>
            </div>

            <div class="accordion-item">
              <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#faq7">
                  Is FlexiEdu available on mobile devices?
                  <i class="fa-solid fa-arrow-up-right ms-auto"></i>
                </button>
              </h2>
              <div id="faq7" class="accordion-collapse collapse">
                <div class="accordion-body">
                  It lasts for 90–120 minutes depending on terrain and mowing intensity.
                </div>
              </div>
            </div>


          </div>
        </div> -->
      </div>
    </div>
  </section>
  <?php } ?>



<?php get_footer(); ?>