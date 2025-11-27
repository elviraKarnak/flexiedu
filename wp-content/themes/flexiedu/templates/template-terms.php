<?php 
/**
* Template Name: Page: Terms & Condition
**/
get_header();     


    $dummyImg = get_template_directory_uri().'/assets/images/inner-banner.jpg';

    if(has_post_thumbnail()){
        $bannerImg = get_the_post_thumbnail_url();
    }else{
         $bannerImg = $dummyImg;
    }

    $banner_title_os = get_field('banner_title_tp');

     $title_au = get_field('title_au');
     $sub_title_au = get_field('sub_title_au');



?>



    <section class="inner-banner position-relative" style="background-image: url(<?php echo $bannerImg; ?>);">
        <?php if($banner_title_os){ ?>
        <div class="container">
            <h1><?php echo $banner_title_os; ?></h1>
        </div>
        <?php } ?>
    </section>



<?php if(have_rows('terms_&_privacy_policy')){ ?>

  <section class="terms-privacy-policy space-mr">
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <div class="policy-list sticky-top">
            <ul>
            <?php 
            
            $i= 0; 
            
            while(have_rows('terms_&_privacy_policy')): the_row(); 

                $name = get_sub_field('title_sin');

                if($name){
                    $slug = sanitize_title($name);
                }
                
                if($i == 0){
                    $atv = "active";
                }else{
                    $atv = "";
                }
                if($name && $slug){
                echo '<li class="'. $atv .'"><a href="#'.$slug.'">'.$name.'</a></li>';  
                }
                $i++; 
            endwhile; ?> 

            </ul>
          </div>

        </div>
        <div class="col-md-8">
          <div class="col-wrap">
            <?php 
            
                $i= 0; 
            
                while(have_rows('terms_&_privacy_policy')): the_row(); 

                $name = get_sub_field('title_sin');
                $content = get_sub_field('content_editor_sin');

                if($name){
                    $slug = sanitize_title($name);
                }
                
                if($i == 0){
                    $atv = "active";
                }else{
                    $atv = "";
                }
                if(($name && $slug) || $content){?>


                <div class="col-blk" id="<?php echo $slug; ?>">
                    <?php if($name){ ?>
                        <h2><?php echo $name; ?></h2>
                    <?php } ?>

                     <?php if($content){ echo $content;  } ?>
                    
                </div>
                
                <?php }
                $i++; 
            endwhile; ?> 
        
          </div>

        </div>
      </div>
    </div>
  </section>
<?php } ?>

<?php get_footer(); ?>