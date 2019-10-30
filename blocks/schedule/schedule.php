<?php

function schedule_render_callback() {
    if(is_page()){
        $current_user = wp_get_current_user();
        if(in_array("enseignant",$current_user->roles)) {
            $controller = new Teacher();
            $controller->displaySchedules();
        }

        if(in_array("etudiant",$current_user->roles)) {
            $controller = new Student();
            $controller->displaySchedules();
        }

        if(in_array("television",$current_user->roles)) {
            $controller = new Television();
            $controller->displaySchedules();
        }

        if (in_array("technicien", $current_user->roles)){
            $controller = new Technician();
            $controller->displaySchedules();
        }

        if(in_array("administrator", $current_user->roles) || in_array("secretary", $current_user->roles)) {
            $controller = new Secretary();
            $this->view->displayWelcomeAdmin();
        }
    }
}

function block_schedule() {
    wp_register_script(
        'schedule-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/schedule', array(
        'editor_script' => 'schedule-script',
        'render_callback' => 'schedule_render_callback'
    ));
}
add_action( 'init', 'block_schedule' );