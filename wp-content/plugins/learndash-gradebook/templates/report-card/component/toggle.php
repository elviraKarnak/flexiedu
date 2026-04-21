<?php
/**
 * Report card component toggle.
 *
 * @since 1.1.0
 * @updated 1.6.0
 * @version 4.3.2
 *
 * @var array $component
 * @var LD_GB_UserGrade $user_grade
 * @var int $gradebook_id
 * @var string $component_handle
 *
 * @package LearnDash_Gradebook
 *
 * cspell:ignore flippable .
 */

defined( 'ABSPATH' ) || die();

?>

<?php if ( $component['grades'] ) : ?>
	<div class="ld-gb-report-card-component-expand list_arrow collapse flippable"
		onclick='return flip_expand_collapse("#ld-gb-report-card-<?php echo $gradebook_id; ?>-component", "<?php echo $component_handle; ?>");'></div>
<?php else : ?>
	<div class="ld-gb-report-card-component-expand collapse"
		onclick='return flip_expand_collapse("#ld-gb-report-card-<?php echo $gradebook_id; ?>-component", "<?php echo $component_handle; ?>";'></div>
<?php endif; ?>
