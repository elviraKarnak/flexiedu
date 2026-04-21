<?php
/**
 * The list table for the Gradebook.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin/includes
 *
 * cspell:ignore safemode .
 */

defined( 'ABSPATH' ) || die();

// Load WP_List_Table if not loaded
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class LD_GB_GradebookListTable
 *
 * The list table for the Gradebook.
 *
 * @since 1.0.0
 *
 * @package LearnDash_Gradebook
 * @subpackage LearnDash_Gradebook/admin
 */
class LD_GB_GradebookListTable extends WP_List_Table {
	/**
	 * Number of items to show per page.
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	public $per_page = 30;

	/**
	 * Gradebook Post ID.
	 *
	 * @since 1.2.0
	 *
	 * @var int
	 */
	public $gradebook;

	/**
	 * Group to view users from.
	 *
	 * @since 1.2.0
	 *
	 * @var int|bool
	 */
	public $group;

	/**
	 * Gradebook components.
	 *
	 * @since 1.2.0
	 *
	 * @var array
	 */
	public $components;

	/**
	 * Stores grade data for easy access.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	public $data = [];

	/**
	 * LD_GB_GradebookListTable constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param int      $gradebook
	 * @param int|bool $group
	 */
	function __construct( $gradebook, $group = false ) {
		$current_screen = get_current_screen();

		$option = $this->get_option( 'per_page', 'option' );
		if ( ! $option ) {
			$option = str_replace( '-', '_', "{$current_screen->id}_per_page" );
		}

		$per_page = (int) get_user_option( $option );

		if ( empty( $per_page ) || $per_page < 1 ) {
			$per_page = $this->get_option( 'per_page', 'default' );
			if ( ! $per_page ) {
				$per_page = $this->per_page;
			}
		}

		$this->per_page = $per_page;

		$this->gradebook  = $gradebook;
		$this->group      = $group;
		$this->components = ld_gb_get_field( 'components', $this->gradebook );

		add_action( 'admin_print_footer_scripts', [ $this, 'print_data' ] );

		parent::__construct(
			[
				'singular' => _x( 'User', 'Singular User for counting Users in the backend Gradebook view', 'learndash-gradebook' ),
				'plural'   => _x( 'Users', 'Plural Users for counting Users in the backend Gradebook view', 'learndash-gradebook' ),
			]
		);
	}

	/**
	 * Gets the User Defined Columns
	 *
	 * @access protected
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_user_columns() {
		$columns = learndash_gradebook_get_user_columns( $this->gradebook, $this->group );

		return $columns;    }

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = $this->get_user_columns();

		$columns['grade'] = _x( 'Overall Grade', 'Overall Grade column text for the backend Gradebook', 'learndash-gradebook' );

		if ( $this->components ) {
			foreach ( $this->components as $component ) {
				$columns[ "component_{$component['id']}" ] = $component['name'];
			}
		}

		/**
		 * Filters the Gradebook columns.
		 *
		 * @since 1.0.0
		 *
		 * @hooked LD_GB_QuickStart->setup_gradebook_mock_data() 10
		 */
		$columns = apply_filters( 'ld_gb_gradebook_columns', $columns );

		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		$columns = $this->get_user_columns();

		// Convert to a format that Sortable Columns likes
		foreach ( $columns as $key => $label ) {
			$columns[ $key ] = [ $key, false ];
		}

		if ( ld_gb_get_option_field( 'gradebook_disable_sorting_by_grades_backend' ) !== 'yes' ) {
			$columns['grade'] = [ 'grade', false ];

			if ( $this->components ) {
				foreach ( $this->components as $component ) {
					$columns[ "component_{$component['id']}" ] = [ "component_{$component['id']}", false ];
				}
			}
		}

		/**
		 * Filters the Gradebook sortable columns.
		 *
		 * @since 1.0.0
		 *
		 * @hooked LD_GB_QuickStart->setup_gradebook_mock_columns() 10
		 */
		$columns = apply_filters( 'ld_gb_gradebook_sortable_columns', $columns );

		return $columns;
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @since 1.0.0
	 *
	 * @return string Name of the primary column.
	 */
	protected function get_primary_column_name() {
		$columns = $this->get_user_columns();

		$column_name = 'display_name';

		foreach ( $columns as $column_name => $label ) {
			// We already have what we want
			break;
		}

		return $column_name;
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $item Contains all the data of the keys
	 * @param string $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $item, $column_name ) {
		$default_column = $this->get_primary_column_name();

		switch ( $column_name ) {
			case 'display_name':
			case 'first_name':
			case 'last_name':
			case 'user_email':
			case 'user_login':
				if ( $column_name == $default_column && $item['ID'] ) {
					$user_link = admin_url( "admin.php?page=learndash-gradebook-user-grades&gradebook={$this->gradebook}&user={$item['ID']}&return=gradebook&referrer=" . urlencode( $_SERVER['REQUEST_URI'] ) );

					$actions = [
						'view' => "<a href=\"{$user_link}#ld-gb-gradebook-anchor\">"
								. _x( 'View/Edit User Grades', 'View/Edit User Grades link text for the backend Gradebook', 'learndash-gradebook' ) . '</a>',
					];

					$output = "<a href=\"{$user_link}#ld-gb-gradebook-anchor\">" . esc_attr( $item[ $column_name ] ) . '</a>'
							. $this->row_actions( $actions );
				} else {
					$output = esc_attr( $item[ $column_name ] );
				}

				break;

			default:
				$output = $this->get_grade_display( $item[ $column_name ] );
		}

		/**
		 * Default output for column data in the Gradebook list table.
		 *
		 * @since 1.2.0
		 */
		$output = apply_filters( 'ld_gb_gradebook_list_table_column_default', $output, $item, $column_name );

		return $output;
	}

	/**
	 * Column output for the grade.
	 *
	 * @since 1.0.0
	 *
	 * @param array $item Contains all the data of the keys
	 *
	 * @return string Column Name
	 */
	public function column_grade( $item ) {
		return $this->get_grade_display( $item['grade'] );
	}

	/**
	 *
	 * Get a list of CSS classes for the WP_List_Table table tag.
	 *
	 * @since 1.1.0
	 *
	 * @return array List of CSS classes for the table tag.
	 */
	protected function get_table_classes() {
		$classes = parent::get_table_classes();

		$classes[] = 'ld-gb-gradebook-table';

		if ( ld_gb_get_option_field( 'gradebook_disable_sorting_by_grades_backend' ) === 'yes' ) {
			$classes[] = 'ld-gb-gradebook-table-sorting-disabled';
		}

		return $classes;
	}

	/**
	 * Retrieve the current page number
	 *
	 * @since 1.0.0
	 *
	 * @return int Current page number
	 */
	public function get_paged() {
		return isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
	}

	/**
	 * Retrieves the search query string
	 *
	 * @since 1.1.0
	 *
	 * @return mixed string If search is present, false otherwise
	 */
	public function get_search() {
		return ! empty( $_GET['s'] ) ? urldecode( trim( $_GET['s'] ) ) : false;
	}

	/**
	 * Outputs the Gradebook select box.
	 *
	 * @since 1.2.0
	 * @since 4.3.0 Removed the $gradebooks parameter
	 *
	 * @param int $active_gradebook Active Gradebook.
	 *
	 * @return void
	 */
	public function gradebook_select( int $active_gradebook ): void {
		$options = [];

		$post = get_post( $active_gradebook );

		// Set the selected Gradebook as the options key, we later will pull in the list via Ajax.
		if ( $post ) {
			$options[ $active_gradebook ] = $post->post_title;
		} else {
			$options[0] = __( 'Select a Gradebook', 'learndash-gradebook' );
		}

		ld_gb_do_field_select(
			[
				'no_init'         => true,
				'name'            => 'gradebook',
				'options'         => $options,
				'value'           => $active_gradebook,
				'wrapper_classes' => [ 'ld-gb-gradebook-selector' ],
				'id'              => 'ld-gb-gradebook-selector',
				'l10n'            => [
					'no_options' => __( 'No Gradebooks Created Yet', 'learndash-gradebook' ),
				],
				'select2_disable' => true,
			]
		);
	}

	/**
	 * Outputs the Group select box.
	 *
	 * @since 1.1.0
	 *
	 * @param array $group_IDs Groups to show
	 * @param int   $active_group_ID Active group
	 */
	public function group_select( $group_IDs, $active_group_ID ) {
		$group_options = [
			[
				'text'  => _x( '- All Users -', 'All Users option for Group Selection in the Backend Gradebook', 'learndash-gradebook' ),
				'value' => '0',
			],
		];

		foreach ( $group_IDs as $group_ID ) {
			if ( ! ( $group = get_post( $group_ID ) ) ) {
				continue;
			}

			$group_options[] = [
				'text'  => $group->post_title,
				'value' => $group_ID,
			];
		}

		ld_gb_do_field_select(
			[
				'no_init'     => true,
				'name'        => 'ld_group',
				'id'          => 'ld-gb-group-selector',
				'label'       => _x( 'Showing Gradebook for:', 'Group Selection Dropdown Label', 'learndash-gradebook' ),
				'options'     => $group_options,
				'value'       => $active_group_ID,
				'input_class' => '',
				'l10n'        => [
					'no_options' => _x( 'No Groups Available', 'No Groups found text', 'learndash-gradebook' ),
				],
			]
		);
	}

	/**
	 * Performs a much simpler query. Suited for large user-database sites.
	 *
	 * @since 1.1.5
	 */
	public function safemode_query() {
		$search = $this->get_search();

		$user_args = ld_gb_get_gradebook_get_users_args(
			$this->gradebook,
			$this->group,
			[
				'number'  => $this->per_page,
				'offset'  => $this->per_page * ( $this->get_paged() - 1 ),
				'order'   => isset( $_GET['order'] ) ? $_GET['order'] : 'asc',
				'orderby' => isset( $_GET['orderby'] ) ? $_GET['orderby'] : 'display_name',
				'search'  => $search ? "*$search*" : '',
			]
		);

		if ( in_array( $user_args['orderby'], [ 'first_name', 'last_name' ] ) ) {
			$user_args['meta_key'] = $user_args['orderby'];
			$user_args['orderby']  = 'meta_value';
		}

		$data = [];

		$query = new WP_User_Query( $user_args );
		$users = $query->get_results();

		if ( $users && ! is_wp_error( $users ) ) {
			foreach ( $users as $user ) {
				$data[ $user->ID ] = ld_gb_get_user_grade( $user->ID, $this->gradebook );
			}
		}

		/**
		 * Filters the Gradebook list table safe mode data.
		 *
		 * @since 1.1.5
		 */
		$data = apply_filters( 'ld_gb_gradebook_list_table_safemode_data', $data );

		return [
			'query'  => $query,
			'grades' => $data,
		];
	}

	/**
	 * Performs the query to get the data, in this case users.
	 *
	 * @since 1.0.0
	 */
	public function query() {
		// Safe mode query. MUCH less database intensive.
		if ( ld_gb_get_option_field( 'gradebook_disable_sorting_by_grades_backend' ) === 'yes' ) {
			return $this->safemode_query();
		}

		$params = wp_parse_args(
			$_GET,
			[
				'per_page' => $this->per_page,
				'paged'    => $this->get_paged(),
			]
		);

		$data = learndash_gradebook_get_gradebook_data( $this->gradebook, $this->group, $params );

		/**
		 * Filters the Gradebook list table data.
		 *
		 * @since 1.0.0
		 */
		$grades = apply_filters( 'ld_gb_gradebook_list_table_data', $data['grades'] );

		return $data;
	}

	/**
	 * Gets the display for the grade.
	 *
	 * @since 1.0.0
	 * @updated 4.0.0
	 * @access protected
	 *
	 * @param $score
	 *
	 * @return string HTML of the grade.
	 */
	protected function get_grade_display( $score ) {
		$grade_format = apply_filters( 'ld_gb_backend_gradebook_grade_format', ld_gb_get_option_field( 'grade_display_mode', 'letter' ), $score, $this->gradebook );

		$html = learndash_gradebook_get_grade_display( $score, $grade_format );

		$html = apply_filters( 'ld_gb_gradebook_list_table_grade_display', $html, $score );

		return $html;
	}

	/**
	 * Message to be displayed when there are no items
	 *
	 * @since 1.0.0
	 */
	public function no_items() {
		_ex( 'No users to show.', 'No Users for the requested Gradebook page', 'learndash-gradebook' );
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * @uses WP_List_Table::set_pagination_args()
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->per_page = isset( $_GET['per_page'] ) ? $_GET['per_page'] : $this->per_page;

		// Get and set columns
		$columns  = $this->get_columns();
		$hidden   = [];
		$sortable = $this->get_sortable_columns();

		$default_column = $this->get_primary_column_name();

		$this->_column_headers = [ $columns, $hidden, $sortable, $default_column ];

		$data = $this->query();

		$total_items = $data['query']->total_users;

		ld_gb_update_largest_total_users( $total_items );

		$this->items = $data['grades'];

		$this->set_pagination_args(
			[
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
				'total_pages' => ceil( $total_items / $this->per_page ),
			]
		);
	}

	/**
	 * Outputs the table data for access.
	 *
	 * @since 1.1.0
	 * @access private
	 */
	function print_data() {
		?>
		<script type="text/javascript">
			var LD_GB_GradebookData = <?php echo json_encode( $this->items ); ?>;
		</script>
		<?php
	}
}
