<?php
/**
 * Processing Sorting type question.
 *
 * @package LearnDash\Instructor_Role
 * @since 5.4.0
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Question_Data;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Sort_Question_Data' ) ) {
	/**
	 * Instructor_Role_Sort_Question_Data Class.
	 *
	 * @class Instructor_Role_Sort_Question_Data
	 */
	class Instructor_Role_Sort_Question_Data extends Instructor_Role_Question_Data {
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
		 * @param array   $answer_data Answer data.
		 * @param integer $question_id Question ID.
		 * @param array   $user_response Array of user response for a single question.
		 * @param integer $current_user_id Current User ID.
		 */
		public function __construct( $answer_data, $question_id, $user_response, $current_user_id, $points_per_answer = 0 ) {
			$this->user_response     = $user_response;
			$this->points_per_answer = $points_per_answer;
			$this->process_data( $answer_data, $question_id, $current_user_id );
		}

		/**
		 * Processes received raw response and answer data and assign to parent vars.
		 *
		 * @param array   $answer_data Answer data.
		 * @param integer $question_id Question ID.
		 * @param integer $current_user_id Current User ID.
		 *
		 * @return void Nothing.
		 */
		protected function process_data( $answer_data, $question_id, $current_user_id ) {
			$user_response = $this->user_response;
			$arr_answers   = [];

			$ans_cnt             = 0;
			$arr_correct_answers = [];

			foreach ( $answer_data as $ans_obj ) {
				$ans_obj_answer          = ir_get_protected_value( $ans_obj, '_answer' );
				$ans_points_answer       = ir_get_protected_value( $ans_obj, '_points' );
				$arr_answers[ $ans_cnt ] = $ans_obj_answer;
				if ( ! empty( $this->points_per_answer ) ) {
					$this->answer_points[] = $ans_points_answer;
				}
				array_push( $arr_correct_answers, $ans_obj_answer );

				++$ans_cnt;
			}

			$arr_user_response = [];

			foreach ( $arr_answers as $ans_key => $ans_val ) {
				$md5 = $this->datapos( $current_user_id, $question_id, $ans_key );
				if ( empty( $user_response ) ) {
					$res_key = false;
				} else {
					$res_key = array_search( $md5, $user_response, true );
				}

				if ( false !== $res_key && isset( $arr_answers[ $res_key ] ) ) {
					$arr_user_response[ $ans_key ] = $arr_answers[ $res_key ];
				}
				if ( ! empty( $this->points_per_answer ) ) {
					if ( $ans_val === $arr_user_response[ $ans_key ] ) {
						$this->correct_options[] = true;
					} else {
						$this->correct_options[] = false;
					}
				}
			}
			$this->set_all_answers( $arr_answers );
			$this->set_correct_answers( $arr_correct_answers );
			$this->set_user_answers( $arr_user_response );
		}
	}
}
