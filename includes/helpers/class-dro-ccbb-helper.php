<?php
/**
 * General-purpose helper utilities for the plugin.
 *
 * @package @package CustomConnectButtonBlockForDiscord
 */

declare(strict_types=1);

namespace Dro\CustomConnectButtonBlock\includes\helpers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dro_CCBB_Helper {

	/**
	 * Get allowed HTML tags for wp_kses().
	 *
	 * @return array
	 */
	public static function get_allowed_html(): array {
		return array(
			'div'  => array(
				'class' => true,
				'style' => true,
			),
			'a'    => array(
				'href'         => true,
				'class'        => true,
				'style'        => true,
				'id'           => true,
				'data-user-id' => true,
			),
			'i'    => array(
				'class' => true,
				'style' => true,
			),
			'span' => array(
				'class' => true,
				'style' => true,
			),
			'img'  => array(
				'src'   => true,
				'class' => true,
				'style' => true,
				'alt'   => true,
			),
			'svg'  => array(
				'xmlns'       => true,
				'width'       => true,
				'height'      => true,
				'viewBox'     => true,
				'fill'        => true,
				'aria-hidden' => true,
				'role'        => true,
				'focusable'   => true,
				'class'       => true,
				'style'       => true,
			),
			'path' => array(
				'd'     => true,
				'fill'  => true,
				'class' => true,
				'style' => true,
			),
		);
	}
	/**
	 * Log messages safely for debugging.
	 *
	 * Writes to the PHP error log only when WP_DEBUG is true.
	 * Sanitizes strings to avoid unsafe output in logs.
	 *
	 * @since 1.0.0
	 *
	 * @param string|\Throwable $message The message or exception to log.
	 * @param string            $level   Optional severity level ('notice', 'warning', 'error').
	 *
	 * @return void
	 */
	public static function safe_log( $message, string $level = 'error' ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			if ( $message instanceof \Throwable ) {
				$message = $message->getMessage();
			}
			$prefix = '[CustomConnectButtonBlockForDiscord][' . strtoupper( $level ) . '] ';

			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			error_log( $prefix . esc_html( (string) $message ) );
		}
	}
	/**
	 * Returns the Discord SVG icon as a string.
	 *
	 * @since 1.0.0
	 * @return string The SVG markup for the Discord icon.
	 */
	public static function get_discord_icon() {
		return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.211.375-.445.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z" fill="currentColor" />
	</svg>';
	}
}
