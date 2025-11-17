<?php 
/**
* Template Name: Page: Our Services
**/
get_header(); 


    $dummyImg = get_template_directory_uri().'/assets/images/inner-banner.jpg';

    if(has_post_thumbnail()){
        $bannerImg = get_the_post_thumbnail_url();
    }else{
         $bannerImg = $dummyImg;
    }

    $banner_title_os = get_field('banner_title_os');


?>


    <section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
        <?php if($banner_title_os){ ?>
        <div class="container">
            <h1><?php echo $banner_title_os; ?></h1>
        </div>
        <?php } ?>
    </section>



      <?php 

    $sub_title_os = get_field('sub_title_os');
    $title_os = get_field('title_os');
    $description_os = get_field('description_os');
    $service_info_section_os = get_field('service_info_section_os');
  
  ?>

 <?php if(have_rows('service_info_section_os')){ ?>

  <section class="why-section our-services our-service-listing space-mr">
    <div class="container">
      <div class="row justify-content-center align-items-center text-center">
        <div class="col-md-8">


         <?php if($sub_title_os){ ?>
            <div class="top-title"><?php echo $sub_title_os; ?></div>
          <?php } ?>

          <?php if($title_os){ ?>
            <h2 class="sc-hd"><?php echo $title_os; ?></h2>
          <?php } ?>
        </div>

        <?php if( $description_os)?>
        <div class="col-md-5">
          <p class="mb-0"><?php echo $description_os; ?></p>
        </div>
      </div>
 
      <div class="row gy-5 btm-row">

        <?php $i=1; 

              while (have_rows('service_info_section_os')) {
                the_row();
                $icon_sin_wc = get_sub_field('icon_sin_wc');
                $title_sin_wc = get_sub_field('title_sin_wc');
                $description_sin_wc = get_sub_field('description_sin_wc');
              
              ?>

              <div class="col-md-6 col-wp">
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
  </section>
<?php } ?>

<?php get_footer(); ?>