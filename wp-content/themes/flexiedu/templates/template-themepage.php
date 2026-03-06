<?php 
/**
* Template Name: Page: Theme Page
**/
get_header();     


    $dummyImg = get_template_directory_uri().'/assets/images/inner-banner.jpg';

    if(has_post_thumbnail()){
        $bannerImg = get_the_post_thumbnail_url();
    }else{
         $bannerImg = $dummyImg;
    }

    $banner_title_os = get_field('banner_title_tp');

?>


    <section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
        <?php if($banner_title_os){ ?>
        <div class="container">
            <h1><?php echo $banner_title_os; ?></h1>
        </div>
        <?php } ?>
    </section>

    
    <?php if(have_posts()) : while (have_posts() ) : the_post();  ?>

    <section class="deault-page space-mr">
        <div class="container">
            <?php the_content(); ?>
        </div>
    </section>


<?php endwhile; endif; 

get_footer(); ?>