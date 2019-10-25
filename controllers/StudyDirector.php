<?php


class StudyDirector extends ControllerG {

    /**
     * Vue de StudyDirector
     * @var StudyDirectorView
     */
    private $view;

    /**
     * Modèle de StudyDirector
     * @var StudyDirectorModel
     */
    private $model;

    /**
     * Constructeur de StudyDirector
     */
    public function __construct(){
        $this->view = new StudyDirectorView();
        $this->model = new StudyDirectorModel();
    }

    /**
     * Ajoute un directeur d'étude
     */
    public function insertDirector() {
        $action = $_POST['createDirec'];
        $login = filter_input(INPUT_POST,'loginDirec');
        $pwd = filter_input(INPUT_POST,'pwdDirec');
        $pwdConf = filter_input(INPUT_POST, 'pwdConfirmDirec');
        $email = filter_input(INPUT_POST,'emailDirec');
        $code = [filter_input(INPUT_POST, 'codeDirec')];

        if(isset($action)){
            if($pwd == $pwdConf) {
                $pwd = wp_hash_password($pwd);
                if($this->model->insertDirector($login, $pwd, $email, $code)){
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
     * Affiche tous les directeurs d'études dans un tableau
     */
    public function displayAllStudyDirector(){
        $results = $this->model->getUsersByRole('directeuretude');
        if(isset($results)){
            $string = $this->view->displayTabHeadDirector();
            $row = 0;
            foreach ($results as $result){
                ++$row;
                $string .= $this->view->displayAllStudyDirector($result, $row);
            }
            $string .= $this->view->displayEndTab();
            return $string;
        } else {
            return $this->view->displayEmpty();
        }
    }

    /**
     * Modifie l'enseignant
     * @param $result   Données de l'enseignant avant modification
     */
    public function modifyStudyDirector($result){
        $page = get_page_by_title( 'Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $action = $_POST['modifValidate'];
        $code = [$_POST['modifCode']];
        $this->view->displayModifyStudyDirector($result);
        if($action === 'Valider'){
            if($this->model->modifyStudyDirector($result, $code)){
                $this->view->displayModificationValidate($linkManageUser);
            }
        }
    }
}