<?php
/**
 * Mock helper for Discord Service tests.
 *
 * @package CustomConnectButtonBlock\Tests\Helpers
 */

namespace CustomConnectButtonBlock\Tests\Helpers;

/**
 * Mock helper class for Discord Service testing.
 */
class Discord_Service_Mock_Helper {

	/**
	 * Original active_plugins option value.
	 *
	 * @var mixed
	 */
	private static $original_active_plugins;

	/**
	 * Setup mock environment.
	 */
	public static function setup() {
		// Store original value
		self::$original_active_plugins = get_option( 'active_plugins' );
	}

	/**
	 * Cleanup mock environment.
	 */
	public static function cleanup() {
		// Restore original value
		if ( self::$original_active_plugins !== false ) {
			update_option( 'active_plugins', self::$original_active_plugins );
		} else {
			delete_option( 'active_plugins' );
		}
	}

	/**
	 * Set active plugins for testing.
	 *
	 * @param array $plugins Array of plugin paths.
	 */
	public static function set_active_plugins( array $plugins ) {
		update_option( 'active_plugins', $plugins );
	}

	/**
	 * Add plugin to active plugins.
	 *
	 * @param string $plugin_path Plugin path.
	 */
	public static function activate_plugin( string $plugin_path ) {
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( ! in_array( $plugin_path, $active_plugins, true ) ) {
			$active_plugins[] = $plugin_path;
			update_option( 'active_plugins', $active_plugins );
		}
	}

	/**
	 * Remove plugin from active plugins.
	 *
	 * @param string $plugin_path Plugin path.
	 */
	public static function deactivate_plugin( string $plugin_path ) {
		$active_plugins = (array) get_option( 'active_plugins', array() );
		$key            = array_search( $plugin_path, $active_plugins, true );

		if ( $key !== false ) {
			unset( $active_plugins[ $key ] );
			update_option( 'active_plugins', array_values( $active_plugins ) );
		}
	}

	/**
	 * Clear all active plugins.
	 */
	public static function clear_active_plugins() {
		update_option( 'active_plugins', array() );
	}

	/**
	 * Generate test plugin data.
	 *
	 * @param int $count Number of plugins to generate.
	 * @return array Array of plugin paths.
	 */
	public static function generate_test_plugins( int $count = 10 ): array {
		$plugins = array();

		for ( $i = 1; $i <= $count; $i++ ) {
			$plugins[] = "test-plugin-{$i}/test-plugin-{$i}.php";
		}

		return $plugins;
	}

	/**
	 * Create realistic plugin names for testing.
	 *
	 * @return array Array of realistic plugin paths.
	 */
	public static function get_realistic_plugin_names(): array {
		return array(
			'woocommerce/woocommerce.php',
			'elementor/elementor.php',
			'yoast-seo/wp-seo.php',
			'akismet/akismet.php',
			'jetpack/jetpack.php',
			'contact-form-7/wp-contact-form-7.php',
			'wordpress-importer/wordpress-importer.php',
			'classic-editor/classic-editor.php',
		);
	}
}
