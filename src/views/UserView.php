<?php

namespace Views;


use Models\CodeAde;
use Models\User;

class UserView extends View
{

    /**
     * Affiche un formulaire classique
     * @param $name     string Nom du rôle à inscrire
     * @return string   Renvoie le formulaire
     */
    protected function displayBaseForm($name)
    {
        $string = '
            <form method="post" class="cadre">
                <label for="login' . $name . '">Login</label>
                <input minlength="4" type="text" name="login' . $name . '" placeholder="Login" required="">
                <label for="email' . $name . '">Email</label>
                <input type="email" name="email' . $name . '" placeholder="Email" required="">
                <label for="pwd' . $name . '">Mot de passe</label>
                <input minlength="4" type="password" id="pwd' . $name . '" name="pwd' . $name . '" placeholder="Mot de passe" required="" onkeyup=checkPwd("' . $name . '")>
                <input minlength="4" type="password" id="pwdConf' . $name . '" name="pwdConfirm' . $name . '" placeholder="Confirmer le Mot de passe" required="" onkeyup=checkPwd("' . $name . '")>
                <input type="submit" id="valid' . $name . '" name="create' . $name . '">
            </form>';
        return $string;
    }

    /**
     * Fin de formulaire pour modifier son mot de passe
     * @return string   Renvoie la fin du formulaire
     */
    public function displayModifyPassword()
    {
        return '
            <form id="check" method="post">
                <h2>Modifier le mot de passe</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
                <label for="newPwd">Votre nouveau mot de passe</label>
                <input type="password" class="form-control text-center" name="newPwd" placeholder="Mot de passe" required="">
                <button type="submit"  name="modifyMyPwd"> Modifier </button>
            </form>';
    }

    /**
     * Fin de formulaire pour envoyer un code de suppression de compte
     * @return string   Renvoie la fin de formulaire
     */
    public function displayDeleteAccount()
    {
        return '
            <form id="check" method="post">
                <h2>Supprimer le compte</h2>
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
                <button type="submit" name="deleteMyAccount">Confirmer</button>
            </form>';
    }

    /**
     * Formulaire pour supprimer son compte
     * @return string   Renvoie le formulaire
     */
    public function displayEnterCode()
    {
        return '
        <form method="post">
            <label for="codeDelete"> Code de suppression de compte</label>
            <input type="text" class="form-control text-center" name="codeDelete" placeholder="Code à rentrer" required="">
            <button type="submit" name="deleteAccount">Supprimer</button>
        </form>';
    }

    /**
     * Affiche un modal signalant le succès de la modification de mot de passe
     */
    public function displayModificationPassValidate()
    {
        $this->displayStartModal('Modification du mot de passe');
        echo '<div class="alert alert-success" role="alert">La modification à été réussie !</div>';
        $this->displayEndModal(home_url());
    }

    /**
     * Affiche un modal signalant que le mot de passe entré est incorrect
     */
    public function displayWrongPassword()
    {
        $this->displayStartModal('Mot de passe incorrect');
        echo '<div class="alert alert-danger"> Mauvais mot de passe </div>';
        $this->displayEndModal();
    }

    /**
     * Affiche un modal signalant qu'un email a été envoyé
     */
    public function displayMailSend()
    {
        $this->displayStartModal('Mail envoyé');
        echo '<div class="alert alert-success"> Un mail a été envoyé à votre adresse mail, merci de bien vouloir entrer le code reçu</div>';
        $this->displayEndModal();
    }

    /**
     * Affiche le bouton d'abonnement aux notifications
     */
    public function displayButtonSubscription()
    {
        //return '<a href="#" id="my-notification-button" class="btn btn-danger">recevoirNotifications</a></br>';
    }

    /**
     * Display a form to change our own codes
     *
     * @param $codes        CodeAde[]
     * @param $years        CodeAde[]
     * @param $groups       CodeAde[]
     * @param $halfGroups   CodeAde[]
     *
     * @return string
     */
    public function displayModifyMyCodes($codes, $years, $groups, $halfGroups)
    {
        $form = '
        <form method="post">
            <h2> Modifier mes emplois du temps</h2>
            <label>Année</label>
            <select class="form-control" name="modifYear">';
        if(!empty($codes[0])) {
        	$form .= '<option value="' . $codes[0]->getCode() . '">' . $codes[0]->getTitle() . '</option>';
        }

        $form .= '<option value="0">Aucun</option>
				  <optgroup label="Année">';

        foreach ($years as $year) {
	        $form .= '<option value="' . $year->getCode() . '">' . $year->getTitle() . '</option >';
        }
	    $form .= '
                </optgroup>
            </select>
            <label>Groupe</label>
            <select class="form-control" name="modifGroup">';

	    if(!empty($codes[1])) {
		    $form .= '<option value="' . $codes[1]->getCode() . '">' . $codes[1]->getTitle() . '</option>';
	    }
	    $form .= '<option value="0">Aucun</option>
                  <optgroup label="Groupe">';

        foreach ($groups as $group) {
	        $form .= '<option value="' . $group->getCode() . '">' . $group->getTitle() . '</option>';
        }
	    $form .= '
                </optgroup>
            </select>
            <label>Demi-groupe</label>
            <select class="form-control" name="modifHalfgroup">';

	    if(!empty($codes[2])) {
		    $form .= '<option value="' . $codes[2]->getCode() . '">' . $codes[2]->getTitle() . '</option>';
	    }
	    $form .= '<option value="0"> Aucun</option>
                  <optgroup label="Demi-Groupe">';

        foreach ($halfGroups as $halfGroup) {
	        $form .= '<option value="' . $halfGroup->getCode() . '">' . $halfGroup->getTitle() . '</option>';
        }
	    $form .= '
                </optgroup>
            </select>
            <input name="modifvalider" type="submit" value="Valider">
         </form>';

        return $form;
    }

    /**
     * Message indiquant de choisir un emploi du temps
     */
    public function displaySelectSchedule()
    {
        return '<p>Veuillez choisir un emploi du temps.</p>';
    }

    public function displayHome()
    {
        echo '<article>
                <h1>' . get_bloginfo("name") . '</h1>
                <p>Retrouvez ici votre emploi du temps</p>
              </article>';
    }

    public function displayNoStudy() {
        return '<p>Vous n\'avez pas cours! </p>';
    }
}