<?php
/**
 * Created by PhpStorm.
 * UserView: SFW
 * Date: 06/05/2019
 * Time: 11:01
 */

class AlertView extends ViewG {

	/**
	 * Display creation form
	 *
	 * @param $years        array
	 * @param $groups       array
	 * @param $halfGroups   array
	 *
	 * @return string
	 */
	public function displayAlertCreationForm($years, $groups, $halfGroups) {
		$dateMin = date('Y-m-d', strtotime("+1 day")); // Fix the date min to the next day

		return '
            <form id="alert" method="post">
            	<div class="form-group">
            		<label for="content">Contenu</label>
                	<input class="form-control" id="content" type="text" name="content" required maxlength="280">
				</div>
                <div class="form-group">
                	<label for="endDateAlert">Date d\'expiration</label>
                	<input class="form-control" id="endDateAlert" type="date" name="endDateAlert" min="' . $dateMin . '" required >
                </div>
                <div class="form-group">
                	<label for="selectAlert">Année, groupe, demi-groupes concernés</label>
                	<select id="selectAlert" class="form-control firstSelect" name="selectAlert[]" required="">
                	' . $this->displaySelect($years, $groups, $halfGroups) . '
                </div>
                <input type="button" onclick="addButtonAlert()" value="+">
                <input type="submit" value="Publier" name="createAlert">
            </form>';
	} //displayCreationForm();

	/**
	 * Display form for modify alert
	 *
	 * @param $alert       AlertModel
	 * @param $years        array
	 * @param $groups       array
	 * @param $halfGroups   array
	 *
	 * @return string
	 */
	public function displayModifyAlertForm($alert, $years, $groups, $halfGroups) {
		$page = get_page_by_title('Gérer les alertes');
		$linkManageAlert = get_permalink($page->ID);

		$dateMin = date('Y-m-d', strtotime("+1 day"));
		$endDate = date('Y-m-d', strtotime($alert->getEndDate()));
		$codes = unserialize($alert->getCodes());

		$count = 0;
		$string = '
                    <form id="alert" method="post">
                    <div class="form-group">
                        <label for="contentInfo">Contenu</label>
                        <input class="form-control" id="contentInfo" type="text" name="contentInfo" value="' . $alert->getText() . '" maxlength="280">
                    </div>
                    <div class="form-group">
                        <label for="endDateInfo">Date d\'expiration</label>
                        <input class="form-control" id="endDateInfo" type="date" name="endDateInfo" min="' . $dateMin . '" value = "' . $endDate . '" required >
                    </div>';
		if (is_array($codes)) {
			foreach ($codes as $code) {
				$count = $count + 1;
				if ($count == 1) {
					$string .= '
					<label for="selectId' . $count . '">Année, groupe, demi-groupes concernés</label>
					<select class="form-control" class="form-control firstSelect" name="selectAlert[]" id="selectId' . $count . '">' .
					           $this->displaySelect($years, $groups, $halfGroups, $code);
				} else {
					$string .= '
                        <div class="row">
                            <select class="form-control select" name="selectAlert[]" id="selectId' . $count . '">' .
					           $this->displaySelect($years, $groups, $halfGroups, $code)
					           . '<input type="button" id="selectId' . $count . '" onclick="deleteRowAlert(this.id)" class="selectbtn" value="Supprimer">
                        </div>';
				}
			}
		} else {
			$string .= '<select class="form-control firstSelect" name="selectAlert[]" id="selectId' . $count . '">' .
			           $this->displaySelect($years, $groups, $halfGroups, $codes);
		}

		$string .= '<input type="button" onclick="addButtonAlert()" value="+">    
                    <input type="submit" name="validateChange" value="Valider" ">
                    <a href="' . $linkManageAlert . '">Annuler</a>
                 </form>';

		return $string;
	} //displayModifyAlertForm()

    /**
     * Display a selector with all groups
     *
     * @param $years        array
     * @param $groups       array
     * @param $halfGroups   array
     * @param $name         string
     *
     * @return string
     */
    public function displaySelect($years, $groups, $halfGroups, $name = 'all') {
        $selected = $name;
        $string = '<option value="0">Aucun</option>
                   <option value="all"';
        if ('all' == $selected) $string .= "selected";
        $string .= '> Tous</option>  
                   <optgroup label="Année">';

        if (is_array($years)) {
            foreach ($years as $year) {
                $string .= '<option value="' . $year['code'] . '" ';
                if ($year['code'] == $selected) $string .= "selected";
                $string .= '>' . $year['title'] . '</option >';
            }
        } else {
            $string .= '<option value="' . $years['code'] . '" ';
            if ($years['code'] == $selected) $string .= "selected";
            $string .= '>' . $years['title'] . '</option >';
        }

        $string .= '</optgroup>
                    <optgroup label="Groupe">';

        if (is_array($groups)) {
            foreach ($groups as $group) {
                $string .= '<option value="' . $group['code'] . '"';
                if ($group['code'] == $selected) $string .= "selected";
                $string .= '>' . $group['title'] . '</option>';
            }
        } else {
            $string .= '<option value="' . $groups['code'] . '"';
            if ($groups['code'] == $selected) $string .= "selected";
            $string .= '>' . $groups['title'] . '</option>';
        }

        $string .= '</optgroup>
                          <optgroup label="Demi groupe">';

	    if ( is_array($halfGroups)) {
		    foreach ($halfGroups as $halfgroup) {
                $string .= '<option value="' . $halfgroup['code'] . '" ';
                if ($halfgroup['code'] == $selected) $string .= "selected";
                $string .= '>' . $halfgroup['title'] . '</option>';
            }
        } else {
            $string .= '<option value="' . $halfGroups['code'] . '" ';
		    if ( $halfGroups['code'] == $selected) $string .= "selected";
            $string .= '>' . $halfGroups['title'] . '</option>';
        }

        $string .= '</optgroup>
        </select>';
        return $string;
    }

    /**
     * Set the head of the table for the alert's management page.
     */
    public function tabHeadAlert() {
        $tab = ["Auteur", "Contenu", "Date de création", "Date de fin"];
        return $this->displayStartTab('alert', $tab);
    }//tabHeadAlert();

    /**
     * Display the information of the alert
     * @param $id               int id
     * @param $author           string
     * @param $content          string
     * @param $creationDate     string
     * @param $endDate          string
     * @param $row              int
     * @return string
     */
    public function displayAllAlert($id, $author, $content, $creationDate, $endDate, $row) {
        $page = get_page_by_title('Modification alerte');
        $linkManageAlert = get_permalink($page->ID);
        $tab = [$author, $content, $creationDate, $endDate];
        return $this->displayRowTable($row, 'alert', $id, $tab) . '
          <td class="text-center"> <a href="' . $linkManageAlert . $id . '" type="submit" value="Modifier">Modifier</a></td>
        </td>';
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