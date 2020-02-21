<?php

use Controllers\StudentController;

function inscr_student_render_callback() {
    $student = new StudentController();
    if(is_page()){
        return $student->inscriptionStudent();
    }
}

function block_inscr_student() {
    wp_register_script(
        'inscr_student-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/inscr-student', array(
        'editor_script' => 'inscr_student-script',
        'render_callback' => 'inscr_student_render_callback'
    ));
}
add_action( 'init', 'block_inscr_student' );