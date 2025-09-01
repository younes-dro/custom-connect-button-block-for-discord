<?php
/**
 * Server-side rendering for the Custom Connect Button Block for Discord.
 *
 * @package @package CustomConnectButtonBlockForDiscord
 */
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Dro\CustomConnectButtonBlock\includes\Dro_CCBB_Render as Discord_Render;
use Dro\CustomConnectButtonBlock\includes\Dro_CCBB_Resolver as Resolver;
use Dro\CustomConnectButtonBlock\includes\helpers\Dro_CCBB_Helper as Helper;


$user_id = get_current_user_id();
$service = Resolver::get_active_service();


if ( $service && $user_id ) {
	$service->load_discord_user_data( $user_id );
}

$render_block = '';
try {
	$render_block = Discord_Render::get_instance( $service )
					->render( $attributes, $content, $block );
} catch ( Throwable $e ) {
	Helper::safe_log( $e );
}

echo wp_kses( $render_block, Helper::get_allowed_html() );
