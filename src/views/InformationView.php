<?php

namespace Views;


use Controllers\InformationController;
use Models\Information;

/**
 * Class InformationView
 *
 * All view for Information (Forms, tables, messages)
 *
 * @package Views
 */
class InformationView extends View
{

	/**
	 * Display a form to create an information with text
	 *
	 * @param $title    string
	 * @param $content  string
	 * @param $endDate  string
	 * @param $type     string
	 *
	 * @return string
	 */
	public function displayFormText($title = null, $content = null, $endDate = null, $type = "createText")
	{
		$dateMin = date('Y-m-d', strtotime("+1 day"));

		return '<form method="post">
					<div class="form-group">
		                <label for="titleInfo">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="titleInfo" class="form-control" type="text" name="titleInfo" placeholder="Inserer un titre" maxlength="60" value="'.$title.'">
		            </div>
		            <div class="form-group">
	                	<label for="contentInfo">Contenu</label>
	                	<textarea class="form-control" id="contentInfo" name="contentInfo" maxlength="1000" rows="3">'.$content.'</textarea>
	                </div>
					<div class="form-group">
			            <label for="endDateInfo">Date d\'expiration</label>
			            <input id="endDateInfo" class="form-control" type="date" name="endDateInfo" min="' . $dateMin . '" value="'.$endDate.'" required >
			        </div>
			        <input class="btn btn-primary" type="submit" value="creer" name="' . $type . '">
			    </form>';
	}

	/**
	 * Display a form to create an information with an image
	 *
	 * @param $title    string
	 * @param $content  string
	 * @param $endDate  string
	 * @param $type     string
	 *
	 * @return string
	 */
	public function displayFormImg($title = null, $content = null, $endDate = null, $type = "createImg")
	{
		$dateMin = date('Y-m-d', strtotime("+1 day"));

		$form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="titleInfo">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="titleInfo" class="form-control" type="text" name="titleInfo" placeholder="Inserer un titre" maxlength="60" value="'.$title.'">
		            </div>';
		if($content != null){
			$form .= '
		       	<figure>
				  <img class="container-fluid" src="'. TV_UPLOAD_PATH  .$content.'" alt="'.$title.'">
				  <figcaption class="text-center">Image actuelle</figcaption>
				</figure>';
		}
		$form .= '
			<div class="form-group">
				<label for="contentFile">Ajouter une image</label>
		        <input class="form-control-file" id="contentFile" type="file" name="contentFile"/>
		        <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
	        </div>
	        <div class="form-group">
				<label for="endDateInfo">Date d\'expiration</label>
				<input id="endDateInfo" class="form-control" type="date" name="endDateInfo" min="' . $dateMin . '" value="'.$endDate.'" required >
			</div>
			<input class="btn btn-primary" type="submit" value="creer" name="' . $type . '">
		</form>';

		return $form;
	}

	/**
	 * Display a form to create an information with a table
	 *
	 * @param null $title
	 * @param null $content
	 * @param null $endDate
	 * @param string $type
	 *
	 * @return string
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */
	public function displayFormTab($title = null, $content = null, $endDate = null, $type = "createTab")
	{
		$dateMin = date('Y-m-d', strtotime("+1 day"));

		$form = '<form method="post" enctype="multipart/form-data">
						<div class="form-group">
			                <label for="titleInfo">Titre <span class="text-muted">(Optionnel)</span></label>
			                <input id="titleInfo" class="form-control" type="text" name="titleInfo" placeholder="Inserer un titre" maxlength="60" value="'.$title.'">
			            </div>';

		if($content != null) {
			$info = new InformationController();
			$list = $info->readSpreadSheet(TV_UPLOAD_PATH.$content);
			foreach ($list as $table) {
				$form .= $table;
			}
		}

		$form .= '
			<div class="form-group">
                <label for="contentFile">Ajout du fichier Xls (ou xlsx)</label>
                <input class="form-control-file" id="contentFile" type="file" name="contentFile" />
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
                <small id="tabHelp" class="form-text text-muted">Nous vous conseillons de ne pas dépasser trois colonnes.</small>
                <small id="tabHelp" class="form-text text-muted">Nous vous conseillons également de ne pas mettre trop de contenu dans une cellule.</small>
            </div>
            <div class="form-group">
				<label for="endDateInfo">Date d\'expiration</label>
				<input id="endDateInfo" class="form-control" type="date" name="endDateInfo" min="' . $dateMin . '" value="'.$endDate.'" required >
			</div>
			<input class="btn btn-primary" type="submit" value="creer" name="' . $type . '">
		</form>';
		return $form;
	}

	/**
	 * Display a form to create an information with a PDF
	 *
	 * @param $title    string
	 * @param $content  string
	 * @param $endDate  string
	 * @param $type     string
	 *
	 * @return string
	 */
	public function displayFormPDF($title = null, $content = null, $endDate = null, $type = "createPDF")
	{
		$dateMin = date('Y-m-d', strtotime("+1 day"));

		$form = '<form method="post" enctype="multipart/form-data">
					<div class="form-group">
		                <label for="titleInfo">Titre <span class="text-muted">(Optionnel)</span></label>
		                <input id="titleInfo" class="form-control" type="text" name="titleInfo" placeholder="Inserer un titre" maxlength="60" value="'.$title.'">
		            </div>';

		if($content != null) {
			$form .= '
			<div class="embed-responsive embed-responsive-16by9">
			  <iframe class="embed-responsive-item" src="'. TV_UPLOAD_PATH . $content . '" allowfullscreen></iframe>
			</div>';
		}

		$form .= '
			<div class="form-group">
                <label>Ajout du fichier PDF</label>
                <input class="form-control-file" type="file" name="contentFile"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
            </div>
            <div class="form-group">
				<label for="endDateInfo">Date d\'expiration</label>
				<input id="endDateInfo" class="form-control" type="date" name="endDateInfo" min="' . $dateMin . '" value="'.$endDate.'" required >
			</div>
			<input class="btn btn-primary" type="submit" value="creer" name="' . $type . '">
		</form>';

		return $form;
	}

	/**
	 * Display a form to create an event information with images or PDFs
	 *
	 * @param $endDate  string
	 * @param $type     string
	 *
	 * @return string
	 */
	public function displayFormEvent($endDate = null, $type = "createEvent")
	{
		$dateMin = date('Y-m-d', strtotime("+1 day"));
		return '
		<form method="post" enctype="multipart/form-data">
			<div class="form-group">
                <label>Sélectionner les fichiers</label>
                <input class="form-control-file" multiple type="file" name="contentFile[]"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
                <small id="fileHelp" class="form-text text-muted">Images ou PDF</small>
        	</div>
        	<div class="form-group">
				<label for="endDateInfo">Date d\'expiration</label>
				<input id="endDateInfo" class="form-control" type="date" name="endDateInfo" min="' . $dateMin . '" value="'.$endDate.'" required >
			</div>
			<input class="btn btn-primary" type="submit" value="creer" name="' . $type . '">
		</form>';
	}

	/**
	 * Display a form to modify an information
	 *
	 * @param $title
	 * @param $content
	 * @param $endDate
	 * @param $type
	 *
	 * @return string
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
	 */
	public function displayModifyInformationForm($title, $content, $endDate, $type)
	{
		if ($type == "text") {
			return $this->displayFormText($title, $content, $endDate, 'submit');
		} elseif ($type == "img") {
			return $this->displayFormImg($title, $content, $endDate, 'submit');
		} elseif ($type == "tab") {
			return $this->displayFormTab($title, $content, $endDate, 'submit');
		} elseif ($type == "pdf") {
			return $this->displayFormPDF($title, $content, $endDate, 'submit');
		} elseif ($type == "event") {
			$extension = explode('.', $content);
			$extension = $extension[1];
			if($extension == "pdf") {
				return $this->displayFormPDF($title, $content, $endDate, 'submit');
			} else {
				return $this->displayFormImg($title, $content, $endDate, 'submit');
			}
		} else {
			return '<p>Désolé, une erreur semble être survenue.</p>';
		}
	} //displayModifyInformationForm()

	/**
	 * Display an information in a line of a table
	 *
	 * @param $informations               Information[]
	 *
	 * @return string
	 */
	public function displayAllInformation($informations)
	{
		// Get the link of the modification page
		$page           = get_page_by_title('Modification information');
		$linkModifyInfo = get_permalink($page->ID);

		$title = 'Informations';
		$name = 'info';
		$header = ['Contenu', 'Auteur', 'Type', 'Date de création', 'Date d\'expiration', 'Modifier'];

		$imgExtension = ['jpg', 'jpeg', 'gif', 'png', 'svg'];

		$row = array();
		$count = 0;
		foreach ($informations as $information) {

			$content = explode('.', $information->getContent());

			if(in_array($content[1], $imgExtension)) {
				$content = '<img src="' . TV_UPLOAD_PATH . $information->getContent() . '" alt="'.$information->getTitle().'">';
			} else if($content[1] === 'pdf') {
				$content = '[pdf-embedder url="' . TV_UPLOAD_PATH . $information->getContent() . '"]';
			} else if($information->getType() === 'tab') {
				$content = 'Tableau Excel';
			} else {
				$content = $information->getContent();
			}

			$type = $information->getType();
			if($information->getType() === 'img') {
				$type = 'Image';
			} else if ($information->getType() === 'pdf') {
				$type = 'PDF';
			} else if ($information->getType() === 'event') {
				$type = 'Événement';
			} else if ($information->getType() === 'text') {
				$type = 'Texte';
			} else if ($information->getType() === 'tab') {
				$type = 'Table Excel';
			}

			++$count;
			$row[] = [$count, $this->buildCheckbox($name, $information->getId()), $content, $information->getAuthor()->getLogin(), $type, $information->getCreationDate(), $information->getEndDate(), $this->buildLinkForModify($linkModifyInfo.'/'.$information->getId())];
		}

		return $this->displayAll($name, $title, $header, $row, 'info');
	} // displayAllInformation()

	/**
	 * Display the begin of the slideshow
	 */
	public function displayStartSlideshow()
	{
		echo '<div class="slideshow-container">';
	}

	/**
	 * Display a slide for the slideshow
	 *
	 * @param $title
	 * @param $content
	 * @param $type
	 */
	public function displaySlide($title, $content, $type)
	{
		echo '<div class="myInfoSlides text-center">';

		// If the title is empty
		if ($title != "Sans titre") {
			echo '<h2 class="titleInfo">' . $title . '</h2>';
		}

		$extension = explode('.', $content);
		$extension = $extension[1];

		if ($type == 'pdf' || $type == "event" && $extension == "pdf") {
			echo '
			<div class="canvas_pdf" id="'.$content.'">
			</div>';
		} elseif ($type == "img" || $type == "event") {
			if ($title != "Sans titre") {
				echo '<img class="img-with-title" src="'. TV_UPLOAD_PATH .$content.'" alt="'.$title.'">';
			} else {
				echo '<img class="img-without-title" src="'. TV_UPLOAD_PATH .$content.'" alt="'.$title.'">';
			}

		}  else if ($type == 'text') {
			echo '<p class="info-text">'.$content.'</p>';
		} else if ($type == 'special') {
			$func = explode('(Do this(function:', $content);
			$text = explode('.', $func[0]);
			foreach ($text as $value) {
				echo '<p class="info-text">' . $value . '</p>';
			}
			$func = explode(')end)', $func[1]);
			echo $func[0]();
		} else {
			echo $content;
		}
		echo '</div>';
	}

	/**
	 * Start the slideshow
	 */
	public function displayStartSlideEvent()
	{
		echo '
            <div id="slideshow-container" class="slideshow-container">';
	}

	/**
	 * Start a slide
	 */
	public function displaySlideBegin()
	{
		echo '
			<div class="mySlides event-slide">';
	}


	/**
	 * Display a modal to validate the creation of an information
	 */
	public function displayCreateValidate()
	{
		$page           = get_page_by_title( 'Gérer les informations' );
		$linkManageInfo = get_permalink( $page->ID );
		$this->displayStartModal( "Ajout d'information validé" );
		echo '<p class="alert alert-success"> L\'information a été ajoutée </p>';
		$this->displayEndModal( $linkManageInfo );
	}

	/**
	 * Display a modal to validate the modification of an information
	 * Redirect to manage page
	 */
	public function displayModifyValidate()
	{
		$page           = get_page_by_title( 'Gérer les informations' );
		$linkManageInfo = get_permalink( $page->ID );
		$this->displayStartModal( "Modification d'information validée" );
		echo '<p class="alert alert-success"> L\'information a été modifiée </p>';
		$this->displayEndModal( $linkManageInfo );
	}

	/**
	 * Display a message if the insertion of the information doesn't work
	 */
	public function displayErrorInsertionInfo()
	{
		echo '<p>Il y a eu une erreur durant l\'insertion de l\'information</p>';
	}
}