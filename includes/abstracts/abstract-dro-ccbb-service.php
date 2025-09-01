<?php

/**
 * Abstract class for handling shared service functionality
 *
 * This abstract class provides common functionality for Discord service
 * implementations, including plugin activation checking.
 *
 * PHP version 7.4+
 *
 * @package  CustomConnectButtonBlock\Abstracts
 * @category Plugin
 * @author   Younes DRO <younesdro@gmail.com>
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 * @version  GIT: 1.0.0
 * @link     https://github.com/younes-dro/custom-connect-button-block-for-discord
 * @since    1.0.0
 */

declare(strict_types=1);

namespace Dro\CustomConnectButtonBlock\includes\Abstracts;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract class Dro_CCBB_Service
 *
 * Provides shared functionality for Discord service implementations.
 *
 * @since    1.0.0
 */
abstract class Dro_CCBB_Service {

	/**
	 * Get the plugin name/slug for the service.
	 *
	 * @return string The plugin slug.
	 */
	abstract protected function get_plugin_name(): string;

	/**
	 * The Discord user ID.
	 *
	 * @var integer|null
	 */
	protected ?int $discord_user_id = null;
	/**
	 * The Discord user avatar.
	 *
	 * @var string|null
	 */
	protected ?string $discord_user_avatar = null;

	/**
	 * The Discord user name.
	 *
	 * @var string|null
	 */
	protected ?string $discord_user_name = null;

	/**
	 * The user discord access token
	 *
	 * @var string|null
	 */
	protected ?string $user_access_token = null;

	/**
	 * The Block attributes with default values.
	 *
	 * @var array
	 */
	protected array $attributes = array(
		'loggedInText'                => 'Connect to Discord',
		'loggedOutText'               => 'Disconnect from Discord',
		'connectButtonBgColor'        => '#77a02e',
		'connectButtonTextColor'      => '#ffffff',
		'disconnectButtonBgColor'     => '#ff0000',
		'disconnectButtonTextColor'   => '#ffffff',
		'discordConnectedAccountText' => 'Connected account:',
		'roleWillAssignText'          => 'You will be assigned the following Discord roles:',
		'roleAssignedText'            => 'You have been assigned the following Discord roles:',
	);

	/**
	 * Map of attribute keys to their sanitization callbacks.
	 *
	 * @var array
	 */
	protected array $attribute_sanitizers = array(
		'loggedInText'                => 'esc_html',
		'loggedOutText'               => 'esc_html',
		'connectButtonBgColor'        => 'esc_attr',
		'connectButtonTextColor'      => 'esc_attr',
		'disconnectButtonBgColor'     => 'esc_attr',
		'disconnectButtonTextColor'   => 'esc_attr',
		'discordConnectedAccountText' => 'esc_html',
		'roleWillAssignText'          => 'esc_html',
		'roleAssignedText'            => 'esc_html',
	);

	/**
	 * Meta key map for Discord user data.
	 * Subclasses should override this with service-specific meta keys.
	 *
	 * @var array
	 */
	protected array $discord_meta_keys = array(
		'discord_username'    => '',
		'discord_user_avatar' => '',
		'discord_user_id'     => '',
		'access_token'        => '',
	);

	/**
	 * Loads Discord user data for the current service.
	 *
	 * This method retrieves Discord-related metadata for a given user ID,
	 * using the service-specific meta key map defined in `$discord_meta_keys`.
	 * It sanitizes each value and assigns it to the corresponding internal property
	 * via setter methods.
	 *
	 * Expected keys in `$discord_meta_keys`:
	 * - 'discord_username'    → Discord username meta key
	 * - 'discord_user_avatar' → Discord avatar URL meta key
	 * - 'discord_user_id'     → Discord user ID meta key
	 *
	 * Subclasses should override `$discord_meta_keys` to provide service-specific keys.
	 *
	 * @param int $user_id The ID of the user whose Discord metadata should be loaded.
	 * @return void
	 */
	public function load_discord_user_data( int $user_id ): void {
		foreach ( $this->discord_meta_keys as $property => $meta_key ) {
			if ( empty( $meta_key ) ) {
				continue;
			}

			$value = sanitize_text_field( get_user_meta( $user_id, $meta_key, true ) );

			switch ( $property ) {
				case 'discord_username':
					$this->set_discord_user_name( $value ?: null );
					break;
				case 'discord_user_avatar':
					$this->set_discord_user_avatar( $value ?: null );
					break;
				case 'discord_user_id':
					$this->set_discord_user_id( $value ? (int) $value : null );
					break;
				case 'access_token':
					$this->set_user_access_token( $value ?: null );
					break;
			}
		}
	}



	/**
	 * Get the Discord user ID.
	 *
	 * @return int|null
	 */
	public function get_discord_user_id(): ?int {
		$discord_user_id = sanitize_text_field( get_user_meta( $user_id, '_ets_pmpro_discord_user_id', true ) );
		return $this->discord_user_id;
	}

	/**
	 * Set the Discord user ID.
	 *
	 * @param int|null $id
	 * @return void
	 */
	public function set_discord_user_id( ?int $id ): void {
		$this->discord_user_id = $id;
	}

	/**
	 * Get the Discord user avatar.
	 *
	 * @return string|null
	 */
	public function get_discord_user_avatar(): ?string {
		return $this->discord_user_avatar;
	}

	/**
	 * Set the Discord user avatar.
	 *
	 * @param string|null $avatar
	 * @return void
	 */
	public function set_discord_user_avatar( ?string $avatar ): void {
		$this->discord_user_avatar = $avatar;
	}

	/**
	 * Get the Discord user name.
	 *
	 * @return string|null
	 */
	public function get_discord_user_name(): ?string {
		return $this->discord_user_name;
	}

	/**
	 * Set the Discord user name.
	 *
	 * @param string|null $name
	 * @return void
	 */
	public function set_discord_user_name( ?string $name ): void {
		$this->discord_user_name = $name;
	}

	public function set_user_access_token( ?string $token ): void {
		$this->user_access_token = $token;
	}

	public function get_user_access_token() {
		return $this->user_access_token;
	}

	/**
	 * Merge the block attributes with defaults and sanitize them.
	 *
	 * @param array $attributes
	 * @return array
	 */
	protected function set_block_attributes( array $attributes ): array {
		$merged = wp_parse_args( $attributes, $this->attributes );

		foreach ( $merged as $key => $value ) {
			if ( isset( $this->attribute_sanitizers[ $key ] ) && is_callable( $this->attribute_sanitizers[ $key ] ) ) {
				$merged[ $key ] = call_user_func( $this->attribute_sanitizers[ $key ], $value );
			}
		}

		return $merged;
	}


	/**
	 * Check if the add-on plugin is active.
	 *
	 * @return bool True if plugin is active, false otherwise.
	 */
	protected function check_active_plugin(): bool {
		$plugin_slug    = $this->get_plugin_name();
		$active_plugins = (array) get_option( 'active_plugins', array() );
		return in_array( $plugin_slug, $active_plugins, true );
	}

	/**
	 * Gets the full URL of the user's Discord avatar.
	 *
	 * @param int    $discord_user_id
	 * @param string $user_avatar
	 * @return string
	 */
	protected function get_user_avatar_img(): string {

		if ( $this->discord_user_avatar ) {

			return '<img src="https://cdn.discordapp.com/avatars/' . esc_attr( $this->discord_user_id ) . '/' . esc_attr( $this->discord_user_avatar ) . '.png" alt="" />';
		}
		$default_avatar_url = DRO_CCBB_URL . 'assets/default-discord-avatar.png';
		if ( $default_avatar_url ) {
			return '<img src="' . esc_url( $default_avatar_url ) . '" alt="Default Discord avatar" />';
		}

		return '';
	}
}
