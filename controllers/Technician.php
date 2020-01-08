<?php


/**
 * Permet de créer et afficher des techniciens
 * Class Technician
 */
class Technician extends User implements Schedule
{
    /**
     * Vue de Technician
     * @var TechnicianView
     */
    private $view;

    /**
     * Modèle de Technician
     * @var TechnicianModel
     */
    private $model;

    /**
     * Constructeur de Secretary.
     */
    public function __construct()
    {
        $this->view = new TechnicianView();
        $this->model = new TechnicianModel();
    }

    public function displayMySchedule()
    {
        $modelCode = new CodeAdeManager();
        $years = $modelCode->getCodeYear();
        $row = 0;
        $string = "";
        foreach ($years as $year) {
            if ($row % 2 == 0) {
                $string .= $this->view->displayRow();
            }
            if($this->displaySchedule($year['code'])) {
	            $string .= $this->view->displayDivSchedule();
                $string .= $this->displaySchedule($year['code']);
	            $string .= $this->view->displayEndDiv();
            } else {
                $string .= $this->view->displayNoStudy();
            }

            if ($row % 2 == 1) {
                $string .= $this->view->displayEndDiv();
            }
            $row = $row + 1;
        }
        return $string;
    }

    /**
     * Ajoute un technicien via un formulaire
     */
    public function insertTechnician()
    {
        //$this->view->displayFormTechnician();
        $action = $_POST['createTech'];
        $login = filter_input(INPUT_POST, 'loginTech');
        $pwd = filter_input(INPUT_POST, 'pwdTech');
        $pwdConf = filter_input(INPUT_POST, 'pwdConfirmTech');
        $email = filter_input(INPUT_POST, 'emailTech');

        if (isset($action)) {
            if ($pwd == $pwdConf) {
                $pwd = wp_hash_password($pwd);
                if ($this->model->insertMyTechnician($login, $pwd, $email)) {
                    $this->view->displayInsertValidate();
                } else {
                    $this->view->displayErrorInsertion();
                }
            } else {
                $this->view->displayBadPassword();
            }
        }
        return $this->view->displayFormTechnician();
    }

    /**
     * Affiche tous les techniciens dans un tableau
     * @return string   Renvoie le tableau avec les techniciens
     */
    public function displayAllTechnician()
    {
        $results = $this->model->getUsersByRole('technicien');
        if (isset($results)) {
            $string = $this->view->displayHeaderTabTechnician();
            $row = 0;
            foreach ($results as $result) {
                ++$row;
                $string .= $this->view->displayAllTechnicians($row, $result['ID'], $result['user_login']);
            }
            $string .= $this->view->displayEndTab();
            return $string;
        } else {
            return $this->view->displayEmpty();
        }
    }
}