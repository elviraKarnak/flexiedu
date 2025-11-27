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
        wp_nav_menu(
          array(
            'container' => '',
            'container_class' => '',
            'container_id' => '',
            'items_wrap' => '<ul id="%1$s menu" class="%2$s header-btn-sc d-flex align-items-center justify-content-end ">%3$s</ul>',
            'theme_location' => 'menu-2',
          )
        );
        ?>


      </div>
    </nav>
  </header>