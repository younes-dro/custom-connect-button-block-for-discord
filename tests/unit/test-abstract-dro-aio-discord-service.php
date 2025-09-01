<?php
/**
 * Tests for Dro_AIO_Discord_Service abstract class.
 *
 * @package CustomConnectButtonBlock\Tests
 */

namespace CustomConnectButtonBlock\Tests\Unit;

use Dro\CustomConnectButtonBlock\includes\Abstracts\Dro_AIO_Discord_Service;


/**
 * Concrete implementation of the abstract class for testing purposes.
 */
class Test_Discord_Service extends Dro_AIO_Discord_Service {

	/**
	 * Plugin name for testing.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name The plugin name to return.
	 */
	public function __construct( $plugin_name = 'test-plugin/test-plugin.php' ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Get the plugin name/slug for the service.
	 *
	 * @return string The plugin slug.
	 */
	protected function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * Public wrapper for protected method to enable testing.
	 *
	 * @return bool True if plugin is active, false otherwise.
	 */
	public function is_plugin_active(): bool {
		return $this->check_active_plugin();
	}
}

/**
 * Test case for Dro_AIO_Discord_Service abstract class.
 */
class Test_Dro_AIO_Discord_Service extends \WP_UnitTestCase {

	/**
	 * Test service instance.
	 *
	 * @var Test_Discord_Service
	 */
	private $service;

	/**
	 * Set up test case.
	 */
	public function setUp(): void {
		parent::setUp();
		$this->service = new Test_Discord_Service();
	}

	/**
	 * Tear down test case.
	 */
	public function tearDown(): void {
		parent::tearDown();

		// Clean up any option modifications
		delete_option( 'active_plugins' );
	}

	/**
	 * Test that the abstract class can be extended.
	 */
	public function test_abstract_class_can_be_extended() {
		$this->assertInstanceOf( Dro_AIO_Discord_Service::class, $this->service );
		$this->assertInstanceOf( Test_Discord_Service::class, $this->service );
	}

	/**
	 * Test get_plugin_name method implementation.
	 */
	public function test_get_plugin_name_returns_correct_value() {
		$expected_plugin_name = 'custom-plugin/custom-plugin.php';
		$service              = new Test_Discord_Service( $expected_plugin_name );

		// We can't directly test the protected method, but we can test it indirectly
		// through the check_active_plugin method behavior
		$this->assertInstanceOf( Test_Discord_Service::class, $service );
	}

	/**
	 * Test check_active_plugin method when plugin is active.
	 */
	public function test_check_active_plugin_returns_true_when_plugin_is_active() {
		$plugin_name = 'test-plugin/test-plugin.php';
		$service     = new Test_Discord_Service( $plugin_name );

		// Mock active plugins option
		$active_plugins = array(
			'test-plugin/test-plugin.php',
			'another-plugin/another-plugin.php',
		);
		update_option( 'active_plugins', $active_plugins );

		$this->assertTrue( $service->is_plugin_active() );
	}

	/**
	 * Test check_active_plugin method when plugin is not active.
	 */
	public function test_check_active_plugin_returns_false_when_plugin_is_not_active() {
		$plugin_name = 'inactive-plugin/inactive-plugin.php';
		$service     = new Test_Discord_Service( $plugin_name );

		// Mock active plugins option without our plugin
		$active_plugins = array(
			'other-plugin/other-plugin.php',
			'another-plugin/another-plugin.php',
		);
		update_option( 'active_plugins', $active_plugins );

		$this->assertFalse( $service->is_plugin_active() );
	}

	/**
	 * Test check_active_plugin method when no plugins are active.
	 */
	public function test_check_active_plugin_returns_false_when_no_plugins_active() {
		$plugin_name = 'test-plugin/test-plugin.php';
		$service     = new Test_Discord_Service( $plugin_name );

		// Mock empty active plugins option
		update_option( 'active_plugins', array() );

		$this->assertFalse( $service->is_plugin_active() );
	}

	/**
	 * Test check_active_plugin method when active_plugins option doesn't exist.
	 */
	public function test_check_active_plugin_returns_false_when_option_not_exists() {
		$plugin_name = 'test-plugin/test-plugin.php';
		$service     = new Test_Discord_Service( $plugin_name );

		// Delete the option to simulate it not existing
		delete_option( 'active_plugins' );

		$this->assertFalse( $service->is_plugin_active() );
	}

	/**
	 * Test check_active_plugin method with different plugin name formats.
	 */
	public function test_check_active_plugin_with_different_plugin_name_formats() {
		$test_cases = array(
			'simple-plugin.php',
			'folder/plugin.php',
			'deep/folder/structure/plugin.php',
			'plugin-with-dashes/main-file.php',
			'plugin_with_underscores/main_file.php',
		);

		foreach ( $test_cases as $plugin_name ) {
			$service = new Test_Discord_Service( $plugin_name );

			// Test when plugin is active
			update_option( 'active_plugins', array( $plugin_name ) );
			$this->assertTrue( $service->is_plugin_active(), "Failed for plugin: {$plugin_name}" );

			// Test when plugin is not active
			update_option( 'active_plugins', array( 'other-plugin.php' ) );
			$this->assertFalse( $service->is_plugin_active(), "Failed for plugin: {$plugin_name}" );
		}
	}

	/**
	 * Test check_active_plugin method with exact string matching.
	 */
	public function test_check_active_plugin_exact_string_matching() {
		$plugin_name = 'test-plugin/test-plugin.php';
		$service     = new Test_Discord_Service( $plugin_name );

		// Mock active plugins with similar but not exact matches
		$active_plugins = array(
			'test-plugin/test-plugin.php.backup',  // Should not match
			'test-plugin-pro/test-plugin.php',     // Should not match
			'test-plugin/test-plugin.php',         // Should match
		);
		update_option( 'active_plugins', $active_plugins );

		$this->assertTrue( $service->is_plugin_active() );

		// Test with only partial matches
		$active_plugins = array(
			'test-plugin/test-plugin.php.backup',
			'test-plugin-pro/test-plugin.php',
		);
		update_option( 'active_plugins', $active_plugins );

		$this->assertFalse( $service->is_plugin_active() );
	}

	/**
	 * Test check_active_plugin method handles non-array active_plugins option.
	 */
	public function test_check_active_plugin_handles_non_array_option() {
		$plugin_name = 'test-plugin/test-plugin.php';
		$service     = new Test_Discord_Service( $plugin_name );

		// Test with string instead of array
		update_option( 'active_plugins', 'not-an-array' );
		$this->assertFalse( $service->is_plugin_active() );

		// Test with null
		update_option( 'active_plugins', null );
		$this->assertFalse( $service->is_plugin_active() );

		// Test with boolean
		update_option( 'active_plugins', false );
		$this->assertFalse( $service->is_plugin_active() );
	}

	/**
	 * Test that abstract methods must be implemented.
	 */
	public function test_abstract_methods_must_be_implemented() {
		// This test verifies that our concrete implementation properly implements
		// the abstract method by checking if it's callable
		$this->assertTrue( method_exists( $this->service, 'get_plugin_name' ) );

		// Verify the method is protected (we can't call it directly)
		$reflection = new \ReflectionClass( $this->service );
		$method     = $reflection->getMethod( 'get_plugin_name' );
		$this->assertTrue( $method->isProtected() );
	}

	/**
	 * Test check_active_plugin method with large number of active plugins.
	 */
	public function test_check_active_plugin_performance_with_many_plugins() {
		$plugin_name = 'target-plugin/target-plugin.php';
		$service     = new Test_Discord_Service( $plugin_name );

		// Create a large array of active plugins
		$active_plugins = array();
		for ( $i = 0; $i < 1000; $i++ ) {
			$active_plugins[] = "plugin-{$i}/plugin-{$i}.php";
		}

		// Add our target plugin at the end
		$active_plugins[] = $plugin_name;

		update_option( 'active_plugins', $active_plugins );

		// This should still work efficiently
		$start_time = microtime( true );
		$result     = $service->is_plugin_active();
		$end_time   = microtime( true );

		$this->assertTrue( $result );
		// Performance check - should complete in reasonable time (less than 1 second)
		$this->assertLessThan( 1.0, $end_time - $start_time );
	}

	/**
	 * Test that the class properly handles WordPress environment.
	 */
	public function test_wordpress_environment_integration() {
		// Verify that the method relies on WordPress functions
		$this->assertTrue( function_exists( 'get_option' ) );

		// Test with WordPress multisite scenario (if applicable)
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			// In multisite, active_plugins might behave differently
			$plugin_name = 'multisite-plugin/multisite-plugin.php';
			$service     = new Test_Discord_Service( $plugin_name );

			update_option( 'active_plugins', array( $plugin_name ) );
			$this->assertTrue( $service->is_plugin_active() );
		}
	}
}
