 <?php 

 $sub_title_tst_flexiedu = get_field('sub_title_tst_flexiedu', 'option');
 $title_tst_flexiedu = get_field('title_tst_flexiedu', 'option');
 $description_tst_flexiedu = get_field('description_tst_flexiedu', 'option');
 $author_box_tst_flexiedu = get_field('author_box_tst_flexiedu', 'option');
 $number_sts_lnr_tst_flexiedu = get_field('number_sts_lnr_tst_flexiedu', 'option');
 $text_sts_lnr_tst_flexiedu = get_field('text_sts_lnr_tst_flexiedu', 'option');
 

 ?>

<?php if(have_rows('testimonials_flexiedu', 'option')){ ?>

    <div class="testimonials-section space-mr">
        <div class="container">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div class="cnt-lft text-start">
                <?php if($sub_title_tst_flexiedu) { ?> 
                        <div class="top-title"><?php echo $sub_title_tst_flexiedu; ?></div>
                    <?php } ?>
                    <?php if($title_tst_flexiedu) { ?> 
                        <h2 class="sc-hd"><?php echo $title_tst_flexiedu; ?></h2>
                    <?php } ?>
                    <?php if($description_tst_flexiedu) { ?> 
                        <p><?php echo $description_tst_flexiedu; ?></p>
                    <?php } ?>
                </div>


                <div class="stats-box">
                <?php if($author_box_tst_flexiedu){ ?>  
                <div class="top-item d-flex">
                    <div class="user-avatars">
                    <?php foreach($author_box_tst_flexiedu as $img){ ?> 
                        <img src="<?php echo $img['url']; ?>" alt="<?php echo $img['alt']; ?>">
                    <?php } ?>
                    </div>
                    <div class="stat-item">
                        <?php if($number_sts_lnr_tst_flexiedu){ ?>
                            <div class="stat-number"><?php echo $number_sts_lnr_tst_flexiedu; ?></div>
                        <?php } ?>
                        <?php if($text_sts_lnr_tst_flexiedu){ ?>
                            <div class="stat-label"><?php echo $text_sts_lnr_tst_flexiedu; ?></div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>



                <div class="stat-item start-item2 d-flex align-items-center">
                        <?php if($star_ratings_tst_flexiedu){ ?>
                            <div class="stat-number"><?php echo $star_ratings_tst_flexiedu; ?></div>
                        <?php } ?>
                        <?php if($feedback_tst_flexiedu){ ?>
                            <div class="stat-label"><?php echo $feedback_tst_flexiedu; ?></div>
                        <?php } ?>
                </div>
                </div>
            </div>

            <div class="owl-carousel owl-theme testimonials-carousel">

            <?php while (have_rows('testimonials_flexiedu', 'option')) { 
                  
                  the_row();

                    $star_ratings_tst_sin = get_sub_field('star_ratings_tst_sin');
                    $testimonials_content_sin = get_sub_field('testimonials_content_sin');
                    $author_name_sin = get_sub_field('author_name_sin');
        
                    $author_designation_sin = get_sub_field('author_designation_sin');
                    $author_image_sin = get_sub_field('author_image_sin');
                ?>
        
                <!-- Testimonial 1 -->
                <div class="testimonial-card">
                    <?php if($star_ratings_tst_sin){ ?>
                        <div class="testimonial-stars">
                            <?php echo $star_ratings_tst_sin; ?>
                        </div>
                    <?php } ?>

                    <?php if($testimonials_content_sin ){ ?>
                        <p class="testimonial-text"><?php echo $testimonials_content_sin; ?></p>
                    <?php } ?>
                   
                    <div class="testimonial-author">
                        <?php if($author_image_sin ){ ?>
                            <img src="<?php echo $author_image_sin; ?>" alt="<?php echo $author_name_sin; ?>" class="author-image">
                        <?php } ?>
                        <div class="author-info">
                         <?php if($author_name_sin){ ?>   
                            <h5><?php echo $author_name_sin; ?></h5>
                         <?php } ?>   
                         <?php if($author_designation_sin){ ?>
                            <p><?php echo $author_designation_sin; ?></p>
                        <?php } ?>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>    