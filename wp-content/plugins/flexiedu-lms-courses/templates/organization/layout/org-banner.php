<?php 

$organizationID =  $organization->ID;  

if(have_rows('banner_org', $organizationID)){ ?>
     <div class="learner-content__banner">
          <div class="banner-slider owl-carousel owl-theme">
           <?php while(have_rows('banner_org', $organizationID)) {
               
               the_row();
            
            ?> 
            <div class="item">
                <a href="#">
                  <img src="<?php echo get_sub_field('banner_image_sin')['url']; ?>" alt="<?php echo get_sub_field('banner_image_sin')['alt']; ?>">
                </a>
                <div class="absolute_text">
                  <?php echo get_sub_field('banner_content_sin'); ?>
                </div>
            </div>
            <?php } ?>
          </div>
          
        </div>
      <?php } ?>
