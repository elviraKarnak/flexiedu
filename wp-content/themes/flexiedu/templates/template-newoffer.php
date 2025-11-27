<?php 
/**
* Template Name: Page: New Offer
**/
get_header();     

    $dummyImg = get_template_directory_uri().'/assets/images/inner-banner.jpg';

    if(has_post_thumbnail()){
        $bannerImg = get_the_post_thumbnail_url();
    }else{
         $bannerImg = $dummyImg;
    }

    $banner_title_os = get_field('title_banner');
    $title_no = get_field('title_no');
    $description_no = get_field('description_no');
    $link_no = get_field('link_no');
?>

    <section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
        <?php if($banner_title_os){ ?>
        <div class="container">
            <h1><?php echo $banner_title_os; ?></h1>
        </div>
        <?php } ?>
    </section>


  <section class="new-offer space-mr">
    <div class="container">


      <div class="row justify-content-between gy-md-0 gy-4">
        <div class="col-md-5">
          
        <?php if($title_no){ ?>
          <h2 class="mb-4"><?php echo $title_no; ?></h2>
          <?php } ?>
          <?php if($link_no){ ?>
          <a href="<?php echo $link_no['url']; ?>" class="btn btn-custom mx-auto" target="_blank">
            <span> <?php echo $link_no['title']; ?></span>

          </a>
          <?php } ?>
        </div>
         <?php if($description_no){ ?>
          <div class="col-md-5">
            <p><?php echo $description_no; ?></p>
          </div>
        <?php } ?>
      </div>

      <div class="masonry-gallery">

      <?php while ( have_rows('document_gallery') ): the_row();
         
         $title_doc = get_sub_field('title_doc');
         $coverImage = get_sub_field('cover_image_box');

         $doctype = get_sub_field('doc_type_box');

         $url_box = '';
         $buttonBox = "";

         if($doctype == 'vid'){
          $url_box = get_sub_field('video_or_document__url_box');
          $buttonBox = '<div class="play-button-ms">
                  <span><img src="'. get_template_directory_uri().'/assets/images/play.svg" alt=""></span>
                </div>';
         }else if($doctype == 'img'){
          $url_box = $coverImage['url'];
          $buttonBox = "";
         }else if($doctype == 'pdf'){
          $url_box = get_sub_field('pdf_url');
          $buttonBox = '<div class="play-button-ms play-btn-pdf">
                  <span><img src="'. get_template_directory_uri().'/assets/images/pdf_btn.svg" alt=""></span>
                </div>';
         }

         

         
      
      
      ?>

          <div class="gallery-item">
            <a data-fancybox="" data-src="<?php echo $url_box; ?>" >


              <div class="course-image-ms">

                <img src="<?php echo $coverImage['url']; ?>" alt="<?php echo $coverImage['alt']; ?>">

                <?php echo $buttonBox;  ?>

                <h4><?php echo $title_doc; ?></h4>

              </div>
            </a>
          </div>

          <?php endwhile; ?>

        <!-- IMAGE ITEM
        <div class="gallery-item">

          <div class="course-image-ms">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas1.jpg" alt="Course Image">
              <h4>Course Image</h4>
          </div>
        </div>

        <!-- VIDEO ITEM -->
        <!-- <div class="gallery-item">
          <div class="course-image-ms">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas2.jpg" alt="Course Image">
              <h4>Course Image</h4>
          </div>
        </div> -->

        <!-- EXTERNAL LINK ITEM -->
        <!-- <div class="gallery-item">
          <a data-fancybox="" data-src="https://www.youtube.com/watch?v=ScMzIvxBSi4" >
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas4.jpg" alt="Course Image">

              <div class="play-button-ms">
                <span><img src="<?php echo get_template_directory_uri(); ?>/assets/images/play.svg" alt=""></span>
              </div>

              <h4>Course Poster</h4>

            </div>
          </a>
        </div> -->

        <!-- <div class="gallery-item">
          <a data-fancybox="" data-src="https://www.youtube.com/watch?v=ScMzIvxBSi4" >
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas4.jpg" alt="Course Image">

              <div class="play-button-ms">
                <span><img src="<?php echo get_template_directory_uri(); ?>/assets/images/play.svg" alt=""></span>
              </div>

              <h4>Course Video</h4>

            </div>
          </a>
        </div> -->

        <!-- <div class="gallery-item">
         
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas5.jpg" alt="Course Image">

             

              <h4>Course Document</h4>

            </div>
      
        </div> -->
        <!-- <div class="gallery-item">
        
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas6.jpg" alt="Course Image">

             

              <h4>Course Poster</h4>

            </div>
         
        </div> -->
        <!-- <div class="gallery-item">
          <a data-fancybox="" data-src="https://www.youtube.com/watch?v=ScMzIvxBSi4" >
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas7.jpg" alt="Course Image">

              <div class="play-button-ms">
                <span><img src="<?php echo get_template_directory_uri(); ?>/assets/images/play.svg" alt=""></span>
              </div>

              <h4>Course Video</h4>

            </div>
          </a>
        </div> -->
        <!-- <div class="gallery-item">
         
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas8.jpg" alt="Course Image">

             

              <h4>Course Image</h4>

            </div>
         
        </div> -->
        <!-- <div class="gallery-item">
          <a href="#">
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas10.jpg" alt="Course Image">

              <div class="play-button-ms">
                <span><img src="<?php echo get_template_directory_uri(); ?>/assets/images/green-arrow.svg" alt=""></span>
              </div>

              <h4>Course Document</h4>

            </div>
          </a>
        </div> -->
        <!-- <div class="gallery-item">
        
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas9.jpg" alt="Course Image">

            

              <h4>Course Poster</h4>

            </div>
          
        </div> -->

        <!-- <div class="gallery-item">
         
            <div class="course-image-ms">
              <img src="<?php echo get_template_directory_uri(); ?>/assets/images/mas11.jpg" alt="Course Image">

           

              <h4>Course Document</h4>

            </div>
         
        </div> --> 

        <!-- Add as many items as you want -->
      </div>

    </div>
  </section>

<?php get_footer(); ?>