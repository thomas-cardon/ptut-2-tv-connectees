<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 29/04/2019
 * Time: 09:54
 */

class ViewCodeAde extends ViewG
{

    /**
     * Affiche un formulaire permettant d'ajouter un code ADE
     * @return string   Renvoie le formulaire
     */
    public function displayFormAddCode(){
        return '
                <form method="post">
                    <label for="titleAde">Titre</label>
                    <input type="text" class="form-control text-center modal-sm" name="titleAde" placeholder="Titre" required="">
                    <label for="codeAde">Code ADE</label>
                    <input type="text" class="form-control text-center modal-sm" name="codeAde" placeholder="Code ADE" required="">
                    <label for="Annee">Année</label>
                    <input type="radio" name="typeCode" id="Annee" value="Annee"> 
                    <label for="Groupe">Groupe</label>
                    <input type="radio" name="typeCode" id="Groupe" value="Groupe">
                    <label for="Demi-groupe">Demi-groupe</label>
                    <input type="radio" name="typeCode" id="Demi-groupe" value="Demi-groupe">
                    <br/>
                  <button type="submit" name="addCode" value="Valider">Ajouter</button>
                </form>';
    }

    /**
     * En-tête du tableau des code ADE
     * @return string   Renvoie l'en-tête
     */
    public function displayTableHeadCode(){
        $tab = ["Titre", "Code ADE", "Type"];
        return $this->displayStartTab('code', $tab);
    }

    /**
     * Affiche toutes les données d'un code
     * @param $result   array Données du code ADE
     * @param $row      int Numéro de la ligne
     * @return string   Renvoie la ligne de tableau
     */
    public function displayAllCode($result, $row){
        $page = get_page_by_title( 'Modification code ADE');
        $linkModifyCode = get_permalink($page->ID);
        $tab = [$result['title'], $result['code'], $result['type']];
        return $this->displayAll($row, 'code',$result['ID'], $tab).'<td class="text-center"> <a href="'.$linkModifyCode.$result['ID'].'" name="modifCode" type="submit">Modifier</a></td>
                </tr>';
    }

    /**
     * Affiche le formulaire de modification de code ADE
     * @param $result   array Données du code ADE non modifié
     * @return string   Renvoie le formulaire
     */
    public function displayModifyCode($result){
        $page = get_page_by_title( 'Gestion des codes ADE');
        $linkManageCode = get_permalink($page->ID);
        return '
         <form method="post">
            <label>Titre</label>
            <input name="modifTitle" type="text" class="form-control" placeholder="Titre" value="'.$result[0]['title'].'">
            <label>Code</label>
            <input name="modifCode" type="text" class="form-control" placeholder="Code" value="'.$result[0]['code'].'">
            <div class="form-group">
            <label for="exampleFormControlSelect1">Selectionner un type</label>
                <select class="form-control" name="modifType">
                    <option>'.$result[0]['type'].'</option>
                    <option>Annee</option>
                    <option>Groupe</option>
                    <option>Demi-Groupe</option>
                </select>
            </div>
            <input name="modifCodeValid" type="submit" value="Valider">
            <a href="'.$linkManageCode.'">Annuler</a>
         </form>';
    }

    /**
     * Envoie un message d'erreur si le titre ou le code existe déjà
     */
    public function displayErrorDoubleCode(){
        echo '<div class="alert alert-danger"> Ce code ou ce titre existe déjà ! </div>';
    }

    /**
     * Signal qu'il n'y a pas de code inscrit
     */
    public function displayEmptyCode() {
        echo '<div> Il n\'y a pas de code ajouté!';
    }
}