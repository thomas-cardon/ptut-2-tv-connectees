<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\Information;
use Models\User;

use Views\UserView;

use R34ICS;

/**
 * Class UserController
 *
 * Manage all users (Create, update, delete)
 *
 * @package Controllers
 */
class UserController extends Controller
{

    /**
     * @var User
     */
    private $model;

    /**
     * @var UserView
     */
    private $view;

    /**
     * UserController constructor.
     */
    public function __construct() {
        $this->model = new User();
        $this->view = new UserView();
    }

    /**
     * Displays user view content
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function displayContent() {
        return $this->view->displayContent();
    }

    /**
     * Delete an user
     *
     * @param $id   int
     */
    public function delete($id) {
        $user = $this->model->get($id);
        $userData = get_userdata($id);
        $user->delete();
        if (in_array("enseignant", $userData->roles) || in_array("secretaire", $userData->roles) ||
            in_array("administrator", $userData->roles) || in_array("directeuretude", $userData->roles)) {
            $modelAlert = new Alert();
            $alerts = $modelAlert->getAuthorListAlert($user->getLogin());
            foreach ($alerts as $alert) {
                $alert->delete();
            }
        }

        if (in_array("secretaire", $userData->roles) || in_array("administrator", $userData->roles) ||
            in_array("directeuretude", $userData->roles)) {
            $modelInfo = new Information();
            $infos = $modelInfo->getAuthorListInformation($user->getId());
            foreach ($infos as $info) {
                $goodType = ['img', 'pdf', 'tab', 'event'];
                if (in_array($info->getType(), $goodType)) {
                    $infoController = new InformationController();
                    $infoController->deleteFile($info->getId());
                }
                $modelInfo->delete();
            }
        }
    }

    /**
     * Modifies user's password, delete their account or modify their groups
     * @author Thomas Cardon
     * @return mixed|string
     */
    public function edit() {
        $current_user = wp_get_current_user();

        return $this->view->renderHeroHeader('Vos réglages', 'Changez votre mot de passe, vos groupes ou supprimez votre compte.', URL_PATH . TV_PLUG_PATH . 'public/img/settings.png')
        . $this->view->renderContainerDivider()
        . $this->view->renderContainer(
           (isset($_GET['message']) ? '<div class="alert alert-' . $_GET['message'] . '">' . $_GET['message_content'] . '</div>' : '') .
           $this->view->displayStartMultiSelect()
        .  $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true)
        .  $this->view->displayTitleSelect('generate', 'Générer un code de suppression')
        .  $this->view->displayTitleSelect('delete', 'Supprimer mon compte')
        .  $this->view->displayEndOfTitle()
        .  $this->view->displayContentSelect('pass', $this->modifyPwd(), true)
        .  $this->view->displayContentSelect('generate', $this->view->displayEnterCode(), false)
        .  $this->view->displayContentSelect('delete', $this->view->displayDeleteAccount())
        .  $this->view->displayEndDiv()
        . '<a role="button" class="btn btn-outline-secondary mt-5" href="/politique-de-confidentialite">Mention légales</a>'
        , '', 'container-sm px-4 pb-3 my-3 text-center');
    }

    /**
     * Modify the password of the user
     */
    public function modifyPwd() {
        $action = filter_input(INPUT_POST, 'modifyMyPwd');
        $current_user = wp_get_current_user();
        if (isset($action)) {
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($pwd, $current_user->user_pass)) {
                $newPwd = filter_input(INPUT_POST, 'newPwd');
                wp_set_password($newPwd, $current_user->ID);
                $this->view->displayModificationPassValidate();
            } else {
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayModifyPassword();
    }

    /**
     * Display schedule
     *
     * @param $code     int Code ADE of the schedule
     * @param $allDay   bool
     *
     * @return string|bool
     */
    public function displaySchedule($code, $allDay = false) {
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
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
        return $R34ICS->display_calendar($url, $code, $allDay, $args);
    }

    /**
     * Display the schedule link to the code in the url
     *
     * @return string
     */
    function displayYearSchedule() {
        $id = $this->getMyIdUrl();

        $codeAde = new CodeAde();

        if (is_numeric($id)) {
            $codeAde = $codeAde->get($id);
            if (!is_null($codeAde->getTitle()) && $codeAde->getType() === 'year') {
                return $this->displaySchedule($codeAde->getCode(), true);
            }
        }

        return $this->view->displaySelectSchedule();
    }

    /**
     * Check if a code Ade already exists with the same title or code
     *
     * @param User $newUser
     *
     * @return bool
     */
    public function checkDuplicateUser(User $newUser) {
        $codesAde = $this->model->checkUser($newUser->getLogin(), $newUser->getEmail());

        if (sizeof($codesAde) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Modify codes ade for the student
     *
     * @return string
     */
    public function modifyCodes() {
        $current_user = wp_get_current_user();
        $codeAde = new CodeAde();
        $this->model = $this->model->get($current_user->ID);

        $action = filter_input(INPUT_POST, 'modifvalider');

        if (isset($action)) {
            $year = filter_input(INPUT_POST, 'modifYear');
            $group = filter_input(INPUT_POST, 'modifGroup');
            $halfGroup = filter_input(INPUT_POST, 'modifHalfgroup');


            if (is_numeric($year) && is_numeric($group) && is_numeric($halfGroup)) {

                $codes = [$year, $group, $halfGroup];
                $codesAde = [];
                foreach ($codes as $code) {
                    if ($code !== 0) {
                        $code = $codeAde->getByCode($code);
                    }
                    $codesAde[] = $code;
                }

                if ($codesAde[0]->getType() !== 'year') {
                    $codesAde[0] = 0;
                }

                if ($codesAde[1]->getType() !== 'group') {
                    $codesAde[1] = 0;
                }

                if ($codesAde[2]->getType() !== 'halfGroup') {
                    $codesAde[2] = 0;
                }

                $this->model->setCodes($codesAde);

                if ($this->model->update()) {
                    $this->view->successMesageChangeCode();
                } else {
                    $this->view->errorMesageChangeCode();
                }
            }
        }

        $years = $codeAde->getAllFromType('year');
        $groups = $codeAde->getAllFromType('group');
        $halfGroups = $codeAde->getAllFromType('halfGroup');

        return $this->view->displayModifyMyCodes($this->model->getCodes(), $years, $groups, $halfGroups);
    }

    public function displayUsers() {
        return $this->view->displayUsers(User::find());
    }
}
