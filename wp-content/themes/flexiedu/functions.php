<?php
// style sheet & scripts

function flexiedu_enqueue(){

	$uri = get_theme_file_uri();
    $ver = 1.0;
    $vert = time();

      wp_register_style( 'bootstrap',   $uri. '/assets/css/bootstrap.min.css', [], $ver);
	  wp_register_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], $ver);
	  wp_register_style( 'owl', $uri. '/assets/css/owl.carousel.min.css', [], $ver);
      wp_register_style( 'owl-theme', $uri. '/assets/css/owl.theme.default.min.css', [], $ver);
	  wp_register_style( 'nouislider', $uri. '/assets/css/nouislider.min.css', [], $ver);
	  wp_register_style( 'fancybox', $uri. '/assets/css/fancybox.min.css', [], $ver);
	  wp_register_style( 'theme-css',  $uri. '/assets/css/style.css', [], $vert);
	  wp_register_style( 'theme_stylesheet', $uri. '/style.css', [], $vert);


	  wp_enqueue_style( 'bootstrap');
	  wp_enqueue_style( 'font-awesome');
	  wp_enqueue_style( 'owl');
	  wp_enqueue_style( 'owl-theme');
	  wp_enqueue_style( 'nouislider');
	  wp_enqueue_style( 'fancybox');
	  wp_enqueue_style( 'theme-css');
	  wp_enqueue_style( 'theme_stylesheet');

	
	  wp_register_script( 'bootstrap', $uri . '/assets/js/bootstrap.bundle.min.js', [], $ver, true );
	  wp_register_script( 'owl',     $uri . '/assets/js/owl.carousel.min.js',  [], $ver, true );
	  wp_register_script( 'nouislider',     $uri . '/assets/js/nouislider.min.js',  [], $ver, true );
	  wp_register_script( 'fancybox',     $uri . '/assets/js/fancybox.umd.js',  [], $ver, true );
	  wp_register_script( 'custom-js', $uri . '/assets/js/custom.js', [], $vert, true );

	  wp_enqueue_script('jquery');
	  wp_enqueue_script('bootstrap');
	  wp_enqueue_script('owl');
	  wp_enqueue_script('nouislider');
	  wp_enqueue_script('fancybox');
	  wp_enqueue_script('custom-js');

  }

  add_action( 'wp_enqueue_scripts', 'flexiedu_enqueue' );



// register navs
register_nav_menus(
	array(
		'menu-1' => __('Primary', 'flexiedu'),
		'menu-2' => __('Login', 'flexiedu'),
		'menu-3' => __('Footer First Menu', 'flexiedu'),
		'menu-4' => __('Footer Second Menu', 'flexiedu'),
    )
);

	// theme support

		function flexiedu_setup_theme(){
			add_theme_support( 'custom-logo' );
		    add_theme_support( 'post-thumbnails' );
			add_theme_support( 'title-tag' );
		}
		add_action( 'after_setup_theme', 'flexiedu_setup_theme' );


require get_template_directory() . '/inc/custom_functions.php';

require get_template_directory() . '/inc/course_function.php';