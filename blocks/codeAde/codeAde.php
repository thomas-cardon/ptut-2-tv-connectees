<?php

function code_ade_render_callback() {
    $codeAde = new CodeAde();
    $model = new CodeAdeModel();
    $view = new CodeAdeView();
    if(is_page()){
        return $codeAde->insertCode();
    }
}

function block_code_ade() {
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