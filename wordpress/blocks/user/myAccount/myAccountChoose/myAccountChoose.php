<?php

use Controllers\UserController;
use Models\User;
use Views\UserView;

function choose_account_render_callback() {
    $myAccount = new UserController();
    $model = new User();
    $view = new UserView();
    $current_user = wp_get_current_user();
    if(is_page()){
        return $myAccount->chooseModif();
    }
}

function block_choose_account() {
    wp_register_script(
        'choose_account-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/choose-account', array(
        'editor_script' => 'choose_account-script',
        'render_callback' => 'choose_account_render_callback'
    ));
}
add_action( 'init', 'block_choose_account' );