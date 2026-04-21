<?php ?>



<section class="learner-content">
  <div class="middle">
    <div class="learner-content__cards">

      <a href="<?php echo $organizationLink;?>courses" class="learner-content__card">
      <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/courses.svg" alt="icon">
        <p class="learner-content__card-title"><?php esc_html_e('My Courses', 'flexiedu-lms-courses'); ?></p>
      </a>

      <a href="<?php echo $organizationLink;?>requiredcourses" class="learner-content__card">
      <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/courses.svg" alt="icon">
        <p class="learner-content__card-title"><?php esc_html_e('My Required Courses', 'flexiedu-lms-courses'); ?></p>
      </a>

      <a href="<?php echo $organizationLink;?>apprenticeships" class="learner-content__card">
      <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/apprenticeships.svg" alt="icon">
        <p class="learner-content__card-title"><?php printf(esc_html__("%s's Apprenticeships", 'flexiedu-lms-courses'), esc_html($organization->post_title)); ?></p>
      </a>

      <a href="<?php echo $organizationLink;?>coursescatalogue" class="learner-content__card">
      <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/e-learning-courses-catalogue.svg" alt="icon">
        <p class="learner-content__card-title"><?php esc_html_e('E-Learning Catalogue', 'flexiedu-lms-courses'); ?></p>
      </a>

      <a href="<?php echo $organizationLink;?>applicationstatus" class="learner-content__card">
      <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/receive-applications.svg" alt="icon">
        <p class="learner-content__card-title"><?php esc_html_e('My Applications', 'flexiedu-lms-courses'); ?></p>
      </a>

      <a href="<?php echo $organizationLink;?>userguide" class="learner-content__card">
      <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/user-guides-downloads.svg" alt="icon">
        <p class="learner-content__card-title"><?php esc_html_e('User Guides', 'flexiedu-lms-courses'); ?></p>
      </a>

      <a href="<?php echo $organizationLink;?>results" class="learner-content__card">
      <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/course.png" alt="icon">
        <p class="learner-content__card-title"><?php esc_html_e('My Results', 'flexiedu-lms-courses'); ?></p>
      </a>

    </div>
  </div>
</section>


