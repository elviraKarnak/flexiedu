<?php
/**
 * Welcome email class file.
 *
 * The class in this file is used to send welcome email to a team leader or team member when they are added to a team.
 *
 * @since 2.1.2
 *
 * @package LearnDash\Groups_Plus
 */

namespace LearnDash\Groups_Plus\Emails;

use LearnDash\Core\Utilities\Cast;
use WP_Session_Tokens;
use WP_User;

/**
 * Welcome email class.
 *
 * Sends welcome email to a team leader or team member when they are added to a team.
 *
 * @since 2.1.2
 */
class Welcome {
	/**
	 * Group ID.
	 *
	 * @since 2.1.2
	 *
	 * @var int
	 */
	private int $group_id;

	/**
	 * User data.
	 *
	 * @since 2.1.2
	 *
	 * @var array<string, string>
	 */
	private array $userdata;

	/**
	 * User type, enum: 'team_leader', 'team_member'.
	 *
	 * @since 2.1.2
	 *
	 * @var string
	 */
	private string $user_type;

	/**
	 * Sender.
	 *
	 * @since 2.1.2
	 *
	 * @var WP_User
	 */
	private WP_User $sender;

	/**
	 * Recipient.
	 *
	 * @since 2.1.2
	 *
	 * @var WP_User
	 */
	private WP_User $recipient;

	/**
	 * Recipient first name.
	 *
	 * @since 2.1.2
	 *
	 * @var string
	 */
	private string $recipient_first_name;

	/**
	 * Recipient last name.
	 *
	 * @since 2.1.2
	 *
	 * @var string
	 */
	private string $recipient_last_name;

	/**
	 * Recipient username.
	 *
	 * @since 2.1.2
	 *
	 * @var string
	 */
	private string $recipient_username;

	/**
	 * User email options.
	 *
	 * @since 2.1.2
	 *
	 * @var array<string, string>
	 */
	private array $user_email_options;

	/**
	 * Global email options.
	 *
	 * @since 2.1.2
	 *
	 * @var array<string, string>
	 */
	private array $global_email_options;

	/**
	 * Group name.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $group_name = '';

	/**
	 * Parent group name.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $parent_group_name = '';

	/**
	 * User password.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $user_password = '';

	/**
	 * Autologin link.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $autologin_link = '';

	/**
	 * Recipient email address.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $to = '';

	/**
	 * Email headers.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $headers = '';

	/**
	 * Email subject.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $email_subject = '';

	/**
	 * Email body.
	 *
	 * @since 2.1.2
	 *
	 * @var string Default empty string.
	 */
	private string $email_body = '';

	/**
	 * Welcome_Email constructor.
	 *
	 * @since 2.1.2
	 *
	 * @param int                   $user_id   User ID.
	 * @param int                   $group_id  Group ID.
	 * @param array<string, string> $userdata  User data.
	 * @param string                $user_type User type.
	 */
	public function __construct( $user_id, $group_id, $userdata, $user_type ) {
		$recipient = get_user_by( 'ID', $user_id );

		if ( ! $recipient instanceof WP_User ) {
			return;
		}

		$this->recipient = $recipient;
		$this->group_id  = $group_id;
		$this->userdata  = $userdata;
		$this->user_type = $user_type;

		$this->set_up();
	}

	/**
	 * Sends the welcome email.
	 *
	 * @since 2.1.2
	 *
	 * @return bool True if the email was sent successfully, false otherwise.
	 */
	public function send(): bool {
		if (
			empty( $this->email_subject )
			|| empty( $this->email_body )
		) {
			return false;
		}

		return learndash_emails_send(
			$this->to,
			[
				'subject'      => $this->email_subject,
				'message'      => $this->email_body,
				'content_type' => 'text/html',
			],
			$this->headers
		);
	}

	/**
	 * Sets up all necessary data.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function set_up() {
		$this->set_sender_data();

		$this->set_recipient_data();

		$this->set_email_options_data();

		$this->set_group_data();

		$this->set_autologin_link();

		$this->set_email_data();
	}

	/**
	 * Creates and sets autologin link.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function set_autologin_link(): void {
		$unique_id = uniqid();
		update_user_meta( $this->recipient->ID, 'unique_id', $unique_id );

		$session_token_manager = WP_Session_Tokens::get_instance( $this->recipient->ID );
		$this->autologin_link  = add_query_arg(
			[
				'action_autologin' => true,
				'username'         => rawurlencode( $this->recipient_username ),
				'unique_id'        => $unique_id,
				'token'            => $session_token_manager->create( time() + DAY_IN_SECONDS ),
			],
			site_url()
		);
	}

	/**
	 * Sets the sender data.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function set_sender_data(): void {
		$this->sender = wp_get_current_user();
	}

	/**
	 * Sets the recipient data.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function set_recipient_data(): void {
		$this->recipient_first_name = Cast::to_string( get_user_meta( $this->recipient->ID, 'first_name', true ) );
		$this->recipient_last_name  = Cast::to_string( get_user_meta( $this->recipient->ID, 'last_name', true ) );
		$this->recipient_username   = $this->recipient->user_login;
		$this->user_password        = ! empty( $this->userdata['user_pass'] )
			? $this->userdata['user_pass']
			: '{' . esc_html__( 'use your current password', 'learndash-groups-plus' ) . '}';
	}

	/**
	 * Sets the email options data.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function set_email_options_data(): void {
		$user_email_options = get_user_meta( $this->sender->ID, $this->user_type . '_email_data', true );
		$this->user_email_options   = is_array( $user_email_options ) ? $user_email_options : [];

		$global_email_options = get_site_option( 'email_tab', [] );
		$this->global_email_options = is_array( $global_email_options ) ? $global_email_options : [];
	}

	/**
	 * Sets the group data.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function set_group_data(): void {
		$this->group_name = get_the_title( $this->group_id );

		$parent_id = wp_get_post_parent_id( $this->group_id );

		if ( $parent_id ) {
			$this->parent_group_name = get_the_title( $parent_id );
		}
	}

	/**
	 * Sets the email data.
	 *
	 * @since 2.1.2
	 *
	 * @return void
	 */
	private function set_email_data(): void {
		$this->to       = $this->recipient->user_email;
		$this->headers  = 'Content-Type: text/html; charset=UTF-8;';
		$this->headers .= "\r\n" . ' From: ' . $this->sender->display_name . ' <' . $this->sender->user_email . '>';

		$find    = [
			'{group_name}',
			'{childgroup_name}',
			'{' . $this->user_type . '_name}',
			'{user_name}',
			'{password}',
			'{autologin}',
		];
		$replace = [
			$this->parent_group_name,
			$this->group_name,
			trim( $this->recipient_first_name . ' ' . $this->recipient_last_name ),
			$this->recipient_username,
			$this->user_password,
			$this->autologin_link,
		];

		if (
			! empty( $this->user_email_options[ $this->user_type . '_email_subject' ] )
			&& ! empty( $this->user_email_options[ $this->user_type . '_email_body' ] )
		) {
			$this->email_subject = $this->user_email_options[ $this->user_type . '_email_subject' ];
			$this->email_body    = str_replace( $find, $replace, $this->user_email_options[ $this->user_type . '_email_body' ] );
		} elseif (
			! empty( $this->global_email_options[ $this->user_type . '_subject' ] )
			&& ! empty( $this->global_email_options[ $this->user_type . '_body' ] )
		) {
			$this->email_subject = $this->global_email_options[ $this->user_type . '_subject' ];
			$this->email_body    = str_replace( $find, $replace, $this->global_email_options[ $this->user_type . '_body' ] );
		}
	}
}
