<?php
/**
 * Integration tests for Dro_AIO_Discord_Service abstract class.
 *
 * @package CustomConnectButtonBlock\Tests\Integration
 */

namespace CustomConnectButtonBlock\Tests\Integration;

use Dro\CustomConnectButtonBlock\Abstracts\Dro_AIO_Discord_Service;
use Dro\CustomConnectButtonBlock\Tests\Helpers\Discord_Service_Mock_Helper;
use WP_UnitTestCase;

/**
 * Multiple concrete implementations for testing different scenarios.
 */
class WooCommerce_Discord_Service extends Dro_AIO_Discord_Service {
	protected function get_plugin_name(): string {
		return 'woocommerce/woocommerce.php';
	}

	public function is_active(): bool {
		return $this->check_active_plugin();
	}
}

class Elementor_Discord_Service extends Dro_AIO_Discord_Service {
	protected function get_plugin_name(): string {
		return 'elementor/elementor.php';
	}

	public function is_active(): bool {
		return $this->check_active_plugin();
	}
}

class Custom_Discord_Service extends Dro_AIO_Discord_Service {
	private $plugin_name;

	public function __construct( string $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	protected function get_plugin_name(): string {
		return $this->plugin_name;
	}

	public function is_active(): bool {
		return $this->check_active_plugin();
	}
}

/**
 * Integration test case for Discord Service.
 */
class Test_Discord_Service_Integration extends WP_UnitTestCase {

	/**
	 * Setup test environment.
	 */
	public function setUp(): void {
		parent::setUp();
		Discord_Service_Mock_Helper::setup();
	}

	/**
	 * Cleanup test environment.
	 */
	public function tearDown(): void {
		Discord_Service_Mock_Helper::cleanup();
		parent::tearDown();
	}

	/**
	 * Test multiple service instances with different plugins.
	 */
	public function test_multiple_service_instances() {
		$wc_service        = new WooCommerce_Discord_Service();
		$elementor_service = new Elementor_Discord_Service();

		// Initially no plugins are active
		Discord_Service_Mock_Helper::clear_active_plugins();
		$this->assertFalse( $wc_service->is_active() );
		$this->assertFalse( $elementor_service->is_active() );

		// Activate WooCommerce only
		Discord_Service_Mock_Helper::activate_plugin( 'woocommerce/woocommerce.php' );
		$this->assertTrue( $wc_service->is_active() );
		$this->assertFalse( $elementor_service->is_active() );

		// Activate Elementor too
		Discord_Service_Mock_Helper::activate_plugin( 'elementor/elementor.php' );
		$this->assertTrue( $wc_service->is_active() );
		$this->assertTrue( $elementor_service->is_active() );

		// Deactivate WooCommerce
		Discord_Service_Mock_Helper::deactivate_plugin( 'woocommerce/woocommerce.php' );
		$this->assertFalse( $wc_service->is_active() );
		$this->assertTrue( $elementor_service->is_active() );
	}

	/**
	 * Test with realistic plugin ecosystem.
	 */
	public function test_realistic_plugin_ecosystem() {
		$realistic_plugins = Discord_Service_Mock_Helper::get_realistic_plugin_names();
		Discord_Service_Mock_Helper::set_active_plugins( $realistic_plugins );

		// Test each realistic plugin
		foreach ( $realistic_plugins as $plugin_path ) {
			$service = new Custom_Discord_Service( $plugin_path );
			$this->assertTrue( $service->is_active(), "Plugin {$plugin_path} should be active" );
		}

		// Test a plugin that's not in the list
		$inactive_service = new Custom_Discord_Service( 'non-existent-plugin/plugin.php' );
		$this->assertFalse( $inactive_service->is_active() );
	}

	/**
	 * Test service behavior with WordPress plugin activation/deactivation hooks.
	 */
	public function test_wordpress_plugin_lifecycle() {
		$test_plugin = 'test-lifecycle/test-lifecycle.php';
		$service     = new Custom_Discord_Service( $test_plugin );

		// Initially inactive
		Discord_Service_Mock_Helper::clear_active_plugins();
		$this->assertFalse( $service->is_active() );

		// Simulate plugin activation
		Discord_Service_Mock_Helper::activate_plugin( $test_plugin );
		$this->assertTrue( $service->is_active() );

		// Simulate plugin deactivation
		Discord_Service_Mock_Helper::deactivate_plugin( $test_plugin );
		$this->assertFalse( $service->is_active() );

		// Simulate reactivation
		Discord_Service_Mock_Helper::activate_plugin( $test_plugin );
		$this->assertTrue( $service->is_active() );
	}

	/**
	 * Test performance with large plugin sets.
	 */
	public function test_performance_with_large_plugin_set() {
		$large_plugin_set = Discord_Service_Mock_Helper::generate_test_plugins( 100 );
		$target_plugin    = 'target-plugin/target-plugin.php';

		// Add target plugin to the middle of the set
		array_splice( $large_plugin_set, 50, 0, $target_plugin );

		Discord_Service_Mock_Helper::set_active_plugins( $large_plugin_set );

		$service = new Custom_Discord_Service( $target_plugin );

		// Measure performance
		$start_time = microtime( true );
		$result     = $service->is_active();
		$end_time   = microtime( true );

		$this->assertTrue( $result );
		$this->assertLessThan( 0.1, $end_time - $start_time, 'Plugin check should be fast even with many plugins' );
	}

	/**
	 * Test edge cases with plugin naming.
	 */
	public function test_plugin_naming_edge_cases() {
		$edge_case_plugins = array(
			'plugin-with-dashes/main-file.php',
			'plugin_with_underscores/main_file.php',
			'UPPERCASE-PLUGIN/UPPERCASE-FILE.PHP',
			'123-numeric-plugin/123-numeric-file.php',
			'special@chars#plugin/special@chars#file.php',
			'very-long-plugin-name-that-exceeds-normal-length/very-long-main-file-name-that-also-exceeds-normal-length.php',
		);

		Discord_Service_Mock_Helper::set_active_plugins( $edge_case_plugins );

		foreach ( $edge_case_plugins as $plugin_path ) {
			$service = new Custom_Discord_Service( $plugin_path );
			$this->assertTrue( $service->is_active(), "Plugin with edge case name should be detected: {$plugin_path}" );
		}
	}

	/**
	 * Test concurrent access simulation.
	 */
	public function test_concurrent_access_simulation() {
		$plugin_path = 'concurrent-test/concurrent-test.php';
		Discord_Service_Mock_Helper::activate_plugin( $plugin_path );

		// Create multiple service instances
		$services = array();
		for ( $i = 0; $i < 10; $i++ ) {
			$services[] = new Custom_Discord_Service( $plugin_path );
		}

		// All should return the same result
		foreach ( $services as $index => $service ) {
			$this->assertTrue( $service->is_active(), "Service instance {$index} should detect active plugin" );
		}

		// Deactivate plugin
		Discord_Service_Mock_Helper::deactivate_plugin( $plugin_path );

		// All should now return false
		foreach ( $services as $index => $service ) {
			$this->assertFalse( $service->is_active(), "Service instance {$index} should detect inactive plugin" );
		}
	}

	/**
	 * Test inheritance chain validation.
	 */
	public function test_inheritance_chain() {
		$service = new Custom_Discord_Service( 'test/test.php' );

		// Verify inheritance
		$this->assertInstanceOf( Dro_AIO_Discord_Service::class, $service );
		$this->assertInstanceOf( Custom_Discord_Service::class, $service );

		// Verify method availability
		$this->assertTrue( method_exists( $service, 'is_active' ) );
		$this->assertTrue( method_exists( $service, 'get_plugin_name' ) );

		// Verify abstract method is properly implemented
		$reflection = new \ReflectionClass( $service );
		$this->assertFalse( $reflection->isAbstract() );
	}

	/**
	 * Test error handling with corrupted data.
	 */
	public function test_error_handling_with_corrupted_data() {
		$service = new Custom_Discord_Service( 'test/test.php' );

		// Test with various corrupted data types
		$corrupted_data_sets = array(
			'string_instead_of_array',
			123,
			null,
			false,
			new \stdClass(),
			array( 'valid-plugin.php', null, 'another-valid-plugin.php' ),
			array( 'valid-plugin.php', 123, 'another-valid-plugin.php' ),
		);

		foreach ( $corrupted_data_sets as $corrupted_data ) {
			update_option( 'active_plugins', $corrupted_data );

			// Should not throw errors and should return false
			$result = $service->is_active();
			$this->assertFalse( $result, 'Should handle corrupted data gracefully' );
		}
	}
}
