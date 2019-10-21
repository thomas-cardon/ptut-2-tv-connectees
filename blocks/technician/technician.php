<?php

function technician_render_callback() {
    $technician = new Technician();
    $view = new ViewTechnician();
    if(is_page()){
        $technician->insertTechnician();
        return $view->displayFormTechnician();
    }
}

function block_technician() {
    wp_register_script(
        'technician-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-technician', array(
        'editor_script' => 'technician-script',
        'render_callback' => 'technician_render_callback'
    ));
}
add_action( 'init', 'block_technician' );