<?php 
  $organizationID =  $organization->ID; 

  $organizationLogo = get_field('organization_logo',  $organizationID);

  $user = wp_get_current_user();
  $organizationLink = get_permalink($organizationID);

 
    if (in_array('organizer', (array) $user->roles)) {
      $organizationLogoLink = get_permalink($organizationID);
    } else {
      $organizationLogoLink = get_permalink($organizationID).'/learner-dashboard/';
    }

  ?>



<div class="learner-sidebar-left">
        <div class='site_logo'>
          <a href='<?php echo $organizationLogoLink; ?>'>
            <?php if(!empty($organizationLogo)){ ?>
              <img src="<?php echo $organizationLogo['url']; ?>" alt="<?php echo $organizationLogo['alt']; ?>">
            <?php }else{
              echo '<h2>' . esc_html($organization->post_title) . '</h2>';
            } ?>
          </a>
        </div>

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
