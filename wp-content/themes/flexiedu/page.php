<?php

get_header(); 

    if(have_posts()) : while (have_posts() ) : the_post(); 
?>

<section class="deault-page space-mr">
  <div class="container">
    <h1 class="text-center"><?php the_title(); ?></h1>
    <?php the_content(); ?>
  </div>
 </section>


<?php endwhile; endif; 

get_footer(); ?>