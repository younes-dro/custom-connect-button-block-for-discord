<?php
/**
 * Custom connect button block for Discord
 *
 * @package   Custom connect button block for Discord
 * @author    Younes DRO <younesdro@gmail.com>
 * @since 1.0.0
 * @license  GPL-2.0-or-later https://www.gnu.org/licenses/gpl-2.0.html
 */

declare(strict_types=1);

namespace Dro\CustomConnectButtonBlock\includes;

use Dro\CustomConnectButtonBlock\includes\Dro_CCBB_Resolver as Resolver;
use Dro\CustomConnectButtonBlock\includes\Dro_CCBB_Rest_Api as Discord_Rest_Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main class for Custom connect button block for Discord.
 */
class Dro_CCBB_Main {

	/**
	 * The singleton instance of the class.
	 *
	 * @var self|null
	 */
	protected static ?self $instance = null;

	/**
	 * Get the instance of the class.
	 * The constructor is private to enforce the singleton pattern.
	 */
	private function __construct() {

		add_action( 'plugins_loaded', array( $this, 'plugin_loaded' ) );
		add_action( 'init', array( $this, 'init' ) );
	}
	/**
	 * Trigger the plugin_loaded action.
	 * Resolves the active Discord service.
	 * Initializes the Discord REST API.
	 *
	 * @return void
	 */
	public function plugin_loaded() {
		Resolver::resolve();
		Discord_Rest_Api::get_instance();
	}
	/**
	 * Get the instance of the class.
	 *
	 * @return self|null
	 */
	public static function get_instance(): ?self {
		return self::$instance ??= new self();
	}

	/**
	 * Prevent cloning of the instance.
	 *
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
	 * @throws \Exception If an attempt to unserialize the instance is made.
	 */
	public function __wakeup() {
		// Prevent unserializing of the instance.
		throw new \Exception( 'Cannot unserialize a singleton.' );
	}

	/**
	 * Initialize the plugin.
	 *
	 * This method is called on the 'init' action hook to set up the plugin.
	 */
	public function init() {
		// Load the block registration.
	}
}
