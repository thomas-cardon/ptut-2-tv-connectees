<?php

function secretary_render_callback() {
    $secretary = new Secretary();
    $view = new SecretaryView();
    if(is_page()){
        $secretary->insertSecretary();
        return $view->displayFormSecretary();
    }
}

function block_secretary() {
    wp_register_script(
        'secretary-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-secretary', array(
        'editor_script' => 'secretary-script',
        'render_callback' => 'secretary_render_callback'
    ));
}
add_action( 'init', 'block_secretary' );