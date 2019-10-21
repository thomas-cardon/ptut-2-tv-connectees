<?php

function student_render_callback() {
    $student = new Student();
    $view = new ViewStudent();
    if(is_page()){
        $student->insertStudent();
        return $view->displayInsertImportFileStudent();
    }
}

function block_student() {
    wp_register_script(
        'student-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-student', array(
        'editor_script' => 'student-script',
        'render_callback' => 'student_render_callback'
    ));
}
add_action( 'init', 'block_student' );