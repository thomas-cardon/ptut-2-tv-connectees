<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 26/04/2019
 * Time: 10:09
 */

class ManagementUsers extends ControllerG{

    /**
     * Vue de ManagementUser
     * @var ViewManagementUsers
     */
    private $view;

    /**
     * Constructeur de ManagementUsers.
     */
    public function __construct(){
        $this->view = new ViewManagementUsers();
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