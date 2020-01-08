<?php

function studyDirector_render_callback() {
    $director = new StudyDirector();
    $view = new StudyDirectorView();
    if(is_page()){
        $director->insertDirector();
        return $view->displayCreateDirector();
    }
}

function block_studyDirector() {
    wp_register_script(
        'director-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-director', array(
        'editor_script' => 'director-script',
        'render_callback' => 'studyDirector_render_callback'
    ));
}
add_action( 'init', 'block_studyDirector' );
