<?php

use Controllers\CodeAdeController;

function code_management_render_callback() {
    $code = new CodeAdeController();
    if(is_page()){
        $code->deleteCodes();
        return $code->displayAllCodes();
    }
}

function block_code_management() {
    wp_register_script(
        'code_manage-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/manage-code', array(
        'editor_script' => 'code_manage-script',
        'render_callback' => 'code_management_render_callback'
    ));
}
add_action( 'init', 'block_code_management' );