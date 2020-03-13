<?php

use Controllers\CodeAdeController;

/**
 * Function of the block
 *
 * @return string
 */
function code_modify_render_callback()
{
    if(is_page()) {
	    $code = new CodeAdeController();
    	return $code->modify();
    }
}

/**
 * Build a block
 */
function block_code_modify()
{
    wp_register_script(
        'code_modify-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-code', array(
        'editor_script' => 'code_modify-script',
        'render_callback' => 'code_modify_render_callback'
    ));
}
add_action( 'init', 'block_code_modify' );