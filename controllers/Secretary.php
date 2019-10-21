<?php
/**
 * Created by PhpStorm.
 * User: Rohrb
 * Date: 25/04/2019
 * Time: 11:37
 */

/**
 * Permet de gérer la création et l'affichage des secrétaires
 * Class Secretary
 */
class Secretary{

    /**
     * Vue de Secretary
     * @var ViewSecretary
     */
    private $view;

    /**
     * Modèle de Secretary
     * @var SecretaryManager
     */
    private $model;

    /**
     * Constructeur de Secretary.
     */
    public function __construct(){
        $this->view = new ViewSecretary();
        $this->model = new SecretaryManager();
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