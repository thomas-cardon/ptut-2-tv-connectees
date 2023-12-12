<?php

use Controllers\AlertController;
use Controllers\CodeAdeController;
use Controllers\InformationController;
use Controllers\SecretaryController;
use Controllers\StudyDirectorController;
use Controllers\TeacherController;
use Controllers\TechnicianController;
use Controllers\TelevisionController;
use Controllers\UserController;
use Controllers\TabletModeController;

use Views\HelpMapView;
use Views\TabletModeScheduleView;

use UserViews\UserView;

/*
* TABLET VIEW BLOCKS
*/

/* Schedule render function */
function tablet_schedule_render_callback()
{
  if(is_page()) {
    $view = new TabletModeScheduleView();
    return $view->display();
  }
}

/* Schedule */
function block_tablet_schedule()
{
  wp_register_script(
    'tablet-schedule-script',
    plugins_url( 'blocks/tablet-mode/schedule/index.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/tablet-schedule', array(
    'editor_script' => 'tablet-schedule-script',
    'render_callback' => 'tablet_schedule_render_callback'
  ));
}

add_action('init', 'block_tablet_schedule');

/* Select year render function */
function tablet_select_year_render_callback()
{
  if(is_page()) {
    $controller = new TabletModeController();
    return $controller->displayYearSelector();
  }
}

/* Select year */
function block_tablet_mode_select_year()
{
  wp_register_script(
    'tablet-year-script',
    plugins_url( 'blocks/tablet-mode/select-year/index.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/tablet-select-year', array(
    'editor_script' => 'tablet-year-script',
    'render_callback' => 'tablet_select_year_render_callback'
  ));
}

add_action('init', 'block_tablet_mode_select_year');

/*
* ALERT BLOCKS
*/

/**
* Function of the block
*
* @return string
*/
function alert_render_callback()
{
  if(is_page()) {
    $alert = new AlertController();
    return $alert->insert();
  }
}

/**
* Build a block
*/
function block_alert()
{
  wp_register_script(
    'alert-script',
    plugins_url( '/blocks/alert/create.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/add-alert', array(
    'editor_script' => 'alert-script',
    'render_callback' => 'alert_render_callback'
  ));
}
add_action('init', 'block_alert');


/**
* Function of the block
*
* @return string
*/
function alert_management_render_callback()
{
  if(is_page()) {
    $alert = new AlertController();
    return $alert->displayTable();
  }
}

/**
* Build a block
*/
function block_alert_management()
{
  wp_register_script(
    'alert_manage-script',
    plugins_url( '/blocks/alert/displayAll.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/manage-alert', array(
    'editor_script' => 'alert_manage-script',
    'render_callback' => 'alert_management_render_callback'
  ));
}
add_action( 'init', 'block_alert_management' );

/**
* Function of the block
*
* @return string
*/
function alert_modify_render_callback()
{
  if(is_page()) {
    $alert = new AlertController();
    return $alert->modify();
  }
}

/**
* Build a block
*/
function block_alert_modify()
{
  wp_register_script(
    'alert_modify-script',
    plugins_url( '/blocks/alert/modify.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/modify-alert', array(
    'editor_script' => 'alert_modify-script',
    'render_callback' => 'alert_modify_render_callback'
  ));
}
add_action( 'init', 'block_alert_modify' );

/*
* CODE ADE BLOCKS
*/

/**
* Function of the block
*
* @return string
*/
function code_ade_render_callback()
{
  if(is_page()) {
    $codeAde = new CodeAdeController();
    return $codeAde->insert();
  }
}

/**
* Build a block
*/
function block_code_ade()
{
  wp_register_script(
    'code_ade-script',
    plugins_url( '/blocks/codeAde/create.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/add-code', array(
    'editor_script' => 'code_ade-script',
    'render_callback' => 'code_ade_render_callback'
  ));
}
add_action( 'init', 'block_code_ade' );

/**
* Function of the block
*
* @return string
*/
function code_management_render_callback($attributes, $content)
{
  if(is_page()) {
    $controller = new CodeAdeController();
    $controller->deleteCodes();

    return $controller->displayContent($content);
  }
}

/**
* Build a block
*/
function block_code_management()
{
  wp_register_script(
    'code_manage-script',
    plugins_url( '/blocks/codeAde/displayAll.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/manage-codes', array(
    'editor_script' => 'code_manage-script',
    'render_callback' => 'code_management_render_callback'
  ));
}
add_action( 'init', 'block_code_management' );


/**
* Function of the block
*
* @return string
*/
function code_modify_render_callback()
{
  if(is_page()) {
    $code = new CodeAdeController();
    return $code->modify();
  }
}

/**
* Build a block
*/
function block_code_modify()
{
  wp_register_script(
    'code_modify-script',
    plugins_url( '/blocks/codeAde/modify.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/modify-code', array(
    'editor_script' => 'code_modify-script',
    'render_callback' => 'code_modify_render_callback'
  ));
}
add_action( 'init', 'block_code_modify' );

/*
* INFORMATION BLOCKS
*/

/**
* Function of the block
*
* @return string
*/
function information_render_callback()
{
  if(is_page()) {
    $information = new InformationController();
    return $information->create();
  }
}

/**
* Build a block
*/
function block_information()
{
  wp_register_script(
    'information-script',
    plugins_url( '/blocks/information/create.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/add-information', array(
    'editor_script' => 'information-script',
    'render_callback' => 'information_render_callback'
  ));
}
add_action( 'init', 'block_information' );

/**
* Function of the block
*
* @return string
*/
function information_management_render_callback()
{
  if(is_page()) {
    $information = new InformationController();
    return $information->displayTable();
  }
}

/**
* Build a block
*/
function block_information_management()
{
  wp_register_script(
    'information_manage-script',
    plugins_url( '/blocks/information/displayAll.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/manage-information', array(
    'editor_script' => 'information_manage-script',
    'render_callback' => 'information_management_render_callback'
  ));
}
add_action( 'init', 'block_information_management' );


/**
* Function of the block
*
* @return string
*/
function information_modify_render_callback()
{
  if(is_page()) {
    $information = new InformationController();
    return $information->modify();
  }
}

/**
* Build a block
*/
function block_information_modify()
{
  wp_register_script(
    'information_modify-script',
    plugins_url( '/blocks/information/modify.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/modify-information', array(
    'editor_script' => 'information_modify-script',
    'render_callback' => 'information_modify_render_callback'
  ));
}
add_action( 'init', 'block_information_modify' );

/*
* SCHEDULE BLOCKS
*/

/**
* Function of the block
*
* @return string
*/
function schedule_render_callback()
{
  $controller = null;
  if (members_current_user_has_role("television"))
    $controller = new TelevisionController();
  else if(members_current_user_has_role("directeuretude")) {
    $controller = new StudyDirectorController();
  } else if (members_current_user_has_role("enseignant")) {
    $controller = new TeacherController();
  } else if (members_current_user_has_role("technicien")) {
    $controller = new TechnicianController();
  } else if (members_current_user_has_role("administrator") || members_current_user_has_role("secretaire")) {
    $controller = new SecretaryController();
  } else {
    $controller = new UserController();
  }

  return $controller->displayContent();
}

/* TV Mode */
function tv_mode_render_callback() {
  $controller = new TelevisionController();
  if (members_current_user_has_role("television")) {
    return $controller->displayTVInterface();
  }

  return $controller->error(403, "Vous n'avez pas les permissions requises pour accéder à cette page.");
}

function block_tv_mode()
{
  wp_register_script(
    'tv-mode-script',
    plugins_url('/blocks/schedule/tv.js', __FILE__),
    array('wp-blocks', 'wp-element', 'wp-data')
  );

  register_block_type('tvconnecteeamu/tv-mode', array(
    'editor_script' => 'tv-mode-script',
    'render_callback' => 'tv_mode_render_callback'
  ));
}

add_action('init', 'block_tv_mode');

/**
* Build a block
*/
function block_schedule()
{
  wp_register_script(
    'schedule-script',
    plugins_url('/blocks/schedule/userSchedule.js', __FILE__),
    array('wp-blocks', 'wp-element', 'wp-data')
  );

  register_block_type('tvconnecteeamu/schedule', array(
    'editor_script' => 'schedule-script',
    'render_callback' => 'schedule_render_callback'
  ));
}
add_action('init', 'block_schedule');

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
    plugins_url( '/blocks/schedule/globalSchedule.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/schedules', array(
    'editor_script' => 'schedules-script',
    'render_callback' => 'schedules_render_callback'
  ));
}
add_action( 'init', 'block_schedules' );

/*
* USER BLOCKS
*/

/**
* Function of the block
*
* @return string
*/
function creation_user_render_callback()
{
  if(is_page()) {
    $controller = new SecretaryController();
    return $controller->displayUserCreationView();
  }
}

/**
* Build a block
*/
function block_creation_user()
{
  wp_register_script(
    'creation_user-script',
    plugins_url( '/blocks/user/create.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/creation-user', array(
    'editor_script' => 'creation_user-script',
    'render_callback' => 'creation_user_render_callback'
  ));
}
add_action( 'init', 'block_creation_user' );

/**
* Function of the block
*
* @return string
*/
function management_user_render_callback()
{
  if(is_page()) {
    $controller = new SecretaryController();
    $controller->deleteUsers();
    return $controller->displayUsers();
  }
}

/**
* Build a block
*/
function block_management_user()
{
  wp_register_script(
    'management_user-script',
    plugins_url( '/blocks/user/displayAll.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/management-user', array(
    'editor_script' => 'management_user-script',
    'render_callback' => 'management_user_render_callback'
  ));
}
add_action( 'init', 'block_management_user' );

/**
* Function of the block
*
* @return string
*/
function user_modify_render_callback()
{
  if(is_page()) {
    $user = new SecretaryController();
    return $user->modifyUser();
  }
}

/**
* Build a block
*/
function block_user_modify()
{
  wp_register_script(
    'user_modify-script',
    plugins_url( '/blocks/user/modify.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/modify-user', array(
    'editor_script' => 'user_modify-script',
    'render_callback' => 'user_modify_render_callback'
  ));
}
add_action( 'init', 'block_user_modify' );

/**
* Function of the block
*
* @return string
*/
function choose_account_render_callback()
{
  if(is_page()) {
    $user = new UserController();
    return $user->edit();
  }
}

/**
* Build a block
*/
function block_choose_account() {
  wp_register_script(
    'choose_account-script',
    plugins_url( '/blocks/user/account.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/choose-account', array(
    'editor_script' => 'choose_account-script',
    'render_callback' => 'choose_account_render_callback'
  ));
}
add_action( 'init', 'block_choose_account' );

/**
* Function of the block
*
* @return string
*/
function delete_account_render_callback()
{
  if(is_page()) {
    $myAccount = new UserController();
    $view = new UserView();
    $myAccount->deleteAccount();
    return $view->displayDeleteAccount().$view->displayEnterCode();
  }
}

/**
* Build a block
*/
function block_delete_account()
{
  wp_register_script(
    'delete_account-script',
    plugins_url( 'block.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/delete-account', array(
    'editor_script' => 'delete_account-script',
    'render_callback' => 'delete_account_render_callback'
  ));
}
add_action( 'init', 'block_delete_account' );

/**
* Function of the block
*
* @return string
*/
function password_modify_render_callback()
{
  if(is_page()) {
    $myAccount = new UserController();
    $view = new UserView();
    $myAccount->modifyPwd();
    return $view->displayModifyPassword();
  }
}

/**
* Build a block
*/
function block_password_modify()
{
  wp_register_script(
    'pass_modify-script',
    plugins_url( 'block.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/modify-pass', array(
    'editor_script' => 'pass_modify-script',
    'render_callback' => 'password_modify_render_callback'
  ));
}
add_action( 'init', 'block_password_modify' );

/**
* Function of the block
*
* @return string
*/
function help_map_render_callback()
{
  if(is_page()) {
    $view = new HelpMapView();
    return $view->displayHelpMap();
  }
}

/**
* Build a block
*/
function block_help_map()
{
  wp_register_script(
    'help_map-script',
    plugins_url( '/blocks/helpMap/display.js', __FILE__ ),
    array( 'wp-blocks', 'wp-element', 'wp-data' )
  );

  register_block_type('tvconnecteeamu/help-map', array(
    'editor_script' => 'help_map-script',
    'render_callback' => 'help_map_render_callback'
  ));
}
add_action( 'init', 'block_help_map' );


/**
 * Function of the block
 *
 * @return string
 */
function secretary_welcome_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_secretary_welcome() {
    register_block_type('tvconnecteeamu/secretary-welcome', array(
        'render_callback' => 'secretary_welcome_render_callback'
    ));
}
add_action( 'init', 'block_secretary_welcome' );


/**
 * Function of the block
 *
 * @return string
 */
function secretary_computer_rooms_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_secretary_computer_rooms() {
    register_block_type('tvconnecteeamu/computer-rooms', array(
        'render_callback' => 'secretary_computer_rooms_render_callback'
    ));
}
add_action( 'init', 'block_secretary_computer_rooms' );


/**
 * Function of the block
 *
 * @return string
 */
function secretary_teacher_schedule_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_secretary_teacher_schedule() {
    register_block_type('tvconnecteeamu/teacher-schedule', array(
        'render_callback' => 'secretary_teacher_schedule_render_callback'
    ));
}
add_action( 'init', 'block_secretary_teacher_schedule' );


/**
 * Function of the block
 *
 * @return string
 */
function secretary_main_menu_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_secretary_main_menu() {
    register_block_type('tvconnecteeamu/main-menu', array(
        'render_callback' => 'secretary_main_menu_render_callback'
    ));
}
add_action( 'init', 'block_secretary_main_menu' );


/**
 * Function of the block
 *
 * @return string
 */
function secretary_room_schedule_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_secretary_room_schedule() {
    register_block_type('tvconnecteeamu/room-schedule', array(
        'render_callback' => 'secretary_room_schedule_render_callback'
    ));
}
add_action( 'init', 'block_secretary_room_schedule' );


/**
 * Function of the block
 *
 * @return string
 */
function year_student_schedule_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_year_student_schedule() {
    register_block_type('tvconnecteeamu/year-student-schedule', array(
        'render_callback' => 'year_student_schedule_render_callback'
    ));
}
add_action( 'init', 'block_year_student_schedule' );


/**
 * Function of the block
 *
 * @return string
 */
function group_student_schedule_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_group_student_schedule() {
    register_block_type('tvconnecteeamu/group-student-schedule', array(
        'render_callback' => 'group_student_schedule_render_callback'
    ));
}
add_action( 'init', 'block_group_student_schedule' );


/**
 * Function of the block
 *
 * @return string
 */
function all_years_schedule_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_all_years_schedule() {
    register_block_type('tvconnecteeamu/all-years-schedule', array(
        'render_callback' => 'all_years_schedule_render_callback'
    ));
}
add_action( 'init', 'block_all_years_schedule' );


/**
 * Function of the block
 *
 * @return string
 */
function teacher_search_schedule_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_teacher_search_schedule() {
    register_block_type('tvconnecteeamu/teacher-search-schedule', array(
        'render_callback' => 'teacher_search_schedule_render_callback'
    ));
}
add_action( 'init', 'block_teacher_search_schedule' );


/**
 * Function of the block
 *
 * @return string
 */
function weekly_computer_room_schedule_render_callback()
{
    if(is_page()) {
        $user = new SecretaryController();
        return $user->displayWelcomePage();
    }
}

/**
 * Build a block
 */
function block_weekly_computer_room_schedule() {
    register_block_type('tvconnecteeamu/weekly-computer-room-schedule', array(
        'render_callback' => 'weekly_computer_room_schedule_render_callback'
    ));
}
add_action( 'init', 'block_weekly_computer_room_schedule' );
