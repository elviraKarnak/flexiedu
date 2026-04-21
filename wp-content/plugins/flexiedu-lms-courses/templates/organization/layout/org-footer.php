<!-- add document modal start -->



       <!-- Modal -->
 <!-- Bootstrap Modal -->
    <div class="modal fade" id="liveClassModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="liveClassModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p id="liveClassModalTime"></p>
                </div>

                <div class="modal-footer">
                    <a id="liveClassModalJoinBtn" href="#" target="_blank" class="btn btn-primary">
                        <?php esc_html_e('Join Class', 'flexiedu-lms-courses'); ?>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <?php
        $title_ftrcta = get_field('title_ftrcta', 'option');
    $footer_link_ftrcta = get_field('footer_link_ftrcta', 'option');
    $footer_logo_ftr = get_field('footer_logo_ftr', 'option');

    ?>

<footer>
    <div class="container">
      <?php 

          $privacy_policy_ftr = get_field('privacy_policy_ftr', 'option');
          $policies_ftr = get_field('policies_ftr', 'option');
          $copyright_text = get_field('copyright_text', 'option');
      
      
      ?>
      <!-- Footer Bottom -->
      <div class="footer-bottom">
        <div class="footer-bottom-content">
          <?php if($copyright_text ){ ?>
          <div class="copyright">
             <?php echo $copyright_text; ?>
          </div>
          <?php } ?>
          <?php 
          
          $developer_name = get_field('developer_name', 'option');
          $developer_link = get_field('developer_link', 'option');
          $developer_logo_img = get_field('developer_logo_img', 'option');
          
          ?>
          <?php if($developer_name || $developer_link || $developer_logo_img){ ?>
            <div class="developer-credit">
              <span><?php echo $developer_name; ?></span>
              <span class="developer-logo">
                <a href="<?php echo $developer_link; ?>" target="_blank">
                  <img src="<?php echo $developer_logo_img['url']; ?>" alt="elvirainfortech">
                </a></span>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </footer>
