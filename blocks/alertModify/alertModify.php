<?php

function alert_modify_render_callback() {
    $alert = new Alert();
    if(is_page()){
	    return $alert->modifyAlert();
    }
}

function block_alert_modify() {
    wp_register_script(
        'alert_modify-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-alert', array(
        'editor_script' => 'alert_modify-script',
        'render_callback' => 'alert_modify_render_callback'
    ));
}
add_action( 'init', 'block_alert_modify' );