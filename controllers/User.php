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
     * @param $model
     * @param $view
     */
    public function __construct($model = null, $view = null) {
        if($model == null && $view == null) {
            $model = new UserModel();
            $view = new UserView();
        }
        $this->model = $model;
        $this->view = $view;
    }

    /**
     * Supprime tout les utilisateurs sélectionnés via des checkboxs
     */
    public function deleteUsers(){
        $actionDelete = $_POST['Delete'];
        $roles = ['etu','teacher','direc','tech','secre','tele'];
        if(isset($actionDelete)){
            foreach ($roles as $role) {
                if(isset($_REQUEST['checkboxstatus'.$role])) {
                    $checked_values = $_REQUEST['checkboxstatus'.$role];
                    foreach($checked_values as $val) {
                        $this->deleteUser($val);
                    }
                }
            }
        }
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
        return $this->view->displayVerifyPassword().$this->view->displayDeleteAccount().$this->view->displayEnterCode();
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
        return $this->view->displayVerifyPassword().$this->view->displayModifyPassword();
    }

    /**
     * @return string
     */
    public function createUsers() {
        $student = new Student();
        $teacher = new Teacher();
        $studyDirector = new StudyDirector();
        $secretary = new Secretary();
        $technician = new Technician();
        $television = new Television();
        return
            $this->view->displayStartMultiSelect().
            $this->view->displayTitleSelect('student', 'Étudiants', true).
            $this->view->displayTitleSelect('teacher', 'Enseignants').
            $this->view->displayTitleSelect('studyDirector', 'Directeurs d\'études').
            $this->view->displayTitleSelect('secretary', 'Secrétaires').
            $this->view->displayTitleSelect('technician', 'Technicien').
            $this->view->displayTitleSelect('television', 'Télévisions').
            $this->view->displayEndOfTitle().
            $this->view->displayContentSelect('student', $student->insertStudent(), true).
            $this->view->displayContentSelect('teacher', $teacher->insertTeacher()).
            $this->view->displayContentSelect('studyDirector', $studyDirector->insertDirector()).
            $this->view->displayContentSelect('secretary', $secretary->insertSecretary()).
            $this->view->displayContentSelect('technician', $technician->insertTechnician()).
            $this->view->displayContentSelect('television', $television->insertTelevision()).
            $this->view->displayEndDiv();
    }

    /**
     * Affiche les utilisateurs choisis dans un tableau
     */
    public function displayUsers(){
        $student = new Student();
        $teacher = new Teacher();
        $studyDirector = new StudyDirector();
        $secretary = new Secretary();
        $technician = new Technician();
        $television = new Television();
        return
            $this->view->displayStartMultiSelect().
            $this->view->displayTitleSelect('student', 'Étudiants', true).
            $this->view->displayTitleSelect('teacher', 'Enseignants').
            $this->view->displayTitleSelect('studyDirector', 'Directeurs d\'études').
            $this->view->displayTitleSelect('secretary', 'Secrétaires').
            $this->view->displayTitleSelect('technician', 'Technicien').
            $this->view->displayTitleSelect('television', 'Télévisions').
            $this->view->displayEndOfTitle().
            $this->view->displayContentSelect('student', $student->displayAllStudents(), true).
            $this->view->displayContentSelect('teacher', $teacher->displayAllTeachers()).
            $this->view->displayContentSelect('studyDirector', $studyDirector->displayAllStudyDirector()).
            $this->view->displayContentSelect('secretary', $secretary->displayAllSecretary()).
            $this->view->displayContentSelect('technician', $technician->displayAllTechnician()).
            $this->view->displayContentSelect('television', $television->displayAllTv()).
            $this->view->displayEndDiv();
    }

    /**
     * Modifie l'utilisateur choisi
     */
    public function modifyUser(){
        if(is_numeric($this->getMyIdUrl())) {
            $user = get_user_by( 'id', $this->getMyIdUrl() );
            if(in_array("etudiant",$user->roles)){
                $controller = new Student();
                $controller->modifyMyStudent($user);
            } elseif (in_array("enseignant",$user->roles)){
                $controller = new Teacher();
                $controller->modifyTeacher($user);
            } elseif (in_array("directeuretude", $user->roles)) {
                $controller = new StudyDirector();
                $controller->modifyStudyDirector($user);
            } elseif (in_array("television",$user->roles)){
                $controller = new Television();
                $controller->modifyTv($user);
            } else {
                $this->view->displaynoUser();
            }
        } else {
            $this->view->displaynoUser();
        }
    }
}