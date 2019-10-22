<?php
/**
 * Created by PhpStorm.
 * UserView: Rohrb
 * Date: 25/04/2019
 * Time: 10:46
 */

class ViewTelevision extends UserView {

    /**
     * Affiche un select permettant de choisir une année un groupe ou un demi-groupe déjà enregistré
     * @param $years        Années enregistrées dans la base de données
     * @param $groups       Groupes enregistrés dans la base de données
     * @param $halfgroups   Demi-groupes enregistrés dans la base de données
     * @return string       Renvoie le select
     */
    public function displaySelect($years, $groups, $halfgroups){
        $string = '<option value="0">Aucun</option>
                        <optgroup label="Année">';
        if(is_array($years)) {
            foreach ($years as $year) {
                $string .= '<option value="'.$year['code'].'">'.$year['title'].'</option >';
            }
        } else {
            $string .= '<option value="'.$years['code'].'">'.$years['title'].'</option >';
        }
        $string .= '</optgroup>
                        <optgroup label="Groupe">';
        if(is_array($groups)) {
            foreach ($groups as $group){
                $string .= '<option value="'.$group['code'].'">'.$group['title'].'</option>';
            }
        } else {
            $string .= '<option value="'.$groups['code'].'">'.$groups['title'].'</option>';
        }
        $string .= '</optgroup>
                        <optgroup label="Demi groupe">';
        if(is_array($halfgroups)) {
            foreach ($halfgroups as $halfgroup){
                $string .= '<option value="'.$halfgroup['code'].'">'.$halfgroup['title'].'</option>';
            }
        } else {
            $string .= '<option value="'.$halfgroups['code'].'">'.$halfgroups['title'].'</option>';
        }
        $string .= '</optgroup>
                </select>';
        return $string;
    }

    /**
     * Affiche un formulaire pour ajouter un compte télévision
     * On peut y ajouter autant d'emploi du temps que l'on souhaite
     * @param $years        Années enregistrées dans la base de données
     * @param $groups       Groupes enregistrés dans la base de données
     * @param $halfgroups   Demi-groupes enregistrés dans la base de données
     * @return string   Renvoie le formulaire
     */
    public function displayFormTelevision($years, $groups, $halfgroups) {
        return '
        <h1> Création compte télévision</h1>
         <div class="cadre">
            <div align="center">
                <form method="post" id="registerTvForm">
                    <label for="loginTv">Login</label>
                    <input type="text" class="form-control text-center modal-sm" name="loginTv" placeholder="Nom de compte" required="">
                    <label for="pwdTv">Mot de passe</label>
                    <input type="password" class="form-control text-center modal-sm" id="pwdTv" name="pwdTv" placeholder="Mot de passe" required="" onkeyup=checkPwd("Tv")>
                    <input type="password" class="form-control text-center modal-sm" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("Tv")>
                    <label>Premier emploi du temps</label>
                    <select class="form-control firstSelect" name="selectTv[]" required="">'.
        $this->displaySelect($years, $groups, $halfgroups). '
                <input type="button" onclick="addButtonTv()" value="Ajouter des emplois du temps">
                    <input type="submit" id="validTv" name="createTv" value="Créer">
                </form>
            </div> 
         </div>';
    }

    /**
     * En-tête du tableau des télévisions
     * @return string   Renvoie l'en-tête
     */
    public function displayHeaderTabTv(){
        $title = "Télévisions";
        $tab = ["Login", "Nombre d'emploi du temps"];
        return $this->displayStartTab('tele', $tab, $title);
    }

    /**
     * Affiche une ligne dans un tableau contenant le nombre d'emploi du temps affichés par la télévision
     * @param $id       ID de la télévision
     * @param $login    Login de la télévision
     * @param $nbCode   Nombre d'emploi du temps dans la télévision
     * @param $row      Numéro de ligne
     * @return string   Renvoie la ligne
     */
    public function displayAllTv($id, $login,  $nbCode, $row){
        $page = get_page_by_title( 'Modification utilisateur');
        $linkModifyUser = get_permalink($page->ID);
        $tab = [$login, $nbCode];
        return $this->displayAll($row, 'tele', $id, $tab).
        '<td class="text-center"> <a href="'.$linkModifyUser.$id.'" name="modif" type="submit" value="Modifier">Modifier</a></td>
        </tr>';
    }

    /**
     * Affiche un select avec comme valeur par défaut celle choisit
     * @param $years        Années enregistrées dans la base de données
     * @param $groups       Groupes enregistrés dans la base de données
     * @param $halfgroups   Demi-groupes enregistrés dans la base de données
     * @param $name         Code par défaut
     * @return string       Renvoie le select
     */
    public function displaySelectSelected($years, $groups, $halfgroups, $name){
        $selected = $name;
        $string = '<option value="0">Aucun</option>
                        <optgroup label="Année">';
        if(is_array($years)) {
            foreach ($years as $year) {
                $string .= '<option value="'.$year['code'].'" '; if($year['code'] == $selected) $string .= "selected"; $string .='>'.$year['title'].'</option >';
            }
        } else {
            $string .= '<option value="'.$years['code'].'" '; if($years['code'] == $selected) $string .= "selected"; $string .='>'.$years['title'].'</option >';
        }
        $string .= '</optgroup>
                          <optgroup label="Groupe">';
        if(is_array($groups)) {
            foreach ($groups as $group){
                $string .= '<option value="'.$group['code'].'"'; if($group['code'] == $selected) $string .= "selected"; $string .='>'.$group['title'].'</option>';
            }
        } else {
            $string .= '<option value="'.$groups['code'].'"'; if($groups['code'] == $selected) $string .= "selected"; $string .='>'.$groups['title'].'</option>';
        }
        $string .= '</optgroup>
                          <optgroup label="Demi groupe">';
        if(is_array($halfgroups)) {
            foreach ($halfgroups as $halfgroup){
                $string .= '<option value="'.$halfgroup['code'].'" '; if($halfgroup['code'] == $selected) $string .= "selected"; $string .='>'.$halfgroup['title'].'</option>';
            }
        } else {
            $string .= '<option value="'.$halfgroups['code'].'" '; if($halfgroups['code'] == $selected) $string .= "selected"; $string .='>'.$halfgroups['title'].'</option>';
        }
        $string .= '</optgroup>
        </select>';
        return $string;
    }

    /**
     * Affiche le formulaire pour modifier une télévision
     * @param $result       Données de la télévision
     * @param $years        Années enregistrées dans la base de données
     * @param $groups       Groupes enregistrés dans la base de données
     * @param $halfgroups   Demi-groupes enregistrés dans la base de données
     */
    public function displayModifyTv($result, $years, $groups, $halfgroups){
        $codes = unserialize($result->code);
        $count = 0;
        echo '
         <h3>'.$result->user_login.'</h3>
         <div class="cadre">
         <div align="center">
         <form method="post" id="registerTvForm">
            <label>Nouveau mot de passe </label>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdTv" name="pwdTv" placeholder="Nouveau mot de passe" onkeyup=checkPwd("Tv")>
            <input  minlength="4" type="password" class="form-control text-center modal-sm" id="pwdConfTv" name="pwdConfirmTv" placeholder="Confirmer le nouveau mot de passe" onkeyup=checkPwd("Tv")>
            <label> Emploi du temps</label>';
        if(is_array($codes)){
            foreach ($codes as $code) {
                $count = $count + 1;
                if($count == 1){
                    echo '<select class="form-control firstSelect" name="selectTv[]" id="selectId'.$count.'">'.
                    $this->displaySelectSelected($years, $groups, $halfgroups, $code).
                    '<br/>';
                } else {
                    echo '<div class="row">'.
                    '<select class="form-control select" name="selectTv[]" id="selectId'.$count.'">'.
                     $this->displaySelectSelected($years, $groups, $halfgroups, $code).
                     '<input type="button" id="selectId'.$count.'" onclick="deleteRow(this.id)" class="selectbtn" value="Supprimer"></div>';
                }
            }
        } else {
            echo '<select class="form-control firstSelect" name="selectTv[]" id="selectId'.$count.'">'.
            $this->displaySelectSelected($years, $groups, $halfgroups, $codes).
            '<br/>';
        }

        $page = get_page_by_title( 'Gestion des utilisateurs');
        $linkManageUser = get_permalink($page->ID);
        echo '
            <input type="button" onclick="addButtonTv()" value="Ajouter des emplois du temps">
            <input name="modifValidate" type="submit" id="validTv" value="Valider">
            <a href="'.$linkManageUser.'">Annuler</a>
         </form>
         </div>
         </div>';
    }

    /**
     * Modal qui signale que le login est déjà utilisé
     */
    public function displayErrorLogin(){
        $this->displayStartModal('Inscription échouée');
        echo '<div class="alert alert-danger"> Le login est déjà utilisé ! </div>';
        $this->displayEndModal();
    }
}