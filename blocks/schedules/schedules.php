<?php

function schedules_render_callback() {
    $schedule = new User();
    if(is_page()){
        return $schedule->displayYearSchedule();
    }
}

function block_schedules() {
    wp_register_script(
        'schedules-script',
        plugins_url( 'block.js', __FILE__ ),
        array( 'wp-blocks', 'wp-element', 'wp-data' )
    );

    register_block_type('tvconnecteeamu/schedules', array(
        'editor_script' => 'schedules-script',
        'render_callback' => 'schedules_render_callback'
    ));
}
add_action( 'init', 'block_schedules' );