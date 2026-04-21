<?php
/**
 * HTMl for the Gradebook generic admin page.
 *
 * @since 1.1.0
 *
 * @var array $sections
 * @var array $active_section
 *
 * @package LearnDash_Gradebook
 */

defined( 'ABSPATH' ) || die();
?>

<div class="wrap">

	<?php if ( count( $sections ) > 1 ) : ?>
		<ul class="subsubsub"> <?php // cspell: disable-line. ?>
			<?php foreach ( $sections as $i => $section ) : ?>
				<li>
					<a href="<?php echo admin_url( "admin.php?page={$_GET['page']}&section={$section['id']}" ); ?>"
						<?php echo $section['id'] == $active_section['id'] ? 'class="current"' : ''; ?>>
						<?php echo isset( $section['tab_label'] ) ? $section['tab_label'] : $section['label']; ?>
					</a>

					<?php echo $i + 1 !== count( $sections ) ? ' | ' : ''; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<div style="clear: both;"></div>

	<?php endif; ?>

	<h2><?php echo $active_section['label']; ?></h2>

	<?php settings_errors(); ?>

	<?php
	if ( is_callable( $active_section['callback'] ) ) {
		call_user_func( $active_section['callback'], $active_section );
	}
	?>
</div>
