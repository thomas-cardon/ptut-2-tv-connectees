<?php

function user_modify_render_callback() {
    $user = new User();
    if(is_page()){
        $user->modifyUser();
    }
}

function block_user_modify() {
    wp_register_script(
        'user_modify-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/modify-user', array(
        'editor_script' => 'user_modify-script',
        'render_callback' => 'user_modify_render_callback'
    ));
}
add_action( 'init', 'block_user_modify' );