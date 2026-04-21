<?php
if (!current_user_can('manage_options')) {
    return '<p>Unauthorized</p>';
}

$query = new WP_Query([
    'post_type'      => 'organization',
    'posts_per_page' => -1,
    'post_status'    => 'publish'
]);

ob_start();



?>

<div class="card p-4">

    <h4 class="mb-3">Organization Feature Control</h4>

    <!-- Organization Select -->
    <div class="mb-3">
        <select id="orgSelect" class="form-control">
            <option value="">Select Organization</option>

            <?php if ($query->have_posts()): ?>
                <?php while ($query->have_posts()): $query->the_post(); ?>
                    <option value="<?php echo get_the_ID(); ?>">
                        <?php echo esc_html(get_the_title()); ?>
                    </option>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

        </select>
    </div>

   <div id="data_box"></div>
</div>
<script>
   jQuery(function ($) {

        $('#orgSelect').on('change', function () {

            var org_id = $(this).val();

            jQuery.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                method: 'POST',
                data: {
                    action: 'get_org_features',
                    org_id: org_id,
                    nonce: '<?php echo wp_create_nonce('flexiedu_nonce'); ?>'
                },
                success: function (response) {
                    //if (response.success) {

                         $('#data_box').html(response);
        

                        // $('#org_id').val(org_id);
                        // $('#featureBox input[type="checkbox"]').prop('checked', false);

                        // $.each(response.data.features, function (feature, enabled) {
                        //     $('#featureBox input[name="features[' + feature + ']"]').prop('checked', enabled);
                        // });

                        //console.log(response.data);
                   // }
                }
            });

        });

        
        jQuery(document).on('submit', '#featureBox', function (e) {

            e.preventDefault();

            let form = jQuery(this);
            let formData = {};

            // 🔹 Get org_id
            formData.org_id = jQuery("#org_id").val();

            // 🔹 Loop checkboxes
            form.find("input[type='checkbox']").each(function () {
                let name = jQuery(this).attr("name");
                formData[name] = jQuery(this).is(":checked") ? 1 : 0;
            });

            formData.nonce = jQuery(this).find("input[name='nonce']").val();
            formData.action = jQuery(this).find("input[name='action']").val();

              console.log("FORM DATA:", formData);

            $.ajax({
                url:'<?php echo admin_url('admin-ajax.php'); ?>',
                method:'POST',
                data:formData,
                success:function(response){
                    console.log("AJAX RESPONSE:", response);
                    if(response.success){
                        alert('Features updated successfully!');
                       // location.reload();
                    }else{
                        alert('Error: ' + response.data);
                    }
                }

            });

        });

    });
</script>

<?php
echo ob_get_clean();