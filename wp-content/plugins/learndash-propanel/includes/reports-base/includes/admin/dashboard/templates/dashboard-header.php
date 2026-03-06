<?php
/**
 * Dashboard Header Template
 *
 * @package LearnDash\Reports
 *
 * @since 3.0.0
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

// Header content on the dashboard menu pages.
$logo_url = WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/images/learndash_logo.svg';
?>

<div class='wrld-dashboard-header'>
	<div class="wrld-header-title-container">
		<div>
			<span class='wrld-header-title-main'> <?php esc_attr_e( 'ProPanel', 'learndash-reports-pro' ); ?> </span>
		</div>
	</div>
</div>
