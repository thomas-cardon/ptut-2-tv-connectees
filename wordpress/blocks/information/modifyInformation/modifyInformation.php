<?php

use Controllers\InformationController;

function information_modify_render_callback() {
    if(is_page()){
	    $information = new InformationController();
        return $information->modifyInformation();
    }
}

function block_information_modify() {
    wp_register_script(
        'information_modify-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-information', array(
        'editor_script' => 'information_modify-script',
        'render_callback' => 'information_modify_render_callback'
    ));
}
add_action( 'init', 'block_information_modify' );