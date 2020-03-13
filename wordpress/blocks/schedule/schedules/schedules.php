<?php

use Controllers\UserController;

/**
 * Function of the block
 *
 * @return string
 */
function schedules_render_callback()
{
    if(is_page()) {
	    $schedule = new UserController();
        return $schedule->displayYearSchedule();
    }
}

/**
 * Build a block
 */
function block_schedules()
{
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