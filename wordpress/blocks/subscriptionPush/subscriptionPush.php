<?php

use Views\UserView;

/**
 * Function of the block
 *
 * @return string
 */
function subscription_render_callback()
{
    if(is_page()) {
	    $view = new UserView();
        return $view->displayButtonSubscription();
    }
}

/**
 * Build a block
 */
function block_subscription()
{
    wp_register_script(
        'subscription-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/subscription', array(
        'editor_script' => 'subscription-script',
        'render_callback' => 'subscription_render_callback'
    ));
}
add_action( 'init', 'block_subscription' );