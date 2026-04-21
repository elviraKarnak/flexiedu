<?php 

$organizationID =  $organization->ID; 
$organizationLink = get_permalink($organizationID);
$authorId = $organization->post_author; 

?>

  <section class="learner-content">
      <div class='middle'>
          <div class="learner-content__cards">
            <a href='javascript:void(0);' data-author-id="<?php echo $authorId; ?>" class="learner-content__card add_course_data">
              <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/add-courses.svg" alt="Add Courses">

              <p class="learner-content__card-title"><?php esc_html_e('Add Courses', 'flexiedu-lms-courses'); ?></p>
            </a>
            <a href='<?php echo $organizationLink;?>courses' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/courses.svg" alt="Courses">
              <p class="learner-content__card-title"><?php esc_html_e('Courses', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='<?php echo $organizationLink;?>apprenticeships' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/apprenticeships.svg" alt="Apprenticeships">
              <p class="learner-content__card-title"><?php esc_html_e('Apprenticeships', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='<?php echo $organizationLink;?>learnermanagement' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/learner-management.svg" alt="Learner Management">
              <p class="learner-content__card-title"><?php esc_html_e('Learner Management', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='<?php echo $organizationLink;?>live-classes' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/workshops-webinars-catalogue.svg" alt="Add Live Classes">
              <p class="learner-content__card-title"><?php esc_html_e('Live Classes', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='<?php echo $organizationLink;?>documents' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/add-document.svg" alt="Add Document">
              <p class="learner-content__card-title"><?php esc_html_e('Documents', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='<?php echo $organizationLink;?>pro-pannel' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/add-document.svg" alt="Add Document">
              <p class="learner-content__card-title"><?php esc_html_e('Pro Panel', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='https://dev.flexiedu.co.uk/wp-content/uploads/2026/03/samplae-pdf-3.pdf'  target="_blank" class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/career-pathways.svg" alt="Career Pathways">
              <p class="learner-content__card-title"><?php esc_html_e('Career Pathways', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='<?php echo $organizationLink;?>receivedapplication' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/receive-applications.svg" alt="Received Applications">
              <p class="learner-content__card-title"><?php esc_html_e('Received Applications', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='https://dev.flexiedu.co.uk/wp-content/uploads/2026/03/samplae-pdf-3.pdf'  target="_blank" class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/workshops-webinars-catalogue.svg" alt="Workshops Webinars Catalogue">
              <p class="learner-content__card-title"><?php esc_html_e('Workshops Webinars Catalogue', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='https://dev.flexiedu.co.uk/wp-content/uploads/2026/03/samplae-pdf-3.pdf'  target="_blank" class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/e-learning-courses-catalogue.svg" alt="E-Learning Courses Catalogue">
              <p class="learner-content__card-title"><?php esc_html_e('E-Learning Courses Catalogue', 'flexiedu-lms-courses'); ?></p>
            </a>

            <a href='https://dev.flexiedu.co.uk/wp-content/uploads/2026/03/samplae-pdf-3.pdf'  target="_blank" class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/user-guides-downloads.svg" alt="User Guides and Downloads">
              <p class="learner-content__card-title"><?php esc_html_e('User Guides and Downloads', 'flexiedu-lms-courses'); ?></p>
            </a>
          </div>
      </div>
    </section>
