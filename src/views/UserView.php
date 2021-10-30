<?php

namespace Views;


use Models\CodeAde;
use Models\User;

class UserView extends View
{

  public function getHeader() {
    return $this->renderHeroHeader('Créer un utilisateur', '
      Vous pouvez créer ici les utilisateurs
      Il y a plusieurs types d\'utilisateur : Les <s>étudiants</s>, enseignants, directeurs d\'études, scrétaires, techniciens, télévisions.
      Les étudiants ont accès à leur emploi du temps et reçoivent les alertes les concernants et les informations.
      Les enseignants ont accès à leur emploi du temps et peuvent poster des alertes.
      Les directeurs d\'études ont accès à leur emploi du temps et peuvent poster des alertes et des informations.
      Les secrétaires peuvent poster des alertes et des informations. Ils peuvent aussi créer des utilisateurs.
      Les techniciens ont accès aux emplois du temps des promotions.
      Les télévisions sont les utilisateurs utilisés pour afficher ce site sur les téléviseurs. Les comptes télévisions peuvent afficher autant d\'emploi du temps que souhaité.
    ');
  }


    /**
     * Display a creation form
     *
     * @param $name     string
     *
     * @return string
     */
    protected function displayBaseForm($name) {
        return '
            <form method="post" class="cadre">
            	<div class="form-group">
                	<label for="login' . $name . '">Login</label>
                	<input class="form-control" minlength="4" type="text" name="login' . $name . '" placeholder="Login" required="">
                	<small id="passwordHelpBlock" class="form-text text-muted">Votre login doit contenir entre 4 et 25 caractère</small>
                </div>
                <div class="form-group">
                	<label for="email' . $name . '">Email</label>
                	<input class="form-control" type="email" name="email' . $name . '" placeholder="Email" required="">
                </div>
                <div class="form-group">
                	<label for="pwd' . $name . '">Mot de passe</label>
                	<input class="form-control" minlength="8" maxlength="25" type="password" id="pwd' . $name . '" name="pwd' . $name . '" placeholder="Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                    <input class="form-control" minlength="8" maxlength="25" type="password" id="pwdConf' . $name . '" name="pwdConfirm' . $name . '" placeholder="Confirmer le Mot de passe" minlength="8" maxlength="25" required="" onkeyup=checkPwd("' . $name . '")>
                	<small id="passwordHelpBlock" class="form-text text-muted">Votre mot de passe doit contenir entre 8 et 25 caractère</small>
                </div>
                <button type="submit" class="btn button_ecran" id="valid' . $name . '" name="create' . $name . '">Créer</button>
            </form>';
    }

    /**
     * Form for modify the password
     *
     * @return string
     */
    public function displayModifyPassword() {
        return '<div class="container-sm px-5"
            <form id="check" method="post">
                <h2 class="mb-5">Modifier le mot de passe</h2>
                <div class="mb-3">
                  <label for="oldPwd" class="form-label">Votre ancien mot de passe</label>
                  <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
                </div>
                <div class="mb-3">
                  <label for="newPwd" class="form-label">Votre nouveau mot de passe</label>
                  <input type="password" class="form-control text-center" name="newPwd" placeholder="Mot de passe" required="">
                </div>
                <div class="d-grid">
                  <button class="btn btn-outline-warning" type="submit" name="modifyMyPwd">Modifier</button>
                </div>
            </form></div>';
    }

    /**
     * Form to generate a code to delete the account
     *
     * @return string
     */
    public function displayEnterCode() {
      return '<div class="container-sm px-5"
          <form id="check" method="post">
              <h2>Générer un code de suppression</h2>
              <p class="lead">
                Avant de supprimer votre compte, vous devez générer un code de suppression <b>ici</b>.
              </p>
              <div class="mb-3">
                <label for="verifPwd">Votre mot de passe actuel</label>
                <input type="password" class="form-control text-center" name="verifPwd" placeholder="Mot de passe" required="">
              </div>
              <div class="d-grid">
                <button class="btn btn-outline-danger" type="submit" name="deleteMyAccount">Générer le code requis pour supprimer le compte</button>
              </div>
          </form></div>';
    }

    /**
     * Form to delete the account
     *
     * @return string
     */
    public function displayDeleteAccount() {
      return '<div class="container-sm px-5"
          <form id="check" method="post">
              <h2>Supprimer le compte</h2>
              <p class="lead">
                Pour supprimer votre compte, vous devez avoir généré un code de suppression via l\'onglet éponyme.
              </p>
              <div class="mb-3">
                <input type="text" class="form-control text-center" name="codeDelete" placeholder="Code à rentrer" required="">
              </div>
              <div class="d-grid">
                <button class="btn btn-outline-danger" type="submit" name="deleteAccount">Supprimer le compte</button>
              </div>
          </form></div>';
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
    public function displayModifyMyCodes($codes, $years, $groups, $halfGroups) {
        $form = '
        <form method="post">
            <h2> Modifier mes emplois du temps</h2>
            <label>Année</label>
            <select class="form-control" name="modifYear">';
        if (!empty($codes[0])) {
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

        if (!empty($codes[1])) {
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

        if (!empty($codes[2])) {
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
            <button name="modifvalider" type="submit" class="btn button_ecran">Valider</button>
         </form>';

        return $form;
    }

    /**
     * Display a message to select a schedule
     */
    public function displaySelectSchedule() {
        return '<p>Veuillez choisir un emploi du temps.</p>';
    }

    /**
     * Displays the welcome page
     *
     * @return string
     */
     public function displayContent() {
       return '<section class="container col-xxl-10 py-5">
         <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
           <div class="col-10 col-sm-8 col-lg-6">
             <img draggable="false" src="https://upload.wikimedia.org/wikipedia/fr/thumb/8/83/Univ_Aix-Marseille_-_IUT.svg/1200px-Univ_Aix-Marseille_-_IUT.svg.png" class="d-block mx-lg-auto img-fluid" alt="Bootstrap Themes" loading="lazy" width="700" height="500">
           </div>
           <div class="col-lg-6">
             <h1 class="display-5 fw-bold title-bold">' . get_bloginfo("name") . '</h1>
             <p class="lead">
               Bienvenue sur la page d\'accueil des écrans connectés. Vous manquez de permissions pour accéder aux autres pages.
             </p>
           </div>
         </div>
       </section>';
     }

    /**
     * Display a message for the modification of the password
     */
    public function displayModificationPassValidate() {
        $this->buildModal('Modification du mot de passe', '<div class="alert alert-success" role="alert">La modification à été réussie !</div>', home_url());
    }

    /**
     * Display a message if the password is wrong
     */
    public function displayWrongPassword() {
        $this->buildModal('Mot de passe incorrect', '<div class="alert alert-danger">Mauvais mot de passe</div>');
    }

    /**
     * Display a message if the
     */
    public function displayMailSend() {
        $this->buildModal('Mail envoyé', '<div class="alert alert-success"> Un mail a été envoyé à votre adresse mail, merci de bien vouloir entrer le code reçu</div>');
    }

    /**
     * Message to prevent a login already exist
     */
    public function displayErrorCreation() {
        $this->buildModal('Inscription échouée', '<div class="alert alert-danger">Il y a eu une erreur dans le formulaire, veuillez vérifier vos information et réessayer</div>');
    }

    /**
     * Message to prevent a login already exist
     */
    public function displayErrorLogin() {
        $this->buildModal('Inscription échouée', '<div class="alert alert-danger"> Le login est déjà utilisé ! </div>');
    }

    /**
     * Display to user, no lesson today
     *
     * @return string
     */
    public function displayNoStudy() {
        return '<p>Vous n\'avez pas cours!</p>';
    }

    public function errorMessageNoCodeRegister() {
        $current_user = wp_get_current_user();
        return '
        <h2>' . $current_user->user_login . '</h2>
        <p>Vous êtes enregistré sans aucun emploi du temps, rendez-vous sur votre compte pour pouvoir vous attribuez un code afin d\'accèder à votre emploi du temps</p>';
    }

    public function successMesageChangeCode() {
        $this->buildModal('Modification validée', '<div class="alert alert-success"> Le changement de groupe a été pris en compte</div>');
    }

    public function errorMesageChangeCode() {
        $this->buildModal('Modification échouée', '<div class="alert alert-danger"> Le changement de groupe n\'a pas été pris en compte</div>');
    }
}
