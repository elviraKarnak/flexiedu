<?php
/**
 * Report Export Form/AJAX request Submission
 *
 * @package LearnDash\Instructor_Role
 * @since 5.4.0
 */

namespace InstructorRole\Modules\Classes;

defined( 'ABSPATH' ) || exit;

use WP_Error;
use LDLMS_DB;
use InstructorRole\Modules\Api\Instructor_Role_Quiz_Attempts_Api_Handler;

// cspell:ignore qkey, qval, qsanswer, qstn, cust .

if ( ! class_exists( 'Instructor_Role_Quiz_Statistics_Export' ) ) {
	/**
	 * Instructor_Role_Quiz_Statistics_Export Class.
	 *
	 * @class Instructor_Role_Quiz_Statistics_Export
	 */
	class Instructor_Role_Quiz_Statistics_Export {
		/**
		 * The single instance of the class.
		 *
		 * @var Instructor_Role_Quiz_Statistics_Export
		 * @since 5.4.0
		 */
		protected static $instance = null;

		/**
		 * Instructor_Role_Quiz_Statistics_Export Instance.
		 *
		 * Ensures only one instance of Instructor_Role_Quiz_Statistics_Export is loaded or can be loaded.
		 *
		 * @since 5.4.0
		 * @static
		 * @return Instructor_Role_Quiz_Statistics_Export - instance.
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Instructor_Role_Quiz_Statistics_Export Constructor.
		 */
		public function __construct() {
		}

		/**
		 * Export request processing.
		 **/
		public function export_quiz_statistics() {
			if ( ! isset( $_GET['file_format'] ) || empty( $_GET['file_format'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification
				wp_send_json_error( new WP_Error( 'ir_rest_not_allowed', __( 'Sorry, but the file format parameter is not optional', 'wdm_instructor_role' ), [ 'status' => 401 ] ) );
				exit;
			}
			if ( ! isset( $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], 'ir_quiz_export' ) ) {
				wp_send_json_error( new WP_Error( 'ir_rest_not_allowed', __( 'Sorry, but nonce validation has failed', 'wdm_instructor_role' ), [ 'status' => 401 ] ) );
				exit;
			}

			if ( ! isset( $_GET['ref_id'] ) || empty( $_GET['ref_id'] ) ) {// phpcs:ignore WordPress.Security.NonceVerification
				wp_send_json_error( new WP_Error( 'ir_rest_not_allowed', __( 'Sorry, but the statistic ref id parameter is not optional', 'wdm_instructor_role' ), [ 'status' => 401 ] ) );
				exit;
			}
			// Don't cache the result.
			wp_suspend_cache_addition( true );

			$params['statistic_ref_id'] = $_GET['ref_id'];// phpcs:ignore WordPress.Security.NonceVerification
			$params['quiz']             = $_GET['quiz'];// phpcs:ignore WordPress.Security.NonceVerification
			$params['learner']          = $_GET['learner'];// phpcs:ignore WordPress.Security.NonceVerification

			// Get all data of the statistic.
			$quiz_attempts_api_instance = new Instructor_Role_Quiz_Attempts_Api_Handler();
			$question_data              = $quiz_attempts_api_instance->fetch_attempt_info( $params );
			$summary_data               = $quiz_attempts_api_instance->fetch_quiz_info( $params );
			if ( empty( $question_data ) ) {
				wp_send_json_error( new WP_Error( 'ir_rest_not_allowed', __( 'Something went wrong OR data not found!!!', 'wdm_instructor_role' ), [ 'status' => 404 ] ) );
				exit;
			}

			$quiz_pro_id = (int) get_post_meta( $params['quiz'], 'quiz_pro_id', true );
			$custom_data = $this->get_custom_form_data( $quiz_pro_id, $params['statistic_ref_id'] );

			// User name.
			$username = $params['learner'] > 0 ? get_userdata( $params['learner'] )->display_name : __( 'Anonymous', 'wdm_instructor_role' );

			// Quiz Title.
			$quiz_title = get_the_title( $params['quiz'] );

			// Format of Export.
			$file_format = $_GET['file_format'];// phpcs:ignore WordPress.Security.NonceVerification
			if ( 'csv' !== $file_format ) {
				if ( ! empty( $question_data ) ) {
					// This array contains data for each row, each cell.
					// Also contains style for each cell.
					wp_send_json_error( new WP_Error( 'ir_rest_not_allowed', __( 'Invalid file format!!!', 'wdm_instructor_role' ), [ 'status' => 400 ] ) );
					exit;
				}
			} else {
				if ( ! empty( $question_data ) ) {
					$table = $this->csv_table( $question_data, $summary_data, $custom_data, $params );
				}
				$table = htmlentities( $table );
			}

			// Checks for the data to be exported.
			if ( '' !== $table && '' !== $file_format ) {
				// If username and Quiz title is not empty then set file name using Username and Quiz title.
				if ( ! empty( $username ) && ! empty( $quiz_title ) ) {
					$file_name = $quiz_title . '-' . $username;
					$file_name = str_replace( ' ', '_', $file_name );
					$file_name = preg_replace( '/[^A-Za-z0-9\-]/', '', $file_name );
				} else { // Else set it to sample.
					$file_name = 'sample';
				}

				// Checks if the format is Csv.
				if ( 'csv' === $file_format ) {
					$this->create_csv_file( $table, $file_name );
				} else {
					// Create Excel sheet if format is xlsx.
					// $this->create_xls_file( $table, $file_name );.
				}
				header( 'Content-type: application/ms-excel' );
				header( 'Content-Disposition: attachment; filename=' . $file_name . '.' . $file_format );
			} else {
				wp_send_json_error( new WP_Error( 'ir_rest_not_allowed', __( 'Something went wrong OR data not found!!!', 'wdm_instructor_role' ), [ 'status' => 404 ] ) );
			}

			exit;
		}

		/**
		 * This method manages information to put in csv file.
		 *
		 * @param array $question_data Quiz Data.
		 * @param array $summary_data  Summary Data.
		 * @param array $custom_data   Custom Form Data.
		 * @param array $params        Input parameters for the request.
		 * @since 5.4.0
		 */
		public function csv_table( $question_data, $summary_data, $custom_data, $params ) {
			$table                    = '';
			$table                   .= '<table id="quiz_export_table">';
			$wisdmlabs_question_types = [
				'single'             => esc_html__( 'Single choice', 'wdm_instructor_role' ),
				'multiple'           => esc_html__( 'Multiple choice', 'wdm_instructor_role' ),
				'free_answer'        => esc_html__( 'Free choice', 'wdm_instructor_role' ),
				'sort_answer'        => esc_html__( 'Sorting choice', 'wdm_instructor_role' ),
				'matrix_sort_answer' => esc_html__( 'Matrix Sorting choice', 'wdm_instructor_role' ),
				'cloze_answer'       => esc_html__( 'Fill in the blank', 'wdm_instructor_role' ),
				'assessment_answer'  => esc_html__( 'Assessment', 'wdm_instructor_role' ),
				'essay'              => esc_html__( 'Essay / Open Answer', 'wdm_instructor_role' ),
			];

			$question_meta = $question_data['question_meta'];

			if ( isset( $question_meta ) && ! is_array( $question_meta ) ) {
				return;
			}
			if ( isset( $question_meta ) ) {
				$table .= '<tr>';
				/* translators: %s : Quiz Title. */
				$table .= '<td>' . sprintf( __( 'QUIZ TITLE: %s', 'wdm_instructor_role' ), str_replace( '&#39;', "'", html_entity_decode( get_the_title( $params['quiz'] ) ) ) ) . '</td>';
				/* translators: %s : User Name. */
				$table .= '<td>' . sprintf( __( 'USER LOGIN: %s', 'wdm_instructor_role' ), $params['learner'] > 0 ? get_userdata( $params['learner'] )->display_name : __( 'Anonymous', 'wdm_instructor_role' ) ) . '</td>';
				/* translators: %s : User ID. */
				$table .= '<td>' . sprintf( __( 'USER ID: %s', 'wdm_instructor_role' ), $params['learner'] ) . '</td>
                </tr><tr>
                    <td>' . __( 'QUESTION', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'Sorting Options (for Matrix Sort)', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'OPTIONS', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'CORRECT ANSWERS', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'USER RESPONSE', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'POINTS', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'POINTS SCORED', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'TIME TAKEN', 'wdm_instructor_role' ) . '</td>
                    <td>' . __( 'QUESTION TYPE', 'wdm_instructor_role' ) . '</td>
                </tr>';

				$question_str = '';
				foreach ( $question_meta as $qkey => $qval ) {
					$question_str .= '<tr>
                        <td>' . str_replace( '&#39;', "'", html_entity_decode( $qval['question'] ) ) . '</td>';
					$question_str .= '<td>';

					$question_str .= $this->get_question_str( $qval['sorting_options'] );

					$question_str .= '</td>';

					$question_str .= '<td>';

					$question_str .= $this->get_question_str( $qval['answers'] );

					$question_str .= '</td>';

					$question_str .= '<td>';

					$question_str .= $this->get_question_str( $qval['correct_answers'] );

					$question_str .= '</td>';

					$question_str .= '<td>';
					$cnt           = 1;
					foreach ( $qval['user_response'] as $answer ) {
						// user response.
						if ( '' !== $answer ) {
							$question_str .= $cnt . ') ' . str_replace( '&#39;', "'", html_entity_decode( $answer ) ) . '<br />';
						}
						++$cnt;
					}
					$question_str .= '</td>';

					$question_str .= '<td>' . $qval['points'] . '</td>
                        <td>' . $qval['points_scored'] . '</td>
                        <td>' . $qval['time_taken'] . '</td>
                        <td>' . $wisdmlabs_question_types[ $qval['question_type'] ] . '</td>
                    </tr>';
				}

				$table .= $question_str;

				// for custom fields - starts.
				if ( isset( $custom_data ) ) {
					$options_types        = [ 'dropdown', 'radio' ];
					$question_str_custom  = '';
					$question_str_custom .= '<tr><td>' . __( 'CUSTOM FIELDS', 'wdm_instructor_role' ) . '</td></tr>';
					foreach ( $custom_data as $cust_val ) {
						$question_str_custom .= '<tr>
                            <td>' . str_replace( '&#39;', "'", html_entity_decode( $cust_val['question'] ) ) . '</td>';

						$question_str_custom .= '<td>';

						if ( in_array( $cust_val['answer_type'], $options_types, true ) ) {
							if ( is_array( $cust_val['answer_data'] ) ) {
								$cnt = 1;
								foreach ( $cust_val['answer_data'] as $answer ) {
									// options.
									$question_str_custom .= $cnt . ') ' . str_replace( '&#39;', "'", html_entity_decode( $answer ) ) . '<br />';
									++$cnt;
								}
							} else {
								$question_str_custom .= str_replace( '&#39;', "'", html_entity_decode( $cust_val['qsanswer_data'] ) );
							}
						}

						$question_str_custom .= '</td>';

						$question_str_custom .= '<td> </td>
                                <td>' . str_replace( '&#39;', "'", html_entity_decode( $cust_val['qsanswer_data'] ) ) . '</td>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td>' . $cust_val['answer_type'] . '</td>
                            </tr>';
					}
					$table .= $question_str_custom;
				}

				// for custom fields - ends.

				$table .= '<tr>
                        <td> ' . __( 'TOTAL', 'wdm_instructor_role' ) . ' </td>
                        <td> </td>
                        <td> </td>
                        <td> </td>
                        <td>' . $summary_data['total_points'] . '</td>
                        <td>' . $summary_data['points'] . ' ( ' . $summary_data['percentage'] . '% )</td>
                        <td>' . $summary_data['time_taken'] . '</td>
                        <td> </td>
                    </tr>';
				$table .= '<tr>
                        <td> </td>
                    </tr>
                    <tr>
                        <td> </td>
                    </tr>';
			}

			$table .= '</table>';

			return $table;
		}

		/**
		 * Get_question_str Loops through array and generates string
		 *
		 * @param array $data input array.
		 * @return string String of input array
		 */
		public function get_question_str( $data = [] ) {
			$question_str = '';
			$cnt          = 1;
			foreach ( $data as $value ) {
				// correct answers.
				$question_str .= $cnt . ') ' . str_replace( '&#39;', "'", html_entity_decode( $value ) ) . '<br />';
				++$cnt;
			}

			return $question_str;
		}

		/**
		 * This method manages information to put in xlsx file.
		 *
		 * @param array $question_data Quiz Data.
		 * @param array $summary_data  Summary Data.
		 * @param array $custom_data   Custom Form Data.
		 * @param array $params        Input parameters for the request.
		 * @since 5.4.0
		 */
		public function xls_table( $question_data, $summary_data, $custom_data, $params ) {
			$table = [
				0 => [
					0 => [
						'value' => '',
						'font'  => [],
					],
				],
			];

			// For row number.
			$index                    = 0;
			$wisdmlabs_question_types = [
				'single'             => esc_html__( 'Single choice', 'wdm_instructor_role' ),
				'multiple'           => esc_html__( 'Multiple choice', 'wdm_instructor_role' ),
				'free_answer'        => esc_html__( 'Free choice', 'wdm_instructor_role' ),
				'sort_answer'        => esc_html__( 'Sorting choice', 'wdm_instructor_role' ),
				'matrix_sort_answer' => esc_html__( 'Matrix Sorting choice', 'wdm_instructor_role' ),
				'cloze_answer'       => esc_html__( 'Fill in the blank', 'wdm_instructor_role' ),
				'assessment_answer'  => esc_html__( 'Assessment', 'wdm_instructor_role' ),
				'essay'              => esc_html__( 'Essay / Open Answer', 'wdm_instructor_role' ),
			];

			$question_meta = $question_data['question_meta'];

			if ( isset( $question_meta ) && ! is_array( $question_meta ) ) {
				return;
			}

			if ( isset( $question_meta ) ) {
				/* translators: %s : Quiz Title. */
				$table[ $index ][0]['value'] = sprintf( __( 'QUIZ TITLE: %s', 'wdm_instructor_role' ), str_replace( '&#39;', "'", html_entity_decode( get_the_title( $params['quiz'] ) ) ) );
				/* translators: %s : User Name. */
				$table[ $index ][1]['value'] = sprintf( __( 'USER LOGIN: %s', 'wdm_instructor_role' ), $params['learner'] > 0 ? get_userdata( $params['learner'] )->display_name : __( 'Anonymous', 'wdm_instructor_role' ) );
				/* translators: %s : User ID. */
				$table[ $index ][2]['value'] = sprintf( __( 'USER ID: %s', 'wdm_instructor_role' ), $params['learner'] );

				$table[ $index ][0]['font'] = [
					'bold'   => 1,
					'italic' => 1,
				];
				$table[ $index ][1]['font'] = [
					'bold'   => 1,
					'italic' => 1,
				];
				$table[ $index ][2]['font'] = [
					'bold'   => 1,
					'italic' => 1,
				];

				++$index;

				$table[ $index ][0]['value'] = __( 'QUESTION', 'wdm_instructor_role' );
				$table[ $index ][1]['value'] = __( 'OPTIONS', 'wdm_instructor_role' );
				$table[ $index ][2]['value'] = __( 'CORRECT ANSWERS', 'wdm_instructor_role' );
				$table[ $index ][3]['value'] = __( 'USER RESPONSE', 'wdm_instructor_role' );
				$table[ $index ][4]['value'] = __( 'POINTS', 'wdm_instructor_role' );
				$table[ $index ][5]['value'] = __( 'POINTS SCORED', 'wdm_instructor_role' );
				$table[ $index ][6]['value'] = __( 'TIME TAKEN', 'wdm_instructor_role' );
				$table[ $index ][7]['value'] = __( 'QUESTION TYPE', 'wdm_instructor_role' );

				$table[ $index ][0]['font'] = [ 'bold' => 1 ];
				$table[ $index ][1]['font'] = [ 'bold' => 1 ];
				$table[ $index ][2]['font'] = [ 'bold' => 1 ];
				$table[ $index ][3]['font'] = [ 'bold' => 1 ];
				$table[ $index ][4]['font'] = [ 'bold' => 1 ];
				$table[ $index ][5]['font'] = [ 'bold' => 1 ];
				$table[ $index ][6]['font'] = [ 'bold' => 1 ];
				$table[ $index ][7]['font'] = [ 'bold' => 1 ];

				// For next row.
				++$index;

				foreach ( $question_meta as $qkey => $qval ) {
					$table[ $index ][0]['value'] = strip_tags( str_replace( '&#39;', "'", html_entity_decode( $qval['question'] ) ) ); // Question Column.

					// To number the options.

					// To append array values.
					$question_str  = '';
					$question_str .= str_replace( '&#39;', "'", html_entity_decode( $this->append_qstn_str( $qval['answers'] ) ) );

					$table[ $index ][1]['value'] = $question_str;
					$question_str                = '';
					$question_str               .= str_replace( '&#39;', "'", html_entity_decode( $this->append_qstn_str( $qval['correct_answers'] ) ) );

					$table[ $index ][2]['value'] = $question_str;
					$question_str                = '';

					$question_str .= str_replace( '&#39;', "'", html_entity_decode( $this->append_qstn_str( $qval['user_response'] ) ) );

					$table[ $index ][3]['value'] = $question_str;
					$question_str                = '';

					$table[ $index ][4]['value'] = $qval['points'];

					$table[ $index ][5]['value'] = $qval['points_scored'];

					$table[ $index ][6]['value'] = gmdate( 'H:i:s', $qval['time_taken'] );

					$table[ $index ][7]['value'] = $wisdmlabs_question_types[ $qval['question_type'] ];

					// Sets font color for different situations.
					if ( $qval['points'] === $qval['points_scored'] ) {
						$table[ $index ][3]['font'] = [ 'color' => [ 'rgb' => '#008000' ] ];
					} elseif ( $qval['points_scored'] <= 0 ) {
						$table[ $index ][3]['font'] = [ 'color' => [ 'rgb' => '#FF0000' ] ];
					} else {
						$table[ $index ][3]['font'] = [ 'color' => [ 'rgb' => '#0000FF' ] ];
					}
					// Next row.
					++$index;
				}

				// for custom fields - starts.
				if ( isset( $custom_data ) ) {
					// To append array values.

					$table[ $index ][0]['value'] = __( 'CUSTOM FIELDS', 'wdm_instructor_role' );
					$table[ $index ][0]['font']  = [ 'bold' => 1 ];

					$options_types = [ 'dropdown', 'radio' ];

					foreach ( $custom_data as $cust_val ) {
						$question_str_custom = '';
						++$index;
						$table[ $index ][0]['value'] = str_replace( '&#39;', "'", html_entity_decode( $cust_val['question'] ) );

						if ( in_array( $cust_val['answer_type'], $options_types, true ) ) {
							$cnt = 1;
							if ( is_array( $cust_val['answer_data'] ) ) {
								foreach ( $cust_val['answer_data'] as $answer ) {
									// options.
									$question_str_custom .= $cnt . ') ' . str_replace( '&#39;', "'", html_entity_decode( $answer ) ) . "\n";
									++$cnt;
								}
							} else {
								$question_str_custom = str_replace( '&#39;', "'", html_entity_decode( $cust_val['qsanswer_data'] ) );
							}
						}

						$table[ $index ][1]['value'] = $question_str_custom;

						$table[ $index ][2]['value'] = '';

						$table[ $index ][3]['value'] = str_replace( '&#39;', "'", html_entity_decode( $cust_val['qsanswer_data'] ) );

						$table[ $index ][4]['value'] = '';

						$table[ $index ][5]['value'] = '';

						$table[ $index ][6]['value'] = '';

						$table[ $index ][7]['value'] = $cust_val['answer_type'];
					}
					// Next row.
					++$index;
				} // for custom fields - ends.
				// For total.

				$table[ $index ][0]['value'] = __( 'TOTAL', 'wdm_instructor_role' );
				$table[ $index ][0]['font']  = [ 'bold' => 1 ];

				$table[ $index ][1]['value'] = '';

				$table[ $index ][2]['value'] = '';

				$table[ $index ][3]['value'] = '';

				$table[ $index ][4]['value'] = $summary_data['total_points'];
				$table[ $index ][4]['font']  = [ 'bold' => 1 ];

				$table[ $index ][5]['value'] = $summary_data['points'] . ' ( ' . $summary_data['percentage'] . '% )';
				$table[ $index ][5]['font']  = [ 'bold' => 1 ];

				$table[ $index ][6]['value'] = gmdate( 'H:i:s', $summary_data['time_taken'] );
				$table[ $index ][6]['font']  = [ 'bold' => 1 ];

				$table[ $index ][7]['value'] = '';
				++$index;

				// For a blank row.
				// $m as a loop variable.
				for ( $m = 0; $m < 8; $m++ ) {
					$table[ $index ][ $m ]['value'] = '';
				}
				++$index;
			}

			return $table;
		}

		/**
		 * Create Question String shown in file.
		 *
		 * @param array $qval          Questions array.
		 * @return string $question_str Question String displayed.
		 */
		public function append_qstn_str( $qval ) {
			$cnt          = 1;
			$question_str = '';
			foreach ( $qval as $answer ) {
				if ( '' !== $answer ) {
					// user response.
					$question_str .= $cnt . ') ' . $answer . "\n";
				}
				++$cnt;
			}

			return $question_str;
		}

		/**
		 * This method creates generates the csv file.
		 *
		 * @param string $table data to put in file.
		 * @param string $filename File Name.
		 **/
		public function create_csv_file( $table, $filename ) {
			// Include library which we used to convert Html to Csv data.
			include INSTRUCTOR_ROLE_ABSPATH . 'libs/Simple-Html-Dom/simple_html_dom.php';
			// Library's function to get Html from the data.
			$html = str_get_html( htmlspecialchars_decode( $table ) );
			$file = fopen( 'php://output', 'w' );

			// Checks if file opened on php output stream.
			if ( $file ) {
				// For each row of the table in Data received.
				foreach ( $html->find( 'tr' ) as $element ) {
					// For Headings.
					$th = [];
					foreach ( $element->find( 'th' ) as $row ) {
						$th[] = $row->plaintext;
					}
					// Inserts Heading into the csv file.
					if ( ! empty( $th ) ) {
						fputcsv( $file, $th );
					}
					// For cell's value.
					$td = [];
					// For each cell.
					foreach ( $element->find( 'td' ) as $row ) {
						$td[] = $row->plaintext;
					}
					// Inserts Each cell's value and points to next row.
					if ( ! empty( $td ) ) {
						fputcsv( $file, $td );
					}
				}
				// Closes the Csv file.
				fclose( $file );
			} else {
				wp_send_json_error( new WP_Error( 'ir_rest_not_allowed', __( 'File Permission Issue!!!', 'wdm_instructor_role' ), [ 'status' => 403 ] ) );
				exit;
			}
		}

		/**
		 * This method creates .xlsx file of report.
		 *
		 * @param string $table data to put in file.
		 * @param string $filename File Name.
		 *
		 * @since 5.4.0
		 */
		public function create_xls_file( $table, $filename ) {
			$spreadsheet = new Spreadsheet();

			$excel_data = [];

			$data  = json_decode( $table, true );
			$table = null;

			// Counter for $data array loop.
			$dnt = 0;

			foreach ( $data as $row_key => $row_val ) {
				$sheet = $spreadsheet->getActiveSheet();

				// Counter for $row_val array loop.
				$rnt = 0;

				foreach ( $row_val as $cell_key => $cell_val ) {
					$excel_data[ $dnt ][ $rnt ] = $cell_val['value'];

					// Setting height if multi lines present.
					if ( strpos( $cell_val['value'], "\n" ) !== false ) {
						$count_lines        = count( explode( "\n", $cell_val['value'] ) ) + 1;
						$current_row_height = $sheet->getRowDimension( $dnt + 1 )->getRowHeight();
						if ( ( 20 * $count_lines ) > $current_row_height ) {
							$sheet->getRowDimension( $dnt + 1 )->setRowHeight( 15 * $count_lines );
						}
						$current_row_height = null;
						$count_lines        = null;
					}

					// Adding formatting if required.
					if ( isset( $cell_val['font'] ) ) {
						$sheet->getStyle( chr( 65 + $rnt ) . '' . ( $dnt + 1 ) )->applyFromArray( [ 'font' => $cell_val['font'] ] );
					}

					$data[ $row_key ][ $cell_key ] = null;

					++$rnt;
				}

				// Here 8 because we have 7 columns in the file.
				for ( $snt = $rnt; $snt < 8; $snt++ ) {
					$excel_data[ $dnt ][ $snt ] = '';
				}

				++$dnt;

				$data[ $row_key ] = null;
			}

			// Adding whole data to object.
			$spreadsheet->getActiveSheet()->fromArray( $excel_data, null, 'A1' );

			// Setting auth width to all the columns.
			$spreadsheet->getActiveSheet()->getColumnDimension( 'A' )->setAutoSize( true );
			$spreadsheet->getActiveSheet()->getColumnDimension( 'B' )->setAutoSize( true );
			$spreadsheet->getActiveSheet()->getColumnDimension( 'C' )->setAutoSize( true );
			$spreadsheet->getActiveSheet()->getColumnDimension( 'D' )->setAutoSize( true );
			$spreadsheet->getActiveSheet()->getColumnDimension( 'E' )->setAutoSize( true );
			$spreadsheet->getActiveSheet()->getColumnDimension( 'F' )->setAutoSize( true );
			$spreadsheet->getActiveSheet()->getColumnDimension( 'G' )->setAutoSize( true );
			$spreadsheet->getActiveSheet()->getColumnDimension( 'H' )->setAutoSize( true );

			$sheet = $spreadsheet->getActiveSheet();

			// ob_end_clean(); // Don't know why this was added.
			// Object to write into the file and save in Php output stream.
			$writer = new Xlsx( $spreadsheet );
			$file   = 'php://output';

			$writer->save( $file );

			$spreadsheet->disconnectWorksheets();
			unset( $spreadsheet );
		}

		/**
		 * This method is used to fetch the custom fields data answers corresponding to the quiz attempt.
		 *
		 * @param int $quiz_pro_id      Pro Quiz ID.
		 * @param int $statistic_ref_id Statistics Ref ID.
		 */
		public function get_custom_form_data( $quiz_pro_id, $statistic_ref_id ) {
			global $wpdb;
			$custom_fields_table = LDLMS_DB::get_table_name( 'quiz_form', 'wpproquiz' );
			$statistic_ref_table = LDLMS_DB::get_table_name( 'quiz_statistic_ref', 'wpproquiz' );

			// First get the form questions and options.
			$custom_form_query = $wpdb->prepare( "SELECT form_id, fieldname, type, data FROM {$custom_fields_table} WHERE quiz_id=%d ORDER BY sort ASC;", $quiz_pro_id );

			// Check for SQL injection.
			if ( preg_match( '[update|delete|drop|alter]', strtolower( $custom_form_query ) ) === true ) {
				throw new \Exception( 'No cheating' );
			}

			$custom_form_data = $wpdb->get_results( $custom_form_query, ARRAY_A );

			// Second get the form response by the user.
			$custom_form_attempt_query = $wpdb->prepare( "SELECT form_data FROM {$statistic_ref_table} WHERE statistic_ref_id=%d;", $statistic_ref_id );

			// Check for SQL injection.
			if ( preg_match( '[update|delete|drop|alter]', strtolower( $custom_form_attempt_query ) ) === true ) {
				throw new \Exception( 'No cheating' );
			}

			$custom_form_attempt_data = maybe_unserialize( $wpdb->get_var( $custom_form_attempt_query ) );

			if ( '' !== $custom_form_attempt_data ) {
				$custom_form_attempt_data = json_decode( $custom_form_attempt_data, 1 );
			}

			// Last, process the data for later use in export methods.
			$arr_custom_process = [];
			$arr_custom_data    = [];

			foreach ( $custom_form_data as $cust_question ) {
				$arr_custom_process['question']    = $cust_question['fieldname'];
				$arr_custom_process['answer_type'] = $cust_question['type'];
				$arr_custom_process['answer_data'] = $cust_question['data'];

				$form_id = $cust_question['form_id']; // id of custom question.

				$arr_custom_process['qsanswer_data'] = isset( $custom_form_attempt_data[ $form_id ] ) ? $custom_form_attempt_data[ $form_id ] : '';

				array_push( $arr_custom_data, $arr_custom_process );
			}

			foreach ( $arr_custom_data as $index => $custom_value ) {
				if ( ! is_array( $custom_value ) ) {
					continue;
				}

				$cust_question_type = $custom_value['answer_type'];

				switch ( $cust_question_type ) {
					case '0': // text.
						$arr_custom_data[ $index ]['answer_type'] = 'text';
						break;

					case '1': // textarea.
						$arr_custom_data[ $index ]['answer_type'] = 'textarea';
						break;

					case '2': // number.
						$arr_custom_data[ $index ]['answer_type'] = 'number';
						break;

					case '3': // checkbox.
						$arr_custom_data[ $index ]['answer_type']   = 'checkbox';
						$arr_custom_data[ $index ]['qsanswer_data'] = ( '' !== $custom_value['qsanswer_data'] ) ? ( ( '1' == $custom_value['qsanswer_data'] ) ? __( 'Yes', 'wdm_instructor_role' ) : __( 'No', 'wdm_instructor_role' ) ) : '';
						break;

					case '4': // email.
						$arr_custom_data[ $index ]['answer_type'] = 'email';
						break;

					case '5': // yes/no.
						$arr_custom_data[ $index ]['answer_type']   = 'yes/no';
						$arr_custom_data[ $index ]['qsanswer_data'] = ( '' !== $custom_value['qsanswer_data'] ) ? ( ( '1' == $custom_value['qsanswer_data'] ) ? __( 'Yes', 'wdm_instructor_role' ) : __( 'No', 'wdm_instructor_role' ) ) : '';
						break;

					case '6': // date.
						$arr_custom_data[ $index ]['answer_type'] = 'date';
						break;

					case '7': // dropdown menu.
						$arr_custom_data[ $index ]['answer_type'] = 'dropdown';

						$arr_custom_data[ $index ]['answer_data'] = json_decode( $custom_value['answer_data'] );

						break;

					case '8': // radio buttons.
						$arr_custom_data[ $index ]['answer_type'] = 'radio';
						$arr_custom_data[ $index ]['answer_data'] = json_decode( $custom_value['answer_data'] );
						break;

					default:
						break;
				}
			}

			return $arr_custom_data;
		}
	}
}
