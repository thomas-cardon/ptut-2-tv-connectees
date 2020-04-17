<?php

namespace Views;


use Models\User;

/**
 * Class TeacherView
 *
 * Contain all view for teacher (Forms, tables)
 *
 * @package Views
 */
class TeacherView extends UserView
{

    /**
     * Display a creation form
     */
    public function displayInsertImportFileTeacher()
    {
	    return '
        <h2>Compte enseignant</h2>
        <a href="' . TV_PLUG_PATH . 'public/files/Ajout Profs.xlsx"
            download="Ajout Prof.xlsx">Télécharger le fichier excel ! </a>
        <form id="Prof" method="post" enctype="multipart/form-data">
            <input type="file" name="excelProf" class="inpFil" required=""/>
            <button type="submit" name="importProf" value="Importer">Importer le fichier</button>
        </form>';
    }

	/**
	 * Display form to modify a teacher
	 *
	 * @param $user   User
	 *
	 * @return string
	 */
	public function modifyForm($user)
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

	/**
	 * Display all teachers in a table
	 *
	 * @param $teachers    User[]
	 *
	 * @return string
	 */
	public function displayAllTeachers($teachers)
	{
		$page = get_page_by_title('Modifier un utilisateur');
		$linkManageUser = get_permalink($page->ID);

		$title = 'Enseignants';
		$name = 'teacher';
		$header = ['Numéro Ent', 'Code ADE', 'Modifier'];

		$row = array();
		$count = 0;
		foreach ($teachers as $teacher) {
			++$count;
			$row[] = [$count, $this->buildCheckbox($name, $teacher->getId()), $teacher->getLogin(), $teacher->getCodes()[0]->getCode(), $this->buildLinkForModify($linkManageUser.'/'.$teacher->getId())];
		}

		return $this->displayAll($name, $title, $header, $row, 'teacher');
	}
}