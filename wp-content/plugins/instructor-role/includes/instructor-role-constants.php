<?php
/**
 * Instructor Role Constants
 *
 * @package LearnDash\Instructor_Role
 */

defined( 'ABSPATH' ) || exit;

// Review course constant.
if ( ! defined( 'WDMIR_REVIEW_COURSE' ) ) {
	$wdmir_admin_settings = get_option( '_wdmir_admin_settings', [] );
	// If Review Course setting is enabled.
	if ( isset( $wdmir_admin_settings['review_course'] ) && '1' == $wdmir_admin_settings['review_course'] ) {
		define( 'WDMIR_REVIEW_COURSE', true );
	} else {
		define( 'WDMIR_REVIEW_COURSE', false );
	}
}

// Review product constant.
if ( ! defined( 'WDMIR_REVIEW_PRODUCT' ) ) {
	$wdmir_admin_settings = get_option( '_wdmir_admin_settings', [] );
	// If Review Product setting is enabled.
	if ( isset( $wdmir_admin_settings['review_product'] ) && '1' == $wdmir_admin_settings['review_product'] ) {
		define( 'WDMIR_REVIEW_PRODUCT', true );
	} else {
		define( 'WDMIR_REVIEW_PRODUCT', false );
	}
}

// Review download constant v3.0.0.
if ( ! defined( 'WDMIR_REVIEW_DOWNLOAD' ) ) {
	$wdmir_admin_settings = get_option( '_wdmir_admin_settings', [] );
	// If Review Product setting is enabled.
	if ( isset( $wdmir_admin_settings['review_download'] ) && '1' == $wdmir_admin_settings['review_download'] ) {
		define( 'WDMIR_REVIEW_DOWNLOAD', true );
	} else {
		define( 'WDMIR_REVIEW_DOWNLOAD', false );
	}
}

// Define core modules meta key constant.
if ( ! defined( 'IR_CORE_MODULES_META_KEY' ) ) {
	define( 'IR_CORE_MODULES_META_KEY', 'ir_active_modules' );
}

global $wdm_ar_post_types;

// array of all custom post types of LD posts.
$wdm_ar_post_types = [
	'sfwd-assignment',
	'sfwd-certificates',
	'sfwd-courses',
	'sfwd-lessons',
	'sfwd-quiz',
	'sfwd-topic',
	'sfwd-essays', // added in v2.4.0.
	'sfwd-question', // added in v2.6.0.
	'achievement-type',
	'elementor_library',
	'students_voice',   // added in v3.5.0.
	'groups',
	'ld-exam',
	'llms_student_notes', // added in v5.9.4. cspell:disable-line -- llms is a prefix in Notes.
];


// Define review update default message.
if ( ! defined( 'IR_REVIEW_UPDATE_NOTICE' ) ) {
	$message = <<<NOTICE
<div class="notice notice-{type} is-dismissible">
    <p>{message}</p>
</div>
NOTICE;
	define( 'IR_REVIEW_UPDATE_NOTICE', $message );
}

// Default quiz completion email subject.
if ( ! defined( 'IR_DEFAULT_QUIZ_COMP_EMAIL_SUB' ) ) {
	define( 'IR_DEFAULT_QUIZ_COMP_EMAIL_SUB', 'Quiz Completion Notification: $quizname' );
}

// Default quiz completion email body.
if ( ! defined( 'IR_DEFAULT_QUIZ_COMP_EMAIL_BODY' ) ) {
	define(
		'IR_DEFAULT_QUIZ_COMP_EMAIL_BODY',
		'We wanted to inform you that user $username has completed the quiz "$quizname" associated with the course you authored or shared.

		<strong>Quiz Result:</strong> $result% <strong>Points Achieved:</strong> $points'
	);
}

// Default course purchase email subject.
if ( ! defined( 'IR_DEFAULT_COURSE_PURCHASE_EMAIL_SUB' ) ) {
	define( 'IR_DEFAULT_COURSE_PURCHASE_EMAIL_SUB', 'New Purchase Notification: [course_title]' );
}

// Default course purchase email body.
if ( ! defined( 'IR_DEFAULT_COURSE_PURCHASE_EMAIL_BODY' ) ) {
	define(
		'IR_DEFAULT_COURSE_PURCHASE_EMAIL_BODY',
		'Hello [ins_first_name] [ins_last_name],
		We\'re excited to inform you that a new purchase has been made for your course "[course_title]" by [customer_name].

		[site_name] is pleased to see the continued interest in your course and the value it offers to our students .

		If you have any questions or need further information, feel free to reach out . Keep up the great work !'
	);
}

// Default course review email subject - admin.
if ( ! defined( 'IR_DEFAULT_COURSE_REVIEW_ADMIN_EMAIL_SUB' ) ) {
	define( 'IR_DEFAULT_COURSE_REVIEW_ADMIN_EMAIL_SUB', 'Course Update Notification to Admin' );
}

// Default course review email body - admin.
if ( ! defined( 'IR_DEFAULT_COURSE_REVIEW_ADMIN_EMAIL_BODY' ) ) {
	define(
		'IR_DEFAULT_COURSE_REVIEW_ADMIN_EMAIL_BODY',
		'This is to notify you that Instructor [ins_first_name] [ins_last_name] has made updates to the following course and is awaiting your approval:

		<strong> Instructor Profile: </strong>
		<ul>
			<li> Instructor First Name: [ins_first_name] </li>
			<li> Instructor Last Name: [ins_last_name] </li>
			<li> Instructor Login ID: [ins_login] </li>
		</ul>
		<strong> Course Information: </strong>
		<ul>
			<li> Course ID: [course_id] </li>
			<li> Course Title: [course_title] </li>
			<li> Updated Date & Time: [course_update_datetime] </li>
		</ul> '
	);
}

// Default course review email subject - instructor.
if ( ! defined( 'IR_DEFAULT_COURSE_REVIEW_INST_EMAIL_SUB' ) ) {
	define( 'IR_DEFAULT_COURSE_REVIEW_INST_EMAIL_SUB', 'Course Update Approval: array( course_content_title )' );
}

// Default course review email body - instructor.
if ( ! defined( 'IR_DEFAULT_COURSE_REVIEW_INST_EMAIL_BODY' ) ) {
	define(
		'IR_DEFAULT_COURSE_REVIEW_INST_EMAIL_BODY',
		'We are pleased to inform you that your recent updates to the course "[course_content_title]" have been approved . Here are the details:

		<strong> Course Information: </strong>
		<ul>
			<li> Course ID: [course_id] </li>
			<li> Course Title: [course_title] </li>
		</ul>

		<strong> Edited Course Information: </strong>
		<ul>
			<li> Edited Course Title: [course_content_title] </li>
			<li> Course Link: [course_permalink] </li>
		</ul>
		<strong> Dashboard Link: </strong> [course_content_edit]'
	);
}

// Default product review email subject - admin.
if ( ! defined( 'IR_DEFAULT_PRODUCT_REVIEW_ADMIN_EMAIL_SUB' ) ) {
	define( 'IR_DEFAULT_PRODUCT_REVIEW_ADMIN_EMAIL_SUB', 'Product Update Notification: array( product_title )' );
}

// Default product review email body - admin.
if ( ! defined( 'IR_DEFAULT_PRODUCT_REVIEW_ADMIN_EMAIL_BODY' ) ) {
	define(
		'IR_DEFAULT_PRODUCT_REVIEW_ADMIN_EMAIL_BODY',
		'Hello,
		We\'d like to inform you that Instructor [ins_first_name] [ins_last_name] has updated the product titled "[product_title]".

		<strong>Instructor Profile:</strong>
		<ul>
			<li>Instructor Profile Link: [ins_profile_link]</li>
			<li>Instructor First Name: [ins_first_name]</li>
			<li>Instructor Last Name: [ins_last_name]</li>
			<li>Instructor Login ID: [ins_login]</li>
		</ul>
		<strong>Product Information:</strong>
		<ul>
			<li>Product ID: [product_id]</li>
			<li>Product Title: [product_title]</li>
			<li>Permalink of Product: [product_permalink]</li>
			<li>Updated Date & Time: [product_update_datetime]</li>
		</ul>
		This update might include changes or improvements to the product. Please review the updates.'
	);
}

// Default product review email subject - instructor.
if ( ! defined( 'IR_DEFAULT_PRODUCT_REVIEW_INST_EMAIL_SUB' ) ) {
	define( 'IR_DEFAULT_PRODUCT_REVIEW_INST_EMAIL_SUB', 'Product Update Approval: [product_title]' );
}

// Default product review email body - instructor.
if ( ! defined( 'IR_DEFAULT_PRODUCT_REVIEW_INST_EMAIL_BODY' ) ) {
	define(
		'IR_DEFAULT_PRODUCT_REVIEW_INST_EMAIL_BODY',
		'Dear [ins_first_name] [ins_last_name],
		We are pleased to inform you that the updates made to the product "[product_title]" have been approved.

		<strong>Product Information:</strong>
		<ul>
			<li>Product ID: [product_id]</li>
			<li>Product Title: [product_title]</li>
			<li>Permalink of Product: [product_permalink]</li>
			<li>Updated Date & Time: [product_update_datetime]</li>
		</ul>
		Your changes have been successfully implemented and approved. Thank you for your contribution to the product\'s improvement.'
	);
}
