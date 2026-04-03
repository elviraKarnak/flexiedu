<?php
/**
 * Template Name: Page: About New dashboard
 **/
get_header();


$dummyImg = get_template_directory_uri() . '/assets/images/inner-banner.jpg';

if (has_post_thumbnail()) {
  $bannerImg = get_the_post_thumbnail_url();
} else {
  $bannerImg = $dummyImg;
}

$banner_title_os = get_field('banner_title');

$title_au = get_field('title_au');
$sub_title_au = get_field('sub_title_au');
$description_au = get_field('description_au');
$images_as_1 = get_field('images_as_1');
$images_as_2 = get_field('images_as_2');
$images_as_3 = get_field('images_as_3');
$images_as_4 = get_field('images_as_4');
$images_as_5 = get_field('images_as_5');



?>


<section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
  <?php if ($banner_title_os) { ?>
    <div class="container">
      <h1><?php echo $banner_title_os; ?></h1>
    </div>
  <?php } ?>
</section>



</section>


<?php if ($title_au || $sub_title_au || $description_au || $images_as_1 || $images_as_2 || $images_as_3 || $images_as_4 || $images_as_5) { ?>

  <section class="who-we-are space-mr">
    <div class="container">
      <div class="row row-main align-items-center">
        <div class="col-md-6">
          <div class="img-sc position-relative">
            <div class="row  g-3">
              <div class="col-6">
                <?php if ($images_as_1 || $images_as_2) { ?>
                  <div class="row g-3">
                    <?php if ($images_as_1) { ?>
                      <div class="col-12">
                        <img src="<?php echo $images_as_1['url']; ?>" alt="<?php echo $images_as_1['alt']; ?>">
                      </div>
                    <?php } ?>
                    <?php if ($images_as_2) { ?>
                      <div class="col-12">
                        <img src="<?php echo $images_as_2['url']; ?>" alt="<?php echo $images_as_2['alt']; ?>">
                      </div>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>
              <div class="col-6">
                <?php if ($images_as_3 || $images_as_4) { ?>
                  <div class="row g-3">
                    <?php if ($images_as_3) { ?>
                      <div class="col-12">
                        <img src="<?php echo $images_as_3['url']; ?>" alt="<?php echo $images_as_3['alt']; ?>">
                      </div>
                    <?php } ?>
                    <?php if ($images_as_4) { ?>
                      <div class="col-12">
                        <img src="<?php echo $images_as_4['url']; ?>" alt="<?php echo $images_as_4['alt']; ?>">
                      </div>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>
            </div>
            <?php if ($images_as_5) { ?>
              <div class="rotate-img">
                <img src="<?php echo $images_as_5['url']; ?>" alt="<?php echo $images_as_5['alt']; ?>">
              </div>
            <?php } ?>
          </div>

        </div>
        <div class="col-md-6">

          <?php if ($sub_title_au) { ?>
            <div class="top-title"><?php echo $sub_title_au; ?></div>
          <?php } ?>

          <?php if ($title_au) { ?>
            <h2 class="sc-hd"><?php echo $title_au; ?></h2>
          <?php } ?>

          <?php if ($description_au) { ?>
            <?php echo $description_au; ?>
          <?php } ?>

        </div>
      </div>
    </div>
  </section>
<?php } ?>

<?php
$title_ht = get_field('title_trusted');
$company_logo_ht = get_field('company_logos');

?>

<?php if ($company_logo_ht) {


  ?>

  <section class="mq-wp space-mr">

    <?php if ($title_ht) { ?>
      <div class="container">
        <h4 class="text-center"><?php echo $title_ht; ?></h4>
      </div>
    <?php } ?>

    <div class="marquee-container">
      <div class="marquee">
        <div class="marquee__group">
          <?php foreach ($company_logo_ht as $galimg) { ?>
            <div class="logo-slider-item gsap-logo-item">
              <div class="logo-slider-in">
                <img src="<?php echo $galimg['url']; ?>" alt="<?php echo $galimg['alt']; ?>"
                  title="<?php echo $galimg['title']; ?>">
              </div>
            </div>
          <?php } ?>
        </div>
        <div class="marquee__group">

          <?php foreach ($company_logo_ht as $galimg) { ?>
            <div class="logo-slider-item gsap-logo-item">
              <div class="logo-slider-in">
                <img src="<?php echo $galimg['url']; ?>" alt="<?php echo $galimg['alt']; ?>"
                  title="<?php echo $galimg['title']; ?>">
              </div>
            </div>
          <?php } ?>

        </div>
      </div>
    </div>
  </section>

<?php } ?>

<?php

$title_ab = get_field('title_ab');
$sub_tittle_ab = get_field('sub_tittle_ab');

?>

<?php if (have_rows('benefits_about')): ?>

  <section class="benefits-section space-pd ">
    <div class="container ">
      <div class="text-center top-sc">

        <?php if ($sub_tittle_ab) { ?>
          <p class="top-title"><?php echo $sub_tittle_ab; ?></p>
        <?php } ?>
        <?php if ($title_ab) { ?>
          <h2 class="sc-hd"><?php echo $title_ab; ?></h2>
        <?php } ?>

      </div>

      <div class="row">

        <?php $i = 1;

        while (have_rows('benefits_about')):
          the_row();


          ?>
          <!-- LEFT COLUMN -->
          <div class="col-md-6 col-wp">
            <!-- Item 01 -->
            <div class="benefit-item d-flex">
              <div class="benefit-number">0<?php echo $i; ?></div>
              <div>
                <h4 class="benefit-title"><?php echo get_sub_field('title_sin'); ?></h4>
                <p class="benefit-text">
                  <?php echo get_sub_field('description_sin'); ?>
                </p>
              </div>
            </div>
          </div>
          <?php $i++; endwhile; ?>

      </div>
    </div>

  </section>
<?php endif; ?>

<?php

$sub_title_mv = get_field('sub_title_mv');
$title_mv = get_field('title_mv');
$description_mv = get_field('description_mv');
$mission_mv = get_field('mission_mv');
$vission_mv = get_field('vission_mv');
$mission_image_mv = get_field('mission_image_mv');
$vission_image_mv = get_field('vission_image_mv');
$section_image_mv = get_field('section_image_mv');

$cta_button_mv = get_field('cta_button_mv');



?>

<section class="dlp-mission-vision-outer-wrap space-mr">
  <div class="container">
    <div class="row align-items-center gx-5">
      <!-- Left Content -->
      <div class="col-lg-4 col-md-12">
        <div class="dlp-left-content-zone">
          <?php if($sub_title_mv){ ?>
            <div class="top-title"><?php  echo $sub_title_mv; ?></div>
          <?php  } ?>
           <?php if($title_mv){ ?>
            <h2><?php  echo $title_mv; ?></h2>
           <?php  } ?>
           <?php if($description_mv){ ?>
              <p><?php  echo $description_mv; ?></p>
          <?php } ?>

          <?php if($cta_button_mv){ ?>
          <a href="<?php echo $cta_button_mv['url']; ?>" class="btn btn-custom" target="<?php echo $cta_button_mv['target']; ?>">
            <span> <?php echo $cta_button_mv['title']; ?></span>
          </a>
          <?php } ?>
        </div>
      </div>

    <?php if($section_image_mv){ ?>
      <div class="col-lg-3 col-md-12">
        <div class="dlp-center-photo-block">

           <img src="<?php echo $section_image_mv['url']?>" alt="<?php echo $section_image_mv['alt']?>">

        </div>
      </div>
    <?php } ?>

      <div class="col-lg-5 col-md-12">
        <div class="dlp-right-features-zone">

          <div class="dlp-feature-box-unit">
          <?php if($mission_image_mv){ ?>
            <div class="dlp-icon-badge-box">
               <img src="<?php echo $mission_image_mv['url']?>" alt="<?php echo $mission_image_mv['alt']?>">
            </div>
          <?php } ?>
          <?php if($mission_image_mv){ ?>
            <div class="dlp-text-details-area">
              <?php echo $mission_mv; ?>
            </div>
            <?php } ?>
          </div>

          <div class="dlp-feature-box-unit">
          <?php if($vission_image_mv){ ?>
            <div class="dlp-icon-badge-box">
               <img src="<?php echo $vission_image_mv['url']?>" alt="<?php echo $vission_image_mv['alt']?>">
            </div>
          <?php } ?>
          <?php if($vission_mv){ ?>
            <div class="dlp-text-details-area">
              <?php echo $vission_mv; ?>
            </div>
            <?php } ?>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>

<?php


$video_banner_title_ht = get_field('title_vid');
$video_banner_description_ht = get_field('description_vid');
$video_banner_cta_ht = get_field('link_vid');

$video_banner_url_ht = get_field('video_url_vid');
$video_banner_cover_ht = get_field('video_cover');

?>

<?php if ($video_banner_url_ht || $video_banner_cover_ht) { ?>

  <section class="home-banner home-video2-sc position-relative d-flex align-items-end justify-content-center">
    <div class="banner-or-vd-sc">
      <!-- <img src="<?php //echo get_template_directory_uri(); ?>/assets/images/banner.jpg" alt=""> -->
      <video autoplay playsinline muted disablePictureInPicture poster="<?php echo $video_banner_cover_ht['url']; ?>">
        <source src="<?php echo $video_banner_url_ht; ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    </div>
    <div class="container">
      <?php if ($video_banner_title_ht) { ?>
        <h2 class="text-white"><?php echo $video_banner_title_ht; ?></h2>
      <?php } ?>
      <?php if ($video_banner_description_ht) { ?>
        <p class="text-white mt-4 mb-4"><?php echo $video_banner_description_ht; ?></p>
      <?php } ?>
      <?php

      if ($video_banner_cta_ht) {
        $link_url = $video_banner_cta_ht['url'];
        $link_title = $video_banner_cta_ht['title'];
        $link_target = $video_banner_cta_ht['target'] ? $video_banner_cta_ht['target'] : '_self';
        ?>

        <a href="<?php echo $link_url; ?>" class="btn btn-custom white-btn " <?php echo $link_target; ?>>
          <span> <?php echo $link_title; ?></span>
        </a>

      <?php } ?>

    </div>
  </section>
<?php } 


  $testimonials_enabled = get_field('testimonials_contols');

  if($testimonials_enabled){
    get_template_part('template-parts/testimonials');
  }

 ?>



<?php get_footer(); ?>