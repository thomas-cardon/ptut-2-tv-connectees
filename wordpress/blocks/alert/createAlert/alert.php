<?php

use Controllers\AlertController;

/**
 * Function of the block
 *
 * @return string
 */
function alert_render_callback()
{
	if(is_page()) {
		$alert = new AlertController();
		return $alert->insert();
	}
}

/**
 * Build a block
 */
function block_alert()
{
	wp_register_script(
		'alert-script',
		plugins_url( 'block.js', __FILE__ ),
		array( 'wp-blocks', 'wp-element', 'wp-data' )
	);

	register_block_type('tvconnecteeamu/add-alert', array(
		'editor_script' => 'alert-script',
		'render_callback' => 'alert_render_callback'
	));
}
add_action( 'init', 'block_alert' );