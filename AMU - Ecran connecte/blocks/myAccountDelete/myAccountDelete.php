<?php

function delete_account_render_callback() {
    $myAccount = new MyAccount();
    $view = new ViewMyAccount();
    if(is_page()){
        $myAccount->deleteAccount();
        return $view->displayVerifyPassword().$view->displayDeleteAccount().$view->displayEnterCode();
    }
}

function block_delete_account() {
    wp_register_script(
        'delete_account-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/delete-account', array(
        'editor_script' => 'delete_account-script',
        'render_callback' => 'delete_account_render_callback'
    ));
}
add_action( 'init', 'block_delete_account' );