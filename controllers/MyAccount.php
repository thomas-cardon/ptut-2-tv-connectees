<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 06/05/2019
 * Time: 08:58
 */

class MyAccount extends ControllerG {

    /**
     * Vue de MyAccount
     * @var ViewMyAccount
     */
    private $view;

    /**
     * Modèle de MyAccount
     * @var MyAccountManager
     */
    private $model;

    /**
     * Constructeur de MyAccount.
     */
    public function __construct(){
        $this->view = new ViewMyAccount();
        $this->model = new MyAccountManager();
    }

    public function chooseModif(){
        $current_user = wp_get_current_user();
        $string = $this->view->displayStartMultiSelect();
        if(in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayTitleSelect('code', 'Modifier mes codes', true).$this->view->displayTitleSelect('pass', 'Modifier mon mot de passe');
        } else {
            $string .= $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true);
        }
        $string .= $this->view->displayTitleSelect('delete', 'Supprimer mon compte').
            $this->view->displayEndOfTitle();
        if(in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayContentSelect('code', $this->modifyMyCodes(), true).
            $this->view->displayContentSelect('pass', $this->modifyPwd());
        } else {
            $string .= $this->view->displayContentSelect('pass', $this->modifyPwd(), true);
        }
        $string .=
          $this->view->displayContentSelect('delete', $this->deleteAccount()).$this->view->displayEndDiv();

        return $string;
    }

    /**
     * Modifie le mot de passe de l'utilisateur
     * s'il rentre son mot de passe actuel
     */
    public function modifyPwd(){

        $action = $_POST['modifyMyPwd'];
        $current_user = wp_get_current_user();
        if(isset($action)){
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if(wp_check_password($pwd, $current_user->user_pass)){
                $newPwd = filter_input(INPUT_POST, 'newPwd');
                wp_set_password( $newPwd, $current_user->ID);
                $this->view->displayModificationPassValidate();
            }
            else{
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayVerifyPassword().$this->view->displayModifyPassword();
    }

    /**
     * Modifie les codes de l'étudiant connecté
     * @param $result   Données de l'étudiant avant modification
     */
    public function modifyMyCodes(){
        //On récupère toutes les années, groupes et demi-groupes
        // pour pouvoir permettre à l'utilisateur de les sélectionner lors de la modification
        $current_user = wp_get_current_user();
        $years = $this->model->getCodeYear();
        $groups = $this->model->getCodeGroup();
        $halfgroups = $this->model->getCodeHalfgroup();
        $action = $_POST['modifvalider'];

        if($action == 'Valider'){
            $year = filter_input(INPUT_POST,'modifYear');
            $group = filter_input(INPUT_POST,'modifGroup');
            $halfgroup = filter_input(INPUT_POST,'modifHalfgroup');

            $codes = [$year, $group, $halfgroup];
            if($this->model->modifyMyCodes($current_user->ID, $current_user->user_login, $codes)){
                $this->view->displayModificationValidate();
            }
        }
        return $this->view->displayModifyMyCodes($current_user, $years, $groups, $halfgroups);
    }

    /**
     * Supprime le compte de l'utilisateur
     * si son mot de passe est correcte, on envoie par mail un code qui doit rentrer
     * et si le code qui rentre est correct
     */
    public function deleteAccount(){
        $action = $_POST['deleteMyAccount'];
        $actionDelete = $_POST['deleteAccount'];
        $current_user = wp_get_current_user();
        if(isset($action)){
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if(wp_check_password($pwd, $current_user->user_pass)) {
                $code = $this->model->createRandomCode($current_user->ID);

                //Le mail en html et utf8
                $to  = $current_user->user_email;
                $subject = "Désinscription à la télé-connecté";
                $message = '
                                 <html>
                                  <head>
                                   <title>Désnscription à la télé-connecté</title>
                                  </head>
                                  <body>
                                   <p>Bonjour, vous avez décidé de vous désinscrire sur le site de la Télé Connecté</p>
                                   <p> Votre code de désinscription est : '.$code.'.</p>
                                   <p> Pour vous désinscrire, rendez-vous sur le site : <a href="'.home_url().'/mon-compte/"> Tv Connectée.</p>
                                  </body>
                                 </html>
                                 ';

                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $to, $subject, $message, $headers );
                $this->view->displayMailSend();
            }
            else{
                $this->view->displayWrongPassword();
            }
        }
        elseif (isset($actionDelete)){
            $code = $_POST['codeDelete'];
            $userCode = $this->model->getCode($current_user->ID);
            if($code == $userCode[0]['Code']){
                $this->model->deleteCode($current_user->ID);
                $this->deleteUser($current_user->ID);
                $this->view->displayModificationValidate();
            } else {
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayVerifyPassword().$this->view->displayDeleteAccount().$this->view->displayEnterCode();
    }
}