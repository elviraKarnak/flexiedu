<?php
/**
 * Processing Essay type question.
 *
 * @package LearnDash\Instructor_Role
 * @since 5.4.0
 */

namespace InstructorRole\Modules\Classes;

use InstructorRole\Modules\Classes\Instructor_Role_Question_Data;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Instructor_Role_Essay_Question_Data' ) ) {
	/**
	 * Instructor_Role_Essay_Question_Data Class.
	 *
	 * @class Instructor_Role_Essay_Question_Data
	 */
	class Instructor_Role_Essay_Question_Data extends Instructor_Role_Question_Data {
		/**
		 * Constructor.
		 *
		 * @param array $user_response Array of user response for a single question.
		 */
		public function __construct( $user_response ) {
			$this->user_response = $user_response;
			$this->process_data();
		}

		/**
		 * Processes received raw response and answer data and assign to parent vars.
		 *
		 * @return void Nothing.
		 */
		protected function process_data() {
			$user_response     = $this->user_response;
			$arr_user_response = [];

			if ( ! empty( $user_response ) ) {
				$ir_link = get_post_meta( $user_response['graded_id'], 'upload', true );
				if ( $ir_link ) {
					array_push( $arr_user_response, $ir_link );
				} else {
					$content_post = get_post( $user_response['graded_id'] );
					$content      = $content_post->post_content;
					array_push( $arr_user_response, $content );
				}
			} else {
				$content = '';
				array_push( $arr_user_response, $content );
			}

			$this->set_user_answers( $arr_user_response );
		}
	}
}
