<?php

namespace Controllers;

use Models\User;
use Views\TeacherView;

/**
 * Class TeacherController
 *
 * Manage teacher (Create, update, delete, display)
 *
 * @package Controllers
 */
class TeacherController extends UserController implements Schedule
{

    /**
     * Modèle de TeacherController
     * @var User
     */
    private $model;

    /**
     * Vue de TeacherController
     * @var TeacherView
     */
    private $view;

    /**
     * Constructor of TeacherController
     */
    public function __construct() {
        parent::__construct();
        $this->model = new User();
        $this->view = new TeacherView();
    }

    /**
     * Displays the schedule of the teacher
     * @author Thomas Cardon
     */
    public function displayContent() {
        $current_user = wp_get_current_user();
        $user = $this->model->get($current_user->ID);
        $schedule = $this->displaySchedule($user->getCodes()[0]->getCode());

        if ($schedule) {
            return $schedule;
        } else {
            return $this->view->displayNoStudy();
        }
    }
    
    /**
     * Modify the teacher
     *
     * @param $user   User
     *
     * @return string
     */
    public function modify($user) {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);

        $action = filter_input(INPUT_POST, 'modifValidate');

        if ($action === 'Valider') {
            $code = filter_input(INPUT_POST, 'modifCode');
            if (is_numeric($code)) {
                $user->setRole('enseignant');
                $user->getCodes()[0]->setCode($code);

                if ($user->update()) {
                    $this->view->displayModificationValidate($linkManageUser);
                }
            }
        }

        return $this->view->modifyForm($user);
    }

    /**
     * Display all teachers in a table
     */
    public function displayTableTeachers() {
        $users = $this->model->getUsersByRole('enseignant');
        $users = $this->model->getMyCodes($users);
        return $this->view->displayTableTeachers($users);
    }
}
