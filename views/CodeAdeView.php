<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 29/04/2019
 * Time: 09:54
 */

class CodeAdeView extends ViewG {

	/**
	 * Display form for create code ade
	 *
	 * @return string
	 */
    public function displayFormAddCode() {
        return '
                <form method="post">
                	<div class="form-group">
                    	<label for="titleAde">Titre</label>
                    	<input class="form-control" type="text" name="titleAde" placeholder="Titre" required="">
                    </div>
                    <div class="form-group">
                    	<label for="codeAde">Code ADE</label>
                    	<input class="form-control" type="text" name="codeAde" placeholder="Code ADE" required="">
                    </div>
                    <div class="form-group">
	                    <div class="form-check form-check-inline">
		                    <input class="form-check-input" type="radio" name="typeCode" id="Annee" value="Annee"> 
		                    <label class="form-check-label" for="Annee">Année</label>
		                </div>
		                <div class="form-check form-check-inline">
		                    <input class="form-check-input" type="radio" name="typeCode" id="Groupe" value="Groupe">
		                    <label class="form-check-label" for="Groupe">Groupe</label>
		                </div>
		                <div class="form-check form-check-inline">
		                    <input class="form-check-input" type="radio" name="typeCode" id="Demi-groupe" value="Demi-groupe">
		                    <label class="form-check-label" for="Demi-groupe">Demi-groupe</label>
	                    </div>
                    </div>
                  <button type="submit" name="addCode" value="Valider">Ajouter</button>
                </form>';
    }

	/**
	 * Display a form for modify a code ade
	 * @param $title    string
	 * @param $type     string
	 * @param $code     int
	 *
	 * @return string
	 */
	public function displayModifyCode($title, $type, $code) {
		$page = get_page_by_title('Gestion codes ADE');
		$linkManageCode = get_permalink($page->ID);
		return '
         <form method="post">
         	<div class="form-group">
            	<label for="modifTitle">Titre</label>
            	<input class="form-control" type="text" name="modifTitle" id="modifTitle" placeholder="Titre" value="' . $title . '">
            </div>
            <div class="form-group">
            	<label for="modifCode">Code</label>
            	<input type="text" class="form-control" name="modifCode" id="modifCode" placeholder="Code" value="' . $code . '">
            </div>
            <div class="form-group">
            	<label for="modifType">Selectionner un type</label>
             	<select class="form-control" name="modifType" id="modifType">
                    <option>' . $type . '</option>
                    <option>Annee</option>
                    <option>Groupe</option>
                    <option>Demi-Groupe</option>
                </select>
            </div>
            <input name="modifCodeValid" type="submit" value="Valider">
            <a href="' . $linkManageCode . '">Annuler</a>
         </form>';
	}

    /**
     * Header of table
     *
     * @return string   Renvoie l'en-tête
     */
    public function displayTableHeadCode() {
        $tab = ["Titre", "Code ADE", "Type"];
        return $this->displayStartTab('code', $tab);
    }

    /**
     * Display all informations of a code ade
     * @param $id       int
     * @param $title    string
     * @param $type     string
     * @param $code     int
     * @param $row      int
     * @return string
     */
    public function displayAllCode($id, $title, $type, $code, $row) {
        $page = get_page_by_title('Modification code ADE');
        $linkModifyCode = get_permalink($page->ID);
        $tab = [$title, $code, $type];
        return $this->displayRowTable($row, 'code', $id, $tab) . '<td class="text-center"> <a href="' . $linkModifyCode . $id . '">Modifier</a></td>
                </tr>';
    }

    /**
     * Error message if title or code exist
     */
    public function displayErrorDoubleCode() {
        echo '<p class="alert alert-danger"> Ce code ou ce titre existe déjà ! </p>';
    }
}