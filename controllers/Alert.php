<?php
/**
 * Created by PhpStorm.
 * UserView: SFW
 * Date: 06/05/2019
 * Time: 11:01
 */

class Alert extends ControllerG
{
    private $DB;
    private $view;

    /**
     * Constructeur d'alert, initialise le modèle et la vue.
     */
    public function __construct()
    {
        $this->DB = new AlertManager();
        $this->view = new AlertView();
    }

    /**
     * Supprime les alertes sélectionnées dans la page de gestion des alertes.
     * @param $action
     * @see alertsManagement()
     */
    public function deleteAlert()
    {
        $actionDelete = $_POST['Delete'];
        if (isset($actionDelete)) {
            if (isset($_REQUEST['checkboxstatusalert'])) {
                $checked_values = $_REQUEST['checkboxstatusalert'];
                foreach ($checked_values as $val) {
                    $this->DB->deleteAlertDB($val);
                }
            }
            $this->view->refreshPage();
        }
    } //deleteAlert()


    public function createAlert()
    {
        $action = $_POST['createAlert'];
        $content = filter_input(INPUT_POST, 'content');
        $endDate = $_POST['endDateAlert'];

        if (isset($action)) {
            $codes = serialize($_POST['selectAlert']);

            $id = $this->DB->addAlertDB($content, $endDate, $codes);
            $this->view->displayAddValidate();
            //$this->sendAlert($id);
        }
    } //createAlert()


    /**
     * Affiche un tableau avec toutes les alertes et des boutons de modification ainsi qu'un bouton de suppression.
     * cf snippet Handle Alert
     */
    function alertsManagement()
    {

        $current_user = wp_get_current_user();
        $user = $current_user->user_login;
        if (in_array("administrator", $current_user->roles)) $result = $this->DB->getListAlert();
        else $result = $this->DB->getListAlertByAuthor($user);

        $string = $this->view->tabHeadAlert();
        $i = 0;


        foreach ($result as $row) {
            $id = $row['ID_alert'];
            $author = $row['author'];
            $content = $row['text'];
            $creationDate = $row['creation_date'];
            $endDate = $row['end_date'];

            $this->endDateCheckAlert($id, $endDate);

            // change l'affichage de la date en français (jour-mois-année)
            $endDatefr = date("d-m-Y", strtotime($endDate));
            $creationDatefr = date("d-m-Y", strtotime($creationDate));

            $string .= $this->view->displayAllAlert($id, $author, $content, $creationDatefr, $endDatefr, ++$i);
        }
        $string .= $this->view->displayEndTab();
        return $string;
    } //alertManagement()

    /**
     * Verifie si la date de fin est dépassée et supprime l'alerte si c'est le cas.
     * @param $id
     * @param $endDate
     */
    public function endDateCheckAlert($id, $endDate)
    {
        if ($endDate <= date("Y-m-d")) {
            $this->DB->deleteAlertDB($id);
        }
    } //endDateCheckAlert()


    /**
     * Récupère l'id de l'alerte depuis l'url et affiche le formulaire de modification pré-remplis.
     * cf snippet Modification Alert
     */
    public function modifyAlert()
    {
        $id = $this->getMyIdUrl();

        $action = filter_input(INPUT_POST, 'validateChange');

        if ($action == "Valider") {
            $content = filter_input(INPUT_POST, 'contentInfo');
            $endDate = filter_input(INPUT_POST, 'endDateInfo');
            $codes = $_POST['selectAlert'];

            $this->DB->modifyAlert($id, $content, $endDate, $codes);
            $this->view->displayModifyValidate();
        }
    } //modifyAlert()


    /**
     * Récupère la liste des alertes et l'affiche sur la page principale
     *cf snippet Display Alert
     */
    public function alertMain()
    {
        // Recuperation des codes de l'utilisateur
        $current_user = wp_get_current_user();
        $codesUserList = array();
        if (in_array("television", $current_user->roles) || in_array("etudiant", $current_user->roles) || in_array("enseignant", $current_user->roles) || in_array("directeuretude", $current_user->roles)) {
            $codes = unserialize($current_user->code);
            if (is_array($codes)) {
                foreach ($codes as $code) {
                    array_push($codesUserList, $code);
                }
            } else {
                array_push($codesUserList, $codes);
            }

            array_push($codesUserList, 'all'); // Pour avoir les alertes concerné par tous
        }

        //Ajoute dans une liste les alertes avec le même code que l'utilisateur
        $result = $this->DB->getListAlert();
        $alertIDList = array();
        foreach ($result as $row) {
            $alertCodes = unserialize($row['codes']);
            $id = $row['ID_alert'];
            if (in_array("administrator", $current_user->roles) || in_array("secretaire", $current_user->roles)) {
                array_push($alertIDList, $id);
            } else {
                foreach ($alertCodes as $code) {
                    if (in_array($code, $codesUserList)) {
                        array_push($alertIDList, $id);
                    }
                }
            }
        }


        $alertIDList = array_unique($alertIDList); //retire les doublons
        $contentList = array();
        foreach ($alertIDList as $id) {
            $result = $this->DB->getAlertByID($id);
            $content = $result['text'];
            $endDate = date('Y-m-d', strtotime($result['end_date']));

            $this->endDateCheckAlert($id, $endDate); //verifie si l'alerte est depassé

            $content .= "&emsp;&emsp;&emsp;&emsp;";
            array_push($contentList, $content);
        }
        if (isset($content)) {
            $this->view->displayAlertMain($contentList);
        }
    } // alertMain()


    // ONESIGNAL NOTIFICATIONS PUSH
    public function sendAlert($id)
    {

        $alert = $this->DB->getAlertByID($id);
        $message = $alert['text'];
        $listCodesAlerts = $this->DB->getListCodes($id);
        $listCodesAlert = array();
        foreach ($listCodesAlerts as $listCodes) {
            $listCodesAlert .= unserialize($listCodes['codes']);
        }

        if (in_array("all", $listCodesAlert)) {
            $this->sendMessage("all", $message);
        } else {
            $students = $this->DB->getUsersByRole("etudiant");
            $studentCodesList = array();
            $studentLoginListToSend = array();
            foreach ($students as $student) {
                $studentCodesList = unserialize($student['code']);

                foreach ($listCodesAlert as $j) {
                    if (in_array($j, $studentCodesList)) {
                        array_push($studentLoginListToSend, $student['user_login']);
                    }
                }
            }
            $studentLoginListToSend = array_unique($studentLoginListToSend);

            foreach ($studentLoginListToSend as $i) {
                $this->sendMessage($i, $message);
            }
        }
    }

    function sendMessage($login, $message)
    {

        $content = array(
            "en" => $message
        );
        $hashes_array = array();
        if ($login == "all") {
            $fields = array(
                'app_id' => "317b1068-1f28-4e19-81e4-9a3553c449ea",
                'included_segments' => array(
                    'All'
                ),
                'data' => array(
                    "foo" => "bar"
                ),
                'contents' => $content,
                'web_buttons' => $hashes_array
            );
        } else {
            $fields = array(
                'app_id' => "317b1068-1f28-4e19-81e4-9a3553c449ea",
                'included_segments' => array(
                    'All'
                ),
                'data' => array(
                    "foo" => "bar"
                ),
                'contents' => $content,
                'web_buttons' => $hashes_array,
                'filters' => array(array("field" => "tag", "key" => "login", "relation" => "=", "value" => $login))
            );
        }

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Authorization: Basic YjY1ZGUxMDktYjNhZi00NTYxLWIwZjYtNWEwMmZhNzQ2ZGY1'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

}