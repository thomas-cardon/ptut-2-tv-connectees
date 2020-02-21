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
	public function createForm($years, $groups, $halfGroups)
	{
		$dateMin = date('Y-m-d', strtotime("+1 day")); // Fix the date min to the next day

		return '
        <form id="alert" method="post">
            <div class="form-group">
                <label for="content">Contenu</label>
                <input class="form-control" type="text" id="content" name="content" required maxlength="280" required>
			</div>
            <div class="form-group">
                <label for="endDateAlert">Date d\'expiration</label>
                <input class="form-control" type="date" id="endDateAlert" name="endDateAlert" min="' . $dateMin . '" required>
            </div>
            <div class="form-group">
                <label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                ' . $this->buildSelectCode($years, $groups, $halfGroups) . '
            </div>
            <input type="button" onclick="addButtonAlert()" value="+">
            <button type="submit" name="submit">Publier</button>
        </form>';
	} //displayCreationForm();

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
		$page = get_page_by_title('Gérer les alertes');
		$linkManageAlert = get_permalink($page->ID);

		$dateMin = date('Y-m-d', strtotime("+1 day"));
		$endDate = date('Y-m-d', strtotime($alert->getEndDate()));
		$codes = $alert->getCodes();

		$count = 1;
		$string = '
        <form id="alert" method="post">
        <div class="form-group">
            <label for="contentInfo">Contenu</label>
            <input class="form-control" id="contentInfo" type="text" name="contentInfo" value="' . $alert->getContent() . '" maxlength="280">
        </div>
        <div class="form-group">
            <label for="endDateInfo">Date d\'expiration</label>
            <input class="form-control" id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required >
        </div>';

		foreach ($codes as $code) {
			if ($count == 1) {
				$string .= '
				<label for="selectId' . $count . '">Année, groupe, demi-groupes concernés</label>'.
				           $this->buildSelectCode($years, $groups, $halfGroups, $code, $count);
			} else {
				$string .= '
				<div class="row">'.
				        $this->buildSelectCode($years, $groups, $halfGroups, $code, $count)
				           . '<input type="button" id="selectId' . $count . '" onclick="deleteRowAlert(this.id)" class="selectbtn" value="Supprimer">
                  </div>';
			}
			$count = $count + 1;
		}

		$string .= '<input type="button" onclick="addButtonAlert()" value="+">    
                    <input type="submit" name="validateChange" value="Valider" ">
                    <a href="' . $linkManageAlert . '">Annuler</a>
                 </form>';

		return $string;
	} //displayModifyAlertForm()

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
    } //displayAllAlert()

    /**
     * Display alerts
     * @param $texts      array
     */
    public function displayAlertMain($texts) {
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

    /**
     * Display modal for validate the creation of an alert
     */
    public function displayAddValidate() {
        $page = get_page_by_title('Gérer les alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->displayStartModal("Ajout d'alerte");
        echo '<div class="alert alert-success"> Votre alerte a été envoyée ! </div>';
        $this->displayEndModal($linkManageAlert);
    }

    /**
     * Display modal for validate the modification of an alert
     */
    public function displayModifyValidate() {
        $page = get_page_by_title('Gérer les alertes');
        $linkManageAlert = get_permalink($page->ID);
        $this->displayStartModal("Ajout d'alerte");
        echo '<div class="alert alert-success"> Votre alerte a été modifiée ! </div>';
        $this->displayEndModal($linkManageAlert);
    }
}