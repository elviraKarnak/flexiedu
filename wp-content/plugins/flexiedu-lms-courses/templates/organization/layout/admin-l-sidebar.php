<div class="learner-sidebar-left">

      <?php if (has_custom_logo()) { ?>
          <div class="site_logo">
            <?php the_custom_logo(); ?>
          </div>
        <?php } ?>


        <?php
            wp_nav_menu(
              array(
                'container' => '',
                'container_class' => '',
                'container_id' => '',
                'items_wrap' => '<ul id="%1$s menu" class="%2$s menu ">%3$s</ul>',
                'theme_location' => 'menu-6',
              )
            );
        ?>
</div>