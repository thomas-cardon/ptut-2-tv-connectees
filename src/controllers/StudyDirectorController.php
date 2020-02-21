<?php

namespace Controllers;


use Models\StudyDirector;
use Models\User;
use Views\StudyDirectorView;
use WP_User;

class StudyDirectorController extends UserController implements Schedule
{

	/**
	 * @var User
	 */
	private $model;

    /**
     * @var StudyDirectorView
     */
    private $view;

    /**
     * Constructor of StudyDirectorController
     */
    public function __construct()
    {
	    parent::__construct();
	    $this->model = new User();
        $this->view = new StudyDirectorView();
    }

    public function displayMySchedule()
    {
        $current_user = wp_get_current_user();
        $codes = unserialize($current_user->code);
        if($this->displaySchedule($codes[0])) {
            return $this->displaySchedule($codes[0]);
        } else {
            return $this->view->displayNoStudy();
        }
    }

    /**
     * Insert a study director in the database
     */
    public function insert()
    {
        $action = filter_input(INPUT_POST, 'createDirec');

        if (isset($action)) {

	        $login = filter_input(INPUT_POST, 'loginDirec');
	        $password = filter_input(INPUT_POST, 'pwdDirec');
	        $passwordConfirm = filter_input(INPUT_POST, 'pwdConfirmDirec');
	        $email = filter_input(INPUT_POST, 'emailDirec');
	        $code = filter_input(INPUT_POST, 'codeDirec');

            if (is_string($login) && strlen($login) >= 4 && strlen($login) <= 25 &&
                is_string($password) && strlen($password) >= 8 && strlen($password) <= 25 &&
            	$password === $passwordConfirm && is_email($email)) {

	            $password = wp_hash_password($password);

	            $this->model->setLogin($login);
	            $this->model->setPassword($password);
	            $this->model->setEmail($email);
	            $this->model->setRole('directeuretude');
	            $this->model->setCodes($code);

                if ($this->model->create()) {
	                $path = $this->getFilePath($code);
	                if (!file_exists($path)) {
		                $this->addFile($code);
	                }
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayBadPassword();
            }
        }
        return $this->view->displayCreateDirector();
    }

    /**
     * Display all study directors
     */
    public function displayAllStudyDirector()
    {
        $users = $this->model->getUsersByRole('directeuretude');
        return $this->view->displayAllStudyDirector($users);
    }

    /**
     * Modify the study director
     *
     * @param $user   User
     *
     * @return string
     */
    public function modify($user)
    {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
	        $code = filter_input(INPUT_POST, 'modifCode');
	        if(is_numeric($code)) {
	        	$user->setRole('directeuretude');
		        $user->getCodes()[0]->setCode($code);

		        if ($user->update()) {
			        $this->view->displayModificationValidate($linkManageUser);
		        }
	        }
        }

        return $this->view->displayModifyStudyDirector($user);
    }
}