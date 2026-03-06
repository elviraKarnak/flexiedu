<?php
/**
* Template Name: Page: Login
**/
get_header(); 

    if(have_posts()) : while (have_posts() ) : the_post(); 
?>

<section class="deault-page space-pd">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-lg-6">
        <!-- <h1 class="text-center sc-hd"><?php //the_title(); ?></h1> -->
        <?php the_content(); ?>
      </div>
      <div class="col-lg-6">
        <div class="left_img">
          <img src="/wp-content/uploads/2026/01/form_bg.webp" alt="form-img">
        </div>
      </div>
    </div>
  </div>
 </section>


<?php endwhile; endif; ?>

<script>
  jQuery(document).ready(function($) {
    // Example jQuery code for login page functionality
     $(".ld-registration__wrapper").removeClass("ld-registration__wrapper--is-active").addClass("ld-registration__wrapper--login");
  });
</script>

<?php get_footer(); ?>