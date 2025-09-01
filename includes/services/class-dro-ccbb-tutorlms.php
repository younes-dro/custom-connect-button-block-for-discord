<?php
/**
 * Tutor LMS Discord Service Implementation
 *
 * Handles Discord integration for Tutor LMS plugin.
 *
 * @package  CustomConnectButtonBlock\Services
 * @category Plugin
 * @author   Younes DRO
 * @license  GPL-2.0-or-later
 * @version  1.0.0
 */

declare(strict_types=1);

namespace Dro\CustomConnectButtonBlock\includes\Services;

use Dro\CustomConnectButtonBlock\includes\Abstracts\Dro_CCBB_Service as Abstract_Service;
use Dro\CustomConnectButtonBlock\includes\Interfaces\Dro_CCBB_Service_Interface as Service_Interface;
use Dro\CustomConnectButtonBlock\includes\helpers\Dro_CCBB_Helper as Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service integration for Tutor LMS + Discord.
 *
 * @since 1.0.0
 */
class Dro_CCBB_TutorLms extends Abstract_Service implements Service_Interface {
	/**
	 * The singleton instance.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	protected static ?self $instance = null;
	/**
	 * Main plugin file path used to detect whether the Tutor LMS Discord add-on is active.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const PLUGIN_NAME = 'connect-discord-tutor-lms/connect-discord-tutor-lms.php';
		/**
		 * Unique internal service slug used for asset naming and identification.
		 *
		 * @since 1.0.0
		 * @var string
		 */
	private const SERVICE_NAME = 'tutor-lms-discord-service';
		/**
		 * URL to the bundled service icon in the plugin's assets directory.
		 *
		 * @since 1.0.0
		 * @var string
		 */
	private const PLUGIN_ICON = DRO_CCBB_URL . '/assets/' . self::SERVICE_NAME . '.png';
	/**
	 * Mapping of logical Discord meta keys to their Tutor LMS add-on user_meta keys.
	 *
	 * @since 1.0.0
	 * @var array<string,string>
	 */
	protected array $discord_meta_keys = array(
		'discord_username'    => '_ets_tutor_lms_discord_username',
		'discord_user_avatar' => '_ets_tutor_lms_discord_avatar',
		'discord_user_id'     => '_ets_tutor_lms_discord_user_id',
		'access_token'        => '_ets_tutor_lms_discord_access_token',
	);

	/**
	 * Flag indicating whether the Tutor LMS Discord script should be enqueued.
	 *
	 * Set to true when the disconnect button is rendered,
	 * ensuring assets are conditionally loaded only when needed.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	private $should_enqueue_script = false;

	/**
	 * Private constructor to prevent direct instantiation.
	 *
	 * This enforces the singleton pattern by restricting object creation
	 * to the `get_instance()` method.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_scripts' ) );
	}

	/**
	 * Prevent cloning of the instance.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __clone() {

		$cloning_message = sprintf(
			/* translators: %s is the class name that cannot be cloned */
			esc_html__( 'You cannot clone instance of %s', 'custom-connect-button-block-for-discord' ),
			get_class( $this )
		);
		_doing_it_wrong( __FUNCTION__, esc_html( $cloning_message ), esc_html( DRO_CCBB_VERSION ) );
	}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup() {

		$unserializing_message = sprintf(
			/* translators: %s is the class name that cannot be unserialized */
			esc_html__( 'You cannot unserialize instance of %s', 'custom-connect-button-block-for-discord' ),
			get_class( $this )
		);
		_doing_it_wrong( __FUNCTION__, esc_html( $unserializing_message ), esc_html( DRO_CCBB_VERSION ) );
	}

	/**
	 * Return the singleton instance of this service.
	 *
	 * @since 1.0.0
	 * @return Service_Interface
	 */
	public static function get_instance(): Service_Interface {
		return self::$instance ?? new self();
	}

	/**
	 * Get the dependent add-on plugin name (path) used for activation checks.
	 *
	 * @since 1.0.0
	 * @return string Plugin file path.
	 */
	protected function get_plugin_name(): string {
		return self::PLUGIN_NAME;
	}

	/**
	 * Determine whether the Ultimate Member Discord add-on is active.
	 *
	 * @since 1.0.0
	 * @return bool True if active, false otherwise.
	 */
	public function is_plugin_active(): bool {
		return $this->check_active_plugin();
	}
	/**
	 * Get the connected Discord username for the current user.
	 *
	 * Removes the discriminator suffix (e.g., #1234) if present.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 * @return string The sanitized Discord username.
	 */
	public function get_user_connected_account( int $user_id ): string {
		$discord_connected_account = $this->discord_user_name;
		if ( strpos( $discord_connected_account, '#' ) !== false ) {
			return esc_html( strstr( $discord_connected_account, '#', true ) );
		}
		return esc_html( $discord_connected_account );
	}
	/**
	 * Get the user access context used for role or permission mapping.
	 *
	 * Tutor LMS does not define membership levels; return null by default.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 * @return array<string,mixed>|null Access context or null when not applicable.
	 */
	public function get_user_access_context( int $user_id ): ?array {
		// Tutor LMS may use course enrollment or instructor roles.
		// You can implement context logic here if needed.
		return null;
	}

	/**
	 * Get the unique service name/slug.
	 *
	 * @since 1.0.0
	 * @return string Service slug.
	 */
	public function get_service_name(): string {
		return self::SERVICE_NAME;
	}

	/**
	 * Get the URL for the service icon.
	 *
	 * @since 1.0.0
	 * @return string Icon URL.
	 */
	public function get_service_icon_url(): string {
		return self::PLUGIN_ICON;
	}

	/**
	 * Build the dynamic block output for Tutor LMS Discord integration.
	 *
	 * Generates connect/disconnect buttons, user info, and role assignments
	 * depending on the user's connection state and Tutor LMS configuration.
	 *
	 * @since 1.0.0
	 * @param array     $attributes Block attributes from editor.
	 * @param string    $content    Saved block content (unused).
	 * @param \WP_Block $block      The parsed block instance.
	 * @return string Rendered HTML for the block.
	 */
	public function build_html_block( array $attributes, string $content, \WP_Block $block ): string {
		$user_id      = get_current_user_id();
		$access_token = $this->get_user_access_token();

		extract( $this->set_block_attributes( $attributes ) );

		$html = '';
		if ( function_exists( 'ets_tutor_lms_discord_check_saved_settings_status' ) ) {
			$allow_none_student = sanitize_text_field( trim( get_option( 'ets_tutor_lms_discord_allow_none_student' ) ) );
			$default_role       = sanitize_text_field( trim( get_option( 'ets_tutor_lms_discord_default_role_id' ) ) );
			if ( ets_tutor_lms_discord_check_saved_settings_status() && $access_token ) {
				$html .= $this->get_disconnect_button(
					$user_id,
					$disconnectButtonBgColor,
					$disconnectButtonTextColor,
					$loggedOutText
				);
				$html .= $this->get_user_infos( $discordConnectedAccountText, $user_id );
				$html .= $this->get_user_roles( $roleAssignedText, $user_id );
			} elseif ( ( ets_tutor_lms_discord_get_student_courses_ids( $user_id ) && $this->get_mapped_role_name() )
								|| ( $default_role != 'none' )
								|| ( $allow_none_student == 'yes' ) ) {
				$html .= $this->get_connect_button(
					$connectButtonBgColor,
					$connectButtonTextColor,
					$loggedInText
				);
				$html .= $this->get_user_roles( $roleWillAssignText, $user_id );
			}
		}

		return $html;
	}
	/**
	 * Generate the HTML markup for the Discord connect button.
	 *
	 * @since 1.0.0
	 * @param string $button_bg_color   Background color for the button.
	 * @param string $button_text_color Text color for the button.
	 * @param string $button_text       Button label text.
	 * @return string HTML markup for the connect button.
	 */
	private function get_connect_button( string $button_bg_color, string $button_text_color, string $button_text ): string {
		return sprintf(
			'<a href="?action=tutor-lms-discord-login" class="dro-ccbb-connect-button" style="background-color:%s; color:%s;">%s <i>%s</i></a>',
			esc_attr( $button_bg_color ),
			esc_attr( $button_text_color ),
			esc_html( $button_text ),
			wp_kses( Helper::get_discord_icon(), Helper::get_allowed_html() )
		);
	}
	/**
	 * Generate the HTML markup for the Discord disconnect button.
	 *
	 * Also sets the internal flag to enqueue Tutor LMS Discord scripts/styles.
	 *
	 * @since 1.0.0
	 * @param int    $user_id           The user ID.
	 * @param string $button_bg_color   Background color for the button.
	 * @param string $button_text_color Text color for the button.
	 * @param string $button_text       Button label text.
	 * @return string HTML markup for the disconnect button.
	 */
	private function get_disconnect_button( int $user_id, string $button_bg_color, string $button_text_color, string $button_text ): string {

		$this->should_enqueue_script = true;

		$button_html = sprintf(
			'<a href="#" class="dro-ccbb-disconnect-button" id="tutor-lms-discord-disconnect-discord" data-user-id="%s" style="background-color:%s; color:%s;">%s <i>%s</i></a>',
			esc_attr( $user_id ),
			esc_attr( $button_bg_color ),
			esc_attr( $button_text_color ),
			esc_html( $button_text ),
			wp_kses( Helper::get_discord_icon(), Helper::get_allowed_html() )
		);

		return $button_html . '<span class="ets-spinner"></span>';
	}
	/**
	 * Render the connected Discord account information.
	 *
	 * Includes the username and avatar image for the current user.
	 *
	 * @since 1.0.0
	 * @param string $discord_connected_account_text Label text for the account section.
	 * @param int    $user_id                        The user ID.
	 * @return string|null HTML markup for the account information, or null if unavailable.
	 */
	private function get_user_infos( $discord_connected_account_text, $user_id ): ?string {
		return sprintf(
			'<div class="user-infos"><span class="roles-text">%s</span><span class="connected-account">%s</span>%s</div>',
			esc_html( $discord_connected_account_text ),
			$this->get_user_connected_account( $user_id ),
			$this->get_user_avatar_img()
		);
	}

	/**
	 * Get user roles label(s) to display.
	 *
	 * Outputs the mapped role (if any) and/or default role with color chips.
	 *
	 * @since 1.0.0
	 * @param string $roles_text Label preceding the roles list.
	 * @param int    $user_id    The user ID.
	 * @return string|null Rendered HTML or null on failure.
	 */
	private function get_user_roles( $roles_text, $user_id ): ?string {
		$default_role                       = sanitize_text_field( trim( get_option( 'ets_tutor_lms_discord_default_role_id' ) ) );
		$ets_tutor_lms_discord_role_mapping = json_decode( get_option( 'ets_tutor_lms_discord_role_mapping' ), true );
		$all_roles                          = maybe_unserialize( get_option( 'ets_tutor_lms_discord_all_roles' ) );
		$roles_color                        = maybe_unserialize( get_option( 'ets_tutor_lms_discord_roles_color' ) );
		$enrolled_courses                   = ets_tutor_lms_discord_get_student_courses_ids( $user_id );
		$mapped_role_name                   = '';
		$user_roles_html                    = '';
		if ( is_array( $enrolled_courses ) && is_array( $all_roles ) && is_array( $ets_tutor_lms_discord_role_mapping ) ) {
			foreach ( $enrolled_courses as $key => $enrolled_course_id ) {
				if ( array_key_exists( 'course_id_' . $enrolled_course_id, $ets_tutor_lms_discord_role_mapping ) ) {

					$mapped_role_id = $ets_tutor_lms_discord_role_mapping[ 'course_id_' . $enrolled_course_id ];

					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						$mapped_role_name .= '<span> <i style="background-color:#' . dechex( $roles_color[ $mapped_role_id ] ) . '">' . $all_roles[ $mapped_role_id ] . '</i></span>';
					}
				}
			}
		}

		$default_role_name = '';
		if ( is_array( $all_roles ) ) {
			if ( $default_role != 'none' && array_key_exists( $default_role, $all_roles ) ) {
				$default_role_name = '<span><i style="background-color:#' . dechex( $roles_color[ $default_role ] ) . '"> ' . $all_roles[ $default_role ] . '</i></span>';
			}
		}

		if ( $mapped_role_name || $default_role_name ) {
			$user_roles_html .= '<span class="roles-text">' . $roles_text . '</span>';
		}
		if ( $mapped_role_name ) {
			$user_roles_html .= $mapped_role_name;
		}

		if ( $default_role_name ) {
			$user_roles_html .= $default_role_name;
		}
		return '<div class="user-infos">' . $user_roles_html . '</div>';
	}
	/**
	 * Get the mapped role name(s) for the current user based on enrolled courses.
	 *
	 * Looks up Tutor LMS → Discord role mappings and returns formatted labels.
	 *
	 * @since 1.0.0
	 * @return string Rendered HTML of mapped role names, or empty string if none found.
	 */
	private function get_mapped_role_name() {
		$user_id = (int) get_current_user_id();

		$ets_tutor_lms_discord_role_mapping = json_decode( get_option( 'ets_tutor_lms_discord_role_mapping' ), true );
		$all_roles                          = maybe_unserialize( get_option( 'ets_tutor_lms_discord_all_roles' ) );
		$roles_color                        = maybe_unserialize( get_option( 'ets_tutor_lms_discord_roles_color' ) );
		$enrolled_courses                   = ets_tutor_lms_discord_get_student_courses_ids( $user_id );
		$mapped_role_name                   = '';
		if ( is_array( $enrolled_courses ) && is_array( $all_roles ) && is_array( $ets_tutor_lms_discord_role_mapping ) ) {
			foreach ( $enrolled_courses as $key => $enrolled_course_id ) {
				if ( array_key_exists( 'course_id_' . $enrolled_course_id, $ets_tutor_lms_discord_role_mapping ) ) {

					$mapped_role_id = $ets_tutor_lms_discord_role_mapping[ 'course_id_' . $enrolled_course_id ];

					if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
						$mapped_role_name .= '<span> <i style="background-color:#' . dechex( $roles_color[ $mapped_role_id ] ) . '"></i>' . $all_roles[ $mapped_role_id ] . '</span>';
					}
				}
			}
		}
		return $mapped_role_name;
	}
	/**
	 * Conditionally enqueue Tutor LMS Discord scripts and styles.
	 *
	 * Runs during the `wp_enqueue_scripts` action and loads assets only if
	 * `$should_enqueue_script` has been set to true (e.g., when disconnect button is rendered).
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function maybe_enqueue_scripts() {
		if ( $this->should_enqueue_script ) {
			wp_enqueue_script( 'connect-discord-tutor-lms' );
			wp_enqueue_style( 'connect-discord-tutor-lms' );
		}
	}
}
