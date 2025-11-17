 <?php 

 $title_ftrcta = get_field('title_ftrcta', 'option');
 $footer_link_ftrcta = get_field('footer_link_ftrcta', 'option');

$footer_logo_ftr = get_field('footer_logo_ftr', 'option');

 

 

 ?>

<footer>
    <div class="container">

    <?php if($title_ftrcta || $footer_link_ftrcta){ ?>
      <!-- CTA Section -->
      <div class="cta-section">
        <div class="row cta-content align-items-center">
          <?php if($title_ftrcta){ ?>
            <div class="col-md-6">
              <h2 class="cta-title"><?php echo $title_ftrcta; ?></h2>
            </div>
          <?php } ?>
          <div class="col-md-6  text-end">
          <?php if($footer_link_ftrcta){ ?>
            <a href="<?php echo $footer_link_ftrcta['url']; ?>" class="btn btn-custom mx-auto">
              <span> <?php echo $footer_link_ftrcta['title']; ?></span>

            </a>
          </div>
          <?php } ?>
        </div>
      </div>

    <?php } ?>

      <!-- Footer Main -->
      <div class="footer-main">
        <div class="footer-content">
          <div class="d-flex justify-content-between">
            <!-- Logo Column -->
             <?php if($footer_logo_ftr){ ?>
              <div class="col-n-1 mb-4">
                <div class="footer-logo-section">
                  <a href="<?php echo home_url(); ?>">
                    <img src="<?php echo $footer_logo_ftr['url']; ?>" alt="<?php echo $footer_logo_ftr['alt']; ?>">
                  </a>
                </div>
              </div>
            <?php } ?>
            <!-- Quick Links -->
            <div class="col-n-2 col-md-6 mb-4">
              <h3 class="footer-section-title"><?php echo get_field('menu_title_1_ftr', 'option'); ?></h3>
              <?php 
                wp_nav_menu(
                array(
                  'container'            => '',
                  'container_class'      => '',
                  'container_id'         => '',
                  'items_wrap'     => '<ul id="%1$s menu" class="%2$s footer-links">%3$s</ul>',
                  'theme_location' => 'menu-3',
                )
              );
            ?>
            </div>

            <!-- Follow Us -->
            <div class="col-n-3 col-md-6 mb-4">
              <h3 class="footer-section-title"><?php echo get_field('menu_title_2_ftr', 'option'); ?></h3>

              <?php 
                wp_nav_menu(
                array(
                  'container'            => '',
                  'container_class'      => '',
                  'container_id'         => '',
                  'items_wrap'     => '<ul id="%1$s menu" class="%2$s footer-links">%3$s</ul>',
                  'theme_location' => 'menu-4',
                )
              );
            ?>
            </div>

            <?php if(have_rows('contact_us_fcu', 'option')){ ?>
            <!-- Contact Us -->
            <div class="col-n-4 col-md-6 mb-4">
              <h3 class="footer-section-title"><?php echo get_field('title_fcu', 'option'); ?></h3>
              <?php while(have_rows('contact_us_fcu', 'option')){ 
                the_row();
                ?>
              <div class="contact-item">
                <?php if(get_sub_field('link_sin')){ ?>
                  <a href="<?php echo get_sub_field('link_sin'); ?>">
                   <?php echo get_sub_field('icon_sin'); ?>
                    <span><?php echo get_sub_field('contact_sin'); ?></span>
                  </a>
                <?php } else { ?>

                    <?php echo get_sub_field('icon_sin'); ?>
                    <span><?php echo get_sub_field('contact_sin'); ?></span>

                  <?php } ?>
              </div>
              <?php } ?>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <?php 

          $privacy_policy_ftr = get_field('privacy_policy_ftr', 'option');
          $policies_ftr = get_field('policies_ftr', 'option');
          $copyright_text = get_field('copyright_text', 'option');
      
      
      ?>
      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <div class="footer-bottom-content">
          <div class="footer-legal">
             <?php if($privacy_policy_ftr ){ ?>
                <a href="<?php echo $privacy_policy_ftr['url']; ?>"><?php echo $privacy_policy_ftr['title']; ?></a>
              <?php } ?>
              <?php if($policies_ftr ){ ?>
                <a href="<?php echo $policies_ftr['url']; ?>"><?php echo $policies_ftr['title']; ?></a>
              <?php } ?>
          </div>

          <?php if($copyright_text ){ ?>
          <div class="copyright">
             <?php echo $copyright_text; ?>
          </div>
          <?php } ?>
          <?php 
          
          $developer_name = get_field('developer_name', 'option');
          $developer_link = get_field('developer_link', 'option');
          $developer_logo_img = get_field('developer_logo_img', 'option');
          
          ?>
          <?php if($developer_name || $developer_link || $developer_logo_img){ ?>
            <div class="developer-credit">
              <span><?php echo $developer_name; ?></span>
              <span class="developer-logo">
                <a href="<?php echo $developer_link; ?>" target="_blank">
                  <img src="<?php echo $developer_logo_img['url']; ?>" alt="elvirainfortech">
                </a></span>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </footer>
  
<?php wp_footer(); ?>

</body>
</html>	