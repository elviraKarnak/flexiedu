<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$avatar_id = get_user_meta($user_id, 'profile_image', true);
?>

<div class="profile-tab">

    <h3><?php esc_html_e('Account Details', 'flexiedu-lms-courses'); ?></h3>

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
                <label><?php esc_html_e('First Name', 'flexiedu-lms-courses'); ?></label>
                <input type="text" name="first_name"
                    value="<?php echo esc_attr(get_user_meta($user_id, 'first_name', true)); ?>">
            </div>

            <div class="col">
                <label><?php esc_html_e('Last Name', 'flexiedu-lms-courses'); ?></label>
                <input type="text" name="last_name"
                    value="<?php echo esc_attr(get_user_meta($user_id, 'last_name', true)); ?>">
            </div>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label><?php esc_html_e('Email', 'flexiedu-lms-courses'); ?></label>
            <input type="email" name="user_email"
                value="<?php echo esc_attr($current_user->user_email); ?>">
        </div>

        <!-- Password -->
        <div class="form-group">
            <label><?php esc_html_e('New Password', 'flexiedu-lms-courses'); ?></label>
            <input type="password" name="new_password">
        </div>

        <div class="form-group">
            <label><?php esc_html_e('Confirm Password', 'flexiedu-lms-courses'); ?></label>
            <input type="password" name="confirm_password">
        </div>

        <!-- Extra -->
        <div class="form-group">
            <label><?php esc_html_e('Phone', 'flexiedu-lms-courses'); ?></label>
            <input type="text" name="phone"
                value="<?php echo esc_attr(get_user_meta($user_id, 'phone', true)); ?>">
        </div>

        <button type="submit"><?php esc_html_e('Save Changes', 'flexiedu-lms-courses'); ?></button>

        <div id="profile-msg"></div>

    </form>
</div>
