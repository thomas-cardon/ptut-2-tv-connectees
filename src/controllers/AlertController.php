<?php

namespace Controllers;

use Models\Alert;
use Models\CodeAde;
use Models\User;
use Views\AlertView;

/**
 * Class AlertController
 *
 * Manage alerts (create, update, delete, display)
 *
 * @package Controllers
 */
class AlertController extends Controller
{

	/**
	 * @var Alert
	 */
    private $model;

	/**
	 * @var AlertView
	 */
    private $view;

    /**
     * AlertController constructor
     */
    public function __construct()
    {
        $this->model = new Alert();
        $this->view  = new AlertView();
    }

	/**
	 * Insert an alert in the database
	 */
	public function insert()
	{
		$action = filter_input(INPUT_POST, 'submit');

		$codeAde    = new CodeAde();

		if (isset($action)) {

			$codes   = $_POST['selectAlert'];
			$content = filter_input(INPUT_POST, 'content');
			$endDate = filter_input(INPUT_POST, 'endDateAlert');

			$creationDate  = date('Y-m-d');

			$endDateString = strtotime($endDate);
			$creationDateString    = strtotime(date('Y-m-d',time()));

			$this->model->setForEveryone(0);

			$codesAde = array();
			foreach ($codes as $code) {
				if($code != 'all' && $code != 0) {
					if(is_null($codeAde->getByCode($code)->getId())) {
						return 'error';
					} else {
						$codesAde[] = $codeAde->getByCode($code);
					}
				} else if($code == 'all') {
					$this->model->setForEveryone(1);
				}
			}

			if(is_string($content) && strlen($content) >= 4 && strlen($content) <= 280 &&
			   $this->isRealDate($endDate) && $creationDateString < $endDateString) {

				$current_user = wp_get_current_user();

				// Set the alert
				$this->model->setAuthor($current_user->ID);
				$this->model->setContent($content);
				$this->model->setCreationDate($creationDate);
				$this->model->setEndDate($endDate);
				$this->model->setCodes($codesAde);

				// Insert
				if($id = $this->model->create()) {

					$this->view->displayAddValidate();
					//$this->sendAlert($id);
				}
			}
		}

		$years      = $codeAde->getAllFromType('year');
		$groups     = $codeAde->getAllFromType('group');
		$halfGroups = $codeAde->getAllFromType('halfGroup');

		return $this->view->createForm($years, $groups, $halfGroups);
	} //createAlert()

	/**
	 * Modify an alert
	 */
	public function modify()
	{
		$current_user = wp_get_current_user();

		$id = $this->getMyIdUrl();
		if(!is_numeric($id) || is_null($this->model->get($id)->getId())) {
			return 'error';
		}

		$alert = $this->model->get($id);

		if(!in_array('administrator', $current_user->roles) && $alert->getAuthor() !== $current_user->ID) {
			return 'error';
		}

		$codeAde    = new CodeAde();


		$action = filter_input(INPUT_POST, 'validateChange');

		if ($action == "Valider") {

			// Get value
			$content = filter_input(INPUT_POST, 'contentInfo');
			$endDate = filter_input(INPUT_POST, 'endDateInfo');
			$codes = $_POST['selectAlert'];

			$endDateString = strtotime($endDate);
			$creationDateString    = strtotime(date('Y-m-d',time()));

			if(is_string($content) && strlen($content) >= 4 && strlen($content) <= 280 &&
			   $this->isRealDate($endDate) && $creationDateString < $endDateString) {

				$alert->setForEveryone(0);

				$codesAde = array();
				foreach ($codes as $code) {
					if($code != 'all' && $code != 0) {
						if(is_null($codeAde->getByCode($code)->getId())) {
							return 'error';
						} else {
							$codesAde[] = $codeAde->getByCode($code);
						}
					} else if($code == 'all') {
						$alert->setForEveryone(1);
					}
				}

				// Set the alert
				$alert->setContent($content);
				$alert->setEndDate($endDate);
				$alert->setCodes($codesAde);

				if($alert->update()) {
					$this->view->displayModifyValidate();
				}
			}
		}

		$years      = $codeAde->getAllFromType('year');
		$groups     = $codeAde->getAllFromType('group');
		$halfGroups = $codeAde->getAllFromType('halfGroup');

		return $this->view->modifyForm($alert, $years, $groups, $halfGroups);
	} //modifyAlert()


	/**
	 * Delete all alerts who are checked
	 */
    public function deleteAlert()
    {
        $actionDelete = $_POST['Delete'];
        if (isset($actionDelete)) {
            if (isset($_REQUEST['checkboxstatusalert'])) {
                $checked_values = $_REQUEST['checkboxstatusalert'];
                foreach ($checked_values as $id) {
                	$alert = $this->model->get($id);
	                $alert->delete();
                }
            }
            $this->view->refreshPage();
        }
    } //deleteAlert()


    /**
     * Display all alerts
     */
    public function alertsManagement()
    {
        $current_user = wp_get_current_user();
        if (in_array("administrator", $current_user->roles)) {
	        $alerts = $this->model->getAll();
        } else {
	        $alerts = $this->model->getAuthorListAlert($current_user->ID);
        }

        return $this->view->displayAllAlert($alerts);
    } //alertManagement()


    /**
     * Display all alerts link to the user
     */
    public function alertMain()
    {
        // Get codes from current user
        $current_user = wp_get_current_user();

        $alertsUser = $this->model->getForUser($current_user->ID);
	    //$alertsUser = array_unique($alertsUser); // Delete duplicate

	    foreach ($this->model->getForEveryone() as $alert) {
	    	$alertsUser[] = $alert;
	    }

        $contentList = array();

        foreach ($alertsUser as $alert) {

            $endDate = date('Y-m-d', strtotime($alert->getEndDate()));
            $this->endDateCheckAlert($alert->getId(), $endDate); // Check alert

	        $content = $alert->getContent() . '&emsp;&emsp;&emsp;&emsp;';
            array_push($contentList, $content);
        }

        if (isset($content)) {
            $this->view->displayAlertMain($contentList);
        }
    } // alertMain()


    // ONESIGNAL NOTIFICATIONS PUSH
    public function sendAlert($id)
    {
        $alert = $this->model->get($id);
        $message = $alert['text'];
        $listCodesAlerts = $this->model->getAll();
        $listCodesAlert = array();
        foreach ($listCodesAlerts as $listCodes) {
            $listCodesAlert .= unserialize($listCodes->getCodes());
        }

        if (in_array("all", $listCodesAlert)) {
            $this->sendMessage("all", $message);
        } else {
        	$user = new User();
            $students = $user->getUsersByRole("etudiant");
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

	/**
	 * Check the end date of the alert
	 *
	 * @param $id
	 * @param $endDate
	 */
	public function endDateCheckAlert($id, $endDate)
	{
		if ($endDate <= date("Y-m-d")) {
			$alert = $this->model->get($id);
			$alert->delete();
		}
	} //endDateCheckAlert()
}