 <?php
     global $wpdb;

   $organizationID =  $organization->ID;  

   $unAuthorizedMessage = get_field('unauthorized_text', 'option'); 


   $havePermission = get_post_meta($organizationID, 'accept_application_oc', true);

    //var_dump($havePermission);

  if(!$havePermission){
      echo '<div class="alert alert-warning" role="alert">
      ' . esc_html__($unAuthorizedMessage, 'flexiedu-lms-courses') . '
    </div>';
      return;
  }


     $table = $wpdb->prefix . 'flexi_enrollment_requests';

      $requested = $wpdb->get_results($wpdb->prepare(
          "SELECT * FROM $table 
          WHERE organization_id = %d ",
          $organizationID
      ));

      // echo "<pre>";
      // print_r($requested);
      // echo "</pre>";

 ?>


 
  <?php if($requested) { ?>
    
    <table id="receivedApplicationsTable" class="table table-striped received-aplications-table" >
          <thead>
            <tr>
              <th scope="col"><?php _e('Sr','flexiedu-lms-courses' ); ?></th>
              <th scope="col"><?php _e('Course Name','flexiedu-lms-courses' ); ?></th>
              <th scope="col"><?php _e('Candidate Name','flexiedu-lms-courses' ); ?></th>
              <th scope="col"><?php _e('Status','flexiedu-lms-courses' ); ?></th>
              <th scope="col"><?php _e('Action','flexiedu-lms-courses' ); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $i = 1;
              foreach($requested as $request) {
                
                  $course_name = get_the_title($request->course_id);
                  $candidate_name = get_user_meta($request->user_id, 'first_name', true) . ' ' . get_user_meta($request->user_id, 'last_name', true);
                ?>
                <tr>
                  <td scope="row"><?php echo $request->id; ?></td>
                  <td scope="row"><?php echo $course_name; ?></td>
                  <td scope="row"><?php echo $candidate_name; ?></td>
                  <td scope="row"><?php echo $request->status; ?></td>
                  <td scope="row" class="action-btns">
                  <?php if($request->status == 'pending'){?>
                    <?php echo "<a href='javascript:void(0);' data-row-id='" . esc_attr($request->id) . "' class='btn btn-success accept-application'>" . esc_html__('Accept', 'flexiedu-lms-courses') . "</a>"; ?>
                    <?php echo "<a href='javascript:void(0);' data-row-id='" . esc_attr($request->id) . "' class='btn btn-danger reject-application'>" . esc_html__('Reject', 'flexiedu-lms-courses') . "</a>"; ?>
                  <?php } else { echo "<p class='no-action-message'>".__("No Action Required.",'flexiedu-lms-courses')."</p>"; } ?>

                  </td>
                </tr>

              <?php $i++; } ?>
          </tbody>
    </table>

    <?php } else { ?>
        <div class="row">
          <div class="col-12">
            <div class="alert alert-info"><?php _e('No applications found','flexiedu-lms-courses' ); ?></div>
          </div>
        </div>
    <?php } ?>


    <?php if($requested) { ?>

    
      <!-- Modal -->
      <div class="modal fade" id="formRejectModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="formRejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="formRejectModalLabel"><?php _e('Reject Application','flexiedu-lms-courses' ); ?></h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body formactionModalBody">
              <form class="reject_application">

              <div class="form-group">
                 <label for="apply_after" class="col-sm-2 col-form-label w-100"><?php esc_html_e('Apply After (in days)', 'flexiedu-lms-courses'); ?></label>
                  <input  class="form-control type="number" placeholder="<?php echo esc_attr__('Number in days', 'flexiedu-lms-courses'); ?>" name="apply_after" id="apply_after" required>
                
                  <input type="hidden" name="row_id" id="row_id" required>
                  <input type="hidden" name="action" value="reject_application" required>
                  <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('flexiedu_nonce')); ?>" required>
              </div>
                  
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-danger"><?php esc_html_e('Reject Application', 'flexiedu-lms-courses'); ?></button>
                    </div>
              </form>
            </div>
          </div>
        </div>
      </div>


    <?php } ?>
