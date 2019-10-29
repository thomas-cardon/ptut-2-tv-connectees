<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 11:37
 */

/**
 * Permet de gérer la création et l'affichage des secrétaires
 * Class Secretary
 */
class Secretary extends User {

    /**
     * Vue de Secretary
     * @var SecretaryView
     */
    private $view;

    /**
     * Modèle de Secretary
     * @var SecretaryModel
     */
    private $model;

    /**
     * Constructeur de Secretary.
     */
    public function __construct(){

        $this->view = new SecretaryView();
        $this->model = new SecretaryModel();
    }

    public function displaySchedules() {
        $this->view->displayWelcomeAdmin();
    }

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
                return $controller->modifyMyStudent($user);
            } elseif (in_array("enseignant",$user->roles)){
                $controller = new Teacher();
                return $controller->modifyTeacher($user);
            } elseif (in_array("directeuretude", $user->roles)) {
                $controller = new StudyDirector();
                return $controller->modifyStudyDirector($user);
            } elseif (in_array("television",$user->roles)){
                $controller = new Television();
                return $controller->modifyTv($user);
            } else {
                return $this->view->displaynoUser();
            }
        } else {
            return $this->view->displaynoUser();
        }
    }

    /**
     * Supprime tout les utilisateurs sélectionnés via des checkboxs
     */
    public function deleteUsers() {
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
     * Ajoute un secrétaire si le login et l'adresse mail ne sont pas déjà enregistrés
     */
    public function insertSecretary(){
        //$this->view->displayFormSecretary();

        $action = $_POST['createSecre'];
        $login = filter_input(INPUT_POST,'loginSecre');
        $pwd = filter_input(INPUT_POST,'pwdSecre');
        $pwdConf = filter_input(INPUT_POST, 'pwdConfirmSecre');
        $email = filter_input(INPUT_POST,'emailSecre');

        if(isset($action)){
            if($pwd == $pwdConf) {
                $pwd = wp_hash_password($pwd);
                if($this->model->insertMySecretary($login, $pwd, $email)){
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayBadPassword();
            }
        }
        return $this->view->displayFormSecretary();
    }

    /**
     * Affiche tous les secrétaires dans un tableau où ils peuvent être supprimés via des checkbox
     * @return string
     */
    public function displayAllSecretary(){
        $results = $this->model->getUsersByRole('secretaire');
        if(isset($results)){
            $string = $this->view->displayHeaderTabSecretary();
            $row = 0;
            foreach ($results as $result){
                ++$row;
                $string .= $this->view->displayAllSecretary($row, $result['ID'], $result['user_login']);
            }
            $string .= $this->view->displayEndTab();
            return $string;
        }
        else{
            return $this->view->displayEmpty();
        }
    }
}