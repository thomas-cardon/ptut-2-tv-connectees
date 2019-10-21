<?php
/**
 * Created by PhpStorm.
 * User: Rohrb
 * Date: 06/05/2019
 * Time: 08:42
 */

class ViewMyAccount extends ViewG
{
    /**
     * Début de formulaire qui vérifie le mot de passe
     * @return string   Renvoie le début du formulaire
     */
    public function displayVerifyPassword(){
        return '
          <div class="cadre">
            <form id="check" method="post">
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">';
    }

    /**
     * Fin de formulaire pour modifier son mot de passe
     * @return string   Renvoie la fin du formulaire
     */
    public function displayModifyPassword(){
        return '
                <label for="newPwd">Votre nouveau mot de passe</label>
                <input type="password" class="form-control text-center" name="newPwd" placeholder="Mot de passe" required="">
                <button type="submit"  name="modifyMyPwd"> Modifier </button>
            </form>
          </div>';
    }

    /**
     * Fin de formulaire pour envoyer un code de suppression de compte
     * @return string   Renvoie la fin de formulaire
     */
    public function displayDeleteAccount(){
        return '
                <button type="submit" name="deleteMyAccount">Confirmer</button>
                </form>
                </div>';
    }

    /**
     * Formulaire pour supprimer son compte
     * @return string   Renvoie le formulaire
     */
    public function displayEnterCode(){
        return '
      <div class="cadre">
        <form method="post">
            <label for="codeDelete"> Code de suppression de compte</label>
            <input type="text" class="form-control text-center" name="codeDelete" placeholder="Code à rentrer" required="">
            <button type="submit" name="deleteAccount">Supprimer</button>
        </form>
      </div>';
    }

    /**
     * Affiche un modal signalant le succès de la modification de mot de passe
     */
    public function displayModificationPassValidate(){
        $this->displayStartModal('Modification du mot de passe');
        echo '<div class="alert alert-success" role="alert">La modification à été réussie !</div>';
        $this->displayEndModal(home_url());
    }

    /**
     * Affiche un modal signalant que le mot de passe entré est incorrect
     */
    public function displayWrongPassword(){
        $this->displayStartModal('Mot de passe incorrect');
        echo '<div class="alert alert-danger"> Mauvais mot de passe </div>';
        $this->displayEndModal();
    }

    /**
     * Affiche un modal signalant qu'un email a été envoyé
     */
    public function displayMailSend(){
        $this->displayStartModal('Mail envoyé');
        echo '<div class="alert alert-success"> Un mail a été envoyé à votre adresse mail, merci de bien vouloir entrer le code reçu</div>';
        $this->displayEndModal();
    }

    public function displayButtonSubscription(){
        //return '<a href="#" id="my-notification-button" class="btn btn-danger">recevoirNotifications</a></br>';
    }

    public function displayModifyMyCodes($result, $years, $groups, $halfgroups){
        $code = unserialize($result->code);
        $model = new CodeAdeManager();
        $titleYear = $model->getTitle($code[0]);
        $titleGroup = $model->getTitle($code[1]);
        $titleHalfgroup = $model->getTitle($code[2]);
        $string = '
        <h1> Modifier mes groupes</h1>
        <div class="cadre">
         <form method="post">
            <label>Année</label>
            <select class="form-control" name="modifYear">
                <option value="'.$code[0].'">'.$titleYear.'</option>
                <option value="0">Aucun</option>
                <optgroup label="Année">';
        $selected = $_POST['modifYear'];
        if(is_array($years)) {
            foreach ($years as $year) {
                $string .= '<option value="'.$year['code'].'"'; if($year['code'] == $selected) $string .= "selected"; $string .='>'.$year['title'].'</option >';
            }
        }

        $string .= '
            </optgroup>
            </select>
            <label>Groupe</label>
            <select class="form-control" name="modifGroup">
                <option value="'.$code[1].'">'.$titleGroup.'</option>
                <option value="0">Aucun</option>
                <optgroup label="Groupe">';
        $selected = $_POST['modifGroup'];
        if(is_array($groups)) {
            foreach ($groups as $group){
                $string .= '<option value="'.$group['code'].'"'; if($group['code'] == $selected) $string .= "selected"; $string .='>'.$group['title'].'</option>';
            }
        }
        $string .= '
            </optgroup>
            </select>
            <label>Demi-groupe</label>
            <select class="form-control" name="modifHalfgroup">
                <option value="'.$code[2].'">'.$titleHalfgroup.'</option>
                <option value="0"> Aucun</option>
                <optgroup label="Demi-Groupe">';
        $selected = $_POST['modifHalfgroup'];
        if(is_array($halfgroups)) {
            foreach ($halfgroups as $halfgroup){
                $string .= '<option value="'.$halfgroup['code'].'"'; if($halfgroup['code'] == $selected) $string .= "selected"; $string .='>'.$halfgroup['title'].'</option>';
            }
        }
        $string .= '
            </optgroup>
            </select>
            <input name="modifvalider" type="submit" value="Valider">
         </form>
         </div>';
        return $string;
    }
}