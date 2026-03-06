<?php
/**
 * Processing Free type question.
 *
 * @package LearnDash\Instructor_Role
 * @since 5.4.0
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Question_Data;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Free_Question_Data' ) ) {
	/**
	 * Instructor_Role_Free_Question_Data Class.
	 *
	 * @class Instructor_Role_Free_Question_Data
	 */
	class Instructor_Role_Free_Question_Data extends Instructor_Role_Question_Data {
		private $points_per_answer = false;

		/**
		 * Answer points.
		 *
		 * @var array<mixed>
		 */
		private $answer_points = [];

		/**
		 * Correct options.
		 *
		 * @var array<mixed>
		 */
		private $correct_options = [];

		public function get_answer_points() {
			return $this->answer_points;
		}

		public function get_correct_options() {
			return $this->correct_options;
		}
		/**
		 * Constructor.
		 *
		 * @param array $answer_data Answer data.
		 * @param array $user_response Array of user response for a single question.
		 */
		public function __construct( $answer_data, $user_response, $points_per_answer = 0 ) {
			$this->user_response     = $user_response;
			$this->points_per_answer = $points_per_answer;
			$this->process_data( $answer_data );
		}

		/**
		 * Processes received raw response and answer data and assign to parent vars.
		 *
		 * @param array $answer_data Answer data.
		 *
		 * @return void Nothing.
		 */
		protected function process_data( $answer_data ) {
			$user_response = $this->user_response;

			$arr_answers         = [];
			$arr_correct_answers = [];
			$ans_obj             = isset( $answer_data[0] ) ? $answer_data[0] : '';
			$ans_obj_answer      = ir_get_protected_value( $ans_obj, '_answer' );

			$ans_obj_answer      = str_replace( [ "\r", "\n" ], ',', $ans_obj_answer );
			$arr_correct_answers = explode( ',', $ans_obj_answer );
			$arr_correct_answers = array_filter( $arr_correct_answers );
			$arr_correct_answers = array_values( $arr_correct_answers );

			// Because "free answer" type questions have correct answers as same as options.
			$arr_answers = $arr_correct_answers;
			if ( ! empty( $this->points_per_answer ) ) {
				foreach ( $arr_answers as &$answer ) {
					$this->correct_options[] = true;
					$this->answer_points[]   = end( explode( '|', $answer ) );
					$answer                  = reset( explode( '|', $answer ) );
				}
				$arr_correct_answers = $arr_answers;
			}

			if ( '' !== $ans_obj ) {
				$arr_user_response = $user_response;
			}
			$this->set_all_answers( $arr_answers );
			$this->set_correct_answers( $arr_correct_answers );
			$this->set_user_answers( $arr_user_response );
		}
	}
}
