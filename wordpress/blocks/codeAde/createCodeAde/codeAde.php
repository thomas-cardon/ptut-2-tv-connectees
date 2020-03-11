<?php

use Controllers\CodeAdeController;

/**
 * Function of the block
 *
 * @return string
 */
function code_ade_render_callback()
{
    if(is_page()) {
	    $codeAde = new CodeAdeController();
        return $codeAde->insert();
    }
}

/**
 * Build a block
 */
function block_code_ade()
{
    wp_register_script(
        'code_ade-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-code', array(
        'editor_script' => 'code_ade-script',
        'render_callback' => 'code_ade_render_callback'
    ));
}
add_action( 'init', 'block_code_ade' );