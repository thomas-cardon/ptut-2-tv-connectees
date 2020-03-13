<?php

/**
 * Plugin Name:       Ecran connecté AMU
 * Plugin URI:        https://github.com/Nicolas-Rohrbach/plugin-ecran-connecte
 * Description:       Plugin écrans connectés de l'AMU, ce plugin permet de générer des fichiers ICS. Ces fichiers sont ensuite lus pour pouvoir afficher l'emploi du temps de la personne connectée. Ce plugin permet aussi d'afficher la météo, des informations, des alertes. Tant en ayant une gestion des utilisateurs et des informations.
 * Version:           1.2.9
 * Author:            Léa Arnaud & Nicolas Rohrbach
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ecran-connecte
 * GitHub Plugin URI: https://github.com/Nicolas-Rohrbach/plugin-ecran-connecte
 */

use Controllers\CodeAdeController;
use Models\CodeAde;
use Models\User;

if (! defined('ABSPATH')) {
	die;
}

include 'config.php';

// Upload schedules
$download = filter_input(INPUT_POST, 'dlEDT');
if (isset($download)) {
    downloadFileICS_func();
}

/**
 * Function for WPCron
 * Upload schedules
 */
function downloadFileICS_func()
{
    move_fileICS_schedule();

	$controllerAde = new CodeAdeController();
    $model = new CodeAde();

    $codesAde = $model->getAll();
    foreach ($codesAde as $codeAde) {
        $controllerAde->addFile($codeAde->getCode());
    }

    $user = new User();
    $teachers = $user->getUsersByRole('enseignant');
    downloadSchedule($teachers);
    $studyDirector = $user->getUsersByRole('directeuretude');
    downloadSchedule($studyDirector);
}

add_action('downloadFileICS', 'downloadFileICS_func');

/**
 * Upload the schedule of users
 *
 * @param $users    User[]
 */
function downloadSchedule($users)
{
    $controllerAde = new CodeAdeController();
    foreach ($users as $user) {
        foreach ($user->getCodes() as $code) {
            $controllerAde->addFile($code->getCode());
        }
    }
}

/**
 * Change place of file
 */
function move_fileICS_schedule()
{
    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3/' . $myFile)) {
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3/' . $myFile);
            }
        }
    }
    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile)) {
                copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file3/' . $myFile);
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile);
            }
        }
    }

    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile)) {
                copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file2/' . $myFile);
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile);
            }
        }
    }

    if ($myFiles = scandir($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0')) {
        foreach ($myFiles as $myFile) {
            if (is_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $myFile)) {
                copy($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $myFile, $_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file1/' . $myFile);
                wp_delete_file($_SERVER['DOCUMENT_ROOT'] . TV_ICSFILE_PATH . 'file0/' . $myFile);
            }
        }
    }
}

/**
 * Function for "Nuit de l'info"
 * Give numbers of participant
 *
 * @return string
 */
function displayParticipant()
{
    $url = "https://www.nuitdelinfo.com/inscription/sites/55";
    $result = file_get_contents($url);
    $result = explode('<li class="list-group-item list-group-item-info">', $result);
    $result1 = substr($result[1], 0, -60);
    $result2 = substr($result[2], 0, 70);
    return '<p class="info-text"> - '.$result1.'</p><p class="info-text"> - '.$result2.'</p>';
}