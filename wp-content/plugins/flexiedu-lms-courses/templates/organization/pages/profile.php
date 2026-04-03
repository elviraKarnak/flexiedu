<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$avatar_id = get_user_meta($user_id, 'profile_image', true);
?>

<div class="profile-tab">

    <h3>Account Details</h3>

    <form id="profile-form" enctype="multipart/form-data">

        <?php wp_nonce_field('save_account_details', 'account_nonce'); ?>

        <!-- Profile Image -->
        <div class="profile-image">
            <?php
            if ($avatar_id) {
                echo wp_get_attachment_image($avatar_id, [120,120], false, ['id' => 'profile-preview']);
            } else {
                echo get_avatar($user_id, 120, '', '', ['id' => 'profile-preview']);
            }
            ?>
            <input type="file" name="profile_image" accept="image/*">
        </div>

        <!-- Name -->
        <div class="row">
            <div class="col">
                <label>First Name</label>
                <input type="text" name="first_name"
                    value="<?php echo esc_attr(get_user_meta($user_id, 'first_name', true)); ?>">
            </div>

            <div class="col">
                <label>Last Name</label>
                <input type="text" name="last_name"
                    value="<?php echo esc_attr(get_user_meta($user_id, 'last_name', true)); ?>">
            </div>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="user_email"
                value="<?php echo esc_attr($current_user->user_email); ?>">
        </div>

        <!-- Password -->
        <div class="form-group">
            <label>New Password</label>
            <input type="password" name="new_password">
        </div>

        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password">
        </div>

        <!-- Extra -->
        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone"
                value="<?php echo esc_attr(get_user_meta($user_id, 'phone', true)); ?>">
        </div>

        <button type="submit">Save Changes</button>

        <div id="profile-msg"></div>

    </form>
</div>


<script>
jQuery(document).ready(function($){

    // Image preview
    $('input[name="profile_image"]').on('change', function(){
        let reader = new FileReader();

        reader.onload = function(e){
            $('#profile-preview').attr('src', e.target.result);
        }

        reader.readAsDataURL(this.files[0]);
    });

    // Form submit
    $('#profile-form').on('submit', function(e){
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'flexi_save_profile');

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,

            beforeSend: function(){
                $('#profile-msg').html('<p>Saving...</p>');
            },

            success: function(response){
                if(response.success){
                    $('#profile-msg').html('<p style="color:green;">'+response.data+'</p>');
                    location.reload();
                } else {
                    $('#profile-msg').html('<p style="color:red;">'+response.data+'</p>');
                }
            }
        });
    });

});
</script>