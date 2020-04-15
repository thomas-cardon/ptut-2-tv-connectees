<?php

namespace Views;

use Models\Alert;
use Models\CodeAde;

/**
 * Class AlertView
 *
 * All view for Alert (Forms, tables, messages)
 *
 * @package Views
 */
class AlertView extends View
{

	/**
	 * Display creation form
	 *
	 * @param $years        array
	 * @param $groups       array
	 * @param $halfGroups   array
	 *
	 * @return string
	 */
	public function creationForm($years, $groups, $halfGroups)
	{
		$dateMin = date('Y-m-d', strtotime("+1 day")); // Fix the date min to the next day

		return '
        <form method="post">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input class="form-control" type="text" id="content" name="content" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
			</div>
            <div class="form-group">
				<label>Date d\'expiration</label>
				<input type="date" class="form-control" id="expirationDate" name="expirationDate" min="'.$dateMin.'" required>
			</div>
            <div class="form-group">
                <label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                ' . $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" onclick="addButtonAlert()" value="+">
            <button type="submit" class="btn button_ecran" name="submit">Valider</button>
        </form>
        <a href="'.esc_url(get_permalink(get_page_by_title('Gérer les alertes'))).'">Voir les alertes</a>'.$this->contextCreateAlert();
	}

    /**
     * Explain how the alert's display
     *
     * @return string
     */
    public function contextCreateAlert()
    {
        return '
		<hr class="half-rule">
		<div>
			<h2>Les alertes</h2>
			<p class="lead">Lors de la création de votre alerte, celle-ci sera posté le lendemain sur tous les téléviseurs qui utilisent le projet de l\'écran connecté.</p>
			<p class="lead">Les alertes que vous créez seront affichées avec les alertes déjà présentes.</p>
			<p class="lead">Les alertes sont affichées les un après les autres défilant à la chaîne en bas des téléviseurs.</p>
			<div class="text-center">
				<figure class="figure">
					<img src="'.TV_PLUG_PATH.'public/img/presentation.png" class="figure-img img-fluid rounded" alt="Représentation d\'un téléviseur">
					<figcaption class="figure-caption">Représentation d\'un téléviseur</figcaption>
				</figure>
			</div>
		</div>';
    }

	/**
	 * Display form for modify alert
	 *
	 * @param $alert       Alert
	 * @param $years        array
	 * @param $groups       array
	 * @param $halfGroups   array
	 *
	 * @return string
	 */
	public function modifyForm($alert, $years, $groups, $halfGroups)
	{
		$dateMin = date('Y-m-d', strtotime("+1 day"));
		$endDate = date('Y-m-d', strtotime($alert->getEndDate()));
		$codes = $alert->getCodes();

		$count = 1;
		$form = '
        <a href="'.esc_url(get_permalink(get_page_by_title('Gérer les alertes'))).'">< Retour</a>
        <form method="post">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input type="text" class="form-control" id="content" name="content" value="'.$alert->getContent().'" placeholder="280 caractères au maximum" minlength="4" maxlength="280" required>
            </div>
            <div class="form-group">
                <label for="expirationDate">Date d\'expiration</label>
                <input type="date" class="form-control" id="expirationDate" name="expirationDate" min="' . $dateMin . '" value = "' . $endDate . '" required>
            </div>';

		foreach ($codes as $code) {
			if ($count == 1) {
				$form .= '<label for="selectId' . $count . '">Année, groupe, demi-groupes concernés</label>'.$this->buildSelectCode($years, $groups, $halfGroups, $code, $count);
			} else {
				$form .= '
				<div class="row">'.
				        $this->buildSelectCode($years, $groups, $halfGroups, $code, $count)
				           . '<input type="button" id="selectId' . $count . '" onclick="deleteRowAlert(this.id)" class="selectbtn" value="Supprimer">
                  </div>';
			}
			$count = $count + 1;
		}

		$form .= '<input type="button" onclick="addButtonAlert()" value="+">    
                  <button type="submit" class="btn button_ecran" name="submit">Valider</button>
                  <button type="submit" class="btn delete_button_ecran" name="delete" onclick="return confirm(\' Voulez-vous supprimer le(s) élément(s) sélectionné(s) ?\');">Supprimer</button>
                </form>'.$this->contextModify();

		return $form;
	}

    public function contextModify()
    {
        return '
		<hr class="half-rule">
		<div>
			<p class="lead">La modification d\'une alerte prend effet comme pour la création, le lendemain.</p>
			<p class="lead">Vous pouvez donc prolonger le temps d\'expiration ou bien modifier le contenu de votre alerte.</p>
		</div>';
    }

    /**
     * Display the information of the alert
     *
     * @param $alerts    Alert[]
     *
     * @return string
     */
    public function displayAllAlert($alerts)
    {
	    $page = get_page_by_title('Modification alerte');
	    $linkManageAlert = get_permalink($page->ID);

	    $title = 'Alertes';
	    $name = 'alert';
	    $header = ['Contenu', 'Auteur', 'Date de création', 'Date d\'expiration', 'Modifier'];

	    $row = array();
	    $count = 0;
	    foreach ($alerts as $alert) {
		    ++$count;
		    $row[] = [$count, $this->buildCheckbox($name, $alert->getId()), $alert->getContent(), $alert->getAuthor()->getLogin(), $alert->getCreationDate(), $alert->getEndDate(), $this->buildLinkForModify($linkManageAlert.'/'.$alert->getId())];
	    }

	    return $this->displayAll($name, $title, $header, $row);
    }

    public function contextDisplayAll()
    {
        return '
		<div class="row">
			<div class="col-6 mx-auto col-md-6 order-md-2">
				<img src="'.TV_PLUG_PATH.'public/img/alert.png" alt="Logo alerte" class="img-fluid mb-3 mb-md-0">
			</div>
			<div class="col-md-6 order-md-1 text-center text-md-left pr-md-5">
				<p class="lead">Vous pouvez retrouver ici toutes les alertes qui ont été créées sur ce site.</p>
				<p class="lead mb-4">Les alertes sont triées de la plus vieille à la plus récente.</p>
				<p class="lead mb-4">Vous pouvez modifier une alerte en cliquant sur "Modifier" à la ligne correspondante à l\'alerte.</p>
				<p class="lead mb-4">Vous souhaitez supprimer une / plusieurs alerte(s) ? Cochez les cases des alertes puis cliquez sur "Supprimer" le bouton ce situe en bas du tableau.</p>
			</div>
		</div>
		<hr class="half-rule">';
    }

    /**
     * Display alerts
     *
     * @param $texts      array
     */
    public function displayAlertMain($texts)
    {
        echo '
        <div class="alerts" id="alert">
             <div class="ti_wrapper">
                <div class="ti_slide">
                    <div class="ti_content">';
        for ($i = 0; $i < sizeof($texts); ++$i) {
            echo '<div class="ti_news"><span>' . $texts[$i] . '</span></div>';
        }
        echo '
                    </div>
                </div>
            </div>
        </div>
        ';
    } //displayAlertMain()

	/**
	 * Build a select with all codes Ade
	 *
	 * @param $years        CodeAde[]
	 * @param $groups       CodeAde[]
	 * @param $halfGroups   CodeAde[]
	 * @param $code         CodeAde
	 * @param $count        int
	 *
	 * @return string
	 */
	public function buildSelectCode($years, $groups, $halfGroups, $code = null,  $count = 0)
	{
		$select = '<select class="form-control firstSelect" id="selectId'.$count.'" name="selectAlert[]" required="">';

		if(!is_null($code)) {
			$select .= '<option value="'.$code->getCode().'">'.$code->getTitle().'</option>';
		}

		$select .= '<option value="all">Tous</option>
					<option value="0">Aucun</option>
            		<optgroup label="Année">';

		foreach ($years as $year) {
			$select .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
		}
		$select .= '</optgroup><optgroup label="Groupe">';

		foreach ($groups as $group) {
			$select .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
		}
		$select .= '</optgroup><optgroup label="Demi groupe">';

		foreach ($halfGroups as $halfGroup) {
			$select .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
		}
		$select .= '</optgroup>
			</select>';

		return $select;
	}

    public function noAlert()
    {
        return '
		<a href="'.esc_url(get_permalink(get_page_by_title('Gérer les alertes'))).'">< Retour</a>
		<div>
			<h3>Alerte non trouvée</h3>
			<p>Cette alerte n\'éxiste pas, veuillez bien vérifier d\'avoir bien cliqué sur une alerte.</p>
			<a href="'.esc_url(get_permalink(get_page_by_title('Créer une alerte'))).'">Créer une alerte</a>
		</div>';
    }

    /**
     * Display modal for validate the creation of an alert
     */
    public function displayAddValidate()
    {
        $page = get_page_by_title('Gérer les alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été envoyée !</div>', $linkManageAlert);
    }

    /**
     * Display modal for validate the modification of an alert
     */
    public function displayModifyValidate()
    {
        $page = get_page_by_title('Gérer les alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->buildModal('Ajout d\'alerte', '<div class="alert alert-success"> Votre alerte a été modifiée ! </div>', $linkManageAlert);
    }
}