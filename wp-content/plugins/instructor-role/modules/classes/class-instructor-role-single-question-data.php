<?php
/**
 * Processing Single/Multiple Choice type question.
 *
 * @package LearnDash\Instructor_Role
 * @since 5.4.0
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Question_Data;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Single_Question_Data' ) ) {
	/**
	 * Instructor_Role_Single_Question_Data Class.
	 *
	 * @class Instructor_Role_Single_Question_Data
	 */
	class Instructor_Role_Single_Question_Data extends Instructor_Role_Question_Data {
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

			$ans_cnt = 0;

			$arr_user_response   = [];
			$arr_answers         = [];
			$arr_correct_answers = [];
			foreach ( $answer_data as $ans_obj ) {
				$ans_obj_answer = ir_get_protected_value( $ans_obj, '_answer' );

				$arr_answers[ $ans_cnt ] = $ans_obj_answer;
				$ans_obj_correct         = ir_get_protected_value( $ans_obj, '_correct' );
				$this->correct_options[] = $ans_obj_correct;
				if ( ! empty( $this->points_per_answer ) ) {
					$this->answer_points[] = ir_get_protected_value( $ans_obj, '_points' );
				}
				if ( 1 == $ans_obj_correct ) {
					// if correct answer, makes entry in $arr_correct_answers array.
					array_push( $arr_correct_answers, $ans_obj_answer );
				}

				if ( isset( $user_response[ $ans_cnt ] ) && 1 == $user_response[ $ans_cnt ] ) {
					// if user has selected answer, '0' if not.
					array_push( $arr_user_response, $ans_obj_answer );
				}

				++$ans_cnt;
			}
			$this->set_all_answers( $arr_answers );
			$this->set_correct_answers( $arr_correct_answers );
			$this->set_user_answers( $arr_user_response );
		}
	}
}
