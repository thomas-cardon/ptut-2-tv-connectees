<?php

use Controllers\AlertController;

/**
 * Function of the block
 *
 * @return string
 */
function alert_management_render_callback()
{
    if(is_page()) {
	    $alert = new AlertController();
        $alert->deleteAlert();
        return $alert->alertsManagement();
    }
}

/**
 * Build a block
 */
function block_alert_management()
{
    wp_register_script(
        'alert_manage-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/manage-alert', array(
        'editor_script' => 'alert_manage-script',
        'render_callback' => 'alert_management_render_callback'
    ));
}
add_action( 'init', 'block_alert_management' );