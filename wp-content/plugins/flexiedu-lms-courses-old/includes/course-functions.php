<?php 
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {

    ob_start();
    ?>
    <span class="cart-count badge rounded-pill position-absolute top-0 start-100 translate-middle"
          style="background:#B1D95A;">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php

    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;

});

add_action( 'wp_enqueue_scripts', function () {

    if ( function_exists( 'is_cart' ) && is_cart() ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }

});


add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {

    $count = WC()->cart->get_cart_contents_count();

    ob_start();
    if ( $count > 0 ) :
    ?>
        <span class="cart-count badge rounded-pill position-absolute top-0 start-100 translate-middle"
              style="background:#B1D95A;">
            <?php echo $count; ?>
        </span>
    <?php
    endif;

    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;

});

// Shop / Archive button
add_filter( 'woocommerce_product_add_to_cart_text', 'flexiedu_change_add_to_cart_text', 10, 2 );
function flexiedu_change_add_to_cart_text( $text, $product ) {
    return 'Enroll Now';
}

// Single product page button
add_filter( 'woocommerce_product_single_add_to_cart_text', 'flexiedu_change_single_add_to_cart_text', 10, 2 );
function flexiedu_change_single_add_to_cart_text( $text, $product ) {
    return 'Enroll Now';
}

/**
 * Hide wp-login.php unless ?flexiedu-admin is present
 */
add_action('init', function () {

    global $pagenow;

    // Only target wp-login.php
    if ($pagenow !== 'wp-login.php') {
        return;
    }

    // Allow login form if secret parameter exists
    if (isset($_GET['flexiedu-admin'])) {
        return;
    }

    // Allow actual login submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return;
    }

    // Allow logout and password reset actions
    if (!empty($_GET['action']) && in_array($_GET['action'], [
        'logout',
        'lostpassword',
        'rp',
        'resetpass'
    ], true)) {
        return;
    }

    // Redirect everyone else to Woo My Account
    if (class_exists('WooCommerce')) {
        $myaccount_url = wc_get_page_permalink('myaccount');
        if ($myaccount_url) {
            wp_safe_redirect($myaccount_url);
            exit;
        }
    }

    // Fallback
    wp_safe_redirect(home_url());
    exit;

});