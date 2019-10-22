<?php
/**
 * Created by PhpStorm.
 * UserView: r17000292
 * Date: 15/04/2019
 * Time: 09:17
 */


include_once '../../../../../../wp-config.php';

date_default_timezone_set('Europe/Paris');
setlocale(LC_TIME, 'fr_FR.utf8','fra');
$dbLink = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Erreur de connexion au serveur : ' .mysqli_connect_error());
mysqli_select_db($dbLink, DB_NAME) or die('Erreur dans la sélection de la base : ' .mysqli_error($dbLink));

if(isset($_GET['action']) && $_GET['action'] == "getTime"){
    echo strftime('%H:%M:%S');
} else if(isset($_GET['action']) && $_GET['action'] == "getDate"){
    echo strftime("%A %d %B %Y");
}