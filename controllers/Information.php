<?php
/**
 * Created by PhpStorm.
 * UserView: Léa Arnaud
 * Date: 17/04/2019
 * Time: 11:33
 */

class Information extends ControllerG {
	private $model;
	private $view;

	/**
	 * Constructeur d'information, initialise le modèle et la vue.
	 */
	public function __construct() {
		$this->model = new InformationModel();
		$this->view  = new InformationView();
	}

	/**
	 * Display forms for create information and add it into the database
	 *
	 * @return string
	 */
	public function insertInformation() {

		// The current user who want to create the information
		$current_user = wp_get_current_user();

		// Les différents formulaires
		$actionText  = $_POST['createText'];
		$actionImg   = $_POST['createImg'];
		$actionTab   = $_POST['createTab'];
		$actionPDF   = $_POST['createPDF'];
		$actionEvent = $_POST['createEvent'];

		// Variables
		$title       = filter_input( INPUT_POST, 'titleInfo' );
		$content     = filter_input( INPUT_POST, 'contentInfo' );
		$endDate     = filter_input( INPUT_POST, 'endDateInfo' );
		$contentFile = $_FILES['contentFile'];

		$creationDate = date('Y-m-d');

		// If the title is empty
		if ($title == '') {
			$title = 'Sans titre';
		}

		// Set the base of all information
		$this->model->setTitle($title);
		$this->model->setAuthor($current_user->ID);
		$this->model->setCreationDate($creationDate);
		$this->model->setEndDate($endDate);

		if ($actionText) {   // If the information is a text
			$this->model->setContent($content);
			$this->model->setType("text");

			// Try to insert the information
			if($this->model->insertInformation()) {
				$this->view->displayCreateValidate();
			} else {
				$this->view->displayErrorInsertionInfo();
			}
		} elseif ($actionImg) {  // If the information is an image
			$type = "img";
			$this->model->setType($type);
			$filename    = $_FILES['contentFile']['name'];
			$fileTmpName = $_FILES['contentFile']['tmp_name'];
			$this->registerFile($filename, $fileTmpName, $type);
		} elseif ($actionTab) { // If the information is a table
			$type = "tab";
			$this->model->setType($type);
			$filename    = $_FILES['contentFile']['name'];
			$fileTmpName = $_FILES['contentFile']['tmp_name'];
			$this->registerFile($filename, $fileTmpName, $type);
		} else if ($actionPDF) {
			$type = "pdf";
			$this->model->setType($type);
			$filename    = $_FILES['contentFile']['name'];
			$fileTmpName = $_FILES['contentFile']['tmp_name'];
			$this->registerFile($filename, $fileTmpName, $type);
		} else if ($actionEvent) {
			$type       = "event";
			$this->model->setType($type);

			// Register all files
			$countFiles = count( $_FILES['contentFile']['name'] );
			for ( $i = 0; $i < $countFiles; $i ++ ) {
				$this->model->setId(null);
				$filename    = $_FILES['contentFile']['name'][$i];
				$fileTmpName = $_FILES['contentFile']['tmp_name'][$i];
				$this->registerFile( $filename, $fileTmpName, $type );
			}
		}

		return
			$this->view->displayStartMultiSelect() .
			$this->view->displayTitleSelect('text','Texte', true) .
			$this->view->displayTitleSelect('image','Image') .
			$this->view->displayTitleSelect('table','Tableau') .
			$this->view->displayTitleSelect('pdf','PDF') .
			$this->view->displayTitleSelect('event', 'Événement') .
			$this->view->displayEndOfTitle() .
			$this->view->displayContentSelect('text', $this->view->displayFormText(), true) .
			$this->view->displayContentSelect('image', $this->view->displayFormImg()) .
			$this->view->displayContentSelect('table', $this->view->displayFormTab()) .
			$this->view->displayContentSelect('pdf', $this->view->displayFormPDF()) .
			$this->view->displayContentSelect('event', $this->view->displayFormEvent()) .
			$this->view->displayEndDiv();

	} //insertInformation()


	/**
	 * Upload a file in a directory and in the database
	 *
	 * @param $filename     string
	 * @param $tmpName      string
	 * @param $type         string
	 */
	public function registerFile($filename, $tmpName, $type) {
		$id               = "temporary";
		$extension_upload = strtolower(substr(strrchr($filename, '.'), 1));
		$nom              = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $id . "." . $extension_upload;

		// Upload the file
		if ($result = move_uploaded_file($tmpName, $nom)) {
			$this->model->setContent("temporary content");
			if($this->model->getId() == null) {
				$id = $this->model->insertInformation();
			} else {
				$this->model->modifyInformation();
				$id = $this->model->getId();
			}
		} else {
			$this->view->displayErrorInsertionInfo();
		}

		// If the file upload and the upload of the information in the database works
		if ($id != 0) {

			$this->model->setId($id);

			// Change filename to the id in the database
			rename( $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . "temporary." . $extension_upload,
				$_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $id . "." . $extension_upload );

			// Put the good extension to the file
			if ($type == "img" || $type == "event") {
				if (in_array($extension_upload, [ 'jpg', 'jpeg', 'gif', 'png', 'svg' ])) {
					$content = TV_UPLOAD_PATH . $id . '.' . $extension_upload;
					//$content = '<img class="img-fluid" src="' . TV_UPLOAD_PATH . $id . '.' . $extension_upload . '">';
				}
			}

			if ($type == "pdf" || $type == "event") {
				if ($extension_upload == "pdf") {
					$content = TV_UPLOAD_PATH . $id. '.' . $extension_upload;
					//$content = '[pdf-embedder url="' . TV_UPLOAD_PATH . $id . '.pdf"]';
				}
			}

			if($type == "tab") {
				$content = TV_UPLOAD_PATH . $id . $extension_upload;
			}

			if(isset($content)) {
				$this->model->setContent($content);
				$this->model->modifyInformation();
				$this->view->displayCreateValidate();
			}
		}
	}

	/**
	 * Modify the information
	 *
	 * @return string   The form
	 */
	public function modifyInformation() {

		// Id of the information
		$id = $this->getMyIdUrl();

		// All types of form
		$actionText      = $_POST['changeText'];
		$actionImg       = $_POST['changeImg'];
		$actionTab       = $_POST['changeTab'];
		$actionPDF       = $_POST['changePDF'];
		$actionEventPDF  = $_POST['changeEventPDF'];
		$actionEventImg  = $_POST['changeEventImg'];

		// The current information
		$this->model  = $this->model->getInformation($id);

		$title   = filter_input(INPUT_POST, 'titleInfo');
		$content = filter_input(INPUT_POST, 'contentInfo');
		$endDate = $_POST['endDateInfo'];

		if ($actionText) {  // If it's a text
			// Set new information
			$this->model->setTitle($title);
			$this->model->setContent($content);
			$this->model->setEndDate($endDate);

			$this->model->modifyInformation();

			$this->view->displayModifyValidate();
		} elseif ($actionImg || $actionEventImg || $actionTab || $actionPDF || $actionEventPDF) {  // If it's an information with a file
			$this->model->setTitle($title);
			$this->model->setEndDate($endDate);

			// Change the content
			if ($_FILES["contentFile"]['size'] != 0 ) { // If it's a new file
				$this->deleteFile($this->model->getId());   //$_SERVER['DOCUMENT_ROOT'].$this->model->getContent()
				$this->registerFile($_FILES["contentFile"]['name'], $_FILES["contentFile"]['tmp_name'], $this->model->getType());
				$this->model->modifyInformation();
				$this->view->displayModifyValidate();
			} else {
				$this->model->modifyInformation();
				$this->view->displayModifyValidate();
			}
		}

		// Display the view / the form
		return $this->view->displayModifyInformationForm($this->model->getTitle(), $this->model->getContent(), $this->model->getEndDate(), $this->model->getType());
	} //modifyInformation()


	/**
	 * Delete the information
	 */
	public function deleteInformations() {
		$actionDelete = $_POST['Delete'];
		if ($actionDelete) {
			if (isset($_REQUEST['checkboxstatusinfo'])) {
				// Take all checkbox
				$checked_values = $_REQUEST['checkboxstatusinfo'];
				foreach ($checked_values as $id) {
					$this->model = $this->model->getInformation($id);
					$type  = $this->model->getType();
					$types = ["img", "pdf", "tab", "event"];
					if (in_array($type, $types)) {
						$this->deleteFile($id);
					}
					$this->model->deleteInformation();
				}
			}
			$this->view->refreshPage();
		}
	} //deleteInformations()

	/**
	 * Delete the file who's link to the id
	 *
	 * @param $id int Code
	 */
	public function deleteFile( $id ) {
		$this->model = $this->model->getInformation($id);
		$source = $_SERVER['DOCUMENT_ROOT'] . $this->model->getContent();
		unlink($source);
	}

	/**
	 * Affiche un tableau avec toutes les informations et des boutons de modification ainsi qu'un bouton de suppression.
	 */
	function informationManagement() {
		$current_user = wp_get_current_user();
		$user         = $current_user->user_login;
		if (in_array( "administrator", $current_user->roles)) {
			$informations = $this->model->getListInformation();
		} else {
			$informations = $this->model->getAuthorListInformation($user);
		}

		$string = $this->view->tabHeadInformation();

		$i      = 0;
		foreach ($informations as $information) {
			$id           = $information->getId();
			$title        = $information->getTitle();
			$author       = $information->getAuthor();
			$content      = $information->getContent();
			$type         = $information->getType();
			$creationDate = $information->getCreationDate();
			$endDate      = $information->getEndDate();

			$this->endDateCheckInfo($id, $endDate);

			// change l'affichage de la date en français (jour-mois-année)
			$endDatefr      = date("d-m-Y", strtotime($endDate));
			$creationDatefr = date("d-m-Y", strtotime($creationDate));

			$string .= $this->view->displayAllInformation($id, $title, $author, $content, $type, $creationDatefr, $endDatefr, ++$i);
		}
		$string .= $this->view->displayEndTab();

		return $string;
	} // informationManagement()



	/**
	 * Verifie si la date de fin est dépassée et supprime l'info si c'est le cas.
	 *
	 * @param $id
	 * @param $endDate
	 */
	public function endDateCheckInfo($id, $endDate) {
		if ($endDate <= date("Y-m-d")) {
			$this->model->deleteInformation();
			$this->deleteFile($id);
		}
	} //endDateCheckInfo()


	/**
	 * Affiche les informations sur la page principale (ou widget)
	 * cf snippet Display Information
	 */
	public function informationMain() {

		$informations = $this->model->getListInformation();
		$idList = $titleList = $contentList = $typeList = array();
		foreach ($informations as $information) {
			$id      = $information->getId();
			$title   = $information->getTitle();
			$content = $information->getContent();
			$endDate = date( 'Y-m-d', strtotime($information->getEndDate()));
			$type    = $information->getType();
			array_push($typeList, $type);
			$this->endDateCheckInfo($id, $endDate);
			if ($type == 'tab') {
				$source = $_SERVER['DOCUMENT_ROOT'] . $content;
				if (! file_exists($source)) {
					array_push($idList, $id);
					array_push($titleList, $title);
					array_push($contentList, 'Un beau tableau devrait être ici !');
				} else {
					$list = $this->readSpreadSheet($id);
					foreach ($list as $table) {
						array_push($idList, $id);
						array_push($titleList, $title);
						array_push($contentList, $table);
					}
				}
			} else {
				if ($type == 'img') {
					$source = home_url() . $content;
					if (! @getimagesize($source)) {
						array_push($idList, $id);
						array_push($titleList, $title);
						array_push($contentList, 'Une belle image devrait être ici !');
					} else {
						array_push($idList, $id);
						array_push($titleList, $title);
						array_push($contentList, $content);
					}
				} else {
					array_push($idList, $id);
					array_push($titleList, $title);
					array_push($contentList, $content);
				}
			}
		}
		$this->view->displayInformationView($titleList, $contentList, $typeList);
	} // informationMain()

	public function displayEvent() {
		$events = $this->model->getListInformationEvent();
		$this->view->displayStartSlideEvent();
		foreach ($events as $event) {
			$this->view->displaySlideBegin();
			$extension = explode('.', $event->getContent());
			$extension = $extension[1];
			if($extension == "pdf") {
				echo do_shortcode('[pdf-embedder url="'.$event->getContent().'"]');
			} else {
				echo '<img src="'.$event->getContent().'" alt="'.$event->getTitle().'"]';
			}
			echo $this->view->displayEndDiv();
		}
		$this->view->displayEndDiv();
	}

	public function readSpreadSheet($id) {

		$file = glob($_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $id . "." . "*");
		foreach ($file as $i) {
			$filename = $i;
		}
		$extension = ucfirst(strtolower(end(explode(".", $filename ))));
		$reader    = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($filename);

		$worksheet  = $spreadsheet->getActiveSheet();
		$highestRow = $worksheet->getHighestRow();

		$contentList = array();
		$content     = "";
		$mod         = 0;

		for ($i = 0; $i < $highestRow; ++ $i) {
			$mod = $i % 10;
			if ($mod == 0) {
				$content .= '<table class ="table table-bordered tablesize">';
			}
			foreach ($worksheet->getRowIterator($i + 1, 1) as $row) {
				$content      .= '<tr scope="row">';
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells(false);
				foreach ($cellIterator as $cell) {
					$content .= '<td class="text-center">' .
					            $cell->getValue() .
					            '</td>';
				}
				$content .= '</tr>';
			}
			if ($mod == 9) {
				$content .= '</table>';
				array_push($contentList, $content);
				$content = "";
			}
		}
		if ($mod != 9 && $i > 0) {
			$content .= '</table>';
			array_push($contentList, $content);
		}

		return $contentList;
	}
}