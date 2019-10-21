<?php
/**
 * Created by PhpStorm.
 * User: r17000292
 * Date: 06/02/2019
 * Time: 17:23
 */

/**
 * Permet de gérer les emplois du temps,
 * C'est ici qu'on appel le controlleur R34ICS
 * Class Schedule
 */
class Schedule extends ControllerG
{
    /**
     * Vue de Schedule
     * @var ViewSchedule
     */
    private $view;

    /**
     * Constructeur de Schedule.
     */
    public function __construct(){
        $this->view = new ViewSchedule();
    }

    /**
     * Affiche l'emploi du temps demandé
     * @param $code     Code ADE de l'emploi du temps
     */
    public function displaySchedule($code){
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = ABSPATH."/wp-content/plugins/TeleConnecteeAmu/controllers/fileICS/".$code;
        // On demande d'afficher l'emploi du temps en liste, les autres arguments ne servent à rien pour nous
        $args = array(
            'count' => 10,
            'description' => null,
            'eventdesc' => null,
            'format' => null,
            'hidetimes' => null,
            'showendtimes' => null,
            'title' => null,
            'view' => 'list',
        );
        $R34ICS->display_calendar($url, $code, $args);
    }

    /**
     * Affiche l'emploi du temps d'une année en fonction de l'ID récupéré dans l'url
     */
    public function displayYearSchedule(){
        $code = $this->getMyIdUrl(); // On récupère l'ID qui sert de code ADE
        if($code == 'emploi-du-temps') {
            $this->view->displaySelectSchedule();
        } else {
            $path = $this->getFilePath($code);
            if(! file_exists($path) || filesize($path) <= 0){
                $this->addFile($code);
            }
            return $this->displaySchedule($code);
        }
    }

    /**
     * Affiche l'emploi du temps de la personne connectée,
     * Si cette personne n'a pas d'emploi du temps, on lui souhaite la bienvenue sur le site
     * @throws Exception
     */
    public function displaySchedules(){
        echo 'test';
        $current_user = wp_get_current_user();
        //test pour admin
        if(in_array("administrator", $current_user->roles)) {

        }
        if (in_array("television",$current_user->roles) || in_array("etudiant",$current_user->roles) || in_array("enseignant",$current_user->roles)) {

            if(isset($current_user->code)) {
                $codes = unserialize($current_user->code); // On utilie cette fonction car les codes dans la base de données sont sérialisés
                if(in_array('etudiant', $current_user->roles) && sizeof($codes) <= 0) {
                    exit();
                }

                $codeNulls = array_keys($codes, '0');
                if(isset($codeNulls)) {
                    foreach ($codeNulls as $codeNull) {
                        unset($codes[$codeNull]);
                    }
                }

                if(! sizeof($codes) <= 0) {

                    // Si l'emploi du temps n'existe pas, on le télécharge
                    if(is_array($codes)) {
                        foreach ($codes as $code) {
                            $path = $this->getFilePath($code);
                            if(! file_exists($path) || filesize($path) <= 0){
                                $this->addFile($code);
                            }
                        }
                    } else {
                        $path = $this->getFilePath($codes);
                        if(! file_exists($path) || file_get_contents($path) == ''){
                            $this->addFile($codes);
                        }
                    }


                    if(in_array("enseignant",$current_user->roles)) {
                        $this->displaySchedule($codes[0]); // On affiche le codes[0] car les enseignants n'ont qu'un code
                    }

                    elseif(in_array("etudiant",$current_user->roles)) {
                        if(is_array($codes)){
                            $this->displaySchedule(end($codes)); // On prend le dernier code car il s'agit du demi-groupe de l'étudiant
                        } else {
                            $this->displaySchedule($codes);
                        }
                    }

                    elseif(in_array("television",$current_user->roles)){
                        if(is_array($codes)){
                            $this->view->displayStartSlide();
                            foreach ($codes as $code) {
                                $path = $this->getFilePath($code);
                                if(file_exists($path)){
                                    $this->displaySchedule($code);
                                    $this->view->displayMidSlide();
                                }
                            }
                            $this->view->displayEndSlide();
                        } else {
                            $this->displaySchedule($codes);
                        }
                    }

                } elseif (in_array("technicien", $current_user->roles)){
                    $model = new CodeAdeManager();
                    $years = $model->getCodeYear();
                    $row = 0;
                    foreach ($years as $year){
                        if($row % 2 == 0) {
                            $this->view->displayRow();
                        }
                        $this->displaySchedule($year['code']);
                        if($row % 2 == 1) {
                            $this->view->displayEndDiv();
                        }
                        $row = $row + 1;
                    }
                } else {
                    $this->view->displayWelcome();
                }
            }
        } else if(in_array("administrator", $current_user->roles) || in_array("secretary", $current_user->roles)) {
            $this->view->displayWelcomeAdmin();
        } else {
            $this->view->displayWelcome(); // Si l'utilisateur n'est pas connecté ou s'il s'agit d'un administrateur ou d'un secrétaire
        }
    }
}
