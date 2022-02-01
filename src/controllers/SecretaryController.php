<?php

namespace Controllers;

use Models\User;
use Views\SecretaryView;

/**
 * Class SecretaryController
 *
 * All actions for secretary (Create, update, display)
 *
 * @package Controllers
 */
class SecretaryController extends UserController
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var SecretaryView
     */
    private $view;

    /**
     * Constructor of SecretaryController.
     */
    public function __construct() {
        parent::__construct();

        $this->model = new User();
        $this->view = new SecretaryView();
    }

    /**
     * Displays view content
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function displayContent() {
        return $this->view->displayContent();
    }

    /**
     * Insert a secretary in the database
     */
    public function insert() {
        $action = filter_input(INPUT_POST, 'createSecre');

        if (isset($action)) {

            $login = filter_input(INPUT_POST, 'loginSecre');
            $password = filter_input(INPUT_POST, 'pwdSecre');
            $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmSecre');
            $email = filter_input(INPUT_POST, 'emailSecre');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
                $password === $passwordConfirm && is_email($email)) {

                $this->model->setLogin($login);
                $this->model->setPassword($password);
                $this->model->setEmail($email);
                $this->model->setRole('secretaire');

                if (!$this->checkDuplicateUser($this->model) && $this->model->insert()) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayErrorCreation();
            }
        }
        return $this->view->displayFormSecretary();
    }

    /**
     * Display all secretary
     * @return string
     */
    public function displayTableSecretary() {
        $users = $this->model->getUsersByRole('secretaire');
        return $this->view->displayTableSecretary($users);
    }

    public function displayUserCreationView() {
      $teacher = new TeacherController();
      $studyDirector = new StudyDirectorController();
      $secretary = new SecretaryController();
      $technician = new TechnicianController();
      $television = new TelevisionController();

      return $this->view->getHeader('Création des utilisateurs', '
      Il y a plusieurs types d\'utilisateurs :
      <br />
      Les <s>étudiants</s>, enseignants, directeurs d\'études, secrétaires, techniciens, télévisions.

      <br /> <br />
      Les <b>étudiants</b> ont accès à leur emploi du temps et reçoivent les alertes les concernants et les informations. <br />
      Les <b>enseignants</b> ont accès à leur emploi du temps et peuvent poster des alertes. <br />
      Les <b>directeurs d\'études</b> ont accès à leur emploi du temps et peuvent poster des alertes et des informations. <br />
      Les <b>secrétaires</b> peuvent poster des alertes et des informations. Ils peuvent aussi créer des utilisateurs. <br />
      Les <b>techniciens</b> ont accès aux emplois du temps des promotions. <br />
      Les <b>télévisions</b> sont les utilisateurs utilisés pour afficher ce site sur les téléviseurs. Les comptes télévisions peuvent afficher autant d\'emploi du temps que souhaité.
  ', URL_PATH . TV_PLUG_PATH . 'public/img/gestion.png') . '' . $this->view->renderContainerDivider() . '' . $this->view->renderContainer(
          $this->view->displayStartMultiSelect()
        . $this->view->displayTitleSelect('form', 'Par formulaire', true)
        . $this->view->displayTitleSelect('excel', 'Par fichier Excel (CSV)')
        . $this->view->displayEndOfTitle()
        . $this->view->displayContentSelect('form', $this->view->displayUserCreationForm(), true)
        . $this->view->displayContentSelect('excel', $this->view->displayUserCreationFormExcel())
        . $this->view->displayEndDiv()
      );
    }

    /**
     * Displays users by roles
     */
    public function displayUsers() {
        $teacher = new TeacherController();
        $studyDirector = new StudyDirectorController();
        $secretary = new SecretaryController();
        $technician = new TechnicianController();
        $television = new TelevisionController();
        $user = new UserController();

        return $this->view->getHeader('Liste des utilisateurs', '
        Il y a plusieurs types d\'utilisateurs :
        <br />
        Les <s>étudiants</s>, enseignants, directeurs d\'études, secrétaires, techniciens, télévisions.

        <br /> <br />
        Les <b>étudiants</b> ont accès à leur emploi du temps et reçoivent les alertes les concernants et les informations. <br />
        Les <b>enseignants</b> ont accès à leur emploi du temps et peuvent poster des alertes. <br />
        Les <b>directeurs d\'études</b> ont accès à leur emploi du temps et peuvent poster des alertes et des informations. <br />
        Les <b>secrétaires</b> peuvent poster des alertes et des informations. Ils peuvent aussi créer des utilisateurs. <br />
        Les <b>techniciens</b> ont accès aux emplois du temps des promotions. <br />
        Les <b>télévisions</b> sont les utilisateurs utilisés pour afficher ce site sur les téléviseurs. Les comptes télévisions peuvent afficher autant d\'emploi du temps que souhaité.
    ', URL_PATH . TV_PLUG_PATH . 'public/img/gestion.png') . '' . $this->view->renderContainerDivider() . '' . $this->view->renderContainer(
              $this->view->displayStartMultiSelect() .
              $this->view->displayTitleSelect('teacher', 'Enseignants', true) .
              $this->view->displayTitleSelect('studyDirector', 'Directeurs d\'études') .
              $this->view->displayTitleSelect('secretary', 'Secrétaires') .
              $this->view->displayTitleSelect('technician', 'Technicien') .
              $this->view->displayTitleSelect('television', 'Télévisions') .
              $this->view->displayTitleSelect('all', 'Tous les utilisateurs') .
              $this->view->displayEndOfTitle() .
              $this->view->displayContentSelect('teacher', $teacher->displayTableTeachers(), true) .
              $this->view->displayContentSelect('studyDirector', $studyDirector->displayTableStudyDirector()) .
              $this->view->displayContentSelect('secretary', $secretary->displayTableSecretary()) .
              $this->view->displayContentSelect('technician', $technician->displayTableTechnician()) .
              $this->view->displayContentSelect('television', $television->displayTableTv()) .
              $this->view->displayContentSelect('all', $user->displayUsers()) .
              $this->view->displayEndDiv(), '', 'container-sm px-4 pb-5 my-5 text-center'
            );
    }

    /**
     * Modify an user
     */
    public function modifyUser() {
        $id = $_GET['id'];
        if (is_numeric($id) && $this->model->get($id)) {
            $user = $this->model->get($id);

            $wordpressUser = get_user_by('id', $id);

            if (in_array("enseignant", $wordpressUser->roles)) {
                $controller = new TeacherController();
                return $controller->modify($user);
            } elseif (in_array("directeuretude", $wordpressUser->roles)) {
                $controller = new StudyDirectorController();
                return $controller->modify($user);
            } elseif (in_array("television", $wordpressUser->roles)) {
                $controller = new TelevisionController();
                return $controller->modify($user);
            } else {
                return $this->view->displayNoUser();
            }
        } else {
            return $this->view->displayNoUser();
        }
    }

    /**
     * Delete users
     */
    public function deleteUsers() {
        $actionDelete = filter_input(INPUT_POST, 'delete');
        $roles = ['Etu', 'Teacher', 'Direc', 'Tech', 'Secre', 'Tele'];
        if (isset($actionDelete)) {
            foreach ($roles as $role) {
                if (isset($_REQUEST['checkboxStatus' . $role])) {
                    $checked_values = $_REQUEST['checkboxStatus' . $role];
                    foreach ($checked_values as $id) {
                        $this->deleteUser($id);
                    }
                }
            }
        }
    }

    /**
     * Delete an user
     *
     * @param $id
     */
    private function deleteUser($id) {
        $user = $this->model->get($id);
        $user->delete();
    }
}
