<?php

include_once 'vendor/R34ICS/R34ICS.php';

// Login for viewer
define('DB_USER_VIEWER', 'viewer');
define('DB_PASSWORD_VIEWER', 'viewer');
define('DB_HOST_VIEWER', 'localhost');
define('DB_NAME_VIEWER', 'adminwordpress');

/**
 * Create all directory
 * (For ICS file and media)
 */
if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH)) {
	mkdir($_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH)) {
	mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH, 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0')) {
	mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0', 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1')) {
	mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1', 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2')) {
	mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2', 0777);
}

if (!file_exists($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3')) {
	mkdir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3', 0777);
}

/**
 * Include all scripts
 * (CSS, JS)
 */
function loadScriptsEcran()
{
    //jQuery
    wp_enqueue_script('jquery_cdn', 'https://code.jquery.com/jquery-3.4.1.slim.min.js');

    //Bootstrap
    wp_enqueue_style('bootstrap_css', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');
    wp_enqueue_script('bootstrap_js', 'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js', array('jquery_cdn'), '', true);

    // LIBRARY
    wp_enqueue_script('pdf-js', 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.2.228/build/pdf.min.js', array(), '', false);
    wp_enqueue_script('plugin-jquerymin', TV_PLUG_PATH . 'public/js/vendor/jquery.min.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-JqueryEzMin', TV_PLUG_PATH . 'public/js/vendor/jquery.easing.min.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-jqueryEzTic', TV_PLUG_PATH . 'public/js/vendor/jquery.easy-ticker.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-jqueryEzMinTic', TV_PLUG_PATH . 'public/js/vendor/jquery.easy-ticker.min.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-marquee', TV_PLUG_PATH . 'public/js/vendor/jquery.marquee.js', array('jquery'), '', true);
    wp_enqueue_script('plugin-ticker', TV_PLUG_PATH . 'public/js/vendor/jquery.tickerNews.js', array('jquery'), '', true);

    //CSS
	wp_enqueue_style('weather-style', TV_PLUG_PATH . 'public/css/weather.css', array(), '1.0');
	wp_enqueue_style('style-style', TV_PLUG_PATH . 'public/css/style.css', array(), '1.0');
	wp_enqueue_style('alert-style', TV_PLUG_PATH . 'public/css/alert.css', array(), '1.0');
	wp_enqueue_style('info-style', TV_PLUG_PATH . 'public/css/information.css', array(), '1.0');
	wp_enqueue_style('schedule-style', TV_PLUG_PATH . 'public/css/schedule.css', array(), '1.0');

	// SCRIPT
	wp_enqueue_script('plugin-addCheckBox', TV_PLUG_PATH . 'public/js/addAllCheckBox.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-addCodeAlert', TV_PLUG_PATH . 'public/js/addOrDeleteAlertCode.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-addCodeTv', TV_PLUG_PATH . 'public/js/addOrDeleteTvCode.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-alertTicker', TV_PLUG_PATH . 'public/js/alertTicker.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-scroll', TV_PLUG_PATH . 'public/js/scroll.js', array('plugin-jquerymin', 'plugin-jqueryEzTic', 'plugin-jqueryEzMinTic', 'plugin-JqueryEzMin'), '', true);
	wp_enqueue_script('plugin-confPass', TV_PLUG_PATH . 'public/js/confirmPass.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-showModal', TV_PLUG_PATH . 'public/js/modal.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-oneSignal', TV_PLUG_PATH . 'public/js/oneSignalPush.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-slideshow', TV_PLUG_PATH . 'public/js/slideshow.js', array('jquery'), '2.0', true);
	wp_enqueue_script('plugin-weather', TV_PLUG_PATH . 'public/js/weather.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-weatherTime', TV_PLUG_PATH . 'public/js/weather_and_time.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-sortTable', TV_PLUG_PATH . 'public/js/sortTable.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-search', TV_PLUG_PATH . 'public/js/search.js', array('jquery'), '1.0', true);
}
add_action('wp_enqueue_scripts', 'loadScriptsEcran');

/**
 * Create tables in the database (Alert & Information)
 */
function installDatabaseEcran()
{
    global $wpdb;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $table_name = 'ecran_information';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			title VARCHAR (40),
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author INT(10) NOT NULL,
			type VARCHAR (10) DEFAULT 'text' NOT NULL,
			id_administration INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    // With wordpress id = 1 can't be access if we do : /page/1
    $sql = "ALTER TABLE $table_name AUTO_INCREMENT = 2;";
    dbDelta($sql);

    $table_name = 'ecran_alert';

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			content VARCHAR(280) NOT NULL,
			creation_date datetime DEFAULT NOW() NOT NULL,
			expiration_date datetime NOT NULL,
			author INT(10) NOT NULL,
			id_administration INT(10) DEFAULT NULL,
			PRIMARY KEY (id),
			FOREIGN KEY (author) REFERENCES wp_users(ID) ON DELETE CASCADE
		) $charset_collate;";

    dbDelta($sql);

    // With wordpress id = 1 can't be access if we do : /page/1
    $sql = "ALTER TABLE $table_name AUTO_INCREMENT = 2;";
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
			id_alert INT(10) NOT NULL ,
			id_code_ade INT(10) NOT NULL ,
			PRIMARY KEY (id_alert, id_code_ade),
			FOREIGN KEY (id_alert) REFERENCES ecran_alert(id) ON DELETE CASCADE,
			FOREIGN KEY (id_code_ade) REFERENCES ecran_information(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);

    $table_name = 'ecran_code_user';

    $query = "CREATE TABLE IF NOT EXISTS $table_name (
			id_user INT(10) NOT NULL ,
			id_code_ade INT(10) NOT NULL ,
			PRIMARY KEY (id_user, id_code_ade),
			FOREIGN KEY (id_user) REFERENCES wp_users(ID) ON DELETE CASCADE,
			FOREIGN KEY (id_code_ade) REFERENCES ecran_information(id) ON DELETE CASCADE
			) $charset_collate;";

    dbDelta($query);
}
add_action('plugins_loaded', 'installDatabaseEcran');