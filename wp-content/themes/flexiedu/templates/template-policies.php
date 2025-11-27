<?php 
/**
* Template Name: Page: Policies and Procedures
**/
get_header();     

do_action('load_filter_scripts');

    $dummyImg = get_template_directory_uri().'/assets/images/inner-banner.jpg';

    if(has_post_thumbnail()){
        $bannerImg = get_the_post_thumbnail_url();
    }else{
         $bannerImg = $dummyImg;
    }

    $banner_title_os = get_field('banner_title');

    $title_pnp = get_field('title_pnp');
    $description_pnp = get_field('description_pnp');

?>




    <section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
        <?php if($banner_title_os){ ?>
        <div class="container">
            <h1><?php echo $banner_title_os; ?></h1>
        </div>
        <?php } ?>
    </section>


  <div class="policies-section space-mr">


    <div class="container ">

    <?php if($title_pnp ||  $description_pnp){ ?>

      <div class="row row-top gy-md-0 gy-3">
         <?php if($title_pnp ){ ?>
        <div class="col-md-6">
          <h2><?php echo $title_pnp; ?></h2>
        </div>
        <?php } ?>
          <?php if($description_pnp){ ?>
        <div class="col-md-6">
          <p><?php echo $description_pnp; ?></p>
        </div>
        <?php } ?>
      </div>
    <?php } ?>


    <?php if(have_rows('pdf_books_pnp')){ ?>

      <div class="col-flex-wrap">

      <?php  while(have_rows('pdf_books_pnp')) : the_row(); ?>

        <!-- Item 1 -->
        <div class="col-wp">
          <a href="<?php echo get_sub_field("pdf_link_sin")['url']; ?>" target="_blank">
            <div class="policy-card green-card">
              <div class="policy-icon"><img src="<?php echo get_template_directory_uri(); ?>/assets/images/file.svg" alt=""></div>
              <h4 class="policy-title"><?php echo get_sub_field("title_sin"); ?></h4>
              <div class="policy-link">View More <img src="<?php echo get_template_directory_uri(); ?>/assets/images/arror-rgt.svg" alt=""></div>
            </div>
          </a>
        </div>

    <?php endwhile; ?>

      </div>

    <?php } ?>

    </div>
  </div>

<?php get_footer(); ?>