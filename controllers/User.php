<?php


class User extends ControllerG {

    /**
     * @var UserModel
     */
    private $model;

    /**
     * @var UserView
     */
    private $view;

    /**
     * User constructor.
     * @param UserModel $model
     * @param UserView $view
     */
    public function __construct()
    {
        $this->model = new UserModel();
        $this->view = new UserView();
    }

    /**
     * Supprime l'utilisateur, si c'est un enseignant, on supprime son fichier ICS et ses alertes
     * De même pour un secrétaire ou un administrateur, on supprime les alertes & informations postés par ces-derniers
     * @param $id   ID de la personne a supprimer
     */
    public function deleteUser($id) {
        $model = new StudentModel();
        $user = $model->getById($id);
        $data = get_userdata($id);
        $model->deleteUser($id);
        if(in_array("enseignant", $data->roles) == 'enseignant' ){
            $code = unserialize($user[0]['code']);
            unlink($this->getFilePath($code[0]));
        }
        if(in_array("enseignant", $data->roles) || in_array("secretaire", $data->roles) || in_array("administrator", $data->roles)){
            $modelAlert = new AlertManager();
            $modelInfo = new InformationManager();
            $alerts = $modelAlert->getListAlertByAuthor($user[0]['user_login']);
            if(isset($alerts)){
                foreach ($alerts as $alert) {
                    $modelAlert->deleteAlertDB($alert['ID_alert']);
                }
            }
            if(in_array("secretaire", $data->roles) || in_array("administrator", $data->roles)) {
                $infos = $modelInfo->getListInformationByAuthor($user[0]['user_login']);
                if(isset($infos)){
                    foreach ($infos as $info) {
                        $type = $info['type'];
                        if($type == "img" || $type == "") {
                            $this->deleteFile($info['ID_info']);
                        }
                        $modelInfo->deleteInformationDB($info['ID_info']);
                    }
                }
            }
        }
    }

    /**
     * Supprime le compte de l'utilisateur
     * si son mot de passe est correcte, on envoie par mail un code qui doit rentrer
     * et si le code qui rentre est correct
     */
    public function deleteAccount(){
        $action = $_POST['deleteMyAccount'];
        $actionDelete = $_POST['deleteAccount'];
        $current_user = wp_get_current_user();
        if(isset($action)){
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if(wp_check_password($pwd, $current_user->user_pass)) {
                $code = $this->model->createRandomCode($current_user->ID);

                //Le mail en html et utf8
                $to  = $current_user->user_email;
                $subject = "Désinscription à la télé-connecté";
                $message = '
                                 <html>
                                  <head>
                                   <title>Désnscription à la télé-connecté</title>
                                  </head>
                                  <body>
                                   <p>Bonjour, vous avez décidé de vous désinscrire sur le site de la Télé Connecté</p>
                                   <p> Votre code de désinscription est : '.$code.'.</p>
                                   <p> Pour vous désinscrire, rendez-vous sur le site : <a href="'.home_url().'/mon-compte/"> Tv Connectée.</p>
                                  </body>
                                 </html>
                                 ';

                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $subject, $message, $headers );
                $this->view->displayMailSend();
            }
            else{
                $this->view->displayWrongPassword();
            }
        }
        elseif (isset($actionDelete)){
            $code = $_POST['codeDelete'];
            $userCode = $this->model->getCode($current_user->ID);
            if($code == $userCode[0]['Code']){
                $this->model->deleteCode($current_user->ID);
                $this->deleteUser($current_user->ID);
                $this->view->displayModificationValidate();
            } else {
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayDeleteAccount().$this->view->displayEnterCode();
    }

    public function chooseModif(){
        $current_user = wp_get_current_user();
        $string = $this->view->displayStartMultiSelect();
        if(in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayTitleSelect('code', 'Modifier mes codes', true).$this->view->displayTitleSelect('pass', 'Modifier mon mot de passe');
        } else {
            $string .= $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true);
        }
        $string .= $this->view->displayTitleSelect('delete', 'Supprimer mon compte').
            $this->view->displayEndOfTitle();
        if(in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayContentSelect('code', $this->modifyMyCodes(), true).
                $this->view->displayContentSelect('pass', $this->modifyPwd());
        } else {
            $string .= $this->view->displayContentSelect('pass', $this->modifyPwd(), true);
        }
        $string .=
            $this->view->displayContentSelect('delete', $this->deleteAccount()).$this->view->displayEndDiv();

        return $string;
    }

    /**
     * Modifie les codes de l'étudiant connecté
     * @param $result   array Données de l'étudiant avant modification
     */
    public function modifyMyCodes(){
        //On récupère toutes les années, groupes et demi-groupes
        // pour pouvoir permettre à l'utilisateur de les sélectionner lors de la modification
        $current_user = wp_get_current_user();
        $years = $this->model->getCodeYear();
        $groups = $this->model->getCodeGroup();
        $halfgroups = $this->model->getCodeHalfgroup();
        $action = $_POST['modifvalider'];
        if($action == 'Valider'){
            $year = filter_input(INPUT_POST,'modifYear');
            $group = filter_input(INPUT_POST,'modifGroup');
            $halfgroup = filter_input(INPUT_POST,'modifHalfgroup');
            $codes = [$year, $group, $halfgroup];
            if($this->model->modifyMyCodes($current_user->ID, $current_user->user_login, $codes)){
                $this->view->displayModificationValidate();
            }
        }
        return $this->view->displayModifyMyCodes($current_user, $years, $groups, $halfgroups);
    }

    /**
     * Modifie le mot de passe de l'utilisateur
     * s'il rentre son mot de passe actuel
     */
    public function modifyPwd(){
        $action = $_POST['modifyMyPwd'];
        $current_user = wp_get_current_user();
        if(isset($action)){
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if(wp_check_password($pwd, $current_user->user_pass)){
                $newPwd = filter_input(INPUT_POST, 'newPwd');
                wp_set_password( $newPwd, $current_user->ID);
                $this->view->displayModificationPassValidate();
            }
            else{
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayModifyPassword();
    }

    /**
     * Affiche l'emploi du temps demandé
     * @param $code     int Code ADE de l'emploi du temps
     */
    public function displaySchedule($code){
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
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
    function displayYearSchedule(){
        $code = $this->getMyIdUrl(); // On récupère l'ID qui sert de code ADE
        if(! is_numeric($code)) {
            return $this->view->displaySelectSchedule();
        } else {
            $path = $this->getFilePath($code);
            if(! file_exists($path) || filesize($path) <= 0){
                $this->addFile($code);
            }
            return $this->displaySchedule($code);
        }
    }

    /**
     * Affiche un titre
     * @param $title    Titre à afficher
     */
    public function displayName($title) {
        echo '<h1>'.$title.'</h1>';
    }

    /**
     * Début du diaporama
     */
    public function displayStartSlide(){
        echo '
            <div class="slideshow-container">
                <div class="mySlides">';
    }

    /**
     * Milieu du dipao, on l'utilise une fois par objet à afficher
     */
    public function displayMidSlide(){
        echo '
                </div>
              <div class="mySlides">';
    }

    /**
     * Fin du diaporama
     */
    public function displayEndSlide() {
        echo '          
                       </div>
                   </div>';
    }

    /**
     * Signal qu'il n'y pas cours
     */
    public function displayEmptySchedule(){
        echo '<p> Vous n\'avez pas cours !</p>';
    }

    /**
     * Souhaite la bienvenue à l'utilisateur
     */
    public function displayWelcome(){
        echo '<h1>Écran connecté</h1>';
    }

    /**
     * Souhaite la bienvenue à l'utilisateur
     */
    public function displayWelcomeAdmin(){
        echo '<h1>Écran connecté</h1>
                <form method="post" id="dlAllEDT">
                    <input type="submit" name="dlEDT" value="Retélécharger les emplois du temps">
                </form>';
    }

    /**
     * Demande à la persone de choisir son emploi du temps
     */
    public function displaySelectSchedule(){
        echo '<p> Veuillez sélectionner un emploi du temps </p>';
    }

    /*
    public function displayYearSchedule(){
        $code = $this->getMyIdUrl(); // On récupère l'ID qui sert de code ADE
        if($code == 'emploi-du-temps') {
            return $this->view->displaySelectSchedule();
        } else {
            $path = $this->getFilePath($code);
            if(! file_exists($path) || filesize($path) <= 0){
                $this->addFile($code);
            }
            return $this->displaySchedule($code);
        }
    }

    public function displaySchedules() {
    $current_user = wp_get_current_user();
        $codes = unserialize($current_user->code); // On utilie cette fonction car les codes dans la base de données sont sérialisés

        if(in_array("enseignant",$current_user->roles)) {
            $this->displaySchedule($codes[0]); // On affiche le codes[0] car les enseignants n'ont qu'un code
        }

        if(in_array("etudiant",$current_user->roles)) {
            if(file_exists($this->getFilePath($codes[2]))) {
                $this->displaySchedule($codes[2]);
            } else if(file_exists($this->getFilePath($codes[1]))) {
                $this->displaySchedule($codes[1]);
            } else if($this->displaySchedule($codes[0])) {
                $this->displaySchedule($codes[0]);
            } else {
                echo "T'as pas cours gros";
            }
        }

        if(in_array("television",$current_user->roles)) {
            $this->view->displayStartSlide();
            foreach ($codes as $code) {
                $path = $this->getFilePath($code);
                if(file_exists($path)){
                    $this->displaySchedule($code);
                    $this->view->displayMidSlide();
                }
            }
            $this->view->displayEndSlide();
        }

        if (in_array("technicien", $current_user->roles)){
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
        }

        if(in_array("administrator", $current_user->roles) || in_array("secretary", $current_user->roles)) {
            $this->view->displayWelcomeAdmin();
        }
     */
}