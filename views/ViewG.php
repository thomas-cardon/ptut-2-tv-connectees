<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 26/04/2019
 * Time: 08:49
 */

/**
 * Vue générale, rassemble toutes les vues utilisées par plusieurs vues
 * Class ViewG
 */
abstract class ViewG {

    /**
     * Affiche l'en-tête d'un tableau
     * @param null $title   Titre du tableau
     * @return string       Renvoi l'en-tête
     */
    protected function displayHeaderTab($name, $title = null){
        $name = "'$name'";
        return '
            <h2>'.$title.'</h2>
            <form method="post">
                <div class="table-responsive">
                <table class="table text-center"> 
                <thead>
                    <tr class="text-center">
                        <th scope="col" width="5%" class="text-center">#</th>
                        <th scope="col" width="5%" class="text-center"><input type="checkbox" onClick="toggle(this, '.$name.')" /></th>';
    }

    /**
     * Affiche l'en-tête d'un tableau avec comme seule colonne Login
     * @param $title    Titre du tableau
     * @return string   Renvoie l'en-tête
     */
    protected function displayStartTabLog($name, $title){
        return $this->displayHeaderTab($name, $title).
        '<th scope="col">Login</th>
                    </tr>
                </thead>
                <tbody>';
    }

    /**
     * Affiche l'en-tête avec les noms de colonnes
     * @param $tab          Noms des colonnes
     * @param null $title   Titre du tableau
     * @return string       Renvoie l'en-tête
     */
    protected function displayStartTab($name, $tab, $title = null){
        $string = $this->displayHeaderTab($name, $title);
        foreach ($tab as $value){
            $string .= '<th scope="col" class="text-center"> '.$value.'</th>';
        }
        $string .= $this->displayEndheaderTab();
        return $string;
    }

    /**
     * Ferme le tableau
     * @return string   Renvoie la fermeture du tableau
     */
    protected function displayEndheaderTab(){
        return'
                <th scope="col" class="text-center">Modifer</th>
                     </tr>
                </thead>
                <tbody>
        ';
    }


    /**
     * Affiche le contenu d'une ligne du tableau
     * @param $row      Numéro de ligne du tableau
     * @param $id       ID de l'objet mis dans le tableau, permet de pouvoir supprimer la ligne via des checkbox
     * @param $tab      Valeur à mettre par colonne
     * @return string   Renvoie la ligne
     */
    protected function displayAll($row, $name, $id, $tab){
        $string = '
        <tr>
          <th scope="row" class="text-center">'.$row.'</th>
          <td class="text-center"><input type="checkbox" name="checkboxstatus'.$name.'[]" value="'.$id.'"/></td>';
        if(isset($tab)){
            foreach ($tab as $value){
                $string .= '<td class="text-center">'.$value.'</td>';
            }
        }
        return $string;
    }


    /**
     * Ferme le tableau avec un bouton de suppression
     * @return string   Renvoie la fin du tableau
     */
    public function displayEndTab(){
        return '
          </tbody>
        </table>
        </div>
        <input type="submit" value="Supprimer" name="Delete" onclick="return confirm(\' Voulez-vous supprimer le(s) élément(s) sélectionné(s) ?\');"/>
        </form>';
    }

    public function displayStartMultiSelect() {
        return '<nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">';
    }

    public function displayTitleSelect($id, $title, $active = false){
        $string = '<a class="nav-item nav-link'; if($active) $string .= ' active'; $string .= '" id="nav-'.$id.'-tab" data-toggle="tab" href="#nav-'.$id.'" role="tab" aria-controls="nav-'.$id.'" aria-selected="false">'.$title.'</a>';
        return $string;
    }

    public function displayEndOfTitle() {
        return '</div>
        </nav>
        <br/>
        <div class="tab-content" id="nav-tabContent">';
    }

    public function displayContentSelect($id, $content, $active = false) {
        $string = '<div class="tab-pane fade show'; if($active) $string .= ' active'; $string .= '" id="nav-'.$id.'" role="tabpanel" aria-labelledby="nav-'.$id.'-tab">'.$content.'</div>';
        return $string;
    }

    /**
     * Relance la page
     */
    public function refreshPage(){
        echo '<meta http-equiv="refresh" content="0">';
    }

    /**
     * Affiche les codes non enregistrées dans un tableau
     * @param $badCodes Code non enregistrés dans un tableau de trois colonnes Année - Groupe - Demi-groupe
     * @return string   Renvoie le tableau
     */
    public function displayUnregisteredCode($badCodes){
        if(! is_null($badCodes[0]) || ! is_null($badCodes[1]) || ! is_null($badCodes[2])) {
            $string = '
        <h3> Ces codes ne sont pas encore enregistrés ! </h3>
        <table class="table text-center"> 
                <thead>
                    <tr class="text-center">
                        <th scope="col" width="33%" class="text-center">Année</th>
                        <th scope="col" width="33%" class="text-center">Groupe</th>
                        <th scope="col" width="33%" class="text-center">Demi-Groupe</th>
                        </tr>
                </thead>
                <tbody>';
            if(is_null($badCodes[0])){
                $sizeYear = 0;
            } else {
                $sizeYear = sizeof($badCodes[0]);
            }
            if(is_null($badCodes[1])){
                $sizeGroup = 0;
            } else {
                $sizeGroup = sizeof($badCodes[1]);
            }
            if(is_null($badCodes[2])){
                $sizeHalfgroup = 0;
            } else {
                $sizeHalfgroup = sizeof($badCodes[2]);
            }
            $size = 0;
            if($sizeYear >= $sizeGroup && $sizeYear >= $sizeHalfgroup) $size = $sizeYear;
            if($sizeGroup >= $sizeYear && $sizeGroup >= $sizeHalfgroup) $size = $sizeGroup;
            if($sizeHalfgroup >= $sizeYear && $sizeHalfgroup >= $sizeGroup) $size = $sizeHalfgroup;
            for($i = 0; $i < $size; ++$i){
                $string .= '<tr>
                    <td class="text-center">';
                if($sizeYear > $i)
                    $string .= $badCodes[0][$i];
                else
                    $string .= ' ';
                $string .= '</td>
            <td class="text-center">';
                if($sizeGroup > $i)
                    $string .= $badCodes[1][$i];
                else
                    $string .= ' ';
                $string .= '</td>
            <td class="text-center">';
                if($sizeHalfgroup > $i)
                    $string .= $badCodes[2][$i];
                else
                    $string .= ' ';
                $string .= '</td>
                  </tr>';
            }
            $string .= '
                </tbody>
        </table>
        ';
            return $string;
        }
    }

    /**
     * Affiche le début d'un modal
     * @param $title    string Titre du modal
     */
    protected function displayStartModal($title){
        echo '
        <div class="modal" id="myModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">'.$title.'</h5>
              </div>
              <div class="modal-body">';
    }

    /**
     * Fin du modal
     * @param null $redirect    Redirection lorsqu'on clique si Fermer
     */
    protected function displayEndModal($redirect = null){
        echo '</div>
              <div class="modal-footer">';
        if(empty($redirect)){
        echo '<button type="button" onclick="closeModal()">Fermer</button>';
        } else {
            echo '<button type="button" onclick="document.location.href =\' '.$redirect.' \'">Fermer</button>';
        }
        echo '</div>
            </div>
          </div>
        </div>
        
        <script> $("#myModal").show() </script>';
    }

    public function displayTest() {
        echo '<div class="alert alert-danger"> Cette fonctionnalitée est en test ! </div>';
    }

    /**
     * Prévient s'il n'y a pas d'utilisateur du rôle demandé inscrit
     */
    public function displayEmpty() {
        return "<div> Il n'y pas d'utilisateur de ce rôle inscrit!</div>";
    }

    /**
     * Affiche un modal signalant les personnes n'ont enregistrés du à un email ou login déjà utilisé
     * @param $doubles  Array de login
     */
    public function displayErrorDouble($doubles){
        $this->displayStartModal('Erreur durant l\'incription ');
        foreach ($doubles as $double) {
            echo "<div class='alert alert-danger'>$double a rencontré un problème lors de l'enregistrement, vérifié son login et son email ! </div>";
        }
        $this->displayEndModal();
    }

    /**
     * Affiche un modal, signalant le succès de l'inscription
     */
    public function displayInsertValidate() {
        $this->displayStartModal('Inscription validée');
        echo "<p class='alert alert-success'>Votre inscription a été validée. </p>";
        $this->displayEndModal();
    }

    /**
     * Affiche un modal, signalant que le fichier à une mauvaise extension
     */
    public function displayWrongExtension() {
        $this->displayStartModal('Mauvais fichier !');
        echo '<p class="alert alert-danger"> Mauvaise extension de fichier ! </p>';
        $this->displayEndModal();
    }

    /**
     * Affiche un modal, signalant que ce n'est pas le bon fichier Excel
     */
    public function displayWrongFile() {
        $this->displayStartModal('Mauvais fichier !');
        echo '<p class="alert alert-danger"> Vous utilisez un mauvais fichier excel / ou vous avez changé le nom des colonnes </p>';
        $this->displayEndModal();
    }

    /**
     * Affiche un modal signalant que la modification à été réussie
     */
    public function displayModificationValidate($redirect = null) {
        $this->displayStartModal('Modification réussie');
        echo '<div class="alert alert-success"> La modification a été appliquée </div>';
        $this->displayEndModal($redirect);
    }

    /**
     * Affiche un modal signalant que l'inscription d'un/des utilisateur(s) a échouée
     */
    public function displayErrorInsertion() {
        $this->displayStartModal('Erreur lors de l\'inscription ');
        echo '<div class="alert alert-danger"> Le login ou l\'adresse mail est déjà utilisé(e) </div>';
        $this->displayEndModal();
    }

    /**
     * Ajoute une ligne
     */
    public function displayRow() {
        echo '<div class="row">';
    }

    /**
     * Ferme une div
     */
    public function displayEndDiv() {
        echo '</div>';
    }

    /**
     * Affiche un modal qui signal que la confirmation de mot de passe lors de l'inscription a échoué
     */
    public function displayBadPassword() {
        $this->displayStartModal("Mauvais mot de passe");
        echo "<div class='alert alert-danger'>Les deux mots de passe ne sont pas correctes </div>";
        $this->displayEndModal();
    }
}