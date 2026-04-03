 <?php
     global $wpdb;

   $organizationID =  $organization->ID;  

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
                    <?php echo "<a href='javascript:void(0);' data-row-id='".$request->id."' class='btn btn-success accept-application'>Accept</a>"; ?>
                    <?php echo "<a href='javascript:void(0);' data-row-id='".$request->id."' class='btn btn-danger reject-application'>Reject</a>"; ?>
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
                 <label for="apply_after" class="col-sm-2 col-form-label w-100">Apply After (in days)</label>
                    <input  class="form-control type="number" placeholder="Number In days" name="apply_after" id="apply_after" required>
                
                  <input type="hidden" name="row_id" id="row_id" required>
                  <input type="hidden" name="action" value="reject_application" required>
              </div>
                  
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-danger">Reject Application</button>
                    </div>
              </form>
            </div>
          </div>
        </div>
      </div>


      <script>
        jQuery(document).ready(function($) {

          var ajaxUrl = "<?php echo admin_url('admin-ajax.php');?>";

          $(".received-aplications-table td.action-btns").each(function() {

         
              $(this).find(".accept-application").on("click", function(e) {

                  var viewButton = $(this);

                  var rowId = viewButton.data("row-id");

                      $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: {action: 'accept_application', rowId: rowId},

                        success: function (res) {

                        if (res.success) {

                            // Title
                            console.log(res)
                             var courseId = res.data.course_id;

                             window.location.reload();
                            

                        } else {
                            alert(res.data || 'Failed to load');
                        }

                        }
                    });

                  //console.log(rowId);
              });


              $(this).find(".reject-application").on("click", function(e) {

                  var viewButton = $(this);

                  var rowId = viewButton.data("row-id");

                  $("#row_id").val(rowId);
                  $('#formRejectModal').modal('show');           
              });

            });

            $(".reject_application").on('submit', function(e){
                e.preventDefault();
                var data = $(this).serialize();

                  $.ajax({
                        url: ajaxUrl,
                        type: 'POST',
                        data: data,

                        success: function (res) {

                        if (res.success) {

                            // Title
                            console.log(res)
                             //var courseId = res.data.course_id;

                             window.location.reload();
                            

                        } else {
                            alert(res.data || 'Failed to load');
                        }

                        }
                    });
              })

            $('#receivedApplicationsTable').DataTable();
        });
      </script>
    <?php } ?>