<?php

namespace Views;


use Models\CodeAde;
use Models\User;

class TelevisionView extends UserView
{

    /**
     * Affiche un select permettant de choisir une année un groupe ou un demi-groupe déjà enregistré
     *
     * @param $years        CodeAde[] Années enregistrées dans la base de données
     * @param $groups       CodeAde[] Groupes enregistrés dans la base de données
     * @param $halfgroups   CodeAde[] Demi-groupes enregistrés dans la base de données
     *
     * @return string       Renvoie le select
     */
    public function displaySelect($years, $groups, $halfgroups)
    {
        $string = '<option value="0">Aucun</option>
                        <optgroup label="Année">';

        foreach ($years as $year) {
        	$string .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }

        $string .= '</optgroup>
                        <optgroup label="Groupe">';

        foreach ($groups as $group) {
        	$string .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
        $string .= '</optgroup>
                        <optgroup label="Demi groupe">';

        foreach ($halfgroups as $halfgroup) {
        	$string .= '<option value="' . $halfgroup->getCode() . '">' . $halfgroup->getTitle() . '</option>';
        }

        $string .= '</optgroup>
                </select>';
        return $string;
    }

    /**
     * Display a form to create a television
     *
     * @param $years        CodeAde[]
     * @param $groups       CodeAde[]
     * @param $halfGroups   CodeAde[]
     *
     * @return string
     */
    public function displayFormTelevision($years, $groups, $halfGroups)
    {
        $form = '
        <form method="post" id="registerTvForm">
            <h2> Compte télévision</h2>
            <label for="loginTv">Login</label>
            <input type="text" class="form-control text-center modal-sm" name="loginTv" placeholder="Nom de compte" required="">
            <label for="pwdTv">Mot de passe</label>
            <input type="password" class="form-control text-center modal-sm" id="pwdTv" name="pwdTv" placeholder="Mot de passe" required="" onkeyup=checkPwd("Tv")>
            <input type="password" class="form-control text-center modal-sm" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Tv")>
            <label>Premier emploi du temps</label>'.
            $this->buildSelectCode($years, $groups, $halfGroups) .'
            <input type="button" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <input type="submit" id="validTv" name="createTv" value="Créer">
        </form>';

	    return $form;
    }

	/**
	 * Display all televisions in a table
	 *
	 * @param $users    User[]
	 *
	 * @return string
	 */
    public function displayAllTv($users)
    {
	    $page = get_page_by_title('Modification utilisateur');
	    $linkManageUser = get_permalink($page->ID);

	    $title = 'Televisions';
	    $name = 'tele';
	    $header = ['Login', 'Nombre d\'emploi du temps ', 'Modifier'];

	    $row = array();
	    $count = 0;
	    foreach ($users as $user) {
		    ++$count;
		    $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), sizeof($user->getCodes()), $this->buildLinkForModify($linkManageUser.'/'.$user->getId())];
	    }

	    return $this->displayAll($name, $title, $header, $row);
    }

    /**
     * Display a form to modify a television
     *
     * @param $user         User
     * @param $years        CodeAde[]
     * @param $groups       CodeAde[]
     * @param $halfGroups   CodeAde[]
     *
     * @return string
     */
    public function modifyForm($user, $years, $groups, $halfGroups)
    {
        $count = 0;
        $string = '
         <form method="post" id="registerTvForm">
            <h2>' . $user->getLogin() . '</h2>
            <label> Emploi du temps</label>';

        foreach ($user->getCodes() as $code) {
            $count = $count + 1;
            if ($count == 1) {
                $string .= $this->buildSelectCode($years, $groups, $halfGroups, $code, $count);
            } else {
                $string .= '
					<div class="row">' .
                        $this->buildSelectCode($years, $groups, $halfGroups, $code, $count) .
                        '<input type="button" id="selectId' . $count . '" onclick="deleteRow(this.id)" class="selectbtn" value="Supprimer">
					</div>';
            }
        }
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $string .= '
            <input type="button" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <input name="modifValidate" type="submit" id="validTv" value="Valider">
            <a href="' . $linkManageUser . '">Annuler</a>
        </form>';
        return $string;
    }

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
    	$select = '<select class="form-control firstSelect" id="selectId'.$count.'" name="selectTv[]" required="">';

    	if(!is_null($code)) {
		    $select .= '<option value="'.$code->getCode().'">'.$code->getTitle().'</option>';
	    }

    	$select .= '<option value="0">Aucun</option>
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
	 * Display form to modify the password of a television
	 *
	 * @return string
	 */
    public function modifyPassword()
    {
    	return '
		<form method="post">
		<label>Nouveau mot de passe </label>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdTv" name="pwdTv" placeholder="Nouveau mot de passe" onkeyup=checkPwd("Tv")>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le nouveau mot de passe" onkeyup=checkPwd("Tv")>
		</form>';

    }

    /**
     * Message to prevent a login already exist
     */
    public function displayErrorLogin()
    {
        $this->displayStartModal('Inscription échouée');
        echo '<div class="alert alert-danger"> Le login est déjà utilisé ! </div>';
        $this->displayEndModal();
    }

    /**
     * Start a slideshow
     *
     * @return string
     */
    public function displayStartSlide()
    {
        return '
            <div id="slideshow-container" class="slideshow-container">
                <div class="mySlides">';
    }

    /**
     * Separate all slide by this
     *
     * @return string
     */
    public function displayMidSlide()
    {
        return '
                </div>
              <div class="mySlides">';
    }

    /**
     * Close the slideshow
     *
     * @return string
     */
    public function displayEndSlide()
    {
        return '</div>
           </div>';
    }
}