<?php


class User extends ControllerG
{

    /**
     * @var UserModel
     */
    private $model;

    /**
     * @var UserView
     */
    private $view;

    /**
     * AdminModel constructor.
     */
    public function __construct()
    {
        $this->model = new UserModel();
        $this->view = new UserView();
    }

    /**
     * Supprime l'utilisateur, si c'est un enseignant, on supprime son fichier ICS et ses alertes
     * De même pour un secrétaire ou un administrateur, on supprime les alertes & informations postés par ces-derniers
     * @param $id   int ID de la personne a supprimer
     */
    public function deleteUser($id)
    {
        $user = $this->model->getById($id);
        $data = get_userdata($id);
        $this->model->deleteUser($id);
        if (in_array("enseignant", $data->roles)) {
            $code = unserialize($user[0]['code']);
            if(file_exists($this->getFilePath($code[0]))) {
                unlink($this->getFilePath($code[0]));
            }
        }
        if (in_array("enseignant", $data->roles) || in_array("secretaire", $data->roles) || in_array("administrator", $data->roles)) {
            $modelAlert = new AlertManager();
            $modelInfo = new InformationManager();
            $alerts = $modelAlert->getListAlertByAuthor($user[0]['user_login']);
            if (isset($alerts)) {
                foreach ($alerts as $alert) {
                    $modelAlert->deleteAlertDB($alert['ID_alert']);
                }
            }
            if (in_array("secretaire", $data->roles) || in_array("administrator", $data->roles)) {
                $infos = $modelInfo->getListInformationByAuthor($user[0]['user_login']);
                if (isset($infos)) {
                    foreach ($infos as $info) {
                        $type = $info['type'];
                        if ($type == "img" || $type == "") {
                            $info = new Information();
                            $info->deleteFile($info['ID_info']);
                        }
                        $modelInfo->deleteInformationDB($info['ID_info']);
                    }
                }
            }
        }
    }

    /**
     * Supprime le compte de l'utilisateur
     * si son mot de passe est correcte, on envoie par mail un code qui doit rentrer
     * et si le code qui rentre est correct
     */
    public function deleteAccount()
    {
        $action = $_POST['deleteMyAccount'];
        $actionDelete = $_POST['deleteAccount'];
        $current_user = wp_get_current_user();
        if (isset($action)) {
            $pwd = filter_input(INPUT_POST, 'verifPwd');
            if (wp_check_password($pwd, $current_user->user_pass)) {
                $code = $this->model->createRandomCode($current_user->ID);

                //Le mail en html et utf8
                $to = $current_user->user_email;
                $subject = "Désinscription à la télé-connecté";
                $message = '     <!DOCTYPE html>
                                 <html lang="fr">
                                  <head>
                                   <title>Désnscription à la télé-connecté</title>
                                  </head>
                                  <body>
                                   <p>Bonjour, vous avez décidé de vous désinscrire sur le site de la Télé Connecté</p>
                                   <p> Votre code de désinscription est : ' . $code . '.</p>
                                   <p> Pour vous désinscrire, rendez-vous sur le site : <a href="' . home_url() . '/mon-compte/"> Tv Connectée.</p>
                                  </body>
                                 </html>
                                 ';

                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail($to, $subject, $message, $headers);
                $this->view->displayMailSend();
            } else {
                $this->view->displayWrongPassword();
            }
        } elseif (isset($actionDelete)) {
            $code = $_POST['codeDelete'];
            $userCode = $this->model->getCode($current_user->ID);
            if ($code == $userCode[0]['Code']) {
                $this->model->deleteCode($current_user->ID);
                $this->deleteUser($current_user->ID);
                $this->view->displayModificationValidate();
            } else {
                $this->view->displayWrongPassword();
            }
        }
        return $this->view->displayDeleteAccount() . $this->view->displayEnterCode();
    }

    /**
     * Permet de modifier son mot de passe ou ses codes s'il y s'agit d'un élève
     * Permet aussi d'accéder à la suppression de compte
     * @return string
     */
    public function chooseModif()
    {
        $current_user = wp_get_current_user();
        $string = $this->view->displayStartMultiSelect();
        if (in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayTitleSelect('code', 'Modifier mes codes', true) . $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe');
        } else {
            $string .= $this->view->displayTitleSelect('pass', 'Modifier mon mot de passe', true);
        }
        $string .= $this->view->displayTitleSelect('delete', 'Supprimer mon compte') .
            $this->view->displayEndOfTitle();
        if (in_array('etudiant', $current_user->roles)) {
            $string .= $this->view->displayContentSelect('code', $this->modifyMyCodes(), true) .
                $this->view->displayContentSelect('pass', $this->modifyPwd());
        } else {
            $string .= $this->view->displayContentSelect('pass', $this->modifyPwd(), true);
        }
        $string .=
            $this->view->displayContentSelect('delete', $this->deleteAccount()) . $this->view->displayEndDiv();

        return $string;
    }

    /**
     * Modifie le mot de passe de l'utilisateur
     * s'il rentre son mot de passe actuel
     */
    public function modifyPwd()
    {
        $action = $_POST['modifyMyPwd'];
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
     * Affiche l'emploi du temps demandé
     * @param $code     int Code ADE de l'emploi du temps
     * @return string|bool
     */
    public function displaySchedule($code)
    {
        global $R34ICS;
        $R34ICS = new R34ICS();

        $url = $this->getFilePath($code);
        // On demande d'afficher l'emploi du temps en liste, les autres arguments ne servent à rien pour nous
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
        return $R34ICS->display_calendar($url, $code, $args);
    }

    /**
     * Affiche l'emploi du temps d'une année en fonction de l'ID récupéré dans l'url
     * @return string
     */
    function displayYearSchedule()
    {
        $code = $this->getMyIdUrl(); // On récupère l'ID qui sert de code ADE
        if (!is_numeric($code)) {
            return $this->view->displaySelectSchedule();
        } else {
            $path = $this->getFilePath($code);
            if (!file_exists($path) || filesize($path) <= 0) {
                $this->addFile($code);
            }
            if($this->displaySchedule($code)) {
                return $this->displaySchedule($code);
            } else {
                return $this->view->displayNoStudy();
            }
        }
    }

    /*
    public function displayYearSchedule(){
        $code = $this->getMyIdUrl(); // On récupère l'ID qui sert de code ADE
        if($code == 'emploi-du-temps') {
            return $this->view->displaySelectSchedule();
        } else {
            $path = $this->getFilePath($code);
            if(! file_exists($path) || filesize($path) <= 0){
                $this->addFile($code);
            }
            return $this->displaySchedule($code);
        }
    }

    public function displaySchedules() {
    $current_user = wp_get_current_user();
        $codes = unserialize($current_user->code); // On utilie cette fonction car les codes dans la base de données sont sérialisés

        if(in_array("enseignant",$current_user->roles)) {
            $this->displaySchedule($codes[0]); // On affiche le codes[0] car les enseignants n'ont qu'un code
        }

        if(in_array("etudiant",$current_user->roles)) {
            if(file_exists($this->getFilePath($codes[2]))) {
                $this->displaySchedule($codes[2]);
            } else if(file_exists($this->getFilePath($codes[1]))) {
                $this->displaySchedule($codes[1]);
            } else if($this->displaySchedule($codes[0])) {
                $this->displaySchedule($codes[0]);
            } else {
                echo "T'as pas cours gros";
            }
        }

        if(in_array("television",$current_user->roles)) {
            $this->view->displayStartSlide();
            foreach ($codes as $code) {
                $path = $this->getFilePath($code);
                if(file_exists($path)){
                    $this->displaySchedule($code);
                    $this->view->displayMidSlide();
                }
            }
            $this->view->displayEndSlide();
        }

        if (in_array("technicien", $current_user->roles)){
            $model = new CodeAdeManager();
            $years = $model->getCodeYear();
            $row = 0;
            foreach ($years as $year){
                if($row % 2 == 0) {
                    $this->view->displayRow();
                }
                $this->displaySchedule($year['code']);
                if($row % 2 == 1) {
                    $this->view->displayEndDiv();
                }
                $row = $row + 1;
            }
        }

        if(in_array("administrator", $current_user->roles) || in_array("secretary", $current_user->roles)) {
            $this->view->displayWelcomeAdmin();
        }
     */
}