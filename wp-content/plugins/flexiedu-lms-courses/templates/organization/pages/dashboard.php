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

              <p class="learner-content__card-title">Add Courses</p>
            </a>
            <a href='<?php echo $organizationLink;?>courses' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/courses.svg" alt="Courses">
              <p class="learner-content__card-title">Courses</p>
            </a>

            <a href='<?php echo $organizationLink;?>apprenticeships' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/apprenticeships.svg" alt="Apprenticeships">
              <p class="learner-content__card-title">Apprenticeships</p>
            </a>

            <a href='<?php echo $organizationLink;?>learnermanagement' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/learner-management.svg" alt="Learner Management">
              <p class="learner-content__card-title">Learner Management</p>
            </a>

            <a href='<?php echo $organizationLink;?>learnermanagement' class="learner-content__card" data-bs-toggle="modal" data-bs-target="#addDocModal">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/add-document.svg" alt="Add Document">
              <p class="learner-content__card-title">Add Document</p>
            </a>

            <a href='<?php echo $organizationLink;?>careerpathways' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/career-pathways.svg" alt="Career Pathways">
              <p class="learner-content__card-title">Career Pathways</p>
            </a>

            <a href='<?php echo $organizationLink;?>receivedapplication' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/receive-applications.svg" alt="Received Applications">
              <p class="learner-content__card-title">Received Applications</p>
            </a>

            <a href='<?php echo $organizationLink;?>webinarscatalogue' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/workshops-webinars-catalogue.svg" alt="Workshops Webinars Catalogue">
              <p class="learner-content__card-title">Workshops Webinars Catalogue</p>
            </a>

            <a href='<?php echo $organizationLink;?>coursescatalogue' class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/e-learning-courses-catalogue.svg" alt="E-Learning Courses Catalogue">
              <p class="learner-content__card-title">E-Learning Courses Catalogue</p>
            </a>

            <a href='<?php echo $organizationLink;?>userguide'  class="learner-content__card">
            <img src="<?php echo FlexiEdu_Courses_URL ?>assets/images/user-guides-downloads.svg" alt="User Guides and Downloads">
              <p class="learner-content__card-title">User Guides and Downloads</p>
            </a>
          </div>
      </div>
    </section>