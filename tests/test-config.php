<?php
/**
 * Test configuration for Discord Service tests.
 *
 * @package CustomConnectButtonBlock\Tests
 */

// Define test constants
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/tmp/wordpress/' );
}

// Load test helpers
require_once __DIR__ . '/helpers/class-discord-service-mock-helper.php';

// Load the abstract class being tested
require_once __DIR__ . '/../includes/abstracts/class-dro-ccbb-service.php';

/**
 * Test configuration class.
 */
class Discord_Service_Test_Config {

	/**
	 * Initialize test configuration.
	 */
	public static function init() {
		// Set up WordPress environment if not already set
		if ( ! function_exists( 'get_option' ) ) {
			self::setup_wordpress_functions();
		}

		// Register test autoloader
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Setup minimal WordPress functions for testing.
	 */
	private static function setup_wordpress_functions() {
		// Mock WordPress functions if they don't exist
		if ( ! function_exists( 'get_option' ) ) {
			function get_option( $option, $default = false ) {
				static $options = array();
				return isset( $options[ $option ] ) ? $options[ $option ] : $default;
			}
		}

		if ( ! function_exists( 'update_option' ) ) {
			function update_option( $option, $value ) {
				static $options     = array();
				$options[ $option ] = $value;
				return true;
			}
		}

		if ( ! function_exists( 'delete_option' ) ) {
			function delete_option( $option ) {
				static $options = array();
				unset( $options[ $option ] );
				return true;
			}
		}

		if ( ! function_exists( 'wp_remote_post' ) ) {
			function wp_remote_post( $url, $args = array() ) {
				// Mock HTTP response for testing
				return array(
					'response' => array( 'code' => 200 ),
					'body'     => json_encode( array( 'success' => true ) ),
				);
			}
		}

		if ( ! function_exists( 'wp_remote_retrieve_body' ) ) {
			function wp_remote_retrieve_body( $response ) {
				return isset( $response['body'] ) ? $response['body'] : '';
			}
		}

		if ( ! function_exists( 'wp_remote_retrieve_response_code' ) ) {
			function wp_remote_retrieve_response_code( $response ) {
				return isset( $response['response']['code'] ) ? $response['response']['code'] : 200;
			}
		}

		if ( ! function_exists( 'is_wp_error' ) ) {
			function is_wp_error( $thing ) {
				return ( $thing instanceof WP_Error );
			}
		}

		if ( ! function_exists( 'esc_attr' ) ) {
			function esc_attr( $text ) {
				return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
			}
		}

		if ( ! function_exists( 'esc_html' ) ) {
			function esc_html( $text ) {
				return htmlspecialchars( $text, ENT_NOQUOTES, 'UTF-8' );
			}
		}

		if ( ! function_exists( 'sanitize_text_field' ) ) {
			function sanitize_text_field( $str ) {
				return trim( strip_tags( $str ) );
			}
		}
	}

	/**
	 * Autoloader for test classes.
	 *
	 * @param string $class_name Class name to load.
	 */
	public static function autoload( $class_name ) {
		// Handle namespaced classes
		if ( strpos( $class_name, 'Dro\\AIODiscordBlock\\' ) === 0 ) {
			$class_path = str_replace( 'Dro\\AIODiscordBlock\\', '', $class_name );
			$class_path = str_replace( '\\', DIRECTORY_SEPARATOR, $class_path );

			// Convert to file path
			$file_path = __DIR__ . '/../includes/' . strtolower( str_replace( '_', '-', $class_path ) ) . '.php';

			if ( file_exists( $file_path ) ) {
				require_once $file_path;
			}
		}
	}

	/**
	 * Get test data directory.
	 *
	 * @return string Test data directory path.
	 */
	public static function get_test_data_dir() {
		return __DIR__ . '/data/';
	}

	/**
	 * Get test fixtures directory.
	 *
	 * @return string Test fixtures directory path.
	 */
	public static function get_test_fixtures_dir() {
		return __DIR__ . '/fixtures/';
	}

	/**
	 * Get mock Discord webhook URL for testing.
	 *
	 * @return string Mock webhook URL.
	 */
	public static function get_mock_webhook_url() {
		return 'https://discord.com/api/webhooks/test/webhook';
	}

	/**
	 * Get sample Discord message data.
	 *
	 * @return array Sample message data.
	 */
	public static function get_sample_message_data() {
		return array(
			'content'    => 'Test message from WordPress',
			'username'   => 'WordPress Bot',
			'avatar_url' => 'https://example.com/avatar.png',
			'embeds'     => array(
				array(
					'title'       => 'Test Embed',
					'description' => 'This is a test embed',
					'color'       => 5814783,
					'fields'      => array(
						array(
							'name'   => 'Field 1',
							'value'  => 'Value 1',
							'inline' => true,
						),
					),
				),
			),
		);
	}

	/**
	 * Create test directories if they don't exist.
	 */
	public static function create_test_directories() {
		$directories = array(
			self::get_test_data_dir(),
			self::get_test_fixtures_dir(),
		);

		foreach ( $directories as $dir ) {
			if ( ! is_dir( $dir ) ) {
				wp_mkdir_p( $dir );
			}
		}
	}

	/**
	 * Clean up test environment.
	 */
	public static function cleanup() {
		// Clean up any global state
		delete_option( 'active_plugins' );
		delete_option( 'dro_aio_discord_settings' );

		// Reset any static variables
		if ( class_exists( 'Dro\\AIODiscordBlock\\Tests\\Helpers\\Discord_Service_Mock_Helper' ) ) {
			\CustomConnectButtonBlock\Tests\Helpers\Discord_Service_Mock_Helper::reset();
		}

		// Clear any cached data
		if ( function_exists( 'wp_cache_flush' ) ) {
			wp_cache_flush();
		}

		// Reset global variables
		global $wp_filter, $wp_actions, $wp_current_filter;
		$wp_filter         = array();
		$wp_actions        = array();
		$wp_current_filter = array();
	}

	/**
	 * Set up test database tables if needed.
	 */
	public static function setup_test_tables() {
		// Add any custom table setup here if your plugin uses custom tables
		// This is just an example structure

		global $wpdb;

		$table_name = $wpdb->prefix . 'discord_logs';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			webhook_url varchar(255) NOT NULL,
			message_data text NOT NULL,
			response_code int(3) DEFAULT 0,
			response_body text,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		// Note: In actual implementation, you'd use dbDelta() function
		// require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		// dbDelta( $sql );
	}

	/**
	 * Tear down test database tables.
	 */
	public static function teardown_test_tables() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'discord_logs';
		$wpdb->query( "DROP TABLE IF EXISTS $table_name" );
	}

	/**
	 * Get WordPress functions that need to be mocked.
	 *
	 * @return array List of WordPress functions to mock.
	 */
	public static function get_wordpress_functions_to_mock() {
		return array(
			'get_option',
			'update_option',
			'delete_option',
			'wp_remote_post',
			'wp_remote_get',
			'wp_remote_retrieve_body',
			'wp_remote_retrieve_response_code',
			'is_wp_error',
			'esc_attr',
			'esc_html',
			'sanitize_text_field',
			'wp_mkdir_p',
			'wp_cache_flush',
		);
	}
}

/**
 * Simple WP_Error class mock for testing.
 */
if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		public $errors     = array();
		public $error_data = array();

		public function __construct( $code = '', $message = '', $data = '' ) {
			if ( empty( $code ) ) {
				return;
			}

			$this->errors[ $code ][] = $message;

			if ( ! empty( $data ) ) {
				$this->error_data[ $code ] = $data;
			}
		}

		public function get_error_code() {
			$codes = array_keys( $this->errors );
			return empty( $codes ) ? '' : $codes[0];
		}

		public function get_error_message( $code = '' ) {
			if ( empty( $code ) ) {
				$code = $this->get_error_code();
			}

			return isset( $this->errors[ $code ] ) ? $this->errors[ $code ][0] : '';
		}
	}
}

// Initialize the test configuration
Discord_Service_Test_Config::init();
