<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 11:41
 */

class Television extends User implements Schedule {

    /**
     * View de Television
     * @var TelevisionView
     */
    private $view;

    /**
     * Model de Television
     * @var TelevisionModel
     */
    private $model;

    /**
     * Constructeur de Television
     */
    public function __construct(){
        $this->view = new TelevisionView();
        $this->model = new TelevisionModel();
    }

    public function displaySchedules() {
        $current_user = wp_get_current_user();
        $codes = unserialize($current_user->code); // On utilie cette fonction car les codes dans la base de données sont sérialisés

        if(is_array($codes)) {
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

    public function insertTelevision(){
        $action = $_POST['createTv'];
        $years = $this->model->getCodeYear();
        $groups = $this->model->getCodeGroup();
        $halfgroups = $this->model->getCodeHalfgroup();
        if(isset($action)){
            $login = filter_input(INPUT_POST,'loginTv');
            $pwd = filter_input(INPUT_POST,'pwdTv');
            $pwdConf = filter_input(INPUT_POST, 'pwdConfirmTv');
            $codes = $_POST['selectTv'];
            if($pwd == $pwdConf) {
                $pwd = wp_hash_password($pwd);
                if($this->model->insertMyTelevision($login, $pwd, $codes)){
                    $this->view->displayInsertValidate();
                }
                else{
                    $this->view->displayErrorLogin();
                }
            } else {
                $this->view->displayBadPassword();
            }
        }
        return $this->view->displayFormTelevision($years, $groups, $halfgroups);
    }

    public function displayAllTv(){
        $results = $this->model->getUsersByRole('television');
        if(isset($results)){
            $string = $this->view->displayHeaderTabTv();
            $row = 0;
            foreach ($results as $result){
                ++$row;
                $id = $result['ID'];
                $login = $result['user_login'];
                $codes = unserialize($result['code']);
                if(is_array($codes)) {
                    $nbCode = sizeof($codes);
                } else {
                    $nbCode = 1;
                }

                $string .= $this->view->displayAllTv($id, $login, $nbCode, $row);
            }
            $string .= $this->view->displayEndTab();
            return $string;
        }
        else {
            return $this->view->displayEmpty();
        }
    }

    public function modifyTv($result){
        $page = get_page_by_title( 'Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $years = $this->model->getCodeYear();
        $groups = $this->model->getCodeGroup();
        $halfgroups = $this->model->getCodeHalfgroup();

        $action = $_POST['modifValidate'];

        if(isset($action)){
            $codes = $_POST['selectTv'];
            $pwd = $result->user_pass;
            if(strlen($_POST['pwdTv']) >= 4){
                $newPwd = filter_input(INPUT_POST, 'pwdTv');
                $pwdConf = filter_input(INPUT_POST, 'pwdConfirmTv');
                if($newPwd != $pwdConf) {
                    $this->view->displayBadPassword();
                } else {
                    $pwd = $newPwd;
                }
            }
            if($this->model->modifyTv($result, $codes)){
                if($pwd != $result->user_pass) {
                    wp_set_password( $pwd, $result->ID);
                }
                $this->view->displayModificationValidate($linkManageUser);
            }
        }
        return $this->view->displayModifyTv($result, $years, $groups, $halfgroups);
    }
}