<?php
/**
 * Template Name: Page: New Dashboard
 **/
?>

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
<div class="learner-container">
  <div class="learner-header">
    <div class='site_logo'>
      <a href='#' class=""><img src="https://dev.flexiedu.co.uk/wp-content/uploads/2025/11/logo1.png" alt="logo"></a>
    </div>
    <ul class='menu'>
      <li>
        <a href="#" class="learner-header__nav-item">Home</a>
      </li>
        <a href="#" class="learner-header__nav-item">My Bookings</a>
      <li>
        <a href="#" class="learner-header__nav-item">My Courses</a>        
      </li>
      <li>
        <a href="#" class="learner-header__nav-item">My Apprenticeship</a>
      </li>
      <li>
        <a href="#" class="learner-header__nav-item">My Profile</a>
      </li>
      <li>
        <a href="#" class="learner-header__nav-item">FAQs</a>
      </li>
    </ul>
        <div class="search_wrap">
          <input type="text" placeholder='Search'>
          <button class="logout"><i class="fas fa-user"></i></button>
        </div>
      </div>
      <div class="learner-content__banner">
          <div class="banner-slider owl-carousel owl-theme">
            <div class="item">
              <a href="https://example.com/page1">
                <img src="https://dev.flexiedu.co.uk/wp-content/uploads/2025/11/about_banner.png" alt="Slide 1">
              </a>
              <div class="absolute_text">
                <p>Lorem ipsum dolor sit amet.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, soluta?</p>
                <a href="#">Read More</a>
            </div>
            </div>
            <div class="item">
              <a href="https://example.com/page2">
                <img src="https://dev.flexiedu.co.uk/wp-content/uploads/2025/11/inner-banner-scaled.jpg" alt="Slide 2">
              </a>
              <div class="absolute_text">
                <p>Lorem ipsum dolor sit amet.</p>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Non, soluta?</p>
                <a href="#">Read More</a>
            </div>
            </div>
              <div class="item">
                <a href="https://example.com/page3">
                  <img src="https://dev.flexiedu.co.uk/wp-content/uploads/2025/11/about_banner.png" alt="Slide 3">
                </a>
              </div>
          </div>
          
        </div>
  <main class="learner-main">
    <aside class="learner-sidebar-left">
      <div class="learner-sidebar-left__box">Sign up for an upcoming event and our courses (click to page)</div>
      <div class="learner-sidebar-left__box">Interactive Calendar (Dropdown list of activities in a date)</div>
    </aside>

    <section class="learner-content">
      <div class='middle'>
          <div class="learner-content__cards">
            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">My Ongoing Courses</p>
            </a>

            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">My Required Courses</p>
            </a>

            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">Company Name Apprenticeships</p>
            </a>

            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">Career Pathways</p>
            </a>

            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">My Applications</p>
            </a>

            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">Workshops Webinars Catalogue</p>
            </a>

            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">E-Learning Courses Catalogue</p>
            </a>

            <a href='#' class="learner-content__card">
              <p class="learner-content__card-title">User Guides and Downloads</p>
            </a>
          </div>
      </div>
    </section>
    <aside class="learner-sidebar-right">
      <div class="learner-sidebar-right__box">Logged in user details</div>
      <div class="learner-sidebar-right__box">My Bookings</div>
    </aside>
  </main>
</div>
<?php wp_footer(); ?>

</body>
</html>	