<?php

function schedule_render_callback() {
    $schedule = new Schedule();
    if(is_page()){
        return $schedule->displaySchedules();
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