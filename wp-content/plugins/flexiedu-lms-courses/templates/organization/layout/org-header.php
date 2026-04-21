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


    $user = wp_get_current_user();
    $user_id = $user->ID;
    $firstName = get_user_meta($user_id, 'first_name', true);
    $lastName = get_user_meta($user_id, 'last_name', true);

  ?>



    <div class="row">
      <div class="col-lg-6">
        <div class="page_title-wrap">
          <div class="toggle">
            <a href="#" class="toggleMenu">
              <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/hemburger.svg" alt="<?php echo esc_attr__('Menu', 'flexiedu-lms-courses'); ?>">
            </a>
          </div>
          <div class="page_title">
            <h3 class="title"><?php echo get_the_title($organization->ID); ?></h3>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="header_align-end">
        <div class="search_wrap">
              <input type="text" placeholder="<?php echo esc_attr__('Search', 'flexiedu-lms-courses'); ?>">
              
            </div>
          <nav class="navbar">
            <ul class="navbar-nav">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user"></i>
                  <?php
                  // Get user data (for login/display name)
                    $user_info = get_userdata($user_id);
                    $loginName = $user_info->user_login;

                    // Display logic
                    if (!empty($firstName)) {
                        // First name exists, show first name + last name
                        echo esc_html($firstName . ' ' . $lastName);
                    } else {
                        // First name empty, show login name
                        echo esc_html($loginName);
                    }
                    ?>
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="<?php echo $organizationLink; ?>/profile"><?php esc_html_e('Profile', 'flexiedu-lms-courses'); ?></a></li>
                  <li><a class="dropdown-item" href="<?php echo wp_logout_url(); ?>"><?php esc_html_e('Logout', 'flexiedu-lms-courses'); ?></a></li>
                </ul>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
