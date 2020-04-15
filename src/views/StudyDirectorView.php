<?php

namespace Views;

use Models\User;

/**
 * Class StudyDirectorView
 *
 * Contain all view for study director (Forms, tables)
 *
 * @package Views
 */
class StudyDirectorView extends UserView
{

    /**
     * Display a form for create a study director
     *
     * @return string
     */
    public function displayCreateDirector()
    {
        return '
        <form class="cadre" method="post">
            <h2> Compte directeur d\'études</h2>
            <div class="form-group">
                <label for="loginDirec">Login</label>
                <input minlength="4" type="text" class="form-control" name="loginDirec" placeholder="Login" required="">
                <small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="emailDirec">Email</label>
                <input type="email" class="form-control" name="emailDirec" placeholder="Email" required="">
            </div>
            <div class="form-group">
                <label for="pwdDirec">Mot de passe</label>
                <input type="password" class="form-control" id="pwdDirec" name="pwdDirec" minlength="8" maxlength="25" placeholder="Mot de passe" required="" onkeyup=checkPwd("Direc")>
                <input type="password" class="form-control" id="pwdConfDirec" name="pwdConfirmDirec" minlength="8" maxlength="25" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Direc")>
                <small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
            </div>
            <div class="form-group">
                <label for="codeADEDirec"> Code ADE</label>
                <input type="text" class="form-control" placeholder="Code ADE" name="codeDirec" required="">
            </div>
            <input type="submit" id="validDirec" name="createDirec" value="Créer">
        </form>';
    }

	/**
	 * Display all study directors in a table
	 *
	 * @param $users    User[]
	 *
	 * @return string
	 */
	public function displayAllStudyDirector($users)
	{
		$page = get_page_by_title('Modification utilisateur');
		$linkManageUser = get_permalink($page->ID);

		$title = 'Directeur d\'études';
		$name = 'direc';
		$header = ['Numéro Ent', 'Code ADE', 'Modifier'];

		$row = array();
		$count = 0;
		foreach ($users as $user) {
			++$count;
			$row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin(), $user->getCodes()[0]->getCode(), $this->buildLinkForModify($linkManageUser.'/'.$user->getId())];
		}

		return $this->displayAll($name, $title, $header, $row, 'director');
	}

    /**
     * Display a form to modify the study director
     *
     * @param $user   User
     *
     * @return string
     */
    public function displayModifyStudyDirector($user)
    {
	    $page = get_page_by_title('Gestion des utilisateurs');
	    $linkManageUser = get_permalink($page->ID);

	    return '
        <form method="post">
            <h2>' . $user->getLogin() . '</h2>
            <label for="modifCode">Code ADE</label>
            <input type="text" class="form-control" id="modifCode" name="modifCode" placeholder="Entrer le Code ADE" value="' . $user->getCodes()[0]->getCode() . '" required="">
            <button name="modifValidate" type="submit" value="Valider">Valider</button>
            <a href="' . $linkManageUser . '">Annuler</a>
        </form>';
    }
}