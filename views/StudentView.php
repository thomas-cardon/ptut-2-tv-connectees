<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:44
 */

class StudentView extends UserView
{

    /**
     * Affiche le formulaire d'inscription des étudiants
     * @return string
     */
    public function displayFormInscription()
    {
        return $this->displayBaseForm('Etu');
    }

    /**
     * Renvoie un formulaire d'inscription par fichier Excel pour les étudiants
     * @return string   Renvoie le formulaire
     */
    public function displayInsertImportFileStudent()
    {
        return '<h2> Comptes étudiants</h2>' . $this->displayInsertImportFile("Etu");
    }

    /**
     * En-tête du tableau des étudiants
     * @return string   Renvoie l'en-tête
     */
    public function displayTabHeadStudent()
    {
        $title = "Étudiants";
        $tab = ["Numéro étudiant", "Année", "Groupe", "Demi groupe"];
        return $this->displayStartTab('etu', $tab, $title);
    }

    /**
     * Affiche une ligne contenant les données de l'étudiant
     * Pour les trois codes ADE, on affiche normaelement le titre, mais si ce n'est pas le cas, met le code en rouge
     * @param $id           int ID de l'étudiant
     * @param $login        string Login de l'étudiant
     * @param $year         int Code ADE de son année
     * @param $group        int Code ADE de son groupe
     * @param $halfgroup    int Code ADE de son demi-groupe
     * @param $row          int Numéro de la ligne
     * @return string       Renvoie la ligne
     */
    public function displayAllStudent($id, $login, $year, $group, $halfgroup, $row)
    {
        $page = get_page_by_title('Modification utilisateur');
        $linkModifyUser = get_permalink($page->ID);
        $string = '
        <tr>
          <th scope="row" class="text-center">' . $row . '</th>
          <td class="text-center"><input type="checkbox" name="checkboxstatusetu[]" value="' . $id . '"/></td>
          <td class="text-center">' . $login . '</td>
          <td class="text-center';
        if (is_numeric($year)) $string .= ' errorNotRegister';
        $string .= '">' . $year . '</td>
          <td class="text-center';
        if (is_numeric($group)) $string .= ' errorNotRegister';
        $string .= '">' . $group . '</td>
          <td class="text-center';
        if (is_numeric($halfgroup)) $string .= ' errorNotRegister';
        $string .= '">' . $halfgroup . '</td>
          <td class="text-center"> <a href="' . $linkModifyUser . $id . '" name="modif" type="submit" value="Modifier">Modifier</a></td>
        </tr>';
        return $string;
    }

    /**
     * Indique la signification du code en rouge
     * @return string   Renvoie la signification
     */
    public function displayRedSignification()
    {
        return '<p class="red">Zone rouge = Code ADE non enregistré</p>';
    }

    /**
     * Affiche un formulaire pour modifier l'étudiant
     * Pour modifier les codes ADE, on les modifies via les codes déjà présent dans la base de données
     * @param $result       WP_User Données de l'étudiant
     * @param $years        array Toutes les années enregistrées
     * @param $groups       array Tous les groupes enregistrés
     * @param $halfgroups   array Tous les demi-groupes enregistrés
     * @return string
     */
    public function displayModifyStudent($result, $years, $groups, $halfgroups)
    {
        $page = get_page_by_title('Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        $code = unserialize($result->code);
        $model = new CodeAdeModel();
        $titleYear = $model->getCodeTitle($code[0]);
        $titleGroup = $model->getCodeTitle($code[1]);
        $titleHalfgroup = $model->getCodeTitle($code[2]);
        $string = '
         <form method="post">
            <h2>' . $result->user_login . '</h2>
            <label for="modifYear">Année</label>
            <select id="modifYear" class="form-control" name="modifYear">
                <option value="' . $code[0] . '">' . $titleYear . '</option>
                <option value="0">Aucun</option>
                <optgroup label="Année">
            ';
        $selected = $_POST['modifYear'];
        foreach ($years as $year) {
            $string .= '<option value="' . $year['code'] . '"';
            if ($year['code'] == $selected) $string .= "selected";
            $string .= '>' . $year['title'] . '</option >';
        }
        $string .= '
                </optgroup>
            </select>
            <label for="modifGroup">Groupe</label>
            <select id="modifGroup" class="form-control" name="modifGroup">
                <option value="' . $code[1] . '">' . $titleGroup . '</option>
                <option value="0">Aucun</option>
                <optgroup label="Groupe">';
        $selected = $_POST['modifGroup'];
        foreach ($groups as $group) {
            $string .= '<option value="' . $group['code'] . '"';
            if ($group['code'] == $selected) $string .= "selected";
            $string .= '>' . $group['title'] . '</option>';
        }
        $string .= '
                </optgroup>
            </select>
            <label for="modifHalfgroup">Demi-groupe</label>
            <select id="modifHalfgroup" class="form-control" name="modifHalfgroup">
                <option value="' . $code[2] . '">' . $titleHalfgroup . '</option>
                <option value="0"> Aucun</option>
                <optgroup label="Demi-Groupe">';
        $selected = $_POST['modifHalfgroup'];
        foreach ($halfgroups as $halfgroup) {
            $string .= '<option value="' . $halfgroup['code'] . '"';
            if ($halfgroup['code'] == $selected) $string .= "selected";
            $string .= '>' . $halfgroup['title'] . '</option>';
        }
        $string .= '
                </optgroup>
            </select>
            <input name="modifvalider" type="submit" value="Valider">
            <a href="' . $linkManageUser . '">Annuler</a>
         </form>';
        return $string;
    }
}