<?php  ob_start(); ?>

    <form id="flexi-register-form" enctype="multipart/form-data">

        <div id="flexi-msg"></div>

        <?php wp_nonce_field('flexiedu_register_action', 'flexiedu_nonce'); ?>

        <div class="row">

            <!-- First + Last Name -->
            <div class="col-md-6 mb-3">
                <input type="text" name="first_name" class="form-control"
                    placeholder="<?php _e('First Name', 'flexiedu-lms-courses'); ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <input type="text" name="last_name" class="form-control"
                    placeholder="<?php _e('Last Name', 'flexiedu-lms-courses'); ?>" required>
            </div>

            <!-- Org + Email -->
            <div class="col-md-6 mb-3">
                <input type="text" name="org_name" class="form-control"
                    placeholder="<?php _e('Organization Name', 'flexiedu-lms-courses'); ?>" required>
            </div>

            <div class="col-md-6 mb-3">
                <input type="email" name="email" id="email" class="form-control"
                    placeholder="<?php _e('Email', 'flexiedu-lms-courses'); ?>" required>
                <div id="email-msg"></div>
            </div>

            <!-- Username + Password -->
            <div class="col-md-6 mb-3">
                <input type="text" name="username" id="username" class="form-control"
                    placeholder="<?php _e('Username', 'flexiedu-lms-courses'); ?>" required>
                <div id="username-msg"></div>
            </div>

            <div class="col-md-6 mb-3">
                <input type="password" name="password" class="form-control"
                    placeholder="<?php _e('Password', 'flexiedu-lms-courses'); ?>" required>
            </div>

            <!-- Image + Bio -->
            <div class="col-md-6 mb-3">
                <input type="file" name="profile_pic" class="form-control">
            </div>

            <div class="col-md-6 mb-3">
                <textarea name="bio" class="form-control"
                    placeholder="<?php _e('Bio (optional)', 'flexiedu-lms-courses'); ?>"></textarea>
            </div>

            <!-- Submit -->
            <div class="col-12">
                <button type="submit" class="btn btn-primary w-100">
                    <?php _e('Register', 'flexiedu-lms-courses'); ?>
                </button>
            </div>

        </div>

    </form>



<script>

    jQuery(document).ready(function($){

        function showMain(msg, type){
            $('#flexi-msg').html(
                '<div class="alert alert-' + type + '">' + msg + '</div>'
            );
        }

        // FORM SUBMIT
        $('#flexi-register-form').on('submit', function(e){

            e.preventDefault();

            let formData = new FormData(this);
            formData.append('action', 'flexi_register_user');

            showMain('Processing...', 'info');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res){

                        if(res.success){

                            showMain(res.data.message, 'success');

                            setTimeout(function(){
                                window.location.href = res.data.redirect;
                            }, 1000);

                        } else {
                            showMain(res.data, 'danger');
                        }

                    },
                    error: function(){
                        showMain('Something went wrong', 'danger');
                    }
                });

            });

});



</script>



<?php echo ob_get_clean(); ?>