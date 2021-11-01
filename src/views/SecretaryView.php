<?php

namespace Views;

use Models\User;

/**
 * Class SecretaryView
 *
 * All view for secretary (Forms, tables, messages)
 *
 * @package Views
 */
class SecretaryView extends UserView
{
    /**
     * Display the creation form
     *
     * @return string
     */
    public function displayFormSecretary() {
        return '
        <h2>Compte secrétaire</h2>
        <p class="lead">Pour créer des secrétaires, remplissez ce formulaire avec les valeurs demandées.</p>
        ' . $this->displayBaseForm('Secre');
    }

    /**
     * Displays the admin dashboard
     * @author Thomas Cardon
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
            Créez des informations pour toutes les télévisions connectées, les informations seront affichées sur chaque télévisions en plus des informations déjà publiées.
            Les informations sur les télévisions peuvent contenir du texte, des images et même des pdf.
            <br /> <br />
            Vous pouvez faire de même avec les <b>alertes</b> des télévisions connectées.
            Les informations seront affichées dans la partie droite, et les alertes dans le bandeau rouge en bas des TV.
          </p>
        </div>
      </div>
      <div class="row align-items-md-stretch my-2">
        <div class="col-md-6">
          <div class="h-100 p-5 text-white bg-dark rounded-3">
            <h2 class="title-block">(+) Ajouter</h2>
            <p>Ajoutez une information ou une alerte.</p>
            <a href="' . home_url('/creer-information') . '" class="btn btn-outline-light" role="button">Information</a>
            <a href="' . home_url('/gerer-les-alertes') . '" class="btn btn-outline-light" role="button">Alerte</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="h-100 p-5 text-white bg-danger border rounded-3">
            <h2 class="title-block">Interface secrétaires</h2>
            <p>Accédez au mode tablette.</p>
            <a href="' . home_url('/tablet-view') . '" class="btn btn-dark" role="button">Voir</a>
          </div>
        </div>
      </div>
      <div class="row align-items-md-stretch my-2">
        <div class="col-md-6">
          <div class="h-100 p-5 bg-light border rounded-3">
            <h2 class="title-block title-bold">👷 Personnel</h2>
            <p>Ajoutez des utilisateurs qui pourront à leur tour des informations, alertes, etc.</p>
            <a href="' . home_url('/creer-utilisateur') . '" class="btn btn-danger" role="button">Créer</a>
            <a href="' . home_url('/liste-utilisateur') . '" class="btn btn-dark" role="button">Voir</a>
          </div>
        </div>
        <div class="col-md-6">
          <div class="h-100 p-5 text-white bg-info rounded-3">
            <h2 class="title-block">Emploi du temps</h2>
            <p>Forcez l\'actualisation des emplois du temps.</p>
            <button type="submit" class="btn btn-outline-light" name="updatePluginEcranConnecte">🔄️ Actualiser</a>
          </div>
        </div>
      </div>
    </section>';
    }

    /**
     * Display all secretary
     *
     * @param $users    User[]
     *
     * @return string
     */
    public function displayTableSecretary($users) {
        $title = '<b>Rôle affiché: </b> Secrétaire';
        $name = 'Secre';
        $header = ['Login'];

        $row = array();
        $count = 0;
        foreach ($users as $user) {
            ++$count;
            $row[] = [$count, $this->buildCheckbox($name, $user->getId()), $user->getLogin()];
        }

        return $this->displayTable($name, $title, $header, $row, 'Secre', '<a type="submit" class="btn btn-primary disabled" href="' . home_url('/creer-utilisateur') . '" role="button" aria-disabled="true">Créer</a>');
    }

    /**
     * Ask to the user to choose an user
     */
    public function displayNoUser() {
        return '<p class="alert alert-danger">Veuillez choisir un utilisateur</p>';
    }
}
