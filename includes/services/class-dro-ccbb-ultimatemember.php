<?php
/**
 * Ultimate Member Discord Service Implementation
 *
 * Handles Discord integration for Ultimate Member plugin.
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
 * Service integration for Ultimate Member + Discord.
 *
 * @since 1.0.0
 */
class Dro_CCBB_UltimateMember extends Abstract_Service implements Service_Interface {

	/**
	 * The singleton instance.
	 *
	 * @since 1.0.0
	 * @var self|null
	 */
	protected static ?self $instance = null;

	/**
	 * Main plugin file path used to detect whether the Ultimate Member Discord add-on is active.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const PLUGIN_NAME = 'ultimate-member-discord-add-on/ultimate-member-discord-add-on.php';

	/**
	 * Unique internal service slug used for asset naming and identification.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const SERVICE_NAME = 'ultimate-member-discord-service';

	/**
	 * URL to the bundled service icon in the plugin's assets directory.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const PLUGIN_ICON = DRO_CCBB_URL . '/assets/' . self::SERVICE_NAME . '.png';

	/**
	 * Mapping of logical Discord meta keys to their Ultimate Member add-on user_meta keys.
	 *
	 * @since 1.0.0
	 * @var array<string,string>
	 */
	protected array $discord_meta_keys = array(
		'discord_username'    => '_ets_ultimatemember_discord_username',
		'discord_user_avatar' => '_ets_ultimatemember_discord_avatar',
		'discord_user_id'     => '_ets_ultimatemember_discord_user_id',
		'access_token'        => '_ets_ultimatemember_discord_access_token',
	);

	/**
	 * Private constructor to prevent direct instantiation.
	 *
	 * This enforces the singleton pattern by restricting object creation
	 * to the `get_instance()` method.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
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
	 * Ultimate Member does not define membership levels; return null by default.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 * @return array<string,mixed>|null Access context or null when not applicable.
	 */
	public function get_user_access_context( int $user_id ): ?array {
		// Ultimate Member doesn't use membership levels like PMPro.
		// You may implement role-based logic here if needed.
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
	 * Build and return the HTML markup for the block frontend.
	 *
	 * Renders connect/disconnect UI, user info, and mapped roles based on the user's state.
	 *
	 * @since 1.0.0
	 * @param array     $attributes Block attributes.
	 * @param string    $content    Block content (unused).
	 * @param \WP_Block $block      Block instance.
	 * @return string Rendered HTML.
	 */
	public function build_html_block( array $attributes, string $content, \WP_Block $block ): string {
		$user_id      = get_current_user_id();
		$access_token = $this->get_user_access_token();

		extract( $this->set_block_attributes( $attributes ) );

		$html = '';

		if ( ultimatemember_discord_check_saved_settings_status() && $access_token ) {
			$html .= $this->get_disconnect_button(
				$user_id,
				$disconnectButtonBgColor,
				$disconnectButtonTextColor,
				$loggedOutText
			);
			$html .= $this->get_user_infos( $discordConnectedAccountText, $user_id );
			$html .= $this->get_user_roles( $roleAssignedText, $user_id );
		} else {
			$html .= $this->get_connect_button(
				$connectButtonBgColor,
				$connectButtonTextColor,
				$loggedInText
			);
			$html .= $this->get_user_roles( $roleWillAssignText, $user_id );
		}

		return $html;
	}

	/**
	 * Generates the HTML markup for the Discord connect button.
	 *
	 * @param string $button_bg_color   Background color for the button.
	 * @param string $button_text_color Text color for the button.
	 * @param string $button_text       Text displayed on the button.
	 *
	 * @return string HTML markup for the connect button.
	 */
	private function get_connect_button( string $button_bg_color, string $button_text_color, string $button_text ): string {

		return sprintf(
			'<a href="?action=ultimate-discord" class="dro-ccbb-connect-button" style="background-color:%s; color:%s;">%s <i>%s</i></a>',
			esc_attr( $button_bg_color ),
			esc_attr( $button_text_color ),
			esc_html( $button_text ),
			wp_kses( Helper::get_discord_icon(), Helper::get_allowed_html() )
		);
	}

	/**
	 * Generates the HTML markup for the Discord disconnect button.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param int    $user_id           ID of the user to disconnect.
	 * @param string $button_bg_color   Background color for the button.
	 * @param string $button_text_color Text color for the button.
	 * @param string $button_text       Text displayed on the button.
	 *
	 * @return string HTML markup for the disconnect button.
	 */
	private function get_disconnect_button( int $user_id, string $button_bg_color, string $button_text_color, string $button_text ): string {
		wp_enqueue_script( 'ultimate-member-discord-add-on' );
		wp_enqueue_style( 'ultimate-member-discord-add-on' );

		$button_html = sprintf(
			'<a href="#" class="dro-ccbb-disconnect-button" id="ultimate-member-disconnect-discord" data-user-id="%s" style="background-color:%s; color:%s;">%s <i>%s</i></a>',
			esc_attr( $user_id ),
			esc_attr( $button_bg_color ),
			esc_attr( $button_text_color ),
			esc_html( $button_text ),
			wp_kses( Helper::get_discord_icon(), Helper::get_allowed_html() )
		);

		return $button_html . '<span class="ets-spinner"></span>';
	}

	/**
	 * Get user information (Discord username and avatar).
	 *
	 * @since 1.0.0
	 * @param string $discord_connected_account_text Label preceding the connected account.
	 * @param int    $user_id                        The user ID.
	 * @return string|null Rendered HTML or null on failure.
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

		$default_role                            = sanitize_text_field( trim( get_option( 'ets_ultimatemember_discord_default_role_id' ) ) );
		$ets_ultimatemember_discord_role_mapping = json_decode( get_option( 'ets_ultimatemember_discord_role_mapping' ), true );
		$all_roles                               = maybe_unserialize( get_option( 'ets_ultimatemember_discord_all_roles' ) );
		$roles_color                             = maybe_unserialize( get_option( 'ets_ultimatemember_discord_roles_color' ) );
		$curr_level_id                           = ets_ultimatemember_discord_get_current_level_id( $user_id );
		$user_roles_html                         = '';
		$mapped_role_name                        = '';
		if ( $curr_level_id && is_array( $all_roles ) ) {
			if ( is_array( $ets_ultimatemember_discord_role_mapping ) && array_key_exists( 'ultimate-member_level_id_' . $curr_level_id, $ets_ultimatemember_discord_role_mapping ) ) {
				$mapped_role_id = $ets_ultimatemember_discord_role_mapping[ 'ultimate-member_level_id_' . $curr_level_id ];
				if ( array_key_exists( $mapped_role_id, $all_roles ) ) {
					$mapped_role_name .= '<span> <i style="background-color:#' . dechex( $roles_color[ $mapped_role_id ] ) . '">' . $all_roles[ $mapped_role_id ] . '</i></span>';
				}
			}
		}
		$default_role_name = '';
		if ( $default_role != 'none' && is_array( $all_roles ) && array_key_exists( $default_role, $all_roles ) ) {
			$default_role_name = '<span><i style="background-color:#' . dechex( $roles_color[ $default_role ] ) . '">' . $all_roles[ $default_role ] . '</i></span>';
		}

		if ( $mapped_role_name || $default_role_name ) {
			$user_roles_html .= '<span class="roles-text">' . $roles_text . '</span>';
		}
		if ( $mapped_role_name ) {
			$user_roles_html .= ets_ultimatemember_discord_allowed_html( $mapped_role_name );
		}

		if ( $default_role_name ) {
			$user_roles_html .= ets_ultimatemember_discord_allowed_html( $default_role_name );
		}

		return '<div class="user-infos">' . $user_roles_html . '</div>';
	}
}
