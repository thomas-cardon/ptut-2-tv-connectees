<?php

define('DB_USER_VIEWER', 'root');
define('DB_PASSWORD_VIEWER', '');
define('DB_HOST_VIEWER', 'localhost');
define('DB_NAME_VIEWER', 'tv');
define('URL_WEBSITE_VIEWER', 'http://localhost/');

define('VERSION', /*'2.0'*/ rand(1, 9999999));

define('PATH', /* $_SERVER['DOCUMENT_ROOT'] */ 'C:/xampp/htdocs/tv/');
define('URL_PATH', /* example: http://localhost/tv -> /tv */ $_SERVER['REQUEST_URI']);

/**
 * Remplacez ici <ONESIGNAL_APP_ID> par la valeur du champ "ONESIGNAL APP ID" dans OneSignal
 */
define('ONESIGNAL_APP_ID',  '<ONESIGNAL_APP_ID>');

/**
 * Remplacez ici <REST_API_KEY> par la valeur du champ "REST API KEY" dans OneSignal
 */
define('ONESIGNAL_API_KEY', '<REST_API_KEY>');
