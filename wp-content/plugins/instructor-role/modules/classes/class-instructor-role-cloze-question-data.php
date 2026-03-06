<?php
/**
 * Processing Cloze type question.
 *
 * @package LearnDash\Instructor_Role
 * @since 5.4.0
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Question_Data;
use LearnDash\Core\Utilities\Str;

defined( 'ABSPATH' ) || exit;

// cspell:ignore ckey, cval .

if ( ! class_exists( 'Instructor_Role_Cloze_Question_Data' ) ) {
	/**
	 * Instructor_Role_Cloze_Question_Data Class.
	 *
	 * @class Instructor_Role_Cloze_Question_Data
	 */
	class Instructor_Role_Cloze_Question_Data extends Instructor_Role_Question_Data {
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
		 * @param array $result Result Array.
		 * @param array $user_response Array of user response for a single question.
		 */
		public function __construct( $answer_data, $result, $user_response, $points_per_answer = 0 ) {
			$this->user_response     = $user_response;
			$this->points_per_answer = $points_per_answer;
			$this->process_data( $answer_data, $result );
		}

		/**
		 * Processes received raw response and answer data and assign to parent vars.
		 *
		 * @param array $answer_data Answer data.
		 * @param array $result Result Array.
		 * @return void Nothing.
		 */
		protected function process_data( $answer_data, $result ) {
			$user_response = $this->user_response;

			$arr_user_response   = [];
			$arr_answers         = [];
			$arr_correct_answers = [];
			$ir_answer           = '';

			$ans_obj             = $answer_data[0];
			$arr_correct_answers = maybe_unserialize( $result['user_answer'] );
			$answer_data         = ir_get_protected_value( $ans_obj, '_answer' );
			$arr_wdm_answer      = $ans_obj;

			// examples.
			// I {[play][love][hate]} soccer.
			// I {play} soccer, with a {ball|3}.

			$ans_obj   = isset( $answer_data ) ? $answer_data : '';
			$ir_answer = $ans_obj;

			$arr_wdm_answer = explode( '{', $ir_answer );

			$arr_options = [];

			if ( ! empty( $arr_wdm_answer ) ) {
				$arr_options = $this->get_ans_string( $arr_wdm_answer );
			}

			$arr_answers = $arr_options;

			$arr_correct_answers = $arr_options;
			foreach ( $arr_answers as $ckey => $cval ) {
				$ir_user_ckey_res = isset( $user_response[ $ckey ] ) ? $user_response[ $ckey ] : '';
				if ( array_key_exists( $ckey, $user_response ) ) {
					array_push( $arr_user_response, $ir_user_ckey_res );
				}
			}

			$this->set_all_answers( $arr_answers );
			$this->set_correct_answers( $arr_correct_answers );
			$this->set_user_answers( $arr_user_response );
			$this->set_answer_obj( $ir_answer );
		}

		/**
		 * Get Answers Array.
		 *
		 * @param Array $arr_wdm_answer Answer Array.
		 * @return Array $arr_options     Answer Array.
		 */
		public function get_ans_string( $arr_wdm_answer ) {
			$arr_options = [];
			$index       = 0;
			foreach ( $arr_wdm_answer as $cloze_key => $cloze_val ) {
				if ( ! Str::contains( $cloze_val, '}' ) ) {
					// first value never be ib.
					// Check if no closing brace.
					continue;
				}

				$arr_wdm_answer2 = explode( '}', $cloze_val );

				$ir_answer_str = isset( $arr_wdm_answer2[0] ) ? $arr_wdm_answer2[0] : '';

				if ( '' !== $ir_answer_str ) {
					if ( Str::contains( $ir_answer_str, '][' ) ) {
						$ir_answer_str      = str_replace( [ '][', ']', '[' ], [ ',', '', '' ], $ir_answer_str );
						$arr_wdm_answer_str = explode( ',', $ir_answer_str );
						foreach ( $arr_wdm_answer_str as $str ) {
							$arr_wdm_answer_str = explode( '|', $str );
							if ( isset( $arr_wdm_answer_str[1] ) ) {
								$ir_answer_str                     = $arr_wdm_answer_str[0];
								$this->correct_options[ $index ][] = true;
								if ( ! empty( $this->points_per_answer ) ) {
									$this->answer_points[ $index ][] = $arr_wdm_answer_str[1];
								}
							} else {
								$ir_answer_str                     = $arr_wdm_answer_str[0];
								$this->correct_options[ $index ][] = true;
								if ( ! empty( $this->points_per_answer ) ) {
									$this->answer_points[ $index ][] = '1';
								}
							}
							$arr_options[ $index ][] = $ir_answer_str;
						}
					} else {
						$arr_wdm_answer_str = explode( '|', $ir_answer_str );
						if ( isset( $arr_wdm_answer_str[1] ) ) {
							$ir_answer_str                     = $arr_wdm_answer_str[0];
							$this->correct_options[ $index ][] = true;
							if ( ! empty( $this->points_per_answer ) ) {
								$this->answer_points[ $index ][] = $arr_wdm_answer_str[1];
							}
						} else {
							$ir_answer_str                     = $arr_wdm_answer_str[0];
							$this->correct_options[ $index ][] = true;
							if ( ! empty( $this->points_per_answer ) ) {
								$this->answer_points[ $index ][] = '1';
							}
						}
						$ir_answer_str           = str_replace( [ '][', ']', '[' ], [ ',', '', '' ], $ir_answer_str );
						$arr_options[ $index ][] = $ir_answer_str;
					}
				}
				++$index;
			}
			return $arr_options;
		}
	}
}
