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
                    <label for="loginDirec">Login</label>
                    <input minlength="4" type="text" class="form-control text-center modal-sm" name="loginDirec" placeholder="Login" required="">
                    <label for="emailDirec">Email</label>
                    <input type="email" class="form-control text-center modal-sm" name="emailDirec" placeholder="Email" required="">
                    <label for="pwdDirec">Mot de passe</label>
                    <input minlength="4" type="password" class="form-control text-center modal-sm" id="pwdDirec" name="pwdDirec" placeholder="Mot de passe" required="" onkeyup=checkPwd("Direc")>
                    <input minlength="4" type="password" class="form-control text-center modal-sm" id="pwdConfDirec" name="pwdConfirmDirec" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Direc")>
                    <label for="codeADEDirec"> Code ADE</label>
                    <input type="text" class="form-control text-center modal-sm" placeholder="Code ADE" name="codeDirec" required="">
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

		return $this->displayAll($name, $title, $header, $row);
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