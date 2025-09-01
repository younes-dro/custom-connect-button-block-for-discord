<?php
/**
 * Custom connect button block for Discord - Service Interface
 *
 * Defines the contract for Discord integration services that
 * fetch user-related data from supported plugins.
 *
 * PHP version 7.4+
 *
 * @category Plugin
 * @package  CustomConnectButtonBlock\Interfaces
 * @author   Younes DRO <younesdro@gmail.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @version  GIT: 1.0.0
 * @link     https://github.com/younes-dro/'custom-connect-button-block-for-discord'
 * @since    1.0.0
 */

namespace Dro\CustomConnectButtonBlock\includes\Interfaces;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface Dro_CCBB_Service_Interface
 *
 * This interface outlines the methods that must be implemented by any service
 * class that integrates with the CCBB plugin.
 * It includes methods for checking plugin activation, fetching user access
 * context, retrieving active Discord roles, and providing service metadata.
 *
 * @category Plugin
 * @package  CustomConnectButtonBlock\Interfaces
 * @author   Younes DRO <younesdro@gmail.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @version  Release: 1.0.0
 * @link     https://github.com/younes-dro/custom-connect-button-block-for-discord
 * @since    1.0.0
 */
interface Dro_CCBB_Service_Interface {

	/**
	 * Detection of whether the plugin is active.
	 *
	 * @return bool True if the plugin is active, false otherwise.
	 */
	public function is_plugin_active(): bool;

	/**
	 * Gets the discord connected account for a user.
	 *
	 * @param integer $user_id
	 * @return string|null
	 */
	public function get_user_connected_account( int $user_id ): ?string;

	/**
	 * Renders the HTML for the Discord connect block.
	 * Each service must implement this method to provide the specific HTML
	 *
	 * @param array     $attributes
	 * @param string    $content
	 * @param \WP_Block $block
	 * @return string
	 */
	public function build_html_block( array $attributes, string $content, \WP_Block $block ): string;


	/**
	 * Fetches user-related context data for a specific user.
	 *
	 * This may include IDs of active membership levels, enrolled courses,
	 * or other relevant data depending on the service used.
	 *
	 * @param int $user_id The ID of the user.
	 *
	 * @return array<int>|null An array of integer IDs, or null if no data
	 *                         is found.
	 */
	public function get_user_access_context( int $user_id ): ?array;


	/**
	 * Get the service name.
	 *
	 * Returns the identifier of the associated plugin integration (e.g.,
	 * 'pmpro-discord-service', 'memberpress-discord-service', etc.).
	 * This name is used internally for settings, logging, and distinguishing
	 * between multiple service integrations.
	 *
	 * @return string The name of the service.
	 */
	public function get_service_name(): string;

	/**
	 * Get the Add-On icon URL.
	 *
	 * @return string TheURL of the service icon.
	 */
	public function get_service_icon_url(): string;

	/**
	 * Initializes the Discord user data for the given user ID.
	 *
	 * @param integer $user_id
	 * @return void
	 */
	public function load_discord_user_data( int $user_id ): void;

	/**
	 * Get single instance of the service.
	 *
	 * @return self
	 */
	public static function get_instance(): self;
}
