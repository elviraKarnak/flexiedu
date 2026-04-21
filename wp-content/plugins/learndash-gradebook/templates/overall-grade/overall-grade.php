<?php
/**
 * Template for the Overall Grade
 *
 * @since 1.6.4
 * @updated 3.0.0
 * @version 4.3.2
 *
 * @var LD_GB_UserGrade $user_grade
 * @var integer         $gradebook_id
 * @var string          $format
 */

defined( 'ABSPATH' ) || die();

// Set the wrapper class using the block attributes if it's a block
if ( $is_block ) {
	$class = 'ld-gb-overall-grade wp-block-report-card-block';

	$wrapper_attributes = get_block_wrapper_attributes( [ 'class' => $class ] );
} else {
	$wrapper_attributes = 'class="ld-gb-overall-grade"';
}
?>

<span <?php echo $wrapper_attributes; ?>>
	<?php $user_grade->display_user_grade( $format ); ?>
</span>
