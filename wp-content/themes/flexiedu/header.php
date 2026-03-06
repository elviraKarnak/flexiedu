<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <!-- Required meta tags -->
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport"
    content="width=device-width, minimum-scale=1, maximum-scale=1, initial-scale=1, shrink-to-fit=no">
  <!-- Page Title -->
  <title><?php wp_title('|', true, 'right');
  bloginfo('name'); ?></title>
  <!-- Stylesheets and Other Head Elements -->
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <header>
    <nav>
      <div class="container nav-container">

        <?php if (has_custom_logo()) { ?>
          <div class="logo">
            <?php the_custom_logo(); ?>
          </div>
        <?php } ?>

        <div class="menu-sc">
          <button class="menu-toggle" id="menuToggle">
            <span></span>
            <span></span>
            <span></span>
          </button>
          <div class="menu-wrap">
            <?php
            wp_nav_menu(
              array(
                'container' => '',
                'container_class' => '',
                'container_id' => '',
                'items_wrap' => '<ul id="%1$s menu" class="%2$s menu ">%3$s</ul>',
                'theme_location' => 'menu-1',
              )
            );
            ?>
          </div>
        </div>

    

        <?php
        if(is_user_logged_in()){

           
            wp_nav_menu(
              array(
                'container' => '',
                'container_class' => '',
                'container_id' => '',
                'items_wrap' => '<ul id="%1$s menu" class="%2$s header-btn-sc d-flex align-items-center justify-content-end ">%3$s</ul>',
                'theme_location' => 'menu-5',
              )
            );

        }else{
          
        wp_nav_menu(
          array(
            'container' => '',
            'container_class' => '',
            'container_id' => '',
            'items_wrap' => '<ul id="%1$s menu" class="%2$s header-btn-sc d-flex align-items-center justify-content-end ">%3$s</ul>',
            'theme_location' => 'menu-2',
          )
        );
        }

        ?>

            <div class="header-cart position-relative ms-4">
            <a href="<?php echo wc_get_cart_url(); ?>" class="text-dark">
                <i class="fa-solid fa-cart-shopping fa-lg"></i>
                <span class="cart-count badge rounded-pill position-absolute top-0 start-100 translate-middle"
                      style="background:#B1D95A;">
                    <?php echo WC()->cart->get_cart_contents_count(); ?>
                </span>
            </a>
          </div>


      </div>
    </nav>
  </header>