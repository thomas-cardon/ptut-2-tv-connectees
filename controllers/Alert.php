<?php
/**
 * Created by PhpStorm.
 * UserView: SFW
 * Date: 06/05/2019
 * Time: 11:01
 */

class Alert extends ControllerG {

	/**
	 * @var AlertModel
	 */
    private $model;

	/**
	 * @var AlertView
	 */
    private $view;

    /**
     * Alert constructor
     */
    public function __construct() {
        $this->model = new AlertModel();
        $this->view  = new AlertView();
    }

	/**
	 * Insert an alert in the database
	 */
	public function insertAlert() {
		$action = $_POST['createAlert'];
		$text = filter_input(INPUT_POST, 'content');
		$endDate = $_POST['endDateAlert'];

		if (isset($action)) {
			$codes = $_POST['selectAlert'];
			$creationDate = date('Y-m-d');
			$current_user = wp_get_current_user();

			// Set the alert
			$this->model->setAuthor($current_user->ID);
			$this->model->setText($text);
			$this->model->setCreationDate($creationDate);
			$this->model->setEndDate($endDate);
			$this->model->setCodes($codes);

			// Insert
			if($id = $this->model->insertAlert()) {
				$this->view->displayAddValidate();
				//$this->sendAlert($id);
			}
		}
	} //createAlert()

	/**
	 * Modify an alert
	 */
	public function modifyAlert() {
		$id = $this->getMyIdUrl();
		$this->model = $this->model->getAlert($id);

		$action = filter_input(INPUT_POST, 'validateChange');

		if ($action == "Valider") {

			// Get value
			$text = filter_input(INPUT_POST, 'contentInfo');
			$endDate = filter_input(INPUT_POST, 'endDateInfo');
			$codes = $_POST['selectAlert'];

			// Set the alert
			$this->model->setText($text);
			$this->model->setEndDate($endDate);
			$this->model->setCodes($codes);

			if($this->model->modifyAlert()) {
				$this->view->displayModifyValidate();
			}
		}

		$years = $this->model->getCodeYear();
		$groups = $this->model->getCodeGroup();
		$halfGroups = $this->model->getCodeHalfgroup();

		return $this->view->displayModifyAlertForm($this->model, $years, $groups, $halfGroups);
	} //modifyAlert()

    /**
     * Delete all alerts from checkbox
     * @see alertsManagement()
     */
    public function deleteAlert() {
        $actionDelete = $_POST['Delete'];
        if (isset($actionDelete)) {
            if (isset($_REQUEST['checkboxstatusalert'])) {
                $checked_values = $_REQUEST['checkboxstatusalert'];
                foreach ($checked_values as $id) {
                	$alert = $this->model->getAlert($id);
	                $alert->deleteAlert();
                }
            }
            $this->view->refreshPage();
        }
    } //deleteAlert()


    /**
     * Display all alerts
     */
    public function alertsManagement() {

        $current_user = wp_get_current_user();
        $user = $current_user->user_login;
        if (in_array("administrator", $current_user->roles)) {
	        $alerts = $this->model->getListAlert();
        } else {
	        $alerts = $this->model->getAuthorListAlert($user);
        }

        $string = $this->view->tabHeadAlert();
        $i = 0;


        foreach ($alerts as $alert) {
            $id = $alert->getId();
            $author = $alert->getAuthor();
            $text = $alert->getText();
            $creationDate = $alert->getCreationDate();
            $endDate = $alert->getEndDate();

            $this->endDateCheckAlert($id, $endDate);

            // Change for a date in french
            $endDatefr = date("d-m-Y", strtotime($endDate));
            $creationDatefr = date("d-m-Y", strtotime($creationDate));

            $string .= $this->view->displayAllAlert($id, $author, $text, $creationDatefr, $endDatefr, ++$i);
        }
        $string .= $this->view->displayEndTab();
        return $string;
    } //alertManagement()

    /**
     * Check the end date of the alert
     * @param $id
     * @param $endDate
     */
    public function endDateCheckAlert($id, $endDate) {
        if ($endDate <= date("Y-m-d")) {
	        $alert = $this->model->getAlert($id);
	        $alert->deleteAlert();
        }
    } //endDateCheckAlert()


    /**
     * Display all alerts link to the user
     */
    public function alertMain() {

        // Get codes from current user
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

            array_push($codesUserList, 'all'); // Alert for all users
        }


        $alerts = $this->model->getListAlert();
        $alertIdList = array();
        foreach ($alerts as $alert) {
	        $id = $alert->getId();
            $alertCodes = unserialize($alert->getCodes());
            if (in_array("administrator", $current_user->roles) || in_array("secretaire", $current_user->roles)) {
                array_push($alertIdList, $id);
            } else {
                foreach ($alertCodes as $code) {
                    if (in_array($code, $codesUserList)) {
                        array_push($alertIdList, $id);
                    }
                }
            }
        }


        $alertIdList = array_unique($alertIdList); // Delete duplicate
        $contentList = array();
        foreach ($alertIdList as $id) {
	        $alert = $this->model->getAlert($id);
            $content = $alert->getText();
            $endDate = date('Y-m-d', strtotime($alert->getEndDate()));

            $this->endDateCheckAlert($id, $endDate); // Check alert

            $content .= "&emsp;&emsp;&emsp;&emsp;";
            array_push($contentList, $content);
        }
        if (isset($content)) {
            $this->view->displayAlertMain($contentList);
        }
    } // alertMain()


    // ONESIGNAL NOTIFICATIONS PUSH
    public function sendAlert($id) {

        $alert = $this->model->getAlert($id);
        $message = $alert['text'];
        $listCodesAlerts = $this->model->getListAlert();
        $listCodesAlert = array();
        foreach ($listCodesAlerts as $listCodes) {
            $listCodesAlert .= unserialize($listCodes->getCodes());
        }

        if (in_array("all", $listCodesAlert)) {
            $this->sendMessage("all", $message);
        } else {
            $students = $this->model->getUsersByRole("etudiant");
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

    function sendMessage($login, $message) {

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