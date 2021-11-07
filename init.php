<?php

use Controllers\AlertRestController;
use Controllers\CodeAdeRestController;
use Controllers\InformationRestController;
use Controllers\ProfileRestController;

//include __DIR__ . '/config-notifs.php';
include_once 'vendor/R34ICS/R34ICS.php';
include 'widgets/WidgetAlert.php';
include 'widgets/WidgetWeather.php';
include 'widgets/WidgetInformation.php';

// Login for viewer
require 'config.php';

/**
 * Create all directory
 * (For ICS file and media)
 */

if (!file_exists(PATH . TV_UPLOAD_PATH)) {
    mkdir(PATH . TV_UPLOAD_PATH);
}

if (!file_exists(PATH . TV_ICSFILE_PATH)) {
    mkdir(PATH . TV_ICSFILE_PATH, 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file0')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file0', 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file1')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file1', 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file2')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file2', 0777);
}

if (!file_exists(PATH . TV_ICSFILE_PATH . 'file3')) {
    mkdir(PATH . TV_ICSFILE_PATH . 'file3', 0777);
}

/**
 * Include all scripts
 * (CSS, JS)
 */

function loadScriptsEcran()
{
    /**
    * Chargement conditionnel
    * Cette technique permet de charger les scripts et styles nécessaires à une page spéciale UNIQUEMENT à cette page,
    * pour améliorer les performances
    * @author Thomas Cardon
    */
    if (is_page('tablet-view')) {
        wp_enqueue_script('tablet-search', TV_PLUG_PATH . 'public/js/tablet-view/search.js', array(), VERSION, true);
        return;
    }
    
    /**
     * 
     * 
     * @author Thomas Cardon
     */
     if (!is_admin()) wp_deregister_script('jquery');

    /**
    * Bootstrap
    * Mise-à-jour 2021: passage de Bootstrap 4.1.3 à Bootstrap 5
    * @author Thomas Cardon
    */
    wp_enqueue_style('bootstrap_css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap_js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array(), false, true);

    /* THIRD PARTY LIBRARIES */
    wp_enqueue_script('pdf-js', 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.2.228/build/pdf.min.js', array(), VERSION, false);

    /* SCRIPTS */
    wp_enqueue_script('global_script', TV_PLUG_PATH . 'public/js/global.js', array(), VERSION, true);

    wp_enqueue_script('addCheckBox_script_ecran', TV_PLUG_PATH . 'public/js/addAllCheckBox.js', array('jquery'), VERSION, true);
    wp_enqueue_script('addCodeAlert_script_ecran', TV_PLUG_PATH . 'public/js/addOrDeleteAlertCode.js', array('jquery'), VERSION, true);
    wp_enqueue_script('addCodeTv_script_ecran', TV_PLUG_PATH . 'public/js/addOrDeleteTvCode.js', array('jquery'), VERSION, true);
    wp_enqueue_script('alertTicker_script_ecran', TV_PLUG_PATH . 'public/js/alertTicker.js', array('jquery'), VERSION, true);
    wp_enqueue_script('confPass_script_ecran', TV_PLUG_PATH . 'public/js/confirmPass.js', array('jquery'), VERSION, true);
    //wp_enqueue_script('oneSignal_script_ecran', TV_PLUG_PATH . 'public/js/oneSignalPush.js', array('jquery'), VERSION, true);
    //wp_add_inline_script('oneSignal_script_ecran', 'const ONESIGNAL_APP_ID = \'' . ONESIGNAL_APP_ID . '\';', 'before');
    wp_enqueue_script('scroll_script_ecran', TV_PLUG_PATH . 'public/js/scroll.js', array('plugin-jquerymin', 'plugin-jqueryEzTic', 'plugin-jqueryEzMinTic', 'plugin-JqueryEzMin'), VERSION, true);
    wp_enqueue_script('search_script_ecran', TV_PLUG_PATH . 'public/js/search.js', array('jquery'), VERSION, true);
    wp_enqueue_script('slideshow_script_ecran', TV_PLUG_PATH . 'public/js/slideshow.js', array('jquery'), '2.0', true);
    wp_enqueue_script('sortTable_script_ecran', TV_PLUG_PATH . 'public/js/sortTable.js', array('jquery'), VERSION, true);

    if (is_page('tv-mode')) {
        /* STYLESHEETS */
        wp_enqueue_style('alert_ecran', TV_PLUG_PATH . 'public/css/alert.css', array(), VERSION);
        wp_enqueue_style('info_ecran', TV_PLUG_PATH . 'public/css/information.css', array(), VERSION);
        wp_enqueue_style('schedule_ecran', TV_PLUG_PATH . 'public/css/schedule.css', array(), VERSION);
        wp_enqueue_style('style_ecran', TV_PLUG_PATH . 'public/css/style.css', array(), VERSION);
        wp_enqueue_style('weather_ecran', TV_PLUG_PATH . 'public/css/weather.css', array(), VERSION);

        /* SCRIPTS */
        wp_enqueue_script('weather_script_ecran', TV_PLUG_PATH . 'public/js/weather.js', array(), VERSION, true);
        wp_enqueue_script('weatherTime_script_ecran', TV_PLUG_PATH . 'public/js/weather_and_time.js', array(), VERSION, true);

        /* THIRD PARTY LIBRARIES */
        wp_enqueue_script('plugin-marquee', TV_PLUG_PATH . 'public/js/vendor/marquee.js', array(), VERSION, true);
        wp_enqueue_script('plugin-tickit', TV_PLUG_PATH . 'public/js/vendor/tickit.js', array(), VERSION, true);

        wp_enqueue_style('style-marquee', TV_PLUG_PATH . 'public/js/vendor/marquee.css', array(), VERSION, true);
        wp_enqueue_style('style-tickit', TV_PLUG_PATH . 'public/js/vendor/tickit.css', array(), VERSION, true);
    }
}

add_action('wp_enqueue_scripts', 'loadScriptsEcran');

/**
 * Create tables in the database (Alert & Information)
 */
function installDatabaseEcran()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_name = 'ecran_information';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			title VARCHAR (40),
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			type VARCHAR (10) DEFAULT 'text' NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_alert';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author BIGINT(20) UNSIGNED NOT NULL,
			for_everyone INT(1) DEFAULT '1' NOT NULL,
			administration_id INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    $table_name = 'ecran_code_ade';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			type VARCHAR(15) NOT NULL,
			title VARCHAR (60) NOT NULL,
			code VARCHAR (20) NOT NULL,
			PRIMARY KEY (id)
			) $charset_collate;";

    dbDelta($query);

    // With wordpress id = 1 can't be access if we do : /page/1
    $sql = "ALTER TABLE $table_name AUTO_INCREMENT = 2;";
    dbDelta($sql);

    $table_name = 'ecran_code_alert';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			alert_id INT(10) NOT NULL ,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (alert_id, code_ade_id),
			FOREIGN KEY (alert_id) REFERENCES ecran_alert(id) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    $table_name = 'ecran_code_user';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code_ade_id INT(10) NOT NULL ,
			PRIMARY KEY (user_id, code_ade_id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
			FOREIGN KEY (code_ade_id) REFERENCES ecran_code_ade(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    $table_name = 'ecran_code_delete_account';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			code VARCHAR(40) NOT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);
}

add_action('plugins_loaded', 'installDatabaseEcran');


/*
 * CREATE ROLES
 */

$result = add_role(
    'secretaire',
    __('Secretaire'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'television',
    __('Television'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'etudiant',
    __('Etudiant'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'enseignant',
    __('Enseignant'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'technicien',
    __('Technicien'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);

$result = add_role(
    'directeuretude',
    __('Directeur etude'),
    array(
        'read' => true,  // true allows this capability
        'edit_posts' => true,
        'delete_posts' => false, // Use false to explicitly deny
    )
);
$result = add_role(
    'informationposter',
    __('informationPoster'),
    array(
        'read' => true,  // true allows this capability
    )
);

/*
 * CREATE REST API ENDPOINTS
 */

add_action('rest_api_init', function () {
    $controller = new InformationRestController();
    $controller->register_routes();

    $controller = new CodeAdeRestController();
    $controller->register_routes();

    $controller = new AlertRestController();
    $controller->register_routes();

    $controller = new ProfileRestController();
    $controller->register_routes();
});
