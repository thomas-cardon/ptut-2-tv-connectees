<?php

use Controllers\SecretaryController;

/**
 * Function of the block
 *
 * @return string
 */
function creation_user_render_callback()
{
    if(is_page()) {
	    $manageUser = new SecretaryController();
        return $manageUser->createUsers();
    }
}

/**
 * Build a block
 */
function block_creation_user()
{
    wp_register_script(
        'creation_user-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/creation-user', array(
        'editor_script' => 'creation_user-script',
        'render_callback' => 'creation_user_render_callback'
    ));
}
add_action( 'init', 'block_creation_user' );