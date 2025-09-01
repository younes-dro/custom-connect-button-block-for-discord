<?php
/**
 * Plugin Name:       Custom connect button block for Discord
 * Plugin URI:        https://github.com/younes-dro/custom-connect-button-block-for-discord
 * Description:       A Gutenberg block that provides a customizable "Connect to Discord" button, designed to work with supported Discord integration add-ons: PMPro Discord Add-on, ExpressTech MemberPress Discord Add-on, Connect Ultimate Member to Discord, and Connect Tutor LMS to Discord.
 * Version:           1.0.1
 * Requires at least: 6.8
 * Requires PHP:      7.4
 * Author:            Younes DRO
 * Author URI:        https://github.com/younes-dro
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       custom-connect-button-block-for-discord
 *
 * @package @package CustomConnectButtonBlockForDiscord
 */

declare( strict_types=1 );

namespace Dro\CustomConnectButtonBlock;

use Dro\CustomConnectButtonBlock\includes\Dro_CCBB_Main;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'DRO_CCBB_VERSION', get_file_data( __FILE__, array( 'Version' ), 'plugin' )[0] ?? '1.0.1' );
define( 'DRO_CCBB_FILE', __FILE__ );
define( 'DRO_CCBB_DIR', plugin_dir_path( __FILE__ ) );
define( 'DRO_CCBB_URL', plugin_dir_url( __FILE__ ) );
define( 'DRO_CCBB_SERVICE_PREFIX', __NAMESPACE__ . '\\includes\\Services\\Dro_CCBB_' );

/**
 * Activation hook for the Custom connect button block for Discord plugin.
 * TO DO: Add maybe some activation logic here in the future.
 *
 * @since 1.0.0
 *
 * @return void
 */
function dro_ccbb_activation() {
	// empty function for now, but can be used in the future for activation logic.
}

register_activation_hook( DRO_CCBB_FILE, __NAMESPACE__ . '\\dro_ccbb_activation' );

/**
 * Registers a custom autoloader.
 *
 * @return void
 */
function dro_ccbb_autoload() {
	spl_autoload_register(
		function ( $current_class ) {
			if ( strncmp( __NAMESPACE__ . '\\', $current_class, strlen( __NAMESPACE__ ) + 1 ) !== 0 ) {
				return;
			}

			$class_portions    = explode( '\\', $current_class );
			$class_portions    = array_map( 'strtolower', $class_portions );
			$class_file_name   = str_replace( '_', '-', strtolower( array_pop( $class_portions ) ) );
			$class_path        = __DIR__ . '/' . implode( DIRECTORY_SEPARATOR, array_slice( $class_portions, 2 ) );
			$class_file_prefix = ( stripos( $current_class, 'abstracts' ) !== false ? 'abstract-' : 'class-' );
			$class_full_path   = $class_path . DIRECTORY_SEPARATOR . $class_file_prefix . $class_file_name . '.php';

			if ( file_exists( $class_full_path ) ) {
				require_once $class_full_path;
			}
		}
	);
}

/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function dro_ccbb_block_init() {

	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}

	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', __NAMESPACE__ . '\\dro_ccbb_block_init' );

/**
 * Initialize the Custom connect button block for Discord plugin.
 * This function is called to set up the plugin's functionality.
 *
 * @since 1.0.0
 *
 * @return void
 */
function dro_ccbb_init() {
	dro_ccbb_autoload();
	Dro_CCBB_Main::get_instance();
}
dro_ccbb_init();
