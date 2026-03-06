<?php
/**
 * Processing Assessment type question.
 *
 * @package LearnDash\Instructor_Role
 * @since 5.4.0
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Question_Data;

defined( 'ABSPATH' ) || exit;
if ( ! class_exists( 'Instructor_Role_Assessment_Question_Data' ) ) {
	/**
	 * Instructor_Role_Assessment_Question_Data Class.
	 *
	 * @class Instructor_Role_Assessment_Question_Data
	 */
	class Instructor_Role_Assessment_Question_Data extends Instructor_Role_Question_Data {
		/**
		 * Points per answer.
		 *
		 * @var int|mixed
		 */
		private $points_per_answer = false;

		/**
		 * Answer points.
		 *
		 * @var array
		 */
		private $answer_points = [];

		/**
		 * Correct options.
		 *
		 * @var array
		 */
		private $correct_options = [];

		/**
		 * Data array.
		 *
		 * @since 5.4.0
		 * @var array
		 */
		protected $data = [
			'all_answers'     => [], // All answers/options for the question.
			'correct_answers' => [], // Correct answers for this questions.
			'user_answers'    => [], // User's answers for this questions.
			'ir_answer'       => '',      // Answer String.
		];

		/*
		|--------------------------------------------------------------------------
		| Setters.
		|--------------------------------------------------------------------------
		*/

		/**
		 * Set answer string for the question.
		 *
		 * @param string $ir_answer Answer string(json) for the question.
		 *
		 * @return array $ir_answer Answer string(json) for the question.
		 */
		protected function set_answer_obj( $ir_answer ) {
			return $this->set_prop( 'ir_answer', $ir_answer );
		}

		/*
		|--------------------------------------------------------------------------
		| Getters.
		|--------------------------------------------------------------------------
		*/

		/**
		 * All answers/options for the question.
		 *
		 * @return array $ir_answer Answer string(json) for the question.
		 */
		public function get_answer_obj() {
			return $this->get_prop( 'ir_answer' );
		}

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
		 * @return void Nothing.
		 */
		protected function process_data( $answer_data ) {
			$user_response = $this->user_response;

			$arr_user_response   = [];
			$arr_answers         = [];
			$arr_correct_answers = [];
			$ir_answer           = '';

			$ans_obj = isset( $answer_data[0] ) ? $answer_data[0] : '';

			$ir_answer = ir_get_protected_value( $ans_obj, '_answer' );

			$arr_wdm_answer = explode( '{', $ir_answer );

			if ( ! empty( $arr_wdm_answer ) ) {
				$arr_wdm_answer2 = explode( '}', isset( $arr_wdm_answer[1] ) ? $arr_wdm_answer[1] : [] );
				$ir_answer_str   = isset( $arr_wdm_answer2[0] ) ? $arr_wdm_answer2[0] : '';

				$ir_answer_str = str_replace( [ '] [', ']', '[' ], [ ',', '', '' ], $ir_answer_str );

				$arr_answers[0]         = $ir_answer_str;
				$arr_correct_answers[0] = $ir_answer_str;
				$ir_actual_ans          = explode( ',', $ir_answer_str );
			}
			$ir_z_user_response = $this->get_user_response( $user_response, $ir_actual_ans );

			array_push( $arr_user_response, $ir_z_user_response );

			$this->set_all_answers( $arr_answers );
			$this->set_correct_answers( $arr_correct_answers );
			$this->set_user_answers( $arr_user_response );
			$this->set_answer_obj( $ir_answer );
		}

		/**
		 * Get_user_response.
		 * checks user response for question
		 *
		 * @param array $ir_user_response WisdmLabs User Response ;).
		 * @param array $ir_actual_ans Actual Answer.
		 * @return array $ir_z_user_response WisdmLabs ZZZ User Response.
		 *
		 * @since 5.4.0
		 */
		public function get_user_response( $ir_user_response, $ir_actual_ans ) {
			if ( ! empty( $ir_user_response ) ) {
				$ir_z_user_response = isset( $ir_user_response[0] ) ? $ir_user_response[0] : '';
			} else {
				$ir_z_user_response = '';
			}
			if ( isset( $ir_actual_ans[ $ir_z_user_response ] ) && isset( $ir_actual_ans ) && 0 !== $ir_z_user_response ) {
				$ir_z_user_response = $ir_actual_ans[ intval( $ir_z_user_response ) - 1 ];
			}

			return $ir_z_user_response;
		}
	}
}
