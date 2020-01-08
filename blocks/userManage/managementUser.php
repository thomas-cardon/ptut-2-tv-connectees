<?php

function management_user_render_callback() {
    $manageUser = new Secretary();
    $view = new SecretaryView();
    if(is_page()){
        $manageUser->deleteUsers();
        return $manageUser->displayUsers();
    }
}

function block_management_user() {
    wp_register_script(
        'management_user-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/management-user', array(
        'editor_script' => 'management_user-script',
        'render_callback' => 'management_user_render_callback'
    ));
}
add_action( 'init', 'block_management_user' );