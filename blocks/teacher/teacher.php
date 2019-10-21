<?php

function teacher_render_callback() {
    $teacher = new Teacher();
    $view = new ViewTeacher();
    if(is_page()){
        $teacher->insertTeacher();
        return $view->displayInsertImportFileTeacher();
    }
}

function block_teacher() {
    wp_register_script(
        'teacher-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/add-teacher', array(
        'editor_script' => 'teacher-script',
        'render_callback' => 'teacher_render_callback'
    ));
}
add_action( 'init', 'block_teacher' );