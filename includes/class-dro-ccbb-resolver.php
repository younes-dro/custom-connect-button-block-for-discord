<?php
/**
 * * Class Dro_CCBB_Resolver
 * * This class will detect which services sould be used.
 *
 * * In case more than one service is available, it will use the first one.
 *
 * @package Custom connect button block for Discord
 */

declare(strict_types=1);
namespace Dro\CustomConnectButtonBlock\includes;

use Dro\CustomConnectButtonBlock\includes\Interfaces\Dro_CCBB_Service_Interface as Service_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Dro_CCBB_Resolver
 * This class is responsible for resolving the active Discord service based on installed plugins.
 * It checks for active plugins and returns the corresponding service instance.
 * The priority order is defined in the $priority_map array.
 *
 * @package CustomConnectButtonBlock\includes
 * @since 1.0.0
 * @version 1.0.0
 * @author Younes DRO<younesdro@gmail.com>
 * @license GPL-2.0-or-later
 */
class Dro_CCBB_Resolver {

	/**
	 * The active service instance.
	 * This property holds the currently active Discord service instance.
	 * It is initialized to null and will be set when the resolve method is called.
	 *
	 * @var Service_Interface|null
	 */
	protected static ?Service_Interface $active_service = null;

	/**
	 * Array mapping plugin slugs to service class suffixes.
	 *
	 * The key is the plugin slug as stored in the 'active_plugins' option.
	 * The value is the service class name suffix (without the 'Dro_AIO_Discord_' prefix).
	 *
	 * This array defines the priority order for resolving active Discord services
	 * based on which supported plugin is currently active.
	 *
	 * Supported add-ons:
	 * - PMPro Discord Add-On
	 * - MemberPress Discord Add-On
	 * - Ultimate Member Discord Add-On
	 * - Connect Tutor LMS to Discord
	 *
	 * @var array<string, string>
	 */

	private static array $priority_map = array(
		'pmpro-discord-add-on/pmpro-discord.php' => 'Pmpro',
		'expresstechsoftwares-memberpress-discord-add-on/memberpress-discord.php' => 'MemberPress',
		'ultimate-member-discord-add-on/ultimate-member-discord-add-on.php' => 'UltimateMember',
		'connect-tutorlms-to-discord/connect-discord-tutor-lms.php' => 'TutorLms',

	);

	/**
	 * Checks if a required plugin is active.
	 *
	 * @param string $plugin_file Relative plugin path (e.g., 'pmpro/pmpro.php').
	 * @return bool
	 */
	protected static function is_plugin_active( string $plugin_file ): bool {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( $plugin_file );
	}

	/**
	 * Resolves and sets the active service, based on installed/active add-ons.
	 *
	 * @return Service_Interface|null
	 */
	public static function resolve(): ?Service_Interface {

		if ( null !== self::$active_service ) {
			return self::$active_service;
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		foreach ( self::$priority_map as $plugin_slug => $service_key ) {
			if ( is_plugin_active( $plugin_slug ) ) {
				self::$active_service = self::set_active_service( $service_key );
				break;
			}
		}

		return self::$active_service;
	}

	/**
	 * Sets the active service based on the provided service name.
	 *
	 * @param string $service_name The name of the service to set as active.
	 * @return Service_Interface|null
	 */
	public static function set_active_service( string $service_name ): ?Service_Interface {
		$service_class = DRO_CCBB_SERVICE_PREFIX . ucfirst( $service_name );

		if ( class_exists( $service_class ) ) {
			return $service_class::get_instance();
		}
		return null;
	}

	/**
	 * Gets the currently active service instance.
	 *
	 * @return Service_Interface|null
	 */
	public static function get_active_service(): ?Service_Interface {
		if ( null === self::$active_service ) {
			self::resolve();
		}
		return self::$active_service;
	}
}
