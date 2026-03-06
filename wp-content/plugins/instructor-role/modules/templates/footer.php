<?php
/**
 * Outputs the footer for Instructor Role frontend templates.
 *
 * @since 5.9.3
 * @version 5.9.3
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;

if ( wp_is_block_theme() ) : ?>
			<footer class="wp-block-template-part">
				<?php block_footer_area(); ?>
			</footer>
			<?php wp_footer(); ?>
		</body>
	</html>
<?php else : ?>
	<?php get_footer(); ?>
	<?php
endif;
