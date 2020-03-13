<?php

use Controllers\SecretaryController;
use Controllers\StudentController;
use Controllers\StudyDirectorController;
use Controllers\TeacherController;
use Controllers\TechnicianController;
use Controllers\TelevisionController;
use Views\UserView;

/**
 * Function of the block
 *
 * @return string
 */
function schedule_render_callback()
{
    if (is_page()) {
        $current_user = wp_get_current_user();
        if(in_array('directeuretude', $current_user->roles)) {
            $controller = new StudyDirectorController();
            return $controller->displayMySchedule();
        } else if (in_array("enseignant", $current_user->roles)) {
            $controller = new TeacherController();
            return $controller->displayMySchedule();
        } else if (in_array("etudiant", $current_user->roles)) {
            $controller = new StudentController();
            return $controller->displayMySchedule();
        } else if (in_array("television", $current_user->roles)) {
            $controller = new TelevisionController();
            return $controller->displayMySchedule();
        } else if (in_array("technicien", $current_user->roles)) {
            $controller = new TechnicianController();
            return $controller->displayMySchedule();
        } else if (in_array("administrator", $current_user->roles) || in_array("secretaire", $current_user->roles)) {
            $controller = new SecretaryController();
            return $controller->displayMySchedule();
        } else {
            $user = new UserView();
            return $user->displayHome();
        }
    }
}

/**
 * Build a block
 */
function block_schedule()
{
    wp_register_script(
        'schedule-script',
        plugins_url('block.js', __FILE__),
        array('wp-blocks', 'wp-element', 'wp-data')
    );

    register_block_type('tvconnecteeamu/schedule', array(
        'editor_script' => 'schedule-script',
        'render_callback' => 'schedule_render_callback'
    ));
}

add_action('init', 'block_schedule');