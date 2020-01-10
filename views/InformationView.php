<?php
/**
 * Created by PhpStorm.
 * UserView: Léa Arnaud
 * Date: 17/04/2019
 * Time: 11:35
 */

class InformationView extends ViewG {

	/**
	 * Display the beginning of a form
	 *
	 * @param $title
	 *
	 * @return string
	 */
	public function displayStartForm($title = null) {
		return '
		<form method="post" enctype="multipart/form-data">
			<div class="form-group">
                <label for="titleInfo">Titre <span class="text-muted">(Optionnel)</span></label>
                <input id="titleInfo" class="form-control" type="text" name="titleInfo" placeholder="Inserer un titre" maxlength="60" value="'.$title.'">
        	</div>';
	}

	/**
	 * Display the end of a form
	 *
	 * @param $type
	 * @param $endDate
	 *
	 * @return string
	 */
	public function displayEndForm( $type, $endDate = null ) {
		$dateMin = date( 'Y-m-d', strtotime( "+1 day" ) );
		return '
		<div class="form-group">
        	<label for="endDateInfo">Date d\'expiration</label>
        	<input id="endDateInfo" class="form-control" type="date" name="endDateInfo" min="' . $dateMin . '" value="'.$endDate.'" required >
        </div>
    	<input class="btn btn-primary" type="submit" value="creer" name="' . $type . '">
            </form>';
	}

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
	public function displayFormText($title = null, $content = null, $endDate = null, $type = "createText") {
		return $this->displayStartForm($title) . '
                <label for="contentInfo">Contenu</label>
                <textarea class="form-control" id="contentInfo" name="contentInfo" maxlength="1000" rows="3">'.$content.'</textarea>' .
		       $this->displayEndForm($type, $endDate);
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
	public function displayFormImg($title = null, $content = null, $endDate = null, $type = "createImg") {
		$string = $this->displayStartForm($title);
		if($content != null){
			$string .= '
		       	<figure>
				  <img class="container-fluid" src="'. TV_UPLOAD_PATH  .$content.'" alt="'.$title.'">
				  <figcaption class="text-center">Image actuelle</figcaption>
				</figure>';
		}
		$string .= '
		<div class="form-group">
			<label for="contentFile">Ajouter une image</label>
	        <input class="form-control-file" id="contentFile" type="file" name="contentFile"/>
	        <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
        </div>' .
		           $this->displayEndForm($type, $endDate);
		return $string;
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
	public function displayFormTab($title = null, $content = null, $endDate = null, $type = "createTab") {

		$string = $this->displayStartForm($title);

		if($content != null) {
			$info = new Information();
			$list = $info->readSpreadSheet($content);
			foreach ( $list as $table ) {
				$string .= $table;
			}
		}

		$string .= '
				<div class="form-group">
	                <label for="contentFile">Ajout du fichier Xls (ou xlsx)</label>
	                <input class="form-control-file" id="contentFile" type="file" name="contentFile" />
	                <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
	                <small id="tabHelp" class="form-text text-muted">Nous vous conseillons de ne pas dépasser trois colonnes.</small>
                	<small id="tabHelp" class="form-text text-muted">Nous vous conseillons également de ne pas mettre trop de contenu dans une cellule.</small>
                </div>'.
		           $this->displayEndForm($type, $endDate);
		return $string;
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
	public function displayFormPDF($title = null, $content = null, $endDate = null, $type = "createPDF") {
		$string = $this->displayStartForm($title);

		if($content != null) {
			$string .= '
			<div class="embed-responsive embed-responsive-16by9">
			  <iframe class="embed-responsive-item" src="'. TV_UPLOAD_PATH . $content . '" allowfullscreen></iframe>
			</div>';
		}

		$string .='
				<div class="form-group">
	                <label>Ajout du fichier PDF</label>
	                <input class="form-control-file" type="file" name="contentFile"/>
	                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
                </div>'.
		          $this->displayEndForm($type, $endDate);
		return $string;
	}

	/**
	 * Display a form to create an event information with images or PDFs
	 *
	 * @param $endDate  string
	 * @param $type     string
	 *
	 * @return string
	 */
	public function displayFormEvent($endDate = null, $type = "createEvent") {
		return '
		<form method="post" enctype="multipart/form-data">
			<div class="form-group">
                <label>Sélectionner les fichiers</label>
                <input class="form-control-file" multiple type="file" name="contentFile[]"/>
                <input type="hidden" name="MAX_FILE_SIZE" value="5000000"/>
                <small id="fileHelp" class="form-text text-muted">Images ou PDF</small>
        	</div>'.
		       $this->displayEndForm($type, $endDate);
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
	public function displayModifyInformationForm($title, $content, $endDate, $type) {
		if ($type == "text") {
			return $this->displayFormText($title, $content, $endDate, "changeText");
		} elseif ($type == "img") {
			return $this->displayFormImg($title, $content, $endDate, "changeImg");
		} elseif ($type == "tab") {
			return $this->displayFormTab($title, $content, $endDate, "changeTab");
		} elseif ($type == "pdf") {
			return $this->displayFormPDF($title, $content, $endDate, "changePDF");
		} elseif ($type == "event") {
			$extension = explode('.', $content);
			$extension = $extension[1];
			if($extension == "pdf") {
				return $this->displayFormPDF($title, $content, $endDate, "changeEventPDF");
			} else {
				return $this->displayFormImg($title, $content, $endDate, "changeEventImg");
			}
		} else {
			return '<p>Désolé, une erreur semble être survenue.</p>';
		}
	} //displayModifyInformationForm()

	/**
	 * Display the header of the table who display all informations
	 * @return string
	 */
	public function tabHeadInformation() {
		$tab = [ "Titre", "Auteur", "Contenu", "Date de création", "Date de fin" ];

		return $this->displayStartTab( 'info', $tab );
	} //tabHeadInformation()

	/**
	 * Display an information in a line of a table
	 *
	 * @param $id               int id info
	 * @param $title            string title of information
	 * @param $author           string login de l'auteur
	 * @param $content          string content of the information
	 * @param $type             string type of information (Pdf, img, tableau, texte)
	 * @param $creationDate     string  creation date of the information
	 * @param $endDate          string end date of the information
	 * @param $row              int number of the line
	 *
	 * @return string
	 */
	public function displayAllInformation($id, $title, $author, $content, $type, $creationDate, $endDate, $row) {
		// Get the link of the modification page
		$page           = get_page_by_title('Modification information');
		$linkModifyInfo = get_permalink($page->ID);

		$source = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $content;

		if($type == "img" || $type == "event") {
			$extension = explode('.', $content);
			$extension = $extension[1];
			$extensions = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
			if(in_array($extension, $extensions)) {
				$content = '<img class="container-fluid" src="'. TV_UPLOAD_PATH .$content.'" alt="'.$title.'">';
			}
		}

		if($type == "pdf" || $type == "event") {
			$extension = explode('.', $content);
			$extension = $extension[1];
			if($extension == "pdf") {
				$content = '[pdf-embedder url="' . TV_UPLOAD_PATH . $content . '"]';
			}
		}

		if($type == "tab") {
			$content = "<p>Tableau Excel</p>";
		}

		$contents = [$title, $author, $content, $creationDate, $endDate];
		$string   = $this->displayRowTable($row, 'info', $id, $contents);

		if ($type == 'tab' || $type == 'img') {
			if (! file_exists($source)) {
				$string .= '<td class="text-center red"> Le fichier n\'exite pas';
			} else {
				$string .= '<td class="text-center">';
			}
		} else {
			$string .= '<td class="text-center">';
		}

		$string .= '
               <a href="' . $linkModifyInfo . $id . '" type="submit" value="Modifier">Modifier</a></td>
            </tr>';

		return $string;
	} // displayAllInformation()

	/**
	 * Display the begin of the slideshow
	 */
	public function displayStartSlideshow() {
		echo '<div class="slideshow-container">';
	}

	/**
	 * Display a slide for the slideshow
	 *
	 * @param $title
	 * @param $content
	 * @param $type
	 */
	public function displaySlide($title, $content, $type) {
		echo '<div class="myInfoSlides">';

		// If the title is empty
		if ($title != "Sans titre") {
			echo '<h2 class="titleInfo">' . $title . '</h2>';
		}

		$extension = explode('.', $content);
		$extension = $extension[1];
		if ($type == 'pdf' || $type == "event" && $extension == "pdf") {    // Display a canvas with a div id with the name of the file
			echo '
			<div class="canvas_pdf" id="'.$content.'">
				<canvas id="the-canvas-'.$content.'"></canvas>
			</div>';
		} elseif ($type == "img" || $type == "event") { // Display an image
			if ($title != "Sans titre") {
				echo '<img class="img-with-title" src="'. TV_UPLOAD_PATH .$content.'" alt="'.$title.'">';
			} else {
				echo '<img class="img-without-title" src="'. TV_UPLOAD_PATH .$content.'" alt="'.$title.'">';
			}

		}  else if ($type == 'text') {
			echo '<p class="info-text">'.$content.'</p>';
		} else if ($type == 'special') {                                    // Call a function from the plugin
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
	public function displayStartSlideEvent() {
		echo '
            <div id="slideshow-container" class="slideshow-container">';
	}

	/**
	 * Start a slide
	 */
	public function displaySlideBegin() {
		echo '
			<div class="mySlides event-slide">';
	}


	/**
	 * Display a modal to validate the creation of an information
	 */
	public function displayCreateValidate() {
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
	public function displayModifyValidate() {
		$page           = get_page_by_title( 'Gérer les informations' );
		$linkManageInfo = get_permalink( $page->ID );
		$this->displayStartModal( "Modification d'information validée" );
		echo '<p class="alert alert-success"> L\'information a été modifiée </p>';
		$this->displayEndModal( $linkManageInfo );
	}

	/**
	 * Display a message if the insertion of the information doesn't work
	 */
	public function displayErrorInsertionInfo() {
		echo '<p>Il y a eu une erreur durant l\'insertion de l\'information</p>';
	}
}