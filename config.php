<?php

define('TV_PLUG_PATH', '/wp-content/plugins/plugin-ecran-connecte/');
define('TV_UPLOAD_PATH', '/wp-content/uploads/media/');
define('TV_ICSFILE_PATH', '/wp-content/uploads/fileICS/');

require __DIR__ . '/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';

//Blocks
include_once 'wordpress/blocks/alert/createAlert/alert.php';
include_once 'wordpress/blocks/alert/manageAlert/manageAlert.php';
include_once 'wordpress/blocks/alert/modifyAlert/modifyAlert.php';

include_once 'wordpress/blocks/codeAde/createCodeAde/codeAde.php';
include_once 'wordpress/blocks/codeAde/manageCodeAde/manageCodeAde.php';
include_once 'wordpress/blocks/codeAde/modifyCodeAde/modifyCodeAde.php';

include_once 'wordpress/blocks/information/createInformation/information.php';
include_once 'wordpress/blocks/information/manageInformation/informationManage.php';
include_once 'wordpress/blocks/information/modifyInformation/modifyInformation.php';

include_once 'wordpress/blocks/schedule/schedule/schedule.php';
include_once 'wordpress/blocks/schedule/schedules/schedules.php';

include_once 'wordpress/blocks/subscriptionPush/subscriptionPush.php';

include_once 'wordpress/blocks/user/userManage/managementUser.php';
include_once 'wordpress/blocks/user/modifyUser/modifyUser.php';
include_once 'wordpress/blocks/user/myAccount/myAccountPass/myAccountPass.php';
include_once 'wordpress/blocks/user/myAccount/myAccountDelete/myAccountDelete.php';
include_once 'wordpress/blocks/user/myAccount/myAccountCode/myAccountCode.php';
include_once 'wordpress/blocks/user/createUser/createUser.php';
include_once 'wordpress/blocks/user/myAccount/myAccountChoose/myAccountChoose.php';
include_once 'wordpress/blocks/user/inscription/inscription.php';

// Widgets
include_once 'wordpress/widgets/WidgetAlert.php';
include_once 'wordpress/widgets/WidgetInformation.php';
include_once 'wordpress/widgets/WidgetWeather.php';
include_once 'wordpress/widgets/WidgetSchedule.php';

include_once 'vendor/R34ICS/R34ICS.php';

include 'database/install_DB_Tv.php';

createDatabase();
createRoles();


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
function wpDocs_plugin_ecran_connectee_scripts()
{
	//CSS
	wp_enqueue_style('plugin-bootstrap-style', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), true);
	wp_enqueue_style('weather-style', TV_PLUG_PATH . 'public/css/weather.css', array(), '1.0');
	wp_enqueue_style('style-style', TV_PLUG_PATH . 'public/css/style.css', array(), '1.0');
	wp_enqueue_style('alert-style', TV_PLUG_PATH . 'public/css/alert.css', array(), '1.0');
	wp_enqueue_style('info-style', TV_PLUG_PATH . 'public/css/information.css', array(), '1.0');
	wp_enqueue_style('schedule-style', TV_PLUG_PATH . 'public/css/schedule.css', array(), '1.0');

	// JQUERY
	wp_enqueue_script('plugin-jquerymin', TV_PLUG_PATH . 'public/js/vendor/jquery.min.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-JqueryEzMin', TV_PLUG_PATH . 'public/js/vendor/jquery.easing.min.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-jqueryEzTic', TV_PLUG_PATH . 'public/js/vendor/jquery.easy-ticker.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-jqueryEzMinTic', TV_PLUG_PATH . 'public/js/vendor/jquery.easy-ticker.min.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-marquee', TV_PLUG_PATH . 'public/js/vendor/jquery.marquee.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-ticker', TV_PLUG_PATH . 'public/js/vendor/jquery.tickerNews.js', array('jquery'), '', true);

	// SCRIPT
	wp_enqueue_script('pdf-js', 'https://cdn.jsdelivr.net/npm/pdfjs-dist@2.2.228/build/pdf.min.js', array(), '', false);
	wp_enqueue_script('plugin-addCheckBox', TV_PLUG_PATH . 'public/js/addAllCheckBox.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-addCodeAlert', TV_PLUG_PATH . 'public/js/addOrDeleteAlertCode.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-addCodeTv', TV_PLUG_PATH . 'public/js/addOrDeleteTvCode.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-alertTicker', TV_PLUG_PATH . 'public/js/alertTicker.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-scroll', TV_PLUG_PATH . 'public/js/scroll.js', array('plugin-jquerymin', 'plugin-jqueryEzTic', 'plugin-jqueryEzMinTic', 'plugin-JqueryEzMin'), '', true);
	wp_enqueue_script('plugin-confPass', TV_PLUG_PATH . 'public/js/confirmPass.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-showModal', TV_PLUG_PATH . 'public/js/modal.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-OneSignal', TV_PLUG_PATH . 'public/js/oneSignalPush.js', array('jquery'), '', true);
	wp_enqueue_script('plugin-slideshow', TV_PLUG_PATH . 'public/js/slideshow.js', array('jquery'), '2.0', true);
	wp_enqueue_script('plugin-weather', TV_PLUG_PATH . 'public/js/weather.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-weatherTime', TV_PLUG_PATH . 'public/js/weather_and_time.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-sortTable', TV_PLUG_PATH . 'public/js/sortTable.js', array('jquery'), '1.0', true);
	wp_enqueue_script('plugin-search', TV_PLUG_PATH . 'public/js/search.js', array('jquery'), '1.0', true);
}

add_action('wp_enqueue_scripts', 'wpDocs_plugin_ecran_connectee_scripts');