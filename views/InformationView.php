<?php
/**
 * Created by PhpStorm.
 * UserView: Léa Arnaud
 * Date: 17/04/2019
 * Time: 11:35
 */

class InformationView extends ViewG {

	/**
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
	 * Affiche le formulaire de création de l'information en format texte
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
	 * Affiche le formulaire de création d'information avec une image
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
				  <img class="container-fluid" src="'.$content.'" alt="'.$title.'">
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
	 * Affiche le formulaire de création d'information avec un tableau
	 *
	 * @param $title    string
	 * @param $content  string
	 * @param $endDate  string
	 * @param $type     string
	 *
	 * @return string
	 */
	public function displayFormTab($title = null, $content = null, $endDate = null, $type = "createTab") {

		$string = $this->displayStartForm($title);

		if($content != null) {
			$info = new Information();
			$id = explode('.', $content);
			$list = $info->readSpreadSheet( $id[0] );
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
	 * Form pour créer une information sous pdf
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
			  <iframe class="embed-responsive-item" src="'.$content.'" allowfullscreen></iframe>
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
	 * Form pour créer une information d'événement
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
	 * Affiche le formulaire de modification d'information
	 *
	 * @param $title        string titre
	 * @param $content      string contenu de l'information
	 * @param $endDate      string date d'expirarion
	 * @param $type     string type de l'information
	 *
	 * @return string
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
			if(substr($content, 0, 1) == "[") {
				return $this->displayFormPDF($title, $content, $endDate, "changeEventPDF");
			} else {
				return $this->displayFormImg($title, $content, $endDate, "changeEventImg");
			}
		} else {
			return '<p>Désolé, une erreur semble être survenue.</p>';
		}
	} //displayModifyInformationForm()

	/**
	 * Affiche l'en-tête du tableau qui affiche toutes les informations créées
	 * @return string
	 */
	public function tabHeadInformation() {
		$tab = [ "Titre", "Auteur", "Contenu", "Date de création", "Date de fin" ];

		return $this->displayStartTab( 'info', $tab );
	} //tabHeadInformation()

	/**
	 * Affiche une ligne du tableau des informations créées
	 *
	 * @param $id               int id alerte
	 * @param $title            string titre de l'information
	 * @param $author           string login de l'auteur
	 * @param $content          string contenu de l'information
	 * @param $type             string type de l'information (Pdf, img, tableau, texte)
	 * @param $creationDate     string date de création de l'information
	 * @param $endDate          string date d'expiration de l'information
	 * @param $row              int numéro de ligne
	 *
	 * @return string
	 */
	public function displayAllInformation( $id, $title, $author, $content, $type, $creationDate, $endDate, $row ) {
		$page           = get_page_by_title('Modification information');
		$linkModifyInfo = get_permalink($page->ID);

		if($type == "img" || $type == "event") {
			$extension = explode('.', $content);
			$extension = $extension[1];
			$extensions = ['jpg', 'jpeg', 'gif', 'png', 'svg'];
			if(in_array($extension, $extensions))
			$content = '<img class="container_fluid" src="'.$content.'" alt="'.$title.'">';
		}

		if($type == "pdf" || $type == "event") {
			$extension = explode('.', $content);
			$extension = $extension[1];
			if($extension == "pdf") {
				$content = '[pdf-embedder url="'.$content.'"]';
			}
		}

		$tab            = [$title, $author, $content, $creationDate, $endDate];
		$string         = $this->displayAll( $row, 'info', $id, $tab );

		if ( $type == 'tab' ) {
			$source = $_SERVER['DOCUMENT_ROOT'] . TV_UPLOAD_PATH . $content;
			if ( ! file_exists( $source ) ) {
				$string .= '<td class="text-center red"> Le ficier n\'exite pas';
			} else {
				$string .= '<td class="text-center">';
			}
		} else {
			if ($type == 'img') {
				$source = home_url() . $content;
				if (! @getimagesize($source)) {
					$string .= '<td class="text-center red"> Le fichier n\'existe pas ';
				} else {
					$string .= '<td class="text-center">';
				}
			} else {
				$string .= '<td class="text-center">';
			}
		}
		$string .= '
               <a href="' . $linkModifyInfo . $id . '" name="modifetud" type="submit" value="Modifier">Modifier</a></td>
            </tr>';

		return $string;
	} // displayAllInformation()


	/**
	 * Affiche les informations sur la page principal avec un carousel
	 *
	 * @param $titles        array titres des informations
	 * @param $contents      array contenus des informations
	 * @param $types        array types des informations
	 */
	public function displayInformationView($titles, $contents, $types) {

		echo '<div class="slideshow-container">';
		for($i = 0; $i < sizeof($titles); ++ $i) {
			echo '<div class="myInfoSlides">';
			if ($titles[ $i ] != "Sans titre") {
				echo '<h2 class="titleInfo">' . $titles[ $i ] . '</h2>';
			}

			$extension = explode('.', $contents[$i]);
			$extension = $extension[1];
			if ($types[$i] == 'pdf' || $types[$i] == "event" && $extension == "pdf") {
				echo do_shortcode('[pdf-embedder url="'.$contents[$i].'"]');
			} elseif ($types[$i] == "img" || $types[$i] == "event") {
				echo '<img src="'.$contents[$i].'" alt="'.$titles[$i].'">';
			}  else if ($types[ $i ] == 'text') {
				echo '<p class="info-text">'.$contents[$i].'</p>';
			} else if ($types[$i] == 'special') {
				$func = explode('(Do this(function:', $contents[$i]);
				$text = explode('.', $func[0]);
				foreach ($text as $value) {
					echo '<p class="info-text">' . $value . '</p>';
				}
				$func = explode(')end)', $func[1]);
				echo $func[0]();
			} else {
				echo $contents[$i];
			}
			echo '</div>';
		}
		echo '
		</div>';
	} //displayInformationView()

	public function displayStartSlideEvent() {
		echo '
            <div id="slideshow-container" class="slideshow-container">';
	}

	public function displaySlideBegin() {
		echo '
			<div class="mySlides event-slide">';
	}


	/**
	 * Affiche un modal qui signal que l'inscription a été validé
	 */
	public function displayCreateValidate() {
		$page           = get_page_by_title( 'Gérer les informations' );
		$linkManageInfo = get_permalink( $page->ID );
		$this->displayStartModal( "Ajout d'information validé" );
		echo '<p class="alert alert-success"> L\'information a été ajoutée </p>';
		$this->displayEndModal( $linkManageInfo );
	}

	/**
	 * Affiche un message de validation dans un modal lorsque une information est modifiée
	 * Redirige à la gestion des informations
	 */
	public function displayModifyValidate() {
		$page           = get_page_by_title( 'Gérer les informations' );
		$linkManageInfo = get_permalink( $page->ID );
		$this->displayStartModal( "Modification d'information validée" );
		echo '<p class="alert alert-success"> L\'information a été modifiée </p>';
		$this->displayEndModal( $linkManageInfo );
	}

	public function displayErrorInsertionInfo() {
		echo '<p>Il y a eu une erreur durant l\'insertion de l\'information</p>';
	}
}