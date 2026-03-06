<?php
/**
 * Common pluggable functions
 *
 * @since 3.5.0
 * @package LearnDash\Instructor_Role
 * @author LearnDash
 */

defined( 'ABSPATH' ) || exit;

use LearnDash\Core\Utilities\Cast;

/**
 * Check whether the user role is instructor or not.
 *
 * @param int $user_id wp user id, if user_id is null then it considers current logged in user_id
 *
 * @return bool if instructor true, else false
 */
if ( ! function_exists( 'wdm_is_instructor' ) ) {
	/**
	 * Check if a user is an instructor
	 *
	 * @param int $user_id  ID of the User.
	 *
	 * @return bool         True if user is instructor, false otherwise.
	 */
	function wdm_is_instructor( $user_id = 0 ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		// v2.4.1 added condition to check is the user is instructor or not.
		$is_instructor = false;

		// check if get_userdata pluggable function present.
		if ( ! function_exists( 'get_userdata' ) ) {
			return false;
			// require_once ABSPATH . WPINC . '/pluggable.php';.
		}

		$user_info = get_userdata( $user_id );

		if ( $user_info && in_array( 'wdm_instructor', $user_info->roles ) ) {
			$is_instructor = true;
		}

		/**
		 * Filter check for instructors
		 *
		 * @param bool $is_instructor   True if current user is instructor, false otherwise.
		 */
		return apply_filters( 'wdm_check_instructor', $is_instructor );
	}
}

if ( ! function_exists( 'ir_category_build_tree' ) ) {
	/**
	 * Recursive function to build nested category data.
	 *
	 * @since 4.5.1
	 *
	 * @param array   $elements this is the json data array.
	 * @param integer $parent_id this is the parent id of the category.
	 *
	 * @return string $returns json string if empty of no data is present
	 */
	function ir_category_build_tree( array &$elements, $parent_id = 0 ) {
		$branch = [];

		foreach ( $elements as &$element ) {
			if ( $element['parent'] == $parent_id ) {
				$children = ir_category_build_tree( $elements, $element['id'] );

				$category_data = [
					'label' => $element['name'],
					'value' => strval( $element['id'] ),
				];

				if ( $children ) {
					$element['children']       = $children;
					$category_data['children'] = $children;
				}

				array_push( $branch, $category_data );
				unset( $element );
			}
		}

		return apply_filters( 'ir_category_build_tree', $branch );
	}
}

/**
 * Returns author id if post has author
 *
 * @param int $post_id post id of post
 * @return int author_id author id of post
 */
if ( ! function_exists( 'wdm_get_author' ) ) {
	function wdm_get_author( $post_id = null ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}
		if ( empty( $post_id ) ) {
			return;
		}

		$postdata = get_post( $post_id );

		if ( isset( $postdata->post_author ) ) {
			return $postdata->post_author;
		}
	}
}

/**
 * To search item in multidimensional array
 *
 * @param string  $needle   needle to find in haystack.
 * @param object  $haystack haystack to find needle in.
 * @param boolean $strict   strict value to check for strict comparison.
 */
function wdm_in_array( $needle, $haystack, $strict = false ) {
	foreach ( $haystack as $item ) {
		if ( ( $strict ? $item === $needle : $item == $needle ) || ( is_array( $item ) && wdm_in_array( $needle, $item, $strict ) ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Custom Author meta box to display on a edit post page.
 *
 * @param object $post  WP_Post object.
 */
function wdm_post_author_meta_box( $post ) {
	global $user_ID;
	?>
	<label class="screen-reader-text" for="post_author_override">
		<?php esc_html_e( 'Author', 'wdm_instructor_role' ); ?>
	</label>
	<?php
	$wdm_args = [
		'name'             => 'post_author_override',
		'selected'         => empty( $post->ID ) ? $user_ID : $post->post_author,
		'include_selected' => true,
	];
	/**
	 * Filter author arguments
	 *
	 * @since 1.0.0
	 *
	 * @param array $wdm_args   Array of arguments.
	 */
	$args = apply_filters( 'wdm_author_args', $wdm_args );
	wdm_wp_dropdown_users( $args );
}

/**
 * To create HTML dropdown element of the users for given argument.
 *
 * @param array $args   Array of arguments.
 */
function wdm_wp_dropdown_users( $args = '' ) {
	$defaults = [
		'show_option_all'         => '',
		'show_option_none'        => '',
		'hide_if_only_one_author' => '',
		'orderby'                 => 'display_name',
		'order'                   => 'ASC',
		'include'                 => '',
		'exclude'                 => '',
		'multi'                   => 0,
		'show'                    => 'display_name',
		'echo'                    => 1,
		'selected'                => 0,
		'name'                    => 'user',
		'class'                   => '',
		'id'                      => '',
		'include_selected'        => false,
		'option_none_value'       => -1,
	];

	$defaults['selected'] = wdmCheckAuthor( get_query_var( 'author' ) );

	$r_var             = wp_parse_args( $args, $defaults );
	$show              = $r_var['show'];
	$show_option_all   = $r_var['show_option_all'];
	$show_option_none  = $r_var['show_option_none'];
	$option_none_value = $r_var['option_none_value'];
	$output            = '';

	$query_args           = wp_array_slice_assoc( $r_var, [ 'blog_id', 'include', 'exclude', 'orderby', 'order' ] );
	$query_args['fields'] = [ 'ID', 'user_login', $show ];

	$users = array_merge( get_users( [ 'role' => 'administrator' ] ), get_users( [ 'role' => 'wdm_instructor' ] ), get_users( [ 'role' => 'author' ] ) );

	if ( ! empty( $users ) && ( count( $users ) > 1 ) ) {
		$name = esc_attr( $r_var['name'] );
		if ( $r_var['multi'] && ! $r_var['id'] ) {
			$idd = '';
		} else {
			$idd = wdmCheckAndGetId( $r_var['id'], $name );
		}
		$output = "<select name='{$name}'{$idd} class='" . $r_var['class'] . "'>\n";

		if ( $show_option_all ) {
			$output .= "\t<option value='0'>$show_option_all</option>\n";
		}

		if ( $show_option_none ) {
			$_selected = selected( $option_none_value, $r_var['selected'], false );
			$output   .= "\t<option value='" . esc_attr( $option_none_value ) . "'$_selected>$show_option_none</option>\n";
		}

		$found_selected = false;
		foreach ( (array) $users as $user ) {
			$user->ID  = (int) $user->ID;
			$_selected = selected( $user->ID, $r_var['selected'], false );
			if ( $_selected ) {
				$found_selected = true;
			}
			$display = wdmGetDisplayName( $user->$show, $user->user_login );
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
		}

		if ( $r_var['include_selected'] && ! $found_selected && ( $r_var['selected'] > 0 ) ) {
			$user      = get_userdata( $r_var['selected'] );
			$_selected = selected( $user->ID, $r_var['selected'], false );

			$display = wdmGetDisplayName( $user->$show, $user->user_login );
			$output .= "\t<option value='$user->ID'$_selected>" . esc_html( $display ) . "</option>\n";
		}

		$output .= '</select>';
	}
	wdmPrintOutput( $r_var['echo'], $output );

	return $output;
}

function wdmCheckAuthor( $query_var_author ) {
	if ( is_author() ) {
		return $query_var_author;
	}

	return 0;
}
function wdmGetDisplayName( $user_show, $user_login ) {
	if ( ! empty( $user_show ) ) {
		return $user_show;
	}

	return '(' . $user_login . ')';
}

/**
 * Returns the ID property of an HTML element.
 *
 * @since 3.5.0
 *
 * @param string $r_var_id ID of the element.
 * @param string $name     Name of the element.
 *
 * @return string
 */
function wdmCheckAndGetId( $r_var_id, $name ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid -- legacy code.
	if ( $r_var_id ) {
		return " id='" . esc_attr( $r_var_id ) . "'";
	}

	return " id='$name'";
}

/**
 * Outputs the HTML content.
 *
 * @since 3.5.0
 *
 * @param bool   $r_var_echo Whether to output the content or not.
 * @param string $output     HTML content.
 *
 * @return void
 */
function wdmPrintOutput( $r_var_echo, $output ) { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid -- legacy code.
	if ( $r_var_echo ) {
		echo $output;
	}
}

/**
 * Get LearnDash content's parent course.
 *
 * @since 2.1
 * @param int $post_id post ID of a post.
 */
function wdmir_get_ld_parent( $post_id ) {
	$post = get_post( $post_id );

	if ( empty( $post ) ) {
		return;
	}

	$parent_course_id = 0;

	$post_type = $post->post_type;

	switch ( $post_type ) {
		case 'sfwd-certificates':
			// Get all quizzes.
			$quizzes = get_posts(
				[
					'post_type'      => 'sfwd-quiz',
					'posts_per_page' => -1,
				]
			);

			foreach ( $quizzes as $quiz ) {
				$sfwd_quiz = get_post_meta( $quiz->ID, '_sfwd-quiz', true );

				if ( isset( $sfwd_quiz['sfwd-quiz_certificate'] ) && $sfwd_quiz['sfwd-quiz_certificate'] == $post_id ) {
					if ( isset( $sfwd_quiz['sfwd-quiz_certificate'] ) ) {
						$parent_course_id = $sfwd_quiz['sfwd-quiz_course'];
					} else {
						$parent_course_id = get_post_meta( $quiz->ID, 'course_id' );
					}

					break;
				}
			}

			break;

		case 'sfwd-lessons':
		case 'sfwd-quiz':
		case 'sfwd-topic':
			$parent_course_id = get_post_meta( $post_id, 'course_id', true );
			break;

		case 'sfwd-courses':
			$parent_course_id = $post_id;
			break;

		default:
			$parent_course_id = apply_filters( 'wdmir_parent_post_id', $post_id );
			break;
	}

	return $parent_course_id;
}

/**
 *
 * Description: To check if post is pending approval.
 *
 * @since 2.1
 * @param int $post_id post ID of a post.
 *
 * @return array/false string/boolean array of data if post has pending approval.
 */
function wdmir_am_i_pending_post( $post_id ) {
	if ( empty( $post_id ) ) {
		return false;
	}

	$parent_course_id = wdmir_get_ld_parent( $post_id );

	if ( empty( $parent_course_id ) ) {
		return false;
	}

	$approval_data = wdmir_get_approval_meta( $parent_course_id );

	if ( isset( $approval_data[ $post_id ] ) && 'pending' == $approval_data[ $post_id ]['status'] ) {
		return $approval_data[ $post_id ];
	}

	return false;
}

/**
 * Description: To get approval meta of a course
 *
 * @since 2.1
 * @param int $course_id post ID of a course.
 *
 * @return array/false string/boolean array of data.
 */
function wdmir_get_approval_meta( $course_id ) {
	$approval_data = get_post_meta( $course_id, '_wdmir_approval', true );

	if ( empty( $approval_data ) ) {
		$approval_data = [];
	}

	return $approval_data;
}

/**
 *
 * Description: To set approval meta of a course
 *
 * @since 2.1
 * @param int   $course_id       post ID of a course.
 * @param array $approval_data approval meta data of a course.
 */
function wdmir_set_approval_meta( $course_id, $approval_data ) {
	update_post_meta( $course_id, '_wdmir_approval', $approval_data );
}

/**
 *
 * Description: To recheck and update course approval data.
 *
 * @since 2.1
 * @param int $course_id      post ID of a course.
 *
 * @return array $approval_data updated new approval data.
 */
function wdmir_update_approval_data( $course_id ) {
	$approval_data = wdmir_get_approval_meta( $course_id );

	if ( ! empty( $approval_data ) ) {
		foreach ( $approval_data as $content_id => $content_meta ) {
			$parent_course_id = wdmir_get_ld_parent( $content_id );

			if ( $parent_course_id != $course_id ) {
				unset( $approval_data[ $content_id ] );
			}
		}

		wdmir_set_approval_meta( $course_id, $approval_data );
	}

	return $approval_data;
}

/**
 *
 * Description: To check if parent post's content has pending approval.
 *
 * @since 2.1
 * @param int $course_id int post ID of a course.
 *
 * @return true/false boolean true if course has pending approval.
 */
function wdmir_is_parent_course_pending( $course_id ) {
	$approval_data = wdmir_get_approval_meta( $course_id );

	if ( empty( $approval_data ) ) {
		return false;
	}

	foreach ( $approval_data as $content_meta ) {
		// If pending content found.
		if ( 'pending' == $content_meta['status'] ) {
			return true;
		}
	}
}

/**
 *
 * Description: To send an email using wp_mail() function
 *
 * @since 2.1
 * @param string|string[] $to_user     Array or comma-separated list of email addresses to send message.
 * @param string          $subject     Email subject.
 * @param string          $message     Message contents.
 * @param string|string[] $headers     Optional. Additional headers.
 * @param string|string[] $attachments Optional. Paths to files to attach.
 * @return bool value of wp_mail function.
 */
function wdmir_wp_mail( $to_user, $subject, $message, $headers = [], $attachments = [] ) {
	if ( ! empty( $to_user ) ) {
		return wp_mail( $to_user, $subject, $message, $headers, $attachments );
	}

	return false;
}

/**
 *
 * Description: To set mail content type to HTML
 *
 * @since 2.1
 * @return string content format for mails.
 */
function wdmir_html_mail() {
	return 'text/html';
}

/**
 *
 * Description: To replace shortcodes in the template for the post.
 *
 * @since 2.1
 * @param int     $post_id     post ID of a post.
 * @param string  $template template to replace words.
 * @param boolean $is_course_content true / false to check if course content or not.
 *
 * @return $template string template by replacing words
 */
function wdmir_post_shortcodes( $post_id, $template, $is_course_content = false ) {
	if ( empty( $template ) || empty( $post_id ) ) {
		return $template;
	}
	$post = get_post( $post_id );

	if ( empty( $post ) ) {
		return $template;
	}

	$post_author_id = $post->post_author;

	$author_login_name = get_the_author_meta( 'user_login', $post_author_id );

	if ( $is_course_content ) {
		$find = [
			'[course_content_title]',
			'[course_content_edit]',
			'[content_update_datetime]',
			'[approved_datetime]',
			'[content_permalink]',
		];

		$replace = [
			$post->post_title, // course_content_title.
			admin_url( 'post.php?post=' . $post_id . '&action=edit' ), // course_content_edit.
			$post->post_modified, // content_update_datetime.
			$post->post_modified, // approved_datetime.
			get_permalink( $post_id ), // content_permalink.
		];

		$replace = apply_filters( 'wdmir_content_template_filter', $replace, $find );
	} else {
		$find = [
			'[post_id]',
			'[course_id]',
			'[product_id]',
			'[download_id]', // v3.0.0.
			'[post_title]',
			'[course_title]',
			'[download_title]', // v3.0.0.
			'[product_title]',
			'[post_author]',
			'[course_permalink]',
			'[product_permalink]',
			'[download_permalink]', // v3.0.0.
			'[course_update_datetime]',
			'[product_update_datetime]',
			'[download_update_datetime]', // v3.0.0.
			'[ins_profile_link]',
		];

		$replace = [
			$post_id, // post_id.
			$post_id, // course_id.
			$post_id, // product_id.
			$post_id, // download_id.
			$post->post_title, // post_title.
			$post->post_title, // course_title.
			$post->post_title, // download_title.
			$post->post_title, // product_title.
			$author_login_name, // post_author.
			get_permalink( $post_id ), // post_permalink.
			get_permalink( $post_id ), // product_permalink.
			get_permalink( $post_id ), // download_permalink.
			$post->post_modified, // course_update_datetime.
			$post->post_modified, // product_update_datetime.
			$post->post_modified, // download_update_datetime.
			admin_url( 'user-edit.php?user_id=' . $post_author_id ), // ins_profile_link.
		];

		$replace = apply_filters( 'wdmir_course_template_filter', $replace, $find );
	}

	$template = str_replace( $find, $replace, $template );

	$template = wdmir_user_shortcodes( $post_author_id, $template );

	return $template;
}

/**
 *
 * Description: To replace shortcodes in the template for the User.
 *
 * @since 2.1
 * @param int    $user_id user ID.
 * @param string $template template to replace words.
 *
 * @return $template string template by replacing words
 */
function wdmir_user_shortcodes( $user_id, $template ) {
	if ( empty( $template ) || empty( $user_id ) ) {
		return $template;
	}

	$userdata = get_userdata( $user_id );

	$find = [
		'[ins_first_name]',
		'[ins_last_name]',
		'[ins_login]',
		'[ins_profile_link]',
	];

	$replace = [
		$userdata->first_name, // ins_first_name.
		$userdata->last_name, // ins_last_name.
		$userdata->user_login, // ins_login.
		admin_url( 'user-edit.php?user_id=' . $user_id ),  // ins_profile_link.
	];

	$replace = apply_filters( 'wdmir_user_template_filter', $replace, $find );

	$template = str_replace( $find, $replace, $template );

	return $template;
}


/**
 * For checking woocommerce dependency
 *
 * @return boolean returns true if plugin is active
 */
function wdmCheckWooDependency() {
	if ( is_multisite() ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( class_exists( 'Learndash_WooCommerce' ) || class_exists( 'learndash_woocommerce' ) ) {
			// in the network.
			return true;
		}
		return false;
	} elseif ( ! class_exists( 'Learndash_WooCommerce' ) || ! class_exists( 'learndash_woocommerce' ) ) {
		return false;
	}
	return true;
}



/**
 * For checking EDD dependency
 *
 * @return boolean returns true if plugin is active
 */
function wdmCheckEDDDependency() {
	if ( is_multisite() ) {
		if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		if ( class_exists( 'LearnDash_EDD' ) ) {
			// in the network.
			return true;
		}
		return false;
	} elseif ( ! class_exists( 'LearnDash_EDD' ) ) {
		return false;
	}
	return true;
}

if ( ! function_exists( 'ir_admin_settings_check' ) ) {
	/**
	 * Get IR admin settings
	 *
	 * @param string $key    IR admin option key whose value is to be fetched.
	 *
	 * @return mixed         Returns admin option value if found, else false.
	 */
	function ir_admin_settings_check( $key ) {
		$ir_admin_settings = get_option( '_wdmir_admin_settings', false );

		if ( empty( $ir_admin_settings ) ) {
			return false;
		}

		if ( array_key_exists( $key, $ir_admin_settings ) ) {
			return $ir_admin_settings[ $key ];
		}

		return false;
	}
}

if ( ! function_exists( 'ir_get_instructors' ) ) {
	/**
	 * Get instructors.
	 *
	 * @param array $atts   Array of Attributes.
	 *
	 * @return array        Array of instructors.
	 */
	function ir_get_instructors( $atts = [] ) {
		// WP_User_Query arguments.
		$args = [
			'role'    => 'wdm_instructor',
			'order'   => 'ASC',
			'orderby' => 'display_name',
			'fields'  => [ 'ID', 'user_login', 'display_name' ],
			'exclude' => '',
		];

		$args = shortcode_atts( $args, $atts );

		// Fetch Instructors.
		$user_query = new WP_User_Query( $args );

		return $user_query->results;
	}
}
if ( ! function_exists( 'ir_get_users_with_course_access' ) ) {
	/**
	 * Get users who have access to a course
	 *
	 * Note : This function excludes users who directly have access for free courses but
	 *        does include them if any progress is made or if they are explicitly enrolled.
	 *
	 * @param int   $course_id    ID of the course.
	 * @param array $sources    Sources to check for course access.
	 */
	function ir_get_users_with_course_access( $course_id, $sources ) {
		global $wpdb;
		$users = [];

		// Check if empty course id.
		if ( empty( $course_id ) ) {
			return $users;
		}

		$course = get_post( $course_id );

		// Check for empty course post.
		if ( empty( $course ) ) {
			return $users;
		}

		// Check if course post type.
		if ( 'sfwd-courses' != $course->post_type ) {
			return $users;
		}

		// 1. Get Direct course access users.
		if ( in_array( 'direct', $sources ) ) {
			$table    = $wpdb->usermeta;
			$meta_key = 'course_' . $course_id . '_access_from';
			$sql      = $wpdb->prepare( "SELECT user_id FROM $table WHERE meta_key = %s", $meta_key );

			$result = $wpdb->get_col( $sql, 0 );

			if ( ! empty( $result ) ) {
				$users = array_merge( $users, $result );
			}
		}

		// 2. Access to course from groups
		if ( in_array( 'group', $sources ) ) {
			$table    = $wpdb->postmeta;
			$meta_key = 'learndash_group_enrolled_' . '%';
			$sql      = $wpdb->remove_placeholder_escape(
				$wpdb->prepare(
					"SELECT meta_key FROM $table WHERE post_id = %d AND meta_key LIKE %s",
					$course_id,
					$meta_key
				)
			);

			$result = $wpdb->get_col( $sql, 0 );

			if ( ! empty( $result ) ) {
				$table = $wpdb->usermeta;

				foreach ( $result as $group ) {
					$group_id = intval( filter_var( $group, FILTER_SANITIZE_NUMBER_INT ) );
					if ( ! $group_id ) {
						continue;
					}
					$meta_key    = 'learndash_group_users_' . $group_id;
					$sql         = $wpdb->prepare( "SELECT user_id FROM $table WHERE meta_key = %s", $meta_key );
					$group_users = $wpdb->get_col( $sql, 0 );
					if ( empty( $group_users ) ) {
						continue;
					}
					$users = array_merge( $users, $group_users );
				}
			}
		}

		// 3. Course access list users
		if ( in_array( 'direct', $sources ) ) {
			$course_access_list = learndash_get_course_meta_setting( $course_id, 'course_access_list' );
			$users              = array_merge( $users, $course_access_list );
		}

		$users = array_unique( $users );

		return apply_filters( 'ir_filter_course_access_users', $users, $course_id, $sources );
	}
}

if ( ! function_exists( 'ir_refresh_shared_course_details' ) ) {
	/**
	 * Refresh shared course details for the current instructor.
	 *
	 * @param int $user_id  ID of the user.
	 *
	 * @since 3.3.0
	 */
	function ir_refresh_shared_course_details( $user_id = 0 ) {
		$refreshed_courses = [];

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$shared_courses_list = get_user_meta( $user_id, 'ir_shared_courses', 1 );

		if ( empty( $shared_courses_list ) ) {
			return false;
		}

		$shared_courses = explode( ',', $shared_courses_list );

		foreach ( $shared_courses as $course_id ) {
			$course_status = get_post_status( $course_id );
			// Remove trashed posts.
			if ( 'trash' == $course_status || empty( $course_status ) ) {
				continue;
			}

			// Get course instructors.
			$course_instructors_list = get_post_meta( $course_id, 'ir_shared_instructor_ids', 1 );
			$course_instructors      = explode( ',', $course_instructors_list );

			// Remove if not is course instructor list.
			if ( ! in_array( $user_id, $course_instructors ) ) {
				continue;
			}

			// Check if not owned course.
			if ( get_post_field( 'post_author', $course_id ) == $user_id ) {
				continue;
			}

			// Add verified shared instructor.
			array_push( $refreshed_courses, $course_id );
		}

		// Check if refreshed and original list same.
		if ( ! empty( array_diff( $shared_courses, $refreshed_courses ) ) || ( empty( $refreshed_courses ) && ! empty( $shared_courses ) ) ) {
			$refreshed_list = implode( ',', $refreshed_courses );
			update_user_meta( $user_id, 'ir_shared_courses', $refreshed_list );
		}
	}
}

if ( ! function_exists( 'ir_get_instructor_shared_course_list' ) ) {
	/**
	 * Get shared course list for a instructor
	 *
	 * @param int $user_id  ID of the user.
	 *
	 * @since 3.3.0
	 */
	function ir_get_instructor_shared_course_list( $user_id = 0 ) {
		$shared_courses = [];

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		// Check if instructor or admin.
		if ( ! wdm_is_instructor( $user_id ) && ! current_user_can( 'manage_options' ) ) {
			return $shared_courses;
		}

		// Get shared courses.
		$shared_courses_list = get_user_meta( $user_id, 'ir_shared_courses', 1 );

		if ( ! empty( $shared_courses_list ) ) {
			$shared_courses = array_map( 'intval', explode( ',', $shared_courses_list ) );
		}

		return $shared_courses;
	}
}

if ( ! function_exists( 'ir_get_instructor_owned_course_list' ) ) {
	/**
	 * Get owned course list for an instructor
	 *
	 * @param int  $user_id     ID of the user.
	 * @param bool $is_builder  Whether data is to be used for builder, if so fetch drafted courses as well.
	 * @param bool $fetch_trashed  If true fetch trashed courses as well, defaults to false.
	 *
	 * @since 3.3.0
	 */
	function ir_get_instructor_owned_course_list( $user_id = 0, $is_builder = false, $fetch_trashed = false ) {
		$owned_courses = [];

		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$args = [
			'post_type'   => 'sfwd-courses',
			'author'      => $user_id,
			'fields'      => 'ids',
			'numberposts' => -1,
		];

		if ( $is_builder ) {
			$args['post_status'] = [ 'publish', 'draft', 'private', 'future' ];
		}

		if ( $fetch_trashed ) {
			$args['post_status'][] = 'trash';
		}

		// @todo Return int array.
		$owned_courses = get_posts( $args );

		return $owned_courses;
	}
}

if ( ! function_exists( 'ir_get_instructor_complete_course_list' ) ) {
	/**
	 * Get shared and owned course list for an instructor
	 *
	 * @since 3.3.0
	 * @since 5.8.0     Updated return format to integer array.
	 *
	 * @param int  $user_id        ID of the user.
	 * @param bool $is_builder     Whether data is to be used for builder, if so fetch drafted courses as well.
	 * @param bool $fetch_trashed  If true fetch trashed courses as well, defaults to false.
	 *
	 * @return array               Array of complete course list.
	 */
	function ir_get_instructor_complete_course_list( $user_id = 0, $is_builder = false, $fetch_trashed = false ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		$owned_courses  = ir_get_instructor_owned_course_list( $user_id, $is_builder, $fetch_trashed );
		$shared_courses = ir_get_instructor_shared_course_list( $user_id );

		return array_merge( $owned_courses, $shared_courses );
	}
}

/**
 * Get templates passing attributes and including the file.
 *
 * @param string $template_path Template path.
 * @param array  $args          Arguments. (default: array).
 * @param bool   $return        Whether to return the result or not. (default: false).
 */
function ir_get_template( $template_path, $args = [], $return = false ) {
	// Check if template exists.
	if ( empty( $template_path ) ) {
		return '';
	}

	/**
	 * Allow 3rd party plugins to filter template arguments
	 *
	 * @since 3.5.0
	 *
	 * @param array  $args              Template arguments for the current template.
	 * @param string $template_path     Path of the current template.
	 */
	$args = apply_filters( 'ir_filter_template_args', $args, $template_path );

	// Check if arguments set.
	if ( ! empty( $args ) && is_array( $args ) ) {
        extract($args); // @codingStandardsIgnoreLine.
	}

	/**
	 * Allow 3rd party plugins to filter template path.
	 *
	 * @since 3.4.0
	 *
	 * @param string $template_path     Path for the current template.
	 * @param array  $args              Template arguments for current template.
	 */
	$template_path = apply_filters( 'ir_filter_template_path', $template_path, $args );

	// Whether to capture contents in output buffer.
	if ( $return ) {
		ob_start();
	}

	/**
	 * Allow 3rd party plugins to perform actions before a template is rendered.
	 *
	 * @since 3.4.0
	 *
	 * @param array     $args           Template arguments for current template.
	 * @param string    $template_path  Path for the current template.
	 */
	do_action( 'ir_action_before_template', $args, $template_path );

	include $template_path;

	/**
	 * Allow 3rd party plugins to perform actions after a template is rendered.
	 *
	 * @since 3.4.0
	 *
	 * @param array     $args           Template arguments for current template.
	 * @param string    $template_path  Path for the current template.
	 */
	do_action( 'ir_action_after_template', $args, $template_path );

	// Return buffered contents.
	if ( $return ) {
		$contents = ob_get_clean();

		/**
		 * Allow 3rd party plugins to filter returned contents.
		 *
		 * @since 3.4.0
		 *
		 * @param string $contents      HTML contents for the rendered template.
		 * @param array  $args          Template arguments for the current template.
		 */
		return apply_filters( 'ir_filter_get_template_contents', $contents, $args );
	}
}

/**
 * Get date in site timezone.
 *
 * @since 3.4.0
 *
 * @param string $time_string Valid date time string identified by strtotime.
 * @param string $format      Valid PHP datetime format.
 *
 * @return string Date in site timezone.
 */
function ir_get_date_in_site_timezone( $time_string, $format = 'l jS \of F Y h:i:s A - T' ) {
	// Get timestamp from the time string.
	$timestamp = strtotime( $time_string );

	// Fetch site timezone.
	/**
	 * Filter the timezone for the returned date
	 *
	 * @since 3.4.0
	 *
	 * @param string $site_timezone     Site timezone
	 */
	$site_timezone = apply_filters( 'ir_filter_date_in_site_timezone_timezone', get_option( 'timezone_string' ) );

	// If not set, default to UTC timezone.
	if ( empty( $site_timezone ) ) {
		$site_timezone = 'UTC';
	}

	if ( empty( $format ) ) {
		$date_format = get_option( 'date_format' );
		$time_format = get_option( 'time_format' );
		$format      = $date_format . ' - ' . $time_format;

		// If empty format, set default format.
		if ( empty( $format ) ) {
			$format = 'l jS \of F Y h:i:s A - T';
		}
	}

	// Set return date format.
	/**
	 * Filter the datetime format for the returned date
	 *
	 * @since 3.4.0
	 *
	 * @param string $format        Valid PHP datetime format.
	 * @param string $timestamp     Unix timestamp of the date.
	 */
	$format = apply_filters( 'ir_filter_date_in_site_timezone_format', $format, $timestamp );

	$date = new DateTime();
	$date->setTimezone( new DateTimeZone( $site_timezone ) );
	$date->setTimestamp( $timestamp );
	$converted_date_string = $date->format( $format );

	/**
	 * Filter the date string to be returned.
	 *
	 * @since 3.4.0
	 *
	 * @param string $converted_date_string     Converted date string to be returned.
	 * @param object $date                      DateTime object of the returned date.
	 */
	return apply_filters( 'ir_filter_date_in_site_timezone', $converted_date_string, $date );
}

/**
 * Get instructor profile designation
 *
 * @param object $userdata  WP User data.
 * @return string
 *
 * @since 3.5.0
 */
function ir_get_profile_designation( $userdata ) {
	$designation = '';

	$role = get_role( $userdata->roles[0] );
	switch ( $role->name ) {
		case 'wdm_instructor':
			$designation = __( 'Instructor', 'wdm_instructor_role' );
			break;

		case 'administrator':
			$designation = __( 'Administrator', 'wdm_instructor_role' );
			break;

		case 'editor':
			$designation = __( 'Editor', 'wdm_instructor_role' );
			break;

		case 'subscriber':
			$designation = __( 'Subscriber', 'wdm_instructor_role' );
			break;
	}

	return apply_filters( 'ir_filter_profile_designation', $designation, $userdata );
}

/**
 * Get list of active core modules for the plugin
 *
 * @return array
 *
 * @since 3.5.0
 */
function ir_get_active_core_modules() {
	if ( ! defined( 'IR_CORE_MODULES_META_KEY' ) ) {
		return [];
	}
	return get_option( IR_CORE_MODULES_META_KEY );
}

/**
 * Disable core instructor role modules
 *
 * @param mixed $target_modules    One or more instructor core modules to be disabled in array.
 * @return bool                    True if successfully disabled, else false.
 *
 * @since 3.5.0
 */
function ir_disable_core_modules( $target_modules ) {
	if ( empty( $target_modules ) ) {
		return false;
	}

	if ( ! is_array( $target_modules ) ) {
		$target_modules = [ $target_modules ];
	}

	$active_modules = ir_get_active_core_modules();
	$modules        = $active_modules;
	foreach ( $target_modules as $disable_module ) {
		$module_key = array_search( $disable_module, $modules );
		if ( false !== $module_key ) {
			unset( $active_modules[ $module_key ] );
		}
	}
	if ( count( $modules ) != count( $active_modules ) ) {
		update_option( IR_CORE_MODULES_META_KEY, array_values( $active_modules ) );
	}
	return true;
}

/**
 * Enable core instructor role modules
 *
 * @param mixed $target_modules    One or more instructor core modules to be enabled in array.
 * @return bool                    True if successfully enabled, else false.
 *
 * @since 3.5.0
 */
function ir_enable_core_modules( $target_modules ) {
	if ( empty( $target_modules ) ) {
		return false;
	}

	if ( ! is_array( $target_modules ) ) {
		$target_modules = [ $target_modules ];
	}

	$active_modules = ir_get_active_core_modules();
	$modules        = $active_modules;
	foreach ( $target_modules as $enable_module ) {
		if ( ! in_array( $enable_module, $modules ) ) {
			$active_modules[] = $enable_module;
		}
	}
	if ( count( $modules ) != count( $active_modules ) ) {
		update_option( IR_CORE_MODULES_META_KEY, array_values( $active_modules ) );
	}
	return true;
}

if ( ! function_exists( 'ir_get_instructor_profile_link' ) ) {
	/**
	 * Get instructor profile link
	 *
	 * @since 3.5.5
	 *
	 * @param int $user_id  ID of the user.
	 */
	function ir_get_instructor_profile_link( $user_id ) {
		// @todo: Fetch updated author url for theme support.
		$author_link = get_author_posts_url( $user_id );

		if ( ! wdm_is_instructor( $user_id ) || ! get_option( 'ir_enable_profile_links', false ) ) {
			return $author_link;
		}

		$structure = get_option( 'permalink_structure' );
		if ( empty( $structure ) ) {
			$author_link = add_query_arg(
				[
					'author'                => $user_id,
					'ir_instructor_profile' => 1,
				],
				get_site_url()
			);
		} else {
			$author_link = get_site_url() . '/instructor/' . rawurlencode( get_the_author_meta( 'nicename', $user_id ) );
		}

		/**
		 * Filter instructor profile link
		 *
		 * @since 3.5.5
		 *
		 * @param string $author_link   Author URL for the user.
		 * @param int $user_id          ID of the user.
		 */
		return apply_filters( 'ir_filter_get_instructor_profile_link', $author_link, $user_id );
	}
}

if ( ! function_exists( 'ir_get_settings' ) ) {
	/**
	 * Get instructor role settings.
	 *
	 * @param string $key   Key of the setting. If empty returns all settings.
	 * @param bool   $force   Set false to get from cache or true to force fetch from database. Defaults to false.
	 *
	 * @return mixed        Setting value on success. Returns false if key not exists or empty array if no settings.
	 */
	function ir_get_settings( $key = '', $force = false ) {
		$setting_value       = false;
		$settings_option_key = '_wdmir_admin_settings';

		$ir_settings = [];

		// Check cache if not forced.
		if ( ! $force ) {
			$ir_settings = wp_cache_get( $settings_option_key );
		}

		// If cache expired or forced, get from db.
		if ( empty( $ir_settings ) ) {
			$ir_settings = get_option( $settings_option_key, [] );
			wp_cache_set( $settings_option_key, $ir_settings, 'instructor_role', DAY_IN_SECONDS );
		}

		// If empty key, return all settings.
		if ( empty( $key ) ) {
			$setting_value = $ir_settings;
		} else {
			$setting_value = array_key_exists( $key, $ir_settings ) ? $ir_settings[ $key ] : false;
		}

		/**
		 * Filter setting value to be returned.
		 *
		 * @since 3.6.0
		 *
		 * @param mixed $setting_value  Setting value returned.
		 * @param string $key           Setting key.
		 * @param bool $force           Whether to get from cache or force fetch from database.
		 */
		return apply_filters( 'ir_filter_get_settings', $setting_value, $key, $force );
	}
}

if ( ! function_exists( 'ir_set_settings' ) ) {
	/**
	 * Set instructor role settings.
	 *
	 * @param string $key   Key of the setting.
	 * @param mixed  $value  Value of the setting.
	 *
	 * @return bool         True if value set, false otherwise.
	 */
	function ir_set_settings( $key, $value ) {
		$settings_option_key = '_wdmir_admin_settings';

		$ir_settings = ir_get_settings();

		$ir_settings[ $key ] = $value;

		// Update database.
		$status = update_option( $settings_option_key, $ir_settings );

		// Update cache ( Cache expiry set to 1 day ).
		wp_cache_set( $settings_option_key, $ir_settings, 'instructor_role', DAY_IN_SECONDS );

		return $status;
	}
}

if ( ! function_exists( 'ir_filter_input' ) ) {
	/**
	 * Filter and sanitize data fetched from GET and POST requests.
	 *
	 * @param string $var_name  Name of the variable to get.
	 * @param int    $type      One of INPUT_GET for $_GET data or INPUT_POST for $_POST data.
	 *                          If not set default set to INPUT_POST.
	 * @param string $filter    One of string, number, float or bool. If not set default set to string.
	 *
	 * @return mixed            Value of requested variable on success.
	 *                          false if variable not set or if GET/POST empty.
	 *                          null if $type other than INPUT_GET or INPUT_POST passed.
	 */
	function ir_filter_input( $var_name, $type = INPUT_POST, $filter = 'string' ) {
		$value = null;

		// Check if POST or GET data.
		if ( INPUT_GET === $type ) {
			// If empty GET or key does not exist, return.
			if ( empty( $_GET ) || ! array_key_exists( $var_name, $_GET ) ) {
				return false;
			}

			// Filter data based on data type.
			switch ( $filter ) {
				case 'string':
					$value = filter_input( INPUT_GET, $var_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					break;
				case 'number':
					$value = filter_input( INPUT_GET, $var_name, FILTER_SANITIZE_NUMBER_INT );
					break;
				case 'float':
					$value = filter_input( INPUT_GET, $var_name, FILTER_SANITIZE_NUMBER_FLOAT );
					break;
				case 'bool':
					$value = filter_input( INPUT_GET, $var_name, FILTER_VALIDATE_BOOLEAN );
					break;
			}
		} else {
			// If empty POST or key does not exist, return.
			if ( empty( $_POST ) || ! array_key_exists( $var_name, $_POST ) ) {
				return false;
			}

			// Filter data based on data type.
			switch ( $filter ) {
				case 'string':
					$value = filter_input( INPUT_POST, $var_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
					break;
				case 'number':
					$value = filter_input( INPUT_POST, $var_name, FILTER_SANITIZE_NUMBER_INT );
					break;
				case 'float':
					$value = filter_input( INPUT_POST, $var_name, FILTER_SANITIZE_NUMBER_FLOAT );
					break;
				case 'bool':
					$value = filter_input( INPUT_POST, $var_name, FILTER_VALIDATE_BOOLEAN );
					break;
			}
		}

		/**
		 * Filter the data returned after filtering and sanitization.
		 *
		 * @since 4.3.0
		 *
		 * @param mixed  $value     Input data after sanitization and filtering.
		 * @param int    $type      One of INPUT_GET or INPUT_POST. If not set default set to INPUT_POST.
		 * @param string $var_name  Name of the variable to get.
		 * @param string $filter    One of string, number, float or bool. If not set default set to string.
		 */
		return apply_filters( 'ir_filter_input', $value, $type, $var_name, $filter );
	}
}

if ( ! function_exists( 'ir_get_instructor_commission_records' ) ) {
	/**
	 * Get instructor commission record details
	 *
	 * @since 5.0.0
	 *
	 * @param int $instructor_id    User ID of instructor. If empty, current user ID is used.
	 * @return array                Array of commission records.
	 */
	function ir_get_instructor_commission_records( $instructor_id = 0 ) {
		$commission_records = [];

		if ( empty( $instructor_id ) ) {
			$instructor_id = get_current_user_id();
		}

		// Check if valid instructor user.
		if ( ! wdm_is_instructor( $instructor_id ) ) {
			return $commission_records;
		}

		global $wpdb;

		$table = $wpdb->prefix . 'wdm_instructor_commission';
		$sql   = $wpdb->prepare( "SELECT * FROM $table where user_id = %d", $instructor_id );

		return $wpdb->get_results( $sql );
	}
}

if ( ! function_exists( 'ir_is_gutenberg_enabled' ) ) {
	/**
	 * Check whether site supports gutenberg editor
	 *
	 * @since 5.0.0
	 *
	 * @return bool     True if gutenberg ready, false otherwise.
	 */
	function ir_is_gutenberg_enabled() {
		$is_gutenberg_ready = false;

		// Check if block editor enabled on post and pages.
		if ( use_block_editor_for_post_type( 'post' ) || use_block_editor_for_post_type( 'page' ) ) {
			$is_gutenberg_ready = true;
		}

		/**
		 * Filter gutenberg ready check.
		 *
		 * @since 5.0.0
		 *
		 * @param bool $is_gutenberg_ready  True if gutenberg ready, false otherwise.
		 */
		return apply_filters( 'ir_filter_is_gutenberg_ready', $is_gutenberg_ready );
	}
}

if ( ! function_exists( 'ir_get_instructor_course_steps' ) ) {
	/**
	 * Get instructor course steps.
	 *
	 * @since 5.2.0
	 *
	 * @param array   $step_types       Array of type of steps to return.
	 * @param array   $course_ids       Array of course ids to return steps for.
	 * @param integer $instructor_id    User ID of instructor.
	 * @param bool    $skip_orphan_steps   Whether to skip orphan steps.
	 * @return array                    List of course steps.
	 */
	function ir_get_instructor_course_steps( $step_types = [], $course_ids = [], $instructor_id = 0, $skip_orphan_steps = false ) {
		$course_steps = [];

		// Get current user steps if instructor id not set.
		if ( empty( $instructor_id ) ) {
			$instructor_id = get_current_user_id();
		}

		// If user is not instructor, return.
		if ( ! wdm_is_instructor( $instructor_id ) ) {
			return $course_steps;
		}

		// If steps not defined, return all steps.
		if ( empty( $step_types ) ) {
			$step_types = [
				'lesson',
				'topic',
				'quiz',
			];
		}

		// If course ids not set, return all instructor courses.
		if ( empty( $course_ids ) ) {
			$course_ids = ir_get_instructor_complete_course_list( $instructor_id );
		} else {
			// Check for courses not accessible to the instructor.
			$instructor_courses = ir_get_instructor_complete_course_list( $instructor_id );
			if ( count( array_diff( $course_ids, $instructor_courses ) ) > 0 ) {
				return $course_steps;
			}
		}

		$course_steps_by_type = [];

		// Fetch all course steps by type.
		foreach ( $course_ids as $course_id ) {
			$ld_course_steps_object = \LDLMS_Factory_Post::course_steps( intval( $course_id ) );

			if ( $ld_course_steps_object ) {
				$data                 = $ld_course_steps_object->get_steps( 't' );
				$course_steps_by_type = array_merge_recursive( $course_steps_by_type, $data );
			}
		}

		$course_steps_by_type = array_map( 'array_unique', $course_steps_by_type );

		// Extract lessons.
		if ( in_array( 'lesson', $step_types, true ) && array_key_exists( 'sfwd-lessons', $course_steps_by_type ) ) {
			if ( ! $skip_orphan_steps ) {
				// Fetch all orphan steps.
				$lessons = new WP_Query(
					[
						'post_type'      => learndash_get_post_type_slug( 'lesson' ),
						'posts_per_page' => -1,
						'post_status'    => [ 'publish', 'draft' ],
						'author'         => $instructor_id,
						'fields'         => 'ids',
					]
				);

				if ( ! empty( $lessons->posts ) ) {
					$course_steps = array_unique( array_merge( $course_steps, $lessons->posts ) );
				}
			}

			$course_steps = array_unique( array_merge( $course_steps, $course_steps_by_type['sfwd-lessons'] ) );
		}

		// Extract topics.
		if ( in_array( 'topic', $step_types, true ) && array_key_exists( 'sfwd-topic', $course_steps_by_type ) ) {
			if ( ! $skip_orphan_steps ) {
				// Fetch all orphan steps.
				$topics = new WP_Query(
					[
						'post_type'      => learndash_get_post_type_slug( 'topic' ),
						'posts_per_page' => -1,
						'post_status'    => [ 'publish', 'draft' ],
						'author'         => $instructor_id,
						'fields'         => 'ids',
					]
				);

				if ( ! empty( $topics->posts ) ) {
					$course_steps = array_unique( array_merge( $course_steps, $topics->posts ) );
				}
			}
			$course_steps = array_merge( $course_steps, $course_steps_by_type['sfwd-topic'] );
		}

		// Extract quizzes.
		if ( in_array( 'quiz', $step_types, true ) && array_key_exists( 'sfwd-quiz', $course_steps_by_type ) ) {
			if ( ! $skip_orphan_steps ) {
				// Fetch all orphan steps.
				$quizzes = new WP_Query(
					[
						'post_type'      => learndash_get_post_type_slug( 'quiz' ),
						'posts_per_page' => -1,
						'post_status'    => [ 'publish', 'draft' ],
						'author'         => $instructor_id,
						'fields'         => 'ids',
					]
				);

				if ( ! empty( $quizzes->posts ) ) {
					$course_steps = array_unique( array_merge( $course_steps, $quizzes->posts ) );
				}
			}
			$course_steps = array_merge( $course_steps, $course_steps_by_type['sfwd-quiz'] );
		}

		/**
		 * Filter list of course steps calculated for instructor.
		 *
		 * @since 5.2.0
		 *
		 * @param array   $step_types       Array of type of steps to return.
		 * @param array   $course_ids       Array of course ids to return steps for.
		 * @param integer $instructor_id    User ID of instructor.
		 *
		 * @return array                    Updated list of course steps.
		 */
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Legacy hook with short prefix.
		return apply_filters( 'ir_filter_get_instructor_course_steps', $course_steps, $step_types, $course_ids, $instructor_id );
	}
}

if ( ! function_exists( 'ir_get_protected_value' ) ) {
	/**
	 * To access protected elements from the object as an array.
	 *
	 * This is needed because LearnDash stores user responses serialized as an object but the object stored only
	 * contains protected properties and none of the corresponding getter methods.
	 *
	 * @param object $obj  object to get protected fields.
	 * @param string $name field name from the object.
	 *
	 * @return array Associative array of an protected field
	 */
	function ir_get_protected_value( $obj, $name ) {
		$array  = (array) $obj;
		$prefix = chr( 0 ) . '*' . chr( 0 );

		return $array[ $prefix . $name ];
	}
}

if ( ! function_exists( 'ir_get_email_settings' ) ) {
	/**
	 * Get instructor role email settings.
	 *
	 * @since 5.4.0
	 *
	 * @param string $key   Key of the setting. If empty returns all settings.
	 * @param bool   $force   Set false to get from cache or true to force fetch from database. Defaults to false.
	 *
	 * @return mixed        Setting value on success. Returns false if key not exists or empty array if no settings.
	 */
	function ir_get_email_settings( $key = '', $force = false ) {
		$setting_value       = false;
		$settings_option_key = '_wdmir_email_settings';

		$ir_settings = [];

		// Check cache if not forced.
		if ( ! $force ) {
			$ir_settings = wp_cache_get( $settings_option_key );
		}

		// If cache expired or forced, get from db.
		if ( empty( $ir_settings ) ) {
			$ir_settings = get_option( $settings_option_key, [] );
			wp_cache_set( $settings_option_key, $ir_settings, 'instructor_role', DAY_IN_SECONDS );
		}

		// If empty key, return all settings.
		if ( empty( $key ) ) {
			$setting_value = $ir_settings;
		} else {
			$setting_value = array_key_exists( $key, $ir_settings ) ? $ir_settings[ $key ] : false;
		}

		/**
		 * Filter setting value to be returned.
		 *
		 * @since 5.4.0
		 *
		 * @param mixed $setting_value  Setting value returned.
		 * @param string $key           Setting key.
		 * @param bool $force           Whether to get from cache or force fetch from database.
		 */
		return apply_filters( 'ir_filter_get_email_settings', $setting_value, $key, $force );
	}
}

if ( ! function_exists( 'ir_set_email_settings' ) ) {
	/**
	 * Set instructor role email settings.
	 *
	 * @param string $key   Key of the setting.
	 * @param mixed  $value  Value of the setting.
	 *
	 * @return bool         True if value set, false otherwise.
	 */
	function ir_set_email_settings( $key, $value ) {
		$settings_option_key = '_wdmir_email_settings';

		$ir_settings = ir_get_email_settings();

		$ir_settings[ $key ] = $value;

		// Update database.
		$status = update_option( $settings_option_key, $ir_settings );

		// Update cache ( Cache expiry set to 1 day ).
		wp_cache_set( $settings_option_key, $ir_settings, 'instructor_role', DAY_IN_SECONDS );

		return $status;
	}
}

if ( ! function_exists( 'ir_get_formatted_course_steps' ) ) {
	/**
	 * Get formatted course steps
	 *
	 * @param array $steps_tree Steps tree.
	 * @return array
	 */
	function ir_get_formatted_course_steps( $steps_tree ) {
		$tree = [];

		foreach ( $steps_tree as $step_type => $step_ids ) {
			$tree[ $step_type ] = [];

			if ( ! empty( $step_ids ) ) {
				foreach ( $step_ids as $step_id => $child_steps ) {
					$child_tree = [
						'id' => $step_id,
					];

					if ( ! empty( $child_steps ) ) {
						$child_tree['steps'] = ir_get_formatted_course_steps( $child_steps );
					}
					array_push( $tree[ $step_type ], $child_tree );
				}
			}
		}
		return $tree;
	}
}

if ( ! function_exists( 'ir_get_learndash_label' ) ) {
	/**
	 * Get LearnDash Custom Labels
	 *
	 * @since 5.8.0
	 *
	 * @param string $method    Name of method.
	 * @param string $label     Type of label.
	 *
	 * @return string           Custom label if found, else returns original label.
	 */
	function ir_get_learndash_label( $method, $label ) {
		if ( class_exists( 'LearnDash_Custom_Label' ) ) {
			return call_user_func( [ 'LearnDash_Custom_Label', $method ], $label );
		}

		return $label;
	}
}

if ( ! function_exists( 'ir_get_instructor_complete_quiz_list' ) ) {
	/**
	 * Get list of all quizzes accessible by instructor.
	 *
	 * @since 5.8.0
	 *
	 * @param int $user_id  User ID of instructor.
	 * @return array
	 */
	function ir_get_instructor_complete_quiz_list( $user_id ) {
		$instructor_quizzes = [];
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
		}

		// Get instructor quiz list.
		$instructor_courses = ir_get_instructor_complete_course_list( $user_id, true, true );

		foreach ( $instructor_courses as $course_id ) {
			$course_quiz = learndash_get_course_steps( $course_id, [ 'sfwd-quiz' ] );

			if ( ! empty( $course_quiz ) ) {
				$instructor_quizzes = array_merge( $instructor_quizzes, $course_quiz );
			}
		}

		// Fetch orphan quiz.
		$orphan_quiz = new WP_Query(
			[
				'post_type'      => learndash_get_post_type_slug( 'quiz' ),
				'posts_per_page' => -1,
				'post_status'    => 'any',
				'author'         => $user_id,
				'fields'         => 'ids',
			]
		);

		if ( ! empty( $orphan_quiz->posts ) ) {
			$instructor_quizzes = array_map( 'intval', array_unique( array_merge( $instructor_quizzes, $orphan_quiz->posts ) ) );
		}

		/**
		 * Filter instructor complete quiz list
		 *
		 * @since 5.8.0
		 *
		 * @param array $instructor_quizzes     Array of instructor quiz ids.
		 * @param int $user_id                  User ID of instructor.
		 */
		return apply_filters( 'ir_filter_get_instructor_complete_quiz_list', $instructor_quizzes, $user_id );
	}
}

if ( ! function_exists( 'learndash_instructor_role_normalize_float_value' ) ) {
	/**
	 * Normalizes a float value by applying decimal places.
	 *
	 * @since 5.9.4
	 *
	 * @param mixed $value           Value to normalize.
	 * @param int   $decimal_places  Number of decimal places. Default 2.
	 *
	 * @return float The normalized float value.
	 */
	function learndash_instructor_role_normalize_float_value( $value, $decimal_places = 2 ): float {
		return round(
			learndash_get_price_as_float(
				Cast::to_string( $value )
			),
			$decimal_places
		);
	}
}
