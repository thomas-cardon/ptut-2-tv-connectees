<?php

/**
 * Plugin Name:       Ecran connecté AMU
 * Plugin URI:        https://github.com/Nicolas-Rohrbach/plugin-ecran-connecte
 * Description:       Plugin écrans connectées de l'AMU, ce plugin permet de générer des fichiers ICS. Ces fichiers sont ensuite lus pour pouvoir afficher l'emploi du temps de la personne connectée. Ce plugin permet aussi d'afficher la météo, des informations, des alertes. Tant en ayant une gestion des utilisateurs et des informations.
 * Version:           1.2.3
 * Author:            Léa Arnaud & Nicolas Rohrbach
 * License:           GNU General Public License v2
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       github-updater
 * GitHub Plugin URI: https://github.com/Nicolas-Rohrbach/plugin-ecran-connecte
 */

//On inclut tous les fichiers du plugin
include_once 'install_DB_Tv.php';
include_once 'recaptchalib.php';

include_once 'controllers/ControllerG.php';
include_once 'models/Model.php';
include_once 'views/ViewG.php';

include_once 'controllers/CodeAde.php';
include_once 'models/CodeAdeManager.php';
include_once 'views/ViewCodeAde.php';

include_once 'controllers/Student.php';
include_once 'models/StudentManager.php';
include_once 'views/ViewStudent.php';

include_once 'controllers/Teacher.php';
include_once 'models/TeacherManager.php';
include_once 'views/ViewTeacher.php';

include_once 'controllers/Television.php';
include_once 'models/TelevisionManager.php';
include_once 'views/ViewTelevision.php';

include_once 'controllers/Secretary.php';
include_once 'models/SecretaryManager.php';
include_once 'views/ViewSecretary.php';

include_once 'controllers/Technician.php';
include_once 'models/TechnicianManager.php';
include_once 'views/ViewTechnician.php';

include_once 'controllers/StudyDirector.php';
include_once 'models/StudyDirectorManager.php';
include_once 'views/ViewStudyDirector.php';

include_once 'controllers/ManagementUsers.php';
include_once 'views/ViewManagementUsers.php';

include_once 'controllers/MyAccount.php';
include_once 'models/MyAccountManager.php';
include_once 'views/ViewMyAccount.php';

include_once 'controllers/R34ICS.php';
include_once 'views/ViewICS.php';
include_once 'controllers/Schedule.php';
include_once 'views/ViewSchedule.php';
include_once 'widgets/WidgetSchedule.php';

include_once 'controllers/Weather.php';
include_once 'views/ViewWeather.php';
include_once 'widgets/WidgetWeather.php';

include_once 'controllers/Information.php';
include_once 'models/InformationManager.php';
include_once 'views/ViewInformation.php';
include_once 'widgets/WidgetInformation.php';

include_once 'controllers/Alert.php';
include_once 'models/AlertManager.php';
include_once 'views/ViewAlert.php';
include_once 'widgets/WidgetAlert.php';

//Blocks
include_once 'blocks/schedule/schedule.php';
include_once 'blocks/schedules/schedules.php';
include_once 'blocks/student/student.php';
include_once 'blocks/teacher/teacher.php';
include_once 'blocks/television/television.php';
include_once 'blocks/secretary/secretary.php';
include_once 'blocks/technician/technician.php';
include_once 'blocks/userManage/managementUser.php';
include_once 'blocks/userModify/userModify.php';
include_once 'blocks/myAccountPass/myAccountPass.php';
include_once 'blocks/myAccountDelete/myAccountDelete.php';
include_once 'blocks/alert/alert.php';
include_once 'blocks/alertManage/alertManage.php';
include_once 'blocks/alertModify/alertModify.php';
include_once 'blocks/information/information.php';
include_once 'blocks/informationManage/informationManage.php';
include_once 'blocks/informationModify/informationModify.php';
include_once 'blocks/codeAde/codeAde.php';
include_once 'blocks/codeAdeManage/codeAdeManage.php';
include_once 'blocks/codeAdeModify/codeAdeModify.php';
include_once 'blocks/subscriptionPush/subscriptionPush.php';
include_once 'blocks/studyDirector/studyDirector.php';
include_once 'blocks/myAccountCode/myAccountCode.php';
include_once 'blocks/userCreation/userCreation.php';
include_once 'blocks/myAccountChoose/myAccountChoose.php';
include_once 'blocks/inscription/inscription.php';

require ('models/Excel/vendor/autoload.php');


// Initialize plugin
add_action('init', function(){
    global $R34ICS;
    $R34ICS = new R34ICS();
});

$dl = $_POST['dlEDT'];
if(isset($dl)) {
    downloadFileICS_func();
}


/**
 * Fonction pour la Cron de WordPress
 * Cette fonction télécharge tous les fichiers ICS des codes ADE enregistrés dans la base de données
 */
function downloadFileICS_func() {
    $model = new CodeAdeManager();
    $allCodes = $model->getAllCode();
    $controllerAde = new CodeAde();
    foreach ($allCodes as $code){
        $path = $controllerAde->getFilePath($code['code']);
        $controllerAde->addFile($code['code']);
        if(filesize($path) < 200){
            $controllerAde->addFile($code['code']);
        }
    }
    $teachers = $model->getUsersByRole('enseignant');
    dlSchedule($teachers);
    $studyDirector = $model->getUsersByRole('directeuretude');
    dlSchedule($studyDirector);

}
add_action( 'downloadFileICS', 'downloadFileICS_func' );

function dlSchedule($users) {
    $controllerAde = new CodeAde();
    foreach ($users as $user) {
        $codes = unserialize($user['code']);
        if(is_array($codes)) {
            foreach ($codes as $code) {
                $path = $controllerAde->getFilePath($code);
                $controllerAde->addFile($code);
                if(file_get_contents($path) == ''){
                    $controllerAde->addFile($code);
                }
            }
        } else {
            $path = $controllerAde->getFilePath($codes);
            $controllerAde->addFile($codes);
            if(file_get_contents($path) == ''){
                $controllerAde->addFile($codes);
            }
        }
    }
}

/**
 * Inclut tous les fichiers CSS et les fichiers JS
 */
function wpdocs_plugin_teleconnecteeAmu_scripts() {
    wp_enqueue_style('plugin-bootstrap-style', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css', array(), true);
    wp_enqueue_style('weather-style', '/wp-content/plugins/TeleConnecteeAmu/views/css/weather.css', array(), true);
    wp_enqueue_style('style-style', '/wp-content/plugins/TeleConnecteeAmu/views/css/style.css', array(), true);
    wp_enqueue_style('alert-style', '/wp-content/plugins/TeleConnecteeAmu/views/css/alert.css', array(), true);
    wp_enqueue_style('info-style', '/wp-content/plugins/TeleConnecteeAmu/views/css/information.css', array(), true);
    wp_enqueue_style('schedule-style', '/wp-content/plugins/TeleConnecteeAmu/views/css/schedule.css', array(), true);
    wp_enqueue_script( 'theme-jquery', get_template_directory_uri() . '/assets/js/jquery-3.3.1.min.js', array (), '', false);
    wp_enqueue_script( 'theme-jqueryUI', get_template_directory_uri() . '/assets/js/jquery-ui.min.js', array ( 'jquery' ), '', false);
    wp_enqueue_script( 'theme-jqueryEzTic', '/wp-content/plugins/TeleConnecteeAmu/views/js/jquery.easy-ticker.js', array ( 'jquery' ), '', false);
    wp_enqueue_script( 'plugin-addCheckBox', '/wp-content/plugins/TeleConnecteeAmu/views/js/addAllCheckBox.js', array ( 'jquery' ), '', false);
    wp_enqueue_script( 'plugin-addCodeTv', '/wp-content/plugins/TeleConnecteeAmu/views/js/addOrDeleteTvCode.js', array ( 'jquery' ), '', false);
    wp_enqueue_script( 'plugin-checkCaptcha', '/wp-content/plugins/TeleConnecteeAmu/views/js/checkCaptcha.js', array ( 'jquery' ), '', false);
    wp_enqueue_script( 'plugin-addCodeAlert', '/wp-content/plugins/TeleConnecteeAmu/views/js/addOrDeleteAlertCode.js', array ( 'jquery' ), '', false);
    wp_enqueue_script( 'plugin-marquee', '/wp-content/plugins/TeleConnecteeAmu/views/js/jquery.marquee.js', array ( 'jquery' ), '', false);
    wp_enqueue_script( 'plugin-slideshow', '/wp-content/plugins/TeleConnecteeAmu/views/js/slideshow.js', array ( 'jquery' ), '', true);
    wp_enqueue_script( 'plugin-showModal', '/wp-content/plugins/TeleConnecteeAmu/views/js/modal.js', array ( 'jquery' ), '', true);
    wp_enqueue_script( 'plugin-ticker', '/wp-content/plugins/TeleConnecteeAmu/views/js/jquery.tickerNews.js', array ( 'jquery' ), '', true);
    wp_enqueue_script( 'plugin-alertTicker', '/wp-content/plugins/TeleConnecteeAmu/views/js/alertTicker.js', array ( 'jquery' ), '', true);
    wp_enqueue_script( 'plugin-OneSignal', '/wp-content/plugins/TeleConnecteeAmu/views/js/oneSignalPush.js', array ( 'jquery' ), '', true);
    wp_enqueue_script( 'plugin-confPass', '/wp-content/plugins/TeleConnecteeAmu/views/js/confirmPass.js', array ( 'jquery' ), '', false);
}
add_action( 'wp_enqueue_scripts', 'wpdocs_plugin_teleconnecteeAmu_scripts' );

function manageStudent() {
    $current_user = wp_get_current_user();
    if(in_array('etudiant', $current_user->roles)) {
        $codes = unserialize($current_user->code);
        if(is_array($codes)) {
            $size = sizeof($codes);
        }
        if(empty($size)) {
            $model = new StudentManager();
            $years = $model->getCodeYear();
            $groups = $model->getCodeGroup();
            $halfgroups = $model->getCodeHalfgroup();
            selectSchedules($years, $groups, $halfgroups);
            $action = $_POST['addSchedules'];
            $year = filter_input(INPUT_POST, 'selectYears');
            $group = filter_input(INPUT_POST, 'selectGroups');
            $halfgroup = filter_input(INPUT_POST, 'selectHalfgroups');
            if($action) {
                $current_user = wp_get_current_user();
                $codes = [$year,$group,$halfgroup];
                $model->modifyStudent($current_user->ID, $codes);
            }
        }
    }
}

function selectSchedules($years, $groups, $halfgroups) {
    echo '
        <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title"> Choix des emplois du temps</h5>
              </div>
              <div class="modal-body">
              <form method="post">
                <select class="form-control firstSelect" name="selectYears" required="">
                <option value="0">Aucun</option>
                        <optgroup label="Année">';
    if(is_array($years)) {
        foreach ($years as $year) {
            echo '<option value="'.$year['code'].'">'.$year['title'].'</option >';
        }
    } else {
        echo '<option value="'.$years['code'].'">'.$years['title'].'</option >';
    }
    echo '</optgroup>
    </select>
    
                <select class="form-control firstSelect" name="selectGroups" required="">
                <option value="0">Aucun</option>
                    <optgroup label="Groupe">';
    if(is_array($groups)) {
        foreach ($groups as $group){
            echo '<option value="'.$group['code'].'">'.$group['title'].'</option>';
        }
    } else {
        echo '<option value="'.$groups['code'].'">'.$groups['title'].'</option>';
    }
    echo '</optgroup>
    </select>
    <select class="form-control firstSelect" name="selectHalfgroups" required="">
    <option value="0">Aucun</option>
          <optgroup label="Demi groupe">';
    if(is_array($halfgroups)) {
        foreach ($halfgroups as $halfgroup){
            echo '<option value="'.$halfgroup['code'].'">'.$halfgroup['title'].'</option>';
        }
    } else {
        echo '<option value="'.$halfgroups['code'].'">'.$halfgroups['title'].'</option>';
    }
    echo '</optgroup>
                </select>
                <input type="submit" name="addSchedules">
                </form>
                </div>
            </div>
          </div>
        </div>
        
        <script> $("#myModal").show() </script>';
}
add_action('check', 'manageStudent');