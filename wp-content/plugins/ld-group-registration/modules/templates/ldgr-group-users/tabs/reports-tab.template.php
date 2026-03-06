<?php
/**
 * Reports tab contents display template.
 *
 * @since 4.0.0
 * @version 4.3.15
 *
 * @package LearnDash\Seats_Plus
 */

defined( 'ABSPATH' ) || exit;

use LdGroupRegistration\Modules\Classes\Ld_Group_Registration_Reports;

Ld_Group_Registration_Reports::get_group_report( $group_id, $content['id'] );
