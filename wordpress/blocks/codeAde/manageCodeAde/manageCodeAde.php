<?php

use Controllers\CodeAdeController;

/**
 * Function of the block
 *
 * @return string
 */
function code_management_render_callback()
{
    if(is_page()) {
	    $code = new CodeAdeController();
        $code->deleteCodes();
        return $code->displayAllCodes();
    }
}

/**
 * Build a block
 */
function block_code_management()
{
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